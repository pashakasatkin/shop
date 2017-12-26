<?php 
if( ! defined('WP_UNINSTALL_PLUGIN') ) exit;
// проверка пройдена успешно. Начиная от сюда удаляем опции и все остальное.

function mfwp_delete_plugin() {
	global $wpdb; // подключаем класс wordpress для работы с БД

	delete_option('mfwp_version');
	delete_option('mfwp_zoom_ManyPoints'); // масштаб карты где много точек
	delete_option('mfwp_center_lat_ManyPoints'); // широта карты где много точек
	delete_option('mfwp_center_lon_ManyPoints'); // долгота карты где много точек
	
	delete_option('mfwp_zoom_OnePoint'); // масштаб карты где одна точка
	delete_option('mfwp_lat_Create'); // широта карты при создании где одна точка
	delete_option('mfwp_long_Create'); // долгота карты при создании где одна точка
	delete_option('mfwp_point_img'); // картинка маркера
		
	delete_option('mfwp_code_hidden'); // коммерческой недвижимостью
	delete_option('mfwp_code_header'); // коммерческой недвижимостью
	delete_option('mfwp_code_body'); // коммерческой недвижимостью
	delete_option('mfwp_code_footer'); // коммерческой недвижимостью
	delete_option('mfwp_add_post'); // коммерческой недвижимостью
	delete_option('mfwp_add_page'); // коммерческой недвижимостью
	delete_option('mfwp_add_tax'); // коммерческой недвижимостью
}

mfwp_delete_plugin(); // стартуем функцию удаления плагина
?>