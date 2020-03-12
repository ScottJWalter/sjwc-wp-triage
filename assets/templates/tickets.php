<?php

/**
 * tickets.php
 */

// No thank you
if ( ! defined('ABSPATH') ) die;

// Include the tickets functions file
require_once(DTJWPT_INCLUDES . 'tickets.php');

// Create a new tickets table object
$ticket_table = new DTJWPT_Ticket_Table($dtjwpt_project_id);

?>

<?php if ( $dtjwpt_show_ticket ) : ?>

	<?php if ( $dtjwpt_ticket->status == "1" ) : ?>

		<div class="notice notice-success dtjwpt-notice-closed-ticket-info">
			<p><strong><?php _e('This ticket has been completed and marked as closed.', 'wp-triage'); ?></strong></p>
		</div>

	<?php endif; ?>

	<?php if ( dtjwpt_can_modify_ticket($dtjwpt_ticket->ticket_id) ) : ?>

		<div class="notice notice-success is-dismissible dtjwpt-notice-update-ticket-success" style="display: none;">
			<p><strong><?php _e('The ticket has been successfully updated!', 'wp-triage'); ?></strong></p>
		</div>

		<div class="notice notice-error is-dismissible dtjwpt-notice-update-ticket-failure" style="display: none;">
			<p><strong><?php _e('The ticket could not be updated, please try again.', 'wp-triage'); ?></strong></p>
		</div>

	<?php endif; ?>

	<?php if ( dtjwpt_can_manage_notes() ) : ?>

		<div class="notice notice-success is-dismissible dtjwpt-notice-create-note-success" style="display: none;">
			<p><strong><?php _e('Your note was successfully posted to this ticket!', 'wp-triage'); ?></strong></p>
		</div>

		<div class="notice notice-error is-dismissible dtjwpt-notice-create-note-failure" style="display: none;">
			<p><strong><?php _e('Your note could not be posted, please try again.', 'wp-triage'); ?></strong></p>
		</div>

		<div class="notice notice-success is-dismissible dtjwpt-notice-delete-note-success" style="display: none;">
			<p><strong><?php _e('The note was successfully deleted from the database.', 'wp-triage'); ?></strong></p>
		</div>

		<div class="notice notice-error is-dismissible dtjwpt-notice-delete-note-failure" style="display: none;">
			<p><strong><?php _e('The note could not be deleted, please try again.', 'wp-triage'); ?></strong></p>
		</div>

	<?php endif; ?>

	<div class="dtjwpt-box dtjwpt-ticket-toolbar">

		<h2 class="dtjwpt-title dtjwpt-ticket-title">
			<span><?php _e('Ticket:', 'wp-triage'); ?> <?php echo stripslashes($dtjwpt_ticket->name); ?></span>
			<a href="<?php echo esc_url( admin_url('admin.php?page=dtjwpt_triage&triage=tickets&project_id=' . $dtjwpt_ticket->project_id) ); ?>" class="button button-secondary"><?php _e('Return to Project', 'wp-triage'); ?></a>
		</h2>

	</div>

	<div class="dtjwpt-box dtjwpt-ticket-form">

		<div class="dtjwpt-content dtjwpt-update-ticket-form">

			<form method="post">

				<table class="dtjwpt-form form-table">

					<tbody>

						<?php if ( dtjwpt_can_modify_ticket($dtjwpt_ticket->ticket_id) ) : ?>

							<tr>
								<th><label for="dtjwpt_ticket_name"><?php _e('Ticket Name', 'wp-triage'); ?></label></th>
								<td>
									<input type="text" id="dtjwpt_ticket_name" class="dtjwpt_ticket_name" value="<?php echo stripslashes($dtjwpt_ticket->name); ?>" />
								</td>
							</tr>

							<tr>
								<th><label for="dtjwpt_ticket_description"><?php _e('Ticket Description', 'wp-triage'); ?></label></th>
								<td>
									<textarea id="dtjwpt_ticket_description" class="dtjwpt_ticket_description"><?php echo stripslashes($dtjwpt_ticket->description); ?></textarea>
								</td>
							</tr>

							<tr>
								<th><?php _e('Reported by', 'wp-triage'); ?></th>
								<td>
									<?php 
										$dtjwpt_get_reporter = get_userdata($dtjwpt_ticket->author_id);
										$dtjwpt_reporter_name = $dtjwpt_get_reporter->data->display_name;

										if ( ! empty($dtjwpt_reporter_name) ) {

											if ( $dtjwpt_ticket->author_id == get_current_user_id() ) {

												echo '<a href="' . admin_url('user-edit.php?user_id=') . $dtjwpt_ticket->author_id . '" class="author-name">' . $dtjwpt_reporter_name . '</a> ' . __('(me)', 'wp-triage');

											} else {

												echo '<a href="' . admin_url('user-edit.php?user_id=') . $dtjwpt_ticket->author_id . '" class="author-name">' . $dtjwpt_reporter_name . '</a>';

											}

										} else {

											echo '<abbr title="' . __('User cannot be found.', 'wp-triage') . '">' . __('Unknown', 'wp-triage') . '</abbr>';

										}
									?>
								</td>
							</tr>

							<tr>
								<th><?php _e('Reported on', 'wp-triage'); ?></th>
								<td>
									<?php echo '<abbr title="' . date("jS F Y H:i:s a", strtotime($dtjwpt_ticket->create_date)) . '">' . date("Y/m/d", strtotime($dtjwpt_ticket->create_date)) . '</abbr>'; ?>
								</td>
							</tr>

							<?php if ( $dtjwpt_ticket->updater_id != 0 ) : ?>

								<tr>
									<th><?php _e('Last updated by', 'wp-triage'); ?></th>
									<td>
										<?php 
											$dtjwpt_get_updater = get_userdata($dtjwpt_ticket->updater_id);
											$dtjwpt_updater_name = $dtjwpt_get_updater->data->display_name;

											if ( $dtjwpt_ticket->updater_id == get_current_user_id() ) {

												echo '<a href="' . admin_url('user-edit.php?user_id=') . $dtjwpt_ticket->updater_id . '" class="author-name">' . $dtjwpt_updater_name . '</a> ' . '(' . __('me', 'wp-triage') . ')';

											} else {

												echo '<a href="' . admin_url('user-edit.php?user_id=') . $dtjwpt_ticket->updater_id . '" class="author-name">' . $dtjwpt_updater_name . '</a>';

											}
										?>
									</td>
								</tr>

							<?php endif; ?>

							<?php if ( strtotime($dtjwpt_ticket->update_date) > strtotime($dtjwpt_ticket->create_date) ) : ?>

								<tr>
									<th><?php _e('Last updated on', 'wp-triage'); ?></th>
									<td>
										<?php echo '<abbr title="' . date("jS F Y H:i:s a", strtotime($dtjwpt_ticket->update_date)) . '">' . date("Y/m/d", strtotime($dtjwpt_ticket->update_date)) . '</abbr>'; ?>
									</td>
								</tr>

							<?php endif; ?>

							<tr>
								<th><label for="dtjwpt_ticket_assignee"><?php _e('Assigned to', 'wp-triage'); ?></label></th>
								<td>
									<?php $dtjwpt_get_users = get_users(); ?>
									<select id="dtjwpt_ticket_assignee" class="dtjwpt_ticket_assignee">
										<option value="0"><?php _e('Unassigned', 'wp-triage'); ?></option>
										<?php foreach ( $dtjwpt_get_users as $dtjwpt_user ) : ?>
											<option value="<?php echo $dtjwpt_user->ID; ?>"<?php if ( $dtjwpt_ticket->assignee_id == $dtjwpt_user->ID ) : ?> selected="selected"<?php endif; ?>><?php echo $dtjwpt_user->data->display_name; ?></option>
										<?php endforeach; ?>
									</select>
								</td>
							</tr>

							<tr>
								<th><label for="dtjwpt_ticket_component"><?php _e('Component', 'wp-triage'); ?></label></th>
								<td>
									<?php $dtjwpt_get_components = dtjwpt_get_component(0); ?>
									<select id="dtjwpt_ticket_component" class="dtjwpt_ticket_component">
										<option value="0" selected="selected"><?php _e('None', 'wp-triage'); ?></option>
										<?php foreach ( $dtjwpt_get_components as $dtjwpt_component ) : ?>
											<option value="<?php echo $dtjwpt_component->component_id; ?>"<?php if ( $dtjwpt_ticket->component_id == $dtjwpt_component->component_id ) : ?> selected="selected"<?php endif; ?>><?php echo $dtjwpt_component->name; ?></option>
										<?php endforeach; ?>
									</select>
								</td>
							</tr>

							<tr>
								<th><label for="dtjwpt_ticket_type"><?php _e('Type', 'wp-triage'); ?></label></th>
								<td>
									<select id="dtjwpt_ticket_type" class="dtjwpt_ticket_type">
										<option value="b"<?php if ( $dtjwpt_ticket->type == "b" ) : ?> selected="selected"<?php endif; ?>><?php _e('Bug Report', 'wp-triage'); ?></option>
										<option value="s"<?php if ( $dtjwpt_ticket->type == "s" ) : ?> selected="selected"<?php endif; ?>><?php _e('Support Query', 'wp-triage'); ?></option>
										<option value="f"<?php if ( $dtjwpt_ticket->type == "f" ) : ?> selected="selected"<?php endif; ?>><?php _e('Feature Request', 'wp-triage'); ?></option>
									</select>
								</td>
							</tr>

							<tr>
								<th><label for="dtjwpt_ticket_priority"><?php _e('Priority', 'wp-triage'); ?></label></th>
								<td>
									<select id="dtjwpt_ticket_priority" class="dtjwpt_ticket_priority">
										<option value="1"<?php if ( $dtjwpt_ticket->priority == "1" ) : ?> selected="selected"<?php endif; ?>><?php _e('Trivial', 'wp-triage'); ?></option>
										<option value="2"<?php if ( $dtjwpt_ticket->priority == "2" ) : ?> selected="selected"<?php endif; ?>><?php _e('Minor', 'wp-triage'); ?></option>
										<option value="3"<?php if ( $dtjwpt_ticket->priority == "3" ) : ?> selected="selected"<?php endif; ?>><?php _e('Major', 'wp-triage'); ?></option>
									</select>
								</td>
							</tr>

							<tr>
								<th><label for="dtjwpt_ticket_status"><?php _e('Status', 'wp-triage'); ?></label></th>
								<td>
									<select id="dtjwpt_ticket_status" class="dtjwpt_ticket_status">
										<option value="0"<?php if ( $dtjwpt_ticket->status == "0" ) : ?> selected="selected"<?php endif; ?>><?php _e('Open', 'wp-triage'); ?></option>
										<option value="1"<?php if ( $dtjwpt_ticket->status == "1" ) : ?> selected="selected"<?php endif; ?>><?php _e('Closed', 'wp-triage'); ?></option>
									</select>
								</td>
							</tr>

							<tr>
								<th></th>
								<td>
									
									<p><strong><?php _e('Make sure that you save any changes that have been made to the ticket.', 'wp-triage'); ?></strong></p>

									<a class="button button-primary dtjwpt-button-update-ticket"><?php _e('Save Ticket', 'wp-triage'); ?></a>
									<span class="spinner dtjwpt-update-ticket-spinner"></span>

									<input type="hidden" name="dtjwpt_ticket_id" class="dtjwpt_ticket_id" value="<?php echo $dtjwpt_ticket->ticket_id; ?>" />

									<?php $dtjwpt_update_ticket_nonce = wp_create_nonce('dtjwpt_update_ticket_nonce'); ?>
									<input type="hidden" name="dtjwpt_update_ticket_nonce" class="dtjwpt_update_ticket_nonce" value="<?php echo $dtjwpt_update_ticket_nonce; ?>" />

								</td>
							</tr>

						<?php else : ?>

							<tr>
								<th><?php _e('Ticket Name', 'wp-triage'); ?></th>
								<td>
									<p><?php echo stripslashes($dtjwpt_ticket->name); ?></p>
								</td>
							</tr>

							<tr>
								<th><?php _e('Ticket Description', 'wp-triage'); ?></th>
								<td>
									<?php if ( empty($dtjwpt_ticket->description) ) : ?>
										<p><?php _e('&mdash;', 'wp-triage'); ?></p>
									<?php else : ?>
										<p><?php echo stripslashes($dtjwpt_ticket->description); ?></p>
									<?php endif; ?>
								</td>
							</tr>

							<tr>
								<th><?php _e('Reported by', 'wp-triage'); ?></th>
								<td>
									<?php 
										$dtjwpt_get_reporter = get_userdata($dtjwpt_ticket->author_id);
										$dtjwpt_reporter_name = $dtjwpt_get_reporter->data->display_name;

										if ( ! empty($dtjwpt_reporter_name) ) {

											if ( $dtjwpt_ticket->author_id == get_current_user_id() ) {

												echo '<a href="' . admin_url('user-edit.php?user_id=') . $dtjwpt_ticket->author_id . '" class="author-name">' . $dtjwpt_reporter_name . '</a> ' . __('(me)', 'wp-triage');

											} else {

												echo '<a href="' . admin_url('user-edit.php?user_id=') . $dtjwpt_ticket->author_id . '" class="author-name">' . $dtjwpt_reporter_name . '</a>';

											}

										} else {

											echo '<abbr title="' . __('User cannot be found.', 'wp-triage') . '">' . __('Unknown', 'wp-triage') . '</abbr>';

										}
									?>
								</td>
							</tr>

							<tr>
								<th><?php _e('Reported on', 'wp-triage'); ?></th>
								<td>
									<?php echo '<abbr title="' . date("jS F Y H:i:s a", strtotime($dtjwpt_ticket->create_date)) . '">' . date("Y/m/d", strtotime($dtjwpt_ticket->create_date)) . '</abbr>'; ?>
								</td>
							</tr>

							<?php if ( $dtjwpt_ticket->updater_id != 0 ) : ?>

								<tr>
									<th><?php _e('Last updated by', 'wp-triage'); ?></th>
									<td>
										<?php 
											$dtjwpt_get_updater = get_userdata($dtjwpt_ticket->updater_id);
											$dtjwpt_updater_name = $dtjwpt_get_updater->data->display_name;

											if ( $dtjwpt_ticket->updater_id == get_current_user_id() ) {

												echo '<a href="' . admin_url('user-edit.php?user_id=') . $dtjwpt_ticket->updater_id . '" class="author-name">' . $dtjwpt_updater_name . '</a> ' . '(' . __('me', 'wp-triage') . ')';

											} else {

												echo '<a href="' . admin_url('user-edit.php?user_id=') . $dtjwpt_ticket->updater_id . '" class="author-name">' . $dtjwpt_updater_name . '</a>';

											}
										?>
									</td>
								</tr>

							<?php endif; ?>

							<?php if ( $dtjwpt_ticket->update_date != $dtjwpt_ticket->create_date ) : ?>

								<tr>
									<th><?php _e('Last updated on', 'wp-triage'); ?></th>
									<td>
										<?php echo '<abbr title="' . date("jS F Y H:i:s a", strtotime($dtjwpt_ticket->update_date)) . '">' . date("Y/m/d", strtotime($dtjwpt_ticket->update_date)) . '</abbr>'; ?>
									</td>
								</tr>

							<?php endif; ?>

							<tr>
								<th><?php _e('Assigned to', 'wp-triage'); ?></th>
								<td>
									<?php 
										if ( $dtjwpt_ticket->assignee_id == 0 ) {

											echo '<abbr title="' . __('Ticket is unassigned.', 'wp-triage') . '">' . __('Unassigned', 'wp-triage') . '</abbr>';

										} else {

											$dtjwpt_get_assignee = get_userdata($dtjwpt_ticket->assignee_id);
											$dtjwpt_assignee_name = $dtjwpt_get_assignee->data->display_name;

											if ( ! empty($dtjwpt_assignee_name) ) {

												echo '<a href="' . admin_url('user-edit.php?user_id=') . $dtjwpt_ticket->assignee_id . '" class="author-name">' . $dtjwpt_assignee_name . '</a>';

											} else {

												echo '<abbr title="' . __('User cannot be found.', 'wp-triage') . '">' . __('Unknown', 'wp-triage') . '</abbr>';

											}

										}
									?>
								</td>
							</tr>

							<tr>
								<th><?php _e('Component', 'wp-triage'); ?></th>
								<td>
									<?php $dtjwpt_get_component = dtjwpt_get_component($dtjwpt_ticket->component_id); ?>
									<?php if ( ! empty($dtjwpt_get_component) ) : ?>
										<?php echo $dtjwpt_get_component->name; ?>
									<?php else : ?>
										<?php _e('None', 'wp-triage'); ?>
									<?php endif; ?>
								</td>
							</tr>

							<tr>
								<th><?php _e('Type', 'wp-triage'); ?></th>
								<td>
									<?php if ( $dtjwpt_ticket->type == "b" ) : ?>
										<?php _e('Bug Report', 'wp-triage'); ?>
									<?php elseif ( $dtjwpt_ticket->type == "f" ) : ?>
										<?php _e('Feature Request', 'wp-triage'); ?>
									<?php endif; ?>
								</td>
							</tr>

							<tr>
								<th><?php _e('Priority', 'wp-triage'); ?></th>
								<td>
									<?php if ( $dtjwpt_ticket->priority == "1" ) : ?>
										<?php _e('Trivial', 'wp-triage'); ?>
									<?php elseif ( $dtjwpt_ticket->priority == "2" ) : ?>
										<?php _e('Minor', 'wp-triage'); ?>
									<?php elseif ( $dtjwpt_ticket->priority == "3" ) : ?>
										<?php _e('Major', 'wp-triage'); ?>
									<?php endif; ?>
								</td>
							</tr>

							<tr>
								<th><?php _e('Status', 'wp-triage'); ?></th>
								<td>
									<?php if ( $dtjwpt_ticket->status == "0" ) : ?>
										<?php _e('Open', 'wp-triage'); ?>
									<?php elseif ( $dtjwpt_ticket->status == "1" ) : ?>
										<?php _e('Closed', 'wp-triage'); ?>
									<?php endif; ?>
								</td>
							</tr>

						<?php endif; ?>

					</tbody>

				</table>

			</form>

		</div>

	</div>

	<div id="dtjwpt-note" class="dtjwpt-box dtjwpt-notes-container">

		<h2 class="dtjwpt-title"><?php _e('Notes', 'wp-triage'); ?></h2>

		<div class="dtjwpt-content dtjwpt-notes">

			<div class="dtjwpt-notes-list">

				<?php if ( dtjwpt_get_ticket_notes($dtjwpt_ticket->ticket_id) !== false ) : ?>

					<form method="post">

						<?php $dtjwpt_get_notes = dtjwpt_get_ticket_notes($dtjwpt_ticket->ticket_id); ?>
						<?php foreach ( $dtjwpt_get_notes as $dtjwpt_note ) : ?>

							<?php

								if ( $dtjwpt_note->author_id != 0 ) {

									$dtjwpt_get_note_author = get_userdata($dtjwpt_note->author_id);
									$dtjwpt_note_author = $dtjwpt_get_note_author->data->display_name;
									$dtjwpt_note_avatar = get_avatar_url($dtjwpt_note->author_id);

								} else {

									$dtjwpt_note_author = '<abbr title="' . __('An automatic system message', 'wp-triage') . '">' . __('System', 'wp-triage') . '</abbr>';
									$dtjwpt_note_avatar = plugins_url('images/avatar.jpg', DTJWPT_IMAGES);

								}

							?>

							<article class="dtjwpt-note note-<?php echo $dtjwpt_note->note_id; ?>" id="note-<?php echo $dtjwpt_note->note_id; ?>">
								<div class="dtjwpt-note-meta">
									<div class="dtjwpt-meta-avatar">
										<img src="<?php echo $dtjwpt_note_avatar; ?>" alt="<?php _e('User Avatar', 'wp-triage'); ?>" />
									</div>
									<div class="dtjwpt-meta-timestamp">
										<?php echo $dtjwpt_note_author; ?> &bull; <abbr title="<?php echo date("jS F Y H:i:s a", strtotime($dtjwpt_ticket->create_date)); ?>"><?php echo date("Y/m/d", strtotime($dtjwpt_ticket->create_date)); ?></abbr>
										<?php if ( dtjwpt_can_modify_note($dtjwpt_note->note_id) ) : ?>
											<a class="icon dtjwpt-button-delete-note" data-note="<?php echo $dtjwpt_note->note_id; ?>" title="<?php _e('Delete this note', 'wp-triage'); ?>"><?php _e('Delete', 'wp-triage'); ?></a>
										<?php endif; ?>
									</div>
								</div>
								<div class="dtjwpt-note-comment">
									<?php echo stripslashes(sanitize_text_field($dtjwpt_note->content)); ?>
								</div>
							</article>

						<?php endforeach; ?>

						<?php $dtjwpt_delete_note_nonce = wp_create_nonce('dtjwpt_delete_note_nonce'); ?>
						<input type="hidden" name="dtjwpt_delete_note_nonce" class="dtjwpt_delete_note_nonce" value="<?php echo $dtjwpt_delete_note_nonce; ?>" />

					</form>

				<?php else : ?>

					<p class="dtjwpt-no-notes"><em><?php _e('No one has posted any notes in response to this ticket yet.', 'wp-triage'); ?></em></p>

				<?php endif; ?>

			</div>

		</div>

		<?php if ( dtjwpt_can_manage_notes() ) : ?>

			<div class="dtjwpt-content dtjwpt-create-note-form">

				<form method="post">
						
					<table class="dtjwpt-form form-table">

						<tbody>

							<tr>
								<td>
									<textarea id="dtjwpt_note_comment" class="dtjwpt_note_comment"></textarea>
								</td>
							</tr>

						</tbody>

						<tfoot>

							<tr>
								<td>

									<p><strong><?php _e('Post a new note to this ticket for other participants to read.', 'wp-triage'); ?></strong></p>

									<a class="button button-primary dtjwpt-button-create-note"><?php _e('Add New Note', 'wp-triage'); ?></a>

									<?php if ( dtjwpt_can_modify_ticket($dtjwpt_ticket->ticket_id) ) : ?>
										<?php if ( $dtjwpt_ticket->status == "0" || $dtjwpt_ticket->status == "1" ) : ?>
											<a class="button button-primary dtjwpt-button-create-note dtjwpt-alter-status">
												<?php if ( $dtjwpt_ticket->status == "0" ) : ?>
													<?php _e('Add Note and Close Ticket', 'wp-triage'); ?>
												<?php elseif ( $dtjwpt_ticket->status == "1" ) : ?>
													<?php _e('Add Note and Open Ticket', 'wp-triage'); ?>
												<?php endif; ?>
											</a>
										<?php endif; ?>
									<?php endif; ?>

									<span class="spinner dtjwpt-create-note-spinner"></span>

									<input type="hidden" name="dtjwpt_ticket_id" class="dtjwpt_ticket_id" value="<?php echo $dtjwpt_ticket->ticket_id; ?>" />

									<?php $dtjwpt_create_note_nonce = wp_create_nonce('dtjwpt_create_note_nonce'); ?>
									<input type="hidden" name="dtjwpt_create_note_nonce" class="dtjwpt_create_note_nonce" value="<?php echo $dtjwpt_create_note_nonce; ?>" />

								</td>
							</tr>

						</tfoot>

					</table>

				</form>

			</div>

		<?php endif; ?>

	</div>

<?php else : ?>

	<?php if ( $dtjwpt_show_project ) : ?>

		<?php if ( dtjwpt_can_manage_tickets() ) : ?>

			<div class="notice notice-success is-dismissible dtjwpt-notice-create-ticket-success" style="display: none;">
				<p><strong><?php _e('The ticket has been successfully created!', 'wp-triage'); ?></strong></p>
			</div>

			<div class="notice notice-error is-dismissible dtjwpt-notice-create-ticket-failure" style="display: none;">
				<p><strong><?php _e('The ticket could not be added, please try again.', 'wp-triage'); ?></strong></p>
			</div>

		<?php endif; ?>

		<?php if ( dtjwpt_can_modify_project($dtjwpt_project->project_id) ) : ?>

			<div class="notice notice-success is-dismissible dtjwpt-notice-update-project-success" style="display: none;">
				<p><strong><?php _e('The project has been successfully updated!', 'wp-triage'); ?></strong></p>
			</div>

			<div class="notice notice-error is-dismissible dtjwpt-notice-update-project-failure" style="display: none;">
				<p><strong><?php _e('The project could not be updated, please try again.', 'wp-triage'); ?></strong></p>
			</div>

		<?php endif; ?>

		<div class="dtjwpt-box dtjwpt-projects">

			<h2 class="dtjwpt-title dtjwpt-projects-title">
				<span><?php _e('Project:', 'wp-triage'); ?> <?php echo stripslashes($dtjwpt_project->name); ?></span>
				<?php if ( dtjwpt_can_manage_tickets() ) : ?>
					<a class="button button-primary dtjwpt-create-ticket-toggle"><?php _e('New Ticket', 'wp-triage'); ?></a>
				<?php endif; ?>
				<?php if ( dtjwpt_can_modify_project($dtjwpt_project->project_id) ) : ?>
					<a class="button button-primary dtjwpt-update-project-toggle"><?php _e('Edit Project', 'wp-triage'); ?></a>
				<?php endif; ?>
				<a href="<?php echo esc_url( admin_url('admin.php?page=dtjwpt_triage&triage=projects') ); ?>" class="button button-secondary"><?php _e('Return to Projects', 'wp-triage'); ?></a>
			</h2>

			<?php if ( dtjwpt_can_manage_tickets() ) : ?>

				<div class="dtjwpt-content dtjwpt-create-ticket-form" style="display: none;">

					<form method="post">

						<table class="dtjwpt-form form-table">

							<tbody>

								<tr>
									<th><label for="dtjwpt_ticket_name"><?php _e('Ticket Name', 'wp-triage'); ?> <span class="dtjwpt-required">*</span></label></th>
									<td>
										<input type="text" id="dtjwpt_ticket_name" class="dtjwpt_ticket_name" />
									</td>
								</tr>

								<tr>
									<th><label for="dtjwpt_ticket_description"><?php _e('Ticket Description', 'wp-triage'); ?></label></th>
									<td>
										<textarea id="dtjwpt_ticket_description" class="dtjwpt_ticket_description"></textarea>
									</td>
								</tr>

								<tr>
									<th><label for="dtjwpt_ticket_assignee"><?php _e('Assigned to', 'wp-triage'); ?></label></th>
									<td>
										<?php $dtjwpt_get_users = get_users(); ?>
										<select id="dtjwpt_ticket_assignee" class="dtjwpt_ticket_assignee">
											<option value="0" selected="selected"><?php _e('Unassigned', 'wp-triage'); ?></option>
											<?php foreach ( $dtjwpt_get_users as $dtjwpt_user ) : ?>
												<option value="<?php echo $dtjwpt_user->ID; ?>"><?php echo $dtjwpt_user->data->display_name; ?></option>
											<?php endforeach; ?>
										</select>
									</td>
								</tr>

								<tr>
									<th><label for="dtjwpt_ticket_component"><?php _e('Component', 'wp-triage'); ?></label></th>
									<td>
										<?php $dtjwpt_get_components = dtjwpt_get_component(0); ?>
										<select id="dtjwpt_ticket_component" class="dtjwpt_ticket_component">
											<option value="0" selected="selected"><?php _e('None', 'wp-triage'); ?></option>
											<?php foreach ( $dtjwpt_get_components as $dtjwpt_component ) : ?>
												<option value="<?php echo $dtjwpt_component->component_id; ?>"><?php echo $dtjwpt_component->name; ?></option>
											<?php endforeach; ?>
										</select>
									</td>
								</tr>

								<tr>
									<th><label for="dtjwpt_ticket_type"><?php _e('Type', 'wp-triage'); ?> <span class="dtjwpt-required">*</span></label></th>
									<td>
										<select id="dtjwpt_ticket_type" class="dtjwpt_ticket_type">
											<option value="0" selected="selected"><?php _e('&mdash; Please Select &mdash;', 'wp-triage'); ?></option>
											<option value="b"><?php _e('Bug Report', 'wp-triage'); ?></option>
											<option value="s"><?php _e('Support Query', 'wp-triage'); ?></option>
											<option value="f"><?php _e('Feature Request', 'wp-triage'); ?></option>
										</select>
									</td>
								</tr>

								<tr>
									<th><label for="dtjwpt_ticket_priority"><?php _e('Priority', 'wp-triage'); ?> <span class="dtjwpt-required">*</span></label></th>
									<td>
										<select id="dtjwpt_ticket_priority" class="dtjwpt_ticket_priority">
											<option value="0" selected="selected"><?php _e('&mdash; Please Select &mdash;', 'wp-triage'); ?></option>
											<option value="1"><?php _e('Trivial', 'wp-triage'); ?></option>
											<option value="2"><?php _e('Minor', 'wp-triage'); ?></option>
											<option value="3"><?php _e('Major', 'wp-triage'); ?></option>
										</select>
									</td>
								</tr>

								<tr>
									<th></th>
									<td>

										<p><strong><?php _e('All fields marked <span class="dtjwpt-required">*</span> are required.', 'wp-triage'); ?></strong></p>

										<a class="button button-primary dtjwpt-button-create-ticket"><?php _e('Create Ticket', 'wp-triage'); ?></a>
										<span class="spinner dtjwpt-create-ticket-spinner"></span>

										<input type="hidden" name="dtjwpt_project_id" class="dtjwpt_project_id" value="<?php echo $dtjwpt_project->project_id; ?>" />

										<?php $dtjwpt_create_ticket_nonce = wp_create_nonce('dtjwpt_create_ticket_nonce'); ?>
										<input type="hidden" name="dtjwpt_create_ticket_nonce" class="dtjwpt_create_ticket_nonce" value="<?php echo $dtjwpt_create_ticket_nonce; ?>" />

									</td>
								</tr>

							</tbody>

						</table>

					</form>

				</div>

				<?php if ( dtjwpt_can_modify_project($dtjwpt_project->project_id) ) : ?>

					<div class="dtjwpt-content dtjwpt-update-project-form" style="display: none;">

						<form method="post">

							<table class="dtjwpt-form form-table">

								<tbody>

									<tr>
										<th><?php _e('Project Name', 'wp-triage'); ?> <span class="dtjwpt-required">*</span></th>
										<td>
											<input type="text" id="dtjwpt_project_name" class="dtjwpt_project_name" value="<?php echo stripslashes($dtjwpt_project->name); ?>" />
										</td>
									</tr>

									<tr>
										<th><?php _e('Project Owner', 'wp-triage'); ?></th>
										<td>
											<?php $dtjwpt_get_users = get_users(); ?>
											<select id="dtjwpt_project_owner" class="dtjwpt_project_owner">
												<option value="0"><?php _e('Unassigned', 'wp-triage'); ?></option>
												<?php foreach ( $dtjwpt_get_users as $dtjwpt_user ) : ?>
													<option value="<?php echo $dtjwpt_user->ID; ?>"<?php if ( $dtjwpt_project->owner_id == $dtjwpt_user->ID ) : ?> selected="selected"<?php endif; ?>><?php echo $dtjwpt_user->data->display_name; ?></option>
												<?php endforeach; ?>
											</select>
										</td>
									</tr>

									<tr>
										<th></th>
										<td>

											<p><strong><?php _e('All fields marked <span class="dtjwpt-required">*</span> are required.', 'wp-triage'); ?></strong></p>

											<a class="button button-primary dtjwpt-button-update-project"><?php _e('Save Project', 'wp-triage'); ?></a>
											<span class="spinner dtjwpt-update-project-spinner"></span>

											<input type="hidden" name="dtjwpt_project_id" class="dtjwpt_project_id" value="<?php echo $dtjwpt_project->project_id; ?>" />

											<?php $dtjwpt_update_project_nonce = wp_create_nonce('dtjwpt_update_project_nonce'); ?>
											<input type="hidden" name="dtjwpt_update_project_nonce" class="dtjwpt_update_project_nonce" value="<?php echo $dtjwpt_update_project_nonce; ?>" />

										</td>
									</tr>

								</tbody>

							</table>

						</form>

					</div>

				<?php endif; ?>

			<?php endif; ?>

		</div>

		<div class="dtjwpt-table">

			<div class="dtjwpt-content-table dtjwpt_tickets_table">

				<form method="post">

					<?php $ticket_table->display(); ?>
				
				</form>

			</div>

		</div>

	<?php else : ?>

		<div class="dtjwpt-box dtjwpt-triage">

			<h2 class="dtjwpt-title"><?php _e('Not Found', 'wp-triage'); ?></h2>

			<div class="dtjwpt-content">

				<p><?php _e('There aren&#39;t any projects or tickets to show you right now, sorry.', 'wp-triage'); ?></p>

			</div>

		</div>

	<?php endif; ?>

<?php endif; ?>

