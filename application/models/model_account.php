<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Model_account extends CI_Model {

	function __construct()
	{
		// Call the Model constructor
		parent::__construct();
	}

	/**
	 * get_packages() :: As per client requirement currently all packages are static..
	 * but this function is for future use and easily managable when any table linked to 'Packages'
	 * @access public
	 * @author Rajnish Savaliya
	 * @return array()
	 */
	public function get_packages()
	{
		//Get Userdatas 
		$this->load->model('general_model');
		$this -> general_model -> set_table('user');
		
		$user_id = $this->session->userdata('user_id');
                
                if(empty($user_id)){                     
                    $ticket_condition['website_id'] = get_assets('website_id','0');
                    $ticket_condition['min_age '] =0;
                    
                }else{
                    $userdatas = $this -> general_model -> get('user_id,website_id,gender_id,CASE WHEN birth_date != "0000-00-00" THEN  TIMESTAMPDIFF(YEAR,`birth_date`,CURDATE()) END as user_age ',
                            array('user_id' => $user_id));
                    $userdatas = $userdatas['0'];
                    $ticket_condition['website_id'] = get_assets('website_id','0');
                    //if($userdatas['website_id'])
                            //$ticket_condition['website_id'] = $userdatas['website_id'];

                    if($userdatas['gender_id'])
                            $ticket_condition['gender_id'] = $userdatas['gender_id'];

                    if($userdatas['user_age'])
                    {
                            $ticket_condition['min_age <='] = $userdatas['user_age'];
                            $ticket_condition['max_age >='] = $userdatas['user_age'];
                    }
                }
		
		
			
		
		//Get user tickets
		$this -> general_model -> set_table('date_ticket_price');
		$ticket_packages = $this -> general_model -> get("",$ticket_condition,array('num_date_tix'=>'asc'));
		
		//echo $this->db->last_query();
		//echo "<pre>";print_r($ticket_packages);exit;
		
		/*
		$ticket_packages[] = array('name'=>'1','description'=>'1 date package','per_date_price'=>'166.0','total'=>'180','currency_id'=>'1','currency'=>'HKD','save_amount'=>'','save_per'=>'','extra'=>'');
		$ticket_packages[] = array('name'=>'3','description'=>'3 date package','per_date_price'=>'114.4','total'=>'450','currency_id'=>'1','currency'=>'HKD','save_amount'=>'90','save_per'=>'17%','extra'=>'');
		$ticket_packages[] = array('name'=>'5','description'=>'5 date package','per_date_price'=>'88.7','total'=>'600','currency_id'=>'1','currency'=>'HKD','save_amount'=>'300','save_per'=>'33%','extra'=>translate_phrase('Most Popular!'));
		$ticket_packages[] = array('name'=>'10','description'=>'10 date package','per_date_price'=>'62.9','total'=>'800','currency_id'=>'1','currency'=>'HKD','save_amount'=>'1000','save_per'=>'56%','extra'=>translate_phrase('Best Value!'));
		 */ 
		return $ticket_packages;
	}

	/**
	 * get_packages() :: As per client requirement currently all packages are static..
	 * but this function is for future use and easily managable when any table linked to 'Packages'
	 * @access public
	 * @author Rajnish Savaliya
	 * @return array()
	 */
	public function get_subscription_packages()
	{
		$ticket_packages[] = array('name'=>'1','description'=>translate_phrase('1 month'),'per_month_price'=>'0','total'=>'0','currency_id'=>'6','currency'=>'HKD','save_amount'=>'','save_per'=>'','extra'=>'');
		$ticket_packages[] = array('name'=>'3','description'=>translate_phrase('3 months'),'per_month_price'=>'0','total'=>'0','currency_id'=>'6','currency'=>'HKD','save_amount'=>'','save_per'=>'35%','extra'=>'');
		$ticket_packages[] = array('name'=>'6','description'=>translate_phrase('6 months'),'per_month_price'=>'0','total'=>'0','currency_id'=>'6','currency'=>'HKD','save_amount'=>'','save_per'=>'69%','extra'=>translate_phrase('Most Popular!'));
		$ticket_packages[] = array('name'=>'12','description'=>translate_phrase('1 year'),'per_month_price'=>'0','total'=>'0','currency_id'=>'6','currency'=>'HKD','save_amount'=>'','save_per'=>'80%','extra'=>translate_phrase('Best Value!'));
		//echo "<pre>";print_r($ticket_packages);exit;
		return $ticket_packages;
	}

	//Get Member Option based on language ID
	public function get_member_options($language_id){
                $website_id=get_assets('website_id','0');
		$this->db->select('*');
		$this->db->where('display_language_id',$language_id);
                $this->db->where('website_id',$website_id);
		$this->db->order_by('view_order','ASC');
		$result = $this->db->get('membership_option');
		$results        = array();
		if($result->num_rows()>0){
			$results    = $result->result_array();
		}
		return $results;
	}
}