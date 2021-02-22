<?php
/**
 * Ideated by Sergio Casizzone
 * User: jambtc
 * Date: 28/01/2020
 */
namespace jambtc\oauthgoogle;


class google extends \yii\base\Widget
{
    public $auth_url;

    function __construct($auth_url){
        $this->auth_url = $auth_url;
    }

    public function loginButton(){
        $this->jsGoogle();
        return '<script src="https://apis.google.com/js/client:platform.js?onload=renderButton" async defer></script><div id="gSignIn"></div>';
    }

    public function jsGoogle(){
        echo "<script>
            // Render Google Sign-in button
            function renderButton() {
                gapi.signin2.render('gSignIn', {
                    'scope': 'profile email',
                    'width': 240,
                    'height': 50,
                    'longtitle': true,
                    'theme': 'dark',
                    'onsuccess': onSuccess,
                    'onfailure': onFailure
                });
            }


            // Sign-in success callback
            function onSuccess(googleUser) {
                // Retrieve the Google account data
                gapi.client.load('oauth2', 'v2', function () {
                  var request = gapi.client.oauth2.userinfo.get({
                      'userId': 'me'
                  });
                  request.execute(function (user) {
                    // Display the user details
                    console.log('[oauth] GOOGLE userData', user);
                    $.ajax({
              			url:'$this->auth_url',
              			type: 'GET',
              			data:{
              				'email'		: user.email,
              				'first_name': user.given_name,
              				'last_name'	: user.family_name,
              				'id'		: user.id,
              				'username'	: user.name,
              				'picture'	: user.picture,
              			},
              			dataType: 'json',
              			success:function(data){
							var auth2 = gapi.auth2.getAuthInstance();
              				console.log('[oauth] Google Check Autorization Response',data);
							
							if (data.success === false){
								auth2.signOut().then(function () {
								console.log(['Google account is now signout']);
							});
                        }
              				},
              				error: function(j){
              					console.log(j);
              				}
              			});
                  });
              });
            }

            // Sign-in failure callback
            function onFailure(error) {
                var json = jQuery.parseJSON(error);
                alert(json);
            }

        </script>";
    }

}
