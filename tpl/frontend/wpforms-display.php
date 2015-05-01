<ul>
				<?php
				foreach($forms as $form){
								?><li class="wpform-id-<?php echo $form->id; ?>"><a href="<?php echo WPFORMS_URL_UPLOAD.$form->filename; ?>" title="<?php echo $form->title; ?>" target="_blank"><?php echo $form->title; ?></a></li><?php
				}
				?>
</ul>