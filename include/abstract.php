<?php 
/** 
 * Abstract class  has been designed to use common functions.
 * This is file is responsible to add custom logic needed by all templates and classes.  
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly   
if ( ! class_exists( 'archivesPostAccordionLib' ) ) { 
	abstract class archivesPostAccordionLib extends WP_Widget {
		
	   /**
		* Default values can be stored
		*
		* @access    public
		* @since     1.0
		*
		* @var       array
		*/
		public $_config = array();

		/**
		 * constructor method.
		 *
		 * Run the following methods when this class is loaded.
		 * 
		 * @access    public
		 * @since     1.0
		 *
		 * @return    void
		 */ 
		public function __construct() {  
		 
			/**
			 * Load text domain
			 */
			add_action( 'plugins_loaded', array( $this, 'archivespostaccordion_text_domain' ) );
			
			parent::__construct( "archivespostaccordion",__( 'Archive Posts Accordion Panel', 'archivespostaccordion' ), array("description"=>"") ); 	
			
			/**
			 * Widget initialization
			 */
			add_action( 'widgets_init', array( &$this, 'initArchivesPostAccordion' ) ); 
			
			/**
			 * Load the CSS/JS scripts
			 */
			add_action( 'init',  array( $this, 'archivespostaccordion_scripts' ) );
			
		}
		
		function init_settings() {
		
			/**
			 * Default values configuration 
			 */
			 $mouse_hover_effect_cls = array(); 
			 for( $i = 0; $i <= 41; $i++ ) {
				$_opt = "ikh-image-style-".$i;
				$_opt_text = "Animation ".$i;
				$mouse_hover_effect_cls[$_opt] = $_opt_text;
			 }
			 
			 $this->avpt_registerPostType();
			 $_categories = $this->getCategoryDataByTaxonomy( "category" ) ;
			 $_cat_array = array();
			 $_default_category_list = array( "0"=>__( 'None', 'archivespostaccordion' ), "all"=>__( 'All', 'archivespostaccordion' ) );
			 if( count( $_categories ) > 0 ) { 
				foreach( $_categories as $_category_items ) {  
						$__chked = "";
						$__id = "";
						$__category = "";
						if(isset($_category_items->id) && !empty($_category_items->id)) {
							$__id = $_category_items->id;
							$__category = $_category_items->category;
						}	
						else {
							$__id = $_category_items->term_id;
							$__category = $_category_items->name;
						}
						$_default_category_list[ $__id ] = $_cat_array[ $__id ] =  ($this->get_hierarchy_dash($_category_items->term_group)).$__category; 
						unset($_default_category_list[0]);
				} 
			 }		
			 
			 
			 $_default_open_date_list = array(
											'month_year' => __( 'Month and Year', 'archivespostaccordion' ),
											'year' => __( 'Only Year', 'archivespostaccordion' )
										); 	
										
			$_all_post_type = $this->archivespostaccordion_getCategoryTypes();
			$arr_all_cat_type = array(); 
			$arr_all_cat_type["none"] = __( 'None', 'archivespostaccordion' );
			foreach($_all_post_type as $key_type){
				$arr_all_cat_type[$key_type->taxonomy] = $key_type->taxonomy;
			}	
			 
			$_all_post_type = $this->getPostTypes(); 
			$arr_all_post_type = array();
			$arr_all_post_type["none"] = __( 'None', 'archivespostaccordion' );
			foreach($_all_post_type as $key_type){  
				$arr_all_post_type[$key_type->post_type] = $key_type->post_type;
			}	
			
			 $this->_config = array( 			 
								'widget_title' => array( 
									"type" => "text",
									"default" => __( 'Rich Post Accordion', 'archivespostaccordion' ),
									"field_title" => __( 'Title', 'archivespostaccordion' ),
									"is_required" => "no",	
									"in_js" => "no",	
									"description" => __( "Please enter the widget/accordion title.", "archivespostaccordion" ),
									"field_group" => __( 'General Settings', 'archivespostaccordion' ),
								),   
								'date_format' => array( 
									"type" => "option",
									"default" => 'year',
									"field_title" => __( 'Accordion tabs date format', 'archivespostaccordion' ),
									"is_required" => "no",	 
									"in_js" => "yes",	
									"options" => array(
										 "year" =>  __( "Year", "archivespostaccordion" ),	 
										 "month" =>  __( "Month", "archivespostaccordion" ),	 
									), 
									"class" => "date_format",
									"onchange" => "avpt_change_default_dates(this)",
									"description" => __( "Please select the date format of accordion tabs.", "archivespostaccordion" ),
									"field_group" => __( "Custom Post Settings", "archivespostaccordion" ),
								),
								'number_of_post_display' => array( 
									"type" => "text",
									"default" => 6,
									"in_js" => "yes",
									"pm" => 1,	
									"field_title" => __( 'Number of post to display', 'archivespostaccordion' ),
									"is_required" => "no",	
									"description" => __( "Add the integer value to load default number of posts.", "archivespostaccordion" ),
									"field_group" => __( 'General Settings', 'archivespostaccordion' ),
								),	
								'title_text_color' => array( 
									"type" => "color",
									"default" => '#000',
									"class" => "archivespostaccordion-color-field-2", 
									"field_title" => __( 'Post title text color', 'archivespostaccordion' ),
									"is_required" => "no",	
									"in_js" => "yes",	
									"description" => __( "Add color code or color name for post title name.", "archivespostaccordion" ),
									"field_group" => __( 'General Settings', 'archivespostaccordion' ),
								),	
								'panel_text_color' => array( 
									"type" => "color",
									"default" => '#000',
									"in_js" => "no",	
									"field_title" => __( 'Accordion tab text color', 'archivespostaccordion' ),
									"is_required" => "no",	
									"class" => "archivespostaccordion-color-field-1", 
									"description" => __( "Add color code or color name for the text of accordion tabs.", "archivespostaccordion" ),
									"field_group" => __( 'General Settings', 'archivespostaccordion' ),
								),
								'panel_background_color' => array( 
									"type" => "color",
									"default" => '#ededed',
									"class" => "archivespostaccordion-color-field-3", 
									"field_title" => __( 'Accordion tab background color', 'archivespostaccordion' ),
									"is_required" => "no",	
									"in_js" => "no",	
									"description" => __( "Add color code or color name for the background of accordion tabs.", "archivespostaccordion" ),
									"field_group" => __( 'General Settings', 'archivespostaccordion' ),
								),	
								'header_text_color' => array( 
									"type" => "color",
									"default" => '#ffffff',
									"class" => "archivespostaccordion-color-field-4", 
									"field_title" => __( 'Widget title text color', 'archivespostaccordion' ),
									"is_required" => "no",	
									"in_js" => "no",	
									"description" => __( "Add color code or color name for widget heading title.", "archivespostaccordion" ),
									"field_group" => __( 'General Settings', 'archivespostaccordion' ),
								),
								'header_background_color' => array( 
									"type" => "color",
									"default" => '#0073e0',
									"class" => "archivespostaccordion-color-field-5", 
									"field_title" => __( 'Widget title background color', 'archivespostaccordion' ),
									"is_required" => "no",	
									"in_js" => "no",	
									"description" => __( "Add color code or color name for the background of widget heading.", "archivespostaccordion" ),
									"field_group" => __( 'General Settings', 'archivespostaccordion' ),
								),	
								'hide_widget_title' => array( 
									"type" => "boolean",
									"default" => 'yes',
									"options" => array(
										"yes" => __( "Yes", 'archivespostaccordion' ),
										"no" => __( "No", 'archivespostaccordion' ),
									),
									"field_title" => __( 'Hide widget title', 'archivespostaccordion' ),
									"is_required" => "no",	
									"in_js" => "no",	
									"description" => __( 'Select "Yes" to hide widget heading. Default is "No" to display it.', "archivespostaccordion" ),
									"field_group" => __( 'General Settings', 'archivespostaccordion' ),
								),
								'hide_searchbox' => array( 
									"type" => "boolean",
									"default" => 'no',
									"options" => array(
										"yes" => __( "Yes", 'archivespostaccordion' ),
										"no" => __( "No", 'archivespostaccordion' ),
									),
									"field_title" => __( 'Hide search textbox?', 'archivespostaccordion' ),
									"is_required" => "no",	
									"in_js" => "yes",	
									"description" => __( 'Select "Yes" to hide search textbox field. Default is "No" to display it.', "archivespostaccordion" ),
									"field_group" => __( 'General Settings', 'archivespostaccordion' ),
								),	
								'hide_categorybox' => array( 
									"type" => "none",
									"default" => 'yes', 
									"options" => array(
										"yes" => __( "Yes", 'archivespostaccordion' ),
										"no" => __( "No", 'archivespostaccordion' ),
									),
									"field_title" => __( 'Hide search category dropdown?', 'archivespostaccordion' ),
									"is_required" => "no",	
									"in_js" => "yes",	
									"description" => __( 'Select "Yes" to hide search category dropdown field. Default is "No" to display it.', "archivespostaccordion" ),
									"field_group" => __( 'General Settings', 'archivespostaccordion' ),
								),	
								'hide_post_title' => array( 
									"type" => "boolean",
									"default" => 'no',
									"options" => array(
										"yes" => __( "Yes", 'archivespostaccordion' ),
										"no" => __( "No", 'archivespostaccordion' ),
									),
									"field_title" => __( 'Hide post title?', 'archivespostaccordion' ),
									"is_required" => "no",	
									"in_js" => "yes",	
									"description" => __( 'Select "Yes" to hide the post title. Default is "No" to display it.', "archivespostaccordion" ),
									"field_group" => __( 'General Settings', 'archivespostaccordion' ),
								), 
								'template' => array( 
									"type" => "option",
									"default" => 'no',
									"field_title" => __( 'Templates', 'archivespostaccordion' ),
									"is_required" => "no",	
									"in_js" => "no",		
									"options" => array(
										 "pane_style_1" =>  __( "Template Style 1", "archivespostaccordion" ),		
										 "pane_style_2" =>  __( "Template Style 2", "archivespostaccordion" ),		
										 "pane_style_3" =>  __( "Template Style 3", "archivespostaccordion" ),
										 "pane_style_4" =>  __( "Template Style 4", "archivespostaccordion" ),
										 "pane_style_5" =>  __( "Template Style 5", "archivespostaccordion" ),
										 "pane_style_6" =>  __( "Template Style 6", "archivespostaccordion" ),	
										 "pane_style_7" =>  __( "Template Style 7", "archivespostaccordion" ),
										 "pane_style_8" =>  __( "Template Style 8", "archivespostaccordion" ),
										 "pane_style_9" =>  __( "Template Style 9", "archivespostaccordion" ),
										 "pane_style_10" =>  __( "Template Style 10", "archivespostaccordion" ),
										 "pane_style_11" =>  __( "Template Style 11", "archivespostaccordion" ),
										 "pane_style_12" =>  __( "Template Style 12", "archivespostaccordion" ),
									),
									"description" => __( "Select the template for accordion", "archivespostaccordion" ),
									"field_group" => __( 'General Settings', 'archivespostaccordion' ),
								), 
								'security_key' => array(   
									"type" => "none",
									"in_js" => "yes",	
									"vcode" => $this->getUCode(),
									"security_key" =>  'AVPT_#s@R$@ASI#TA(!@@21M3',
									"field_group" => __( 'General Settings', 'archivespostaccordion' ),
								),  
								'avpt_category_type' => array( 
									"type" => "option",
									"default" => 'category',
									"field_title" => __( 'Category Types', 'archivespostaccordion' ),
									"is_required" => "yes",	 
									"in_js" => "yes",	
									"onchange" => "sel_change_categories_on_type(this)",
									"options" => $arr_all_cat_type,
									"pm" => 1,
									"description" => __( "Select category type to load categories.", "archivespostaccordion" ),
									"field_group" => __( "Custom Post Settings", "archivespostaccordion" ),
								),
								'category_id' => array( 
									"type" => "checkbox",
									"default" => implode(",",array_keys($_cat_array)),
									"field_title" => __( 'Category', 'archivespostaccordion' ),
									"is_required" => "no",
									"in_js" => "yes",	
									"onchange" => "ck_category_check(this)",
									"options" => $_cat_array, 
									"inherit_type" => "avpt_category_type",
									"description" => __( "Please select the categories.", "archivespostaccordion" ),
									"field_group" => __( 'Custom Post Settings', 'archivespostaccordion' ),
								), 
								'avpt_default_category' => array( 
									"type" => "none",
									"is_required" => "no",	 
									"inherit_type" => "avpt_category_type",
									"in_js" => "yes",	 
									"description" => __( 'Select a default category in the search area.', "archivespostaccordion" ),
									"field_group" => __( 'Custom Post Settings', 'archivespostaccordion' ),
								),
								'tp_widget_width' => array( 
									"type" => "text",
									"default" => '100%',
									"field_title" => __( 'Widget Width', 'archivespostaccordion' ),
									"is_required" => "no",	
									"in_js" => "no",	
									"description" => __( "Add width of widget in pixel or percentage. Default width is 100%", "archivespostaccordion" ),
									"field_group" => __( 'General Settings', 'archivespostaccordion' ),
								),
								'post_type' => array( 
									"type" => "option",
									"default" => 'post',
									"field_title" => __( 'Post Types', 'archivespostaccordion' ),
									"is_required" => "yes",	 
									"in_js" => "yes",	
									"class" =>  "ac_post_type", 
									"options" => $arr_all_post_type,
									"pm" => 1,
									"onchange" => "avpt_change_default_dates(this)",
									"description" => __( "Select post type to load categories.", "archivespostaccordion" ),
									"field_group" => __( 'Custom Post Settings', 'archivespostaccordion' ),
								), 
								'avpt_short_category_name_by' => array( 
									"type" => "option",
									"default" => 'asc',
									"field_title" => __( 'Short/order category name by', 'archivespostaccordion' ),
									"is_required" => "no",	 
									"in_js" => "yes",	
									"options" => array(
										 "asc" =>  __( "Ascending", "archivespostaccordion" ),	 
										 "desc" =>  __( "Descending", "archivespostaccordion" ),	 
										 "id" =>  __( "Shorting by category IDs", "archivespostaccordion" ),	 
									),
									"description" => __( 'Select "Ascending" or "Descending" shorting order of category name. Default is "Ascending" to display it.', "archivespostaccordion" ),
									"field_group" => __( 'General Settings', 'archivespostaccordion' ),
								), 
								'avpt_enable_rtl' => array( 
									"type" => "boolean",
									"default" => 'no',
									"options" => array(
										"yes" => __( "Yes", 'archivespostaccordion' ),
										"no" => __( "No", 'archivespostaccordion' ),
									),
									"field_title" => __( 'Enable RTL', 'archivespostaccordion' ),
									"is_required" => "no",	
									"in_js" => "yes",	
									"description" => __( 'Select "Yes" to enable rtl support. Default is "No" to display it.', "archivespostaccordion" ),
									"field_group" => __( 'General Settings', 'archivespostaccordion' ),
								),
								'avpt_enable_post_count' => array( 
									"type" => "boolean",
									"default" => 'no',
									"options" => array(
										"yes" => __( "Yes", 'archivespostaccordion' ),
										"no" => __( "No", 'archivespostaccordion' ),
									),
									"field_title" => __( 'Enable post count with accordion', 'archivespostaccordion' ),
									"is_required" => "no",	
									"in_js" => "no",	
									"description" => __( 'Select "Yes" to enable post count with accordion tabs. Default value is "No" as disabled.', "archivespostaccordion" ),
									"field_group" => __( 'Custom Post Settings', 'archivespostaccordion' ),
								),
								'avpt_hide_empty_category' => array( 
									"type" => "boolean",
									"default" => 'no',
									"options" => array(
										"yes" => __( "Yes", 'archivespostaccordion' ),
										"no" => __( "No", 'archivespostaccordion' ),
									),
									"field_title" => __( 'Hide empty categories', 'archivespostaccordion' ),
									"is_required" => "no",	
									"in_js" => "yes",	
									"description" => __( 'Select "Yes" to hide empty categories. Default value is "No" to display it.', "archivespostaccordion" ),
									"field_group" => __( 'Custom Post Settings', 'archivespostaccordion' ),
								), 
								'avpt_show_all_pane' => array( 
									"type" => "boolean",
									"default" => 'yes',
									"options" => array(
										"yes" => __( "Yes", 'archivespostaccordion' ),
										"no" => __( "No", 'archivespostaccordion' ),
									),
									"field_title" => __( 'Show "All" label accordion ', 'archivespostaccordion' ),
									"is_required" => "no",	 
									"in_js" => "no",	
									"description" => __( 'Show/Hide "All" label accordion that will display all posts. Default value is "No".', "archivespostaccordion" ),
									"field_group" => __( 'Custom Post Settings', 'archivespostaccordion' ),
								),
								'avpt_default_date_open' => array( 
									"type" => "option",
									"default" => 'all',
									"field_title" => __( 'Accordion default date', 'archivespostaccordion' ),
									"is_required" => "no",	 
									"class" => "avpt_default_date_open",  
									"in_js" => "no",	
									"options" => $_default_open_date_list,
									"inherit_type" => "date_format",
									"description" => __( 'Select date format to be opened as default accordion. Default value is "None".', "archivespostaccordion" ),
									"field_group" => __( 'Custom Post Settings', 'archivespostaccordion' ),
								), 
								'avpt_hide_comment_count' => array( 
									"type" => "boolean",
									"default" => 'no',
									"options" => array(
										"yes" => __( "Yes", 'archivespostaccordion' ),
										"no" => __( "No", 'archivespostaccordion' ),
									),
									"field_title" => __( 'Hide comments count', 'archivespostaccordion' ),
									"is_required" => "no",	 
									"in_js" => "yes",	
									"description" => __( 'Select "Yes" to hide comments count of the post. Default is "No" to display it.', "archivespostaccordion" ),
									"field_group" => __( 'General Settings', 'archivespostaccordion' ),
								),
								'avpt_hide_posted_date' => array( 
									"type" => "boolean",
									"default" => 'no',
									"options" => array(
										"yes" => __( "Yes", 'archivespostaccordion' ),
										"no" => __( "No", 'archivespostaccordion' ),
									),
									"field_title" => __( 'Hide posted date', 'archivespostaccordion' ),
									"is_required" => "no",	 
									"in_js" => "yes",	
									"description" => __( 'Select "Yes" to hide posted date of posts. Default is "No" to display it.', "archivespostaccordion" ),
									"field_group" => __( 'General Settings', 'archivespostaccordion' ),
								), 
								'avpt_exclude_category' => array( 
									"type" => "none",
									"default" => '', 
									"field_title" => __( 'Exclude Categories IDs', 'archivespostaccordion' ),
									"is_required" => "no",	 
									"in_js" => "yes",	
									"description" => __( 'Add comma separated categories IDs to exclude posts from accordion. eg. 1,3,8,2', "archivespostaccordion" ),
									"field_group" => __( 'General Settings', 'archivespostaccordion' ),
								),
								'avpt_hide_paging' => array( 
									"type" => "boolean",
									"default" => 'no',
									"options" => array(
										"yes" => __( "Yes", 'archivespostaccordion' ),
										"no" => __( "No", 'archivespostaccordion' ),
									),
									"field_title" => __( 'Hide ajax paging, load more or next-prev links', 'archivespostaccordion' ),
									"is_required" => "no",	 
									"in_js" => "yes",	
									"description" => __( 'Select "Yes" to hide ajax paging, load more or next-prev links.', "archivespostaccordion" ),
									"field_group" => __( 'General Settings', 'archivespostaccordion' ),
								),
								'avpt_hide_post_image' => array( 
									"type" => "boolean",
									"default" => 'no',
									"options" => array(
										"yes" => __( "Yes", 'archivespostaccordion' ),
										"no" => __( "No", 'archivespostaccordion' ),
									),
									"field_title" => __( 'Hide post image', 'archivespostaccordion' ),
									"is_required" => "no",	 
									"in_js" => "yes",	
									"description" => __( 'Select "Yes" to hide the post image.', "archivespostaccordion" ),
									"field_group" => __( 'General Settings', 'archivespostaccordion' ),
								),
								'avpt_hide_post_short_content' => array( 
									"type" => "boolean",
									"default" => 'no',
									"options" => array(
										"yes" => __( "Yes", 'archivespostaccordion' ),
										"no" => __( "No", 'archivespostaccordion' ),
									),
									"field_title" => __( 'Hide post short content', 'archivespostaccordion' ),
									"is_required" => "no",	 
									"in_js" => "yes",	
									"description" => __( 'Select "Yes" to hide post short content.', "archivespostaccordion" ),
									"field_group" => __( 'General Settings', 'archivespostaccordion' ),
								),
								'avpt_select_paging_type' => array( 
									"type" => "option",
									"default" => 'load_more_option',
									"options" => array(
										"load_more_option" => __( "Load more option", 'archivespostaccordion' ),
										"next_and_previous_links" => __( "Next and previous links", 'archivespostaccordion' ),
										"simple_numeric_pagination" => __( "Simple numeric pagination", 'archivespostaccordion' ),
									),
									"field_title" => __( 'Pagination Type', 'archivespostaccordion' ),
									"is_required" => "no",	 
									"in_js" => "yes",
									"pm" => 1,	
									"description" => __( 'Select the ajax pagination type like load more option, next and previous links or simple numeric pagination.', "archivespostaccordion" ),
									"field_group" => __( 'General Settings', 'archivespostaccordion' ),
								), 
								'avpt_allow_autoclose_accordion' => array( 
									"type" => "boolean",
									"default" => 'no',
									"options" => array(
										"yes" => __( "Yes", 'archivespostaccordion' ),
										"no" => __( "No", 'archivespostaccordion' ),
									),
									"field_title" => __( 'Allow to auto close other accordion tabs when opening new tab', 'archivespostaccordion' ),
									"is_required" => "no",	 
									"in_js" => "no",	
									"description" => __( 'Select "Yes" to auto close other accordion tab when opening new accordion tab.', "archivespostaccordion" ),
									"field_group" => __( 'General Settings', 'archivespostaccordion' ),
								),
								'avpt_hide_post_short_content_length' => array( 
									"type" => "text",
									"default" => '52', 
									"field_title" => __( 'Short content character length', 'archivespostaccordion' ),
									"is_required" => "no",	 
									"in_js" => "yes",	
									"description" => __( 'Add the length of short content if short content has enabled to view. Default content length is 100', "archivespostaccordion" ),
									"field_group" => __( 'General Settings', 'archivespostaccordion' ),
								),
								'avpt_read_more_link' => array( 
									"type" => "boolean",
									"default" => 'no',
									"options" => array(
										"yes" => __( "Yes", 'archivespostaccordion' ),
										"no" => __( "No", 'archivespostaccordion' ),
									),
									"field_title" => __( 'Hide read more link', 'archivespostaccordion' ),
									"is_required" => "no",	 
									"in_js" => "yes",	
									"description" => __( 'Select "Yes" to hide read more link. Default value is "No" to display it.', "archivespostaccordion" ),
									"field_group" => __( 'General Settings', 'archivespostaccordion' ),
								),
								'avpt_image_content_width' => array( 
									"type" => "text",
									"default" => '200', 
									"field_title" => __( 'Maximum image and content block width', 'archivespostaccordion' ),
									"is_required" => "no",	 
									"in_js" => "yes",	
									"pm" => 1,	
									"description" => __( 'Set the width of image and content block in pixel. eg. 200 <br /> Note: Do not add "px" after the number', "archivespostaccordion" ),
									"field_group" => __( 'General Settings', 'archivespostaccordion' ),
								),
								'avpt_image_height' => array( 
									"type" => "text",
									"default" => '200', 
									"field_title" => __( 'Maximum image height', 'archivespostaccordion' ),
									"is_required" => "no",	 
									"in_js" => "yes",	
									"pm" => 1,
									"description" => __( 'Set the height of image in pixel. eg. 200 <br /> Note: Do not add "px" after the number', "archivespostaccordion" ),
									"field_group" => __( 'General Settings', 'archivespostaccordion' ),
								),
								'avpt_shorting_posts_by' => array( 
									"type" => "option",
									"default" => 'date',
									"field_title" => __( 'Ordering/ shorting posts by', 'archivespostaccordion' ),
									"is_required" => "no",
									"in_js" => "yes",		
									"options" => array(
										 "id" => __( "Post ID", "archivespostaccordion" ),	 
										 "title" => __( "Title", "archivespostaccordion" ),	 
										 "date" => __( "Posted/Created Date", "archivespostaccordion" ),	 
									), 
									"description" => __( 'Select the shorting/ordering field like post id, title or posted/created date.', "archivespostaccordion" ),
									"field_group" => __( 'General Settings', 'archivespostaccordion' ),
								),
								'avpt_post_ordering_type' => array( 
									"type" => "option",
									"default" => 'ascending',
									"field_title" => __( 'Select the post ordering type', 'archivespostaccordion' ),
									"is_required" => "no",	 
									"in_js" => "yes",	
									"options" => array(
										 "ascending" => __( "Ascending", "archivespostaccordion" ),	 
										 "descending" => __( "Descending", "archivespostaccordion" ),	  	 
									), 
									"description" => __( 'Change the post ordering/shorting like ascending, descending.', "archivespostaccordion" ),
									"field_group" => __( 'General Settings', 'archivespostaccordion' ),
								),
								'avpt_space_margin_between_posts' => array( 
									"type" => "text",
									"default" => '15',
									"field_title" => __( 'Space/margin between posts', 'archivespostaccordion' ),
									"is_required" => "no",	   
									"in_js" => "no",	
									"description" => __( 'Set the space/margin between posts items. eg. 10 <br /> Note: Do not add "px" after the number', "archivespostaccordion" ),
									"field_group" => __( 'General Settings', 'archivespostaccordion' ),
								),
								'avpt_posts_grid_alignment' => array( 
									"type" => "option",
									"default" => 'fit_to_sides',
									"options" => array(
										 "fit_to_sides" => __( "Auto adjust image width to maximum width", "archivespostaccordion" ),	 
										 "fixed_width_center" => __( "Fixed/static image width with centered aligned", "archivespostaccordion" ),	  	 
										 "fixed_width_left" => __( "Fixed/static image width with left aligned", "archivespostaccordion" ),	  	 
									),
									"field_title" => __( 'Posts grid alignment', 'archivespostaccordion' ),
									"is_required" => "no",	 
									"in_js" => "no",	
									"description" => __( 'Set the space/margin between posts items. eg. 10 <br /> Note: Do not add "px" after the number', "archivespostaccordion" ),
									"field_group" => __( 'General Settings', 'archivespostaccordion' ),
								),
								'avpt_posts_loading_effect_on_pagination' => array( 
									"type" => "option",
									"default" => 'none',
									"options" => array(
										 "none" => __( "None", "archivespostaccordion" ),	 
										 "left" => __( "Loads grid posts from left", "archivespostaccordion" ),	 
										 "right" => __( "Loads grid posts from right", "archivespostaccordion" ),	  	 
										 "top" => __( "Loads grid posts from top", "archivespostaccordion" ),	  	 
										 "bottom" => __( "Loads grid posts from bottom", "archivespostaccordion" ),	  	 
									),
									"field_title" => __( 'Posts loading effect on pagination', 'archivespostaccordion' ),
									"is_required" => "no",	   
									"in_js" => "no",	
									"description" => __( 'Select posts loading effect or animation style like loads post grid from left, right, top and bottom', "archivespostaccordion" ),
									"field_group" => __( 'General Settings', 'archivespostaccordion' ),
								),
								'avpt_mouse_hover_effect' => array( 
									"type" => "option",
									"default" => 'ikh-image-style-0',
									"options" =>  $mouse_hover_effect_cls,
									"field_title" => __( 'Mouse hover effect', 'archivespostaccordion' ),
									"is_required" => "no",	 
									"in_js" => "yes",	
									"description" => __( 'Select an animation style for the mouse hover of posts item.', "archivespostaccordion" ),
									"field_group" => __( 'General Settings', 'archivespostaccordion' ),
								),
								'avpt_show_author_image_and_name' => array( 
									"type" => "boolean",
									"default" => 'no',
									"options" => array(
										"yes" => __( "Yes", 'archivespostaccordion' ),
										"no" => __( "No", 'archivespostaccordion' ),
									),
									"field_title" => __( 'Show author image and name', 'archivespostaccordion' ),
									"is_required" => "no",	 
									"in_js" => "yes",	
									"description" => __( 'Select "Yes" to show the image and name of posts author.', "archivespostaccordion" ),
									"field_group" => __( 'General Settings', 'archivespostaccordion' ),
								),  
								'st' => array(  
									"type" => "none",
									"in_js" => "no",	
									"flag" => get_option('archivespostaccordion_license_status'),
									"field_group" => __( 'General Settings', 'archivespostaccordion' ),
								),	  
								'archivespostaccordion_license_url' => array(
									"type" => "none",
									"in_js" => "no",	
									"license_url" => 'https://www.ikhodal.com/activate-license',
									"field_group" => __( 'General Settings', 'archivespostaccordion' ),
								),							
								'avpt_media' =>  array( 
									"type" => "none",
									"in_js" => "no",	
									"media_url" => avpt_media,
									"field_group" => __( 'General Settings', 'archivespostaccordion' ),
								), 
						);   
						
						$this->_plugin_settings = $this->_config;
						$this->setPluginValue();
		}		
		
		
		/**
		 * Load all the fields from templates
		 *
 		 * @access  public
		 * @since   1.0
		 *
		 * @return html
		 */
		function loadConfigFields(  $all_fields, $shortcode_config, $type ) {
		    
			$_field_html = array();   
			 
			foreach( $all_fields as $kw => $kw_val ) {
				
				if( isset($all_fields[$kw]["field_group"]) && !isset($_field_html[$all_fields[$kw]["field_group"]]) && !empty($kw) ) {
					$_field_html[$all_fields[$kw]["field_group"]] = array();
				}
				
				if( $kw_val["type"] == "color" ) 
					$_field_html[$all_fields[$kw]["field_group"]][] = $this->createInputColorField($kw, $all_fields, $shortcode_config, $type);
					
				else if( $kw_val["type"] == "text" ) 
					 $_field_html[$all_fields[$kw]["field_group"]][] = $this->createInputTextField($kw, $all_fields, $shortcode_config, $type);	
				else if( $kw_val["type"] == "option" )  
					$_field_html[$all_fields[$kw]["field_group"]][] = $this->createOptionField($kw, $all_fields, $shortcode_config, $type);	
					
				else if( $kw_val["type"] == "boolean" )  
					$_field_html[$all_fields[$kw]["field_group"]][] = $this->createBooleanField($kw, $all_fields, $shortcode_config, $type);		
				
				else if( $kw_val["type"] == "checkbox") 
					$_field_html[$all_fields[$kw]["field_group"]][] = $this->createInputCheckboxField($kw, $all_fields, $shortcode_config, $type);	
				
			} 
			 
			$_field_html = array_reverse($_field_html);
			$_group_html = "";
			foreach( $_field_html as $key_group => $group_fields ) {
				
				$group_title = $key_group;
				$group_field = implode( "", $group_fields );
				
				// Load template according to admin settings
				ob_start();
				require( $this->getArchivesPostAccordionTemplate( 'fields/fld_group.php' ) );	
				$_group_html .= ob_get_clean();	  
				
			}
			
			return $_group_html;
		
		}
		
		/**
		 * Creates the checkbox fields with it's default value
		 *
 		 * @access  public
		 * @since   1.0
		 *
		 * @param   string $key  Unique key of the form field
		 * @param   array  $fields  Contains all the fields for settings
		 * @param   array  $shortcode_config  Array of default/saved values
		 * @param   string $type Specify the type of field
		 */
		function createInputCheckboxField( $key, $fields, $shortcode_config, $type ) { 
		 
			if( isset( $fields[$key] ) ) {
				 
				$default_val = $fields[$key]["default"];
				if( isset($shortcode_config[$key]) && trim($shortcode_config[$key]) != "" ) {
					$default_val = $shortcode_config[$key]; 
				}

				if( isset($fields[$key]["inherit_type"]) && trim($fields[$key]["inherit_type"]) != "" && isset($shortcode_config[$fields[$key]["inherit_type"]]) ) {
							
					$_categories = $this->getCategoryDataByTaxonomy( $shortcode_config[$fields[$key]["inherit_type"]]  ) ;
					$_cat_array = array();
					$_default_open_category_list = array( "0"=>__( 'None', 'archivespostaccordion' ), "all"=>__( 'All', 'archivespostaccordion' ) );
					if( count( $_categories ) > 0 ) { 
						foreach( $_categories as $_category_items ) { 
							$__id = "";
							$__category = "";
							if(isset($_category_items->id) && !empty($_category_items->id)) {
								$__id = $_category_items->id;
								$__category = $_category_items->category;
							}	
							else {
								$__id = $_category_items->term_id;
								$__category = $_category_items->name;
							}
							  $_cat_array[ $__id ] =  ($this->get_hierarchy_dash($_category_items->term_group)).$__category; 
						} 
					}
					$fields[$key]["options"] = $_cat_array;
				}	
				
				// Load template according to admin settings
				ob_start();
				require( $this->getArchivesPostAccordionTemplate( 'fields/fld_checkbox.php' ) );	
				return ob_get_clean();	
				
			}
			
		}
		
		/**
		 * Creates the color field with it's default value
		 *
 		 * @access  public
		 * @since   1.0
		 *
		 * @param   string $key  Unique key of the form field
		 * @param   array $fields  Contains all the fields for settings
		 * @param   string $shortcode_config  Array of default/saved values 
		 */
		function createInputColorField( $key, $fields, $shortcode_config, $type ) { 
		 
			if( isset( $fields[$key] ) ) {
			 				
				$default_val = $fields[$key]["default"];
				if( isset($shortcode_config[$key]) && trim($shortcode_config[$key]) != "" ) {
					$default_val = $shortcode_config[$key]; 
				}
				
				// Load template according to admin settings
				ob_start();
				require( $this->getArchivesPostAccordionTemplate( 'fields/fld_color.php' ) );	
				return ob_get_clean();	
				
			}
			
		} 
		
		/**
		 * Creates the boolean form field for the admin
		 *
 		 * @access  public
		 * @since   1.0
		 *
		 * @param   string $key  Unique key of the form field
		 * @param   array  $fields  Contains all the fields for settings
		 * @param   array  $shortcode_config  Array of default/saved values
		 * @param   string $type Specify the type of field
		 */
		function createBooleanField( $key, $fields, $shortcode_config, $type ) {

			if( isset( $fields[$key] ) ) {
			  
				$default_val = $fields[$key]["default"];
				if( isset($shortcode_config[$key]) && trim($shortcode_config[$key]) != "" ) {
					$default_val = $shortcode_config[$key]; 
				}
				
				// Load template according to admin settings
				ob_start();
				require( $this->getArchivesPostAccordionTemplate( 'fields/fld_boolean.php' ) );	
				return ob_get_clean();	
			}
			 
		}	
		
		/**
		 * Creates the drop down field for the admin settings
		 *
 		 * @access  public
		 * @since   1.0
		 *
		 * @param   string $key  Unique key of the form field
		 * @param   array  $fields  Contains all the fields for settings
		 * @param   array  $shortcode_config  Array of default/saved values
		 * @param   string $type Specify the type of field
		 */
		function createOptionField( $key, $fields, $shortcode_config, $type ) { 
		
			if( isset( $fields[$key] ) ) {
			 
				$default_val = $fields[$key]["default"];
				if( isset($shortcode_config[$key]) && trim($shortcode_config[$key]) != "" ) {
					$default_val = $shortcode_config[$key]; 
				} 
				
				if( isset($fields[$key]["inherit_type"]) && trim($fields[$key]["inherit_type"]) != "" && trim($fields[$key]["inherit_type"]) == "date_format" ) {
					
					$date_format = $fields[$fields[$key]["inherit_type"]]["default"];
					if( isset($shortcode_config[$fields[$key]["inherit_type"]]) )
						$date_format = $shortcode_config[$fields[$key]["inherit_type"]]; 
					
					$post_type = $fields["post_type"]["default"];
					if( isset($shortcode_config["post_type"]) )
						$post_type = $shortcode_config["post_type"];  
					  
					$_panel_list = $this->getPanelArray( $post_type, $date_format );  
					$_default_open_category_list = array( "0"=>__( 'None', 'archivespostaccordion' ), "all"=>__( 'All', 'archivespostaccordion' ) );
					foreach( $_panel_list as $__pane_key => $__pane_text ) {  
						$_default_open_category_list[$__pane_key] = $__pane_text;
					}
					$fields[$key]["options"] = $_default_open_category_list;
					
				} else if( isset($fields[$key]["inherit_type"]) && trim($fields[$key]["inherit_type"]) != "" ) {
							
					$_categories = $this->getCategoryDataByTaxonomy( $shortcode_config[$fields[$key]["inherit_type"]]  ) ;
					$_default_open_category_list = array( "0"=>__( 'None', 'archivespostaccordion' ), "all"=>__( 'All', 'archivespostaccordion' ) );
					if( count( $_categories ) > 0 ) { 
						foreach( $_categories as $_category_items ) {   
							   $__id = "";
								$__category = "";
								if(isset($_category_items->id) && !empty($_category_items->id)) {
									$__id = $_category_items->id;
									$__category = $_category_items->category;
								}	
								else {
									$__id = $_category_items->term_id;
									$__category = $_category_items->name;
								}
						
							  $_default_open_category_list[ $__id ] =  ($this->get_hierarchy_dash($_category_items->term_group)).$__category; 
						} 
					}
					$fields[$key]["options"] = $_default_open_category_list;
				}
				
				// Load template according to admin settings
				ob_start();
				require( $this->getArchivesPostAccordionTemplate( 'fields/fld_option.php' ) );	
				return ob_get_clean();	
				
			}
			
		}
		
		/**
		 * Creates the text field for the admin settings
		 *
 		 * @access  public
		 * @since   1.0
		 *
		 * @param   string $key  Unique key of the form field
		 * @param   array  $fields  Contains all the fields for settings
		 * @param   array  $shortcode_config  Array of default/saved values
		 * @param   string $type Specify the type of field
		 */
		function createInputTextField( $key, $fields, $shortcode_config, $type ) { 
		
			if( isset( $fields[$key] ) ) {
			 
				$default_val = $fields[$key]["default"];
				if( isset($shortcode_config[$key]) && trim($shortcode_config[$key]) != "" ) {
					$default_val = $shortcode_config[$key]; 
				}
				
				// Load template according to admin settings
				ob_start();
				require( $this->getArchivesPostAccordionTemplate( 'fields/fld_text.php' ) );	
				return ob_get_clean();	
				
			}
			
		}
		
		
		/**
		 * Load the CSS/JS scripts
		 *
		 * @return  void
		 *
		 * @access  public
		 * @since   1.0
		 */
		function archivespostaccordion_scripts() { 
		
		 	 /**
			  * Default values configuration 
			  */
			 $this->init_settings();
			 
			 $dependencies = array( 'jquery' );
			/**
			 * Include Archive Posts Accordion Panel JS/CSS 
			 */
			wp_enqueue_style( 'archivespostaccordion', $this->_config["avpt_media"]["media_url"]."css/archivespostaccordion.css" );
			 
			wp_enqueue_script( 'archivespostaccordion', $this->_config["avpt_media"]["media_url"]."js/archivespostaccordion.js"  );
			
			/**
			 * Define global javascript variable
			 */
			wp_localize_script( 'archivespostaccordion', 'archivespostaccordion', array(
				'avpt_ajax_url' => admin_url( 'admin-ajax.php' ),
				'avpt_security'  =>  wp_create_nonce($this->_config["security_key"]["security_key"]),
				'avpt_all'  => __( 'All', 'archivespostaccordion' ),
				'avpt_plugin_url' => plugins_url( '/', __FILE__ ),
				'avpt_media' => $this->_config["avpt_media"]["media_url"]
			));
		}

		/**
		 * Loads categories as per taxonomy 
		 *
 		 * @access  public
		 * @since   1.0
		 *
		 * @param   string  $taxonomy  Type of category
		 * @return  object  Returns categories object
		 */ 
		 public function getCategoryDataByTaxonomy( $taxonomy ) {
				 
			global $wpdb;
	
			if( !$taxonomy || trim( $taxonomy ) == "" )
				$taxonomy = "category";
					  
			/**
			 * Fetch all the categories from database of the provided type
			 */   
			$_categories =  $wpdb->get_results($wpdb->prepare( "SELECT wtt.term_taxonomy_id as id,wtt.term_taxonomy_id as term_id,wtm.meta_value as depth,wtt.parent, wt.name as name, wt.name as category FROM `{$wpdb->prefix}terms` as wt INNER JOIN {$wpdb->prefix}term_taxonomy as wtt on wtt.term_id = wt.term_id and wtt.taxonomy = %s INNER JOIN {$wpdb->prefix}termmeta as wtm on wtm.term_id = wt.term_id and wtm.meta_key = 'order' ", $taxonomy ));	
			$_cats = (array)$_categories; 
			 
			$is_wc = 1;
			foreach( $_cats as $_category_data ) {
				if( count($_category_data) <= 0 ) {
					$is_wc = 0;	
				}
			}
			
			if( count($_cats) <= 0 ) {
				$is_wc = 0;	
			}
			
			if( $is_wc == 0 ) {
				$_cats = (array)get_terms( $taxonomy, array('hide_empty'=>false,'order'=>'ASC') ); 
				$_cats = (array)$_cats;  
				$this->sort_terms_hierarchy($_cats); 
			} 
			
			return	$_cats;
		}
		 
		
		/**
		 * Loads ajax categories as per type selection
		 *
		 * @access  private
		 * @since   1.0
		 *
		 * @return  void
		 */
		public function avpt_getCategoriesOnTypes() { 
		
			global $wpdb;
			
			/**
			* Check security token from ajax request
			*/
			check_ajax_referer( $this->_config["security_key"]["security_key"], 'security' );
			
			$__category_type = "";
			$_flh = 0;
			
			if( isset( $_REQUEST['category_type'] ) && trim( $_REQUEST['category_type'] ) != "" ) {
			
				$__category_type = sanitize_text_field( $_REQUEST['category_type'] );
				
				/**
				 * Fetch all the categories from database of the provided type
				 */  
				$_categories = $this->getCategoryDataByTaxonomy( $__category_type ) ;
				
				
				if( count( $_categories ) > 0 ) { 
				
					if( isset( $_REQUEST["category_field_name"] ) && trim( $_REQUEST["category_field_name"] ) != "" ) {
					
						$_category_field_name = sanitize_text_field( $_REQUEST['category_field_name'] );
					
						foreach( $_categories as $_category_items ) { 
							
							$__id = "";
							$__category = "";
							if(isset($_category_items->id) && !empty($_category_items->id)) {
								$__id = $_category_items->id;
								$__category = $_category_items->category;
							}	
							else {
								$__id = $_category_items->term_id;
								$__category = $_category_items->name;
							}	
								
							?><p><input  class="checkbox-category-ids" type="checkbox" name="<?php echo $_category_field_name; ?>[]" id="ckCategory_<?php echo $__id; ?>" onchange="ck_category_check(this)" value="<?php echo $__id; ?>" /><label for ="ckCategory_<?php echo $__id; ?>" ><?php echo ($this->get_hierarchy_dash($_category_items->term_group)).$__category; ?></label></p><?php 							
						}						
					
					} else {
						  
						foreach( $_categories as $_category_items ) { 
						
											
							$__id = "";
							$__category = "";
							if(isset($_category_items->id) && !empty($_category_items->id)) {
								$__id = $_category_items->id;
								$__category = $_category_items->category;
							}	
							else {
								$__id = $_category_items->term_id;
								$__category = $_category_items->name;
							}	
							?><p><input  class="checkbox-category-ids" type="checkbox" name="nm_category_id[]" id="ckCategory_<?php echo $__id; ?>" onchange="ck_category_check(this)" value="<?php echo $__id; ?>" /><label for ="ckCategory_<?php echo $__id; ?>" ><?php echo ($this->get_hierarchy_dash($_category_items->term_group)).$__category; ?></label></p><?php 
						}
					
					}
					
					$_flh = 1;  
				}  
				 
			}  
			die();
			 
		}
		
		
		/**
		 * Loads ajax date list as per post type selection
		 *
		 * @access  private
		 * @since   1.0
		 *
		 * @return  void
		 */
		public function avpt_getListDateArray() { 

			global $wpdb;
			
			/**
			* Check security token from ajax request
			*/
			check_ajax_referer( $this->_config["security_key"]["security_key"], 'security' );
			
			$__category_type = "";
			$_flh = 0;
			
			if(isset( $_REQUEST['date_format'] ) && trim( $_REQUEST['date_format'] ) != "" && isset( $_REQUEST['pst_type'] ) && trim( $_REQUEST['pst_type'] ) != "" ) {
			
				$__pst_type = sanitize_text_field( $_REQUEST['pst_type'] );
				$date_format = sanitize_text_field( $_REQUEST['date_format'] );
				 
				$_panel_fetch_format_display_text = "%M - %Y";
				$_panel_fetch_format_comapre_text = "%m%Y";
				if($date_format == "year") {
				
					$_panel_fetch_format_display_text = "%Y";
					$_panel_fetch_format_comapre_text = "%Y";
				
				} 
				 
				$_result_items = $wpdb->get_results( " SELECT DATE_FORMAT(post_date,'".$_panel_fetch_format_display_text."') as d1, DATE_FORMAT(post_date,'".$_panel_fetch_format_comapre_text."') as d2 FROM `{$wpdb->prefix}posts` where post_status = 'publish' and post_type = '".$__pst_type."' group by DATE_FORMAT(post_date,'".$_panel_fetch_format_display_text."') order by DATE_FORMAT(post_date,'".$_panel_fetch_format_comapre_text."') desc" ); 
				
				if( count( $_result_items ) > 0 ) { 
					?> <option selected="true" value="0"><?php echo __( 'None', 'archivespostaccordion' ); ?></option> <?php 	
					foreach( $_result_items as $_value ) {  
						?> <option value="<?php echo $_value->d2; ?>"><?php echo $_value->d1; ?></option> <?php  
					} 
				}
				 
			}
			
			die();
			 
		}	
		
		
		/**
		 * Loads ajax categories as per type selection
		 *
		 * @access  private
		 * @since   1.0
		 *
		 * @return  void
		 */
		public function avpt_getExcludeCategoriesOnTypes() { 
		
			global $wpdb;
			
			/**
			* Check security token from ajax request
			*/
			check_ajax_referer( $this->_config["security_key"]["security_key"], 'security' );
			
			$__category_type = "";
			$_flh = 0;
			if( isset( $_REQUEST['category_type'] ) && trim( $_REQUEST['category_type'] ) != "" ) {
			
				$__category_type = sanitize_text_field( $_REQUEST['category_type'] );
				
				/**
				 * Fetch all the categories from database of the provided type
				 */  
				$_categories = $this->getCategoryDataByTaxonomy( $__category_type ) ;
				
				if( count( $_categories ) > 0 ) { 
				
					if( isset( $_REQUEST["category_field_name"] ) && !empty( $_REQUEST["category_field_name"] ) ) {
					
						$_category_field_name = sanitize_text_field( $_REQUEST['category_field_name'] );
					
						foreach( $_categories as $_category_items ) { 
							
							?> 
								<p><input checked="true" class="checkbox-category-ids" type="checkbox" name="<?php echo $_category_field_name; ?>[]" id="<?php echo $_category_field_name; ?><?php echo $_category_items->id; ?>" value="<?php echo $_category_items->id; ?>"  onchange="ck_category_check(this)"  />
								<label for ="<?php echo $_category_field_name; ?><?php echo $_category_items->id; ?>" ><?php echo ($this->get_hierarchy_dash($_category_items->depth)).$_category_items->category; ?></label></p>
								
							<?php 
						}
						
					
					} else {
						
						foreach( $_categories as $_category_items ) { 
							?> 
								<p><input  class="checkbox-category-ids" checked="true" type="checkbox" name="avpt_exclude_category[]" id="ckCategory_<?php echo $_category_items->id; ?>" onchange="ck_category_check(this)" value="<?php echo $_category_items->id; ?>" />
								<label for ="ckCategory_<?php echo $_category_items->id; ?>" ><?php echo ($this->get_hierarchy_dash($_category_items->depth)).$_category_items->category; ?></label></p>
							<?php 
						}
					
					}
					
					$_flh = 1;  
				}  
			}
			
			if( $_flh == 0 )  
					 echo __( 'No category found.', 'archivespostaccordion' );  
			die();
			 
		}
		
		
		/**
		 * Loads the text domain
		 *
		 * @access  private
		 * @since   1.0
		 *
		 * @return  void
		 */
		public function archivespostaccordion_text_domain() {

		  /**
		   * Load text domain
		   */
		   load_plugin_textdomain( 'archivespostaccordion', false, avpt_plugin_dir . '/languages' );
			
		}
		 
		/**
		 * Load and register widget settings
		 *
		 * @access  private
		 * @since   1.0
		 *
		 * @return  void
		 */ 
		public function initArchivesPostAccordion() { 
			
		  /**
		   * Widget registration
		   */ 
		 	register_widget( 'archivesPostAccordionWidget_Admin' );
			
		}  
		 
		/**
		 * Create different panel from the post dates
		 *
		 * @access  public
		 * @since   1.0 
		 * @param   string $_post_type Type of the posts
		 * @param   string $date_format assign the date format like month and year or only year 
		 * @return  array  An array of the date
		 */
		public function getPanelArray( $_post_type = "", $date_format = "" ) { 
			
			global $wpdb; 
			
			$_panel_fetch_format_display_text = "%M - %Y";
			$_panel_fetch_format_comapre_text = "%m%Y";
			$_category_filter_query = "";
			if($date_format == "year") {
				$_panel_fetch_format_display_text = "%Y";
				$_panel_fetch_format_comapre_text = "%Y";
			}
			
			$_check_type = "";
			if( $_post_type != "" ) {
				$_check_type = " and wp.post_type = '".sanitize_text_field($_post_type)."' ";	 
			}
			
			/*$avpt_exclude_category = "";
			if( $avpt_exclude_category != "" && $avpt_exclude_category != "0" )  {				
				$_category_filter_query .= ( " INNER JOIN {$wpdb->prefix}term_relationships as wtr on wtr.term_taxonomy_id  in (".sanitize_text_field($avpt_exclude_category).") and wtr.object_id = wp.ID" );
			}*/

			$_arr_list = array();  
			
			$_result_items = $wpdb->get_results( " SELECT DATE_FORMAT(wp.post_date,'".$_panel_fetch_format_display_text."') as d1, DATE_FORMAT(wp.post_date,'".$_panel_fetch_format_comapre_text."') as d2 FROM `{$wpdb->prefix}posts` as wp ".$_category_filter_query." where wp.post_status = 'publish' ".$_check_type." group by DATE_FORMAT(wp.post_date,'".$_panel_fetch_format_display_text."') order by DATE_FORMAT(wp.post_date,'".$_panel_fetch_format_comapre_text."') desc" ); 
			
			foreach( $_result_items as $_value ) {
			
				$_arr_list["a".$_value->d2] = $_value->d1; 
			
			} 
			
			return $_arr_list;	
		
		}    
		
		/**
		 * Short terms hierarchy order
		 *
		 * @access  public
		 * @since   1.0
		 *
		 * @param   array $terms terms array to make hierarchy
		 * @return  object It contains all the hierarchy terms for shop
		 */
		function sort_terms_hierarchy(Array &$terms) {
			$result = array();
			$parent = 0;
			$depth = 0;
			$i = 0;
			do {
				$temp = array();
				foreach($terms as $j => $term) {
					if ($term->parent == $parent) { 
						if(isset($term->term_group))
					 	$term->term_group = $depth;  
						array_push($temp, $term);
						unset($terms[$j]);
					} 
					if(isset($term->category))
				 	$term->category = $term->name;
					if(isset($term->id))
				 	$term->id = $term->term_id;
				}
				array_splice($result, $i, 0, $temp);
				if(isset($result[$i])){
					$parent = $result[$i]->term_id;
					$depth = $result[$i]->term_group + 1;
				}
			} while ($i++ < count($result));
			$terms = $result;
		} 
		
		/**
		 * Get the number of dash string
		 *
		 * @access  public
		 * @since   1.0
		 *
		 * @param   number $depth Numeric value that indicates the depth of term
		 * @return  string It returns dash string.
		 */
		function get_hierarchy_dash($depth) {
			$_dash = "";
			for( $i = 0; $i < $depth; $i++ ) {
				$_dash .= "--"; 
			} 
			return $_dash." ";
		}
		
		/**
		 * Get the new image as per width and height from a image source based on new image size calculation
		 *
		 * @access  public
		 * @since   1.0
		 *
		 * @param   string $img_sc Path of the image 
		 * @param   string $re_width  New width of the image to be displayed on front view
		 * @param   string $re_height  New height of the image to be displayed on front view 
		 * @return  array It returns array of the image size.
		 */ 		
		function getWPImage($img_sc, $re_width, $re_height) { 
		 
			$quality = 80; 
			if($re_height=="auto")
				$re_height = "180";
			
			$file_parts = explode(".", $img_sc);
			$extention = strtolower( $file_parts[ count( $file_parts ) - 1 ] );
			$_site_urlpath = $directory_cache_root = 'wp-content/uploads'; 
			if(!is_dir($directory_cache_root)) { 
				$directory_cache_root = '../wp-content/uploads';
			} 
			
			$directory_cache = $directory_cache_root.'/pl_cache'; 
			$cache = md5( $img_sc . $re_width . $re_height ).".".strtolower($extention); 
			if(!file_exists($directory_cache)) { 
				mkdir($directory_cache); 
				chmod($directory_cache, 0777);
			}   		
			
			$img_type = array(
				'jpg'  => 'image/jpeg',
				'jpeg' => 'image/jpeg',
				'png'  => 'image/png',
				'gif'  => 'image/gif',
				'bmp'  => 'image/bmp',  
			); 
			$imgtype = $img_type[$extention];
			if(!strlen($imgtype)) { $imgtype = 'unknown'; }		
			 
			$image_url = $directory_cache . '/' . $cache;  
			$_site_urlpath = $_site_urlpath . '/pl_cache/' . $cache; 
			 
			if ( !file_exists($image_url)) { 
				if( touch( $image_url ) ) { 
					chmod( $image_url, 0666 ); 
				}  
			}   
			 
			$max_width = $re_width;
			$max_height = $re_height;  
			$image = $img_sc;   
			$size	= GetImageSize( $image );
			$mime	= $size['mime']; 

			$width = $size[0];
			$height = $size[1]; 
			$color		= FALSE; 
			if (!$max_width && $max_height) 
				$max_width	= 99999999999999; 
			elseif ($max_width && !$max_height) 
				$max_height	= 99999999999999; 
			 
			if ( $max_width >= $width && $max_height >= $height ) {
				 $max_width = $width;
				 $max_height = $height;
			}  
			$xRatio		= $max_width / $width;
			$yRatio		= $max_height / $height; 
			if ($xRatio * $height < $max_height) {  
				$img_new_height	= ceil($xRatio * $height);
				$img_new_width	= $max_width;
			} else {
				$img_new_width	= ceil($yRatio * $width);
				$img_new_height	= $max_height;
			}  
			$quality = 90;   
			 
			$img_dest = imagecreatetruecolor($img_new_width, $img_new_height); 
			switch ($size['mime'])
			{
				case 'image/gif': 
					$img_create	= 'ImageCreateFromGif';
					$img_output_function = 'ImagePng';
					$mime = 'image/png';  
					$is_sharpen = FALSE;
					$quality = round(10 - ($quality / 10));  
				break; 
				case 'image/x-png':
				case 'image/png':
					$img_create	= 'ImageCreateFromPng';
					$img_output_function = 'ImagePng';
					$is_sharpen = FALSE;
					$quality = round(10 - ($quality / 10)); 
				break;
				
				default:
					$img_create	= 'ImageCreateFromJpeg';
					$img_output_function = 'ImageJpeg';
					$is_sharpen = TRUE;
				break;
			}
			 
			$img_source	= $img_create( $image); 
			if (in_array($size['mime'], array('image/gif', 'image/png'))) {
				if (!$color) { 
					imagealphablending($img_dest, false);
					imagesavealpha($img_dest, true);
				}
				else {
					 if ($color[0] == '#')
						$color = substr($color, 1);
					
					$background	= FALSE;
					
					if (strlen($color) == 6)
						$background	= imagecolorallocate($img_dest, hexdec($color[0].$color[1]), hexdec($color[2].$color[3]), hexdec($color[4].$color[5]));
					else if (strlen($color) == 3)
						$background	= imagecolorallocate($img_dest, hexdec($color[0].$color[0]), hexdec($color[1].$color[1]), hexdec($color[2].$color[2]));
					if ($background)
						imagefill($img_dest, 0, 0, $background);
				}
			}
			 
			ImageCopyResampled($img_dest, $img_source, 0, 0, 0, 0, $img_new_width, $img_new_height, $width, $height);

			if ($is_sharpen) {
				 
				$img_new_width	= $img_new_width * (750.0 / $width);
				$ik_a		= 52;
				$ik_b		= -0.27810650887573124;
				$ik_c		= .00047337278106508946;
				
				$ik_result = $ik_a + $ik_b * $img_new_width + $ik_c * $img_new_width * $img_new_width; 
				$srp	= max(round($ik_result), 0);
				
				$image_sharpen	= array(
					array(-1, -2, -1),
					array(-2, $srp + 12, -2),
					array(-1, -2, -1)
				);
				$divisor		= $srp;
				$offset			= 0;
				imageconvolution($img_dest, $image_sharpen, $divisor, $offset);
			} 
			$img_output_function($img_dest, $image_url, $quality); 
			ImageDestroy($img_source);
			ImageDestroy($img_dest);  
			 
			return  $_site_urlpath; 
		 
		 }
		 
		/**
		 * Get post image by given image attachment id and image size
		 *
 		 * @access  public
		 * @since   1.0
		 *
		 * @param   int $img  Attachment ID of the image
		 * @param   int $width  Specify the new width of the image
		 * @param   int $height  Specify the new height of the image 
		 * @return  string  Returns the image html from the post attachment
		 */
		 public function getPostImage( $img, $width = 180, $height = 180) {
				
			$image_link = wp_get_attachment_url( $img ); 
			  
			if( $image_link ) {				
				$image_title = esc_attr( get_the_title( $img ) ); 
				$_src = site_url()."/".$this->getWPImage($image_link, $width, $height);  
				return "<div style='min-height:".$height."px'><img title='".$image_title."'  alt='".$image_title."'  src='".$_src."' /></div>";
			} else {
				$_defa_media_image = $this->_config["avpt_media"]["media_url"]."images/no-img.png";
				$_src = site_url()."/".$this->getWPImage( $_defa_media_image, $width, $height);
				return "<div style='min-height:".$height."px'><img src='".$_src."' /></div>";		 
			} 
		 } 
		 
		/**
		 * Get all the categories
		 *
		 * @access  public
		 * @since   1.0
		 *
		 * @param   string $category_ids Specify the comma separated categories IDs 
		 * @param   string $category_type Type of the categories
		 * @param   string $avpt_hide_empty_category Allow to hide or show empty categories
		 * @return  object It contains all the categories by type or IDs
		 */ 
		public function getCategories( $category_ids = "", $category_type="", $avpt_hide_empty_category="" ) {

			global $wpdb; 
			
			if( $avpt_hide_empty_category == "" )
			$avpt_hide_empty_category= $this->_config["avpt_hide_empty_category"]; 
			
			if( trim($avpt_hide_empty_category) != "" ) { 
				$avpt_hide_empty_category = sanitize_text_field( $avpt_hide_empty_category ); 
			}
			
			$avpt_hide_empty_category_flag = false;
			if($avpt_hide_empty_category=="yes")
				$avpt_hide_empty_category_flag = true;  
			 

			$__category_type = $this->_config["avpt_category_type"];
			
			if( trim($category_type) != "" ) { 
				$__category_type = sanitize_text_field( $category_type ); 
			} 
			 
			if(trim($category_ids) != "")
				$_cats = get_terms( $__category_type, array('include'=>$category_ids,'hide_empty'=>$avpt_hide_empty_category_flag,'order'=>"ASC") );
			else	
				$_cats = get_terms( $__category_type, array('hide_empty'=>$avpt_hide_empty_category_flag,'order'=>"ASC") );  
			
			$_cats = (array)$_cats;
			$this->sort_terms_hierarchy($_cats);  
			return $_cats;
			
		}
		
		/**
		 * Get all the categories types
		 *
		 * @access  public
		 * @since   1.0
		 *
		 * @return  object It contains all the types of categories
		 */
		public function archivespostaccordion_getCategoryTypes() {
		
			global $wpdb;
			 
			return $wpdb->get_results( "select taxonomy from {$wpdb->prefix}term_taxonomy group by taxonomy" );
		
		}
		
		/**
		 * Get all the post types
		 *
		 * @access  public
		 * @since   1.0
		 *
		 * @return  object It contains all the types of posts
		 */
		public function getPostTypes() {
		
			global $wpdb;
			 
			return $wpdb->get_results( "SELECT post_type FROM {$wpdb->prefix}posts group by post_type" );
		
		}
		
		 /**
		* Get the total numbers of posts
		*
		* @access  public
		* @since   1.0
		*
		* @param   float  $_date_format  		Set the date format
		* @param   int    $category_id  		Category ID 
		* @param   string $post_search_text  	Post name or any search keyword to filter the posts
		* @param   int    $c_flg  				Whether to fetch posts by category id or prevent for searching
		* @param   int    $is_default_category_with_hidden   Allow to check settings for the default category
		* @return  int	  Total number of posts  	
		*/  
		public function avpt_getTotalPosts( $_date_format=0, $category_id, $post_search_text, $c_flg, $is_default_category_with_hidden) { 
		
			global $wpdb;   
			 
		   /**
			* Fetch posts as per search filter
			*/	
			$_res_total = $this->getSqlResult( $_date_format, $category_id, $post_search_text, 0, 0, $c_flg, $is_default_category_with_hidden, 1, 0);
			
			return $_res_total[0]->total_val;
			 
		}
		
	   /**
		* Fetch the posts data by formatted date, category, search text and item limit
		*
		* @access  public
		* @since   1.0 
		*
		* @param   float  $date_format  		Set the date format 
		* @param   int    $category_id  		Category ID 
		* @param   string $post_search_text  	Post name or any search keyword to filter posts
		* @param   int    $_limit_start  		Limit to fetch post starting from given position
		* @param   int    $_limit_end  			Limit to fetch post ending to given position
		* @param   int    $category_flg  		Whether to fetch posts by category id or prevent for searching
		* @param   int    $is_default_category_with_hidden  Allow to check settings for the default category
		* @param   int    $is_count  			Whether to fetch only number of posts from database as count or do not set it
		* @return  object Set of searched post data
		*/
		 function getSqlResult( $date_format, $category_id, $post_search_text, $_limit_start, $_limit_end, $category_flg = 0, $is_default_category_with_hidden = 0, $is_count = 0) {
			
			global $wpdb; 
			$_category_filter_query = "";
			$_post_text_filter_query = "";
			$_fetch_fields = "";
			$_limit = "";
			
			$__post_type = $this->_config["post_type"]; 
			if( isset( $_REQUEST['post_type'] ) && trim( $_REQUEST['post_type'] ) != "" ) {
			   $__post_type = sanitize_text_field( $_REQUEST['post_type'] );
			} 
			
			/*$avpt_exclude_category = "";
			if( isset( $_REQUEST['avpt_exclude_category'] ) && trim( $_REQUEST['avpt_exclude_category'] ) != "" ) {
			   $avpt_exclude_category = sanitize_text_field( $_REQUEST['avpt_exclude_category'] );
			}*/
			
			$avpt_shorting_posts_by = $this->_config["avpt_shorting_posts_by"];
			$avpt_post_ordering_type = $this->_config["avpt_post_ordering_type"];
			if( isset( $_REQUEST['avpt_shorting_posts_by'] ) && (trim( $_REQUEST['avpt_shorting_posts_by'] ) == "id" || trim( $_REQUEST['avpt_shorting_posts_by'] ) == "title" || trim( $_REQUEST['avpt_shorting_posts_by'] ) == "date" ) ) {			
				$avpt_shorting_posts_by = sanitize_text_field( $_REQUEST['avpt_shorting_posts_by'] );	 
			}
			
			if(trim($avpt_shorting_posts_by)=="id")
				$avpt_shorting_posts_by = "ID";
			if(trim($avpt_shorting_posts_by)=="title")
				$avpt_shorting_posts_by = "post_title";
			if(trim($avpt_shorting_posts_by)=="date")
				$avpt_shorting_posts_by = "post_date";	
				
			if( isset( $_REQUEST['avpt_post_ordering_type'] ) && ( trim( $_REQUEST['avpt_post_ordering_type'] ) == "ascending" || trim( $_REQUEST['avpt_post_ordering_type'] ) == "descending" ) ) {			
				$avpt_post_ordering_type = sanitize_text_field( $_REQUEST['avpt_post_ordering_type'] );	 
			}
			if(trim($avpt_post_ordering_type)=="ascending")
				$avpt_post_ordering_type = "ASC";	
			if(trim($avpt_post_ordering_type)=="descending")
				$avpt_post_ordering_type = "DESC";
			 
		   /**
			* Prepare safe mysql database query
			*/
			
			if( strpos( $category_id, "," ) > 0 ) {
				$arr_category_id = explode( "," , $category_id );
				$category_id = array();
				foreach ($arr_category_id as $__k => $__v) {
					$category_id[] = intval($__v);	
				}
				$category_id  = implode("','", $category_id);
			} else {
				$category_id  = intval( $category_id );
			} 
			 
			if( $is_count == 1 ) {
				if( trim($category_id) != "0" && ( $category_flg == 1 || $is_default_category_with_hidden == 1 ) ) {
					$_category_filter_query .=  " INNER JOIN {$wpdb->prefix}term_relationships as wtr on wtr.term_taxonomy_id in ('".$category_id."') and wtr.object_id = wp.ID ";
				} 
				$_fetch_fields = " count(*) as total_val ";
			} else { 
				if(  trim($category_id) != "0" ) {
					$_category_filter_query .=  " INNER JOIN {$wpdb->prefix}term_relationships as wtr on wtr.term_taxonomy_id in ('".$category_id."') and wtr.object_id = wp.ID ";
				} 
				$_fetch_fields = " wp.post_content, wp.post_type,  wu.display_name, wp.post_author, wp.post_date, pm_image.meta_value as post_image, wp.ID as post_id, wp.post_title as post_name ";
				$_limit = $wpdb->prepare( " group by wp.ID order by wp.".$avpt_shorting_posts_by." ".$avpt_post_ordering_type."  limit  %d, %d ", $_limit_start, $_limit_end );
			} 
			 
			
			if( $post_search_text != "" ) {
				$_post_text_filter_query .= trim( " and wp.post_title like '%".esc_sql( $post_search_text )."%'" );
			}
			
		/*	if( $avpt_exclude_category != "" &&  ( $category_id <= 0 || trim($category_id) == "" || trim($category_id) == "0" ) )  {				
				$_category_filter_query .= ( " INNER JOIN {$wpdb->prefix}term_relationships as wtr on wtr.term_taxonomy_id  in (".sanitize_text_field($avpt_exclude_category).") and wtr.object_id = wp.ID" );			 	
			} */
			 
			if( trim( $date_format ) != "all" && trim( $date_format ) != "0"  && trim( $date_format ) != "" ){
				$date_format = explode("a",$date_format);
				$date_format = $date_format[1];
				$_post_text_filter_query .=  " and ( DATE_FORMAT(wp.post_date,'%m%Y') = ".intval($date_format)." OR DATE_FORMAT(wp.post_date,'%Y') = ".intval($date_format)." )";   
			}    
			
		   /**
			* Fetch post data from database
			*/
			$_result_items = $wpdb->get_results( "select $_fetch_fields from {$wpdb->prefix}posts as wp  
				$_category_filter_query LEFT JOIN {$wpdb->prefix}postmeta as pm_image on pm_image.post_id = wp.ID and pm_image.meta_key = '_thumbnail_id' inner JOIN {$wpdb->base_prefix}users as wu on wu.ID = wp.post_author where wp.post_status = 'publish' and wp.post_type = '".$__post_type."' $_post_text_filter_query $_limit " ); 
				
			return $_result_items;
		
		}
		
		/**
		 * Replace the specific text into the string
		 *
		 * @access  public
		 * @since   1.0
		 *
		 * @param   string $wc_string  Content string
		 * @param   string $replace_from  string to be replaced from
		 * @param   string $replace_to  By which string should be replaced 
		 * @return  string Returns replaced string content
		 */ 
		function avpt_js_obj($data_object) { 

				$_js_data_ob = array(); 
				foreach( $data_object as $ob_key => $ob_val ) {
					 
					if( (isset($this->_plugin_settings[$ob_key]["in_js"]) && $this->_plugin_settings[$ob_key]["in_js"] == "yes") ||  $ob_key == "vcode" )
						$_js_data_ob[] = $ob_key.":'".esc_js($ob_val)."'";				

				}
				return 'var request_obj_'.$data_object["vcode"].' = { '.implode( ",", $_js_data_ob ).' } ';				
		}  
		
		 
		/**
		 * Get Unique Block ID
		 *
		 * @access  public
		 * @since   1.0
		 *
		 * @return  string 
		 */
		public function getUCode() { 
			return 'uid_'.md5( "KASITAJDDRAM@APAP".time() ); 
		} 
		
		/**
		 * Load the plugin templates
		 *
		 * @access  public
		 * @since   1.0
		 *
		 * @param   string $file Template file name
		 * @return  string Returns template file path
		 */
		public function getArchivesPostAccordionTemplate( $file ) { 
		
			// Get template file path
			if( locate_template( $file ) != "" ) {
				return locate_template( $file );
			} else {
				return plugin_dir_path( dirname( __FILE__ ) ) . 'templates/' . $file ;
			}  
				
	    } 
	   
	   /**
		 * Validate the plugin
		 *
		 * @access  public
		 * @since   1.0
		 *
		 * @return    void
		 */
	    function setPluginValue() { 
		
			$license 	= get_option( 'archivespostaccordion_license_key' );
			$status 	= get_option( 'archivespostaccordion_license_status' );
			$_valid_key = md5(home_url().$status.$license);
			$ls_reff 	= get_option( 'archivespostaccordion_license_reff' );
			$_st = 'ac';
			
			if( $ls_reff != $_valid_key ) {
			
				$license = trim( get_option( 'archivespostaccordion_license_key' ) );
				  
				$api_params = array( 
					'action'=> 'deactivate_license', 
					'license' 	=> $license, 
					'item_name' => 'wp_archivespostaccordion', 
					'url'       => home_url()
				);
				
				$response = wp_remote_get( add_query_arg( $api_params, $this->_config["archivespostaccordion_license_url"]["license_url"] ), array( 'timeout' => 15, 'sslverify' => false ) );
				if ( is_wp_error( $response ) )
					wp_redirect(site_url()."/wp-admin/edit.php?post_type=avpt_archives&page=archivespostaccordion_settings&st=10");
				
				$license_data = json_decode( wp_remote_retrieve_body( $response ) );
				
				delete_option( 'archivespostaccordion_license_status' );
				delete_option( 'archivespostaccordion_license_key' );
				delete_option( 'archivespostaccordion_license_reff' );
				$this->_config['st']['flag'] = $_st."r";
				
			}
		
		}	
	   
	   /**
		* Register post type for shortcode
		*
		* @access  private
		* @since   1.0
		*
		* @return  void
		*/  
		function avpt_registerPostType() { 
			
		   /**
			* Post type and menu labels 
			*/
			$labels = array(
				'name' => __('Archive Posts Accordion Panel', 'archivespostaccordion' ),
				'singular_name' => __( 'Archive Posts Accordion Panel', 'archivespostaccordion' ),
				'add_new' => __( 'Add New Shortcode', 'archivespostaccordion' ),
				'add_new_item' => __( 'Add New Shortcode', 'archivespostaccordion' ),
				'edit_item' => __( 'Edit', 'archivespostaccordion'  ),
				'new_item' => __( 'New', 'archivespostaccordion'  ),
				'all_items' => __( 'All', 'archivespostaccordion'  ),
				'view_item' => __( 'View', 'archivespostaccordion'  ),
				'search_items' => __( 'Search', 'archivespostaccordion'  ),
				'not_found' =>  __( 'No item found', 'archivespostaccordion'  ),
				'not_found_in_trash' => __( 'No item found in Trash', 'archivespostaccordion'  ),
				'parent_item_colon' => '',
				'menu_name' => __( 'AVPT', 'archivespostaccordion'  ) 
			);
			
		   /**
			* Archive Posts Accordion Panel post type registration options
			*/
			$args = array(
				'labels' => $labels,
				'public' => false,
				'publicly_queryable' => false,
				'show_ui' => true,
				'show_in_menu' => true,
				'query_var' => false,
				'rewrite' => false,
				'capability_type' => 'post',
				'menu_icon' => 'dashicons-list-view',
				'has_archive' => false,
				'hierarchical' => false,
				'menu_position' => null,
				'supports' => array( 'title' )
			);
			
		   /**
			* Register archivespostaccordion post type
			*/ 
			 register_post_type( 'avpt_archives', $args );
			
			
		}
	   
	   
	   
	    /**
		 * Load the pagination with list of items
		 *
		 * @access  public
		 * @since   1.0
		 *
		 * @param   int $org_page Specify the current page no.
		 * @param   int $total Specify the total number of the pages
		 * @param   int $category_id Category ID
		 * @param   int $_limit_start  Limit to fetch post starting from given position
		 * @param   int $_limit_end    Limit to fetch post ending to given position
		 * @param   string $params_vcode  Specify the plugin view code
		 * @param   int $flg Specify to show only next previous pagination or show the full pagination links 
		 * @return  string Returns ajax pagination links
		 */ 
	   function displayPagination(  $org_page, $total, $category_id, $_limit_start, $_limit_end, $params_vcode, $flg = 1 ) {

				$page = ($org_page == 0 ? 1 : $org_page + 1); 
				$start = ($page - 1) * $_limit_end;                              
				$adj = "1"; 
				$prev = (intval($org_page) == 0)?1:$page;             
				$next = (intval($org_page) == 0)?1:$page;
				$pageEnd = ceil($total/$_limit_end);
				$nxtprv = $pageEnd - 1; 
				$setPaginate = "";
				if($pageEnd > 1)
				{  
					$setPaginate .= "<ul class='avpt-st-paging'>";  
					if($page>1)	
					$setPaginate.= "<li><a  href=\"javascript:void(0)\" onclick = 'avpt_loadMorePosts( \"".esc_js( $category_id )."\", \"".esc_js( ( ( ($prev-1) * intval($_limit_end)) - intval($_limit_end) ) )."\", \"".esc_js( $params_vcode."-".$category_id )."\", \"".esc_js( $total )."\", request_obj_".esc_js( $params_vcode )." )' > &laquo; ".__( 'Previous', 'archivespostaccordion' )."</a></li>";
					
					$setPaginate1 = ""; 
					
					if ($pageEnd < 7 + ($adj * 2))
					{  
						for ($counter = 1; $counter <= $pageEnd; $counter++)
						{
							if ($counter == $page)
								$setPaginate1.= "<li><a class='current_page'>$counter</a></li>";
							else
								$setPaginate1.= "<li><a href=\"javascript:void(0)\" onclick = 'avpt_loadMorePosts( \"".esc_js( $category_id )."\", \"".esc_js( ( ($counter * intval($_limit_end)) - intval($_limit_end) ) )."\", \"".esc_js( $params_vcode."-".$category_id )."\", \"".esc_js( $total )."\", request_obj_".esc_js( $params_vcode )." )' >$counter</a></li>";                 
						}
					}
					elseif($pageEnd > 5 + ($adj * 2))
					{
						if($page < 1 + ($adj * 2))    
						{
							for ($counter = 1; $counter < 4 + ($adj * 2); $counter++)
							{
								if ($counter == $page)
									$setPaginate1.= "<li><a class='current_page'>$counter</a></li>";
								else
									$setPaginate1.= "<li><a href=\"javascript:void(0)\" onclick = 'avpt_loadMorePosts( \"".esc_js( $category_id )."\", \"".esc_js( ( ($counter * intval($_limit_end)) - intval($_limit_end) ) )."\", \"".esc_js( $params_vcode."-".$category_id )."\", \"".esc_js( $total )."\", request_obj_".esc_js( $params_vcode )." )' >$counter</a></li>";     
									
							}
							$setPaginate1.= "<li class='dot'>...</li>";
							$setPaginate1.= "<li><a href=\"javascript:void(0)\" onclick = 'avpt_loadMorePosts( \"".esc_js( $category_id )."\", \"".esc_js( ( ($nxtprv * intval($_limit_end)) - intval($_limit_end) ) )."\", \"".esc_js( $params_vcode."-".$category_id )."\", \"".esc_js( $total )."\", request_obj_".esc_js( $params_vcode )." )' >$nxtprv</a></li>";
							$setPaginate1.= "<li><a href=\"javascript:void(0)\" onclick = 'avpt_loadMorePosts( \"".esc_js( $category_id )."\", \"".esc_js( ( ($pageEnd * intval($_limit_end)) - intval($_limit_end) ) )."\", \"".esc_js( $params_vcode."-".$category_id )."\", \"".esc_js( $total )."\", request_obj_".esc_js( $params_vcode )." )' >$pageEnd</a></li>";     
							 
						}
						elseif($pageEnd - ($adj * 2) > $page && $page > ($adj * 2))
						{
							$setPaginate1.= "<li><a href=\"javascript:void(0)\" onclick = 'avpt_loadMorePosts( \"".esc_js( $category_id )."\", \"".esc_js( ( ( 1 * intval($_limit_end)) - intval($_limit_end) ) )."\", \"".esc_js( $params_vcode."-".$category_id )."\", \"".esc_js( $total )."\", request_obj_".esc_js( $params_vcode )." )'>1</a></li>";
							$setPaginate1.= "<li><a href=\"javascript:void(0)\" onclick = 'avpt_loadMorePosts( \"".esc_js( $category_id )."\", \"".esc_js( ( ( 2 * intval($_limit_end)) - intval($_limit_end) ) )."\", \"".esc_js( $params_vcode."-".$category_id )."\", \"".esc_js( $total )."\", request_obj_".esc_js( $params_vcode )." )'>2</a></li>";
							$setPaginate1.= "<li class='dot'>...</li>";
							for ($counter = $page - $adj; $counter <= $page + $adj; $counter++)
							{
								if ($counter == $page)
									$setPaginate1.= "<li><a class='current_page'>$counter</a></li>";
								else if( $counter != 2 )
									$setPaginate1.= "<li><a href=\"javascript:void(0)\" onclick = 'avpt_loadMorePosts( \"".esc_js( $category_id )."\", \"".esc_js( ( ($counter * intval($_limit_end)) - intval($_limit_end) ) )."\", \"".esc_js( $params_vcode."-".$category_id )."\", \"".esc_js( $total )."\", request_obj_".esc_js( $params_vcode )." )' >$counter</a></li>";                 
							}
							$setPaginate1.= "<li class='dot'>..</li>";
							$setPaginate1.= "<li><a href=\"javascript:void(0)\" onclick = 'avpt_loadMorePosts( \"".esc_js( $category_id )."\", \"".esc_js( ( ($nxtprv * intval($_limit_end)) - intval($_limit_end) ) )."\", \"".esc_js( $params_vcode."-".$category_id )."\", \"".esc_js( $total )."\", request_obj_".esc_js( $params_vcode )." )' >$nxtprv</a></li>";
							$setPaginate1.= "<li><a href=\"javascript:void(0)\" onclick = 'avpt_loadMorePosts( \"".esc_js( $category_id )."\", \"".esc_js( ( ($pageEnd * intval($_limit_end)) - intval($_limit_end) ) )."\", \"".esc_js( $params_vcode."-".$category_id )."\", \"".esc_js( $total )."\", request_obj_".esc_js( $params_vcode )." )' >$pageEnd</a></li>";     
						}
						else
						{
							$setPaginate1.= "<li><a href=\"javascript:void(0)\" onclick = 'avpt_loadMorePosts( \"".esc_js( $category_id )."\", \"".esc_js( ( ( 1 * intval($_limit_end)) - intval($_limit_end) ) )."\", \"".esc_js( $params_vcode."-".$category_id )."\", \"".esc_js( $total )."\", request_obj_".esc_js( $params_vcode )." )' >1</a></li>";
							$setPaginate1.= "<li><a href=\"javascript:void(0)\" onclick = 'avpt_loadMorePosts( \"".esc_js( $category_id )."\", \"".esc_js( ( ( 2 * intval($_limit_end)) - intval($_limit_end) ) )."\", \"".esc_js( $params_vcode."-".$category_id )."\", \"".esc_js( $total )."\", request_obj_".esc_js( $params_vcode )." )'>2</a></li>";
							$setPaginate1.= "<li class='dot'>..</li>";
							for ($counter = $pageEnd - (2 + ($adj * 2)); $counter <= $pageEnd; $counter++)
							{
								if ($counter == $page)
									$setPaginate1.= "<li><a class='current_page'>$counter</a></li>";
								else
									$setPaginate1.= "<li><a href=\"javascript:void(0)\" onclick = 'avpt_loadMorePosts( \"".esc_js( $category_id )."\", \"".esc_js( ( ($counter * intval($_limit_end)) - intval($_limit_end) ) )."\", \"".esc_js( $params_vcode."-".$category_id )."\", \"".esc_js( $total )."\", request_obj_".esc_js( $params_vcode )." )'>$counter</a></li>";                 
							}
						}
					} 
							
					if( $flg == 1) {
						$setPaginate .= $setPaginate1;
					} else {
						$setPaginate .= "<li class='bet-pages'>$page / $pageEnd</li>";
					}

					if ($page < $counter - 1){
						$setPaginate.= "<li><a  href=\"javascript:void(0)\" onclick = 'avpt_loadMorePosts( \"".esc_js( $category_id )."\", \"".esc_js( ( ( ($next+1) * intval($_limit_end)) - intval($_limit_end) ) )."\", \"".esc_js( $params_vcode."-".$category_id )."\", \"".esc_js( $total )."\", request_obj_".esc_js( $params_vcode )." )' >".__( 'Next', 'archivespostaccordion' )."  &raquo;</a></li>";						
					} 
					
					$setPaginate.= "<li><a class='wp-load-icon-li'><img width='18px' height='18px' src='".avpt_media."images/loader.gif' /></a></li>";
		 
					$setPaginate.= "</ul>\n";    
				}
			 
			 
				return $setPaginate;
		}
   }
}