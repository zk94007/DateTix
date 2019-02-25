<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');
class Account extends MY_Controller {
	var $language_id = '1';
	var $user_id = '';
	public function __construct() {
		parent::__construct();

		//Load Model
		$this -> load -> model('model_user');
		$this -> load -> model('model_account');
		$this -> load -> model('general_model', 'general');

		//Check User is Looged In or not
		if ($this -> user_id = $this -> session -> userdata('user_id')) {
			$this -> user_id = $this -> session -> userdata('user_id');
			$this -> language_id = $this -> session -> userdata('sess_language_id');
		} else {
			//Also works in ajax call
			echo '<script type="text/javascript">window.location.href = "' . base_url() . '"</script>';
		}

		$this -> load -> library('merchant');
		$this -> merchant -> load('paypal_express');
		$settings = array('username' => $this -> config -> item('username'), 'password' => $this -> config -> item('password'), 'signature' => $this -> config -> item('signature'), 'test_mode' => $this -> config -> item('test_mode'));
		$this -> merchant -> initialize($settings);
		
		$params['return_url'] = base_url() . 'account/upgrade_membership';
		$params['cancel_url'] = base_url() . url_city_name() . '/upgrade-account.html';
		$params['notify_url'] = base_url() . "upgrade_account/notify";
		
		$this -> load -> library('paypal_subscription',$params);
		
	}

	/**
	 * Index Function :: Redirect to Upgrade Account.
	 * @access public
	 * @author Rajnish Savaliya
	 */
	public function index() {
		$this -> get_more_tickets();
	}
	
	/**
	 * Upgrade Function :: Redirect to Upgrade Account.
	 * @access public
	 * @author Rajnish Savaliya
	 */
	public function upgrade() {
		$this -> general -> set_table('user');
		$user_data = $this -> general -> get('user.*,
		CASE
					WHEN
						birth_date != "0000-00-00" 	
					THEN 
						TIMESTAMPDIFF(YEAR,`birth_date`,CURDATE())
				END as user_age
		', array('user_id' => $this -> user_id));

		$user = $user_data['0'];

		if ($return_to = $this -> input -> get('return_to')) {
			if ($tab = $this -> input -> get('tab')) {
				$return_to .= '#' . $tab;
			}

			$this -> session -> set_userdata('return_url', $return_to);
		}

		if ($post_data = $this -> input -> post()) {
			
			
			$params["BILLINGPERIOD"] = "Day";
			$params["BILLINGFREQUENCY"] = "1";
			$params['currencyID'] = isset($post_data['currency']) ? trim($post_data['currency']) : 'USD';
			$params["AMT"] = isset($post_data['amount']) ? preg_replace("/[^0-9]/", "", $post_data['amount']) : '';;
			$params["DESC"] = isset($post_data['plan_name']) ? $post_data['plan_name'] : 'Plan';
			
			
			$this -> session -> set_userdata('post_data', $post_data);			
			$this -> paypal_subscription -> subscribe($params);
			redirect($this -> paypal_subscription -> get_checkout_url());		
		}

		for ($i = date('Y'); $i < (date('Y') + 20); $i++) {
			$year[$i] = $i;
		}

		$data['user_data'] = $user;
		$data['year'] = $year;
		$data['month'] = $this -> model_user -> get_month();
		$data['membership_options'] = $this -> model_account -> get_member_options($this -> language_id);
		$data['user_photos'] = $this -> _my_feature_users_photo();

		$selected_membership_options = array('1', '2', '3', '4', '5', '6');
		$this -> session -> set_userdata('apply_membership_discount', 'no');
		$this -> session -> set_userdata('user_membership_option', $selected_membership_options);
		$this -> session -> set_userdata('default_selected_key', '2');

		$data['ticket_packages'] = $this -> get_membership_package($data['membership_options'], $selected_membership_options);
		$data['user_membership_options'] = $selected_membership_options;

		$data['page_title'] = translate_phrase('Upgrade Account');
		$data['page_name'] = 'user/account/upgrade';
		$this -> load -> view('template/editProfileTemplate', $data);
	}

	/**
	 * Save Upgrade Membership data and redirect to paypal
	 * @access public
	 * @author Rajnish Savaliya
	 */
	public function upgrade_membership() {
		if ($post_data = $this -> session -> userdata('post_data')) {
			
			
			$params["BILLINGPERIOD"] = "Day";
			$params["BILLINGFREQUENCY"] = "1";
			$params['currencyID'] = isset($post_data['currency']) ? trim($post_data['currency']) : 'USD';
			$params["AMT"] = isset($post_data['amount']) ? preg_replace("/[^0-9]/", "", $post_data['amount']) : '';;
			$params["DESC"] = isset($post_data['plan_name']) ? $post_data['plan_name'] : 'Plan';
			
			$params['return_url'] = base_url() . 'account/upgrade_membership';
			$params['cancel_url'] = base_url() . url_city_name() . '/upgrade-account.html';
			$params['notify_url'] = base_url() . "upgrade_account/notify";
			
			
			$this -> paypal_subscription -> subscribe($params);
			$response = $this->paypal_subscription -> start_subscription();
			
			//If Profile Active then store details
			if ($response['PROFILESTATUS'] == 'ActiveProfile') {
				
				$txn_id = $response['PROFILEID'];
				
				$profile_details = $this->paypal_subscription -> get_profile_details($txn_id);
				
				//Order Entry
				$user_order['user_id'] = $this -> user_id;
				$user_order['status'] = '1';
				$user_order['transaction_id'] = $txn_id;
				$user_order['order_currency_id'] = isset($post_data['currency_id']) ? $post_data['currency_id'] : '';
				$user_order['order_time'] = SQL_DATETIME;
				$user_order['order_amount'] = $params['AMT'];
				$user_order['order_membership_options'] = isset($post_data['membership_options_id']) ? $post_data['membership_options_id'] : '';
				$user_order['order_membership_duration_months'] = isset($post_data['no_of_unit']) ? $post_data['no_of_unit'] : '';

				//insert Order Record
				$this -> general -> set_table('user_subscription');
				if ($this -> general -> save($user_order)) {
					$this -> session -> unset_userdata('post_data');

					$order_membership_options = explode(',', $user_order['order_membership_options']);
					$flag = 0;
					if ($order_membership_options) {
						$this -> general -> set_table('user_membership_option');
						$order_membership_options_data['user_id'] = $this -> user_id;
						foreach ($order_membership_options as $value) {
							$order_membership_options_data['membership_option_id'] = $value;
							if ($user_member_data = $this -> general -> get("", $order_membership_options_data)) {
								if ($user_member_data['0']['expiry_date'] && $user_member_data['0']['expiry_date'] >= date('Y-m-d')) {
									$order_membership_options_update_data['expiry_date'] = date('Y-m-d', strtotime($user_order['order_membership_duration_months'] . " month", strtotime($user_member_data['0']['expiry_date'])));
								} else {
									$order_membership_options_update_data['expiry_date'] = date('Y-m-d', strtotime($user_order['order_membership_duration_months'] . " month"));
								}
								if ($this -> general -> update($order_membership_options_update_data, $order_membership_options_data)) {
									$flag = 1;
								}
							} else {
								$order_membership_options_data['expiry_date'] = date('Y-m-d', strtotime($user_order['order_membership_duration_months'] . " month"));
								if ($this -> general -> save($order_membership_options_data)) {
									$flag = 1;
								}
							}
						}
					}

					if ($flag) {
						$this -> session -> set_flashdata('paypal', $params['DESC'] . translate_phrase(' has been applied to your account, Enjoy the dating!'));
					}
				}
			} else {
				$this -> session -> set_flashdata('paypal', $params['DESC'] . translate_phrase('Sorry Transaction is failed. Please try again!'));
			}
		}
		if ($return_url = $this -> session -> userdata('return_url')) {
			$this -> session -> unset_userdata('return_url');
			redirect('/' . url_city_name() . '/' . $return_url);
		} else {
			redirect('/' . url_city_name() . '/upgrade-account.html');
		}
	}
	
	public function notify() {

		$raw_post_data = file_get_contents('php://input');
		$raw_post_array = explode('&', $raw_post_data);
		$myPost = array();
		foreach ($raw_post_array as $keyval) {
			$keyval = explode('=', $keyval);
			if (count($keyval) == 2)
				$myPost[$keyval[0]] = urldecode($keyval[1]);
		}
		
		$data['ipn_id'] = $myPost['ipn_track_id'];
		$data['response'] = $raw_post_data;
		$data['ddate_time'] = DATE_TIME;
		
		
		$this -> general -> set_table('user_subscription_details');
		$this -> general -> save($data);

		$this -> general -> set_table('user_subscription');
		if (isset($myPost['subscr_id']) && $myPost['subscr_id'] != "") {
			$up_con['transaction_id'] = $myPost['subscr_id'];
			if ($myPost['payment_status'] == 'Completed') {
				$up_data['payment_status'] = '1';
			} else {
				$up_data['payment_status'] = '0';
			}
		} elseif (isset($myPost['mp_id']) && $myPost['mp_id'] != "") {
			$up_con['transaction_id'] = $myPost['mp_id'];
			if ($myPost['payment_status'] == 'Completed') {
				$up_data['status'] = '1';
			} else {
				$up_data['status'] = '0';
			}
		} else {
			$up_con['transaction_id'] = $myPost['recurring_payment_id'];

			if ($myPost['profile_status'] == 'Active') {
				$up_data['status'] = '1';
			} else {
				$up_data['status'] = '0';
			}
		}
		$up_con['transaction_id'] = $myPost['recurring_payment_id'];
		$this -> general -> update($up_data, $up_con);

		// read the IPN message sent from PayPal and prepend 'cmd=_notify-validate'
		$req = 'cmd=_notify-validate';
		if (function_exists('get_magic_quotes_gpc')) {
			$get_magic_quotes_exists = true;
		}
		foreach ($myPost as $key => $value) {
			if ($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1) {
				$value = urlencode(stripslashes($value));
			} else {
				$value = urlencode($value);
			}
			$req .= "&$key=$value";
		}

		// Step 2: POST IPN data back to PayPal to validate

		$ch = curl_init('https://www.paypal.com/cgi-bin/webscr');
		curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));

		// In wamp-like environments that do not come bundled with root authority certificates,
		// please download 'cacert.pem' from "http://curl.haxx.se/docs/caextract.html" and set
		// the directory path of the certificate as shown below:
		// curl_setopt($ch, CURLOPT_CAINFO, dirname(__FILE__) . '/cacert.pem');
		if (!($res = curl_exec($ch))) {
			// error_log("Got " . curl_error($ch) . " when processing IPN data");
			curl_close($ch);
			exit ;
		}
		curl_close($ch);
	}
	/**
	 * Upgrade Function :: Redirect to Upgrade Account.
	 * @access public
	 * @author Rajnish Savaliya
	 */
	public function bkp_upgrade() {
		$this -> general -> set_table('user');
		$user_data = $this -> general -> get('user.*,
		CASE
					WHEN
						birth_date != "0000-00-00" 	
					THEN 
						TIMESTAMPDIFF(YEAR,`birth_date`,CURDATE())
				END as user_age
		', array('user_id' => $this -> user_id));

		$user = $user_data['0'];

		if ($return_to = $this -> input -> get('return_to')) {
			if ($tab = $this -> input -> get('tab')) {
				$return_to .= '#' . $tab;
			}

			$this -> session -> set_userdata('return_url', $return_to);
		}

		if ($post_data = $this -> input -> post()) {
			$params['amount'] = isset($post_data['amount']) ? preg_replace("/[^0-9]/", "", $post_data['amount']) : '';
			$params['currency'] = isset($post_data['currency']) ? trim($post_data['currency']) : 'USD';
			$params['description'] = isset($post_data['plan_name']) ? $post_data['plan_name'] : 'Plan';
			$params['return_url'] = base_url() . 'account/upgrade_membership';
			$params['cancel_url'] = base_url() . url_city_name() . '/upgrade-account.html';

			$this -> session -> set_userdata('post_data', $post_data);
			$response = $this -> merchant -> purchase($params);
			if ($response -> status() == Merchant_response::FAILED) {
				$this -> session -> set_flashdata('paypal', translate_phrase('Gatway Error - ' . $response -> message()));
				redirect($params['cancel_url']);
			}
		}

		for ($i = date('Y'); $i < (date('Y') + 20); $i++) {
			$year[$i] = $i;
		}

		$data['user_data'] = $user;
		$data['year'] = $year;
		$data['month'] = $this -> model_user -> get_month();
		$data['membership_options'] = $this -> model_account -> get_member_options($this -> language_id);
		$data['user_photos'] = $this -> _my_feature_users_photo();

		$selected_membership_options = array('1', '2', '3', '4', '5', '6');
		$this -> session -> set_userdata('apply_membership_discount', 'no');
		$this -> session -> set_userdata('user_membership_option', $selected_membership_options);
		$this -> session -> set_userdata('default_selected_key', '2');

		$data['ticket_packages'] = $this -> get_membership_package($data['membership_options'], $selected_membership_options);
		$data['user_membership_options'] = $selected_membership_options;

		$data['page_title'] = translate_phrase('Upgrade Account');
		$data['page_name'] = 'user/account/upgrade';
		$this -> load -> view('template/editProfileTemplate', $data);
	}

	/**
	 * Save Upgrade Membership data and redirect to paypal
	 * @access public
	 * @author Rajnish Savaliya
	 */
	public function bkp_upgrade_membership() {
		if ($post_data = $this -> session -> userdata('post_data')) {
			$params['amount'] = isset($post_data['amount']) ? preg_replace("/[^0-9]/", "", $post_data['amount']) : '';
			$params['currency'] = isset($post_data['currency']) ? trim($post_data['currency']) : 'USD';
			$params['description'] = isset($post_data['plan_name']) ? $post_data['plan_name'] : 'Plan';
			$params['return_url'] = base_url() . 'account/success_payment';
			$params['cancel_url'] = base_url() . url_city_name() . '/get-more-tickets.html';

			$response = $this -> merchant -> purchase_return($params);

			if ($response -> status() == Merchant_response::COMPLETE) {
				//Order Entry
				$user_order['user_id'] = $this -> user_id;
				$user_order['order_currency_id'] = isset($post_data['currency_id']) ? $post_data['currency_id'] : '';
				$user_order['order_time'] = SQL_DATETIME;
				$user_order['order_amount'] = $params['amount'];
				$user_order['order_membership_options'] = isset($post_data['membership_options_id']) ? $post_data['membership_options_id'] : '';
				$user_order['order_membership_duration_months'] = isset($post_data['no_of_unit']) ? $post_data['no_of_unit'] : '';

				//insert Order Record
				$this -> general -> set_table('user_order');
				if ($this -> general -> save($user_order)) {
					$this -> session -> unset_userdata('post_data');

					$order_membership_options = explode(',', $user_order['order_membership_options']);
					$flag = 0;
					if ($order_membership_options) {
						$this -> general -> set_table('user_membership_option');
						$order_membership_options_data['user_id'] = $this -> user_id;
						foreach ($order_membership_options as $value) {
							$order_membership_options_data['membership_option_id'] = $value;
							if ($user_member_data = $this -> general -> get("", $order_membership_options_data)) {
								if ($user_member_data['0']['expiry_date'] && $user_member_data['0']['expiry_date'] >= date('Y-m-d')) {
									$order_membership_options_update_data['expiry_date'] = date('Y-m-d', strtotime($user_order['order_membership_duration_months'] . " month", strtotime($user_member_data['0']['expiry_date'])));
								} else {
									$order_membership_options_update_data['expiry_date'] = date('Y-m-d', strtotime($user_order['order_membership_duration_months'] . " month"));
								}
								if ($this -> general -> update($order_membership_options_update_data, $order_membership_options_data)) {
									$flag = 1;
								}
							} else {
								$order_membership_options_data['expiry_date'] = date('Y-m-d', strtotime($user_order['order_membership_duration_months'] . " month"));
								if ($this -> general -> save($order_membership_options_data)) {
									$flag = 1;
								}
							}
						}
					}

					if ($flag) {
						$this -> session -> set_flashdata('paypal', $params['description'] . translate_phrase(' has been applied to your account, Enjoy the dating!'));
					}
				}
			} else {
				$this -> session -> set_flashdata('paypal', $params['description'] . translate_phrase('Sorry Transaction is failed. Please try again!'));
			}
		}
		if ($return_url = $this -> session -> userdata('return_url')) {
			$this -> session -> unset_userdata('return_url');
			redirect('/' . url_city_name() . '/' . $return_url);
		} else {
			redirect('/' . url_city_name() . '/upgrade-account.html');
		}
	}

	/**
	 * Get More Tickets :: Purchase More Tickets Account.
	 * @access public
	 * @author Rajnish Savaliya
	 */
	public function get_more_tickets() {

		$this -> general -> set_table('user');
		$user_data = $this -> general -> get('user.*,
		CASE
					WHEN
						birth_date != "0000-00-00" 	
					THEN 
						TIMESTAMPDIFF(YEAR,`birth_date`,CURDATE())
				END as user_age
		', array('user_id' => $this -> user_id));

		$user = $user_data['0'];

		if ($return_to = $this -> input -> get('return_to')) {
			if ($tab = $this -> input -> get('tab')) {
				$return_to .= '#' . $tab;
			}

			$this -> session -> set_userdata('return_url', $return_to);
		}

		if ($post_data = $this -> input -> post()) {
			$params['amount'] = isset($post_data['amount']) ? preg_replace("/[^0-9]/", "", $post_data['amount']) : '';
			$params['currency'] = isset($post_data['currency']) ? trim($post_data['currency']) : 'USD';

			$params['description'] = isset($post_data['plan_name']) ? $post_data['plan_name'] : 'Plan';
			$params['return_url'] = base_url() . 'account/success_payment';
			$params['cancel_url'] = base_url() . url_city_name() . '/get-more-tickets.html';

			$this -> session -> set_userdata('post_data', $post_data);
			$response = $this -> merchant -> purchase($params);
			if ($response -> status() == Merchant_response::FAILED) {
				$this -> session -> set_flashdata('paypal', translate_phrase('Gatway Error - ' . $response -> message()));
				redirect($params['cancel_url']);
			}
		}

		for ($i = date('Y'); $i < (date('Y') + 20); $i++) {
			$year[$i] = $i;
		}

		$this -> session -> set_userdata('apply_ticket_package_discount', 'no');
		$data['user_data'] = $user;
		$data['year'] = $year;
		$data['month'] = $this -> model_user -> get_month();
		$data['ticket_packages'] = $this -> get_ticket_package();
		$data['page_title'] = translate_phrase('Get More Tickets');
		$data['user_photos'] = $this -> _my_feature_users_photo();
		$data['page_name'] = 'user/account/get_more_tickets';

		$this -> load -> view('template/editProfileTemplate', $data);
	}

	/**
	 * Apply_discount :: Apply Discount on package Price.[Get More Ticket Page]
	 * @access public
	 * @author Rajnish Savaliya
	 */
	public function apply_discount() {
		$this -> session -> set_userdata('apply_ticket_package_discount', 'yes');
		$data['ticket_packages'] = $this -> get_ticket_package();
		$this -> load -> view('user/account/load_packages', $data);
	}

	/**
	 * Calculate Ticket Packages based on discount.
	 * @access public
	 * @author Rajnish Savaliya
	 */
	public function get_ticket_package() {
		$city_id = $this -> session -> userdata('sess_city_id');
		$fields = array('ct.description as city_description', 'prvnce.description as province_description', 'cntry.description as country_description', 'cntry.country_code', 'cntry.flag_url', 'crncy.*', 'crncy.description as currency_description', );

		$from = 'city as ct';
		$joins = array('province as prvnce' => array('ct.province_id = prvnce.province_id', 'LEFT'), 'country as cntry' => array('prvnce.country_id = cntry.country_id', 'LEFT'), 'currency as crncy' => array('cntry.currency_id = crncy.currency_id ', 'LEFT'));

		$where['ct.city_id'] = $city_id;

		$where['ct.display_language_id'] = $this -> language_id;
		$where['prvnce.display_language_id'] = $this -> language_id;
		$where['cntry.display_language_id'] = $this -> language_id;
		$where['crncy.display_language_id'] = $this -> language_id;

		$currency_id = '6';
		$currency_rate = '1';
		$currency_name = 'HKD';

		if ($temp = $this -> model_user -> multijoins($fields, $from, $joins, $where, '', 'city_description asc')) {
			$currency_id = $temp['0']['currency_id'];
			$currency_rate = $temp['0']['rate'];
			$currency_name = $temp['0']['currency_description'];
		}

		$ticket_packages = $this -> model_account -> get_packages();
		if ($ticket_packages) {
			//Code By Micheal & Percentage : by Rajnish
			//			foreach ($ticket_packages as $key=>$package) {
			//				$original_price = round($ticket_packages[0]['per_date_price'] * $currency_rate) * $ticket_packages[$key]['name'];
			//				$actual_price = round($ticket_packages[$key]['per_date_price'] * $currency_rate) * $ticket_packages[$key]['name'];
			//				$ticket_packages[$key]['save_amount'] =  $original_price - $actual_price ;
			//				$ticket_packages[$key]['save_per'] = round(100-($actual_price*100)/ $original_price);
			//			}
			//end

			foreach ($ticket_packages as $key => $package) {

				$ticket_packages[$key]['currency_id'] = $currency_id;
				$ticket_packages[$key]['currency'] = $currency_name;
				$ticket_packages[$key]['per_date_price'] = round($ticket_packages[$key]['per_date_price'] * $currency_rate);
				$ticket_packages[$key]['total'] = $ticket_packages[$key]['per_date_price'] * $ticket_packages[$key]['name'];
				$original_price = $ticket_packages[0]['per_date_price'] * $ticket_packages[$key]['name'];
				$actual_price = $ticket_packages[$key]['per_date_price'] * $ticket_packages[$key]['name'];
				$ticket_packages[$key]['save_amount'] = $original_price - $actual_price;
				$ticket_packages[$key]['save_per'] = round(100 - ($actual_price * 100) / $original_price);
			}
		}
		if ($this -> session -> userdata('apply_ticket_package_discount') == 'yes') {
			//APPLY 30% discount
			$discount = 0.7;

			if ($ticket_packages) {
				foreach ($ticket_packages as $key => $package) {

					if ($package['per_date_price']) {
						$package['per_date_price'] = round($package['per_date_price'] * $discount);
					}
					if ($package['total']) {
						$package['total'] = $package['per_date_price'] * $package['name'];
					}

					//Offer Text
					if ($package['save_amount']) {
						$package['save_amount'] = $ticket_packages[0]['per_date_price'] * $package['name'] - $package['per_date_price'] * $package['name'];
					}

					$ticket_packages[$key] = $package;
				}
			}
		}
		//echo "<pre>";print_r($ticket_packages);exit;
		return $ticket_packages;
	}

	/**
	 * success_payment :: Callback function of Paypal Response - Insert DateTickets based on successfull transaction.
	 * @return : Get More Tickets Page
	 * @param purchase_params [Session Variable (set in paypal call)]
	 * @access public
	 * @author Rajnish Savaliya
	 */
	public function success_payment() {
		if ($post_data = $this -> session -> userdata('post_data')) {
			$params['amount'] = isset($post_data['amount']) ? preg_replace("/[^0-9]/", "", $post_data['amount']) : '';
			$params['currency'] = isset($post_data['currency']) ? trim($post_data['currency']) : 'USD';

			$params['description'] = isset($post_data['plan_name']) ? $post_data['plan_name'] : 'Plan';
			$params['return_url'] = base_url() . 'account/success_payment';
			$params['cancel_url'] = base_url() . url_city_name() . '/get-more-tickets.html';

			$response = $this -> merchant -> purchase_return($params);
			if ($response -> status() == Merchant_response::COMPLETE) {
				//Order Entry
				$user_order['user_id'] = $this -> user_id;
				$user_order['order_currency_id'] = isset($post_data['currency_id']) ? $post_data['currency_id'] : '';
				$user_order['order_time'] = SQL_DATETIME;
				$user_order['order_amount'] = $params['amount'];
				$user_order['order_num_date_tix'] = isset($post_data['no_of_unit']) ? $post_data['no_of_unit'] : '';

				//insert Order Record
				$this -> general -> set_table('user_order');
				if ($this -> general -> save($user_order)) {
					$this -> session -> unset_userdata('post_data');

					$this -> general -> set_table('user');
					$user_data = $this -> general -> get("", array('user_id' => $this -> user_id));
					$user = $user_data['0'];

					//Update User Tickets
					$update_data['num_date_tix'] = $user['num_date_tix'] + $user_order['order_num_date_tix'];
					if ($this -> general -> update($update_data, array('user_id' => $this -> user_id))) {
						$this -> session -> set_flashdata('paypal', $params['description'] . translate_phrase(' tickets have been credited to your account, Enjoy the dating!'));
					}
				}
			} else {
				$this -> session -> set_flashdata('paypal', $params['description'] . translate_phrase('Sorry Transaction is failed. Please try again!'));
			}
		}
		if ($return_url = $this -> session -> userdata('return_url')) {
			$this -> session -> unset_userdata('return_url');
			redirect('/' . url_city_name() . '/' . $return_url);
		} else {
			redirect('/' . url_city_name() . '/get-more-tickets.html');
		}
	}

	/**
	 * apply_discount :: Apply Discount on Membershipt Price.[Upgrade Account Page]
	 * @access public
	 * @author Rajnish Savaliya
	 */
	public function apply_membership_discount() {
		$this -> session -> set_userdata('apply_membership_discount', 'yes');
		//APPLY 30% discount
		$data['membership_options'] = $this -> model_account -> get_member_options($this -> language_id);
		$selected_membership_options = $this -> session -> userdata('user_membership_option');
		$data['ticket_packages'] = $this -> get_membership_package($data['membership_options'], $selected_membership_options);
		$data['user_membership_options'] = $selected_membership_options;
		$data['ticket_packages'] = $this -> get_membership_package($data['membership_options'], $selected_membership_options);
		$this -> load -> view('user/account/load_membership_packages', $data);
	}

	/**
	 * Calculate Premium Membership Packages based on discount, selected member option.
	 * @access public
	 * @author Rajnish Savaliya
	 */

	public function get_membership_package($membership_options, $selected_membership_options) {
		$city_id = $this -> session -> userdata('sess_city_id');
		$fields = array('ct.description as city_description', 'prvnce.description as province_description', 'cntry.description as country_description', 'cntry.country_code', 'cntry.flag_url', 'crncy.*', 'crncy.description as currency_description', );

		$from = 'city as ct';
		$joins = array('province as prvnce' => array('ct.province_id = prvnce.province_id', 'LEFT'), 'country as cntry' => array('prvnce.country_id = cntry.country_id', 'LEFT'), 'currency as crncy' => array('cntry.currency_id = crncy.currency_id ', 'LEFT'));

		$where['ct.city_id'] = $city_id;

		$where['ct.display_language_id'] = $this -> language_id;
		$where['prvnce.display_language_id'] = $this -> language_id;
		$where['cntry.display_language_id'] = $this -> language_id;
		$where['crncy.display_language_id'] = $this -> language_id;

		$currency_id = '6';
		$currency_rate = '1';
		$currency_name = 'HKD';

		if ($temp = $this -> model_user -> multijoins($fields, $from, $joins, $where, '', 'city_description asc')) {
			$currency_id = $temp['0']['currency_id'];
			$currency_rate = $temp['0']['rate'];
			$currency_name = $temp['0']['currency_description'];
		}

		$ticket_packages = $this -> model_account -> get_subscription_packages();
		$month_1_total = 0;
		$month_3_total = 0;
		$month_6_total = 0;
		$month_12_total = 0;

		if ($membership_options) {
			foreach ($membership_options as $option) {
				if (in_array($option['membership_option_id'], $selected_membership_options)) {
					$month_1_total += $option['price_1month'];
					$month_3_total += $option['price_3month'];
					$month_6_total += $option['price_6month'];
					$month_12_total += $option['price_12month'];
				}
			}
		}
		if ($ticket_packages) {
			foreach ($ticket_packages as $key => $package) {
				$ticket_packages[$key]['currency_id'] = $currency_id;
				$ticket_packages[$key]['currency'] = $currency_name;

				if ($package['name'] == '1') {
					$ticket_packages[$key]['per_month_price'] = $month_1_total;
				}

				if ($package['name'] == '3') {
					$ticket_packages[$key]['per_month_price'] = $month_3_total;
				}

				if ($package['name'] == '6') {
					$ticket_packages[$key]['per_month_price'] = $month_6_total;
				}

				if ($package['name'] == '12') {
					$ticket_packages[$key]['per_month_price'] = $month_12_total;
				}

				$ticket_packages[$key]['per_month_price'] = round($ticket_packages[$key]['per_month_price'] * $currency_rate);
				$ticket_packages[$key]['total'] = $ticket_packages[$key]['per_month_price'] * $ticket_packages[$key]['name'];
			}

			foreach ($ticket_packages as $key => $package) {
				$original_price = $ticket_packages[0]['per_month_price'] * $ticket_packages[$key]['name'];
				$actual_price = $ticket_packages[$key]['per_month_price'] * $ticket_packages[$key]['name'];
				$ticket_packages[$key]['save_amount'] = $original_price - $actual_price;
				$ticket_packages[$key]['save_per'] = round(100 - ($actual_price * 100) / $original_price);
			}
		}

		if ($this -> session -> userdata('apply_membership_discount') == 'yes') {
			//APPLY 30% discount
			$discount = 0.7;

			if ($ticket_packages) {
				foreach ($ticket_packages as $key => $package) {

					if ($package['per_month_price']) {
						$package['per_month_price'] = round($package['per_month_price'] * $discount);
					}
					if ($package['total']) {
						$package['total'] = $package['per_month_price'] * $package['name'];
					}

					//Offer Text
					if ($package['save_amount']) {
						$package['save_amount'] = $ticket_packages[0]['per_month_price'] * $package['name'] - $package['per_month_price'] * $package['name'];
					}

					$ticket_packages[$key] = $package;
				}

			}
		}
		return $ticket_packages;
	}

	/**
	 * [AjaxCALL] :: Fetch user clicked membership value and save in session for future use
	 * @access public
	 * @author Rajnish Savaliya
	 */
	public function apply_membership_option() {
		$data['membership_options'] = $this -> model_account -> get_member_options($this -> language_id);
		if ($post = $this -> input -> post('membership_options_id')) {
			$selected_membership_options = explode(',', $post);
			$this -> session -> set_userdata('user_membership_option', $selected_membership_options);
		} else {
			$selected_membership_options = array();
		}

		$data['ticket_packages'] = $this -> get_membership_package($data['membership_options'], $selected_membership_options);
		$data['user_membership_options'] = $selected_membership_options;
		$this -> load -> view('user/account/load_membership_packages', $data);
	}

	/**
	 * my_feature_users_photo Function :: Fetch current user recent intro with users which have primary photo show ,
	 * LIMIT = 9
	 * @access Private :: Access only this controller not accesssible outside.
	 * @author Rajnish Savaliya
	 */
	private function _my_feature_users_photo() {
		$query = 'SELECT user_photo.*,
				user_intro.user_intro_id, user_intro.intro_available_time,
				user.user_id, user.first_name , user.privacy_photos,
				CONCAT("' . base_url() . 'user_photos/user_",user.user_id,"/","",user_photo.photo) as photo_url
				FROM user_intro
				
				JOIN user on user.user_id = CASE 
					WHEN user_intro.user1_id = "' . $this -> user_id . '" THEN user_intro.user2_id
					WHEN user_intro.user2_id = "' . $this -> user_id . '" THEN user_intro.user1_id
				END
				RIGHT JOIN user_photo on user_photo.user_id = user.user_id
				WHERE (user1_id = "' . $this -> user_id . '" OR user2_id = "' . $this -> user_id . '") 
				AND set_primary=1
				AND privacy_photos = "SHOW"
				Group BY user.user_id 
				ORDER BY `intro_available_time` DESC limit 8';

		return $this -> general -> sql_query($query);
		//return array();
	}

	/* Setting Page Features */
	/**
	 * Setting Page :: Setting User account data
	 * @access public
	 * @author Rajnish Savaliya
	 */
	public function settings() {
		if ($post_data = $this -> input -> post()) {
			//Account Tab data
			$user_table_data['facebook_page'] = $post_data['facebook_page'];
			$user_table_data['linkedin_page'] = $post_data['linkedin_page'];
			$user_table_data['twitter_username'] = $post_data['twitter_username'];
			$user_table_data['wechat_id'] = $post_data['wechat_id'];

			$user_table_data['preferred_date_days'] = $post_data['daysPreference'];

			$user_table_data['matchmaking_selectivity'] = $post_data['matchmaking_selectivity'];
			$user_table_data['account_status_id'] = $post_data['account_status_id'];
			$user_table_data['auto_renew'] = $post_data['auto_renew'];

			//Privacy Tab data
			$user_table_data['privacy_first_name'] = $post_data['privacy_first_name'];
			$user_table_data['privacy_photos'] = $post_data['privacy_photos'];
			$user_table_data['privacy_contact_email'] = $post_data['privacy_contact_email'];
			$user_table_data['privacy_mobile_phone'] = $post_data['privacy_mobile_phone'];
			$user_table_data['privacy_wechat'] = $post_data['privacy_wechat'];

			$this -> general -> set_table('user');
			$this -> general -> update($user_table_data, array('user_id' => $this -> user_id));

			$dateTypePrefrences = $this -> input -> post('dateTypePreference') ? $this -> input -> post('dateTypePreference') : NULL;
			$dateTypePrefrencesOther = $this -> input -> post('otherDateTypePrefrence') ? $this -> input -> post('otherDateTypePrefrence') : NULL;

			$this -> model_user -> clear_data($this -> user_id, 'user_preferred_date_type');
			$this -> model_user -> insert_user_preferred_date_type($this -> user_id, $dateTypePrefrences, $dateTypePrefrencesOther);

			if ($prefered_contacts = $this -> input -> post('contactMethodPreference')) {
				$pref_contact = explode(',', $prefered_contacts);
				$this -> model_user -> insert_user_preferred_contact_method($this -> user_id, $pref_contact);
			}
			$tab_id = 'account';
			if ($tabName = $this -> input -> post('current_tab')) {
				$tab_id = str_replace('Tab', '', $tabName);
			}
			$this -> session -> set_flashdata('success_msg', translate_phrase("Your setting has been changed successfully."));
			redirect(base_url() . url_city_name() . '/setting.html#' . $tab_id);
		}

		$this -> general -> set_table('user');
		$user_data = $this -> general -> get('user.*,
		CASE
					WHEN
						birth_date != "0000-00-00" 	
					THEN 
						TIMESTAMPDIFF(YEAR,`birth_date`,CURDATE())
				END as user_age
		', array('user_id' => $this -> user_id));

		$user = $user_data['0'];
		$data['user_data'] = $user;
		//Fetch Account Information
		$account_condition['display_language_id'] = $this -> language_id;

		$this -> general -> set_table('account_status');
		$data['account_data'] = $this -> general -> get("", $account_condition);

		$account_condition['account_status_id'] = $user['account_status_id'];
		$account_data = $this -> general -> get("", $account_condition);
		$data['user_account'] = $account_data['0'];

		//CONTACT DETAILS : Email
		$this -> general -> set_table('user_email');
		$data['user_emails'] = $this -> general -> get("", array('user_id' => $this -> user_id));

		//CONTACT DETAILS : Phone Number
		$city_id = $this -> session -> userdata('sess_city_id');
		$current_country = $this -> model_user -> getCountryByCity($city_id);
		$data['country_code'] = $current_country ? $current_country -> country_code : " ";
		$data['date_type'] = $this -> model_user -> get_date_type($this -> language_id);

		$fields = array('usr.date_type_id', 'dt_type.description as date_type_description');
		$from = 'user_preferred_date_type as usr';
		$joins = array('date_type as dt_type' => array('usr.date_type_id = dt_type.date_type_id', 'LEFT'));

		$where['dt_type.display_language_id'] = $this -> language_id;
		$where['usr.user_id'] = $this -> user_id;
		$data['user_prefered_date_type'] = $this -> model_user -> multijoins($fields, $from, $joins, $where, NULL, 'usr.user_preferred_date_type_id asc');
		unset($where);
		$data['user_prefered_date_type_ids'] = array();
		if ($data['user_prefered_date_type']) {
			foreach ($data['user_prefered_date_type'] as $date_type) {
				$data['user_prefered_date_type_ids'][] = $date_type['date_type_id'];
			}
		}

		$fields = array('usr.*');
		$from = 'user_preferred_date_type as usr';
		$joins = array('date_type as dt_type' => array('usr.date_type_id = dt_type.date_type_id', 'LEFT'));

		$where['dt_type.display_language_id'] = $this -> language_id;
		$where['dt_type.view_other'] = 1;
		//if selected other
		$where['usr.user_id'] = $this -> user_id;
		$data['user_prefered_date_type_other']['date_type_other'] = '';
		if ($user_prefere_date_type_other = $this -> model_user -> multijoins($fields, $from, $joins, $where, NULL, 'usr.user_preferred_date_type_id asc')) {
			$data['user_prefered_date_type_other'] = $user_prefere_date_type_other['0'];
		}
		unset($where);

		$data['contact_method'] = $this -> model_user -> get_contact_method($this -> language_id);
		//Contact Method
		$fields = array('usr.contact_method_id', 'cm.description as contact_method_description');

		$from = 'user_preferred_contact_method as usr';
		$joins = array('contact_method as cm' => array('usr.contact_method_id = cm.contact_method_id', 'LEFT'));

		$where['cm.display_language_id'] = $this -> language_id;
		$where['usr.user_id'] = $this -> user_id;
		$data['user_preferred_contact_method'] = $this -> model_user -> multijoins($fields, $from, $joins, $where, '', 'view_order asc');
		unset($where);
		
		$data['user_preferred_contact_method_ids'] = array();
		if ($data['user_preferred_contact_method']) {
			foreach ($data['user_preferred_contact_method'] as $method) {
				$data['user_preferred_contact_method_ids'][] = $method['contact_method_id'];
			}
		}
		$fields = array('membership.*', 'um.*');
		$from = 'membership_option as membership';
		$joins = array('user_membership_option as um' => array('membership.membership_option_id = um.membership_option_id', 'INNER'));
		$where['um.user_id'] = $this -> user_id;
		$where['membership.display_language_id'] = $this -> language_id;
		$data['user_membership_option'] = $this -> model_user -> multijoins($fields, $from, $joins, $where, '', 'view_order asc');
		
		
		$fields = array('usr_photo_req.*', 'usr.first_name','usr.last_name');
		$from = 'user_photo_request as usr_photo_req';
		$joins = array('user as usr' => array('usr_photo_req.requested_by_user_id = usr.user_id', 'INNER'));
		$req_photo_condition['usr_photo_req.user_id'] = $this -> user_id;
		$data['user_photo_request'] = $this -> model_user -> multijoins($fields, $from, $joins, $req_photo_condition, '', 'request_time asc, response_time asc');
		
		
		if($redirect_tab_name  = $this->session->userdata('redirect_tab_name')){
			if(($status = $this->input->get('status')) && ($user_photo_request_id = $this->input->get('photo_request_id')))
			{
				$this->general->set_table('user_photo_request');
				
				$photo_request_condition['user_photo_request_id'] = $user_photo_request_id;
				$photo_request_condition['user_id'] = $this->user_id;
				if($this->general->checkDuplicate($photo_request_condition)){
					
					$flag = "0";
					if($status == "1")
					{
						$flag = "1";	
					}
					else if($status == "2")
					{
						$flag = "2";	
					}
					$photo_request['status'] = $flag;
					
					$photo_request['response_time'] = SQL_DATETIME;
					$this->general->update($photo_request,$photo_request_condition);
				}
			}
			$this->session->unset_userdata('redirect_tab_name');
			redirect(base_url() . url_city_name() . '/setting.html#' . $redirect_tab_name);
		}
		
		/*
		 * */
						
		$data['page_title'] = translate_phrase('Your Setting');
		$data['page_name'] = 'user/account/setting';
		
		//echo "<pre>";print_r($data['user_photo_request']);exit;
		$this -> load -> view('template/editProfileTemplate', $data);
	}

	/**
	 * Change Account Password
	 * @author Rajnish Savaliya
	 */
	public function photo_request($user_photo_request_id = 1) {

		$response['type'] = "0";
		$response['msg'] = "error occured, Please try again";
		
		if ($postData = $this -> input -> post()) {
			
			$this -> general -> set_table('user');
			$request_condition['user_id'] = $this -> user_id;
			$request_condition['privacy_photos'] = "HIDE";
			
			if ($user_photo_privacy_data = $this -> general -> get("user_id, first_name, password, facebook_id",$request_condition)) {
				$this->general->set_table('user_photo_request');
				
				$photo_request_condition['user_photo_request_id'] = $user_photo_request_id;
				$photo_request_condition['user_id'] = $this->user_id;
				if($user_photo_request = $this->general->get("",$photo_request_condition)){
					
					
					$photo_request['status'] = $postData['status'];
					$photo_request['response_time'] = SQL_DATETIME;
					
					if($this->general->update($photo_request,$photo_request_condition))
					{
						$response['type'] = "1";
						if($photo_request['status'] == "1")
						{
							$data['user_data'] = $user_photo_privacy_data['0'];
							
							$subject = $data['user_data']['first_name'].translate_phrase(' has approved your request to view his photos!');
							$user_email_data = $this -> model_user -> get_user_email($user_photo_request['0']['requested_by_user_id']);
							
							$this -> general -> set_table('user');
							$intro_data = $this -> general -> get("user_id, first_name, password, facebook_id", array('user_id' => $user_photo_request['0']['requested_by_user_id']));
							$data['intro_user_data'] = $intro_data['0'];
							if ($user_email_data) {
								$data['btn_link'] = base_url().'user/user_info/'.$this->utility->encode($data['user_data']['user_id']).'/'.$this->utility->encode($data['intro_user_data']['user_id']).'/'.$data['intro_user_data']['password'];
								$data['btn_text'] = translate_phrase("View ".$data['user_data']['first_name']."'s Profile");
								
								$data['email_title'] = $subject;
								$data['email_content'] = '';
								
								$email_template = $this -> load -> view('email/common', $data, true);
								$this -> datetix -> mail_to_user($user_email_data['email_address'], $subject, $email_template);
							}
							
							$response['msg'] = translate_phrase("Photo request has been approved sucessfully.");
						}
						else if($photo_request['status'] == "2"){
							$response['msg'] = translate_phrase("Photo request has been declined sucessfully .");
						}
					}
				}
				else {
					$response['msg'] = translate_phrase("Sorry, No photo request found.");
				}
			} else {
				$response['msg'] = translate_phrase("No profile privacy in your profile.");
			}
		}
		echo json_encode($response);
	}

	/**
	 * Change Account Password
	 * @author Rajnish Savaliya
	 */
	public function change_password() {

		$this -> general -> set_table('user');
		$user_data = $this -> general -> get('user.*,
		CASE
					WHEN
						birth_date != "0000-00-00" 	
					THEN 
						TIMESTAMPDIFF(YEAR,`birth_date`,CURDATE())
				END as user_age
		', array('user_id' => $this -> user_id));

		$user = $user_data['0'];
		$data['user_data'] = $user;

		if ($this -> input -> post()) {
			$this -> load -> library('form_validation');
			$this -> form_validation -> set_rules('oldpassword', 'old password', 'required|callback_password_check');
			$this -> form_validation -> set_rules('newpassword', 'new password', 'required');
			$this -> form_validation -> set_rules('repeatpassword', 'confirm passord', 'required|matches[newpassword]');
			$this -> form_validation -> set_message('matches', translate_phrase('The two passwords you entered do not match each other.'));
			if ($this -> form_validation -> run() != FALSE) {
				#update password
				$this -> model_user -> update_user($this -> user_id, array('password' => $this -> input -> post('newpassword')));
				$this -> session -> set_flashdata('success_msg', translate_phrase("Password has been changed successfully."));
				redirect(base_url(url_city_name() . '/setting.html'));
			}
		}

		$data['page_title'] = translate_phrase('Change Password');
		$data['page_name'] = 'user/account/change_password';
		$this -> load -> view('template/editProfileTemplate', $data);
	}

	/**
	 * [Helper function for form validation] : check old password with entered text
	 * @access public
	 * @return TRUE FALSE
	 * @author Rajnish Savaliya
	 */
	public function password_check($str) {
		$this -> general -> set_table('user');
		$user_data = $this -> general -> get('password,user_id,facebook_id', array('user_id' => $this -> user_id));

		$user = $user_data['0'];
		if ($user['password'] && sha1($str) == $user['password']) {
			return TRUE;
		} else {
			if ($user['facebook_id'])
				$this -> form_validation -> set_message('password_check', translate_phrase("Sorry, You are a facebook user, We didn't have your password."));
			else
				$this -> form_validation -> set_message('password_check', translate_phrase("You have entered wrong password."));

			return FALSE;

		}
	}

	/**
	 * [AJAX Call] : Add new email to user_email table
	 * @access public
	 * @return TRUE FALSE with response.
	 * @author Rajnish Savaliya
	 */
	public function add_email() {
		$response['type'] = '0';
		$response['msg'] = '';
		if ($this -> input -> is_ajax_request()) {
			if ($data = $this -> input -> post()) {
				$this -> load -> library('form_validation');
				$this -> form_validation -> set_rules('new_email', 'Email', 'trim|required|valid_email|xss_clean|is_unique[user_email.email_address]');
				$this -> form_validation -> set_error_delimiters('', '');
				$this -> form_validation -> set_message('is_unique', translate_phrase('Email already exists'));

				if ($this -> form_validation -> run() == FALSE) {
					$response['msg'] = translate_phrase(validation_errors());
					$response['type'] = '0';
				} else {
					$this -> general -> set_table('user_email');
					$verification_code = $this -> utility -> generateRandomString(6);
					$insert_array = array('user_id' => $this -> user_id, 'is_verified' => '0', 'verification_code' => $verification_code, 'created_date' => date('Y-m-d'), 'email_address' => $data['new_email']);

					if ($id = $this -> general -> save($insert_array)) {
						$subject = translate_phrase('Email Verification');
						$email_data['email_title'] = translate_phrase('Please use following code to verify your email address.');
						$email_data['email_content'] = 'Verification Code : <b>' . $verification_code . '</b>';
						$email_template = $this -> load -> view('email/common', $email_data, true);
						$this -> datetix -> mail_to_user($data['new_email'], $subject, $email_template);

						$response['msg'] = translate_phrase('Email added successfully.');
						$response['type'] = '1';
						$response['user_email_id'] = $id;
					} else {
						$response['msg'] = translate_phrase('Please try again.');
						$response['type'] = '0';
					}
				}
			} else {
				$response['msg'] = 'No, Data Send.';
				$response['type'] = '0';
			}
		}
		echo json_encode($response);
	}

	/**
	 * [AJAX Call] : verify email to user_email table
	 * @access public
	 * @return TRUE FALSE with response.
	 * @author Rajnish Savaliya
	 */
	public function verify_email() {
		$response['type'] = '0';
		$response['msg'] = '';
		if ($this -> input -> is_ajax_request()) {
			if ($data = $this -> input -> post()) {
				$this -> general -> set_table('user_email');
				if ($email_data = $this -> general -> get("*", array('user_email_id' => $data['user_email_id']))) {
					$email_data = $email_data['0'];
					if ($email_data['verification_code'] == $data['verification_code']) {
						$this -> general -> update(array('verification_code' => '', 'is_verified' => 1), array('user_email_id' => $data['user_email_id']));
						$response['msg'] = translate_phrase('Email verified successfully.');
						$response['type'] = '1';
					} else {
						$response['msg'] = translate_phrase('Verification code is not matched.');
					}
				} else {
					$response['msg'] = translate_phrase('Please try again.');
				}
			} else {
				$response['msg'] = 'No, Data Send.';
			}
		}
		echo json_encode($response);
	}

	/**
	 * [AJAX Call] : Change Phone Number from user table
	 * @access public
	 * @return TRUE FALSE with response.
	 * @author Rajnish Savaliya
	 */
	public function change_phone_number() {
		$response['type'] = '0';
		$response['msg'] = '';
		if ($this -> input -> is_ajax_request()) {
			if ($data = $this -> input -> post()) {
				$this -> load -> library('form_validation');
				$this -> form_validation -> set_rules('new_mobile_no', 'Phone Number', 'required|is_unique[user.mobile_phone_number]');
				$this -> form_validation -> set_error_delimiters('', '');
				$this -> form_validation -> set_message('is_unique', translate_phrase('Mobile number is already registered.'));

				if ($this -> form_validation -> run() == FALSE) {
					$response['msg'] = translate_phrase(validation_errors());
					$response['type'] = '0';
				} else {

					$this -> general -> set_table('user');

					$update_phone_data['mobile_phone_number'] = $data['new_mobile_no'];
					$update_phone_data['mobile_phone_is_verified'] = 0;
					$update_phone_data['mobile_phone_verification_code_sent'] = 0;
					$update_phone_data['mobile_phone_verification_code'] = 0;

					if ($id = $this -> general -> update($update_phone_data, array('user_id' => $this -> user_id))) {
						$response['msg'] = translate_phrase('Phone Number added successfully.');
						$response['type'] = '1';
					} else {
						$response['msg'] = translate_phrase('Please try again.');
						$response['type'] = '0';
					}
				}
			} else {
				$response['msg'] = 'No, Data Send.';
				$response['type'] = '0';
			}
		}
		echo json_encode($response);
	}

	/**
	 * [AJAX Call] : Remove email From user_email table
	 * @access public
	 * @return TRUE FALSE with response.
	 * @author Rajnish Savaliya
	 */
	public function remove_email() {

		$response['type'] = '0';
		$response['msg'] = '';
		if ($this -> input -> is_ajax_request()) {
			if ($data = $this -> input -> post()) {
				$this -> general -> set_table('user_email');
				$total_mails = $this -> general -> count_record(array('user_id' => $this -> user_id));
				if ($total_mails > 1) {
					if ($this -> general -> delete(array('user_id' => $this -> user_id, 'user_email_id' => $data['user_email_id']))) {
						$response['msg'] = translate_phrase('Email remove successfully.');
						$response['type'] = '1';
					} else {
						$response['msg'] = translate_phrase('Please try again.');
					}
				} else {
					$response['msg'] = translate_phrase('You must have at least one email address in your account.');
				}
			} else {
				$response['msg'] = 'No, Data Send.';
			}
		}
		echo json_encode($response);
	}

	/**
	 * [AJAX Call] : Set contact flage variable email From user_email table
	 * @access public
	 * @return TRUE FALSE with response.
	 * @author Rajnish Savaliya
	 */
	public function set_contact_mail() {

		$response['type'] = '0';
		$response['msg'] = '';
		if ($this -> input -> is_ajax_request()) {
			if ($data = $this -> input -> post()) {
				$this -> general -> set_table('user_email');
				$this -> general -> update(array('is_contact' => 0), array('user_id' => $this -> user_id));
				if ($this -> general -> update(array('is_contact' => 1), array('user_id' => $this -> user_id, 'user_email_id' => $data['user_email_id']))) {
					$response['msg'] = translate_phrase('Contact email address is changed successfully.');
					$response['type'] = '1';
				} else {
					$response['msg'] = translate_phrase('Please try again.');
					$response['type'] = '0';
				}

			} else {
				$response['msg'] = 'No, Data Send.';
				$response['type'] = '0';
			}
		}
		echo json_encode($response);
	}

}
?>
