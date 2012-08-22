# BEA Post Offline

Create new post status "offline" and add WP Cron task to change post status when the expiration date has passed

 * Contributors: momo360modena
 * Donate link: http://www.beapi.fr/donate/
 * Tags: post, status, expiration, date, cron, wp-cron, unpublish, custom, offline
 * Requires at least: 3.4
 * Tested up to: 3.4.1
 * Stable tag: 1.0.1
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


* Version 1.0.1 :
	* Fix version number JS/CSS
	* Move test for test post_type before PHP logic
	* Add readme
* Version 1.0.0 :
	* First version stable