<?php 
if ( ! defined('ABSPATH') ) { exit; } // Защита от прямого вызова скрипта
function mfwp_set_page() {
if (isset($_REQUEST['submit_action'])) {
update_option('mfwp_zoom_OnePoint', stripslashes($_POST['mfwp_zoom_OnePoint']));
update_option('mfwp_zoom_ManyPoints', stripslashes($_POST['mfwp_zoom_ManyPoints']));
update_option('mfwp_center_lat_ManyPoints', stripslashes($_POST['mfwp_center_lat_ManyPoints']));
update_option('mfwp_center_lon_ManyPoints', stripslashes($_POST['mfwp_center_lon_ManyPoints']));
update_option('mfwp_point_img', stripslashes($_POST['mfwp_point_img']));
update_option('mfwp_code_hidden', stripslashes($_POST['mfwp_code_hidden']));

update_option('mfwp_lat_Create', stripslashes($_POST['mfwp_lat_Create']));
update_option('mfwp_long_Create', stripslashes($_POST['mfwp_long_Create']));

} ?>
<h1><?php _e('Settings Maps for WP', 'mfwp'); ?></h1>
<form class="form-table" id="yaiset" method="post" action="<? echo $_SERVER['REQUEST_URI'];?>">
<table class="form-table">
<tbody>
 <tr>
	<th scope="row"><label><?php _e('Text when you hover over a point', 'mfwp'); ?></label></th>
	<td><input type="text" name="mfwp_code_hidden" value="<?php echo get_option('mfwp_code_hidden'); ?>"></td>
 </tr>
 <tr>
	<th scope="row"><label><?php _e('The image point on the map (36×36)', 'mfwp'); ?></label></th>
	<td><?php 
		if (get_option('mfwp_point_img') !== '') { // если фото загружено
			$image_attributes = wp_get_attachment_image_src(get_option('mfwp_point_img'), array(44, 44));
			$src = $image_attributes[0]; // урл картинки
			$idimg = get_option('mfwp_point_img');
			} else {$idimg = ''; $src = plugin_dir_url(__FILE__).'img/no-img.png'; /* если картинки нет */}
		?>	
		<div class="cacf">
			<img data-src="<?php echo $default; ?>" src="<?php echo $src; ?>" width="44px" height="44px" />
			<div>
			<input type="hidden" name="mfwp_point_img" value="<?php echo $idimg; ?>" />
			<button style="padding-top: 3px;" type="button" class="upload_image_button button"><span class="dashicons dashicons-upload"></span></button>
			<button style="padding-top: 3px;" type="button" class="remove_image_button button"><span class="dashicons dashicons-no"></span></button>			
			</div>
		</div>
	</td>
 </tr>
 <tr>
	<th scope="row"><label><?php _e('Zoom map with many points', 'mfwp'); ?></label></th>
	<td><input required min="0" max="18" type="number" name="mfwp_zoom_ManyPoints" value="<?php echo get_option('mfwp_zoom_ManyPoints'); ?>"></td>
 </tr>
 <tr>
	<th scope="row"><label><?php _e('Latitude center map with many points', 'mfwp'); ?></label></th>
	<td><input required type="number" step="any" name="mfwp_center_lat_ManyPoints" value="<?php echo get_option('mfwp_center_lat_ManyPoints'); ?>"></td>
 </tr>
 <tr>
	<th scope="row"><label><?php _e('Longitude center map with many points', 'mfwp'); ?></label></th>
	<td><input required type="number" step="any" name="mfwp_center_lon_ManyPoints" value="<?php echo get_option('mfwp_center_lon_ManyPoints'); ?>"></td>
 </tr>
 <tr>
	<th scope="row"><label><?php _e('Zoom map with one point', 'mfwp'); ?></label></th>
	<td><input required min="0" max="18" type="number" name="mfwp_zoom_OnePoint" value="<?php echo get_option('mfwp_zoom_OnePoint'); ?>"></td>
 </tr>
 <tr>
	<th scope="row"><label><?php _e('Latitude map with one point', 'mfwp'); ?></label></th>
	<td><input required type="number" name="mfwp_lat_Create" value="<?php echo get_option('mfwp_lat_Create'); ?>"></td>
 </tr>
	<th scope="row"><label><?php _e('Longitude map with one point', 'mfwp'); ?></label></th>
	<td><input required type="number" name="mfwp_long_Create" value="<?php echo get_option('mfwp_long_Create'); ?>"></td>
 </tr>
 <tr>
	<th scope="row"></th>
	<td><input class="button-primary" type="submit" name="submit_action" value="<?php _e('Save settings', 'mfwp'); ?>" /></td>
 </tr>
</tbody>
</table>
</form>

<?php
} 
/* end функция настроек */ 
?>