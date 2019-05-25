var recoveryPage = $('#recoveryPage');
var loginPage = $('#loginPage');
var profilePage = $('#profilePage');

$('#recoveryForm').submit(function (event) {
  event.preventDefault();
  var username = $('#username').val();
  var apiUrl = "http://api.helloprint.test:8001/recovery.php";
  var submitBtn = $('#submitRecovery');
  var formError = $('#formError');
  var formSuccess = $('#formSuccess');

  submitBtn.attr('disabled', true);

  $.ajax({
    type: "POST",
    url: apiUrl,
    dataType: 'json',
    contentType: false,
    data: JSON.stringify({
      username: username
    })
  })
  .done(function (data) {
    submitBtn.attr('disabled', false);
     if (!data.success) {
       formError.html(data.message);
       formSuccess.hide();
       formError.show();
     } else {
       formError.hide();
       formSuccess.show();
       setTimeout(
         function()
         {
           recoveryPage.hide();
           loginPage.show();
           $('#username').val('');
           formSuccess.hide();
         }, 2000);
     }
  });
});

$('#loginForm').submit(function (event) {
  event.preventDefault();
  var username = $('#usernameLogin').val();
  var password = $('#passwordLogin').val();
  var apiUrl = "http://api.helloprint.test:8001/login.php";
  var submitBtn = $('#submitLogin');
  var formError = $('#formErrorLogin');
  var formSuccess = $('#formSuccessLogin');
  submitBtn.attr('disabled', true);

  $.ajax({
    type: "POST",
    url: apiUrl,
    dataType: 'json',
    contentType: false,
    data: JSON.stringify({
      username: username,
      password: password,
    })
  })
    .done(function (data) {
      submitBtn.attr('disabled', false);
      if (!data.success) {
        formError.html(data.message);
        formSuccess.hide();
        formError.show();
      } else {
        formError.hide();
        formSuccess.show();
        setTimeout(
          function()
          {
            $('#userWelcome').html('Hi ' + username + ' Welcome!');
            $('#emailProfile').html('Your Email is: ' + data.email);
            loginPage.hide();
            profilePage.show();
            $('#usernameLogin').val('');
            $('#passwordLogin').val('');
            formSuccess.hide();
          }, 2000);
      }
    });
});

$('#backToRecovery').click(function (event) {
  event.preventDefault();
  loginPage.hide();
  profilePage.hide();
  recoveryPage.show();
});


$('#goToLogin').click(function (event) {
  event.preventDefault();
  goToLogin();
});

$('#logout').click(function (event) {
  event.preventDefault();
  goToLogin();
});

function goToLogin() {
  loginPage.show();
  profilePage.hide();
  recoveryPage.hide();
}