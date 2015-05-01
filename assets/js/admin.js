var WPForms_Admin = {
				'cache': {},
				'animSpeed': 'medium'
}; //Init the primary admin object

(function($) {
				$(document).ready(function(){
								WPForms_Admin.init_dom_cache();
								//WPForms_Admin.init_layout();
								WPForms_Admin.bind_events();
				});
				
				/**********************************************************************
					* Caches DOM elements for faster access
					*********************************************************************/
				WPForms_Admin.init_dom_cache = function(){
								WPForms_Admin.cache.$container = $('body.wp-admin div#wpforms-container');
								WPForms_Admin.cache.$upload_form = WPForms_Admin.cache.$container.find('div#wpforms-upload');
								WPForms_Admin.cache.$search = WPForms_Admin.cache.$container.find('#wpforms-search');
								WPForms_Admin.cache.$listing = WPForms_Admin.cache.$container.find('#wpforms-listing');
				}
				
				/**********************************************************************
					* Initialize the layout
					*********************************************************************/
				WPForms_Admin.init_layout = function(){
								//Hide extra Forms link
								$('#toplevel_page_wpforms ul.wp-submenu li.wp-first-item').empty().remove();
				}
				
				/**********************************************************************
					* Bind events to the admin page's controls
					*********************************************************************/
				WPForms_Admin.bind_events = function(){
								//Bind the upload form events
								WPForms_Admin.bind_events_upload();
								
								//Bind the forms listing events
								WPForms_Admin.bind_events_listing();
								
								//Bind the forms association events
								WPForms_Admin.bind_events_associations();
								
								//Bind the settings events
								WPForms_Admin.bind_events_settings();
								
								//Verify the post page poststuff div is inside the form
								var ie_version = navigator.userAgent.toLowerCase();
								if (ie_version.indexOf('msie') != -1){
									/*if (parseInt(ie_version.split('msie')[1])==8){
										$('div#postbox-container-1').detach().appendTo('#poststuff');
                                    } else */if (parseInt(ie_version.split('msie')[1])==9){ $('form#post + div#poststuff').detach().appendTo('form#post'); }
								}
                                
				}
				
				/**********************************************************************
					* Bind events to the upload form
					*********************************************************************/
				WPForms_Admin.bind_events_upload = function(){
								WPForms_Admin.cache.$upload_form.on('click', 'input#wpforms-upload-button', function(e){
												//Get filename
												var filename = $(this).siblings('input#wpforms-upload-file').attr('value');
												
												//Verify a file has been chosen
												if ((filename=='' || filename=='undefined') && $('#wpforms-mode').val()!='edit'){
																alert('Please select a file before submitting the form.');
																
																//If not, cancel form submission
																e.preventDefault();
																return false;
												}
								});
				}
				
				/**********************************************************************
					* Bind events to the forms listing page
					*********************************************************************/
				WPForms_Admin.bind_events_listing = function(){
								WPForms_Admin.cache.$listing.on('click', 'a.menu-delete', function(e){
												//Get the function name
												var func_name = $('body').hasClass('forms_page_wpforms-associations') ? 'delete_association' : 'delete';
												
												//Check the checkbox
												$(this).parent('td').parent('tr').find('th.check-column input').attr('checked', 'checked');
												
												//Set the delete option for the bulk actions select
												WPForms_Admin.cache.$listing.next('#wpforms-bulk-actions select option[value="delete"]').attr('selected', 'selected');
												
												//Set the hidden function name element
												WPForms_Admin.cache.$container.find('#wpforms-hid-func-name').val(func_name);
												
												//Verify the user wants to delete this form
												if (confirm('Are you sure you would like to delete this form?')){
																switch(func_name){
																				case 'delete':
																								WPForms_Admin.cache.$listing.parent('form').trigger('submit');
																								break;
																				case 'delete_association':
																								WPForms_Admin.cache.$listing.parent('div').parent('form').trigger('submit');
																								break;
																}
												}
												
												e.preventDefault();
												return false;
								}).next('div#wpforms-bulk-actions').on('change', 'select#wpforms-bulk-actions-select', function(e){
												//Verify the select has a value that is not empty
												if ($(this).find('option:selected').val()!=''){
																//Verify the user wants to delete these forms
																if (confirm('Are you sure you would like to delete the selected form(s)?')){
																				//Set function name to delete the associations
																				//$('#wpforms-hid-func-name').val('delete_association');
																				
																				//Submit the form
																				$(this).parent('div#wpforms-bulk-actions').parent('form').trigger('submit');
																}
												}
								}).on('click', 'input#cb-select-all', function(e){
												//Select or deselect all checkboxes
												if ($(this).is(':checked')){
																WPForms_Admin.cache.$listing.find('tbody tr th input').attr('checked', 'checked');
												} else {
																WPForms_Admin.cache.$listing.find('tbody tr th input').removeAttr('checked');
												}
								});
				}
				
				/**********************************************************************
					* Bind events to the forms associations page
					*********************************************************************/
				WPForms_Admin.bind_events_associations = function(){
								WPForms_Admin.cache.$container.find('#wpforms-associations-form').on('change', 'select#wpforms-form-id', function(e){
												//Get the base URL for this page (without the id)
												var url = WPForms_Admin.cache.$container.find('#wpforms-hid-url').val();
												
												//Verify the select has a value that is not empty
												if ($(this).find('option:selected').val()!='') window.location = url + '&form_id=' + $(this).find('option:selected').val();
								})/*.on('click', '#wpforms-associations-aside ul li label', function(e){
												$(this).find('input').trigger('click');
								})*/;
								
								//Bind events for the associations popup form (media button)
								$('#wpforms-container #wpforms-associations-associate').on('click', function(){
												//Get the form ID for this form
												var form_id = $(this).prev('select#wpforms-form-id').find('option:selected').val(),
																form_title = $(this).prev('select#wpforms-form-id').find('option:selected').text(),
																post_id = $('#wpforms-container #wpforms-post-id').val(),
																is_listed = false;
												
												//Loop through all table rows
												$('#wpforms-container table tbody tr').each(function(){
																//Find the first table data element and see if it matches the form_title variable
																if ($(this).find('td:first-of-type').text()==form_title) is_listed = true;
												});
												
												//Verify the form is not listed
												if (!is_listed){
																//Generate the form HTML
																var html = '<tr><th scope="row" class="check-column"><label class="screen-reader-text" for="cb-select-' + form_id + '">Select Form</label><input type="checkbox" id="cb-select-' + form_id + '" name="wpforms[]" value="' + form_id + '"><div class="locked-indicator"></div></th><td>' + form_title + '</td></tr>';
																
																//Add the form to the associated list below the button
																$('#wpforms-container table tbody').append(html);
																
																//Add the association to the post meta table through Ajax
																$.ajax({
																				url: ajaxurl,
																				type: 'post',
																				data: {
																								action: 'associate_form',
																								post_id: post_id,
																								form_id: form_id
																				}
																});
												}
								});
				}
				
				/**********************************************************************
					* Bind events to the settings page
					*********************************************************************/
				WPForms_Admin.bind_events_settings = function(){
								/*WPForms_Admin.cache.$container.find('#wpforms-settings-form').on('mouseover', 'div.form-block', function(e){
												$(this).find('p').fadeIn(WPForms_Admin.animSpeed);
								}).on('mouseout', 'div.form-block', function(e){
												$(this).find('p').fadeOut(WPForms_Admin.animSpeed);
								});*/
								
								//Bind the event handler for the media uploader
								WPForms_Admin.bind_media_uploader(WPForms_Admin.cache.$container.find('#wpforms-settings-form #wpforms-choose-file-button'));
								
								//Bind the icon image upload link
								WPForms_Admin.cache.$container.find('#wpforms-settings-form').on('click', 'a[href="#wpforms-icon-link"]', function(e){
												//Trigger the click event for the media uploader button
												$(this).siblings('input#wpforms-choose-file-button').trigger('click');
												
												e.preventDefault();
												return false;
								});
								
								//Bind the convert posts button
								$('#wpforms-convert-to-posts').on('click', function(){
												//Fade out the message
												$('#wpforms-convert-to-posts').next('p.wpforms-message').fadeOut('fast', function(){
																//Set the converting message
																$(this).html('Converting...');
																
																//Fade the message back in
																$('#wpforms-convert-to-posts').next('p.wpforms-message').fadeIn('fast');
												});
												
												//Perform ajax call to convert the posts
												$.ajax({
																url: ajaxurl,
																type: 'post',
																data: { action: 'wpforms_convert_to_posts' },
																dataType: 'html',
																error: function(errorThrown){
																				$('#wpforms-convert-to-posts').next('p.wpforms-message').html('An error occurred while converting the posts. Error: ' + errorThrown);
																},
																success:function(html){
																				//Fade out the message
																				$('#wpforms-convert-to-posts').next('p.wpforms-message').fadeOut('fast', function(){
																								//Set the converting message
																								$(this).html(html);
																								
																								//Fade the message back in
																								$('#wpforms-convert-to-posts').next('p.wpforms-message').fadeIn('fast');
																				});
																}
												});
								});
				}
				
				/**********************************************************************
					* Binds the media uploader to an element click
					*********************************************************************/
				WPForms_Admin.bind_media_uploader = function(id){
								var _custom_media = true,
												_orig_send_attachment = wp.media.editor.send.attachment;
				
								// ADJUST THIS to match the correct button
								$(id).click(function(e) {
												var send_attachment_bkp = wp.media.editor.send.attachment;
												var button = $(this);
												_custom_media = true;
												wp.media.editor.send.attachment = function(props, attachment){
																console.log($('#wpforms-icon').prev('a.wpforms-img-link'));
																console.log($('#wpforms-icon').prev('a.wpforms-img-link').find('img'));
																if (_custom_media) $('#wpforms-icon').val(attachment.url).prev('a.wpforms-img-link').find('img').attr({ src: attachment.url, width: '', height: '' }); else return _orig_send_attachment.apply(this, [props, attachment]);
												}
				
												wp.media.editor.open(button);
												return false;
								});
				
								$('.add_media').on('click', function(){
												_custom_media = false;
								});
				}
}(jQuery));