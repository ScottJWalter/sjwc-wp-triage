<?php

/**
 * tickets.php
 */

// No thank you
if ( ! defined('ABSPATH') ) die;

// Get the project and ticket queries from the url
$dtjwpt_project_id = isset($_GET['project_id']) ? sanitize_key($_GET['project_id']) : 0;
$dtjwpt_ticket_id = isset($_GET['ticket_id']) ? sanitize_key($_GET['ticket_id']) : 0;

// Change our ids into intergers
$dtjwpt_project_id = intval($dtjwpt_project_id);
$dtjwpt_ticket_id = intval($dtjwpt_ticket_id);

// Set the show object variables to a default value
$dtjwpt_show_project = false;
$dtjwpt_show_ticket = false;

// Check to see if the ticket exists
if ( ! empty($dtjwpt_ticket_id) ) {

	// Check that the ticket exists
	if ( dtjwpt_get_ticket($dtjwpt_ticket_id) != false ) {

		// Set the variable to true to show the ticket
		$dtjwpt_show_ticket = true;

		// Get the ticket data and put it in a variable to use later
		$dtjwpt_ticket = dtjwpt_get_ticket($dtjwpt_ticket_id);

	}

} else {

	// Check to see if the project exists
	if ( ! empty($dtjwpt_project_id) ) {

		// Check that the project exists
		if ( dtjwpt_get_project($dtjwpt_project_id) != false ) {

			// Set the variable to true to show the project
			$dtjwpt_show_project = true;

			// Get the project data and put it in a variable to use later
			$dtjwpt_project = dtjwpt_get_project($dtjwpt_project_id);

		}

	}

}

// Include the WP_List_Table class
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * Extends the WP_List_Table class to show a list of tickets.
 * Needs the project id to show tickets based on the current project.
 */
class DTJWPT_Ticket_Table extends WP_List_Table {

	protected $project;

	public function __construct($project) {

		$this->project = $project;

		parent::__construct(array(
			'singular' => __('Ticket', 'wp-triage'),
			'plural'   => __('Tickets', 'wp-triage'),
			'ajax'     => false
		));

		$this->prepare_items();

	}

	function get_columns() {

		$columns = [
			'cb'          => '<input type="checkbox" />',
			'name'        => __('Name', 'wp-triage'),
			'component'   => __('Component', 'wp-triage'),
			'type'        => __('Type', 'wp-triage'),
			'priority'    => __('Priority', 'wp-triage'),
			'status'      => __('Status', 'wp-triage'),
			'create_date' => __('Created On', 'wp-triage'),
			'update_date' => __('Last Updated', 'wp-triage')
		];

		return $columns;

	}

	function get_sortable_columns() {

		$sortable_columns = array(
			'type'				=> array('type', false),
			'priority'			=> array('priority', true),
			'status'			=> array('status', true),
			'create_date'		=> array('create_date', false)
		);

		return $sortable_columns;
	
	}

	function column_cb($item) {

		global $dtjwpt_current_user;

		if ( dtjwpt_can_modify_ticket($item->ticket_id) ) {
		
			return sprintf('<input type="checkbox" name="%1$s[]" value="%2$s" />', $this->_args['singular'], $item->ticket_id);

		} else {

			return;

		}

	}

	function column_name($item) {

		return '<strong><a href="' . admin_url('admin.php?page=dtjwpt_triage&triage=tickets&ticket_id=') . $item->ticket_id . '" class="project">' . stripslashes($item->name) . '</a></strong>';
	
	}

	function column_component($item) {

		if ( $item->component_id == 0 ) {

			return '<abbr title="' . __('A component hasn\'t been selected.', 'wp-triage') . '">' . __('None', 'wp-triage') . '</abbr>';

		} else {

			$dtjwpt_component = dtjwpt_get_component($item->component_id);

			return '<a href="' . admin_url('admin.php?page=dtjwpt_components') . '">' . $dtjwpt_component->name . '</a>';

		}

	}

	function column_type($item) {

		if ( $item->type == "b" ) {

			$dtjwpt_ticket_type = __('Bug Report', 'wp-triage');

		} elseif ( $item->type == "s" ) {

			$dtjwpt_ticket_type = __('Support Query', 'wp-triage');

		} elseif ( $item->type == "f" ) {

			$dtjwpt_ticket_type = __('Feature Request', 'wp-triage');

		} else {

			$dtjwpt_ticket_type = '<abbr title="' . __('Unknown ticket type.', 'wp-triage') . '">' . __('Unknown', 'wp-triage') . '</abbr>';

		}

		return $dtjwpt_ticket_type;
	
	}

	function column_priority($item) {

		if ( $item->priority == "1" ) {

			$dtjwpt_ticket_priority = __('Trivial', 'wp-triage');

		} elseif ( $item->priority == "2" ) {

			$dtjwpt_ticket_priority = __('Minor', 'wp-triage');

		} elseif ( $item->priority == "3" ) {

			$dtjwpt_ticket_priority = __('Major', 'wp-triage');

		} else {

			$dtjwpt_ticket_priority = '<abbr title="' . __('Unknown ticket priority.', 'wp-triage') . '">' . __('Unknown', 'wp-triage') . '</abbr>';

		}

		return $dtjwpt_ticket_priority;
	
	}

	function column_status($item) {

		if ( $item->status == "0" ) {

			$dtjwpt_ticket_status = __('Open', 'wp-triage');

		} elseif ( $item->status == "1" ) {

			$dtjwpt_ticket_status = __('Closed', 'wp-triage');

		} else {

			$dtjwpt_ticket_status = '<abbr title="' . __('Unknown ticket status.', 'wp-triage') . '">' . __('Unknown', 'wp-triage') . '</abbr>';

		}

		return $dtjwpt_ticket_status;
	
	}

	function column_create_date($item) {

		return '<abbr title="' . date("jS F Y H:i:s a", strtotime($item->create_date)) . '">' . date("Y/m/d", strtotime($item->create_date)) . '</abbr>';
	
	}

	function column_update_date($item) {

		// Check if the original timestmp matches the updated time
		if ( strtotime($item->update_date) > strtotime($item->create_date) ) {

			return '<abbr title="' . date("jS F Y H:i:s a", strtotime($item->update_date)) . '">' . date("Y/m/d", strtotime($item->update_date)) . '</abbr>';

		} else {

			return '<abbr title="' . __('Not updated since created.', 'wp-triage') . '">' . __('Never', 'wp-triage') . '</abbr>';

		}

	}

	function column_default($item, $column_name) {

		return $item->$column_name;

	}

	function no_items() {
	
		_e('Sorry, there are no tickets to see yet.', 'wp-triage');
	
	}

	function get_bulk_actions() {

		return array(
			'close'		=> __('Close', 'wp-triage'),
			'open'		=> __('Open', 'wp-triage'),
			'delete'	=> __('Delete', 'wp-triage')
		);

	}

	function get_total_tickets() {

		global $wpdb;

		if ( get_option('dtjwpt_hide_closed_tickets') == "on" ) {

			$total_tickets = $wpdb->get_var("SELECT COUNT(*) FROM " . $wpdb->prefix . DTJWPT_DB_TICKETS . " WHERE project_id = " . $this->project . " AND status = 0");

		} else {

			$total_tickets = $wpdb->get_var("SELECT COUNT(*) FROM " . $wpdb->prefix . DTJWPT_DB_TICKETS . " WHERE project_id = " . $this->project);

		}

		return $total_tickets;

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

				if ( ! empty($_POST['ticket']) ) {

					if ( $action == "close" ) {

						foreach ( $_POST['ticket'] as $ticket ) {

							$wpdb->query(
								$wpdb->prepare(
									"UPDATE " . $wpdb->prefix . DTJWPT_DB_TICKETS . " SET status = 1 WHERE ticket_id = %s", $ticket
								)
							);

						}

					} elseif ( $action == "open" ) {

						foreach ( $_POST['ticket'] as $ticket ) {

							$wpdb->query(
								$wpdb->prepare(
									"UPDATE " . $wpdb->prefix . DTJWPT_DB_TICKETS . " SET status = 0 WHERE ticket_id = %s", $ticket
								)
							);

						}

					} elseif ( $action == "delete" ) {

						foreach ( $_POST['ticket'] as $ticket ) {

							$wpdb->query(
								$wpdb->prepare(
									"DELETE FROM " . $wpdb->prefix . DTJWPT_DB_NOTES . " WHERE ticket_id = %s", $ticket
								)
							);

							$wpdb->query(
								$wpdb->prepare(
									"DELETE FROM " . $wpdb->prefix . DTJWPT_DB_TICKETS . " WHERE ticket_id = %s LIMIT 1", $ticket
								)
							);

						}

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
		$total_items = $this->get_total_tickets();

		$this->set_pagination_args([
			'total_items' => $total_items,
			'per_page'    => $per_page,
			'total_pages' => ceil($total_items / $per_page)
		]);

		$query = "SELECT * FROM " . $wpdb->prefix . DTJWPT_DB_TICKETS . " WHERE project_id = " . $this->project;

		if ( get_option('dtjwpt_hide_closed_tickets') == "on" ) {

			$query .= " AND status = 0";

		}

		if ( ( ! empty( $_REQUEST['orderby'] ) ) && ( ! empty( $_REQUEST['order'] ) ) ) {

			$order_by = esc_sql( strtoupper($_REQUEST['orderby']) );
			$order_col = esc_sql( strtoupper($_REQUEST['order']) );

			$query .= " ORDER BY " . $order_by;
			$query .= $order_col == "ASC" ? ' ' . $order_col : ' ' . $order_col;

		} else {

			$query .= " ORDER BY status ASC, priority DESC, type ASC";

		}

		if ( $current_page >= 1 ) {

			$query .= " LIMIT " . $per_page;
			$query .= " OFFSET " . ($current_page - 1) * $per_page;

		}

		$this->items = $wpdb->get_results($query);

	}

}

