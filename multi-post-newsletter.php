<?php
/*
Plugin Name: Multi Post Newsletter
Plugin URI: http://hughwillfayle.de/wordpress/multipostnewsletter
Description: The Multi Post Newsletter is a simple plugin, which provides to link several posts to a newsletter. This procedure is similar to the categories. Within the flexible configuration and templating, you're able to set the newsletters appearance to your requirement.
Author: Thomas Herzog
Version: 0.1.1
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
		add_menu_page( 'MP-Newsletter', 'MP-Newsletter', 9, 'mpnl_generate', array( &$this, 'mpnl_switch' ) );
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
		$mailTemplate	= str_replace( '\\', '', get_option( 'mp-newsletter-template-main' ) );
		$postTemplate	= str_replace( '\\', '', get_option( 'mp-newsletter-template-post' ) );
		
		// Load the letter
		$letter = get_terms( 'newsletter', array( 'slug' => $params['letter'] ) );
		$letter = $letter[0];
		
		// Custom Loop and several checks
		$categories    = get_categories( array( 'exclude' => $options['exclude'], 'hide_empty' => true, 'parent' => 0 ) );
		$mailBodyPosts = '';
		$color         = 'even';
		$mailContents  = $options['contents_before'] . __( 'Contents', 'th_mpnl' ) . $options['contents_after'];
		$mailContents .= '<ul style="list-style:none;">';
		foreach ( $categories as $category ) {
			
			$customQuery = new WP_Query( array( 'category_name' => $category->slug , 'newsletter' => $params['letter'] ) );
			//The Loop
			if ( $customQuery->post_count > 0 ) {
				
				// Contents and Headlines
				$mailContents .= '<li>' . $category->name . '<ul style="list-style:none;">';
				$mailBodyPosts .= $options['categorie_before'] . $category->name . $options['categorie_after'];
				
				if ( $customQuery->have_posts() ) : while ( $customQuery->have_posts() ) : $customQuery->the_post();
					// Contents
					$mailContents .= '<li><a href="#' . get_the_ID() . '">' . get_the_title() . '</a></li>';
					
					if ( $color == 'even' ) {
						$theColor = $options['color_even'];
						$color = 'odd';
					}
					else {
						$theColor = $options['color_odd'];
						$color = 'even';
					}
					
					// Posts
					$tmp = str_replace( '%DATE%', get_the_date(), $postTemplate );
					$post->post_title = '<a name="' . get_the_ID() . '"></a>' . get_the_title();
					$tmp = str_replace( '%TITLE%', $post->post_title, $tmp );
					$post->post_content = get_the_content();
					$tmp = str_replace( '%CONTENT%', nl2br( $post->post_content ),$tmp );
					$tmp = str_replace( '%AUTHOR%', get_the_author(), $tmp );
					$tmp = str_replace( '%COLOR%', $theColor, $tmp );
					$mailBodyPosts .= $tmp;
				
				endwhile;endif;
				//Reset Query
				wp_reset_query();
				
				// Contents close
				$mailContents .= '</ul></li>';
			}
		}
		
		// Contents close
		$mailContents .= '</ul>';
		
		// Build Mailbody
		$mailBody = str_replace( '%HEADER%', $_POST['param']['header'], $mailTemplate );
		$mailBody = str_replace( '%NAME%', $params['title'], $mailBody );
		$mailBody = str_replace( '%DATE%', date( 'd.m.Y' ), $mailBody );
		$mailBody = str_replace( '%BODY%', $mailBodyPosts, $mailBody );
		$mailBody = str_replace( '%CONTENTS%', $mailContents, $mailBody );
		
		// Send mail if this is no preview
		if ( !isset( $_POST['preview'] ) ) {
			$this->send_mail( $params['title'], $mailBody );
		}
		else {
			return $mailBody;
		}
	}
	
	/**
	 * Send the mail
	 * @param string $title
	 * @param string $mailBody
	 */
	function send_mail ( $title, $mailBody ) {
		// I need HTML!
		add_filter( 'wp_mail_content_type', array( &$this, 'get_content_type' ) );

		// Load options and set some settings
		$options = get_option( 'mp-newsletter-params' );
		$subject = $title;
		$message = $mailBody;
		
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