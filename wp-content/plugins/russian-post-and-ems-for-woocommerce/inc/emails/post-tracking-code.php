<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}



/**
 * @hooked WC_Emails::email_header() Output the email header
 */
do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<p>Здравствуйте <?php echo $recipientname; ?>, ваш заказ был отправлен <?php echo ($ems_field) ? 'EMS' : ''; ?> Почтой России с присвоением следующего трек-номера:</p>

<h3>№: <?php echo $customer_note ?></h3>
<p></p>
<p>Вы можете отслеживать данное почтовое отправление на официальном сайте <?php echo ($ems_field) ? 'EMS' : ''; ?> Почты России. <a href="<?php echo ($ems_field) ? 'http://emspost.ru/ru/tracking/' : 'https://www.pochta.ru/tracking'; ?>">Отследить отправление</a></p>
<br><br>
<p>Для вашего удобства, детали заказа указаны ниже.</p>

<?php

do_action( 'woocommerce_email_order_details', $order, $sent_to_admin, $plain_text, $email );

do_action( 'woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text, $email );

// do_action( 'woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text, $email );

do_action( 'woocommerce_email_footer', $email );
