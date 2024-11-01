=== WP Plugin Info ===
Contributors: _luigi
Plugin URI: http://wordpress.org/extend/plugins/wp-plugin-info/
Tags: api, developer, info, information, plugin, remote, repository, shortcode, template, tool, wordpress
Requires at least: 3.3
Tested up to: 3.5.2
Stable tag: 1.1.3
License: GPL v2.0

Lets you retrieve information about a plugin from WordPress.org and show them into a post or page.

== Description ==

**BEWARE! Development dropped. It will turn into a [Glitch](http://en.wikipedia.org/wiki/Glitch) sooner than later.**

*WP Plugin Info* is addressed mainly to plugin developers. It lets you show, in a post or page, any kind of information related to a plugin hosted on WordPress.org. I developed it for my own use on [sitetreeplugin.com](http://sitetreeplugin.com), some of the information shown in the homepage and other areas of the website are fetched with this plugin.

= Features =
* Shortcode-based plugin: you just need the shortcode `[wp-plugin-info]` to use it.
* The information retrieved are cached and refreshed once per hour. It is able to efficiently manage the caching of multiple arrays of information (each one related to a different plugin) at the same time.
* Optimised performance: even if you insert the shortcode multiple times into the same page, the access to the cached data will be performed just once.
* Template tags to use the plugin directly into a template file.

= Shortcode Attributes =

**`plugin_id`** (Required)  
*Description:* The slug name of the plugin whose information you want to retrieve.  
*Value:* the basename of the url of the plugin page.  

For example, the value of `plugin_id` for this plugin is `wp-plugin-info`, because the url of this plugin page is `http://wordpress.org/extend/plugins/wp-plugin-info/`.

**`info_id`** (Required)  
*Description:* The unique identifier associated to the information to retrieve.  
*Accepted values:* name, slug, version, author, author_profile, contributors, requires, tested, rating, num_ratings, downloaded, last_updated, added, homepage, download_link, description, installation, screenshots, changelog, faq

**`default`** (Optional)  
*Description:* The string to show if an error occurs while retrieving the information.  
*Default:* The character "-".

**`date_format`** (Optional)  
*Description:* The format of the date to show. This is an attribute to be used only when retrieving a date.  
*Value:* A valid [PHP date format](http://codex.wordpress.org/Formatting_Date_and_Time).

= Template Tags =
The following tags can be used directly into a template file.  

`<?php wp_plugin_info( $info_id, $args ); ?>`

*$info_id* (Required): a string value  
*$args* (Optional): an associative array  

`<?php

$args = array(
	'plugin_id'	  => WPPI_PLUGIN_ID,
	'default'	  => '-',
	'date_format' => null
);

?>`

If you want to manipulate the result, use the tag below instead. The arguments are the same.

`<?php get_wp_plugin_info( $info_id, $args ); ?>`

= Configuration =
The plugin is ready to use just after its activation, however, there are a couple settings you can customise through the php constants listed below.  
It is recommended to define these constants in your `wp-config.php` file instead of directly editing them in the main plugin file. In the latter case you should set them every time you update the plugin.

* `WPPI_CACHE_EXPIRATION`: cannot be set a value lower than 600 (seconds).
* `WPPI_PLUGIN_ID`: if set, you don't need to include the attribute `plugin_id` into the shortcode. However, The attribute `plugin_id` has an higher priority, so it can override the value of the constant.
* `WPPI_ENABLE_SHORTCODE`: the default value is `true`. Set it to `false` if you want to disable the shortcode feature – recommended if you don't use it.

= In Practice =
Suppose you want to show the information listed in the homepage of sitetreeplugin.com and you set the php constant `WPPI_PLUGIN_ID`, what you would write in a post or page should look like this:

> Version: `[wp-plugin-info info_id="version"]`  
> Requires: WordPress `[wp-plugin-info info_id="requires"]` or higher  
> Release date: `[wp-plugin-info info_id="last_updated" date_format="Y-n-j"]`  
> Downloads: `[wp-plugin-info info_id="downloaded"]`

The same can be achieved with the template tag:

> Version: `<?php wp_plugin_info( 'version' ); ?>`  
> Requires: WordPress `<?php wp_plugin_info( 'requires' ); ?>` or higher  
> Release date: `<?php wp_plugin_info( 'last_updated', array( 'date_format' => 'Y-n-j' ) ); ?>`  
> Downloads: `<?php wp_plugin_info( 'downloaded' ); ?>`

== Installation ==

1. Upload the folder `wp-plugin-info` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress

== Upgrade Notice ==

= 1.1.3 =
The cache expiration date is now displayed in the admin area. Minor changes.

== Changelog ==

= 1.1.3 =
* The cache expiration date is now displayed in the admin area.
* Minor changes.

= 1.1.2 =
Fixed an error in the documentation.

= 1.1.1 =
* Added the template tag `get_wp_plugin_info()`

= 1.1 =
* Reduced the number of database queries performed in the worst case.
* Added a button to delete the cache.
* Added the template tag `wp_plugin_info()`.
* Added the configuration constant `WPPI_ENABLE_SHORTCODE`.
* Minor fixes.

= 1.0 =
* Initial release.