<?php
/*
Plugin Name: Russian Post and EMS for WooCommerce
Description: The plugin allows you to automatically calculate the shipping cost for "Russian Post" or "EMS"
Version: 0.9
Author: Artem Komarov
Author URI: mailto:yumecommerce@gmail.com
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html
Text Domain: rpaefw-post-calc
Domain Path: /languages
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! defined( 'RPAEFW_PLUG_DIR' ) ) {
    define( 'RPAEFW_PLUG_DIR', plugin_dir_path( __FILE__ ) );
}

if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

	// Register new status
	function rpaefw_register_order_confirmed_order_status() {
	    register_post_status( 'wc-delivering', array(
	        'label' => 'Доставляется',
	        'public' => true,
	        'exclude_from_search' => false,
	        'show_in_admin_all_list' => true,
	        'show_in_admin_status_list' => true,
	        'label_count' => _n_noop( 'Доставляется <span class="count">(%s)</span>', 'Доставляется <span class="count">(%s)</span>' )
	    ) );
	}
	add_action( 'init', 'rpaefw_register_order_confirmed_order_status' );

	// Add to list of WC Order statuses
	function rpaefw_add_order_confirmed_to_order_statuses( $order_statuses ) {
	    $new_order_statuses = array();
	// add new order status after processing
	    foreach ( $order_statuses as $key => $status ) {
	        $new_order_statuses[ $key ] = $status;
	        if ( 'wc-processing' === $key ) {
	            $new_order_statuses['wc-delivering'] = 'Доставляется';
	        }
	    }
	    return $new_order_statuses;
	}
	add_filter( 'wc_order_statuses', 'rpaefw_add_order_confirmed_to_order_statuses' );

	// Add new email class to woocommerce emails tab
	function rpaefw_expedited_woocommerce_email( $email_classes ) {
		$email_classes['RPAEFW_Postcode_Tracking_Code_Class'] = include( 'inc/class-postcode-tracking-code.php' );
		return $email_classes;
	}
	add_filter( 'woocommerce_email_classes', 'rpaefw_expedited_woocommerce_email' );

	// Add trigger action
	function rpaefw_woocommerce_email_add_actions( $actions ){
	    $actions[] = 'rpaefw_tracking_code_send';
	    return $actions;
	}
	add_filter( 'woocommerce_email_actions', 'rpaefw_woocommerce_email_add_actions' );

	// Add meta box
	function rpaefw_add_meta_tracking_code_box() {
	    add_meta_box( 'rpaefw_meta_tracking_code', 'Трек-номер', 'rpaefw_tracking_code', 'shop_order', 'side', 'default' );
	}
	add_action( 'add_meta_boxes', 'rpaefw_add_meta_tracking_code_box' );

	// Add html form to meta box
	function rpaefw_tracking_code() {	    
	    global $post;
	    $post_tracking_number = get_post_meta($post->ID, 'post_tracking_number', true);
	    $ems_tracking_number = get_post_meta($post->ID, 'ems_tracking_number', true);

	    echo '<p><label for="rpaefw_postcode_tracking_provider" style="width: 50px; display: inline-block;">Почта:</label>';
        echo '<input type="text" id="rpaefw_postcode_tracking_provider" name="rpaefw_postcode_tracking_provider" value="' . $post_tracking_number . '"/></p>';
        echo '<p><label for="rpaefw_ems_tracking_provider" style="width: 50px; display: inline-block;">EMS:</label>';
        echo '<input type="text" id="rpaefw_ems_tracking_provider" name="rpaefw_ems_tracking_provider" value="' . $ems_tracking_number . '"/></p>';
        echo '<p><input type="submit" class="add_note button" name="save" value="Сохранить и Отправить"></p>';

	}
	add_action('woocommerce_process_shop_order_meta', 'rpaefw_save_tracking_code', 0, 2);

	// Save new meta and sent email
	function rpaefw_save_tracking_code($post_id) {

		if ( $_POST['save'] != 'Сохранить и Отправить' )
		{
			return;
		}
		if ( $_POST['rpaefw_postcode_tracking_provider'] != '' || $_POST['rpaefw_ems_tracking_provider'] != '' )
		{
			$post_tracking_number = sanitize_text_field($_POST['rpaefw_postcode_tracking_provider']);
			update_post_meta($post_id, 'post_tracking_number', $post_tracking_number);

			$ems_tracking_number = sanitize_text_field($_POST['rpaefw_ems_tracking_provider']);
			update_post_meta($post_id, 'ems_tracking_number', $ems_tracking_number);

			$ems_tracking_field = true;

			if ( $_POST['rpaefw_ems_tracking_provider'] == '' )
			{
				$ems_tracking_field = false;
			}

			$comment_post_ID        = $post_id;
			$comment_author_url     = '';
			$comment_content        = 'Email с трек-номером: ' . $post_tracking_number . $ems_tracking_number . ', был отправлен клиенту';
			$comment_agent          = 'WooCommerce';
			$comment_type           = 'order_note';
			$comment_parent         = 0;
			$comment_approved       = 1;
			$commentdata            = apply_filters( 'woocommerce_new_order_note_data', compact( 'comment_post_ID', 'comment_author', 'comment_author_email', 'comment_author_url', 'comment_content', 'comment_agent', 'comment_type', 'comment_parent', 'comment_approved' ), array( 'order_id' => $post_id, 'is_customer_note' => 0 ) );
			wp_insert_comment( $commentdata );

			WC()->mailer();
			do_action('rpaefw_tracking_code_send', array( 'order_id' => $post_id, 'customer_note' => $post_tracking_number . $ems_tracking_number, 'ems_field' =>  $ems_tracking_field));
		} 
	}
	
	function rpaefw_post_calc_shipping_method_init() {
		if ( ! class_exists( 'WC_Ru_Post_Calc_Method' ) ) {
			class WC_RPAEFW_Post_Calc_Method extends WC_Shipping_Method {
			
				public function __construct( $instance_id = 0 ) {
					$this->id                    = 'rpaefw_post_calc';
					$this->instance_id           = absint( $instance_id );
					$this->method_title          = __( 'Russian Post', 'rpaefw-post-calc' );
					$this->method_description    = __( 'The plugin allows you to automatically calculate shipping costs "Russian Post" or "EMS" on the checkout page using postcalc.ru service.', 'rpaefw-post-calc' );
					$this->supports              = array(
						'shipping-zones',
						'instance-settings'
					);

			    	$this->instance_form_fields  = array(
		        		'title' => array(
		        			'title' 		=> __( 'Title', 'rpaefw-post-calc' ),
		        			'type' 			=> 'text',
		        			'default'		=> __( 'Russian Post', 'rpaefw-post-calc' ),
		        		),
		        		'from' => array(
		        			'title' 		=> __( 'Оrigin Postcode', 'rpaefw-post-calc' ),
		        			'description' 	=> __( '6-digit code of the sender.', 'rpaefw-post-calc' ),
		        			'type' 			=> 'number',
		        		),
		        		'addcost' => array(
		        			'title' 		=> __( 'Сost', 'rpaefw-post-calc' ),
		        			'description' 	=> __( 'Additional flat rate for shipping method. This may be the average value of the package or the cost of fuel, spent on the road to the post;)', 'rpaefw-post-calc' ),
		        			'type' 			=> 'number',
		        			'default'		=> 0,
		        		),
		        		'type' => array(
		        			'title' 		=> __( 'Type', 'rpaefw-post-calc' ),
		        			'type' 			=> 'select',
		        			'default' 		=> 'ЦеннаяПосылка',
		        			'options'		=> array(
		        				'ПростаяБандероль'           => __( 'Simple wrapper', 'rpaefw-post-calc' ),
		        				'ЗаказнаяБандероль'          => __( 'Custom wrapper', 'rpaefw-post-calc' ),
		        				'ЗаказнаяБандероль1Класс'    => __( 'Custom wrapper 1 class', 'rpaefw-post-calc' ),
		        				'ЦеннаяБандероль'            => __( 'Valued wrapper', 'rpaefw-post-calc' ),
		        				'ПростаяПосылка'             => __( 'Simple Parcel', 'rpaefw-post-calc' ),
		        				'ЦеннаяПосылка'              => __( 'Valued parcel', 'rpaefw-post-calc' ),
		        				'ЦеннаяАвиаБандероль'        => __( 'Valued avia wrapper', 'rpaefw-post-calc' ),
		        				'ЦеннаяАвиаПосылка'          => __( 'Valued avia parcel', 'rpaefw-post-calc' ),
		        				'ЦеннаяБандероль1Класс'      => __( 'Valued wrapper 1 class', 'rpaefw-post-calc' ),
		        				'EMS'                        => __( 'EMS', 'rpaefw-post-calc' ),

		        				'МждМешокМ'                  =>	'Международный мешок М',
		        				'МждМешокМАвиа'              =>	'Международный мешок М авиа',
		        				'МждМешокМЗаказной'          =>	'Международный мешок М заказной',
		        				'МждМешокМАвиаЗаказной'      =>	'Международный мешок М авиа заказной',
		        				'МждБандероль'               => 'Международная бандероль',
		        				'МждБандерольАвиа'           => 'Международная авиабандероль',
		        				'МждБандерольЗаказная'       => 'Международная бандероль заказная',
		        				'МждБандерольАвиаЗаказная'   => 'Международная авиабандероль заказная',
		        				'МждМелкийПакет' 		     =>	'Международный мелкий пакет',
		        				'МждМелкийПакетАвиа'         =>	'Международный мелкий пакет авиа',
		        				'МждМелкийПакетЗаказной'     =>	'Международный мелкий пакет заказной',
		        				'МждМелкийПакетАвиаЗаказной' =>	'Международный мелкий пакет авиа заказной',
		        				'МждПосылка'                 => 'Международная посылка',
		        				//'МждПосылкаАвиа'             => 'Международная авиапосылка',
		        				'EMS_МждДокументы'           => 'ЕMS международное - документы',
		        				'EMS_МждТовары'              =>	'ЕMS международное - товары',
		        			)
		        		),

		        		'fixedpackvalue' => array(
		        			'title' 		=> __( 'Max. Fixed Package Value', 'rpaefw-post-calc' ),
		        			'description' 	=> __( 'You can set max. fixed value for some types of departure. By default value equals sum of the order.', 'rpaefw-post-calc'),
		        			'type' 			=> 'number',
		        		),

		        		// Packaging
		        		'addpackweight' => array(
		        			'title' 		=> __( 'Packaging', 'rpaefw-post-calc' ),
		        			'description' 	=> __( 'Weight of the one packaging in grams. This weight will be added to the total weight of the order.', 'rpaefw-post-calc'),
		        			'type' 			=> 'number',
		        			'default'		=> 0,
		        		),
		        		'addpackcost' => array(
		        			'description' 	=> __( 'Cost of the one packaging. This cost will be added to the final amount of delivery.', 'rpaefw-post-calc'),
		        			'type' 			=> 'number',
		        			'default'		=> 0,
		        		),

		        		'fixedvalue_disable' => array(
		        			'title' 		=> __( 'Min. cost of order', 'rpaefw-post-calc' ),
		        			'description' 	=> __( 'Disable this method if the cost of the order is less than inputted value. Leave this field empty to allow any order cost.', 'rpaefw-post-calc'),
		        			'type' 			=> 'number',
		        		),

		        		'overweight_disable' => array(
		        			'title' 		=> __( 'Do not allow overweight', 'rpaefw-post-calc' ),
		        			'type' 			=> 'checkbox',
		        			'label' 		=> __( 'Disable this method in case of overweight.', 'rpaefw-post-calc' ),
		        			'description' 	=> __( 'Hide this method if package weight is heavier than the allowed weight for a chosen type of departure. By default, if there is overweight the package will be split into two or more packages.', 'rpaefw-post-calc' ),
		        			'default' 		=> 'no',
		        		),

		        		// Message options
		        		'overweight' => array(
		        			'title' 		=> __( 'Notice of overweight', 'rpaefw-post-calc' ),
		        			'type' 			=> 'checkbox',
		        			'label' 		=> __( 'Show a notice about overweight.', 'rpaefw-post-calc' ),
		        			'description' 	=> __( 'Show a message to the customer if the total weight of the order exceeds the permitted weight for specific type of delivery', 'rpaefw-post-calc' ),
		        			'default' 		=> 'no',
		        		),
		        		'overweighttext' => array(
		        			'type' 			=> 'textarea',
		        			'default'		=> 'превышен максимально допустимый вес. В случае выбора данного метода Ваш заказ будет разбит на несколько отправлений.',
		        		),

		        		'time' => array(
		        			'title' 		=> __( 'Delivery Time', 'rpaefw-post-calc' ),
		        			'type' 			=> 'checkbox',
		        			'label' 		=> __( 'Show time of delivery.', 'rpaefw-post-calc' ),
		        			'description' 	=> __( 'Displayed next to the title. For international shipments, it works only for EMS - international.', 'rpaefw-post-calc' ),
		        			'default' 		=> 'no',
		        		),
					);
				    
				    $this->title   		  = $this->get_option( 'title' );
				    $this->from      	  = $this->get_option( 'from' );
				    $this->addcost 		  = $this->get_option( 'addcost' );
				    $this->type			  = $this->get_option( 'type' );
				    $this->addpackweight  = $this->get_option( 'addpackweight' );
				    $this->addpackcost	  = $this->get_option( 'addpackcost' );
				    $this->overweight 	  = $this->get_option( 'overweight' );
				    $this->overweighttext = $this->get_option( 'overweighttext' );
				    $this->time 		  = $this->get_option( 'time' );
				    $this->fixedpackvalue = $this->get_option( 'fixedpackvalue' );
				    $this->overweight_disable = $this->get_option( 'overweight_disable' );
				    $this->fixedvalue_disable = $this->get_option( 'fixedvalue_disable' );
	
					add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );

				}

				// check if curl is on
				public function _isCurl(){
				    return function_exists('curl_version');
				}

				/**
				 * calculate_shipping function.
				 * @param array $package (default: array())
				 */
				public function calculate_shipping( $package = array() ) {

					$from_code = $this->from;

					// origin Postcode must be set
					if ( $from_code == '' )  {
						return;
					}

					global $woocommerce;

					$plugin_sw = 'rpaefw_post_calc_0.9';
					$to_city = '';
					$to_country = 'RU';
					
					$country_code = $package['destination']['country'];
					$postal_code = $package['destination']['postcode'];
					$city = $package['destination']['city'];

					// if country field is not empty 
					// for future update
					if ( $country_code != '' ) {
						$to_country = $country_code;
					}

					// validate poscode
					if ($postal_code != '' && strlen($postal_code) < 6 && $to_country == 'RU') {
						return;
					}

					// if postal code is empty take city
					if ( $postal_code != '' ) {
						$to_city = $postal_code;
					} else {
						$to_city = $city;
					}
					
					$admin_email = get_option('admin_email');
					$site_url    = get_option('siteurl');
					$site_url    = implode('/', array_slice(explode('/', preg_replace('/https?:\/\/|www./', '', $site_url)), 0, 1));
					
					$packweight  = $this->addpackweight;

					// get weight of the cart
					// normalise weights, unify to g  
					$weight      = $woocommerce->cart->cart_contents_weight;
					$weight      = wc_get_weight( $weight, 'g' );

					// plus pack weight
					$weight = $weight + $packweight;

					// get total value
					$totalval = $package['contents_cost'];
					
					if ( !$to_city || $to_city == '' ) {
						return;
					}
						
					// additional cost
					$addcost = intval($this->addcost);
					$addpackcost = intval($this->addpackcost);

					// get type of delivery
					$type = $this->type;

					// max weight of type 2kg
					$banderol = array( 'ПростаяБандероль', 'ЗаказнаяБандероль', 'ЗаказнаяБандероль1Класс', 'ЦеннаяБандероль', 'ЦеннаяБандероль1Класс' );
					// max weight of intl type 2kg
					$paket_intl = array( 'МждМелкийПакет', 'МждМелкийПакетАвиа', 'МждМелкийПакетЗаказной', 'МждМелкийПакетАвиаЗаказной' );
					// max weight of intl type 5kg
					$banderol_intl = array( 'МждБандероль', 'МждБандерольАвиа', 'МждБандерольЗаказная', 'МждБандерольАвиаЗаказная' );
					// max weight of intl type 14.5kg
					$meshok_intl = array( 'МждМешокМ', 'МждМешокМАвиа', 'МждМешокМЗаказной', 'МждМешокМАвиаЗаказной' );
					// max weight of type 20kg
					$posilka = array( 'ПростаяПосылка', 'ЦеннаяПосылка', 'ЦеннаяАвиаПосылка' );
					// max weight of type 31.5kg
					$emstype = array( 'EMS' );

					// by default only one shipping
					$kolotprav = 1;

					// check if order exceeds limited of post 
					if ( in_array($type, $banderol) && $weight >= '2000' ) 
					{
						$kolotprav = intval($weight / 2000 + 1);
					}
					elseif ( in_array($type, $paket_intl) && $weight >= '2000' ) 
					{
						$kolotprav = intval($weight / 2000 + 1);
					}
					elseif ( in_array($type, $banderol_intl) && $weight >= '5000' ) 
					{
						$kolotprav = intval($weight / 5000 + 1);
					}
					elseif ( in_array($type, $meshok_intl) && $weight >= '14500' ) 
					{
						$kolotprav = intval($weight / 14500 + 1);
					}
					elseif ( in_array($type, $posilka) && $weight >= '20000' )
					{
						$kolotprav = intval($weight / 20000 + 1);
					} 
					elseif ( in_array($type, $emstype) && $weight >= '31500' )
					{
						$kolotprav = intval($weight / 31500 + 1);
					}

					// count new weight
					if ( $kolotprav > 1 ) {
						// if overweight is not allowed
						if ($this->overweight_disable != 'no') {
							return;
						}

						$weight = $weight + ( $packweight * $kolotprav ) - $packweight;

						// add notice if yes and there is an overweight
						if ( $this->overweight == 'yes' ) {
							add_filter( "woocommerce_before_checkout_form", 
								function () {
									return wc_print_notice(
										__( 'For the method', 'rpaefw-post-calc') 
										. ' "' . $this->title . '" ' . $this->overweighttext, 'notice' ); 
								}
							);
						}	
					}

					// if cost is less than provided in options
					if ($this->fixedvalue_disable != '' && $this->fixedvalue_disable > 0) {
						if ($totalval < $this->fixedvalue_disable) {
							return;
						}
					}

					// check weight and set minimum value for Russian Post if it is less than required
					if ($weight < 100) {
						$weight = 100;
					}

					// fixed value 
					$fixedvalue = intval($this->fixedpackvalue);
					if ($fixedvalue != '' && $fixedvalue > 0 && $totalval > $fixedvalue) {
						$totalval = $fixedvalue;
					}
					
					// data
					$From      = $from_code;
					$To        = $to_city;
					$Weight    = $weight;
					$Valuation = $totalval;
					$Country   = $to_country;
					$Site      = $site_url;
					$Email     = $admin_email;
					 
					// a query with all the variables
					$QueryString  = "f=" .rawurlencode($From);
					$QueryString .= "&t=" .rawurlencode($To);
					$QueryString .= "&w=$Weight&v=$Valuation&c=$Country&o=php&cs=utf-8";
					$QueryString .= "&st=$Site&ml=$Email";
					$QueryString .= "&sw=" . $plugin_sw;
					$QueryString .= "&r=100"; // round
					 
					// the request URL
					$Request = "http://api.postcalc.ru/?$QueryString";
					 
					// a query option
					$arrOptions = array('http' =>
						array( 'header'  => 'Accept-Encoding: gzip','timeout' => 5, 'user_agent' => phpversion() )
					);

					// connecting to the server
					if ($this->_isCurl()) {
						$curl = curl_init();

						curl_setopt_array($curl, array(
						    CURLOPT_RETURNTRANSFER => 1,
						    CURLOPT_URL => $Request,
						    CURLOPT_USERAGENT => phpversion()
						));

						if (!curl_exec($curl)) {
							return;
						} else {
							$Response = curl_exec($curl);
						}

						curl_close($curl);

					} else {
						$Response = file_get_contents($Request, false , stream_context_create($arrOptions));
					}
					 
					if ( !$Response ) {
						return;
					}
						
					// unpack the answer
					if ( substr($Response,0,3) == "\x1f\x8b\x08" ) {
						$Response=gzinflate(substr($Response,10,-8));
					}  
					 
					// switch into a PHP array
					$arrResponse = unserialize($Response);

					// create new type based on choose
					if ( $type == 'ПростаяПосылка' ) {
						$new_type = 'ЦеннаяПосылка';
					} else {
						$new_type = $type;
					}
					
					if ( $arrResponse['Отправления'][$new_type]['Доставка'] != '0' )  {				
						$finalshippingcost = $arrResponse['Отправления'][$new_type]['Доставка'];
						
						// if simple parcel take only tariff cost
						if ( $type == 'ПростаяПосылка' ) {
							$finalshippingcost = $arrResponse['Отправления'][$new_type]['Тариф'];
						}

						// show time delivery if yes
						$time = '';
						$deliverytime = $arrResponse['Отправления'][$new_type]['СрокДоставки'];
						if ( $this->time === 'yes' && $deliverytime ) {
							if ( strpos($deliverytime, "-") !== false ) {
								$deliverytimeday = explode("-", $deliverytime);
								$deliverytime = $deliverytimeday[1];
							}
							$time = rpaefw_decl_num($deliverytime, array('день', 'дня', 'дней'));
						}

						// shipping cost
						// plus packages cost 
						// plus additional cost
						$cost = intval($finalshippingcost + $addcost + $addpackcost * $kolotprav);

						$this->add_rate( array(
							'id'    => $this->get_rate_id(),
							'label' => $this->title . $time,
							'cost'  => $cost,
						) );
					}
					
				}
			}
		}
	}

	add_action( 'woocommerce_shipping_init', 'rpaefw_post_calc_shipping_method_init' );

	// склонение дней для срока доставки
	function rpaefw_decl_num($number, $titles) {
	    $cases = array(2, 0, 1, 1, 1, 2);
	    return " (" . $number." ".$titles[ ($number%100 > 4 && $number %100 < 20) ? 2 : $cases[min($number%10, 5)] ] . ")";
	}

	function rpaefw_add_html_credits() {
		echo '<!-- ' . __( 'For the calculation of delivery used', 'rpaefw-post-calc' ) . ' www.postcalc.ru -->';
	}
	add_action( 'woocommerce_before_cart_totals', 'rpaefw_add_html_credits' );
	//add_action( 'woocommerce_review_order_before_payment', 'rpaefw_add_html_credits' );
	

	// add new shipping method
	function rpaefw_add_post_calc_shipping_method( $methods ) {
		$methods[ 'rpaefw_post_calc' ] = 'WC_RPAEFW_Post_Calc_Method';
		return $methods;
	}
	add_filter( 'woocommerce_shipping_methods', 'rpaefw_add_post_calc_shipping_method' );

} // if woocommerce is active

// load plugin textdomain.
add_action( 'plugins_loaded', 'rpaefw_load_textdomain' );
function rpaefw_load_textdomain() {
	load_plugin_textdomain( 'rpaefw-post-calc', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' ); 
}

add_action( 'wp_print_scripts', 'rpaefw_add_custom_order_status_icon' );
function rpaefw_add_custom_order_status_icon() {
	
	if ( !is_admin() ) { 
		return; 
	}
	?>
	<style>
		/* Add custom status order icons */
		.column-order_status mark.delivering {
			position: relative;
		}

		.column-order_status mark.delivering:before {
			content: "\f230";
			position: absolute;
			color: #00b9eb;
			display: inline-block;
			width: 20px;
			height: 20px;
			font-size: 24px;
			left: 0;
			right: 0;
			top: 0;
			text-indent: 0;
			bottom: 0;
			line-height: 1;
			font-family: dashicons;
		} 
	</style>
	<?php
}