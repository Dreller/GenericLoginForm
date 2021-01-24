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
            #btOK{
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
                                    <label><?php echo $loginConfig["GUI"]["UserLabel"]; ?></label>
                                    <div class="ui left icon input">
                                        <input type="text" name="txUser" id="txUser" required>
                                        <i class="user icon"></i>
                                    </div>
                                </div>
                                <div class="field">
                                    <label><?php echo $loginConfig["GUI"]["PasswordLabel"]; ?></label>
                                    <div class="ui left icon input">
                                        <input type="password" name="txPasswd" id="txPasswd" required>
                                        <i class="lock icon"></i>
                                    </div>
                                </div>
                                <div class="ui <?php echo $loginConfig["GUI"]["LoginButtonColor"]; ?> submit button" id="btOK" onclick="sendLoginForm();">
                                    <?php echo $loginConfig["GUI"]["LoginButtonLabel"]; ?>
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
                                            '.$loginConfig["Registration"]["Invite"].'
                                        </div></p>';
                                }
                                if( $loginConfig["PasswordReset"]["Enabled"]=="Y" ){
                                    echo '<p><div class="ui '.$loginConfig["PasswordReset"]["Invite"].' button">
                                            <i class="signup icon"></i>
                                            '.$loginConfig["PasswordReset"]["Invite"].'
                                        </div></p>';
                                }
                            echo '</div>';
                        }
                    ?>
                </div>
                <?php 
                    if( $loginOptions ){
                        echo '<div class="ui vertical divider">
                            '.$loginConfig["GUI"]["SeparatorLabel"].'
                        </div>';
                    }
                ?>
            </div>
        </div>




        <div class="ui tiny modal" id="registrationModal">
            <i class="close icon"></i>
            <div class="header">
                Registration form
            </div>
            <div id="modalText" class="content">
                <div class="ui form" id="registrationForm">
                    <div class="field">
                        <label><?php echo $loginConfig["GUI"]["UserLabel"]; ?></label>
                        <input type="text" id="registrationUserCode">
                    </div>
                </div>
            </div>
            <div class="actions">
                <div class="ui cancel button">Cancel</div>
                <div class="ui button">OK</div>
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
                console.log(jsonWorker);
                return jsonWorker;
            }
            function sendLoginForm(){
                $("#btOK").addClass("loading");
                if( !validateForm('loginForm') ){
                    $("#btOK").removeClass("loading");
                    return false;
                }
                var loginData = wrapForm('loginForm');
                //$('.ui.modal').modal('show');
                sendForm(loginData);
            }
            function sendForm(jsonData){
                $.ajax({
                    type: "POST",
                    url: "engine.php",
                    data: jsonData,
                    success: function(result){
                        processResult(result);
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

            function processResult(myData){
                $("#btOK").removeClass("loading");
                console.log(myData);
                //data = JSON.parse(myData);

                if(myData['status']=='error'){
                    document.getElementById('loginErrorMessageText').innerHTML = myData['message'];
                    $('#loginErrorMessage').removeClass('hidden');
                    $('#loginErrorMessage').addClass('visible');
                }
                if(myData['status']=='ok'){
                    window.location.replace(myData['reference']);
                }


            }
            function displayRegistrationForm(){
                $("#registrationModal").modal('show');
            }
            
        </script>
    </body>
</html>