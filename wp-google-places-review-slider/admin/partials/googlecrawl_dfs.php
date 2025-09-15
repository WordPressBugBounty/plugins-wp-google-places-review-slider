<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    WP_FB_Reviews
 * @subpackage WP_FB_Reviews/admin/partials
 */
 
     // check user capabilities
    if (!current_user_can('manage_options')) {
        return;
    }
	
	$currentplace = "";
	if(isset($_GET['place'])){
		$currentplace = urldecode($_GET['place']);
	}
	$editid="";
	$editplace ="";
	if(isset($_GET['ract']) && $_GET['ract']=="edit"){
		$editidedit="edit";
		$editplace = urldecode($_GET['place']);
	}
	
$googlecrawlsarray = Array();
$googlecrawlsarray[] =Array();

$googlecrawlsarray = json_decode(get_option('wprev_google_crawls'),true);
$savedplaceid = '';	
$savedsortoption = 'newest'; // Default to newest
$savedtaskid = '';
$savedtaskstatus = '';
$hascompletedtask = false;
$savedbusinessname = '';



if(isset($googlecrawlsarray[$currentplace]['enteredidorterms'])){
	$savedplaceid = $googlecrawlsarray[$currentplace]['enteredidorterms'];
} else if(!isset($googlecrawlsarray[$currentplace]['enteredidorterms'])){
	// Handle case where we only have nhful and no other data - use the key as place ID
	$savedplaceid = stripslashes($currentplace);
}
if(isset($googlecrawlsarray[$currentplace]['nhful'])){
	$savedsortoption = $googlecrawlsarray[$currentplace]['nhful'];
}
if(isset($googlecrawlsarray[$currentplace]['task_id'])){
	$savedtaskid = $googlecrawlsarray[$currentplace]['task_id'];
}
if(isset($googlecrawlsarray[$currentplace]['task_status'])){
	$savedtaskstatus = $googlecrawlsarray[$currentplace]['task_status'];
	if($savedtaskstatus === 'completed'){
		$hascompletedtask = true;
	}
}
if(isset($googlecrawlsarray[$currentplace]['crawl_check']['businessname'])){
	$savedbusinessname = stripslashes($googlecrawlsarray[$currentplace]['crawl_check']['businessname']);
}
//echo '<pre>';
//print_r($googlecrawlsarray);
//echo '</pre>';
//echo '<pre>';
//print_r($currentplace);
//echo '</pre>';
//echo '<pre>';
//print_r($editplace);
//echo '</pre>';
//echo '<pre>';
//print_r($editid);
//echo '</pre>';
//echo '<pre>';
//print_r($editidedit);
//echo '</pre>';

?>
<div class="">
<h1></h1>
<div class="wrap" id="wp_rev_maindiv">
<img class="wprev_headerimg" src="<?php echo plugin_dir_url( __FILE__ ) . 'logo.png'; ?>">
<?php 
include("tabmenu.php");
?>	
<div class="">
	<div class="w3-container w3-padding-16">
	<h3 class=""><?php _e('Connect Google Review Page', 'wp-google-reviews'); ?> </h3>
	</div>
	
<div class="w3-col welcomediv w3-container w3-white w3-border w3-border-light-gray2 w3-round-small">




<div class="w3-row-padding w3-padding-32">
  <?php if($savedbusinessname != ''): ?>
  <div class="w3-padding-small">
    <h3 style="color: #333; margin-bottom: 10px;"><?php echo esc_html($savedbusinessname); ?></h3>
  </div>
  <?php endif; ?>
  <div class=" w3-cell w3-padding-small">
    <h4>Google Search Terms or Place ID:</h4>
  </div>
  <div class=" w3-cell w3-cell-middle w3-padding-small">
    <input id="gplaceid" style="width: 300px;" value="<?php echo stripslashes($savedplaceid); ?>" class="w3-input w3-border w3-round" type="text" placeholder="e.g.: ChIJOUW7JL0RYogRgDxol-LP_sU">
  </div>

  <div class="w3-padding-small"><span class="wprevdescription">
  <?php _e('Need help finding your', 'wp-google-reviews'); ?><a href="https://ljapps.com/wp-content/uploads/2021/08/google_search_terms.mp4" target="_blank" style="text-decoration: none;">
<?php _e('Google Search Terms', 'wp-google-reviews'); ?></a> <?php _e('or', 'wp-google-reviews'); ?> <a href="https://ljapps.com/two-methods-to-find-your-google-place-id/" target="_blank" style="text-decoration: none;">
<?php _e('Place ID?', 'wp-google-reviews'); ?></a></span>
</div>

<div class=" w3-padding-small">
	<div>
	<div><h5>Which reviews would you like to download?</h5></div>
	<div>
	<input class="w3-radio" id="sortoptionnewest" type="radio" name="sortoption" value="newest" <?php echo ($savedsortoption == 'newest') ? 'checked' : ''; ?>>&nbsp;
	<label for="sortoptionnewest">Newest</label>&nbsp;&nbsp;&nbsp;&nbsp;
	
	<input class="w3-radio" id="sortoptionrelevant" type="radio" name="sortoption" value="relevant" <?php echo ($savedsortoption == 'relevant') ? 'checked' : ''; ?> >&nbsp;
	<label for="sortoptionrelevant">Most Relevant</label>&nbsp;&nbsp;&nbsp;&nbsp;
	<div id='smallnote'>Note: It may not always be possible to download the newest reviews. The plugin will default to Most Relevant if it must.</div>
	</div>

  </div>
</div>

<div class=" w3-cell w3-cell-middle w3-padding-small">
	<?php if($hascompletedtask): ?>
		<!-- Show options for completed task -->
		<div class="w3-panel w3-pale-blue w3-border w3-round" style="margin-bottom: 15px;">
			<h5><i class="fa fa-check-circle"></i> Completed Task Found</h5>
			<p>Task ID: <code><?php echo esc_html($savedtaskid); ?></code></p>
			<p>You can either download the completed task or submit a new task for fresh reviews.</p>
		</div>
		
		<button id="downloadcompletedtask" data-task-id="<?php echo esc_attr($savedtaskid); ?>" data-place-id="<?php echo esc_attr($savedplaceid); ?>" type="button" class="w3-btn w3-padding-small2 w3-blue w3-small" style="width:150px; margin-right: 10px;">
			<i class="fa fa-download"></i> Download Completed Task
		</button>
		
		<button id="savetest" data-editplace="<?php echo urlencode($editplace); ?>" type="button" class="w3-btn w3-padding-small2 w3-orange w3-small" style="width:150px">
			<i class="fa fa-refresh"></i> Submit New Task
		</button>
	<?php else: ?>
		<!-- Show normal submit button for new tasks -->
		<button id="savetest" data-editplace="<?php echo urlencode($editplace); ?>" type="button" class="w3-btn w3-padding-small2 w3-green w3-small" style="width:120px">Submit Task&nbsp; ❯</button>
	<?php endif; ?>
	<div id="buttonloader" style="display: none;margin: 10px 0;" class="wprevloader w3-row-padding"></div>
  </div>

</div>
<div id="divdownloadreviews"  class="w3-row-padding">
  <div class=" w3-padding-small">
	<div class="">
	<button id="downloadreviews" type="button" class="mt20 w3-btn w3-padding-small2 w3-green w3-large" style="display:none;">
		<i class="fa fa-download"></i> Download Reviews
	</button>
	<div id="buttonloader2" style="display:none;" class="wprevloader"></div>
	</div>
	
	<div id='googletestresults2' style='display:none;'>
		<div class="w3-panel w3-pale-green w3-display-container  w3-border">
		  <p id='googletestresultstext2'></p>
		</div>
	</div>
	<div id='googletestresultserror2' style="display:none;" class="w3-panel w3-pale-red w3-display-container w3-border">
		  <span onclick="this.parentElement.style.display='none'" class="w3-button w3-large w3-display-topright">×</span>
		  <p id='googletestresultserrortext2'></p>
	</div> 
	</div>
</div>

</div>
</div>
</div>
	

