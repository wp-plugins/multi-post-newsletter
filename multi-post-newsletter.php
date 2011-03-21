<?php
/*
Plugin Name: Multi Post Newsletter
Plugin URI: http://hughwillfayle.de/wordpress/multipostnewsletter
Description: The Multi Post Newsletter is a simple plugin, which provides to link several posts to a newsletter. This procedure is similar to the categories. Within the flexible configuration and templating, you're able to set the newsletters appearance to your requirement.
Author: Thomas Herzog
Version: 0.5.1
Author URI: http://hughwillfayle.de/
*/

if ( ! class_exists( 'multi_post_newsletter' ) ) {
	// Version Check on start up
	register_activation_hook( __FILE__, array( 'multi_post_newsletter', 'on_activate' ) );
	// Init the plugin
	if ( function_exists( 'add_action' ) ) {
		add_action( 'plugins_loaded', array( 'multi_post_newsletter', 'get_object' ) );
	}
	// Load the view
	require_once 'php/multi-post-newsletter-view.php';
	require_once 'php/multi-post-newsletter-model.php';
	require_once 'php/class.html2text.php';
	
	class multi_post_newsletter extends multi_post_newsletter_model {
		static private $classobj;
		public $action;
		
		/**
		 * Get the object of this class
		 */
		public function get_object () {
			if ( null === self::$classobj ) {
				self::$classobj = new self;
			}
			return self::$classobj;
		}
		
		/**
		 * Get the Textdomain
		 */
		public static function get_textdomain () {
			return 'th_mpnl';
		}
	
		/**
		 * Load the textdomain
		 */
		public function load_textdomain () {
			load_plugin_textdomain( multi_post_newsletter::get_textdomain(), FALSE, dirname( plugin_basename( __FILE__ ) ) . '/i18n' );
		}
	
		/**
		 * Get the plugin data
		 * @param unknown_type $value
		 */
		private function get_plugin_data ( $value = 'Version' ) {
			$plugin_data = get_plugin_data( __FILE__ );
			return $plugin_data[$value];
		}
	
		/**
		 * Check the versions
		 */
		static public function on_activate () {
	
			$obj = multi_post_newsletter::get_object();
			$obj->load_textdomain();
	
			global $wp_version;
	
			// check wp version
			if ( ! version_compare( $wp_version, '3.0', '>=' ) ) {
				deactivate_plugins( __FILE__ );
				die( 
					wp_sprintf( 
						'<strong>%s:</strong> ' . 
						__( 'Sorry, This plugin requires WordPress 3.0+', $obj->get_textdomain() ), 
						self::get_plugin_data( 'Name' )
					)
				);
			}
	
			// check php version
			if ( ! version_compare( PHP_VERSION, '5.2.0', '>=' ) ) {
				deactivate_plugins( __FILE__ ); // Deactivate ourself
				die( 
					wp_sprintf(
						'<strong>%1s:</strong> ' . 
						__( 'Sorry, This plugin has taken a bold step in requiring PHP 5.0+, Your server is currently running PHP %2s, Please bug your host to upgrade to a recent version of PHP which is less bug-prone. At last count, <strong>over 80%% of WordPress installs are using PHP 5.2+</strong>.', $obj->get_textdomain() )
						, self::get_plugin_data( 'Name' ), PHP_VERSION 
					)
				);
			}
		}
		
		public function __construct () {
			$pages = array( 'mpnl_generate', 'mpnl_template', 'mpnl_config', 'newsletter' );
			if ( user_can( get_current_user_id(), 'manage_options' ) && is_admin() ) {
				// Action
				$this->action = $_GET['page'];
				// Load Text-Domain
				$this->load_textdomain();
				// Menu
				add_action( 'admin_menu', array( &$this, 'init_menu' ) );
				// Taxonomy
				add_action( 'init', array( &$this, 'init_taxonomy' ), 0 );
				// Remove menu item
				add_action( 'admin_head', array( &$this, 'remove_custom_taxonomy_menu' ) );
				// Custom Column
				add_filter( 'manage_posts_columns',  array( &$this, 'custom_column_head' ) );
				add_action( 'manage_posts_custom_column', array( &$this, 'custom_column_content' ), 10, 2 );

				add_action( 'wp_ajax_save_post_order', array( $this, 'ajax_save_post_order' ) );
				if ( in_array( $_GET['page'], $pages ) || in_array( $_GET['taxonomy'], $pages ) ) {
					// jQuery, JSON and AJAX Stuff Standards
					wp_enqueue_script( 'multi_post_newsletter', plugin_dir_url( __FILE__ ) . 'js/mpnl.main.js', array( 'jquery', 'json2', 'wp-lists', 'wp-ajax-response', 'utils', 'jquery-ui-core', 'jquery-ui-sortable', 'jquery-ui-draggable', 'jquery-ui-droppable' ) );
				}
			}
		}
		
		/**
		 * Do I really have to explain this?
		 */
		public function init_menu () {
			add_menu_page( 'MP-Newsletter', 'MP-Newsletter', 'manage_options', 'mpnl_generate', array( &$this, 'backend' ) );
			add_submenu_page( 'mpnl_generate', __( 'Generate Newsletter', multi_post_newsletter::get_textdomain() ), __( 'Generate Newsletter', multi_post_newsletter::get_textdomain() ), 'manage_options', 'mpnl_generate', array( &$this, 'backend' ) );
			add_submenu_page( 'mpnl_generate', __( 'Editions', multi_post_newsletter::get_textdomain() ), __( 'Editions', multi_post_newsletter::get_textdomain() ), 'manage_options', 'edit-tags.php?taxonomy=newsletter' );
			add_submenu_page( 'mpnl_generate', __( 'Template', multi_post_newsletter::get_textdomain() ), __( 'Template', multi_post_newsletter::get_textdomain() ), 'manage_options', 'mpnl_template', array( &$this, 'backend' ) );
			add_submenu_page( 'mpnl_generate', __( 'Settings' ), __( 'Settings' ), 'manage_options', 'mpnl_config', array( &$this, 'backend' ) );
		}
		
		/**
		 * Register the custom taxonomie
		 */
		public function init_taxonomy () {
			register_taxonomy(	'newsletter',
								array( 'post' ),
								array(	'public' => true,
										'query_var' => 'newsletter',
										'show_ui' => true,
										'show_tagcloud' => false,
										'hierarchical' => true,
										'show_in_nav_menus' => true,
										'labels' => array(	'name' => __( 'MP-Newsletter - Editions', multi_post_newsletter::get_textdomain() ),
															'singular_name' => __( 'Newsletter Edition', multi_post_newsletter::get_textdomain() ),
															'search_items' => __( 'Search Editions', multi_post_newsletter::get_textdomain() ),
															'popular_items' => __( 'Popular Editions', multi_post_newsletter::get_textdomain() ),
															'all_items' => __( 'All Editions', multi_post_newsletter::get_textdomain() ),
															'parent_item' => __( 'Parent Editions', multi_post_newsletter::get_textdomain() ),
															'parent_item_colon' => __( 'Parent Editions:', multi_post_newsletter::get_textdomain() ),
															'edit_item' => __( 'Edit Edition', multi_post_newsletter::get_textdomain() ),
															'update_item' => __( 'Update Edition', multi_post_newsletter::get_textdomain() ),
															'add_new_item' => __( 'Add Edition', multi_post_newsletter::get_textdomain() ),
															'new_item_name' => __( 'Add Edition', multi_post_newsletter::get_textdomain() ), ), )
			);
		}
		
		/**
		 * Removes the menu entry of the registered custom taxonomy
		 * I am glad, that we only have two layers
		 */
		public function remove_custom_taxonomy_menu () {
			global $submenu;
			foreach ( $submenu as $entries ) {
				foreach ( $entries as $key => $value ) {
					if ( in_array( 'edit-tags.php?taxonomy=newsletter', $value ) ) {
						if ( '1' != $key ) {
							unset( $submenu['edit.php'][$key] );
						}
					}
				}
			}
		}
		
		/**
		 * Show header of the custom column
		 * @param mixed $defaults
		 */
		public function custom_column_head ( $defaults ) {
		    $defaults['newsletter'] = __( 'Newsletter', multi_post_newsletter::get_textdomain() );
		    return $defaults;
		}
		
		/**
		 * Show content of the custom column
		 * @param string $column_name
		 */
		public function custom_column_content ( $column_name ) {
			if( 'newsletter' == $column_name ) {
				global $post;
				echo get_the_term_list( $post->ID, 'newsletter', '', ', ', '' );
		    }
		}

		/**
		 * Load the template
		 */
		public function get_my_template () {
			$template['main_template'] = str_replace( '\\', '', get_option( 'mp-newsletter-template-main' ) );
			$template['post_template'] = str_replace( '\\', '', get_option( 'mp-newsletter-template-post' ) );
			$template['params']        = get_option( 'mp-newsletter-template-params' );
			return $template;
		}
		
		/**
		 * Load the options
		 */
		public function get_my_params () {
			$params = get_option( 'mp-newsletter-params' );
			return $params;
		}
		
		/**
		 * Because of the %LINK% Tag
		 * @param mixed $more
		 */
		function force_clean_expert ( $more ) {
			return '';
		}
		
		/**
		 * Save the post order
		 */
		public function ajax_save_post_order () {
			foreach ( $_POST['order'] as $category_id => $posts ) {
				$posts = split( ',', $posts );
				$i     = 1;
				foreach ( $posts as $post ) {
					$this->save_post_order( $post, $i );
					++$i;
				}
			}
			die;
		}
		
		/**
		 * Formats the content.
		 * @param string $more_link_text
		 * @param int $stripteaser
		 * @param mixed $more_file
		 */
		public function get_the_content_with_formatting ( $more_link_text = '', $stripteaser = 0, $more_file = '' ) {
			$content = get_the_content( $more_link_text, $stripteaser, $more_file );
			$content = apply_filters( 'the_content', $content );
			$content = str_replace( ']]>', ']]&gt;', $content );
			return $content;
		}
			
		/**
		 * Gimme Messages
		 * @param string $msg
		 */
		function display_message ( $msg ) {
			switch ( $msg ) {
				case 'testmail':
					multi_post_newsletter_view::update( __( 'Testmail successfully send!', multi_post_newsletter::get_textdomain() ) );
					break;
				case 'mail':
					multi_post_newsletter_view::update( __( 'Newsletter successfully send!', multi_post_newsletter::get_textdomain() ) );
					break;
				case 'settings':
					multi_post_newsletter_view::update( __( 'Settings have been saved!', multi_post_newsletter::get_textdomain() ) );
					break;
				case 'template':
					multi_post_newsletter_view::update( __( 'Template has been saved!', multi_post_newsletter::get_textdomain() ) );
					break;
			}
			return false;
		}
		
		/**
		 * Backend Controller
		 */
		public function backend () {
			$headlines = array(
				'mpnl_generate' => __( 'Generate Newsletter', multi_post_newsletter::get_textdomain() ),
				'mpnl_template' => __( 'Template', multi_post_newsletter::get_textdomain() ),
				'mpnl_config'   => __( 'Settings' )
			);
			multi_post_newsletter_view::wrapper_start( $headlines[$this->action] );
			$this->display_message( null );
			switch ( $this->action ) {
				case 'mpnl_template':
					$this->edit_template();
					break;
				case 'mpnl_config':
					$this->edit_settings();
					break;
				case 'mpnl_generate':
					$this->generate_newsletter();
					break;
			}
			multi_post_newsletter_view::wrapper_end();
		}
		
		/**
		 * Template page
		 */
		public function edit_template () {
			// Load template params
			$template_params = $this->get_my_template();

			// Check the posts
			if ( isset( $_POST['save_settings'] ) ) {
				// Update Settings
				update_option( 'mp-newsletter-template-params', $_POST['param'] );
				update_option( 'mp-newsletter-template-main', $_POST['template']['main_template'] );
				update_option( 'mp-newsletter-template-post', $_POST['template']['post_template'] );
				
				// Replace basic vars
				$template_params['params']        = $_POST['param'];
				$template_params['main_template'] = str_replace( '\\', '', $_POST['template']['main_template'] );
				$template_params['post_template'] = str_replace( '\\', '', $_POST['template']['post_template'] );
				$this->display_message( 'template' );
			}
			// Show form
			multi_post_newsletter_view::form_template( $template_params );
		}
		
		/**
		 * Settings page
		 */
		public function edit_settings () {
			$params = $this->get_my_params();
			
			// Check the posts
			if ( isset( $_POST['save_settings'] ) ) {
				// Update Settings
				update_option( 'mp-newsletter-params', $_POST['param'] );
				
				// Replace basic vars
				$params = $_POST['param'];
				$this->display_message( 'settings' );
			}
			// Show forms
			multi_post_newsletter_view::form_settings( $params );
		}
		
		/**
		 * Controller to generate newsletter and switch the single actions
		 * This Controller leads us to the endproduct, the newsletter
		 */
		public function generate_newsletter () {
			if ( !$_POST ) {
				$editions = get_terms( 'newsletter' );
				multi_post_newsletter_view::show_editions( $editions );
			}
			
			// Send Newsletter
			if ( isset( $_POST['send_test_newsletter'] ) || isset( $_POST['send_newsletter'] ) ) {
				$this->send_newsletter( $_POST['edition'] );
			}
			
			// Sortable Content
			if ( isset( $_POST['generate_newsletter'] ) ) {
				multi_post_newsletter_view::sortable_start();
				
				$params     = $this->get_my_params();
				$categories = get_categories( array( 'exclude' => $params['exclude'], 'hide_empty' => true, 'parent' => 0 ) );
				foreach ( $categories as $category ) {
					// Show Category
					multi_post_newsletter_view::show_sortable_category( $category );
					// Loop
					$custom_query = new WP_Query( array( 'category_name' => $category->slug , 'orderby' => 'menu_order', 'order' => 'ASC', 'newsletter' => $_POST['edition'] ) );
					if ( $custom_query->have_posts() ) : while ( $custom_query->have_posts() ) : $custom_query->the_post();
						multi_post_newsletter_view::show_sortable_post();
					endwhile; endif;
					multi_post_newsletter_view::show_sortable_end();
				}
				
				multi_post_newsletter_view::sortable_end();
			}
			
			// Preview
			if ( isset( $_POST['preview_newsletter'] ) ) {
				$newsletter_html = $this->generate_html_newsletter( $_POST['edition'] );
				$newsletter_text = nl2br( $this->generate_text_newsletter( $_POST['edition'] ) );
				multi_post_newsletter_view::newsletter_preview( $newsletter_html, $newsletter_text );
			}
		}
		
		public function send_newsletter ( $edition ) {
			
			// Generate Newsletter
			$newsletter_html = $this->generate_html_newsletter( $edition );
			$newsletter_text = $this->generate_text_newsletter( $edition );
			
			// Newsletter
			$letter = get_terms( 'newsletter', array( 'slug' => $edition ) );
			$letter = $letter[0];
			
			// Setup vars
			$params  = $this->get_my_params();
			$from    = $params['from_name'] . '<' . $params['from_mail'] . '>';
			$to_test = $params['to_test'];
			$to      = $params['to'];
			$subject = $letter->description;
			
			$boundary = "next_part";
			$headers  = "MIME-Version: 1.0\r\n";
			$headers .= "From: " . $from . "\r\n";
			$headers .= "Content-Type: multipart/alternative; boundary = " . $boundary . "\r\n";
			
			//text version
			$headers .= "\n--" . $boundary . "\n"; // beginning \n added to separate previous content
			$headers .= "Content-type: text/plain; charset=" . get_bloginfo( "charset" ) . "\r\n";
			$headers .= $newsletter_text;
			
			//html version
			$headers .= "\n--" . $boundary . "\n";
			$headers .= "Content-Disposition: inline\n";
			$headers .= "Content-Transfer-Encoding: quoted-printable\n";
			$headers .= "Content-type: text/html; charset=" . get_bloginfo( "charset" ) . "\n";
			$headers .= $newsletter_html;
			
			if ( isset( $_POST['send_test_newsletter'] ) ) {
				if ( mail( $to_test, $subject, '', $headers ) ) {
					$this->display_message( 'testmail' );
				}
			}
			else if ( isset( $_POST['send_newsletter'] ) ) {
				if ( mail( $to, $subject, '', $headers ) ) {
					$this->display_message( 'mail' );
				}
			}
		}
		
		/**
		 * Generate Text-Mail
		 */
		public function generate_text_newsletter ( $edition ) {
			// Load params
			$params          = $this->get_my_params();
			$template_params = $this->get_my_template();
			$categories = get_categories( array( 'exclude' => $params['exclude'], 'hide_empty' => true, 'parent' => 0 ) );
			// Category Loop
			foreach ( $categories as $category ) {
				
				
				// Custom Loop
				$custom_query = new WP_Query( array( 'category_name' => $category->slug , 'orderby' => 'menu_order', 'order' => 'ASC', 'newsletter' => $edition ) );
				if ( $custom_query->post_count > 0 ) {
					
					// Contents
					if ( 'on' == $template_params['params']['contents'] ) {
						if ( ! $post_contents ) {
							$post_contents = "\n\r== " . __( 'Contents', multi_post_newsletter::get_textdomain() ) . " ==\n\r\n\r";
						}
						$post_contents .= $category->name . "\n\r";
					}
					
					if ( $custom_query->have_posts() ) : while ( $custom_query->have_posts() ) : $custom_query->the_post();
					
						// Contents
						if ( 'on' == $template_params['params']['contents'] ) {
							$post_contents .= ' | ' . get_the_title() . "\n\r";
						}
					
						// Title
						$post_title = '== ' . get_the_title() . " ==\n\r\n\r";
						
						// Content
						if ( 'on' == $template_params['params']['excerpt'] ) {
							add_filter( 'excerpt_more', array( $this, 'force_clean_expert' ) );
							$post_content = get_the_excerpt();
						}
						else {
							$post_content = get_the_content();
						}
						$h2t =& new html2text( $post_content );
			 			$post_content  = $h2t->get_text();
			 			$post_content .= "\n\r\n\r" . __( 'Read the Article in the blog', multi_post_newsletter::get_textdomain() );
			 			$post_content .= "\n\r" . get_permalink();
			 			$post_content .= "\n\r=========================================\n\r";
			 			
			 			// Build string
			 			$content .= $post_title . $post_content;
					endwhile; endif;
					//Reset Query
					wp_reset_query();
				}
			}
			
			// Build return string
			$return_string  = $template_params['params']['header'];
			if ( 'on' == $template_params['params']['contents'] ) {
				$return_string .= $post_contents . "\n\r";
			}
			$return_string .= $content;
			$return_string .= $template_params['params']['footer'];
			
			return $return_string;
		}
		
		public function generate_html_newsletter ( $edition ) {
			// Load Options and Template
			$params          = $this->get_my_params();
			$template_params = $this->get_my_template();
			$mail_template	= str_replace( '\\', '', $template_params['main_template'] );
			$post_template	= str_replace( '\\', '', $template_params['post_template'] );
			
			// Load the letter
			$letter = get_terms( 'newsletter', array( 'slug' => $edition ) );
			$letter = $letter[0];
			
			// Custom Loop and several checks
			$categories    = get_categories( array( 'exclude' => $params['exclude'], 'hide_empty' => true, 'parent' => 0 ) );
			$mail_body_posts = '';
			$color         = 'even';
	
			if ( 'on' == $template_params['params']['contents'] ) {
				$mail_contents  = $options['contents_before'] . __( 'Contents', multi_post_newsletter::get_textdomain() ) . $options['contents_after'];
				$mail_contents .= '<ul style="list-style:none;">';
			}
			
			foreach ( $categories as $category ) {
				
				$custom_query = new WP_Query( array( 'category_name' => $category->slug, 'orderby' => 'menu_order', 'order' => 'ASC', 'newsletter' => $letter->slug ) );
				//The Loop
				if ( $custom_query->post_count > 0 ) {
					
					// Contents and Headlines
					if ( 'on' == $template_params['params']['contents'] ) {
						$mail_contents .= '<li>' . $category->name . '<ul style="list-style:none;">';
					}
					$mail_body_posts .= $template_params['params']['categorie_before'] . $category->name . $template_params['params']['categorie_after'];
					
					if ( $custom_query->have_posts() ) : while ( $custom_query->have_posts() ) : $custom_query->the_post();
					
						// Contents
						if ( 'on' == $template_params['params']['contents'] ) {
							$mail_contents .= '<li><a href="#' . get_the_ID() . '">' . get_the_title() . '</a></li>';
						}
						
						if ( $color == 'even' ) {
							$the_color = $template_params['params']['color_even'];
							$color = 'odd';
						}
						else {
							$the_color = $template_params['params']['color_odd'];
							$color = 'even';
						}
						
						$the_link = get_permalink();
						
						// Prepare
						$post->post_title = '<a name="' . get_the_ID() . '"></a>' . get_the_title();
						if ( 'on' == $template_params['params']['excerpt'] ) {
							add_filter( 'excerpt_more', array( $this, 'force_clean_expert' ) );
							$content_to_post = get_the_excerpt();
						}
						else {
							$post->post_content = $this->get_the_content_with_formatting();
							$content_to_post = $post->post_content;
						}
						
						// Replace Template Vars
						$haystack = array( '%DATE%', '%TITLE%', '%CONTENT%', '%AUTHOR%', '%COLOR%', '%LINK%' );
						$needle   = array( get_the_date(), $post->post_title, $content_to_post, get_the_author(), $the_color, $the_link );
						$replace  = str_replace( $haystack , $needle, $post_template );
	
						$mail_body_posts .= $replace;
						
					endwhile;endif;
					//Reset Query
					wp_reset_query();
					
					// Contents close
					if ( 'on' == $template_params['params']['contents'] ) {
						$mail_contents .= '</ul></li>';
					}
				}
			}
			
			// Contents close
			if ( 'on' == $template_params['params']['contents'] ) {
				$mail_contents .= '</ul>';
			}
			
			// Build Mailbody
			$haystack  = array( '%HEADER%', '%NAME%', '%DATE%', '%BODY%', '%CONTENTS%', '%FOOTER%' );
			$needle    = array( nl2br( $template_params['params']['header'] ), $letter->description, date( 'd.m.Y' ), $mail_body_posts, $mail_contents, nl2br( $template_params['params']['footer'] ) );
			$mail_body = str_replace( $haystack, $needle, $mail_template );
		
			return $mail_body;
		}
	}
}