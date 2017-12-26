<?php 
add_action( 'init', 'mfwp_add_new_custom_fields', 30 );
function mfwp_add_new_custom_fields() {
$options = array(
 array( // второй метабокс
	'id'	=>	'mfwp',
	'name'	=>	__('Location', 'mfwp'),
	'post'	=>	array('post','page'), // не только для постов, но и для страниц
	'pos'	=>	'normal',
	'pri'	=>	'high',
	'cap'	=>	'edit_posts',
	'args'	=>	array(
		array(
			'id'			=>	'lat',
			'title'			=>	__('Latitude', 'mfwp'),
			'class'			=>	'',
			'desc'			=>	'',
			'type'			=>	'text',
			'placeholder'		=>	'', // атрибут placeholder
			'cap'			=>	'edit_posts'
		),
		array(
			'id'			=>	'lon',
			'title'			=>	__('Longitude', 'mfwp'),
			'class'			=>	'',
			'desc'			=>	'',
			'type'			=>	'text',
			'placeholder'		=>	'', // атрибут placeholder
			'cap'			=>	'edit_posts'
		),
		array(
			'id'			=>	'country',
			'title'			=>	__('Country', 'mfwp'),
			'class'			=>	'',
			'desc'			=>	'',
			'type'			=>	'text',
			'placeholder'		=>	'', // атрибут placeholder
			'cap'			=>	'edit_posts'
		),
		array(
			'id'			=>	'region',
			'title'			=>	__('Region', 'mfwp'),
			'class'			=>	'',
			'desc'			=>	'',
			'type'			=>	'text',
			'placeholder'		=>	'', // атрибут placeholder
			'cap'			=>	'edit_posts'
		),
		array(
			'id'			=>	'subadministrative',
			'title'			=>	__('Subadministrative', 'mfwp'),
			'class'			=>	'',
			'desc'			=>	'',
			'type'			=>	'text',
			'placeholder'		=>	'', // атрибут placeholder
			'cap'			=>	'edit_posts'
		),
		array(
			'id'			=>	'city',
			'title'			=>	__('City', 'mfwp'),
			'class'			=>	'',
			'desc'			=>	'',
			'type'			=>	'text',
			'placeholder'		=>	'', // атрибут placeholder
			'cap'			=>	'edit_posts'
		),
		array(
			'id'			=>	'locat',
			'title'			=>	__('The exact address', 'mfwp'),
			'class'			=>	'',
			'desc'			=>	'',
			'type'			=>	'text',
			'placeholder'		=>	'', // атрибут placeholder
			'cap'			=>	'edit_posts'
		),			
		array(
			'id'			=>	'yadiv', // атрибуты name и id без префикса, например с префиксом будет meta1_fio
			'title'			=>	__('Container Yandex Maps', 'mfwp'), // лейбл поля
			'class'			=>	'', // лейбл поля			
			'type'			=>	'yadiv', // тип, в данном случае обычное текстовое поле
			'placeholder'		=>	'', // атрибут placeholder
			'desc'			=>	'', // что-то типа пояснения, подписи к полю
			'cap'			=>	'edit_posts'
		),	
	)
 )
);
foreach ($options as $option) {
	$tarifstabox2 = new mfwp_trueMetaBox($option);
}
}
?>