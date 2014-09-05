=== THA Hooks Interface ===
Contributors: slobodanmanic, nikolicdragan
Tags: theme hook alliance, hooks, THA, ThematoSoup
Requires at least: 3.5
Tested up to: 4.0
Stable tag: 1.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

THA Hooks Interface allows you to customize and alter theme functionality from within dashboard by hooking into Theme Hook Alliance hooks. 

== Description ==

THA Hooks Interface is a WordPress plugin developed by [ThematoSoup](http://thematosoup.com). It creates a settings page which allows you to add code (PHP and HTML) by using hooks defined by the [Theme Hook Alliance](https://github.com/zamoose/themehookalliance).

Core WordPress offers a suite of action hooks and template tags, but does not cover many of the common use cases. The Theme Hook Alliance is a community-driven effort to agree on a set of third-party action hooks that THA-compatible themes pledge to implement in order to give desired consistency.

THA Hooks Interface checks whether a theme uses these standardized hooks and if it does, it creates an interface where you can add code to almost any part of the theme.

If your active theme does not declare support for [Theme Hook Alliance](https://github.com/zamoose/themehookalliance) hooks, you can still use with wp_head and wp_footer hooks which are standard WordPress hooks. Also, if it doesn't, please bug your theme developer about it :)

Banner image credits: http://www.flickr.com/photos/notahipster/3974559627/


== Installation ==

1. Navigate to the 'Add New' in the plugins dashboard
2. Search for THA Hooks Interface
3. Click 'Install Now'
4. Activate the plugin on the Plugin dashboard

= Uploading in WordPress Dashboard =

1. Navigate to the 'Add New' in the plugins dashboard
2. Navigate to the 'Upload' area
3. Select `tha-hooks-interface.zip` from your computer
4. Click 'Install Now'
5. Activate the plugin in the Plugin dashboard

= Using FTP =

1. Download 'tha-hooks-interface.zip'
2. Extract the 'tha-hooks-interface' directory to your computer
3. Upload the 'tha-hooks-interface' directory to the '/wp-content/plugins/' directory
4. Activate the plugin in the Plugin dashboard


== Frequently Asked Questions ==

= The plugin doesn't work. Why? =

You must have a theme that's created using standardized hooks from Theme Hook Alliance.

== Screenshots ==

1. THA Hooks Interface Dashboard - screenshot-1.png

== Changelog ==


= 1.1 =
* Fixes two more undefined index notices in class-tha-hooks-interface-admin.php, props @codecandid

= 1.0.1 =
* Fixes an undefined index notice in class-tha-hooks-interface-admin.php

= 1.0 =
* The first version of the plugin.