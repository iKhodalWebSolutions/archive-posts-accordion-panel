<?php

/**
 * Clean data on activation / deactivation
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly  
 
register_activation_hook( __FILE__, 'archivespostaccordion_activation');

function archivespostaccordion_activation() {

	if( ! current_user_can ( 'activate_plugins' ) ) {
		return;
	} 
	add_option( 'archivespostaccordion_license_status', 'invalid' );
	add_option( 'archivespostaccordion_license_key', '' );
	add_option( 'archivespostaccordion_license_reff', '' );

}

register_uninstall_hook( __FILE__, 'archivespostaccordion_uninstall');

function archivespostaccordion_uninstall() {

	delete_option( 'archivespostaccordion_license_status' );
	delete_option( 'archivespostaccordion_license_key' );
	delete_option( 'archivespostaccordion_license_reff' ); 
	
}