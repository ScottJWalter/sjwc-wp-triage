<?php

/**
 * triage.php
 */

// No thank you
if ( ! defined('ABSPATH') ) die;

// Include the core triage functions
require_once(DTJWPT_INCLUDES . 'triage.php');

?>

<div class="dtjwpt">

	<div class="dtjwpt-wrap wrap">

		<h1>

			<?php _e('WP Triage', 'wp-triage'); ?>

			<a href="https://wordpress.org/plugins/wp-triage/" class="page-title-action" target="_blank"><?php _e('Support', 'wp-triage'); ?></a>

		</h1>

		<?php settings_errors(); ?>

		<div class="dtjwpt-main">

			<?php require_once(DTJWPT_TEMPLATES . $dtjwpt_triage_page . '.php'); ?>

		</div>

		<div class="dtjwpt-aside">

			<?php require_once(DTJWPT_TEMPLATES . 'aside.php'); ?>

		</div>

	</div>

</div>
