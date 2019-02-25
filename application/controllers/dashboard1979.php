<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Dashboard1979 extends CI_Controller {
	var $language_id = '1';
	var $admin_id = '';
	var $default_month_year = "";
	var $default_city_id = "";

	public function __construct() {
		parent::__construct();
		$this -> load -> model('general_model', 'general');
		$this -> load -> library('datetix');

		$this -> default_month_year = date('M-Y');
		if ($search_month_year = $this -> input -> get('select_month_year')) {
			$this -> default_month_year = date('M-Y', strtotime($search_month_year));
		}

		if ($city_id = $this -> input -> get('select_city_id')) {
			$this -> default_city_id = $city_id;
		}

		$logged_in = $this -> session -> userdata('admin_dashboard_logged_in');
		if ($logged_in != TRUE) {
			$user = $this -> config -> item('dashboard_username');
			$password = $this -> config -> item('dashboard_password');

			if (!isset($_SERVER['PHP_AUTH_USER'])) {
				header('WWW-Authenticate: Basic realm="Datetix Dashboard"');
				header('HTTP/1.0 401 Unauthorized');
				echo("Please enter a valid username and password");
				exit();
			} else if (($_SERVER['PHP_AUTH_USER'] == $user) && ($_SERVER['PHP_AUTH_PW'] == $password)) {
				$this -> session -> set_userdata(array('admin_dashboard_logged_in' => TRUE));
			} else {
				header('WWW-Authenticate: Basic realm="Datetix Dashboard"');
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
		$city_select = "select ct.city_id, ct.description from user JOIN city as ct on ct.city_id = user.current_city_id WHERE current_city_id IS NOT NULL GROUP BY current_city_id ORDER BY ct.description asc";
		$data['cities'] = $this -> general -> sql_query($city_select);

		$data['page_name'] = 'admin/analytics';
		$this -> load -> view('template/admin', $data);
	}

	/**
	 * [Ajax Call] Load new members data
	 * @author Rajnish Savaliya
	 */
	public function get_new_member() {
		$to_month = date('Y-m-d', strtotime($this -> default_month_year . ' -5 Months'));
		$start_month = date('Y-m-d', strtotime($this -> default_month_year . '1 Month'));

		//new members
		$sql = "select count(user_id) as total_user, MONTH(applied_date) as month from user WHERE user.current_city_id IS NOT NULL ";

		if ($this -> default_city_id) {
			$sql .= "AND current_city_id = '" . $this -> default_city_id . "'";
		}

		$sql .= "AND applied_date IS NOT NULL AND DATE(applied_date) > '" . $to_month . "' AND DATE(applied_date) < '" . $start_month . "' GROUP BY MONTH(applied_date)";
		$chart_data = $this -> general -> sql_query($sql);
		echo $this -> formate_chart_data($chart_data);
	}

	/**
	 * [Ajax Call] Load active members data
	 * @author Rajnish Savaliya
	 */
	public function get_active_member() {
		$to_month = date('Y-m-d', strtotime($this -> default_month_year . ' -5 Months'));
		$start_month = date('Y-m-d', strtotime($this -> default_month_year . '1 Month'));

		//new members
		$sql = "select COUNT(DISTINCT user_log.user_id) as total_user, MONTH(log_time) as month 
		FROM user_log
		LEFT JOIN user ON user_log.user_id = user.user_id
		WHERE user.current_city_id IS NOT NULL ";
		if ($this -> default_city_id) {
			$sql .= "AND current_city_id = '" . $this -> default_city_id . "'";
		}

		$sql .= " AND log_time IS NOT NULL AND DATE(log_time) > '" . $to_month . "' AND DATE(log_time) < '" . $start_month . "' GROUP BY MONTH(log_time)";
		$chart_data = $this -> general -> sql_query($sql);
		
		echo $this -> formate_chart_data($chart_data);
	}

	/**
	 * [Ajax Call] Get Members with at least one message grouping by msg time
	 * @author Rajnish Savaliya
	 */
	public function get_member_atleast_one_msg() {
		$to_month = date('Y-m-d', strtotime($this -> default_month_year . ' -5 Months'));
		$start_month = date('Y-m-d', strtotime($this -> default_month_year . '1 Month'));

		$sql = "SELECT count(DISTINCT chat.user_id) as total_user, MONTH(chat_message_time) as month FROM 
				user_intro_chat as chat RIGHT JOIN user ON user.user_id = chat.user_id 
				WHERE chat.chat_message_time IS NOT NULL ";

		if ($this -> default_city_id) {
			$sql .= "AND current_city_id = '" . $this -> default_city_id . "'";
		}

		$sql .= "AND DATE(chat_message_time) > '" . $to_month . "' 
				AND DATE(chat_message_time) < '" . $start_month . "' 
				GROUP BY MONTH(chat_message_time)
				";

		$chart_data = $this -> general -> sql_query($sql);
		echo $this -> formate_chart_data($chart_data);
	}

	/**
	 * [Ajax Call] Get Members with phone number grouping by applied date
	 * @author Rajnish Savaliya
	 */
	public function get_member_have_phone() {
		$to_month = date('Y-m-d', strtotime($this -> default_month_year . ' -5 Months'));
		$start_month = date('Y-m-d', strtotime($this -> default_month_year . '1 Month'));

		//members with phone
		$sql = "select count(user_id) as total_user,MONTH(applied_date) as month from user WHERE mobile_phone_number IS NOT NULL AND mobile_phone_number != '' AND user.current_city_id IS NOT NULL ";

		if ($this -> default_city_id) {
			$sql .= "AND current_city_id = '" . $this -> default_city_id . "'";
		}

		$sql .= " AND applied_date IS NOT NULL AND DATE(applied_date) > '" . $to_month . "' AND DATE(applied_date) < '" . $start_month . "' GROUP BY MONTH(applied_date)";
		$chart_data = $this -> general -> sql_query($sql);
		echo $this -> formate_chart_data($chart_data);
	}

	/**
	 * [Ajax Call] Load approve members data
	 * @author Rajnish Savaliya
	 */
	public function get_approve_member() {
		$to_month = date('Y-m-d', strtotime($this -> default_month_year . ' -5 Months'));
		$start_month = date('Y-m-d', strtotime($this -> default_month_year . '1 Month'));

		//approved members
		$sql = "select count(user_id) as total_user,MONTH(approved_date) as month from user WHERE user.current_city_id IS NOT NULL ";

		if ($this -> default_city_id) {
			$sql .= "AND current_city_id = '" . $this -> default_city_id . "'";
		}

		$sql .= " AND approved_date IS NOT NULL AND DATE(approved_date) > '" . $to_month . "' AND DATE(approved_date) < '" . $start_month . "' GROUP BY MONTH(approved_date)";
		$chart_data = $this -> general -> sql_query($sql);
		echo $this -> formate_chart_data($chart_data);
	}

	/**
	 * [Ajax Call] Get Premium Member data
	 * @author Rajnish Savaliya
	 */
	public function get_premium_member() {
		$to_month = date('Y-m-d', strtotime($this -> default_month_year . ' -5 Months'));
		$start_month = date('Y-m-d', strtotime($this -> default_month_year . '1 Month'));

		$sql = "
		select count(user_id) as total_user,MONTH(applied_date) as month from user 
		WHERE user_id IN (select DISTINCT member.user_id from user_membership_option as member where member.expiry_date IS NOT NULL AND member.expiry_date >= CURDATE()) 
		AND user.current_city_id IS NOT NULL ";

		if ($this -> default_city_id) {
			$sql .= "AND current_city_id = '" . $this -> default_city_id . "'";
		}

		$sql .= "
		AND applied_date IS NOT NULL 
		AND DATE(applied_date) > '" . $to_month . "' 
		AND DATE(applied_date) < '" . $start_month . "' 
		GROUP BY MONTH(applied_date)";
		$chart_data = $this -> general -> sql_query($sql);
		echo $this -> formate_chart_data($chart_data);
	}

	/**
	 * [Ajax Call] Get Premium Member data
	 * @author Rajnish Savaliya
	 */
	public function get_avg_revenue_per_premium_member() {
		$to_month = date('Y-m-d', strtotime($this -> default_month_year . ' -5 Months'));
		$start_month = date('Y-m-d', strtotime($this -> default_month_year . '1 Month'));

		$sql = "SELECT user_order.user_id, order_amount,order_currency_id, MONTH(order_time) as month
		FROM user_order JOIN user on user.user_id = user_order.user_id
		WHERE user.current_city_id IS NOT NULL ";

		if ($this -> default_city_id) {
			$sql .= "AND current_city_id = '" . $this -> default_city_id . "'";
		}

		$sql .= " AND order_time IS NOT NULL 
		AND DATE(order_time) > '" . $to_month . "' 
		AND DATE(order_time) < '" . $start_month . "'
		";

		$chart_data = array();
		if ($row_data = $this -> general -> sql_query($sql)) {
			//Grouping monthwise data
			foreach ($row_data as $row) {
				$order_group_data[$row['month']][] = $row;
			}

			//Calculate Avg. Revanue per user
			foreach ($order_group_data as $month => $monthly_row) {
				$total_amount = 0;
				$total_user = count($monthly_row);

				foreach ($monthly_row as $user_order) {
					$total_amount += get_currency_in_usd($user_order['order_amount'], $user_order['order_currency_id']);

				}
				$temp['month'] = $month;

				//revenue
				$temp['total_user'] = $total_amount / $total_user;

				$chart_data[] = $temp;
			}
		}
		echo $this -> formate_chart_data($chart_data);
	}

	/**
	 * [Ajax Call] Get Premium Member and Active data
	 * @author Rajnish Savaliya
	 */
	public function get_premium_active_member_ration() {
		$to_month = date('Y-m-d', strtotime($this -> default_month_year . ' -5 Months'));
		$start_month = date('Y-m-d', strtotime($this -> default_month_year . '1 Month'));

		$sql = "select COUNT(DISTINCT user_log.user_id) as total_user, MONTH(log_time) as month 
		FROM user_log
		JOIN user ON user_log.user_id = user.user_id
		WHERE user.current_city_id IS NOT NULL ";
		if ($this -> default_city_id) {
			$sql .= "AND current_city_id = '" . $this -> default_city_id . "'";
		}

		$sql .= " AND log_time IS NOT NULL AND DATE(log_time) > '" . $to_month . "' AND DATE(log_time) < '" . $start_month . "' GROUP BY MONTH(log_time)";
		$chart_data = $this -> general -> sql_query($sql);
		$active_member_data = json_decode($this -> formate_chart_data($chart_data), true);
		
		//Premium Members
		$sql = "
		select count(user_id) as total_user,MONTH(applied_date) as month from user 
		WHERE user_id IN (select DISTINCT member.user_id from user_membership_option as member where member.expiry_date IS NOT NULL AND member.expiry_date >= CURDATE()) 
		AND user.current_city_id IS NOT NULL ";

		if ($this -> default_city_id) {
			$sql .= "AND current_city_id = '" . $this -> default_city_id . "'";
		}

		$sql .= "
		AND applied_date IS NOT NULL 
		AND DATE(applied_date) > '" . $to_month . "' 
		AND DATE(applied_date) < '" . $start_month . "' 
		GROUP BY MONTH(applied_date)";
		$chart_data = $this -> general -> sql_query($sql);
		$premium_member_data = json_decode($this -> formate_chart_data($chart_data), true);

		$chart_data = array();
		if ($premium_member_data && $active_member_data) {
			foreach ($premium_member_data as $key => $premium_user) {
				//Active Premium members
				if ($premium_user > 0 && $active_member_data[$key] > 0)
				{
					$chart_data[$key] = $active_member_data[$key] * 100 / $premium_user;
				}
				else
					$chart_data[$key] = 0;

			}
		}
		echo json_encode($chart_data,JSON_NUMERIC_CHECK);
	}

	/**
	 * [Ajax Call] Pie chart - Get users by gender data
	 * @author Rajnish Savaliya
	 */
	public function get_user_by_gender() {
		$to_month = date('Y-m-d', strtotime($this -> default_month_year . ' -5 Months'));
		$start_month = date('Y-m-d', strtotime($this -> default_month_year . '1 Month'));

		$sql = "
		SELECT count(user_id) as total_user, gender.description
		FROM user JOIN  gender ON gender.gender_id = user.gender_id 
		WHERE 
		gender.display_language_id = 1 AND
		user.current_city_id IS NOT NULL ";

		if ($this -> default_city_id) {
			$sql .= "AND current_city_id = '" . $this -> default_city_id . "'";
		}

		$sql .= " AND applied_date IS NOT NULL 
		AND DATE(applied_date) > '" . $to_month . "' 
		AND DATE(applied_date) < '" . $start_month . "' 
		GROUP BY gender.gender_id";
		$chart_data = array();
		if ($row_data = $this -> general -> sql_query($sql)) {
			foreach ($row_data as $row) {
				$chart_data[$row['description']] = $row['total_user'];
			}
		}
		echo json_encode($chart_data,JSON_NUMERIC_CHECK);
	}

	/**
	 * [Ajax Call] Pie chart - Get users by Age data
	 * @author Rajnish Savaliya
	 */
	public function get_user_by_age() {
		$to_month = date('Y-m-d', strtotime($this -> default_month_year . ' -5 Months'));
		$start_month = date('Y-m-d', strtotime($this -> default_month_year . '1 Month'));
		//		SELECT count(user_id) as total_user,  TIMESTAMPDIFF(YEAR,`birth_date`,CURDATE()) as user_age FROM user

		$sql = "
		select  concat(5*floor(TIMESTAMPDIFF(YEAR,`birth_date`,CURDATE())/5), '-', 5*floor(TIMESTAMPDIFF(YEAR,`birth_date`,CURDATE())/5) + 5) as `age_range`, count(user_id) as `total_user` from user 
		WHERE 
		user.current_city_id IS NOT NULL ";

		if ($this -> default_city_id) {
			$sql .= "AND current_city_id = '" . $this -> default_city_id . "'";
		}

		$sql .= " AND applied_date IS NOT NULL AND birth_date IS NOT NULL  AND birth_date != '0000-00-00'
		AND DATE(applied_date) > '" . $to_month . "' 
		AND DATE(applied_date) < '" . $start_month . "' 
		group by 1";
		$chart_data = array();
		if ($row_data = $this -> general -> sql_query($sql)) {

			foreach ($row_data as $row) {
				$chart_data[$row['age_range']] = $row['total_user'];
			}
		}
		echo json_encode($chart_data,JSON_NUMERIC_CHECK);
	}

	/**
	 * [Ajax Call] Pie chart - Get users by gender data
	 * @author Rajnish Savaliya
	 */
	public function get_user_by_ethnicity() {
		$to_month = date('Y-m-d', strtotime($this -> default_month_year . ' -5 Months'));
		$start_month = date('Y-m-d', strtotime($this -> default_month_year . '1 Month'));

		$sql = "
		SELECT count(user_id) as total_user, ethnicity.description
		FROM user JOIN  ethnicity ON ethnicity.ethnicity_id = user.ethnicity_id 
		WHERE 
		ethnicity.display_language_id = 1 AND
		user.current_city_id IS NOT NULL ";

		if ($this -> default_city_id) {
			$sql .= "AND current_city_id = '" . $this -> default_city_id . "'";
		}

		$sql .= " AND applied_date IS NOT NULL 
		AND DATE(applied_date) > '" . $to_month . "' 
		AND DATE(applied_date) < '" . $start_month . "' 
		GROUP BY ethnicity.ethnicity_id";
		$chart_data = array();
		if ($row_data = $this -> general -> sql_query($sql)) {
			foreach ($row_data as $row) {
				$chart_data[$row['description']] = $row['total_user'];
			}
		}
		echo json_encode($chart_data,JSON_NUMERIC_CHECK);
	}

	/**
	 * [Ajax Call] stack chart - get_user_by_source
	 * @author Rajnish Savaliya
	 */
	public function get_user_by_source() {
		$to_month = date('Y-m-d', strtotime($this -> default_month_year . ' -5 Months'));
		$start_month = date('Y-m-d', strtotime($this -> default_month_year . '1 Month'));

		$sql = "
		SELECT count(user_id) as total_user, description, MONTH(applied_date) as month
		FROM user JOIN  how_you_heard_about_us ON how_you_heard_about_us.how_you_heard_about_us_id = user.how_you_heard_about_us_id 
		WHERE 
		how_you_heard_about_us.display_language_id = 1 AND
		user.current_city_id IS NOT NULL ";

		if ($this -> default_city_id) {
			$sql .= "AND current_city_id = '" . $this -> default_city_id . "'";
		}

		$sql .= " AND applied_date IS NOT NULL 
		AND DATE(applied_date) > '" . $to_month . "' 
		AND DATE(applied_date) < '" . $start_month . "' 
		GROUP BY MONTH(applied_date), how_you_heard_about_us.how_you_heard_about_us_id order by view_order";
		$chart_data = array();
		if ($row_data = $this -> general -> sql_query($sql)) {
			$start_month = strtotime($this -> default_month_year . ' -5 Months');
			$end_month = strtotime($this -> default_month_year . '1 Month');

			$month_data = array();
			$tag_array = array();
			$label_array = array();

			while ($start_month < $end_month) {

				//chart data key
				$i = date('m', $start_month);
				$key = date('M-y', $start_month);

				foreach ($row_data as $row) {
					if ($row['month'] == $i) {
						if (!in_array($row['description'], $tag_array)) {
							$tag_array[] = $row['description'];
						}
						if (!in_array($key, $label_array)) {
							$label_array[] = $key;
						}
						$chart_data[$key][$row['description']] = $row['total_user'];
					}
				}
				$start_month = strtotime("+1 month", $start_month);
			}

			if ($chart_data && $tag_array) {
				$series = array();
				$i = 0;

				foreach ($tag_array as $tag) {
					//fill data with zero if not value by tags
					foreach ($chart_data as $month => $monthy_data) {
						$value = isset($chart_data[$month][$tag]) ? $chart_data[$month][$tag] : 0;
						$chart_data[$month][$tag] = $value;
					}

					foreach ($chart_data as $month => $monthy_data) {
						$series[$i][] = $monthy_data[$tag];
					}
					$i++;
				}
				$json_data['series'] = $series;

			}

		}

		$json_data['tags'] = $tag_array;
		$json_data['labels'] = $label_array;

		
		echo json_encode($json_data, JSON_NUMERIC_CHECK);
	}

	/**
	 * [Ajax Call] BAR chart - get_avg_message_per_intro
	 * @author Rajnish Savaliya
	 */
	public function get_avg_message_per_intro() {
		$to_month = date('Y-m-d', strtotime($this -> default_month_year . ' -5 Months'));
		$start_month = date('Y-m-d', strtotime($this -> default_month_year . '1 Month'));

		//Total intro used in message by user in a particular months
		$sql = "SELECT count(DISTINCT chat.user_intro_id) as total_intro_used_per_month, MONTH(chat_message_time) as month 
				FROM  user_intro_chat as chat RIGHT JOIN user ON user.user_id = chat.user_id 
				WHERE chat.chat_message_time IS NOT NULL ";
		if ($this -> default_city_id) {
			$sql .= "AND current_city_id = '" . $this -> default_city_id . "'";
		}
		$sql .= "AND DATE(chat_message_time) > '" . $to_month . "' 
				AND DATE(chat_message_time) < '" . $start_month . "' 
				GROUP BY MONTH(chat_message_time)";

		$total_intro_data = $this -> general -> sql_query($sql);

		//Total Messages sent by user in particular months
		$sql = "SELECT count(chat.user_intro_chat_id) as total_intro_msg_per_month, MONTH(chat_message_time) as month 
				FROM  user_intro_chat as chat RIGHT JOIN user ON user.user_id = chat.user_id 
				WHERE chat.chat_message_time IS NOT NULL ";
		if ($this -> default_city_id) {
			$sql .= "AND current_city_id = '" . $this -> default_city_id . "'";
		}
		$sql .= "AND DATE(chat_message_time) > '" . $to_month . "' 
				AND DATE(chat_message_time) < '" . $start_month . "' 
				GROUP BY MONTH(chat_message_time)";

		$chat_per_intros = $this -> general -> sql_query($sql);
		if ($chat_per_intros && $total_intro_data) {
			foreach ($total_intro_data as $key => $intro_data) {
				$temp = array();
				$temp['month'] = $intro_data['month'];
				$temp['total_user'] = ($chat_per_intros[$key]['total_intro_msg_per_month'] / $total_intro_data[$key]['total_intro_used_per_month']);
				$chart_data[$key] = $temp;
			}
		}
		echo $this -> formate_chart_data($chart_data);
	}

	/**
	 * [Ajax Call] BAR chart - Count total user_intro_id
	 * @author Rajnish Savaliya
	 */
	public function get_intro_msg_sent() {
		$to_month = date('Y-m-d', strtotime($this -> default_month_year . ' -5 Months'));
		$start_month = date('Y-m-d', strtotime($this -> default_month_year . '1 Month'));

		$sql = "SELECT count(chat.user_intro_chat_id) as total_user, MONTH(chat_message_time) as month 
				FROM  user_intro_chat as chat RIGHT JOIN user ON user.user_id = chat.user_id 
				WHERE chat.chat_message_time IS NOT NULL ";
		if ($this -> default_city_id) {
			$sql .= "AND current_city_id = '" . $this -> default_city_id . "'";
		}
		$sql .= "AND DATE(chat_message_time) > '" . $to_month . "' 
				AND DATE(chat_message_time) < '" . $start_month . "' 
				GROUP BY MONTH(chat_message_time)";

		$chart_data = $this -> general -> sql_query($sql);
		echo $this -> formate_chart_data($chart_data);
	}

	
	/**
	 * [Ajax Call] BAR chart - get_intro_email_sent
	 * @author Rajnish Savaliya
	 */
	public function get_intro_email_sent() {
		$to_month = date('Y-m-d', strtotime($this -> default_month_year . ' -5 Months'));
		$start_month = date('Y-m-d', strtotime($this -> default_month_year . '1 Month'));

		$sql = "select count(user_id) as total_user,MONTH(last_intro_mail_sent) as month from user WHERE user.current_city_id IS NOT NULL ";

		if ($this -> default_city_id) {
			$sql .= "AND current_city_id = '" . $this -> default_city_id . "'";
		}

		$sql .= " AND last_intro_mail_sent IS NOT NULL AND DATE(last_intro_mail_sent) > '" . $to_month . "' AND DATE(last_intro_mail_sent) < '" . $start_month . "' GROUP BY MONTH(last_intro_mail_sent)";

		$chart_data = $this -> general -> sql_query($sql);
		echo $this -> formate_chart_data($chart_data);
	}
	
	/**
	 * [Ajax Call] BAR chart - get_intro_generated
	 * @author Rajnish Savaliya
	 */
	public function get_intro_generated() {
		$to_month = date('Y-m-d', strtotime($this -> default_month_year . ' -5 Months'));
		$start_month = date('Y-m-d', strtotime($this -> default_month_year . '1 Month'));

		$sql = "select count(DISTINCT user_intro_id) as total_user, MONTH(intro_created_time) as month 
		FROM user_intro 
		JOIN user ON user.user_id = user_intro.user1_id OR user.user_id = user_intro.user2_id
		WHERE user.current_city_id IS NOT NULL ";

		if ($this -> default_city_id) {
			$sql .= "AND current_city_id = '" . $this -> default_city_id . "'";
		}

		$sql .= " AND intro_created_time IS NOT NULL 
		AND DATE(intro_created_time) > '" . $to_month . "' 
		AND DATE(intro_created_time) < '" . $start_month . "' 
		GROUP BY MONTH(intro_created_time)";

		$chart_data = $this -> general -> sql_query($sql);
		echo $this -> formate_chart_data($chart_data);
	}
	
	/**
	 * [Ajax Call] BAR chart - get_intro_view_per_one_side :
	 * Get all records with profile_view_by_user1 = 0 or profile_view_by_user2 = 0
	 * @author Rajnish Savaliya
	 */
	public function get_intro_view_per_one_side() {
		$to_month = date('Y-m-d', strtotime($this -> default_month_year . ' -5 Months'));
		$start_month = date('Y-m-d', strtotime($this -> default_month_year . '1 Month'));

		$sql = "select count(DISTINCT user_intro_id) as total_user_view, MONTH(intro_email_sent_time) as month 
		FROM user_intro 
		JOIN user ON user.user_id = user_intro.user1_id OR user.user_id = user_intro.user2_id
		WHERE user.current_city_id IS NOT NULL ";

		if ($this -> default_city_id) {
			$sql .= "AND current_city_id = '" . $this -> default_city_id . "'";
		}

		$sql .= " AND intro_email_sent_time != '0000-00-00 00:00:00' AND (profile_viewed_by_user1 = '0' OR profile_viewed_by_user2 = '0')
		AND DATE(intro_email_sent_time) > '" . $to_month . "' 
		AND DATE(intro_email_sent_time) < '" . $start_month . "' 
		GROUP BY MONTH(intro_email_sent_time)";

		if ($chart_data = $this -> general -> sql_query($sql)) {
			//calculate total intros [alternative way to get all record count from db, This is optimize solution to get count without db call]
			$total_intro_views = 0;

			foreach ($chart_data as $key => $data) {
				$total_intro_views += $data['total_user_view'];
			}

			//get per(%) share from results set
			foreach ($chart_data as $key => $data) {
				$chart_data[$key]['total_user'] = ($data['total_user_view'] / $total_intro_views) * 100;
			}

		}
		echo $this -> formate_chart_data($chart_data);
	}

	/**
	 * [Ajax Call] BAR chart - get_intro_view_per_both_sides :
	 * Get all records with profile_view_by_user1 != 0 AND profile_view_by_user2 != 0
	 * @author Rajnish Savaliya
	 */
	public function get_intro_view_per_both_sides() {

		$to_month = date('Y-m-d', strtotime($this -> default_month_year . ' -5 Months'));
		$start_month = date('Y-m-d', strtotime($this -> default_month_year . '1 Month'));

		$sql = "select count(user_intro_id) as total_user_view, MONTH(intro_email_sent_time) as month 
		FROM user_intro
		JOIN user ON user.user_id = user_intro.user1_id OR user.user_id = user_intro.user2_id
		WHERE user.current_city_id IS NOT NULL ";

		if ($this -> default_city_id) {
			$sql .= "AND current_city_id = '" . $this -> default_city_id . "'";
		}

		$sql .= " AND intro_email_sent_time != '0000-00-00 00:00:00' 
		AND profile_viewed_by_user1 != '0' 
		AND profile_viewed_by_user2 != '0'
		AND DATE(intro_email_sent_time) > '" . $to_month . "' 
		AND DATE(intro_email_sent_time) < '" . $start_month . "' 
		GROUP BY MONTH(intro_email_sent_time)";

		if ($chart_data = $this -> general -> sql_query($sql)) {
			//calculate total intros [alternative way to get all record count from db, This is optimize solution to get count without db call]
			$total_intro_views = 0;
			//Count total user_views
			foreach ($chart_data as $key => $data) {
				$total_intro_views += $data['total_user_view'];
			}

			//get per(%) share from results set
			foreach ($chart_data as $key => $data) {
				$chart_data[$key]['total_user'] = ($data['total_user_view'] / $total_intro_views) * 100;
			}
		}
		echo $this -> formate_chart_data($chart_data);
	}

	/**
	 * [Ajax Call] BAR chart - % intros with messages from one side
	 * @author Rajnish Savaliya
	 */
	public function get_intro_msg_per_one_side() {
		$this -> default_month_year = "Sep-2014";
		$start_month = strtotime($this -> default_month_year . ' -5 Months');
		$end_month = strtotime($this -> default_month_year . '1 Month');

		$chart_data = array();
		while ($start_month < $end_month) {
				
			$msg_percentage = 0;
			$cur_month = date('m', $start_month);
			$key = date('M-y', $start_month);
			
			$sql = "select user_intro_id,user1_id, user2_id
			FROM user_intro 
			WHERE intro_email_sent_time != '0000-00-00 00:00:00' 
			AND MONTH(intro_email_sent_time) ='" . $cur_month ."'";
			
			if($monthly_user_intros = $this -> general -> sql_query($sql))
			{
				$total_monthly_intros = count($monthly_user_intros);
				
				$atleast_one_msg_with_intro  = 0;
				
				foreach ($monthly_user_intros as $data_key=>$value) {
					
					$condition = "user_intro_id = '".$value['user_intro_id']."' AND (user_id='".$value['user1_id']."' OR user_id='".$value['user2_id']."')";
					if($flag = $this->general->checkDuplicate($condition,'user_intro_chat'))
					{
						$atleast_one_msg_with_intro++;
					}
				}
				//calculate % from all atleast one msg counter of montly intros / all monthly intros
				$msg_percentage = ($atleast_one_msg_with_intro/$total_monthly_intros)*100;
				
			}
			
			$chart_data[$key] = $msg_percentage;			
			$start_month = strtotime("+1 month", $start_month);
		}
		echo json_encode($chart_data,JSON_NUMERIC_CHECK);
	}

	/**
	 * [Ajax Call] BAR chart - get_intro_msg_per_both_sides :
	 * Get all records with profile_view_by_user1 != 0 AND profile_view_by_user2 != 0
	 * @author Rajnish Savaliya
	 */
	public function get_intro_msg_per_both_sides() {

		$start_month = strtotime($this -> default_month_year . ' -5 Months');
		$end_month = strtotime($this -> default_month_year . '1 Month');

		$month_data = array();
		$chart_data = array();
		
		while ($start_month < $end_month) {
			
			$msg_percentage = 0;
			$cur_month = date('m', $start_month);
			$key = date('M-y', $start_month);
			
			$sql = "select user_intro_id,user1_id, user2_id
			FROM user_intro 
			WHERE intro_email_sent_time != '0000-00-00 00:00:00' 
			AND MONTH(intro_email_sent_time) ='" . $cur_month ."'";
			
			if($monthly_user_intros = $this -> general -> sql_query($sql))
			{
				$total_monthly_intros = count($monthly_user_intros);
				
				$both_usr_msg_with_intro  = 0;
				
				foreach ($monthly_user_intros as $data_key=>$value) {
					
					$condition = "user_intro_id = '".$value['user_intro_id']."' AND user_id='".$value['user1_id']."'";
					$user1_msg_flag = $this->general->checkDuplicate($condition,'user_intro_chat');
					
					$condition = "user_intro_id = '".$value['user_intro_id']."' AND user_id='".$value['user2_id']."'";
					$user2_msg_flag = $this->general->checkDuplicate($condition,'user_intro_chat');
					
					if($user1_msg_flag && $user2_msg_flag)
					{
						$both_usr_msg_with_intro++;
					}
				}
				//calculate % from all both users msg counter of montly intros / all monthly intros
				$msg_percentage = ($both_usr_msg_with_intro/$total_monthly_intros)*100;
			}
			
			$chart_data[$key] = $msg_percentage;			
			$start_month = strtotime("+1 month", $start_month);			
		}
		echo json_encode($chart_data,JSON_NUMERIC_CHECK);
	}
	
	/**
	 * [Ajax Call] Get Subscription Revenue Data
	 * City_id is not
	 * @author Rajnish Savaliya
	 */
	public function get_subscription_revenue() {
		$to_month = date('Y-m-d', strtotime($this -> default_month_year . ' -5 Months'));
		$start_month = date('Y-m-d', strtotime($this -> default_month_year . '1 Month'));

		$sql = "SELECT count(order_amount) as total_amount, order_currency_id, MONTH(order_time) as month 
		FROM user_order 
		JOIN user ON user.user_id = user_order.user_id 
		WHERE
		user.current_city_id IS NOT NULL ";

		if ($this -> default_city_id) {
			$sql .= "AND current_city_id = '" . $this -> default_city_id . "'";
		}
		$sql .= "AND 
		order_time IS NOT NULL
		AND DATE(order_time) > '" . $to_month . "' 
		AND DATE(order_time) < '" . $start_month . "'
		GROUP BY MONTH(order_time), order_currency_id";

		$chart_data = array();
		if ($row_data = $this -> general -> sql_query($sql)) {
				
			//Grouping monthwise data
			foreach ($row_data as $row) {
				$order_group_data[$row['month']][] = $row;
			}
			
			//Calculate Avg. Revanue per user
			foreach ($order_group_data as $month => $monthly_row) {
				$total_amount = 0;
				foreach ($monthly_row as $user_order) {
					$total_amount += get_currency_in_usd($user_order['total_amount'], $user_order['order_currency_id']);
				}
				$temp['month'] = $month;
				$temp['total_user'] = $total_amount;
				$chart_data[] = $temp;
			}
		}
		echo $this -> formate_chart_data($chart_data);
	}
	
	
	
	/**
	 * [Ajax Call] Get Event subscription Data
	 * City_id is not
	 * @author Rajnish Savaliya
	 */
	public function get_subscription_orders() {
		$to_month = date('Y-m-d', strtotime($this -> default_month_year . ' -5 Months'));
		$start_month = date('Y-m-d', strtotime($this -> default_month_year . '1 Month'));

		$sql = "SELECT count(user_order_id) as total_user, MONTH(order_time) as month 
		FROM user_order
		JOIN user ON user.user_id = user_order.user_id 
		WHERE
		user.current_city_id IS NOT NULL ";

		if ($this -> default_city_id) {
			$sql .= "AND current_city_id = '" . $this -> default_city_id . "'";
		}
		$sql .= "AND order_time IS NOT NULL
		AND DATE(order_time) > '" . $to_month . "' 
		AND DATE(order_time) < '" . $start_month . "'
		GROUP BY MONTH(order_time)";
		$chart_data = $this -> general -> sql_query($sql);
		echo $this -> formate_chart_data($chart_data);
	}
	
	
	
	/**
	 * [Ajax Call] Get Event Revenue Data
	 * City_id is not
	 * @author Rajnish Savaliya
	 */
	public function get_event_revenue() {
		$to_month = date('Y-m-d', strtotime($this -> default_month_year . ' -5 Months'));
		$start_month = date('Y-m-d', strtotime($this -> default_month_year . '1 Month'));

		$sql = "SELECT count(order_amount) as total_amount, currency_id as order_currency_id, MONTH(order_time) as month 
		FROM event_order 
		WHERE
		order_time IS NOT NULL
		AND DATE(order_time) > '" . $to_month . "' 
		AND DATE(order_time) < '" . $start_month . "'
		GROUP BY MONTH(order_time), currency_id";

		$chart_data = array();
		if ($row_data = $this -> general -> sql_query($sql)) {
			
			//Grouping monthwise data
			foreach ($row_data as $row) {
				$order_group_data[$row['month']][] = $row;
			}
			
			//Calculate Avg. Revanue per user
			foreach ($order_group_data as $month => $monthly_row) {
				$total_amount = 0;
				foreach ($monthly_row as $user_order) {
					$total_amount += get_currency_in_usd($user_order['total_amount'], $user_order['order_currency_id']);
				}
				
				$temp['month'] = $month;
				$temp['total_user'] = $total_amount;
				$chart_data[] = $temp;
			}
		}
		echo $this -> formate_chart_data($chart_data);
	}
	
	
	/**
	 * [Ajax Call] Get Event orders Data
	 * City_id is not
	 * @author Rajnish Savaliya
	 */
	public function get_event_orders() {
		$to_month = date('Y-m-d', strtotime($this -> default_month_year . ' -5 Months'));
		$start_month = date('Y-m-d', strtotime($this -> default_month_year . '1 Month'));

		$sql = "SELECT count(event_order_id) as total_user, MONTH(order_time) as month 
		FROM event_order 
		WHERE
		order_time IS NOT NULL
		AND DATE(order_time) > '" . $to_month . "' 
		AND DATE(order_time) < '" . $start_month . "'
		GROUP BY MONTH(order_time)";

		$chart_data = $this -> general -> sql_query($sql);
		echo $this -> formate_chart_data($chart_data);
	}
	
	
	/**
	 * [Helper Function] Formate row data in form of JqPlot chart- lable and states
	 * @access Private
	 * @param Chart Array - total_user*, month*
	 * @return [Json String] formated chart data
	 * @author Rajnish Savaliya
	 */
	private function formate_chart_data($chart_data = array()) {
		$start_month = strtotime($this -> default_month_year . ' -5 Months');
		$end_month = strtotime($this -> default_month_year . '1 Month');

		$month_data = array();
		while ($start_month < $end_month) {
			//chart data key
			$i = date('m', $start_month);

			$key = date('M-y', $start_month);

			foreach ($chart_data as $stat) {

				if ($stat['month'] == $i) {
					$month_data[$key] = $stat['total_user'];
					break;
				} else {
					$month_data[$key] = 0;
				}
			}

			$start_month = strtotime("+1 month", $start_month);
		}

		return json_encode($month_data,JSON_NUMERIC_CHECK);
	}

	public function logout() {
		$this -> session -> sess_destroy();
		redirect('/');
	}

}
?>
