<?php global $swt; ?>
<div class="wpforms-icon-container">
				<h3 class="wpforms-icon">
								<a href="#" title="View Forms">Forms <img src="<?php echo $swt->theme_path_uri; ?>/assets/images/icons/forms.png" alt="View" width="20" height="26" /></a>
				</h3>
				<div class="wpforms-popup">
								<div class="wpforms-popup-body">
												<ul>
																<?php
																foreach($this->associations as $form){
																				?><li class="form-<?php echo $form->id; ?>"><a href="<?php echo WPFORMS_URL_UPLOAD.$form->filename; ?>" title="<?php echo $form->title; ?>" target="_blank"><?php echo $form->title; ?></a></li><?php
																}
																?>
												</ul>
								</div>
				</div>
</div>