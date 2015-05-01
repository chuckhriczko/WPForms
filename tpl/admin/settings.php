<div id="wpforms-container">
				<h2>Settings</h2>
				<?php
				if (isset($message) && !empty($message)){
								?><div class="wpforms-message"><?php echo $message; ?></div><?
				}
				?>
								
				<form id="wpforms-settings-form" method="post" action="<?php echo $this->url; ?>">
								<div class="form-block">
												<h3>Icon Settings</h3>
												<label for="wpforms-autoshow-content">
																<input type="checkbox" id="wpforms-autoshow-content" name="wpforms-autoshow-content" value="1"<?php echo $this->settings['wpforms-autoshow-content']==1 ? ' checked="checked"' : ''; ?> />
																Automatically show forms icon in content
																<p class="wpforms-message">If this option is selected, a forms icon will be shown in the page/post's content automatically. Note, this is only applicable for pages or posts that have forms associated with them. No icon will be shown if forms are not associated.</p>
												</label>
								</div>
								<div class="form-block">
												<label for="wpforms-icon">
																<label>Icon Image (24x24 Recommended)</label>
																<a href="#wpforms-icon-link" title="Choose image file..." class="wpforms-img-link"><img src="<?php echo $this->settings['wpforms-icon']; ?>" alt="Icon Image" width="<?php echo $width; ?>" height="<?php echo $height; ?>" /></a>
																<input type="text" id="wpforms-icon" name="wpforms-icon" value="<?php echo $this->settings['wpforms-icon']; ?>" />
																<input type="button" value="Choose File..." class="button button-secondary" id="wpforms-choose-file-button" name="wpforms-choose-file-button" />
												</label>
								</div>
								<input type="submit" value="Save Settings" class="button button-primary" id="wpforms-save-settings-button" name="wpforms-save-settings-button">
								<input type="button" class="button button-secondary" id="wpforms-convert-to-posts" name="wpforms-convert-to-posts" value="Convert To Posts" />
				</form>
</div>