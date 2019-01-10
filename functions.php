<?php
// UPDATED 1/10/2019 create a shortcode to produce a login form and control login and logout pages with error handling
function my_login_form_shortcode() {
        if ( is_user_logged_in() )
                return '';
              // return  wp_login_form();
    ?>
      <script>
        $("#wp-submit").click(function() {
          var user = $("input#user_login").val();
            if (user == "") {
            $("input#user_login").focus();
            return false;
          }
         });
  </script>
  <div class="wp_login_error">
    <?php if( isset( $_GET['login'] ) && (preg_match('/failed/', $_GET['login'] )) ) { ?>
        <p style="color:red;">The login is incorrect. Please try again.</p>
    <?php unset($_GET['login']); } 
    else if( isset( $_GET['login'] ) && $_GET['login'] == 'empty' ) { ?>
        <p style="color:red;">Please enter both username and password.</p>
    <?php } ?>
</div>  
    <link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri(); ?>/login-style.css" type="text/css" media="all">
    <div id="loginform">
			<form method="post" action="<?php bloginfo('url') ?>/wp-login.php" class="wp-user-form">
				<h2>Login to access this form</h2>
				<div>
					<input type="text" placeholder="UserID" required="" id="user_login" name="log" />
				</div>
				<div>
					<input type="password" placeholder="Password" required="" id="user_pass" name="pwd" />
				</div>
				<div>
					<input id="wp-submit" type="submit" value="Login" name="submit" />
				</div>
				<input type="hidden" name="redirect_to" value="<?php $req_uri =  esc_attr($_SERVER['REQUEST_URI']); 
				if (preg_match('/failed/', $req_uri)) {
				 $req_uri = substr($req_uri, 0, strpos($req_uri, '?')); echo $req_uri; } 
				 else { echo $req_uri; } ?>" />
				 
			</form>
			</div>
		<?php
            return $form;
            }

//use shortcode [my-login-form] to show the login on any page or post
function my_add_shortcodes() {
        add_shortcode( 'my-login-form', 'my_login_form_shortcode' );
}
add_action( 'init', 'my_add_shortcodes' );

add_action( 'wp_login_failed', 'my_front_end_login_fail' );  // hook failed login

//control login failures
function my_front_end_login_fail( $username ) {
   $referrer = $_SERVER['HTTP_REFERER'];  // where did the post submission come from?
   // if there's a valid referrer, and it's not the default log-in screen
   if ( !empty($referrer) && !strstr($referrer,'wp-login') && !strstr($referrer,'wp-admin') ) {
      wp_redirect( $referrer . '?login=failed' );  // let's append some information (login=failed) to the URL for the theme to use
      exit;
   }
}

//control where logout goes
function my_logout_redirect() {
$logouturl = esc_attr($_SERVER['HTTP_REFERER']);
    wp_redirect($logouturl);
    die;
}
add_action('wp_logout', 'my_logout_redirect', PHP_INT_MAX);