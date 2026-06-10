<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    WP_Google_Reviews
 * @subpackage WP_Google_Reviews/public/partials
 */
 	//db function variables
	global $wpdb;
	$table_name = $wpdb->prefix . 'wpfb_post_templates';
	
 //use the template id to find template in db, echo error if we can't find it or just don't display anything
 	//Get the form--------------------------
	$tid = htmlentities($a['tid']);
	$tid = intval($tid);
	$currentform = $wpdb->get_results("SELECT * FROM $table_name WHERE id = ".$tid);

	//print_r($currentform);
	//echo count($currentform);
	//check to make sure template found
	if(count($currentform)>0){
	
	
		//use values from currentform to get reviews from db
		$table_name = $wpdb->prefix . 'wpfb_reviews';
		
		if($currentform[0]->hide_no_text=="yes"){
			$rlength = 1;
		} else {
			$rlength = 0;
		}
		
		if($currentform[0]->display_order=="random"){
			$sorttable = "RAND() ";
			$sortdir = "";
		} else {
			$sorttable = "created_time_stamp ";
			$sortdir = "DESC";
		}
		$rtype = "Google";
		if($currentform[0]->rtype=='["fb"]'){
			$rtype = "Facebook";
		}
		if($currentform[0]->rtype=='["google"]'){
			$rtype = "Google";
		}
		$reviewsperpage= $currentform[0]->display_num*$currentform[0]->display_num_rows;
		$tablelimit = $reviewsperpage;
		//change limit for slider
		if($currentform[0]->createslider == "yes"){
			$tablelimit = $tablelimit*$currentform[0]->numslides;
		}
		
				//min_rating filter----
		if($currentform[0]->min_rating>0){
			$min_rating = intval($currentform[0]->min_rating);
			//grab positive recommendations as well
			if($min_rating==1){
				$min_rating=0;
			}
			if($min_rating<3){
				//show positive and negative
				$ratingquery = " AND rating >= '".$min_rating."' ";
			} else {
				//only show positive
				$ratingquery = " AND (rating >= '".$min_rating."' OR recommendation_type = 'positive' ) ";
			}
			
		} else {
			$min_rating ="";
			$ratingquery ="";
		}
		
			$totalreviews = $wpdb->get_results(
				$wpdb->prepare("SELECT * FROM ".$table_name."
				WHERE id>%d AND review_length >= %d AND type = %s".$ratingquery."
				ORDER BY ".$sorttable." ".$sortdir." 
				LIMIT ".$tablelimit." ", "0","$rlength","$rtype")
			);
			
			//print_r($totalreviews);
			//echo "<br><br>";
			
	//only continue if some reviews found
	$makingslideshow=false;
	if(count($totalreviews)>0){

		//if creating a slider than we need to split into chunks for each slider
		//if($currentform[0]->createslider == "yes"){
			//print_r(array_chunk($totalreviews, $reviewsperpage));
			$totalreviewschunked = array_chunk($totalreviews, $reviewsperpage);
		//}
		//loop through each chunk
		//print_r($totalreviewschunked);
		
		//if making slide show then add it here
		if($currentform[0]->createslider == "yes"){
			//make sure we have enough to create a show here
			if($totalreviews>$reviewsperpage){
				$makingslideshow = true;
				echo '<div class="wprev-slider-widget"><ul>';
			}
		}
		
		foreach ( $totalreviewschunked as $reviewschunked ){
			//echo "loop1";
			$totalreviewstemp = $reviewschunked;
			
			//need to break $totalreviewstemp up based on how many rows, create an multi array containing them
			if($currentform[0]->display_num_rows>1 && count($totalreviewstemp)>$currentform[0]->display_num){
				//count of reviews total is greater than display per row then we need to break in to multiple rows
				for ($row = 0; $row < $currentform[0]->display_num_rows; $row++) {
					$n=1;
					foreach ( $totalreviewstemp as $tempreview ){
						//echo "<br>".$tempreview->reviewer_name;
						//echo $n."-".$row."-".$currentform[0]->display_num;
						if($n>($row*$currentform[0]->display_num) && $n<=(($row+1)*$currentform[0]->display_num)){
							$rowarray[$row][$n]=$tempreview;
						}
						$n++;
					}
				}
			} else {
				//everything on one row so just put in multi array
				$rowarray[0]=$totalreviewstemp;
			}
			
			//add styles from template misc here (all color/number values sanitized for CSS context)
			$template_misc_array = json_decode($currentform[0]->template_misc, true);
			if(is_array($template_misc_array)){
				$misc_style = WP_Google_Reviews_Sanitize::build_template_misc_style( $currentform[0]->id, $currentform[0]->style, $template_misc_array, '_widget', false );

				echo "<style>".$misc_style."</style>";
			}

			//print out user style added
			echo "<style>".esc_html($currentform[0]->template_css)."</style>";
			 
			//if making slide show
			if($makingslideshow){
					echo '<li>';
			}
		 
				//include the correct tid here
				if($currentform[0]->style=="1"){
				$iswidget=true;
					include(plugin_dir_path( __FILE__ ) . '/template_style_1.php');
				} else if($currentform[0]->style=="6"){
				$iswidget=true;
					include(plugin_dir_path( __FILE__ ) . '/template_style_6.php');
				}
			
			//if making slide show then end loop here
			if($makingslideshow){
					echo '</li>';
			}
		
		}	//end loop chunks
		//if making slide show then end it
				if($makingslideshow){
				echo '</ul></div>';
				/*echo "<script type='text/javascript'>
						function wprs_defer(method) {
							if (window.jQuery) {
								if(jQuery.fn.wprs_unslider){
									method();
								} else {
									setTimeout(function() { wprs_defer(method) }, 500);
									console.log('waiting for wppro_rev_slider js...');									
								}
							} else {
								setTimeout(function() { wprs_defer(method) }, 100);
							}
						}
						wprs_defer(function () {
							jQuery(document).ready(function($) {
								var slider_widget = $('.wprev-slider-widget').wprs_unslider(
									{
									autoplay:false,
									speed: '750',
									animation: 'horizontal',
									arrows: false,
									animateHeight: true,
									activeClass: 'wprs_unslider-active',
									}
								);
								
								setTimeout(function(){ 
								//height of active slide
								var firstheight = $('.wprs_unslider-active').height();
								$('.wprev-slider-widget').css( 'height', firstheight );
								}, 500);

							});
						});
						</script>";
						*/
		}
	 
	}
}
?>

