<?php 
if ( ! defined('ABSPATH') ) { exit; } // Защита от прямого вызова скрипта
function mfwp_help_page() {
?>
<div class="wrap">
<h1>Manual</h1>
<p>Add a customized Yandex maps to your WordPress posts and/or pages quickly and easily with the supplied shortcode. Also adds a Metabox in admin panel when editing posts or custom pages or pages.</p>

<h2>Adds Yandex map with one point</h2>

<p><code>[MapOnePoint id="" lat="" lon="" zoom="" h="" img=""]</code></p>

<p>All parameters are not required</p>

<p><code>id</code> - Required only when the shortcode on the page [MapOnePoin] used two or more times;<br>
<code>lat</code> and <code>lon</code> - latitude and longitude of the point. The separator dot. If not specified the plugin will try to get it from custom fields of the current page;<br>
<code>zoom</code> - zoom the map. A value from <code>1</code> to <code>18</code>. If not specified the plugin will try to get it from settings Maps for WP;<br>
<code>h</code> - map height in pixels. Default <code>450</code>;<br>
<code>img</code> - url of the image point (with <code>http://</code>). If not specified the plugin will try to get it from settings Maps for WP.</p>

<p>Example:</p>

<p><code>[MapOnePoint id="m1" img="http://site.ru/point.png"]</code><br>
<code>[MapOnePoint id="m2" lon="55.75197479670444" lat="37.617726067459024" zoom="5"]</code></p>

<p>Incorrectly:

<p><code>[MapOnePoint id="m1" img="site.ru/point.png"]</code><br>
<code>[MapOnePoint id="m1" lon="55,75197479670444" lat="37,617726067459024" zoom="19"]</code></p>

<h2>Adds Yandex map with many points</h2>

<p><code>[MapManyPoints h="" posttype="" img=""]</code></p>

<p>The plugin gets the coordinates from a custom page fields, records, pages, or custom page, specified in the parameter <code>posttype</code> using meta_query. If custom field is empty - the page is excluded from the query.</p>

<p><strong>It is important!</strong> Shortcode <code>[MapManyPoints]</code> not used on the page more than once.</p>

<p><code>h</code> - map height in pixels. Default <code>450</code>;<br>
<code>posttype</code> - Post type. The separator comma. Default <code>post,page</code>;<br>
<code>img</code> - url of the image point (with <code>http://</code>). If not specified the plugin will try to get it from settings Maps for WP.</p>

<p>Example:</p>

<p><code>[MapManyPoints]</code><br>
<code>[MapManyPoints h="500" posttype="post,page,myposttype" img="http://site.ru/point.jpg"]</code></p>

<p>Incorrectly:</p>

<p><code>[MapManyPoints h="500px" posttype="post, page, myposttype" img="site.ru/point.jpg"]</code></p>
</div>
<?php
} 
/* end функция настроек */ 
?>