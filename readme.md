# BEA Post Offline

Create new post status "offline" and add WP Cron task to change post status when the expiration date has passed

 * Contributors: http://profiles.wordpress.org/momo360modena/
 * Donate link: http://www.beapi.fr/donate/
 * Tags: post, status, expiration, date, cron, wp-cron, unpublish, custom, offline
 * Requires at least: 3.8
 * Tested up to: 4.9.x
 * Stable tag: 1.1
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html

## Description

This plugin allow :
	
	* Create new post status "offline" 
	* Set an expiration date for each post/page (extensible with hook)
	* Set a WP Cron task to change post status when the expiration date has passed

No custom UI, less code as possible, no notices.
Write with WP_DEBUG to true.

##  Installation

1. Download, unzip and upload to your WordPress plugins directory
2. Activate the plugin within you WordPress Administration Backend
3. Write content and set expiration date

## Changelog
* Version 1.1 :
	* Check compatibility for WP > 4.9
	* Some minor improvements in code (code standards)
* Version 1.0.3 :
	* Style and code for WP 3.8 >
* Version 1.0.2 :
	* Add compiled JS/CSS
	* Fix bug on JS with latest comma and IE
	* Add pot translation file and french translation
	* Fix bug with JS on new content page (auto-draft)
* Version 1.0.1 :
	* Fix version number JS/CSS
	* Move test for test post_type before PHP logic
	* Add readme
* Version 1.0.0 :
	* First version stable