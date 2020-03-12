<?php

/**
 * projects.php
 */

// No thank you
if ( ! defined('ABSPATH') ) die;

// Include the projects functions file
require_once(DTJWPT_INCLUDES . "projects.php");

// Create a new projects table object
$project_table = new DTJWPT_Project_Table();

?>

<?php if ( dtjwpt_can_manage_projects() ) : ?>

	<div class="notice notice-success is-dismissible dtjwpt-notice-create-project-success" style="display: none;">
		<p><strong><?php _e('The project has been successfully created!', 'wp-triage'); ?></strong></p>
	</div>

	<div class="notice notice-error is-dismissible dtjwpt-notice-create-project-failure" style="display: none;">
		<p><strong><?php _e('The new project couldn&#39;t be added, please try again.', 'wp-triage'); ?></strong></p>
	</div>

<?php endif; ?>

<div class="dtjwpt-box">

	<h2 class="dtjwpt-title">
		<span><?php _e('All Projects', 'wp-triage'); ?></span>
		<?php if ( dtjwpt_can_manage_projects() ) : ?>
			<a href="#" class="button button-primary dtjwpt-create-project-toggle"><?php _e('New Project', 'wp-triage'); ?></a>
		<?php endif; ?>
	</h2>

	<?php if ( dtjwpt_can_manage_projects() ) : ?>

		<div class="dtjwpt-content dtjwpt-create-project-form" style="display: none;">

			<form method="post">

				<table class="dtjwpt-form form-table">

					<tbody>

						<tr>
							<th><?php _e('Project Name', 'wp-triage'); ?> <span class="dtjwpt-required">*</span></th>
							<td>
								<input type="text" id="dtjwpt_project_name" class="dtjwpt_project_name" />
							</td>
						</tr>

						<tr>
							<th><?php _e('Project Owner', 'wp-triage'); ?></th>
							<td>
								<?php $dtjwpt_get_users = get_users(); ?>
								<select id="dtjwpt_project_owner" class="dtjwpt_project_owner">
									<option value="0" selected="selected"><?php _e('Unassigned', 'wp-triage'); ?></option>
									<?php foreach ( $dtjwpt_get_users as $dtjwpt_user ) : ?>
										<option value="<?php echo $dtjwpt_user->ID; ?>"><?php echo $dtjwpt_user->data->display_name; ?></option>
									<?php endforeach; ?>
								</select>
							</td>
						</tr>

						<tr>
							<th></th>
							<td>

								<p><strong><?php _e('All fields marked <span class="dtjwpt-required">*</span> are required.', 'wp-triage'); ?></strong></p>

								<a class="button button-primary dtjwpt-button-create-project"><?php _e('Create Project', 'wp-triage'); ?></a>
								<span class="spinner dtjwpt-create-project-spinner"></span>

								<?php $dtjwpt_create_project_nonce = wp_create_nonce('dtjwpt_create_project_nonce'); ?>
								<input type="hidden" name="dtjwpt_create_project_nonce" class="dtjwpt_create_project_nonce" value="<?php echo $dtjwpt_create_project_nonce; ?>" />

							</td>
						</tr>

					</tbody>

				</table>

			</form>

		</div>

	<?php endif; ?>

</div>

<div class="dtjwpt-table">

	<div class="dtjwpt-content-table dtjwpt_projects_table">

		<form method="post">

			<?php $project_table->display(); ?>
		
		</form>

	</div>

</div>

