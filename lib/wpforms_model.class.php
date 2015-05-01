<?php
/*******************************************************************************
 * Define our model class for data operations
 ******************************************************************************/
class WPForms_Model{
				/*******************************************************************************
				 * Get all the forms in our plugin table
				 ******************************************************************************/
				public function get_forms($id = null, $count = -1, $offset = 0, $order_by = 'title', $order = 'ASC', $reverse_num = false){
								global $wpdb;
								
								//Are we getting a single ID or a number of forms?
								if (empty($id)){
												//Get all the forms
												$forms = $wpdb->get_results('SELECT * FROM '.$wpdb->prefix.WPFORMS_DB_TABLE.' ORDER BY '.$order_by.' '.$order.' '.(empty($count) || $count<1 ? '' : 'LIMIT '.$offset.', '.$count));
												
												//Check if we should reverse the number sorting
												if ($reverse_num){
																//Init temporary arrays
																$letters = array();
																$numbers = array();
																
																//Loop through each form
																foreach($forms as $form){
																				//If the first character of the form title is a number push it to the numbers array
																				//Otherwise it goes in the letters array
																				if (is_numeric(substr($form->title, 0, 1))) array_push($numbers, $form); else array_push($letters, $form);
																}
																
																//Finally we merge both arrays into the forms array
																$forms = array_merge($letters, $numbers);
												}
								} else {
												//Get a single form
												$forms = $wpdb->get_row('SELECT * FROM '.$wpdb->prefix.WPFORMS_DB_TABLE.' WHERE id = '.$id);
								}
								
								//Return the forms
								return $forms;
				}
				
				/*******************************************************************************
				 * Search the forms database table
				 ******************************************************************************/
				public function get_forms_by_term($term = null, $count = -1, $offset = 0, $order_by = 'title', $order = 'ASC'){
								global $wpdb;
								
								//Init forms variable
								$forms = array();
								
								//Get forms
								if (!empty($term)) $forms = $wpdb->get_results('SELECT * FROM '.$wpdb->prefix.WPFORMS_DB_TABLE.' WHERE title LIKE "%'.$term.'%" OR description LIKE "%'.$term.'%" ORDER BY '.$order_by.' '.$order.' '.(empty($count) || $count<1 ? '' : 'LIMIT '.$offset.', '.$count));
								
								//Return the forms
								return $forms;
				}
				
				/*******************************************************************************
				 * Get all the associations in our table
				 ******************************************************************************/
				public function get_associations_by_form_id($form_id){
								global $wpdb;

								//Get all the forms from the database
								return $wpdb->get_results('SELECT '.$wpdb->posts.'.* FROM '.$wpdb->postmeta.' JOIN '.$wpdb->posts.' ON '.$wpdb->posts.'.id = '.$wpdb->postmeta.'.post_id WHERE '.$wpdb->postmeta.'.meta_value = '.$form_id.' AND '.$wpdb->postmeta.'.meta_key LIKE "wpforms_association_%"');
				}
				
				/*******************************************************************************
				 * Get all the attachments based on post ID
				 ******************************************************************************/
				public function get_associations_by_post_id($post_id = 0){
								global $wpdb;
								
								//Get all the forms associated with this post and return them
								return $wpdb->get_results('SELECT '.$wpdb->prefix.WPFORMS_DB_TABLE.'.id, '.$wpdb->prefix.WPFORMS_DB_TABLE.'.title, '.$wpdb->prefix.WPFORMS_DB_TABLE.'.filename FROM '.$wpdb->postmeta.' JOIN '.$wpdb->prefix.WPFORMS_DB_TABLE.' ON '.$wpdb->prefix.WPFORMS_DB_TABLE.'.id = '.$wpdb->postmeta.'.meta_value WHERE '.$wpdb->postmeta.'.post_id = '.$post_id.' AND '.$wpdb->postmeta.'.meta_key LIKE "wpforms_association_%"');
				}
				
				/*******************************************************************************
				 * Get all the pages in the posts table
				 ******************************************************************************/
				public function get_pages(){
								//Get all the subpages of the passed page ID
								return get_pages(array(
												'posts_per_page' => -1,
												'post_type' => 'page',
												'sort_column' => 'post_parent'
								));
				}
				
				/*******************************************************************************
				 * Adds a form to the database
				 ******************************************************************************/
				public function insert_form($post_id = 0, $filename = '', $name = '', $description = ''){
								global $wpdb;
								
								//Get all the forms from the database
								return $wpdb->insert($wpdb->prefix.WPFORMS_DB_TABLE, array(
												'post_id' => $post_id,
												'title' => $name,
												'description' => $description,
												'filename' => $filename
								));
				}
				
				/*******************************************************************************
				 * Associates posts with forms
				 ******************************************************************************/
				public function add_associations($form_id, $pages = null){
								global $wpdb;
								
								//Prepare arguments
								$pages = empty($pages) ? array() : $pages;
								
								//Make an array if this is a single argument
								$pages = !is_array($pages) && !is_object($pages) ? array($pages) : $pages;
								
								//Loop through each page
								foreach($pages as $page){
												//Check if this page is already associated with the passed form
												$query = $wpdb->get_results('SELECT COUNT(meta_id) AS count FROM '.$wpdb->postmeta.' WHERE post_id = '.$page.' AND meta_key LIKE "wpforms_association_%" AND meta_value = '.$form_id);
												
												//If the page was not found, add it
												if ($query[0]->count<1 || empty($query[0]->count)){
																$wpdb->insert($wpdb->postmeta, array(
																				'post_id' => $page,
																				'meta_key' => 'wpforms_association_'.strtotime('now'),
																				'meta_value' => $form_id
																));
												}
								}
				}
				
				/*******************************************************************************
				 * Gets all the plugin's settings
				 ******************************************************************************/
				public function get_settings(){
								global $wpforms;
								
								//Init the default settings
								$settings = array(
												'wpforms-autoshow-content' => true,
												'wpforms-icon' => str_replace('/lib', '', plugin_dir_url(__FILE__)).'assets/images/forms-icon.png'
								);
								
								//Loop through defaults and get settings from WP database
								foreach($settings as $key=>$setting){
												$settings[$key] = get_option($key, $setting);
								}
								
								return $settings;
				}
				
				/*******************************************************************************
				 * Gets all the plugin's settings
				 ******************************************************************************/
				public function update_settings($post, &$settings){
								if (!empty($post)){
												//Process $_POST data
												$post['wpforms-autoshow-content'] = isset($post['wpforms-autoshow-content']) ? $post['wpforms-autoshow-content'] : 0;
												
												//Update options
												foreach($settings as $key=>$setting){
																//Loop through all the post data
																foreach($post as $post_key=>$item){
																				//If the post data exists for this key, change the settings array
																				//and update the option in the database
																				if (array_key_exists($post_key, $settings)){
																								$settings[$key] = $item;
																								update_option($post_key, $item);
																				}
																}
												}
								}
								
								return true;
				}
				
				/*******************************************************************************
				 * Upload the form to the server
				 ******************************************************************************/
				public function upload_form($files){
								//Allowed filetypes
								$allowed_types = array('.gif' => 'image/gif', '.jpg' => 'image/jpeg', '.pdf' => 'application/pdf', '.doc' => 'application/msword', '.txt' => 'text/plain');
								
								//Get the maximum upload size
								$max_upload_size = (min((int)ini_get('upload_max_filesize'), (int)ini_get('post_max_size'), (int)ini_get('memory_limit'))*1024)*1024;
												
								//Validate the file type
								//if (in_array($files['wpforms-upload-file']['type'], $allowed_types)){
												//Validate the file size
												if ($files['wpforms-upload-file']['size']<=$max_upload_size){
																//Get the extension of the filename
																$ext = pathinfo($files['wpforms-upload-file']['name'], PATHINFO_EXTENSION);
																
																//Generate unique filename for the chosen file
																$filename = str_replace(array('.'.$ext, $ext), '', $files['wpforms-upload-file']['name']).'-'.strtotime('now').'.'.$ext;
																
																//Move the uploaded file to the upload directory
																move_uploaded_file($files['wpforms-upload-file']['tmp_name'], WPFORMS_DIR_UPLOAD.'/'.$filename);
												} else {
																$message = 'The file you chose to upload exceeds the maximum size allowed. Please choose a file that is no larger than '.(($max_upload_size/1024)/1024).'MB.';
												}
												
												return isset($filename) ? $filename : $message;
								/*} else {
												$message = 'The file you chose to upload was not the correct filetype. Please choose one of the following filetypes: '.implode(', ', array_keys($allowed_types)).'.';
								}*/
				}
}
?>