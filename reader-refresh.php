<?php
/*
 * Plugin Name: Reader Refresh
 * Version: 1.0
 * Plugin URI: http://www.google.com/
 * Description: Automatically refresh your site's page
 * Author: Rooster Glue
 * Author URI: http://www.google.com/
 * Requires at least: 4.0
 * Tested up to: 4.0
 *
 * Text Domain: reader-refresh
 * Domain Path: /lang/
 *
 * @package WordPress
 * @author Rooster Glue
 * @since 1.0.0
 */



if ( ! defined( 'ABSPATH' ) ) exit;

// Load plugin class files
require_once( 'includes/class-reader-refresh.php' );
require_once( 'includes/class-reader-refresh-settings.php' );

// Load plugin libraries
require_once( 'includes/lib/class-reader-refresh-admin-api.php' );
require_once( 'includes/lib/class-reader-refresh-post-type.php' );
require_once( 'includes/lib/class-reader-refresh-taxonomy.php' );

/**
 * Returns the main instance of Reader_Refresh to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return object Reader_Refresh
 */
function Reader_Refresh () {
	$instance = Reader_Refresh::instance( __FILE__, '1.0.0' );

	if ( is_null( $instance->settings ) ) {
		$instance->settings = Reader_Refresh_Settings::instance( $instance );
	}

	return $instance;
}

Reader_Refresh();


function getURL($redirect, $specific_url, $white_list){

	switch ($redirect) {
    case 'same':
				global $wp;
        return home_url( $wp->request );
        break;
    case 'specific':
        return $specific_url;
        break;
    case 'random':
        return getRandomURL();
        break;
		case 'list':
        return $white_list[rand(0, (count($white_list) - 1))];
        break;
	}

}

function getRandomURL(){
	// set arguments for WP_Query on published posts to get 1 at random
	$args = array(
	    'post_type' => 'post',
	    'post_status' => 'publish',
	    'posts_per_page' => 1,
	    'orderby' => 'rand'
	);

	$my_random_post = new WP_Query ( $args );

	while ( $my_random_post->have_posts () ) {
	  $my_random_post->the_post ();

	  // redirect to the random post
	  return get_permalink ();
	}
}


function my_custom_redirect () {
		//Get all options
		$continuous_refresh = get_option('wpt_continuous_refresh');
		$user_disable_refresh = get_option('wpt_user_disable_refresh');
		$delay = get_option('wpt_delay');
		$random = get_option('wpt_random');
		$redirect = get_option('wpt_redirect');
		$specific_url = get_option('wpt_specific_url');
		$white_list = explode(", ",get_option('wpt_white_list'));
		$url = getURL($redirect, $specific_url, $white_list);

		//Create script bassed of options
		$scriptOutput = '<script>
					(function () {
							var t;
							window.onload = resetTimer;
							/*
								Possible DOM Events
								document.onload = resetTimer;
								document.onmousemove = resetTimer;
								document.onmousedown = resetTimer; // touchscreen presses
								document.ontouchstart = resetTimer;
								document.onclick = resetTimer;     // touchpad clicks
								document.onscroll = resetTimer;    // scrolling with arrow keys
								document.onkeypress = resetTimer;
							*/

							function redirect() {
								 var userDisable = "' . $user_disable_refresh  . '";
									if(userDisable){
											if(confirm("You are being redirected!")){
												  clearTimeout(t);
													window.location = "'. $url .'";
											}
									}else{
										window.location = "'. $url .'";
									}
							}

							function resetTimer() {
									clearTimeout(t);
									t = setTimeout(redirect, ('. $delay . ' * 1000));
							}
						})();
					</script>';
		echo $scriptOutput;
		debug_to_console($url);

}
add_action( 'get_header', 'my_custom_redirect' );
