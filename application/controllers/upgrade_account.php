<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');
class upgrade_account extends MY_Controller {
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
		}

		$settings['return_url'] = base_url() . "upgrade_account/success";
		$settings['cancel_url'] = base_url() . "upgrade_account/cancel";
		$settings['notify_url'] = base_url() . "upgrade_account/notify";
		$this -> load -> library('paypal_subscription', $settings);

	}

	/**
	 * Index Function :: Redirect to Upgrade Account.
	 * @access public
	 * @author Rajnish Savaliya
	 */
	public function index() {
		
		$subscription_details['description'] =  'Subscription: $100/Day';
		$subscription_details['amount'] =  "100.00";
		$subscription_details['period'] = 'Day';
		$subscription_details['frequency'] = 1;
		
		$this -> paypal_subscription -> subscribe($subscription_details);
		redirect($this -> paypal_subscription -> get_checkout_url());
	}

	public function cancel() {
		echo 'You have cancel subscription';
		echo '<br/><a href="'.base_url('upgrade_account').'">Click here to subscribe Again</a>';
		

	}

	public function success() {
		
		$subscription_details['description'] =  'Subscription: $100/Day';
		$subscription_details['amount'] =  "100";
		$subscription_details['period'] = 'Day';
		$subscription_details['frequency'] = 1;
		echo "<pre>";
		$this -> paypal_subscription -> subscribe($subscription_details);
		if($response = $this->paypal_subscription -> start_subscription())
		{
			echo 'response:';
			print_r($response);
			$profile_details = $this->paypal_subscription -> get_profile_details($response['PROFILEID']);
			echo 'profile:';
			print_r($profile_details);
		}
		
		$txn_id = $response['PROFILEID'];
		
		//Order Entry
		$user_order['user_id'] = '3';
		$user_order['status'] = '1';
		$user_order['transaction_id'] = $txn_id;
		$user_order['order_time'] = SQL_DATETIME;
		
		//insert Order Record
		$this -> general -> set_table('user_subscription');
		$this -> general -> save($user_order);	
		echo 'order details:'; 
		print_r($user_order);exit;
		
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
		if($myPost)
		{
			$data['ipn_id'] = isset($myPost['recurring_payment_id'])?$myPost['recurring_payment_id']:'0';
			$data['response'] = $raw_post_data;
			$data['ddate_time'] = SQL_DATETIME;
			
			if($data)
			{
				echo 'Recurring plan data';
				print_r($data);
				$this -> general -> set_table('user_subscription_details');
				echo $this -> general -> save($data);	
			}
			else {
				echo 'Sorry, No data found..';
			}
		}
		else {
			echo 'Sorry, No IPN request.';
		}
		
	}
	public function orders()
	{
		$this -> general -> set_table('user_subscription');
		$ipns = $this -> general -> get("");
		echo "<pre>";print_r($ipns);exit;
	}
	public function all_details()
	{
		$this -> general -> set_table('user_subscription_details');
		$ipns = $this -> general -> get("");
		echo "<pre>";print_r($ipns);exit;
	}
}
?>
