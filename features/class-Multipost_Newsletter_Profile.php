<?php
/**
 * Feature Name:	Multipost Newsletter Profile
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

if ( ! class_exists( 'Multipost_Newsletter_Profile' ) ) {

	class Multipost_Newsletter_Profile extends Multipost_Newsletter {
		
		/**
		 * Instance holder
		 *
		 * @since	0.1
		 * @access	private
		 * @static
		 * @var		NULL | Multipost_Newsletter_Profile
		 */
		private static $instance = NULL;
		
		/**
		 * Method for ensuring that only one instance of this object is used
		 *
		 * @since	0.1
		 * @access	public
		 * @static
		 * @return	Multipost_Newsletter_Profile
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
		 * @uses	add_filter
		 * @return	void
		 */
		public function __construct () {
			
			// Add profile stuff
			add_filter( 'show_user_profile', array( $this, 'user_profile' ) );
			add_filter( 'edit_user_profile', array( $this, 'user_profile' ) );
			
			// Save profile stuff
			add_filter( 'edit_user_profile_update', array( $this, 'profile_update' ) );
			add_filter( 'personal_options_update', array( $this, 'profile_update' ) );
			
			// Add Column
			add_filter( 'manage_users_columns', array( $this, 'column_head' ) );
			add_filter( 'manage_users_custom_column', array( $this, 'column_content' ), 10, 3 );
		}
		
		/**
		 * Add Costum Collumn Head
		 *
		 * @since	0.1
		 * @access	public
		 * @param	array $defaults current default headers
		 * @uses	__
		 * @return	array $defaults modified headers
		 */
		public function column_head( $defaults ) {
			$defaults[ 'newsletter' ] = __( 'Newsletter', parent::$textdomain );
			return $defaults;
		}
		
		/**
		 * Add Costum Column Content
		 *
		 * @since	0.1
		 * @access	public
		 * @param	string $column_name
		 * @uses	get_user_meta, _e
		 * @return	void
		 */
		public function column_content( $value = 0, $column_name, $user_id ) {
			
			if ( 'newsletter' == $column_name ) {
				$newsletter_receive = get_user_meta( $user_id, 'newsletter_receive', TRUE );
				$newsletter_type = get_user_meta( $user_id, 'newsletter_type', TRUE );
				$newsletter_groups = get_user_meta( $user_id, 'newsletter_groups', TRUE );
				
				$return  = __( 'Recipient: ', parent::$textdomain ) . ( $newsletter_receive ? __( 'Yes' ) : __( 'No' ) ) . '<br />';
				
				if ( is_array( $newsletter_type ) && 0 < count( $newsletter_type ) )
					$return  .= __( 'Type: ', parent::$textdomain ) . implode( ', ', $newsletter_type ) . '<br />';
				
				if ( is_array( $newsletter_groups ) && 0 < count( $newsletter_groups ) )
					$return  .=  __( 'Groups: ', parent::$textdomain ) . implode( ', ', $newsletter_groups );
				
				return $return;
			}
		}
		
		/**
		 * Save the User Extra Fields
		 *
		 * @since	0.1
		 * @access	public
		 * @uses	update_user_meta
		 * @param	int $user_id The user id
		 * @return	void
		 */
		public function profile_update( $user_id ) {
		
			if ( 0 < count( $_POST ) ) {
				
				// Fix notices
				if ( ! isset( $_POST[ 'newsletter_receive' ] ) )
					$_POST[ 'newsletter_receive' ] = '';
				
				update_user_meta( $user_id, 'newsletter_receive', $_POST[ 'newsletter_receive' ] );

				if ( isset( $_POST[ 'newsletter_type' ] ) && 0 < count( $_POST[ 'newsletter_type' ] ) )
					update_user_meta( $user_id, 'newsletter_type', $_POST[ 'newsletter_type' ] );
				else
					delete_user_meta( $user_id, 'newsletter_type' );
				
				if ( isset( $_POST[ 'newsletter_groups' ] ) && 0 < count( $_POST[ 'newsletter_groups' ] ) )
					update_user_meta( $user_id, 'newsletter_groups', $_POST[ 'newsletter_groups' ] );
				else
					delete_user_meta( $user_id, 'newsletter_groups' );
			}
		}
		
		/**
		 * Displays the user input
		 *
		 * @since	0.1
		 * @access	public
		 * @param	object $user current user object
		 * @uses	get_option, get_user_meta, _e
		 * @return	void
		 */
		public function user_profile( $user ) {
			
			$groups = get_option( 'mp-newsletter-groups' );
			
			$newsletter_receive = get_user_meta( $user->ID, 'newsletter_receive', TRUE );
			
			$newsletter_type = get_user_meta( $user->ID, 'newsletter_type', TRUE );
			if ( ! is_array( $newsletter_type ) )
				$newsletter_type = array();
			
			$newsletter_groups = get_user_meta( $user->ID, 'newsletter_groups', TRUE );
			if ( ! is_array( $newsletter_groups ) )
				$newsletter_groups = array();
			?>
			<h3><?php _e( 'Newsletter Settings', parent::$textdomain ); ?></h3>

			<table class="form-table">
				<tr>
					<th>
						<label for="newsletter_receive"><?php _e( 'Receive Newsletter', parent::$textdomain ); ?></label>
					</th>
					<td>
						<input id="newsletter_receive" name="newsletter_receive" type="checkbox" <?php if ( isset( $newsletter_receive ) && '' != $newsletter_receive ) { echo 'checked="checked"'; } ?> />
					</td>
				</tr>
				<?php if ( TRUE == parent::$is_pro ) { ?>
				<tr>
					<th>
						<label for="newsletter_type"><?php _e( 'Type', parent::$textdomain ); ?></label>
					</th>
					<td>
						<input id="newsletter_type_text" name="newsletter_type[]" value="text" type="checkbox" <?php if ( in_array( 'text', $newsletter_type ) ) { echo 'checked="checked"'; } ?> /> <label for="newsletter_type_text"><?php _e( 'Text', parent::$textdomain ); ?></label>
						<input id="newsletter_type_html" name="newsletter_type[]" value="html" type="checkbox" <?php if ( in_array( 'html', $newsletter_type ) ) { echo 'checked="checked"'; } ?> /> <label for="newsletter_type_html"><?php _e( 'HTML', parent::$textdomain ); ?></label><br />
					</td>
				</tr>
				<?php } else {
					?><input name="newsletter_type[]" value="text" type="hidden" /><?php
				} ?>
				<?php if ( TRUE == parent::$is_pro && is_array( $groups ) && 0 < count( $groups ) ) {  ?>
				<tr>
					<th>
						<label for="newsletter_groups"><?php _e( 'Groups', parent::$textdomain ); ?></label>
					</th>
					<td>
						<?php foreach ( $groups as $group ) { ?>
							<input id="newsletter_groups_<?php echo $group ?>" name="newsletter_groups[]" value="<?php echo $group ?>" type="checkbox" <?php if ( in_array( $group, $newsletter_groups ) ) { echo 'checked="checked"'; } ?> /> <label for="newsletter_groups_<?php echo $group ?>"><?php echo $group; ?></label><br />
						<?php } ?>
					</td>
				</tr>
				<?php } ?>
			</table>
			<?php
		}
	}
	
	// Kickoff
	if ( function_exists( 'add_filter' ) )
		Multipost_Newsletter_Profile::get_instance();
}