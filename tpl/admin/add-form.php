<div id="wpforms-container">
				<h2><?php echo isset($_GET['mode']) ? str_replace(array('-', '_'), ' ', ucwords($_GET['mode'])) : 'Add'; ?> Form</h2>
				<?php
				if (isset($message) && !empty($message)){
								?><div class="wpforms-message"><?php echo $message; ?></div><?
				}
				?>
				<div id="wpforms-upload">
								<form method="post" action="<?php echo $this->url; ?>" enctype="multipart/form-data">
												<label for="wpforms-upload-name">Title</label>
												<input type="text" id="wpforms-upload-name" name="wpforms-upload-name"<?php echo isset($form->title) ? ' value="'.$form->title.'"' : ''; ?> />
												<label for="wpforms-upload-description">Description</label>
												<textarea id="wpforms-upload-description" name="wpforms-upload-description"><?php echo isset($form->description) ? $form->description : ''; ?></textarea>
												<input type="file" id="wpforms-upload-file" name="wpforms-upload-file"<?php echo isset($form->filename) ? ' value="'.$form->filename.'"' : ''; ?> />
												<?php if (isset($_GET['mode'])) if ($_GET['mode']=='edit') {?>
																<input type="hidden" id="wpforms-mode" name="wpforms-mode" value="<?php echo $_GET['mode']; ?>" />
																<input type="hidden" id="wpforms-form-id" name="wpforms-form-id" value="<?php echo $form->id; ?>" />
																<input type="hidden" id="wpforms-post-id" name="wpforms-post-id" value="<?php echo $form->post_id; ?>" />
												<?php } ?>
												<input type="submit" value="<?php echo isset($_GET['mode']) ? $_GET['mode']=='edit' ? 'Update' : 'Upload' : 'Upload'; ?> Form" class="button button-primary" id="wpforms-upload-button" name="wpforms-upload-button">
												<?php //echo WPFORMS_HTML_ANIM_DOTS; ?>
								</form>
				</div>
</div>