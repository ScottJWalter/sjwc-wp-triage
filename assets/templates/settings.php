<?php

/**
 * projects.php
 */

// No thank you
if ( ! defined('ABSPATH') ) die;

?>

<div class="dtjwpt">

	<div class="dtjwpt-wrap wrap">

		<h1>

			<?php _e('WP Triage', 'wp-triage'); ?>

			<a href="https://wordpress.org/plugins/wp-triage/" class="page-title-action" target="_blank"><?php _e('Support', 'wp-triage'); ?></a>

		</h1>

		<?php settings_errors(); ?>

		<div class="dtjwpt-main">

			<div class="dtjwpt-box">

				<h2 class="dtjwpt-title"><?php _e('Settings', 'wp-triage'); ?></h2>

				<div class="dtjwpt-content">

					<form method="post" action="options.php">

						<table class="dtjwpt-form form-table">

							<tbody>

								<?php settings_fields('dtjwpt_settings_fields'); ?>
								<?php do_settings_sections('dtjwpt_settings_group'); ?>

							</tbody>

						</table>

						<?php submit_button( __('Save Settings', 'wp-triage'), 'primary' ); ?>

					</form>

				</div>

			</div>

		</div>

		<div class="dtjwpt-aside">

			<?php require_once(DTJWPT_TEMPLATES . 'aside.php'); ?>

		</div>

	</div>

</div>
