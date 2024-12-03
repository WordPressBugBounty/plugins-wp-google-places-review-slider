<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://wpreviewslider.com
 * @since             1.0
 * @package           WP_Google_Places_Reviews
 *
 * @wordpress-plugin
 * Plugin Name:       WP Google Review Slider
 * Plugin URI:        https://wpreviewslider.com/
 * Description:       Embed Google reviews on your WordPress site in seconds. Boost SEO, trust, and sales! 
 * Version:           15.7
 * Author:            LJ Apps
 * Author URI:        https://wpreviewslider.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wp-google-reviews
 * Domain Path:       /languages
 */
 
if ( ! function_exists( 'wgprs_fs' ) ) {
    // Create a helper function for easy SDK access.
    function wgprs_fs() {
        global $wgprs_fs;

        if ( ! isset( $wgprs_fs ) ) {
            // Include Freemius SDK.
            require_once dirname(__FILE__) . '/freemius/start.php';

            $wgprs_fs = fs_dynamic_init( array(
                'id'                  => '10974',
                'slug'                => 'wp-google-places-review-slider',
                'type'                => 'plugin',
                'public_key'          => 'pk_1269fa90e55f76af9a72660d9979a',
                'is_premium'          => false,
                'has_addons'          => false,
                'has_paid_plans'      => false,
                'menu'                => array(
                    'slug'           => 'wp_google-welcome',
                ),
            ) );
        }

        return $wgprs_fs;
    }

    // Init Freemius.
    wgprs_fs();
    // Signal that SDK was initiated.
    do_action( 'wgprs_fs_loaded' );
}

 
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'WPREV_GOOGLE_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'WPREV_GOOGLE_PLUGIN_URL', plugins_url( '', __FILE__ ) );


/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wp-google-reviews-activator.php
 */
function activate_WP_Google_Reviews($networkwide) {
	//save time activated
	$newtime=time();
	update_option( 'wprev_activated_time_google', $newtime );
	
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-google-reviews-activator.php';
	WP_Google_Reviews_Activator::activate_all($networkwide);
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wp-google-reviews-deactivator.php
 */
function deactivate_WP_Google_Reviews() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-google-reviews-deactivator.php';
	WP_Google_Reviews_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_WP_Google_Reviews' );
register_deactivation_hook( __FILE__, 'deactivate_WP_Google_Reviews' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wp-google-reviews.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_WP_Google_Reviews() {
	define( 'wpgooglerev_plugin_dir', plugin_dir_path( __FILE__ ) );
	define( 'wpgooglerev_plugin_url', plugins_url( "",__FILE__) );
	
	
	// Not like register_uninstall_hook(), you do NOT have to use a static function.
    wgprs_fs()->add_action('after_uninstall', 'wgprs_fs_uninstall_cleanup');
	
	
	$plugin = new WP_Google_Reviews();
	$plugin->run();

}
//uninstall cleanup.
function wgprs_fs_uninstall_cleanup()
{
	
			// Leave no trail
		$option1 = 'widget_wprev_widget';
		$option2 = 'wp-google-reviews_version';
		$option3 = 'wpfbr_options';
		$option4 = 'wpfbr_fb_app_id';
		$option5 = 'wprev_notice_hide_google';
		$option6 = 'wprev_activated_time_google';
		
		
	//================
	//check for pro version, if yes then do not delete this stuff
$filename = plugin_dir_path( __DIR__ ).'/wp-review-slider-pro-premium/wp-review-slider-pro.php';
$filename2 = plugin_dir_path( __DIR__ ).'/wp-review-slider-pro/wp-review-slider-pro.php';

	if ( is_plugin_active( 'wp-review-slider-pro-premium/wp-review-slider-pro.php' ) || file_exists($filename)) {
		//pro version is installed and activated do not delete tables
		
	} else if ( is_plugin_active( 'wp-review-slider-pro/wp-review-slider-pro.php' ) || file_exists($filename2)) {
		//pro version is installed and activated do not delete tables
		
	} else {
	
		//pro version not installed, okay to delete tables
		if ( !is_multisite() ) 
		{
			delete_option( $option1 );
			delete_option( $option2 );
			delete_option( $option3 );
			delete_option( $option4 );
			delete_option( $option5);
			delete_option( $option6 );
			
			//delete review table in database
			global $wpdb;

			$table_name = $wpdb->prefix . 'wpfb_reviews';
			
			$wpdb->query( "DROP TABLE IF EXISTS $table_name" );
		
			//drop review template table 
			$table_name = $wpdb->prefix . 'wpfb_post_templates';
			
			$wpdb->query( "DROP TABLE IF EXISTS $table_name" );
		} 
		else 
		{
			global $wpdb;
			$blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
			$original_blog_id = get_current_blog_id();

			foreach ( $blog_ids as $blog_id ) 
			{
				switch_to_blog( $blog_id );
				delete_option( $option1 );
				delete_option( $option2 );
				delete_option( $option3 );
				delete_option( $option4 );
				delete_option( $option5);
			delete_option( $option6 );
			
				$table_name = $wpdb->prefix . 'wpfb_reviews';
			
				$wpdb->query( "DROP TABLE IF EXISTS $table_name" );
			
				//drop review template table 
				$table_name = $wpdb->prefix . 'wpfb_post_templates';
				
				$wpdb->query( "DROP TABLE IF EXISTS $table_name" );

				// OR
				// delete_site_option( $option_name );  
			}

			switch_to_blog( $original_blog_id );
		}
	}
	
}

//add link to change log on plugins menu
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'wprevgoo_action_links' );
function wprevgoo_action_links( $links )
{
    $links[] = '<a href="https://wpreviewslider.com/" target="_blank"><strong style="color: #009040; display: inline;">Go Pro!</strong></a>';
    return $links;
}
run_WP_Google_Reviews();