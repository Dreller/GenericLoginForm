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
                                        <input type="text" name="txUser" id="txUser">
                                        <i class="user icon"></i>
                                    </div>
                                </div>
                                <div class="field">
                                    <label><?php echo $loginConfig["GUI"]["PasswordLabel"]; ?></label>
                                    <div class="ui left icon input">
                                        <input type="password" name="txPasswd" id="txPasswd">
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
                                    echo '<p><div class="ui '.$loginConfig["Registration"]["Color"].' button">
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




        <div class="ui modal">
            <i class="cloase icon"></i>
            <div class="header">
                Test
            </div>
            <div id="modalText" class="content">
                    ALLO
            </div>
            <div class="actions">
                <div class="ui button">Cancel</div>
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
            function validateLoginForm(){
                
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
            
        </script>
    </body>
</html>