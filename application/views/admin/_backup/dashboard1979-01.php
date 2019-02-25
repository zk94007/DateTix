<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Dashboard1979 extends CI_Controller {
	var $language_id = '1';
	var $admin_id = '';
	var $default_month_year = "";
	var $default_city_id = "";
	
	public function __construct() {
		parent::__construct();
		$this -> load -> model('general_model', 'general');
		$this -> load -> library('datetix');
		
		$this->default_month_year = date('M-Y');
		if($search_month_year = $this->input->get('select_month_year'))
		{
			$this->default_month_year = date('M-Y',strtotime($search_month_year));
		}
		
		$this->default_city_id = $this -> config->item('default_city');
		if($city_id = $this->input->get('select_city_id'))
		{
			$this->default_city_id = $city_id;
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
	public function index()
	{
		$city_select = "select ct.city_id, ct.description from user JOIN city as ct on ct.city_id = user.current_city_id WHERE current_city_id IS NOT NULL GROUP BY current_city_id ORDER BY ct.description asc";
		$data['cities'] = $this -> general -> sql_query($city_select);
		
		$data['page_name'] = 'admin/analytics';
		$this -> load -> view('template/admin', $data);
	}

	/**
	 * [Ajax Call] Load new members data
	 * @author Rajnish Savaliya
	 */
	public function get_new_member()
	{
		$to_month = date('Y-m-d',strtotime($this->default_month_year.' -5 Months'));
		$start_month = date('Y-m-d',strtotime($this->default_month_year.'1 Month'));
		
		//new members
		$sql = "select count(user_id) as total_user,MONTH(applied_date) as month from user WHERE current_city_id = '".$this->default_city_id."' AND applied_date IS NOT NULL AND DATE(applied_date) > '".$to_month."' AND DATE(applied_date) < '".$start_month."' GROUP BY MONTH(applied_date)";
		$chart_data = $this -> general -> sql_query($sql);
		echo $this->formate_chart_data($chart_data);
	}
	
	/**
	 * [Ajax Call] Load active members data
	 * @author Rajnish Savaliya
	 */
	public function get_active_member()
	{
		$to_month = date('Y-m-d',strtotime($this->default_month_year.' -5 Months'));
		$start_month = date('Y-m-d',strtotime($this->default_month_year.'1 Month'));
		
		//new members
		$sql = "select count(user_id) as total_user,MONTH(last_active_time) as month from user WHERE current_city_id = '".$this->default_city_id."' AND last_active_time IS NOT NULL AND DATE(last_active_time) > '".$to_month."' AND DATE(last_active_time) < '".$start_month."' GROUP BY MONTH(last_active_time)";
		$chart_data = $this -> general -> sql_query($sql);
		echo $this->formate_chart_data($chart_data);
	}
	
	/**
	 * [Ajax Call] Load approve members data
	 * @author Rajnish Savaliya
	 */
	public function get_approve_member()
	{
		$to_month = date('Y-m-d',strtotime($this->default_month_year.' -5 Months'));
		$start_month = date('Y-m-d',strtotime($this->default_month_year.'1 Month'));
		
		//approved members
		$sql = "select count(user_id) as total_user,MONTH(approved_date) as month from user WHERE current_city_id = '".$this->default_city_id."' AND approved_date IS NOT NULL AND DATE(approved_date) > '".$to_month."' AND DATE(approved_date) < '".$start_month."' GROUP BY MONTH(approved_date)";
		$chart_data = $this -> general -> sql_query($sql);
		echo $this->formate_chart_data($chart_data);
	}
	
	/**
	 * [Ajax Call] Get Premium Member data
	 * @author Rajnish Savaliya
	 */
	public function get_premium_member()
	{
		$to_month = date('Y-m-d',strtotime($this->default_month_year.' -5 Months'));
		$start_month = date('Y-m-d',strtotime($this->default_month_year.'1 Month'));
		
		$sql = "
		select count(user_id) as total_user,MONTH(applied_date) as month from user 
		WHERE user_id IN (select DISTINCT member.user_id from user_membership_option as member where member.expiry_date IS NOT NULL AND member.expiry_date >= CURDATE()) 
		AND current_city_id = '".$this->default_city_id."' 
		AND applied_date IS NOT NULL 
		AND DATE(applied_date) > '".$to_month."' 
		AND DATE(applied_date) < '".$start_month."' 
		GROUP BY MONTH(applied_date)";
		$chart_data = $this -> general -> sql_query($sql);
		echo $this->formate_chart_data($chart_data);
	}
	/**
	 * [Ajax Call] Get Premium Member data
	 * @author Rajnish Savaliya
	 */
	public function get_avg_revenue_per_premium_member()
	{
		$to_month = date('Y-m-d',strtotime($this->default_month_year.' -5 Months'));
		$start_month = date('Y-m-d',strtotime($this->default_month_year.'1 Month'));
		
		$sql = "SELECT user_order.user_id, order_amount,order_currency_id, MONTH(order_time) as month
		FROM user_order JOIN user on user.user_id = user_order.user_id
		WHERE user.current_city_id IS NOT NULL 
		AND user.current_city_id = '".$this->default_city_id."' 
		AND order_time IS NOT NULL 
		AND DATE(order_time) > '".$to_month."' 
		AND DATE(order_time) < '".$start_month."'
		";
		
		$chart_data = array();
		if($row_data = $this -> general -> sql_query($sql))
		{
			//Grouping monthwise data
			foreach($row_data as $row)
			{
				$order_group_data[$row['month']][] = $row;
			}
			
			//Calculate Avg. Revanue per user
			foreach($order_group_data as $month=>$monthly_row)
			{
				$total_amount = 0;
				$total_user = count($monthly_row);
								
				foreach($monthly_row as $user_order)
				{
					$total_amount += get_currency_in_usd($user_order['order_amount'],$user_order['order_currency_id']);
						
				}
				$temp['month'] = $month;
				
				//revenue
				$temp['total_user'] = $total_amount/$total_user;
				
				$chart_data[] = $temp;
			}
		}
		
		echo $this->formate_chart_data($chart_data);
	}
	
	/* Formate chart data in form of JqPlot - lable and states */
	private function formate_chart_data($chart_data = array())
	{
		$start_month = strtotime($this->default_month_year.' -5 Months');
		$end_month = strtotime($this->default_month_year.'1 Month');
		
		
		$month_data = array();
		while($start_month < $end_month)
		{
			//chart data key
			$i= date('m', $start_month);
			
			$key = date('M-y',$start_month);
			
			foreach($chart_data as $stat)
			{
					
				if($stat['month'] == $i)
				{
					$month_data[$key] = $stat['total_user'];
					break;
				}
				else {
					$month_data[$key] = 0;
				}
			}
			
			$start_month = strtotime("+1 month", $start_month);
		}
		
		return json_encode($month_data);
	}
	
	public function logout()
	{
		$this->session->sess_destroy();
		redirect('/');
	}
}
?>
