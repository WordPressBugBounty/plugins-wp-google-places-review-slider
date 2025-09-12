(function( $ ) {
	'use strict';

	/**
	 * DataForSEO Google Reviews JavaScript
	 * 
	 * Handles frontend interactions for DataForSEO Google Reviews download method
	 * Includes daily limit tracking, task monitoring, and user feedback
	 */

	// Global variables
	 var businessname;
	 var foundplaceid;
	 var fromurl;
	var dailyRemaining = 100;
	var taskInProgress = false;
	var dataforseoPollingInterval = null;
	var dataforseoTaskId = null;
	var dataforseoPollingAttempts = 0;
	var dataforseoMaxPollingAttempts = 60; // 10 minutes max (10 second intervals)

	 $(function(){
		 var placeid = '';


		// Initialize DataForSEO interface
		initDataForSEOInterface();
		
		// Hide download section by default
		$("#divdownloadreviews").hide();
		
		// Save & Test button click handler
		$("#savetest").click(function(event){
			event.preventDefault();
			
			if (taskInProgress) {
				return false;
			}
			
			var editplace = $(this).attr("data-editplace");
			var searchTerms = $("#gplaceid").val().trim();
			var sortOption = $("input[name='sortoption']:checked").val();
			
			if (!searchTerms) {
				showError('Please enter search terms or place ID.');
				return false;
			}
			
			// Check daily limit
			if (dailyRemaining <= 0) {
				showError('Daily limit of 100 reviews exceeded. Please try again tomorrow.');
				return false;
			}
			
			// Save place ID first
			savePlaceId(searchTerms, function() {
				// Start task after saving
				startDataForSEOTask(searchTerms, editplace, sortOption);
			});
		});
		
		// Download Reviews button click handler
		$("#downloadreviews").click(function(event){
			event.preventDefault();
			
			if (taskInProgress) {
				return false;
			}
			
			var searchTerms = $("#gplaceid").val().trim();
			var sortOption = $("#dfs_sort_option").val();
			var requestedCount = parseInt($("#dfs_review_count").val());
			var reviewCount = Math.min(requestedCount, dailyRemaining);
			
			if (!searchTerms) {
				showError('Please enter search terms or place ID.');
				return false;
			}
			
			if (dailyRemaining <= 0) {
				showError('Daily limit of 100 reviews exceeded. Please try again tomorrow.');
				return false;
			}
			
			if (reviewCount <= 0) {
				showError('No reviews remaining for today. Please try again tomorrow.');
				return false;
			}
			
			// Start download
			downloadDataForSEOReviews(searchTerms, sortOption, reviewCount);
		});
		
		// Download Completed Task button click handler
		$("#downloadcompletedtask").click(function(event){
			event.preventDefault();
			
			if (taskInProgress) {
				return false;
			}
			
			var taskId = $(this).data('task-id');
			var placeId = $(this).data('place-id');
			
			console.log('Download completed task clicked. Task ID:', taskId, 'Place ID:', placeId);
			
			if (!taskId) {
				showError('No task ID found for completed task.');
				return false;
			}
			
			// Start download from completed task
			downloadReviewsFromCompletedTask(taskId, placeId);
		});
		
		// Location and language are fixed to US/English
		
		// Handle DataForSEO action buttons on googlechoose page
		$(document).on('click', '.dfs-action-button', function(event) {
			event.preventDefault();
			
			var action = $(this).data('action');
			var placeId = $(this).data('place-id');
			var taskId = $(this).data('task-id');
			var place = $(this).data('place');
			var nhful = $(this).data('nhful');
			
			if (action === 'check-task-status') {
				checkTaskStatus(taskId, placeId, place, nhful);
			} else if (action === 'download-reviews') {
				showError('Download functionality will be implemented next');
			} else if (action === 'start-task') {
				showError('Start new task functionality will be implemented next');
			}
		});
	});

	/**
	 * Initialize DataForSEO interface
	 */
	function initDataForSEOInterface() {
		// Load daily remaining count
		loadDailyRemaining();
		
		// Update UI elements
		updateDailyLimitDisplay();
	}

	/**
	 * Load daily remaining count from server
	 */
	function loadDailyRemaining() {
		$.ajax({
			url: adminjs_script_vars.ajax_url,
			type: 'POST',
			data: {
				action: 'wpfbr_dfs_daily_remaining',
				nonce: adminjs_script_vars.wpfb_nonce
			},
			success: function(response) {
				if (response.success) {
					dailyRemaining = response.data.remaining;
					updateDailyLimitDisplay();
				}
			},
			error: function() {
				console.log('Failed to load daily remaining count');
			}
		});
	}

	/**
	 * Update daily limit display
	 */
	function updateDailyLimitDisplay() {
		$("#dfs_daily_remaining").text(dailyRemaining);
		$("#dfs_daily_limit").text(100);
		
		if (dailyRemaining <= 0) {
			$("#dfs_daily_status").removeClass('w3-green').addClass('w3-red').text('Limit Exceeded');
			$("#savetest, #downloadreviews").prop('disabled', true);
		} else if (dailyRemaining <= 20) {
			$("#dfs_daily_status").removeClass('w3-green').addClass('w3-orange').text('Low');
		} else {
			$("#dfs_daily_status").removeClass('w3-red w3-orange').addClass('w3-green').text('Available');
		}
	}

	// Location and language are fixed to US/English - no setup needed

	/**
	 * Start DataForSEO task (test/search)
	 */
	function startDataForSEOTask(searchTerms, editplace, sortOption) {
		taskInProgress = true;
		
		// Hide button and show loader
		$("#savetest").hide();
		$("#buttonloader").show();
		$("#googletestresults").hide();
		$("#googletestresultserror").hide();
		
		// Fixed options: US location, English language
		var location = "US";
		var language = "en";
		
		console.log('Making AJAX request to:', adminjs_script_vars.ajax_url);
		console.log('AJAX data:', {
			action: 'wpfbr_dfs_test_search',
			search_terms: searchTerms,
			location: location,
			language: language,
			sort_option: sortOption,
			edit_place: editplace,
			nonce: adminjs_script_vars.wpfb_nonce
		});
		
		$.ajax({
			url: adminjs_script_vars.ajax_url,
			type: 'POST',
			data: {
				action: 'wpfbr_dfs_test_search',
				search_terms: searchTerms,
				location: location,
				language: language,
				sort_option: sortOption,
				edit_place: editplace,
				nonce: adminjs_script_vars.wpfb_nonce
			},
			success: function(response) {
				console.log('AJAX success response:', response);
				handleTestSearchResponse(response);
			},
			error: function(xhr, status, error) {
				console.log('AJAX error:', xhr, status, error);
				showError('Network error: ' + error);
			},
			complete: function() {
				console.log('AJAX request completed');
				taskInProgress = false;
				$("#savetest").show();
				$("#buttonloader").hide();
			}
		});
	}

	/**
	 * Handle test search response
	 */
	function handleTestSearchResponse(response) {
		console.log('handleTestSearchResponse called with:', response);
		
		if (response.success) {
			// Check if we got a task ID (async processing)
			if (response.data.task_id && response.data.task_id !== null) {
				console.log('Starting polling with task_id:', response.data.task_id);
				dataforseoTaskId = response.data.task_id;
				dataforseoPollingAttempts = 0;
				
				// Save task ID to database
				var searchTerms = $("#gplaceid").val().trim();
				saveTaskId(searchTerms, response.data.task_id, function() {
					// Show polling status
					$("#googletestresultstext2").html('Task submitted successfully. Polling for results... (Attempt 0/' + dataforseoMaxPollingAttempts + ')');
					$("#googletestresults2").show();
					
					// Show loading gif
					showLoadingGif();
					
					// Start polling
					startPollingForResults();
				});
			} else if (response.data.business_info) {
				// Immediate results (synchronous)
				console.log('Processing immediate results');
				updateBusinessInfo(response.data.business_info);
				
				// Show results
				$("#googletestresults2").show();
				$("#divdownloadreviews").show();
				
				// Update daily remaining
				if (response.data.daily_remaining !== undefined && response.data.daily_remaining !== null) {
					dailyRemaining = response.data.daily_remaining;
					updateDailyLimitDisplay();
				}
				
				// Store business info for download
				if (response.data.business_info) {
					businessname = response.data.business_info.businessname || '';
					foundplaceid = response.data.business_info.foundplaceid || '';
					fromurl = response.data.business_info.googleurl || '';
				}
			} else {
				// No task_id and no business_info - something went wrong
				console.log('No task_id or business_info in response');
				showError('No results received from server');
			}
			
		} else {
			// Enhanced error handling with specific messages
			var errorMessage = response.data.message || 'Unknown error occurred';
			console.log('Processing error response, message:', errorMessage);
			
			// Check for specific error types
			if (errorMessage.includes('daily crawl limit') || errorMessage.includes('exceeded your daily')) {
				// Daily limit exceeded - show more helpful message
				console.log('Daily limit error detected, calling showError');
				showError('Daily Limit Exceeded: ' + errorMessage);
			} else if (errorMessage.includes('Invalid') || errorMessage.includes('invalid')) {
				// Invalid input - show helpful message
				console.log('Invalid input error detected, calling showError');
				showError('Invalid Input: ' + errorMessage);
			} else if (errorMessage.includes('Failed to contact')) {
				// Network/server error
				console.log('Server error detected, calling showError');
				showError('Server Error: ' + errorMessage);
			} else {
				// Generic error - check for unknown error
				console.log('Generic error detected, calling showError');
				if (errorMessage === 'Unknown error occurred') {
					showError('An unexpected error occurred while processing your request. Please contact support for assistance.');
				} else {
					showError(errorMessage);
				}
			}
		}
	}

	/**
	 * Download DataForSEO reviews
	 */
	function downloadDataForSEOReviews(searchTerms, sortOption, reviewCount) {
		taskInProgress = true;
		
		// Hide button and show loader
		$("#downloadreviews").hide();
		$("#buttonloader2").show();
		$("#googletestresults2").hide();
		$("#googletestresultserror2").hide();
		
		// Fixed options: US location, English language
		var location = "US";
		var language = "en";
		
		$.ajax({
			url: adminjs_script_vars.ajax_url,
			type: 'POST',
			data: {
				action: 'wpfbr_dfs_download_reviews',
				search_terms: searchTerms,
				location: location,
				language: language,
				sort_option: sortOption,
				review_count: reviewCount,
				nonce: adminjs_script_vars.wpfb_nonce
			},
			success: function(response) {
				handleDownloadResponse(response);
			},
			error: function(xhr, status, error) {
				showError('Network error: ' + error);
			},
			complete: function() {
				taskInProgress = false;
				$("#downloadreviews").show();
				$("#buttonloader2").hide();
			}
		});
	}

	/**
	 * Handle download response
	 */
	function handleDownloadResponse(response) {
		if (response.success) {
			// Show success message
			$("#googletestresultstext2").html(
				'<strong>Success!</strong> Downloaded ' + response.data.downloaded_count + ' reviews. ' +
				'<br>Daily remaining: ' + response.data.daily_remaining + ' reviews.'
			);
			$("#googletestresults2").show();
			
			// Update daily remaining
			dailyRemaining = response.data.daily_remaining;
			updateDailyLimitDisplay();
			
			// Refresh page after 3 seconds to show new reviews
			setTimeout(function() {
				location.reload();
			}, 3000);
			
		} else {
			showError(response.data.message || 'Unknown error occurred');
		}
	}


	// Location and language are fixed - no update functions needed


	/**
	 * Show error message (generic)
	 */
	function showError(message) {
		// Hide success sections but keep the download div visible for error display
		$("#googletestresults2").hide();
		$("#divdownloadreviews").css('display', 'block').show(); // Force show with explicit CSS
		
		// Show error message in the correct element
		$("#googletestresultserrortext2").html(message);
		$("#googletestresultserror2").show();
		
		// Reset button states
		$("#savetest").show();
		$("#buttonloader").hide();
		taskInProgress = false;
	}

	/**
	 * Start polling for task results
	 */
	function startPollingForResults() {
		if (dataforseoPollingInterval) {
			clearInterval(dataforseoPollingInterval);
		}
		
		dataforseoPollingInterval = setInterval(function() {
			pollForTaskResults();
		}, 10000); // Poll every 10 seconds
	}

	/**
	 * Poll for task results
	 */
	function pollForTaskResults() {
		dataforseoPollingAttempts++;
		
		// Update status display
		$("#googletestresultstext2").html('Polling for results... (Attempt ' + dataforseoPollingAttempts + '/' + dataforseoMaxPollingAttempts + ')');
		$("#googletestresults2").show();
		
		// Update loading gif message
		$("#googletestresults2").html('<div class="w3-panel w3-pale-blue w3-display-container w3-border"><p>Task is processing... (Attempt ' + dataforseoPollingAttempts + '/' + dataforseoMaxPollingAttempts + ') Please wait while we fetch your reviews.</p></div>');
		
		// Check if we've exceeded max attempts
		if (dataforseoPollingAttempts >= dataforseoMaxPollingAttempts) {
			stopPolling();
			showError('Task timeout: Please try again later');
			return;
		}
		
		$.ajax({
			url: adminjs_script_vars.ajax_url,
			type: 'POST',
			data: {
				action: 'wpfbr_dfs_poll_results',
				task_id: dataforseoTaskId,
				nonce: adminjs_script_vars.wpfb_nonce
			},
			success: function(response) {
				console.log('Polling response:', response);
				console.log('Polling response.data:', response.data);
				console.log('Polling response.data.completed:', response.data.completed);
				
				if (response.success) {
					if (response.data.completed) {
						// Task completed successfully
						console.log('Task completed! Stopping polling and handling success');
						stopPolling();
						handlePollingSuccess(response.data);
					} else if (response.data.error) {
						// Check if it's "Task in Queue" - if so, continue polling
						if (response.data.error.includes('Task in Queue') || response.data.error.includes('Task In Queue')) {
							console.log('Task in queue, continuing to poll...');
							// Continue polling - don't stop
						} else {
							// Task failed with other error
							console.log('Task failed! Stopping polling and handling error');
							stopPolling();
							showError(response.data.error);
						}
					} else {
						console.log('Task still processing, continuing to poll...');
					}
					// If not completed and no error, continue polling
				} else {
					// Polling request failed - check if it's "Task in Queue"
					console.log('Polling request failed:', response.data);
					
					// Check for error in both message and error fields
					var errorMessage = response.data.message || response.data.error || 'Unknown error occurred';
					console.log('Error message extracted:', errorMessage);
					
					// Check if it's "Task in Queue" - if so, continue polling
					if (errorMessage.includes('Task in Queue') || errorMessage.includes('Task In Queue')) {
						console.log('Task in queue, continuing to poll...');
						// Continue polling - don't stop
					} else {
						// Stop polling and show error
						stopPolling();
						
						// Check if it's an unknown error and show support message
						if (errorMessage === 'Unknown error occurred') {
							showError('An unexpected error occurred while processing your request. Please contact support for assistance.');
						} else {
							showError(errorMessage);
						}
					}
				}
			},
			error: function(xhr, status, error) {
				console.log('Polling error:', xhr, status, error);
				// Stop polling after too many consecutive network errors
				if (dataforseoPollingAttempts >= 5) {
					stopPolling();
					showError('Network error: Unable to check task status. Please try again later.');
				}
				// Continue polling on network errors for first few attempts
			}
		});
	}

	/**
	 * Stop polling
	 */
	function stopPolling() {
		if (dataforseoPollingInterval) {
			clearInterval(dataforseoPollingInterval);
			dataforseoPollingInterval = null;
		}
		dataforseoTaskId = null;
		dataforseoPollingAttempts = 0;
		
		// Show download button as disabled when polling stops
		// Task stopped - button state handled elsewhere
	}

	/**
	 * Handle successful polling results
	 */
	function handlePollingSuccess(data) {
		// Update business info display
		updateBusinessInfo(data.business_info);
		
		// Show results
		$("#googletestresults").show();
		
		// Show download section with success message
		$("#divdownloadreviews").show();
		showDownloadSuccess(data);
		
		// Update daily remaining
		if (data.daily_remaining !== undefined) {
			dailyRemaining = data.daily_remaining;
			updateDailyLimitDisplay();
		}
		
		// Store business info for download
		businessname = data.business_info.businessname;
		foundplaceid = data.business_info.foundplaceid;
		fromurl = data.business_info.googleurl;
		
		// Update task status in database
		var searchTerms = $("#gplaceid").val().trim();
		updateTaskStatusInDatabase(searchTerms, 'completed');
		
		// Show success message instead of auto-downloading
		showDownloadSuccess(data);
	}

	/**
	 * Save place ID to database
	 */
	function savePlaceId(placeId, callback) {
		console.log('Saving place ID:', placeId);
		
		var sortOption = $("input[name='sortoption']:checked").val();
		
		$.ajax({
			url: adminjs_script_vars.ajax_url,
			type: 'POST',
			data: {
				action: 'wpfbr_dfs_save_place_id',
				place_id: placeId,
				sort_option: sortOption,
				nonce: adminjs_script_vars.wpfb_nonce
			},
			success: function(response) {
				console.log('Place ID saved successfully');
				if (callback) callback();
			},
			error: function(xhr, status, error) {
				console.error('Error saving place ID:', error);
				showError('Failed to save place ID. Please try again.');
			}
		});
	}

	/**
	 * Save task ID to database
	 */
	function saveTaskId(placeId, taskId, callback) {
		console.log('Saving task ID:', taskId, 'for place:', placeId);
		
		$.ajax({
			url: adminjs_script_vars.ajax_url,
			type: 'POST',
			data: {
				action: 'wpfbr_dfs_save_task_id',
				place_id: placeId,
				task_id: taskId,
				nonce: adminjs_script_vars.wpfb_nonce
			},
			success: function(response) {
				console.log('Task ID saved successfully');
				if (callback) callback();
			},
			error: function(xhr, status, error) {
				console.error('Error saving task ID:', error);
				showError('Failed to save task ID. Please try again.');
			}
		});
	}

	/**
	 * Check task status for existing task
	 */
	function checkTaskStatus(taskId, placeId, place, nhful) {
		console.log('Checking task status for task ID:', taskId);
		
		$.ajax({
			url: adminjs_script_vars.ajax_url,
			type: 'POST',
			data: {
				action: 'wpfbr_dfs_poll_results',
				task_id: taskId,
				nonce: adminjs_script_vars.wpfb_nonce
			},
			success: function(response) {
				console.log('Task status response:', response);
				
				if (response.success) {
					if (response.data.completed) {
						// Task completed - update status and show download button
						$("#googletestresults").html('<div class="notice notice-success"><p>Task completed! Reviews have been downloaded.</p></div>').show();
						$("#divdownloadreviews").show();
					} else {
						// Still processing
						showLoadingGif();
					}
				} else {
					showError('Failed to check task status: ' + (response.data.message || 'Unknown error'));
				}
			},
			error: function(xhr, status, error) {
				console.error('Error checking task status:', error);
				showError('Failed to check task status. Please try again.');
			}
		});
	}

	
	/**
	 * Download reviews from completed task (for the main form)
	 */
	function downloadReviewsFromCompletedTask(taskId, placeId) {
		console.log('Downloading reviews from completed task. Task ID:', taskId, 'Place ID:', placeId);
		
		if (taskInProgress) {
			console.log('Task already in progress, ignoring download request');
			return false;
		}
		
		taskInProgress = true;
		$("#downloadcompletedtask").hide();
		$("#buttonloader").show();
		
		// Hide any previous messages
		$("#googletestresults2").hide();
		$("#googletestresultserror2").hide();
		
		// Make AJAX call to poll the completed task and download reviews
		$.ajax({
			url: adminjs_script_vars.ajax_url,
			type: 'POST',
			data: {
				action: 'wpfbr_dfs_poll_results',
				task_id: taskId,
				nonce: adminjs_script_vars.wpfb_nonce
			},
			success: function(response) {
				console.log('Download completed task response:', response);
				
				if (response.success) {
					if (response.data.completed) {
						// Task is completed, reviews should be automatically saved
						handlePollingSuccess(response.data);
					} else {
						// Task not completed yet (shouldn't happen for completed tasks)
						showError('Task is not completed yet. Please try again later.');
					}
				} else {
					// Handle error response
					var errorMsg = response.data.message || response.data.error || 'Unknown error occurred';
					showError(errorMsg);
				}
			},
			error: function(xhr, status, error) {
				console.error('Error downloading from completed task:', error);
				showError('Failed to download reviews from completed task. Please try again.');
			},
			complete: function() {
				taskInProgress = false;
				$("#downloadcompletedtask").show();
				$("#buttonloader").hide();
			}
		});
	}



	/**
	 * Update task status in database via AJAX
	 */
	function updateTaskStatusInDatabase(placeId, status) {
		console.log('Updating task status in database for place ID:', placeId, 'to:', status);
		
		$.ajax({
			url: adminjs_script_vars.ajax_url,
			type: 'POST',
			data: {
				action: 'wpfbr_dfs_update_task_status',
				place_id: placeId,
				task_status: status,
				nonce: adminjs_script_vars.wpfb_nonce
			},
			success: function(response) {
				console.log('Task status updated successfully');
			},
			error: function(xhr, status, error) {
				console.error('Error updating task status:', error);
			}
		});
	}


	/**
	 * Check if we have an existing completed task for the current place ID
	 */
	function checkForExistingTask() {
		var placeId = $("#gplaceid").val().trim();
		if (!placeId) {
			// Hide download section if no place ID
			$("#divdownloadreviews").hide();
			return;
		}
		
		console.log('Checking for existing task for place ID:', placeId);
		
		$.ajax({
			url: adminjs_script_vars.ajax_url,
			type: 'POST',
			data: {
				action: 'wpfbr_dfs_check_existing_task',
				place_id: placeId,
				nonce: adminjs_script_vars.wpfb_nonce
			},
			success: function(response) {
				console.log('Existing task check response:', response);
				
				if (response.success && response.data.task_id) {
					if (response.data.task_status === 'completed') {
						// Task is completed, show success message
						$("#divdownloadreviews").show();
						showDownloadSuccess(response.data);
						$("#googletestresults").html('<div class="notice notice-success"><p>Task completed! Reviews have been downloaded.</p></div>').show();
						console.log('Found completed task, showing success message');
					} else if (response.data.task_status === 'processing') {
						// Task is still processing, show loading gif
						showLoadingGif();
						console.log('Found processing task, showing loading gif');
					}
				} else {
					// No existing task, hide download section
					$("#divdownloadreviews").hide();
				}
			},
			error: function(xhr, status, error) {
				console.error('Error checking existing task:', error);
				// Hide download section on error
				$("#divdownloadreviews").hide();
			}
		});
	}

	/**
	 * Show loading gif while task is processing
	 */
	function showLoadingGif() {
		$("#divdownloadreviews").show();
		$("#downloadreviews").hide();
		$("#buttonloader2").show();
		$("#googletestresults2").html('<div class="w3-panel w3-pale-blue w3-display-container w3-border"><p>Task is processing... Please wait while we fetch your reviews. This could be quick or take a few minutes if there is a lot of traffic. Feel free to close this window and check back later.</p></div>').show();
	}

	/**
	 * Show download success message
	 */
	function showDownloadSuccess(data) {
		$("#downloadreviews").hide();
		$("#buttonloader2").hide();
		
		var totalReviews = data.total_reviews || 0;
		var savedReviews = data.saved_reviews || 0;
		var businessName = data.business_info ? data.business_info.businessname : 'Business';
		
		var message = '';
		if (savedReviews === totalReviews) {
			// All reviews were saved
			message = '<strong>' + savedReviews + ' reviews</strong> have been downloaded for <strong>' + businessName + '</strong>';
		} else {
			// Some reviews were duplicates
			message = '<strong>' + totalReviews + ' reviews</strong> returned from the crawl server, <strong>' + savedReviews + ' reviews</strong> added to Review List for <strong>' + businessName + '</strong>';
		}
		
		$("#googletestresults2").html(
			'<div class="w3-panel w3-pale-green w3-display-container w3-border">' +
			'<h4><i class="fa fa-check-circle" style="color: green;"></i> Reviews Downloaded Successfully!</h4>' +
			'<p>' + message + '</p>' +
			'<p>You can now view your reviews in the <a href="?page=wp_google-reviews">Review List</a> page.</p>' +
			'</div>'
		).show();
	}

	/**
	 * Update business info display
	 */
	function updateBusinessInfo(businessInfo) {
		console.log('updateBusinessInfo called with:', businessInfo);
		
		if (!businessInfo) {
			console.log('No business info provided');
			return;
		}
		
		// Update business name display if element exists
		if (businessInfo.businessname && $("#businessname").length) {
			$("#businessname").text(businessInfo.businessname);
		}
		
		// Update rating display if element exists
		if (businessInfo.rating && $("#rating").length) {
			$("#rating").text(businessInfo.rating);
		}
		
		// Update total reviews display if element exists
		if (businessInfo.totalreviews && $("#totalreviews").length) {
			$("#totalreviews").text(businessInfo.totalreviews);
		}
		
		// Update address display if element exists
		if (businessInfo.address && $("#address").length) {
			$("#address").text(businessInfo.address);
		}
		
		// Update phone display if element exists
		if (businessInfo.phone && $("#phone").length) {
			$("#phone").text(businessInfo.phone);
		}
		
		// Update website display if element exists
		if (businessInfo.website && $("#website").length) {
			$("#website").attr('href', businessInfo.website).text(businessInfo.website);
		}
		
		console.log('Business info updated successfully');
	}


})( jQuery );
