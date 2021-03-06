<?php
/*
Plugin Name: Commentpress for Multisite
Version: 1.0.1
Plugin URI: http://www.futureofthebook.org/commentpress/
Description: This plugin integrates Commentpress in a WordPress Multisite environment. It can be used alone or in conjunction with BuddyPress and BP Groupblog, depending on your needs. <strong>Note: this plugin must be Network Activated.</strong>
Author: Institute for the Future of the Book
Author URI: http://www.futureofthebook.org
Network: true
*/





// define version
define( 'CPMU_PLUGIN_VERSION', '1.0' );

// store reference to this file
if ( !defined( 'CPMU_PLUGIN_FILE' ) ) {
	define( 'CPMU_PLUGIN_FILE', __FILE__ );
}

// store URL to this plugin's directory
if ( !defined( 'CPMU_PLUGIN_URL' ) ) {
	define( 'CPMU_PLUGIN_URL', plugin_dir_url( CPMU_PLUGIN_FILE ) );
}
// store PATH to this plugin's directory
if ( !defined( 'CPMU_PLUGIN_PATH' ) ) {
	define( 'CPMU_PLUGIN_PATH', plugin_dir_path( CPMU_PLUGIN_FILE ) );
}





/*
----------------------------------------------------------------
Init plugin
----------------------------------------------------------------
*/

// do we have our class?
if ( !class_exists( 'CommentPressMultiSiteLoader' ) ) {

	// define filename
	$class_file = 'class_commentpress_mu_loader.php';

	// get path
	$class_file_path = cpmu_file_is_present( $class_file );
	
	// we're fine, include class definition
	require_once( $class_file_path );

	// define as global
	global $cpmu_obj;

	// instantiate it
	$cpmu_obj = new CommentPressMultiSiteLoader;
	
}





/*
--------------------------------------------------------------------------------
Misc Utility Functions
--------------------------------------------------------------------------------
*/

/** 
 * @description: utility to check for presence of vital files
 * @param string $filename the name of the Commentpress Plugin file
 * @return string $filepath absolute path to file
 * @todo: 
 *
 */
function cpmu_file_is_present( $filename ) {

	// define path to our requested file
	$filepath = CPMU_PLUGIN_PATH . $filename;

	// is our class definition present?
	if ( !is_file( $filepath ) ) {
	
		// oh no!
		die( 'File "'.$filepath.'" is missing from the plugin directory.' );
	
	}
	
	
	
	// --<
	return $filepath;

}







/** 
 * @description: get WP plugin reference by name (since we never know for sure what the enclosing
 * directory is called)
 * @todo: 
 *
 */
function cpmu_find_plugin_by_name( $plugin_name = '' ) {

	// kick out if no param supplied
	if ( $plugin_name == '' ) { return false; }



	// init path
	$path_to_plugin = false;
	
	// get plugins
	$plugins = get_plugins();
	//print_r( $plugins ); die();
	
	// because the key is the path to the plugin file, we have to find the
	// key by iterating over the values (which are arrays) to find the
	// plugin with the name Commentpress. Doh!
	foreach( $plugins AS $key => $plugin ) {
	
		// is it ours?
		if ( $plugin['Name'] == $plugin_name ) {
		
			// now get the key, which is our path
			$path_to_plugin = $key;
			break;
		
		}
	
	}
	
	
	
	// --<
	return $path_to_plugin;
	
}





/*
--------------------------------------------------------------------------------
Force a plugin to activate: adapted from https://gist.github.com/1966425
Audited with reference to activate_plugin() with extra commenting inline
--------------------------------------------------------------------------------
*/

/** 
 * @description: Helper to activate a plugin on another site without causing a 
 * fatal error by including the plugin file a second time
 * Based on activate_plugin() in wp-admin/includes/plugin.php
 * $buffer option is used for plugins which send output
 * @todo: 
 *
 */
function cpmu_activate_plugin( $plugin, $buffer = false ) {
	
	// find our already active plugins
	$current = get_option( 'active_plugins', array() );
	
	// no need to validate it...
	
	// check that the plugin isn't already active
	if ( !in_array( $plugin, $current ) ) {
	
		// no need to redirect...
	
		// open buffer if required
		if ( $buffer ) { ob_start(); }
		
		// safe include
		// Note: this a valid use of WP_PLUGIN_DIR since there is no plugins_dir()
		include_once( WP_PLUGIN_DIR . '/' . $plugin );
		
		// no need to check silent activation, just go ahead...
		do_action( 'activate_plugin', $plugin );
		do_action( 'activate_' . $plugin );
		
		// housekeeping
		$current[] = $plugin;
		sort( $current );
		update_option( 'active_plugins', $current );
		do_action( 'activated_plugin', $plugin );
		
		// close buffer if required
		if ( $buffer ) { ob_end_clean(); }

	}

}






/** 
 * @description: utility to show theme environment
 * @todo: 
 *
 */
function _cpmu_environment() {
	
	// don't show in admin
	if ( !is_admin() ) {
		
		// dump our environment
		echo '<strong>TEMPLATEPATH</strong><br />'.TEMPLATEPATH.'<br /><br />';
		echo '<strong>STYLESHEETPATH</strong><br />'.STYLESHEETPATH.'<br /><br />';
		echo '<strong>template_directory</strong><br />'.get_bloginfo('template_directory').'<br /><br />';	
		echo '<strong>stylesheet_directory</strong><br />'.get_bloginfo('stylesheet_directory').'<br /><br />';
		echo '<strong>template_url</strong><br />'.get_bloginfo('template_url').'<br /><br />';	
		echo '<strong>stylesheet_url</strong><br />'.get_bloginfo('stylesheet_url').'<br /><br />';
		echo '<strong>get_template_directory</strong><br />'.get_template_directory().'<br /><br />';
		echo '<strong>get_stylesheet_directory</strong><br />'.get_stylesheet_directory().'<br /><br />';
		echo '<strong>get_stylesheet_directory_uri</strong><br />'.get_stylesheet_directory_uri().'<br /><br />';
		echo '<strong>get_template_directory_uri</strong><br />'.get_template_directory_uri().'<br /><br />';
		echo '<strong>locate_template</strong><br />'.locate_template( array( 'style/js/cp_js_common.js' ), false ).'<br /><br />';
		die();
	
	}
	
}

//add_action( 'template_redirect', '_cpmu_environment' );






/** 
 * @description: utility to show tests
 * @todo: 
 *
 */
function _cpmu_test() {

	global $commentpress_obj;
	//print_r( $commentpress_obj ); die();
	
}

//add_action( 'wp_head', '_cpmu_test' );





