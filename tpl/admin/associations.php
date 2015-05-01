<div id="wpforms-container">
				<h2>Associations</h2>
				<?php
				if (isset($message) && !empty($message)){
								?><div class="wpforms-message"><?php echo $message; ?></div><?
				}
				?>
				
				<div id="wpforms-associations-aside">
								<form method="post" action="<?php echo $this->url; ?>">
												<h3>Pages</h3>
												<p>Select the pages below that you would like to associate with the selected form.</p>
												<ul>
																<?php
																foreach($this->pages as $page){
																				$class_name = isset($this->pages[$key+1]) && $this->pages[$key+1]->post_parent!=$page->post_parent ? 'child' : '';
																				?><li<?php echo !empty($class_name) ? ' class="'.$class_name.'"' : ''; ?>><label for="wpforms-pages"><input type="checkbox" id="wpforms-pages-<?php echo $page->ID; ?>" name="wpforms-pages[]" value="<?php echo $page->ID; ?>" /> <?php echo $page->post_title; ?></label></li><?php
																}
																?>
												</ul>
												<input type="submit" value="Add" class="button button-primary" id="wpforms-add-association-button" name="wpforms-add-association-button">
												<input type="hidden" name="wpforms-hid-func-name" value="add_associations" />
												<input type="hidden" id="wpforms-hid-form-id" name="wpforms-hid-form-id" value="<?php echo isset($form_id) ? $form_id : 0; ?>" />
								</form>
				</div>
				
				<form id="wpforms-associations-form" method="post" action="<?php echo $this->url; ?>">
								<h3>Select A Form</h3>
								<select id="wpforms-form-id" name="wpforms-form-id">
												<?php
												//Loop through all the forms and build the options section
												foreach($this->forms as $key=>$form){
																?><option value="<?php echo $form->id; ?>"<?php echo $form_id==$form->id ? ' selected="selected"' : ''; ?>><?php echo $form->title; ?></option><?php
												}
												?>
								</select>
								
								<div id="wpforms-associations">
												<table id="wpforms-listing" class="wp-list-table">
																<thead>
																				<tr>
																								<th class="manage-column column-cb check-column"><label for="cb-select-all" class="screen-reader-text">Select All</label><input type="checkbox" id="cb-select-all" /></th>
																								<th>Post Title</th>
																								<th>Actions</th>
																				</tr>
																</thead>
																<tbody>
																				<?php
																				foreach($this->associations as $key=>$post){
																								?>
																								<tr>
																												<th class="check-column" scope="row">
																																<label for="cb-select-<?php echo $post->ID; ?>" class="screen-reader-text">Select Form</label>
																																<input type="checkbox" value="<?php echo $post->ID; ?>" name="wpforms[]" id="cb-select-<?php echo $post->ID; ?>">
																																<div class="locked-indicator"></div>
																												</th>
																												<td><a href="<?php echo get_permalink($post->ID); ?>" title="View <?php echo $post->post_title; ?>" target="_blank"><?php echo $post->post_title; ?></a></td>
																												<td><a href="#<?php echo $post->ID; ?>" class="menu-delete" title="Delete">Delete</a></td>
																								</tr>
																								<?php
																				}
																				?>
																</tbody>
												</table>
												<div id="wpforms-bulk-actions">
																<label for="wpforms-bulk-actions-select">Bulk Actions</label>
																<select id="wpforms-bulk-actions-select" name="wpforms-bulk-actions-select">
																				<option value="">Select...</option>
																				<option value="delete_association">Delete</option>
																</select>
												</div>
												<input type="hidden" id="wpforms-hid-url" name="wpforms-hid-url" value="<?php echo admin_url('admin.php?page=wpforms-associations'); ?>" />
												<input type="hidden" id="wpforms-hid-func-name" name="wpforms-hid-func-name" value="add_associations" />
								</div>
				</form>
				<br class="clear" />
</div>