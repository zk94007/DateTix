<?php
use Omnipay\Omnipay;
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Test_account extends CI_Controller {
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
			$this -> model_user -> update_user($this -> user_id, array('last_active_time' => SQL_DATETIME));
			$this -> user_id = $this -> session -> userdata('user_id');
			$this -> language_id = $this -> session -> userdata('sess_language_id');
		} else {
			//Also works in ajax call
			echo '<script type="text/javascript">window.location.href = "' . base_url() . '"</script>';
		}

		/*
		 $this->load->library('merchant');
		 $this->merchant->load('paypal_express');
		 $settings = array('username' => $this->config->item('username'),'password' => $this->config->item('password'),'signature' => $this->config->item('signature'),'test_mode' => $this->config->item('test_mode'));
		 $this->merchant->initialize($settings);
		 */

		$gateway = Omnipay::create('PayPal_Express');
		$gateway -> setUsername($this -> config -> item('username'));
		$gateway -> setPassword($this -> config -> item('password'));
		$gateway -> setSignature($this -> config -> item('signature'));
		$gateway -> setTestMode($this -> config -> item('test_mode'));

	}

	/**
	 * Index Function :: Redirect to Upgrade Account.
	 * @access public
	 * @author Rajnish Savaliya
	 */
	public function index() {
		$this -> test();
	}

	public function test() {

		$gateway = Omnipay::create('PayPal_Express');
		$gateway -> setUsername($this -> config -> item('username'));
		$gateway -> setPassword($this -> config -> item('password'));
		$gateway -> setSignature($this -> config -> item('signature'));
		$gateway -> setTestMode($this -> config -> item('test_mode'));

		$params['amount'] = '1.00';
		$params['description'] = 'description';
		$params['return_url'] = base_url() . 'test_account/test_success';
		$params['cancel_url'] = base_url() . 'test_account/cancell_success';

		try {
			$response = $gateway -> purchase($params) -> send();
			if ($response -> isSuccessful()) {
				// mark order as complete
				$responsereturn = $response -> getData();
			} elseif ($response -> isRedirect()) {
				$response -> redirect();
			} else {
				// display error to customer
				exit($response -> getMessage());
			}
		} catch (\Exception $e) {
			// internal error, log exception and display a generic message to the customer
			exit('Sorry, there was an error processing your payment. Please try again later.');
		}
	}

	public function test_success() {
		$gateway = Omnipay::create('PayPal_Express');
		$gateway -> setUsername($this -> config -> item('username'));
		$gateway -> setPassword($this -> config -> item('password'));
		$gateway -> setSignature($this -> config -> item('signature'));
		$gateway -> setTestMode($this -> config -> item('test_mode'));

		$params['amount'] = '1.00';
		$params['description'] = 'description';
		$params['return_url'] = base_url() . 'account/test_success';
		$params['cancel_url'] = base_url() . 'account/cancell_success';
		$response = $gateway -> completePurchase($params) -> send();

		$data = $response -> getData();
		// this is the raw response object
		echo '<pre>';
		print_r($data);
	}
}