<?php
class Bea_Post_Offline_Client{
	/**
	 * Constructor, register hooks
	 */
	public function __construct() {
		add_action( 'init', array( __CLASS__, 'init'), 1 );
		add_action( 'bea_post_cron_event', array( __CLASS__, 'cron') );
	}
	
	/**
	 * Register post status for post offline
	 */
	public static function init() {
		register_post_status( 'offline', array(
			'label' => __('Offline', 'bea-po'),
			'public' => false,
			'exclude_from_search' => true,
			'show_in_admin_all_list' => true,
			'show_in_admin_status_list' => true,
			'label_count' => _nx_noop( 'Offline <span class="count">(%s)</span>', 'Offline <span class="count">(%s)</span>', 'bea-po' ),
		) );
	}

	/**
	 * Action to update post status as offline
	 */
	public static function cron() {
		$cron_query = new WP_Query( array(
			'fields' => 'ids', 
			'post_type'=> Bea_Post_Offline_Base::getPostTypes(), 
			'meta_query' => array(
				array(
					'key' => '_offline_date',
					'value' => current_time('timestamp'),
					'type' => 'numeric',
					'compare' => '<='
				)
			)
		) );
		
		
		if ( isset($cron_query->posts) && is_array($cron_query->posts) && !empty($cron_query->posts) ) {
			foreach( $cron_query->posts as $post_id ) {
				wp_update_post( array( 'ID' => $post_id, 'post_status' => 'offline' ) );
			}
			
			return true;
		}
		
		return false;
	}
}