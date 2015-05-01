var WPForms = {
				'cache': {},
				'animSpeed': 'medium',
				'popup_offset': 56
}; //Init the primary frontend object

(function($) {
				$(document).ready(function(){
								WPForms.init_dom_cache();
								WPForms.bind_events();
				});
				
				/**********************************************************************
					* Caches DOM elements for faster access
					*********************************************************************/
				WPForms.init_dom_cache = function(){
								WPForms.cache.container = $('.wpforms-icon-container');
								WPForms.cache.icon = WPForms.cache.container.find('.wpforms-icon');
								WPForms.cache.popup = WPForms.cache.container.find('.wpforms-popup');
				}
				
				/**********************************************************************
					* Bind events to the frontend page's controls
					*********************************************************************/
				WPForms.bind_events = function(){
								//Initialize the hover on the icon
								WPForms.bind_events_icon();
				}
				
				/**********************************************************************
					* Bind events to the frontend page's icon
					*********************************************************************/
				WPForms.bind_events_icon = function(){
								//Attach hover event to icon
								WPForms.cache.container.on('click, tap', '.wpforms-icon a', function(e){
												//Slide up if the popup is visible or down if it is not visible
												if (parseInt(WPForms.cache.popup.css('height'))==0){
																//Get the height of the box
																var height = (parseInt(WPForms.cache.popup.find('.wpforms-popup-body').css('height')) + WPForms.popup_offset) + 'px';
																
																//Animate the height down
																WPForms.cache.popup.animate({ height: height }, WPForms.animSpeed);
												} else {
																//Animate the height up
																WPForms.cache.popup.animate({ height: 0 }, WPForms.animSpeed);
												}
												
												e.preventDefault();
												return false;
								});
				}
}(jQuery));