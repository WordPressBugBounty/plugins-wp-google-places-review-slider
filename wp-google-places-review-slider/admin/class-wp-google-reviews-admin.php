<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    WP_Google_Reviews
 * @subpackage WP_Google_Reviews/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    WP_Google_Reviews
 * @subpackage WP_Google_Reviews/admin
 * @author     Your Name <email@example.com>
 */
class WP_Google_Reviews_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugintoken    The ID of this plugin.
	 */
	private $plugintoken;
	private $_token;
	private $_default_api_token;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugintoken       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugintoken, $version ) {

		$this->_token = $plugintoken;
		$this->_default_api_token = "";
		//$this->version = $version;
		//for testing==============
		$this->version = time();
		//===================
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in WP_Google_Reviews_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The WP_Google_Reviews_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		//only load for this plugin
		if(isset($_GET['page'])){
			if( strpos( $_GET['page'], "wp_google-" ) !== false ){
				//wp_enqueue_style( $this->_token."_wprev_fonta", plugin_dir_url( __FILE__ ) . 'css/font-awesome.min.css', array(), '4.7.0', 'all' );
				
				wp_register_style( 'Font_Awesome', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css' );
			wp_enqueue_style('Font_Awesome');
				
				wp_enqueue_style( $this->_token."_wprev_w3", plugin_dir_url( __FILE__ ) . 'css/wprev_w3.css', array(), $this->version, 'all' );
				
				wp_enqueue_style( $this->_token, plugin_dir_url( __FILE__ ) . 'css/wprev_admin.css', array(), $this->version, 'all' );
			}
			
			//load template styles for wp_pro-templates_posts page
			if($_GET['page']=="wp_google-templates_posts" || $_GET['page']=="wp_google-get_pro" || $_GET['page']=="wp_google-welcome"){
				//enque template styles for preview
				wp_enqueue_style( $this->_token."_style1", plugin_dir_url(dirname(__FILE__)) . 'public/css/wprev-public_combine.css', array(), $this->version, 'all' );
				
				//also load javascript for creating preview.
				wp_enqueue_script( $this->_token."_plublic_comb", plugin_dir_url(dirname(__FILE__)) . 'public/js/wprev-public-com-min.js', array( 'jquery' ), $this->version, true );

				wp_localize_script($this->_token."_plublic_comb", 'wprevpublicjs_script_vars', 
					array(
					'wpfb_nonce'=> wp_create_nonce('randomnoncestring'),
					'wpfb_ajaxurl' => admin_url( 'admin-ajax.php' ),
					'wprevpluginsurl' => WPREV_GOOGLE_PLUGIN_URL
					)
				);
				

			}
			//review list
			if($_GET['page']=="wp_google-reviews"){
				wp_enqueue_style( $this->_token."lity_min", plugin_dir_url( __FILE__ ) . 'css/lity.min.css', array(), $this->version, 'all' );
			}
		}

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in WP_Google_Reviews_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The WP_Google_Reviews_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		

		//scripts for all pages in this plugin
		if(isset($_GET['page'])){
			if( strpos( $_GET['page'], "wp_google-" ) !== false ){
				//pop-up script
				wp_register_script( 'simple-popup-js',  plugin_dir_url( __FILE__ ) . 'js/wprev_simple-popup.min.js' , '', $this->version, false );
				wp_enqueue_script( 'simple-popup-js' );
			}
		}

		//scripts for get google reviews page
		if(isset($_GET['page'])){
			if($_GET['page']=="wp_google-googlesettings" || $_GET['page']=="wp_google-googleplacesapi"){
				$options = get_option('wpfbr_google_options');
				$default_key = "";
				
				if(isset($options)){
					if(empty( $options['google_api_key'] )){
						$google_api_key = $default_key;
					} 
					if (isset($options['select_google_api']) && $options['select_google_api']=="mine"){
						$google_api_key =$options['google_api_key'];
					} else {
						$google_api_key = $default_key;
					}
				} else {
					$google_api_key = $default_key;
				}
				
				
				if( ! empty($google_api_key ) )
				{
				wp_register_script( 'wpfbr_google_places_gmaps', 'https://maps.googleapis.com/maps/api/js?v=3.exp&libraries=places&key=' . esc_attr( $google_api_key ), array( 'jquery' ) );
				wp_enqueue_script( 'wpfbr_google_places_gmaps' );
				}

				//admin js
				wp_enqueue_script( $this->_token, plugin_dir_url( __FILE__ ) . 'js/wprev_getgoogle.js', array( 'jquery' ), $this->version, false );
				//used for ajax
				wp_localize_script($this->_token, 'adminjs_script_vars', 
					array(
						'wpfb_nonce'=> wp_create_nonce('randomnoncestring'),
						'ajax_url' => admin_url( 'admin-ajax.php' ),
						'i18n'     => array( 'google_auth_error' => 
						sprintf( 
						__( '%1$sGoogle API Error:%2$s Wrong Key or Maps API not added. Due to recent changes by Google you must now add the Maps API to your existing API key in order to use the Location Lookup feature of the Google Places Widget. %3$sView documentation here%4$s', 'wp-google-reviews' ), 
						'<strong>', 
						'</strong>', 
						'<br><a href="https://ljapps.com/google-places-api-key/" target="_blank" class="new-window">', 
						'</a>' 
						) ) 
					)
				);
				
			}
		}
		
		//scripts for get fb reviews page
		/*
		if(isset($_GET['page'])){
			if($_GET['page']=="wp-google-reviews"){
				//admin js
				wp_enqueue_script( $this->_token, plugin_dir_url( __FILE__ ) . 'js/wprev_admin.js', array( 'jquery' ), $this->version, false );
				//used for ajax
				wp_localize_script($this->_token, 'adminjs_script_vars', 
					array(
					'wpfb_nonce'=> wp_create_nonce('randomnoncestring')
					)
				);
			}
		}
		*/
		
		//scripts for review list page
		if(isset($_GET['page'])){
			if($_GET['page']=="wp_google-reviews"){
				global $wpdb;
				$reviews_table_name = $wpdb->prefix . 'wpfb_reviews';
				$tempquery = "select pagename from ".$reviews_table_name." WHERE type='Google' group by pagename ";
				$pagenamearray = $wpdb->get_col($tempquery);
				
				
				//admin js
				wp_enqueue_script('review_list_page-js', plugin_dir_url( __FILE__ ) . 'js/review_list_page.js', array( 'jquery' ), $this->version, false );
				
				//used for ajax
				wp_localize_script('review_list_page-js', 'adminjs_script_vars', 
					array(
					'wpfb_nonce'=> wp_create_nonce('randomnoncestring'),
					'wpfb_ajaxurl' => admin_url( 'admin-ajax.php' ),
					'pagenamearray' => json_encode($pagenamearray),
					)
				);
				
				//for media popup
				wp_enqueue_script('wpgoo_lity-js', plugin_dir_url( __FILE__ ) . 'js/lity.min.js', array( 'jquery' ), $this->version, false );
				
				wp_enqueue_script('thickbox');
				wp_enqueue_style('thickbox');
		 
				wp_enqueue_script('media-upload');
				wp_enqueue_script('wptuts-upload');
			}
			
			//script for crawl page
			if($_GET['page']=="wp_google-googlecrawl" || $_GET['page']=="wp_google-googlesettings"){
				wp_enqueue_script( $this->_token.'getgoogle_crawl-js', plugin_dir_url( __FILE__ ) . 'js/wprev_getgoogle_crawl.js', array( 'jquery' ), $this->version, false );
				//used for ajax
				wp_localize_script($this->_token.'getgoogle_crawl-js', 'adminjs_script_vars', 
					array(
						'wpfb_nonce'=> wp_create_nonce('randomnoncestring'),
						'ajax_url' => admin_url( 'admin-ajax.php' )
					)
				);

			}
			
			//scripts for templates posts page
			if($_GET['page']=="wp_google-templates_posts"){
				//admin js
				wp_enqueue_script('templates_posts_page-js', plugin_dir_url( __FILE__ ) . 'js/templates_posts_page.js', array( 'jquery' ), $this->version, false );
				wp_localize_script('templates_posts_page-js', 'adminjs_script_vars', 
					array(
					'wpfb_nonce'=> wp_create_nonce('randomnoncestring'),
					'pluginsUrl' => wpgooglerev_plugin_url
					)
				);
				//add color picker here
				wp_enqueue_style( 'wp-color-picker' );
				//enque alpha color add-on wprevpro-wp-color-picker-alpha.js
				wp_enqueue_script( 'wp-color-picker-alpha', plugin_dir_url( __FILE__ ) . 'js/wprevpro-wp-color-picker-alpha.js', array( 'wp-color-picker' ), '3.0.0', false );
				
			}
		}
		
	}
	
	public function add_menu_pages() {

		/**
		 * adds the menu pages to wordpress
		 */

		$page_title = 'WP Google Reviews: Welcome';
		$menu_title = 'WP Google Reviews';
		$capability = 'manage_options';
		$menu_slug = 'wp_google-welcome';
		
		add_menu_page($page_title, $menu_title, $capability, $menu_slug, array($this,'wp_fb_welcome'),'dashicons-star-half');
		
		// We add this submenu page with the same slug as the parent to ensure we don't get duplicates
		$sub_menu_title = 'Welcome';
		add_submenu_page($menu_slug, $page_title, $sub_menu_title, $capability, $menu_slug, array($this,'wp_fb_welcome'));		
		
		// Now add the submenu page
		$submenu_page_title = 'WP Google Reviews: Get Google Reviews';
		$submenu_title = 'Get Google Reviews';
		$submenu_slug = 'wp_google-googlesettings';
		add_submenu_page($menu_slug, $submenu_page_title, $submenu_title, $capability, $submenu_slug, array($this,'wp_fb_googlesettings'));
		
		// Now add the submenu page
		$submenu_page_title = 'WP Google Reviews: Google Places API';
		$submenu_title = 'Google API';
		$submenu_slug = 'wp_google-googleplacesapi';
		add_submenu_page('', $submenu_page_title, $submenu_title, $capability, $submenu_slug, array($this,'wp_fb_googleplacesapi'));
		
		// Now add the submenu page
		$submenu_page_title = 'WP Google Reviews: Crawl Google';
		$submenu_title = 'Google Crawl';
		$submenu_slug = 'wp_google-googlecrawl';
		add_submenu_page('', $submenu_page_title, $submenu_title, $capability, $submenu_slug, array($this,'wp_fb_googlecrawl'));
		
		
		// Now add the submenu page for the actual reviews list
		$submenu_page_title = 'WP Google Reviews: Review List';
		$submenu_title = 'Review List';
		$submenu_slug = 'wp_google-reviews';
		add_submenu_page($menu_slug, $submenu_page_title, $submenu_title, $capability, $submenu_slug, array($this,'wp_fb_reviews'));
		
		// Now add the submenu page for the reviews templates
		$submenu_page_title = 'WP Google Reviews: Templates';
		$submenu_title = 'Templates';
		$submenu_slug = 'wp_google-templates_posts';
		add_submenu_page($menu_slug, $submenu_page_title, $submenu_title, $capability, $submenu_slug, array($this,'wp_fb_templates_posts'));
		
		// Now add the submenu page for the reviews templates
		//$submenu_page_title = 'WP Google Reviews: Upgrade';
		//$submenu_title = 'Get Pro';
		//$submenu_slug = 'wp_google-get_pro';
		//add_submenu_page($menu_slug, $submenu_page_title, $submenu_title, $capability, $submenu_slug, array($this,'wp_fb_getpro'));
	}
	
	public function wp_fb_settings() {
		require_once plugin_dir_path( __FILE__ ) . '/partials/settings.php';
	}

	public function wp_fb_reviews() {
		require_once plugin_dir_path( __FILE__ ) . '/partials/review_list.php';
	}
	public function wp_fb_googlesettings() {
		require_once plugin_dir_path( __FILE__ ) . '/partials/googlechoose.php';
	}
	public function wp_fb_googleplacesapi() {
		require_once plugin_dir_path( __FILE__ ) . '/partials/googleapi.php';
	}
	public function wp_fb_googlecrawl() {
		require_once plugin_dir_path( __FILE__ ) . '/partials/googlecrawl.php';
	}
	public function wp_fb_welcome() {
		require_once plugin_dir_path( __FILE__ ) . '/partials/welcome.php';
	}

	public function wp_fb_templates_posts() {
		require_once plugin_dir_path( __FILE__ ) . '/partials/templates_posts.php';
	}
	public function wp_fb_getpro() {
		require_once plugin_dir_path( __FILE__ ) . '/partials/get_pro.php';
	}
	
	
	public function wprev_google_add_external_link_admin_submenu() {
		global $submenu;

		$menu_slug = 'wp_google-welcome'; // used as "key" in menus
		$menu_pos = 1; // whatever position you want your menu to appear
		if (array_key_exists($menu_slug, $submenu)) {
			// add the external links to the slug you used when adding the top level menu
			$submenu[$menu_slug][] = array('<div id="wprev-66022">Go Pro!</div>', 'manage_options', 'https://wpreviewslider.com/');
		}
	}
	public function wpse_66022_add_jquery() 
	{
		?>
		<script type="text/javascript">
			jQuery(document).ready( function($) {   
				$('#wprev-66022').parent().attr('target','_blank');  
			});
		</script>
		<?php
	}
	
	/**
	 * custom option and settings on settings page
	 */
	public function wpfbr_settings_init()
	{

		//--======================= GOOGLE =======================--//

		// register a new setting for "wp_fb-google_settings" page
		register_setting('wp_fb-google_settings', 'wpfbr_google_options', array( &$this, 'wpfbr_schedule_cron' ) );

		// register a new section in the "wp_fb-google_settings" page
		add_settings_section(
			'wpfbr_section_developers_google',
			'',
			array($this,'wpfbr_section_developers_google_cb'),
			'wp_fb-google_settings'
		);
	 
		//register Google API key input field
		add_settings_field(
			'google_api_key', 
			'Google API Key',
			array($this,'wpfbr_field_google_api_key_cb'),
			'wp_fb-google_settings',
			'wpfbr_section_developers_google',
			array(
				'label_for'         => 'google_api_key',
				'class'             => 'wpfbr_row'
			)
		);
		//register location type
		/*
		add_settings_field(
			'google_location_type', 
			'Location Type',
			array($this,'wpfbr_location_type_cb'),
			'wp_fb-google_settings',
			'wpfbr_section_developers_google',
			array(
				'label_for'         => 'google_location_type',
				'class'             => 'wpfbr_row wpfbr_hide2'
			)
		);
		*/
		//register location search field
		add_settings_field(
			'google_location_txt', 
			'Location Search',
			array($this,'wpfbr_location_cb'),
			'wp_fb-google_settings',
			'wpfbr_section_developers_google',
			array(
				'label_for'         => 'google_location_txt',
				'class'             => 'wpfbr_row wpfbr_hide2'
			)
		);
		//register location field after autocomplete
		
		add_settings_field(
			'google_location_set', 
			'Name & Place ID',
			array($this,'wpfbr_location_set_cb'),
			'wp_fb-google_settings',
			'wpfbr_section_developers_google',
			array(
				'label_for'         => 'google_location_set', 
				'class'             => 'wpfbr_row wpfbr_hide2',
				'tarray'			=> array( 'google_location_set' => array( 'location'=>'', 'place_id'=>'' ) ),
			)
		);
		
		//fetch google reviews with a minimum of X rating
		add_settings_field(
			'google_location_minrating', 
			'Minimum Rating',
			array($this,'wpfbr_location_minrating_cb'),
			'wp_fb-google_settings',
			'wpfbr_section_developers_google',
			array(
				'label_for'         => 'google_location_minrating',
				'class'             => 'wpfbr_row wpfbr_hide2'
			)
		);

		//fetch google reviews with a minimum of X rating
		add_settings_field(
			'google_location_sort', 
			'Review Type',
			array($this,'wpfbr_location_sort_cb'),
			'wp_fb-google_settings',
			'wpfbr_section_developers_google',
			array(
				'label_for'         => 'google_location_sort',
				'class'             => 'wpfbr_row wpfbr_hide2'
			)
		);
		
		//register language field
		add_settings_field(
			'google_language_option', // as of WP 4.6 this value is used only internally
			'Language Code',
			array($this,'wpfbr_location_language'),
			'wp_fb-google_settings',
			'wpfbr_section_developers_google',
			[
				'label_for'         => 'google_language_option',
				'class'             => 'wpfbr_hide2'
			]
		);
				//run cron everyday to get 5 more reviews; google places API only gives 5 reviews;
		add_settings_field(
			'google_review_cron',
			'Auto Fetch Reviews', 
			array($this,'wpfbr_location_review_cron_cb'),
			'wp_fb-google_settings',
			'wpfbr_section_developers_google',
			array(
				'label_for'         => 'google_review_cron',
				'class'             => 'wpfbr_row wpfbr_hide2'
			)
		);

		//--======================= /GOOGLE =======================--//
	}
	/**
	 * custom option and settings:
	 * callback functions
	 */
	 
	//==== developers section cb ====
	// section callbacks can accept an $args parameter, which is an array.
	// $args have the following keys defined: title, id, callback.
	// the values are defined at the add_settings_section() function.
	public function wpfbr_section_developers_cb($args)
	{
		//echos out at top of section
	}


	//--======================= GOOGLE =======================--//
	//callback function for register_settings::line 295; 
	public function wpfbr_schedule_cron( $input )
	{
		if(isset($input['google_review_cron'])){
			if( $input['google_review_cron'] == 1 )
			{
				if( ! wp_next_scheduled( 'wpfbr_cron_google_review' )) 
				{
					wp_schedule_event(time(), 'daily', 'wpfbr_cron_google_review');
				}
			}
			else
				wp_clear_scheduled_hook( 'wpfbr_cron_google_review' );
		}
		return $input;
	}
	
	
	
	public function wpfbr_cron_googlereviews()
	{
		//$options = get_option('wpfbr_google_options');
		//$options = get_option('wpfbr_google_options');
		$ret = "";
		$googleapisarray = json_decode(get_option('wprev_google_apis'),true);

		foreach ($googleapisarray as $key =>$savedplace) {
			if(isset($key) && $key !=0 && $key!=""){
				if(isset($savedplace['google_review_cron']) && $savedplace['google_review_cron']>0){
					
				$savedplace['google_review_cron']==1;
				//$options = $googleapisarray[$gplaceid];

				$ret = $this->get_google_reviews($googleapisarray[$key]);
				}
			}
		}
		//return $ret;
	}

	// the values are defined at the add_settings_section() function.
	public function wpfbr_section_developers_google_cb($args)
	{
		//echos out at top of section
	}
	
	//==== field cb =====
	// field callbacks can accept an $args parameter, which is an array.
	// $args is defined at the add_settings_field() function.
	// wordpress has magic interaction with the following keys: label_for, class.
	// the "label_for" key value is used for the "for" attribute of the <label>.
	// the "class" key value is used for the "class" attribute of the <tr> containing the field.
	// you can add custom key value pairs to be used inside your callbacks.
	public function wpfbr_field_google_api_key_cb($args)
	{
		// get the value of the setting we've registered with register_setting()
		$options = get_option('wpfbr_google_options');
		//print_r($options);
		//if(!isset($options['select_google_api'])){
		//	$options['select_google_api']="mine";
		//}
		//if(!isset($options['google_api_key'])){
		//	$options['google_api_key']="";
		//}
		

		// output the field
		?>
		<select class="select_google_api" style="display:none;" id="select_google_api" name="wpfbr_google_options[select_google_api]">
			<option value="mine" selected="selected">Use My API Key</option>
			<option value="default">Use Default API Key</option>
			
		</select> 
		<p class="usedefaultkey description" style="display:none;"><?php _e('You can either use the default community API Key or create your own. It is more reliable to create and use your own key since the default key may exceed the daily call limit.', 'wp-google-reviews'); ?></p>
		<div class="showapikey">
			<input class="regular-text showapikey" id="<?php echo esc_attr($args['label_for']); ?>" type="text" name="wpfbr_google_options[<?php echo esc_attr($args['label_for']); ?>]" placeholder="<?php _e('Enter Your Google API Key', 'wp-google-reviews'); ?>" value="<?php echo esc_attr( $options[$args['label_for']] ); ?>"> <button id="wpfbr_testgooglekey" type="button" class="button">Test API Key</button>
			<p class="showapikey description"><?php _e('Once you have your Google API Key paste it in the box and click the "Save Settings" button.', 'wp-google-reviews'); ?> </p>
			<div class="showapikey" id="createbtns">
				<a href="http://ljapps.com/google-places-api-key/" target="_blank" id="fb_create_google_app_help" type="button" class=""><?php _e('How To Get API Key', 'wp-google-reviews'); ?></a>
			</div>
			<p class="showapikey description"><?php _e('Note: Google now requires an active billing account associated with your API Key.', 'wp-google-reviews'); ?> </p>
			
		</div>
		<?php
	}
	public function wpfbr_location_cb($args)
	{
		$options = get_option('wpfbr_google_options');
		if(!isset($options[$args['label_for']])){
			$options[$args['label_for']]="";
		}
		?>
		<input class="regular-text" id="<?php echo esc_attr($args['label_for']); ?>" type="text" name="wpfbr_google_options[<?php echo esc_attr($args['label_for']); ?>]" 
			placeholder="Enter a location" autocomplete="off" value="<?php echo esc_attr( $options[$args['label_for']] ); ?>">
			<span id="autocomplete_error"></span>
		<p class="description">
			<?php echo  esc_html__('Start typing to search for your Location. If your API Key is setup correctly, then you should get a drop down list of places to click when you start typing.', 'wp-google-reviews'); ?> <?php echo  esc_html__('If you can\'t find your location with the search box then manually input the values below. Look them up and copy them from this ', 'wp-google-reviews'); ?>
			<a href="https://developers.google.com/maps/documentation/javascript/examples/places-placeid-finder"  target="_blank"><?php echo  esc_html__('page', 'wp-google-reviews'); ?></a>.
		</p>
		<div id="wpfbr_result"></div>
		<?php
	}
	public function wpfbr_location_type_cb($args)
	{
		// get the value of the setting we've registered with register_setting()
		$options = get_option('wpfbr_google_options');

		$location_type = array( "all" => __('All','wp-google-reviews'), "address"=>__('Address','wp-google-reviews'), "establishment" => __('Establishment','wp-google-reviews'), "(regions)" => __('Regions','wp-google-reviews') );
		// output the field
		?>
		<select id="<?php echo  esc_attr($args['label_for']); ?>" name="wpfbr_google_options[<?php echo  esc_attr($args['label_for']); ?>]">
			<?php foreach( $location_type as $location_i => $location_v ) { ?>
			<option value="<?php echo esc_attr( $location_i ); ?>" <?php selected( $options[$args['label_for']], $location_i ); ?>><?php echo esc_attr( $location_v ); ?></option>
			<?php } ?>
		</select>		
		<p class="description">
			<?php echo  esc_html__('Enter Location Type.', 'wp-google-reviews'); ?>
		</p>
		<?php
	}

	public function wpfbr_location_set_cb( $args )
	{
		
		$options = get_option('wpfbr_google_options');

		//foreach( $args['label_for'] as $label=>$val ){
		//	foreach( $val as $labeli=>$valv ){
				
		$x=0;
		foreach( $args['tarray'] as $label=>$val ){
			foreach( $val as $labeli=>$valv ){
			if(!isset($options[$label][$labeli])){
				$options[$label][$labeli]="";
			}	
			$tempplaceholder = __('Enter Location Name', 'wp-google-reviews');
			if($x>0){
				$tempplaceholder = __('Enter Place ID', 'wp-google-reviews');
			}
		?>
		<label>
		<span class="locationlabel"><?php echo ucfirst( $labeli ); ?>:</span> <input class="regular-text" id="wpfbr_<?php echo esc_attr($labeli); ?>" type="text"  
				name="wpfbr_google_options[<?php echo esc_attr($label); ?>][<?php echo esc_attr($labeli); ?>]" 
				placeholder="<?php echo $tempplaceholder;?>" value="<?php echo esc_attr($options[$label][$labeli]); ?>"> </label><br/>
		<?php
		$x++;
		}
		
		}
		?>
		<?php
		
	}
	public function wpfbr_location_minrating_cb($args)
	{
		$options = get_option('wpfbr_google_options');
		?>
		<select id="<?php echo  esc_attr($args['label_for']); ?>" name="wpfbr_google_options[<?php echo  esc_attr($args['label_for']); ?>]">
			<?php foreach( range( 1, 5 ) as $minr ) { ?>
			<option value="<?php echo esc_attr( $minr ); ?>" <?php selected( $options[$args['label_for']], $minr ); ?>><?php echo esc_attr( $minr ); ?></option>
			<?php } ?>
		</select>		
		<p class="description">
			<?php echo  esc_html__('Only import reviews with a minimum rating of X.', 'wp-google-reviews'); ?>
		</p>
		<?php
	}

	public function wpfbr_location_review_cron_cb($args)
	{
		$options = get_option('wpfbr_google_options');
		$temparg = esc_attr($args['label_for']);
		if(!isset($options[$temparg])){
			$options[$temparg]='';
		}
		?>
		<input type="checkbox" id="<?php echo  esc_attr($args['label_for']); ?>" name="wpfbr_google_options[<?php echo esc_attr($args['label_for']); ?>]" value="1" <?php checked( $options[$args['label_for']], "1" ); ?>/>
		<p class="description">
		<?php echo  esc_html__('Run a wp-cron to import 5 reviews everyday. Google limits the amount of reviews you can download using their Google Places API to 5, this option allows you to get new reviews as they are added. The Pro version allows you to download all your reviews', 'wp-google-reviews'); ?>
		</p>
		<?php
	}
	
	public function wpfbr_location_language($args)
	{
		// get the value of the setting we've registered with register_setting()
		$options = get_option('wpfbr_google_options');
		if(!isset($options[$args['label_for']])){
			$options[$args['label_for']]='';
		}
		//print_r($options);
		// output the field
		?>
		<input class="regular-text" id="<?php echo esc_attr($args['label_for']); ?>" type="text" name="wpfbr_google_options[<?php echo esc_attr($args['label_for']); ?>]" value="<?php echo esc_attr( $options[$args['label_for']] ); ?>">
				<p class="description">
			<?php echo  esc_html__('Optional: The language code, indicating in which language the results should be returned, if possible. Click ', 'wp_pro-settings'); ?><a href="https://developers.google.com/maps/faq#languagesupport" target='_blank'><?php echo  esc_html__('here', 'wp_pro-settings'); ?></a>
			<?php echo  esc_html__(' for the language codes.', 'wp_pro-settings'); ?>
		</p>
		<?php
	}
	public function wpfbr_location_sort_cb($args)
	{
		$options = get_option('wpfbr_google_options');
		?>
		<select id="<?php echo  esc_attr($args['label_for']); ?>" name="wpfbr_google_options[<?php echo  esc_attr($args['label_for']); ?>]">
		<option value="most_relevant" <?php selected( $options[$args['label_for']], "newest" ); ?>>Most Relevant</option>
			<option value="newest" <?php selected( $options[$args['label_for']], "newest" ); ?>>Newest</option>
			
		</select>		
		<p class="description">
			<?php echo  esc_html__('Download the 5 Newest or Most Relevant reviews.', 'wp-google-reviews'); ?>
		</p>
		<?php
	}

	public function wpfbr_ajax_google_reviews()
	{
		global $wpdb, $current_user;
		//get_currentuserinfo();

		if(!is_user_logged_in())  
		{
			$out = __('User not logged in','wpcvt_lang');
			//header( "Content-Type: application/json" );
			echo $out;
			die();
		}
		check_ajax_referer('randomnoncestring', 'wpfb_nonce');
		
		//need to get placeid
		$gplaceid = sanitize_text_field($_POST['placeid']);
		$gplaceid = trim($gplaceid);
		
		
		if(!defined('DOING_AJAX')) define('DOING_AJAX', 1);
		
		if (strpos(@ini_get('disable_functions'), 'set_time_limit') === false) {
			@set_time_limit(3600);
		}

		//$options = get_option('wpfbr_google_options');
		$googleapisarray = json_decode(get_option('wprev_google_apis'),true);
		
		$options = $googleapisarray[$gplaceid];

		//if( empty( $options['google_api_key'] ) && empty( $options['google_api_key_default'] )){
		//	_e('Google Places API Key not found.');
		//	die();
		//}

		if( empty( $options['google_location_set']['place_id'] )){
			_e('There is no location set. Please search and select location to get reviews.');
			die();
		}

		echo $this->get_google_reviews( $options );
		die();
	}

	public function get_google_reviews( $options = array() )
	{
		global $wpdb;

		if( empty( $options ) )
			$options = get_option('wpfbr_google_options');

		
		if( empty( $options['google_api_key'] ) ){
			$google_api_key = $this->_default_api_token;
		} else {
			$google_api_key = $options['google_api_key'];
		}
		if(isset($options['select_google_api'])){
			if($options['select_google_api']=='default'){
				$google_api_key = $this->_default_api_token;
			} else if ($options['select_google_api']=='mine'){
				$google_api_key = $options['google_api_key'];
			}
		}
		if(!isset($options['google_location_sort'])){
			$google_review_order = "most_relevant";
		} else if($options['google_location_sort']=="most_relevant" || $options['google_location_sort']=="newest"){
			$google_review_order = $options['google_location_sort'];
		}
		
		$google_places_url = add_query_arg(
			array(
				'placeid' => trim($options['google_location_set']['place_id']),
				'key'     => trim($google_api_key),
				'language' => trim($options['google_language_option']),
				'reviews_sort' => trim($google_review_order),
			),
			'https://maps.googleapis.com/maps/api/place/details/json'
		);
		$response = $this->get_reviews( $google_places_url );

		//Error message from google;
		//if ( ! empty( $response->error_message ) ) 
		//{
		//	return '<strong>'.$response->status.'</strong>: '.$response->error_message;
		//} 
		//Error message from google;
		

		if ( ! empty( $response['error_message'] ) ) 
		{
			//print_r($response);
			//return '<strong>'.$response->status.'</strong>: '.$response->error_message;
			$error= sprintf( __('Google Error: %s, %s, <a href="%s" target="_blank">more info</a> </br>', 'wp-google-reviews' ), $response['status'], $response['error_message'], $google_places_url );
			return $error;
		} 
				
		//Error essage from wordpress//
		elseif ( isset( $response['error_message'] ) && ! empty( $response['error_message'] ) ) 
		{
			return '<strong>' . $response['status'] . '</strong>: ' . $response['error_message'];
		}
		//-----------------------------------------------

		$stats = array();
		$table_name = $wpdb->prefix . 'wpfb_reviews';

		$numreturned = count($response['result']['reviews']);
		
		//foreach element in $arr
		foreach( $response['result']['reviews'] as $item) 
		{ 
			//only enter reviews with ratings more than X;
			if( ! empty( $options['google_location_minrating'] )&& ! empty($item['rating'])&& (int)$options['google_location_minrating'] > (int)$item['rating'])
				continue;

			//get reviewer id from author url so we can display on front end
			$intreviewer_id = filter_var($item['author_url'], FILTER_SANITIZE_NUMBER_INT);
			
			if (extension_loaded('mbstring')) {
				$reviewlength = mb_substr_count($item['text'], ' ');
			} else {
				$reviewlength = substr_count($item['text'], ' ');
			}
			//fix for one word reviews
			if($reviewlength==0 && strlen($item['text'])>0){
				$reviewlength=1;
			}
						
			//check to see if row is in db already
			$checkrow = $wpdb->get_var( "SELECT id FROM ".$table_name." WHERE reviewer_name = \"".$item['author_name']."\" AND  review_length = '".$reviewlength."'" );
			
			$slashedusername = addslashes($item['author_name']);
			
			$checkrow2 = $wpdb->get_var( "SELECT id FROM ".$table_name." WHERE reviewer_name = '".$slashedusername."' AND  review_length = '".$reviewlength."'" );
						
			if( empty( $checkrow ) && empty( $checkrow2 ) )
			{
				$stats[] =array( 
					'pageid' 			=> $response['result']['place_id'], 
					'pagename' 			=> $response['result']['name'], 
					'created_time' 		=> date( "Y-m-d H:i:s", $item['time'] ),
					'created_time_stamp' 	=> $item['time'],
					'reviewer_name' 		=> $item['author_name'],
					'reviewer_id' 		=> $intreviewer_id,
					'rating' 			=> $item['rating'],
					'review_text' 		=> $item['text'],
					'hide' 			=> '',
					'review_length' 		=> $reviewlength,
					'type' 			=> 'Google',
					'userpic'			=> $item['profile_photo_url'],
					'from_url' =>$response['result']['url']
				);
			}
		}
		$i = 0;
		$insertnum = 0;
		
		//print_r($stats);
		
		$wpdb->show_errors();
		
		foreach ( $stats as $stat ){
			$insertnum = $wpdb->insert( $table_name, $stat );
			
			//if($wpdb->last_error !== '') :
			//	$wpdb->print_error();
			//endif;

			 
			//echo $wpdb->last_error;
			//echo $wpdb->last_result;
			
			
			$i=$i + 1;
		}
		
		
		if(!isset($response['result']['user_ratings_total'])){
					$response['result']['user_ratings_total']='';
				}
		//update totals and averages
		if(isset($response['result']['place_id']) && isset($response['result']['name'])){
			$this->updatetotalavgreviews('google', $response['result']['place_id'], $response['result']['rating'], $response['result']['user_ratings_total'],$response['result']['name'] );
		}
				
				
		echo sprintf( __('%d Reviews returned from API. %d New Reviews downloaded.', 'wp-google-reviews' ), $numreturned,$i );
		
		echo "<br><a href='".$google_places_url."' target='_blank'>Google Result</a> ";
		
		return;
	}

	/**
	 * cURL (wp_remote_get) the Google Places API
	 *
	 * @description: CURLs the Google Places API with our url parameters and returns JSON response
	 *
	 * @param $url
	 *
	 * @return array|mixed
	 */
	function get_reviews( $url ) 
	{
		//Sanitize the URL
		$url = esc_url_raw( $url );

		// Send API Call using WP's HTTP API
		$data = wp_remote_get( $url );

		if ( is_wp_error( $data ) ) 
		{
			$response['error_message'] 	= $data->get_error_message();
			$reponse['status'] 		= $data->get_error_code();
			return $response;
		}
		$response = json_decode( $data['body'], true );

		if( ! ( isset( $response['result']['reviews'] ) && ! empty( $response['result']['reviews'] ) ) )
		{
			$response['error_message'] 	= __('No Google Reviews Found.','wp-google-reviews');
			$reponse['status'] 		= __LINE__;
			return $response;
		}

		//Get Reviewers Avatars
		$response = $this->get_reviewers_avatars( $response );

		//Google response data
		return $response;
	}

	/**
	 * Get Reviewers Avatars
	 *
	 * Get avatar from Places API response or provide placeholder.
	 *
	 * @return array
	 */
	function get_reviewers_avatars( $response ) 
	{
		// Includes Avatar image from user.
		if ( isset( $response['result']['reviews'] ) && ! empty( $response['result']['reviews'] ) ) 
		{
			// Loop Google Places reviews.
			foreach( $response['result']['reviews'] as $i => $review ) {
				// Check to see if image is empty (no broken images).
				if ( ! empty( $review['profile_photo_url'] ) ) {
					$avatar_img = $review['profile_photo_url'] . '?sz=100';
				} else {
					$avatar_img = wpgooglerev_plugin_url . '/public/css/imgs/mystery-man.png';
				}
				$response['result']['reviews'][$i]['profile_photo_url'] = $avatar_img;
			}
		}
		return $response;
	}
	
	//ajax for crawling to check placeid. now calling the crawl.ljapps.com site.
	public function wpfbr_ajax_crawl_placeid(){
		check_ajax_referer('randomnoncestring', 'wpfb_nonce');
		
		$gplaceid = sanitize_text_field($_POST['gplaceid']);
		$gplaceid = trim($gplaceid);
		
		//are we editing this one? if so then delete it first so we can resave.
		$geditplace = sanitize_text_field(urldecode($_POST['geditplace']));
		if($geditplace!=''){
			$googlecrawlsarray = json_decode(get_option('wprev_google_crawls'),true);
			unset($googlecrawlsarray[$geditplace]);
			update_option('wprev_google_crawls',json_encode($googlecrawlsarray) );
		}
		
		//save placeid in the options table_name
		//update_option('wprev_google_crawl_placeid',$gplaceid);
		

		if(!isset($gplaceid) || $gplaceid==''){
				$results['ack'] = 'error';
				$results['ackmsg'] = 'Please a enter a Place ID or Search Terms.';
				$results = json_encode($results);
				delete_option('wprev_google_crawl_check');
				delete_option('wprev_google_crawl_placeid');
				echo $results;
				die();
		}
		if(isset($_SERVER['SERVER_ADDR']) && $_SERVER['SERVER_ADDR']!=''){
			$ip_server = $_SERVER['SERVER_ADDR'];
		} else {
			//get url of site.
			$ip_server = urlencode(get_site_url());
		}
		
		$siteurl = urlencode(get_site_url());
		$gplaceid = str_replace("\'","",$gplaceid);
		$tempurlvalue = 'https://crawl.ljapps.com/crawlrevs?rip='.$ip_server.'&surl='.$siteurl.'&stype=googlecheck&scrapequery='.urlencode($gplaceid).'&nobot=1&sfp=free';
		$results['firsturlvalue']=$tempurlvalue;
		
		$serverresponse='';

		if (ini_get('allow_url_fopen') == true) {
			$serverresponse=file_get_contents($tempurlvalue);
		} else if (function_exists('curl_init')) {
			$serverresponse=$this->file_get_contents_curl($tempurlvalue);
		} else {
			$fileurlcontents='<html><body>'.esc_html__('fopen is not allowed on this host.', 'wp-google-reviews').'</body></html>';
			$errormsg = $errormsg . '<p style="color: #A00;">'.esc_html__('fopen is not allowed on this host and cURL did not work either. Ask your web host to turn fopen on or fix cURL.', 'wp-google-reviews').'</p>';
			$this->errormsg = $errormsg;
			$results['ack'] ='error';
			$results['ackmsg'] =$errormsg;
			$results = json_encode($results);
			echo $results;
			die();
		}

		if($serverresponse==false || $serverresponse==''){
		//try remote_get if we dont' have serverresponse yet
		    $args = array(
				'timeout'     => 30,
				'sslverify' => false,
				'headers' => array( 
					'Content-Type' => ' application/json',
					'Accept'=> 'application/json'
				)
			); 
			$response = wp_remote_get( $tempurlvalue, $args );
			if ( is_array( $response ) && ! is_wp_error( $response ) ) {
				$headers = $response['headers']; // array of http header lines
				$serverresponse    = $response['body']; // use the content
			} else {
				//must have been an error
				$results['ack'] ='error';
				$results['ackmsg'] ='Error 0001a: trouble contacting crawling server with remote_get. Please try again or contact support. CMD: '.$response->get_error_message();
				$results = json_encode($results);
				echo $results;
				die();
			}
		}


		//check for block
		if (strpos($serverresponse, "Please wait while your request is being verified") !== false || !isset($serverresponse) || $serverresponse=='' || strpos($serverresponse, "Access denied by Imunify360 bot-protection.") !== false || strpos($serverresponse, "415 Unsupported Media Type") !== false) {
		   //this site is greylisted by imunify360 on cloudways, call backup digital ocean server
		   $tempurlvalue = 'https://ocean.ljapps.com/crawlrevs.php?rip='.$ip_server.'&surl='.$siteurl.'&stype=googlecheck&scrapequery='.urlencode($gplaceid).'&nobot=1&sfp=free';
		   $response = wp_remote_get( $tempurlvalue, array( 'sslverify' => false, 'timeout' => 60 ) );
			if ( is_array( $response ) && ! is_wp_error( $response ) ) {
				$headers = $response['headers']; // array of http header lines
				$serverresponse    = $response['body']; // use the content
			}
		}
		
		//echo $serverresponse;
		$serverresponsearray = json_decode($serverresponse, true);
		//print_r($serverresponsearray);
		
		if($serverresponse=='' || !is_array($serverresponsearray)){
			$results['ack'] ='error';
			$results['ackmsg'] ='Error 0001: trouble contacting crawling server. Please try again or contact support.';
			
			$results = json_encode($results);
			echo $results;
			die();
		}
		
		if($serverresponsearray['ack']=='error'){
			$results['ack'] ='error';
			if(isset($serverresponsearray['ackmessage'])){
			$results['ackmsg'] =$serverresponsearray['ackmessage'];
			}
			if(isset($serverresponsearray['ackmsg'])){
			$results['ackmsg'] =$serverresponsearray['ackmsg'];
			}
			$results = json_encode($results);
			echo $results;
			die();
			
		}
		$businessdetails = $serverresponsearray['result'];
		if($businessdetails['ack']!='success'){
			$results['ack'] ='error';
			$results['ackmsg'] =$businessdetails['ackmsg'];
			$results = json_encode($results);
			echo $results;
			die();
			
		}
		
		$tempbusinessdetails = $businessdetails;
		//$tempbusinessdetails['img']="";

		$results = json_encode($businessdetails);
		
		update_option('wprev_google_crawl_check',$results );
		
				//get all saved crawls and add or update if needed.
		$crawlsarray = Array();
		$crawlsarray[] = Array();
		$crawlsarray = json_decode(get_option('wprev_google_crawls'),true);
		$crawlsarray[$gplaceid]['enteredidorterms'] = $gplaceid;
			$crawlsarray[$gplaceid]['crawl_check'] = $tempbusinessdetails;
			update_option('wprev_google_crawls',json_encode($crawlsarray) );

		
		echo $results;
	
		die();
	}
	
		//ajax for crawling and downloading reviews
	public function wpfbr_ajax_crawl_placeid_go(){
		check_ajax_referer('randomnoncestring', 'wpfb_nonce');
		global $wpdb;
		$results['ack'] ='success';
		$results['ackmsg'] ='';
		$nhful = trim($_POST['nhful']);	//newest or relevant
		
		$savedplaceid ="";
		$crawlpage=false;
		
		//print_r($_POST);
		
		if(isset($_POST['gplaceid'])){	//coming from the crawl page
			$savedplaceid = trim($_POST['gplaceid']);
			$crawlpage=true;
		}
		
		//if we are coming from getrevs page then we need to urldecode it
		if(isset($_POST['getrevsplaceid'])){
			$savedplaceid = trim(urldecode($_POST['getrevsplaceid']));
		}
		
		//$checkdetails = json_decode(get_option('wprev_google_crawl_check'),true);
		$crawlsarray = Array();
		$crawlsarray[] = Array();
		$crawlsarray = json_decode(get_option('wprev_google_crawls'),true);
		$checkdetails = $crawlsarray[$savedplaceid]['crawl_check'];
		//save newest or most helpful
		$crawlsarray[$savedplaceid]['nhful'] = $nhful;
		update_option('wprev_google_crawls',json_encode($crawlsarray) );
		
		
		if($checkdetails['idorquery'] == 'query' && $checkdetails['enteredterms']!=''){
			$tempbusinessname=$checkdetails['enteredterms'];
		} else {
			$tempbusinessname=$checkdetails['businessname'];
		}
		
		if(isset($checkdetails['foundplaceid']) && $checkdetails['foundplaceid']!=''){
			$gplaceid = $checkdetails['foundplaceid'];
		} else {
			//error no place id.
			$results['ack'] = 'error';
			$results['ackmsg'] ='Error 103a: Please enter your search terms or place id above and click the Save & Test button.';
		}
		
		if(isset($_SERVER['SERVER_ADDR']) && $_SERVER['SERVER_ADDR']!=''){
			$ip_server = $_SERVER['SERVER_ADDR'];
		} else {
			//get url of site.
			$ip_server = urlencode(get_site_url());
		}
		$locationtype = '';
		if($checkdetails['placetype']=='hotel'){
			$locationtype = 'hotel';
		} else if($checkdetails['placetype']=='product'){
				$locationtype = 'product';
		}
		$siteurl = urlencode(get_site_url());

		if($crawlpage==true){
			$gplaceid=$savedplaceid;
		}
		
		$tempurlvalue = 'https://crawl.ljapps.com/crawlrevs?rip='.$ip_server.'&surl='.$siteurl.'&stype=google&nhful='.$nhful.'&locationtype='.$locationtype.'&scrapequery='.urlencode($gplaceid).'&tempbusinessname='.urlencode($tempbusinessname).'&nobot=1&sfp=free';
		
		//echo $tempurlvalue;
		//die();
		$serverresponse='';

		if (ini_get('allow_url_fopen') == true) {
			$serverresponse=file_get_contents($tempurlvalue);
		} else if (function_exists('curl_init')) {
			$serverresponse=$this->file_get_contents_curl($tempurlvalue);
		} else {
			$fileurlcontents='<html><body>'.esc_html__('fopen is not allowed on this host.', 'wp-google-reviews').'</body></html>';
			$errormsg = $errormsg . '<p style="color: #A00;">'.esc_html__('fopen is not allowed on this host and cURL did not work either. Ask your web host to turn fopen on or fix cURL.', 'wp-google-reviews').'</p>';
			$this->errormsg = $errormsg;
			$results['ack'] ='error';
			$results['ackmsg'] =$errormsg;
			$results = json_encode($results);
			echo $results;
			die();
		}
		
		if($serverresponse==false || $serverresponse==''){
		//try remote_get if we dont' have serverresponse yet
		    $args = array(
				'timeout'     => 50,
				'sslverify' => false,
				'headers' => array( 
					'Content-Type' => ' application/json',
					'Accept'=> 'application/json'
				)
			); 
			$response = wp_remote_get( $tempurlvalue, $args );
			if ( is_array( $response ) && ! is_wp_error( $response ) ) {
				$headers = $response['headers']; // array of http header lines
				$serverresponse    = $response['body']; // use the content
			} else {
				//must have been an error
				$results['ack'] ='error';
				$results['ackmsg'] ='Error 0001a: trouble contacting crawling server with remote_get. Please try again or contact support. CMD: '.$response->get_error_message();
				$results = json_encode($results);
				echo $results;
				die();
			}
		}

		//check for block
		if (strpos($serverresponse, "Please wait while your request is being verified") !== false || !isset($serverresponse) || $serverresponse=='' || strpos($serverresponse, "Access denied by Imunify360 bot-protection.") !== false || strpos($serverresponse, "415 Unsupported Media Type") !== false) {
		   //this site is greylisted by imunify360 on cloudways, call backup digital ocean server
		   $tempurlvalue = 'https://ocean.ljapps.com/crawlrevs.php?rip='.$ip_server.'&surl='.$siteurl.'&stype=google&nhful='.$nhful.'&locationtype='.$locationtype.'&scrapequery='.urlencode($gplaceid).'&tempbusinessname='.urlencode($tempbusinessname).'&nobot=1&sfp=free';

		   
		   $response = wp_remote_get( $tempurlvalue, array( 'sslverify' => false, 'timeout' => 60 ) );
			if ( is_array( $response ) && ! is_wp_error( $response ) ) {
				$headers = $response['headers']; // array of http header lines
				$serverresponse    = $response['body']; // use the content
			}
		}
		
		
		
		$serverresponsearray = json_decode($serverresponse, true);

		if($serverresponse=='' || !is_array($serverresponsearray)){
			$results['ack'] ='error';
			$results['ackmsg'] ='Error 0001b: trouble contacting crawling server. Please contact support.';
			$results = json_encode($results);
			echo $results;
			die();
		}
		//catch limit error
		if($serverresponsearray['ack']=='error'){
			$results['ack'] ='error';
			$results['ackmsg'] ='Error 0002: '.$serverresponsearray['ackmessage'];
			$results = json_encode($results);
			echo $results;
			die();
		}
		if(!isset($serverresponsearray['result']) || !is_array($serverresponsearray['result'])){
			$results['ack'] ='error';
			$results['ackmsg'] ='Error 0002: trouble finding reviews. Contact support with this error code and the search terms or place id you are using.';
			$results = json_encode($results);
			echo $results;
			die();
		}
		//catch error
		if($serverresponsearray['result']['ack']=='error'){
			$results['ack'] ='error';
			$results['ackmsg'] ='Error 0003: '.$serverresponsearray['result']['ackmsg'];
			$results = json_encode($results);
			echo $results;
			die();
		}

//print_r($serverresponsearray);
//die();
		//made it this far assume we have reviews.
		$crawlerresultarray = $serverresponsearray['result'];
		
		if(!isset($crawlerresultarray['reviews']) || !is_array($crawlerresultarray['reviews'])){
			$results['ack'] ='error';
			$results['ackmsg'] ='Error 0004: trouble finding reviews. Contact support with this error code and the search terms or place id you are using.';
			$results = json_encode($results);
			echo $results;
			die();
		}
		$crawlerreviewsarray = $crawlerresultarray['reviews'];

		//need totals and avg for this place $getreviewsarray['total']
			$results['total']='';
			$results['avg']='';
			if(isset($crawlerresultarray['avg'])){
				$results['avg']=$crawlerresultarray['avg'];
			}
			if(isset($crawlerresultarray['total'])){
				$results['total']=$crawlerresultarray['total'];
			}
			
	
		$stats = array();
		$table_name = $wpdb->prefix . 'wpfb_reviews';
		
		$x=0;
		$numreturned = count($crawlerreviewsarray);
		
		foreach ($crawlerreviewsarray as $review) {
			// Find user_name
			$results['user_name']=$review['user_name'];

			//created time
			$results['created_time']=$review['created_time'];
			$results['created_time_stamp']=$review['created_time_stamp'];

			
			//find reviewer_id from maps link		//https://www.google.com/maps/contrib/117800412986895302631?hl=en-US&sa=X&ved=2ahUKEwit9_W6s-PyAhWFTTABHZaUBeIQvvQBegQIARAh
			$results['user_link']=$review['user_link'];
			$results['reviewer_id']=$review['reviewer_id'];
			
			//find review rating
			$results['rating']=$review['rating'];
			
			//find review text jscontroller, MZnM8e
			$results['review_text']=$review['review_text'];

			$results['reviewlength']=$review['reviewlength'];
			
			//find user image
			$results['userpic'] = $review['userpic'];
			
			//user images
			$mediaurlsarrayjson = '';
			if(isset($review['mediaurlsarrayjson'])){
				$mediaurlsarrayjson = $review['mediaurlsarrayjson'];
			}

			//check to see if row is in db already
			$checkrow = $wpdb->get_var( "SELECT id FROM ".$table_name." WHERE reviewer_name = \"".$results['user_name']."\" AND  review_length = '".$results['reviewlength']."'" );
			
			//$checkrow = $wpdb->get_var( "SELECT id FROM ".$table_name." WHERE reviewer_name = '".$results['user_name']."' AND review_length = '".$results['reviewlength']."'" );
			
			$slashedusername = addslashes($results['user_name']);
			
			$checkrow2 = $wpdb->get_var( "SELECT id FROM ".$table_name." WHERE reviewer_name = '".$slashedusername."' AND  review_length = '".$results['reviewlength']."'" );
						
			if( empty( $checkrow ) && empty( $checkrow2 ) )
			{
				$stats[] =array( 
					'pageid' 			=> $gplaceid, 
					'pagename' 			=> $checkdetails['businessname'], 
					'created_time' 		=> date( "Y-m-d H:i:s", $results['created_time_stamp'] ),
					'created_time_stamp' 	=> $results['created_time_stamp'],
					'reviewer_name' 		=> $results['user_name'],
					'reviewer_id' 		=> $results['reviewer_id'],
					'rating' 			=> $results['rating'],
					'review_text' 		=> $results['review_text'],
					'hide' 				=> '',
					'review_length' 	=> $results['reviewlength'],
					'type' 				=> 'Google',
					'userpic'			=> $results['userpic'],
					'from_url' 			=> $checkdetails['googleurl'],
					'mediaurlsarrayjson' => $mediaurlsarrayjson,
				);
			}
			
			$x++;
		}
		
		
				$i = 0;
		$insertnum = 0;
		foreach ( $stats as $stat ){
			$insertnum = $wpdb->insert( $table_name, $stat );
			if($insertnum>0){
			$i=$i + 1;
			}
		}
		
		//update total and avg for badges.
				if(trim($gplaceid)!=''){
					$temptype = 'google';
					$this->updatetotalavgreviews($temptype, $gplaceid, $results['avg'], $results['total'],$checkdetails['businessname']);
				}
	
		$results['ackmsg'] =sprintf( __('Success! <b>%d</b> Reviews found. <b>%d</b> New Reviews downloaded. Check Review List page for downloaded reviews. The Pro version can download all your reviews from multiple locations and automatically check for new reviews!', 'wp-google-reviews' ), $numreturned,$i );
		
		if(isset($_POST['getrevsplaceid'])){
			$results['ackmsg'] =sprintf( __('<b>%d</b> New Reviews downloaded', 'wp-google-reviews' ), $i );
		}
		
		$results = json_encode($results);
		echo $results;
		die();
		
	}
	
	public function get_string_between($string, $start, $end, $end2=''){
		$string = ' ' . $string;
		$ini = strpos($string, $start);
		if ($ini == 0) return '';
		$ini += strlen($start);
		$len = strpos($string, $end, $ini) - $ini;
		$len2 =500000;
		if($end2!=''){
			$len2 = strpos($string, $end2, $ini) - $ini;
		}
		if($len2<$len){
			$len=$len2;
		}
		return substr($string, $ini, $len);
	}
	
		//for using curl instead of fopen
	private function file_get_contents_curl($url) {
		set_time_limit(60);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60); 
		curl_setopt($ch, CURLOPT_TIMEOUT, 60);
		curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);       

		$data = curl_exec($ch);
		curl_close($ch);

		return $data;
	}
	
	/**
	 * ajax for testing the api key
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function wpfbr_ajax_testing_api(){
		//echo "here";
		check_ajax_referer('randomnoncestring', 'wpfb_nonce');
		
		$apikey = $_POST['apikey'];
		
		$goodkey = false;
		
		//remote get the autocomplete first
		//https://maps.googleapis.com/maps/api/place/autocomplete/json?input=1600+Amphitheatre&key=<API_KEY>		
		$url = "https://maps.googleapis.com/maps/api/place/autocomplete/json?input=1600+Amphitheatre&key=".$apikey;
		$data = wp_remote_get( $url );

		if ( is_wp_error( $data ) ) 
		{
			$response['error_message'] 	= $data->get_error_message();
			$reponse['status'] 		= $data->get_error_code();
			print_r($response);
		}
		$response = json_decode( $data['body'], true );
		
		if(isset($response['predictions'][0]['place_id'])){
			//autocomplete is working
			echo "- Autocomplete is working.<br>";
			$goodkey = true;
		} else {
			//key not good
			echo "- Something is wrong with this Google API Key. Error from Google...<br><br>";
			print_r($response);
		}
		
		if($goodkey){
				//remote get place if passed outcomplete
				$url = "https://maps.googleapis.com/maps/api/place/details/json?placeid=ChIJC8DB3J5sYogRV8b_lTk20U4&key=".$apikey;
				$data = wp_remote_get( $url );

				if ( is_wp_error( $data ) ) 
				{
					$response['error_message'] 	= $data->get_error_message();
					$reponse['status'] 		= $data->get_error_code();
					print_r($response);
				}
				$response = json_decode( $data['body'], true );
				
				if(isset($response['result']['name'])){
					//place lookup is working
					echo "- Place Look-up is working.<br><br>";
					echo "- This key should be good to go. Make sure to click Save Settings at the bottom.<br><br>";
				} else {
					echo "- Something is wrong with this Google API Key. Error from Google...<br><br>";
					print_r($response);
				}
		}
		die();
				
	}
	
		/**
	 * displays message in admin if it's been longer than days.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function wprp_admin_notice__success () {

		$activatedtime = get_option('wprev_activated_time_google');
		//if this is an old install then use 23 days ago
		if($activatedtime==''){
			$activatedtime= time() - (86400*12);
			update_option( 'wprev_activated_time_google', $activatedtime );
		}
		$thirtydaysago = time() - (86400*15);
		
		//check if an option was clicked on
		if (isset($_GET['wprevpronotice'])) {
		  $wprevpronotice = $_GET['wprevpronotice'];
		} else {
		  //Handle the case where there is no parameter
		   $wprevpronotice = '';
		}
		if($wprevpronotice=='mlater_google'){		//hide the notice for another 30 days
			update_option( 'wprev_notice_hide_google', 'later' );
			$newtime = time() - (86400*15);
			update_option( 'wprev_activated_time_google', $newtime );
			$activatedtime = $newtime;
			
		} else if($wprevpronotice=='notagain_google'){		//hide the notice forever
			update_option( 'wprev_notice_hide_google', 'never' );
		}
		
		$wprev_notice_hide = get_option('wprev_notice_hide_google');

		if($activatedtime<$thirtydaysago && $wprev_notice_hide!='never'){
		
			$urltrimmedtab = remove_query_arg( array('taction', 'tid', 'sortby', 'sortdir', 'opt') );
			$urlmayberlater = esc_url( add_query_arg( 'wprevpronotice', 'mlater_google',$urltrimmedtab ) );
			$urlnotagain = esc_url( add_query_arg( 'wprevpronotice', 'notagain_google',$urltrimmedtab ) );
			
			$temphtml = '<p>Hey, I noticed you\'ve been using my <b>WP Google Review Slider</b> plugin for a while now – that’s awesome! Could you please do me a BIG favor and give it a 5-star rating on WordPress? <br>
			Thanks!<br>
			~ Josh W.<br></p>
			<ul>
			<li><a href="https://wordpress.org/support/plugin/wp-google-places-review-slider/reviews/#new-post" target="_blank">Ok, you deserve it</a></li>
			<li><a href="'.$urlmayberlater.'">Not right now, maybe later</a></li>
			<li><a href="'.$urlnotagain.'">Don\'t remind me again</a></li>
			</ul>
			<p>P.S. If you\'ve been thinking about upgrading to the <a href="https://wpreviewslider.com/" target="_blank">Pro</a> version, here\'s a 10% off coupon code you can use! ->  <b>wprevpro10off</b></p>';
			
			?>
			<div class="notice notice-info">
				<div class="wprevpro_admin_notice" style="color: #007500;">
				<?php _e( $temphtml, $this->_token ); ?>
				</div>
			</div>
			<?php
		}

	}
	
		/**
	 * add dashboard widget to wordpress admin
	 * @access  public
	 * @since   7.3
	 * @return  void
	 */
	public function wprevpro_dashboard_widget() {
		global $wp_meta_boxes;
		//wp_add_dashboard_widget('custom_help_widget', 'Theme Support', 'custom_dashboard_help');
		add_meta_box( 'googlerevsid', 'WP Google Review Slider Recent Reviews', array($this,'custom_dashboard_help'), 'dashboard', 'side', 'high' );
	}
	 
	public function custom_dashboard_help() {
		global $wpdb;
		$reviews_table_name = $wpdb->prefix . 'wpfb_reviews';
		$tempquery = "select * from ".$reviews_table_name." ORDER by created_time_stamp Desc limit 4";
		$reviewrows = $wpdb->get_results($tempquery);
		$now = time(); // or your date as well
		
		echo '<style>
			img.wprev_dash_avatar {float: left;margin-right: 8px;border-radius: 20px;}
			.wprev_dash_stars {float: right;}
			p.wprev_dash_text {margin-top: -6px;}
			span.wprev_dash_timeago {font-size: 12px;font-style: italic;}
			</style>';
			
		echo '<style>
			img.wprev_dash_avatar {float: left;margin-right: 8px;border-radius: 20px;}
			.wprev_dash_stars {float: right;}
			p.wprev_dash_text {margin-top: -6px;}
			span.wprev_dash_timeago {font-size: 12px;font-style: italic;}
			.wprev_dash_revdiv {min-height: 50px;}
			</style>';
		echo '<ul>';
		foreach ( $reviewrows as $review ) 
		{
			$timesince = '';
			if(strlen($review->review_text)>130){
				$reviewtext = substr($review->review_text,0,130).'...';
			} else {
				$reviewtext = $review->review_text;
			}
			
			$your_date = $review->created_time_stamp;
			$datediff = $now - $your_date;
			$daysago = round($datediff / (60 * 60 * 24));
			if($daysago==1){
				$daysagohtml = $daysago.' day ago';
			} else {
				$daysagohtml = $daysago.' days ago';
			}
			if($review->rating<1){
				if($review->recommendation_type=='positive'){
					$review->rating=5;
				} else {
					$review->rating=2;
				}
			}

			$imgs_url = plugin_dir_url(__DIR__).'/public/partials/imgs/';
			$starfile = 'stars_'.$review->rating.'_yellow.png';
			$starhtml='<img src="'.$imgs_url."".$starfile.'" alt="'.$review->rating.' star rating" class="wprev_dash_stars">';
			
			$avatarhtml = '';
			if(isset($review->userpic) && $review->userpic!=''){
				$avatarhtml = '<img alt="" src="'.$review->userpic.'" class="wprev_dash_avatar" height="40" width="40">';
			}
			
			echo '<li><div class="wprev_dash_revdiv">'.$avatarhtml.'<div class="wprev_dash_stars">'.$starhtml.'</div><h4 class="wprev_dash_name">'.$review->reviewer_name.' - <span class="wprev_dash_timeago">'.$daysagohtml.'</span></h4><p class="wprev_dash_text">'.$reviewtext.'</p></div></li>';
			
		}
		echo '</ul>';
		
		echo '<div><a href="admin.php?page=wp_google-reviews">All Reviews</a> - <a href="https://wpreviewslider.com/" target="_blank">Go Pro For More Cool Features!</a></div>';
	}
	
	//-----for updating options for total and avg based on pageid
	public function updatetotalavgreviews($type, $pageid, $avg, $total, $pagename = ''){
		$ratingsarray= array();
		$pagetype='';
		global $wpdb;
		$table_name = $wpdb->prefix . 'wpfb_reviews';
		//fix for comma in some languages
		$avg = str_replace(",",".",$avg);
		$option = 'wppro_total_avg_reviews';

		//get existing option array of fb cron pages
		$wppro_total_avg_reviews_array = get_option( $option );
		if(isset($wppro_total_avg_reviews_array)){
			$wppro_total_avg_reviews_array = json_decode($wppro_total_avg_reviews_array, true);
		} else {
			$wppro_total_avg_reviews_array = array();
		}
			$field_name = 'rating';
			$prepared_statement = $wpdb->prepare( "SELECT rating, recommendation_type, type FROM {$table_name} WHERE hide != %s AND pageid = %s", 'yes', $pageid);
			$fbreviews = $wpdb->get_results( $prepared_statement );
			
			foreach ( $fbreviews as $fbreview ) 
				{
					//echo $fbreview->post_title;
					if($fbreview->rating>0){
						$tempnum=$fbreview->rating;
					} else if($fbreview->recommendation_type=='positive'){
						$tempnum=5;
					} else if($fbreview->recommendation_type=='negative'){
						$tempnum=2;
					}
					if(isset($tempnum)){
					$ratingsarray[]=$tempnum;
					}
					
					$pagetype = $fbreview->type;
				}
			
			//print_r($ratingsarray);
			$ratingsarray = array_filter($ratingsarray);
			if(count($ratingsarray)>0){
				$avgdb = round(array_sum($ratingsarray) / count($ratingsarray), 3);
				$totaldb =  round(count($ratingsarray), 0);
				$wppro_total_avg_reviews_array[$pageid]['total_indb'] = $totaldb;
				if($avg>0){
					$wppro_total_avg_reviews_array[$pageid]['avg'] = round($avg,3);
				} else {
					//$wppro_total_avg_reviews_array[$pageid]['avg'] = $avgdb;
				}
				if($total>0){
					$wppro_total_avg_reviews_array[$pageid]['total'] = $total;
				} else {
					//$wppro_total_avg_reviews_array[$pageid]['total'] = $totaldb;
				}
				$wppro_total_avg_reviews_array[$pageid]['total_indb'] = $totaldb;
				$wppro_total_avg_reviews_array[$pageid]['avg_indb'] = $avgdb;
			}
		
		//ratings for badge 2
		$temprating = $this->wprp_get_temprating($ratingsarray);
		if(isset($temprating)){
			$wppro_total_avg_reviews_array[$pageid]['numr1'] = array_sum($temprating[1]);
			$wppro_total_avg_reviews_array[$pageid]['numr2'] = array_sum($temprating[2]);
			$wppro_total_avg_reviews_array[$pageid]['numr3'] = array_sum($temprating[3]);
			$wppro_total_avg_reviews_array[$pageid]['numr4'] = array_sum($temprating[4]);
			$wppro_total_avg_reviews_array[$pageid]['numr5'] = array_sum($temprating[5]);
		}

		$new_value = json_encode($wppro_total_avg_reviews_array, JSON_FORCE_OBJECT);
		update_option( $option, $new_value);
		
		//added in 10.9.3 now adding this to table wpfb_total_averages---------
		//will enventually replace the options save

			$valuearray['btp_id']=$pageid;
			$valuearray['btp_name'] = $pagename;
			$valuearray['pagetype']= $pagetype;
			$valuearray['total']='';
			if(isset($wppro_total_avg_reviews_array[$pageid]['total'])){
				$valuearray['total']=$wppro_total_avg_reviews_array[$pageid]['total'];
			}
			$valuearray['total_indb']='';
			if(isset($wppro_total_avg_reviews_array[$pageid]['total_indb'])){
			$valuearray['total_indb']=$wppro_total_avg_reviews_array[$pageid]['total_indb'];
			}
			$valuearray['avg']='';
			if(isset($wppro_total_avg_reviews_array[$pageid]['avg'])){
				$valuearray['avg']=$wppro_total_avg_reviews_array[$pageid]['avg'];
			}
			$valuearray['avg_indb']='';
			if(isset($wppro_total_avg_reviews_array[$pageid]['avg_indb'])){
			$valuearray['avg_indb']=$wppro_total_avg_reviews_array[$pageid]['avg_indb'];
			}
			$valuearray['numr1']=$wppro_total_avg_reviews_array[$pageid]['numr1'];
			$valuearray['numr2']=$wppro_total_avg_reviews_array[$pageid]['numr2'];
			$valuearray['numr3']=$wppro_total_avg_reviews_array[$pageid]['numr3'];
			$valuearray['numr4']=$wppro_total_avg_reviews_array[$pageid]['numr4'];
			$valuearray['numr5']=$wppro_total_avg_reviews_array[$pageid]['numr5'];
			
			
			$this->updatetotalavgreviewstableinsert('page',$valuearray);
		//---------------------------
		//go ahead and update the total and avg for badge here since something has been downloaded.
		//$this->updateallavgtotalstable_templates();
		//$this->updateallavgtotalstable_badges();
	}
		
	//used to actually insert the values from function above
	public function updatetotalavgreviewstableinsert($btp_type,$valuearray){
		global $wpdb;
		$table_name_totalavg = $wpdb->prefix . 'wpfb_total_averages';
		$key = $valuearray['btp_id'];
		$name = $valuearray['btp_name'];
		$temp_total_indb=$valuearray['total_indb'];
		$temp_total='';
		if(isset($valuearray['total'])){
			$temp_total=$valuearray['total'];
		}
		$temp_avg_indb=$valuearray['avg_indb'];
		$temp_avg='';
		if(isset($valuearray['avg'])){
			$temp_avg=$valuearray['avg'];
		}
		$temp_numr1=$valuearray['numr1'];
		$temp_numr2=$valuearray['numr2'];
		$temp_numr3=$valuearray['numr3'];
		$temp_numr4=$valuearray['numr4'];
		$temp_numr5=$valuearray['numr5'];
		$pagetype = '';
		if(isset($valuearray['pagetype'])){
			$pagetype=$valuearray['pagetype'];
		}
		$data = array( 
				'btp_id' => "$key",
				'btp_name' => "$name",
				'btp_type' => "$btp_type",
				'pagetype' => "$pagetype",
				'total_indb' => "$temp_total_indb",
				'total' => "$temp_total",
				'avg_indb' => "$temp_avg_indb",
				'avg' => "$temp_avg",
				'numr1' => "$temp_numr1",
				'numr2' => "$temp_numr2",
				'numr3' => "$temp_numr3",
				'numr4' => "$temp_numr4",
				'numr5' => "$temp_numr5",
				);
				//print_r($data);
		$format = array('%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s'); 
		$insertrow = $wpdb->replace( $table_name_totalavg, $data, $format );
	}
	/*
	public function updateallavgtotalstable_templates(){
		global $wpdb;
		//now update all template totals and averages
		//select all templates and loop through each updating the total and avg
		$table_name = $wpdb->prefix . 'wpfb_post_templates';
		$currentformsobj = $wpdb->get_results("SELECT * FROM $table_name");
		if(count($currentformsobj)>0){
			require_once WPREV_PLUGIN_DIR . 'public/partials/getreviews_class.php';
			$reviewsclass = new GetReviews_Functions();
			foreach ($currentformsobj as &$singleform) {
				//fix for naming
				$currentform[0]=$singleform;
				//turn on load more so we can totals and avgs
				$currentform[0]->load_more='yes';
				$totalreviewsarray = $reviewsclass->wppro_queryreviews($currentform);

				$valuearray['btp_id'] = "template_".$singleform->id;
				$valuearray['btp_name'] = $singleform->title;
				$valuearray['total_indb']= $totalreviewsarray['totalcount'];
				$valuearray['avg_indb']= $totalreviewsarray['totalavg'];
				$valuearray['numr1']=$totalreviewsarray['numr1'];
				$valuearray['numr2']=$totalreviewsarray['numr2'];
				$valuearray['numr3']=$totalreviewsarray['numr3'];
				$valuearray['numr4']=$totalreviewsarray['numr4'];
				$valuearray['numr5']=$totalreviewsarray['numr5'];
				$this->updatetotalavgreviewstableinsert('template',$valuearray);
			}
		}
	}
	public function updateallavgtotalstable_badges(){
		global $wpdb;
		//now updating all badges
		$table_name = $wpdb->prefix . 'wpfb_badges';
		$currentbadgesobj = $wpdb->get_results("SELECT * FROM $table_name");
		if(count($currentbadgesobj)>0){
			require_once WPREV_PLUGIN_DIR . 'public/partials/badge_class.php';	
			foreach ($currentbadgesobj as &$singlebadge) {
				$badgeid = $singlebadge->id;
				$badgetools = new badgetools($badgeid);
				//fix for naming
				$currentform[0]=$singlebadge;
				$badgetotalavgarray = $badgetools->gettotalsaverages();
				//print_r($badgetotalavgarray);
				$valuearray['btp_id'] = "badge_".$singlebadge->id;
				$valuearray['btp_name'] = $singlebadge->title;
				$valuearray['total_indb']= $badgetotalavgarray['finaltotal'];
				$valuearray['avg_indb']= $badgetotalavgarray['finalavg'];
				$temprating = $badgetotalavgarray['temprating'];
				$valuearray['numr1']=array_sum($temprating[1]);
				$valuearray['numr2']=array_sum($temprating[2]);
				$valuearray['numr3']=array_sum($temprating[3]);
				$valuearray['numr4']=array_sum($temprating[4]);
				$valuearray['numr5']=array_sum($temprating[5]);
				$this->updatetotalavgreviewstableinsert('badge',$valuearray);
			}
		}
	}
	*/
	//used to get back number of ratings for each value
	private function wprp_get_temprating($ratingsarray){
		//fist set to blank instead of null
		for ($x = 0; $x <= 5; $x++) {
			$temprating[$x][]=0;
		}
		foreach ( $ratingsarray as $tempnum ) 
		{
			//need to count number of each rating
			if($tempnum==1){
				$temprating[1][]=1;
			} else if($tempnum==2){
				$temprating[2][]=1;
			} else if($tempnum==3){
				$temprating[3][]=1;
			} else if($tempnum==4){
				$temprating[4][]=1;
			} else if($tempnum==5){
				$temprating[5][]=1;
			}
		}
		return $temprating;
	}
	
	
	/**
	 * Ajax, grab template html to preview on admin
	 * @access  admin
	 * @since   14.4
	 * @return  void
	 */
	public function wprp_previewtemplate_ajax(){
		$tid = stripslashes($_POST['tid']);
		check_ajax_referer('randomnoncestring', 'wpfb_nonce');
		
		$returnarray = $this->wprp_previewtemplate_ajax_get($tid);
		
		$returnjson = json_encode($returnarray);
		
		echo $returnjson;
		die();
	}
	public function wprp_previewtemplate_ajax_get($tid){
		
		$dbmsg = "success";
		$atts = array('tid' => $tid);
		
		$dir = plugin_dir_path( __FILE__ );
		//get the form html here and return it.
		require_once plugin_dir_path( __DIR__ ). 'public/class-wp-google-reviews-public.php';
		$plugin_public_class = new WP_Google_Reviews_Public( $this->get_token(), $this->get_version() );
		
		$templatehtml = $plugin_public_class->wprev_usetemplate_func( $atts, $content = null );

		$returnarray['tid']=$tid;
		$returnarray['ack'] =$dbmsg;
		$returnarray['templatehtml'] =$templatehtml;
		
		return $returnarray;

	}
	
	/**
	 * Ajax, save review template to db
	 * @access  admin
	 * @since   14.4
	 * @return  void
	 */
	public function wprp_savetemplate_ajax(){
		$formdata = stripslashes($_POST['data']);
		$formarray = json_decode($formdata,true);
		//print_r($formarray);
		//die();
		
		check_ajax_referer('randomnoncestring', 'wpfb_nonce');
		
		global $wpdb;
		$table_name = $wpdb->prefix . 'wpfb_post_templates';

		//get form submission values and then save or update
		$t_id = sanitize_text_field($formarray['edittid']);


		$title = htmlentities($formarray['wpfbr_template_title']);
		$template_type = htmlentities($formarray['wpfbr_template_type']);
		$style = htmlentities($formarray['wprevpro_template_style']);
		$display_num = htmlentities($formarray['wpfbr_t_display_num']);
		$display_num_rows = htmlentities($formarray['wpfbr_t_display_num_rows']);
		$display_order = htmlentities($formarray['wpfbr_t_display_order']);
		$hide_no_text = htmlentities($formarray['wpfbr_t_hidenotext']);
		$template_css = htmlentities($formarray['wpfbr_template_css']);
		
		$createslider = htmlentities($formarray['wpfbr_t_createslider']);
		$numslides = htmlentities($formarray['wpfbr_t_numslides']);
		
		$read_more = sanitize_text_field($formarray['wprevpro_t_read_more']);
		$read_more_text = sanitize_text_field($formarray['wprevpro_t_read_more_text']);
		$review_same_height = sanitize_text_field($formarray['wprevpro_t_review_same_height']);

		$slidermobileview ="";
		if(isset($formarray['wprevpro_slidermobileview'])){
		$slidermobileview = sanitize_text_field($formarray['wprevpro_slidermobileview']);
		}

		
		//santize
		$title = sanitize_text_field( $title );
		$template_type = sanitize_text_field( $template_type );
		$display_order = sanitize_text_field( $display_order );
		$template_css = sanitize_text_field( $template_css );
		$display_order = sanitize_text_field( $display_order );

		
		//template misc
		$templatemiscarray = array();
		
		$templatemiscarray['showstars']=sanitize_text_field($formarray['wprevpro_template_misc_showstars']);
		$templatemiscarray['showdate']=sanitize_text_field($formarray['wprevpro_template_misc_showdate']);
		$templatemiscarray['avataropt']=sanitize_text_field($formarray['wprevpro_template_misc_avataropt']);
		$templatemiscarray['showicon']=sanitize_text_field($formarray['wprevpro_template_misc_showicon']);
		$templatemiscarray['bgcolor1']=sanitize_hex_color($formarray['wprevpro_template_misc_bgcolor1']);
		$templatemiscarray['bgcolor2']=sanitize_hex_color($formarray['wprevpro_template_misc_bgcolor2']);
		$templatemiscarray['tcolor1']=sanitize_hex_color($formarray['wprevpro_template_misc_tcolor1']);
		$templatemiscarray['tcolor2']=sanitize_hex_color($formarray['wprevpro_template_misc_tcolor2']);
		$templatemiscarray['tcolor3']=sanitize_hex_color($formarray['wprevpro_template_misc_tcolor3']);
		$templatemiscarray['bradius']=sanitize_text_field($formarray['wprevpro_template_misc_bradius']);
		$templatemiscarray['showmedia']=sanitize_text_field($formarray['wprevpro_t_showmedia']);
		$templatemiscarray['verified']=sanitize_text_field($formarray['wprevpro_template_misc_verified']);
		$templatemiscarray['lastnameformat']=sanitize_text_field($formarray['wprevpro_template_misc_lastname']);
		
		//badge options
		$templatemiscarray['blocation']=sanitize_text_field($formarray['wprevpro_t_blocation']);
		$templatemiscarray['filtersource']=sanitize_text_field($formarray['wprevpro_t_filtersource']);
		
		if(isset($formarray['wprevpro_t_bhreviews'])){
			$templatemiscarray['bhreviews']=sanitize_text_field($formarray['wprevpro_t_bhreviews']);
		}
		if(isset($formarray['wprevpro_t_bhbtn'])){
			$templatemiscarray['bhbtn']=sanitize_text_field($formarray['wprevpro_t_bhbtn']);
		}
		if(isset($formarray['wprevpro_t_bhbased'])){
			$templatemiscarray['bhbased']=sanitize_text_field($formarray['wprevpro_t_bhbased']);
		}
		if(isset($formarray['wprevpro_t_bhphoto'])){
			$templatemiscarray['bhphoto']=sanitize_text_field($formarray['wprevpro_t_bhphoto']);
		}
		if(isset($formarray['wprevpro_t_bhname'])){
			$templatemiscarray['bhname']=sanitize_text_field($formarray['wprevpro_t_bhname']);
		}
		if(isset($formarray['wprevpro_t_bcenter'])){
			$templatemiscarray['bcenter']=sanitize_text_field($formarray['wprevpro_t_bcenter']);
		}
		if(isset($formarray['wprevpro_t_bdropsh'])){
			$templatemiscarray['bdropsh']=sanitize_text_field($formarray['wprevpro_t_bdropsh']);
		}
		if(isset($formarray['wprevpro_t_bhpow'])){
			$templatemiscarray['bhpow']=sanitize_text_field($formarray['wprevpro_t_bhpow']);
		}
		if(isset($formarray['wprevpro_t_bobasedon'])){
			$templatemiscarray['bobasedon']=sanitize_text_field($formarray['wprevpro_t_bobasedon']);
		}
		if(isset($formarray['wprevpro_t_borevus'])){
			$templatemiscarray['borevus']=sanitize_text_field($formarray['wprevpro_t_borevus']);
		}
				//more slider options.
		$templatemiscarray['slideautodelay']=sanitize_text_field($formarray['wpfbr_t_slideautodelay']);
		$templatemiscarray['slidespeed']=sanitize_text_field($formarray['wpfbr_t_slidespeed']);
		if(isset($formarray['wprevpro_sliderautoplay'])){
			$templatemiscarray['sliderautoplay']=sanitize_text_field($formarray['wprevpro_sliderautoplay']);
		}
		if(isset($formarray['wprevpro_sliderhideprevnext'])){
			$templatemiscarray['sliderhideprevnext']=sanitize_text_field($formarray['wprevpro_sliderhideprevnext']);
		}
		if(isset($formarray['wprevpro_sliderhidedots'])){
			$templatemiscarray['sliderhidedots']=sanitize_text_field($formarray['wprevpro_sliderhidedots']);
		}
		if(isset($formarray['wprevpro_sliderfixedheight'])){
			$templatemiscarray['sliderfixedheight']=sanitize_text_field($formarray['wprevpro_sliderfixedheight']);
		}

		$templatemiscarray['bshape']=sanitize_text_field($formarray['wprevpro_t_bshape']);
		$templatemiscarray['bimgsize']=sanitize_text_field($formarray['wprevpro_t_bimgsize']);
		$templatemiscarray['bbradius']=sanitize_text_field($formarray['wprevpro_t_bbradius']);
		$templatemiscarray['bbkcolor']=sanitize_text_field($formarray['wprevpro_t_bbkcolor']);
		$templatemiscarray['bbtnurl']=sanitize_text_field($formarray['wprevpro_t_bbtnurl']);
		$templatemiscarray['bbtncolor']=sanitize_text_field($formarray['wprevpro_t_bbtncolor']);
		$templatemiscarray['bimgurl']=sanitize_text_field($formarray['wprevpro_t_bimgurl']);
		$templatemiscarray['bname']=sanitize_text_field($formarray['wprevpro_t_bname']);
		$templatemiscarray['bnameurl']=sanitize_text_field($formarray['wprevpro_t_bnameurl']);
		$templatemiscarray['blocation']=sanitize_text_field($formarray['wprevpro_t_blocation']);
		
		//read more
		$templatemiscarray['read_more_num']=sanitize_text_field($formarray['wprevpro_t_read_more_num']);
		$templatemiscarray['read_more_color']=sanitize_text_field($formarray['wprevpro_t_read_more_color']);


		$templatemiscjson = json_encode($templatemiscarray);
		
		
		//only save if using pro version
		
		
		$min_rating = sanitize_text_field($formarray['wpfbr_t_min_rating']);
		
			$min_words = "";
			$max_words = "";			
			$rtype = '["google"]';
			$rpage = "";
			$showreviewsbyid="";
			$sliderautoplay = "";
			$sliderdirection = "";
			$sliderarrows = "";
			$sliderdots = "";
			$sliderdelay = "";
			$sliderheight = "";

		$timenow = time();
		
		//+++++++++need to sql escape using prepare+++++++++++++++++++
		//+++++++++++++++++++++++++++++++++++++++++++++++++++++
		//insert or update
			$data = array( 
				'title' => "$title",
				'template_type' => "$template_type",
				'style' => "$style",
				'created_time_stamp' => "$timenow",
				'display_num' => "$display_num",
				'display_num_rows' => "$display_num_rows",
				'display_order' => "$display_order", 
				'hide_no_text' => "$hide_no_text",
				'template_css' => "$template_css", 
				'min_rating' => "$min_rating", 
				'min_words' => "$min_words",
				'max_words' => "$max_words",
				'rtype' => "$rtype", 
				'rpage' => "$rpage",
				'createslider' => "$createslider",
				'numslides' => "$numslides",
				'sliderautoplay' => "$sliderautoplay",
				'sliderdirection' => "$sliderdirection",
				'sliderarrows' => "$sliderarrows",
				'sliderdots' => "$sliderdots",
				'sliderdelay' => "$sliderdelay",
				'sliderheight' => "$sliderheight",
				'showreviewsbyid' => "$showreviewsbyid",
				'template_misc' => "$templatemiscjson",
				'read_more' => "$read_more",
				'read_more_text' => "$read_more_text",
				'slidermobileview' => "$slidermobileview",
				'review_same_height' => "$review_same_height"
				);
			$format = array( 
					'%s',
					'%s',
					'%d',
					'%d',
					'%d',
					'%d',
					'%s',
					'%s',
					'%s',
					'%d',
					'%d',
					'%d',
					'%s',
					'%s',
					'%s',
					'%d',
					'%s',
					'%s',
					'%s',
					'%s',
					'%d',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s'
				); 
		$returnarray['iu'] =''; 
		$returnarray['ack'] ='';
		$returnarray['ackmessage'] ='';
		$returnarray['t_id']='';
		if($t_id==""){
			$returnarray['iu'] ='insert';
			//insert
			$inserttemplate = $wpdb->insert( $table_name, $data, $format );
			$t_id = $wpdb->insert_id;
				if(!$inserttemplate){
					$dbmsg = __('Unable to update. Try refreshing the page. If that does not work then try de-activating and re-activating the plugin.', 'wp-review-slider-pro');
					$returnarray['ack'] ='error';
				} else {
					$dbmsg = __('Template Saved!', 'wp-review-slider-pro');
					$returnarray['ack'] ='success';
				}
		} else {
			//update
			$returnarray['iu'] ='update';
			$updatetempquery = $wpdb->update($table_name, $data, array( 'id' => $t_id ), $format, array( '%d' ));
			if($updatetempquery>0){
				$returnarray['ack'] ='success';
				$dbmsg = __('Template Updated!', 'wp-review-slider-pro');
			} else {
				//$wpdb->show_errors();
				//$wpdb->print_error();
				$returnarray['ack'] ='error';
				$dbmsg = __('Unable to update. Try refreshing the page. If that does not work then try de-activating and re-activating the plugin.', 'wp-review-slider-pro');
			}
			
			
		}
		
		$returnarray['t_id']=$t_id;
		$returnarray['ackmessage'] =$dbmsg;
		$returnjson = json_encode($returnarray);
		
		//can we go ahead and return the html for the preview here?
		$returnpreview = $this->wprp_previewtemplate_ajax_get($t_id);
		$returnarray['templatehtml'] = $returnpreview['templatehtml'];
		
		$returnjson = json_encode($returnarray);
		echo $returnjson;
		die();
	}
		
	
	
	public function get_token() {
		return $this->_token;
	}
	public function get_version() {
		return $this->version;
	}
	
	

}
