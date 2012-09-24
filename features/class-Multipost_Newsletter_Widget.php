<?php
/**
 * Feature Name:	Multipost Newsletter Widget
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

if ( ! class_exists( 'Multipost_Newsletter_Widget' ) ) {
	
	if ( function_exists( 'add_filter' ) )
		add_filter( 'widgets_init', array( 'Multipost_Newsletter_Widget', 'register' ) );

	class Multipost_Newsletter_Widget extends WP_Widget {

		/**
		 * The plugins textdomain
		 *
		 * @since	0.1
		 * @access	public
		 * @static
		 * @var		string
		 */
		public static $textdomain = '';
		
		/**
		 * Checks if Plugin is a pro
		 *
		 * @since	0.1
		 * @access	public
		 * @static
		 * @var		boolean
		 */
		public static $is_pro = '';
		
		/**
		 * constructor
		 *
		 * @since	0.1
		 * @access	public
		 * @uses	__
		 * @return	void
		 */
		public function __construct() {
			
			self::$textdomain = Multipost_Newsletter::$textdomain;
			self::$is_pro = Multipost_Newsletter::$is_pro;
			
			// Add Lost Password Link
			add_filter( 'login_form_bottom', array( $this, 'login_form_bottom' ), 10, 2 );
			
			parent::__construct(
				'multipost-newsletter-widget',
				'Multipost Newsletter Widget',
				array(
					'description' => __( 'Use this widget to display the register formular.', self::$textdomain )
				)
			);
		}
		
		/**
		 * Add Lost Password Link
		 *
		 * @since	0.1
		 * @access	public
		 * @param	string $foo nothing
		 * @param	array $args
		 * @uses	
		 * @return	$string
		 */
		public function login_form_bottom( $foo = '', $args ) {
			
			return '<a href="' . get_bloginfo( 'url' ) . '/wp-login.php?action=lostpassword">' . __( 'Lost Password' ) . '</a>';
		}
		
		/**
		 * displays the widget in frontend
		 *
		 * @since	0.1
		 * @access	public
		 * @param	array $args the widget arguments
		 * @param	array $instance current instance
		 * @uses	apply_filters, get_option, is_email_address_unsafe, filter_var, FILTER_VALIDATE_EMAIL,
		 * 			__, get_site_option, email_exists, wp_insert_user, update_user_meta, sanitize_title_with_dashes,
		 * 			wp_new_user_notification, is_user_logged_in
		 * @return	void
		 */
		public function widget( $args, $instance ) {
			
			extract( $args );
			
			echo $before_widget;
			
			$title = '';
			if ( isset( $instance[ 'title' ] ) )
				$title = $instance[ 'title' ];
			
			$title = apply_filters( 'widget_title', $title );
			
			if ( '' != $title )
				echo $before_title . $title . $after_title;
			
			// Load Groups
			$groups = get_option( 'mp-newsletter-groups' );
			
			// Set index
			$success = FALSE;
			$my_errors = array();
			
			if ( isset( $_POST[ 'register' ] ) ) {
					
				// Is email valid?
				if ( '' == trim( $_POST[ 'user_email' ] ) || ! filter_var( $_POST[ 'user_email' ], FILTER_VALIDATE_EMAIL ) )
					$my_errors[ 'user_email' ] = __( 'Please enter a correct email address' );
			
				$limited_email_domains = get_site_option( 'limited_email_domains' );
				if ( is_array( $limited_email_domains ) && empty( $limited_email_domains ) == false ) {
					$emaildomain = substr( $_POST[ 'user_email' ], 1 + strpos( $_POST[ 'user_email' ], '@' ) );
					if ( in_array( $emaildomain, $limited_email_domains ) == false )
						$my_errors[ 'user_email' ] = __( 'Sorry, that email address is not allowed!' );
				}
			
				if ( email_exists( $_POST[ 'user_email' ] ) )
					$my_errors[ 'user_email' ] = __( 'Sorry, that email address is already used!' );
					
				// Check Type
				if ( ! isset( $_POST[ 'newsletter_type' ] ) || 0 == count( $_POST[ 'newsletter_type' ] ) )
					$my_errors[ 'newsletter_type' ] = __( 'Please chose if you want html or a text mail!', self::$textdomain );
					
				// Check Group
				if ( ( is_array( $groups ) && 0 < count( $groups ) ) && ! isset( $_POST[ 'groups' ] ) )
					$my_errors[ 'newsletter_group' ] = __( 'Please chose a group!', self::$textdomain );
					
				if ( ! empty( $my_errors ) ) {
					echo '<div class="error"><p><strong>' . __( 'An error occured!', self::$textdomain ) . '</strong></p><ul>';
					foreach ( $my_errors as $my_error )
						echo '<li>' . $my_error . '</li>';
					echo '<ul></div>';
				} else {
			
					// Generate Username
					$user_name = str_replace( '-', '', sanitize_title_with_dashes( $_POST[ 'user_email' ] ) );
			
					// Generate Password
					$password = substr( sha1( $user_name ), 0, 9 );
			
					// Insert User
					$userdata = array(
						'user_login'	=> $user_name,
						'user_email'	=> $_POST[ 'user_email' ],
						'user_pass'		=> $password,
						'role'			=> 'subscriber'
					);
					$new_user_id = wp_insert_user( $userdata );
			
					// Update Meta Information
					update_user_meta( $new_user_id, 'newsletter_receive', 'on' );
					update_user_meta( $new_user_id, 'newsletter_type', $_POST[ 'newsletter_type' ] );
					update_user_meta( $new_user_id, 'newsletter_groups', $_POST[ 'groups' ] );
					
					// Send Mail to user
					wp_new_user_notification( $new_user_id, $password );
					
					// Empty Post array
					$_POST = array( 0 );
					$success = TRUE;
				}
			}
			
			// Output
			if ( is_user_logged_in() ) {
				if ( isset( $_POST[ 'save_profile' ] ) ) {
					
					update_user_meta( get_current_user_id(), 'newsletter_receive', $_POST[ 'newsletter_receive' ] );
					
					if ( isset( $_POST[ 'newsletter_type' ] ) && 0 < count( $_POST[ 'newsletter_type' ] ) )
						update_user_meta( get_current_user_id(), 'newsletter_type', $_POST[ 'newsletter_type' ] );
					else
						delete_user_meta( get_current_user_id(), 'newsletter_type' );
					
					if ( isset( $_POST[ 'newsletter_groups' ] ) && 0 < count( $_POST[ 'newsletter_groups' ] ) )
						update_user_meta( get_current_user_id(), 'newsletter_groups', $_POST[ 'newsletter_groups' ] );
					else
						delete_user_meta( get_current_user_id(), 'newsletter_groups' );
					
					echo '<p><strong>' . __( 'Settings saved', self::$textdomain ) . '</strong></p>';
				}
				
				$groups = get_option( 'mp-newsletter-groups' );
					
				$newsletter_receive = get_user_meta( get_current_user_id(), 'newsletter_receive', TRUE );
					
				$newsletter_type = get_user_meta( get_current_user_id(), 'newsletter_type', TRUE );
				if ( ! is_array( $newsletter_type ) )
					$newsletter_type = array();
				
				$newsletter_groups = get_user_meta( get_current_user_id(), 'newsletter_groups', TRUE );
				if ( ! is_array( $newsletter_groups ) )
					$newsletter_groups = array();
				?>
				<form action="" method="post" id="register_form">
					<p><?php _e( 'Edit your newsletter settings here.', self::$textdomain ); ?></p>
					<p>
						<input id="newsletter_receive" name="newsletter_receive" type="checkbox" <?php if ( isset( $newsletter_receive ) && '' != $newsletter_receive ) { echo 'checked="checked"'; } ?> />
						<label for="newsletter_receive"><?php _e( 'Receive Newsletter', self::$textdomain ); ?></label>
					</p>
					
					<?php if ( TRUE == self::$is_pro ) { ?>
					<p>
						<input id="newsletter_type_text" name="newsletter_type[]" value="text" type="checkbox" <?php if ( in_array( 'text', $newsletter_type ) ) { echo 'checked="checked"'; } ?> /> <label for="newsletter_type_text"><?php _e( 'Text', self::$textdomain ); ?></label>
						<input id="newsletter_type_html" name="newsletter_type[]" value="html" type="checkbox" <?php if ( in_array( 'html', $newsletter_type ) ) { echo 'checked="checked"'; } ?> /> <label for="newsletter_type_html"><?php _e( 'HTML', self::$textdomain ); ?></label>
					</p>
					<?php } else {
						?><input name="newsletter_type[]" value="text" type="hidden" /><?php
					} ?>
					 
					<?php if ( TRUE == self::$is_pro && is_array( $groups ) && 0 < count( $groups ) ) {  ?>
					<p>
						<label for="groups"><?php _e( 'Groups', self::$textdomain ); ?></label>
						<select data-placeholder="Choose some Groups" id="groups" name="newsletter_groups[]" style="width: 100%;" multiple class="chzn-select">
							<?php foreach ( $groups as $group ) { ?>
								<option value="<?php echo $group; ?>" <?php if ( in_array( $group, $newsletter_groups ) ) { echo 'selected="selected"'; } ?>><?php echo $group; ?></option>
							<?php } ?>
						</select>
					</p>
					<?php } ?>
					<p>
						<a href="<?php echo wp_logout_url(); ?>" style="float: right;"><?php _e( 'Logout', self::$textdomain ); ?></a>
						<input type="submit" id="save_profile" name="save_profile" class="button-primary" value="<?php _e( 'Save' ); ?>" />
					</p>
				</form>
				<?php
			} else if ( TRUE == $success ) {
				?>
				<div class="updated"><p>
					<?php echo sprintf( __( 'You have been registered successfully. You\'ll now get an email with your generated username and password. Please <a href="%s">login</a> and change your credentials.', self::$textdomain ), wp_login_url() ); ?>
				</p></div>
				<?php
			} else {
				?>
				<form action="" method="post" id="register_form">
					<p>
						<input type="text" name="user_email" value="<?php if ( isset( $_POST[ 'user_email' ] ) ) echo $_POST[ 'user_email' ]; ?>" placeholder="<?php _e( 'E-mail' ); ?>" id="user_email" />
					</p>
					<?php if ( TRUE == self::$is_pro ) { ?>
					<p>
						<input id="newsletter_type_html" name="newsletter_type[]" value="html" type="checkbox" <?php if ( isset( $_POST[ 'newsletter_type' ] ) && in_array( 'html', $_POST[ 'newsletter_type' ] ) ) echo 'checked="checked"'; ?> /> <label for="newsletter_type_html"><?php _e( 'HTML', self::$textdomain ); ?></label>
						<input id="newsletter_type_text" name="newsletter_type[]" value="text" type="checkbox" <?php if ( isset( $_POST[ 'newsletter_type' ] ) && in_array( 'text', $_POST[ 'newsletter_type' ] ) ) echo 'checked="checked"'; ?> /> <label for="newsletter_type_text"><?php _e( 'Text', self::$textdomain ); ?></label>
					</p>
					<?php } else {
						?><input name="newsletter_type[]" value="text" type="hidden" /><?php
					} ?>
					
					<?php if ( TRUE == self::$is_pro && is_array( $groups ) && 0 < count( $groups ) ) {  ?>
					<p>
						<select data-placeholder="Choose some Groups" id="groups" name="groups[]" style="width: 100%;" multiple class="chzn-select">
							<?php foreach ( $groups as $group ) { ?>
								<option value="<?php echo $group; ?>" <?php if ( isset( $_POST[ 'groups' ] ) && in_array( $group, $_POST[ 'groups' ] ) ) echo 'selected="selected"'; ?>><?php echo $group; ?></option>
							<?php } ?>
						</select>
					</p>
					<?php } ?>
					
					<p>
						<input type="submit" id="submit_register" name="register" class="button-primary" value="<?php _e( 'Register' ); ?>" />
					</p>
					<p>
						<a href="#" id="already_registered"><?php _e( 'Already registered?', self::$textdomain ) ?></a>
					</p>
				</form>
				<div id="login_form">
					<?php wp_login_form(); ?>
					<p>
						<a href="#" id="register_for_newsletter"><?php _e( 'Register for newsletter', self::$textdomain ) ?></a>
					</p>
				</div>
				<?php
			}
			
			echo $after_widget;
		}
		
		/**
		 * process the options-updateing
		 *
		 * @since	0.1
		 * @access	public
		 * @param	array $new_instance
		 * @param	array $old_instance
		 * @return	array
		 */
		public function update( $new_instance, $old_instance ) {

			$instance = $old_instance;
			$instance[ 'title' ] = strip_tags( $new_instance[ 'title' ] );
			return $instance;
		}

		/**
		 * the backend options form
		 *
		 * @since	0.1
		 * @access	public
		 * @param	array $instance
		 * @uses	_e, esc_attr
		 * @return	string
		 */
		public function form( $instance ) {

			$title = '';

			if ( isset( $instance[ 'title' ] ) )
				$title = esc_attr( $instance[ 'title' ] );

			?>
			<p>
				<label for="<?php $this->get_field_id( 'title' );?>">
					<?php _e( 'Title:', self::$textdomain );?>
				</label><br />
				<input type="text" id="<?php echo $this->get_field_id( 'title' );?>" name="<?php echo $this->get_field_name( 'title' );?>" value="<?php echo $title; ?>" />
			</p>
			<?php
			return TRUE;
		}

		/**
		 * register
		 *
		 * @since	0.1
		 * @access	public
		 * @static
		 * @uses	register_widget
		 * @return	void
		 */
		public static function register() {
			register_widget( __CLASS__ );
		}
	}
}