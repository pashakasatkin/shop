<?php
if ( ! defined('ABSPATH') ) { exit; } // Защита от прямого вызова скрипта
/* ШОРТКОД ЯК МНОГО ТОЧЕК */
 add_action('init', 'mfwp_add_shortcode_ManyPoints');
 /* где:
	[MapManyPoints h="" posttype="" img=""] - так выглядит шорткод для вывода формы
	mfwp_add_shortcode2 - функция добавления шорткода
	mfwp_visibility_map - функция вывода карты
 */
 function mfwp_add_shortcode_ManyPoints(){ 
	add_shortcode('MapManyPoints', 'mfwp_visibility_map');
 }
 function mfwp_visibility_map($atts){
	if (isset($atts['posttype'])) {
		$arrposttype = explode(",", $atts['posttype']);		
	} else {
		$arrposttype = array('page','post');
	}
	if (isset($atts['h'])) {$h = $atts['h'];} else {$h = '700';} 
	$true_args = array(
		'post_type' => $arrposttype,
		'meta_query' => array(
			'relation' => 'AND',
			array(
				'key' => 'mfwp_lat',
				'value' => array( -547, 549 ),
				'type' => 'numeric',
				'compare' => 'BETWEEN'
			),
			array(
				'key' => 'mfwp_lon',
				'value' => array( -547, 533 ),
				'type' => 'numeric',
				'compare' => 'BETWEEN'
			)
		)
	);
	$q = new WP_Query( $true_args );
	if($q->have_posts()) {
	 $nn=0;
	 
	if (isset($atts['img'])) {
		$src = $atts['img'];
	} else {
		if (get_option('mfwp_point_img') == '') {
			$src = WP_PLUGIN_URL ."/maps-for-wp/img/marker.png";
		} else {
			$image_attributes_res = wp_get_attachment_image_src(get_option('mfwp_point_img'), array(130, 130));
			$src = $image_attributes_res[0]; // урл картинки		
		}	
	}
	 
	 while($q->have_posts()){ $q->the_post(); 
		$arr[] = array(
		'latitude'=>get_post_meta(get_the_ID(), 'mfwp_lat', 1),
		'longitude'=>get_post_meta(get_the_ID(), 'mfwp_lon', 1), 
		'id'=>get_the_ID(),
		'mfwp_lat_centerManyPoints'=> get_option('mfwp_center_lat_ManyPoints'),
		'mfwp_long_centerManyPoints'=> get_option('mfwp_center_lon_ManyPoints'),
		'mfwp_zoom_ManyPoints'=> get_option('mfwp_zoom_ManyPoints'),			
		'thumbnail'=>get_the_post_thumbnail(get_the_ID(), array(100,100)),
		'the_title'=>'<a href="'.get_the_permalink().'" title="'.get_the_title().'">'.get_the_title().'</a>',
		'iconImageHref'=>$src,
//		'hintContent'=>get_option('mfwp_code_hidden'),
		);
		
		$nn++;
	 }
	}
	/* восстанавливаем глобальную переменную $post */
	wp_reset_postdata();
	$js_obj = json_encode($arr);
	print "<script language='javascript'>var mfwp_setings_ManyPoints=$js_obj; </script>";
	wp_reset_postdata(); // восстанавливаем глобальную переменную $post
	?><div id="yamapManyPoints" style="height: <?php echo $h; ?>px; border: 1px solid #DFDFDF;"></div><?php 
 }
/* END ШОРТКОД ЯК МНОГО ТОЧЕК */
/* ШОРТКОД ЯК ОДНА ТОЧКА */
 add_action('init', 'mfwp_add_shortcode');
 /* где:
	[MapOnePoint id="" lat="" lon="" zoom="" h="" img=""] - так выглядит шорткод для вывода формы
	mfwp_add_shortcode - функция добавления шорткода
	mfwp_visibility_map_onepoint - функция вывода карты
 */
 function mfwp_add_shortcode(){
	add_shortcode('MapOnePoint', 'mfwp_visibility_map_onepoint');
 }
 function mfwp_visibility_map_onepoint($atts){
	if (isset($atts['id'])) {$mfwp_id = $atts['id'];} else {$mfwp_id="0";}
	if ((isset($atts['lat']))&&(isset($atts['lon']))) {
		$mfwp_latitude = $atts['lat'];
		$mfwp_longitude = $atts['lon'];
	} else {
		$mfwp_latitude = get_post_meta(get_the_ID(), 'mfwp_lat', 1);
		$mfwp_longitude = get_post_meta(get_the_ID(), 'mfwp_lon', 1);
	}
	if (isset($atts['img'])) {
		$src = $atts['img'];
	} else {
		if (get_option('mfwp_point_img') == '') {			
			$src = WP_PLUGIN_URL ."/maps-for-wp/img/marker.png";
		} else {
			$image_attributes_res = wp_get_attachment_image_src(get_option('mfwp_point_img'), array(130, 130));
			$src = $image_attributes_res[0]; // урл картинки		
		}
	}
	
	if (isset($atts['zoom'])) {$zoom = '5'.$atts['zoom'];} else {$zoom = get_option('mfwp_zoom_OnePoint');}
	if (isset($atts['h'])) {$h = $atts['h'];} else {$h = '400';}
	$arr2[] = array(
		'mfwp_latitude' => $mfwp_latitude,
		'mfwp_longitude' => $mfwp_longitude,
		'mfwp_zoom' => get_option('mfwp_zoom_OnePoint'),
		'iconImageHref'=> $src,
		'hintContent'=>get_option('mfwp_code_hidden'),
		'mfwp_id' => $mfwp_id
	);
	$js_obj2 = json_encode($arr2); 

	print "<script language='javascript'>if (typeof mfwp_setings_OnePoint2 =='undefined'){ var mfwp_setings_OnePoint2 = []; mfwp_setings_OnePoint2.push($js_obj2); } else {mfwp_setings_OnePoint2.push($js_obj2);}</script>";
	return "<div class='yamapOnePoint' id='yamapOnePoint".$mfwp_id."' style='clear: both; width: 100%; height: ".$h."px; border: 1px solid #DFDFDF; margin-right: 40px; '></div>";
 }
/* END ШОРТКОД ЯК ОДНА ТОЧКА */	
?>