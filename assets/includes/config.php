<?php

/**
 * config.php
 */

// No thank you
if ( ! defined('ABSPATH') ) die;

/**
 * Function that is run on plugin activation.
 * 
 * @since 1.0
 * @return null Does not return anything.
 */
function dtjwpt_plugin_install() {

	global $wpdb;

	// Find out the current database version
	$dtjwpt_db_version = get_option('dtjwpt_database_version');

	// Add the settings defaults to our installation
	add_option('dtjwpt_items_per_page', '20');
	add_option('dtjwpt_admin_toolbar_link', 'on');
	add_option('dtjwpt_notify_by_email', '');
	add_option('dtjwpt_hide_closed_tickets', '');
	add_option('dtjwpt_auto_assign_owners', 'on');
	add_option('dtjwpb_donate_upsell', '');
	add_option('dtjwpt_uninstall_remember', 'on');

	// Set defaults for the user capabilities (just admins to begin with)
	$dtjwpt_admin_role = get_role('administrator');
	$dtjwpt_admin_role->add_cap(DTJWPT_CAP_MANAGE_PROJECTS);
	$dtjwpt_admin_role->add_cap(DTJWPT_CAP_MANAGE_COMPONENTS);
	$dtjwpt_admin_role->add_cap(DTJWPT_CAP_MANAGE_TICKETS);
	$dtjwpt_admin_role->add_cap(DTJWPT_CAP_MANAGE_NOTES);

	// Check to see if the database exists or not
	if ( DTJWPT_DB_VERSION != $dtjwpt_db_version ) {

		// Set the table names that we need to add
		$dtjwpt_table_projects = $wpdb->prefix . DTJWPT_DB_PROJECTS;
		$dtjwpt_table_components = $wpdb->prefix . DTJWPT_DB_COMPONENTS;
		$dtjwpt_table_tickets = $wpdb->prefix . DTJWPT_DB_TICKETS;
		$dtjwpt_table_notes = $wpdb->prefix . DTJWPT_DB_NOTES;

		// Set the charset for the database
		$dtjwpt_charset = $wpdb->get_charset_collate();

		// Create the SQL queries for our databases
		$dtjwpt_sql_create_projects = "CREATE TABLE $dtjwpt_table_projects (
			project_id mediumint(9) NOT NULL AUTO_INCREMENT,
			owner_id mediumint(9) NOT NULL,
			name varchar(100) DEFAULT '' NOT NULL,
			PRIMARY KEY  (project_id)
		) $dtjwpt_charset;";

		$dtjwpt_sql_create_components = "CREATE TABLE $dtjwpt_table_components (
			component_id mediumint(9) NOT NULL AUTO_INCREMENT,
			name varchar(100) DEFAULT '' NOT NULL,
			create_date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			PRIMARY KEY  (component_id)
		) $dtjwpt_charset;";

		$dtjwpt_sql_create_tickets = "CREATE TABLE $dtjwpt_table_tickets (
			ticket_id mediumint(9) NOT NULL AUTO_INCREMENT,
			project_id mediumint(9) NOT NULL,
			component_id mediumint(9) NOT NULL,
			author_id mediumint(9) NOT NULL,
			assignee_id mediumint(9) NOT NULL,
			updater_id mediumint(9) NOT NULL,
			name varchar(250) DEFAULT '' NOT NULL,
			description varchar(5000) DEFAULT '' NOT NULL,
			type varchar(10) DEFAULT '' NOT NULL,
			status varchar(10) DEFAULT '' NOT NULL,
			priority varchar(10) DEFAULT '' NOT NULL,
			create_date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			update_date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			PRIMARY KEY  (ticket_id)
		) $dtjwpt_charset;";

		$dtjwpt_sql_create_notes = "CREATE TABLE $dtjwpt_table_notes (
			note_id mediumint(9) NOT NULL AUTO_INCREMENT,
			ticket_id mediumint(9) NOT NULL,
			project_id mediumint(9) NOT NULL,
			author_id mediumint(9) NOT NULL,
			content varchar(2500) DEFAULT '' NOT NULL,
			timestamp datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			PRIMARY KEY  (note_id)
		) $dtjwpt_charset;";

		// Include the database upgrade file
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

		// Run the queries
		dbDelta($dtjwpt_sql_create_projects);
		dbDelta($dtjwpt_sql_create_components);
		dbDelta($dtjwpt_sql_create_tickets);
		dbDelta($dtjwpt_sql_create_notes);

		// Add the plugin database version to the options table
		add_option('dtjwpt_database_version', DTJWPT_DB_VERSION);

	}

}
register_activation_hook(DTJWPT_URL, 'dtjwpt_plugin_install', 10, 0);

/**
 * Function that is run on plugin deactivation.
 * 
 * @since 1.0
 * @return null Does not return anything.
 */
function dtjwpt_plugin_uninstall() {

	global $wpdb;

	// Check if we need to keep the plugin data or not
	if ( get_option('dtjwpt_uninstall_remember') != "on" ) {

		// Create the queries for deleting the database tables
		$dtjwpt_sql_delete_projects = "DROP TABLE IF EXISTS " . $wpdb->prefix . DTJWPT_DB_PROJECTS;
		$dtjwpt_sql_delete_components = "DROP TABLE IF EXISTS " . $wpdb->prefix . DTJWPT_DB_COMPONENTS;
		$dtjwpt_sql_delete_tickets = "DROP TABLE IF EXISTS " . $wpdb->prefix . DTJWPT_DB_TICKETS;
		$dtjwpt_sql_delete_notes = "DROP TABLE IF EXISTS " . $wpdb->prefix . DTJWPT_DB_NOTES;

		// Delete all of the tables from the database
		$wpdb->query($dtjwpt_sql_delete_projects);
		$wpdb->query($dtjwpt_sql_delete_components);
		$wpdb->query($dtjwpt_sql_delete_tickets);
		$wpdb->query($dtjwpt_sql_delete_notes);

		// Delete all of the settings from the options table
		delete_option('dtjwpt_database_version');
		delete_option('dtjwpt_items_per_page');
		delete_option('dtjwpt_admin_toolbar_link');
		delete_option('dtjwpt_notify_by_email');
		delete_option('dtjwpt_hide_closed_tickets');
		delete_option('dtjwpt_auto_assign_owners');
		delete_option('dtjwpb_donate_upsell');
		delete_option('dtjwpt_uninstall_remember');

	}

}
register_deactivation_hook(DTJWPT_URL, 'dtjwpt_plugin_uninstall', 10, 0);

/**
 * Function which upgrades the database.
 * 
 * @since 1.0
 * @return string Returns response code to Ajax request.
 */
function dtjwpt_upgrade_plugin_db() {

	global $wpdb;

	// Grab the current stored database version
	$dtjwpt_db_version = get_option('dtjwpt_database_version');

	// See if the database versions match up
	if ( DTJWPT_DB_VERSION != $dtjwpt_db_version ) {

		// Set the database table names
		$dtjwpt_table_projects = $wpdb->prefix . DTJWPT_DB_PROJECTS;
		$dtjwpt_table_components = $wpdb->prefix . DTJWPT_DB_COMPONENTS;
		$dtjwpt_table_tickets = $wpdb->prefix . DTJWPT_DB_TICKETS;
		$dtjwpt_table_notes = $wpdb->prefix . DTJWPT_DB_NOTES;

		// Set the database character set
		$dtjwpt_charset = $wpdb->get_charset_collate();

		// Create the SQL queries for our databases
		$dtjwpt_sql_update_projects = "CREATE TABLE $dtjwpt_table_projects (
			project_id mediumint(9) NOT NULL AUTO_INCREMENT,
			owner_id mediumint(9) NOT NULL,
			name varchar(100) DEFAULT '' NOT NULL,
			PRIMARY KEY  (project_id)
		) $dtjwpt_charset;";

		$dtjwpt_sql_update_components = "CREATE TABLE $dtjwpt_table_components (
			component_id mediumint(9) NOT NULL AUTO_INCREMENT,
			name varchar(100) DEFAULT '' NOT NULL,
			create_date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			PRIMARY KEY  (component_id)
		) $dtjwpt_charset;";

		$dtjwpt_sql_update_tickets = "CREATE TABLE $dtjwpt_table_tickets (
			ticket_id mediumint(9) NOT NULL AUTO_INCREMENT,
			project_id mediumint(9) NOT NULL,
			component_id mediumint(9) NOT NULL,
			author_id mediumint(9) NOT NULL,
			assignee_id mediumint(9) NOT NULL,
			updater_id mediumint(9) NOT NULL,
			name varchar(250) DEFAULT '' NOT NULL,
			description varchar(5000) DEFAULT '' NOT NULL,
			type varchar(10) DEFAULT '' NOT NULL,
			status varchar(10) DEFAULT '' NOT NULL,
			priority varchar(10) DEFAULT '' NOT NULL,
			create_date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			update_date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			PRIMARY KEY  (ticket_id)
		) $dtjwpt_charset;";

		$dtjwpt_sql_update_notes = "CREATE TABLE $dtjwpt_table_notes (
			note_id mediumint(9) NOT NULL AUTO_INCREMENT,
			ticket_id mediumint(9) NOT NULL,
			project_id mediumint(9) NOT NULL,
			author_id mediumint(9) NOT NULL,
			content varchar(2500) DEFAULT '' NOT NULL,
			timestamp datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			PRIMARY KEY  (note_id)
		) $dtjwpt_charset;";

		// Include the database upgrade file
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

		// Run the queries
		dbDelta($dtjwpt_sql_update_projects);
		dbDelta($dtjwpt_sql_update_components);
		dbDelta($dtjwpt_sql_update_tickets);
		dbDelta($dtjwpt_sql_update_notes);

		// Update the new database version in the database
		update_option('dtjwpt_database_version', DTJWPT_DB_VERSION);

		// Return a value for the Ajax request
		$dtjwpt_upgrade_value = "1";

	} else {

		// The database versions match, don't do anything else
		$dtjwpt_upgrade_value = "0";

	}

	// We've finished the upgrade
	echo $dtjwpt_upgrade_value;
	wp_die();

}
add_action('wp_ajax_dtjwpt_upgrade_plugin_db', 'dtjwpt_upgrade_plugin_db', 10, 0);

