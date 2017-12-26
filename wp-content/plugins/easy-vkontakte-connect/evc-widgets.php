<?php

function evc_widget_tabs_cm( $tabs ) {
	$tabs['evc_widget_community_messages'] = array(
		'id'       => 'evc_widget_community_messages',
		'name'     => 'evc_widget_community_messages',
		'title'    => __( 'Сообщения', 'evc' ),
		'desc'     => __( '', 'evc' ),
		'sections' => array(
			'evc_widget_community_messages_section'         => array(
				'id'    => 'evc_widget_community_messages_section',
				'name'  => 'evc_widget_community_messages_section',
				'title' => __( 'Виджет Сообщения сообществ', 'evc' ),
				'desc'  => __( 'Глобальные настройки виджета Сообщения сообщества.', 'evc' ),
			),
			'evc_widget_community_messages_options_section' => array(
				'id'    => 'evc_widget_community_messages_options_section',
				'name'  => 'evc_widget_community_messages_options_section',
				'title' => __( 'Настройки виджета', 'evc' ),
				'desc'  => __( 'Детальные настройки виджета Сообщения сообщества.', 'evc' ),
			)
		)
	);

	return $tabs;
}

add_filter( 'evc_widget_tabs', 'evc_widget_tabs_cm' );


function evc_widget_fields_cm( $fields ) {

	$fields['evc_widget_community_messages_section'] = array(
		array(
			'name'    => 'evc_community_messages',
			'label'   => __( 'Сообщения сообществ', 'evc' ),
			'desc'    => __( 'Показывать или нет виджет Сообщения сообщества.
			<br><br><strong>Внимание!</strong> Для начала работы виджет нужно включить в настройках группы на сайте ВКонтакте: <strong>Управление сообществом </strong>- <strong>Сообщения</strong>.
			<br><br><b>Внимание!</b> Виджет привязан к группе, которая задана в <a href = "'.admin_url('admin.php?page=evc-autopost').'">настройках плагина</a> на сайте: <b>EVC</b> - <b>Автопостинг</b>.', 'evc' ),
			'type'    => 'radio',
			'default' => '0',
			'options' => array(
				'1' => 'Показывать',
				'0' => 'Не показывать'
			)
		),
		array(
			'name'    => 'evc_community_messages_scenarios',
			'label'   => __( 'Сценарии', 'evc' ),
			'desc'    => __( 'Примерные сценарии отображения виджета.
<ol>
<li><b>Агрессивный</b> - показывается максимальному количеству посетителей, максимально привлекая к себе внимание. </li>
<li><b>Оптимальный</b> - показывается заинтересованным посетителям, привлекая к себе внимание.</li>
<li><b>Незаметный</b> - показывается только заинтересованным посетителям, по возможности, не привлекая к себе дополнительного внимания.</li>
<li><b>Пользовательский</b> - данный сценарий никак не влияет на настройки.</li>
</ol>
<b>Внимание!</b> Нажмите кнопку Сохранить, чтобы настройки вступили в силу.', 'evc' ),
			'type'    => 'radio',
			'default' => 'optimal',
			'options' => array(
				'aggresive' => 'Агрессивный / <small>кнопка, подсказка, автооткрытие 3с., звуки</small>',
				'optimal'   => 'Обычный / <small>кнопка, подсказка, автооткрытие 15с., звуки, приветствие</small>',
				'silent'    => 'Незаметный / <small>нет кнопки, автооткрытие 25с., нет звуков</small>',
				'manual'    => 'Пользовательский / <small>не меняет настройки</small>',
			)
		),

	);

	$fields['evc_widget_community_messages_options_section'] = array(
		array(
			'name'    => 'evc_community_messages_expanded',
			'label'   => __( 'Раскрыть виджет', 'evc' ),
			'desc'    => __( 'Управление раскрытием виджета.', 'evc' ),
			'type'    => 'multicheck',
			'options' => array(
				'1' => 'Раскрывать виджет сразу',
			)
		),
		array(
			'name'    => 'evc_community_messages_expand_timeout',
			'desc'    => __( 'Раскрыть виджет через некоторое время в сукундах. Например, <code>15.25</code>.', 'evc' ),
			'default' => '15',
			'type'    => 'text'
		),
		array(
			'name'    => 'evc_community_messages_button_type',
			'label'   => __( 'Кнопка', 'evc' ),
			'desc'    => __( 'Отображение кнопки виджета.', 'evc' ),
			'type'    => 'radio',
			'default' => 'blue_circle',
			'options' => array(
				'blue_circle' => 'Показывать кнопку',
				'no_button'   => 'Не показывать кнопку'
			)
		),
		array(
			'name'    => 'evc_community_messages_widget_position',
			'label'   => __( 'Положение виджета', 'evc' ),
			'desc'    => __( 'Положение виджета и кнопки на странице.', 'evc' ),
			'type'    => 'radio',
			'default' => 'right',
			'options' => array(
				'left'  => 'Слева',
				'right' => 'Справа'
			)
		),
		array(
			'name'    => 'evc_community_messages_disable_button_tooltip',
			'label'   => __( 'Всплывающая подсказка', 'evc' ),
			'desc'    => __( 'Управление всплывающей подсказкой для кнопки виджета.', 'evc' ),
			'type'    => 'multicheck',
			'options' => array(
				'1' => 'Отключить подсказку',
			)
		),
		array(
			'name'    => 'evc_community_messages_tooltip_button_text',
			'desc'    => __( 'Текст всплывающей подсказки.', 'evc' ),
			'type'    => 'text',
			'default' => 'Есть вопрос?'
		),
		array(
			'name'    => 'evc_community_messages_etc',
			'label'   => __( 'Дополнительно', 'evc' ),
			'desc'    => __( 'Дополнительные настройки.', 'evc' ),
			'type'    => 'multicheck',
			'default' => 'welcomeScreen',
			'options' => array(
				'welcomeScreen'           => 'Показывать экран приветствия',
				'disableNewMessagesSound' => 'Отключить звук о новом сообщении',
				'disableExpandChatSound'  => 'Отключить звук при раскрытии виджет',
				'disableTitleChange'      => 'Отключить изменение заголовка страницы, когда приходит новое сообщение'
			)
		),
		array(
			'name'    => 'evc_community_messages_insert_in',
			'label'   => __( 'Показывать виджет', 'evc' ),
			'desc'    => __( 'Вы можете отметить типы страниц на которых будет показан виджет.', 'evc' ),
			'type'    => 'multicheck',
			'default' => array(
				'front_page' => 'front_page',
				'singular'   => 'singular'
			),
			'options' => array(
				'front_page' => __( 'Главная страница, <small>is_front_page()</small>.', 'evc' ),
				'single'     => __( 'Страницы записей, <small>is_single()</small>.', 'evc' ),
				'tax'        => __( 'Страницы таксономии, <small>is_tax()</small>.', 'evc' ),
				'singular'   => __( 'Страницы вложений, записей, страницы (page), <small>is_singular()</small>.', 'evc' )
			)
		)
	);

	return $fields;
}

add_filter( 'evc_widget_fields', 'evc_widget_fields_cm' );


function evc_vk_widget_cm( $element_id = null, $_options = array(), $page_url = '' ) {
	$options  = evc_get_all_options( array( 'evc_autopost', 'evc_widget_community_messages' ) );
	$owner_id = '';

	if ( ! empty( $page_url ) ) {
		$vk_object = evc_get_vk_object( $page_url );

		if ( ! empty( $vk_object['id'] ) ) {
			$owner_id = $vk_object['id'];
		}
	} else {
		if ( ! empty( $options['page_id'] ) ) {
			$owner_id = $options['page_id'];
		}
	}

	if ( empty( $owner_id ) ) {
		return '';
	}

	if ( ! isset( $element_id ) ) {
		$element_id = 'vk-community-messages-' . substr( md5( microtime() ), rand( 0, 26 ), 3 );
	}

	$defaults = array(
		//'shown' => $options['evc_community_messages_shown'], // deprecated

		//'onCanNotWrite' => 'function() {VK.Widgets.CommunityMessages.destroy("'.$element_id.'");}' // (function) — функция, которая будет вызвана, если пользователь по каким-то причинам не может писать сообщения

		//'welcomeScreen' => 0,
		//'expandTimeout' => '3500',
		//'expanded' => 1,
		//'widgetPosition'    => $options['evc_community_messages_widget_position'],
		//'buttonType'        => $options['evc_community_messages_button_type'],
		// blue_circle, no_button
		//'disableButtonTooltip',
		//'tooltipButtonText' => 'Есть вопрос?',
		//'disableNewMessagesSound',
		//'disableExpandChatSound',
		//'disableTitleChange'
	);

	if ( ! empty( $options['evc_community_messages_widget_position'] ) ) {
		$defaults['widgetPosition'] = $options['evc_community_messages_widget_position'];
	}

	if ( ! empty( $options['evc_community_messages_button_type'] ) ) {
		$defaults['buttonType'] = $options['evc_community_messages_button_type'];
	} else {
		$defaults['buttonType'] = 'blue_circle';
	}

	if ( ! empty( $options['evc_community_messages_expanded'] ) ) {
		$defaults['expanded'] = 1;
	}

	if ( ! empty( $options['evc_community_messages_expand_timeout'] ) ) {
		$expand_timeout            = str_replace( ',', '.', $options['evc_community_messages_expand_timeout'] );
		$expand_timeout            = (float) $expand_timeout * 1000;
		$defaults['expandTimeout'] = $expand_timeout;
	}

	if ( ! empty( $options['evc_community_messages_disable_button_tooltip'] ) ) {
		$defaults['disableButtonTooltip'] = 1;
	} else {
		if ( ! empty( $options['evc_community_messages_tooltip_button_text'] ) ) {
			$defaults['tooltipButtonText'] = $options['evc_community_messages_tooltip_button_text'];
		} else {
			$defaults['tooltipButtonText'] = 'Есть вопрос?';
		}
	}

	if ( ! empty( $options['evc_community_messages_etc'] ) ) {
		foreach ( $options['evc_community_messages_etc'] as $etc ) {
			$defaults[ $etc ] = 1;
		}
	}


	$o        = wp_parse_args( $_options, $defaults );
	$o        = evc_vk_widget_data_encode( $o );
	$owner_id = ( $owner_id < 0 ) ? $owner_id * - 1 : $owner_id;

	$out = '
	<script type="text/javascript">
  		VKWidgetsCommunityMessages.push ({
    		element_id: "' . $element_id . '",
            group_id: ' . $owner_id . ',
            options: ' . $o . '
 		});
	</script>';

	$out .= '<div class = "vk_widget_community_messages" id = "' . $element_id . '"></div>';

	return $out;
}


function evc_cm_code() {
	global $post;
	$options = get_option( 'evc_widget_community_messages' );

	if ( ! isset( $options['evc_community_messages'] ) || empty( $options['evc_community_messages'] ) ) {
		return '';
	}

	$insert_in = false;
	if ( isset( $options['evc_community_messages_insert_in'] ) ) {

		foreach ( $options['evc_community_messages_insert_in'] as $key => $value ) {

			if ( call_user_func( 'is_' . $key ) ) {
				$insert_in = true;
				break;
			}
		}
	}

	if ( ! $insert_in ) {
		return '';
	}

	echo evc_vk_widget_cm();

	return '';
}

add_action( 'wp_footer', 'evc_cm_code', 5 );


function evc_community_messages_vk_async_init() {
	?>
	<?php /* <script> */ ?>
	// console.log(VKWidgetsContactUs);
	// Contact Us
	if (typeof VKWidgetsCommunityMessages !== 'undefined') {
	console.log(VKWidgetsCommunityMessages);

	for (index = 0; index < VKWidgetsCommunityMessages.length; ++index) {
	VK.Widgets.CommunityMessages(
	VKWidgetsCommunityMessages[index].element_id,
	VKWidgetsCommunityMessages[index].group_id,
	VKWidgetsCommunityMessages[index].options
	);
	}
	}
	<?php /* </script> */ ?>
	<?php
}

add_action( 'evc_vk_async_init', 'evc_community_messages_vk_async_init' );


function evc_community_messages_js() {

	?>

		$('input[type=radio][name=evc_widget_community_messages\\[evc_community_messages_scenarios\\]]').change(function () {
				if (this.value == 'aggresive') {
					$('#evc_widget_community_messages\\[evc_community_messages_expanded\\]\\[1\\]').attr('checked', false);
					$('#evc_widget_community_messages\\[evc_community_messages_expand_timeout\\]').val('3');

					$('input[name=evc_widget_community_messages\\[evc_community_messages_button_type\\]]').attr('checked', false); // no_button
					$('input[name=evc_widget_community_messages\\[evc_community_messages_button_type\\]][value=blue_circle]').attr('checked', true); // no_button

					//$('input[name=evc_widget_community_messages\\[evc_community_messages_widget_position\\]]').val('right');
					$('#evc_widget_community_messages\\[evc_community_messages_disable_button_tooltip\\]\\[1\\]').attr('checked', false);
					//$('#evc_widget_community_messages\\[evc_community_messages_tooltip_button_text\\]').val('');

					$('#evc_widget_community_messages\\[evc_community_messages_etc\\]\\[welcomeScreen\\]').attr('checked', false);
					$('#evc_widget_community_messages\\[evc_community_messages_etc\\]\\[disableNewMessagesSound\\]').attr('checked', false);
					$('#evc_widget_community_messages\\[evc_community_messages_etc\\]\\[disableExpandChatSound\\]').attr('checked', false);
					$('#evc_widget_community_messages\\[evc_community_messages_etc\\]\\[disableTitleChange\\]').attr('checked', false);

					$('#evc_widget_community_messages\\[evc_community_messages_insert_in\\]\\[front_page\\]').attr('checked', true);
					$('#evc_widget_community_messages\\[evc_community_messages_insert_in\\]\\[single\\]').attr('checked', true);
					$('#evc_widget_community_messages\\[evc_community_messages_insert_in\\]\\[tax\\]').attr('checked', true);
					$('#evc_widget_community_messages\\[evc_community_messages_insert_in\\]\\[singular\\]').attr('checked', true);
				}
				else if (this.value == 'optimal') {

					$('#evc_widget_community_messages\\[evc_community_messages_expanded\\]\\[1\\]').attr('checked', false);
					$('#evc_widget_community_messages\\[evc_community_messages_expand_timeout\\]').val('15');

					$('input[name=evc_widget_community_messages\\[evc_community_messages_button_type\\]]').attr('checked', false); // no_button
					$('input[name=evc_widget_community_messages\\[evc_community_messages_button_type\\]][value=blue_circle]').attr('checked', true); // no_button

					//$('input[name=evc_widget_community_messages\\[evc_community_messages_widget_position\\]]').val('right');
					$('#evc_widget_community_messages\\[evc_community_messages_disable_button_tooltip\\]\\[1\\]').attr('checked', false);
					//$('#evc_widget_community_messages\\[evc_community_messages_tooltip_button_text\\]').val('');

					$('#evc_widget_community_messages\\[evc_community_messages_etc\\]\\[welcomeScreen\\]').attr('checked', true);
					$('#evc_widget_community_messages\\[evc_community_messages_etc\\]\\[disableNewMessagesSound\\]').attr('checked', false);
					$('#evc_widget_community_messages\\[evc_community_messages_etc\\]\\[disableExpandChatSound\\]').attr('checked', false);
					$('#evc_widget_community_messages\\[evc_community_messages_etc\\]\\[disableTitleChange\\]').attr('checked', false);

					$('#evc_widget_community_messages\\[evc_community_messages_insert_in\\]\\[front_page\\]').attr('checked', true);
					$('#evc_widget_community_messages\\[evc_community_messages_insert_in\\]\\[single\\]').attr('checked', false);
					$('#evc_widget_community_messages\\[evc_community_messages_insert_in\\]\\[tax\\]').attr('checked', false);
					$('#evc_widget_community_messages\\[evc_community_messages_insert_in\\]\\[singular\\]').attr('checked', true);
				}
				else if (this.value == 'silent') {

					$('#evc_widget_community_messages\\[evc_community_messages_expanded\\]\\[1\\]').attr('checked', false);
					$('#evc_widget_community_messages\\[evc_community_messages_expand_timeout\\]').val('25');

					$('input[name=evc_widget_community_messages\\[evc_community_messages_button_type\\]]').attr('checked', false); // no_button
					$('input[name=evc_widget_community_messages\\[evc_community_messages_button_type\\]][value=no_button]').attr('checked', true); // no_button
					//$('input[name=evc_widget_community_messages\\[evc_community_messages_widget_position\\]]').val('right');
					$('#evc_widget_community_messages\\[evc_community_messages_disable_button_tooltip\\]\\[1\\]').attr('checked', true);
					//$('#evc_widget_community_messages\\[evc_community_messages_tooltip_button_text\\]').val('');

					$('#evc_widget_community_messages\\[evc_community_messages_etc\\]\\[welcomeScreen\\]').attr('checked', false);
					$('#evc_widget_community_messages\\[evc_community_messages_etc\\]\\[disableNewMessagesSound\\]').attr('checked', true);
					$('#evc_widget_community_messages\\[evc_community_messages_etc\\]\\[disableExpandChatSound\\]').attr('checked', true);
					$('#evc_widget_community_messages\\[evc_community_messages_etc\\]\\[disableTitleChange\\]').attr('checked', true);

					$('#evc_widget_community_messages\\[evc_community_messages_insert_in\\]\\[front_page\\]').attr('checked', true);
					$('#evc_widget_community_messages\\[evc_community_messages_insert_in\\]\\[single\\]').attr('checked', false);
					$('#evc_widget_community_messages\\[evc_community_messages_insert_in\\]\\[tax\\]').attr('checked', false);
					$('#evc_widget_community_messages\\[evc_community_messages_insert_in\\]\\[singular\\]').attr('checked', true);
				}
			}
		);

	<?php
}

add_action( 'evc_widget_settings_page_js', 'evc_community_messages_js' );