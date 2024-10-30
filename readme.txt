=== BotMonitoring ===
Contributors: BotMonitoring
Tags: ads, advertising, bot, fraud, protection
Requires at least: 3.0.6
Tested up to: 3.9
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Prevent bots from clicking ADs

== Description ==

Checks user's data (IP address and behaviour) and hides ADs from page when bot-like activity is detected.

== Installation ==

1. Install from WordPress plugin directory or unpacking ZIP file into botmonitoring directory;
2. Make sure relative d/ directory inside plugin is server-writable;
3. Activate plugin;
4. Go to plugin settings and enter you API key;
5. List banner container IDs. (You may need to wrap it in a DIV first and give an ID);
  
IMPORTANT! This plugin does not work with Google AdSense yet.

= Requirements =
* PHP 5.2 or higher.
* WordPress v3.0.x or higher.
* You must obtain API key by writing to maxd@firstbeatmedia.com.

== Changelog ==

= 0.0.23 =
* Special JS for iPad users preventing false positives

= 0.0.22 =
* Cleanup on de-activation, make possible to update from WP plugins repo

= 0.0.21 =
* Activation self-check for writable data directory

= 0.0.19 =
* Released for WP plugins repository

