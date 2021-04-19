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
        url: "GenLogFrm/engine.php",
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

        // Passwd comparison
        if( $(this).attr("name")==fieldPassword ){
            var passwd1 = $("#" + formID + " #" + fieldPassword).val();
            var passwd2 = $("#" + formID + " #" + fieldPassword + '2').val();
            if( passwd1 != passwd2 ){
                $(this).parent().parent().addClass('error');
                displayErrorMessage(formID, errorPwdDiff);
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
    $("#bt_loginOK").removeClass("loading");
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
    document.getElementById(target + 'ErrorText').innerHTML = message;
    $('#' + target + 'Error').removeClass('hidden');
    $('#' + target + 'Error').addClass('visible');
}
