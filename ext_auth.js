jQuery(document).ready(function()
{
  // upload finalize page
  $("#ext_auth_button").click(function ()
  {

    var form_data = "log=bob&pwd=asdf&rememberme=forever&wp-submit=Log+In";

    $.ajax(
      {
      type: "post",
      url: "/wp-login.php",
      data: form_data,
      success: function(json) {
          alert('you are now logged in. Taking you to the admin page');
          window.location = '/wp-admin'; 
      },
      error: function () {
          alert('something bad happened');
      }
      });
    return false;
  });
});
