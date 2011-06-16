<?php
/**
 * Multi Post Newsletter
 * @license CC-BY-SA-NC
 * @package Multi Post Newsletter
 * @subpackage Model
 */

class multi_post_newsletter_model {
	protected function save_post_order ( $post_id, $order ) {
		global $wpdb;
		$this -> data = array(
				'menu_order' => $wpdb -> escape( $order ),
			);
		$this -> where = array(
				'ID' => $wpdb -> escape( $post_id ),
			);
		return $wpdb -> update( $wpdb -> posts, $this -> data, $this -> where );
	}
}