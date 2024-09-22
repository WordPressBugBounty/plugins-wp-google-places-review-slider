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
	$dbmsg = "";
	$html="";
	$currenttemplate= new stdClass();
	$currenttemplate->id="";
	$currenttemplate->title ="";
	$currenttemplate->template_type ="";
	$currenttemplate->style ="";
	$currenttemplate->created_time_stamp ="";
	$currenttemplate->display_num ="";
	$currenttemplate->display_num_rows ="";
	$currenttemplate->display_order ="";
	$currenttemplate->hide_no_text ="";
	$currenttemplate->template_css ="";
	$currenttemplate->min_rating ="";
	$currenttemplate->min_words ="";
	$currenttemplate->max_words ="";
	$currenttemplate->rtype ="";
	$currenttemplate->rpage ="";
	$currenttemplate->showreviewsbyid ="";
	$currenttemplate->createslider ="yes";
	$currenttemplate->numslides ="3";
	$currenttemplate->sliderautoplay ="";
	$currenttemplate->sliderdirection ="";
	$currenttemplate->sliderarrows ="";
	$currenttemplate->sliderdots ="";
	$currenttemplate->sliderdelay ="";
	$currenttemplate->sliderheight ="";
	$currenttemplate->template_misc ="";
	$currenttemplate->read_more ="";
	$currenttemplate->read_more_text ="read more";
	$currenttemplate->slidermobileview ="";
	
	//echo $this->_token;
	//if token = wp-google-reviews then using free version
	
	//db function variables
	global $wpdb;
	$table_name = $wpdb->prefix . 'wpfb_post_templates';
	
	//form deleting and updating here---------------------------
	if(isset($_GET['taction'])){
		$tid = htmlentities($_GET['tid']);
		$tid = intval($tid);
		//for deleting
		if($_GET['taction'] == "del" && $_GET['tid'] > 0){
			//security
			check_admin_referer( 'tdel_');
			//delete
			$wpdb->delete( $table_name, array( 'id' => $tid ), array( '%d' ) );
		}
		//for updating
		if($_GET['taction'] == "edit" && $_GET['tid'] > 0){
			//security
			check_admin_referer( 'tedit_');
			//get form array
			//$currenttemplate = $wpdb->get_row( "SELECT * FROM ".$table_name." WHERE id = ".$tid );
			$currenttemplate = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".$table_name." WHERE id = %d",$tid));
		}
		
	}
	//------------------------------------------

	//form posting here--------------------------------
	//check to see if form has been posted.
	//if template id present then update database if not then insert as new.

	if (isset($_POST['wpfbr_submittemplatebtn'])){
		//verify nonce wp_nonce_field( 'wpfbr_save_template');
		check_admin_referer( 'wpfbr_save_template');

		//get form submission values and then save or update
		$t_id = htmlentities($_POST['edittid']);
		$title = htmlentities($_POST['wpfbr_template_title']);
		$template_type = htmlentities($_POST['wpfbr_template_type']);
		$style = htmlentities($_POST['wprevpro_template_style']);
		$display_num = htmlentities($_POST['wpfbr_t_display_num']);
		$display_num_rows = htmlentities($_POST['wpfbr_t_display_num_rows']);
		$display_order = htmlentities($_POST['wpfbr_t_display_order']);
		$hide_no_text = htmlentities($_POST['wpfbr_t_hidenotext']);
		$template_css = htmlentities($_POST['wpfbr_template_css']);
		
		$createslider = htmlentities($_POST['wpfbr_t_createslider']);
		$numslides = htmlentities($_POST['wpfbr_t_numslides']);
		
		$read_more = sanitize_text_field($_POST['wprevpro_t_read_more']);
		$read_more_text = sanitize_text_field($_POST['wprevpro_t_read_more_text']);
		
		$review_same_height = sanitize_text_field($_POST['wprevpro_t_review_same_height']);
		

		$slidermobileview ="";
		if(isset($_POST['wprevpro_slidermobileview'])){
		$slidermobileview = sanitize_text_field($_POST['wprevpro_slidermobileview']);
		}

		
		//santize
		$title = sanitize_text_field( $title );
		$template_type = sanitize_text_field( $template_type );
		$display_order = sanitize_text_field( $display_order );
		$template_css = sanitize_text_field( $template_css );
		$display_order = sanitize_text_field( $display_order );

		
		//template misc
		$templatemiscarray = array();
		
		$templatemiscarray['showstars']=sanitize_text_field($_POST['wprevpro_template_misc_showstars']);
		$templatemiscarray['showdate']=sanitize_text_field($_POST['wprevpro_template_misc_showdate']);
		$templatemiscarray['avataropt']=sanitize_text_field($_POST['wprevpro_template_misc_avataropt']);
		$templatemiscarray['showicon']=sanitize_text_field($_POST['wprevpro_template_misc_showicon']);
		$templatemiscarray['bgcolor1']=sanitize_hex_color($_POST['wprevpro_template_misc_bgcolor1']);
		$templatemiscarray['bgcolor2']=sanitize_hex_color($_POST['wprevpro_template_misc_bgcolor2']);
		$templatemiscarray['tcolor1']=sanitize_hex_color($_POST['wprevpro_template_misc_tcolor1']);
		$templatemiscarray['tcolor2']=sanitize_hex_color($_POST['wprevpro_template_misc_tcolor2']);
		$templatemiscarray['tcolor3']=sanitize_hex_color($_POST['wprevpro_template_misc_tcolor3']);
		$templatemiscarray['bradius']=sanitize_text_field($_POST['wprevpro_template_misc_bradius']);
		$templatemiscarray['showmedia']=sanitize_text_field($_POST['wprevpro_t_showmedia']);
		$templatemiscarray['verified']=sanitize_text_field($_POST['wprevpro_template_misc_verified']);
		$templatemiscarray['lastnameformat']=sanitize_text_field($_POST['wprevpro_template_misc_lastname']);
		
		//badge options
		$templatemiscarray['blocation']=sanitize_text_field($_POST['wprevpro_t_blocation']);
		$templatemiscarray['filtersource']=sanitize_text_field($_POST['wprevpro_t_filtersource']);
		
		if(isset($_POST['wprevpro_t_bhreviews'])){
			$templatemiscarray['bhreviews']=sanitize_text_field($_POST['wprevpro_t_bhreviews']);
		}
		if(isset($_POST['wprevpro_t_bhbtn'])){
			$templatemiscarray['bhbtn']=sanitize_text_field($_POST['wprevpro_t_bhbtn']);
		}
		if(isset($_POST['wprevpro_t_bhbased'])){
			$templatemiscarray['bhbased']=sanitize_text_field($_POST['wprevpro_t_bhbased']);
		}
		if(isset($_POST['wprevpro_t_bhphoto'])){
			$templatemiscarray['bhphoto']=sanitize_text_field($_POST['wprevpro_t_bhphoto']);
		}
		if(isset($_POST['wprevpro_t_bhname'])){
			$templatemiscarray['bhname']=sanitize_text_field($_POST['wprevpro_t_bhname']);
		}
		if(isset($_POST['wprevpro_t_bcenter'])){
			$templatemiscarray['bcenter']=sanitize_text_field($_POST['wprevpro_t_bcenter']);
		}
		if(isset($_POST['wprevpro_t_bdropsh'])){
			$templatemiscarray['bdropsh']=sanitize_text_field($_POST['wprevpro_t_bdropsh']);
		}
		if(isset($_POST['wprevpro_t_bhpow'])){
			$templatemiscarray['bhpow']=sanitize_text_field($_POST['wprevpro_t_bhpow']);
		}
		
		//more slider options.
		$templatemiscarray['slideautodelay']=sanitize_text_field($_POST['wpfbr_t_slideautodelay']);
		$templatemiscarray['slidespeed']=sanitize_text_field($_POST['wpfbr_t_slidespeed']);
		if(isset($_POST['wprevpro_sliderautoplay'])){
			$templatemiscarray['sliderautoplay']=sanitize_text_field($_POST['wprevpro_sliderautoplay']);
		}
		if(isset($_POST['wprevpro_sliderhideprevnext'])){
			$templatemiscarray['sliderhideprevnext']=sanitize_text_field($_POST['wprevpro_sliderhideprevnext']);
		}
		if(isset($_POST['wprevpro_sliderhidedots'])){
			$templatemiscarray['sliderhidedots']=sanitize_text_field($_POST['wprevpro_sliderhidedots']);
		}
		if(isset($_POST['wprevpro_sliderfixedheight'])){
			$templatemiscarray['sliderfixedheight']=sanitize_text_field($_POST['wprevpro_sliderfixedheight']);
		}
		
		$templatemiscarray['bbradius']=sanitize_text_field($_POST['wprevpro_t_bbradius']);
		$templatemiscarray['bbkcolor']=sanitize_text_field($_POST['wprevpro_t_bbkcolor']);
		$templatemiscarray['bbtnurl']=sanitize_text_field($_POST['wprevpro_t_bbtnurl']);
		$templatemiscarray['bbtncolor']=sanitize_text_field($_POST['wprevpro_t_bbtncolor']);
		$templatemiscarray['bimgurl']=sanitize_text_field($_POST['wprevpro_t_bimgurl']);
		$templatemiscarray['bshape']=sanitize_text_field($_POST['wprevpro_t_bshape']);
		$templatemiscarray['bimgsize']=sanitize_text_field($_POST['wprevpro_t_bimgsize']);
		$templatemiscarray['bname']=sanitize_text_field($_POST['wprevpro_t_bname']);
		$templatemiscarray['bnameurl']=sanitize_text_field($_POST['wprevpro_t_bnameurl']);
		$templatemiscarray['blocation']=sanitize_text_field($_POST['wprevpro_t_blocation']);

		//read more
		$templatemiscarray['read_more_num']=sanitize_text_field($_POST['wprevpro_t_read_more_num']);


		$templatemiscjson = json_encode($templatemiscarray);
		
		
		//only save if using pro version
		
		
		$min_rating = sanitize_text_field($_POST['wpfbr_t_min_rating']);
		
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
				'review_same_height' => "$review_same_height",
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

		if($t_id==""){
			//insert
			$wpdb->insert( $table_name, $data, $format );
				//exit( var_dump( $wpdb->last_error ) );
				//Print last SQL query string
				//$wpdb->last_query;
				// Print last SQL query result
				//$wpdb->last_result;
				// Print last SQL query Error
				//$wpdb->last_error;
		} else {
			//update
			$updatetempquery = $wpdb->update($table_name, $data, array( 'id' => $t_id ), $format, array( '%d' ));
			if($updatetempquery>0){
				$dbmsg = '<div id="setting-error-wpfbr_message" class="updated settings-error notice is-dismissible">'.__('<p><strong>Template Updated!</strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>', 'wp-google-reviews').'</div>';
			} else {
				//exit( var_dump( $wpdb->last_error ) );
				//Print last SQL query string
				$wpdb->last_query;
				// Print last SQL query result
				$wpdb->last_result;
				// Print last SQL query Error
				$wpdb->last_error;
				exit( var_dump( $wpdb->last_error ) );
			}
		}
		
	}

	//Get list of all current forms--------------------------
	$currentforms = $wpdb->get_results("SELECT id, title, template_type, created_time_stamp, style FROM $table_name WHERE `rtype` LIKE '%google%' ");
	
	//-------------------------------------------------------
	

	
	//check to see if reviews are in database
	//total number of rows
	$reviews_table_name = $wpdb->prefix . 'wpfb_reviews';
	$reviewtotalcount = $wpdb->get_var( 'SELECT COUNT(*) FROM '.$reviews_table_name );
	if($reviewtotalcount<1){
		$dbmsg = $dbmsg . '<div id="setting-error-wpfbr_message" class="updated settings-error notice is-dismissible">'.__('<p><strong>No reviews found. Please visit the <a href="?page=wp_google-googlesettings">Get Google Reviews</a> page to retrieve reviews from Google. </strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>', 'wp-google-reviews').'</div>';
	}
	
	//add thickbox
	add_thickbox();
	
?>
<div class="">

<div class="wrap" id="wp_rev_maindiv">
<h1 class=""></h1>
<img class="wprev_headerimg" src="<?php echo plugin_dir_url( __FILE__ ) . 'logo.png'; ?>">
<?php 
include("tabmenu.php");
?>	
<div class="wpfbr_margin10">
	<a id="wpfbr_helpicon_posts" class="wpfbr_btnicononly button dashicons-before dashicons-editor-help"></a>
	<a id="wpfbr_addnewtemplate" class="button dashicons-before dashicons-plus-alt"> <?php _e('Add New Reviews Template', 'wp-google-reviews'); ?></a>
</div>

<?php
//display message
echo $dbmsg;
		$html .= '
		<table class="wp-list-table widefat striped posts">
			<thead>
				<tr>
					<th scope="col" width="30px" class="manage-column">'.__('ID', 'wp-google-reviews').'</th>
					<th scope="col" class="manage-column">'.__('Title', 'wp-google-reviews').'</th>
					<th scope="col" width="170px" class="manage-column">'.__('Date Created', 'wp-google-reviews').'</th>
					<th scope="col" width="300px" class="manage-column">'.__('Action', 'wp-google-reviews').'</th>
				</tr>
				</thead>
			<tbody id="review_list">';
	$haswidgettemplate = false;	//for hiding widget type, going to be phasing widget types out.
	foreach ( $currentforms as $currentform ) 
	{
		//delete any that are missing a name.
		if($currentform->title==""){
			$wpdb->delete( $table_name, array( 'id' => $currentform->id ), array( '%d' ) );
		}
		
		//remove query args we just used
		$urltrimmed = remove_query_arg( array('taction', 'id') );
		$tempeditbtn =  add_query_arg(  array(
			'taction' => 'edit',
			'tid' => "$currentform->id",
			),$urltrimmed);
			
		$url_tempeditbtn = wp_nonce_url( $tempeditbtn, 'tedit_');
			
		$tempdelbtn = add_query_arg(  array(
			'taction' => 'del',
			'tid' => "$currentform->id",
			),$urltrimmed) ;
			
		$url_tempdelbtn = wp_nonce_url( $tempdelbtn, 'tdel_');
		
		if($currentform->template_type=='widget'){
			$haswidgettemplate = true;
		}	
		if($currentform->title!=""){
		$html .= '<tr id="'.$currentform->id.'">
				<th scope="col" class="wpfbr_upgrade_needed manage-column">'.$currentform->id.'</th>
				<th scope="col" class="wpfbr_upgrade_needed manage-column"><b>'.$currentform->title.'</b></th>
				<th scope="col" class="wpfbr_upgrade_needed manage-column">'.date("F j, Y",$currentform->created_time_stamp) .'</th>
				<th scope="col" class="manage-column" templateid="'.$currentform->id.'" templatetype="'.$currentform->template_type.'"><a href="'.$url_tempeditbtn.'" class="button button-primary dashicons-before dashicons-admin-generic"> '.__('Edit', 'wp-google-reviews').'</a> <a href="'.$url_tempdelbtn.'" class="button button-secondary dashicons-before dashicons-trash"> '.__('Delete', 'wp-google-reviews').'</a> <a class="wpfbr_displayshortcode button button-secondary dashicons-before dashicons-visibility"> '.__('Shortcode', 'wp-google-reviews').'</a></th>
			</tr>';
		}
	}
	if($reviewtotalcount<1){
		$html .= "<tr><th colspan=4>".__('Please download some reviews on the "Get Google Reviews" page.', 'wp-google-reviews')."</th></tr>";
	} else if(count($currentforms)<1){
		$html .= "<tr><th colspan=4>".__('Use the "Add New Reviews Template" button to create a review slider or grid for your site. ', 'wp-google-reviews')."</th></tr>";
	}
	
		$html .= '</tbody></table>';
			
 echo $html;			
?>


<div class="wpfbr_margin10 w3-white" id="wpfbr_new_template">
<form name="newtemplateform" id="newtemplateform" action="?page=wp_google-templates_posts" method="post" onsubmit="return validateForm()">
	<table class="wpfbr_margin10 form-table ">
		<tbody>
			<tr class="wpfbr_row">
				<th scope="row">
					* <?php _e('Template Title:', 'wp-google-reviews'); ?>
				</th>
				<td>
					<input id="wpfbr_template_title" data-custom="custom" type="text" name="wpfbr_template_title" placeholder="<?php _e('Enter a title/name', 'wp-google-reviews'); ?>" value="<?php echo $currenttemplate->title; ?>" required>
				</td>
			</tr>
			<tr <?php if($haswidgettemplate==false){echo "style='display:none;'";} ?> class="wpfbr_row">
				<th scope="row">
					<?php _e('Choose Template Type:', 'wp-google-reviews'); ?>
				</th>
				<td><div id="divtemplatestyles">

					<input type="radio" name="wpfbr_template_type" id="wpfbr_template_type1-radio" value="post" checked="checked">
					<label for="wpfbr_template_type1-radio"><?php _e('Post or Page', 'wp-google-reviews'); ?></label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

					<input type="radio" name="wpfbr_template_type" id="wpfbr_template_type2-radio" value="widget" <?php if($currenttemplate->template_type== "widget"){echo 'checked="checked"';}?>>
					<label for="wpfbr_template_type2-radio"><?php _e('Widget Area', 'wp-google-reviews'); ?></label>
					</div>
					<p class="description">
					<?php _e('Are you going to use this on a Page/Post or in a Widget area like your sidebar?', 'wp-google-reviews'); ?></p>
				</td>
			</tr>
		</table>
			

<h2 class="nav-tab-wrapper">
	<span id="settingtab0" class="settingtab nav-tab cursorpointer gotopage0 nav-tab-active">Template Style</span>
	<span id="settingtab1" class="settingtab nav-tab cursorpointer gotopage1">General Settings</span>
	<span id="settingtab2" class="settingtab nav-tab cursorpointer gotopage2">Filter Settings</span>
	<span id="settingtab3" class="settingtab nav-tab cursorpointer gotopage3">Badge Settings</span>
</h2>
<table id="settingtable0" class="form-table settingstable ">
	<tr class="wprevpro_row">
				<td>
					<div class="w3_wprs-row">
						  <div class="w3_wprs-col s6">
							<div class="w3_wprs-col s6">
								<div class="wprevpre_temp_label_row">
								<?php _e('Style:', 'wp-google-reviews'); ?>
								</div>
								<div class="wprevpre_temp_label_row">
								<?php _e('Show Stars:', 'wp-google-reviews'); ?>
								</div>
								<div class="wprevpre_temp_label_row">
								<?php _e('Show Verified:', 'wp-google-reviews'); ?>
								</div>
								<div class="wprevpre_temp_label_row">
								<?php _e('Show Date:', 'wp-google-reviews'); ?>
								</div>
								<div class="wprevpre_temp_label_row">
								<?php _e('Last Name:', 'wp-review-slider-pro'); ?>
								</div>
								<div class="wprevpre_temp_label_row">
								<?php _e('Display Avatar:', 'wp-google-reviews'); ?>
								</div>
								<div class="wprevpre_temp_label_row">
								<?php _e('Show Icon:', 'wp-google-reviews'); ?>
								</div>
								<div class="wprevpre_temp_label_row">
								<?php _e('Border Radius:', 'wp-google-reviews'); ?>
								</div>
								<div class="wprevpre_temp_label_row">
								<?php _e('Background Color 1:', 'wp-google-reviews'); ?>
								</div>
								<div class="wprevpre_temp_label_row wprevpre_bgcolor2">
								<?php _e('Background Color 2:', 'wp-google-reviews'); ?>
								</div>
								<div class="wprevpre_temp_label_row">
								<?php _e('Text Color 1:', 'wp-google-reviews'); ?>
								</div>
								<div class="wprevpre_temp_label_row">
								<?php _e('Text Color 2:', 'wp-google-reviews'); ?>
								</div>
								<div class="wprevpre_temp_label_row wprevpre_tcolor3">
								<?php _e('Text Color 3:', 'wp-google-reviews'); ?>
								</div>
							</div>
							<div class="w3_wprs-col s6">
								<div class="wprevpre_temp_label_row">
									<select name="wprevpro_template_style" id="wprevpro_template_style">
									  <option value="1" <?php if($currenttemplate->style=='1' || $currenttemplate->style==""){echo "selected";} ?>>Style 1</option>
									</select>
								</div>
				<?php
				//echo $currenttemplate->template_misc;
				$template_misc_array = json_decode($currenttemplate->template_misc, true);
				if(!is_array($template_misc_array)){
					$template_misc_array=array();
					$template_misc_array['showstars']="";
					$template_misc_array['showdate']="";
					$template_misc_array['bgcolor1']="";
					$template_misc_array['bgcolor2']="";
					$template_misc_array['tcolor1']="";
					$template_misc_array['tcolor2']="";
					$template_misc_array['tcolor3']="";
					$template_misc_array['bradius']="0";
					$template_misc_array['showicon']="yes";
				}
				if(!isset($template_misc_array['showicon'])){
					$template_misc_array['showicon']="yes";
				}
				if(!isset($template_misc_array['avataropt'])){
					$template_misc_array['avataropt']="show";
				}
				if(!isset($template_misc_array['verified'])){
					$template_misc_array['verified']="yes1";
				}
				if(!isset($template_misc_array['lastnameformat'])){
					$template_misc_array['lastnameformat']='show';
				}
				?>
								<div class="wprevpre_temp_label_row">
									<select name="wprevpro_template_misc_showstars" id="wprevpro_template_misc_showstars">
									  <option value="yes" <?php if($template_misc_array['showstars']=='yes'){echo "selected";} ?>>Yes</option>
									  <option value="no" <?php if($template_misc_array['showstars']=='no'){echo "selected";} ?>>No</option>
									</select>
								</div>
								<div class="wprevpre_temp_label_row">
									<select name="wprevpro_template_misc_verified" id="wprevpro_template_misc_verified">
										<option value="no" <?php if($template_misc_array['verified']=='no' || $template_misc_array['verified']==''){echo "selected";} ?>><?php _e('No', 'wp-google-reviews'); ?></option>
										<option value="yes1" <?php if($template_misc_array['verified']=='yes1'){echo "selected";} ?>><?php _e('Yes', 'wp-google-reviews'); ?></option>
									  
									</select>
								</div>
								<div class="wprevpre_temp_label_row">
									<select name="wprevpro_template_misc_showdate" id="wprevpro_template_misc_showdate">
									  <option value="yes" <?php if($template_misc_array['showdate']=='yes'){echo "selected";} ?>><?php _e('Yes', 'wp-google-reviews'); ?></option>
									  <option value="no" <?php if($template_misc_array['showdate']=='no'){echo "selected";} ?>><?php _e('No', 'wp-google-reviews'); ?></option>
									</select>
								</div>
								
								<div class="wprevpre_temp_label_row firstlastnamerow">
									<select name="wprevpro_template_misc_lastname" id="wprevpro_template_misc_lastname">
									  <option value="show" <?php if($template_misc_array['lastnameformat']=='show'){echo "selected";} ?>><?php _e('Show', 'wp-google-reviews'); ?></option>
									  <option value="hide" <?php if($template_misc_array['lastnameformat']=='hide'){echo "selected";} ?>><?php _e('Hide', 'wp-google-reviews'); ?></option>
									  <option value="initial" <?php if($template_misc_array['lastnameformat']=='initial'){echo "selected";} ?>><?php _e('Initial', 'wp-google-reviews'); ?></option>
									</select>
								</div>
								
								<div class="wprevpre_temp_label_row">
									<select name="wprevpro_template_misc_avataropt" id="wprevpro_template_misc_avataropt">
									  <option value="show" <?php if($template_misc_array['avataropt']=='show'){echo "selected";} ?>><?php _e('Yes', 'wp-google-reviews'); ?></option>
									  <option value="hide" <?php if($template_misc_array['avataropt']=='hide'){echo "selected";} ?>><?php _e('No', 'wp-google-reviews'); ?></option>
									  <option value="mystery" <?php if($template_misc_array['avataropt']=='mystery'){echo "selected";} ?>><?php _e('Mystery', 'wp-google-reviews'); ?></option>
									  <option value="init" <?php if($template_misc_array['avataropt']=='init'){echo "selected";} ?>><?php _e('Initial', 'wp-google-reviews'); ?></option>
									</select>
								</div>
								
								<div class="wprevpre_temp_label_row">
									<select name="wprevpro_template_misc_showicon" id="wprevpro_template_misc_showicon">
									  <option value="no" <?php if($template_misc_array['showicon']=='no'){echo "selected";} ?>>No</option>
									  <option value="yes" <?php if($template_misc_array['showicon']=='yes'){echo "selected";} ?>>Yes</option>
									  <option value="lin" <?php if($template_misc_array['showicon']=='lin'){echo "selected";} ?>>Yes + Link</option>
									</select>
								</div>
								<div class="wprevpre_temp_label_row">
									<input id="wprevpro_template_misc_bradius" type="number" min="0" name="wprevpro_template_misc_bradius" placeholder="" value="<?php echo $template_misc_array['bradius']; ?>" style="width: 4em">
								</div>
								<div class="wprevpre_temp_label_row">
									<input type="text" data-alpha="true" value="<?php echo esc_html($template_misc_array['bgcolor1']); ?>" name="wprevpro_template_misc_bgcolor1" id="wprevpro_template_misc_bgcolor1" class="my-color-field" />
								</div>
								<div class="wprevpre_temp_label_row wprevpre_bgcolor2">
									<input type="text" data-alpha="true" value="<?php echo esc_html($template_misc_array['bgcolor2']); ?>" name="wprevpro_template_misc_bgcolor2" id="wprevpro_template_misc_bgcolor2" class="my-color-field" />
								</div>
								<div class="wprevpre_temp_label_row">
									<input type="text" value="<?php echo esc_html($template_misc_array['tcolor1']); ?>" name="wprevpro_template_misc_tcolor1" id="wprevpro_template_misc_tcolor1" class="my-color-field" />
								</div>
								<div class="wprevpre_temp_label_row">
									<input type="text" value="<?php echo esc_html($template_misc_array['tcolor2']); ?>" name="wprevpro_template_misc_tcolor2" id="wprevpro_template_misc_tcolor2" class="my-color-field" />
								</div>
								<div class="wprevpre_temp_label_row wprevpre_tcolor3">
									<input type="text" value="<?php echo esc_html($template_misc_array['tcolor3']); ?>" name="wprevpro_template_misc_tcolor3" id="wprevpro_template_misc_tcolor3" class="my-color-field" />
								</div>
								
								
								<a id="wprevpro_pre_resetbtn" class="button"><?php _e('Reset Colors', 'wp-google-reviews'); ?></a>
							</div>
						  </div>
						  <div class="w3_wprs-col s6" id="">
								<div class="w3_wprs-col" id="wprevpro_template_preview">

								</div>
						  
						  <p class="description"><i>
						<?php _e('Date format is based on your WordPress > Settings value.', 'wp-google-reviews'); ?></i></p>
						<div>
							<?php _e('Custom CSS:', 'wp-google-reviews'); ?><br>
							<textarea name="wpfbr_template_css" id="wpfbr_template_css" cols="50" rows="4"><?php echo esc_html($currenttemplate->template_css); ?></textarea>
							<p class="description">
							<?php _e('Enter custom CSS code to control the look even more.', 'wp-google-reviews'); ?></p>
						</div>
						</div>
					</div>
					<p class="description">
					<?php _e('More styles and options available in <a href="https://wpreviewslider.com/">Pro Version</a> of plugin!', 'wp-google-reviews'); ?></p>
				</td>
			</tr>
			<tr class="wprevpro_row">
				<th scope="row" colspan="1">
				<span class="nextprevbtn w3-green button button-secondary dashicons-before dashicons-arrow-right-after gotopage1">Next</span>
				</th>
			</tr>

			</table>
<table id="settingtable1" class="form-table settingstable " style="display:none;">
			<tr class="wpfbr_row">
				<th scope="row">
					<?php _e('Number of Reviews', 'wp-google-reviews'); ?><a class="wprevpro_helpicon_p wprevpro_btnicononlyhelp dashicons-before dashicons-editor-help"></a>
				</th>
				<td><div class="divtemplatestyles">
					<label for="wpfbr_t_display_num"><?php _e('How many per a row?', 'wp-google-reviews'); ?>&nbsp;</label>
					<select name="wpfbr_t_display_num" id="wpfbr_t_display_num">
					  <option value="1" <?php if($currenttemplate->display_num==1){echo "selected";} ?>>1</option>
					  <option value="2" <?php if($currenttemplate->display_num==2){echo "selected";} ?>>2</option>
					  <option value="3" <?php if($currenttemplate->display_num==3 || $currenttemplate->display_num==""){echo "selected";} ?>>3</option>
					  <option value="4" <?php if($currenttemplate->display_num==4){echo "selected";} ?>>4</option>
					</select>
					
					<label for="wpfbr_t_display_num_rows"><?php _e('How many total rows?', 'wp-google-reviews'); ?>&nbsp;</label>
					<input id="wpfbr_t_display_num_rows" type="number" name="wpfbr_t_display_num_rows" placeholder="" value="<?php if($currenttemplate->display_num_rows>0){echo $currenttemplate->display_num_rows;} else {echo "1";}?>">
					
					</div>
					<p class="description">
					<?php _e('How many reviews to display on the page at a time.', 'wp-google-reviews'); ?></p>
				</td>
			</tr>
			
			<?php
			if(!isset($template_misc_array['sliderhideprevnext'])){
					$template_misc_array['sliderhideprevnext']='';
			}
			if(!isset($template_misc_array['sliderautoplay'])){
					$template_misc_array['sliderautoplay']='';
			}
			if(!isset($template_misc_array['slidespeed'])){
					$template_misc_array['slidespeed']='';
			}
			if(!isset($template_misc_array['slideautodelay'])){
					$template_misc_array['slideautodelay']='';
			}
			if(!isset($template_misc_array['sliderhidedots'])){
					$template_misc_array['sliderhidedots']='';
			}
			if(!isset($template_misc_array['sliderhidedots'])){
					$template_misc_array['sliderhidedots']='';
			}
			if(!isset($template_misc_array['sliderfixedheight'])){
					$template_misc_array['sliderfixedheight']='';
			}
			?>

			<tr class="wpfbr_row">
				<th scope="row"  style="min-width:220px">
					<?php _e('Slider or Grid', 'wp-google-reviews'); ?><a class="wprevpro_helpicon_p wprevpro_btnicononlyhelp dashicons-before dashicons-editor-help"></a>
				</th>
				<td>
					<div class="divtemplatestyles">
						<label for="wpfbr_t_createslider"><?php _e('', 'wp-google-reviews'); ?>&nbsp;</label>
						<select name="wpfbr_t_createslider" id="wpfbr_t_createslider">
							<option value="no" <?php if($currenttemplate->createslider=="no"){echo "selected";} ?>><?php _e('Grid', 'wp-google-reviews'); ?></option>
							<option value="yes" <?php if($currenttemplate->createslider=="yes"){echo "selected";} ?>><?php _e('Slider', 'wp-google-reviews'); ?></option>
						</select>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<label for="wpfbr_t_display_num_rows"><?php _e('Total slides:', 'wp-google-reviews'); ?>&nbsp;</label>
						<select name="wpfbr_t_numslides" id="wpfbr_t_numslides">
							<option value="2" <?php if($currenttemplate->numslides=="2"){echo "selected";} ?>>2</option>
							<option value="3" <?php if($currenttemplate->numslides=="3"){echo "selected";} ?>>3</option>
							<option value="4" <?php if($currenttemplate->numslides=="4"){echo "selected";} ?>>4</option>
							<option value="5" <?php if($currenttemplate->numslides=="5"){echo "selected";} ?>>5</option>
							<option value="6" <?php if($currenttemplate->numslides=="6"){echo "selected";} ?>>6</option>
							<option value="7" <?php if($currenttemplate->numslides=="7"){echo "selected";} ?>>7</option>
							<option value="8" <?php if($currenttemplate->numslides=="8"){echo "selected";} ?>>8</option>
							<option value="9" <?php if($currenttemplate->numslides=="9"){echo "selected";} ?>>9</option>
							<option value="10" <?php if($currenttemplate->numslides=="10"){echo "selected";} ?>>10</option>
						</select>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<label for="wpfbr_t_slidespeed"><?php _e('Slide speed:', 'wp-google-reviews'); ?>&nbsp;</label>
						<input id="wpfbr_t_slidespeed" type="number" name="wpfbr_t_slidespeed" placeholder="" value="<?php if($template_misc_array['slidespeed']>0){echo $template_misc_array['slidespeed'];} else {echo "1";}?>" style="width: 4em">
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<label for="wpfbr_t_slideautodelay"><?php _e('Auto-play delay:', 'wp-google-reviews'); ?>&nbsp;</label>
						<input id="wpfbr_t_slideautodelay" type="number" name="wpfbr_t_slideautodelay" placeholder="" value="<?php if($template_misc_array['slideautodelay']>0){echo $template_misc_array['slideautodelay'];} else {echo "5";}?>" style="width: 4em">
						
						
					</div>
					<div class="divmoreslidersettings">
						<div class="badgeinfo">
							<div class="badgeinfosetting checkboxes">
							<input type="checkbox" id="wprevpro_slidermobileview" name="wprevpro_slidermobileview" value="one" <?php if($currenttemplate->slidermobileview){echo 'checked="checked"';}?>>
							<label for="wprevpro_slidermobileview"> <?php _e('One review on mobile', 'wp-google-reviews'); ?></label>
							</div>

							<div class="badgeinfosetting checkboxes">
							<input type="checkbox" id="wprevpro_sliderhideprevnext" name="wprevpro_sliderhideprevnext" value="yes" <?php if($template_misc_array['sliderhideprevnext']== "yes"){echo 'checked="checked"';}?>>
							<label for="wprevpro_sliderhideprevnext"> <?php _e('Hide Prev/Next', 'wp-google-reviews'); ?></label>
							</div>
							
							<div class="badgeinfosetting checkboxes">
							<input type="checkbox" id="wprevpro_sliderhidedots" name="wprevpro_sliderhidedots" value="yes" <?php if($template_misc_array['sliderhidedots']== "yes"){echo 'checked="checked"';}?>>
							<label for="wprevpro_sliderhidedots"> <?php _e('Hide Dots', 'wp-google-reviews'); ?></label>
							</div>
							
							<div class="badgeinfosetting checkboxes">
							<input type="checkbox" id="wprevpro_sliderautoplay" name="wprevpro_sliderautoplay" value="yes" <?php if($template_misc_array['sliderautoplay']== "yes"){echo 'checked="checked"';}?>>
							<label for="wprevpro_sliderautoplay"> <?php _e('Auto-play', 'wp-google-reviews'); ?></label>
							</div>
							
							<div class="badgeinfosetting checkboxes">
							<input type="checkbox" id="wprevpro_sliderfixedheight" name="wprevpro_sliderfixedheight" value="yes" <?php if($template_misc_array['sliderfixedheight']== "yes"){echo 'checked="checked"';}?>>
							<label for="wprevpro_animateHeight"> <?php _e('Static Height', 'wp-google-reviews'); ?></label>
							</div>
							
						</div>
					</div>
					<p class="description">
					<?php _e('Allows you to create a slide show with your reviews.', 'wp-google-reviews'); ?><br>

					</p>

			
				</td>
			</tr>
			
			<?php
			if(!isset($currenttemplate->read_more)){
				$currenttemplate->read_more='';
				$currenttemplate->read_more_text='';
			}
			if(!isset($template_misc_array['read_more_num'])){
					$template_misc_array['read_more_num']='30';
			}
			?>
			<tr class="wprevpro_row">
				<th scope="row">
					<?php _e('Add Read More Link', 'wp-google-reviews'); ?><a class="wprevpro_helpicon_p wprevpro_btnicononlyhelp dashicons-before dashicons-editor-help"></a>
				</th>
				<td><div class="divtemplatestyles">
					<label for="wprevpro_t_read_more"><?php _e('', 'wp-google-reviews'); ?></label>
					<select name="wprevpro_t_read_more" id="wprevpro_t_read_more" class="mt2">
						<option value="no" <?php if($currenttemplate->read_more=='no' || $currenttemplate->read_more==''){echo "selected";} ?>>No</option>
						<option value="yes" <?php if($currenttemplate->read_more=='yes'){echo "selected";} ?>>Yes</option>
					</select>&nbsp;&nbsp;
					<label for="wprevpro_t_read_more_text">&nbsp;&nbsp;<?php _e('Read More Text:', 'wp-google-reviews'); ?>&nbsp;</label>
					<input id="wprevpro_t_read_more_text" type="text" name="wprevpro_t_read_more_text" placeholder="read more" value="<?php if($currenttemplate->read_more_text!=''){echo $currenttemplate->read_more_text;} else {echo "read more";}?>" style="width: 6em">
					<label for="wprevpro_t_read_more_num">&nbsp;&nbsp;<?php _e('Number of Words:', 'wp-google-reviews'); ?>&nbsp;</label>
					<input id="wprevpro_t_read_more_num" type="number" name="wprevpro_t_read_more_num" placeholder="30" value="<?php if($template_misc_array['read_more_num']!=''){echo $template_misc_array['read_more_num'];} else {echo "30";}?>" style="width: 4em">
					</div>
					<p class="description">
					<?php _e('Allows you to cut off long reviews and add a read more link that will show the rest of the review when clicked.', 'wp-google-reviews'); ?></p>
				</td>
			</tr>

			<?php
			if(!isset($currenttemplate->review_same_height)){
				$currenttemplate->review_same_height='no';
			}
			?>
			<tr class="wprevpro_row">
				<th scope="row">
					<?php _e('Reviews Same Height', 'wp-google-reviews'); ?><a class="wprevpro_helpicon_p wprevpro_btnicononlyhelp dashicons-before dashicons-editor-help"></a>
				</th>
				<td><div class="divtemplatestyles">
					<select name="wprevpro_t_review_same_height" id="wprevpro_t_review_same_height">
								<option value="no" <?php if($currenttemplate->review_same_height=="no" || $currenttemplate->review_same_height==""){echo "selected";} ?>><?php _e('No', 'wp-review-slider-pro'); ?></option>
								<option value="yes" <?php if($currenttemplate->review_same_height=="yes"){echo "selected";} ?>><?php _e('Yes', 'wp-review-slider-pro'); ?></option>
							</select>
					</div>
					<p class="description">
					<?php _e('The individual review boxes will all be equal to the biggest one in all slides.', 'wp-google-reviews'); ?></p>
				</td>
			</tr>
			
			<?php
			if(!isset($template_misc_array['showmedia'])){
					$template_misc_array['showmedia']='yes';
			}
			?>
			<tr class="wprevpro_row">
				<th scope="row">
					<?php _e('Review Media', 'wp-google-reviews'); ?><a class="wprevpro_helpicon_p wprevpro_btnicononlyhelp dashicons-before dashicons-editor-help"></a>
				</th>
				<td><div class="">
					<select name="wprevpro_t_showmedia" id="wprevpro_t_showmedia">
						<option value="no" <?php if($template_misc_array['showmedia']=='no'){echo "selected";} ?>>No</option>
						<option value="yes" <?php if($template_misc_array['showmedia']=='yes'){echo "selected";} ?>>Yes</option>
					</select>
					</div>
					<p class="description">
					<?php _e('Show images that users have added with their review.', 'wp-google-reviews'); ?></p>
				</td>
			</tr>
			
			<tr class="wprevpro_row">
				<th scope="row" colspan="2">
				<span class="nextprevbtn w3-green button button-secondary dashicons-before dashicons-arrow-left gotopage0">Previous</span>
				<span class="nextprevbtn w3-green button button-secondary dashicons-before dashicons-arrow-right-after gotopage2">Next</span>
				</th>
			</tr>
</table>

<table id="settingtable2" class="form-table settingstable " style="display:none;">		
			<tr class="wpfbr_row">
				<th scope="row">
					<?php _e('Choose Source', 'wp-google-reviews'); ?><a class="wprevpro_helpicon_p wprevpro_btnicononlyhelp dashicons-before dashicons-editor-help"></a>
				</th>
				<td>
					<select name="wprevpro_t_filtersource" id="wprevpro_t_filtersource">
					<?php
					$reviews_table_name = $wpdb->prefix . 'wpfb_reviews';
					$tempquery = "select * from ".$reviews_table_name." WHERE type = 'Google' group by pageid Order By id Desc";
					$currentlocations = $wpdb->get_results($tempquery);
					if(!isset($template_misc_array['filtersource'])){
						$template_misc_array['filtersource']="";
					}
			
					//$currentform->id
					foreach ( $currentlocations as $currentlocation ) 
					{
						//grab average and total for each from avg total
						//$table_name = $wpdb->prefix . 'wpfb_total_averages';
						//$currentlocation = $wpdb->get_results("SELECT * FROM $table_name WHERE `pagetype` LIKE '%Google%' AND `btp_id` = '".$currentlocation->pageid."' ");
						
						$selected = "";
						if($template_misc_array['filtersource']==$currentlocation->pageid){
							$selected = "selected";
						}
						echo '<option value="'.$currentlocation->pageid.'" '.$selected.'>'.$currentlocation->pagename.'</option>';
					
					}
					?>
					</select>
					<p class="description">
					<?php _e('Filter by source.', 'wp-google-reviews'); ?></p>
				</td>
			</tr>
			<tr class="wpfbr_row">
				<th scope="row">
					<?php _e('Display Order', 'wp-google-reviews'); ?><a class="wprevpro_helpicon_p wprevpro_btnicononlyhelp dashicons-before dashicons-editor-help"></a>
				</th>
				<td>
					<select name="wpfbr_t_display_order" id="wpfbr_t_display_order">
						<option value="random" <?php if($currenttemplate->display_order=="random"){echo "selected";} ?>><?php _e('Random', 'wp-google-reviews'); ?></option>
						<option value="newest" <?php if($currenttemplate->display_order=="newest"){echo "selected";} ?>><?php _e('Newest', 'wp-google-reviews'); ?></option>
					</select>
					<p class="description">
					<?php _e('The order in which the reviews are displayed.', 'wp-google-reviews'); ?></p>
				</td>
			</tr>
			<tr class="wpfbr_row">
				<th scope="row" style="min-width:220px">
					<?php _e('Hide Reviews Without Text', 'wp-google-reviews'); ?><a class="wprevpro_helpicon_p wprevpro_btnicononlyhelp dashicons-before dashicons-editor-help"></a>
				</th>
				<td>
					<select name="wpfbr_t_hidenotext" id="wpfbr_t_hidenotext">
						<option value="yes" <?php if($currenttemplate->hide_no_text=="yes"){echo "selected";} ?>><?php _e('Yes', 'wp-google-reviews'); ?></option>
						<option value="no" <?php if($currenttemplate->hide_no_text=="no"){echo "selected";} ?>><?php _e('No', 'wp-google-reviews'); ?></option>
					</select>
					<p class="description">
					<?php _e('Set to Yes and only display reviews that have text included.', 'wp-google-reviews'); ?></p>
				</td>
			</tr>
			<?php
			if(!isset($currenttemplate->min_rating)){
				$currenttemplate->min_rating==1;
			}
			
			?>
			<tr class="wpfbr_row">
				<th scope="row">
					<?php _e('Filter By Rating', 'wp-google-reviews'); ?><a class="wprevpro_helpicon_p wprevpro_btnicononlyhelp dashicons-before dashicons-editor-help"></a>
				</th>
				<td>
					<select name="wpfbr_t_min_rating" id="wpfbr_t_min_rating">
					  <option value="1" <?php if($currenttemplate->min_rating==1){echo "selected";} ?>><?php _e('Show All', 'wp-google-reviews'); ?></option>
					  <option value="2" <?php if($currenttemplate->min_rating==2){echo "selected";} ?>><?php _e('2 & Higher', 'wp-google-reviews'); ?></option>
					  <option value="3" <?php if($currenttemplate->min_rating==3){echo "selected";} ?>><?php _e('3 & Higher', 'wp-google-reviews'); ?></option>
					  <option value="4" <?php if($currenttemplate->min_rating==4){echo "selected";} ?>><?php _e('4 & Higher', 'wp-google-reviews'); ?></option>
					  <option value="5" <?php if($currenttemplate->min_rating==5){echo "selected";} ?>><?php _e('Only 5 Star', 'wp-google-reviews'); ?></option>
					</select>
					<p class="description">
					<?php _e('Show only reviews with at least this value rating. Allows you to hide low reviews.', 'wp-google-reviews'); ?></p>
				</td>
			</tr>
<tr class="wprevpro_row">
				<th scope="row" colspan="2">
				<span class="nextprevbtn w3-green button button-secondary dashicons-before dashicons-arrow-left gotopage1">Previous</span>
				<span class="nextprevbtn w3-green button button-secondary dashicons-before dashicons-arrow-right-after gotopage3">Next</span>
				</th>
			</tr>
			
		</table>
		
	<table id="settingtable3" class="form-table settingstable " style="display:none;">	
<?php
//badge settings.
if(!isset($template_misc_array['blocation'])){
	$template_misc_array['blocation']="";
}
  //set default values from filtered location above. grab the values from attributes
if(!isset($template_misc_array['bname'])){
	$template_misc_array['bname']="";
}
if(!isset($template_misc_array['bimgurl'])){
	$template_misc_array['bimgurl']=WPREV_GOOGLE_PLUGIN_URL."/public/partials/imgs/google-my-business-icon-300x300.png";
}
if(!isset($template_misc_array['bbtncolor'])){
	$template_misc_array['bbtncolor']="#0a6cff";
}
if(!isset($template_misc_array['bbtnurl'])){
	$template_misc_array['bbtnurl']="https://search.google.com/local/writereview?placeid=".$currentlocations[0]->pageid;
}
if(!isset($template_misc_array['bnameurl'])){
	$template_misc_array['bnameurl']="https://search.google.com/local/reviews?placeid=".$currentlocations[0]->pageid;
}
if(!isset($template_misc_array['bbkcolor'])){
	$template_misc_array['bbkcolor']="#ffffff";
}
if(!isset($template_misc_array['bbradius'])){
	$template_misc_array['bbradius']="0";
}
if(!isset($template_misc_array['bshape'])){
	$template_misc_array['bshape']="";
}
if(!isset($template_misc_array['bimgsize'])){
	$template_misc_array['bimgsize']="50";
}
?>
			<tr class="wpfbr_row tabnoterow">
				<td colspan="2">
				<div class="tabnote">
				Use this page to place a badge next to your reviews. This is a brand new feature so let me know if you see any formatting issues.
				</div>
				</td>
			</tr>	
			<tr class="wpfbr_row">
				<td colspan="2">
				<div class="badgeinfo">

					<div class="badgeinfosetting">
						<div class="bsetlabel"><?php _e('Location:', 'wp-google-reviews'); ?></div>
						<select name="wprevpro_t_blocation" id="wprevpro_t_blocation">
						<option value="" <?php if($template_misc_array['blocation']==""){echo "selected";} ?>><?php _e('Select One', 'wp-google-reviews'); ?></option>
						<option value="left" <?php if($template_misc_array['blocation']=="left"){echo "selected";} ?>><?php _e('Left', 'wp-google-reviews'); ?></option>
						<option value="leftmid" <?php if($template_misc_array['blocation']=="leftmid"){echo "selected";} ?>><?php _e('Left Middle', 'wp-google-reviews'); ?></option>
						<option value="above" <?php if($template_misc_array['blocation']=="above"){echo "selected";} ?>><?php _e('Above', 'wp-google-reviews'); ?></option>
						<option value="abovewide" <?php if($template_misc_array['blocation']=="abovewide"){echo "selected";} ?>><?php _e('Above Wide', 'wp-google-reviews'); ?></option>
						<option value="right" <?php if($template_misc_array['blocation']=="right"){echo "selected";} ?>><?php _e('Right', 'wp-google-reviews'); ?></option>
						<option value="rightmid" <?php if($template_misc_array['blocation']=="rightmid"){echo "selected";} ?>><?php _e('Right Middle', 'wp-google-reviews'); ?></option>
					</select>
					</div>
					<div class="badgeinfosetting badgehide">
						<div class="bsetlabel"><?php _e('Name:', 'wp-google-reviews'); ?></div>
						<input id="wprevpro_t_bname" type="text" name="wprevpro_t_bname" value="<?php if($template_misc_array['bname']!=""){echo $template_misc_array['bname'];} ?>" style="width: 15em">
					</div>	
					<div class="badgeinfosetting badgehide">
						<div class="bsetlabel"><?php _e('Name Link URL:', 'wp-google-reviews'); ?></div>
						<input id="wprevpro_t_bnameurl" type="text" name="wprevpro_t_bnameurl" value="<?php if($template_misc_array['bnameurl']!=""){echo $template_misc_array['bnameurl'];} ?>" style="width: 15em">
					</div>						
				</div>
				</td>
			</tr>
			<tr class="wpfbr_row badgehide">
				<td colspan="2">
				<div class="badgeinfo">
					<div class="badgeinfosetting">
						<div class="bsetlabel"><?php _e('Business Image URL:', 'wp-google-reviews'); ?></div>
						<input id="wprevpro_t_bimgurl" type="text" name="wprevpro_t_bimgurl" value="<?php if($template_misc_array['bimgurl']!=""){echo $template_misc_array['bimgurl'];} ?>" style="width: 15em"><a id="upload_licon_button" class="button">Upload</a>
					</div>
					<div class="badgeinfosetting">
						<div class="bsetlabel"><?php _e('Image Shape:', 'wp-google-reviews'); ?></div>
						<select name="wprevpro_t_bshape" id="wprevpro_t_bshape">
						<option value="" <?php if($template_misc_array['bshape']==""){echo "selected";} ?>>&nbsp;<?php _e('Square', 'wp-google-reviews'); ?>&nbsp;&nbsp;&nbsp;</option>
						<option value="round" <?php if($template_misc_array['bshape']=="round"){echo "selected";} ?>>&nbsp;<?php _e('Round', 'wp-google-reviews'); ?>&nbsp;&nbsp;&nbsp;</option>
						</select>
					</div>
					<div class="badgeinfosetting">
						<div class="bsetlabel"><?php _e('Image Size:', 'wp-google-reviews'); ?></div>
						<input id="wprevpro_t_bimgsize" type="number" name="wprevpro_t_bimgsize" value="<?php if($template_misc_array['bimgsize']!=""){echo $template_misc_array['bimgsize'];} ?>" style="width: 6em">
						
					</div>
					<div class="badgeinfosetting">
						<div class="bsetlabel"><?php _e('Button Color:', 'wp-google-reviews'); ?></div>
						<input type="text" data-alpha="true" value="<?php if($template_misc_array['bbtncolor']!=""){echo $template_misc_array['bbtncolor'];} ?>" name="wprevpro_t_bbtncolor" id="wprevpro_t_bbtncolor" class="my-color-field" />
					</div>
					<div class="badgeinfosetting">
						<div class="bsetlabel"><?php _e('Button Link URL:', 'wp-google-reviews'); ?></div>
						<input id="wprevpro_t_bbtnurl" type="text" name="wprevpro_t_bbtnurl" value="<?php if($template_misc_array['bbtnurl']!=""){echo $template_misc_array['bbtnurl'];} ?>" style="width: 15em">
					</div>
					<div class="badgeinfosetting">
						<div class="bsetlabel"><?php _e('Background:', 'wp-google-reviews'); ?></div>
						<input type="text" data-alpha="true" value="<?php if($template_misc_array['bbkcolor']!=""){echo $template_misc_array['bbkcolor'];} ?>" name="wprevpro_t_bbkcolor" id="wprevpro_t_bbkcolor" class="my-color-field" />
					</div>
					<div class="badgeinfosetting">
						<div class="bsetlabel"><?php _e('Border Radius:', 'wp-google-reviews'); ?></div>
						<input id="wprevpro_t_bbradius" type="number" name="wprevpro_t_bbradius" value="<?php if($template_misc_array['bbradius']!=""){echo $template_misc_array['bbradius'];} ?>" style="width: 7em">
					</div>
				</div>
				</td>
			</tr>
			<?php
			if(!isset($template_misc_array['bdropsh'])){
				$template_misc_array['bdropsh']="yes";
			}
			if(!isset($template_misc_array['bcenter'])){
				$template_misc_array['bcenter']="";
			}
			if(!isset($template_misc_array['bhname'])){
				$template_misc_array['bhname']="";
			}
			if(!isset($template_misc_array['bhphoto'])){
				$template_misc_array['bhphoto']="";
			}
			if(!isset($template_misc_array['bhbased'])){
				$template_misc_array['bhbased']="";
			}
			if(!isset($template_misc_array['bhbtn'])){
				$template_misc_array['bhbtn']="";
			}
			if(!isset($template_misc_array['bhpow'])){
				$template_misc_array['bhpow']="";
			}
			if(!isset($template_misc_array['bhreviews'])){
				$template_misc_array['bhreviews']="";
			}
			?>
			<tr class="wpfbr_row badgehide">
				<td colspan="2">
				<div class="badgeinfo">
					<div class="badgeinfosetting checkboxes">
					<input type="checkbox" id="wprevpro_t_bdropsh" name="wprevpro_t_bdropsh" value="yes" <?php if($template_misc_array['bdropsh']== "yes"){echo 'checked="checked"';}?>>
					<label for="wprevpro_t_bdropsh"><?php _e('Drop Shadow', 'wp-google-reviews'); ?></label>
					</div>
					<div class="badgeinfosetting checkboxes">
					<input type="checkbox" id="wprevpro_t_bcenter" name="wprevpro_t_bcenter" value="yes" <?php if($template_misc_array['bcenter']== "yes"){echo 'checked="checked"';}?>>
					<label for="wprevpro_t_bcenter"><?php _e('Center Text', 'wp-google-reviews'); ?></label>
					</div>
					<div class="badgeinfosetting checkboxes">
					<input type="checkbox" id="wprevpro_t_bhphoto" name="wprevpro_t_bhphoto" value="yes" <?php if($template_misc_array['bhphoto']== "yes"){echo 'checked="checked"';}?>>
					<label for="wprevpro_t_bhphoto"><?php _e('Hide Photo', 'wp-google-reviews'); ?></label>
					</div>
					<div class="badgeinfosetting checkboxes">
					<input type="checkbox" id="wprevpro_t_bhname" name="wprevpro_t_bhname" value="yes" <?php if($template_misc_array['bhname']== "yes"){echo 'checked="checked"';}?>>
					<label for="wprevpro_t_bhname"><?php _e('Hide Name', 'wp-google-reviews'); ?></label>
					</div>	
					
					<div class="badgeinfosetting checkboxes">
					<input type="checkbox" id="wprevpro_t_bhbased" name="wprevpro_t_bhbased" value="yes" <?php if($template_misc_array['bhbased']== "yes"){echo 'checked="checked"';}?>>
					<label for="wprevpro_t_bhbased"><?php _e('Hide "Based On..."', 'wp-google-reviews'); ?></label>
					</div>
					<div class="badgeinfosetting checkboxes">
					<input type="checkbox" id="wprevpro_t_bhpow" name="wprevpro_t_bhpow" value="yes" <?php if($template_misc_array['bhpow']== "yes"){echo 'checked="checked"';}?>>
					<label for="wprevpro_t_bhpow"><?php _e('Hide "powered By..."', 'wp-google-reviews'); ?></label>
					</div>
					<div class="badgeinfosetting checkboxes">
					<input type="checkbox" id="wprevpro_t_bhbtn" name="wprevpro_t_bhbtn" value="yes" <?php if($template_misc_array['bhbtn']== "yes"){echo 'checked="checked"';}?>>
					<label for="wprevpro_t_bhbtn"><?php _e('Hide "Review Us..."', 'wp-google-reviews'); ?></label>
					</div>
					
					<div class="badgeinfosetting checkboxes">
					<input type="checkbox" id="wprevpro_t_bhreviews" name="wprevpro_t_bhreviews" value="yes" <?php if($template_misc_array['bhreviews']== "yes"){echo 'checked="checked"';}?>>
					<label for="wprevpro_t_bhreviews"><?php _e('Hide Reviews', 'wp-google-reviews'); ?></label>
					</div>
				</div>
				</td>
			</tr>
			
			<tr class="wprevpro_row">
				<th scope="row" colspan="2">
				<span class="nextprevbtn w3-green button button-secondary dashicons-before dashicons-arrow-left gotopage2">Previous</span>
				</th>
			</tr>
			
		</tbody>
	</table>
	<?php 
	//security nonce
	wp_nonce_field( 'wpfbr_save_template');
	?>
	<input type="hidden" name="edittid" id="edittid"  value="<?php echo $currenttemplate->id; ?>">
	<a id="wpfbr_addnewtemplate_cancel" class="button button-secondary"><?php _e('Cancel', 'wp-google-reviews'); ?></a>
	<input type="submit" name="wpfbr_submittemplatebtn" id="wpfbr_submittemplatebtn" class="button button-primary" value="<?php _e('Save & Close', 'wp-google-reviews'); ?>">
	<a id="wprevpro_addnewtemplate_update" class="button button-primary"><?php if($currenttemplate->id>0){_e('Update', 'wp-google-reviews');} else {_e('Update', 'wp-google-reviews');} ?></a>
	<div id="update_form_msg_div"><img src="<?php echo WPREV_GOOGLE_PLUGIN_URL; ?>/public/partials/imgs/loading_ripple.gif" id="savingformimg" class="wprptemplate_update_loading_image" style="display:none;"><span id="update_form_msg" style="display:none;"><span class="dashicons dashicons-saved"></span></span></div>
	</form>
</div>

<div class="wpfbr_margin10 w3-white" id="wpfbr_preview_outermost" style="display:none;">
	<div class="" id="wpfbr_loading_prev_div">
		<img src="<?php echo WPREV_GOOGLE_PLUGIN_URL; ?>/public/partials/imgs/loading_ripple.gif" id="loadingpreview" class="wprptemplate_loadingpreview_image" style="display:none;">
	</div>
	<div class="wpfbr_margin10 w3-white" id="wpfbr_preview_outer">
	</div>
</div>

<div class="small_message"><p>Do you like this plugin? If so please take a moment to leave a review <a href="https://wordpress.org/plugins/wp-google-reviews/" target="blank">here!</a> If it's missing something then please contact me <a href="http://ljapps.com/contact/" target="blank">here</a>. Thanks!</p><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br></div>

	<div id="popup_review_list" class="popup-wrapper wpfbr_hide">
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