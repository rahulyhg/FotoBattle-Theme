<?php
/**
 * Extras
 * 
 * @package JobstairsVoting
 * @subpackage Voting_App
 * @since 1.0.0
 * 
 * Redirect subscriber accounts out of admin to homepage
 * Hide the admin bar for the subscribers
 * Disable self pingbacks
 * Redirect author page to homepage
 */

/**
 * Redirect subscriber accounts out of admin to homepage
 */
function binspired_redirect_subscriber_to_frontend() {
	$ourCurrentUser = wp_get_current_user();
	if (count($ourCurrentUser->roles) == 1 AND $ourCurrentUser->roles[0] == 'subscriber') {
		wp_redirect(site_url('/'));
		exit;
	}
}
add_action('admin_init', 'binspired_redirect_subscriber_to_frontend');

/**
 * Hide the admin bar for the subscribers
 */
function binspired_no_subscriber_admin_bar() {
	$ourCurrentUser = wp_get_current_user();
	if (count($ourCurrentUser->roles) == 1 AND $ourCurrentUser->roles[0] == 'subscriber') {
		show_admin_bar(false);
	}	
}
add_action('wp_loaded', 'binspired_no_subscriber_admin_bar');

/**
 * Disable self pingbacks
 */
function binspired_no_self_ping( &$links ) {
    $home = get_option( 'home' );
    foreach ( $links as $l => $link )
        if ( 0 === strpos( $link, $home ) )
            unset($links[$l]);
}
add_action( 'pre_ping', 'binspired_no_self_ping' );

/**
 * Redirect author page to homepage
 */
function binspired_author_link() {
	return site_url( '/' );
}
add_filter( 'author_link', 'binspired_author_link' );