<?php

/**
 * roles.php
 */

// No thank you
if ( ! defined('ABSPATH') ) die;

// Get a new object of the current site roles
$dtjwpt_get_roles = new WP_Roles();

?>

<div class="dtjwpt">

	<div class="dtjwpt-wrap wrap">

		<h1>

			<?php _e('WP Triage', 'wp-triage'); ?>

			<a href="https://wordpress.org/plugins/wp-triage/" class="page-title-action" target="_blank"><?php _e('Support', 'wp-triage'); ?></a>

		</h1>

		<div class="dtjwpt-main">

			<div class="notice notice-success is-dismissible dtjwpt-notice-plugin-roles-success" style="display: none;">
				<p><strong><?php _e('The user roles have been successfully updated.', 'wp-triage'); ?></strong></p>
			</div>

			<div class="notice notice-warning is-dismissible dtjwpt-notice-plugin-roles-warning" style="display: none;">
				<p><strong><?php _e('The user roles were updated but no one has any plugin capabilities anymore.', 'wp-triage'); ?></strong></p>
			</div>

			<div class="notice notice-error is-dismissible dtjwpt-notice-plugin-roles-failure" style="display: none;">
				<p><strong><?php _e('There was a problem updating the user roles, please try again.', 'wp-triage'); ?></strong></p>
			</div>

			<div class="dtjwpt-box">

				<h2 class="dtjwpt-title"><?php _e('Roles &amp; Capabilities', 'wp-triage'); ?></h2>

				<div class="dtjwpt-content dtjwpt-plugin-roles-form">

					<form method="post">

						<table class="dtjwpt-form form-table">

							<tbody>

								<tr>
									<th><?php _e('Manage Projects', 'wp-triage'); ?></th>
									<td>
									
										<div class="dtjwpt-list">
											<ul class="dtjwpt_project_roles">
												<?php foreach ( $dtjwpt_get_roles->roles as $role ) : ?>
													<?php $dtjwpt_the_role = strtolower($role['name']); ?>
													<li>
														<label><input type="checkbox" id="dtjwpt_project_role" class="dtjwpt_project_role role_<?php echo $dtjwpt_the_role; ?>"<?php if ( array_key_exists( DTJWPT_CAP_MANAGE_PROJECTS, $role['capabilities'] ) ) : ?> checked="checked"<?php endif; ?> value="<?php echo $dtjwpt_the_role; ?>" /> <?php echo ucfirst($dtjwpt_the_role); ?></label>
													</li>
												<?php endforeach; ?>
											</ul>
										</div>

									</td>
								</tr>

								<tr>
									<th><?php _e('Manage Components', 'wp-triage'); ?></th>
									<td>

										<div class="dtjwpt-list">
											<ul class="dtjwpt_component_roles">
												<?php foreach ( $dtjwpt_get_roles->roles as $role ) : ?>
													<?php $dtjwpt_the_role = strtolower($role['name']); ?>
													<li>
														<label><input type="checkbox" id="dtjwpt_component_role" class="dtjwpt_component_role role_<?php echo $dtjwpt_the_role; ?>"<?php if ( array_key_exists( DTJWPT_CAP_MANAGE_COMPONENTS, $role['capabilities'] ) ) : ?> checked="checked"<?php endif; ?> value="<?php echo $dtjwpt_the_role; ?>" /> <?php echo ucfirst($dtjwpt_the_role); ?></label>
													</li>
												<?php endforeach; ?>
											</ul>
										</div>

									</td>
								</tr>

								<tr>
									<th><?php _e('Manage Tickets', 'wp-triage'); ?></th>
									<td>

										<div class="dtjwpt-list">
											<ul class="dtjwpt_ticket_roles">
												<?php foreach ( $dtjwpt_get_roles->roles as $role ) : ?>
													<?php $dtjwpt_the_role = strtolower($role['name']); ?>
													<li>
														<label><input type="checkbox" id="dtjwpt_ticket_role" class="dtjwpt_ticket_role role_<?php echo $dtjwpt_the_role; ?>"<?php if ( array_key_exists( DTJWPT_CAP_MANAGE_TICKETS, $role['capabilities'] ) ) : ?> checked="checked"<?php endif; ?> value="<?php echo $dtjwpt_the_role; ?>" /> <?php echo ucfirst($dtjwpt_the_role); ?></label>
													</li>
												<?php endforeach; ?>
											</ul>
										</div>

									</td>
								</tr>

								<tr>
									<th><?php _e('Manage Notes', 'wp-triage'); ?></th>
									<td>

										<div class="dtjwpt-list">
											<ul class="dtjwpt_note_roles">
												<?php foreach ( $dtjwpt_get_roles->roles as $role ) : ?>
													<?php $dtjwpt_the_role = strtolower($role['name']); ?>
													<li>
														<label><input type="checkbox" id="dtjwpt_note_role" class="dtjwpt_note_role role_<?php echo $dtjwpt_the_role; ?>"<?php if ( array_key_exists( DTJWPT_CAP_MANAGE_NOTES, $role['capabilities'] ) ) : ?> checked="checked"<?php endif; ?> value="<?php echo $dtjwpt_the_role; ?>" /> <?php echo ucfirst($dtjwpt_the_role); ?></label>
													</li>
												<?php endforeach; ?>
											</ul>
										</div>

									</td>
								</tr>

							</tbody>

							<tfoot>

								<tr>
									<td colspan="2">

										<p><?php _e('You can select who can manage projects, components, tickets and notes within the WordPress admin area from this page. <em>It is highly recommended that only <strong>Administrators</strong> have the capability for all four options and roles ranging from <strong>Editor</strong> to <strong>Contributor</strong> have the manage tickets and notes capabilities.</em>', 'wp-triage'); ?></p>

										<a class="button button-primary dtjwpt-button-plugin-roles"><?php _e('Save Roles', 'wp-triage'); ?></a>
										<span class="spinner dtjwpt-plugin-roles-spinner"></span>

										<?php $dtjwpt_plugin_roles_nonce = wp_create_nonce('dtjwpt_plugin_roles_nonce'); ?>
										<input type="hidden" name="dtjwpt_plugin_roles_nonce" class="dtjwpt_plugin_roles_nonce" value="<?php echo $dtjwpt_plugin_roles_nonce; ?>" />

									</td>
								</tr>

							</tfoot>

						</table>

					</form>

				</div>

			</div>

		</div>

		<div class="dtjwpt-aside">

			<?php require_once(DTJWPT_TEMPLATES . 'aside.php'); ?>

		</div>

	</div>

</div>
