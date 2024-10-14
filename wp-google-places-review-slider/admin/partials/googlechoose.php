<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://ljapps.com
 * @since      1.0.0
 *
 * @package    WP_Google_Reviews
 * @subpackage WP_Google_Reviews/admin/partials
 */

    // check user capabilities
    if (!current_user_can('manage_options')) {
        return;
    }
 
    // add error/update messages
 
    // check if the user have submitted the settings
    // wordpress will add the "settings-updated" $_GET parameter to the url
    if (isset($_GET['settings-updated'])) {
        // add settings saved message with the class of "updated"
        add_settings_error('wpfbr_messages', 'wpfbr_message', __('Settings Saved', 'wp-google-reviews'), 'updated');
    }
    // show error/update messages
    settings_errors('wpfbr_messages');
	
	
	//get previous crawls
	//$googlecrawlsarray = Array();
	//$googlecrawlsarray[] =Array("empty");
	//$tempsaved = get_option('wprev_google_crawls');
	//if($tempsaved!=''){
	if ( 'not-exists' === get_option( 'wprev_google_crawls', 'not-exists' ) ) {
		update_option('wprev_google_crawls',json_encode(array(array())));
	}
	$googlecrawlsarray = json_decode(get_option('wprev_google_crawls'),true);
	//}


	//get previous apis if set.
	//$googleapisarray = Array();
	//$googleapisarray[] =Array("empty");
	//$tempsaved = get_option('wprev_google_crawls');
	//if($tempsaved!=''){
	if ( 'not-exists' === get_option( 'wprev_google_apis', 'not-exists' ) ) {
		update_option('wprev_google_apis',json_encode(array(array())));
	}
	$googleapisarray = json_decode(get_option('wprev_google_apis'),true);
	//}	

	

	//check if we need to delete a source here
	if(isset($_GET['ract']) && $_GET['ract']=="del"){
		
		$delplace = urldecode($_GET['place']);
		$delplaceid = urldecode($_GET['placeid']);
		
		if($_GET['type']=="crawl"){
			unset($googlecrawlsarray[$delplace]);
			update_option('wprev_google_crawls',json_encode($googlecrawlsarray) );
		} else if($_GET['type']=="api"){
			unset($googleapisarray[$delplaceid]);
			update_option('wprev_google_apis',json_encode($googleapisarray) );
		}
		//remove all reviews from this place id and delete from total and avg table.
		//==========================
		global $wpdb;
		
		if($delplaceid != ''){
			$table_name_revs = $wpdb->prefix . 'wpfb_reviews';
			$deleterevs = $wpdb->query("DELETE FROM `".$table_name_revs."` WHERE pageid = '".$delplaceid."'");
			
			$table_name_tots = $wpdb->prefix . 'wpfb_total_averages';
			$deletetotsavgs = $wpdb->query("DELETE FROM `".$table_name_tots."` WHERE btp_id = '".$delplaceid."'");

		}

		$googlecrawlsarray = json_decode(get_option('wprev_google_crawls'),true);
		
	}

//echo $googlecrawlsarray;
//print_r($googlecrawlsarray);
?>

<div class="">
<h1></h1>
<div class="wrap" id="wp_rev_maindiv">
<img class="wprev_headerimg" src="<?php echo plugin_dir_url( __FILE__ ) . 'logo.png'; ?>">
<?php 
include("tabmenu.php");
?>	
<div class="wpfbr_margin10">

<?php

//if(!isset($googlecrawlsarray[0])){
?>
<div id='currentsources'>
	  <table class="w3-table-all wpfbr_mb15 welcomediv w3-container w3-white w3-border w3-border-light-gray2 w3-round-small">
    <tr>
	  <th>Business Name</th>
	  <th>Google Place ID</th>
      <th>Download Type</th>
	  <th>Action</th>
    </tr>
<?php
$crawlcount = 0;
foreach ($googlecrawlsarray as $key =>$savedplace) {
//echo "<br>key:".$key;
   // if(isset($key) && $key!=0 && $key!=""){
	if(isset($savedplace['crawl_check']) && is_array($savedplace['crawl_check'])){
		$crawlcount++;
		$tempbusines ="";
		$tempfoundplaceid ="";
		$nhful="";
		
				$tempbusiness = $savedplace['crawl_check']['businessname'];
				$tempfoundplaceid = $savedplace['crawl_check']['foundplaceid'];
				$nhful = $savedplace['nhful'];

		echo "<tr><td> ".$tempbusiness ."</td><td>".$tempfoundplaceid."</td><td> Crawl : ".$savedplace['nhful'] ."</td><td> 
		<a class='w3-button w3-red w3-padding-small' href='?page=wp_google-googlesettings&ract=del&place=".urlencode($key)."&placeid=".urlencode($tempfoundplaceid)."&type=crawl'>Delete</a>
		<a class='w3-button w3-dark-grey w3-padding-small' href='".$urlgooglegooglecrawl."&ract=edit&place=".urlencode($key)."&placeid=".urlencode($tempfoundplaceid)."'>Edit</a>
		<a class='downloadrevs w3-button w3-green w3-padding-small' data-type='crawl' data-placeid='".$tempfoundplaceid."' data-place='".urlencode($key)."' data-nhful='".urlencode($nhful)."'>Download Reviews</a>&nbsp;<img class='buttonloader2 loadinggifchoosepage' width='20' height='20' src='".plugin_dir_url( __FILE__ )."loading.gif' style='display:none;'><span class='googletestresults2'></span>
		</td></tr>";
	}
}
?>
<?php
foreach ($googleapisarray as $key =>$savedplace) {

        //echo "$key => $savedplace\n <br>";
	if(isset($savedplace['google_location_set'])){
	if(is_array($savedplace['google_location_set']) && $savedplace['google_location_set']['place_id']!=""){
		$tempbusines ="";
		$tempfoundplaceid ="";
		$nhful="";

				$tempbusiness = $savedplace['google_location_set']['location'];
				$tempfoundplaceid = $savedplace['google_location_set']['place_id'];
				

		$nhful = $savedplace['google_location_sort'];
		echo "<tr><td> ".$tempbusiness ."</td><td>".$tempfoundplaceid."</td><td> Places API : ".$savedplace['google_location_sort'] ."</td><td> 
		<a class='w3-button w3-red w3-padding-small' href='?page=wp_google-googlesettings&ract=del&place=".urlencode($key)."&placeid=".urlencode($tempfoundplaceid)."&type=api'>Delete</a>
		<a class='w3-button w3-dark-grey w3-padding-small' href='".$urlgoogleapi."&ract=edit&placeid=".urlencode($key)."'>Edit</a>
		<a onclick='getgooglereviewsfunction(\"".$key."\")' class='w3-button w3-green w3-padding-small' data-type='api' data-placeid='".$key."' data-place='".urlencode($tempbusiness)."' data-nhful='".urlencode($nhful)."'>Download Reviews</a>&nbsp;<img class='buttonloader2 loadinggifchoosepage' width='20' height='20' src='".plugin_dir_url( __FILE__ )."loading.gif' style='display:none;'><span class='googletestresults2'></span>
		</td></tr>";

		}
	}
}


?>


  </table>
</div>
<?php
//} else {
	//echo '	<div class="w3-white">
	//<div class="w3-container">
	//<h6>Use the button below to download Google reviews from one or more locations.</h6>
	//</div>
	//</div><br>';
//}
?>
	<div class="w3-padding-8">
		<button id="shownewsourceoption" type="button" class="mt20 w3-btn w3-padding-small2 w3-green">Add New Google Source</button><br><br>
	</div>



<div id="chooseoption" style="display:none;" class="w3-col wpfbr_mb15 welcomediv w3-container w3-white w3-border w3-border-light-gray2 w3-round-small">

	<div class="w3-container w3-padding-16">
	<h4 class="">Choose one or both of these options to download reviews...</h3>
	</div>
<div class="w3-row-padding wppro_choose wpfbr_mb25">
	<div class="w3-col l6">
	<div class="w3-card-4 w3-white">
	<header class="w3-container w3-light-grey">
	  <h4><i class="fa fa-cogs" aria-hidden="true"></i> Crawl Google Review Page</h4>
	</header>
	<div class="w3-container">
	<h5>Pros:</h5>
	  <p>- Will download your Newest 40 or Most Relevant 40 reviews.</p>
	  <p>- Will also download user images on reviews.</p>
	  <p>- No API Key required.</p>
	  <p>- Can also work for service area businesses.</p>
	  <hr>
	  <h5>Cons:</h5>
	  <p>- Limited to only 15 locations allowed.</p>
	  <p>- Can not automatically check for new reviews.</p>
	  <p>- Date must be inferred, since Google does not list exact dates on reviews.</p>
	</div>
	<?php
	if($crawlcount<15){
	?>
	<a class="w3-button w3-block w3-dark-grey" href="<?php echo $urlgooglegooglecrawl; ?>">+ Select</a>
	<?php
	} else {
		
	?>
	<div class="w3-container">
	<p><b>You have reached your max amount of crawl locations. The Pro version has no limit and can download over 90 different review types!</b></p></div>
	<?php
	}
		
	?>
	</div>
	</div>

	<div class="w3-col l6 ">
	<div class="w3-card-4 w3-white">
	<header class="w3-container w3-light-grey">
	  <h4><i class="fa fa-map-o" aria-hidden="true"></i> Google Places API</h4>
	</header>
	<div class="w3-container">
	<h5>Pros:</h5>
	<p>- Official Google Places API Method.</p>
	  <p>- Can download your Newest 5 and/or Most Relevant 5 reviews.</p>
	  <p>- Can automatically check for reviews daily.</p>
	  <hr>
	  <h5>Cons:</h5>
	  <p>- Must have a physical address on Google Maps.</p>
	  <p>- Requires you to obtain Google Places API Key from Google.</p>
	  <p>- Can not download user images on reviews.</p>
	  <p>- Limited to 5 Newest or 5 Most Relevant.</p>
	</div>
	<a class="w3-button w3-block w3-dark-grey" href="<?php echo $urlgoogleapi; ?>&newplace=yes">+ Select</a>
	</div>
	</div>

</div>
<div class="w3-container w3-padding-16"><span class="small_message">
	The Free version is limited to 15 locations. The <a href="https://wpreviewslider.com/">Pro Version</a> of this plugin can download all of your Google reviews from multiple locations and keep them updated automatically!</span></div>
</div>



</div>


<div class="small_message"><p>Do you like this plugin? If so please take a moment to leave a review <a href="https://wordpress.org/plugins/wp-google-reviews/" target="blank">here!</a> If it's missing something then please contact me <a href="https://wpreviewslider.com/contact/" target="blank">here</a>. Thanks!</p><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br></div>

<?php
	/*
echo "googlecrawlsarray:";
print "<pre>";
print_r($googlecrawlsarray);
print "</pre>";	
echo "googleapisarray:";
print "<pre>";
print_r($googleapisarray);
print "</pre>";	

$options = get_option('wpfbr_google_options');

print "<pre>";
print_r($options);
print "</pre>";

//echo $options;
*/
?>

	<div id="popup" class="popup-wrapper wpfbr_hide">
	  <div class="popup-content">
		<div class="popup-title">
		  <button type="button" class="popup-close">&times;</button>
		  <h3 id="popup_titletext"></h3>
		</div>
		<div class="popup-body">
		  <div id="popup_bobytext1"></div>
		  <div id="popup_bobytext2"></div>
		</div>
	  </div>
	</div>
</div>
</div>