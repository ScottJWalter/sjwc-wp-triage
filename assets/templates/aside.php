<?php

/**
 * aside.php
 */

// No thank you
if ( ! defined('ABSPATH') ) die;

?>

	<?php if ( 'no' != DTJWPT_DONATE_UPSELL ) : ?>

		<?php if ( 'on' != get_option('dtjwpt_donate_upsell') ) : ?>

			<div class="dtjwpt-box">

				<h3 class="dtjwpt-title blue"><?php _e('Buy me a coffee', 'wp-triage'); ?></h2>

				<div class="dtjwpt-content">

					<p><?php _e('Are you enjoying using this plugin? Please consider donating to the author, it would help to continue the development of the plugin and make it even better. Even a small amount is appreciated!', 'wp-triage'); ?></p>

					<p><a href="https://profiles.wordpress.org/danieltj/#content-plugins" target="_blank"><?php _e('Check out my other plugins.', 'wp-triage'); ?></a></p>

					<p><a href="https://www.paypal.me/dtj27" class="button button-primary" target="_blank"><?php _e('Donate Now', 'wp-triage'); ?></a></p>

				</div>

			</div>

		<?php endif; ?>

	<?php endif; ?>

	<div class="dtjwpt-box">

		<h3 class="dtjwpt-title"><?php _e('Information', 'wp-triage'); ?></h2>

		<div class="dtjwpt-content">

			<p><strong><?php _e('Need help with something?', 'wp-triage'); ?></strong> <?php _e('You can ask a question today in the WordPress forums or read the plugin information on the plugin details page.', 'wp-triage'); ?></p>

			<p><?php _e('You&#39;re currently using', 'wp-triage'); ?> <strong><?php _e('Version ' . DTJWPT_VERSION, 'wp-triage'); ?></strong> <?php _e('of the plugin', 'wp-triage'); ?>.</p>

			<p><em><?php _e('Thanks for using WP Triage!', 'wp-triage'); ?></em></p>

		</div>

	</div>
