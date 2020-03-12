<?php

/**
 * triage.php
 */

// No thank you
if ( ! defined('ABSPATH') ) die;

global $wpdb;

// Set the value of our page request variable for checking later
$dtjwpt_page_request = isset($_GET['triage']) ? sanitize_key($_GET['triage']) : "";

// Find out which view we want
if ( $dtjwpt_page_request == "tickets" ) {

	// Set to tickets
	$dtjwpt_triage_page = "tickets";

} else {

	// Always fallback to projects
	$dtjwpt_triage_page = "projects";

}

