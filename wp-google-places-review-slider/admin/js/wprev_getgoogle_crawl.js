(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 * $( document ).ready(function() same as
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */
	 
	 //document ready
	 $(function(){
		 var placeid = '';
		//function wpfbr_testapikey(pagename) {
		$("#savetest").click(function(event){
			
			//hide button
			$( this ).hide();
			var editplace = $( this ).attr("data-editplace");
			//add class wprevloader to parent div
			$( '#buttonloader' ).show();
			$( '#googletestresults' ).hide();
			$( '#googletestresultstext2' ).html('');
			$( '#googletestresultserrortext2' ).html('');
			$( '#googletestresults2' ).hide();

			
			placeid = $("#gplaceid").val();
			if(placeid==''){
				//alert("Please enter your Place ID.");
				//return false;
			}
			var data = {
			action	:	'wpfbr_crawl_placeid',
			gplaceid	:	placeid,
			geditplace :	editplace,
			_ajax_nonce		: adminjs_script_vars.wpfb_nonce,
				};
			var myajax = jQuery.post(ajaxurl, data, function(response) {
					console.log(response);
					    try {
							const objresponse = JSON.parse(response);
							$( '#divgoogletestresults' ).show();
							console.log(objresponse);
							if(objresponse.ack!='success'){
								//show error
								$( '#divdownloadreviews' ).hide();
								$( '#googletestresults' ).hide();
								$( '#googletestresultserror' ).show();
								$( '#buttonloader' ).hide();
								$( '#savetest' ).show();
								$( '#googletestresultserrortext' ).html('<p>'+objresponse.ackmsg+'</p>');
							} else {
								$( '#divdownloadreviews' ).show();
								$( '#googletestresults' ).show();
								$( '#googletestresultserror' ).hide();
								if(objresponse.img && objresponse.img!=''){
								$( '#businessimg' ).attr("src", objresponse.img);
								}
								$( '#businessname' ).html(objresponse.businessname);
								$( '#website' ).html(objresponse.website);
								$( '#googleurl' ).html(objresponse.googleurl);
								$( '#googleurl' ).attr("href", objresponse.googleurl)
								$( '#reviewtext' ).html('Rated <b>'+objresponse.rating+'</b> out of <b>'+objresponse.totalreviews+'</b>');
								$( '#downloadreviews' ).show();
							}
						} catch (e) {
							$( '#googletestresults' ).hide();
							$( '#googletestresultserror' ).show();
							$( '#googletestresultserror' ).html('<p>Error crawling Google. Please contact support.</p>');
							return false;
						}
					//show button
					$("#savetest").show();
					//add class wprevloader to parent div
					$( '#buttonloader' ).hide();
					
			});
			jQuery(window).unload( function() { myajax.abort(); } );

		});
		
		//for downloading on crawl page.
		$("#downloadreviews").click(function(event){
			
			//hide button
			$( this ).hide();
			//add class wprevloader to parent div
			$( '#buttonloader2' ).show();
			$( '#googletestresultstext2' ).html('');
			
			var placeid = $("#gplaceid").val();
			var newestorhelpful = $('input[name="sortoption"]:checked').val();
			if(placeid==''){
				alert("Please enter your Place ID or Search Terms.");
				return false;
			}
			var data = {
			action	:	'wpfbr_crawl_placeid_go',
			gplaceid	:	placeid,
			nhful	: newestorhelpful,
			_ajax_nonce		: adminjs_script_vars.wpfb_nonce,
				};
			var myajax = jQuery.post(ajaxurl, data, function(response) {
				console.log(response);
					    try {
							const objresponse = JSON.parse(response);
							$( '#googletestresults2' ).show();
							//console.log(objresponse);
							if(objresponse.ack!='success'){
								//show error
								$( '#googletestresults2' ).hide();
								$( '#googletestresultserror2' ).show();
								$( '#googletestresultserrortext2' ).html('<p>'+objresponse.ackmsg+'</p>');
							} else {
								$( '#divdownloadreviews' ).show();
								$( '#googletestresults2' ).show();
								$( '#googletestresultserror2' ).hide();
								$( '#googletestresultstext2' ).html(objresponse.ackmsg);
							}
						} catch (e) {
							//console.log(response);
							$( '#googletestresults2' ).hide();
							$( '#buttonloader2' ).hide();
							$( '#googletestresultserror2' ).show();
							$( '#googletestresultserrortext2' ).html('<p>Error crawling Google. Please contact support.</p>');
							return false;
						}

					//add class wprevloader to parent div
					$( '#buttonloader2' ).hide();
					
			});
			jQuery(window).unload( function() { myajax.abort(); } );

		});
		
				
		$('input[type=radio][name=sortoption]').change(function() {
			//hide messages and show button.
			$( '#googletestresultserror2' ).hide();
			$( '#googletestresults2' ).hide();
			$( '#divdownloadreviews' ).show();
			$("#downloadreviews").show();
		});
		

		//for downloading on get reviews page
		$("#currentsources").on("click",".downloadrevs", function(){
			//alert("here");
			//hide button
			$( this ).hide();
			//add class wprevloader to parent div
			$( this ).next( '.buttonloader2' ).show();
			
			//var placeid = $("#gplaceid").val();
			//var newestorhelpful = $('input[name="sortoption"]:checked').val();

			var placeid = $( this ).attr("data-place");
			var newestorhelpful = $( this ).attr("data-nhful");
			
			if(newestorhelpful!="newest" && newestorhelpful!="relevant"){
				newestorhelpful="newest";
			}
			
			if(placeid==''){
				alert("Please enter your Place ID or Search Terms.");
				return false;
			}
			
			console.log('here');
			console.log(placeid);
			console.log(newestorhelpful);
			
			var data = {
			action	:	'wpfbr_crawl_placeid_go',
			getrevsplaceid	:	placeid,
			nhful	: newestorhelpful,
			_ajax_nonce		: adminjs_script_vars.wpfb_nonce,
				};
			var elem = $(this);
			var myajax = jQuery.post(ajaxurl, data, function(response) {
				console.log(response);
					    try {
							const objresponse = JSON.parse(response);
							//console.log(objresponse);
							if(objresponse.ack!='success'){
								//show error
								elem.nextAll( '.googletestresults2' ).html('<p>'+objresponse.ackmsg+'</p>');
							} else {
								elem.nextAll( '.googletestresults2' ).html(objresponse.ackmsg);
							}
						} catch (e) {
							//console.log(response);
							elem.next( '.buttonloader2' ).hide();
							elem.nextAll( '.googletestresults2' ).html('<p>Error crawling Google. Please contact support.</p>');
							return false;
						}

					//add class wprevloader to parent div
					elem.next( '.buttonloader2' ).hide();
					setTimeout(function(){
						elem.show();
						elem.nextAll( '.googletestresults2' ).html('');
					}, 2000);
					
			});
			jQuery(window).unload( function() { myajax.abort(); } );

		});
		
		
		$("#shownewsourceoption").click(function(event){
			//alert("here");
			//hide show the options.
			$("#chooseoption").toggle('slow');
			
		});
	



		
	 });

})( jQuery );
