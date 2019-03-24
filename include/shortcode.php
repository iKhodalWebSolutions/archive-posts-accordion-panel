<?php 
/** 
 * Register custom post type to manage shortcode
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly   
if ( ! class_exists( 'archivesPostAccordionShortcode_Admin' ) ) {
	class archivesPostAccordionShortcode_Admin extends archivesPostAccordionLib {
	
		public $_shortcode_config = array();
		 
		/**
		 * Constructor method.
		 *
		 * Register post type for accordion panel shortcode
		 * 
		 * @access    public
		 * @since     1.0
		 *
		 * @return    void
		 */
		public function __construct() {
			
			parent::__construct();
			
	       /**
		    * Register hooks to manage custom post type
		    */
			add_action( 'init', array( &$this, 'avpt_registerPostType' ) );  
			// add_action( 'admin_menu', array( &$this, 'avpt_addadminmenu' ) );  
			add_action( 'add_meta_boxes', array( &$this, 'add_archivespostaccordion_metaboxes' ) );
			add_action( 'save_post', array(&$this, 'wp_save_archivespostaccordion_meta' ), 1, 2 ); 
			add_action( 'admin_enqueue_scripts', array( $this, 'avpt_admin_enqueue' ) ); 
			
		   /* Register hooks for displaying shortcode column. */ 
			if( isset( $_REQUEST["post_type"] ) && !empty( $_REQUEST["post_type"] ) && trim($_REQUEST["post_type"]) == "avpt_archives" ) {
				add_action( "manage_posts_custom_column", array( $this, 'archivespostaccordionShortcodeColumns' ), 10, 2 );
				add_filter( 'manage_posts_columns', array( $this, 'avpt_shortcodeNewColumn' ) );
			}
			
			add_action( 'wp_ajax_avpt_getCategoriesOnTypes',array( &$this, 'avpt_getCategoriesOnTypes' ) ); 
			add_action( 'wp_ajax_nopriv_avpt_getCategoriesOnTypes', array( &$this, 'avpt_getCategoriesOnTypes' ) );
			 
			add_action( 'wp_ajax_avpt_getListDateArray',array( &$this, 'avpt_getListDateArray' ) ); 
			add_action( 'wp_ajax_nopriv_avpt_getListDateArray', array( &$this, 'avpt_getListDateArray' ) ); 

			add_action( 'wp_ajax_avpt_getExcludeCategoriesOnTypes',array( &$this, 'avpt_getExcludeCategoriesOnTypes' ) ); 
			add_action( 'wp_ajax_nopriv_avpt_getExcludeCategoriesOnTypes', array( &$this, 'avpt_getExcludeCategoriesOnTypes' ) ); 

			add_filter( 'wp_editor_settings', array( $this, 'avpt_postbodysettings' ), 10, 2 );	
			
		}  
		
		/**
		* Admin menu configuration 
		*
		* @access  private
		* @since   1.0
		*
		* @return  void
		*/  
		public function avpt_addadminmenu() { 
		
		
			add_submenu_page('edit.php?post_type=avpt_archives', __( 'All Accordion Posts', 'archivespostaccordion' ), __( 'All Accordion Posts', 'archivespostaccordion' ),  'manage_options', 'edit.php?post_type=avpt_postaccordions');
			
			add_submenu_page('edit.php?post_type=avpt_archives', __( 'New Accordion Post', 'archivespostaccordion' ), __( 'New Accordion Post', 'archivespostaccordion' ),  'manage_options', 'post-new.php?post_type=avpt_postaccordions'); 
			
			add_submenu_page('edit.php?post_type=avpt_archives', __( 'Accordion Categories', 'archivespostaccordion' ), __( 'Accordion Categories', 'archivespostaccordion' ),  'manage_options', 'edit-tags.php?taxonomy=avpt_accordion_categories&post_type=avpt_archives'); 
						
		}
		
		/**
		* Set the post body type
		*
		* @access  private
		* @since   1.0
		*
		* @return  void
		*/  
		public function avpt_postbodysettings( $settings, $editor_id ) { 
		
			global $post; 
			
			if( $post->post_type == "avpt_postaccordions" ) {
			
				$settings = array(
						'wpautop'             => false,
						'media_buttons'       => false,
						'default_editor'      => '',
						'drag_drop_upload'    => false,
						'textarea_name'       => $editor_id,
						'textarea_rows'       => 20,
						'accordionindex'            => '',
						'accordionfocus_elements'   => ':prev,:next',
						'editor_css'          => '',
						'editor_class'        => '',
						'teeny'               => false,
						'dfw'                 => false,
						'_content_editor_dfw' => false,
						'tinymce'             => true,
						'quicktags'           => true
					);
			
			}
			
			return $settings;
			
		}
		
 	   /**
		* Register and load JS/CSS for admin widget configuration 
		*
		* @access  private
		* @since   1.0
		*
		* @return  bool|void It returns false if not valid page or display HTML for JS/CSS
		*/  
		public function avpt_admin_enqueue() {

			if ( ! $this->validate_page() )
				return FALSE;
			
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_script( 'wp-color-picker' );
			wp_enqueue_style( 'admin-archivespostaccordion.css', $this->_config["avpt_media"]["media_url"]."css/admin-archivespostaccordion.css" );
			wp_enqueue_script( 'admin-archivespostaccordion.js', $this->_config["avpt_media"]["media_url"]."js/admin-archivespostaccordion.js" ); 
			
		}		
		 
	   /**
		* Add meta boxes to display shortcode
		*
		* @access  private
		* @since   1.0
		*
		* @return  void
		*/ 
		public function add_archivespostaccordion_metaboxes() {
			
			/**
			 * Add custom fields for shortcode settings
		     */
			add_meta_box( 'wp_archivespostaccordion_fields', __( 'Archive Posts Accordion Panel', 'archivespostaccordion' ),
				array( &$this, 'wp_archivespostaccordion_fields' ), 'avpt_archives', 'normal', 'high' );
			
			/**
			 * Display the saved shortcode
		     */
			add_meta_box( 'wp_archivespostaccordion_shortcode', __( 'Shortcode', 'archivespostaccordion' ),
				array( &$this, 'shortcode_meta_box' ), 'avpt_archives', 'side' );	
		
		}  
		
	   /**
		* Validate widget or shortcode post type page
		*
		* @access  private
		* @since   1.0
		*
		* @return  bool It returns true if page is post.php or widget otherwise returns false
		*/ 
		private function validate_page() {

			if ( ( isset( $_GET['post_type'] )  && $_GET['post_type'] == 'avpt_archives' ) || strpos($_SERVER["REQUEST_URI"],"widgets.php") > 0  || strpos($_SERVER["REQUEST_URI"],"post.php" ) > 0 || strpos($_SERVER["REQUEST_URI"], "archivespostaccordion_settings" ) > 0  )
				return TRUE;
		
		} 			
 
	   /**
		* Display archivespostaccordion block configuration fields
		*
		* @access  private
		* @since   1.0
		*
		* @return  void Returns HTML for configuration fields 
		*/  
		public function wp_archivespostaccordion_fields() {
		
			global $post; 
			 
			foreach( $this->_config as $kw => $kw_val ) {
				$this->_shortcode_config[$kw] = get_post_meta( $post->ID, $kw, true ); 
			}
			 
			foreach ( $this->_shortcode_config as $sc_key => $sc_val ) {
				if( trim( $sc_val ) == "" )
					unset( $this->_shortcode_config[ $sc_key ] );
				else {
					if(!is_array($sc_val) && trim($sc_val) != "" ) 
						$this->_shortcode_config[ $sc_key ] = htmlspecialchars( $sc_val, ENT_QUOTES );
					else 
						$this->_shortcode_config[ $sc_key ] = $sc_val;
				}	
			}
			
			foreach( $this->_config as $kw => $kw_val ) {
				if( isset($this->_shortcode_config[$kw]) && !is_array($this->_shortcode_config[$kw]) && trim($this->_shortcode_config[$kw]) == "" ) {
					$this->_shortcode_config[$kw] = $this->_config[$kw]["default"];
				} 
			}
			
			$this->_shortcode_config["vcode"] = get_post_meta( $post->ID, 'vcode', true );   
			require( $this->getArchivesPostAccordionTemplate( "admin/admin_shortcode_post_type.php" ) );
			
		}
		
	   /**
		* Display shortcode in edit mode
		*
		* @access  private
		* @since   1.0
		*
		* @param   object  $post Set of configuration data.
		* @return  void	   Displays HTML of shortcode
		*/
		public function shortcode_meta_box( $post ) {

			$archivespostaccordion_id = $post->ID;

			if ( get_post_status( $archivespostaccordion_id ) !== 'publish' ) {

				echo '<p>'.__( 'Please make the publish status to get the shortcode', 'archivespostaccordion' ).'</p>';

				return;

			}

			$archivespostaccordion_title = get_the_title( $archivespostaccordion_id );

			$shortcode = sprintf( "[%s id='%s']", 'archivespostaccordion', $archivespostaccordion_id );
			
			echo "<p class='tpp-code'>".$shortcode."</p>";
		}
				  
	   /**
		* Save shortcode fields
		*
		* @access  private
		* @since   1.0 
		*
		* @param   int    	$post_id Post ID
		* @param   object   $post    Object of a post data 
		* @return  void
		*/ 
		function wp_save_archivespostaccordion_meta( $post_id, $post ) {
			
		   /**
			* Verify _nonce from request
			*/
			/* if( !wp_verify_nonce( $_POST['archivespostaccordion_nonce'], plugin_basename(__FILE__) ) ) {
				return $post->ID;
			} */
			
		   /**
			* Check current user permission to edit post
			*/
			if(!current_user_can( 'edit_post', $post->ID ))
				return $post->ID;
				
		   /**
			* sanitize text fields 
			*/
			$archivespostaccordion_meta = array(); 
			
			foreach( $this->_config as $kw => $kw_val ) { 
				if(!isset($_POST["nm_".$kw])) continue;
				$_save_value =  $_POST["nm_".$kw];
				if($kw_val["type"]=="boolean"){
					$_save_value = $_POST["nm_".$kw][0];
				}
				if( $kw_val["type"]=="checkbox" && count($_POST["nm_".$kw]) > 0 ) {
					$_save_value = implode( ",", $_POST["nm_".$kw] );
				}
				$archivespostaccordion_meta[$kw] =  sanitize_text_field( $_save_value );
			}     
			 
			foreach ( $archivespostaccordion_meta as $key => $value ) {
			
			   if( $post->post_type == 'revision' ) return;
				$value = implode( ',', (array)$value );
				
				if( trim($value) == "Array" || is_array($value) )
					$value = "";
					
			   /**
				* Add or update posted data 
				*/
				if( get_post_meta( $post->ID, $key, FALSE ) ) { 
					update_post_meta( $post->ID, $key, $value );
				} else { 
					add_post_meta( $post->ID, $key, $value );
				} 
			
			} 
		}
		 
		
	   /**
		* Display shortcode column into list
		*
		* @access  private
		* @since   1.0
		*
		* @param   string  $column  Column name
		* @param   int     $post_id Post ID
		* @return  void	   Display shortcode in column	
		*/
		public function archivespostaccordionShortcodeColumns( $column, $post_id ) { 
		
			if( $column == "shortcode" ) {
				 echo sprintf( "[%s id='%s']", 'archivespostaccordion', $post_id ); 
			}  
		
		}
		
	   /**
		* Register shortcode column
		*
		* @access  private
		* @since   1.0
		*
		* @param   array  $columns  Column list 
		* @return  array  Returns column list
		*/
		public function avpt_shortcodeNewColumn( $columns ) {
			
			$_edit_column_list = array();	
			$_i = 0;
			
			foreach( $columns as $__key => $__value) {
					
					if($_i==2){
						$_edit_column_list['shortcode'] = __( 'Shortcode', 'archivespostaccordion' );
					}
					$_edit_column_list[$__key] = $__value;
					
					$_i++;
			}
			
			return $_edit_column_list;
		
		}
		
	} 

}

new archivesPostAccordionShortcode_Admin();
 
?>