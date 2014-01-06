<?php
/*
Plugin Name: ext_auth_demo
Description:  Wordpress external authentication demo. Based on http://ben.lobaugh.net/blog/7175/wordpress-replace-built-in-user-authentication
Version: 1
Author: Bob Coggeshall 
Author URI: http://automattic.com/wordpress-plugins/
License: GPLv2 or later
 */


//add_action('admin_enqueue_scripts','auth_enqueue_scripts');


add_shortcode('ext_auth_demo_page','ext_auth_demo_page'); // comment this out in production

wp_enqueue_script( 'ext_auth', plugin_dir_url( __FILE__ ) . 'ext_auth.js', array( 'jquery' ) );

add_filter( 'authenticate', 'demo_auth', 10, 3 ); // override wordpress's authentication

wp_deregister_script( 'jquery');
$google_jquery = "//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js";
wp_register_script( 'jquery', $google_jquery, array(), '2.0.3', true );
wp_enqueue_script( 'jquery' );


function demo_auth( $user, $username, $password ){
  // called by wp when someone posts to the login page
  error_log('demo_auth()');
  // Make sure a username and password are present for us to work with
  if($username == '' || $password == '') return;

  $ext_auth_url = plugin_dir_url(__FILE__) . "/ext_auth_demo_server.php?user=$username&pass=$password" ;

  error_log($ext_auth_url);

  $response = wp_remote_get( $ext_auth_url );

  $ext_auth = json_decode( $response['body'], true );

  if( $ext_auth['result'] == 0 ) {
    error_log('demo_auth() result 0');
    // User does not exist, send back an error message
    $user = new WP_Error( 'denied', __("<strong>ERROR</strong>: User/pass bad") );

  } else if( $ext_auth['result'] == 1 ) {
    error_log('demo_auth() result 1');
    // External user exists, try to load the user info from the WordPress user table
    $userobj = new WP_User();
    $user = $userobj->get_data_by( 'email', $ext_auth['email'] ); // Does not return a WP_User object <img src="http://ben.lobaugh.net/blog/wp-includes/images/smilies/icon_sad.gif" alt=":(" class="wp-smiley" />
    $user = new WP_User($user->ID); // Attempt to load up the user with that ID

    if( $user->ID == 0 ) {
      error_log('demo_auth() uid 0');
      // The user does not currently exist in the WordPress user table.
      // You have arrived at a fork in the road, choose your destiny wisely

      // If you do not want to add new users to WordPress if they do not
      // already exist uncomment the following line and remove the user creation code
      //$user = new WP_Error( 'denied', __("<strong>ERROR</strong>: Not a valid user for this system") );

      // Setup the minimum required user information for this example
      $userdata = array( 'user_email' => $ext_auth['email'],
        'user_login' => $ext_auth['email'],
        'first_name' => $ext_auth['first_name'],
        'last_name' => $ext_auth['last_name']
      );
      $new_user_id = wp_insert_user( $userdata ); // A new user has been created

      // Load the new user info
      $user = new WP_User ($new_user_id);
    }

  }

  // Comment this line if you wish to fall back on WordPress authentication
  // Useful for times when the external service is offline
  //remove_action('authenticate', 'wp_authenticate_username_password', 20);

  return $user;
} 


function ext_auth_demo_page() {


// <a href="/wp-login.php/?log=bob&pwd=asdf&rememberme=forever&wp-submit=Log+In&redirect_to=http%3A%2F%2Fdo.sudobob.com%2Fwp-admin%2F&testcookie=1" class="btn btn-success">Log Into Wordpress</a>
    
?>

  <button id="ext_auth_button" class="btn btn-success">Log Into Wordpress</button>


<? 


}
