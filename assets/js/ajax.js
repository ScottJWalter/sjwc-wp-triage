/**
 * ajax.js
 */
jQuery(document).ready(function($) {

	// Once the button is clicked for the database upgrade
	$('body').on('click', '.dtjwpt-upgrade-db-link', function(e) {
	
		// Stop usual event from happening
		e.preventDefault();

		// Get the nonce field value that we need
		var ajax_nonce = $('.dtjwpt_upgrade_db_nonce').val();

		// Show the spinner so the user knows something is happening
		$('.dtjwpt-db-upgrade-spinner').css('visibility', 'visible');
	
		// Ask the user if they want to upgrade first
		if ( confirm( DTJWPT_AJAX.confirm_upgrade_db ) ) {

			// Run the Ajax request
			$.ajax({

				data: {
					action: 'dtjwpt_upgrade_plugin_db',
					safety: ajax_nonce
				},
				type: 'post',
				url: DTJWPT_AJAX.admin_ajax,
				success: function(response) {

					// Check that the upgrade worked or not
					if ( response == "1" ) {

						// Remove the warning message and show the success message
						$('.dtjwpt-notice-upgrade-success').show();
						$('.dtjwpt-notice-upgrade-warning').remove();

					}

					// Hide the spinner, we don't need it now
					$('.dtjwpt-db-upgrade-spinner').css('visibility', 'hidden');
				
				}
			
			});

		} else {

			// Hide the spinner, upgrade was cancelled
			$('.dtjwpt-db-upgrade-spinner').css('visibility', 'hidden');

		}
	
	});

	// When clicked, run the update roles function
	$('body').on('click', '.dtjwpt-button-plugin-roles', function(e) {
	
		// Prevent the normal click event
		e.preventDefault();

		// Get our form values from the request
		var ajax_nonce = $('.dtjwpt_plugin_roles_nonce').val();
		var projects = [];
		var components = [];
		var tickets = [];
		var notes = [];

		// Get all of the project checkboxes that are checked and put them into an array
		$('.dtjwpt-plugin-roles-form input[type="checkbox"]:checked.dtjwpt_project_role').each(function() {

			projects.push($(this).val());

		});

		// Get all of the component checkboxes that are checked and put them into an array
		$('.dtjwpt-plugin-roles-form input[type="checkbox"]:checked.dtjwpt_component_role').each(function() {

			components.push($(this).val());

		});

		// Get all of the project checkboxes that are checked and put them into an array
		$('.dtjwpt-plugin-roles-form input[type="checkbox"]:checked.dtjwpt_ticket_role').each(function() {

			tickets.push($(this).val());

		});

		// Get all of the note checkboxes that are checked and put them into an array
		$('.dtjwpt-plugin-roles-form input[type="checkbox"]:checked.dtjwpt_note_role').each(function() {

			notes.push($(this).val());

		});

		// Hide all notices until we need them
		$('.dtjwpt-notice-plugin-roles-success').hide();
		$('.dtjwpt-notice-plugin-roles-warning').hide();
		$('.dtjwpt-notice-plugin-roles-failure').hide();

		// Show the spinner element for progress
		$('.dtjwpt-plugin-roles-spinner').css('visibility', 'visible');	
	
		// Run the Ajax request
		$.ajax({

			data: {
				action: 'dtjwpt_plugin_roles_ajax',
				safety: ajax_nonce,
				project_roles: projects,
				component_roles: components,
				ticket_roles: tickets,
				note_roles: notes
			},
			type: 'post',
			url: DTJWPT_AJAX.admin_ajax,
			success: function(response) {

				// Hide the spinner now we've made the request
				$('.dtjwpt-plugin-roles-spinner').css('visibility', 'hidden');

				// Check whether the project was added or not
				if ( response == 1 || response == 2 ) {

					// Reload ticket info form
					$('.dtjwpt-button-plugin-roles tbody').load(window.location + ' .dtjwpt-button-plugin-roles tbody');

					// See which notice to show to the user
					if ( response == 1 ) {

						// Show the success notice to the user
						$('.dtjwpt-notice-plugin-roles-success').show();

					} else if ( response == 2 ) {

						// Show the warning notice to the user
						$('.dtjwpt-notice-plugin-roles-warning').show();

					}

				} else {

					// Show the error notice to the user
					$('.dtjwpt-notice-plugin-roles-failure').show();

				}

				$("html, body").animate({
					scrollTop: 0
				}, 'slow');
			
			}
		
		});
	
	});

	// Show and hide our add project form
	$('body').on('click', '.dtjwpt-create-project-toggle', function(e) {

		// Prevent the normal click event
		e.preventDefault();

		// Make sure the create ticket form is hidden
		$('.dtjwpt-create-ticket-form').hide();

		// Toggle the new project form
		$('.dtjwpt-create-project-form').slideToggle();

	});

	// Show and hide our update project form
	$('body').on('click', '.dtjwpt-update-project-toggle', function(e) {

		// Prevent the normal click event
		e.preventDefault();

		// Make sure the create ticket form is hidden
		$('.dtjwpt-create-ticket-form').hide();

		// Toggle the new project form
		$('.dtjwpt-update-project-form').slideToggle();

	});

	// Show and hide our add component form
	$('body').on('click', '.dtjwpt-create-component-toggle', function(e) {

		// Prevent the normal click event
		e.preventDefault();

		// Toggle the new project form
		$('.dtjwpt-create-component-form').slideToggle();

	});

	// Show and hide our add ticket form
	$('body').on('click', '.dtjwpt-create-ticket-toggle', function(e) {

		// Prevent the normal click event
		e.preventDefault();

		// Make sure the edit project form is hidden
		$('.dtjwpt-update-project-form').hide();

		// Toggle the new project form
		$('.dtjwpt-create-ticket-form').slideToggle();

	});

	// When the create project button is clicked
	$('body').on('click', '.dtjwpt-button-create-project', function(e) {
	
		// Prevent the normal click event
		e.preventDefault();

		// Get our form values from the request
		var ajax_nonce = $('.dtjwpt_create_project_nonce').val();
		var project_name = $('.dtjwpt-create-project-form .dtjwpt_project_name').val();
		var project_owner = $('.dtjwpt-create-project-form .dtjwpt_project_owner').val();

		// Hide all notices until we need them
		$('.dtjwpt-notice-create-project-success').hide();
		$('.dtjwpt-notice-create-project-failure').hide();

		// Show the spinner element for progress
		$('.dtjwpt-create-project-spinner').css('visibility', 'visible');	
	
		// Run the Ajax request
		$.ajax({

			data: {
				action: 'dtjwpt_create_project_ajax',
				safety: ajax_nonce,
				name: project_name,
				owner: project_owner
			},
			type: 'post',
			url: DTJWPT_AJAX.admin_ajax,
			success: function(response) {

				// Hide the spinner now we've made the request
				$('.dtjwpt-create-project-spinner').css('visibility', 'hidden');

				// Check whether the project was added or not
				if ( response == 1 ) {

					// Reload projects table
					$('.dtjwpt_projects_table').load(window.location + ' .dtjwpt_projects_table');

					// Reload create projects form
					$('.dtjwpt-create-project-form').slideToggle();

					// Reset the form to default values
					$('.dtjwpt-create-project-form .dtjwpt_project_name').val('');
					$('.dtjwpt-create-project-form .dtjwpt_project_owner').prop('selectedIndex', 0);

					// Show the success notice to the user
					$('.dtjwpt-notice-create-project-success').show();

				} else {

					// Show the error notice to the user
					$('.dtjwpt-notice-create-project-failure').show();

				}

				$("html, body").animate({
					scrollTop: 0
				}, 'slow');
			
			}
		
		});
	
	});

	// On click of the create new component button
	$('body').on('click', '.dtjwpt-button-create-component', function(e) {
	
		// Prevent the normal click event
		e.preventDefault();

		// Get our form values from the request
		var ajax_nonce = $('.dtjwpt_create_component_nonce').val();
		var component_name = $('.dtjwpt-create-component-form .dtjwpt_component_name').val();

		// Hide all notices until we need them
		$('.dtjwpt-notice-create-component-success').hide();
		$('.dtjwpt-notice-create-component-failure').hide();

		// Show the spinner element for progress
		$('.dtjwpt-create-component-spinner').css('visibility', 'visible');	
	
		// Run the Ajax request
		$.ajax({

			data: {
				action: 'dtjwpt_create_component_ajax',
				safety: ajax_nonce,
				name: component_name
			},
			type: 'post',
			url: DTJWPT_AJAX.admin_ajax,
			success: function(response) {

				// Hide the spinner now we've made the request
				$('.dtjwpt-create-component-spinner').css('visibility', 'hidden');

				// Check whether the component was added or not
				if ( response == 1 ) {

					// Reload components table
					$('.dtjwpt_components_table').load(window.location + ' .dtjwpt_components_table');

					// Reload create components form
					$('.dtjwpt-create-component-form').slideToggle();

					// Reset the form to default values
					$('.dtjwpt-create-component-form .dtjwpt_component_name').val('');

					// Show the success notice to the user
					$('.dtjwpt-notice-create-component-success').show();

				} else {

					// Show the error notice to the user
					$('.dtjwpt-notice-create-component-failure').show();

				}

				$("html, body").animate({
					scrollTop: 0
				}, 'slow');
			
			}
		
		});
	
	});

	// On click of the update project button
	$('body').on('click', '.dtjwpt-button-update-project', function(e) {
	
		// Prevent the normal click event
		e.preventDefault();

		// Get our form values from the request
		var ajax_nonce = $('.dtjwpt_update_project_nonce').val();
		var project_id = $('.dtjwpt-update-project-form .dtjwpt_project_id').val();
		var project_name = $('.dtjwpt-update-project-form .dtjwpt_project_name').val();
		var project_owner = $('.dtjwpt-update-project-form .dtjwpt_project_owner').val();

		// Hide all notices until we need them
		$('.dtjwpt-notice-update-project-success').hide();
		$('.dtjwpt-notice-update-project-failure').hide();

		// Show the spinner element for progress
		$('.dtjwpt-update-project-spinner').css('visibility', 'visible');	
	
		// Run the Ajax request
		$.ajax({

			data: {
				action: 'dtjwpt_update_project_ajax',
				safety: ajax_nonce,
				id: project_id,
				name: project_name,
				owner: project_owner
			},
			type: 'post',
			url: DTJWPT_AJAX.admin_ajax,
			success: function(response) {

				// Hide the spinner now we've made the request
				$('.dtjwpt-update-project-spinner').css('visibility', 'hidden');

				// Check whether the project was added or not
				if ( response == 1 ) {

					// Reload projects table
					$('.dtjwpt-projects').load(window.location + ' .dtjwpt-projects-title');

					// Reload update projects form
					$('.dtjwpt-update-project-form').slideToggle();

					// Show the success notice to the user
					$('.dtjwpt-notice-update-project-success').show();

				} else {

					// Show the error notice to the user
					$('.dtjwpt-notice-update-project-failure').show();

				}

				$("html, body").animate({
					scrollTop: 0
				}, 'slow');
			
			}
		
		});
	
	});

	// Once the create ticket button is clicked
	$('body').on('click', '.dtjwpt-button-create-ticket', function(e) {
	
		// Prevent the normal click event
		e.preventDefault();

		// Get our form values from the request
		var ajax_nonce = $('.dtjwpt_create_ticket_nonce').val();
		var project_id = $('.dtjwpt-create-ticket-form .dtjwpt_project_id').val();
		var ticket_name = $('.dtjwpt-create-ticket-form .dtjwpt_ticket_name').val();
		var ticket_description = $('.dtjwpt-create-ticket-form .dtjwpt_ticket_description').val();
		var ticket_assignee = $('.dtjwpt-create-ticket-form .dtjwpt_ticket_assignee').val();
		var ticket_component = $('.dtjwpt-create-ticket-form .dtjwpt_ticket_component').val();
		var ticket_type = $('.dtjwpt-create-ticket-form .dtjwpt_ticket_type').val();
		var ticket_priority = $('.dtjwpt-create-ticket-form .dtjwpt_ticket_priority').val();

		// Hide all notices until we need them
		$('.dtjwpt-notice-create-ticket-success').hide();
		$('.dtjwpt-notice-create-ticket-failure').hide();

		// Show the spinner element for progress
		$('.dtjwpt-create-ticket-spinner').css('visibility', 'visible');	
	
		// Run the Ajax request
		$.ajax({

			data: {
				action: 'dtjwpt_create_ticket_ajax',
				safety: ajax_nonce,
				project: project_id,
				name: ticket_name,
				description: ticket_description,
				assignee: ticket_assignee,
				component: ticket_component,
				type: ticket_type,
				priority: ticket_priority
			},
			type: 'post',
			url: DTJWPT_AJAX.admin_ajax,
			success: function(response) {

				// Hide the spinner now we've made the request
				$('.dtjwpt-create-ticket-spinner').css('visibility', 'hidden');

				// Check whether the project was added or not
				if ( response == 1 ) {

					// Reload projects table
					$('.dtjwpt_tickets_table').load(window.location + ' .dtjwpt_tickets_table');

					// Reload create tickets form
					$('.dtjwpt-create-ticket-form').slideToggle();

					// Reset the form to default values
					$('.dtjwpt-create-ticket-form .dtjwpt_ticket_name').val('');
					$('.dtjwpt-create-ticket-form .dtjwpt_ticket_description').val('');
					$('.dtjwpt-create-ticket-form .dtjwpt_ticket_assignee').prop('selectedIndex', 0);
					$('.dtjwpt-create-ticket-form .dtjwpt_ticket_component').prop('selectedIndex', 0);
					$('.dtjwpt-create-ticket-form .dtjwpt_ticket_type').prop('selectedIndex', 0);
					$('.dtjwpt-create-ticket-form .dtjwpt_ticket_priority').prop('selectedIndex', 0);

					// Show the success notice to the user
					$('.dtjwpt-notice-create-ticket-success').show();

				} else {

					// Show the error notice to the user
					$('.dtjwpt-notice-create-ticket-failure').show();

				}

				$("html, body").animate({
					scrollTop: 0
				}, 'slow');
			
			}
		
		});
	
	});

	// On click, update the ticket information
	$('body').on('click', '.dtjwpt-button-update-ticket', function(e) {
	
		// Prevent the normal click event
		e.preventDefault();

		// Get our form values from the request
		var ajax_nonce = $('.dtjwpt_update_ticket_nonce').val();
		var ticket_id = $('.dtjwpt-update-ticket-form .dtjwpt_ticket_id').val();
		var ticket_name = $('.dtjwpt-update-ticket-form .dtjwpt_ticket_name').val();
		var ticket_description = $('.dtjwpt-update-ticket-form .dtjwpt_ticket_description').val();
		var ticket_assignee = $('.dtjwpt-update-ticket-form .dtjwpt_ticket_assignee').val();
		var ticket_component = $('.dtjwpt-update-ticket-form .dtjwpt_ticket_component').val();
		var ticket_type = $('.dtjwpt-update-ticket-form .dtjwpt_ticket_type').val();
		var ticket_priority = $('.dtjwpt-update-ticket-form .dtjwpt_ticket_priority').val();
		var ticket_status = $('.dtjwpt-update-ticket-form .dtjwpt_ticket_status').val();

		// Hide all notices until we need them
		$('.dtjwpt-notice-update-ticket-success').hide();
		$('.dtjwpt-notice-update-ticket-failure').hide();

		// Show the spinner element for progress
		$('.dtjwpt-update-ticket-spinner').css('visibility', 'visible');	
	
		// Run the Ajax request
		$.ajax({

			data: {
				action: 'dtjwpt_update_ticket_ajax',
				safety: ajax_nonce,
				ticket: ticket_id,
				name: ticket_name,
				description: ticket_description,
				assignee: ticket_assignee,
				component: ticket_component,
				type: ticket_type,
				priority: ticket_priority,
				status: ticket_status
			},
			type: 'post',
			url: DTJWPT_AJAX.admin_ajax,
			success: function(response) {

				// Hide the spinner now we've made the request
				$('.dtjwpt-update-ticket-spinner').css('visibility', 'hidden');

				// Check whether the project was added or not
				if ( response == 1 ) {

					// Reload ticket info form
					$('.dtjwpt-ticket-form').load(window.location + ' .dtjwpt-update-ticket-form');

					// Show the success notice to the user
					$('.dtjwpt-notice-update-ticket-success').show();

				} else {

					// Show the error notice to the user
					$('.dtjwpt-notice-update-ticket-failure').show();

				}

				$("html, body").animate({
					scrollTop: 0
				}, 'slow');
			
			}
		
		});
	
	});

	// When posting a note to a ticket
	$('body').on('click', '.dtjwpt-button-create-note', function(e) {
	
		// Prevent the normal click event
		e.preventDefault();

		// Get our form values from the request
		var ajax_nonce = $('.dtjwpt_create_note_nonce').val();
		var ticket_id = $('.dtjwpt-create-note-form .dtjwpt_ticket_id').val();
		var note = $('.dtjwpt-create-note-form .dtjwpt_note_comment').val();
		var alter;

		// Check if the button had the alter status class
		if ( $(this).hasClass('dtjwpt-alter-status') ) {

			// The alter status button was clicked
			alter = '1';

		} else {

			// The alter status button wasn't clicked
			alter = '0';

		}

		// Hide all notices until we need them
		$('.dtjwpt-notice-create-note-success').hide();
		$('.dtjwpt-notice-create-note-failure').hide();

		// Show the spinner element for progress
		$('.dtjwpt-create-note-spinner').css('visibility', 'visible');	
	
		// Run the Ajax request
		$.ajax({

			data: {
				action: 'dtjwpt_create_note_ajax',
				safety: ajax_nonce,
				ticket: ticket_id,
				note: note,
				alter: alter
			},
			type: 'post',
			url: DTJWPT_AJAX.admin_ajax,
			success: function(response) {

				// Hide the spinner now we have a response back
				$('.dtjwpt-create-note-spinner').css('visibility', 'hidden');

				// Get whether or not it was a successful request
				if ( response == 1 ) {

					// Reload the list of notes for this ticket
					$('.dtjwpt-notes').load(window.location + ' .dtjwpt-notes-list');

					// Check if we need to reload the ticket details as well
					if ( alter == '1' ) {

						// Reload the ticket details section
						$('.dtjwpt-ticket-form').load(window.location + ' .dtjwpt-update-ticket-form');

					}

					// Reset the note textarea so it's blank
					$('.dtjwpt-create-note-form .dtjwpt_note_comment').val('');

					// Show the user a success message
					$('.dtjwpt-notice-create-note-success').show();

				} else {

					// Show the user a failure message
					$('.dtjwpt-notice-create-note-failure').show();

				}

				$("html, body").animate({
					scrollTop: 0
				}, 'slow');
			
			}
		
		});
	
	});

	// When deleting an existing note
	$('body').on('click', '.dtjwpt-button-delete-note', function(e) {
	
		// Prevent the normal click event
		e.preventDefault();

		// Get our form values from the request
		var ajax_nonce = $('.dtjwpt_delete_note_nonce').val();
		var note_id = $(this).attr('data-note');

		// Hide all notices until we need them
		$('.dtjwpt-notice-delete-note-success').hide();
		$('.dtjwpt-notice-delete-note-failure').hide();

		if ( confirm( DTJWPT_AJAX.confirm_delete_note ) ) {

			// Run the Ajax request
			$.ajax({

				data: {
					action: 'dtjwpt_delete_note_ajax',
					safety: ajax_nonce,
					note: note_id
				},
				type: 'post',
				url: DTJWPT_AJAX.admin_ajax,
				success: function(response) {

					// Get whether or not it was a successful request
					if ( response == 1 ) {

						// Reload the list of notes for this ticket
						$('.dtjwpt-notes').load(window.location + ' .dtjwpt-notes-list');

						// Show the user a success message
						$('.dtjwpt-notice-delete-note-success').show();

					} else {

						// Show the user a failure message
						$('.dtjwpt-notice-delete-note-failure').show();

					}

					$("html, body").animate({
						scrollTop: 0
					}, 'slow');
				
				}
			
			});

		}
	
	});

});

