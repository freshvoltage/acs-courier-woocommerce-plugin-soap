<?php
/*
Plugin Name: ACS Shipping
Plugin URI:
Description: Address Validation, Price Calculation, Vouchers, Tracking, Receipt List
Version: 1.0.20
Author: Fresh Voltage
Author URI: https://freshvoltage.com/
Text Domain: acs-shipping
*/
class WC_ACS_Shipping_Method extends WC_Shipping_Method

	{

	public function __construct($instance_id = 0)
		{
		$this->id = 'acs_courier';
		$this->instance_id = absint($instance_id);
		$this->method_title = __('ACS Courier', 'woocommerce');
		$this->method_description = __('Address Validation, Price Calculation, Vouchers, Tracking, Receipt List', 'woocommerce');
		$this->supports = array(
			'settings',
			'shipping-zones',
			'instance-settings',
			'instance-settings-modal',
		);
		$this->instance_form_fields = array(
			'weight-min1' => array(
				'title' => __('Βάρος (Kg) από', 'woocommerce') ,
				'label' => __('', 'woocommerce') ,
				'type' => 'text',
				'description' => '',
				'default' => '0.000'
			) ,
			'weight-max1' => array(
				'title' => __('Βάρος (Kg) έως', 'woocommerce') ,
				'label' => __('', 'woocommerce') ,
				'type' => 'text',
				'description' => '',
				'default' => '3.050'
			) ,
			'customerId1' => array(
				'title' => __('Κωδικός χρέωσης', 'woocommerce') ,
				'label' => __('', 'woocommerce') ,
				'type' => 'text',
				'description' => '',
				'default' => '2ΑΘ999999'
			) ,
			'weight-min2' => array(
				'title' => __('Βάρος (Kg) από', 'woocommerce') ,
				'label' => __('', 'woocommerce') ,
				'type' => 'text',
				'description' => '',
				'default' => '3.051'
			) ,
			'weight-max2' => array(
				'title' => __('Βάρος (Kg) έως', 'woocommerce') ,
				'label' => __('', 'woocommerce') ,
				'type' => 'text',
				'description' => '',
				'default' => '6.050'
			) ,
			'customerId2' => array(
				'title' => __('Κωδικός χρέωσης', 'woocommerce') ,
				'label' => __('', 'woocommerce') ,
				'type' => 'text',
				'description' => '',
				'default' => '2ΑΘ999999'
			) ,
			'weight-min3' => array(
				'title' => __('Βάρος (Kg) από', 'woocommerce') ,
				'label' => __('', 'woocommerce') ,
				'type' => 'text',
				'description' => '',
				'default' => '6.051'
			) ,
			'weight-max3' => array(
				'title' => __('Βάρος (Kg) έως', 'woocommerce') ,
				'label' => __('', 'woocommerce') ,
				'type' => 'text',
				'description' => '',
				'default' => '8.050'
			) ,
			'customerId3' => array(
				'title' => __('Κωδικός χρέωσης', 'woocommerce') ,
				'label' => __('', 'woocommerce') ,
				'type' => 'text',
				'description' => '',
				'default' => '2ΑΘ999999'
			) ,
			'weight-min4' => array(
				'title' => __('Βάρος (Kg) από', 'woocommerce') ,
				'label' => __('', 'woocommerce') ,
				'type' => 'text',
				'description' => '',
				'default' => '8.051'
			) ,
			'weight-max4' => array(
				'title' => __('Βάρος (Kg) έως', 'woocommerce') ,
				'label' => __('', 'woocommerce') ,
				'type' => 'text',
				'description' => '',
				'default' => '10'
			) ,
			'customerId4' => array(
				'title' => __('Κωδικός χρέωσης', 'woocommerce') ,
				'label' => __('', 'woocommerce') ,
				'type' => 'text',
				'description' => '',
				'default' => '2ΑΘ999999'
			) ,
		);
		$this->form_fields = array(
			'title' => array(
				'title' => __('Title', 'woocommerce') ,
				'type' => 'text',
				'description' => __('Shipping method description that the customer will see on your checkout.', 'woocommerce') ,
				'default' => __('ACS Courier', 'woocommerce') ,
				'desc_tip' => true,
			) ,
			'ws_host' => array(
				'title' => __('Host', 'woocommerce') ,
				'type' => 'text',
				'description' => __('Enter the web service domain.', 'woocommerce') ,
				'default' => 'https://services.acscourier.net',
				'desc_tip' => true
			) ,
			'ws_username' => array(
				'title' => __('Auth username', 'woocommerce') ,
				'type' => 'text',
				'description' => __('Enter the web service username', 'woocommerce') ,
				'default' => 'test1@acscourier.net',
				'desc_tip' => true
			) ,
			'ws_password' => array(
				'title' => __('Auth password', 'woocommerce') ,
				'type' => 'password',
				'description' => __('Enter the web service password', 'woocommerce') ,
				'default' => '123456',
				'desc_tip' => true
			) ,
			'ws_companyId' => array(
				'title' => __('Company Id', 'woocommerce') ,
				'type' => 'text',
				'description' => __('Enter the company Id', 'woocommerce') ,
				'default' => 'demo',
				'desc_tip' => true
			) ,
			'ws_companyPass' => array(
				'title' => __('Company Pass', 'woocommerce') ,
				'type' => 'password',
				'description' => __('Enter the company password', 'woocommerce') ,
				'default' => 'demo',
				'desc_tip' => true
			) ,
			'ws_params_username' => array(
				'title' => __('Username', 'woocommerce') ,
				'type' => 'text',
				'description' => __('Enter the username', 'woocommerce') ,
				'default' => 'demo',
				'desc_tip' => true
			) ,
			'ws_params_password' => array(
				'title' => __('Password', 'woocommerce') ,
				'type' => 'password',
				'description' => __('Enter the password', 'woocommerce') ,
				'default' => 'demo',
				'desc_tip' => true
			) ,
			'ws_companyname' => array(
				'title' => __('Company Name On Vouchers', 'woocommerce') ,
				'type' => 'text',
				'description' => __('Change the default company name on Vouchers', 'woocommerce') ,
				'default' => 'Aποστολέας',
				'desc_tip' => true
			) ,

			'ws_cod' => array(
				'title' => __('Additional Costs on Cash On Delivery Excluding VAT', 'woocommerce') ,
				'type' => 'text',
				'description' => __('This will enable additional costs for Cash On Delivery - Prices without VAT', 'woocommerce') ,
				'default' => 2,
				'desc_tip' => true
			) ,

			'ws_timezone' => array(
				'title' => __('Timezone', 'woocommerce') ,
				'type' => 'text',
				'description' => __('Timezone', 'woocommerce') ,
				'default' => 'Europe/Athens',
				'desc_tip' => true
			) ,
			'ws_usezipcodeonly' => array(
				'title' => __('Use Zipcode function for calculation?', 'woocommerce') ,
				'type' => 'checkbox',
				'description' => __('ACS provides and alternative to calculate Shipping costs only via Zip Code.', 'woocommerce') ,
				'default' => 'yes',
				'desc_tip' => true
			) ,
			'ws_addvat' => array(
				'title' => __('Include VAT on Shipping?', 'woocommerce') ,
				'type' => 'checkbox',
				'description' => __('This will enable the calculation of VAT on the Shipping totals. ?', 'woocommerce') ,
				'default' => 'yes',
				'desc_tip' => true
			) ,
			'ws_a4print' => array(
				'title' => __('Print on A4 Form?', 'woocommerce') ,
				'type' => 'checkbox',
				'description' => __('This will enable the A4 form with 3 vouchers?', 'woocommerce') ,
				'default' => 'no',
				'desc_tip' => true
			) ,
			'ws_acsadditionaloptions' => array(
				'title' => __('Disable ACS Delivery Saturday and Delivery Reception?', 'woocommerce') ,
				'type' => 'checkbox',
				'description' => __('Disables the ACS Delivery Saturday and Delivery to the Reception Options on the checkout page.', 'woocommerce') ,
				'default' => 'no',
				'desc_tip' => true
			) ,
			    'ws_acsadditionaloptionsdeadlinetime' => array(
				'title' => __('Last accepted time for previous date for Delivery On Saturday?', 'woocommerce') ,
				'type' => 'text',
				'description' => __('Sets the last accepted time of Fridays time to validate the Delivery on Saturday.', 'woocommerce') ,
				'default' => '1400',
				'desc_tip' => true
			) ,
			'ws_showadminparams' => array(
				'title' => __('Show Voucher Params On Orders Backend', 'woocommerce') ,
				'type' => 'checkbox',
				'description' => __('Show Voucher Params On Orders Backend to edit and save extra details.', 'woocommerce') ,
				'default' => 'no',
				'desc_tip' => true
			) ,
			'ws_trackingcodealtposition' => array(
				'title' => __('Alternative position for email tracking code?', 'woocommerce') ,
				'type' => 'checkbox',
				'description' => __('This will place the tracking code on the top area of the email.', 'woocommerce') ,
				'default' => 'no',
				'desc_tip' => true
			) ,
			'ws_customcalculationamount' => array(
				'title' => __('Fixed Shipping Amount', 'woocommerce') ,
				'type' => 'text',
				'description' => __('Enter the fixed shipping amount for all orders. This ovverides the ACS calculation and returns the custom amount. Use . for decimals Eg 3.5', 'woocommerce') ,
				'default' => '0',
				'desc_tip' => true
			) ,
			'ws_freeamountthreshold' => array(
				'title' => __('Free Shipping Amount Threshold', 'woocommerce') ,
				'type' => 'text',
				'description' => __('Enter the amount threshold to ovveride ACS calculation.', 'woocommerce') ,
				'default' => '0',
				'desc_tip' => true
			)
		);
		$this->billing_first_name = '';
		$this->billing_last_name = '';
		$this->billing_company = '';
		$this->billing_email = '';
		$this->billing_phone = '';
		$this->billing_country = '';
		$this->billing_address_1 = '';
		$this->billing_ = '';
		$this->billing_city = '';
		$this->billing_state = '';
		$this->billing_postcode = '';
		$this->acs_delivery_saturday = '';
		$this->acs_delivery_reception = '';
		$this->account_password = '';
		$this->ship_to_different_address = '';
		$this->ship_to_different_address = '';
		$this->shipping_first_name = '';
		$this->shipping_last_name = '';
		$this->shipping_company = '';
		$this->shipping_country = '';
		$this->shipping_address_1 = '';
		$this->shipping_ = '';
		$this->shipping_city = '';
		$this->shipping_state = '';
		$this->shipping_postcode = '';
		$this->order_comments = '';
		$this->shipping = '';
		$this->shipping_method = '';
		$this->payment_method = '';
		$this->_acs_voucher_id = '0';
		$this->_acs_voucher_created = '0';
		$this->_acs_voucher_printed = '0';
		$this->_acs_voucher_pickup = '0';
		$this->_acs_voucher_saturday = '0';
		$this->_acs_voucher_reception = '0';
		$this->_acs_voucher_status = '0';
		$this->_acs_voucher_items = '0';
		$this->_acs_order_items = 0;
		$this->shipping_total = 0;
		$this->extra_fees = 0;
		$this->extra_notice = '';
		$this->params = array();
		$this->enabled = $this->get_option('enabled');
		$this->title = $this->get_option('title');
		$this->get_option('ws_a4print') === 'yes' ? $a4print = '&PrintType=2&StartFromNumber=1' : $a4print = '&PrintType=1&StartFromNumber=1';
		$addresscheckurl =  "https://www.acscourier.net/el/search-areas?p_p_id=acssearchareaportlet_WAR_ACSSearchAreaportlet&p_p_lifecycle=1&p_p_state=normal&p_p_mode=view&p_p_col_id=column-4&p_p_col_pos=1&p_p_col_count=2&_acssearchareaportlet_WAR_ACSSearchAreaportlet_javax.portlet.action=getAreas";
		$this->acs_voucher_print_url = 'https://acs-eud2.acscourier.net/Eshops/GetVoucher.aspx?MainID=' . $this->get_option('ws_companyId') . '&MainPass=' . $this->get_option('ws_companyPass') . '&UserID=' . $this->get_option('ws_params_username') . '&UserPass=' . $this->get_option('ws_params_password') . $a4print;
		$this->acs_receipts_print_url = 'https://acs-eud2.acscourier.net/Eshops/getlist.aspx?MainID=' . $this->get_option('ws_companyId') . '&MainPass=' . $this->get_option('ws_companyPass') . '&UserID=' . $this->get_option('ws_params_username') . '&UserPass=' . $this->get_option('ws_params_password');
		!is_admin() ? $this->check_post_data() : false;
		date_default_timezone_set($this->get_option('ws_timezone'));
		$this->saturdaytime = $this->get_option('ws_acsadditionaloptionsdeadlinetime') != '' ? $this->saturdaytime : '1400';
		$this->init_settings();
		add_action('woocommerce_update_options_shipping_' . $this->id, array(
			$this,
			'process_admin_options'
		));
		}


	protected
	function check_post_data()
		{
		if (isset($_POST) && count($_POST) > 0)
			{
			if (isset($_POST) && count($_POST) > 0 && isset($_POST['post_data']))
				{
				parse_str($_POST['post_data'], $post_data);
				$post_data = array_map('trim', $post_data);
				}

			$post_data = array_map('trim', $post_data);
			$this->billing_first_name = isset($post_data) ? $post_data['billing_first_name'] : $_POST['billing_first_name'];
			$this->billing_last_name = isset($post_data) ? $post_data['billing_last_name'] : $_POST['billing_last_name'];
			$this->billing_company = isset($post_data) ? $post_data['billing_company'] : $_POST['billing_company'];
			$this->billing_email = isset($post_data) ? $post_data['billing_email'] : $_POST['billing_email'];
			$this->billing_phone = isset($post_data) ? $post_data['billing_phone'] : $_POST['billing_phone'];
			$this->billing_country = isset($post_data) ? $post_data['billing_country'] : $_POST['billing_country'];
			$this->billing_address_1 = isset($post_data) ? $post_data['billing_address_1'] : $_POST['billing_address_1'];
			$this->billing_address_2 = isset($post_data) ? $post_data['billing_address_2'] : $_POST['billing_address_2'];
			$this->billing_city = isset($post_data) ? $post_data['billing_city'] : $_POST['billing_city'];
			$this->billing_state = isset($post_data) ? $post_data['billing_state'] : $_POST['billing_state'];
			$this->billing_postcode = isset($post_data) ? $post_data['billing_postcode'] : $_POST['billing_postcode'];
			$this->acs_delivery_saturday = isset($post_data) ? $post_data['_acs_delivery_saturday'] : $_POST['_acs_delivery_saturday'];
			$this->acs_delivery_reception = isset($post_data) ? $post_data['_acs_delivery_reception'] : $_POST['_acs_delivery_reception'];
			$this->account_password = isset($post_data) ? $post_data['account_password'] : $_POST['account_password'];
			$this->ship_to_different_address = isset($post_data) ? $post_data['ship_to_different_address'] : $_POST['ship_to_different_address'];
			$this->shipping_first_name = isset($post_data) ? $post_data['shipping_first_name'] : $_POST['shipping_first_name'];
			$this->shipping_last_name = isset($post_data) ? $post_data['shipping_last_name'] : $_POST['shipping_last_name'];
			$this->shipping_company = isset($post_data) ? $post_data['shipping_company'] : $_POST['shipping_company'];
			$this->shipping_country = isset($post_data) ? $post_data['shipping_country'] : $_POST['shipping_country'];
			$this->shipping_address_1 = isset($post_data) ? $post_data['shipping_address_1'] : $_POST['shipping_address_1'];
			$this->shipping_address_2 = isset($post_data) ? $post_data['shipping_address_2'] : $_POST['shipping_address_2'];
			$this->shipping_city = isset($post_data) ? $post_data['shipping_city'] : $_POST['shipping_city'];
			$this->shipping_state = isset($post_data) ? $post_data['shipping_state'] : $_POST['shipping_state'];
			$this->shipping_postcode = isset($post_data) ? $post_data['shipping_postcode'] : $_POST['shipping_postcode'];
			$this->order_comments = isset($post_data) ? $post_data['order_comments'] : $_POST['order_comments'];
			$this->shipping = isset($post_data) ? (explode(":", $post_data['shipping_method'][0])) : (explode(":", $_POST['shipping_method'][0]));
			$this->shipping_method = $this->shipping[0];
			$this->payment_method = isset($post_data) ? $post_data['payment_method'] : $_POST['payment_method'];
			}
		}

	public

	function calculate_shipping($package = array()) {

		// Creates a file with the package contents for debugging ACS webservice
		 //file_put_contents(__FILE__.'-calculate_shipping.txt',print_r($package,true),FILE_APPEND);

		global $woocommerce;
		if (defined('WP_DEBUG') && true === WP_DEBUG)
			{
			$this->extra_notice = '- Debug ACS : ' . $this->acs_validate_address();
			}

		if ($this->acs_validate_address() === false)
			{
			$this->isvalidated = false;
			$this->shipping_total = 0;
			$this->get_option('ws_usezipcodeonly') === 'no' ? $this->extra_notice = ' [Λάθος διεύθυνση ή ταχυδρομικός κώδικας. Παρακαλώ ξαναπροσπαθήστε χωρίς κενά ή παύλες (π.χ. ΜΙΑΟΥΛΗ 16) (ΓΕΡΑΚΑΣ) (15344)​]' : $this->extra_notice = ' [Λάθος ή ανύπαρκτος ταχυδρομικός κώδικας. Παρακαλώ ξαναπροσπαθήστε χωρίς κενά ή παύλες (π.χ.15344)​]'  ;
			}
        
		$cost = $this->shipping_total + $this->extra_fees;
		$this->add_rate(array(
			'id' => $this->id,
			'label' => $this->title . $this->extra_notice,
			'cost' => $cost,
			'package' => $package
		));
		}

	private function acs_validate_address()
		{
		global $woocommerce;
		$result = null;
		$this->extra_notice = '';
		
		WC()->session->set('acsadditionaloptions', $acsadditionaloptions);
		WC()->session->set('trackingcodealtposition', $trackingcodealtposition);
		if ($this->shipping_method != 'acs_courier')
			{

			// return false;

			}

		if ($this->ship_to_different_address)
			{
			if ($this->shipping_address_1 != '' && $this->shipping_postcode != '')
				{
				$this->params['address'] = $this->shipping_address_1 . ', ' . $this->shipping_postcode;
				$this->params['lang'] = 'GR';
				$this->params['postcode'] = $this->shipping_postcode;
				$this->params['address_1'] = $this->shipping_address_1;
				$this->params['address_2'] = $this->shipping_address_2;
				$this->params['company'] = $this->billing_company;
				$this->params['address_city'] = $this->shipping_city;
				$this->params['fullname'] = $this->shipping_first_name . ' ' . $this->shipping_last_name;
				$this->params['fullname'] = $this->shipping_first_name . ' ' . $this->shipping_last_name;
				}
			}
		  else
			{
			if ($this->billing_address_1 != '' && $this->billing_postcode != '')
				{
				$this->params['address'] = $this->billing_address_1 . ', ' . $this->billing_postcode;
				$this->params['lang'] = 'GR';
				$this->params['postcode'] = $this->billing_postcode;
				$this->params['address_1'] = $this->billing_address_1;
				$this->params['address_2'] = $this->billing_address_2;
				$this->params['company'] = $this->shipping_company;
				$this->params['address_city'] = $this->billing_city;
				$this->params['fullname'] = $this->billing_first_name . ' ' . $this->billing_last_name;
				$this->params['fullname'] = $this->billing_first_name . ' ' . $this->billing_last_name;
				}
			}

		if ($this->params)
			{
			$acs = $this->acs_web_service();
			if ($this->get_option('ws_usezipcodeonly') === 'yes')
				{
				defined('WP_DEBUG') && true === WP_DEBUG ? $this->extra_notice.= ' - Υπολογισμός με Τ.κ.' : false;
				$result = $acs->findByZipCode($this->params);
				}
			  else
			if ($this->get_option('ws_usezipcodeonly') === 'no')
				{
				defined('WP_DEBUG') && true === WP_DEBUG ? $this->extra_notice.= ' - Υπολογισμός με πλήρη διεύθυνση ' : false;
				$result = $acs->validateAddress($this->params);
				}
			}


		if (!is_array($result))
			{
			return false;
			}
		  else
			{
			if ($result['dp_dx'] != '') {
				$acsDeliveryDpDx = 'yes';
				$this->extra_notice.= ' + Δυσπρόσιτη διεύθυνση ';

                $this->acs_delivery_saturday === 'yes' && $acsDeliveryDpDx ==='yes' ? $this->extra_notice = ' - Δυσπρόσιτος προορισμός με Παράδοση Σάββατο δεν συνδυάζεται, θα σας παραδοθεί σύμφωνα με τους χρόνους παράδοσης της ACS!' : false;
	    		$this->acs_delivery_reception === 'yes' &&  $acsDeliveryDpDx ==='yes' ? $this->extra_notice .= ' - Δυσπρόσιτος προορισμός με Παράδοση στο πλησιέστερο κατάστημα ACS δεν συνδυάζεται, θα σας παραδοθεί σύμφωνα με τους χρόνους παράδοσης της ACS!' : false;

				$acsDeliverySaturday = 'no';
				$acsDeliveryReception = 'no';
				$this->acs_delivery_saturday ='no';
				$this->acs_delivery_reception = 'no';
	   			$fields['billing']['_acs_delivery_reception']['default'] = 'no';
                $fields['billing']['_acs_delivery_saturday']['default'] = 'no';
	            WC()->session->set('acs_delivery_reception', $acsDeliveryReception);
				WC()->session->set('acs_delivery_saturday', $acsDeliverySaturday);
				
				}
			  else
				{
				$acsDeliveryDpDx = 'no';
				}
            
			WC()->session->set('acs_delivery_dp_dx', $acsDeliveryDpDx);
			$acs_zone_options = get_option('woocommerce_acs_courier_' . $this->instance_id . '_settings');
			if (sizeof($woocommerce->cart->get_cart()) > 0)
				{ //if ( $woocommerce->cart->cart_contents_count  > 0 ) {
				$weight = 0;
				$quantity = 0;
				$quantities = $woocommerce->cart->cart_contents_count; //array();
				foreach($woocommerce->cart->get_cart() as $cart_item_key => $values)
					{
					$_product = $values['data'];
					$weight = $woocommerce->cart->cart_contents_weight; //+= $_product->weight * $values['quantity'];
					$weight < 0.5 ? $weight = 0.5 : false; //min weight 0.5 per delivery
					}
				}

			$this->params['customerId'] = $this->get_customer_id($weight, $acs_zone_options);
			$this->extra_notice.= ' + Βάρος: ' . $weight . 'Kg';
			$this->params['products'] = '';
            
			if ((date('Hi') > $this->get_option('ws_acsadditionaloptionsdeadlinetime') && (date('l', strtotime($today)) == 'Friday' )) || (date('l', strtotime($today)) == 'Saturday' )) {
			        $this->extra_notice = '- Η παράδοση Σάββατο αφορά παραγγελίες που γίνονται μέχρι το αργότερο την Παρασκευή στις ' . $this->get_option('ws_acsadditionaloptionsdeadlinetime'); 
			        $acsDeliverySaturday = 'no';
				    $this->acs_delivery_saturday ='no';
                    $fields['billing']['_acs_delivery_saturday']['default'] = 'no';
				    WC()->session->set('acs_delivery_saturday', $acsDeliverySaturday);
			     } 
			
			if ($this->acs_delivery_saturday == 'yes')
				{
				    
				$acsDeliverySaturday = 'yes';
				    $this->extra_notice.= ' + Παράδοση Σάββατο';
				    $this->params['products'].= '5Σ,';
				}
			  else
				{
				$acsDeliverySaturday = 'no';
				}

			WC()->session->set('acs_delivery_saturday', $acsDeliverySaturday);
			
			if ($this->acs_delivery_reception == 'yes')
				{
				$acsDeliveryReception = 'yes';
				$this->extra_notice.= ' + Παραλαβή από κοντινότερο κατάστημα ACS';
				$this->params['products'].= 'ΡΣ,';
				}
			  else
				{
				$acsDeliveryReception = 'no';
				}

			$acsDeliveryDpDx == 'yes' ? $this->params['products'] = 'ΔΠ,' : false;
			
			WC()->session->set('acs_delivery_reception', $acsDeliveryReception);
			
			if ($this->payment_method === 'cod')
				{
				$this->extra_notice.= ' + Αντικαταβολή';
				$this->params['products'].= 'ΑΝ';
				}

			$this->params['st_from'] = 'ΑΘ';
			if ($this->get_option('ws_usezipcodeonly') === 'no')
				{
				$this->params['st_to'] = $result['station_id'];
				}
			  else
			if ($this->get_option('ws_usezipcodeonly') === 'yes')
				{
				$this->params['st_to'] = $result['acs_station'];
				}

			$this->params['varos'] = $weight;
			$this->params['date_par'] = date("d/m/Y");
			$this->params['xrewsh'] = 2;
			$this->params['zone'] = null;
			$this->params['asf_poso'] = 0.00;
            
            // Add Additional Costs/Fees when COD payment method is selected
            $this->payment_method === 'cod' && $this->get_option('ws_cod') !='' ?  $codadddedfees = wc_price($this->get_option('ws_cod') * 1.24) : $codadddedfees= '0';

			// Allow using custom theme code for calculating shipping
			// add_filter must be used with a returning value of true

			$hasOverrideCalculation = apply_filters('acs_payment_has_overriden_price_calculation', false);
			if ($hasOverrideCalculation)
				{

				// Bundle above calculated values to an options array for
				// the custom calculating function to use

				$calculationOptions = ['acsDeliveryDpDx' => $acsDeliveryDpDx, 'acsDeliveryReception' => $acsDeliveryReception, 'acsDeliverySaturday' => $acsDeliverySaturday];

				// Call the custom calculating function passing $this and above pre-calculated options
				$price = apply_filters('acs_payment_overriding_price_calculation_function', $this, $calculationOptions);
				if (isset($price))
					{
					list($basicCost, $additionalCosts, $Price, $Vat) = explode('|', $price['ammountDet']);
					$this->extra_notice.= ' + Κόστος: ' . wc_price($basicCost);
					$this->extra_notice.= ' + Επιπλέον Κόστη: ' . wc_price($additionalCosts+$codadddedfees);
					$this->extra_notice.= ' + Καθαρό Σύνολο: ' . wc_price($codadddedfees + $Price);;
					$this->extra_notice.= ' + ΦΠΑ: ' . wc_price($Vat);
					$this->shipping_total = ($this->get_option('ws_addvat') === 'yes') ? round($Price, 2) + round($Vat, 2) + round($codadddedfees,2) : round($price['price'],2)+round($codadddedfees,2);
					}

				if (floatval($this->get_option('ws_customcalculationamount')) > 0)
					{
					$this->extra_notice = ' ';

					//  $this->shipping_total = $this->get_option('ws_customcalculationamount') ;

					}
				}
			  else
				{
				$price = $acs->getPrice($this->params);
				if (!is_array($price))
				{
					return false;
				}
				  else
				{
				if ($price['errorMsg'] !== '')
				    {
					    $this->extra_notice.= $price['errorMsg'];
					    return false;
				    }
				}
				list($basicCost, $additionalCosts, $Price, $Vat) = explode('|', $price['ammountDet']);
				$this->extra_notice.= ' + Κόστος: ' . $basicCost;
				$this->extra_notice.= ' + Επιπλέον Κόστη: ' . wc_price($additionalCosts+$codadddedfees);
				$this->extra_notice.= ' + Καθαρό Σύνολο: ' . wc_price($codadddedfees + $Price);
				$this->extra_notice.= ' + ΦΠΑ: ' . $Vat;
				$this->get_option('ws_addvat') === 'yes' ? $this->shipping_total = number_format($Price, 2) + number_format($Vat, 2) + number_format($codadddedfees,2): $this->shipping_total = number_format($price['price'],2) + number_format($codadddedfees,2);
				
				global $woocommerce;
				if (floatval($this->get_option('ws_customcalculationamount')) > 0)
					{
					$this->extra_notice = ' ';
					$this->shipping_total = floatval($this->get_option('ws_customcalculationamount'));
					}

				if ((WC()->cart->get_cart_contents_total() > floatval($this->get_option('ws_freeamountthreshold'))))
					{
					if ($this->get_option('ws_freeamountthreshold') !== '0')
						{
						$this->extra_notice = ' - Δωρεάν Μεταφορικά ποσά άνω των ' . $this->get_option('ws_freeamountthreshold') . '€';
						$this->shipping_total = 0;
						}
					}
				}
				
			// Additional Costs to add on the Checκout Page
			//$woocommerce->cart->add_fee('Επιπλέον Κόστη Μεταφορικών (Εκτός ACS)' . $this->get_option('ws_cod'), $this->get_option('ws_cod') . '€', true, $this->get_option('ws_cod')) ;

			WC()->session->set('stationIdDest', $this->params['st_to']);
			WC()->session->set('acDiakStoixs', $this->params['products']);
			WC()->session->set('diakNotes', trim($this->order_comments));
			WC()->session->set('diakVaros', $weight);
			WC()->session->set('customerId', $this->params['customerId']); 
			WC()->session->set('diakParalhpthsOnoma', $this->params['fullname']);
			WC()->session->set('diakParalhpthsDieth', $this->params['address_1']);
			WC()->session->set('diakParalhpthsOrofos', $this->params['address_2']);
			WC()->session->set('diakParalhpthsCompany', $this->params['company']);
			WC()->session->set('acDiakParalhpthsDiethAr', ' ');
			WC()->session->set('acDiakParalhpthsDiethPer', $this->params['address_city']);
			WC()->session->set('diakParalhpthsThlef', $this->billing_phone); //strval($this->params['billing_phone']) );
			WC()->session->set('diakParalhpthsCell', $this->billing_phone); //strval($this->params['billing_phone']) );
			WC()->session->set('diakParalhpthsTk', $this->params['postcode']);
			if ($this->payment_method == 'cod')
				{
				WC()->session->set('diakAntikatPoso', $this->shipping_total);
				WC()->session->set('diakTroposPlAntikat', 'Μ');
				}
			  else
				{
				WC()->session->set('diakAntikatPoso', 0);
				WC()->session->set('diakTroposPlAntikat', null);
				}

			return true;
			
			}

		return false;
		}

	public function get_acs_voucher_print_url()
		{
		return $this->acs_voucher_print_url;
		}

	public function get_acs_receipts_print_url()
		{
		return $this->acs_receipts_print_url;
		}

	public

	function get_acs_voucher_admin_params()
		{
		return $this->get_option('ws_showadminparams');
		}

	public

	function delete_voucher($id)
		{
		global $woocommerce;
		$acs = $this->acs_web_service();
		$this->params['noPod'] = $id;
		$res = $acs->deleteVoucher($this->params);
		return $res;
		}

	public

	function get_MassNumbers($date)
		{
		global $woocommerce;
		$acs = $this->acs_web_service();
		$this->params['dateParal'] = $date;
		$this->params['lang'] = 'GR';
		$res = $acs->getMassNumbers($this->params);
		return $res;
		}

	public

	function tracking($id)
		{
		global $woocommerce;
		$acs = $this->acs_web_service();
		$this->params['pod_no'] = $id;
		$res = $acs->tracking($this->params);
		return $res;
		}

	public

	function receipt_list($date)
		{
		global $woocommerce;
		$data = array();
		$acs = $this->acs_web_service();
		$this->params['dateParal'] = $date;
		$r = $acs->receiptsList($this->params);
		if ($r['error'] != '')
			{
			$unprinted = $acs->getUnprintedPods($this->params);
			$data['message'] = $r['error'];
			foreach($unprinted as $r)
				{
				$data['rows'][] = $r['no_pod'];
				}
			}
		  else
			{
			$data['message'] = 'Success';
			$data['rows'][] = $r['massNumber'];
			}

		return $data;
		}

	public

	function create_voucher_admin($order_id, $params)
		{
		$_acs_pickup_saturday = get_post_meta($order_id, '_acs_voucher_pickup_saturday', true);
		$today = date('Y-m-d');
		if (date('l', strtotime($today)) == 'Saturday' || date('l', strtotime($today)) == 'Sunday' // ||	( date('l', strtotime($today)) == 'Friday')// && date('Hi') > '1700')
		)
			{
			$pickup_date = date('Y-m-d', strtotime('next monday'));
			}
		elseif (date('Hi') > '1700')
			{
			$pickup_date = $today; //date('Y-m-d', strtotime('tomorrow'));
			if ($_acs_pickup_saturday == 'yes')
				{
				$pickup_date = date('Y-m-d', strtotime('next friday'));
				$this->_acs_voucher_saturday = 'yes';
				}
			}
		  else
			{
			$pickup_date = $today;
			if ($_acs_pickup_saturday == 'yes')
				{
				$pickup_date = date('Y-m-d', strtotime('next friday'));
				$this->_acs_voucher_saturday = 'yes';
				}
			}

		$acs = $this->acs_web_service();
		$this->params = $params;
		$this->params['diakDateParal'] = $pickup_date;
		$result = $acs->createVoucher($this->params);
		if ($result['errorMsg'] != '')
			{
			$this->_acs_voucher_status = $result['errorMsg'];
			update_post_meta($order_id, '_acs_voucher_status', $this->_acs_voucher_status);
			$_SESSION['_acs_error'] = $this->_acs_voucher_status;
			}
		  else
			{
			$this->_acs_voucher_status = '';
			$this->_acs_voucher_id = $result['no_pod'];
			$this->_acs_voucher_created = $result['diakDateParal'];
			$this->_acs_voucher_pickup = $this->params['diakDateParal'];
			update_post_meta($order_id, '_acs_voucher_id', $this->_acs_voucher_id);
			update_post_meta($order_id, '_acs_voucher_date_created', $this->_acs_voucher_created);
			update_post_meta($order_id, '_acs_voucher_date_pickup', $this->_acs_voucher_pickup);
			update_post_meta($order_id, '_acs_voucher_pickup_saturday', $this->_acs_voucher_saturday);
			update_post_meta($order_id, '_acs_voucher_pickup_reception', get_post_meta($order_id, '_acs_voucher_pickup_reception', true));
			update_post_meta($order_id, '_acs_voucher_status', $this->_acs_voucher_status);
			update_post_meta($order_id, '_acs_voucher_params', json_encode($this->params, JSON_UNESCAPED_UNICODE));
			}

		$_acs_voucher_id = $this->_acs_voucher_id;
		return $_acs_voucher_id;
		}

	public

	function create_voucher($order_id)
		{
		global $woocommerce;
		$order = new WC_Order($order_id);
		$_payment_method = get_post_meta($order_id, '_payment_method', true);
		$_acs_voucher_id = get_post_meta($order_id, '_acs_voucher_id', true);
		$_order_total = get_post_meta($order_id, '_order_total', true);
		if ($_acs_voucher_id == '' || $_acs_voucher_id == '0')
			{
			foreach($order->get_items('shipping') as $el)
				{
				$order_shipping_method_id = $el['method_id'];
				}

			foreach($order->get_items('line_item') as $item)
				{
				$this->_acs_order_items = 1; //+= $item['qty'];
				}

			if ($order_shipping_method_id == 'acs_courier')
				{
				$acs = $this->acs_web_service();
				$today = date('Y-m-d');
				if (date('l', strtotime($today)) == 'Saturday' || date('l', strtotime($today)) == 'Sunday' //||( date('l', strtotime($today)) == 'Friday')// && date('Hi') > '1700')
				)
					{
					$pickup_date = date('Y-m-d', strtotime('next monday'));
					}
				elseif (date('Hi') > '1700')
					{
					$pickup_date = $today; //date('Y-m-d', strtotime('tomorrow'));
					if ($_acs_pickup_saturday == 'yes')
						{
						$pickup_date = date('Y-m-d', strtotime('next friday'));
						$this->_acs_voucher_saturday = 'yes';
						}
					}
				  else
					{
					$pickup_date = $today;
					if ($_acs_pickup_saturday == 'yes')
						{
						$pickup_date = date('Y-m-d', strtotime('next friday'));
						$this->_acs_voucher_saturday = 'yes';
						}
					}

				$diakTroposPlAntikat = WC()->session->get('diakTroposPlAntikat');
				if (isset($diakTroposPlAntikat) && $diakTroposPlAntikat != '')
					{
					$this->params['diakTroposPlAntikat'] = $diakTroposPlAntikat;
					$this->params['diakAntikatPoso'] = $_payment_method == 'cod' ? $_order_total : null; //$_order_total
					}
				  else
					{
					$this->params['diakAntikatPoso'] = $_payment_method == 'cod' ? $_order_total : null; //$_order_total
					$this->params['diakTroposPlAntikat'] = null;
					}

				$this->params['diakDateParal'] = $pickup_date;
				$this->params['stationIdDest'] = WC()->session->get('stationIdDest');
				$this->params['acDiakStoixs'] = WC()->session->get('acDiakStoixs');
				$this->params['diakNotes'] = WC()->session->get('diakNotes');
				$this->params['diakTemaxia'] = $this->_acs_order_items;
				$this->params['diakVaros'] = WC()->session->get('diakVaros');
				$this->params['customerId'] = WC()->session->get('customerId');   
				$this->params['diakParalhpthsOnoma'] = WC()->session->get('diakParalhpthsOnoma');
				$this->params['diakParalhpthsDieth'] = WC()->session->get('diakParalhpthsDieth');
				$this->params['acDiakParalhpthsDiethAr'] = WC()->session->get('acDiakParalhpthsDiethAr');
				$this->params['acDiakParalhpthsDiethPer'] = WC()->session->get('acDiakParalhpthsDiethPer');
				$this->params['diakParalhpthsThlef'] = WC()->session->get('diakParalhpthsThlef');
				$this->params['diakParalhpthsTk'] = WC()->session->get('diakParalhpthsTk');
				$this->params['branchIdDest'] = 0;
				$this->params['diakXrewsh'] = 2;
				$this->params['diakWraMexri'] = '';
				$this->params['hostName'] = '';
				$this->params['diakCountry'] = 'GR';
				$this->params['diakcFiller'] = '1';
				$this->params['diakParalhpthsCell'] = WC()->session->get('diakParalhpthsCell');
				$this->params['diakParalhpthsOrofos'] = WC()->session->get('diakParalhpthsOrofos');
				$this->params['diakParalhpthsCompany'] = WC()->session->get('diakParalhpthsCompany');
				$this->params['withReturn'] = 0;
				$this->params['diakcCompCus'] = '1';
				$this->params['specialDir'] = '';
				/*
				if($_payment_method != 'bacs'){
				$result = $acs->createVoucher($this->params);
				if( $result['errorMsg'] != '' ){
				$this->_acs_voucher_status   = $result['errorMsg'];
				}
				  else {
				$_acs_voucher_id   = $result['no_pod'];
				$this->_acs_voucher_created   = $result['diakDateParal'];
				$this->_acs_voucher_pickup  = $this->params['diakDateParal'];
				}
				}

				*/
				$_acs_voucher_id = $result['no_pod'];
				$this->_acs_voucher_created = $result['diakDateParal'];
				$this->_acs_voucher_pickup = $this->params['diakDateParal'];
				}

			update_post_meta($order_id, '_acs_voucher_id', $_acs_voucher_id);
			update_post_meta($order_id, '_acs_voucher_date_created', $this->_acs_voucher_created);
			update_post_meta($order_id, '_acs_voucher_date_printed', $this->_acs_voucher_printed);
			update_post_meta($order_id, '_acs_voucher_date_pickup', $this->_acs_voucher_pickup);
			update_post_meta($order_id, '_acs_voucher_pickup_saturday', $this->_acs_voucher_saturday);
			update_post_meta($order_id, '_acs_voucher_pickup_reception', get_post_meta($order_id, '_acs_voucher_pickup_reception', true));
			update_post_meta($order_id, '_acs_voucher_printed', $this->_acs_voucher_printed);
			update_post_meta($order_id, '_acs_voucher_status', $this->_acs_voucher_status);
			update_post_meta($order_id, '_acs_voucher_items', $this->_acs_order_items);
			update_post_meta($order_id, '_acs_voucher_params', json_encode($this->params, JSON_UNESCAPED_UNICODE));
			}

		return $_acs_voucher_id;
		}


	public function get_acs_delivery_options() {
		return  $this->get_option('ws_acsadditionaloptions');
	}

	public function acs_acs_delivery_options_defaults(){
 
	    
	}
	
	public function get_acs_email_alternative_position()
		{
		return $this->get_option('ws_trackingcodealtposition');
		}

	public function get_acs_free_shipping()
		{
		return $this->get_option('ws_freeamountthreshold');
		}

	public function get_acs_validated_address()
		{
		return $this->acs_validate_address();
		}

	public function get_acs_company_name()
		{
		return $this->get_option('ws_companyname');
		}

	private function acs_web_service()
		{
		require_once ('acs_ws.php');

		$acs = new WS_SOAP_ACS($this->get_option('ws_host') , $this->get_option('ws_username') , $this->get_option('ws_password') , $this->get_option('ws_companyId') , $this->get_option('ws_companyPass') , $this->get_option('ws_params_username') , $this->get_option('ws_params_password'));
		return $acs;
		}

	private function get_customer_id($weight, $acs_zone_options)
		{
		if ($weight > $acs_zone_options['weight-max1'])
			{
			if ($weight > $acs_zone_options['weight-max2'])
				{
				if ($weight > $acs_zone_options['weight-max3'])
					{
					if ($weight > $acs_zone_options['weight-max4'])
						{
						return $acs_zone_options['customerId4'];
						}
					  else
						{
						return $acs_zone_options['customerId4'];
						}
					}
				  else
					{
					return $acs_zone_options['customerId3'];
					}
				}
			  else
				{
				return $acs_zone_options['customerId2'];
				}
			}
		  else
			{
			return $acs_zone_options['customerId1'];
			}
		}
	}
