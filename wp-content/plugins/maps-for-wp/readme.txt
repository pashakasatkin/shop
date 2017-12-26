=== Maps for WP ===
Contributors: icopydoc
Donate link: https://icopydoc.ru/donate/
Tags: yandex, maps, yandex maps, maps, map, яндекс, яндекс карты, карты, карта
Requires at least: 4.4.2
Tested up to: 4.8
Stable tag: 1.0.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Add a customized Yandex maps to your WordPress posts and/or pages quickly and easily with the supplied shortcode.

== Description ==

Add a customized Yandex maps to your WordPress posts and/or pages quickly and easily with the supplied shortcode. Also adds a Metabox in admin panel when editing posts or custom pages or pages.

= Adds Yandex map with one point =

[MapOnePoint id="" lat="" lon="" zoom="" h="" img=""] - Adds Yandex map with one point.

All parameters are not required

*   "id" - Required only when the shortcode on the page [MapOnePoin] used two or more times.
*   "lat" and "lon" - latitude and longitude of the point. The separator dot. If not specified the plugin will try to get it from custom fields of the current page.
*   "zoom" - zoom the map. A value from `1` to `18`. If not specified the plugin will try to get it from settings Maps for WP.
*   "h" - map height in pixels. Default `450`.
*   "img" - url of the image point (with `http://`). If not specified the plugin will try to get it from settings Maps for WP.

Example:

`[MapOnePoint id="m1" img="http://site.ru/point.png"]`
`[MapOnePoint id="m2" lon="55.75197479670444" lat="37.617726067459024" zoom="5"]`

Incorrectly:

`[MapOnePoint id="m1" img="site.ru/point.png"]`
`[MapOnePoint id="m1" lon="55,75197479670444" lat="37,617726067459024" zoom="19"]`

= Adds Yandex map with many points =

[MapManyPoints h="" posttype="" img=""] - Adds Yandex map with many points.

The plugin gets the coordinates from a custom page fields, records, pages, or custom page, specified in the parameter `posttype` using meta_query. If custom field is empty - the page is excluded from the query.

It is important! Shortcode `[MapManyPoints]` not used on the page more than once.

*   "h" - map height in pixels. Default `450`.
*   "posttype" - Post type. The separator comma. Default `post,page`.
*   "img" - url of the image point (with `http://`). If not specified the plugin will try to get it from settings Yandex Maps 7.

Example:

`[MapManyPoints]`
`[MapManyPoints h="500" posttype="post,page,myposttype" img="http://site.ru/point.jpg"]`

Incorrectly:

`[MapManyPoints h="500px" posttype="post, page, myposttype" img="site.ru/point.jpg"]`

== Installation ==

This section describes how to install the plugin and get it working.

1. Upload the entire `yandex-maps-for-wp` folder to the `/wp-content/plugins/` directory.
1. Activate the plugin through the 'Plugins' screen in WordPress
1. Use the Yandex Maps->Settings screen to configure the plugin
1. Add the shortcode (`[MapOnePoint]` or `[MapManyPoints]`) to the page topic

== Frequently Asked Questions ==

= Is it possible for one page to contain 2 of the shortcode [OneManyPoints]? =

Yes. These shortcodes can be an unlimited number. Provided that the parameters 'id' are different.

= Is it possible for one page to contain 2 of the shortcode [MapManyPoints]? =

No.

== Screenshots ==

1. screenshot-1.png

== Changelog ==

= 1.0.0 =
* First relise.

= 1.0.1 =
* Fixed a bug in which maps appear at the top of the page.

= 1.0.2 =
* Fixed plugin settings.

== Upgrade Notice ==

= 1.0.2 =
* Fixed plugin settings.