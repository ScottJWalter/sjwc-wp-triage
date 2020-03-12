<?php

/**
 * ajax.php
 */

// No thank you
if ( ! defined('ABSPATH') ) die;

/**
 * Function that updates the plugin capabilities
 * Accepts zero arguments.
 * 
 * @since 1.0
 * @return string Response returned to the Ajax request.
 */
function dtjwpt_plugin_roles_ajax() {

	global $wpdb;

	// Set the return value to a default
	$dtjwpt_plugin_roles_value = "0";

	// Get the full list of current WordPress roles
	$dtjwpt_get_roles = new WP_Roles();

	// Get our nonce from the Ajax request
	$dtjwpt_nonce_check = check_ajax_referer('dtjwpt_plugin_roles_nonce', 'safety', false);

	// Get the request data and put them into variables
	$dtjwpt_project_roles = $_REQUEST['project_roles'];
	$dtjwpt_component_roles = $_REQUEST['component_roles'];
	$dtjwpt_ticket_roles = $_REQUEST['ticket_roles'];
	$dtjwpt_note_roles = $_REQUEST['note_roles'];

	// Check if the nonce matches up correctly
	if ( $dtjwpt_nonce_check == true ) {

		// First, remove all plugin related capabilities
		foreach ( $dtjwpt_get_roles->roles as $role ) {

			// Get the name of the current role
			$dtjwpt_role_name = strtolower(str_replace(' ', '_', sanitize_text_field($role['name'])));

			// Get the role based on the current role name
			$dtjwpt_role_object = get_role($dtjwpt_role_name);

			// Make sure the role object isn't empty
			if ( ! empty( $dtjwpt_role_object ) ) {

				// Remove all plugin related capcbilities from the role
				$dtjwpt_role_object->remove_cap(DTJWPT_CAP_MANAGE_PROJECTS);
				$dtjwpt_role_object->remove_cap(DTJWPT_CAP_MANAGE_COMPONENTS);
				$dtjwpt_role_object->remove_cap(DTJWPT_CAP_MANAGE_TICKETS);
				$dtjwpt_role_object->remove_cap(DTJWPT_CAP_MANAGE_NOTES);

			}

		}

		// Check if we need to add the projects capabilities
		if ( $dtjwpt_project_roles !== NULL ) {

			// Loop through the roles
			foreach ( $dtjwpt_project_roles as $project_role ) {

				// Make the role lower case and add the capability
				$dtjwpt_project_role_name = strtolower(sanitize_text_field($project_role));
				$dtjwpt_project_role_object = get_role($dtjwpt_project_role_name);

				// Make sure the role object isn't empty
				if ( ! empty( $dtjwpt_project_role_object ) ) {
				
					// Add the capability to this role
					$dtjwpt_project_role_object->add_cap(DTJWPT_CAP_MANAGE_PROJECTS);

				}

			}

		}

		// Check if we need to add the component capabilities
		if ( $dtjwpt_component_roles !== NULL ) {

			// Loop through the roles
			foreach ( $dtjwpt_component_roles as $component_role ) {

				// Make the role lower case and add the capability
				$dtjwpt_component_role_name = strtolower(sanitize_text_field($component_role));
				$dtjwpt_component_role_object = get_role($dtjwpt_component_role_name);

				// Make sure the role object isn't empty
				if ( ! empty( $dtjwpt_component_role_object ) ) {

					// Add the capability to this role
					$dtjwpt_component_role_object->add_cap(DTJWPT_CAP_MANAGE_COMPONENTS);

				}

			}

		}

		// Check if we need to add the tickets capabilities
		if ( $dtjwpt_ticket_roles !== NULL ) {

			// Loop through the roles
			foreach ( $dtjwpt_ticket_roles as $ticket_role ) {

				// Make the role lower case and add the capability
				$dtjwpt_ticket_role_name = strtolower(sanitize_text_field($ticket_role));
				$dtjwpt_ticket_role_object = get_role($dtjwpt_ticket_role_name);

				// Make sure the role object isn't empty
				if ( ! empty( $dtjwpt_ticket_role_object ) ) {

					// Add the capability to this role
					$dtjwpt_ticket_role_object->add_cap(DTJWPT_CAP_MANAGE_TICKETS);

				}

			}

		}

		// Check if we need to add the notes capabilities
		if ( $dtjwpt_note_roles !== NULL ) {

			// Loop through the roles
			foreach ( $dtjwpt_note_roles as $note_role ) {

				// Make the role lower case and add the capability
				$dtjwpt_note_role_name = strtolower(sanitize_text_field($note_role));
				$dtjwpt_note_role_object = get_role($dtjwpt_note_role_name);

				// Make sure the role object isn't empty
				if ( ! empty( $dtjwpt_note_role_object ) ) {

					// Add the capability to this role
					$dtjwpt_note_role_object->add_cap(DTJWPT_CAP_MANAGE_NOTES);

				}

			}

		}

		// Check whether or not the roles for either options were updated
		if ( $dtjwpt_project_roles === NULL && $dtjwpt_component_roles === NULL && $dtjwpt_ticket_roles === NULL && $dtjwpt_note_roles === NULL ) {

			// Return feedback but report that no one has any capabilities for the plugin
			$dtjwpt_plugin_roles_value = "2";

		} else {

			// Return feedback to acknowledge the submission was successful
			$dtjwpt_plugin_roles_value = "1";

		}

	}

	// Return a value on completion
	echo $dtjwpt_plugin_roles_value;
	wp_die();

}
add_action('wp_ajax_dtjwpt_plugin_roles_ajax', 'dtjwpt_plugin_roles_ajax');

/**
 * Function that creates a new project within the system.
 * Accepts zero arguments.
 * 
 * @since 1.0
 * @return string Response returned to the Ajax request.
 */
function dtjwpt_create_project_ajax() {

	global $wpdb;

	// Set the default response code
	$dtjwpt_create_project_value = "0";

	// Get the form nonce to check that it was a legit request
	$dtjwpt_nonce_check = check_ajax_referer('dtjwpt_create_project_nonce', 'safety', false);

	// Get the data sent from the form
	$dtjwpt_new_project_name = trim(sanitize_text_field($_REQUEST['name']));
	$dtjwpt_new_project_owner = sanitize_text_field($_REQUEST['owner']);

	// Check if the nonce passed the check
	if ( $dtjwpt_nonce_check == true ) {

		// Check that the current user can manage projects
		if ( dtjwpt_can_manage_projects() ) {

			// Make sure the project name is at least 1 character
			if ( strlen($dtjwpt_new_project_name) >= 1 ) {

				// Convert the project owner id to an integer
				$dtjwpt_new_project_owner = intval($dtjwpt_new_project_owner);

				// Set the value to 1 because it was a success
				$dtjwpt_create_project_value = "1";

				// Add the project to the database
				$wpdb->insert(
					$wpdb->prefix . DTJWPT_DB_PROJECTS, 
					array(
						'owner_id'	=> $dtjwpt_new_project_owner,
						'name'		=> $dtjwpt_new_project_name
					)
				);

				// Set the default for sending the email
				$dtjwpt_send_the_email = false;

				// Get the data object for the project
				$dtjwpt_get_project = dtjwpt_get_project($wpdb->insert_id);

				// Get certain data that we want to use within the body text
				$dtjwpt_email_project_user = get_userdata(get_current_user_id());
				$dtjwpt_email_link = admin_url('admin.php?page=dtjwpt_triage&triage=tickets&project_id=' . $dtjwpt_get_project->project_id);

				// Check if the project has an owner or not
				if ( $dtjwpt_get_project->owner_id != 0 ) {

					// Get the project owner email address
					$dtjwpt_send_to_user = get_userdata($dtjwpt_get_project->owner_id);

					// Send the email
					$dtjwpt_send_the_email = true;

				}

				// Continue if we're allowed to send the email
				if ( $dtjwpt_send_the_email ) {

					// Create the email arguments to send
					$dtjwpt_email_args = array(
						'to' => $dtjwpt_send_to_user->data->user_email,
						'subject' => __('New Project Created', 'wp-triage'),
						'body' => sprintf(__('<p>Hello!</p><p>A new project has been created by <strong>%s</strong>.</p><p><a href="%s">Review the new project #%s</a>.</p><p>Sent from WordPress via WP Triage.</p>', 'wp-triage'), $dtjwpt_email_project_user->data->display_name, $dtjwpt_email_link, $dtjwpt_get_project->project_id)
					);

					// Send the email out
					dtjwpt_send_mail($dtjwpt_email_args);

				}

			}

		}

	}

	// Return a response to the request
	echo $dtjwpt_create_project_value;
	wp_die();

}
add_action('wp_ajax_dtjwpt_create_project_ajax', 'dtjwpt_create_project_ajax');

/**
 * Function that updates an existing project within the system.
 * Accepts zero arguments.
 * 
 * @since 1.0
 * @return string Response returned to the Ajax request.
 */
function dtjwpt_update_project_ajax() {

	global $wpdb;

	// Set the default response code
	$dtjwpt_update_project_value = "0";

	// Get the form nonce to check that it was a legit request
	$dtjwpt_nonce_check = check_ajax_referer('dtjwpt_update_project_nonce', 'safety', false);

	// Get the data sent from the form
	$dtjwpt_update_project_id = sanitize_text_field($_REQUEST['id']);
	$dtjwpt_update_project_name = trim(sanitize_text_field($_REQUEST['name']));
	$dtjwpt_update_project_owner = sanitize_text_field($_REQUEST['owner']);

	// Check if the nonce passed the check
	if ( $dtjwpt_nonce_check == true ) {

		// Check that the current user can manage projects
		if ( dtjwpt_can_modify_project($dtjwpt_update_project_id) ) {

			// Make sure the project name is at least 1 character
			if ( strlen($dtjwpt_update_project_name) >= 1 ) {

				// Set the value to 1 because it was a success
				$dtjwpt_update_project_value = "1";

				// Update the project row in the database
				$wpdb->update(
					$wpdb->prefix . DTJWPT_DB_PROJECTS, 
					array(
						'name'		=> $dtjwpt_update_project_name,
						'owner_id'	=> $dtjwpt_update_project_owner
					),
					array(
						'project_id' => $dtjwpt_update_project_id
					)
				);

				// Set the default for sending the email
				$dtjwpt_send_the_email = false;

				// Get the data object for the project
				$dtjwpt_get_project = dtjwpt_get_project($dtjwpt_update_project_id);

				// Get certain data that we want to use within the body text
				$dtjwpt_email_project_user = get_userdata(get_current_user_id());
				$dtjwpt_email_link = admin_url('admin.php?page=dtjwpt_triage&triage=tickets&project_id=' . $dtjwpt_get_project->project_id);

				// Check if the project has an owner or not
				if ( $dtjwpt_get_project->owner_id != 0 ) {

					// Get the project owner email address
					$dtjwpt_send_to_user = get_userdata($dtjwpt_get_project->owner_id);

					// Send the email
					$dtjwpt_send_the_email = true;

				}

				// Continue if we're allowed to send the email
				if ( $dtjwpt_send_the_email ) {

					// Create the email arguments to send
					$dtjwpt_email_args = array(
						'to' => $dtjwpt_send_to_user->data->user_email,
						'subject' => __('Project Updated', 'wp-triage'),
						'body' => sprintf(__('<p>Hello!</p><p>Changes have been made by <strong>%s</strong> to the following project.</p><p><a href="%s">Review the changes for project #%s</a>.</p><p>Sent from WordPress via WP Triage.</p>', 'wp-triage'), $dtjwpt_email_project_user->data->display_name, $dtjwpt_email_link, $dtjwpt_get_project->project_id)
					);

					// Send the email out
					dtjwpt_send_mail($dtjwpt_email_args);

				}

			}

		}

	}

	// Return a response to the request
	echo $dtjwpt_update_project_value;
	wp_die();

}
add_action('wp_ajax_dtjwpt_update_project_ajax', 'dtjwpt_update_project_ajax');

/**
 * Function that creates a new component within the system.
 * Accepts zero arguments.
 * 
 * @since 1.2
 * @return string Response returned to the Ajax request.
 */
function dtjwpt_create_component_ajax() {

	global $wpdb;

	// Set the default response code
	$dtjwpt_create_component_value = "0";

	// Get the form nonce to check that it was a legit request
	$dtjwpt_nonce_check = check_ajax_referer('dtjwpt_create_component_nonce', 'safety', false);

	// Get the data sent from the form
	$dtjwpt_new_component_name = trim(sanitize_text_field($_REQUEST['name']));

	// Check if the nonce passed the check
	if ( $dtjwpt_nonce_check == true ) {

		// Check that the current user can manage components
		if ( dtjwpt_can_manage_components() ) {

			// Make sure the component name is at least 1 character
			if ( strlen($dtjwpt_new_component_name) >= 1 ) {

				// Set the value to 1 because it was a success
				$dtjwpt_create_component_value = "1";

				// Add the component to the database
				$wpdb->insert(
					$wpdb->prefix . DTJWPT_DB_COMPONENTS,
					array(
						'name'			=> $dtjwpt_new_component_name,
						'create_date'	=> date('Y-m-d H:i:s')
					)
				);

			}

		}

	}

	// Return a response to the request
	echo $dtjwpt_create_component_value;
	wp_die();

}
add_action('wp_ajax_dtjwpt_create_component_ajax', 'dtjwpt_create_component_ajax');

/**
 * Function that creates a new ticket within the system.
 * Accepts zero arguments.
 * 
 * @since 1.0
 * @return string Response returned to the Ajax request.
 */
function dtjwpt_create_ticket_ajax() {

	global $wpdb;

	// Set the response code now to be overridden if successful
	$dtjwpt_create_ticket_value = "0";

	// Get the form nonce to check that it was a legit request
	$dtjwpt_nonce_check = check_ajax_referer('dtjwpt_create_ticket_nonce', 'safety', false);

	// Get the data sent from the form
	$dtjwpt_new_ticket_project = sanitize_text_field($_REQUEST['project']);
	$dtjwpt_new_ticket_name = trim(sanitize_text_field($_REQUEST['name']));
	$dtjwpt_new_ticket_description = trim(sanitize_textarea_field($_REQUEST['description']));
	$dtjwpt_new_ticket_assignee = sanitize_text_field($_REQUEST['assignee']);
	$dtjwpt_new_ticket_component = sanitize_text_field($_REQUEST['component']);
	$dtjwpt_new_ticket_type = sanitize_text_field($_REQUEST['type']);
	$dtjwpt_new_ticket_priority = sanitize_text_field($_REQUEST['priority']);

	// Convert the number based values to integers
	$dtjwpt_new_ticket_project = intval($dtjwpt_new_ticket_project);
	$dtjwpt_new_ticket_assignee = intval($dtjwpt_new_ticket_assignee);
	$dtjwpt_new_ticket_component = intval($dtjwpt_new_ticket_component);
	$dtjwpt_new_ticket_priority = intval($dtjwpt_new_ticket_priority);

	// Check if the nonce passed the check
	if ( $dtjwpt_nonce_check == true ) {

		// Check if the current user can create new tickets
		if ( dtjwpt_can_manage_tickets() ) {

			// Check that the project for this ticket exists
			$dtjwpt_get_project_data = $wpdb->get_var(
				$wpdb->prepare(
					"SELECT COUNT(*) FROM " . $wpdb->prefix . DTJWPT_DB_PROJECTS . " WHERE project_id = %s", $dtjwpt_new_ticket_project
				)
			);

			// Check that it exists within the database
			if ( $dtjwpt_get_project_data == 1 ) {

				// Get the project meta data from the database
				$dtjwpt_get_project_data = $wpdb->get_results(
					$wpdb->prepare(
						"SELECT * FROM " . $wpdb->prefix . DTJWPT_DB_PROJECTS . " WHERE project_id = %s", $dtjwpt_new_ticket_project
					)
				);

				// Check if the assignee field is left to unassigned
				if ( $dtjwpt_new_ticket_assignee == 0 ) {

					// Check if the auto assign option is on
					if ( get_option('dtjwpt_auto_assign_owners') == "on" ) {

						// Override the assign for this ticket
						$dtjwpt_new_ticket_assignee = intval($dtjwpt_get_project_data[0]->owner_id);

					}

				}

				// Check that the ticket name has been provided
				if ( strlen($dtjwpt_new_ticket_name) >= 1 ) {

					// Check the ticket type is either a bug (b) or feature (f)
					if ( $dtjwpt_new_ticket_type == "b" || $dtjwpt_new_ticket_type == "s" || $dtjwpt_new_ticket_type == "f" ) {

						// Check that the priority is also valid
						if ( $dtjwpt_new_ticket_priority == 1 || $dtjwpt_new_ticket_priority == 2 || $dtjwpt_new_ticket_priority == 3 ) {

							// Set the value to 1 because it was a success
							$dtjwpt_create_ticket_value = "1";

							// Create the new ticket in the database
							$wpdb->insert(
								$wpdb->prefix . DTJWPT_DB_TICKETS, 
								array(
									'project_id'		=> $dtjwpt_new_ticket_project,
									'author_id'			=> get_current_user_id(),
									'assignee_id'		=> $dtjwpt_new_ticket_assignee,
									'component_id'		=> $dtjwpt_new_ticket_component,
									'updater_id'		=> 0,
									'name'				=> $dtjwpt_new_ticket_name,
									'description'		=> $dtjwpt_new_ticket_description,
									'type'				=> $dtjwpt_new_ticket_type,
									'priority'			=> $dtjwpt_new_ticket_priority,
									'status'			=> 0,
									'create_date'		=> date('Y-m-d H:i:s'),
									'update_date'		=> date('Y-m-d H:i:s')
								)
							);

							// Set the default for sending the email
							$dtjwpt_send_the_email = false;

							// Get the data objects for the project and ticket
							$dtjwpt_get_ticket = dtjwpt_get_ticket($wpdb->insert_id);
							$dtjwpt_get_project = dtjwpt_get_project($dtjwpt_get_ticket->project_id);

							// Get certain data that we want to use within the body text
							$dtjwpt_email_ticket_user = get_userdata(get_current_user_id());
							$dtjwpt_email_link = admin_url('admin.php?page=dtjwpt_triage&triage=tickets&ticket_id=' . $dtjwpt_get_ticket->ticket_id);

							// Check that someone is assigned to the ticket
							if ( $dtjwpt_get_ticket->assignee_id != 0 ) {

								// Get the ticket assignee email address
								$dtjwpt_send_to_user = get_userdata($dtjwpt_get_ticket->assignee_id);

								// Send the email
								$dtjwpt_send_the_email = true;

							} else {

								// No one is assigned, check the project owner
								if ( $dtjwpt_get_project->owner_id != 0 ) {

									// Get the project owner email address
									$dtjwpt_send_to_user = get_userdata($dtjwpt_get_project->owner_id);

									// Send the email
									$dtjwpt_send_the_email = true;

								}

							}

							// Continue if we're allowed to send the email
							if ( $dtjwpt_send_the_email ) {

								// Create the email arguments to send
								$dtjwpt_email_args = array(
									'to' => $dtjwpt_send_to_user->data->user_email,
									'subject' => __('New Ticket Created', 'wp-triage'),
									'body' => sprintf(__('<p>Hello!</p><p>A new ticket has been created by <strong>%s</strong>.</p><p><a href="%s">Review the new ticket #%s</a>.</p><p>Sent from WordPress via WP Triage.</p>', 'wp-triage'), $dtjwpt_email_ticket_user->data->display_name, $dtjwpt_email_link, $dtjwpt_get_ticket->ticket_id)
								);

								// Send the email out
								dtjwpt_send_mail($dtjwpt_email_args);

							}

						}

					}

				}

			}

		}

	}

	// Return a response to the request
	echo $dtjwpt_create_ticket_value;
	wp_die();

}
add_action('wp_ajax_dtjwpt_create_ticket_ajax', 'dtjwpt_create_ticket_ajax');

/**
 * Function that updates a ticket within the system.
 * Accepts zero arguments.
 * 
 * @since 1.0
 * @return string Response returned to the Ajax request.
 */
function dtjwpt_update_ticket_ajax() {

	global $wpdb;

	// Set the response code to the default value npw
	$dtjwpt_update_ticket_value = "0";

	// Get our form nonce to check that it came from the correct request
	$dtjwpt_nonce_check = check_ajax_referer('dtjwpt_update_ticket_nonce', 'safety', false);

	// Get the data sent from the form
	$dtjwpt_update_ticket_id = sanitize_text_field($_REQUEST['ticket']);
	$dtjwpt_update_ticket_name = trim(sanitize_text_field($_REQUEST['name']));
	$dtjwpt_update_ticket_description = trim(sanitize_textarea_field($_REQUEST['description']));
	$dtjwpt_update_ticket_assignee = sanitize_text_field($_REQUEST['assignee']);
	$dtjwpt_update_ticket_component = sanitize_text_field($_REQUEST['component']);
	$dtjwpt_update_ticket_type = sanitize_text_field($_REQUEST['type']);
	$dtjwpt_update_ticket_priority = sanitize_text_field($_REQUEST['priority']);
	$dtjwpt_update_ticket_status = sanitize_text_field($_REQUEST['status']);

	// Convert the number based values to integers
	$dtjwpt_update_ticket_assignee = intval($dtjwpt_update_ticket_assignee);
	$dtjwpt_update_ticket_component = intval($dtjwpt_update_ticket_component);
	$dtjwpt_update_ticket_priority = intval($dtjwpt_update_ticket_priority);
	$dtjwpt_update_ticket_status = intval($dtjwpt_update_ticket_status);

	// Check if the nonce passed the check
	if ( $dtjwpt_nonce_check == true ) {

		// Check that the user can actually update this ticket
		if ( dtjwpt_can_modify_ticket($dtjwpt_update_ticket_id) ) {

			// Check the ticket name is at least one character
			if ( strlen($dtjwpt_update_ticket_name) >= 1 ) {

				// Check that the ticket type is valid
				if ( $dtjwpt_update_ticket_type == "b" || $dtjwpt_update_ticket_type == "s" || $dtjwpt_update_ticket_type == "f" ) {

					// Check the priority value is valid
					if ( $dtjwpt_update_ticket_priority == 1 || $dtjwpt_update_ticket_priority == 2 || $dtjwpt_update_ticket_priority == 3 ) {

						// Lastly, check that the status is valid as well
						if ( $dtjwpt_update_ticket_status == 0 || $dtjwpt_update_ticket_status == 1 ) {

							// All the checks passed, update the ticket
							$dtjwpt_update_ticket_value = "1";

							// Update the ticket within the database
							$wpdb->update(
								$wpdb->prefix . DTJWPT_DB_TICKETS, 
								array(
									'name'				=> $dtjwpt_update_ticket_name,
									'description'		=> $dtjwpt_update_ticket_description,
									'assignee_id'		=> $dtjwpt_update_ticket_assignee,
									'component_id'		=> $dtjwpt_update_ticket_component,
									'updater_id'		=> get_current_user_id(),
									'type'				=> $dtjwpt_update_ticket_type,
									'priority'			=> $dtjwpt_update_ticket_priority,
									'status'			=> $dtjwpt_update_ticket_status,
									'update_date'		=> date('Y-m-d H:i:s')
								),
								array(
									'ticket_id' => $dtjwpt_update_ticket_id
								)
							);

							// Set the default for sending the email
							$dtjwpt_send_the_email = false;

							// Get the data objects for the project and ticket
							$dtjwpt_get_ticket = dtjwpt_get_ticket($dtjwpt_update_ticket_id);
							$dtjwpt_get_project = dtjwpt_get_project($dtjwpt_get_ticket->project_id);

							// Get certain data that we want to use within the body text
							$dtjwpt_email_update_user = get_userdata(get_current_user_id());
							$dtjwpt_email_link = admin_url('admin.php?page=dtjwpt_triage&triage=tickets&ticket_id=' . $dtjwpt_get_ticket->ticket_id);

							// Check that someone is assigned to the ticket
							if ( $dtjwpt_get_ticket->assignee_id != 0 ) {

								// Get the ticket assignee email address
								$dtjwpt_send_to_user = get_userdata($dtjwpt_get_ticket->assignee_id);

								// Send the email
								$dtjwpt_send_the_email = true;

							} else {

								// No one is assigned, check the project owner
								if ( $dtjwpt_get_project->owner_id != 0 ) {

									// Get the project owner email address
									$dtjwpt_send_to_user = get_userdata($dtjwpt_get_project->owner_id);

									// Send the email
									$dtjwpt_send_the_email = true;

								}

							}

							// Continue if we're allowed to send the email
							if ( $dtjwpt_send_the_email ) {

								// Create the email arguments to send
								$dtjwpt_email_args = array(
									'to' => $dtjwpt_send_to_user->data->user_email,
									'subject' => __('Ticket Updated', 'wp-triage'),
									'body' => sprintf(__('<p>Hello!</p><p>Changes have been made by <strong>%s</strong> to the following ticket.</p><p><a href="%s">Review the changes for ticket #%s</a>.</p><p>Sent from WordPress via WP Triage.</p>', 'wp-triage'), $dtjwpt_email_update_user->data->display_name, $dtjwpt_email_link, $dtjwpt_get_ticket->ticket_id)
								);

								// Send our email off
								dtjwpt_send_mail($dtjwpt_email_args);

							}

						}

					}

				}

			}

		}

	}

	// Return a response to the request
	echo $dtjwpt_update_ticket_value;
	wp_die();

}
add_action('wp_ajax_dtjwpt_update_ticket_ajax', 'dtjwpt_update_ticket_ajax');

/**
 * Function which is called when posting a new note.
 * Accepts zero arguments.
 * 
 * @since 1.0
 * @return string Response returned to the Ajax request.
 */
function dtjwpt_create_note_ajax() {

	global $wpdb;

	// Set the response value now before anything else
	$dtjwpt_create_note_value = "0";

	// Get the nonce check so we can make sure it's a legit request
	$dtjwpt_nonce_check = check_ajax_referer('dtjwpt_create_note_nonce', 'safety', false);

	// Get the data sent from the form
	$dtjwpt_create_note_ticket = sanitize_text_field($_REQUEST['ticket']);
	$dtjwpt_create_note_comment = trim(sanitize_textarea_field($_REQUEST['note']));
	$dtjwpt_create_note_alter_status = sanitize_text_field($_REQUEST['alter']);

	// Convert the number based values to integers
	$dtjwpt_create_note_alter_status = intval($dtjwpt_create_note_alter_status);

	// Check if the nonce passed the check
	if ( $dtjwpt_nonce_check == true ) {

		// Check whether the user can post notes
		if ( dtjwpt_can_modify_note() ) {

			// Check that the ticket exists
			if ( dtjwpt_get_ticket($dtjwpt_create_note_ticket) !== NULL ) {

				// Get the project id relating to said ticket
				$dtjwpt_create_note_project = dtjwpt_get_ticket($dtjwpt_create_note_ticket);

				// Get the id of the project from the ticket object
				$dtjwpt_create_note_project_id = $dtjwpt_create_note_project->project_id;

				// Check that the note has some content in it
				if ( strlen($dtjwpt_create_note_comment) >= 1 ) {

					// The note can be posted, get it done
					$dtjwpt_create_note_value = "1";

					// Create the note within the database
					$wpdb->insert(
						$wpdb->prefix . DTJWPT_DB_NOTES, 
						array(
							'ticket_id'		=> $dtjwpt_create_note_ticket,
							'project_id'	=> $dtjwpt_create_note_project_id,
							'author_id'		=> get_current_user_id(),
							'content'		=> $dtjwpt_create_note_comment,
							'timestamp'		=> date('Y-m-d H:i:s')
						)
					);

					// Now check if we need to alter the ticket status
					if ( $dtjwpt_create_note_alter_status == '1' ) {

						// Get the ticket data for this note
						$dtjwpt_get_note_ticket = dtjwpt_get_ticket($dtjwpt_create_note_ticket);

						// Check whether it's open or closed and set the new value
						if ( $dtjwpt_get_note_ticket->status == '1' ) {

							$dtjwpt_alter_ticket_status = '0';

						} else {

							$dtjwpt_alter_ticket_status = '1';

						}

						// Update the ticket with the new status value
						$wpdb->update(
							$wpdb->prefix . DTJWPT_DB_TICKETS, 
							array(
								'status'	=> $dtjwpt_alter_ticket_status
							),
							array(
								'ticket_id' => $dtjwpt_create_note_ticket
							)
						);

					}

					// Set the default for sending the email
					$dtjwpt_send_the_email = false;

					// Get the data objects for the project and ticket
					$dtjwpt_get_ticket = dtjwpt_get_ticket($dtjwpt_create_note_ticket);
					$dtjwpt_get_project = dtjwpt_get_project($dtjwpt_get_ticket->project_id);

					// Get certain data that we want to use within the body text
					$dtjwpt_email_note_user = get_userdata(get_current_user_id());
					$dtjwpt_email_link = admin_url('admin.php?page=dtjwpt_triage&triage=tickets&ticket_id=' . $dtjwpt_create_note_ticket);

					// Check that someone is assigned to the ticket
					if ( $dtjwpt_get_ticket->assignee_id != 0 ) {

						// Get the ticket assignee email address
						$dtjwpt_send_to_user = get_userdata($dtjwpt_get_ticket->assignee_id);

						// Send the email
						$dtjwpt_send_the_email = true;

					} else {

						// No one is assigned, check the project owner
						if ( $dtjwpt_get_project->owner_id != 0 ) {

							// Get the project owner email address
							$dtjwpt_send_to_user = get_userdata($dtjwpt_get_project->owner_id);

							// Send the email
							$dtjwpt_send_the_email = true;

						}

					}

					// Continue if we're allowed to send the email
					if ( $dtjwpt_send_the_email ) {

						// Create the email arguments to send
						$dtjwpt_email_args = array(
							'to' => $dtjwpt_send_to_user->data->user_email,
							'subject' => __('New Note Posted', 'wp-triage'),
							'body' => sprintf(__('<p>Hello!</p><p>A new note has been posted by <strong>%s</strong> to the following ticket.</p><p><a href="%s">Read the notes for ticket #%s</a>.</p><p>Sent from WordPress via WP Triage.</p>', 'wp-triage'), $dtjwpt_email_note_user->data->display_name, $dtjwpt_email_link, $dtjwpt_get_ticket->ticket_id)
						);

						// Send our email off
						dtjwpt_send_mail($dtjwpt_email_args);

					}

				}

			}

		}

	}

	// Return a response to the request
	echo $dtjwpt_create_note_value;
	wp_die();

}
add_action('wp_ajax_dtjwpt_create_note_ajax', 'dtjwpt_create_note_ajax');

/**
 * Function which is called when deleting an existing note.
 * Accepts zero arguments.
 * 
 * @since 1.0
 * @return string Response returned to the Ajax request.
 */
function dtjwpt_delete_note_ajax() {

	global $wpdb;

	// Set the response code to the default value
	$dtjwpt_delete_note_value = "0";

	// Check the nonce request to make sure it's valid
	$dtjwpt_nonce_check = check_ajax_referer('dtjwpt_delete_note_nonce', 'safety', false);

	// Get the data sent from the form
	$dtjwpt_delete_note_id = sanitize_text_field($_REQUEST['note']);

	// Check the nonce to make sure it passes the validation
	if ( $dtjwpt_nonce_check == true ) {

		// Make sure the current user can modify this ticket
		if ( dtjwpt_can_modify_note() ) {

			// Make sure the ticket exists in the database and isn't fake
			if ( dtjwpt_get_ticket($dtjwpt_delete_note_id) !== NULL ) {

				// The note exists and can be deleted
				$dtjwpt_delete_note_value = "1";

				// Delete the note from the database
				$wpdb->query(
					$wpdb->prepare(
						'DELETE FROM ' . $wpdb->prefix . DTJWPT_DB_NOTES . ' WHERE note_id = %s LIMIT 1', $dtjwpt_delete_note_id
					)
				);

			}

		}

	}

	// Return a response to the request
	echo $dtjwpt_delete_note_value;
	wp_die();

}
add_action('wp_ajax_dtjwpt_delete_note_ajax', 'dtjwpt_delete_note_ajax');

