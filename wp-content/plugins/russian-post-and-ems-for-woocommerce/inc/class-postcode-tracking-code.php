<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'RPAEFW_Postcode_Tracking_Code_Class' ) ) :

class RPAEFW_Postcode_Tracking_Code_Class extends WC_Email {

	/**
	 * Customer note.
	 *
	 * @var string
	 */
	public $customer_note;
	public $recipientname;
	public $ems_field;

	/**
	 * Constructor.
	 */
	public function __construct() {

		$this->id             = 'rpaefw_postcode_tracking_number';
		$this->customer_email = true;
		$this->title          = 'Трекинг-код';
		$this->description    = 'Письма отправляются клиенту с указанием трек-кода через страницу заказа.';
		$this->subject        = 'Отправлен заказ с сайта {site_title}';
		$this->heading        = 'Ваш заказ был отправлен';

		// Triggers
		add_action( 'rpaefw_tracking_code_send', array( $this, 'trigger' ) );

		// Call parent constructor
		parent::__construct();
	}

	/**
	 * Trigger.
	 *
	 * @param array $args
	 */
	public function trigger( $args ) {

		if ( ! empty( $args ) ) {

			$defaults = array(
				'order_id'      => '',
				'customer_note' => '',
				'ems_field' => true,
			);

			$args = wp_parse_args( $args, $defaults );

			extract( $args );

			if ( $order_id && ( $this->object = wc_get_order( $order_id ) ) ) {
				$this->recipient               = $this->object->billing_email;
				$this->recipientname           = $this->object->billing_first_name;
				$this->customer_note           = $customer_note;
				$this->ems_field               = $ems_field;

				$this->find['order-number']    = '{order_number}';

				$this->replace['order-date']   = date_i18n( wc_date_format(), strtotime( $this->object->order_date ) );
				$this->replace['order-number'] = $this->object->get_order_number();
			} else {
				return;
			}
		}

		if ( ! $this->is_enabled() || ! $this->get_recipient() ) {
			return;
		}

		$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
	}

	/**
	 * Get content html.
	 *
	 * @access public
	 * @return string
	 */
	public function get_content_html() {
		return wc_get_template_html( 'post-tracking-code.php', array(
			'order'         => $this->object,
			'email_heading' => $this->get_heading(),
			'customer_note' => $this->customer_note,
			'recipientname' => $this->recipientname,
			'ems_field'     => $this->ems_field,
			'sent_to_admin' => false,
			'plain_text'    => false,
			'email'			=> $this
		), RPAEFW_PLUG_DIR.'inc/emails/', RPAEFW_PLUG_DIR.'inc/emails/' );
	}

	/**
	 * Get content plain.
	 *
	 * @access public
	 * @return string
	 */
	public function get_content_plain() {
		return wc_get_template_html( 'post-tracking-code.php', array(
			'order'         => $this->object,
			'email_heading' => $this->get_heading(),
			'customer_note' => $this->customer_note,
			'recipientname' => $this->recipientname,
			'ems_field'     => $this->ems_field,
			'sent_to_admin' => false,
			'plain_text'    => true,
			'email'			=> $this
		), RPAEFW_PLUG_DIR.'inc/emails/plain/', RPAEFW_PLUG_DIR.'inc/emails/plain/' );
	}
}

endif;

return new RPAEFW_Postcode_Tracking_Code_Class();
