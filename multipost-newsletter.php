<?php
/**
 * Plugin Name:	Multipost Newsletter
 * Plugin URI:	http://marketpress.com/product/multipost-newsletter/
 * Description:	The Multi Post Newsletter is a simple plugin, which provides to link several posts to a newsletter. This procedure is similar to the categories. Within the flexible configuration and templating, you're able to set the newsletters appearance to your requirement.
 * Version:		1.0.6
 * Author:		Inpsyde GmbH
 * Author URI:	http://inpsyde.com
 * Licence:		GPLv3
 * Text Domain:	multipost-newsletter
 * Domain Path:	/language
 *
 * The Multi Post Newsletter is a simple plugin, which provides
 * to link several posts to a newsletter. This procedure is
 * similar to the categories. Within the flexible configuration
 * and templating, you're able to set the newsletters appearance
 * to your requirement.
 *
 * Changelog
 * 
 * 1.0.6
 * - Code: Updated Auto Updater
 * - Version: Version Hopping due to some Auto Update issues
 * 
 * 1.0.4
 * - Code: Fixed fatal error on widget
 * 
 * 1.0.3
 * - Code: Fixed several Warnings and Notices
 * - Code: Fixed SMTP Connection
 * - Code: Fixed article remove from an edition
 * - Code: Added target="_blank" at PDF-Link in Preview
 * 
 * 1.0.2
 * - Code: Fixed Warning in Prepare Dialog
 * - Code: Fixed Warning in PDF Preview
 * - Code: Fixed Warning while check for the pro folder
 * - Code: Fixed Warning in Auto Update
 * - Code: Fixed Warning in Template
 * - Code: Fixed unnecessary type in user and widget
 * 
 * 1.0.1
 * - Code: Fixed several Notices
 * - Code: Fixed Charset problems
 * - Code: Fixed phpmailer recipient problems
 * - Code: Fixed bug in checkboxes on post preview
 * 
 * 1.0
 * - License: Changed to GPLv3
 * - Version: Hopped Version due to too many changes
 * - Code: Complete new Codebase
 * - Feature: Automattic PDF Export
 * - Feature: Templating for Text-Mail
 * - Feature: Support Article Pictures
 * - Feature: Improved UI
 * - Feature: One Default Template
 * - Feature: Add Custom Fields to the template
 * - Feature: Fixed translations
 * - Feature: Mass-Mailing-Feature
 * - Feature: Support for custom post types
 * - Feature: Widget for Subscription
 * - Feature: Send mail to specific reciptions (groups)
 * 
 * 0.5.5.5
 * - Feature: URL-Shortener is.gd for text-mail
 * - Code: Several Fixes for the text-mail
 * - Code: Language Check-Ups
 * 
 * 0.5.5.4
 * - Feature: Frontend-Templating
 * 
 * 0.5.5.3
 * - Code: Fixed broken full page view
 * - Code: Fixed a "\" bug in the template options
 * 
 * 0.5.5.2
 * - Code: Styling
 * - Code: Fixed a ' bug in the template options
 * 
 * 0.5.5.1
 * - Code: Fixed annoying Bug in "sending the main newsletter"
 * 
 * 0.5.5
 * - Code: Fixed Doubled Mail Problem
 * - Code: Fixed Encoding Issues
 * 
 * 0.5.4
 * - Code: Fixed limit of posts
 * - Code: Fixed Encoding Issues
 * 
 * 0.5.3
 * - Code: Several fixes through the display of the newsletter
 * - Code: Added a new tag %LINK_NAME%
 * 
 * 0.5.2
 * - Code: Merged txt and html loop
 * - Code: Fixed Boundary and Headers
 * - Code: Some usability fixes
 * - Code: Fix in title/link conflict
 * 
 * 0.5.1
 * - Code: Fix in Contents ( Text-Version )
 * - Code: %LINK% now just gives the permalink
 * - Code: Language Fixes
 * 
 * 0.5
 * - Version: Hopping because of many changes
 * - Code: New improved Code
 * - Code: Several Checks
 * - Misc: Custom Collumn "Newsletter" in Article Overview
 * - Feature: text/plain mail
 * - Feature: New and better option pages
 * - Feature: New workflow to generate a Newsletter
 * - Feature: AJAX-functionalities for sortable Articles
 * 
 * 0.2
 * - Feature: Option to choose excerpt
 * - Feature: Option to choose display contents
 * - Feature: Added %LINK% in Template
 * - Code: Fixed Capabilities
 * - Code: Fixed i18n
 * - Code: Styling
 * 
 * 0.1.1
 * - Code: Clean Ups, added comments and some other stuff
 *
 * 0.1
 * - Initial Release
 */

if ( ! class_exists( 'Multipost_Newsletter' ) ) {
	
	if ( function_exists( 'add_filter' ) )
		add_filter( 'plugins_loaded' ,  array( 'Multipost_Newsletter', 'get_instance' ) );
	
	class Multipost_Newsletter {
		
		/**
		* The plugins textdomain
		*
		* @since	0.6
		* @access	public
		* @static
		* @var		string
		*/
		public static $textdomain = '';
		
		/**
		 * The plugins textdomain path
		 *
		 * @since	0.6
		 * @access	public
		 * @static
		 * @var		string
		 */
		public static $textdomainpath = '';
		
		/**
		 * Instance holder
		 *
		 * @since	0.6
		 * @access	private
		 * @static
		 * @var		NULL | Multipost_Newsletter
		 */
		private static $instance = NULL;
		
		/**
		 * The plugins Name
		 *
		 * @since 	1.0
		 * @static
		 * @access	public
		 * @var 	string
		 */
		public static $plugin_name = '';
		
		/**
		 * The plugins plugin_base
		 *
		 * @since 	1.0
		 * @access	public
		 * @static
		 * @var 	string
		 */
		public static $plugin_base_name = '';
		
		/**
		 * The plugins URL
		 *
		 * @since 	1.0
		 * @access	public
		 * @static
		 * @var 	string
		 */
		public static $plugin_url = '';
		
		/**
		 * Checks if plugin is pro
		 *
		 * @since 	1.0
		 * @access	public
		 * @static
		 * @var 	boolean
		 */
		public static $is_pro = FALSE;
		
		/**
		 * Method for ensuring that only one instance of this object is used
		 *
		 * @since	0.6
		 * @access	public
		 * @static
		 * @return	Multipost_Newsletter
		 */
		public static function get_instance() {
			
			if ( ! self::$instance )
				self::$instance = new self;
			return self::$instance;
		}
		
		/**
		 * Setting up some data, initialize localization and load
		 * the features
		 * 
		 * @since	0.6
		 * @access	public
		 * @return	void
		 */
		public function __construct () {
			
			// Textdomain
			self::$textdomain = $this->get_textdomain();
			// Textdomain Path
			self::$textdomainpath = $this->get_domain_path();
			// Initialize the localization
			$this->load_plugin_textdomain();
			
			// The Plugins Basename
			self::$plugin_base_name = plugin_basename( __FILE__ );
			// The Plugins URL
			self::$plugin_url = $this->get_plugin_header( 'PluginURI' );
			// The Plugins Name
			self::$plugin_name = $this->get_plugin_header( 'Name' );
			
			// Load the features
			$this->load_features();
		}
		
		/**
		 * Get a value of the plugin header
		 *
		 * @since	0.5
		 * @access	protected
		 * @param	string $value
		 * @uses	get_plugin_data, ABSPATH
		 * @return	string The plugin header value
		 */
		protected function get_plugin_header( $value = 'TextDomain' ) {
			
			if ( ! function_exists( 'get_plugin_data' ) )
				require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
		
			$plugin_data = get_plugin_data( __FILE__ );
			$plugin_value = $plugin_data[ $value ];
		
			return $plugin_value;
		}
		
		/**
		 * Get the Textdomain
		 *
		 * @since	0.5
		 * @access	public
		 * @return	string The plugins textdomain
		 */
		public function get_textdomain() {
			
			return $this->get_plugin_header( 'TextDomain' );
		}
		
		/**
		 * Get the Textdomain Path where the language files are located
		 *
		 * @since	0.5
		 * @access	public
		 * @return	string The plugins textdomain path
		 */
		public function get_domain_path() {
			
			return $this->get_plugin_header( 'DomainPath' );
		}
		
		/**
		 * Load the localization
		 *
		 * @since	0.5
		 * @access	public
		 * @uses	load_plugin_textdomain, plugin_basename
		 * @return	void
		 */
		public function load_plugin_textdomain() {
			
			load_plugin_textdomain( self::$textdomain, FALSE, dirname( plugin_basename( __FILE__ ) ) . self::$textdomainpath );
		}
		
		/**
		 * Returns array of features, also
		 * Scans the plugins subfolder "/features"
		 *
		 * @since	0.5
		 * @access	protected
		 * @return	void
		 */
		protected function load_features() {
			
			// Load Pro-Features
			if ( is_dir( dirname( __FILE__ ) . '/pro' ) );
				$handle = @opendir( dirname( __FILE__ ) . '/pro' );
			
			if ( $handle ) {
				
				// Loop through directory files
				while ( FALSE != ( $plugin = readdir( $handle ) ) ) {
						
					// Is this file for us?
					if ( '.php' == substr( $plugin, -4 ) ) {
						
						// Check Pro
						self::$is_pro = TRUE;
							
						// Include module file
						require_once dirname( __FILE__ ) . '/pro/' . $plugin;
					}
				}
				closedir( $handle );
			}
		
			// Get dir
			$handle = opendir( dirname( __FILE__ ) . '/features' );
			if ( ! $handle )
				return;
				
			// Loop through directory files
			while ( FALSE != ( $plugin = readdir( $handle ) ) ) {
		
				// Is this file for us?
				if ( '.php' == substr( $plugin, -4 ) ) {
					
					// Include module file
					require_once dirname( __FILE__ ) . '/features/' . $plugin;
				}
			}
			closedir( $handle );
		}
	}
	
	if ( ! function_exists( 'p' ) ) {
		/**
		 * This helper function outputs a given string,
		 * object or array
		 *
		 * @since	0.1
		 * @param 	mixed $output
		 * @return	void
		 */
		function p( $output ) {
			print '<br /><br /><br /><pre>';
			print_r( $output );
			print '</pre>';
		}
	}
}