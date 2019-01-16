<?php
/*
 * Plugin Name: Reader Refresh
 * Version: 1.0
 * Plugin URI: https://roosterglue.com/
 * Description: Automatically refresh your site's page
 * Author: Rooster Glue
 * Author URI: https://roosterglue.com/
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
	global $post;
	if ( is_page() || is_object( $post ) ) {
		//Get all options
		$enable_refresh = get_option('wpt_enable_refresh');
		if($enable_refresh){
			$continuous_refresh = get_option('wpt_continuous_refresh');
			$user_disable_refresh = get_option('wpt_user_disable_refresh');
			$random = get_option('wpt_random');
			$min = get_option('min_refresh');
			$max = get_option('max_refresh');
			$redirect = get_option('wpt_redirect');
			$specific_url = get_option('wpt_specific_url');
			$white_list = explode(", ",get_option('wpt_white_list'));
			$url = getURL($redirect, $specific_url, $white_list);
			$delay = $random ? rand($min, $max) : get_option('wpt_delay');
			$triggers = get_option('wpt_triggers') ? implode("','", get_option('wpt_triggers')) : '';
			$pop_title = get_option('wpt_pop_title') ?: "Hello";
			$pop_message = get_option('wpt_pop_message') ?: "You are about to be redirected";
			$pop_cancel = get_option('wpt_pop_cancel') ?: "Cancel";
			$pop_continue = get_option('wpt_pop_continue') ?: "Continue";
			$pop_font = get_option('wpt_pop_font') ?: "Monserrat";
			$pop_mobile = get_option('wpt_pop_mobile') ?: "90";
			$pop_max_width = get_option('wpt_pop_max_width') ?: "600";
			$pop_padding = get_option('wpt_pop_padding') ?: "20";
			$pop_bg_color = get_option('wpt_pop_bg_color') ?: "#FFFFFF";
			$pop_color = get_option('wpt_pop_color') ?: "#000000";
			$pop_button_bg_color = get_option('wpt_pop_button_bg_color') ?: "#000000";
			$pop_button_color = get_option('wpt_pop_button_color') ?: "#FFFFFF";
			$pop_show_count =  get_option('wpt_pop_show_count') ?: 'false';
			$pop_count =  get_option('wpt_pop_count') ?: 8;
			$pop_radius = get_option('wpt_pop_radius') ?: 15;
			$pop_border_width = get_option('wpt_pop_border_width') ?: 2;
			$pop_border_color = get_option('wpt_pop_border_color') ?: "#000000";
			$pop_button_border_width = get_option('wpt_pop_button_border_width') ?: 2;
			$pop_button_border_color = get_option('wpt_pop_button_border_color') ?: "#000000";
			$pop_button_hover_bg_color = get_option('wpt_pop_button_hover_bg_color') ?: "#000000";
			$pop_button_hover_color = get_option('wpt_pop_button_hover__color') ?: "#ffffff";
			$pop_button_hover_border_color = get_option('wpt_pop_button_hover_border_color') ?: "#000000";
			$pop_top = get_option('wpt_pop_top') ?: 'auto';
			$pop_bottom = get_option('wpt_pop_bottom') ?: 'auto';
			$transitionName = get_option('wpt_pop_transition') ?: 'top';
			$transitionTo = '50';
			$overlay =  get_option('wpt_overlay') ?: "#000000";
			$overlay_opacity =  get_option('wpt_overlay_opacity') ?: "90";

			if($transitionName === 'top'){
				 $transitionTo = $pop_top;
			}else if($transitionName === 'bottom'){
				$transitionTo = $pop_bottom;
			}

			if($continuous_refresh){
				$url = $url. '?rr=true';
			}


			//Create script bassed of options
			$scriptOutput = '<script>
						(function () {
								var t;
								var l;
								var m;
								var ct = '.$pop_count.';
								var c = false;
								if(window.location.href.indexOf("?rr=true") > -1 || window.location.href.indexOf("rr") > -1){
									var triggers = [\''.$triggers.'\'];

									// window onload trigger always added so the plugin fires.
									window.onload = resetTimer;

									// Add triggers based off of plugin selections
									if(triggers.indexOf("click") !== -1){
										document.onmousedown = resetTimer; // touchscreen presses
										document.onclick = resetTimer;     // touchpad clicks
									}
									if(triggers.indexOf("scroll") !== -1){
										document.onscroll = resetTimer;    // scrolling with arrow keys
									}
									if(triggers.indexOf("keypress") !== -1){
										document.onkeypress = resetTimer;
									}
									if(triggers.indexOf("touch") !== -1){
										document.ontouchstart = resetTimer;
									}
								}
								var canRedirect = function(){
									clearTimeout(t);
									clearTimeout(l);
									window.location = "'.$url.'";
								}
								var clearRefresh = function(){
									var pop = document.getElementById("refresh-pop-up-shell");
									if(pop){
										pop.outerHTML = "";
										delete pop;
									}
									window.clearTimeout(t);
									window.clearTimeout(l);
									t = null;
									l = null;
									c = true;
									if(m){
										window.clearTimeout(m);
										m = null;
									}
								}
								var countdown = function(){
										ct--;
										window.clearTimeout(m);
										if(ct > -1){
											document.getElementById("pop-count").innerHTML = ct;
											m = setTimeout(countdown, 1000);
										}
								}
								function hexToRgb(hex) {
									var arrBuff = new ArrayBuffer(4);
								  var vw = new DataView(arrBuff);
								  vw.setUint32(0,parseInt(hex, 16),false);
								  var arrByte = new Uint8Array(arrBuff);

								  return arrByte[1] + "," + arrByte[2] + "," + arrByte[3];
								}

								function addPopup(url){
									var showCount = "'.$pop_show_count.'";
									var count = showCount ? \'<h2 id="pop-count">\'+ ct +\'</h2>\' : "";
									var rgb = hexToRgb("'.$overlay.'");
									var opacity = '.$overlay_opacity.' / 100;
									var overlay = "rgba("+rgb+", "+opacity+")";
									var popup = \'<div class="refresh-pop-up">\' +
															\' <h1>'.$pop_title.'</h1>\' +
															\' <p>'.$pop_message.'</p>\' +
																 count +
															\' <button style="background-color:'.$pop_button_bg_color.'; \' +
															 								\' color:'.$pop_button_color.'; \' +
															 								\' border:'.$pop_button_border_width.'px solid '.$pop_button_border_color.' ;"\' +
																							\' id="cancel-refresh">'.$pop_cancel.'</button>\' +
															 \' <button style="background-color:'.$pop_button_bg_color.';\' +
															 								\' border:'.$pop_button_border_width.'px solid '.$pop_button_border_color.' ;"\' +
																							\' color:'.$pop_button_color.';\' +
																							\' id="continue-refresh">'.$pop_continue.'</button</div>\';
									var spn = document.createElement("span");
									var style = document.createElement("style");
									spn.innerHTML = popup;
									spn.style.background = overlay;
									spn.id = "refresh-pop-up-shell";
									var styleSheet = \'.refresh-pop-up button:hover{background-color: '.$pop_button_hover_bg_color.' !important; color:'.$pop_button_hover_color.'!important;border-color:'.$pop_button_hover_border_color.'!important;} \' +
																	\'.refresh-pop-up {animation-name: slide;\' +
																					\' font-family:'.$pop_font.';\' +
		 													 						\'	width:'.$pop_mobile.'%;\' +
		 																			\'	max-width:'.$pop_max_width.'px;\' +
		 																			\'	background-color:'.$pop_bg_color.';\' +
		 																			\'	color:'.$pop_color.';\' +
		 																			\'	border-radius:'.$pop_radius.'px;\' +
		 																			\'	padding:'.$pop_padding.'px;\' +
		 																			\'	border:'.$pop_border_width.'px solid '.$pop_border_color.' ;\' +
																					\'  top: '.$pop_top. ($pop_top != 'auto' ? '%' : '') . '; \' +
																					\'  bottom: '.$pop_bottom. ($pop_bottom != 'auto' ? '%' : '') . '; \' +
		 																			\'	'.$transitionName.': -100%;\' +
																	\' }\'+
																	\'@-webkit-keyframes slide {\' +
																	    \' from {'.$transitionName.' : -100%;}\'+
																			\' to {'.$transitionName.' : '.$transitionTo. ($transitionTo != 'auto' ? '%' : '') . ';}\'+
																	\'}\'+
																	\'@keyframes slide {\' +
																		    \' from {'.$transitionName.' : -100%; }\'+
																				\' to {'.$transitionName.' : '.$transitionTo. ($transitionTo != 'auto' ? '%' : '') . ';}\'+
																		\'}\'
									style.innerHTML = styleSheet;
									document.getElementsByTagName("head")[0].appendChild(style);
									document.getElementsByTagName("body")[0].appendChild(spn);
									// document.getElementById("page").appendChild(spn);

									if(spn){
										document.getElementById("cancel-refresh").onclick = clearRefresh;
										document.getElementById("continue-refresh").onclick = canRedirect;
									}

									l = setTimeout(canRedirect, ('.$pop_count.' * 1000));
									if(showCount){
										m = setTimeout(countdown, 1000);
									}
								}
								function addCSSRule(sheet, selector, rules, index) {
									if("insertRule" in sheet) {
										sheet.insertRule(selector + "{" + rules + "}", index);
									}
									else if("addRule" in sheet) {
										sheet.addRule(selector, rules, index);
									}
								}
								function redirect() {
									 var userDisable = "' . $user_disable_refresh  . '";
										if(userDisable){
												addPopup();
										}else{
											window.location = "'. $url .'";
										}
								}

								function resetTimer() {
										if(!c){
											clearTimeout(t);
											t = setTimeout(redirect, ('. $delay . ' * 1000));
										}
								}
							})();
						</script>';
			echo $scriptOutput;
		}
	}
}
add_action( 'wp_head', 'my_custom_redirect' );
