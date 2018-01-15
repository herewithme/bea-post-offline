<?php

class Bea_Post_Offline_Base {
	/**
	 * Register cron during activation
	 */
	public static function activate() {
		if ( ! wp_next_scheduled( 'bea_post_cron_event' ) ) {
			wp_schedule_event( current_time( 'timestamp' ), 'quarterly', 'bea_post_cron_event' );
		}
	}

	/**
	 * Remove cron during deactivation
	 */
	public static function deactivate() {
		wp_clear_scheduled_hook( 'bea_post_cron_event' );
	}

	public static function getStatus() {
		return apply_filters( 'bea_post_offline_custom_status', array( 'offline' ) );
	}

	public static function getPostTypes() {
		return apply_filters( 'bea_post_offline_custom_cpt', array( 'post', 'page' ) );
	}

	/**
	 * Add custom intervall for WP-CRON
	 * 
	 * @param array $schedules
	 *
	 * @return array
	 */
	public static function cron_schedules( $schedules = array() ) {
		$schedules['minutely']     = array( 'interval' => 60, 'display' => __( 'Once 1 minute', 'bea-po' ) );
		$schedules['fiveminutely'] = array( 'interval' => 300, 'display' => __( 'Once every 5 minutes', 'bea-po' ) );
		$schedules['quarterly']    = array( 'interval' => 900, 'display' => __( 'Once every 15 minutes', 'bea-po' ) );

		return $schedules;
	}
}
