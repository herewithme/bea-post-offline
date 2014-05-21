<?php
/*
Plugin Name: BEA Post Offline
Plugin URI: https://github.com/herewithme/bea-post-offline/
Description: Create new post status "offline" and add WP Cron task to change post status when the expiration date has passed
Version: 1.0.3
Author: Amaury Balmer
Author URI: http://www.beapi.fr
Text Domain: bea-po
Domain Path: /languages/
Network: false

----

Copyright 2012 Amaury Balmer (amaury@beapi.fr)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

define( 'BEA_PO_VERSION', '1.0.3' );
define( 'BEA_PO_URL', plugins_url('', __FILE__) );
define( 'BEA_PO_DIR', dirname(__FILE__) );

// Lib
require( BEA_PO_DIR . '/inc/class-base.php');
require( BEA_PO_DIR . '/inc/class-client.php');
if( is_admin() ) {
	require( BEA_PO_DIR . '/inc/class-admin.php');
}

// Activate/Desactive
add_filter( 'cron_schedules', array( 'Bea_Post_Offline_Base', 'cron_schedules') );
register_activation_hook  ( __FILE__, array('Bea_Post_Offline_Base', 'activate') );
register_deactivation_hook( __FILE__, array('Bea_Post_Offline_Base', 'deactivate') );

// Init plugin
function bea_post_offline_init() {
	global $bea_po;
	
	// Load translations
	load_plugin_textdomain ( 'bea-po', false, basename(rtrim(dirname(__FILE__), '/')) . '/languages' );
	
	$bea_po['client'] = new Bea_Post_Offline_Client();
	
	if( is_admin() ) {
		$bea_po['admin'] = new Bea_Post_Offline_Admin();
	}

}
add_action( 'plugins_loaded', 'bea_post_offline_init' );