<?php
/**
 * Configuration section.
 *
 * NOTICE: Do not directly edit these constants here, define them in your wp-config.php file instead.
 */

// The slug name of the plugin whose information you want to retrieve. 
// More info in the read me file.
if (! defined('WPPI_PLUGIN_ID') ) define('WPPI_PLUGIN_ID', '');

// Expiration time of cached data expressed in number of seconds.
// Min value: 600 = 10 minutes
if (! defined('WPPI_CACHE_EXPIRATION') ) define('WPPI_CACHE_EXPIRATION', 3600);

// For the sake of performance, set this constant to false if you want 
// to completely disable the shortcode feature.
if (! defined('WPPI_ENABLE_SHORTCODE') ) define('WPPI_ENABLE_SHORTCODE', true);

// ---------------------------------------------------------------------------------------------------------


/**
 * Plugin Name: WP Plugin Info
 * Plugin URI: http://wordpress.org/extend/plugins/wp-plugin-info/
 * Description: Lets you retrieve information about a plugin from WordPress.org and show them into a post or page.
 * Version: 1.1.3
 * Author: Luigi Cavalieri
 * Author URI: http://profiles.wordpress.org/_luigi
 * License: GPLv2 or later
 * License URI: license.txt
 * 
 * 
 * @package WP Plugin Info
 * @version 1.1.3
 * @author Luigi Cavalieri
 * @license http://opensource.org/licenses/GPL-2.0 GPLv2.0 Public license
 * 
 * 
 * Copyright (c) 2012 Luigi Cavalieri (email: luigi.wpdev@gmail.com)
 * 
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * ---------------------------------------------------------------------------------------- */

define('WPPI_LOADER_DIR', dirname(__FILE__));

include( WPPI_LOADER_DIR . '/classes/wp-plugin-info.class.php' );

if ( is_admin() ) {
	include( WPPI_LOADER_DIR . '/classes/wp-plugin-info-admin.class.php' );
			
	WPPluginInfoAdmin::load();
}
elseif ( WPPI_ENABLE_SHORTCODE ) {
	WPPluginInfo::instance()->register_shortcode();
}


/**
 * Template tag.
 * Check the readme.txt for documentation.
 *
 * @since 1.1.1
 *
 * @param string $info_id
 * @param array $args
 * @return string
 */
function get_wp_plugin_info( $info_id, $args = array() ) {
	$defaults = array(
		'plugin_id' => WPPI_PLUGIN_ID,
		'default' => '-',
		'date_format' => null
	);
	$args = &wp_parse_args( $args, $defaults );
	$args['info_id'] = $info_id;
	
	return WPPluginInfo::instance()->get_plugin_info( $args );
}

/**
 * Template tag.
 * It is the same as get_wp_plugin_info() except that it echos its output.
 * Check the readme.txt for documentation.
 *
 * @since 1.1
 *
 * @param string $info_id
 * @param array $args
 */
function wp_plugin_info( $info_id, $args = array() ) {
	echo get_wp_plugin_info( $info_id, $args );
}
?>