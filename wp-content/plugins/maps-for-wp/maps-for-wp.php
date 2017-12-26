<?php 
/*
Plugin Name: Maps for WP
Description: Профессиональный плагин для создания яндекс карт при помощи шорткодов.
Tags: yandex, maps, yandex maps, maps, map, яндекс, яндекс карты, карты, карта
Author: Maxim Glazunov
Author URI: https://icopydoc.ru
License: GPLv2
Version: 1.0.2
Text Domain: maps-for-wp
Domain Path: /languages/
*/
/*  Copyright YEAR  PLUGIN_AUTHOR_NAME  (email : djdiplomat@yandex.ru)
 
    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.
 
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
 
    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
/* АКТИВАЦИЯ ПЛАГИНА */
 // Хук при активации плагина
 register_activation_hook(__FILE__, 'mfwp_set_options');
 function mfwp_set_options() {
	global $wpdb; // класс wordpress для работы с БД
	// Устанавливаем опции по умолчанию 
	// (они будут храниться в таблице настроек WP)
	add_option('mfwp_version', '1.0.0');
	add_option('mfwp_zoom_ManyPoints', '5'); // масштаб карты где много точек
	add_option('mfwp_center_lat_ManyPoints', '47.236'); // широта карты где много точек
	add_option('mfwp_center_lon_ManyPoints', '38.896'); // долгота карты где много точек
	
	add_option('mfwp_zoom_OnePoint', '14'); // масштаб карты где одна точка
	add_option('mfwp_lat_Create', '47.236'); // широта карты при создании где одна точка
	add_option('mfwp_long_Create', '38.896'); // долгота карты при создании где одна точка	
	
	add_option('mfwp_point_img', ''); // картинка маркера
		
	add_option('mfwp_code_hidden', ''); 
	add_option('mfwp_code_header', ''); 
	add_option('mfwp_code_body', ''); 
	add_option('mfwp_code_footer', ''); 
	add_option('mfwp_add_post', 'checked'); 
	add_option('mfwp_add_page', 'checked'); 
	add_option('mfwp_add_tax', 'checked'); 
 }
 // Добавляем новое меню в админку Wordpress
 add_action('admin_menu', 'mfwp_admin_page'); 
 /* где:
	mfwp_admin_page - Создаёт кнопку для перехода к страницу настроек плагина в админке WP
	mfwp_options_page - функция в файле set.php. Отвечает за страницу настроек в админке
 */
 /* Подключаем файлы перевода*/
 add_action( 'plugins_loaded', 'mfwp_load_plugin_textdomain' );
 function mfwp_load_plugin_textdomain() {
	load_plugin_textdomain( 'mfwp', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' ); 
 }
 /* end Подключаем файлы перевода*/
 function mfwp_admin_page() {
	add_menu_page(null , 'YandexMaps', 'manage_options', 'mfwp_yamaps', 'mfwp_help_page', 'dashicons-location-alt', 29);
	add_submenu_page( 'mfwp_yamaps', __('Settings', 'mfwp'), __('Settings', 'mfwp'), 'manage_options', 'mfwp_slug', 'mfwp_set_page');
 }		
/* END АКТИВАЦИЯ ПЛАГИНА */
/* СИСТЕМНЫЙ БЛОК */
 define('mfwp_DIR', plugin_dir_path(__FILE__)); // mfwp_DIR содержит /home/p13687/www/site.ru/wp-content/plugins/maps-for-wp/
 define('mfwp_URL', plugin_dir_url(__FILE__)); // mfwp_URL содержит http://site.ru/wp-content/plugins/maps-for-wp/
 add_action('init', 'class_add_custom_fields', 10); // вешаем на init
 function class_add_custom_fields() {	
	require_once (WP_PLUGIN_DIR .'/maps-for-wp/includes/class-add-custom-fields.php'); // Подключаем класс 
 }
/* ПОДКЛЮЧЕНИЕ СКРИПТОВ  */
 add_action('wp_print_scripts', 'mfwp_script_enqueuer', 10 ); // Вешаем на хук в момент печати скриптов. Заголовки уже переданы.
 /* где:
 *	mfwp_script_enqueuer - Функция инициализации скриптов
 *	mfwp_send_ajax - функция отправки письма
 *	send.js - аякс-скрипт. Отвечает за передачу писем.
 */
 class mfwp_set_pugin {
	public function set_ymaps() {
		$arr2[] = array(
			'mfwp_lat_Create'=> get_option('mfwp_lat_Create'),
			'mfwp_long_Create'=> get_option('mfwp_long_Create'),
			'mfwp_zoom_Create'=> 10,
			
			'mfwp_lat_OnePoint'=> get_post_meta(get_the_ID(), 'mfwp_lat', 1),
			'mfwp_long_OnePoint'=> get_post_meta(get_the_ID(), 'mfwp_lon', 1),
			'mfwp_zoom_OnePoint'=> get_option('mfwp_zoom_OnePoint')	
		);
		$js_obj2 = json_encode($arr2);	
		print "<script language='javascript'>var mfwp_setings=$js_obj2; </script>";
	}
 } 
 function mfwp_script_enqueuer() { // добавляет скрипт
 	$mfwp_set_pugin = new mfwp_set_pugin(); // содаем экземпляр класса
	$mfwp_set_pugin->set_ymaps(); //вызываем настроки карты
 
	wp_enqueue_script( 'jquery' );
	wp_register_script( 'mfwp_control', plugins_url('/control.js', __FILE__) );
	wp_enqueue_script( 'mfwp_control' );
 }
 function mfwp_enqueue($hook) { // регаем скрипты для админки, кроме некоторых разделов
	if ( 'plugins.php' == $hook ) {
		return;
	}
	if ( 'update-core.php' == $hook ) {
		return;
	}
	if ( 'plugin-install.php' == $hook ) {
		return;
	}		
	wp_register_script( 'mfwp_yandexmaps', 'https://api-maps.yandex.ru/2.1/?lang=ru_RU' );
	wp_enqueue_script( 'mfwp_yandexmaps','', '','', true ); // подключаем в футре
	wp_register_script( 'mfwp_geocode_js', plugins_url('/create-ymaps.js', __FILE__) );
	wp_enqueue_script( 'mfwp_geocode_js','', '','', true  ); // подключаем в футре
 }
 add_action( 'admin_enqueue_scripts', 'mfwp_enqueue' );
	
 function mfwp_enqueue_fp() { //регистрируем скрипты для внешней части сайта
	wp_register_script( 'mfwp_yandexmaps', 'https://api-maps.yandex.ru/2.1/?lang=ru_RU' );
	wp_enqueue_script( 'mfwp_yandexmaps','', '','', true ); // подключаем в футре
	wp_register_script( 'mfwp_geocode_js', plugins_url('/create-ymaps.js', __FILE__) );
	wp_enqueue_script( 'mfwp_geocode_js','', '','', true  ); // подключаем в футре
 }		
 add_action( 'wp_enqueue_scripts', 'mfwp_enqueue_fp' );
/* END СИСТЕМНЫЙ БЛОК */
/* СТРАНИЦА НАСТРОЕК ПЛАГИНА */
 require_once mfwp_DIR.'/settings.php'; // Подключаем файл настроек
 require_once mfwp_DIR.'/yandex-custom-fields.php'; // Подключаем файл настроек
 require_once mfwp_DIR.'/yandex-shortcode.php'; // Подключаем файл настроек
 require_once mfwp_DIR.'/yandex-help-page.php'; // Подключаем файл помощи
/* END СТРАНИЦА НАСТРОЕК ПЛАГИНА */
?>