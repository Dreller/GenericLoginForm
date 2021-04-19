<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


# Read configs
$loginConfig = parse_ini_file('.login.config', TRUE);

# Server language
$srv_lang = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
$lang = explode('-', $srv_lang)[0];
if( $lang != 'fr' ){
    $lang = strtolower( $loginConfig['Application']['Language'] );
}
# Import language pack
include_once('_lang_' . $lang . '.php');

# Check if there is any enabled options
$loginOptions = false;
    if( $loginConfig["PasswordReset"]["Enabled"]=="Y" ){
        $loginOptions = true;
    }
    if( $loginConfig["Registration"]["Enabled"]=="Y" ){
        $loginOptions = true;
    }

# Functions - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
function validateDataType($type){
    $returnType = "text";
    switch($type){
        case "text":
        case "date":
        case "email":
            $returnType = $type;
    }
    return $returnType;
}
function randomString($len) { 
    $pool = '23456789abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ!?-+$'; 
    $string = '';
    for ($i = 0; $i < $len; $i++) { 
        $index = rand(0, strlen($pool) - 1); 
        $string .= $pool[$index]; 
    } 
    return $string; 
} 



# POST - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 
# If the engine gets data from a POST call
if( getenv('REQUEST_METHOD') == 'POST' ){
    $raw = file_get_contents("php://input");
    $input = json_decode($raw, true);
    $method = $input["method"];

    $json = Array();

    $dbHost = $loginConfig["Database"]["Host"];
    $dbUser = $loginConfig["Database"]["User"];
    $dbPass = $loginConfig["Database"]["Password"];
    $dbName = $loginConfig["Database"]["Database"];
    $dbPort = $loginConfig["Database"]["Port"];

    if($dbPort != ""){
        $dbHost = $dbHost . ':' . $dbPort;
    }

    $tableName  = $loginConfig["Database"]["UserTableName"];
    $fieldUser  = $loginConfig["Database"]["UserCodeField"];
    $fieldPass  = $loginConfig["Database"]["UserPasswordField"];
    $fieldID    = $loginConfig["Database"]["UserIdField"];
    
    if( $method == 'auth' ){
        # Extract Authentication data
            $userCode = $input["txUser"];
            $userPass = $input["txPasswd"];
        # Authenticate user
            require_once('php/MysqliDb.php');
            $db = new MysqliDb($dbHost, $dbUser, $dbPass, $dbName);
              
        # Select User from database
            $db->where($fieldUser, $userCode);
            $user = $db->getOne($tableName);

        # If there is no user found
            if( $db->count == 0 ){
                $json['status']     = 'error';
                $json['message']    = sprintf(_ERROR_USERNOTFOUND, $userCode);
                goto OutputJSON;
            }
        # If the password is not the right one
            if( !password_verify($userPass, $user[$fieldPass]) ){
                $json['status']     = 'error';
                $json['message']    = _ERROR_BADPWD;
                goto OutputJSON;
            }

        # If the password is expired and user have to change it
        # To use this function, the 'PasswordReset' feature must be enabled.
        if( $loginConfig["PasswordReset"]["Enabled"] == "Y" ){
            $expirationField = $loginConfig["PasswordReset"]["ExpiredField"];
            if( $user[$expirationField] != 0 ){
                $json['status']     = 'passwd';
                $json['message']    = _LABEL_PWDCHG_HELP;
                $json['u']          = $userCode;
                $json['p']          = $userPass;
                goto OutputJSON;
            }
        }

        
        # If every tests are passed, we start a PHP Session with all user infos.
        session_start();
        # Build the array of values that should be excluded
        $temp = $loginConfig['Database']['UserPasswordField'] . ', ' . $loginConfig['Database']['SessionExclusions'];
        $temp = str_replace(' ', '', $temp);
        $exclArray = explode(',', $temp);
        foreach( $user as $key=>$value ){
            if( !in_array($key, $exclArray) ){
                $_SESSION[$key] = $value;
            }
        }
        $json['status']     = 'ok';
        $json['message']    = _LABEL_WELCOME;
        $json['reference']  = $loginConfig["Application"]["RedirectPage"];
        goto OutputJSON;
    }

    if( $method == 'passwd' ){
        # Extract Authentication data
            $userCode = $input["txPasswdUser"];
            $userPass = $input["txPasswdCurrent"];
        # Authenticate user
            require_once('php/MysqliDb.php');
            $db = new MysqliDb($dbHost, $dbUser, $dbPass, $dbName);
              
        # Select User from database
            $db->where($fieldUser, $userCode);
            $user = $db->getOne($tableName);

        # If there is no user found
            if( $db->count == 0 ){
                $json['status']     = 'error';
                $json['message']    = sprintf(_ERROR_USERNOTFOUND, $userCode);
                goto OutputJSON;
            }
        # If the password is not the right one
            if( !password_verify($userPass, $user[$fieldPass]) ){
                $json['status']     = 'error';
                $json['message']    = _ERROR_BADPWD;
                goto OutputJSON;
            }

        # Update the Password with the new one
        $newData = Array(
            $fieldPass => password_hash($input[$fieldPass], PASSWORD_DEFAULT),
            $loginConfig["PasswordReset"]["ExpiredField"] => 0
        );

        $db->where($fieldID, $user[$fieldID]);
        $db->update($tableName, $newData);

        # Re-get User infos
        $db->where($fieldID, $user[$fieldID]);
        $user = $db->getOne($tableName);
        
        # If every tests are passed, we start a PHP Session with all user infos.
        session_start();
        foreach( $user as $key=>$value ){
            $_SESSION[$key] = $value;
        }
        $json['status']     = 'ok';
        $json['message']    = _LABEL_WELCOME;
        $json['reference']  = $loginConfig["Application"]["RedirectPage"];
        goto OutputJSON;
    }
    if( $method == "reset" ){
        # Extract user email address from form 
            $userEmail = $input[$loginConfig["PasswordReset"]["EmailField"]];
        
        # Database
            require_once('php/MysqliDb.php');
            $db = new MysqliDb($dbHost, $dbUser, $dbPass, $dbName);

        # Validate if this email address exists 
            $db->where($loginConfig["PasswordReset"]["EmailField"], $userEmail);
            $user = $db->getOne( $tableName );

            if( $db->count == 0 ){
                $json['status']     = 'error';
                $json['message']    = _ERROR_MAILNOTFOUND;
                goto OutputJSON;
            }
            if( $db->count > 1 ){
                $json['status']     = 'error';
                $json['message']    = _ERROR_MORETHAN1USER;
                goto OutputJSON;
            }
            # At this point, we have found the only user related to this email address.
            # We set an temporary password.

            $newPasswd = randomString(10);
            $newHashed = password_hash($newPasswd, PASSWORD_DEFAULT);

            $newData = Array(
                $fieldPass => $newHashed,
                $loginConfig["PasswordReset"]["ExpiredField"] => 1
            );

            ######## Must implement something to handle Table Key
            $db->where($fieldID, $user[$fieldID]);
            if( $db->update($tableName, $newData) ){
                $mailOK =  mail($userEmail,_EMAIL_PWD_SUBJECT,sprintf(_EMAIL_PWD_BODY, $newPasswd)) ;
                if( $mailOK === true ){
                    $json['status']     = 'tell';
                    $json['message']    = sprintf(_EMAIL_PWD_CONFIRM, $userEmail);
                    goto OutputJSON;
                }else{
                    $json['status']     = 'error';
                    $json['message']    = sprintf(_EMAIL_PWD_NOEMAIL, $userEmail);
                    goto OutputJSON;
                }
                
            }else{
                $json['status']     = 'error';
                $json['message']    = 'Unable to change the password, MySQLi Error: ' . $db->getLastError();
                goto OutputJSON;
            }
    }
    if( $method == "register" ){
        # Extract registration data 
        unset( $input['method']);
        unset( $input[ $fieldPass . '2' ] );

        # Hash Password
        $input[$fieldPass] = password_hash( $input[$fieldPass], PASSWORD_DEFAULT);

        # Database
        require_once('php/MysqliDb.php');
        $db = new MysqliDb($dbHost, $dbUser, $dbPass, $dbName);

        # Check for unique data
        $uniques = array_map('trim', explode(",", $loginConfig["Registration"]["Uniques"]));
        foreach( $uniques as $unique ){
            $db->where($unique, $input[$unique]);
            $db->get($tableName);
            if( $db->count > 0 ){
                $json['status']     = 'error';
                $json['message']    = _ERROR_USREXISTS;
                goto OutputJSON;
            }
        }

        # Create new User Entry
        $newID = $db->insert( $tableName, $input );

        # Retrieve data from database, to proceed to the session start.
        $db->where($fieldID, $newID);
        $user = $db->getOne($tableName);

        # If every tests are passed, we start a PHP Session with all user infos.
        session_start();
        foreach( $user as $key=>$value ){
            $_SESSION[$key] = $value;
        }
        $json['status']     = 'ok';
        $json['message']    = 'Welcome!';
        $json['reference']  = $loginConfig["Application"]["RedirectPage"];
        goto OutputJSON;
    }

OutputJSON:
    header('Content-Type: application/json');
    echo json_encode($json);
}

