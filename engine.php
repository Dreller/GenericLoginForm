<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

# Read configs
$loginConfig = parse_ini_file('.login.config', TRUE);

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



# POST - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 
# If the engine gets data from a POST call
if( getenv('REQUEST_METHOD') == 'POST' ){
    $raw = file_get_contents("php://input");
    $input = json_decode($raw);
    $method = $input->method;

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
    
    if( $method == 'auth' ){
        # Extract Authentication data
            $userCode = $input->txUser;
            $userPass = $input->txPasswd;
        # Authenticate user
            require_once('php/MysqliDb.php');
            $db = new MysqliDb($dbHost, $dbUser, $dbPass, $dbName);
              
        # Select User from database
            $db->where($fieldUser, $userCode);
            $user = $db->getOne($tableName);

        # If there is no user found
            if( $db->count == 0 ){
                $json['status']     = 'error';
                $json['message']    = sprintf($loginConfig["Literal"]["NoUserFound"], $userCode);
                goto OutputJSON;
            }
        # If the password is not the right one
            if( !password_verify($userPass, $user[$fieldPass]) ){
                $json['status']     = 'error';
                $json['message']    = $loginConfig["Literal"]["BadPassword"];
                goto OutputJSON;
            }

        
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
    if( $method == "register" ){
        # Extract registration data 
        $recData = json_decode($raw, true);
        unset( $recData[ 'method' ] );
        unset( $recData[ $fieldPass . '2' ] );

        # Hash Password
        $recData[$fieldPass] = password_hash( $recData[$fieldPass], PASSWORD_DEFAULT);

        # Database
        require_once('php/MysqliDb.php');
        $db = new MysqliDb($dbHost, $dbUser, $dbPass, $dbName);

        # Check for unique data
        $uniques = array_map('trim', explode(",", $loginConfig["Registration"]["Uniques"]));
        foreach( $uniques as $unique ){
            $db->where($unique, $recData[$unique]);
            $db->get($tableName);
            if( $db->count > 0 ){
                $json['status']     = 'error';
                $json['message']    = 'Looks like you already have an account.';
                goto OutputJSON;
            }
        }

        # Create new User Entry
        $newID = $db->insert( $tableName, $recData );

        # Retrieve data from database, to proceed to the session start.
        $db->where( $fieldUser, $recData[ $fieldUser ] );
        $user = $db->getOne( $tableName );

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

