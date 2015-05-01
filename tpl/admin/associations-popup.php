<div id="wpforms-container" style="border-radius: 0; box-shadow: none; margin: 0; padding: 0; width: 100%;">
				<h2>Associations</h2>
				<?php
				if (isset($message) && !empty($message)){
								?><div class="wpforms-message"><?php echo $message; ?></div><?
				}
				?>
				
				<form id="wpforms-associations-form" method="post" action="<?php echo $this->url; ?>">
								<h3>Select A Form</h3>
								<select id="wpforms-form-id" name="wpforms-form-id">
												<?php
												//Loop through all the forms and build the options section
												foreach($this->forms as $key=>$form){
																?><option value="<?php echo $form->id; ?>"><?php echo $form->title; ?></option><?php
												}
												?>
								</select>
								<button type="button" id="wpforms-associations-associate" class="button">Associate</button>
								
								<div id="wpforms-associations">
												<table id="wpforms-listing" class="wp-list-table">
																<thead>
																				<tr>
																								<th class="manage-column column-cb check-column"><label for="cb-select-all" class="screen-reader-text">Select All</label><input type="checkbox" id="cb-select-all" /></th>
																								<th>Post Title</th>
																				</tr>
																</thead>
																<tbody>
																				<?php
																				foreach($this->associations as $key=>$post_obj){
																								?>
																								<tr>
																												<th class="check-column" scope="row">
																																<label for="cb-select-<?php echo $post_obj->id; ?>" class="screen-reader-text">Select Form</label>
																																<input type="checkbox" value="<?php echo $post_obj->id; ?>" name="wpforms[]" id="cb-select-<?php echo $post_obj->id; ?>">
																																<div class="locked-indicator"></div>
																												</th>
																												<td><?php echo $post_obj->title; ?></td>
																								</tr>
																								<?php
																				}
																				?>
																</tbody>
												</table>
								</div>
								<input type="hidden" id="wpforms-post-id" name="wpforms-post-id" value="<?php echo $post_id; ?>" />
				</form>
				<br class="clear" />
</div>