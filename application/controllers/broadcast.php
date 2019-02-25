<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Broadcast extends CI_Controller {
	var $language_id = '1';
	var $token = '';

	public function __construct() {
		parent::__construct();
		$this -> load -> model('general_model', 'general');
		$this -> load -> library('datetix');

		$logged_in = $this -> session -> userdata('broadcast_logged_in');
		if ($logged_in != TRUE) {
			$user = $this -> config -> item('broadcast_username');
			$password = $this -> config -> item('broadcast_password');

			if (!isset($_SERVER['PHP_AUTH_USER'])) {
				header('WWW-Authenticate: Basic realm="Datetix Broadcast Message Panel"');
				header('HTTP/1.0 401 Unauthorized');
				echo("Please enter a valid username and password");
				exit();
			} else if (($_SERVER['PHP_AUTH_USER'] == $user) && ($_SERVER['PHP_AUTH_PW'] == $password)) {
				$this -> session -> set_userdata(array('broadcast_token' => $this -> utility -> uniqueCode()));
				$this -> session -> set_userdata(array('broadcast_logged_in' => TRUE));
			} else {
				header('WWW-Authenticate: Basic realm="Datetix Broadcast Message Panel"');
				header('HTTP/1.0 401 Unauthorized');
				echo("Please enter a valid username and password");
				exit();
			}
		}
	}

	/**
	 * Admin Dashboard: super admin panel
	 * @author Rajnish Savaliya
	 */
	public function index() {
		if ($token = $this -> session -> userdata('broadcast_token')) {
			redirect('broadcast/process/' . $token);
		} else {
			echo 'direct access not allowed';
		}
	}

	/**
	 * Loop through all user_intro_ids returned by SELECT user_intro_id FROM user_intro WHERE user1_id=1,
	 * For each user_intro_id, send a message from user_id=1 that contains the text in the multi-line text box (try to call common existing chat box code to insert row into user_intro_chat table and then sending "You have received a new message from Michael" email to the user receiving the message)
	 * @author Rajnish Savaliya
	 */
	public function process($token = "") {
		if ($token != "" && $token == $this -> session -> userdata('broadcast_token')) {
			$user_id = "1";
			$msg_sent = 0;

			if ($postData = $this -> input -> post()) {
				$msg_body = $postData['email_body'];

				$this -> general -> set_table('user');
				$user_data = $this -> general -> get("user_id, first_name, password, facebook_id", array('user_id' => $user_id));
				$data['user_data'] = $user_data['0'];

				$condition = "user1_id='" . $user_id . "' OR user2_id='" . $user_id . "'";
				$this -> general -> set_table('user_intro');

				if ($admin_intros = $this -> general -> custom_get("user_intro_id,user1_id,user2_id", $condition)) {
					
					//echo "<pre>";print_r($admin_intros);exit;
					
					//set chat data..
					$chat_data['chat_message_time'] = SQL_DATETIME;
					$chat_data['user_id'] = $user_id;
					$chat_data['chat_message'] = $msg_body;

					//if (1==2)
					{
						foreach ($admin_intros as $intro) {

							if ($intro['user1_id'] == $user_id) {
								$intro_id = $intro['user2_id'];
							} else {
								$intro_id = $intro['user1_id'];
							}

							$this -> general -> set_table('user');
							$intro_data = $this -> general -> get("user_id, first_name, password, facebook_id, current_city_id, approved_date", array('user_id' => $intro_id));
							$data['intro_user_data'] = $intro_data['0'];
	
							if ($data['intro_user_data']['current_city_id'] == 260 && $data['intro_user_data']['approved_date'] != "" &&
								$data['intro_user_data']['user_id'] != 444) {

								$chat_data['user_intro_id'] = $intro['user_intro_id'];	
								$chat_data['chat_message'] = 'Hey ' . $data['intro_user_data']['first_name'] . ', want to come to our Alumni Summer Mixer on July 31 at LEVELS? It will be a great chance to meet interesting new people! You can RSVP at www.DateTix.hk/summer Would you be free to join? ';
								
								$this -> general -> set_table('user_intro_chat');
								if ($this -> general -> save($chat_data)) {
		
									$subject = translate_phrase('You have received a new message from ') . $data['user_data']['first_name'];
									$user_email_data = $this -> model_user -> get_user_email($intro_id);
		
									//echo $this->db->last_query();exit;
									if ($user_email_data) {
										$data['email_content'] = '';
										$data['btn_link'] = base_url() . 'user/user_info/' . $this -> utility -> encode($data['user_data']['user_id']) . '/' . $this -> utility -> encode($data['intro_user_data']['user_id']) . '/' . $data['intro_user_data']['password'] . '?redirect_intro_id=' . $intro['user_intro_id'];
										$data['btn_text'] = translate_phrase('View Message');
										$data['email_title'] = translate_phrase('You have received a new message from ') . $data['user_data']['first_name'] . translate_phrase(' on ') . date('F j ') . translate_phrase(' at ') . date('g:ia');
										$email_template = $this -> load -> view('email/common', $data, true);
										if ($this -> datetix -> mail_to_user($user_email_data['email_address'], $subject, $email_template)) {
											$msg_sent++;
										}
									}
								}
								if ($msg_sent > 0) {
									$this -> session -> set_flashdata('success_msg', $msg_sent . translate_phrase(' messages was broadcasted.'));
								} else {
									$this -> session -> set_flashdata('error_msg', translate_phrase('Error occured while sending mail, Please try again.'));
								}
							}
						}
					}
				}
				redirect('broadcast/process/' . $token);
			} else {
				$data['page_title'] = translate_phrase('Broadcast Message to all members');
				$data['page_name'] = 'broadcast/form';
				$this -> load -> view('template/broadcast', $data);
			}
		} else {
			echo 'direct access not allowed';
		}

	}

	public function logout() {
		$this -> session -> sess_destroy();
		redirect('/');
	}

}
?>
