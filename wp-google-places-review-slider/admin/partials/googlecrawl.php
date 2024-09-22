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
	//$savedplaceid = esc_html(get_option('wprev_google_crawl_placeid'));
	
	$currentplace = urldecode($_GET['place']);
	$editid="";
	$editplace ="";
	if(isset($_GET['ract']) && $_GET['ract']=="edit"){
		$editidedit="edit";
		$editplace = urldecode($_GET['place']);
	}
	
$googlecrawlsarray = Array();
$googlecrawlsarray[] =Array();
$googlecrawlsarray = json_decode(get_option('wprev_google_crawls'),true);

$savedplaceid = $googlecrawlsarray[$currentplace]['enteredidorterms'];
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
  <div class=" w3-cell w3-padding-small">
    <h4>Google Search Terms or Place ID:</h4>
  </div>
  <div class=" w3-cell w3-cell-middle w3-padding-small">
    <input id="gplaceid" style="width: 300px;" value="<?php echo stripslashes($savedplaceid); ?>" class="w3-input w3-border w3-round" type="text" placeholder="e.g.: ChIJOUW7JL0RYogRgDxol-LP_sU">
  </div>
  <div class=" w3-cell w3-cell-middle w3-padding-small">
    <button id="savetest" data-editplace="<?php echo urlencode($editplace); ?>" type="button" class="w3-btn w3-padding-small2 w3-green w3-small" style="width:120px">Save & Test &nbsp; ❯</button><div id="buttonloader" style="display:none;" class="wprevloader"></div>
  </div>
  <div class="w3-padding-small"><span class="wprevdescription">
  <?php _e('Need help finding your', 'wp-google-reviews'); ?><a href="https://ljapps.com/wp-content/uploads/2021/08/google_search_terms.mp4" target="_blank" style="text-decoration: none;">
<?php _e('Google Search Terms', 'wp-google-reviews'); ?></a> <?php _e('or', 'wp-google-reviews'); ?> <a href="https://ljapps.com/two-methods-to-find-your-google-place-id/" target="_blank" style="text-decoration: none;">
<?php _e('Place ID?', 'wp-google-reviews'); ?></a></span>
</div>
</div>

<?php
//$previouscheck = json_decode(get_option('wprev_google_crawl_check'),true);

$previouscheck = $googlecrawlsarray[$savedplaceid]['crawl_check'];
//print_r($previouscheck);

$tempimg = plugin_dir_url( __FILE__ ) . 'google_small_icon.png';
if(isset($previouscheck['img']) && $previouscheck['img']!='' && $previouscheck['img']!='unknown' && $previouscheck['img']!='(unknown)'){
	$tempimg =$previouscheck['img'];
}
?>
<div id='divgoogletestresults' <?php if(!isset($previouscheck['foundplaceid']) || $previouscheck['foundplaceid']==''){echo 'style="display:none;"';} ?> class="w3-row-padding">
  <div class="w3-padding-small">
	<div id='googletestresults' <?php if(!isset($previouscheck['foundplaceid']) || $previouscheck['foundplaceid']==''){echo 'style="display:none;"';} ?>>
		<div class="w3-card-4">
			<div class="w3-container">
				<div class="w3-row">
				  <div class="w3-col" style="width:85px"><img id='businessimg' src="<?php echo $tempimg; ?>" alt="location logo" class="w3-circle"></div>
				  <div class="w3-rest"><p><strong id='businessname'><?php if($previouscheck['businessname']!=''){echo $previouscheck['businessname'];} ?></strong><br>
					  <span id='website'><?php if($previouscheck['website']!=''){echo $previouscheck['website'];} ?></span><br>
					  <span id='reviewtext'><?php if($previouscheck['rating']!=''){echo 'Rated <b>'.$previouscheck['rating'].'</b> out of <b>'.$previouscheck['totalreviews'].'</b>';} ?></span><br>
					  <a id='googleurl' href='<?php if($previouscheck['googleurl']!=''){echo $previouscheck['googleurl'];} ?>' target="_blank"><?php if($previouscheck['googleurl']!=''){echo $previouscheck['googleurl'];} ?></a>
					</p>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div id='googletestresultserror' style="display:none;" class="w3-panel w3-pale-red w3-display-container w3-border">
		  <span onclick="this.parentElement.style.display='none'" class="w3-button w3-large w3-display-topright">×</span>
		  <p id='googletestresultserrortext'></p>
	</div> 
  </div>
</div>

<div id="divdownloadreviews" <?php if(!isset($previouscheck['foundplaceid']) || $previouscheck['foundplaceid']==''){echo 'style="display:none;"';} ?> class="w3-row-padding w3-padding-32">
  <div class=" w3-padding-small">
	<div>
	<div><h4>Which reviews would you like to download?</h4></div>
	<div>
	<input class="w3-radio" id="sortoptionnewest" type="radio" name="sortoption" value="newest" checked>&nbsp;
	<label for="sortoptionnewest">Newest</label>&nbsp;&nbsp;&nbsp;&nbsp;
	
	<input class="w3-radio" id="sortoptionrelevant" type="radio" name="sortoption" value="relevant" >&nbsp;
	<label for="sortoptionrelevant">Most Relevant</label>&nbsp;&nbsp;&nbsp;&nbsp;
	<div id='smallnote'>Note: It may not always be possible to download the newest reviews. The plugin will default to Most Relevant if it must.</div>
	</div>
	<div class="w3-padding-32">
	<button id="downloadreviews" type="button" class="mt20 w3-btn w3-padding-small2 w3-green">Download Reviews</button><div id="buttonloader2" style="display:none;" class="wprevloader"></div>
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
</div>
	

