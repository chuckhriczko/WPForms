<?php
				//Define our DB constants
				define('WPFORMS_DB_TABLE', 'wpforms');
				
				//Directories
				define('WPFORMS_DIR_UPLOAD', WP_CONTENT_DIR.'/forms/');
				define('WPFORMS_URL_UPLOAD', WP_CONTENT_URL.'/forms/');
				
				//Dates
				define('WPFORMS_DATE_USER', 'F jS, Y');
				define('WPFORMS_TIME_USER', 'g:ha');
				
				//HTML Constants
				define('WPFORMS_HTML_ANIM_DOTS', '<div id="wpforms-anim-dots"><div id="wpforms-anim-dots_1" class="wpforms-anim-dots"></div><div id="wpforms-anim-dots_2" class="wpforms-anim-dots"></div><div id="wpforms-anim-dots_3" class="wpforms-anim-dots"></div></div>');
				
				//Messages
				define('WPFORMS_MSG_UPLOAD_SUCCESS', 'Your form has successfully been uploaded!');
				define('WPFORMS_MSG_UPLOAD_FAILED', 'Your form was unable to be uploaded! Please try again.');
?>