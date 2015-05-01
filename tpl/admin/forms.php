<div id="wpforms-container">
				<h2>Forms</h2>
				<div id="wpforms-search">
								<form method="post" action="<?php echo $this->url; ?>">
												<input type="submit" id="wpforms-search-button" name="wpforms-search-button" value="Search" />
												<input type="textbox" id="wpforms-query" name="wpforms-query"<?php echo isset($_POST['wpforms-query']) ? $_POST['wpforms-query'] : 'Search...'; ?> />
								</form>
				</div>
				
				<form method="post" action="<?php echo $this->url; ?>">
								<table id="wpforms-listing" class="wp-list-table">
												<thead>
																<tr>
																				<th class="manage-column column-cb check-column"><label for="cb-select-all" class="screen-reader-text">Select All</label><input type="checkbox" id="cb-select-all" /></th>
																				<th>Title</th>
																				<th>Description</th>
																				<th>Date Created</th>
																				<th>Actions</th>
																</tr>
												</thead>
												<tbody>
																<?php
																foreach($this->forms as $key=>$form){
																				?>
																				<tr>
																								<th class="check-column" scope="row">
																												<label for="cb-select-<?php echo $form->id; ?>" class="screen-reader-text">Select Form</label>
																												<input type="checkbox" value="<?php echo $form->id; ?>" name="wpforms[]" id="cb-select-<?php echo $form->id; ?>">
																												<div class="locked-indicator"></div>
																								</th>
																								<td><?php echo $form->title; ?></td>
																								<td><?php echo $form->description; ?></td>
																								<td>
																												<?php echo date(WPFORMS_DATE_USER, strtotime($form->date_created)); ?> @ <?php echo date(WPFORMS_TIME_USER, strtotime($form->date_created)); ?>
																								</td>
																								<td>
																												<a href="#<?php echo $form->id; ?>" class="menu-delete" title="Delete">Delete</a> |
																												<a href="<?php echo WPFORMS_URL_UPLOAD.$form->filename; ?>" target="_blank" title="View">View</a> | 
																												<a href="<?php echo $this->url; ?>-add&mode=edit&form_id=<?php echo $form->id; ?>" class="menu-edit" title="Edit...">Edit...</a><br />
																												<a href="<?php echo $this->url; ?>-associations&form_id=<?php echo $form->id; ?>" title="Edit Associations...">Associations...</a>
																								</td>
																				</tr>
																				<?php
																}
																?>
												</tbody>
								</table>
								<?php
												if (empty($this->forms)){
																?><h2>No forms were found in your search. Please try a different query or <a href="<?php echo $this->url; ?>-add" title="Add Form">add a form</a>.</h2><div class="clear"></div><?php
												} else {
								?>
												<div id="wpforms-bulk-actions">
																<label for="wpforms-bulk-actions-select">Bulk Actions</label>
																<select id="wpforms-bulk-actions-select" name="wpforms-bulk-actions-select">
																				<option value="">Select...</option>
																				<option value="delete">Delete</option>
																</select>
												</div>
								<?php } ?>
								<input type="hidden" id="wpforms-hid-func-name" name="wpforms-hid-func-name" value="" />
				</form>
</div>