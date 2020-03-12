<?php

/**
 * core.php
 */

// No thank you
if ( ! defined('ABSPATH') ) die;

/**
 * Function which loads the plugin text domain.
 * 
 * @since 1.0
 * @return void
 */
function dtjwpt_load_text_domain() {

	// Load the plugin text domain for language localisation
	load_plugin_textdomain('wp-triage', false, DTJWPT_LANGUAGE);

}
add_action('plugins_loaded', 'dtjwpt_load_text_domain', 10);

/**
 * Function that loads plugin stylesheets and scripts.
 * 
 * @since 1.0
 * @return void
 */
function dtjwpt_plugin_assets() {

	// Register the plugin stylesheets
	wp_register_style("dtjwpt_admin_css", plugins_url('wp-triage', 'wp-triage') . "/assets/css/style.css", array(), DTJWPT_VERSION);
	wp_register_script('dtjwpt_admin_js', plugins_url('wp-triage', 'wp-triage') . "/assets/js/ajax.js", array('jquery'), DTJWPT_VERSION, true);

	// Check the user is logged in first
	if ( is_user_logged_in() ) {

		// Enqueue the stylesheet and load it
		wp_enqueue_style("dtjwpt_admin_css");

	}

	// Localise the ajax script
	wp_localize_script(
		'dtjwpt_admin_js',
		'DTJWPT_AJAX',
		array(
			'admin_ajax' => admin_url('admin-ajax.php'),
			'confirm_delete_note' => __('Are you sure you want to delete this note?', 'wp-triage'),
			'confirm_upgrade_db' => __('Are you sure you want to upgrade the database?', 'wp-triage')
		)
	);

	// Enqueue only if we're in the dashboard
	if ( is_admin() ) {

		// Enqueue our JavaScript file
		wp_enqueue_script('dtjwpt_admin_js');

	}

}
add_action('admin_enqueue_scripts', 'dtjwpt_plugin_assets', 10);
add_action('wp_enqueue_scripts', 'dtjwpt_plugin_assets', 10);

// Get the current version of the plugin database
$dtjwpt_db_version = get_option('dtjwpt_database_version');

// Check if the current version matches the plugins version
if ( DTJWPT_DB_VERSION != $dtjwpt_db_version ) {

	/**
	 * Function that adds an upgrade warning to the admin notices.
	 * 
	 * @since 1.0
	 * @return void
	 */
	function dtjwpt_database_upgrade_warning() {

		// Create a nonce for the form when it's submitted
		$dtjwpt_upgrade_db_nonce = wp_create_nonce('dtjwpt_upgrade_db_nonce');

		?>
		
			<div class="notice notice-error dtjwpt-notice-upgrade-warning">
				<form method="post" style="display: none;">
					<input type="hidden" name="dtjwpt_upgrade_db_nonce" class="dtjwpt_upgrade_db_nonce" value="<?php echo $dtjwpt_upgrade_db_nonce; ?>" />
				</form>
				<p><strong><?php _e('A database upgrade is required for WP Triage. Please backup your database before running the upgrade!', 'wp-triage'); ?></strong></p>
				<p><?php printf( _x('The database upgrade will update the plugin database tables to the latest version. You may experience issues with the plugin until you %1$supgrade the database%2$s.', 'Placeholders represent anchor link tags', 'wp-triage'), '<a href="#" class="dtjwpt-upgrade-db-link">', '</a>' ); ?></p>
				<span class="spinner dtjwpt-db-upgrade-spinner"></span>
			</div>

			<div class="notice notice-success is-dismissible dtjwpt-notice-upgrade-success" style="display: none;">
				<p><strong><?php _e('The database was upgraded successfully!', 'wp-triage'); ?></strong></p>
			</div>

		<?php

	}
	add_action('admin_notices', 'dtjwpt_database_upgrade_warning', 10);

}

/**
 * Function that adds links to the plugin list page.
 * 
 * @since 1.0
 * @return string Returns a string of HTML links.
 */
function dtjwpt_add_plugin_links($links, $file) {

	// Check if this is the current file
	if ( $file != DTJWPT_BASENAME ) {

		// Nope, not this time
		return $links;

	}

	// Build the link that we want to show in the list
	$dtjwpt_settings_link = '<a href="' . menu_page_url('dtjwpt_settings', false) . '">' . __('Settings', 'wp-triage') . '</a>';

	// Add the link to the array of links
	array_unshift($links, $dtjwpt_settings_link);

	// Return the links
	return $links;

}
add_filter('plugin_action_links', 'dtjwpt_add_plugin_links', 10, 2);

// Check the setting to see if we need to show the toolbar links
if ( get_option('dtjwpt_admin_toolbar_link') == "on" ) {

	/**
	 * Function used as a delay for another hook
	 * 
	 * @since 1.0
	 * @return void
	 */
	function dtjwpt_delay_function_hook() {

		// Check the current user can read (is a user)
		if ( current_user_can('read') ) {

			/**
			 * Function which adds links to the admin toolbar.
			 * Accepts zero arguments.
			 * Creates links to go in the admin toolbar.
			 */
			function dtjwpt_add_toolbar_link($wp_admin_bar) {

				global $wp_admin_bar;

				// Setup the link to be added to the toolbar
				$dtjwpt_triage_link = array(
					'id'	=> 'dtjwpt_triage_link',
					'title'	=> __('Triage', 'wp-triage'),
					'href'	=> admin_url('admin.php?page=dtjwpt_triage'),
					'meta'	=> array(
						'class'	=> 'dtjwpt_triage_link'
					)
				);

				// Add the parent link to the toolbar
				$wp_admin_bar->add_node($dtjwpt_triage_link);

				// Create the arguments for our extra links
				$dtjwpt_projects_link = array(
					'id'		=> 'dtjwpt_projects_link',
					'title'		=> __('Projects', 'wp-triage'),
					'parent'	=> 'dtjwpt_triage_link',
					'href'		=> admin_url('admin.php?page=dtjwpt_triage'),
					'meta'		=> array(
						'class'	=> 'dtjwpt_projects_link'
					)
				);

				$dtjwpt_components_link = array(
					'id'		=> 'dtjwpt_components_link',
					'title'		=> __('Components', 'wp-triage'),
					'parent'	=> 'dtjwpt_triage_link',
					'href'		=> admin_url('admin.php?page=dtjwpt_components'),
					'meta'		=> array(
						'class'	=> 'dtjwpt_components_link'
					)
				);

				$dtjwpt_roles_link = array(
					'id'		=> 'dtjwpt_roles_link',
					'title'		=> __('Roles', 'wp-triage'),
					'parent'	=> 'dtjwpt_triage_link',
					'href'		=> admin_url('admin.php?page=dtjwpt_roles'),
					'meta'		=> array(
						'class'	=> 'dtjwpt_roles_link'
					)
				);

				$dtjwpt_settings_link = array(
					'id'		=> 'dtjwpt_settings_link',
					'title'		=> __('Settings', 'wp-triage'),
					'parent'	=> 'dtjwpt_triage_link',
					'href'		=> admin_url('admin.php?page=dtjwpt_settings'),
					'meta'		=> array(
						'class'	=> 'dtjwpt_settings_link'
					)
				);

				// Check the current user can see projects & tickets
				if ( current_user_can('dtjwpt_manage_tickets') ) {
					$wp_admin_bar->add_node($dtjwpt_projects_link);
				}

				// Check the current user can manage components
				if ( current_user_can('dtjwpt_manage_components') ) {
					$wp_admin_bar->add_node($dtjwpt_components_link);
				}
				
				// Check that the current user is able to manage options
				if ( current_user_can('manage_options') ) {

					$wp_admin_bar->add_node($dtjwpt_roles_link);
					$wp_admin_bar->add_node($dtjwpt_settings_link);

				}

			}
			add_action('admin_bar_menu', 'dtjwpt_add_toolbar_link', 101);

		}

	}
	add_action('plugins_loaded', 'dtjwpt_delay_function_hook', 10);

}

/**
 * Function that adds menu items/page to the WordPress admin.
 * 
 * @since 1.0
 * @return void
 */
function dtjwpt_admin_pages() {

	// Setup the parent menu page
	add_menu_page(
		__('Triage', 'wp-triage'),
		__('Triage', 'wp-triage'),
		DTJWPT_CAP_MANAGE_TICKETS,
		'dtjwpt_triage',
		false,
		'dashicons-sos',
		2700
	);

	// Add the tickets link
	add_submenu_page(
		'dtjwpt_triage',
		__('Projects', 'wp-triage'),
		__('Projects', 'wp-triage'),
		'manage_options',
		'dtjwpt_triage',
		'dtjwpt_triage_template'
	);

	// Add the components link
	add_submenu_page(
		'dtjwpt_triage',
		__('Components', 'wp-triage'),
		__('Components', 'wp-triage'),
		DTJWPT_CAP_MANAGE_COMPONENTS,
		'dtjwpt_components',
		'dtjwpt_components_template'
	);

	// Add the roles link
	add_submenu_page(
		'dtjwpt_triage',
		__('Roles', 'wp-triage'),
		__('Roles', 'wp-triage'),
		'manage_options',
		'dtjwpt_roles',
		'dtjwpt_roles_template'
	);

	// Add the settings link
	add_submenu_page(
		'dtjwpt_triage',
		__('Settings', 'wp-triage'),
		__('Settings', 'wp-triage'),
		'manage_options',
		'dtjwpt_settings',
		'dtjwpt_settings_template'
	);

}
add_action('admin_menu', 'dtjwpt_admin_pages', 10);

/**
 * Function callbacks for page specific templates.
 * 
 * @since 1.0
 * @return mixed Returns a HTML template.
 */
function dtjwpt_triage_template() {
	require_once(DTJWPT_TEMPLATES . "triage.php");
}

function dtjwpt_components_template() {
	require_once(DTJWPT_TEMPLATES . "components.php");
}

function dtjwpt_settings_template() {
	require_once(DTJWPT_TEMPLATES . "settings.php");
}

function dtjwpt_roles_template() {
	require_once(DTJWPT_TEMPLATES . "roles.php");
}

/**
 * Function to register core settings fields.
 * 
 * @since 1.0
 * @return void
 */
function dtjwpt_register_core_settings() {

	// Create our main settings section
	add_settings_section(
		'dtjwpt_core_settings_section',
		'',
		false,
		'dtjwpt_settings_group'
	);

	// Create each of our plugin settings
	add_settings_field(
		'dtjwpt_items_per_page',
		__('Items Per Page', 'wp-triage'),
		'dtjwpt_items_per_page_callback',
		'dtjwpt_settings_group',
		'dtjwpt_core_settings_section',
		array(
			'dtjwpt_items_per_page'
		)
	);

	add_settings_field(
		'dtjwpt_admin_toolbar_link',
		__('Show Toolbar Links', 'wp-triage'),
		'dtjwpt_admin_toolbar_link_callback',
		'dtjwpt_settings_group',
		'dtjwpt_core_settings_section',
		array(
			'dtjwpt_admin_toolbar_link'
		)
	);

	add_settings_field(
		'dtjwpt_notify_by_email',
		__('Notify Via Email', 'wp-triage'),
		'dtjwpt_notify_by_email_callback',
		'dtjwpt_settings_group',
		'dtjwpt_core_settings_section',
		array(
			'dtjwpt_notify_by_email'
		)
	);

	add_settings_field(
		'dtjwpt_hide_closed_tickets',
		__('Hide Closed Tickets', 'wp-triage'),
		'dtjwpt_hide_closed_tickets_callback',
		'dtjwpt_settings_group',
		'dtjwpt_core_settings_section',
		array(
			'dtjwpt_hide_closed_tickets'
		)
	);

	add_settings_field(
		'dtjwpt_auto_assign_owners',
		__('Auto Assign Assignees', 'wp-triage'),
		'dtjwpt_auto_assign_owners_callback',
		'dtjwpt_settings_group',
		'dtjwpt_core_settings_section',
		array(
			'dtjwpt_auto_assign_owners'
		)
	);

	add_settings_field(
		'dtjwpt_donate_upsell',
		__('Hide Donation Box', 'wp-triage'),
		'dtjwpt_donate_upsell_field_callback',
		'dtjwpt_settings_group',
		'dtjwpt_core_settings_section',
		array(
			'dtjwpt_donate_upsell'
		)
	);

	add_settings_field(
		'dtjwpt_uninstall_remember',
		__('Keep Data on Uninstall', 'wp-triage'),
		'dtjwpt_uninstall_remember_callback',
		'dtjwpt_settings_group',
		'dtjwpt_core_settings_section',
		array(
			'dtjwpt_uninstall_remember'
		)
	);

	// Register our core settings so the data is saved properly
	register_setting('dtjwpt_settings_fields', 'dtjwpt_items_per_page', 'esc_attr');
	register_setting('dtjwpt_settings_fields', 'dtjwpt_admin_toolbar_link', 'esc_attr');
	register_setting('dtjwpt_settings_fields', 'dtjwpt_notify_by_email', 'esc_attr');
	register_setting('dtjwpt_settings_fields', 'dtjwpt_hide_closed_tickets', 'esc_attr');
	register_setting('dtjwpt_settings_fields', 'dtjwpt_auto_assign_owners', 'esc_attr');
	register_setting('dtjwpt_settings_fields', 'dtjwpt_donate_upsell', 'esc_attr');
	register_setting('dtjwpt_settings_fields', 'dtjwpt_uninstall_remember', 'esc_attr');

}
add_action('admin_init', 'dtjwpt_register_core_settings', 10);

/**
 * Function that outputs the HTML for the settings.
 * 
 * @since 1.0
 * @return string Returns the HTML for the specified settings.
 */
function dtjwpt_items_per_page_callback($args) {

	echo '<p><input type="number" class="dtjwpt-number ' . $args[0] . '" id="' . $args[0] . '" name="' . $args[0] . '" value="' . get_option($args[0], 'wp-triage') . '" aria-describedby="" aria-describedby="description-' . $args[0] . '" /></p>';
	echo '<p class="description" id="description-' . $args[0] . '">' . __('Choose how many projects and tickets are show in the list tables per page. The default value is <code>20</code>.', 'wp-triage') . '</p>';

}

function dtjwpt_admin_toolbar_link_callback($args) {

	if ( get_option($args[0]) == "on" ) {
		echo '<p><input type="checkbox" class="dtjwpt-checkbox ' . $args[0] . '" id="' . $args[0] . '" name="' . $args[0] . '" aria-describedby="description-' . $args[0] . '" checked="checked" /></p>';
	} else {
		echo '<p><input type="checkbox" class="dtjwpt-checkbox ' . $args[0] . '" id="' . $args[0] . '" name="' . $args[0] . '" aria-describedby="description-' . $args[0] . '" /></p>';
	}
	echo '<p class="description" id="description-' . $args[0] . '">' . __('Choose whether you would like to show a link within the admin toolbar to go to projects and tickets quickly.', 'wp-triage') . '</p>';

}

function dtjwpt_notify_by_email_callback($args) {

	if ( get_option($args[0]) == "on" ) {
		echo '<p><input type="checkbox" class="dtjwpt-checkbox ' . $args[0] . '" id="' . $args[0] . '" name="' . $args[0] . '" aria-describedby="description-' . $args[0] . '" checked="checked" /></p>';
	} else {
		echo '<p><input type="checkbox" class="dtjwpt-checkbox ' . $args[0] . '" id="' . $args[0] . '" name="' . $args[0] . '" aria-describedby="description-' . $args[0] . '" /></p>';
	}
	echo '<p class="description" id="description-' . $args[0] . '">' . __('You can choose whether or not you would like an email to be sent when projects or tickets are altered by someone.', 'wp-triage') . '</p>';

}

function dtjwpt_hide_closed_tickets_callback($args) {

	if ( get_option($args[0]) == "on" ) {
		echo '<p><input type="checkbox" class="dtjwpt-checkbox ' . $args[0] . '" id="' . $args[0] . '" name="' . $args[0] . '" aria-describedby="description-' . $args[0] . '" checked="checked" /></p>';
	} else {
		echo '<p><input type="checkbox" class="dtjwpt-checkbox ' . $args[0] . '" id="' . $args[0] . '" name="' . $args[0] . '" aria-describedby="description-' . $args[0] . '" /></p>';
	}
	echo '<p class="description" id="description-' . $args[0] . '">' . __('An option that lets you hide tickets which are marked as closed. The tickets will still be accessible to anyone with the correct capabilities via the tickets link.', 'wp-triage') . '</p>';

}

function dtjwpt_auto_assign_owners_callback($args) {

	if ( get_option($args[0]) == "on" ) {
		echo '<p><input type="checkbox" class="dtjwpt-checkbox ' . $args[0] . '" id="' . $args[0] . '" name="' . $args[0] . '" aria-describedby="description-' . $args[0] . '" checked="checked" /></p>';
	} else {
		echo '<p><input type="checkbox" class="dtjwpt-checkbox ' . $args[0] . '" id="' . $args[0] . '" name="' . $args[0] . '" aria-describedby="description-' . $args[0] . '" /></p>';
	}
	echo '<p class="description" id="description-' . $args[0] . '">' . __('When checked, project owners will be auto assigned to tickets in their respective projects when first created if the ticket is left as unassigned on creation.', 'wp-triage') . '</p>';

}

function dtjwpt_donate_upsell_field_callback($args) {

	if ( get_option($args[0]) == "on" ) {
		echo '<p><input type="checkbox" class="dtjwpt-checkbox ' . $args[0] . '" id="' . $args[0] . '" name="' . $args[0] . '" aria-describedby="description-' . $args[0] . '" checked="checked" /></p>';
	} else {
		echo '<p><input type="checkbox" class="dtjwpt-checkbox ' . $args[0] . '" id="' . $args[0] . '" name="' . $args[0] . '" aria-describedby="description-' . $args[0] . '" /></p>';
	}
	echo '<p class="description" id="description-' . $args[0] . '">' . __('Hide the donation box if you&#39;ve already donated or don&#39;t want to see it.', 'wp-triage') . '</p>';

}

function dtjwpt_uninstall_remember_callback($args) {

	if ( get_option($args[0]) == "on" ) {
		echo '<p><input type="checkbox" class="dtjwpt-checkbox ' . $args[0] . '" id="' . $args[0] . '" name="' . $args[0] . '" aria-describedby="description-' . $args[0] . '" checked="checked" /></p>';
	} else {
		echo '<p><input type="checkbox" class="dtjwpt-checkbox ' . $args[0] . '" id="' . $args[0] . '" name="' . $args[0] . '" aria-describedby="description-' . $args[0] . '" /></p>';
	}
	echo '<p class="description" id="description-' . $args[0] . '">' . __('Whether you would like to keep your project and ticket data if you decide to uninstall the plugin.', 'wp-triage') . '</p>';

}

/**
 * Function that sends an email to a user.
 * 
 * @since 1.0
 * @return string Returns a value for the Ajax request.
 */
function dtjwpt_send_mail($dtjwpt_mail = array()) {

	// Set the mail value default
	$dtjwpt_mail_value = '0';

	// Get the plugin send email option
	$dtjwpt_should_send_mail = get_option('dtjwpt_notify_by_email');

	/**
	 * The following fields are required from the array
	 * 1. To address ('to')
	 * 2. Subject ('subject')
	 * 2. Message ('body')
	 * 
	 * The from address is taken from the admin email address
	 * and the headers are automatically set on send. The function is
	 * skipped if the send emails setting is turned off.
	 */
	if ( $dtjwpt_should_send_mail == 'on' ) {

		// Make sure the array is not empty
		if ( ! empty($dtjwpt_mail) ) {

			// Check that the email to send to is a valid address
			if ( filter_var($dtjwpt_mail['to'], FILTER_VALIDATE_EMAIL) ) {

				// Check the subject line is at least one character
				if ( strlen($dtjwpt_mail['subject']) >= 1 ) {

					// Check the message body is at least a character long too
					if ( strlen($dtjwpt_mail['body']) >= 1 ) {

						// The email passed the checks, return a positive value for the Ajax request
						$dtjwpt_mail_value = '1';

						// Add a prefix to the email subject so we know it's from WordPress
						$dtjwpt_mail['subject'] = '[' . get_bloginfo('name') . ']' . ' ' . $dtjwpt_mail['subject'];

						// Build the mail headers here before we send the email
						$dtjwpt_mail['headers'] = 'Content-Type: text/html; charset=UTF-8';

						// Send the email to the user
						wp_mail($dtjwpt_mail['to'], $dtjwpt_mail['subject'], $dtjwpt_mail['body'], $dtjwpt_mail['headers']);

					}

				}

			}

		}

	}

	// Return a value for output
	return $dtjwpt_mail_value;

}

/**
 * Function that returns project data.
 * Returns all projects if the id is 0 or blank.
 * 
 * @since 1.0
 * @return object Returns an object of data for a project.
 */
function dtjwpt_get_project($dtjwpt_project_id = 0) {

	global $wpdb;

	// Get the project id and check it is valid
	if ( $dtjwpt_project_id == 0 || $dtjwpt_project_id == '' || $dtjwpt_project_id == NULL ) {

		// Count how many projects we have
		$dtjwpt_get_project = $wpdb->get_var(
			'SELECT COUNT(*) FROM ' . $wpdb->prefix . DTJWPT_DB_PROJECTS
		);

		// Check that we have at least one project to return
		if ( $dtjwpt_get_project >= 1 ) {

			// Get all of the projects we have stored
			$dtjwpt_project_data = $wpdb->get_results(
				'SELECT * FROM ' . $wpdb->prefix . DTJWPT_DB_PROJECTS
			);

		} else {

			// We have no projects, return an empty array
			$dtjwpt_project_data = array();

		}

	} else {

		// Check the project exists in the database
		$dtjwpt_get_project = $wpdb->get_var(
			$wpdb->prepare(
				'SELECT COUNT(*) FROM ' . $wpdb->prefix . DTJWPT_DB_PROJECTS . ' WHERE project_id = %s', $dtjwpt_project_id
			)
		);

		// Check that it returned one row exactly (it exists)
		if ( $dtjwpt_get_project == 1 ) {

			// Get the project data from the database
			$dtjwpt_the_project = $wpdb->get_results(
				$wpdb->prepare(
					'SELECT * FROM ' . $wpdb->prefix . DTJWPT_DB_PROJECTS . ' WHERE project_id = %s', $dtjwpt_project_id
				)
			);

			// Get the project data and pass it into our result variable
			$dtjwpt_project_data = $dtjwpt_the_project[0];

		} else {

			// Doesn't exist, return false
			$dtjwpt_project_data = false;

		}

	}

	// Return our project object data
	return $dtjwpt_project_data;

}

/**
 * Function that returns component data.
 * Returns all components if the id is 0 or blank.
 * 
 * @since 1.2
 * @return object Returns an object of data for a component.
 */
function dtjwpt_get_component($dtjwpt_component_id = 0) {

	global $wpdb;

	// Get the component id and make sure it's valid
	if ( $dtjwpt_component_id == 0 || $dtjwpt_component_id == '' || $dtjwpt_component_id == NULL ) {

		// Count how many components we have
		$dtjwpt_get_component = $wpdb->get_var(
			'SELECT COUNT(*) FROM ' . $wpdb->prefix . DTJWPT_DB_COMPONENTS
		);

		// Check that we have at least one component to return
		if ( $dtjwpt_get_component >= 1 ) {

			// Get all of the components we have stored
			$dtjwpt_component_data = $wpdb->get_results(
				'SELECT * FROM ' . $wpdb->prefix . DTJWPT_DB_COMPONENTS
			);

		} else {

			// We have no components, return an empty array
			$dtjwpt_component_data = array();

		}

	} else {

		// Make sure the component exists in the database
		$dtjwpt_get_component = $wpdb->get_var(
			$wpdb->prepare(
				'SELECT COUNT(*) FROM ' . $wpdb->prefix . DTJWPT_DB_COMPONENTS . ' WHERE component_id = %s', $dtjwpt_component_id
			)
		);

		// Check that it returned just the one row (it exists)
		if ( $dtjwpt_get_component == 1 ) {

			// Get the component data from the database
			$dtjwpt_the_component = $wpdb->get_results(
				$wpdb->prepare(
					'SELECT * FROM ' . $wpdb->prefix . DTJWPT_DB_COMPONENTS . ' WHERE component_id = %s', $dtjwpt_component_id
				)
			);

			// Get the component data and pass it into our result variable
			$dtjwpt_component_data = $dtjwpt_the_component[0];

		} else {

			// Doesn't exist, return false
			$dtjwpt_component_data = false;

		}

	}

	// Return the component data
	return $dtjwpt_component_data;

}

/**
 * Function that returns ticket data.
 * Returns all tickets if the id is 0 or blank.
 * 
 * @since 1.0
 * @return object Returns an object of data for a ticket.
 */
function dtjwpt_get_ticket($dtjwpt_ticket_id = 0) {

	global $wpdb;

	// Get the ticket id and make sure it's valid
	if ( $dtjwpt_ticket_id == 0 || $dtjwpt_ticket_id == '' || $dtjwpt_ticket_id == NULL ) {

		// Count how many tickets we have
		$dtjwpt_get_ticket = $wpdb->get_var(
			'SELECT COUNT(*) FROM ' . $wpdb->prefix . DTJWPT_DB_TICKETS
		);

		// Check that we have at least one ticket to return
		if ( $dtjwpt_get_ticket >= 1 ) {

			// Get all of the tickets we have stored
			$dtjwpt_ticket_data = $wpdb->get_results(
				'SELECT * FROM ' . $wpdb->prefix . DTJWPT_DB_TICKETS
			);

		} else {

			// We have no tickets, return an empty array
			$dtjwpt_ticket_data = array();

		}

	} else {

		// Make sure the ticket exists in the database
		$dtjwpt_get_ticket = $wpdb->get_var(
			$wpdb->prepare(
				'SELECT COUNT(*) FROM ' . $wpdb->prefix . DTJWPT_DB_TICKETS . ' WHERE ticket_id = %s', $dtjwpt_ticket_id
			)
		);

		// Check that it returned just the one row (it exists)
		if ( $dtjwpt_get_ticket == 1 ) {

			// Get the ticket data from the database
			$dtjwpt_the_ticket = $wpdb->get_results(
				$wpdb->prepare(
					'SELECT * FROM ' . $wpdb->prefix . DTJWPT_DB_TICKETS . ' WHERE ticket_id = %s', $dtjwpt_ticket_id
				)
			);

			// Get the ticket data and pass it into our result variable
			$dtjwpt_ticket_data = $dtjwpt_the_ticket[0];

		} else {

			// Doesn't exist, return false
			$dtjwpt_ticket_data = false;

		}

	}

	// Return the ticket data
	return $dtjwpt_ticket_data;

}

/**
 * Function that returns note data.
 * Returns all notes if the id is 0 or blank.
 * 
 * @since 1.0
 * @return object Returns an object of data for a note.
 */
function dtjwpt_get_note($dtjwpt_note_id = 0) {

	global $wpdb;

	// Check the note id is valid
	if ( $dtjwpt_note_id == 0 || $dtjwpt_note_id == '' || $dtjwpt_note_id == NULL ) {

		// Count how many notes we have
		$dtjwpt_get_note = $wpdb->get_var(
			'SELECT COUNT(*) FROM ' . $wpdb->prefix . DTJWPT_DB_NOTES
		);

		// Check that we have at least one note to return
		if ( $dtjwpt_get_note >= 1 ) {

			// Get all of the notes we have stored
			$dtjwpt_note_data = $wpdb->get_results(
				'SELECT * FROM ' . $wpdb->prefix . DTJWPT_DB_NOTES
			);

		} else {

			// We have no notes, return an empty array
			$dtjwpt_note_data = array();

		}

	} else {

		// Check the note exists in the database
		$dtjwpt_get_note = $wpdb->get_var(
			$wpdb->prepare(
				'SELECT COUNT(*) FROM ' . $wpdb->prefix . DTJWPT_DB_NOTES . ' WHERE note_id = %s', $dtjwpt_note_id
			)
		);

		// Check that it actually exists
		if ( $dtjwpt_get_note == 1 ) {

			// Get the note data from the database
			$dtjwpt_the_note = $wpdb->get_results(
				$wpdb->prepare(
					'SELECT * FROM ' . $wpdb->prefix . DTJWPT_DB_NOTES . ' WHERE note_id = %s', $dtjwpt_note_id
				)
			);

			// Get the note data and pass it into our result variable
			$dtjwpt_note_data = $dtjwpt_the_note[0];

		} else {

			// Doesn't exist, return false
			$dtjwpt_note_data = false;

		}

	}

	// Return the note object
	return $dtjwpt_note_data;

}

/**
 * Function which returns a note object dump based on a ticket id.
 * 
 * @since 1.0
 * @return object Returns an object of data for a note.
 */
function dtjwpt_get_ticket_notes($dtjwpt_ticket_id = 0) {

	global $wpdb;

	// Make sure we've been given a valid id
	if ( $dtjwpt_ticket_id == 0 || $dtjwpt_ticket_id == '' || $dtjwpt_ticket_id == NULL ) {

		// Wasn't valid, just return false
		$dtjwpt_ticket_notes = false;

	} else {

		// Make sure the ticket we've been given exists
		$dtjwpt_get_notes = $wpdb->get_var(
			$wpdb->prepare(
				'SELECT COUNT(*) FROM ' . $wpdb->prefix . DTJWPT_DB_NOTES . ' WHERE ticket_id = %s', $dtjwpt_ticket_id
			)
		);

		// Make sure that we have some notes to grab too
		if ( $dtjwpt_get_notes >= 1 ) {

			// Get the note data from the database
			$dtjwpt_notes_data = $wpdb->get_results(
				$wpdb->prepare(
					'SELECT * FROM ' . $wpdb->prefix . DTJWPT_DB_NOTES . ' WHERE ticket_id = %s', $dtjwpt_ticket_id
				)
			);

			// Get the list of note ids and put them into an array
			$dtjwpt_ticket_notes = $dtjwpt_notes_data;

		} else {

			// Nothing found, return false
			$dtjwpt_ticket_notes = false;

		}

	}

	// Return the data of note ids
	return $dtjwpt_ticket_notes;

}

/**
 * Function that checks if a user can manage projects.
 * 
 * @since 1.0
 * @return boolean Returns either true or false based on capabilities.
 */
function dtjwpt_can_manage_projects() {

	// Simple check against the user role permissions
	if ( current_user_can(DTJWPT_CAP_MANAGE_PROJECTS) ) {

		// The user can manage projects
		return true;

	} else {

		// The user can't manage projects
		return false;

	}

}

/**
 * Function that checks if a user can manage a particular project.
 * 
 * @since 1.0
 * @return boolean Returns either true or false based on capabilities.
 */
function dtjwpt_can_modify_project($dtjwpt_project_id = 0) {

	// Simple check against the user role permissions
	if ( dtjwpt_can_manage_projects() ) {

		// Check the project exists
		if ( dtjwpt_get_project($dtjwpt_project_id) !== NULL ) {

			// Get the project data
			$dtjwpt_get_project = dtjwpt_get_project($dtjwpt_project_id);

			// Check that the user matches any of these (is admin or project owner)
			if ( current_user_can('manage_options') || $dtjwpt_get_project->owner_id == get_current_user_id() ) {

				// The user can manage this project
				return true;

			} else {

				// The user can't manage projects
				return false;

			}

		} else {

			// The user can't manage projects
			return false;

		}

	} else {

		// The user can't manage projects
		return false;

	}

}

/**
 * Function that checks if a user can manage components.
 * 
 * @since 1.2
 * @return boolean Returns either true or false based on capabilities.
 */
function dtjwpt_can_manage_components() {

	// Simple check against the user role permissions
	if ( current_user_can(DTJWPT_CAP_MANAGE_COMPONENTS) ) {

		// The user can manage components
		return true;

	} else {

		// The user can't manage components
		return false;

	}

}

/**
 * Function that checks if a user can manage tickets.
 * 
 * @since 1.0
 * @return boolean Returns either true or false based on capabilities.
 */
function dtjwpt_can_manage_tickets() {

	// Simple check against the user role permissions
	if ( current_user_can(DTJWPT_CAP_MANAGE_TICKETS) ) {

		// The user can manage tickets
		return true;

	} else {

		// The user can't manage tickets
		return false;

	}

}

/**
 * Function that checks if a user can manage a particular ticket.
 * 
 * @since 1.0
 * @return boolean Returns either true or false based on capabilities.
 */
function dtjwpt_can_modify_ticket($dtjwpt_ticket_id = 0) {

	// Simple check against the user role permissions
	if ( dtjwpt_can_manage_tickets() ) {

		// Check the ticket exists
		if ( dtjwpt_get_ticket($dtjwpt_ticket_id) !== NULL ) {

			// Get the ticket data
			$dtjwpt_get_ticket = dtjwpt_get_ticket($dtjwpt_ticket_id);

			// Check that the user matches any of these (is admin or ticket owner)
			if ( current_user_can('manage_options') || $dtjwpt_get_ticket->author_id == get_current_user_id() || $dtjwpt_get_ticket->assignee_id == get_current_user_id() ) {

				// The user can manage this ticket
				return true;

			} else {

				// The user can't manage tickets
				return false;

			}

		} else {

			// The user can't manage tickets
			return false;

		}

	} else {

		// The user can't manage tickets
		return false;

	}

}

/**
 * Function that checks if a user can manage notes.
 * 
 * @since 1.0
 * @return boolean Returns either true or false based on capabilities.
 */
function dtjwpt_can_manage_notes() {

	// Simple check against the user role permissions
	if ( current_user_can(DTJWPT_CAP_MANAGE_NOTES) ) {

		// The user can manage notes
		return true;

	} else {

		// The user can't manage notes
		return false;

	}

}

/**
 * Function that checks if a user can manage a particular note.
 * 
 * @since 1.0
 * @return boolean Returns either true or false based on capabilities.
 */
function dtjwpt_can_modify_note($note_id = 0) {

	// Check if the permissions if the user can manage notes
	if ( dtjwpt_can_manage_notes() ) {

		// Check the note exists
		if ( dtjwpt_get_note($note_id) !== NULL ) {

			// Get the note data
			$dtjwpt_get_note = dtjwpt_get_note($note_id);

			// Check that the user matches any of these (is admin or ticket owner)
			if ( current_user_can('manage_options') || $dtjwpt_get_note->author_id == get_current_user_id() ) {

				// The user can manage this note
				return true;

			} else {

				// The user can't manage notes
				return false;

			}

		} else {

			// The user can't manage notes
			return false;

		}

	} else {

		// The user can't manage notes
		return false;

	}

}

