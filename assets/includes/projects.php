<?php

/**
 * projects.php
 */

// No thank you
if ( ! defined('ABSPATH') ) die;

// Include the WP_List_Table class
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * Extends the WP_List_Table class to show a list of projects.
 * Requires no predefined data for showing output.
 */
class DTJWPT_Project_Table extends WP_List_Table {

	public function __construct() {

		parent::__construct(array(
			'singular' => __('Project', 'wp-triage'),
			'plural'   => __('Projects', 'wp-triage'),
			'ajax'     => false
		));

		$this->prepare_items();

	}

	function get_columns() {

		$columns = [
			'cb'             => '<input type="checkbox" />',
			'name'           => __('Project Name', 'wp-triage'),
			'project_id'     => __('Project ID', 'wp-triage'),
			'owner_id'       => __('Project Owner', 'wp-triage'),
			'open_tickets'   => __('Open Tickets', 'wp-triage'),
			'closed_tickets' => __('Closed Tickets', 'wp-triage')
		];

		return $columns;

	}

	function get_sortable_columns() {

		$sortable_columns = array(
			'name'				=> array('name', false),
			'project_id'		=> array('project_id', true)
		);

		return $sortable_columns;
	
	}

	function column_cb( $item ) {

		if ( dtjwpt_can_modify_project($item->project_id) ) {
		
			return sprintf('<input type="checkbox" name="%1$s[]" value="%2$s" />', $this->_args['singular'], $item->project_id);

		} else {

			return;

		}
	
	}

	function column_name($item) {

		return '<strong><a href="' . admin_url('admin.php?page=dtjwpt_triage&triage=tickets&project_id=') . $item->project_id . '" class="project">' . stripslashes($item->name) . '</a></strong>';
	
	}

	function column_project_id($item) {

		return $item->project_id;

	}

	function column_owner_id($item) {

		if ( $item->owner_id == 0 ) {

			return '<abbr title="' . __('Project is unassigned.', 'wp-triage') . '">' . __('Unassigned', 'wp-triage') . '</abbr>';

		} else {

			$get_user_data = get_userdata($item->owner_id);
			$get_display_name = $get_user_data->display_name;

			if ( ! empty($get_display_name) ) {

				return '<a href="' . admin_url('user-edit.php?user_id=') . $item->owner_id . '" class="profile">' . $get_display_name . '</a>';

			} else {

				return '<abbr title="' . __('User cannot be found.', 'wp-triage') . '">' . __('Unknown', 'wp-triage') . '</abbr>';

			}

		}

	}

	function column_open_tickets($item) {

		global $wpdb;

		$total_open_tickets = $wpdb->get_var("SELECT COUNT(*) FROM " . $wpdb->prefix . DTJWPT_DB_TICKETS . " WHERE project_id = " . $item->project_id . " AND status = 0");

		echo $total_open_tickets;

	}

	function column_closed_tickets($item) {

		global $wpdb;

		$total_closed_tickets = $wpdb->get_var("SELECT COUNT(*) FROM " . $wpdb->prefix . DTJWPT_DB_TICKETS . " WHERE project_id = " . $item->project_id . " AND status = 1");

		echo $total_closed_tickets;

	}

	function column_default($item, $column_name) {

		return $item->$column_name;

	}

	function no_items() {
	
		_e('Sorry, there are no projects to see yet.', 'wp-triage');
	
	}

	function get_bulk_actions() {

		return array(
			'delete' => __('Delete', 'wp-triage')
		);

	}

	function get_total_projects() {

		global $wpdb;

		$total_projects = $wpdb->get_var("SELECT COUNT(*) FROM " . $wpdb->prefix . DTJWPT_DB_PROJECTS);

		return $total_projects;

	}

	function process_bulk_action() {

		global $wpdb;

		if ( isset( $_POST['_wpnonce'] ) && ! empty( $_POST['_wpnonce'] ) ) {

			$nonce  = filter_input( INPUT_POST, '_wpnonce', FILTER_SANITIZE_STRING );
			$action = 'bulk-' . $this->_args['plural'];

			if ( ! wp_verify_nonce( $nonce, $action ) ) {

				wp_die('Could not process request.');

			} else {

				$action = $this->current_action();

				if ( ( $action == "delete" ) && ( ! empty($_POST['project']) ) ) {

					foreach ( $_POST['project'] as $project ) {

						$wpdb->query(
							$wpdb->prepare(
								"DELETE FROM " . $wpdb->prefix . DTJWPT_DB_NOTES . " WHERE project_id = %s", $project
							)
						);

						$wpdb->query(
							$wpdb->prepare(
								"DELETE FROM " . $wpdb->prefix . DTJWPT_DB_TICKETS . " WHERE project_id = %s", $project
							)
						);

						$wpdb->query(
							$wpdb->prepare(
								"DELETE FROM " . $wpdb->prefix . DTJWPT_DB_PROJECTS . " WHERE project_id = %s LIMIT 1", $project
							)
						);

					}

				}

			}

		}

	}

	function prepare_items() {

		global $wpdb;

		$per_page = intval(get_option('dtjwpt_items_per_page'));

		if ( $per_page < 1 ) {

			$per_page = 20;

		}

		$columns = $this->get_columns();
		$hidden = array();
		$sortable = $this->get_sortable_columns();
		$this->_column_headers = array($columns, $hidden, $sortable);
		$this->process_bulk_action();

		$current_page = $this->get_pagenum();
		$total_items = $this->get_total_projects();

		$this->set_pagination_args([
			'total_items' => $total_items,
			'per_page'    => $per_page,
			'total_pages' => ceil($total_items / $per_page)
		]);

		if ( ( ! empty( $_REQUEST['orderby'] ) ) && ( ! empty( $_REQUEST['order'] ) ) ) {

			$query = "SELECT * FROM " . $wpdb->prefix . DTJWPT_DB_PROJECTS;

			$order_by = esc_sql( strtoupper($_REQUEST['orderby']) );
			$order_col = esc_sql( strtoupper($_REQUEST['order']) );

			$query .= " ORDER BY " . $order_by;
			$query .= $order_col == "ASC" ? ' ' . $order_col : ' ' . $order_col;

		} else {

			$query = "SELECT * FROM " . $wpdb->prefix . DTJWPT_DB_PROJECTS . " ORDER BY project_id DESC";

		}

		if ( $current_page >= 1 ) {

			$query .= " LIMIT " . $per_page;
			$query .= " OFFSET " . ($current_page - 1) * $per_page;

		}

		$this->items = $wpdb->get_results($query);

	}

}

