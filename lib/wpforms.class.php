<?php
require_once(__DIR__.'/wpforms_model.class.php'); //Make sure we include our model for data operations

/*******************************************************************************
 * Define our initial class
 ******************************************************************************/
class WPForms{
				//Instantiate our public variables
				public $model, $plugin_path, $plugin_uri, $url, $post, $pages, $associations, $forms, $settings, $debug = false;
				
				//Instantiate our protected variables
				protected static $instance = NULL;
				
				/*******************************************************************************
				 * Instantiate our constructor
				 ******************************************************************************/
				public function __construct(){
								//Call the init function
								$this->init();
				}
				
				/*******************************************************************************
				 * Allows our views to access our functions
				 ******************************************************************************/
				public static function get_instance(){
        //Create an instance of this object and return it
        return NULL === self::$instance and self::$instance = new self;
    }
				
				/*******************************************************************************
				 * Perform initialization functions
				 ******************************************************************************/
				public function init(){
								//Enable debugging if the flag is set to true
								if ($this->debug) $this->init_debugging();
								
								//Init upload path
								$this->init_upload_path();
								
								//Init paths
								$this->plugin_path = __DIR__.'/..';
								$this->plugin_uri = str_replace('/lib', '', plugin_dir_url(__FILE__));
								$this->url = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS']!=='off' || $_SERVER['SERVER_PORT']==443) ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
								
								//Init our model
								$this->model = new WPForms_Model();
								
								//Get plugin settings
								$this->settings = $this->model->get_settings();
								
								//Init our hooks
								$this->init_hooks();
								
								//Init filters
								$this->init_filters();
								
								//Init shortcodes
								$this->init_shortcodes();
				}
				
				/*******************************************************************************
				 * Initializes and returns our upload path
				 ******************************************************************************/
				public function init_upload_path(){
								//Create directory if it does not exist
								if (!file_exists(WPFORMS_DIR_UPLOAD)) @mkdir(WPFORMS_DIR_UPLOAD, 0775);
								
								//Return the path
								return WPFORMS_DIR_UPLOAD;
    }
				
				/*******************************************************************************
				 * Initializes our hooks
				 ******************************************************************************/
				public function init_hooks(){
								//Register custom post type
								add_action('init', array(&$this, 'plugin_init'));
								
								//Get the post ID
								add_action('wp_head', array(&$this, 'wp_head'));
								
								//Init the admin menu
								add_action('admin_menu', array(&$this, 'admin_menu'));
								
								//Include scripts and styles for the admin
								add_action('admin_enqueue_scripts', array(&$this, 'admin_enqueue_scripts'));
								
								//Include scripts and styles for the frontend
								add_action('wp_enqueue_scripts', array(&$this, 'wp_enqueue_scripts'));
								
								//Ajax action for converting forms to posts
								add_action('wp_ajax_wpforms_convert_to_posts', array(&$this, 'wpforms_convert_to_posts_callback'));
								
								//Ajax action for associating forms with a post from the media button
								add_action('wp_ajax_associate_form', array(&$this, 'ajax_associate_form'));
								
								//Ajax action for converting forms to posts
								add_action('media_buttons', array(&$this, 'media_buttons'), 15);
    }
								
				/*******************************************************************************
				 * Initializes custom post type
				 ******************************************************************************/
				public function plugin_init(){
								//Register custom post type
								register_post_type('wpform', array(
												'label'               => 'wpform',
												'description'         => 'Forms',
												'labels'              => array(
																'name'                => 'Forms',
																'singular_name'       => 'Form',
																'menu_name'           => 'Forms',
																'parent_item_colon'   => 'Parent Form:',
																'all_items'           => 'All Forms',
																'view_item'           => 'View Form',
																'add_new_item'        => 'Add New Form',
																'add_new'             => 'New Form',
																'edit_item'           => 'Edit Form',
																'update_item'         => 'Update Form',
																'search_items'        => 'Search forms',
																'not_found'           => 'No forms found',
																'not_found_in_trash'  => 'No forms found in Trash',
												),
												'hierarchical'        => false,
												'public'              => true,
												'show_ui'             => false,
												'show_in_menu'        => false,
												'show_in_nav_menus'   => false,
												'show_in_admin_bar'   => false,
												'can_export'          => true,
												'has_archive'         => false,
												'exclude_from_search' => false,
												'publicly_queryable'  => true
								));
    }
				
				/*******************************************************************************
				 * Puts the post in a global plugin variable and get attachments
				 ******************************************************************************/
				public function wp_head(){
								global $wp_query;
								
								//Set the post object
								$this->post = isset($wp_query->post) ? $wp_query->post : new stdClass();
								
								//Get attachments for current post if the post object exists
								$this->associations = isset($this->post->ID) ? $this->model->get_associations_by_post_id($this->post->ID) : null;
    }
				
				/*******************************************************************************
				 * Inits the admin menus
				 ******************************************************************************/
				public function admin_menu(){
								//Add the main menu for this plugin
								add_menu_page('Forms', 'Forms', 'edit_posts', 'wpforms', array(&$this, 'admin_page_forms'), $this->plugin_uri.'assets/images/forms-icon-16.png', 58);
								
								//Add the "Add Form" menu option
								add_submenu_page('wpforms', 'Add Form', 'Add Form', 'edit_posts', 'wpforms-add', array(&$this, 'admin_page_add_form'));
								
								//Add the "Associations" menu options
								add_submenu_page('wpforms', 'Associations', 'Associations', 'edit_posts', 'wpforms-associations', array(&$this, 'admin_page_associations'));
								
								//Add the "Settings" menu options
								add_submenu_page('wpforms', 'Settings', 'Settings', 'edit_posts', 'wpforms-settings', array(&$this, 'admin_page_settings'));
    }
				
				/*******************************************************************************
				 * Initializes our filters
				 ******************************************************************************/
				public function init_filters(){
								if ($this->settings['wpforms-autoshow-content']==1) add_filter('the_content', array(&$this, 'the_content_filter'));
    }
				
				/*******************************************************************************
				 * Initializes content filter
				 ******************************************************************************/
				public function the_content_filter($content){
								return $this->wpforms_display_icon(null, $content);
    }
				
				/*******************************************************************************
				 * Initializes our shortcodes
				 ******************************************************************************/
				public function init_shortcodes(){
								add_shortcode('wpforms_display', array(&$this, 'wpforms_display'));
								add_shortcode('wpforms_display_icon', array(&$this, 'wpforms_display_icon'));
    }
				
				/*******************************************************************************
				 * Initializes debugging
				 ******************************************************************************/
				public function init_debugging(){
								//Enable errors
								ini_set('display_startup_errors',1);
								ini_set('display_errors',1);
								error_reporting(-1);
				}
				
				/*******************************************************************************
				 * Registers scripts and styles to be placed in the admin header
				 ******************************************************************************/
				public function admin_enqueue_scripts(){
								//Set the script dependencies
								$deps = array('jquery');
								
								//Enqueue the media scripts for the media uploader
								wp_enqueue_media();
								
								//Enqueue the styles after they're registered
								wp_enqueue_style('wpforms_admin_style', $this->plugin_uri.'assets/css/admin.css');
								
								//Enqueue the scripts after they're registered
								wp_enqueue_script('wpforms_admin_script', $this->plugin_uri.'assets/js/admin.js', $deps);
				}
				
				/*******************************************************************************
				 * Registers scripts and styles to be placed in the frontend header
				 ******************************************************************************/
				public function wp_enqueue_scripts(){
								//Set the script dependencies
								$deps = array('jquery');
								
								//Enqueue the styles after they're registered
								wp_enqueue_style('wpforms_frontend_style', $this->plugin_uri.'assets/css/frontend.css');
								
								//Enqueue the scripts after they're registered
								wp_enqueue_script('wpforms_frontend_script', $this->plugin_uri.'assets/js/frontend.js', $deps);
				}
				
				/*******************************************************************************
				 * Shows the primary admin page
				 ******************************************************************************/
				public function admin_page_forms(){
								global $wpdb;
								
								//Check if the form has been POSTed
								$func_name = isset($_POST['wpforms-bulk-actions-select']) ? empty($_POST['wpforms-bulk-actions-select']) ? isset($_POST['wpforms-hid-func-name']) ? $_POST['wpforms-hid-func-name'] : '' : $_POST['wpforms-bulk-actions-select'] : '';
								
								//Check to see which function to perform
								if ($func_name=='delete' && isset($_POST['wpforms'])){
												//Loop through all the forms and delete them from the DB
												foreach($_POST['wpforms'] as $id){
																$wpdb->delete($wpdb->prefix.WPFORMS_DB_TABLE, array('id' => $id));
												}
								}
												
								//Get the forms
								$this->forms = isset($_POST['wpforms-query']) ? $this->model->get_forms_by_term($_POST['wpforms-query']) : $this->model->get_forms();
								
								//Show the HTML
								@include($this->plugin_path.'/tpl/admin/forms.php');
				}
				
				/*******************************************************************************
				 * Shows the add form admin page
				 ******************************************************************************/
				public function admin_page_add_form(){
								global $wpdb;
								
								//Init the message variable, in case the form is submitted
								$message = '';
								
								//Check if the file size exceeded the upload limit
								if (isset($_SERVER['CONTENT_LENGTH']) && !empty($_SERVER['CONTENT_LENGTH'])){
												//Get the maximum upload size
												$max_upload_size = (min((int)ini_get('upload_max_filesize'), (int)ini_get('post_max_size'), (int)ini_get('memory_limit'))*1024)*1024;
																				
												if (empty($_POST) && empty($_FILES)){
																$message = 'The file you chose to upload exceeds the maximum size allowed. Please choose a file that is no larger than '.(($max_upload_size/1024)/1024).'MB.';
												} else {
																//Check if the upload form was submitted
																if (isset($_POST['wpforms-upload-button'])){
																				if (isset($_GET['mode']) && $_GET['mode']=='edit'){
																								//Move the form if a file has been uploaded
																								if (isset($_FILES['wpforms-upload-file']['name']) && !empty($_FILES['wpforms-upload-file']['name'])) $filename = $this->model->upload_form($_FILES);
																								
																								//Add new post with custom post type
																								wp_insert_post(array(
																												'id'            => $_POST['wpforms-post-id'],
																												'post_title'    => $_POST['wpforms-upload-name'],
																												'post_content'  => $_POST['wpforms-upload-description'],
																												'post_status'   => 'publish',
																												'post_type'     => 'wpform'
																								));
																								
																								//Add the form_id and form_filename metadata to the post
																								update_post_meta($_POST['wpforms-post-id'], 'wpforms-form-id', $_GET['form_id']);
																								update_post_meta($_POST['wpforms-post-id'], 'wpforms-form-filename', $filename);
																								
																								//Update the information in the database
																								$wpdb->update($wpdb->prefix.WPFORMS_DB_TABLE, array(
																												'post_id'     => $_POST['wpforms-post-id'],
																												'title'       => $_POST['wpforms-upload-name'],
																												'description' => $_POST['wpforms-upload-description']
																								), array('id' => $_GET['form_id']));
																								
																								if (isset($filename)) $wpdb->update($wpdb->prefix.WPFORMS_DB_TABLE, array('filename' => $filename), array('id' => $_GET['form_id']));
																								$message = 'Your form has been updated successfully!';
																				} else {
																								//Move the form if a file has been uploaded
																								if (isset($_FILES)) $filename = $this->model->upload_form($_FILES); else $message = 'There was an error uploading your form. Please try again.';
																								
																								//Add new post with custom post type
																								$post_id = wp_insert_post(array(
																												'post_title'    => $_POST['wpforms-upload-name'],
																												'post_content'  => $_POST['wpforms-upload-description'],
																												'post_status'   => 'publish',
																												'post_type'     => 'wpform'
																								));
																								
																								//Insert the form and return the ID
																								$form_id = $this->model->insert_form($post_id, isset($filename) ? $filename : '', $_POST['wpforms-upload-name'], $_POST['wpforms-upload-description']);
																								
																								//Now that the file is in the correct directory, we add it to the DB
																								$message = $form_id ? WPFORMS_MSG_UPLOAD_SUCCESS : WPFORMS_MSG_UPLOAD_FAILED;
																								
																								//Add the form_id and form_filename metadata to the post
																								update_post_meta($post_id, 'wpforms-form-id', $form_id);
																								update_post_meta($post_id, 'wpforms-form-filename', $filename);
																				}
																}
												}
								}
								
								//Check if we're in edit mode
								if (isset($_GET['mode'])){
												switch($_GET['mode']){
																case 'edit':
																				$form = $this->model->get_forms(isset($_GET['form_id']) ? $_GET['form_id'] : 0);
																				break;
												}
								}
								
								//Show the HTML
								@include($this->plugin_path.'/tpl/admin/add-form.php');
				}
				
				/*******************************************************************************
				 * Shows the associations admin page
				 ******************************************************************************/
				public function admin_page_associations(){
								global $wpdb;
								
								//Check if the form has been POSTed
								$func_name = isset($_POST['wpforms-bulk-actions-select']) ? empty($_POST['wpforms-bulk-actions-select']) ? isset($_POST['wpforms-hid-func-name']) ? $_POST['wpforms-hid-func-name'] : '' : $_POST['wpforms-bulk-actions-select'] : '';
								
								//Check if the delete function has been called
								if ($func_name=='delete_association' && isset($_POST['wpforms']) && isset($_POST['wpforms-form-id'])){
												//Get the form ID
												$form_id = $_POST['wpforms-form-id'];
												
												//Loop through all the submitted association post IDs
												foreach($_POST['wpforms'] as $post_id){
																//Delete from DB
																$wpdb->delete($wpdb->postmeta, array('post_id' => $post_id, 'meta_value' => $form_id));
												}
								} elseif (isset($_POST['wpforms-add-association-button'])){
												//Get the form ID from the form
												$form_id = $_POST['wpforms-hid-form-id'];
												
												//Get the pages from the list
												$pages = $_POST['wpforms-pages'];
												
												//Add the associations
												$this->model->add_associations($form_id, $pages);
								}
								
								//Get all forms
								$this->forms = $this->model->get_forms();
								
								//Get the preselected form ID if it has been passed in the URL
								$form_id = isset($_GET['form_id']) ? !empty($_GET['form_id']) ? $_GET['form_id'] : $this->forms[0]->id : $this->forms[0]->id;
								
								//Get the associations for this ID
								$this->associations = !empty($form_id) ? $this->model->get_associations_by_form_id($form_id) : array();
								
								//Get all the pages
								$this->pages = $this->model->get_pages();
								
								//Show the HTML
								@include($this->plugin_path.'/tpl/admin/associations.php');
				}
				
				/*******************************************************************************
				 * Shortcode that displays forms on the frontend
				 ******************************************************************************/
				public function wpforms_display($atts){
								global $wpdb;
								
								//Init the HTML variable
								$html = '';
								
								//Extract shortcode attributes
								extract(shortcode_atts(array(
												'echo' => true,
												'count' => -1,
												'id' => null,
												'offset' => 0,
												'order_by' => 'title',
												'order' => 'ASC',
												'reverse_num' => false
								), $atts));
								
								//Process specific attributes to allow for multiple values
								$count = ((is_numeric($count) && $count<0) || strtolower($count)=='all') ? -1 : $count;
												
								//Get the forms
								$forms = $this->model->get_forms($id, $count, 0, $order_by, $order, $reverse_num);
								
								//Determine which template to use
								$tpl_path = get_template_directory().'/wpforms/tpl/wpforms-display.php';
								$plugin_path = $this->plugin_path.'/tpl/frontend/wpforms-display.php';
								
								//Start the output buffer
								ob_start();
								
								//Show the HTML
								@include(file_exists($tpl_path) ? $tpl_path : $plugin_path);
								
								//Save the contents of the above include into the HTML variable
								$html .= ob_get_contents();
								
								//Close and clear the output buffer
								ob_end_clean();
								
								//Finally we return the generated HTML
								return $html;
				}
				
				/*******************************************************************************
				 * Shortcode that displays form icon on the frontend. Useful if you don't want
				 * it on every post or want to place it in a different location in the markup
				 ******************************************************************************/
				public function wpforms_display_icon($atts = null, $content = ''){
								//Process attributes
								$atts = empty($atts) ? array() : $atts;
								
								//Extract the shortcode attributes
								extract(shortcode_atts(array(
												'before' => '',
												'after' => ''
								), $atts));
								
								//Init the HTML variable
								$html = $before;
								
								//Verify we have attachments for the current post
								if (!empty($this->associations)){
												//Get the settings
												$this->settings = $this->model->get_settings();
												
												//Get the size of the icon image
												list($this->icon_width, $this->icon_height) = file_exists($this->settings['wpforms-icon']) ? getimagesize($this->settings['wpforms-icon']) : array(0, 0);
												
												//Determine which template to use
												$tpl_path = get_template_directory().'/wpforms/tpl/wpforms-display-icon.php';
												$plugin_path = $this->plugin_path.'/tpl/frontend/wpforms-display-icon.php';

												//Begin the output buffer so we can save the template HTML as a variable
												ob_start();
												
												//Include the template file
												@include(file_exists($tpl_path) ? $tpl_path : $plugin_path);
												
												//Save the contents of the output buffer to a variable
												$html = ob_get_contents();
												
												//Close the output buffer and clear it
												ob_end_clean();
								}
								
								//Prepend the HTML to the content and return it
								return $html.$after.$content;
    }
				
				/*******************************************************************************
				 * Shows the settings admin page
				 ******************************************************************************/
				public function admin_page_settings(){
								global $wpdb;
								
								//Check if form was submitted
								if (isset($_POST['wpforms-save-settings-button'])){
												$this->model->update_settings($_POST, $this->settings);
												$message = 'Your settings have been saved successfully!';
								}
								
								//Get the settings
								$this->settings = $this->model->get_settings();
								
								//Get the dimensions for the image
								list($width, $height) = file_exists($this->settings['wpforms-icon']) ? getimagesize($this->settings['wpforms-icon']) : array(0, 0);
								
								//Show the HTML
								@include($this->plugin_path.'/tpl/admin/settings.php');
				}
				
				/*******************************************************************************
				 * Converts all forms to posts of our custom post type
				 ******************************************************************************/
				public function wpforms_convert_to_posts_callback(){
								global $wpdb;
								
								//Get all the forms in our database table sorted by their id
								$forms = $this->model->get_forms(null, -1, 0, 'id');
								
								//Get all the posts of our custom post type
								$form_posts = get_posts(array(
												'post_status' => 'publish',
												'post_type'   => 'wpform'
								));
								
								//Init the counter for the number of posts added
								$posts_added = 0;
								
								//Loop through each form and add the form to the WP Posts table if the post does not exist
								foreach($forms as $key=>$form){
												//Set a flag for determining whether the form is in the WP Posts table
												$post_exists = false;
												
												//Loop through each post
												foreach($form_posts as $form_key=>$form_post){
																//Check if the form is in the list of posts
																if ($form_post->post_title==$form->title){
																				$post_exists = true;
																				break;
																}
												}
												
												//If the form is not in the list of posts, then we add it
												if ($post_exists==false){
																//Add the form to the WP Posts DB
																$post_id = wp_insert_post(array(
																				'post_title'   => $form->title,
																				'post_content' => $form->description,
																				'post_type'    => 'wpform',
																				'post_date'    => $form->date_created,
																				'post_status'  => 'publish'
																));
																
																//Verify the post was inserted
																if ($post_id){
																				//Set the metadata
																				update_post_meta($post_id, 'wpforms-form-id', $form->id);
																				update_post_meta($post_id, 'wpforms-form-filename', $form->filename);
																				
																				//Update our DB table with the new post ID
																				if ($wpdb->update($wpdb->prefix.WPFORMS_DB_TABLE, array('post_id' => $post_id), array('id' => $form->id))){
																								$posts_added++;
																				}
																}
												}
								}
								
								echo $posts_added.' forms were converted into posts.';
				}
				
				/*******************************************************************************
				 * Adds media buttons above content editor
				 ******************************************************************************/
				public function ajax_associate_form(){
								//Get the POST parameters
								$post_id = $_POST['post_id'];
								$form_id = $_POST['form_id'];
								
								//Associate the form with this post
								return $this->model->add_associations($form_id, array($post_id));
				}
				
				/*******************************************************************************
				 * Adds media buttons above content editor
				 ******************************************************************************/
				public function media_buttons(){
								//Get the post ID
								$post_id = isset($_GET['post']) && !empty($_GET['post']) ? $_GET['post'] : 0;
								
								//Get the list of forms
								$this->forms = $this->model->get_forms();
								
								//Get the list of associations
								$this->associations = $this->model->get_associations_by_post_id($post_id);
								?>
								<a href="#TB_inline?width=600&height=550&inlineId=wpforms-media-button-associate-content" id="wpforms-media-button-associate" class="button thickbox insert_media wpforms-media-button" title="Associate a form with this post"><img src="<?php echo $this->plugin_uri; ?>assets/images/forms-icon-16.png" width="16" height="16" />Associate Form</a>
								<div id="wpforms-media-button-associate-content">
								<?php
								
								//Include the content template
								include($this->plugin_path.'/tpl/admin/associations-popup.php');
								
								?></div><?php
				}
}
?>