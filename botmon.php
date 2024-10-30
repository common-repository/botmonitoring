<?php
/**
 * @package Botmon
 */
/*
Plugin Name: Botmon
Description: Bot monitoring and AD fraud preventing
Version: 0.0.23
Author: Max Deshkevich <maxd@firstbeatmedia.com>
Text Domain: botmon
* License: GPLv2
*
*  Copyright 2014 Content.ad (info@content.ad)
*
*  This program is free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License, version 2, as
*  published by the Free Software Foundation.
*
*  This program is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  You should have received a copy of the GNU General Public License
*  along with this program; if not, write to the Free Software
*  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

define( 'BOTMON_VERSION', '0.0.23' );
define( 'BOTMON_JS_CLIENT_VERSION', '0.0.17' );
define( 'BOTMON__PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'BOTMON__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

require_once(BOTMON__PLUGIN_DIR.'botmon.class.php');
require_once(BOTMON__PLUGIN_DIR.'botmon_admin.class.php');

register_activation_hook( __FILE__, array( 'Botmon', 'plugin_activation' ) );
register_deactivation_hook( __FILE__, array( 'Botmon', 'plugin_deactivation' ) );
add_action( 'wp_enqueue_scripts', array( 'Botmon', 'load_resources' ) );
add_action( 'wp_head', array( 'Botmon', 'header' ) );



if( is_admin() ) 
{
	add_action('admin_menu', function() {
		add_options_page('BotMonitoring Settings', 'BotMonitoring', 'administrator', 'botmon.php', array('BotmonAdmin', 'build_options_page'));
	});
	
	add_action('admin_init', function() {
		register_setting('botmon_options', 'botmon_options', function($botmon_options) {
			
			if( !is_array($botmon_options['ads']) ) 
			{
				$arAds = explode(',', $botmon_options['ads']);
			}
			else
			{
				$arAds = $botmon_options['ads'];
			}
			
			$arAds = array_map('trim', $arAds);
			$arAds = array_filter($arAds);
			
			$botmon_options['ads'] = $arAds;
			$botmon_options['api_key'] = trim(@$botmon_options['api_key']);
			
			file_put_contents(BOTMON__PLUGIN_DIR.'d/apikey.txt', $botmon_options['api_key']);
			
			return $botmon_options;
		});
		
		add_settings_section('main_section', 'BotMon Settings', 'intval', 'botmon.php');
		add_settings_field('api_key', 'API key:', function() {
			$options = get_option('botmon_options');
			echo('<input name="botmon_options[api_key]" type="text" value="'.$options['api_key'].'" />');
		}, 'botmon.php', 'main_section');
		add_settings_field('ads', 'ADs container IDs: (comma separated)', function() {
			$options = get_option('botmon_options');
			if( !is_array($options['ads']) ) $options['ads'] = array();
			echo('<textarea name="botmon_options[ads]">'.join(', ', $options['ads']).'</textarea>');
		}, 'botmon.php', 'main_section');
	});  

	add_filter("plugin_action_links_".plugin_basename(__FILE__), function ($links) {
		$settings_link = '<a href="options-general.php?page=botmon.php">Settings</a>';
		array_unshift($links, $settings_link);
		return $links;
	} );

}





