<?php
/**
 * Feature Name:	Multipost Newsletter Init
 * Version:			0.1
 * Author:			Inpsyde GmbH
 * Author URI:		http://inpsyde.com
 * Licence:			GPLv3
 * 
 * Changelog
 *
 * 0.1
 * - Initial Commit
 */

if ( ! class_exists( 'Multipost_Newsletter_Init' ) ) {

	class Multipost_Newsletter_Init extends Multipost_Newsletter {
		
		/**
		 * Instance holder
		 *
		 * @since	0.1
		 * @access	private
		 * @static
		 * @var		NULL | Multipost_Newsletter_Init
		 */
		private static $instance = NULL;
		
		/**
		 * Method for ensuring that only one instance of this object is used
		 *
		 * @since	0.1
		 * @access	public
		 * @static
		 * @return	Multipost_Newsletter_Init
		 */
		public static function get_instance() {
			
			if ( ! self::$instance )
				self::$instance = new self;
			return self::$instance;
		}
		
		/**
		 * Setting up some data, initialize translations and start the hooks
		 *
		 * @since	0.1
		 * @access	public
		 * @uses	get_option, add_filter, is_admin
		 * @return	void
		 */
		public function __construct () {
			
			// Setup standard data
			if ( 'true' != get_option( 'mp-newsletter-standards-setted' ) ) {
				$this->setup_standard_data();
			}
			
			// Adding the menu
			add_filter( 'admin_menu', array( $this, 'init_menu' ) );
			
			// Adding the js stuff only on our own pages
			$pages = array(
				'mpnl_options',
				'mpnl_generate',
				'mpnl_template',
				'newsletter',
				'recipient-category',
				'mpnl_groups',
				'mpnl_create'
			);
			if ( is_admin() && ( ( isset( $_GET[ 'page' ] ) && in_array( $_GET[ 'page' ], $pages ) ) || ( isset( $_GET[ 'taxonomy' ] ) && in_array( $_GET[ 'taxonomy' ], $pages ) ) ) )
				add_filter( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
			
			// Frontend Scripts
			if ( ! is_admin() )
				add_filter( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
			
			// Add custom field to images
			add_filter( 'attachment_fields_to_edit', array( $this, 'attachment_fields_to_edit' ), 10, 2 );
		}
		
		/**
		 * Enqueue Scripts
		 *
		 * @since	0.1
		 * @access	public
		 * @uses	wp_enqueue_script, plugin_dir_url
		 * @return	void
		 */
		public function enqueue_scripts () {
		
			// Style
			wp_enqueue_style( 'Multipost_Newsletter', plugin_dir_url( __FILE__ ) . '../css/class-Multipost_Newsletter.css' );
		
			// Script
			wp_enqueue_script( 'chosen', plugin_dir_url( __FILE__ ) . '../js/chosen.jquery.min.js', array( 'jquery' ) );
			wp_enqueue_script( 'Multipost_Newsletter_Frontend', plugin_dir_url( __FILE__ ) . '../js/class-Multipost_Newsletter_Frontend.js', array( 'jquery' ) );
		}
		
		/**
		 * Load the admin scripts
		 *
		 * @since	0.1
		 * @access	public
		 * @uses	wp_enqueue_script, wp_enqueue_style, plugin_dir_url, wp_localize_script
		 * @return	void
		 */
		public function admin_scripts() {
			
			// Style
			wp_enqueue_style( 'Multipost_Newsletter', plugin_dir_url( __FILE__ ) . '../css/class-Multipost_Newsletter.css' );
			
			// Script
			wp_enqueue_script( 'multipost_newsletter', plugin_dir_url( __FILE__ ) . '../js/class-Multipost_Newsletter.js', array( 'jquery', 'json2', 'wp-lists', 'wp-ajax-response', 'utils', 'jquery-ui-core', 'jquery-ui-sortable', 'jquery-ui-draggable', 'jquery-ui-droppable' ) );
			
			// Localize
			$vars = $this->load_js_vars();
			wp_localize_script( 'multipost_newsletter', 'multipost_newsletter_vars', $vars );
		}
		
		/**
		 * load javasrcipt variables
		 *
		 * @since	0.1
		 * @access	public
		 * @uses	__, plugin_dir_url
		 * @return	array
		 */
		public function load_js_vars() {
				
			$vars = array(
				'send_newsletter_to'	=> __( 'Send Newsletter to ', parent::$textdomain ),
				'send_newsletter_to_of'	=> __( ' of ', parent::$textdomain ),
				'recipients'			=> __( ' Recipients ', parent::$textdomain ),
				'wp_spin'				=> plugin_dir_url( __FILE__ ) . '../images/wpspin_light.gif',
				'all_done'				=> __( 'All Done!', parent::$textdomain ),
				'unknown_error'			=> __( 'An unknown error occured. Please contact your server administrator!', parent::$textdomain ),
			);
			
			if ( TRUE == parent::$is_pro )
				$vars[ 'is_pro' ] = 'true';
			else
				$vars[ 'is_pro' ] = 'false';
		
			return $vars;
		}
		
		/**
		 * Inits the main menu
		 * 
		 * @since	0.1
		 * @access	public
		 * @uses	add_menu_page, add_submenu_page, __, plugin_dir_url
		 * @return	void
		 */
		public function init_menu () {
			
			// Set globals for the admin pages
			global $mpnl_options_page, $mpnl_template_page, $mpnl_groups_page, $mpnl_prepare_page, $mpnl_create_page;
			
			add_menu_page( __( 'Newsletter', parent::$textdomain ), __( 'Newsletter', parent::$textdomain ), 'manage_options', 'mpnl_options', array( 'Multipost_Newsletter_Options', 'options_page' ), plugin_dir_url( __FILE__ ) . '../images/mail.png' );
			$mpnl_options_page = add_submenu_page( 'mpnl_options', __( 'Options', parent::$textdomain ), __( 'Options', parent::$textdomain ), 'manage_options', 'mpnl_options', array( 'Multipost_Newsletter_Options', 'options_page' ) );
			$mpnl_template_page = add_submenu_page( 'mpnl_options', __( 'Template', parent::$textdomain ), __( 'Template', parent::$textdomain ), 'manage_options', 'mpnl_template', array( 'Multipost_Newsletter_Template', 'template_page' ) );
			add_submenu_page( 'mpnl_options', __( 'Editions', parent::$textdomain ), __( 'Editions', parent::$textdomain ), 'manage_options', 'edit-tags.php?taxonomy=newsletter' );
			
			if ( TRUE == parent::$is_pro )
				$mpnl_groups_page = add_submenu_page( 'mpnl_options', __( 'Groups', parent::$textdomain ), __( 'Groups', parent::$textdomain ), 'manage_options', 'mpnl_groups', array( 'Multipost_Newsletter_Groups', 'groups_page' ) );
			
			$mpnl_prepare_page = add_submenu_page( 'mpnl_options', __( 'Prepare Newsletter', parent::$textdomain ), __( 'Prepare Newsletter', parent::$textdomain ), 'manage_options', 'mpnl_generate', array( 'Multipost_Newsletter_Prepare', 'generate_newsletter_page' ) );
			$mpnl_create_page = add_submenu_page( 'mpnl_options', __( 'Create Newsletter', parent::$textdomain ), __( 'Create Newsletter', parent::$textdomain ), 'manage_options', 'mpnl_create', array( 'Multipost_Newsletter_Create', 'create_newsletter_page' ) );
			
			// Adds help tabs
			add_filter( 'load-' . $mpnl_options_page, array( 'Multipost_Newsletter_Help', 'mpnl_options_page_help' ) );
			add_filter( 'load-' . $mpnl_template_page, array( 'Multipost_Newsletter_Help', 'mpnl_template_page_help' ) );
			
			if ( TRUE == parent::$is_pro )
				add_filter( 'load-' . $mpnl_groups_page, array( 'Multipost_Newsletter_Help', 'mpnl_groups_page_help' ) );
			
			add_filter( 'load-' . $mpnl_prepare_page, array( 'Multipost_Newsletter_Help', 'mpnl_prepare_page_help' ) );
			add_filter( 'load-' . $mpnl_create_page, array( 'Multipost_Newsletter_Help', 'mpnl_create_page_help' ) );
		}
		
		/**
		 * Adds a new field to the media files
		 *
		 * @since	0.1
		 * @access	public
		 * @param	array $fields
		 * @param	object $attachment
		 * @uses	__, get_attached_file
		 * @return	array $fields
		 */
		public function attachment_fields_to_edit( $fields, $attachment ) {

			$fields[ 'image_path' ] = array(
				'label'      => __( 'File Path', parent::$textdomain ),
				'input'      => 'html',
				'html'       => "<input type='text' class='text urlfield' readonly='readonly' name='attachments[$attachment->ID][file_path]' value='" . esc_attr( get_attached_file( $attachment->ID ) ) . "' /><br />",
				'value'      => get_attached_file( $attachment->ID ),
				'helps'      => __( 'Path of the uploaded file.', parent::$textdomain )
			);
			
			return $fields;
		}
		
		/**
		 * Setup standard data
		 *
		 * @since	0.1
		 * @access	public
		 * @uses	__, update_option, get_bloginfo, plugin_dir_path
		 * @return	array $fields
		 */
		public function setup_standard_data() {
			
			// HTML
			$html_main = '
<table cellspacing="0" border="0" style="background-color: #c5c5c5;" cellpadding="0" width="100%">
	<tr>
		<td valign="top">
			<table cellspacing="0" border="0" align="center" style="background: #fff; border-right: 1px solid #ccc; border-left: 1px solid #ccc;" cellpadding="0" width="600">
				<tr>
					<td valign="top">
						<hr style="border: 0; border-bottom: 1px solid #ccc;" />
						<!-- subline -->
						<table cellspacing="0" border="0" cellpadding="0" width="600">
							<tr>
								<td class="main-title" height="13" valign="top" style="padding: 0 20px; font-size: 25px; font-family: Georgia; font-style: italic;" width="600" colspan="2" align="center">
									' . get_bloginfo( 'name' ) . '
								</td>
							</tr>
							<tr>
								<td class="header-bar" valign="top" style="color: #999; font-family: Verdana; font-size: 10px; text-transform: uppercase; padding: 0 20px; height: 15px;" width="400" height="15">
									%NAME%
								</td>
								<td class="header-bar" valign="top" style="color: #999; font-family: Verdana; font-size: 10px; text-transform: uppercase; padding: 0 20px; height: 15px; text-align: right;" width="200">
									%DATE%
								</td>
							</tr>
						</table>
						<hr style="border: 0; border-bottom: 1px solid #ccc;" />
						<!-- / subline -->
					</td>
				</tr>
				<tr>
					<td valign="top" width="600">
						<!-- header -->
						<table cellspacing="0" border="0" height="202" cellpadding="0" width="600">
							<tr>
								<td valign="top">
									<table cellspacing="0" border="0" width="600" cellpadding="0">
										<tr>
											<td class="unsubscribe" valign="top" style="padding: 0 20px; color: #333; font-size: 14px; font-family: Georgia; line-height: 20px;" width="600">
						 			 			 %HEADER%
											</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
						<!-- / end header -->
						<hr style="border: 0; border-bottom: 1px solid #ccc;" />
					</td>
				</tr>
				<tr>
					<td valign="top" width="600">
						<!-- Contents -->
						<table cellspacing="0" border="0" height="202" cellpadding="0" width="600"> 
							<tr>
								<td valign="top">
									<table cellspacing="0" border="0" width="600" cellpadding="0">
										<tr>
											<td class="unsubscribe" valign="top" style="padding: 0 20px; color: #333; font-size: 14px; font-family: Georgia; line-height: 20px;" width="600">
						 			 			 %CONTENTS%
											</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
						<!-- / end contents --> 
						<hr style="border: 0; border-bottom: 1px solid #ccc;" />
					</td>
				</tr>
				<tr>
					<td>
						<!-- content -->
						<table cellspacing="0" border="0" cellpadding="0" width="600">
							%BODY%
						</table>
						<!--  / content -->
					</td>
				</tr>
				<tr>
					<td valign="top" width="600">
						<hr style="border: 0; border-bottom: 1px solid #ccc;" />
						<!-- footer -->
						<table cellspacing="0" border="0" height="202" cellpadding="0" width="600">
							<tr>
								<td valign="top">
									<table cellspacing="0" border="0" width="600" cellpadding="0">
										<tr>
											<td class="unsubscribe" valign="top" style="padding: 0 20px; color: #999; font-size: 14px; font-family: Georgia; line-height: 20px;" width="600">
												%FOOTER%
											</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
						<!-- / end footer -->
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>';
			
			$html_post = '							<tr>
								<td class="article-title" height="45" valign="top" style="padding: 10px 20px 0; font-family: Georgia; font-size: 20px; font-weight: bold; background-color: #%COLOR%;" width="600" colspan="2">
									%LINK_NAME%<a href="%LINK%">%TITLE%</a>
								</td>
							</tr>
							<tr>
								<td class="header-bar" valign="top" style="color: #999; font-family: Verdana; font-size: 10px; text-transform: uppercase; padding: 0 20px; height: 15px; background-color: #%COLOR%;" width="400" height="15" colspan="2">
									' . __( 'written by', parent::$textdomain ) . ' %AUTHOR% ' . __( 'at', parent::$textdomain ) . ' %DATE%
								</td>
							</tr>
							<tr>
								<td class="content-copy" valign="top" style="padding: 0 20px; color: #000; font-size: 14px; font-family: Georgia; line-height: 20px; background-color: #%COLOR%;" colspan="2">
									%THUMBNAIL% %CONTENT%
									<p><a href="%LINK%">' . __( 'Read more', parent::$textdomain ) . '</a></p>
									<hr style="border: 0; border-bottom: 1px solid #ccc;" />
								</td>
							</tr>';
			
			$html_params[ 'color_even' ] = 'f6f6f6';
			$html_params[ 'color_odd' ] = 'fff';
			$html_params[ 'contents_before' ] = '<h2>';
			$html_params[ 'contents_after' ] = '</h2>';
			$html_params[ 'html_head' ] = '<html> 
<head> 
	<title>' . get_bloginfo( 'name' ) . '</title>
	<meta content="text/html; charset=iso-8859-1" http-equiv="Content-Type" />
	<style type="text/css">
	.list a {color: #cc0000; text-transform: uppercase; font-family: Verdana; font-size: 11px; text-decoration: none;}
	h1, h2, h3 { background: none; font-family: Georgia; font-style: italic; }
	</style>
</head>
<body marginheight="0" topmargin="0" marginwidth="0" bgcolor="#c5c5c5" leftmargin="0">';
			$html_params[ 'html_footer' ] = '</body> 
</html>';
			
			// Text
			$text_main = '===== %NAME% =====
vom %DATE%

%HEADER%

%CONTENTS%

%BODY%

%FOOTER%';
			$text_post = '------------------------------------------------
%TITLE%
vom %DATE% geschrieben von %AUTHOR%

%CONTENT%

%LINK%
';
			
			$text_params[ 'contents_before' ] = '=== ';
			$text_params[ 'contents_after' ] = ' ===';
			
			// PDF
			$pdf_options = array(
				'headline_font' => 'arialblack',
				'headline_font_size' => '40',
				'headline_font_color_red' => '255',
				'headline_font_color_green' => '255',
				'headline_font_color_blue' => '255',
				'headline_x' => '10',
				'headline_y' => '1',
				'description_show' => 'on',
				'description_font' => 'arial',
				'description_font_size' => '10',
				'description_font_color_red' => '100',
				'description_font_color_green' => '100',
				'description_font_color_blue' => '100',
				'description_x' => '10',
				'description_y' => '28',
				'subline_show' => 'on',
				'subline_font' => 'arial',
				'subline_font_size' => '10',
				'subline_font_color_red' => '100',
				'subline_font_color_green' => '100',
				'subline_font_color_blue' => '100',
				'subline_x' => '160',
				'subline_y' => '28',
				'content_headline_font' => 'georgia',
				'content_headline_font_size' => '16',
				'content_headline_font_color_red' => '0',
				'content_headline_font_color_green' => '0',
				'content_headline_font_color_blue' => '0',
				'content_font' => 'arial',
				'content_font_size' => '12.5',
				'content_font_color_red' => '0',
				'content_font_color_green' => '0',
				'content_font_color_blue' => '0',
				'content_footer_show' => 'on',
				'content_footer_font' => 'arial',
				'content_footer_font_size' => '10',
				'content_footer_font_color_red' => '150',
				'content_footer_font_color_green' => '150',
				'content_footer_font_color_blue' => '150',
				'page_numbers_show' => 'on',
				'page_numbers_font' => 'booter',
				'page_numbers_font_size' => '20',
				'page_numbers_font_color_red' => '255',
				'page_numbers_font_color_green' => '255',
				'page_numbers_font_color_blue' => '255',
				'logo' => plugin_dir_path( __FILE__ ) . '../images/header.png',
				'logo_x' => '0',
				'logo_y' => '0',
				'watermark' => plugin_dir_path( __FILE__ ) . '../images/watermark.png',
				'watermark_x' => '65',
				'watermark_y' => '80',
				'corner_bottom_right' => plugin_dir_path( __FILE__ ) . '../images/corner-left.png',
				'corner_bottom_right_x' => '-1',
				'corner_bottom_right_y' => '270',
				'corner_bottom_left' => plugin_dir_path( __FILE__ ) . '../images/corner-right.png',
				'corner_bottom_left_x' => '183',
				'corner_bottom_left_y' => '270',
				'header_subpages_height' => '10',
				'header_subpages_red' => '141',
				'header_subpages_green' => '173',
				'header_subpages_blue' => '27',
				'link_color_red' => '141',
				'link_color_green' => '173',
				'link_color_blue' => '27'
			);
			
			// Update Options
			update_option( 'mp-newsletter-html-main', $html_main );
			update_option( 'mp-newsletter-html-post', $html_post );
			update_option( 'mp-newsletter-html-params', $html_params );
			update_option( 'mp-newsletter-text-main', $text_main );
			update_option( 'mp-newsletter-text-post', $text_post );
			update_option( 'mp-newsletter-text-params', $text_params );
			update_option( 'mp-newsletter-pdf', $pdf_options );
			
			// Set True
			update_option( 'mp-newsletter-standards-setted', 'true' );
		}
	}
	
	// Kickoff
	if ( function_exists( 'add_filter' ) )
		Multipost_Newsletter_Init::get_instance();
}