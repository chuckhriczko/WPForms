<?php
/*******************************************************************************
 * Obligatory WordPress plugin information
 ******************************************************************************/
/*
Plugin Name: WPForms                                                                                                                                                                                                 s
Plugin URI: http://www.objectunoriented.com/projects/wpforms
Description: Adds a forms management area
Version: 1.0
Author: Charles Hriczko
Author URI: http://objectunoriented.com/projects/wpforms
License: GPLv2
*/
/*******************************************************************************
 * Require necessary files
 ******************************************************************************/
require_once('lib/constants.php');
require_once('lib/wpforms.class.php');

/*******************************************************************************
* Register the activation hook
******************************************************************************/
function wpforms_register_activation_hook(){
				global $wpdb;
								
				//Create the database table
				return $wpdb->query('
								CREATE TABLE IF NOT EXISTS `'.$wpdb->prefix.WPFORMS_DB_TABLE.'` (
												`id` int(11) NOT NULL AUTO_INCREMENT,
												`post_id` int(11) NOT NULL,
												`title` text NOT NULL,
												`description` text NOT NULL,
												`filename` text NOT NULL,
												`date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
												`status` int(11) NOT NULL DEFAULT \'1\',
								PRIMARY KEY (`id`)
								) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
				');
}
//Register the activation hook
register_activation_hook(__FILE__, 'wpforms_register_activation_hook');

/*******************************************************************************
 * Instantiate our class
 ******************************************************************************/
$wpforms = new WPForms(); //Initialize the Article Manager class
?>
