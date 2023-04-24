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

if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
	/**
	 * Init ACS Shipping Method
	 */
	function acs_shipping_method_init()
	{
		if (!class_exists('WC_ACS_Shipping_Method')) {
			require_once ('inc/acs_shipping_class.php');

			$acs = new WC_ACS_Shipping_Method();
			
			function acs_checkout_fields($fields)
			{

				// Add custom checkout fields and get user selections from session

				$deliveryOnSaturday = WC()->session->get('acs_delivery_saturday');
				$deliveryReception = WC()->session->get('acs_delivery_reception');
				switch ($deliveryOnSaturday) {
				case 'yes':
					break;

				default:
					$deliveryOnSaturday = 'no';
				}

				switch ($deliveryReception) {
				case 'yes':
					break;

				default:
					$deliveryReception = 'no';
				}

				$fields['billing']['_acs_delivery_saturday'] = array(
					'type' => 'select',
					'label' => __('Παράδοση Σάββατο (Δεν ισχύει για Δυσπρόσιτους Προορισμούς)', 'woocommerce') ,
					'required' => true,
					'class' => array(
						'form-row-wide saturday',
						'custom_acs_shipping',
						'update_totals_on_change'
					) ,
					'options' => array(
						'yes' => 'Ναι',
						'no' => 'Όχι'
					) ,
					'default' => $deliveryOnSaturday
				);
				$fields['billing']['_acs_delivery_reception'] = array(
					'type' => 'select',
					'label' => __('Παράδοση στο πλησιέστερο κατάστημα ACS (Δεν ισχύει για Δυσπρόσιτους Προορισμούς)', 'woocommerce') ,
					'required' => true,
					'class' => array(
						'form-row-wide reception',
						'custom_acs_shipping',
						'update_totals_on_change'
					) ,
					'options' => array(
						'yes' => 'Ναι',
						'no' => 'Όχι'
					) ,
					'default' => $deliveryReception
				);
				return $fields;
			}

			$acs->get_acs_delivery_options() === 'no'  ? add_filter('woocommerce_checkout_fields', 'acs_checkout_fields') : false;

		}
	}

	add_action('woocommerce_shipping_init', 'acs_shipping_method_init');
	
   
	/**
	 * Hook ACS Shipping Method
	 */
	function acs_shipping_method($methods)
	{
		$methods['acs_courier'] = 'WC_ACS_Shipping_Method';
		return $methods;
	}

	add_filter('woocommerce_shipping_methods', 'acs_shipping_method');
	/**
	 * Trigger Payment Method to update Cash on Delivery costs
	 */
	function acs_payment_method_trigger()
	{
?>
        <script>
            jQuery("#order_review").on("change", "input[name='payment_method']", function () {
                jQuery(document.body).trigger('update_checkout', { update_shipping_method: true });
            });
        </script>
        <?php
	}

	add_action('woocommerce_after_checkout_form', 'acs_payment_method_trigger');
	
	/**
	 * Trigger Calculate Shipping to validate and update costs
	 */
	 
	function acs_calculate_shipping()
	{
		WC()->cart->calculate_totals();
		$packages = WC()->shipping->get_packages();

		// Remove package information from the session cache to allow for
		// proper recalculation of user selections

		foreach($packages as $package_key => $package) {
			$session_key = 'shipping_for_package_' . $package_key;
			WC()->session->set($session_key, NULL);
		}

		WC()->shipping->calculate_shipping($packages);

	}

	//add_action('woocommerce_review_order_before_cart_contents', 'acs_calculate_shipping', 10);
	add_action('woocommerce_checkout_update_order_review', 'acs_calculate_shipping', 10);  
	//add_action('woocommerce_review_order_before_payment', 'acs_calculate_shipping', 10);
	//add_action('woocommerce_review_order_before_submit', 'acs_calculate_shipping', 10);
	add_action('woocommerce_review_order_before_shipping', 'acs_calculate_shipping', 10); 
	
	function acs_validate_order($posted)	
	{	
		WC()->cart->calculate_totals();	
		$packages = WC()->shipping->get_packages();	
		WC()->shipping->calculate_shipping($packages);	
		$shipping = isset($post_data) ? (explode(":", $post_data['shipping_method'][0])) : (explode(":", $_POST['shipping_method'][0]));	
		$acs = new WC_ACS_Shipping_Method();	
		if ($shipping[0] == 'acs_courier') {	
			if ($_GET['wc-ajax'] == 'checkout' && ($packages[0]['rates']['acs_courier']->cost < 1 && $acs->get_acs_validated_address() === false)) {	
				wc_add_notice('Λάθος διεύθυνση ή ταχυδρομικός κώδικας. Παρακαλώ ξαναπροσπαθήστε χωρίς κενά ή παύλες (π.χ. ΜΙΑΟΥΛΗ 16) (ΓΕΡΑΚΑΣ) (15344)​', 'error');	
			}
		}	
	}	

	add_action('woocommerce_after_checkout_validation', 'acs_validate_order', 10, 1);
	
	/**
	 * Create Voucher Params before order payment
	 */
	function acs_checkout_order_processed($order_id, $posted_data, $order)
	{
		if ($posted_data['shipping_method'][0] == 'acs_courier') {
			require_once ('inc/acs_shipping_class.php');

			if (!is_admin()) {
				$acs = new WC_ACS_Shipping_Method();
				$voucher = $acs->create_voucher($order_id);
			}
		}
	}

	add_action('woocommerce_checkout_order_processed', 'acs_checkout_order_processed', 10, 3);
	/**
	 * Get Voucher ID after order payment
	 */
	function acs_thankyou($order_id)
	{
		global $woocommerce;
		if (!is_admin()) {
			$_acs_voucher_id = get_post_meta($order_id, '_acs_voucher_id', true);
			if ($_acs_voucher_id != '' && $_acs_voucher_id != '0') {
				'<p><strong>ACS Voucher Number:</strong><br /><a href="https://www.acscourier.net/el/track-and-trace?p_p_id=ACSCustomersAreaTrackTrace_WAR_ACSCustomersAreaportlet&p_p_lifecycle=1&p_p_state=normal&p_p_mode=view&p_p_col_id=column-4&p_p_col_pos=1&p_p_col_count=3&_ACSCustomersAreaTrackTrace_WAR_ACSCustomersAreaportlet_javax.portlet.action=trackTrace&&generalCode=' . $_acs_voucher_id . '", target="_blank">' . $_acs_voucher_id . '</a></p>';

			}
		}
	}

	add_action('woocommerce_thankyou', 'acs_thankyou', 10, 1);
	/**
	 * Display Voucher Number in admin orders
	 */
	function acs_voucher_id_display_admin($order)
	{
		global $woocommerce;
		$_acs_voucher_id = get_post_meta($order->id, '_acs_voucher_id', true);
		if (isset($_acs_voucher_id) && $_acs_voucher_id != '0') echo '<br /><p><strong>ACS Voucher Number:</strong><br /><a href="https://www.acscourier.net/el/track-and-trace?p_p_id=ACSCustomersAreaTrackTrace_WAR_ACSCustomersAreaportlet&p_p_lifecycle=1&p_p_state=normal&p_p_mode=view&p_p_col_id=column-4&p_p_col_pos=1&p_p_col_count=3&_ACSCustomersAreaTrackTrace_WAR_ACSCustomersAreaportlet_javax.portlet.action=trackTrace&&generalCode=' . $_acs_voucher_id . '", target="_blank">' . $_acs_voucher_id . '</a></p>';
	}

	add_action('woocommerce_admin_order_data_after_billing_address', 'acs_voucher_id_display_admin', 10, 1);
	/**
	 * Display Voucher Number in email
	 */
	function acs_voucher_id_display_email($order, $sent_to_admin, $plain_text)
	{
		global $woocommerce;
		$acs = new WC_ACS_Shipping_Method();
		$_acs_voucher_id = get_post_meta($order->id, '_acs_voucher_id', true);
		if (isset($_acs_voucher_id) && $_acs_voucher_id != '' && $_acs_voucher_id != '0') {
			_e('<H4><a href="https://www.acscourier.net/el/track-and-trace?p_p_id=ACSCustomersAreaTrackTrace_WAR_ACSCustomersAreaportlet&p_p_lifecycle=1&p_p_state=normal&p_p_mode=view&p_p_col_id=column-4&p_p_col_pos=1&p_p_col_count=3&_ACSCustomersAreaTrackTrace_WAR_ACSCustomersAreaportlet_javax.portlet.action=trackTrace&&generalCode=' . $_acs_voucher_id . '", target="_blank">ACS Voucher (Αναζήτηση παραγγελίας): ' . $_acs_voucher_id .'</a></H4></br></br></br>', 'acs-shipping');
		}
	}

	add_action('woocommerce_email_order_details', 'acs_voucher_id_display_email', 10, 3);

	// $acs->get_acs_email_alternative_position() == 'no' ? add_action('woocommerce_email_customer_details', 'acs_voucher_id_display_email', 10, 3) : add_action('woocommerce_email_order_details', 'acs_voucher_id_display_email', 10, 3);
	// $acs->get_acs_email_alternative_position() == 'yes' ?  add_action('woocommerce_email_order_details', 'acs_voucher_id_display_email', 10, 3) : false;

	/**
	 * Register ACS Vouchers in Menu
	 */
	function acs_register_vouchers()
	{
		add_submenu_page('woocommerce', 'ACS Vouchers', 'ACS Vouchers', 'read', 'acs-vouchers', 'acs_vouchers');
	}

	add_action('admin_menu', 'acs_register_vouchers');
	/**
	 * ACS Vouchers Page
	 */
	function acs_vouchers()
	{
		global $woocommerce;
		require_once ('inc/acs_shipping_class.php');

		$acs = new WC_ACS_Shipping_Method();
		if (isset($_GET['s']) && $_GET['s'] != '') {
			switch ($_GET['s_type']) {
			case 'view_voucher':
				if (isset($_SESSION['_acs_error'])) {
					_e("<div class=\"notice notice-error is-dismissible\"><p>Άκυρος αριθμός εντολής</p></div>", 'acs-shipping');
					unset($_SESSION['_acs_error']);
					continue;
				}

				if ($_GET['s'] == 0) {
					_e("<div class=\"notice notice-error is-dismissible\"><p>Άκυρος αριθμός εντολής</p></div>", 'acs-shipping');
					echo get_post_meta($order->id, '_acs_voucher_status', false);
					continue;
				}

				$_orders = get_posts(array(
					'numberposts' => - 1,
					'meta_key' => '_acs_voucher_id',
					'meta_value' => $_GET['s'],
					'post_type' => wc_get_order_types() ,
					'post_status' => array_keys(wc_get_order_statuses()) ,
				));
				if (!$_orders) {
					$message = 'Δεν βρέθηκε αποτέλεσμα για το voucher id';
					_e("<div class=\"notice notice-warning is-dismissible\"><p>{$message}</p></div>", 'acs-shipping');
				}
				else {
					foreach($_orders as $o) {
						$printed = get_post_meta($o->ID, '_acs_voucher_printed', true);
						$printed_date = get_post_meta($o->ID, '_acs_voucher_date_printed', true);
						$created_date = get_post_meta($o->ID, '_acs_voucher_date_created', true);
						$pickup_date = get_post_meta($o->ID, '_acs_voucher_date_pickup', true);
						$printed_user = get_post_meta($o->ID, '_acs_voucher_printed_user', true);
						$items = get_post_meta($o->ID, '_acs_voucher_items', true);
						$data.= '<h2>Voucher ' . $_GET['s'] . '</h2>';
						$data.= '<table class="wp-list-table widefat fixed striped posts"><thead><tr><th colspan="2"><h2>';
						if ($printed == '0') {
							$data.= '<a href="admin.php?page=acs-vouchers&s_type=print_voucher&s=' . $_GET['s'] . '&order_id=' . $o->ID . '">Print</a> | <a href="admin.php?page=acs-vouchers&s_type=delete_voucher&s=' . $_GET['s'] . '&order_id=' . $o->ID . '">Delete</a> | <a href="https://www.acscourier.net/el/track-and-trace?p_p_id=ACSCustomersAreaTrackTrace_WAR_ACSCustomersAreaportlet&p_p_lifecycle=1&p_p_state=normal&p_p_mode=view&p_p_col_id=column-4&p_p_col_pos=1&p_p_col_count=3&_ACSCustomersAreaTrackTrace_WAR_ACSCustomersAreaportlet_javax.portlet.action=trackTrace&&generalCode=' . $_GET['s'] . '", target="_blank">Track</a>';
						}
						else {
							$data.= '<a target="_blank" href="admin.php?page=acs-vouchers&s_type=print_voucher&s=' . $_GET['s'] . '&order_id=' . $o->ID . '">View</a> | <a href="admin.php?page=acs-vouchers&s_type=delete_voucher&s=' . $_GET['s'] . '&order_id=' . $o->ID . '">Delete</a> | <a href="https://www.acscourier.net/el/track-and-trace?p_p_id=ACSCustomersAreaTrackTrace_WAR_ACSCustomersAreaportlet&p_p_lifecycle=1&p_p_state=normal&p_p_mode=view&p_p_col_id=column-4&p_p_col_pos=1&p_p_col_count=3&_ACSCustomersAreaTrackTrace_WAR_ACSCustomersAreaportlet_javax.portlet.action=trackTrace&&generalCode=' . $_GET['s'] . '", target="_blank">Track</a>';
						}

						$data.= '</h2></th></tr></thead><tbody>';
						$data.= '<tr><td width="200">Voucher ID:</td><td>' . $_GET['s'] . '</td></tr>';
						$data.= '<tr><td width="200">Voucher Created Date:</td><td>' . $created_date . '</td></tr>';
						$data.= '<tr><td width="200">Voucher Pickup Date:</td><td>' . $pickup_date . '</td></tr>';
						$data.= '<tr><td width="200">Voucher Items:</td><td>' . $items . '</td></tr>';
						if ($printed == '1') {
							$data.= '<tr><td width="200">Voucher Printed Date:</td><td>' . $printed_date . '</td></tr>';
							$data.= '<tr><td width="200">Voucher Printed by User:</td><td>' . $printed_user . '</td></tr>';
						}

						$data.= '<tr><td width="200">Order ID:</td><td>' . $o->ID . '</td></tr>';
						$data.= '<tr><td width="200">Order Name:</td><td>' . $o->post_title . '</td></tr>';
						$data.= '<tr><td width="200">Order Date:</td><td>' . $o->post_date . '</td></tr>';
						$data.= '</tbody></table>';
					}
				}

				$_GET['s'] = '';
				break;

			case 'print_voucher':
				$url = $acs->get_acs_voucher_print_url();
				$url = $url . '&voucherno=' . $_GET['s'];
				$order = new WC_Order($_GET['order_id']);
				$printed = get_post_meta($order->id, '_acs_voucher_printed', true);
				if ($printed == '1') {
					wp_redirect($url);
					exit;
				}
				elseif ($printed == '0') {
					$handle = curl_init($url);
					curl_setopt($handle, CURLOPT_RETURNTRANSFER, TRUE);
					$response = curl_exec($handle);
					$httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
					if ($httpCode == 200) {
						$current_user = wp_get_current_user();
						update_post_meta($order->id, '_acs_voucher_printed', '1');
						update_post_meta($order->id, '_acs_voucher_date_printed', date('Y-m-d G:i:s T'));
						update_post_meta($order->id, '_acs_voucher_printed_user', $current_user->user_login);
						curl_close($handle);
						wp_redirect('admin.php?page=acs-vouchers&s_type=view_voucher&s=' . $_GET['s']);
						exit;
					}
					else {

						_e('<div class="notice notice-warning is-dismissible"><p>Μη έγκυρος αριθμός Voucher</p></div>', 'acs-shipping');
					}

					curl_close($handle);
				}
				else {

					_e('<div class="notice notice-error"><p>Σφάλμα</p></div>', 'acs-shipping');
				}

				break;

			case 'delete_voucher':
				$acs = new WC_ACS_Shipping_Method();
				$order = new WC_Order($_GET['order_id']);
				$res = $acs->delete_voucher($_GET['s']);
				if ($res['error'] != '') {
					$data = $res['error'];
				}
				else {
					update_post_meta($order->id, '_acs_voucher_id', '0');
					update_post_meta($order->id, '_acs_voucher_date_created', '0');
					update_post_meta($order->id, '_acs_voucher_date_printed', '0');
					update_post_meta($order->id, '_acs_voucher_date_pickup', '0');
					update_post_meta($order->id, '_acs_voucher_printed', '0');
					update_post_meta($order->id, '_acs_voucher_status', '0');
					wp_redirect('edit.php?post_type=shop_order');
					exit;
				}

				break;

			case 'tracking':
				$result = $acs->tracking($_GET['s']);
				$order = new WC_Order($_GET['order_id']);

				$_orders = get_posts(array(
					'numberposts' => - 1,
					'meta_key' => '_acs_voucher_id',
					'meta_value' => $_GET['s'],
					'post_type' => wc_get_order_types() ,
					'post_status' => array_keys(wc_get_order_statuses()) ,
				));
				if (!$_orders) {
					$message = 'Δεν βρέθηκε αποτέλεσμα για το voucher id';
					"<div class=\"notice notice-warning is-dismissible\"><p>_e({$message}, 'acs-shipping')</p></div>";
				}
				else {
				    print_r($result);
					foreach($_orders as $o) {
						$order_id = $o->ID;
					}
				    this.acs_tracking_track_order($theid);
				    wp_redirect('https://www.acscourier.net/el/track-and-trace?p_p_id=ACSCustomersAreaTrackTrace_WAR_ACSCustomersAreaportlet&p_p_lifecycle=1&p_p_state=normal&p_p_mode=view&p_p_col_id=column-4&p_p_col_pos=1&p_p_col_count=3&_ACSCustomersAreaTrackTrace_WAR_ACSCustomersAreaportlet_javax.portlet.action=trackTrace&&generalCode=' . $result);

				}

				break;

			case 'create_voucher':
				$o = get_post($_GET['s'], 'ARRAY_A', 'db');
				$status = get_post_status($o['ID']);
				if ($status == 'wc-processing' || $status == 'wc-completed') {
					$params = json_decode(get_post_meta($o['ID'], '_acs_voucher_params', true) , true);
					$res = $acs->create_voucher_admin($_GET['s'], $params);
					wp_redirect('admin.php?page=acs-vouchers&s_type=view_voucher&s=' . $res);
					exit;
				}
				else {

					// echo '<div class="notice notice-warning is-dismissible"><p>Η παραγγελία είναι ' . $status . '</p></div>';

					_e('<div class="notice notice-warning is-dismissible"><p>Η παραγγελία είναι ' . $status . '</p></div>', 'acs-shipping');
				}

				break;

			case 'receipt_list':
				if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $_GET['s'])) {
					if ($res = $acs->receipt_list($_GET['s'])) {
						defined('WP_DEBUG') && true === WP_DEBUG ? print_r($res) : false;
						foreach($res['rows'] as $row) {
							if ($res['message'] == 'Success') {
								$result = $acs->get_MassNumbers($_GET['s']);
								if ($result) {

									// Get existing receipt lists

									foreach($result as $r) {
										$url = $acs->get_acs_voucher_print_url() . '&voucherno=';
										$_orders = get_posts(array(
											'numberposts' => - 1,
											'meta_key' => '_acs_voucher_date_pickup',
											'meta_value' => $r['dateParal'],
											'post_type' => wc_get_order_types() ,
											'post_status' => array_keys(wc_get_order_statuses()) ,
										));
										if ($_orders) {
											$vouchers = array();
											foreach($_orders as $o) {
												$vouchers[] = get_post_meta($o->ID, '_acs_voucher_id', true);
											}

											$url = $url . implode('|', $vouchers);
										}

										$table.= '<tr>
												<td width="200">' . $r['dateParal'] . '</td>
												<td>' . $r['massNumber'] . '</td>
												<td><a target="_blank" href="' . $acs->get_acs_receipts_print_url() . '&MassNumber=' . $r['massNumber'] . '&DateParal=' . $r['dateParal'] . '">Print Receipt List</a></td>
												<td><a target="_blank" href="#"></a></td>
											</tr>';
									}
								}
							}
							else {
								$_orders = get_posts(array(
									'numberposts' => - 1,
									'meta_key' => '_acs_voucher_id',
									'meta_value' => $row,
									'post_type' => wc_get_order_types() ,
									'post_status' => array_keys(wc_get_order_statuses()) ,
								));
								if ($_orders) {
									$total = count($_orders);
									foreach($_orders as $o) {
										$table.= '<tr>
												<td width="200">' . $_GET['s'] . '</td>
												<td>' . $o->ID . '</td>
												<td>' . $row . '</td>
												<td><a href="admin.php?page=acs-vouchers&s_type=view_voucher&s=' . $row . '">Edit Voucher</a></td>
											</tr>';
									}
								}
								else {
									if (isset($res['message'])) {
										echo "<div class=\"notice notice-error is-dismissible\"><p>" . $res['message'] . $row . "</p></div>";
									}
								}
							}
						}

						if ($res['message'] == 'Success') {
							$head = '<h2>Λίστες παραληπτών</h2>';
							$head.= '<table class="wp-list-table widefat fixed striped posts"><thead>';
							$head.= '<tr><th>Ημερομηνία</th><th>Αριθμός λίστας</th><th>&nbsp;</th><th>&nbsp;</th></tr>';
							$head.= '</thead><tbody>';
						}
						else {
							$head = '<h2>Βρέθηκαν ατύπωτα Vouchers</h2>';
							$head.= '<table class="wp-list-table widefat fixed striped posts"><thead>';
							$head.= '<tr><th>Ημερομηνία</th><th>Αριθμός Παραγγελίας</th><th>Αριθμός Voucher</th><th>&nbsp;</th></tr>';
							$head.= '</thead><tbody>';
						}

						$data.= $head . $table;
						$data.= '</tbody></table>';
					}
				}
				else {
					$message = 'Παρακαλώ εισάγετε ημερομηνία με την μορφή YYYY-mm-dd';
					echo '<div class="notice notice-warning is-dismissible"><p>' . $message . '</p></div>';
				}

				break;

			default:
				exit();
				break;
			}
		}

		echo '<div class="wrap">
				<h1>ACS Vouchers</h1>
				<h2 class="screen-reader-text">Filter orders</h2>
				<form method="get">
					<p class="search-box">
						<label class="screen-reader-text" for="post-search-input">Receipt Lists</label>
						<input type="hidden" name="page" value="acs-vouchers" />
						<input type="search" name="s" placeholder="Date: ' . date('Y-m-d') . '"
						 value="' . (isset($_GET['s']) && $_GET['s'] != '' ? $_GET['s'] : '') . '" />
						<select name="s_type">
							<option value="receipt_list" ' . (isset($_GET['s_type']) && $_GET['s_type'] == 'receipt_list' ? "selected=selected" : '') . '>Receipt Lists</option>
						</select>
						<input type="submit" class="button" value="Apply"  />
					</p>
				</form>
			';
		echo $data;
		echo '</div>';
	}

	/**
	 * ACS Vouchers on Orders page
	 */
	function acs_orders_vouchers_column($columns)
	{
		$columns['shipping_method'] = __('ACS Voucher', 'theme_slug');
		return $columns;
	}

	add_filter('manage_edit-shop_order_columns', 'acs_orders_vouchers_column', 11);
	function acs_orders_vouchers_column_value($column)
	{
		global $post;
		$order_factory = new WC_Order_Factory();
		$order = $order_factory->get_order($post->ID);
		$status = get_post_status($order->id);
		$_acs_voucher_id = get_post_meta($order->id, '_acs_voucher_id', true);
		$printed = get_post_meta($order->id, '_acs_voucher_printed', true);
		if ($column == 'shipping_method') {
			$shipping_methods = @array_shift($order->get_shipping_methods());
			$shipping_method = explode(':', $shipping_methods['method_id']);
			if ($shipping_method[0] == 'acs_courier') {
				if (!$_acs_voucher_id || $_acs_voucher_id == '' || $_acs_voucher_id == '0' || $status == 'wc-pending') {
					echo '<a class="acs_admin_voucher" style="color:red;" href="admin.php?page=acs-vouchers&s_type=create_voucher&s=' . $order->id . '">Create Voucher</a><br />';
				}
				else {
					if ($printed == '1') {
						echo '<a class="acs_admin_voucher" style="color:green;" href="admin.php?page=acs-vouchers&s_type=view_voucher&s=' . $_acs_voucher_id . '">View Printed</a><br />';
					}
					else {
						echo '<a class="acs_admin_voucher" style="color:orange;" href="admin.php?page=acs-vouchers&s_type=view_voucher&s=' . $_acs_voucher_id . '">Edit Voucher</a><br />';
					}
				}
			}
			else {
				echo $shipping_method[0];
			}
		}
	}

	add_action('manage_shop_order_posts_custom_column', 'acs_orders_vouchers_column_value', 2);
	
	/**
	 * Bulk Vouchers Print - Shown In Orders Page
	 */
	function mark_register_bulk_action($bulk_actions)
	{
		$bulk_actions['mark_bulk_print'] = 'Μαζική Εκτύπωση Voucher';
		return $bulk_actions;
	}

	add_filter('bulk_actions-edit-shop_order', 'mark_register_bulk_action');
	function mark_bulk_print_process_custom_status()
	{
		if (!isset($_REQUEST['post']) && !is_array($_REQUEST['post'])) return;
		require_once ('inc/acs_shipping_class.php');

		$url = "";
		$vouchers = array();
		$acs = new WC_ACS_Shipping_Method();
		$url = $acs->get_acs_voucher_print_url() . '&voucherno=';
		foreach($_REQUEST['post'] as $order_id) {
			$order = new WC_Order($order_id);
			get_post_meta($order_id, '_acs_voucher_id', true) ? $vouchers[] = get_post_meta($order_id, '_acs_voucher_id', true) : false;
		}

		$url = $url . implode('%7C', $vouchers);
		wp_redirect($url);
		exit;
	}

	add_action('admin_action_mark_bulk_print', 'mark_bulk_print_process_custom_status');
	/**
	 * Display Voucher Data on Admins - Shown In Admin on Orders
	 */
	function acs_display_order_data_in_admin($order)
	{ ?>
   </br> 
    <div class="wide">
 
        </br><h4><?php
		_e('Στοιχεία ACS Voucher', 'woocommerce'); ?><a href="#" class="edit_address"><?php
		_e('Edit', 'woocommerce'); ?></a></h4>
        <div class="address">
        <?php
		$obj = json_decode(get_post_meta($order->id, '_acs_voucher_params', true));
	   //echo '<p class="form-field _billing_company_field"><strong>' . __('Αριθμός Vouchers') . ':</strong>' . get_post_meta($order->id, '_acs_voucher_items', true) . '</p>';
		//echo '<p class="form-field _billing_company_field"><strong>' . __('Παράδοση Σάββατο') . ':</strong>' . get_post_meta($order->id, '_acs_voucher_pickup_saturday', true) . '</p>';
		//echo '<p class="form-field _billing_company_field"><strong>' . __('Παράδοση Ρεσεψιόν') . ':</strong>' . get_post_meta($order->id, '_acs_voucher_pickup_reception', true) . '</p>';
		//echo '<p class="form-field _billing_company_field"><strong>' . __( 'Ποσό Αντικαταβολής' ) . ':</strong>' . $antikatavoli . '</p>';

?>
        </div>
        <div class="edit_address">
            <?php
		woocommerce_wp_text_input(array(
			'id' => '_acs_voucher_params',
			'label' => __('Παράμετροι Voucher:') ,
			'wrapper_class' => '_acs_voucher_params'
		)); ?>
            <!--<?php
		woocommerce_wp_text_input(array(
			'id' => '_acs_voucher_items',
			'label' => __('Αριθμός Vouchers:') ,
			'wrapper_class' => '_acs_voucher_items'
		)); ?>
            <?php
		woocommerce_wp_text_input(array(
			'id' => '_acs_voucher_pickup_saturday',
			'label' => __('Παράδοση Σάββατο:') ,
			'wrapper_class' => '_acs_voucher_pickup_saturday'
		)); ?>
            <?php
		woocommerce_wp_text_input(array(
			'id' => '_acs_voucher_pickup_reception',
			'label' => __('Παράδοση Ρεσεψιόν:') ,
			'wrapper_class' => '_acs_voucher_pickup_reception'
		)); ?>
           -->
        </div>
    </div>
<?php
	}

	 add_action( 'woocommerce_admin_order_data_after_order_details', 'acs_display_order_data_in_admin' ) ;

	/**
	 * Save Voucher Params on Admin - Shown In Admin on Orders
	 */
	function acs_save_extra_details($post_id, $post)
	{
		update_post_meta($post_id, '_acs_voucher_params', wc_clean($_POST['_acs_voucher_params']));

		// update_post_meta( $post_id, '_acs_voucher_items', wc_clean( $_POST[ '_acs_voucher_items' ] ) );
		// update_post_meta( $post_id, '_acs_voucher_pickup_saturday', wc_clean( $_POST[ '_acs_voucher_pickup_saturday' ] ) );
		// update_post_meta( $post_id, '_acs_voucher_pickup_reception', wc_clean( $_POST[ '_acs_voucher_pickup_reception' ] ) );

	}

	 add_action( 'woocommerce_process_shop_order_meta', 'acs_save_extra_details', 45, 2 );
	 add_action( 'woocommerce_mark_order_status', 'acs_save_extra_details', 50, 2 ) ;
	 
/**
 * Extend - Make acs tracking order code 
 * --------------------------
 *  
 *
 *
 */

function acs_tracking_track_order($theid) {
	require_once ('inc/acs_ws.php');

	global $wpdb;
	$acs = new WS_SOAP_ACS;
	$order_id = empty($_REQUEST['orderid']) ? $theid : esc_attr($_REQUEST['orderid']);
	$v = $wpdb->get_row("SELECT post_id FROM $wpdb->postmeta WHERE meta_key = '_order_number' and meta_value = " . $order_id);
	$params['pod_no'] = get_post_meta($v->post_id, '_acs_voucher_id', true);

	$res = $acs->tracking_info($params);
	if ($res['diak_dateparal'] != '')
		{
		echo '<div class="track-order"><h2>Κατάσταση αποστολής</h2>';
		echo '<table class="shop_table order_details">
				<thead>
					<tr>
						<th>Παραλαβή</th>
						<th>Ημερομηνία</th>
						<th>Παράδοση</th>
						<th>Σημειώσεις</th>
					</tr>
				</thead>
				<tbody>';
		echo '<tr class="order_item">
				<td class="product-name">' . $res['sa_perigr_paral'] . '</td>
				<td class="product-name">' . $res['diak_dateparal'] . '</td>
				<td class="product-name">' . $res['sa_perigr_dest'] . '</td>
				<td class="product-name">' . $res['notes'] . '</td>
			</tr>';
		echo '</tbody></table></div>';
		}

	$res0 = $acs->tracking($params);
	if (count($res0['response']) > 0)
		{
		echo '<div class="track-order"><h2>Σημεία ελέγχου</h2>';
		echo '<table class="shop_table order_details">
				<thead>
					<tr>
						<th>Ημερομηνία</th>
						<th>Περιγραφή</th>
						<th>Σημείο ελέγχου</th>
						<th>Σημειώσεις</th>
					</tr>
				</thead>
				<tbody>';
		foreach($res0['response'] as $res)
			{
			echo '<tr class="order_item">
				<td class="product-name">' . date('Y-m-d', strtotime($res['date_time'])) . '</td>
				<td class="product-name">' . $res['description'] . '</td>
				<td class="product-name">' . $res['check_point'] . '</td>
				<td class="product-name">' . $res['remarks'] . '</td>
			</tr>';
			}

		echo '</tbody></table></div>';
		}

	return;
	}

add_action('woocommerce_track_order', 'acs_tracking_track_order', 1);

function disable_shipping_calc_on_cart( $show_shipping ) {
    if( is_cart() ) {
        return false;
    }
    return $show_shipping;
}
add_filter( 'woocommerce_cart_ready_to_calc_shipping', 'disable_shipping_calc_on_cart', 99 );

}