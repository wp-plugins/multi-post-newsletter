<?php
/*
Plugin Name: Multi Post Newsletter
Plugin URI: http://hughwillfayle.de/wordpress/multipostnewsletter
Description: The Multi Post Newsletter is a simple plugin, which provides to link several posts to a newsletter. This procedure is similar to the categories. Within the flexible configuration and templating, you're able to set the newsletters appearance to your requirement.
Author: Thomas Herzog
Version: 0.2
Author URI: http://hughwillfayle.de/
*/

// Model not in use yet, will come
require_once 'php/multi-post-newsletter-model.php';
// I don't like HTML in PHP Code. If I am able to, I put this to an external file
require_once 'php/multi-post-newsletter-view.php';

/**
 * Main class contains the functions for the Newsletter
 */
class mpnl extends mpnl_modul {
	
	/**
	 * Add the actions
	 */
	function __construct() {
		add_action( 'init', array( &$this, 'mpnl_taxonomy' ), 0 );
		add_action( 'admin_menu', array( &$this,"mpnl_init" ) );
		load_plugin_textdomain( 'th_mpnl', false, 'multi-post-newsletter/i18n' );
	}
	
	/**
	 * Kick off
	 */
	function mpnl_init () {
		add_menu_page( 'MP-Newsletter', 'MP-Newsletter', 'manage_options', 'mpnl_generate', array( &$this, 'mpnl_switch' ) );
		add_options_page( 'MP-Newsletter', 'MP-Newsletter', 'manage_options', 'mpnl_config', array( &$this, 'mpnl_switch' ) );
	}
	
	/**
	 * Register custom taxonomie
	 */
	function mpnl_taxonomy () {
		register_taxonomy(	'newsletter',
							array( 'post' ),
							array(	'public' => true,
									'query_var' => 'newsletter',
									'public' => true,
									'show_ui' => true,
									'show_tagcloud' => false,
									'hierarchical' => true,
									'show_in_nav_menus' => true,
									'labels' => array(	'name' => __( 'Newsletter', 'th_mpnl' ),
														'singular_name' => __( 'Newsletter', 'th_mpnl' ),
														'search_items' => __( 'Search Newsletter', 'th_mpnl' ),
														'popular_items' => __( 'Popular Newsletter', 'th_mpnl' ),
														'all_items' => __( 'All Newsletter', 'th_mpnl' ),
														'parent_item' => __( 'Parent Newsletter', 'th_mpnl' ),
														'parent_item_colon' => __( 'Parent Newsletter:', 'th_mpnl' ),
														'edit_item' => __( 'Edit Newsletter', 'th_mpnl' ),
														'update_item' => __( 'Update Newsletter', 'th_mpnl' ),
														'add_new_item' => __( 'Add Newsletter', 'th_mpnl' ),
														'new_item_name' => __( 'Add Newsletter', 'th_mpnl' ), ), )
		);
	}
	
	/**
	 * Where am I and what should i Load?
	 */
	function mpnl_switch ( ) {
		?>
		<div class="wrap">
			<h2>Multi Post Newsletter</h2>
			<?php $this->display_msg( null ); ?>
			<?php
				if ( $_GET['page'] == 'mpnl_config' ) {
					$this->mpnl_configuration(); }
				else if ( $_GET['page'] == 'mpnl_generate' ) {
					$this->mpnl_generate(); }
			?>
		</div>
		<?php
	}
	
	/**
	 * Main configuration
	 */
	function mpnl_configuration () {
		// Load basic vars
		$params = get_option( 'mp-newsletter-params' );
		$template['main_template'] = str_replace( '\\', '', get_option( 'mp-newsletter-template-main' ) );
		$template['post_template'] = str_replace( '\\', '', get_option( 'mp-newsletter-template-post' ) );
		
		// Check the posts
		if ( isset( $_POST['save_settings'] ) ) {
			// Update Settings
			update_option( 'mp-newsletter-params', $_POST['param'] );
			update_option( 'mp-newsletter-template-main', $_POST['template']['main_template'] );
			update_option( 'mp-newsletter-template-post', $_POST['template']['post_template'] );
			
			// Replace basic vars
			$params = $_POST['param'];
			$template['main_template'] = str_replace( '\\', '', $_POST['template']['main_template'] );
			$template['post_template'] = str_replace( '\\', '', $_POST['template']['post_template'] );
			$this->display_msg( 'settings' );
		}
		
		// Display form
		mpnl_view::form_config( $params, $template );
	}
	
	/**
	 * The newsletterfunction
	 */
	function mpnl_generate () {
		global $wpdb;
		
		if ( isset( $_POST['send_test'] ) || isset( $_POST['send_mail'] ) || isset( $_POST['preview'] ) ) {
			// Build the Newsletter
			$preview = $this->mpnl_build( $_POST['param'] );
		}
		
		// Load configuration
		$letters = get_terms( 'newsletter' );
		mpnl_view::generate_newsletter( $letters, $_POST['param'] );
		
		// Preview
		if ( isset( $_POST['preview'] ) ) {
			echo $preview;
		}
	}
	
	/**
	 * Build Newsletter
	 * @param array $params Some settings from POST array
	 */
	function mpnl_build ( $params ) {
		// Load Options and Template
		$options		= get_option( "mp-newsletter-params" );
		$mail_template	= str_replace( '\\', '', get_option( 'mp-newsletter-template-main' ) );
		$post_template	= str_replace( '\\', '', get_option( 'mp-newsletter-template-post' ) );
		
		// Load the letter
		$letter = get_terms( 'newsletter', array( 'slug' => $params['letter'] ) );
		$letter = $letter[0];
		
		// Custom Loop and several checks
		$categories    = get_categories( array( 'exclude' => $options['exclude'], 'hide_empty' => true, 'parent' => 0 ) );
		$mail_body_posts = '';
		$color         = 'even';

		if ( 'on' == $params['contents'] ) {
			$mail_contents  = $options['contents_before'] . __( 'Contents', 'th_mpnl' ) . $options['contents_after'];
			$mail_contents .= '<ul style="list-style:none;">';
		}
		
		foreach ( $categories as $category ) {
			
			$custom_query = new WP_Query( array( 'category_name' => $category->slug , 'newsletter' => $params['letter'] ) );
			//The Loop
			if ( $custom_query->post_count > 0 ) {
				
				// Contents and Headlines
				if ( 'on' == $params['contents'] ) {
					$mail_contents .= '<li>' . $category->name . '<ul style="list-style:none;">';
				}
				$mail_body_posts .= $options['categorie_before'] . $category->name . $options['categorie_after'];
				
				if ( $custom_query->have_posts() ) : while ( $custom_query->have_posts() ) : $custom_query->the_post();
				
					// Contents
					if ( 'on' == $params['contents'] ) {
						$mail_contents .= '<li><a href="#' . get_the_ID() . '">' . get_the_title() . '</a></li>';
					}
					
					if ( $color == 'even' ) {
						$the_color = $options['color_even'];
						$color = 'odd';
					}
					else {
						$the_color = $options['color_odd'];
						$color = 'even';
					}
					
					$the_link = '<a href="' . get_permalink() . '">' . __( 'Read the Article in the blog', 'th_mpnl' ) . '</a>';
					
					// Prepare
					$post->post_title = '<a name="' . get_the_ID() . '"></a>' . get_the_title();
					if ( 'on' == $params['excerpt'] ) {
						add_filter( 'excerpt_more', array( &$this, 'force_strip_expert' ) );
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
				if ( 'on' == $params['contents'] ) {
					$mail_contents .= '</ul></li>';
				}
			}
		}
		
		// Contents close
		if ( 'on' == $params['contents'] ) {
			$mail_contents .= '</ul>';
		}
		
		// Build Mailbody
		$haystack  = array( '%HEADER%', '%NAME%', '%DATE%', '%BODY%', '%CONTENTS%' );
		$needle    = array( nl2br( $_POST['param']['header'] ), $params['title'], date( 'd.m.Y' ), $mail_body_posts, $mail_contents );
		$mail_body = str_replace( $haystack, $needle, $mail_template );
		
		// Send mail if this is no preview
		if ( !isset( $_POST['preview'] ) ) {
			$this->send_mail( $params['title'], $mail_body );
		}
		else {
			return $mail_body;
		}
	}
	
	/**
	 * Send the mail
	 * @param string $title
	 * @param string $mail_body
	 */
	function send_mail ( $title, $mail_body ) {
		// I need HTML!
		add_filter( 'wp_mail_content_type', array( &$this, 'get_content_type' ) );

		// Load options and set some settings
		$options = get_option( 'mp-newsletter-params' );
		$subject = $title;
		$message = $mail_body;
		
		// Build headerinformation
		$header = 'From: '.$options['from_name'].' <'.$options['from_mail'].'>';
		
		// Is it a test or is it real?
		if ( isset( $_POST['send_test'] ) ) {
			$to = $options['to_test'];
			wp_mail( $to, $subject, $message, $header );
			$this->display_msg( 'testmail' );
		}
		else {
			$to = $options['to'];
			wp_mail( $to, $subject, $message, $header );
			$this->display_msg( 'mail' );
		}
	}
	
	/**
	 * In future there will be also plain text
	 */
	function get_content_type ( ) {
		return 'text/html';
	}
	
	/**
	 * Formats the content.
	 * @param string $more_link_text
	 * @param int $stripteaser
	 * @param mixed $more_file
	 */
	function get_the_content_with_formatting ( $more_link_text = '', $stripteaser = 0, $more_file = '' ) {
		$content = get_the_content( $more_link_text, $stripteaser, $more_file );
		$content = apply_filters( 'the_content', $content );
		$content = str_replace( ']]>', ']]&gt;', $content );
		return $content;
	}
	
	/**
	 * Because of the %LINK% Tag
	 * @param mixed $more
	 */
	function force_strip_expert ( $more ) {
		return '';
	}
	
	/**
	 * Gimme Messages
	 * @param string $msg
	 */
	function display_msg ( $msg ) {
		switch ( $msg ) {
			case 'testmail':
				mpnl_view::updated( __( 'Testmail successfully send!', 'th_mpnl' ) );
				break;
			case 'mail':
				mpnl_view::updated( __( 'Newsletter successfully send!', 'th_mpnl' ) );
				break;
			case 'settings':
				mpnl_view::updated( __( 'Settings have been saved!', 'th_mpnl' ) );
				break;
		}
		return false;
	}
}

function multi_post_newsletter_start () { 
	new mpnl(); 
}
add_action('plugins_loaded', 'multi_post_newsletter_start');