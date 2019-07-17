<?php
/**
 * Security
 * 
 * @package JobstairsVoting
 * @subpackage Voting_App
 * @since 1.0.0
 * 
 * 
 * Remove version from WordPress
 * Remove version from RSS
 * Remove version from scripts and styles
 * Show me the default REST API endpoints
 * Hide me the default REST API endpoints
 * Hide me the default REST API endpoints but show custom ones
 * Customizing the REST URL prefix
 */

/**
 * Remove version from WordPress
 */
remove_action('wp_head', 'wp_generator');

/**
 * Remove version from RSS
 */
add_filter('the_generator', '__return_empty_string');

/**
 * Remove version from scripts and styles
 */
function jobstairs_remove_version($src) {
	if (strpos($src, 'ver=')) {
		$src = remove_query_arg('ver', $src);
	}
	return $src;
}
add_filter('style_loader_src', 'jobstairs_remove_version', 9999);
add_filter('script_loader_src', 'jobstairs_remove_version', 9999);

/**
 * Show me the default REST API endpoints
 */
function show_default_endpoints( $endpoints ) {
  var_export( array_keys( $endpoints ) );
  die;
}
//add_filter( 'rest_endpoints', 'show_default_endpoints' );

/**
 * Hide me the default REST API endpoints
 */
function remove_default_endpoints( $endpoints ) {
  return array( );
}
// add_filter( 'rest_endpoints', 'remove_default_endpoints' );

/**
 * Hide me the default REST API endpoints but show custom ones
 * https://wpreset.com/remove-default-wordpress-rest-api-routes-endpoints/
 */ 
function remove_default_endpoints_smarter( $endpoints ) {
  $prefix = 'voting';
 
  foreach ( $endpoints as $endpoint => $details ) {
    if ( !fnmatch( '/' . $prefix . '/*', $endpoint, FNM_CASEFOLD ) ) {
      unset( $endpoints[$endpoint] );
    }
  }
 
  return $endpoints;
}
add_filter( 'rest_endpoints', 'remove_default_endpoints_smarter' );

 /**
 * Customizing the REST URL prefix
 * https://wpreset.com/remove-default-wordpress-rest-api-routes-endpoints/
 */ 
function rest_url_prefix( ) {
  return 'api';
}
add_filter( 'rest_url_prefix', 'rest_url_prefix' );