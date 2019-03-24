<?php 
/*
  Plugin Name: Monthly category archive accordion wordpress plugin
  Description: Displays archive posts in accordion panel
  Author: iKhodal Web Solution
  Plugin URI: https://www.ikhodal.com/wp-archive-posts-accordion-panel/
  Author URI: https://www.ikhodal.com
  Version: 2.1
  Text Domain: archivespostaccordion
*/ 
  
//////////////////////////////////////////////////////
// Defines the constants for use within the plugin. //
////////////////////////////////////////////////////// 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly  
  
        
/**
* Define store url to validate license
*/
define( 'archivespostaccordion_license_url', 'https://www.ikhodal.com');
                
/**
*  Assets of the plugin
*/
$avpt_plugins_url = plugins_url( "/assets/", __FILE__ );

define( 'avpt_media', $avpt_plugins_url ); 

/**
*  Plugin DIR
*/
$avpt_plugin_DIR = plugin_basename(dirname(__FILE__));

define( 'avpt_plugin_dir', $avpt_plugin_DIR ); 
 
/**
 * Include abstract class for common methods
 */
require_once 'include/abstract.php';


///////////////////////////////////////////////////////
// Include files for widget and shortcode management //
///////////////////////////////////////////////////////

/**
 * Register custom post type for shortcode
 */ 
require_once 'include/shortcode.php';

/**
 * Admin panel widget configuration
 */ 
require_once 'include/admin.php'; 

/**
 * Load Archive Posts Accordion Panel on frontent pages
 */
require_once 'include/archivespostaccordion.php'; 

/**
 * Clean data on activation / deactivation
 */
require_once 'include/activation_deactivation.php';  
 