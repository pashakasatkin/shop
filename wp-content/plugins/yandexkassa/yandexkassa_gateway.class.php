<?php

if (!defined('ABSPATH')) exit;
if (!class_exists('WC_Payment_Gateway')) return;

class WC_yamoney_Gateway extends WC_Payment_Gateway
{
    protected $long_name;
    protected $payment_type;
    private $order;

    public function __construct()
    {
        $this->has_fields = false;
        $this->init_form_fields();
        $this->init_settings();
        $this->title = $this->settings['title'];
        $this->description = $this->settings['description'];
        $this->liveurl = '';
        $this->msg['message'] = "";
        $this->msg['class'] = "";

        if (version_compare(WOOCOMMERCE_VERSION, '2.0.0', '>=')) {
            add_action('woocommerce_update_options_payment_gateways_' . $this->id, array(&$this, 'process_admin_options'));
        } else {
            add_action('woocommerce_update_options_payment_gateways', array(&$this, 'process_admin_options'));
        }
        add_action('woocommerce_receipt_' . $this->id, array(&$this, 'receipt_page'));
    }

    function init_form_fields()
    {

        $this->form_fields = array(
            'enabled' => array(
                'title' => __('Включить/Выключить', 'yandex_money'),
                'type' => 'checkbox',
                'label' => $this->long_name,
                'default' => 'no'),
            'title' => array(
                'title' => __('Заголовок', 'yandex_money'),
                'type' => 'text',
                'description' => __('Название, которое пользователь видит во время оплаты', 'yandex_money'),
                'default' => $this->method_title),
            'description' => array(
                'title' => __('Описание', 'yandex_money'),
                'type' => 'textarea',
                'description' => __('Описание, которое пользователь видит во время оплаты', 'yandex_money'),
                'default' => $this->long_name)
        );
    }

    public function admin_options()
    {
        echo '<h3>' . $this->long_name . '</h3>';
        echo '<h5>' . __('Для работы с модулем необходимо <a href="https://money.yandex.ru/joinups/">подключить магазин к Яндек.Кассе</a>. После подключения вы получите параметры для приема платежей (идентификатор магазина — shopId и номер витрины — scid).', 'yandex_money') . '</h5>';
        echo '<table class="form-table">';
        // Generate the HTML For the settings form.
        $this->generate_settings_html();
        echo '</table>';

    }

    /**
     *  There are no payment fields for payu, but we want to show the description if set.
     **/
    function payment_fields()
    {
        if ($this->description) echo wpautop(wptexturize($this->description));
    }

    /**
     * Receipt Page
     **/
    function receipt_page($order)
    {
        echo $this->generate_payu_form($order);
    }

    protected function get_success_fail_url($name)
    {
        switch (get_option($name)) {
            case "wc_success":
                return $this->order->get_checkout_order_received_url();
                break;
            case "wc_checkout":
                return $this->order->get_view_order_url();
                break;
            case "wc_payment":
                return $this->order->get_checkout_payment_url();
                break;
            default:
                return get_page_link(get_option($name));
                break;
        }
    }

    protected function get_fail_url()
    {

    }

    /**
     * Generate payu button link
     **/
    public function generate_payu_form($order_id)
    {

        global $woocommerce;
        $this->order = new WC_Order($order_id);
        $order = $this->order;
        if (version_compare($woocommerce->version, "3.0", ">=")) {
            $billing_first_name = $this->order->get_billing_first_name();
            $billing_last_name = $this->order->get_billing_last_name();
            $billing_phone = $this->order->get_billing_phone();
            $billing_email = $this->order->get_billing_email();
            $order_total = $order->get_total();
        } else {
            $billing_first_name = $this->order->billing_first_name;
            $billing_last_name = $this->order->billing_last_name;
            $billing_phone = $this->order->billing_phone;
            $billing_email = $this->order->billing_email;
            $order_total = number_format($order->order_total, 2, '.', '');
        }
        $txnid = $order_id;
        $sendurl = get_option('ym_Demo') == '1' ? 'https://demomoney.yandex.ru/eshop.xml' : 'https://money.yandex.ru/eshop.xml';
        $result = '';
        $result .= '<form name=ShopForm method="POST" id="submit_' . $this->id . '_payment_form" action="' . $sendurl . '">';
        $result .= '<input type="hidden" name="firstname" value="' . $billing_first_name . '">';
        $result .= '<input type="hidden" name="lastname" value="' . $billing_last_name . '">';
        $result .= '<input type="hidden" name="scid" value="' . get_option('ym_Scid') . '">';
        $result .= '<input type="hidden" name="shopId" value="' . get_option('ym_ShopID') . '"> ';
        $result .= '<input type="hidden" name="shopSuccessUrl" value="' . $this->get_success_fail_url('ym_success') . '"> ';
        $result .= '<input type="hidden" name="shopFailUrl" value="' . $this->get_success_fail_url('ym_fail') . '"> ';
        $result .= '<input type="hidden" name="CustomerNumber" value="' . $txnid . '" size="43">';
        if ($billing_phone) $result .= '<input name="cps_phone" type="hidden" value="' . $billing_phone . '">';
        if ($billing_email) $result .= '<input name="cps_email" type="hidden" value="' . $billing_email . '">';
        if ($this->isReceiptEnabled()) {
            $result .= '<input name="ym_merchant_receipt" type="hidden" value="' . $this->getReceiptJson($order) . '">';
        }
        $result .= '<input type="hidden" name="sum" value="' . $order_total . '">';
        $result .= '<input name="paymentType" value="' . $this->payment_type . '" type="hidden">';
        $result .= '<input name="cms_name" type="hidden" value="wp-woocommerce">';
        $result .= '<input type="submit" value="Оплатить">';
        $result .= '<script type="text/javascript">';
        $result .= 'jQuery(document).ready(function ($){ jQuery("#submit_' . $this->id . '_payment_form").submit(); });';
        $result .= '</script></form>';
        $woocommerce->cart->empty_cart();
        return $result;
    }

    /**
     * Process the payment and return the result
     **/
    function process_payment($order_id)
    {
        $order = new WC_Order($order_id);
        return array('result' => 'success', 'redirect' => $order->get_checkout_payment_url(true));
    }

    function showMessage($content)
    {
        return '<div class="box ' . $this->msg['class'] . '-box">' . $this->msg['message'] . '</div>' . $content;
    }

    // get all pages
    function get_pages($title = false, $indent = true)
    {
        $wp_pages = get_pages('sort_column=menu_order');
        $page_list = array();
        if ($title) $page_list[] = $title;
        foreach ($wp_pages as $page) {
            $prefix = '';
            // show indented child pages?
            if ($indent) {
                $has_parent = $page->post_parent;
                while ($has_parent) {
                    $prefix .= ' - ';
                    $next_page = get_page($has_parent);
                    $has_parent = $next_page->post_parent;
                }
            }
            // add to page list array array
            $page_list[$page->ID] = $prefix . $page->post_title;
        }
        return $page_list;
    }

    /**
     * @return bool
     */
    private function isReceiptEnabled()
    {
        $taxRatesRelations = get_option('ym_tax_rate');
        $defaultTaxRate = get_option('ym_default_tax_rate');

        return get_option('ym_enable_receipt') && ($taxRatesRelations || $defaultTaxRate);
    }

    /**
     * @param WC_Order $order
     * @return string
     */
    private function getReceiptJson($order)
    {
        global $woocommerce;
        require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'YandexMoneyReceipt.php';

        $defaultTaxRate = (int)get_option('ym_default_tax_rate');

        if (version_compare($woocommerce->version, "3.0", ">=")) {
            $data = $order->get_data();
            if ($order->get_billing_email()) {
                $customerContact = $order->get_billing_email();
            } else if ($order->get_billing_phone()) {
                $customerContact = $order->get_billing_phone();
            }
            $currency = $data['currency'];

            $receipt = new YandexMoneyReceipt($defaultTaxRate, $currency);
            $receipt->setCustomerContact($customerContact);

            $items = $order->get_items();
            $shipping = $data['shipping_lines'];
            $hasShipping = (bool)count($shipping);
            foreach ($items as $item) {
                $amount = $item->get_total() / $item->get_quantity() + $item->get_total_tax() / $item->get_quantity();
                $amount = round($amount, 2);
                $quantity = $item->get_quantity();
                $taxes = $item->get_taxes();
                $tax = $this->getYmTaxRate($taxes);
                $receipt->addItem($item['name'], $amount, $quantity, $tax);
            }

            if ($hasShipping) {
                $shippingData = array_shift($shipping);
                $amount = $shippingData['total'] + $shippingData['total_tax'];
                $amount = round($amount, 2);
                $taxes = $shippingData->get_taxes();
                $tax = $this->getYmTaxRate($taxes);
                $receipt->addShipping('Доставка', $amount, $tax);
            }

            $result = $receipt
                ->normalize($order->get_total())
                ->getJson();
        } else {
            if ($order->billing_email) {
                $customerContact = $order->billing_email;
            } else if ($order->billing_phone) {
                $customerContact = $order->billing_phone;
            }
            $currency = $order->get_order_currency();
            $receipt = new YandexMoneyReceipt($defaultTaxRate, $currency);
            $receipt->setCustomerContact($customerContact);
            $items = $order->get_items();
            $shipping = $order->get_items('shipping');
            $hasShipping = (bool)count($shipping);
            $orderTotal = number_format($order->order_total, 2, '.', '');
            foreach ($items as $itemId => $item) {
                $taxes = $order->get_item_meta($itemId, '_line_tax_data', true);
                $quantity = $order->get_item_meta($itemId, '_qty', true);
                $itemTotal = $order->get_item_meta($itemId, '_line_total', true);
                $tax = $order->get_item_meta($itemId, '_line_tax', true);
                $amount = $itemTotal / $quantity + $tax / $quantity;
                $amount = round($amount, 2);
                $this->getYmTaxRate($taxes);
                $taxRate = $this->getYmTaxRate($taxes);
                $receipt->addItem($item['name'], $amount, $quantity, $taxRate);
            }

            if ($hasShipping) {
                $itemId = key($shipping);
                $taxes = $order->get_item_meta($itemId, 'taxes', true);
                $amount = $order->get_total_shipping() + $order->get_shipping_tax();
                $amount = round($amount, 2);
                $tax = $this->getYmTaxRate(array('total' => $taxes));
                $receipt->addShipping('Доставка', $amount, $tax);
            }

            $result = $receipt
                ->normalize($orderTotal)
                ->getJson();
        }

        return htmlspecialchars($result);
    }

    /**
     * @param $taxes
     * @return int
     */
    private function getYmTaxRate($taxes)
    {
        $taxRatesRelations = get_option('ym_tax_rate');
        $defaultTaxRate = (int)get_option('ym_default_tax_rate');

        if ($taxRatesRelations) {
            $taxesSubtotal = $taxes['total'];
            if ($taxesSubtotal) {
                $wcTaxIds = array_keys($taxesSubtotal);
                $wcTaxId = $wcTaxIds[0];
                if (isset($taxRatesRelations[$wcTaxId])) {
                    return (int)$taxRatesRelations[$wcTaxId];
                }
            }
        }

        return $defaultTaxRate;
    }
}

class WC_yamoney_mpos_Gateway extends WC_yamoney_Gateway
{
    public function __construct()
    {
        parent::__construct();
    }

    public function generate_payu_form($order_id)
    {
        global $woocommerce;
        $order = new WC_Order($order_id);
        $txnid = $order_id;
        $result = '';
        $result .= '<form name=ShopForm method="POST" id="submit_' . $this->id . '_payment_form" action="' . get_page_link(get_option('ym_page_mpos')) . '">';
        $result .= '<input type="hidden" name="CustomerNumber" value="' . $txnid . '" size="43">';
        $result .= '<input type="hidden" name="Sum" value="' . number_format($order->order_total, 2, '.', '') . '" size="43">';
        $result .= '<input name="paymentType" value="' . $this->payment_type . '" type="hidden">';
        $result .= '<input name="cms_name" type="hidden" value="wp-woocommerce">';
        $result .= '<input type="submit" value="Перейти к инcтрукции по оплате">';
        $result .= '</form>';
        $woocommerce->cart->empty_cart();
        return $result;
    }
}

class WC_yamoney_smartpay_Gateway extends WC_yamoney_Gateway
{
    public function __construct()
    {
        parent::__construct();
    }

    public function admin_options()
    {
        echo '<h3>' . $this->long_name . '</h3>';
        echo '<table class="form-table">';
        // Generate the HTML For the settings form.
        $this->generate_settings_html();
        echo '</table>';
    }
}

?>