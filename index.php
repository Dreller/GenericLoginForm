<?php 
# Import engine
require_once('engine.php');
?>
<!doctype html>
<html class="no-js" lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title><?php echo $loginConfig["Application"]["Name"]; ?></title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/semantic-ui@2.4.2/dist/semantic.min.css">
        <style type="text/css">
            body{
                background-color: #DADADA;
            }
            .button{
                cursor:pointer;
            }
        </style>
    </head>
    <body>
        <p>
            <h2 class="ui center aligned icon header">
                <i class="circular <?php echo $loginConfig["GUI"]["AppIcon"];?> icon"></i>
                <?php echo $loginConfig["Application"]["Name"]; ?>
            </h2>
        </p>

        <div class="ui container">
            <div class="ui placeholder segment">
                <div class="ui <?php echo ($loginOptions?'two':''); ?> column very relaxed stackable grid">
                    <div class="column">
                        <div class="ui form">
                            <div class="ui hidden message negative" id="loginErrorMessage">
                            <p id="loginErrorMessageText"></p>
                            </div>
                            <form id="loginForm">
                                <input type="hidden" name="method" value="auth">
                                <div class="field">
                                    <label><?php echo _PROMPT_USERCODE; ?></label>
                                    <div class="ui left icon input">
                                        <input type="text" name="txUser" id="txUser" required>
                                        <i class="user icon"></i>
                                    </div>
                                </div>
                                <div class="field">
                                    <label><?php echo _PROMPT_PWD; ?></label>
                                    <div class="ui left icon input">
                                        <input type="password" name="txPasswd" id="txPasswd" required>
                                        <i class="lock icon"></i>
                                    </div>
                                </div>
                                <div class="ui <?php echo $loginConfig["GUI"]["LoginButtonColor"]; ?> submit button" id="bt_loginOK" onclick="sendLoginForm();">
                                    <?php echo _BUTTON_LOGIN; ?>
                                </div>
                            </form>
                        </div>
                    </div>


                    <?php 
                        if( $loginOptions ){
                            echo '<div class="middle aligned column">';
                                if( $loginConfig["Registration"]["Enabled"]=="Y" ){
                                    echo '<p><div class="ui '.$loginConfig["Registration"]["Color"].' button" onclick="displayRegistrationForm();">
                                            <i class="signup icon"></i>
                                            '. _BUTTON_REGISTER .'
                                        </div></p>';
                                }
                                if( $loginConfig["PasswordReset"]["Enabled"]=="Y" ){
                                    echo '<p><div class="ui '.$loginConfig["PasswordReset"]["Color"].' button" onclick="displayResetForm();">
                                            <i class="signup icon"></i>
                                            '. _BUTTON_PWDRESET .'
                                        </div></p>';
                                }
                            echo '</div>';
                        }
                    ?>
                </div>
                <?php 
                    if( $loginOptions ){
                        echo '<div class="ui vertical divider">
                            '. _LABEL_OR .'
                        </div>';
                    }
                ?>
            </div>
        </div>

        <div class="ui tiny modal" id="resetModal">
            <i class="close icon"></i>
            <div class="header">
                <?php echo _LABEL_PWDRESET_FORM; ?>
            </div>
            <div class="content">
                <div class="ui hidden message negative" id="resetErrorMessage">
                    <p id="resetErrorMessageText"></p>
                </div>
                <form id="resetForm">
                    <input type="hidden" name="method" value="reset">
                    <div class="ui form">
                        <div class="field">
                            <div class="field">
                                <label><?php echo _PROMPT_PWDRESET; ?>:</label>
                                <input type="email" id="<?php echo $loginConfig["PasswordReset"]["EmailField"]; ?>" name="<?php echo $loginConfig["PasswordReset"]["EmailField"]; ?>">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="actions">
                <div class="ui cancel button"><?php echo _BUTTON_CANCEL; ?></div>
                <div class="ui button" id="btn_resetOK" onclick="sendResetForm();">OK</div>
            </div>
        </div>

        <div class="ui tiny modal" id="passwdModal">
            <i class="close icon"></i>
            <div class="header">
                <?php echo _LABEL_PWDCHG_FORM; ?>
            </div>
            <div class="content">
                <div class="ui hidden message negative" id="passwdErrorMessage">
                    <p id="passwdErrorMessageText"></p>
                </div>
                <form id="passwdForm">
                    <input type="hidden" name="method" value="passwd">
                    <div class="ui form">
                        <div class="ui info message">
                            <div class="header">
                                <?php echo _LABEL_PWDCHG_HELP; ?>.
                            </div>
                            <p>
                                <?php echo sprintf(_LABEL_PWDCHG_NOTICE, _PROMPT_USERCODE, _PROMPT_PWD); ?>
                            </p>
                        </div>
                        <div class="field">
                            <div class="field">
                                <label><?php echo _PROMPT_USERCODE; ?></label>
                                <input type="text" name="txPasswdUser" id="txPasswdUser" required>
                            </div>
                            <div class="field">
                                <label><?php echo _PROMPT_PWDCURRENT ; ?></label>
                                <input type="password" name="txPasswdCurrent" id="txPasswdCurrent" required>
                            </div>
                            <div class="field">
                                <label><?php echo _PROMPT_PWDNEW ; ?></label>
                                <input type="password" name="<?php echo $loginConfig["Database"]["UserPasswordField"]; ?>" id="<?php echo $loginConfig["Database"]["UserPasswordField"]; ?>" required>
                            </div>
                            <div class="field">
                                <label><?php echo _PROMPT_PWDCONFIRM ; ?></label>
                                <input type="password" name="<?php echo $loginConfig["Database"]["UserPasswordField"]; ?>2" id="<?php echo $loginConfig["Database"]["UserPasswordField"]; ?>2" required>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="actions">
                <div class="ui cancel button"><?php echo _BUTTON_CANCEL; ?></div>
                <div class="ui button" id="btn_passwdOK" onclick="sendPasswdForm();">OK</div>
            </div>
        </div>


        <div class="ui tiny modal" id="registrationModal">
            <i class="close icon"></i>
            <div class="header">
                <?php echo _LABEL_REGISTER_FORM; ?>
            </div>
            <div id="modalText" class="content">
                <div class="ui hidden message negative" id="registrationErrorMessage">
                    <p id="registrationErrorMessageText"></p>
                </div>
                <form id="registerForm">
                    <input type="hidden" name="method" value="register">
                    <div class="ui form">
                        <div class="field">
                            <div class="field">
                                <label><?php echo _PROMPT_USERCODE ; ?></label>
                                <input type="text" id="<?php echo $loginConfig["Database"]["UserCodeField"]; ?>" name="<?php echo $loginConfig["Database"]["UserCodeField"]; ?>" required>
                            </div>
                        </div>
                        <div class="two fields">
                            <div class="field">
                                <label><?php echo _PROMPT_PWD ; ?></label>
                                <input type="password" id="<?php echo $loginConfig["Database"]["UserPasswordField"]; ?>" name="<?php echo $loginConfig["Database"]["UserPasswordField"]; ?>" required>
                            </div>
                            <div class="field">
                                <label><?php echo _PROMPT_PWDCONFIRM; ?></label>
                                <input type="password" id="<?php echo $loginConfig["Database"]["UserPasswordField"]; ?>2" name="<?php echo $loginConfig["Database"]["UserPasswordField"]; ?>2" required>
                            </div>
                        </div>
                        <?php
                                $promptNames = explode(",", $loginConfig["Registration"]["Fields"]);
                                $promptLabels = explode(",", $loginConfig["Registration"]["Labels"]);
                                $promptTypes = explode(",", $loginConfig["Registration"]["Types"]);
                                $i = -1;

                                foreach($promptNames as $dummy){
                                    $i++;

                                    $thisLabel = trim($promptLabels[$i]);
                                    $thisName = trim($promptNames[$i]);
                                    $thisType = validateDataType(trim($promptTypes[$i]));

                                    echo '<div class="field">';
                                    echo '<label>' . $thisLabel . '</label>';
                                    echo '<input type="' . $thisType. '" id="' . $thisName . '" name="' . $thisName . '">';
                                    echo '</div>';
                                }
                            ?>
                        
                    </div>
                </form>
            </div>
            <div class="actions">
                <div class="ui cancel button"><?php echo _BUTTON_CANCEL; ?></div>
                <div class="ui button" id="btn_registrationOK" onclick="sendRegistrationForm();">OK</div>
            </div>
        </div>

    <div id="dimmerScreen" class="ui page dimmer">
        <div id="dimmerText" class="content">
            
        </div>
    </div>
       
        <script src="js/jquery_3.5.1.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/semantic-ui@2.4.2/dist/semantic.min.js"></script>
        <script>
            function wrapForm(formID){
                var mixed_array = $('#' + formID).serializeArray();
                var sorted_array = {};
                $.map(mixed_array, function(n, i){
                    sorted_array[n['name']] = n['value'];
                });
                var jsonWorker = JSON.stringify(sorted_array);
                return jsonWorker;
            }
            function sendLoginForm(){
                $("#bt_loginOK").addClass("loading");
                if( !validateForm('loginForm') ){
                    $("#bt_loginOK").removeClass("loading");
                    return false;
                }
                var loginData = wrapForm('loginForm');
                sendForm(loginData, 'login');
            }
            function sendRegistrationForm(){
                $("#bt_registrationOK").addClass("loading");
                if( !validateForm('registerForm') ){
                    $("#bt_registrationOK").removeClass("loading");
                    return false;
                }
                var registerData = wrapForm('registerForm');
                sendForm(registerData, 'registration');
            }
            function sendResetForm(){
                $("#bt_resetOK").addClass("loading");
                if( !validateForm('resetForm') ){
                    $("#bt_resetOK").removeClass("loading");
                    return false;
                }
                var resetData = wrapForm('resetForm');
                sendForm(resetData, 'reset');
            }
            function sendPasswdForm(){
                $("#bt_passwdOK").addClass("loading");
                if( !validateForm('passwdForm') ){
                    $("#bt_passwdOK").removeClass("loading");
                    return false;
                }
                var passwdData = wrapForm('passwdForm');
                sendForm(passwdData, 'passwd');
            }
            function sendForm(jsonData, target){
                $.ajax({
                    type: "POST",
                    url: "engine.php",
                    data: jsonData,
                    success: function(result, target){
                        processResult(result, target);
                    }
                });
            }
            function validateForm(formID){
                var iErrors = 0;
                $("form#" + formID).find('input').each(function(){

                    if( $(this).prop('required') ){
                        if( isEmpty( $(this) )){
                            $(this).parent().parent().addClass('error');
                            console.log('Field ' + $(this).prop('id') + ' is required and empty.');
                            iErrors++;
                        }else{
                            $(this).parent().parent().removeClass('error');
                        }
                    }

                    if( $(this).attr("name")!="<?php echo $loginConfig["Database"]["UserPasswordField"]; ?>" ){
                        $passwd1 = document.getElementById('<?php echo $loginConfig["Database"]["UserPasswordField"]; ?>').value;
                        $passwd2 = document.getElementById('<?php echo $loginConfig["Database"]["UserPasswordField"]; ?>2').value;
                        if( $passwd1 != $passwd2 ){
                            $(this).parent().parent().addClass('error');
                            displayErrorMessage('registration', 'Password and its confirmation are differents.');
                            iErrors++;
                        }
                    }
                });
                
                if( iErrors > 0 ){
                    return false;
                }else{
                    return true;
                }
            }

            function isEmpty(element){
                // Source: https://stackoverflow.com/a/6813294
                return !$.trim(element.val());
            }

            function processResult(myData, target){
                $("#bt" + target + "OK").removeClass("loading");
                console.log(myData);

                if( myData['status'] == 'error' ){
                    displayErrorMessage(target, myData['message']);
                }
                if( myData['status'] == 'tell' ){
                    document.getElementById("dimmerText").innerHTML = myData['message'];
                    console.log(myData['message']);
                    $("#dimmerScreen").dimmer('show');
                }
                if( myData['status'] == 'ok' ){
                    window.location.replace(myData['reference']);
                }
                if( myData['status'] == 'passwd' ){
                    $("#txPasswdUser").val(myData['u']);
                    $("#txPasswdCurrent").val(myData['p']);
                    displayPasswdForm();
                }

            }
            function displayRegistrationForm(){
                $("#registrationModal").modal('show');
            }
            function displayResetForm(){
                $("#resetModal").modal('show');
            }
            function displayPasswdForm(){
                $("#passwdModal").modal('show');
            }
            function displayErrorMessage(target, message){
                document.getElementById(target + 'ErrorMessageText').innerHTML = message;
                $('#' + target + 'ErrorMessage').removeClass('hidden');
                $('#' + target + 'ErrorMessage').addClass('visible');
            }
            
        </script>
    </body>
</html>