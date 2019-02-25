<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_user extends CI_Model {

	public function get_ethnicity($language_id,$country_id=3){
		
		$ethnicity = array();
		$this->db->select('ethnicity.ethnicity_id,ethnicity.description');
		$this->db->join('ethnicity_parent','ethnicity_parent.ethnicity_parent_id=ethnicity.ethnicity_parent_id','inner');
		$this->db->join('country_ethnicity','country_ethnicity.ethnicity_id = ethnicity.ethnicity_id','inner');
		$this->db->where('ethnicity.display_language_id',$language_id);
		$this->db->where('ethnicity_parent.display_language_id',$language_id);
		$this->db->where('country_ethnicity.country_id',$country_id);
		$this->db->order_by('country_ethnicity.view_order','ASC');
		$result = $this->db->get('ethnicity');
		if($result->num_rows()>0){
			foreach($result->result_array() as $row){
				$ethnicity[$row['ethnicity_id']]  = $row['description'];
			}
		}else{
                    $this->db->select('ethnicity.ethnicity_id,ethnicity.description');
                    $this->db->join('ethnicity_parent','ethnicity_parent.ethnicity_parent_id=ethnicity.ethnicity_parent_id','inner');
                    $this->db->join('country_ethnicity','country_ethnicity.ethnicity_id = ethnicity.ethnicity_id','inner');
                    $this->db->where('ethnicity.display_language_id',$language_id);
                    $this->db->where('ethnicity_parent.display_language_id',$language_id);
                    $this->db->where('country_ethnicity.country_id',1);
                    $this->db->order_by('country_ethnicity.view_order','ASC');
                    $result = $this->db->get('ethnicity');
                    foreach($result->result_array() as $row){
				$ethnicity[$row['ethnicity_id']]  = $row['description'];
			}
                }
		return $ethnicity;
	}
	public function get_gender($language_id){
		$this->db->select('gender_id,description');
		$this->db->where('display_language_id',$language_id);
		$this->db->order_by('view_order','ASC');
		$result = $this->db->get('gender');
		$results        = array();
		if($result->num_rows()>0){
			$results    = $result->result_array();
		}
		return $results;
	}
	public function get_country($language_id){
		$this->db->select('country.country_id,country.description,country.is_active');
		$this->db->join('province','province.country_id=country.country_id');
		$this->db->join('city','city.province_id =province.province_id');
		
		$this->db->where('country.display_language_id',$language_id);
       	$this->db->where('country.is_active','1');
		$this->db->where('province.display_language_id',$language_id);
		$this->db->where('city.display_language_id',$language_id);
		
		$this->db->order_by('country.view_order','ASC');
		$this->db->group_by('country.country_id');
		
		$result = $this->db->get('country');
		$country = array(''=>translate_phrase('Select country'));
		
		if($result->num_rows()>0){
			foreach($result->result_array() as $row){
				$country[$row['country_id']]  = ucfirst($row['description']);
			}
		}
		return $country;
	}
        
        public function get_country_city($language_id){
		
		$this->db->select('country.country_id,country.description');
		$this->db->join('province','province.country_id = country.country_id');
		$this->db->join('city','city.province_id =province.province_id');
		
		$this->db->where('country.display_language_id',$language_id);
		$this->db->where('province.display_language_id',$language_id);
		$this->db->where('city.display_language_id',$language_id);
		
		$this->db->order_by('country.view_order','ASC');
		$this->db->group_by('country.country_id');
		
		$result = $this->db->get('country');
		$country = array(''=>translate_phrase('Select country'));
		if($result->num_rows()>0){
			foreach($result->result_array() as $row){
				$country[$row['country_id']]  = ucfirst($row['description']);
			}
		}
		return $country;
	}
	public function get_nationality($language_id){
		$this->db->select('nationality_id,description');
		$this->db->where('display_language_id',$language_id);
		$this->db->order_by('view_order','ASC');
		$result = $this->db->get('nationality');
		$country = array(''=>translate_phrase('Select nationality'));
		if($result->num_rows()>0){
			foreach($result->result_array() as $row){
				$country[$row['nationality_id']]  = ucfirst($row['description']);
			}
		}
		return $country;
	}
	public function get_city($language_id,$country_id){
		$this->db->select('city_id,city.description');
		$this->db->join('province','city.province_id =province.province_id');
		$this->db->join('country','province.country_id=country.country_id');
		$this->db->where('country.country_id',$country_id);
		$this->db->where('city.display_language_id',$language_id);
                $this->db->where('city.is_active','1');
		
		if($language_id == '1')
			$this->db->order_by('city.description','ASC');
		else
			$this->db->order_by('city.description','ASC');
		
		$result = $this->db->get('city');
		$city = array();
		if($result->num_rows()>0){
			foreach($result->result_array() as $row){
				$city[$row['city_id']]  = ucfirst($row['description']);
			}
		}
		return $city;
	}

	public function get_fb_friends_with_datetix($user_id)
	{
		$this->db->select('usr.user_id, usr.first_name, usr.last_name, usr.facebook_id');
		$this->db->join('user_fb_friend as fb','usr.facebook_id = fb.facebook_id');
		$this->db->where('fb.user_id',$user_id);
		$result = $this->db->get('user as usr');

		$results        = array();
		if($result->num_rows()>0){
			$results    = $result->result_array();
		}
		return $results;

	}

	public function get_custom_user_data($select="*",$where=array())
	{
		$this->db->select($select);
		$this->db->where($where);
		return $this->db->get('user')->row();
	}
	public function get_district($language_id,$city_id=''){
		//TABLE RENAME

		$this->db->select('neighborhood_id,description');
		//$this->db->select('district_id,description');

		$this->db->where('display_language_id',$language_id);
		if($city_id)
		$this->db->where('city_id',$city_id);
		//TABLE RENAME
		$result = $this->db->get('neighborhood');
		//$result = $this->db->get('district');

		$result = $result->result_array();
		$filter_arr = array();
		if($result)
		{
			foreach ($result as $value)
			{
				//TABLE RENAME
				$filter_arr[$value['neighborhood_id']] = $value['description'];

				//$filter_arr[$value['district_id']] = $value['description'];
			}
		}
		return $filter_arr;


		//changed by rajnish
		/*
		$district = '<option value="">'.translate_phrase('Select district').'</option>';
		if($result->num_rows()>0){
		foreach($result->result_array() as $row){
		$district .= '<option value="'.$row['district_id'].'">'.ucfirst($row['description']).'</option>';
		}
		}
		//$district .='</select>';
		return $district;

		*/
	}
	public function get_residence_type($language_id){
		$this->db->select('residence_type_id,description');
		$this->db->where('display_language_id',$language_id);
		$this->db->order_by('view_order','ASC');
		$result = $this->db->get('residence_type');
		$residence = array(''=>translate_phrase('Select residence type'));

		if($result->num_rows()>0){
			foreach($result->result_array() as $row){
				$residence[$row['residence_type_id']]  = ucfirst($row['description']);
			}
		}
		return $residence;
	}
	public function get_carrier_stage($language_id){
		$this->db->select('career_stage_id,description');
		$this->db->where('display_language_id',$language_id);
		$this->db->order_by('view_order','ASC');
		$result = $this->db->get('career_stage');
		$results        = array();
		if($result->num_rows()>0)
		{
			/*=====Change by Hannan=====*/
			//$results[''] = translate_phrase('Select career stage');
			foreach($result->result_array() as $row)
			{
				$results[$row['career_stage_id']] = ucfirst($row['description']);
			}
			/*=====Change by Hannan=====*/
		}
		return $results;
	}
	public function get_relationship_type($language_id){
		$this->db->select('relationship_type_id,description');
		$this->db->where('display_language_id',$language_id);
		$this->db->order_by('view_order','ASC');
		$result = $this->db->get('relationship_type');
		$results        = array();
		if($result->num_rows()>0){
			$results    = $result->result_array();
		}
		return $results;
	}

	public function getHeightMetric($country_id,$language_id)
	{
		$this->db->select('use_meters');
		$this->db->where(array('display_language_id' => $language_id,'country_id' => $country_id));
		$result = $this->db->get('country');
	}
	public function send_verification_mail($user_id){
		//verification code
		$this->load->library('encrypt');
		$user_email          = $this->get_user_email_data($this->encrypt->decode(str_replace('-', '/', $user_id)));
		
		$verification_code   = $user_email['verification_code'];
		$code                = str_replace('/', '-', $this->encrypt->encode($verification_code));
		$from_email          = INFO_EMAIL;//"hongkong@datetix.com";
		$team                = translate_phrase("The ").get_assets('name','DateTix').translate_phrase(" Team");
		$subject             = get_assets('name','DateTix').translate_phrase(" Account Email Verification");
		$body                = translate_phrase("Hi ");

		$first_name = '';		
		if($this->input->post('first_name'))
		{
			$first_name = $this->input->post('first_name');
		}
		else {
			if($user_id = $this->session->userdata('user_id'))
			{
				$first_name = $this->get_user_field($user_id,'first_name');
			}
		}
		
		$body               .= $first_name.",\n";		
		$body               .= translate_phrase("Thanks for applying to ").get_assets('name','DateTix').translate_phrase(". Please click on this link to verify your email address: ")."\n\n";
		$body               .= base_url("email-verification/$user_id/$code")." \n\n";
		$body               .= translate_phrase("If clicking on the above link doesn't work, just copy and paste it into the browser address textbox.")."\n";
		$body               .= translate_phrase("Remember to add $from_email to your address book to ensure that you don't miss any email communications from us.")."\n";
		$body               .= translate_phrase("Thanks,")."\n";
		$body               .= $team;
		//$user_email          = $this->input->post('email');
		$user_email         = $user_email['email_address'];//change by Hannan Munshi.
		$this->send_email($from_email,$user_email,$subject,$body);

	}
	function verification_code(){
		$better_token = md5(uniqid(rand(), true));
		$unique_code  = substr($better_token, 24);
		$uniqueid     = strtoupper($unique_code);
		return $uniqueid;
	}


	public function insert_user($insert_array){

		$new_array['num_date_tix'] = 1000;
		$new_array['applied_date'] = date('Y-m-d H:i:s');
		$new_array['current_city_id'] = $this->session->userdata('sess_city_id');
		
		//add event_id and add_id
		$new_array['event_id'] = $this->session->userdata('event_id');
		$new_array['ad_id'] = $this->session->userdata('ad_id');
		$new_array['website_id'] = get_assets('website_id','0');
        
        $new_array['partner_id']=$this->session->userdata('partner_id');
                
        if(($this->session->userdata('event_id'))){
            $event_id = $this->session->userdata('event_id');
            $ad_id = $this->session->userdata('ad_id');
            $url = $this->session->userdata('url');
            $partner_id = $this->session->userdata('partner_id');
            $new_array['from_url'] = base_url().  url_city_name()."/event.html?id=".$event_id."&src=".$ad_id."&url=".$url."&partner_id=".$partner_id;            
        }
		
		$insert_array   = array_merge($insert_array, $new_array);
		//echo "<pre>";print_r($insert_array);exit;
		
		$this->db->insert('user',$insert_array);
		return $this->db->insert_id();
	}

	/*
	 *  
	- For male, set user.want_age_range_lower = user's age - 5 (but minimum is 18) and user.want_age_range_upper = user's age
	- For female, set user.want_age_range_lower = user's age and user.want_age_range_upper = user's age + 5
	- Get all users from user table with user.want_age_range_lower = NULL and then set value based on above rules for male and female
	- Get all users from user table with user.want_age_range_upper= NULL and then set value based on above rules for male and female
	 * 
	 */ 
	public function update_user_want_age($user_id=0) {
		
		$this -> general_model -> set_table('user');
		$user_select = "user_id,first_name,last_name,gender_id, want_age_range_lower,want_age_range_upper,
		
				CASE
					WHEN
						birth_date != '0000-00-00'
					THEN 
						TIMESTAMPDIFF(YEAR,`birth_date`,CURDATE())
				END as age";
				
		if($user_id)
		{
			$user_condition['user_id'] = $user_id;
			$user_condition['want_age_range_lower'] = null;
			$user_condition['want_age_range_upper'] = null;
			//$this->db->or_where($user_condition);
			
			$users = $this -> general_model -> get($user_select,$user_condition);
			if($users)
			{
				foreach($users  as $user)
				{
					if($user['age'])
					{
						if($user['age'] < 18)
						{
							$user['age'] = 18;
						}
						
						$udpate_user_data = array();
					
						//For Male
						if($user['gender_id'] == 1)
						{
							$want_age_lower = $user['age']-5;
							
							$want_age_lower_limit = $want_age_lower > 18?$want_age_lower : 18;							
							$udpate_user_data['want_age_range_lower'] = $want_age_lower_limit;
							
							$udpate_user_data['want_age_range_upper'] = $user['age'];					
						}
						else {
							//For Female
							$udpate_user_data['want_age_range_lower'] = $user['age'];
							$udpate_user_data['want_age_range_upper'] = $user['age']+5;					
						}
						
						$this -> general_model -> update($udpate_user_data,array('user_id'=>$user['user_id']));					
					}
				}
			}
		}

		
		
	}
	public function insert_email($insert_id){
		$this->load->library('encrypt');
		$insert_array   = array('user_id'=>$insert_id,
                                'is_verified'=>'0',
                                'is_contact'=>'1',
                                'verification_code' => $this->encrypt->encode($this->verification_code()),
                                'created_date'=>date('Y-m-d'),
                                'email_address'=>$this->input->post('email'));
		$this->db->insert('user_email',$insert_array);
	}
	public function verify_email($user_id,$verification_code){

		$data   = array( 'is_verified'=>'1','verification_code'=>$verification_code,'is_contact'=>'1');
		$this->db->where('user_id',$user_id);
		$this->db->update('user_email',$data);
	}
	public function check_email_verification($user_id){
		$this->db->where('is_verified','1');
		$this->db->where('user_id',$user_id);
		$result = $this->db->count_all_results('user_email');
		return $result;
	}
	public function get_year(){
		//$year   = array(''=>translate_phrase('Year'));
		$year   = array();
		for($i=(date('Y') - 18);$i>=1910;$i--){
			$year[$i] = $i;
		}
		return $year;
	}
	public function get_date(){
		//$date   = array(''=>translate_phrase('Day'));
		$date   = array();
		for($i=1;$i<=31;$i++){
			$date[$i] = $i;
		}
		return $date;
	}
	public function get_month(){
		//$month['']   = translate_phrase('Month');
		$month['1']  = translate_phrase('January');
		$month['2']  = translate_phrase('February');
		$month['3']  = translate_phrase('March');
		$month['4']  = translate_phrase('April');
		$month['5']  = translate_phrase('May');
		$month['6']  = translate_phrase('June');
		$month['7']  = translate_phrase('July');
		$month['8']  = translate_phrase('August');
		$month['9']  = translate_phrase('September');
		$month['10'] = translate_phrase('October');
		$month['11'] = translate_phrase('November');
		$month['12'] = translate_phrase('December');
		return $month;

	}
	/**
	 * make a height of CM array
	 * @access public
	 * @author Rajnish Savaliya
	 * @return array()
	 */
	public function get_height_cm(){
		//$height   = array(''=>translate_phrase('height'));
		//$height   = array(''=>' - ');
		$height   = array();
		for($i=145;$i<=335;$i++){
			$height[$i] = $i;
		}
		return $height;
	}

	public function get_feet(){
		//$feet   = array(''=>  translate_phrase('Feet'));
		//$feet   = array(''=>' - ');
		$feet   = array();

		for($i=1;$i<=10;$i++){
			$feet[$i] = $i;
		}
		return $feet;
	}
	public function get_inches(){
		//$inches   = array(''=>translate_phrase('Inches'));
		//$inches = array(''=>' - ');
		$inches = array();

		for($i=0;$i<=11;$i++){
			$inches[$i] = $i;
		}
		return $inches;
	}
	public function get_body_type($language_id){
		$this->db->select('body_type_id,description');
		$this->db->where('display_language_id',$language_id);
		$this->db->order_by('view_order','ASC');
		$result     = $this->db->get('body_type');
		//$body_type  = array(''=>translate_phrase('Select body type'));
		if($result->num_rows()>0){
			foreach($result->result_array() as $row){
				$body_type[$row['body_type_id']]  = ucfirst($row['description']);
			}
		}
		return $body_type;
	}
	public function get_looks($language_id){
		$this->db->select('looks_id,description');
		$this->db->where('display_language_id',$language_id);
		$this->db->order_by('view_order','ASC');
		$result     = $this->db->get('looks');
		$looks      = array(''=>translate_phrase('Select looks'));
		if($result->num_rows()>0){
			foreach($result->result_array() as $row){
				$looks[$row['looks_id']]  = ucfirst($row['description']);
			}
		}
		return $looks;
	}
	public function get_eye_color($language_id){
		$this->db->select('eye_color_id,description');
		$this->db->where('display_language_id',$language_id);
		$this->db->order_by('view_order','ASC');
		$result     = $this->db->get('eye_color');
		$eye_color  = array(''=>translate_phrase('Select eye color'));
		if($result->num_rows()>0){
			foreach($result->result_array() as $row){
				$eye_color[$row['eye_color_id']]  = ucfirst($row['description']);
			}
		}
		return $eye_color;
	}
	public function get_hair_color($language_id){
		$this->db->select('hair_color_id,description');
		$this->db->where('display_language_id',$language_id);
		$this->db->order_by('view_order','ASC');
		$result       = $this->db->get('hair_color');
		$hair_colorr  = array(''=>translate_phrase('Select hair color'));
		if($result->num_rows()>0){
			foreach($result->result_array() as $row){
				$hair_colorr[$row['hair_color_id']]  = ucfirst($row['description']);
			}
		}
		return $hair_colorr;
	}
	public function get_hair_length($language_id){
		$this->db->select('hair_length_id,description');
		$this->db->where('display_language_id',$language_id);
		$this->db->order_by('view_order','ASC');
		$result       = $this->db->get('hair_length');
		$hair_length  = array(''=>translate_phrase('Select hair length'));
		if($result->num_rows()>0){
			foreach($result->result_array() as $row){
				$hair_length[$row['hair_length_id']]  = ucfirst($row['description']);
			}
		}
		return $hair_length;
	}
	public function get_skin_tone($language_id){
		$this->db->select('skin_tone_id,description');
		$this->db->where('display_language_id',$language_id);
		$this->db->order_by('view_order','ASC');
		$result       = $this->db->get('skin_tone');
		$skin_tone    = array(''=>translate_phrase('Select skin tone'));
		if($result->num_rows()>0){
			foreach($result->result_array() as $row){
				$skin_tone[$row['skin_tone_id']]  = ucfirst($row['description']);
			}
		}
		return $skin_tone;
	}
	public function get_relationship_status($language_id){
		$this->db->select('relationship_status_id,description');
		$this->db->where('display_language_id',$language_id);
		$this->db->order_by('view_order','ASC');
		$result     = $this->db->get('relationship_status');
		$results    = array();
		if($result->num_rows()>0){
			$results = $result->result_array();
		}
		return $results;
	}
	public function get_religious_belief($language_id){
		$this->db->select('religious_belief_id,description');
		$this->db->where('display_language_id',$language_id);
		$this->db->order_by('view_order','ASC');
		$result             = $this->db->get('religious_belief');
		//$religious_belief   = array(''=>translate_phrase('Select religious belief'));
		if($result->num_rows()>0){
			foreach($result->result_array() as $row){
				$religious_belief[$row['religious_belief_id']]  = ucfirst($row['description']);
			}
		}
		return $religious_belief;
	}
	public function get_spoken_language($language_id){
		$this->db->select('spoken_language_id,description');
		$this->db->where('display_language_id',$language_id);
		$this->db->order_by('view_order','ASC');
		$result             = $this->db->get('spoken_language');
		$spoken_language   = array(''=>translate_phrase('Select language'));
		if($result->num_rows()>0){
			foreach($result->result_array() as $row){
				$spoken_language[$row['spoken_language_id']]  = ucfirst($row['description']);
			}
		}
		return $spoken_language;
	}
	public function get_proficiency($language_id){
		$this->db->select('spoken_language_level_id,description');
		$this->db->where('display_language_id',$language_id);
		$this->db->order_by('view_order','ASC');
		$result             = $this->db->get('spoken_language_level');
		$proficiency        = array(''=>translate_phrase('Select proficiency'));
		if($result->num_rows()>0){
			foreach($result->result_array() as $row){
				$proficiency[$row['spoken_language_level_id']]  = ucfirst($row['description']);
			}
		}
		return $proficiency;
	}
	public function check_postal_code_exist($language_id,$country_id){
		$this->db->where('country_id',$country_id);
		$this->db->where('has_postal_code','1');
		$this->db->where('display_language_id',$language_id);
		$result = $this->db->count_all_results('country');
		return $result;
	}
	public function check_district_exist($language_id,$city_id){
		$this->db->where('city_id',$city_id);
		$this->db->where('has_district','1');
		$this->db->where('display_language_id',$language_id);
		$result = $this->db->count_all_results('city');
		return $result;
	}

	public function get_city_by_district_id($language_id,$district_id){
		$this->db->select('city_id');
		$this->db->where('neighborhood_id',$district_id);
		$this->db->where('display_language_id',$language_id);
		
		$result = $this->db->get('neighborhood');
		$city_id = "";
		if($result->num_rows()>0){
			$results = $result->result_array();
			
			$city_id = $results['0']['city_id'];  
		}
		return $city_id;
	}
	public function get_eye_wear($language_id){
		$this->db->select('eyewear_id,description');
		$this->db->where('display_language_id',$language_id);
		$this->db->order_by('view_order','ASC');
		$result     = $this->db->get('eyewear');
		$results    = array();
		if($result->num_rows()>0){
			$results = $result->result_array();
		}
		return $results;
	}
	public function upload($file_name){
		if ( ! $this->upload->do_upload($file_name)){
			return $this->upload->display_errors('','');
		}else{
			return 1;
		}
	}

	public function insert_photo($upload_data,$id,$file){
		#inserting user photo
			
		if($file=='fileToUpload'){
			$count  = $this->check_photo_exist($id);
			if($count==0)
			{
				$data = array('user_id'=>$id,'set_primary'=>'1','photo'=>$upload_data['file_name'],'uploaded_time'=>SQL_DATETIME);
			}
			else
			{
				$data = array('user_id'=>$id,'photo'=>$upload_data['file_name'],'uploaded_time'=>SQL_DATETIME);
			}

			$this->db->insert('user_photo',$data);
			return $this->db->insert_id();
		} else if($file=='photo_id_or_passport'){
			#inserting id card/passport photo
			$data = array('photo_id'=>$upload_data['file_name']);
			$this->update_user($id,$data);
			return $this->db->insert_id();
		} else if($file=='photo_diploma'){
			#inserting id card/passport photo
			$data = array('photo_id'=>$upload_data['file_name']);
			$this->update_user($id,$data);
			return TRUE;
		}
	}
	public function check_photo_exist($user_id){
		$this->db->where('user_id',$user_id);
		$result = $this->db->count_all_results('user_photo');
		return $result;
	}

	/**
	 * Function fetch photo results
	 * @access public
	 * @param $user_id - User id
	 * @param $type - which type of photos i.e. User profile pic, education, etc.
	 * @return array   - Photo result
	 * @author Rajnish Savaliya
	 */
	public function get_photos($user_id,$type){

		$image_data = array();

		if($type == 'profile'){

			$this->db->where('user_id',$user_id);
			$this->db->order_by('set_primary','desc');
			$result = $this->db->get('user_photo');
			$result = $result->result_array();
			if($result)
			{
				foreach ($result as $key=>$value) {
					$image_data[$key]  = $value;
					$image_data[$key]['url'] = base_url() . "user_photos/user_$user_id/" . $value['photo'];
				}
			}
		}
		return $image_data;		
	}

	public function get_photo($user_id,$file,$upload_data){
		$current_session = $this->session->userdata('session_id');
		$image_data      = array('url' => '','id'  => '');
		
		if($file=='fileToUpload'){
			// profile photo

			$this->db->where('user_id',$user_id);
			$result = $this->db->get('user_photo');

			$string = "";
			if($result->num_rows()>0){

				$row = $result->row_array();
				$image_data['url'] = base_url() . "user_photos/user_$user_id/" . $row['photo'];
				$image_data['id']  = $row['user_photo_id'];
			}

		} else if($file=='photo_business_card'){
			// business card

			$image_data['url'] = base_url().'user_photos/user_'.$user_id.'/company_'.$current_session.'/'.$upload_data['file_name'];
			$image_data['id']  = "company_$current_session";
		} else if($file=='photo_id_or_passport'){
			// passport

			$this->db->select('photo_id');
			$this->db->where('user_id',$user_id);
			$result  = $this->db->get('user');
			if($result->num_rows()>0){
				$row = $result->row_array();
				$image_data['url'] = base_url() . "user_photos/user_$user_id/" . $row['photo_id'];
				$image_data['id']  = '';
			}
		} else if($file=='photo_diploma'){
			//  school

			$image_data['url'] = base_url().'user_photos/user_'.$user_id.'/school_'.$current_session.'/'.$upload_data['file_name'];
			$image_data['id']  = "school_$current_session";
			$image_data['name'] = $upload_data['file_name'];
		}

		return $image_data;
	}

	public function update_user($user_id,$data){
		$this->db->where('user_id',$user_id);
		$this->db->update('user',$data);
	}
	public function get_photo_name($field,$user_id){
		$this->db->select($field);
		$this->db->where('user_id',$user_id);
		$result     = $this->db->get('user');
		$photo      = "";
		if($result->num_rows()>0){
			$results   = $result->result_array();
			$photo     =  $results['0'][$field];
		}
		return $photo;
	}
	public function set_primary_photo($id,$user_id){
		$current_primary_id = $this->get_current_primary_photo($user_id);
		if($current_primary_id!=""){
			$data    = array('set_primary'=>'0');
			$this->update_primary_photo($current_primary_id,$data);
		}
		$data    = array('set_primary'=>'1');
		$this->update_primary_photo($id,$data);
	}
	public function get_current_primary_photo($user_id){
		$this->db->select('user_photo_id');
		$this->db->where('user_id',$user_id);
		$this->db->where('set_primary','1');
		$r       = "";
		$result  = $this->db->get('user_photo');
		if($result->num_rows()>0){
			$row     = $result->result_array();
			$r       = $row[0]['user_photo_id'];
		}
		return $r;
	}
	public function update_primary_photo($id,$data){
		$this->db->where('user_photo_id',$id);
		$this->db->update('user_photo',$data);
	}
	public function check_use_meters($country_id){
		$this->db->select('use_meters');
		$this->db->where('country_id',$country_id);
		$r       = "";
		$result  = $this->db->get('country');
		if($result->num_rows()>0){
			$row     = $result->result_array();
			$r       = $row[0]['use_meters'];
		}
		return $r;
	}
	public function insert_spoken_language($user_id,$spoken_language_id,$level_id){
		$count   = $this->check_spoken_language_exist($user_id,$spoken_language_id);
		if($count==0){
			$data    = array('user_id'=>$user_id,
                            'spoken_language_id'=>$spoken_language_id,
                            'spoken_language_level_id'=>$level_id );
			$this->db->insert('user_spoken_language',$data);
		}
	}
	public function check_spoken_language_exist($user_id,$spoken_language_id){
		$this->db->where('spoken_language_id ',$spoken_language_id);
		$this->db->where('user_id',$user_id);
		$result = $this->db->count_all_results('user_spoken_language');
		return $result;
	}
	public function remove_spoken_language($user_id,$spoken_language_id){
		$this->db->where('spoken_language_id ',$spoken_language_id);
		$this->db->where('user_id',$user_id);
		$this->db->delete('user_spoken_language');
	}
	public function insert_living_city($user_id,$city_name,$language_id){
		$city_id = $this->get_city_id($city_name,$language_id);
		if($city_id!=""){
			$count   = $this->check_living_city($user_id,$city_id);
			if($count==0){
				$data    = array('user_id'=>$user_id,
                                    'city_id'=>$city_id);
				$this->db->insert('user_city_lived_in',$data);
			}
		}else{
			$data    = array('user_id'=>$user_id,
                                         'city_name'=>$city_name);
			$this->db->insert('user_city_lived_in',$data);
		}
	}

	public function insert_user_lived_in_city($user_id,$country_id,$city_name,$language_id){
		//echo $country_id.'==='.$city_name;die();
		$this->deleteDataFromTable('user_city_lived_in', $user_id);
		if(!empty($user_id) && !empty($country_id) && !empty($city_name))
		{
			$country_ids = explode(',',$country_id);
			$city_names =  explode(',',$city_name);


			foreach($country_ids as $key => $val)
			{
				$data    = array('user_id'=>$user_id,
                                    'city_name'=>$city_names[$key],
                                    'country_id' => $val);
				$this->db->insert('user_city_lived_in',$data);
			}

		}
	}

	public function check_living_city($user_id,$city_id){
		$this->db->where('city_id ',$city_id);
		$this->db->where('user_id',$user_id);
		$result = $this->db->count_all_results('user_city_lived_in');
		return $result;
	}
	public function remove_living_city($user_id,$city_name,$language_id){
		$city_id = $this->get_city_id($city_name,$language_id);
		if($city_id!=""){
			$this->db->where('city_id ',$city_id);
			$this->db->where('user_id',$user_id);
			$this->db->delete('user_city_lived_in');
		}
	}
	public function get_city_id($city_name,$language_id){
		$this->db->select('city_id');
		$this->db->where('description',trim($city_name));
		$this->db->where('display_language_id',$language_id);
		$r       = "";
		$result  = $this->db->get('city');
		if($result->num_rows()>0){
			$row     = $result->result_array();
			$r       = $row[0]['city_id'];
		}
		return $r;
	}
	public function get_descriptive_word($language_id){
		$this->db->select('descriptive_word_id,description');
		$this->db->where('display_language_id',$language_id);
		$this->db->order_by('view_order','ASC');
		$result     = $this->db->get('descriptive_word');
		$results    = array();
		if($result->num_rows()>0){
			$results = $result->result_array();
		}
		return $results;
	}
	public function insert_decriptive_word($user_id,$decriptive_word_id){
		$keyword = $this->input->post('keyword');
		if($keyword==""){
			$count   = $this->check_decriptive_word($user_id,$decriptive_word_id,$keyword);
			if($count==0){
				$data    = array('user_id'=>$user_id,
                                    'descriptive_word_id'=>$decriptive_word_id );
				$this->db->insert('user_descriptive_word',$data);
				echo "1";
			}
		}else{
			#insert user want descriptive word
			$count   = $this->check_decriptive_word($user_id,$decriptive_word_id,$keyword);
			if($count==0){
				$data    = array('user_id'=>$user_id,
                                     'descriptive_word_id'=>$decriptive_word_id);
				$this->db->insert('user_want_descriptive_word',$data);
				echo "1";
			}
		}
	}
	public function check_decriptive_word($user_id,$decriptive_word_id,$keyword){
		if($keyword==""){
			$this->db->where('descriptive_word_id',$decriptive_word_id);
			$this->db->where('user_id',$user_id);
			$result = $this->db->count_all_results('user_descriptive_word');
			return $result;
		}else{
			$this->db->where('descriptive_word_id',$decriptive_word_id);
			$this->db->where('user_id',$user_id);
			$result = $this->db->count_all_results('user_want_descriptive_word');
			return $result;
		}
	}
	public function remove_decriptive_word($user_id,$decriptive_word_id){
		$keyword = $this->input->post('keyword');
		if($keyword==""){
			$this->db->where('descriptive_word_id ',$decriptive_word_id);
			$this->db->where('user_id',$user_id);
			$this->db->delete('user_descriptive_word');
		}else{
			$this->db->where('descriptive_word_id',$decriptive_word_id);
			$this->db->where('user_id',$user_id);
			$this->db->delete('user_want_descriptive_word');
		}
	}
	public function get_personality($language_id){
		$this->db->select('personality_id,description');
		$this->db->where('display_language_id',$language_id);
		$this->db->order_by('view_order','ASC');
		$result     = $this->db->get('personality');
		$results    = array();
		if($result->num_rows()>0){
			$results = $result->result_array();
		}
		return $results;
	}
	public function get_education_level($language_id){
		$this->db->select('education_level_id,description');
		$this->db->where('display_language_id',$language_id);
		$this->db->order_by('view_order','ASC');
		$result     = $this->db->get('education_level');
		$results    = array();
		if($result->num_rows()>0){
			$results = $result->result_array();
		}
		return $results;
	}
	public function get_school_subject($language_id){
		$this->db->select('school_subject_id,description');
		$this->db->where('display_language_id',$language_id);
		$this->db->order_by('view_order','ASC');
		$result     = $this->db->get('school_subject');
		$subject    = array();
		if($result->num_rows()>0){
			foreach($result->result_array() as $row){
				$subject[$row['school_subject_id']]  = ucfirst($row['description']);
			}
		}
		return $subject;
	}
	
	public function get_subject_id($subject_name = ""){
		$this->db->select('school_subject_id,description');
		$this->db->where('description',$subject_name);
		$result     = $this->db->get('school_subject',1);
		
		$subject_id    = "";
		if($result->num_rows()>0){
			foreach($result->result_array() as $row){
				$subject_id = $row['school_subject_id'];
			}
		}
		return $subject_id;
	}
	public function insert_school($language_id,$user_id,$data){

		$user_school_id  = $data['user_school_id'];
		//$school_id       = $this->get_school_id($data['school_name'],$language_id);
		$school_id       = $this->get_school_id($data['school_name']);
		
		$count           = "0";


		# school email
		if($data['school_domain']!=""){
			$school_domain     = $this->get_school_domain_name($data['school_domain']);
			if($data['school_email'] != "")
			{
				$emailHasAtSymbol = strstr(trim($data['school_email']),'@');
				if($emailHasAtSymbol === FALSE)
				{
					$school_email  = trim($data['school_email']).'@'.$school_domain ;
				}
				else
				{
					$school_email  = trim($data['school_email']).$school_domain ;
				}
			}

			else
			$school_email  = "";

		}else{
			$school_email      = trim($data['school_email']);
		}

		//if valid email then insert
		if($school_email!=""){
			if (!filter_var($school_email , FILTER_VALIDATE_EMAIL)) {
				return 1;
			}
		}

		$sc_id        = $school_id ? $school_id : NULL;
		$school_name  = $data['school_name'] ? $data['school_name'] : NULL;
		$education_type = isset($data['education_type'])&&$data['education_type'] ? $data['education_type'] : NULL;
		
		$insert_array = array(  'user_id'=>$user_id,
                                'school_id'=>$sc_id,
                                'school_name'=>$school_name,
                                 'education_type'=>$education_type,
                                'degree_name'=>$data['degree_name'],
                                'is_degree_completed'=>$data['is_degree_completed'],
                                'years_attended_start'=>$data['years_attended_start'],
                                'years_attended_end'=>$data['years_attended_end'],
                                'school_email_address'=>$school_email
		);
		$is_school_update = '';
		//insert or update
		if($user_school_id!="")
		{
			if($this->check_user_school_email_exist($user_school_id,$school_email)==0)
			{	//$new_array      = array("is_verified"=>'0');

				$new_array      = array("is_verified"=>'0','verification_code'=>'');
				$insert_array   = array_merge($insert_array, $new_array);
			}
			$this->update_user_school($user_school_id,$insert_array);
			$last_id            = $user_school_id;
			$is_school_update = '1';
		}else
		{
			$result             = $this->db->insert('user_school',$insert_array);
			$last_id            = $this->db->insert_id();

			//$this->db->last_query();
		}

		$this->session->set_userdata('last_inserted_school_id',$last_id);

		//image uploading
		$this->model_user->insert_image($last_id,$user_id,'school');


		#insert majors and minors
		$major_id           = $data['majors'];
		$minor_id           = $data['minors'];
		if($major_id!="")
		{
			$this->insert_majors($major_id,$last_id);
		}
			
		if($minor_id!="")
			$this->insert_minors($minor_id,$last_id);

		//list school details
		//$school_details     = $this->get_school_details($last_id,$language_id);		
		$school_details     = $this->get_school_details($last_id);

		$list_school        = $this->list_school_details($last_id,$school_details,$language_id,$is_school_update);
		return $list_school;

	}
	public function check_user_school_email_exist($user_school_id,$school_email){
		$this->db->where('user_school_id',$user_school_id);
		$this->db->where('school_email_address',$school_email);
		$count   = $this->db->count_all_results('user_school');
		return $count;
	}
	public function check_user_company_email_exist($user_company_id,$company_email){
		$this->db->where('user_company_id',$user_company_id);
		$this->db->where('company_email_address',$company_email);
		$count   = $this->db->count_all_results('user_job');
		return $count;
	}
	public function insert_image($user_school_id,$user_id,$image_for){
		$this->load->helper('directory');
		$current_session        = $this->session->userdata('session_id');

		if($image_for=='school'){
			$url                = './user_photos/user_'.$user_id.'/school_'.$current_session.'/';
			$file_name          = get_filenames($url);
			$image_name         = $file_name[0]?$user_school_id.'_'.$file_name[0]:"";
			$insert_array       = array('photo_diploma'=>$image_name);
			$this->update_user_school($user_school_id,$insert_array);
			if($file_name[0]!="")
			copy($url.$file_name[0], './user_photos/user_'.$user_id.'/'.$image_name);
		}
		if($image_for=='company'){
			$url                = './user_photos/user_'.$user_id.'/company_'.$current_session.'/';
			$file_name          = get_filenames($url);
			$image_name         = $file_name[0]?$user_school_id.'_'.$file_name[0]:"";
			$insert_array       = array('photo_business_card'=>$image_name);
			$this->update_user_company($user_school_id,$insert_array);
			if($file_name[0]!="")
			copy($url.$file_name[0], './user_photos/user_'.$user_id.'/'.$image_name);

		}

		if(is_dir($url)){
			delete_files($url, true);
			rmdir($url);
		}
	}
	public function update_user_school($user_school_id,$insert_array){
		$this->db->where('user_school_id',$user_school_id);
		$this->db->update('user_school',$insert_array);
	}
	public function send_school_verification_mail($school_email,$verification_code){
		$user                = $this->get_user($this->session->userdata('user_id'));
		$from_email          = INFO_EMAIL;//"hongkong@datetix.com";
		$team                = translate_phrase("The DateTix Team");
		$subject             = translate_phrase("DateTix School Email Verification");
		$body                = translate_phrase("Hi ");
		$body               .= $user->first_name.",\n\r";
		$body               .= translate_phrase("To verify this email address as your school email, please enter or copy and paste the following verification code into the registration form: ").$verification_code."\n\r";
		$body               .= translate_phrase("Remember to add hongkong@datetix.com to your address book to ensure that you don't miss any email communications from us.")."\n\r";
		$body               .= "\n\r".translate_phrase("Thanks,")."\n\r";
		$body               .= $team;
		$user_email          = $school_email;
		$res                 = $this->send_email($from_email,$user_email,$subject,$body);
		return $res;
	}
	public function send_company_verification_mail($company_email,$verification_code){
		$user                = $this->get_user($this->session->userdata('user_id'));
		$from_email          = INFO_EMAIL;//"hongkong@datetix.com";
		$team                = translate_phrase("The ").get_assets('name','DateTix').translate_phrase(" Team");
		$subject             = get_assets('name','DateTix').translate_phrase(" Company Email Verification");
		$body                = translate_phrase("Hi ");
		$body               .= $user->first_name.",\n\r";
		$body               .= translate_phrase("To verify this email address as your company email, please enter or copy and paste the following verification code into the registration form: ").$verification_code."\n\r";
		$body               .= translate_phrase("Remember to add hongkong@datetix.com to your address book to ensure that you don't miss any email communications from us.")."\n\r";
		$body               .= "\n\r".translate_phrase("Thanks,")."\n\r";
		$body               .= $team;
		$user_email          = $company_email;
		$res                 = $this->send_email($from_email,$user_email,$subject,$body);
		return $res;
	}

	public function send_email($from_email,$user_email,$subject,$body,$type="text",$from_text = 'DateTix'){
		
		if($type=="html")
		{
			$config['mailtype']  = 'html';
			$config['charset']   = 'UTF-8';
			$this->load->library('email',$config);
		}
		else
		{	
			$this->load->library('email');		
		}
		
		$unsubscribe_link = base_url('home/unsubscribe/'.$this->utility -> encode($user_email));		
		$body = str_replace('DATETIX_UNSUBSCRIBE_LINK', $unsubscribe_link, $body);		
		$user_website_data = $this->model_user->get_website_by_user_email($user_email);		
		
		$logo_url = isset($user_website_data['logo_url'])?$user_website_data['logo_url']:base_url().'assets/images/logo.png';		
		$website_name = isset($user_website_data['name'])?$user_website_data['name']:"Datetix";		
		$from_email = isset($user_website_data['from_email_address'])?$user_website_data['from_email_address']:$from_email;
		$from_text = isset($user_website_data['from_email_name'])?$user_website_data['from_email_name']:$from_text;
		
		$body = str_replace('WEBSITE_LOGO', $logo_url, $body);
		$body = str_replace('WEBSITE_NAME', $website_name, $body);
		
		$this->email->from($from_email, $from_text);
		$this->email->to($user_email);
		$this->email->subject($subject);
		$this->email->message($body);

		if (!$this->email->send())
		{
			return false;
		}
		else {
			return true;
		}
	}

	public function insert_majors($major_id,$school_id){
		$this->delete_majors($school_id);
		$major_id_array     = explode(',',$major_id);
		for($i=0;$i<count($major_id_array);$i++){
			$insert_array   = array('user_school_id'=>$school_id,
                                   'major_id'=>$major_id_array[$i]);
			$result         = $this->db->insert('user_school_major',$insert_array);
		}
	}
	public function delete_majors($school_id){
		$this->db->where('user_school_id',$school_id);
		$this->db->delete('user_school_major');
	}
	public function insert_minors($minor_id,$school_id){
		$this->delete_minors($school_id);
		$minor_id_array     = explode(',',$minor_id);
		for($i=0;$i<count($minor_id_array);$i++){
			$insert_array   = array('user_school_id'=>$school_id,
                                   'minor_id'=>$minor_id_array[$i]);
			$result         = $this->db->insert('user_school_minor',$insert_array);
		}
	}
	public function delete_minors($school_id){
		$this->db->where('user_school_id',$school_id);
		$this->db->delete('user_school_minor');
	}
	public function get_school_domain_name($school_domain_id){
		$this->db->select('email_domain');
		$this->db->where('school_email_domain_id',$school_domain_id);
		$r       = "";
		$result  = $this->db->get('school_email_domain');
		if($result->num_rows()>0){
			$row     = $result->result_array();
			$r       = $row['0']['email_domain'];
		}
		return $r;
	}
	public function check_school_exist($school_id,$user_id,$school_name){
		if($school_id!="")
		$this->db->where('school_id',$school_id);
		else
		$this->db->where('school_name',$school_name);
		$this->db->where('user_id',$user_id);
		$result = $this->db->count_all_results('user_school');
		return $result;
	}
	public function get_school_id($school_name,$language_id = ""){
		$this->db->select('school_id');
		$this->db->where('school_name',trim($school_name));
		
		if($language_id != "")
			$this->db->where('display_language_id',$language_id);
		
		$r       = "";
		$result  = $this->db->get('school');
		if($result->num_rows()>0){
			$row     = $result->result_array();
			$r       = $row[0]['school_id'];
		}
		return $r;
	}
	public function get_school($language_id){
		$this->db->select('school_id,school_name');
		$this->db->where('display_language_id',$language_id);
		$this->db->where('is_active','1');
		$result = $this->db->get('school');
		$school = array();
		if($result->num_rows()>0){
			foreach($result->result_array() as $row){
				$school[$row['school_id']]  = ucfirst($row['school_name']);
			}
		}
		return $school;
	}
	
	
	public function get_active_schools(){
		$this->db->distinct();
		$this->db->select('school_name, school_id');
		$this->db->where('is_active','1');
		$result = $this->db->get('school');
		return $result->result_array();
	}
	public function get_school_logo($school_name,$language_id = ""){
		$school_id   = $this->get_school_id($school_name,$language_id);
		$this->db->select('logo_url');
		$this->db->where('school_id',$school_id);
		$this->db->where('is_active','1');
		$result             = $this->db->get('school');
		$school_logo        = "";
		if($result->num_rows()>0){
			$row            = $result->row_array();
			if (file_exists("./school_logos/{$row['logo_url']}")) {
				$school_logo = $row['logo_url'];
			}
		}
		return $school_logo;
	}
	public function get_school_email_domain($school_name,$language_id = ""){
		$school_id   = $this->get_school_id($school_name,$language_id);
		$this->db->select('school_email_domain_id,email_domain');
		$this->db->where('school_id',$school_id);
		$result     = $this->db->get('school_email_domain');

		/* Changed by Rajnish */
		$rs = $result->result_array();
		$result_arr = array();
		if($rs)
		{
			foreach ($rs as $value) {
				$result_arr[$value['school_email_domain_id']] = $value['email_domain'];
			}
		}
		else
		{
			return false;
		}
		return $result_arr;

		/*
		 $domain     = "";
		 if($result->num_rows()>0){
			$domain      = '<select style="margin-left:0px;" nmae="school_email_domain_id" id="school_email_domain_id">';
			foreach($result->result_array() as $row){
			$domain .= '<option value="'.$row['school_email_domain_id'].'">'.$row['email_domain'].'</option>';
			}
			$domain     .= '</select>';
			}
			return $domain;
			*/
	}
	public function get_school_details($user_school_id,$language_id = ""){
		$this->db->where('user_school_id',$user_school_id);
		$result             = $this->db->get('user_school');
		$school_details     = array();

		if($result->num_rows()>0){
			$school_details = $result->result_array();

			if($school_details['0']['school_id']!="")
			{
				//Lang Change
				//$school_details['0']['school_name']    =  $this->get_school_name($school_details['0']['school_id'],$language_id);
				$school_details['email']['domain']     =  $this->get_school_email_domain($school_details['0']['school_name'],$language_id);
				if($school_details['email']['domain'] )
				{
					if($school_details['0']['school_email_address'])
					{
						//$email_address = substr($school_details['0']['school_email_address'],0, strpos($school_details['0']['school_email_address'], '@'));
						$email_address = str_replace(reset($school_details['email']['domain']),'',$school_details['0']['school_email_address']);
						$email_address = rtrim($email_address,'@');
					}
				}
				else
				$email_address     = $school_details['0']['school_email_address'];
				$school_details['email']['adress']     =  isset($email_address)?$email_address:"";
			}else{
				$school_details['email']['adress']    = $school_details['0']['school_email_address'];
				$school_details['email']['domain']    = "";
			}
			$school_details['school_logo']    = $this->get_school_logo($school_details['0']['school_name']);
			$school_majors = $this->get_school_majors($user_school_id);
			foreach($school_majors as $row){
				$school_details['majors'][$row['major_id']]=$row['description'];
			}
			$school_minors = $this->get_school_minors($user_school_id);
			foreach($school_minors as $row){
				$school_details['minors'][$row['minor_id']]=$row['description'];
			}
		}
		return $school_details;
	}
	public function get_school_majors($user_school_id,$language_id = ""){
		$this->db->select('major_id,description');
		$this->db->join('school_subject','school_subject.school_subject_id=user_school_major.major_id');
		$this->db->where('user_school_id',$user_school_id);
		
		if($language_id == "")
			$language_id = $this -> session -> userdata('sess_language_id');
		
		$this->db->where('school_subject.display_language_id',$language_id);
		
		$result    = $this->db->get('user_school_major');
		$row       = array();
		if($result->num_rows()>0){
			$row  = $result->result_array();
		}
		
		return $row;
	}
	public function get_school_minors($user_school_id,$language_id=""){
		$this->db->select('minor_id,description');
		$this->db->join('school_subject','school_subject.school_subject_id=user_school_minor.minor_id');
		$this->db->where('user_school_id',$user_school_id);
		
		if($language_id == "")
			$language_id = $this -> session -> userdata('sess_language_id');
		
		$this->db->where('school_subject.display_language_id',$language_id);
		
		$result    = $this->db->get('user_school_minor');
		$row       = array();
		if($result->num_rows()>0){
			$row  = $result->result_array();
		}
		return $row;
	}

	public function list_school_details($last_id,$school_details,$language_id,$is_school_update='',$new_added=''){
		
		$year_start = "";
		$majors     = '';
		$minors     = "";

		$yearStartValue = $school_details['0']['years_attended_start'];
		$yearEndValue   = $school_details['0']['years_attended_end'];

		//if($school_details['0']['years_attended_start']!="0" && $school_details['0']['years_attended_end']!="0" ){
		if($yearStartValue !="0" || $yearEndValue !="0" ){

			if(!empty($yearStartValue) && !empty($yearEndValue))
			$year_start = " - ".$yearStartValue." to ".$yearEndValue;

			else if(!empty ($yearEndValue))
			$year_start = " - ".$yearEndValue;
		}

		if(!empty($school_details['majors'])){
			foreach($school_details['majors'] as $key=>$value){
				$majors .= $value.', ';
			}
			$majors     = rtrim($majors,', ');
		}
		if(!empty($school_details['minors'])){
			foreach($school_details['minors'] as $key=>$value){
				$minors .= $value.', ';
			}
			$minors     = rtrim($minors,', ');
		}

		$startDiv = '';
		$endDiv = '';
		if($is_school_update == '')
		{//insert time by Rajnish
			$startDiv = '<div class="Univ-logoSec" completedYear="'.$yearEndValue.'" id="user_school'.$last_id.'">';
			$endDiv = '</div>';
		}
		$img_path = 'school_logos/'.$school_details['school_logo'];

		$img = '';

		if($school_details['school_logo'] != '' && file_exists($img_path))
		{
			$img = '<img src="'.base_url().$img_path.'" style="max-width:100px; max-height:100px;"  >';
		}
		
		//$year_start - REMOVED after .'----------'.'</div>		
		$html = $startDiv.' <div class="Univ-Leftlogo">'.$img.'</div>
                    <div class="Univ-right-part">
                      <div class="harw-head-main">
                        <div class="harw-head"><img alt="" src="'.base_url().'assets/images/flg01.png">'.str_replace(' ', '&nbsp;',$school_details['0']['school_name']).''.'</div>
                      </div>
                      <div class="harw-subH"><img alt="" src="'.base_url().'assets/images/flg02.png">'.translate_phrase($school_details['0']['degree_name']).'</div>';

		
		//if($majors!='')
			//$html .= '<div class="harw-l-space">'.translate_phrase("Major in ").$majors.'</div>';
			
		//if($minors!='')
			//$html .= '<div class="harw-l-space">'.translate_phrase("Minor in ").$minors.'</div>';


		$divVar = '';
		if($school_details['email']['adress']!="")
		{
			$html .='<div class="harw-subH">
					<div class="harw-mail"> <img alt="" src="'.base_url().'assets/images/flg03.png"> 
					<span>'.$school_details['0']['school_email_address'].'</span> 
					</div>
				';
			$divVar = '</div>';
		}



		if($school_details['0']['is_verified']=="1") {
			$html   .= '<div class="unverified" id="sc_verified_label'.$last_id.'"><span><img class="mar-verify" alt="" src="'.base_url().'assets/images/verified.png"></span></div>';
		}
		else if( $school_details['email']['adress']!=""){
			$html   .= '<div class="unverified" id="sc_verified_label'.$last_id.'"><span>*'.translate_phrase('Unverified').'*</span></div>';
		}
		$html .= $divVar;
		$html.='
	            	<div class="Ed-rM-but">
	                	<a href="javascript:edit_school('.$last_id.');" class="Edit-Button01">'.translate_phrase('Edit').'</a>
	                    <a href="javascript:remove_school('.$last_id.');" title="Remove" class="Delete-Photo01">'.translate_phrase('Remove').'</a>
	               	</div>';

		/*if($school_details['0']['is_verified']!="1") {
			$html.='<div class="send-code" id="email_verified'.$last_id.'">
			<div class="Verification-Button" id="sc_verified'.$last_id.'"><a href="javascript:send_verification_mail('.$last_id.',\'school\','."'".$school_details['0']['school_email_address']."'".');" title="'.translate_phrase("Send Verification Code").'">Send Verification Code</a></div>
			</div>';
			}*/

		/*
		 if($school_details['email']['adress']!="" && $school_details['0']['is_verified']!="1") {
			$html.='<div class="send-code" id="email_verified'.$last_id.'">
			<div class="Verification-Button" id="sc_verified'.$last_id.'">
			<a href="javascript:send_verification_mail('.$last_id.',\'school\','."'".$school_details['0']['school_email_address']."'".');" title="'.translate_phrase("Send Verification Code").'">'.translate_phrase('Send Verification Code').'</a>
			</div>
			</div>';
			}
			*/

		if($school_details['0']['is_verified'] !="1" && $school_details['email']['adress'] != "") {
			$html.='<div class="send-code" id="email_verified'.$last_id.'">';

			$new_email = $school_details['email']['adress'];

			if(isset($school_details['0']['school_email_address']))
			{
				$new_email = $school_details['0']['school_email_address'];
			}


			if($school_details['0']['school_name'] == $new_added || $school_details['0']['verification_code'])
			{
				$html.='
	       		<div class="Verification-Button" id="sc_verified'.$last_id.'"><a href="javascript:send_verification_mail('.$last_id.',\'school\','."'".$school_details['0']['school_email_address']."'".');" title="Send Verification Code">'.translate_phrase('Re-send Verification Code').'</a></div>
	       		
	       		<div id="verified'.$last_id.'" class="verfiy-message" ><div class="varify-text">'.translate_phrase("A verification email has been sent to ").$new_email.'</div>
		                 	<div class="inline-form">
		                 		<label class="input-label">'.translate_phrase("Enter the verification code in the verification email you just received").':</label>
		                 		<div class="input-wrapper">
		                 			<input class="Degree-input" name="school_verification_code'.$last_id.'" id="school_verification_code'.$last_id.'">
		                 			<label class="input-hint error" id="email_error'.$last_id.'" ></label>
		                 		</div>
		                 		<a class="Edit-Button01" href="javascript:;" onclick="verify_email('.$last_id.',\'school\');">'.translate_phrase('Verify').'</a>
		                 	</div>
	                 	</div>';
			}
			else
			{
				$html.='<div class="Verification-Button" id="sc_verified'.$last_id.'"><a href="javascript:send_verification_mail('.$last_id.',\'school\','."'".$school_details['0']['school_email_address']."'".');" title="Send Verification Code">'.translate_phrase('Send Verification Code').'</a></div>';
			}

			$html.='</div>';
		}

		//$html.='</div>'.$endDiv;
		if($is_school_update == '')
		{
			$html.=$endDiv;
		}

		$html .= '</div>';

		/*
		 $list    = '<div  id="user_school'.$last_id.'"><ul>';
		 $list   .= '<li><b>'.str_replace(' ', '&nbsp;',$school_details['0']['school_name']).$year_start.'</b><a class="school-padd-left" href="javascript:edit_school('.$last_id.');" title="Edit">['.translate_phrase("Edit").']</a> <a href="javascript:remove_school('.$last_id.');" title="Remove">['.translate_phrase("Remove").']</a></li>';
		 $list   .= '<li><img src="images/study-class.png" width="14" height="14" alt="class">'.translate_phrase($school_details['0']['degree_name']).'</li>';
		 if($majors!='')
		 $list   .= '<li>'.translate_phrase("Major in ").$majors.'</li>';
		 if($minors!='')
		 $list   .= '<li>'.translate_phrase("Minor in ").$minors .'</li>';
		 if($school_details['0']['school_email_address']!="" && $school_details['0']['is_verified']!="1"){
			$list   .= '<li id="email_verified'.$last_id.'"><span><img src="images/study-mail.png" width="14" height="14" alt="mail">'.$school_details['0']['school_email_address'].'</span><span id="sc_verified'.$last_id.'"><font color="red"> *'.translate_phrase("unverified").'*</font></span><span id="link'.$last_id.'"> <a class="school-padd-left" href="javascript:send_verification_mail('.$last_id.',\'school\','."'".$school_details['0']['school_email_address']."'".');" title="'.translate_phrase("Send Verification Code").'">'.translate_phrase("Send Verification Code").'</a></span><span id="error'.$last_id.'" class=".error_msg"></span></li>';
			}
			if($school_details['0']['is_verified']=="1") {
			$list   .= '<li id="email_verified'.$last_id.'"><span><img src="images/study-mail.png" width="14" height="14" alt="mail">'.$school_details['0']['school_email_address'].'</span><span id="sc_verified'.$last_id.'"><font color="green"><b> *'.translate_phrase("verified").'*</b></font></span></li>';
			}
			$list   .= '</ul>';
			if($school_details['school_logo']){
			$list   .= '<img class="logo" src="'.base_url().'school_logos/'.$school_details['school_logo'].'" width=50 >';
			}
			$list   .= '</div>';
			//return $list;
			*/
		return $html;
	}
	public function get_school_name($school_id,$language_id=""){
		$this->db->select('school_name');
		$this->db->where('school_id',$school_id);
		
		if($language_id != "")
			$this->db->where('display_language_id',$language_id);
			
		$r       = "";
		$result  = $this->db->get('school');
		if($result->num_rows()>0){
			$row     = $result->result_array();
			$r       = $row[0]['school_name'];
		}
		return $r;
	}
	public function remove_school($user_school_id){
		$this->delete_majors($user_school_id);
		$this->delete_minors($user_school_id);
		$this->db->where('user_school_id',$user_school_id);
		$this->db->delete('user_school');
	}
	public function get_company($language_id=""){
		$this->db->select('company_id,company_name');
		
		//$this->db->where('display_language_id',$language_id);
		
		if($language_id != "")
			$this->db->where('display_language_id',$language_id);
		
		
		$this->db->where('is_active','1');
		$result = $this->db->get('company');
		$school = array();
		if($result->num_rows()>0){
			foreach($result->result_array() as $row){
				$school[$row['company_id']]  = ucfirst($row['company_name']);
			}
		}
		return $school;
	}
	public function get_active_companies(){
		$this->db->distinct();
		$this->db->select('company_name,company_id');
		$this->db->where('is_active','1');
		$result = $this->db->get('company');
		return $result->result_array();
	}

	public function get_company_industry($company_name,$language_id=""){
		$company_id = $this->get_company_id($company_name,$language_id);
		$industry = '';
		if($company_id)
		{
			$this->db->select('industry.industry_id,industry.description');
			$this->db->join('company','company.industry_id=industry.industry_id');
			$this->db->where('company_id',$company_id);
			
			//$this->db->where('industry.display_language_id',$language_id);
			
			if($language_id != "")
				$this->db->where('industry.display_language_id',$language_id);
		
		
			$rs = $this->db->get('industry')->row();
			//return $details;
			if($rs)
			{
				return '<label class="inline-label">'.$rs->description.'</label>
						<input type="hidden" id="industry_id" name="industry_id" value="'.$rs->industry_id.'"/>';
			}
		}
		else
		{
			$this->db->select('industry.industry_id,description');
			/*
			 if($company_id){

				$this->db->join('company_industry','company_industry.industry_id=industry.industry_id');
				$this->db->where('company_id',$company_id);
				}
				*/
			$this->db->where('display_language_id',$language_id);
			$this->db->order_by('view_order','ASC');
			$result = $this->db->get('industry');
			if($result->num_rows()>0){

				foreach($result->result_array() as $row){
					$industry[$row['industry_id']]  = ucfirst($row['description']);
				}

				return form_dt_dropdown('industry_id',$industry,'','class="dropdown-dt majordowndomain"',translate_phrase(' Select company industry '),"hiddenfield");
				/*
				 $industry      = '<select nmae="industry_id" id="industry_id">';
				 $industry     .= '<option value="">'.translate_phrase('Select Industry').'</option>';
				 foreach($result->result_array() as $row){
					$industry .= '<option value="'.$row['industry_id'].'">'.$row['description'].'</option>';
					}
					$industry     .= '</select>';
					*/

			}else{
				$industry      = "0";
			}
		}

		return $industry;
	}

	public function get_industry($language_id=""){
		$this->db->select('industry.industry_id,description');
		
		if($language_id !="")
			$this->db->where('display_language_id',$language_id);
		
		$this->db->order_by('view_order','ASC');
		$result     = $this->db->get('industry');
		$industry   = "";
		if($result->num_rows()>0){

			//Edited by Rajnish
			foreach($result->result_array() as $row){
				$industry[$row['industry_id']]  = ucfirst($row['description']);
			}
			/*
			 $industry      = '<select nmae="industry_id" id="industry_id">';
			 $industry     .= '<option value="">'.translate_phrase('Select Industry').'</option>';
			 foreach($result->result_array() as $row){
				$industry .= '<option value="'.$row['industry_id'].'">'.$row['description'].'</option>';
				}
				$industry     .= '</select>';
				*/
		}

		return $industry;
	}
	public function get_job_functions($language_id=""){
		$this->db->select('job_function_id,description');
		
		
		if($language_id !="")
			$this->db->where('display_language_id',$language_id);
		
		
		$this->db->order_by('view_order','ASC');
		$result = $this->db->get('job_function');
		//$job_function = array(''=>translate_phrase('Select Job Function'));
		if($result->num_rows()>0){
			foreach($result->result_array() as $row){
				$job_function[$row['job_function_id']]  = ucfirst($row['description']);
			}
		}
		return $job_function;
	}
	public function get_company_id($company_name,$language_id=""){
		$this->db->select('company_id');
		$this->db->where('company_name',trim($company_name));
		
		if($language_id !="")
			$this->db->where('display_language_id',$language_id);
		
		$result  = $this->db->get('company');
		$r       = "";
		if($result->num_rows()>0){
			$row     = $result->result_array();
			$r       = $row['0']['company_id'];
		}
		return $r;
	}
	public function get_company_logo($company_name,$language_id=""){
		$company_id   = $this->get_company_id($company_name,$language_id);
		$this->db->select('logo_url');
		$this->db->where('company_id',$company_id);
		$this->db->where('is_active','1');
		$result             = $this->db->get('company');
		$company_logo       = "";
		if($result->num_rows()>0){
			$row            = $result->row_array();
			if (file_exists("./company_logos/{$row['logo_url']}")) {
				$company_logo = $row['logo_url'];
			}
		}
		return $company_logo;
	}
	public function get_company_email_domain($company_name,$language_id=""){
		$company_id   = $this->get_company_id($company_name,$language_id);
		$result_arr = array();
		if($company_id){
			$this->db->select('company_email_domain_id,email_domain');
			$this->db->where('company_id',$company_id);
			$result     = $this->db->get('company_email_domain');
			if($result->num_rows()>0){
				$rs = $result->result_array();
					
				if($rs)
				{
					foreach ($rs as $value) {
						$result_arr[$value['company_email_domain_id']] = $value['email_domain'];
					}
				}
					
				/*
				 $domain      = '<select style="margin-left:0px;" nmae="company_email_domain_id" id="company_email_domain_id">';
				 foreach($result->result_array() as $row){
					$domain .= '<option value="'.$row['company_email_domain_id'].'">'.$row['email_domain'].'</option>';
					}
					$domain     .= '</select>';
					*/
			}
		}
		else
		{
			return false;
		}
		return $result_arr;
	}
	public function insert_company($language_id,$user_id,$data){

		$user_company_id  = $data['user_company_id'];
		$count            = "0";
		$company_id       = $this->get_company_id($data['company_name'],$language_id);
		
		/*---------------------------*/
		if($data['company_domain']!=""){
			$school_domain     = $this->get_company_domain_name($data['company_domain']);
			if($data['company_email'] != "")
			{
				$emailHasAtSymbol = strstr(trim($data['company_email']),'@');
				if($emailHasAtSymbol === FALSE)
				{
					$company_email  = trim($data['company_email']).'@'.$school_domain ;
				}
				else
				{
					$company_email  = trim($data['company_email']).$school_domain ;
				}
			}

			else
			$company_email  = "";

		}else{
			$company_email      = trim($data['company_email']);
		}
		/*---------------------------*/
		if($company_email!=""){
			if (!filter_var(trim($company_email) , FILTER_VALIDATE_EMAIL)) {
				return 1;
			}
		}

		$city_id      = $this->get_city_id($data['job_city_id'],$language_id);
		$insert_array = array(  'user_id'=>$user_id,
                                    'show_company_name'=>$data['show_company_name'],
                                    'job_title'=>$data['job_title'],
                                    'years_worked_start'=>$data['year_work_start'],
                                    'years_worked_end'=>$data['year_work_end'],
                                    'company_email_address'=>$company_email
		);
		if($company_id!=""){
			$new_array    = array("company_id"=>$company_id,"company_name"=>NULL);
			$insert_array = array_merge($insert_array, $new_array);
		}else{
			$new_array    = array("company_name"=>$data['company_name'],"company_id"=>NULL);
			$insert_array = array_merge($insert_array, $new_array);
		}
		
		if($city_id!=""){
			$new_array    = array("job_city_id"=>$city_id);
			$insert_array = array_merge($insert_array, $new_array);
		}else{
			$new_array    = array("job_city_name"=>$data['job_city_id'],"job_city_id"=>null);
			$insert_array = array_merge($insert_array, $new_array);
		}
		if($data['industry_id']!=""){
			$new_array    = array("industry_id"=>$data['industry_id']);
			$insert_array = array_merge($insert_array, $new_array);
		}
		if($data['job_function_id']!=""){
			$new_array    = array("job_function_id"=>$data['job_function_id']);
			$insert_array = array_merge($insert_array, $new_array);
		}
		if($user_company_id!=""){

			if($this->check_user_company_email_exist($user_company_id,$company_email)==0)
			{
				//$new_array      = array("is_verified"=>'0');
				$new_array      = array("is_verified"=>'0','verification_code'=>'');
				$insert_array   = array_merge($insert_array, $new_array);
			}
			$this->db->where('user_company_id',$user_company_id);
			$result      = $this->db->update('user_job',$insert_array);
			$last_id     = $user_company_id;
		}else{
			$result      = $this->db->insert('user_job',$insert_array);
			$last_id     = $this->db->insert_id();
		}
		$this->session->set_userdata('last_inserted_company_id',$last_id);

		//image uploading
		$this->model_user->insert_image($last_id,$user_id,'company');

		$copmany_details = $this->get_company_details($last_id,$language_id);
		$list            = $this->list_company_details($last_id,$copmany_details,$language_id);
		return $list;
		//}
		//return 0;
	}
	public function update_user_company($user_company_id,$insert_array){
		$this->db->where('user_company_id',$user_company_id);
		$this->db->update('user_job',$insert_array);
	}
	public function check_company_exist($company_id,$user_id,$compnay_name){
		if($company_id!="")
		$this->db->where('company_id',$company_id);
		else
		$this->db->where('company_name',$compnay_name);
			
		$this->db->where('user_id',$user_id);
		$result = $this->db->count_all_results('user_job');
		return $result;
	}
	public function get_company_details($last_id,$language_id=""){

		$language_id = $this->session->userdata('sess_language_id');;
		$this->db->select('user_job.*,city.description as job_loc');
		$this->db->where('user_company_id',$last_id);
		$this->db->join('city','city.city_id=user_job.job_city_id','LEFT');

		$result             = $this->db->get('user_job');
		$company_details    = array();
		if($result->num_rows()>0){
			$company_details     = $result->result_array();
		}
		if($company_details['0']['company_id']!=""){
			$company_details['0']['company_name']   = $this->get_company_name($company_details['0']['company_id'],$language_id);
			$company_details['0']['domain']         = $this->get_company_email_domain($company_details['0']['company_name'],$language_id);
			if($company_details['0']['domain'] ){
				if($company_details['0']['company_email_address']){
					//$email_address = substr($company_details['0']['company_email_address'],0, strpos($company_details['0']['company_email_address'], '@'));
					$email_address = str_replace(reset($company_details['0']['domain']),'',$company_details['0']['company_email_address']);
					$email_address = rtrim($email_address,'@');
				}
			}else
			$email_address                      = $company_details['0']['company_email_address'];
			$company_details['0']['adress']         = isset($email_address)?$email_address:"";
		}else{
			$company_details['0']['adress']        = $company_details['0']['company_email_address'];
			$company_details['0']['domain']        = "";
		}
		$company_details['0']['job_city']           = $company_details['0']['job_city_id']?$company_details['0']['job_loc'] :$company_details['0']['job_city_name'] ;

		$company_details['0']['company_logo']       = $this->get_company_logo($company_details['0']['company_name']);
		return $company_details['0'];
	}
	public function list_company_details($last_id,$copmany_details,$language_id,$new_added_company = ''){

		/*
		 if(date('Y')==$copmany_details['years_worked_end'])
		 $year_end   = translate_phrase("Present");
		 else
		 $year_end   = $copmany_details['years_worked_end'];
		 */

		$year_end   = $copmany_details['years_worked_end'];
		$year     = "";
		if($copmany_details['years_worked_start']!="0" && $year_end !="" ){
			if($year_end == 9999)
			{
				$year_end = 'Present';
			}
			$year = '- '.$copmany_details['years_worked_start']." to ".$year_end;
		}

		$startDiv = '';
		$endDiv = '';
		/*
		 if($is_school_update == '')
		 {//insert time by Rajnish

		 }
		 */


		$img_path = 'company_logos/'.$copmany_details['company_logo'];

		$img = '';

		if($copmany_details['company_logo'] != '' && file_exists($img_path))
		{
			$img = '<img src="'.base_url().$img_path.'" style="max-width:100px; max-height:100px;" >';
		}
		
		//'.$year.' - REMOVED AFTER NAME
		$startDiv = '<div class="Univ-logoSec" id="user_company'.$last_id.'">';
		$endDiv = '</div>';
		$html = $startDiv.' <div class="Univ-Leftlogo">'.$img.'</div>
                    <div class="Univ-right-part">
                      <div class="harw-head-main">
                        <div class="harw-head"><!--<img alt="" src="'.base_url().'assets/images/flg01.png">-->'.str_replace(' ', '&nbsp;',$copmany_details['company_name']).' </div>
                      </div>
                      <div class="harw-subH"><!--<img alt="" src="'.base_url().'assets/images/flg02.png">-->'.$copmany_details['job_title'].'</div>';
		if($copmany_details['job_city']!="0"){
			$html .= '<div class="harw-l-space">'.$copmany_details['job_city'].'</div>';
		}
		$html .='<div class="harw-subH">';

		if($copmany_details['company_email_address'] != ""){
			$html .='<div class="harw-mail"> <img alt="" src="'.base_url().'assets/images/flg03.png"> <span>'.$copmany_details['company_email_address'].'</span> </div>';
		}
		if($copmany_details['is_verified']=="1") {
			$html   .= '<div class="unverified" id="com_verified_label'.$last_id.'"><span><img class="mar-verify" alt="" src="'.base_url().'assets/images/verified.png"></span></div>';
		}
		else if($copmany_details['company_email_address'] != ""){
			$html   .= '<div class="unverified" id="com_verified_label'.$last_id.'"><span>*'.translate_phrase('Unverified').'*</span></div>';
		}

		$html.='</div>
	            	<div class="Ed-rM-but">
	                	<a href="javascript:edit_company('.$last_id.');" class="Edit-Button01">'.translate_phrase('Edit').'</a>
	                    <a href="javascript:remove_company('.$last_id.');" title="Remove" class="Delete-Photo01">'.translate_phrase('Remove').'</a>
	               	</div>';

		if($copmany_details['is_verified'] !="1" && $copmany_details['company_email_address'] != "") {
			$html.='<div class="send-code" id="comp_email_verified'.$last_id.'">';

			if(strcasecmp($copmany_details['company_name'],$new_added_company) == 0 || $copmany_details['verification_code'])
			{
				$html.='
	       		<div class="Verification-Button" id="com_verified'.$last_id.'"><a href="javascript:send_verification_mail('.$last_id.',\'company\','."'".$copmany_details['company_email_address']."'".');" title="Send Verification Code">'.translate_phrase('Re-send Verification Code').'</a></div>
	       		
	       		<div id="company_verified'.$last_id.'" class="verfiy-message" ><div class="varify-text">'.translate_phrase("A verification email has been sent to ").$copmany_details['company_email_address'].'</div>
		                 	<div class="inline-form">
		                 		<label class="input-label">'.translate_phrase("Enter the verification code in the verification email you just received").':</label>
		                 		<div class="input-wrapper">
		                 			<input class="Degree-input" name="company_verification_code'.$last_id.'" id="company_verification_code'.$last_id.'">
		                 			<label class="input-hint error" id="company_error'.$last_id.'" ></label>
		                 		</div>
		                 		<a class="Edit-Button01" href="javascript:;" onclick="verify_email('.$last_id.',\'company\');">'.translate_phrase('Verify').'</a>
		                 	</div>
	                 	</div>';
			}
			else
			{
				$html.='<div class="Verification-Button" id="com_verified'.$last_id.'"><a href="javascript:send_verification_mail('.$last_id.',\'company\','."'".$copmany_details['company_email_address']."'".');" title="Send Verification Code">'.translate_phrase('Send Verification Code').'</a></div>';
			}

			$html.='</div>';
		}

		$html.='</div>'.$endDiv;

		/*
		 $list    = '<div id="user_company'.$last_id.'"><ul>';
		 $list   .= '<li><b>'.str_replace(' ', '&nbsp;',$copmany_details['company_name']).$year.'</b><a class="school-padd-left" href="javascript:edit_company('.$last_id.');" title="Edit">['.translate_phrase('Edit').']</a> <a href="javascript:remove_company('.$last_id.');" title="Remove">['.translate_phrase('Remove').']</a></li>';
		 $list   .= '<li>'.$copmany_details['job_title'].'</li>';
		 $list   .= '<li>'.$copmany_details['job_city'].'</li>';
		 if($copmany_details['company_email_address']!="" && $copmany_details['is_verified']!="1"){
			$list   .= '<li id="comp_email_verified'.$last_id.'"><span>'.$copmany_details['company_email_address'].'</span><span id="com_verified'.$last_id.'"><font color="red"> *'.translate_phrase('unverified').'*</font></span><span id="link_com'.$last_id.'"> <a class="school-padd-left" href="javascript:send_verification_mail('.$last_id.',\'company\','."'".$copmany_details['company_email_address']."'".');" title="Send Verification Code">'.translate_phrase('Send Verification Code').'</a></span><span id="error_com'.$last_id.'" class=".error_msg"></span></li>';
			}

			if($copmany_details['is_verified']=="1") {
			$list   .= '<li id="comp_email_verified'.$last_id.'"><span><img src="images/study-mail.png" width="14" height="14" alt="mail">'.$copmany_details['company_email_address'].'</span><span id="sc_verified'.$last_id.'"><font color="green"><b> *'.translate_phrase('verified').'*</b></font></span></li>';
			}
			$list   .= '</ul>';
			if($copmany_details['company_logo']){
			$list   .= '</ul><img class="logo" src="'.base_url().'company_logos/'.$copmany_details['company_logo'].'" width=50>';
			}
			$list   .= '</div>';
			*/
		return $html;
	}
	public function  get_company_domain_name($company_domain_id){
		$this->db->select('email_domain');
		$this->db->where('company_email_domain_id',$company_domain_id);
		$r       = "";
		$result  = $this->db->get('company_email_domain');
		if($result->num_rows()>0){
			$row     = $result->result_array();
			$r       = $row['0']['email_domain'];
		}
		return $r;
	}
	public function get_company_name($company_id,$language_id=""){
		$this->db->select('company_name');
		$this->db->where('company_id',$company_id);
		if($language_id !="")
			$this->db->where('display_language_id',$language_id);
		
		$r       = "";
		$result  = $this->db->get('company');
		if($result->num_rows()>0){
			$row     = $result->result_array();
			$r       = $row['0']['company_name'];
		}
		return $r;
	}
	public function remove_company($user_company_id){
		$this->db->where('user_company_id',$user_company_id);
		$this->db->delete('user_job');
	}
	public function email_verification(){
		$verification_code  = $this->input->post('verification_code');
		$id                 = $this->input->post('id');
		$value              = $this->input->post('value');
		//$data               = array( 'is_verified'=>'1','verification_code'=>$verification_code);

		$data               = array( 'is_verified'=>'1','verification_code'=>'');

		if($value=='school'){
			$this->db->where('user_school_id',$id);
			$this->db->update('user_school',$data);
		}else{
			$this->db->where('user_company_id',$id);
			$this->db->update('user_job',$data);
		}
	}
	public function get_child_status($language_id){
		$this->db->select('child_status_id,description');
		$this->db->where('display_language_id',$language_id);
		$this->db->order_by('view_order','ASC');
		$result         = $this->db->get('child_status');
		$child_status   = array(''=>translate_phrase('Select child status'));
		if($result->num_rows()>0){
			foreach($result->result_array() as $row){
				$child_status[$row['child_status_id']]  = ucfirst($row['description']);
			}
		}
		return $child_status;
	}

	public function get_child_plans($language_id){
		$this->db->select('child_plan_id,description');
		$this->db->where('display_language_id',$language_id);
		$this->db->order_by('view_order','ASC');
		$result        = $this->db->get('child_plan');
		$child_plans   = array(''=>translate_phrase('Select child plans'));
		if($result->num_rows()>0){
			foreach($result->result_array() as $row){
				$child_plans[$row['child_plan_id']]  = ucfirst($row['description']);
			}
		}
		return $child_plans;
	}
	public function get_drinking_status($language_id){
		$this->db->select('drinking_status_id,description');
		$this->db->where('display_language_id',$language_id);
		$this->db->order_by('view_order','ASC');
		$result = $this->db->get('drinking_status');
		$results        = array();
		if($result->num_rows()>0){
			$results    = $result->result_array();
		}
		return $results;
	}
	public function get_smoking_status($language_id){
		$this->db->select('smoking_status_id,description');
		$this->db->where('display_language_id',$language_id);
		$this->db->order_by('view_order','ASC');
		$result = $this->db->get('smoking_status');
		$results        = array();
		if($result->num_rows()>0){
			$results    = $result->result_array();
		}
		return $results;
	}
	public function get_exercise_frequency($language_id){
		$this->db->select('exercise_frequency_id,description');
		$this->db->where('display_language_id',$language_id);
		$this->db->order_by('view_order','ASC');
		$result = $this->db->get('exercise_frequency');
		$results        = array();
		if($result->num_rows()>0){
			$results    = $result->result_array();
		}
		return $results;
	}
	public function get_annual_income_range($country_id){
		$language_id = $this->session->userdata('sess_language_id');

		$this->db->select('annual_income_range.annual_income_range_id,annual_income_range.description,currency.description as currency_sign');
		$this->db->join('country','country.country_id = annual_income_range.country_id');
		$this->db->join('currency','currency.currency_id=country.currency_id');
		$this->db->where('country.country_id',$country_id);
		$this->db->where('currency.display_language_id',$language_id);
		$this->db->where('country.display_language_id',$language_id);

		$this->db->order_by('annual_income_range.view_order','ASC');

		$result = $this->db->get('annual_income_range');
		$results        = array();
		if($result->num_rows()>0){
			$results    = $result->result_array();
			$results = array();
			foreach($result->result_array() as $row){
				if($row['description']['0'] == '<')
				{
					$results[$row['annual_income_range_id']]  = $row['description']['0'].' '.$row['currency_sign'].' '.str_replace("<"," ",$row['description']);
				}
				else if( $row['description']['0'] == '>')
				{
					$results[$row['annual_income_range_id']]  = $row['description']['0'].' '.$row['currency_sign'].' '.str_replace(">"," ",$row['description']);
				}
				else
				{
					$results[$row['annual_income_range_id']]  = $row['currency_sign'].' '.$row['description'];
				}
			}
		}
		return $results;
	}

	public function get_user($user_id)
	{
		$this->db->where('user_id', $user_id);
		return $this->db->get('user')->row();
	}
	public function select_importance($language_id){
		$this->db->select('criteria_importance_id,description');
		$this->db->where('display_language_id',$language_id);
		$this->db->order_by('view_order','ASC');
		$result        = $this->db->get('criteria_importance');
		$criteria_importance   = array(''=>translate_phrase('Select importance'));
		if($result->num_rows()>0){
			foreach($result->result_array() as $row){
				$criteria_importance[$row['criteria_importance_id']]  = ucfirst($row['description']);
			}
		}
		return $criteria_importance;
	}
	public function get_industries($language_id){
		$this->db->select('industry.industry_id,description');
		$this->db->where('display_language_id',$language_id);
		$this->db->order_by('view_order','ASC');
		$result     = $this->db->get('industry');
		$industry   = array();
		if($result->num_rows()>0){
			foreach($result->result_array() as $row){
				$industry[$row['industry_id']]=$row['description'];
			}

		}
		return $industry;
	}
	public function get_currency($language_id){
		$this->db->select('currency_id,description');
		$this->db->where('display_language_id',$language_id);
		$this->db->order_by('view_order','ASC');
		$result     = $this->db->get('currency');
		$industry   = array();
		if($result->num_rows()>0){
			foreach($result->result_array() as $row){
				$industry[$row['currency_id']]=$row['description'];
			}

		}
		return $industry;
	}
	public function insert_user_step2($user_id){

		#height range lower
		$feet              = $this->input->post('feetFrom');
		$height_lower      = NULL;
		if($this->input->post('feetFrom')!="" && $this->input->post('inchFrom')!=""){
			$inch_to_feet = $this->input->post('inchFrom')* 0.083333;
			$feet         = $feet+$inch_to_feet;
		}
		if( $this->input->post('feetFrom')!=""){
			$cm           = $feet * 30.48;
			$height_lower = round($cm);
		}
		#height range upper
		$feet_higher       = $this->input->post('feetTo');
		$height_higher     = NULL;
		if($this->input->post('feetTo')!="" && $this->input->post('inchTo')!=""){
			$inch_to_feet = $this->input->post('inchTo')* 0.083333;
			$feet_higher  = $feet_higher+$inch_to_feet;
		}
		if($this->input->post('feetTo')!=""){
			$cm           = $feet_higher * 30.48;
			$height_higher = round($cm);
		}

		$preferredDateDays                    = $this->input->post('daysPreference')       ?$this->input->post('daysPreference'):NULL;
		$dateTypePrefrences                   = $this->input->post('dateTypePreference')   ?$this->input->post('dateTypePreference'):NULL;
		$dateTypePrefrencesOther              = $this->input->post('otherDateTypePrefrence')   ?$this->input->post('otherDateTypePrefrence'):NULL;

		$want_age_lower              = $this->input->post('ageRangeLowerLimit')   ?$this->input->post('ageRangeLowerLimit'):NULL;
		$want_age_upper              = $this->input->post('ageRangeUpperLimit')   ?$this->input->post('ageRangeUpperLimit'):NULL;

		
		$want_age_range_importance            = $this->input->post('wantAgeRangeImportance')                  ?$this->input->post('wantAgeRangeImportance'):NULL;
		$want_height_range_importance         = $this->input->post('wantHeightImportance')                    ?$this->input->post('wantHeightImportance'):NULL;
		
		$want_ethnicity_importance            = $this->input->post('wantEthnicityImportance')                 ?$this->input->post('wantEthnicityImportance'):NULL;
		$want_personality_importance          = $this->input->post('wantPersonalityImportance')               ?$this->input->post('wantPersonalityImportance'):NULL;
		$want_education_level_importance      = $this->input->post('wantEducationImportance')                 ?$this->input->post('wantEducationImportance'):NULL;
		$want_industry_importance             = $this->input->post('wantParticularIndustryImportance')        ?$this->input->post('wantParticularIndustryImportance'):NULL;
		$not_want_to_date                     = $this->input->post('not_want_to_date')                        ?$this->input->post('not_want_to_date'):NULL;
		$ideal_date                           = $this->input->post('ideal_date')                              ?$this->input->post('ideal_date'):NULL;
		$looking_for_importance               = $this->input->post('wantRelationshipGoalImportance')          ?$this->input->post('wantRelationshipGoalImportance') : NULL;
		
		$ageRangeLowerLimit = $this->input->post('ageRangeLowerLimit');
		$ageRangeUpperLimit = $this->input->post('ageRangeUpperLimit' );

		$heard_about_us      = $this->input->post('heared_abou_us')?$this->input->post('heared_abou_us'):NULL;
		$insert_array   = array(
			'want_age_range_lower'=>!empty($ageRangeLowerLimit) ? $ageRangeLowerLimit : NULL,
			'want_age_range_upper'               =>!empty($ageRangeUpperLimit) ? $ageRangeUpperLimit : NULL,
			'want_age_range_importance'          =>!empty($want_age_range_importance) ? $want_age_range_importance : NULL,
			'want_height_range_lower'            =>!empty($height_lower) ? $height_lower : NULL,
			'want_height_range_upper'            =>!empty($height_higher) ? $height_higher : NULL,
			'want_height_range_importance'       =>!empty($want_height_range_importance) ? $want_height_range_importance : NULL,
			'want_ethnicity_importance'          =>!empty($want_ethnicity_importance) ? $want_ethnicity_importance : NULL,
			'want_personality_importance'        =>!empty($want_personality_importance) ? $want_personality_importance : NULL,
			'want_education_level_importance'    =>!empty($want_education_level_importance) ? $want_education_level_importance : NULL,
			'want_industry_importance'           =>!empty($want_industry_importance) ? $want_industry_importance : NULL,
			'want_common_interest_importance'	=>!empty($want_common_interest_importance) ? $want_common_interest_importance : NULL,
			'preferred_date_days'               =>$preferredDateDays,
			'ideal_date'                        =>$this->input->post('ideal_date')?$this->input->post('ideal_date'):NULL,
			'not_want_to_date'                  =>$this->input->post('not_want_to_date')?$this->input->post('not_want_to_date'):NULL,
			'matchmaking_selectivity'			=>$this->input->post('matchmaking_selectivity')?$this->input->post('matchmaking_selectivity'):NULL,
			'how_you_heard_about_us_id'=>$heard_about_us,
			'how_you_heard_about_us_other'=>$this->input->post('heard_about_us_other'),
			'completed_application_step'        =>'2'
		);
		
		$this->db->where('user_id',$user_id);
		$this->db->update('user',$insert_array);
        //add by Rajnish
		if ($prefered_contacts = $this -> input -> post('contactMethodPreference')) {
			$pref_contact = explode(',', $prefered_contacts);
			$this -> insert_user_preferred_contact_method($user_id, $pref_contact);
		}
		
		$this->genericInsert($user_id,'ethnicity',$this->input->post('ethnicityPreference' ));
		$this->genericInsert($user_id,'body_type',           $this->input->post('bodyTypePreference'));
		$this->genericInsert($user_id,'industry',    	     $this->input->post('industryPreference'));
		$this->genericInsert($user_id,'descriptive_word',$this->input->post('personalityPreference'));
		
		$this->clear_data($user_id,'user_preferred_date_type');
		$this->insert_user_preferred_date_type($user_id,$dateTypePrefrences,$dateTypePrefrencesOther);
	}

	public function insert_filters($user_id,$dataToInsert)
	{
		$this->insert_ideal_match_filters($user_id, $dataToInsert);
	}

	public function updateIdealMatch($user_id,$userMeters)
	{
		if($userMeters == 0 && $userMeters !== FALSE)
		{
			$feet = $this->input->post('feetFrom');
			$height_lower = "";
			
			if($this->input->post('feetFrom')!="" && $this->input->post('inchFrom')!=""){
				$inch_to_feet = $this->input->post('inchFrom')* 0.083333;
				$feet         = $feet+$inch_to_feet;
			}

			if( $this->input->post('feetFrom')!=""){
				$cm           = $feet * 30.48;
				$height_lower = round($cm);
			}
				
			//height range upper
			$feet_higher       = $this->input->post('feetTo');
			$height_higher     = "";

			if($this->input->post('feetTo')!="" && $this->input->post('inchTo')!=""){
				$inch_to_feet = $this->input->post('inchTo')* 0.083333;
				$feet_higher  = $feet_higher+$inch_to_feet;
			}
			
			if($this->input->post('feetTo')!=""){
				$cm           = $feet_higher * 30.48;
				$height_higher = round($cm);
			}
		}
		else
		{
			$height_higher = $this->input->post('centemetersTo');
			$height_lower  = $this->input->post('centemetersFrom');
		}

		$want_age_range_importance            = $this->input->post('wantAgeRangeImportance')                  ?$this->input->post('wantAgeRangeImportance'):NULL;
		$want_height_range_importance         = $this->input->post('wantHeightImportance')                    ?$this->input->post('wantHeightImportance'):NULL;
		$want_body_type_importance            = $this->input->post('wantBodyTypeImportance')                  ?$this->input->post('wantBodyTypeImportance'):NULL;
		$want_looks_importance                = $this->input->post('wantLooksImportance')                     ?$this->input->post('wantLooksImportance'):NULL;
		//we will need to add a field in database here. previously only one value was stored for looks but now there are two values i.e.looks from and looks to.
		$want_looks_range_higher_id           = $this->input->post('looksTo')                                 ?$this->input->post('looksTo'):NULL;
		$want_looks_range_lower_id           = $this->input->post('looksFrom')                                ?$this->input->post('looksFrom'):NULL;
		$want_ethnicity_importance            = $this->input->post('wantEthnicityImportance')                 ?$this->input->post('wantEthnicityImportance'):NULL;
		$want_personality_importance          = $this->input->post('wantPersonalityImportance')               ?$this->input->post('wantPersonalityImportance'):NULL;
		$want_education_level_importance      = $this->input->post('wantEducationImportance')                 ?$this->input->post('wantEducationImportance'):NULL;
		$want_school_subject_importance       = $this->input->post('wantSubjectImportance')                   ?$this->input->post('wantSubjectImportance'):NULL;
		$want_career_stage_importance         = $this->input->post('wantOpenForDatingImportance')             ?$this->input->post('wantOpenForDatingImportance'):NULL;
		$want_annual_income_importance        = $this->input->post('wantIncomePrefrenceImportance')           ?$this->input->post('wantIncomePrefrenceImportance'):NULL;
		$want_job_function_importance         = $this->input->post('wantParticularJobImportance')             ?$this->input->post('wantParticularJobImportance'):NULL;
		$want_industry_importance             = $this->input->post('wantParticularIndustryImportance')        ?$this->input->post('wantParticularIndustryImportance'):NULL;
		$want_relationship_status_importance  = $this->input->post('wantRelationshipStatusImportance')        ?$this->input->post('wantRelationshipStatusImportance'):NULL;
		$want_child_status_importance         = $this->input->post('wantDateGirlsWithChildrensImportance')    ?$this->input->post('wantDateGirlsWithChildrensImportance'):NULL;
		$want_child_plan_importance           = $this->input->post('wantChildrensPlanImportance')             ?$this->input->post('wantChildrensPlanImportance'):NULL;
		$want_religious_belief_importance     = $this->input->post('wantRegligiousBeliefImportance')          ?$this->input->post('wantRegligiousBeliefImportance'):NULL;
		$want_smoking_status_importance       = $this->input->post('wantSmokingImportance')                   ?$this->input->post('wantSmokingImportance'):NULL;
		$want_drinking_status_importance      = $this->input->post('wantDrinkingImportance')                  ?$this->input->post('wantDrinkingImportance'):NULL;
		$want_exercise_frequency_importance   = $this->input->post('wantExcersiseImportance')                 ?$this->input->post('wantExcersiseImportance'):NULL;
		$want_residence_type_importance       = $this->input->post('wantLivingPlaceImportance')               ?$this->input->post('wantLivingPlaceImportance'):NULL;
		$not_want_to_date                     = $this->input->post('not_want_to_date')                        ?$this->input->post('not_want_to_date'):NULL;
		$ideal_date                           = $this->input->post('ideal_date')                              ?$this->input->post('ideal_date'):NULL;
		$looking_for_importance               = $this->input->post('wantRelationshipGoalImportance')          ?$this->input->post('wantRelationshipGoalImportance') : NULL;
		$want_common_interest_importance               = $this->input->post('want_common_interest_importance')          ?$this->input->post('want_common_interest_importance') : NULL;

		$wantSchoolImportance				  = $this->input->post('wantSchoolImportance');
		$wantCompanyImportance				  = $this->input->post('wantCompanyImportance');

		$wantIncomeCurrency = $this->input->post('incomePrefrence');
		$wantIncomeAmount   = $this->input->post('incomeAmount');
		$ageRangeLowerLimit = $this->input->post('ageRangeLowerLimit');
		$ageRangeUpperLimit = $this->input->post('ageRangeUpperLimit');

		$dataToUpdate   = array('want_age_range_lower'           =>!empty($ageRangeLowerLimit) ? $ageRangeLowerLimit : NULL,
        							'want_school_importance'	=> !empty($wantSchoolImportance) ? $wantSchoolImportance : NULL,
									'want_company_importance'	=> !empty($wantCompanyImportance) ? $wantCompanyImportance : NULL,
									'want_age_range_upper'               =>!empty($ageRangeUpperLimit) ? $ageRangeUpperLimit : NULL,
                                    'want_age_range_importance'          =>!empty($want_age_range_importance) ? $want_age_range_importance : NULL,
                                    'want_height_range_lower'            =>!empty($height_lower) ? $height_lower : NULL,
                                    'want_height_range_upper'            =>!empty($height_higher) ? $height_higher : NULL,
                                    'want_height_range_importance'       =>!empty($want_height_range_importance) ? $want_height_range_importance : NULL,
                                    'want_body_type_importance'          =>!empty($want_body_type_importance) ? $want_body_type_importance : NULL,
                                    'want_looks_importance'              =>!empty($want_looks_importance) ? $want_looks_importance : NULL,
                                    'want_looks_range_higher_id'         =>!empty($want_looks_range_higher_id) ? $want_looks_range_higher_id : NULL,
                                    'want_looks_range_lower_id'          =>!empty($want_looks_range_lower_id) ? $want_looks_range_lower_id : NULL,
                                    'want_ethnicity_importance'          =>!empty($want_ethnicity_importance) ? $want_ethnicity_importance : NULL,
                                    'want_personality_importance'        =>!empty($want_personality_importance) ? $want_personality_importance : NULL,
                                    'want_education_level_importance'    =>!empty($want_education_level_importance) ? $want_education_level_importance : NULL,
                                    'want_school_subject_importance'     =>!empty($want_school_subject_importance) ? $want_school_subject_importance : NULL,
                                    'want_career_stage_importance'       =>!empty($want_career_stage_importance) ? $want_career_stage_importance : NULL,      
                                    'want_annual_income_importance'      =>!empty($want_annual_income_importance) ? $want_annual_income_importance : NULL,
                                    'want_annual_income_currency_id'     =>!empty($wantIncomeCurrency) ? $wantIncomeCurrency : NULL,
                                    'want_annual_income'                 =>!empty($wantIncomeAmount) ? $wantIncomeAmount : NULL,
                                    'want_job_function_importance'       =>!empty($want_job_function_importance) ? $want_job_function_importance : NULL,
                                    'want_industry_importance'           =>!empty($want_industry_importance) ? $want_industry_importance : NULL,
                                    'want_relationship_status_importance'=>!empty($want_relationship_status_importance) ? $want_relationship_status_importance : NULL,
                                    'want_child_status_importance'       =>!empty($want_child_status_importance) ? $want_child_status_importance : NULL,
                                    'want_child_plan_importance'         =>!empty($want_child_plan_importance) ? $want_child_plan_importance : NULL,
                                    'want_religious_belief_importance'   =>!empty($want_religious_belief_importance) ? $want_religious_belief_importance : NULL,
                                    'want_smoking_status_importance'     =>!empty($want_smoking_status_importance) ? $want_smoking_status_importance : NULL,
                                    'want_drinking_status_importance'    =>!empty($want_drinking_status_importance) ? $want_drinking_status_importance : NULL,
                                    'want_exercise_frequency_importance' =>!empty($want_exercise_frequency_importance) ? $want_exercise_frequency_importance : NULL,
                                    'want_residence_type_importance'     =>!empty($want_residence_type_importance) ? $want_residence_type_importance : NULL,
                                    'ideal_date'                         =>!empty($ideal_date) ? $ideal_date : NULL,
                                    'not_want_to_date'                   =>!empty($not_want_to_date) ? $not_want_to_date : NULL,
                                    'want_looking_for_importance'        =>!empty($looking_for_importance) ? $looking_for_importance : NULL,
									'want_common_interest_importance'	=>!empty($want_common_interest_importance) ? $want_common_interest_importance : NULL
		);

		//update 'user' table with the new 'importance' values
		$this->db->where('user_id',$user_id);
		$res = $this->db->update('user',$dataToUpdate);

		$this->genericInsert($user_id,'body_type',           $this->input->post('bodyTypePreference'));
		$this->genericInsert($user_id,'ethnicity',           $this->input->post('ethnicityPreference'));
		$this->genericInsert($user_id,'education_level',     $this->input->post('educationPreference'));
		$this->genericInsert($user_id,'career_stage',        $this->input->post('openForDatingPreference'));
		$this->genericInsert($user_id,'school_subject',      $this->input->post('subjectPreference'));
		$this->genericInsert($user_id,'job_function',        $this->input->post('jobPreference'));
		$this->genericInsert($user_id,'industry',    	     $this->input->post('industryPreference'));
		$this->genericInsert($user_id,'relationship_status', $this->input->post('replationshipStatusPreference'));
		$this->genericInsert($user_id,'child_plan',          $this->input->post('childrensPlanPreference'));
		$this->genericInsert($user_id,'religious_belief',    $this->input->post('religiousBeliefPreference'));
		$this->genericInsert($user_id,'smoking_status',      $this->input->post('smokingPreference'));
		$this->genericInsert($user_id,'drinking_status',     $this->input->post('drinkingPreference'));
		$this->genericInsert($user_id,'exercise_frequency',  $this->input->post('excersisePreference'));
		$this->genericInsert($user_id,'residence_type',      $this->input->post('livingPlacePreference'));
		$this->genericInsert($user_id,'descriptive_word',    $this->input->post('personalityPreference'));
			
		$this->genericInsert($user_id,'child_status',	     $this->input->post('dateGirlsWithChildrensPreference'));
		$this->genericInsert($user_id,'school',	     $this->input->post('user_want_school_ids'));
		$this->genericInsert_company($user_id,$this->input->post('user_want_company_ids' ));

			return;
	}
	
	public function get_match_filters($language_id){
		
		$filter = array();
		$this->db->select('filter_id,description');
		$this->db->where('language_id',$language_id);
		$this->db->order_by('view_order','ASC');
		$result = $this->db->get('filter');

		if($result->num_rows()>0){
			foreach($result->result_array() as $row){
				$filter[$row['filter_id']]  = ucfirst($row['description']);
			}
		}
		return $filter;
	}
	
	public function insert_ideal_match_filters($user_id,$ideal_match_filter){
			
		$this->db->where('user_id',$user_id);
		$this->db->delete('user_ideal_match_filter');
		
		if(!empty($ideal_match_filter)){
				
			$idealMatchValues = explode(',',$ideal_match_filter);
			foreach ($idealMatchValues as $key => $value){
				$insert_array[] = array('user_id'   => $user_id,'filter_id' => $value);
			}
			$this->db->insert_batch('user_ideal_match_filter',$insert_array);
		}
		return;
	}
	
	//Author : Hannan Munshi
	//generic insert function for inserting WANT prefrences.
	public function genericInsert($userId,$tableName,$valuesToInsert)
	{
		$fullTableName = 'user_want_'.$tableName;
		$fieldName     = $tableName.'_id';

		/**first delete the old values if any.
		/**(Deletion was done only if $valuesToInsert was !empty but now that check has been removed as per clients req.)**/
		$this->deleteDataFromTable($fullTableName, $userId);

		if(!empty($valuesToInsert))
		{
			$valuesArray = explode(',',$valuesToInsert);

			//insert the new values
			foreach($valuesArray as $key => $value)
			{
				$insertArray[] = array('user_id'   => $userId,
				$fieldName => $value);
			}
			$this->db->insert_batch($fullTableName,$insertArray);
		}
	}

	//Author : Hannan Munshi
	//generic insert function for inserting WANT prefrences.
	public function genericInsert_company($userId,$valuesToInsert)
	{
		$fullTableName = 'user_want_company';
		$fieldName     = 'company_id';
		$this->deleteDataFromTable($fullTableName, $userId);
		if(!empty($valuesToInsert))
		{
			$valuesArray = explode(',',$valuesToInsert);
			//insert the new values
			foreach($valuesArray as $key => $value)
			{
				preg_match('/_(.*?)_/',$value, $display);
				if ($display)
				{
					$insertArray[] = array('user_id'   => $userId,'company_name' => $display['1'],$fieldName=>NULL);
					unset($display);
				} else {
					$insertArray[] = array('user_id'   => $userId,$fieldName => $value,'company_name'=>NULL);
				}
			}
			$this->db->insert_batch($fullTableName,$insertArray);
		}
	}


	public function genericUserInsert($tableName,$user_id,$data,$want=FALSE)
	{

		//compute the table name.either 'user' or 'want'
		if($want === TRUE)
		$fullTableName = 'user_want_'.$tableName;
		else
		$fullTableName = 'user_'.$tableName;

		//TABLE RENAME
		if($tableName == 'nationality')
		{
			$fieldName = 'country_id';
		}
		else
		$fieldName = $tableName.'_id';
		$this->deleteDataFromTable($fullTableName, $user_id);

		//check if data supplied is not empty
		if(!empty($data))
		{
			//if data is already an array then use this old method for inserting
			if(is_array($data))
			{
				//$this->deleteDataFromTable($fullTableName, $user_id);
				$dataCount = count($data);
				for($i=0;$i<$dataCount;$i++)
				{
					//make sure that this index is not empty else 0 will be inserted in database
					if(!empty($data[$i]))
					{
						$insert_array   = array(
                                                                        'user_id'  => $user_id,
						$fieldName => $data[$i]);
						//finally insert into database
						$this->db->insert($fullTableName,$insert_array);
					}

				}
			}
			else
			{
				$this->deleteDataFromTable($fullTableName, $user_id);
				//if data is according to new structure i.e. CSV then use this method for inserting.i.e exdplode and insert on by one.
				$explodedValues = explode(',',$data);
				foreach ($explodedValues as $key => $value)
				{
					$insert_array   = array(
                                        'user_id'  => $user_id,
					$fieldName => $value);
					$this->db->insert($fullTableName,$insert_array);
				}
			}
		}
	}

	public function  deleteDataFromTable($tableName,$user_id)
	{
		$this->db->where('user_id',$user_id);
		$this->db->delete($tableName);
	}
	public function insertUserEveyWear($user_id,$valuesToInsert)
	{
		if(!empty($valuesToInsert))
		{
			$this->db->where('user_id',$user_id);
			$this->db->delete('user_eyewear');

			$this->db->insert('user_eyewear',array('user_id'=>$user_id,'eyewear_id'=>$valuesToInsert));
		}
	}


	public function insert_user_step1($user_id,$language_id){

		$birth_date     = $this->input->post('yearId').'-'.$this->input->post('monthId').'-'.$this->input->post('dateId');
		$district_id    = $this->input->post('district')?$this->input->post('district'):NULL;
		$child_status   = $this->input->post('child_status')?$this->input->post('child_status'):NULL;
		$child_plan     = $this->input->post('child_plans')?$this->input->post('child_plans'):NULL;
		$smoking_status = ($this->input->post('smoking_status')=="0")?NULL:$this->input->post('smoking_status');
		$drinking_status = ($this->input->post('drinking_status')=="0")?NULL:$this->input->post('drinking_status');
		$exercise_frequency = ($this->input->post('exercise_frequency')=="0")?NULL:$this->input->post('exercise_frequency');

		$birth_city_id  = $this->get_city_id($this->input->post('city_born'), $language_id);
		$educationLevel = $this->input->post('education_level') ? $this->input->post('education_level') : '';
		$computedHeight = '';

		$city_id                = $this->session->userdata('sess_city_id');
		$current_country        = $this->model_user->getCountryByCity($city_id);
		$city                   = $this->model_user->get_city_by_id($city_id);
		$looking_for = $this->input->post('looking_for');
		$critearea_for_skipp_step2 = array('1','2','3');
		
		$step = '1';
		if(!array_intersect($looking_for,$critearea_for_skipp_step2))
		{
			$step = '2';
		}
		$useMeters = $current_country->use_meters;

		if($useMeters == 1)
		{
			$computedHeight = $this->input->post('height');
		}
		else
		{
			$feet   = $this->input->post('feet');
			$inches = $this->input->post('inches');

			$totalInches = ($feet.'.'.$inches)*12;
			$computedHeight = round(($totalInches * 2.54),2);
		}
		$insert_array   = array(
							'gender_id'=>$this->input->post('gender'),
							'ethnicity_id'=>$this->input->post('ethnicity'),
							'career_stage_id'=>$this->input->post('career_stage_id'),
							'current_city_id'=>$city_id,
							'current_postal_code'=>$this->input->post('postal_code'),
							'current_district_id'=>$district_id,
							'birth_date'=>$birth_date,
							'height'=>$computedHeight,
							'body_type_id'=>$this->input->post('bodyTypeId'),
							'looks_id'=>$this->input->post('lookId'),
							'relationship_status_id'=>$this->input->post('relationship_status')?$this->input->post('relationship_status'):NULL,
							'religious_belief_id'=>$this->input->post('religiousBeliefId'),
							'self_summary'=>$this->input->post('self_summary')?$this->input->post('self_summary'):NULL,
							'annual_income_range_id'=>$this->input->post('annual_income_range_id'),
							'completed_application_step'=>$step);
		//echo "<pre>";print_r($insert_array);exit;
		if($birth_city_id!=""){
			$new_array      = array("birth_city_id"=>$birth_city_id);
			$insert_array   = array_merge($insert_array, $new_array);
		}else{
			$new_array      = array("birth_city_name"=>$this->input->post('city_born'));
			if($this->input->post('city_born') == '')
			{
				$new_array      = array("birth_city_name"=>null);
			}
			$insert_array   = array_merge($insert_array, $new_array);
		}
		//echo "<pre>";print_r($insert_array);exit;
		$this->db->where('user_id',$user_id);
		$this->db->update('user',$insert_array);


		$this->clear_data($user_id,'user_want_gender');
		$this->insert_user_want_gender($user_id,$this->input->post('want_to_date'));

		$this->clear_data($user_id,'user_want_relationship_type');
		$this->insert_user_want_relationship_type($user_id,$looking_for);

		$this->genericUserInsert('education_level', $user_id, $educationLevel);
		//$this->insert_user_eyewear($user_id,$this->input->post('usually_wear'));
		//$this->insert_user_spoken_language($user_id,$this->input->post('spoken_language_id'),$this->input->post('spoken_language_level_id'));
		//$this->insert_user_living_city($user_id,$this->input->post('lived_city_id'),$language_id);
		//$this->insert_user_nationality($user_id,$this->input->post('nationality_id'));
		$this->insert_user_want_descriptive_word($user_id,$this->input->post('descriptive_word_id'),'user_descriptive_word');
		//$this->insert_user_want_education_level($user_id,$this->input->post('education_level'),'user_education_level');
		$this->insert_user_interest($user_id,$this->input->post('interests'),$language_id);
	}

	public function updateUserProfile($user_id,$language_id,$useMeters='')
	{

		$firstName      = $this->input->post('first_name')   ? $this->input->post('first_name') : null;
		$lastName       = $this->input->post('last_name')    ? $this->input->post('last_name') : null;
		$gender         = $this->input->post('gender')       ? $this->input->post('gender') : null;
		$ethnicity      = $this->input->post('ethnicity')    ? $this->input->post('ethnicity') : null;

		//format the values .this values goes into want tables
		$wantToDate     = $this->input->post('want_to_date') ? $this->input->post('want_to_date') : null;

		//format the values.this values goes into want tables
		$lookingFor      = $this->input->post('looking_for') ? $this->input->post('looking_for') : null;

		$postCode        = $this->input->post('postal_code') ? $this->input->post('postal_code') : null;

		$birth_date      = $this->input->post('yearId').'-'.$this->input->post('monthId').'-'.$this->input->post('dateId');
		//$city_id        = $this->get_city_id($this->input->post('current_location'), $language_id);
		$district_id    = $this->input->post('district')?$this->input->post('district'):NULL;
		
		if($useMeters == 1)
		{
			//$computedHeight = $this->input->post('height');
			$height = $this->input->post('height') ? $this->input->post('height') : '';
		}
		else
		{
			$feet   = $this->input->post('feet_id');
			$inches = $this->input->post('inches_id');

			$totalInches = ($feet.'.'.$inches)*12;
			$height = round(($totalInches * 2.54),2);
		}
		//$height             = $this->input->post('height') ? $this->input->post('height') : '';
		$bodyType           = $this->input->post('bodyTypeId') ? $this->input->post('bodyTypeId') : null;
		$looks              = $this->input->post('lookId') ? $this->input->post('lookId') : null;
		$relationshipStatus = $this->input->post('relationship_status') ? $this->input->post('relationship_status') : null;
		$religiousBelief    = $this->input->post('religiousBeliefId') ? $this->input->post('religiousBeliefId') : null;
		$selfSummary        = $this->input->post('self_summary') ? $this->input->post('self_summary') :null;

		$educationLevel     = $this->input->post('user_education_level') ? $this->input->post('user_education_level'):null;
		$careerStage        = $this->input->post('career_stage_id') ? $this->input->post('career_stage_id'):null;
		$annualIncome       = $this->input->post('annual_income_range_id') ? $this->input->post('annual_income_range_id'):null;

		//insert in user_education_level
		$descriptiveWordId  = $this->input->post('descriptive_word_id') ? $this->input->post('descriptive_word_id'):null;


		$smoking_status = ($this->input->post('smoking_status')=="0")?NULL:$this->input->post('smoking_status');
		$drinking_status = ($this->input->post('drinking_status')=="0")?NULL:$this->input->post('drinking_status');
		$exercise_frequency = ($this->input->post('exercise_frequency')=="0")?NULL:$this->input->post('exercise_frequency');
		$residenceType       = $this->input->post('residence_type') ? $this->input->post('residence_type'):null;

		$child_status   = $this->input->post('child_status')?$this->input->post('child_status'):NULL;
		$child_plan     = $this->input->post('child_plan')?$this->input->post('child_plan'):NULL;

		$eyeColor         = $this->input->post('eye_color')?$this->input->post('eye_color'):NULL;
		$hairColor        = $this->input->post('hair_color')?$this->input->post('hair_color'):NULL;
		$hairLength       = $this->input->post('hair_length')?$this->input->post('hair_length'):NULL;
		$skinTone         = $this->input->post('skin_tone')?$this->input->post('skin_tone'):NULL;

		//insert in user_eyewear
		$usuallyWear      = $this->input->post('usually_wear')?$this->input->post('usually_wear'):NULL;
		if($usuallyWear)
		{
			$usuallyWear = explode(',', $usuallyWear);
		}

		//insert in user_spoken_langfuage
		$languageId          = $this->input->post('spoken_language_id')?$this->input->post('spoken_language_id'):NULL;
		$languageFluency     = $this->input->post('spoken_language_level_id')?$this->input->post('spoken_language_level_id'):NULL;

		//insert in user_nationality
		$nationality         = $this->input->post('nationality_id')?$this->input->post('nationality_id'):NULL;

		//insert in user_lived_in_city
		$livedInCityId       = $this->input->post('lived_city_id')?$this->input->post('lived_city_id'):NULL;

		//not availabel in database
		$livedInCountryId    = $this->input->post('lived_country_id')?$this->input->post('lived_country_id'):NULL;

		$user_birth_city = $this->get_city_id($this->input->post('born_in_city'), $language_id);
		$birth_city_id  = $user_birth_city ? $user_birth_city : NULL ;
		$birth_city_name = $this->input->post('city_born') ? $this->input->post('city_born') : NULL;
		$birth_country_id = $this->input->post('country_born') ? $this->input->post('country_born') : NULL;
		$current_city_id = $this->input->post('current_city_id') ? $this->input->post('current_city_id') : NULL;
		
		
		if($district_id)
		{
			$district_city_id = $this->get_city_by_district_id($language_id,$district_id);	
			if($district_city_id != "" && $district_city_id !=$current_city_id)
			{
				$district_id = NULL;
			}
		}
		
		$insert_array   = array('gender_id'             =>$gender,
									'current_city_id' =>$current_city_id,
                                    'ethnicity_id'              =>$ethnicity,
                                    'career_stage_id'           =>$careerStage,
                                    'first_name'                =>$firstName,
                                    'last_name'                 =>$lastName,
									'current_district_id'=>$district_id,
                                    'current_postal_code'       =>$postCode,
                                    'residence_type'            =>$residenceType,
                                    'birth_date'                =>$birth_date,
                                    'birth_country_id'          =>$birth_country_id,
                                    'birth_city_id'             =>$birth_city_id,
                                    'birth_city_name'           =>$birth_city_name,
                                    'height'                    =>$height,
                                    'body_type_id'              =>$bodyType,
                                    'looks_id'                  =>$looks,
                                    'eye_color_id'              =>$eyeColor,
                                    'hair_color_id'             =>$hairColor,
                                    'hair_length_id'            =>$hairLength,
                                    'skin_tone_id'              =>$skinTone,
                                    'relationship_status_id'    =>$relationshipStatus,
                                    'religious_belief_id'       =>$religiousBelief,
                                    'child_status_id'           =>$child_status,
                                    'child_plan_id'             =>$child_plan,
                                    'smoking_status_id'         =>$smoking_status,
                                    'drinking_status_id'        =>$drinking_status,
                                    'exercise_frequency_id'     =>$exercise_frequency,
                                    'self_summary'              =>$selfSummary,
                                    'annual_income_range_id'    =>$annualIncome,
		);

		/*if($birth_city_id!=""){
		 $new_array      = array("birth_city_id"=>$birth_city_id);
		 $insert_array   = array_merge($insert_array, $new_array);
		 }else{
		 $new_array      = array("birth_city_name"=>$this->input->post('city_born'));
		 $insert_array   = array_merge($insert_array, $new_array);
		 }*/
		$this->db->where('user_id',$user_id);
		$this->db->update('user',$insert_array);


		$this->genericUserInsert('gender', $user_id,$wantToDate,TRUE);
		$this->genericUserInsert('education_level', $user_id, $educationLevel);
		$this->genericUserInsert('descriptive_word', $user_id,$descriptiveWordId);
		$this->genericUserInsert('eyewear', $user_id,$usuallyWear);
		//echo $nationality;die();
		$this->genericUserInsert('nationality', $user_id,$nationality);
		$this->genericUserInsert('relationship_type', $user_id,$this->input->post('looking_for'), TRUE);
		$this->insert_user_spoken_language($user_id, $languageId, $languageFluency);
		//$this->insert_user_living_city($user_id,$livedInCityId,$language_id);
		$this->insert_user_lived_in_city($user_id,$livedInCountryId,$livedInCityId,$language_id);
		$this->genericUserInsert('interest', $user_id,$this->input->post('interests'));
		return;
	}

	public function get_user_email_by_id($user_email_id){
		$this->db->where('user_email_id',$user_email_id);
		$result        = $this->db->get('user_email');
		$user_email[0] = array();
		if($result->num_rows()>0){
			$user_email = $result->result_array();
		}
		return $user_email[0];

	}
	public function get_user_email_data($user_id){
		$this->db->where('user_id',$user_id);
		$result        = $this->db->get('user_email');
		$user_email[0] = array();
		if($result->num_rows()>0){
			$user_email = $result->result_array();
		}
		return $user_email[0];

	}
	public function get_user_email($user_id){
		$this->db->where('user_id',$user_id);
		$this->db->where('is_contact','1');
		$result        = $this->db->get('user_email');
		$user_email[0] = array();
		if($result->num_rows()>0){
			$user_email = $result->result_array();
		}
		return $user_email[0];

	}
	public function insert_user_want_gender($user_id,$want_to_date){
		if(!empty($want_to_date)){
			for($i=0;$i<count($want_to_date);$i++){
				if($want_to_date[$i])
				{
					$insert_array   = array('user_id'=>$user_id,'gender_id'=>$want_to_date[$i]);
					$this->db->insert('user_want_gender',$insert_array);
				}

			}
		}
	}
	public function insert_user_nationality($user_id,$nationality){
		if($nationality!=""){
			$nationality_array         = explode(",",$nationality);
			for($i=0;$i<count($nationality_array);$i++){
				$insert_array   = array(
                                        'user_id'=>$user_id,
                                        'country_id'=>$nationality_array[$i]);
				$this->db->insert('user_nationality',$insert_array);
			}
		}
	}


	public function insert_user_want_relationship_type($user_id,$looking_for){
		$looking_for = array_diff( $looking_for, array( '' ) );
		if(!empty($looking_for)){
			foreach ($looking_for as $val){
				if($val)
				{
					$insert_array   = array('user_id'=>$user_id,'relationship_type_id'=>$val);
					$this->db->insert('user_want_relationship_type',$insert_array);
				}
			}
		}
	}
	public function insert_user_eyewear($user_id,$eyewear){
		$insert_array   = array(
                                'user_id'=>$user_id,
                                'eyewear_id'=>$eyewear);
		$this->db->insert('user_eyewear',$insert_array);
	}
	public function insert_user_spoken_language($user_id,$spoken_language_id,$language_level_id){
		/*if($spoken_language_id!=""){
			$this->db->where('user_id',$user_id);
			$this->db->delete('user_spoken_language');
			$language_array         = explode(",",$spoken_language_id);
			$language_level_array   = explode(",",$language_level_id);
			for($i=0;$i<count($language_array);$i++){
			$this->insert_spoken_language($user_id,$language_array[$i],$language_level_array[$i]);
			}
			}*/

		if(!empty($user_id))
		{
			$this->db->where('user_id',$user_id);
			$this->db->delete('user_spoken_language');
		}
		if($spoken_language_id != "" && $language_level_id != ""){
			$language_array         = explode(",",$spoken_language_id);
			$language_level_array   = explode(",",$language_level_id);
			for($i=0;$i<count($language_array);$i++)
			{
				$this->insert_spoken_language($user_id,$language_array[$i],$language_level_array[$i]);
			}
		}
	}
	public function insert_user_living_city($user_id,$city,$language_id){
		if($city!=""){
			/*---------change by HANNAN----------------*/
			$this->deleteDataFromTable('user_city_lived_in',$user_id);
			/*---------change by HANNAN----------------*/
			$city_array         = explode(",",$city);
			for($i=0;$i<count($city_array);$i++){
				$this->insert_living_city($user_id,$city_array[$i],$language_id);
			}
		}
	}
	public function insert_user_interest($user_id,$interest,$language_id){
		if($interest!=""){
			$interest_array         = explode(",",$interest);
			for($i=0;$i<count($interest_array);$i++){

				//no need to check
				//$interest_id        = $this->get_interest_by_name($interest_array[$i],$language_id);

				$interest_id = $interest_array[$i];
				if($interest_id)
				{
					$count   = $this->check_interest_exist($user_id,$interest_id);
					if($count==0)
					$this->insert_interest($user_id,$interest_id);
				}
				else
				$this->db->insert('user_interest', array('user_id'=>$user_id,'interest_other'=>$interest_array[$i]));
			}
		}
	}
	public function insert_interest($user_id,$interest){
		$insert_array   = array('user_id'=>$user_id,
                                 'interest_id'=>$interest);
		$this->db->insert('user_interest',$insert_array);
	}
	public function insert_user_want_body_type($user_id,$body_type){
		if(!empty($body_type)){
			for($i=0;$i<count($body_type);$i++){
				$insert_array   = array(
                                        'user_id'=>$user_id,
                                        'body_type_id'=>$body_type[$i]);
				$this->db->insert('user_want_body_type',$insert_array);
			}
		}
	}
	public function check_interest_exist($user_id,$interest_id){
		$this->db->where('user_id',$user_id);
		$this->db->where('interest_id',$interest_id);
		$result = $this->db->count_all_results('user_interest');
		return $result;
	}
	public function get_interest_by_name($interest,$language_id){
		$this->db->select('interest_id');
		$this->db->where('description',trim($interest));
		$this->db->where('display_language_id',$language_id);
		$r       = "";
		$result  = $this->db->get('interest');
		if($result->num_rows()>0){
			$row     = $result->result_array();
			$r       = $row[0]['interest_id'];
		}
		return $r;
	}
	public function insert_user_want_ethnicity($user_id,$ethnicity){
		if(!empty($ethnicity)){
			for($i=0;$i<count($ethnicity);$i++){
				$insert_array   = array(
                                        'user_id'=>$user_id,
                                        'ethnicity_id'=>$ethnicity[$i]);
				$this->db->insert('user_want_ethnicity',$insert_array);
			}
		}
	}
	public function insert_user_want_nationality($user_id,$nationality){
		if($nationality!=""){
			$nationality_array   = explode(",",$nationality);
			for($i=0;$i<count($nationality_array);$i++){
				$insert_array    = array(
                                        'user_id'=>$user_id,
                                        'nationality_id'=>$nationality_array[$i]);
				$this->db->insert('user_want_natonality',$insert_array);
			}
		}
	}
	public function insert_user_want_eyecolor($user_id,$eyecolor){
		if(!empty($eyecolor)){
			for($i=0;$i<count($eyecolor);$i++){
				$insert_array   = array(
                                        'user_id'=>$user_id,
                                        'eye_color_id'=>$eyecolor[$i]);
				$this->db->insert('user_want_eye_color',$insert_array);
			}
		}
	}
	public function insert_user_want_haircolor($user_id,$haircolor){
		if(!empty($haircolor)){
			for($i=0;$i<count($haircolor);$i++){
				$insert_array   = array(
                                        'user_id'=>$user_id,
                                        'hair_color_id'=>$haircolor[$i]);
				$this->db->insert('user_want_hair_color',$insert_array);
			}
		}
	}
	public function insert_user_want_hair_length($user_id,$hair_length){
		if(!empty($hair_length)){
			for($i=0;$i<count($hair_length);$i++){
				$insert_array   = array(
                                        'user_id'=>$user_id,
                                        'hair_length_id'=>$hair_length[$i]);
				$this->db->insert('user_want_hair_length',$insert_array);
			}
		}
	}
	public function insert_user_want_skin_tone($user_id,$skin_tone){
		if(!empty($skin_tone)){
			for($i=0;$i<count($skin_tone);$i++){
				$insert_array   = array(
                                        'user_id'=>$user_id,
                                        'skin_tone_id'=>$skin_tone[$i]);
				$this->db->insert('user_want_skin_tone',$insert_array);
			}
		}
	}
	public function insert_user_want_eyewear($user_id,$eyewear){
		if(!empty($eyewear)){
			for($i=0;$i<count($eyewear);$i++){
				$insert_array   = array(
                                        'user_id'=>$user_id,
                                        'eyewear_id'=>$eyewear[$i]);
				$this->db->insert('user_want_eyewear',$insert_array);
			}
		}
	}
	public function insert_user_want_education_level($user_id,$education_level,$table){
		if(!empty($education_level)){
			for($i=0;$i<count($education_level);$i++){
				$insert_array   = array(
                                        'user_id'=>$user_id,
                                        'education_level_id'=>$education_level[$i]);
				$this->db->insert($table,$insert_array);
			}
		}
	}
	public function insert_user_want_school_subject($user_id,$school_subject){
		if(!empty($school_subject)){
			for($i=0;$i<count($school_subject);$i++){
				$insert_array   = array(
                                        'user_id'=>$user_id,
                                        'school_subject_id'=>$school_subject[$i]);
				$this->db->insert('user_want_school_subject',$insert_array);
			}
		}
	}
	public function insert_user_want_carrier_stage($user_id,$career_stage){
		if(!empty($career_stage)){
			for($i=0;$i<count($career_stage);$i++){
				$insert_array   = array(
                                        'user_id'=>$user_id,
                                        'career_stage_id'=>$career_stage[$i]);
				$this->db->insert('user_want_career_stage',$insert_array);
			}
		}
	}
	public function insert_user_want_job_functions($user_id,$job_functions){
		if(!empty($job_functions)){
			for($i=0;$i<count($job_functions);$i++){
				$insert_array   = array(
                                        'user_id'=>$user_id,
                                        'job_function_id'=>$job_functions[$i]);
				$this->db->insert('user_want_job_function',$insert_array);
			}
		}
	}
	public function insert_user_want_company_industry($user_id,$company_industry){
		if(!empty($company_industry)){
			for($i=0;$i<count($company_industry);$i++){
				$insert_array   = array(
                                        'user_id'=>$user_id,
                                        'industry_id'=>$company_industry[$i]);
				$this->db->insert('user_want_industry',$insert_array);
			}
		}
	}
	public function insert_user_want_relationship_status($user_id,$relationship_status){
		if(!empty($relationship_status)){
			for($i=0;$i<count($relationship_status);$i++){
				$insert_array   = array(
                                        'user_id'=>$user_id,
                                        'relationship_status_id'=>$relationship_status[$i]);
				$this->db->insert('user_want_relationship_status',$insert_array);
			}
		}
	}
	public function insert_user_want_child_status($user_id,$child_status){
		if(!empty($child_status)){
			for($i=0;$i<count($child_status);$i++){
				$insert_array   = array(
                                        'user_id'=>$user_id,
                                        'child_status_id'=>$child_status[$i]);
				$this->db->insert('user_want_child_status',$insert_array);
			}
		}
	}
	public function insert_user_want_child_plan($user_id,$child_plan){
		if(!empty($child_plan)){
			for($i=0;$i<count($child_plan);$i++){
				$insert_array   = array(
                                        'user_id'=>$user_id,
                                        'child_plan_id'=>$child_plan[$i]);
				$this->db->insert('user_want_child_plan',$insert_array);
			}
		}
	}
	public function insert_user_want_religious_belief($user_id,$religious_belief){
		if(!empty($religious_belief)){
			for($i=0;$i<count($religious_belief);$i++){
				$insert_array   = array(
                                        'user_id'=>$user_id,
                                        'religious_belief_id'=>$religious_belief[$i]);
				$this->db->insert('user_want_religious_belief',$insert_array);
			}
		}
	}
	public function insert_user_want_smoking_status($user_id,$smoking_status){
		if(!empty($smoking_status)){
			for($i=0;$i<count($smoking_status);$i++){
				$insert_array   = array(
                                        'user_id'=>$user_id,
                                        'smoking_status_id'=>$smoking_status[$i]);
				$this->db->insert('user_want_smoking_status',$insert_array);
			}
		}
	}
	public function insert_user_want_drinking_status($user_id,$drinking_status){
		if(!empty($drinking_status)){
			for($i=0;$i<count($drinking_status);$i++){
				$insert_array   = array(
                                        'user_id'=>$user_id,
                                        'drinking_status_id'=>$drinking_status[$i]);
				$this->db->insert('user_want_drinking_status',$insert_array);
			}
		}
	}
	public function insert_user_want_exercise_frequency($user_id,$exercise_frequency){
		if(!empty($exercise_frequency)){
			for($i=0;$i<count($exercise_frequency);$i++){
				$insert_array   = array(
                                        'user_id'=>$user_id,
                                        'exercise_frequency_id'=>$exercise_frequency[$i]);
				$this->db->insert('user_want_exercise_frequency',$insert_array);
			}
		}
	}
	public function insert_user_want_descriptive_word($user_id,$descriptive_word,$table){
		if($descriptive_word!=""){
			$descriptive_word_array  = explode(',',$descriptive_word);
			for($i=0;$i<count($descriptive_word_array);$i++){
				$insert_array   = array(
                                        'user_id'=>$user_id,
                                        'descriptive_word_id'=>$descriptive_word_array[$i]);
				$this->db->insert($table,$insert_array);
			}
		}
	}
	public function insert_user_want_residence_type($user_id,$residence_type){
		if(!empty($residence_type)){
			for($i=0;$i<count($residence_type);$i++){
				$insert_array   = array(
                                        'user_id'=>$user_id,
                                        'residence_type_id'=>$residence_type[$i]);
				$this->db->insert('user_want_residence_type',$insert_array);
			}
		}
	}

	/**
	 * Clear Values from table
	 * @access public
	 * @param  User_id, Table Name
	 * @return null
	 * @author Rajnish Savaliya
	 */
	public function clear_data($user_id,$table)
	{
		//first delete the data.
		$this->db->where('user_id',$user_id);
		$this->db->delete($table);
	}

	/*----Change by Hannan Munshi----*/
	public function insert_user_want_personality($user_id,$personality,$table=''){

		$table = 'user_want_personality';
		if(!empty($personality))
		{
			//first delete the old personality prefrences then insert the new ones.
			$this->db->where('user_id',$user_id);
			$this->db->delete('user_want_personality');

			$personalityArray = explode($personality);

			foreach ($personalityArray as $key => $value)
			{
				$insert_array = array('user_id'=>$user_id,
                                          'personality_id' => $value 
				);
				$this->insert_personality($insert_array,$table) ;
			}
		}

		//		foreach($personality as $row){
		//			$insert_array = array('user_id'=>$user_id,
		//                                  'personality_id'=>$row['personality_id'],
		//                                  'personality_value'=>$this->input->post("personality".$row['personality_id']));
		//
		//			$exist   = $this->check_personality_exist($user_id,$row['personality_id'],$table);
		//			if($exist>0){
		//				$this->update_personality($user_id, $insert_array,$table) ;
		//			}
		//			else{
		//				$this->insert_personality($insert_array,$table) ;
		//			}
		//
		//		}
	}
	public function insert_personality($insert_array,$table){
		$this->db->insert($table,$insert_array);
	}
	public function update_personality($user_id,$insert_array,$table){
		$this->db->where('user_id',$user_id);
		$this->db->update($table,$insert_array);
	}
	public function check_personality_exist($user_id,$personality_id,$table){
		$this->db->where('user_id',$user_id);
		$this->db->where('personality_id',$personality_id);
		$result = $this->db->count_all_results($table);
		return $result;
	}
	public function get_date_type($language_id){
		$this->db->select('date_type_id,description,view_other');
		$this->db->where('display_language_id',$language_id);
                $this->db->where('is_active',1);
		$this->db->order_by('view_order','ASC');
		$result = $this->db->get('date_type');
		$results        = array();
		if($result->num_rows()>0){
			$results    = $result->result_array();
		}
		return $results;
	}
	public function get_contact_method($language_id){
		$this->db->select('contact_method_id,description');
		$this->db->where('display_language_id',$language_id);
		$this->db->order_by('view_order','ASC');
		$result = $this->db->get('contact_method');
		$results        = array();
		if($result->num_rows()>0){
			$results    = $result->result_array();
		}
		return $results;
	}

	/** OLD FUNCTION */
	public function insert_user_step3($user_id){
		$date_days  = $this->input->post('preferred_date_days');
		$preferred_date_days = "";
		for($i=0;$i<count($date_days);$i++){
			$preferred_date_days .= $date_days[$i].',';
		}
		$preferred_date_days = rtrim($preferred_date_days,',');
		$insert_array        = array('user_id'=>$user_id,
                                     'preferred_date_days'=>$preferred_date_days,
                                     'matchmaking_selectivity'=> $this->input->post('matchmaking_selectivity'),
                                     'completed_application_step'=>'3'   
                                     );
                                     $this->db->where('user_id',$user_id);
                                     $this->db->update('user',$insert_array);
                                     // $this->insert_user_preferred_date_num_people($user_id,$this->input->post('date_num_people'));
                                     $this->insert_user_preferred_date_type($user_id,$this->input->post('date_type'),$this->input->post('other_date_type'));
                                     //$this->insert_user_preferred_contact_method($user_id,$this->input->post('contact_method'));

	}

	public function insert_user_preferred_date_type($user_id,$date_type,$date_type_other){
		if(!empty($date_type)){
			$this->deleteDataFromTable('user_preferred_date_type', $user_id);
			$date_type = explode(',',$date_type);
			for($i=0;$i<count($date_type);$i++){
				$other_date_type = ($this->check_other_date_type($date_type[$i])==1)?$date_type_other:"";
					
				$insert_array    = array(
                                        'user_id'=>$user_id,
                                        'date_type_id'=>$date_type[$i],
                                        'date_type_other'=>$other_date_type);
				$this->db->insert('user_preferred_date_type',$insert_array);
			}
		}
	}
	public function check_other_date_type($date_type_id){
		$this->db->select('view_other');
		$this->db->where('date_type_id',$date_type_id);
		$q = $this->db->get('date_type');
		if($q->num_rows() > 0)
		$result =  $q->row() ;
		return $result->view_other;
	}
	public function insert_user_preferred_contact_method($user_id,$contact_method){
		$this->db->where('user_id',$user_id);
		$this->db->delete('user_preferred_contact_method');

		if(!empty($contact_method)){
			for($i=0;$i<count($contact_method);$i++){

				$insert_array   = array('user_id'=>$user_id,'contact_method_id'=>$contact_method[$i]);
				if(!$this->checkDuplicate($insert_array,'user_preferred_contact_method'))
				{
					$this->db->insert('user_preferred_contact_method',$insert_array);
				}
			}
		}
	}

	//    public function insert_user_preferred_date_num_people($user_id,$date_num_people){
	//        if(!empty($date_num_people)){
	//            for($i=0;$i<count($date_num_people);$i++){
	//                $insert_array   = array(
	//                                        'user_id'=>$user_id,
	//                                        'date_num_people_id'=>$date_num_people[$i]);
	//                $this->db->insert('user_preferred_date_num_people',$insert_array);
	//            }
	//        }
	//    }
	public function get_date_num_people($language_id){
		$this->db->select('date_num_people_id,description');
		$this->db->where('display_language_id',$language_id);
		$this->db->order_by('view_order','ASC');
		$result = $this->db->get('date_num_people');
		$results        = array();
		if($result->num_rows()>0){
			$results    = $result->result_array();
		}
		return $results;
	}
	
	
	public function insert_user_step4($user_id){
		$heard_about_us      = $this->input->post('heared_abou_us')?$this->input->post('heared_abou_us'):NULL;
		$insert_array        = array('user_id'=>$user_id,
                                     'mobile_phone_number'=>$this->input->post('mobile_phone_number'),
                                     'facebook_page'=>$this->input->post('facebook_page'),
                                     'linkedin_page'=>$this->input->post('linkedin_page'),
                                     'twitter_username'=>$this->input->post('twitter_username'),
                                     'other_info_for_application'=>$this->input->post('other_info_for_application'),
		//'applied_date' => date('Y-m-d H:i:s'),
                                     'how_you_heard_about_us_id'=>$heard_about_us,
                                     'how_you_heard_about_us_other'=>$this->input->post('heard_about_us_other'),
                                     'completed_application_step'=>'3'
                                     );
                                     $this->db->where('user_id',$user_id);
                                     $this->db->update('user',$insert_array);

	}
	public function get_gender_id($gender){
		$this->db->select('gender_id');
		$this->db->where('description',$gender);
		$result     = $this->db->get('gender');
		$gender_id  = "";
		if($result->num_rows()>0){
			$row        = $result->result_array();
			$gender_id  = $row['0']['gender_id'];
		}
		return $gender_id;
	}
	public function get_relationship_status_id($relationship_status){
		$this->db->select('relationship_status_id');
		$this->db->where('description',$relationship_status);
		$result = $this->db->get('relationship_status');
		$relationship_status_id  = "";
		if($result->num_rows()>0){
			$row        = $result->result_array();
			$relationship_status_id  = $row['0']['relationship_status_id'];
		}
		return $relationship_status_id;
	}
	public function get_country_id($country){
		$this->db->select('country_id');
		$this->db->where('description',$country);
		$this->db->order_by('view_order','ASC');
		$result         = $this->db->get('country');
		$country_id     = "";
		if($result->num_rows()>0){
			$row        = $result->result_array();
			$country_id = $row['0']['country_id'];
		}
		return $country_id;
	}
	public function get_country_by_city($city){
		$this->db->select('country.country_id,country.description');
		$this->db->join('province','city.province_id =province.province_id');
		$this->db->join('country','province.country_id=country.country_id');
		$this->db->where('city.description',$city);
		$result         = $this->db->get('city');
		$country_id     = "";
		if($result->num_rows()>0){
			$row        = $result->result_array();
			$country_id = $row['0']['description'];
		}
		return $country_id;
	}
	public function get_spoken_language_id($language){
		$this->db->select('spoken_language_id');
		$this->db->where('description',$language);
		$result          = $this->db->get('spoken_language');
		$language_id     = "";
		if($result->num_rows()>0){
			$row         = $result->result_array();
			$language_id = $row['0']['spoken_language_id'];
		}
		return $language_id;
	}
	public function get_language_level_id($language_level){
		$this->db->select('spoken_language_level_id');
		$this->db->where('description',$language_level);
		$result          = $this->db->get('spoken_language_level');
		$language_id     = "";
		if($result->num_rows()>0){
			$row         = $result->result_array();
			$language_id = $row['0']['spoken_language_level_id'];
		}
		return $language_id;
	}
	public function get_school_by_facebok($language_id,$user_id,$school_details){
		
		$data       = array();
		$majors     = "";
		foreach($school_details as $key=>$row){
			
			$major_ids = array();	
			$data['user_school_id'] = "";
			$data['school_name']    = $row['school'];
			$data['school_domain']  = "";
			$data['school_email']   = "";
			$data['degree_name']    = $row['degree'];
			$data['education_type']    = $row['education_type'];
			$data['is_degree_completed']="";
			$data['years_attended_start']="";
			$data['years_attended_end']=$row['year'];
			
			if($row['majors']){				
				foreach($row['majors'] as $row){
					$major_ids[] = $this->get_subject_id($row);
				}
			}
			$data['majors'] = implode(',', $major_ids);
			$data['minors'] = "";
			
			$this->insert_school($language_id,$user_id,$data);
		}
		return $data;
	}
	public function get_company_by_facebok($language_id,$user_id,$company_details){
		$data       = array();
		foreach($company_details as $row){

			$city_id = '';
			if(isset($row['job_city_name']))
			{
				if($city_id = $this->find_city($row['job_city_name']))
				{

				}
				else
				{
					$city_name = explode(', ', $row['job_city_name']);
					foreach ($city_name as $value) {
							
						if($city_id = $this->find_city($value))
						{
							break;
						}
					}
				}

				if($city_id == '')
				{
					$city_id = $row['job_city_name'];
				}
			}
			else
			{
				$city_id = 'Hongkong';
			}

			$data['user_company_id']  = "";
			$data['company_name']     = $row['company'];
			$data['company_domain']   = "";
			$data['company_email']    = "";
			$data['job_city_id']      = $city_id;
			$data['show_company_name']= 1;
			$data['job_title']        = $row['job_title'];
			$data['year_work_start']  = $row['year_from'];
			$data['year_work_end']    = $row['year_to'];
			$data['income_range']     = "";
			$data['industry_id']      = "";
			$data['job_function_id']  = "";
			$this->insert_company($language_id,$user_id,$data);
		}
		return $data;
	}

	public function find_city($fb_city_string)
	{
		$fb_city_string = trim(strtolower($fb_city_string));
		$language_id = $this->session->userdata('sess_language_id');
		$this->db->select('city_id,description');
		$this->db->where('display_language_id',$language_id);

		$this->db->like('LOWER(description)', $fb_city_string);
		$query          = $this->db->get('city');
		$result = $query->result_array();
		if($result)
		{
			return $result['0']['description'];
		}
		else
		{
			return false;
		}

	}

	public function get_user_school_id($user_id){
		$this->db->select('user_school_id');
		$this->db->where('user_id',$user_id);
		$result          = $this->db->get('user_school');
		$user_school     = array();
		if($result->num_rows()>0){
			foreach($result->result_array() as $row){
				$user_school[$row['user_school_id']] = $row['user_school_id'];
			}
		}

		return $user_school;
	}

	public function get_user_schools($user_id,$getAllFields = TRUE ){
		//$this->db->select('user_school_id');
		if($getAllFields ===  FALSE)
		{
			$this->db->select('user_school_id');
		}
		$this->db->where('user_id',$user_id);
		$result          = $this->db->get('user_school')->result_array();
		$user_school     = array();
		$user_school = $result;

		return $user_school;
	}


	public function get_user_company_id($user_id){
		$this->db->select('user_company_id');
		$this->db->where('user_id',$user_id);
		$result          = $this->db->get('user_job');
		$user_company    = array();
		if($result->num_rows()>0){
			foreach($result->result_array() as $row){
				$user_company[$row['user_company_id']] = $row['user_company_id'];
			}
		}

		return $user_company;
	}
	public function get_currency_by_country($language_id,$country_id){
		$this->db->select('currency.currency_id,currency.description');
		$this->db->join('country','currency.currency_id=country.currency_id');
		$this->db->where('currency.display_language_id',$language_id);
		$this->db->where('country_id',$country_id);
		$result          = $this->db->get('currency');
		$currency        = "";
		if($result->num_rows()>0){
			$row         = $result->result_array();
			$currency['description']= $row['0']['description'];
			$currency['currency_id']= $row['0']['currency_id'];
		}
		return $currency;
	}

	public function addUserEmail($data)
	{
		$this->db->insert('user_email',$data);
	}

	public function setFacebookData($fb_user, $invite_id,$set_session = TRUE)
	{
		//echo "<pre>";print_r($fb_user);exit;
		$fb_details['relationship_status'] = "";
		$gender_id                         = $this->model_user->get_gender_id(trim($fb_user['gender']));
		//$relationship_status_id            = $this->model_user->get_relationship_status_id(trim($fb_user['relationship_status']));
		
		$relationship_status_id = '1';
		//edit complete.

		$fb_details                        = array();
		$fb_details['gender']              = $gender_id;
		$fb_details['relationship_status'] = $relationship_status_id;

		//myself
		$fb_details['myself'] = '';
		if (isset($fb_user['bio'])) {
			$fb_details['myself'] = trim($fb_user['bio']);
		}
		
		//dob
		$fb_details['dob'] = array('d' => '','m' => '','y' => '');
		
		if (isset($fb_user['birthday'])) {			
			$dob = explode('/', $fb_user['birthday']);
			$fb_details['dob']['d'] = trim($dob[1]);
			$fb_details['dob']['m'] = trim($dob[0]);
			$fb_details['dob']['y'] = trim($dob[2]);
		}	
		
		// location
        $fb_details['location'] = array('city'=> '','country' => '');
        
        if (isset($fb_user['location'])) {            	
			$location = explode(',', $fb_user['location']['name'], 2);
			//$country  = $this->model_user->get_country_id(trim($location[1]));
			$fb_details['location']['city']    = trim($location[0]);
			$fb_details['location']['country'] = trim($location[0]);
        }
        
        // born in
        $fb_details['born_in'] = array('city'    => '','country' => '');
		if (isset($fb_user['hometown'])) {
			$location      = explode(',', $fb_user['hometown']['name'], 2);
			if(isset($location[1]))
			{
				$born_country  = $this->model_user->get_country_id(trim($location[1]));
				$fb_details['born_in']['city']    = trim($location[0]);
				$fb_details['born_in']['country'] = $born_country;
			}
		}
		
		// want to date
		$fb_details['want_date'] = array();

		if (!empty($fb_user['interested_in'])) {
			for($i=0;$i<count($fb_user['interested_in']);$i++){
				$want_to_date   = $this->model_user->get_gender_id(trim($fb_user['interested_in'][$i]));
				$fb_details['want_date'][$i]= $want_to_date;
			}
		}
		
		
		// education
		$fb_details['education'] = array();
		if (isset($fb_user['education'])) {
			foreach ($fb_user['education'] as $education) {
				
				if(isset($education['school']['name']) /*&& isset($education['degree']['name'])*/){
					
					$fb_edu = array(
						'school' => trim($education['school']['name']),
						'year'   => 'N.A',
						'degree' => 'N.A',
						'education_type'=>'N.A',
						'majors' => array(),
					);
					
					if (isset($education['type'])) {
						$fb_edu['education_type'] = trim($education['type']);
					}
					
					if (isset($education['year'])) {
						$fb_edu['year'] = trim($education['year']['name']);
					}

					if (isset($education['degree'])) {
						$fb_edu['degree'] = trim($education['degree']['name']);
					}

					if (isset($education['concentration'])) {
						foreach ($education['concentration'] as $major) {
							$fb_edu['majors'][] = trim($major['name']);
						}
					}

					$fb_details['education'][] = $fb_edu;
				}
			}
		}
		
		//echo "<pre>";print_r($fb_user['work']);exit;
		// work
		$fb_details['work'] = array();
		if (isset($fb_user['work'])) {
			foreach ($fb_user['work'] as $work) {
				$fb_work = array(
				'company' => trim($work['employer']['name']),
				'job_title' => 'N.A',
				'year_from' => 'N.A',
				'year_to' => 'N.A'
				);

				if (isset($work['position'])) {
					$fb_work['job_title'] = trim($work['position']['name']);
				}

				if (isset($work['location'])) {
					$fb_work['job_city_name'] = trim($work['location']['name']);
				}

				if (isset($work['start_date'])) {
					list($fb_work['year_from']) = explode('-', $work['start_date']);
				}

				if (isset($work['end_date'])) {
					list($fb_work['year_to']) = explode('-', $work['end_date']);
				}
				else
				{
					$fb_work['year_to'] = '9999';
				}
				$fb_details['work'][] = $fb_work;
			}
		}
		// speak
		$fb_details['languages'] = array();
		if (isset($fb_user['languages'])) {
			foreach ($fb_user['languages'] as $language) {
				$language_id   = $this->model_user->get_spoken_language_id(trim($language['name']));
				if($language_id!="")
				$fb_details['languages'][$language_id] = trim($language['name']);
			}
		}

		/*-------------change By Hannan Munshi----------------*/

		if(isset($fb_user['religion']))
		{
			$fbReligionName = $fb_user['religion'];
			$religionId = $this->getReligionId($fbReligionName);

			if(!empty($religionId))
			$fb_details['religionId'] = $religionId;
		}
		
		$user = $this->getByFacebookId($fb_user['id']);
		if(!empty($user)){
			$user_id    = $user->user_id;
		}else{

			$isEmail = $this->getByEmailId($fb_user['email']);

			if(!empty($isEmail))
			{
				$this->session->set_userdata('is_email_exist', '1');
				$user_id = '0';
			}
			else
			{
				// create user with facebook id
				$user_data = array(
				'first_name'  => $fb_user['first_name'],
				'last_name'   => $fb_user['last_name'],
				'facebook_id' => $fb_user['id'],
				//'applied_date'=> date('Y-m-d H:i:s'),
				'ref_user_id' => $invite_id

				);
				$user_id = $this->model_user->insert_user($user_data);
				if ($fb_user['email']) {
					// insert user email
					$this->addUserEmail(array(
						'user_id'       => $user_id,
						'email_address' => $fb_user['email'],
						'is_verified'   => 1,
						'is_contact'    => 1
					));
				}
			}
		}
		$this->session->set_userdata('user_id', $user_id);
		$this->session->set_userdata('sign_up_id', $user_id);
		//$this->session->set_userdata('ad_id', $user->ad_id);		
		//$this->session->set_userdata('temp_user_data',$fb_user);
		
		/*-------------Rajnish ----------------*/
		
		$this->session->set_userdata('fb_user_data', $fb_details);
		$website_name = get_assets('name','DateTix');
		$this->session->set_userdata('succ_email_verify',"Thanks for applying to ".$website_name.", ".$fb_user['first_name']."." );
		
		if(isset($fb_user['profile_album']))
		{
			$user_id = $this->session->userdata('user_id');
			if($user_id)
			{
				$pathToUpload = './user_photos/user_'.$user_id.'/';

				if ( ! file_exists($pathToUpload) )
				{
					$create = mkdir($pathToUpload, 0777);
				}
				 
				foreach ($fb_user['profile_album']  as $key=>$img)
				{
					$file_name = $img['id'].'_profile_pic.jpg';
					if(!file_exists($pathToUpload.$file_name))
					{
						file_put_contents($pathToUpload.$file_name,file_get_contents($img['source']));
						$data   = array('user_id'=>$user_id,'photo'=>$file_name,'uploaded_time'=>SQL_DATETIME);
						$this->db->insert('user_photo',$data);
					}
					
					/*
					 $fb_details['photo'][$key] = $img['images']['0'];
					 $fb_details['photo'][$key]['user_photo_id'] = $key;
					*/
										
					if($key == 0)
					{
						$last_id = $this->db->insert_id();
						if($last_id != 0)
						{
							//Remove Primary Photos
							$this->db->where(array('user_id'=>$user_id));
							$this->db->update('user_photo',array('set_primary'=>'0'));
							
							//set Primary Photos
							$this->db->where(array('user_photo_id'=>$last_id));
							$this->db->update('user_photo',array('set_primary'=>'1'));
						}
					}
				}
			}
		}
	}

	public function getReligionId($religion)
	{
		$language_id = $this->session->userdata('sess_language_id');
		$this->db->where(array('description'=>$religion,'display_language_id'=>$language_id));
		$data = $this->db->get('religious_belief')->row();

		if(!empty($data))
			return $data->religious_belief_id;
		else
			return false;
	}

	public function getByFacebookId($facebook_id = "")
	{
		if($facebook_id != "")
		{
			$this->db->where('facebook_id', $facebook_id);
			$q = $this->db->get('user', 1);
			return ($q->num_rows() > 0) ? $q->row() : false ;
		}
		else
			return false;
		
	}

	public function getByEmailId($email)
	{
		$this->db->where('email_address',$email);
		$q = $this->db->get('user_email', 1);
		return ($q->num_rows() > 0) ? $q->row() : false ;
	}

	public function send_sms($mobile_number,$verification_code){
		$this->load->library('nexmo');
		// set response format: xml or json, default json
		$this->nexmo->set_format('json');
		$from = get_assets('name','DateTix');
		$to         = $mobile_number;
		$body       = translate_phrase("Your ").get_assets('name','DateTix').translate_phrase(" verification code is: ").$verification_code;
		$message    = array('text' => $body );
		$response   = $this->nexmo->send_message($from, $to, $message);
		if($response->messages['0']->status==0){
			$msg        = "We have sent a SMS verification code to  +".$mobile_number.". Please <b>enter the code below:</b>";
			$return_msg = '<div><span>'.translate_phrase($msg).'</span></div>';
		}else{
			$return_msg = '<div class="error_msg" style="color:#ed217c;">'.translate_phrase("Failed to sent sms.Please try again").'</div>';
		}
		echo $return_msg;
	}

	public function send_veri_sms($mobile_number,$verification_code){
		$this->load->library('nexmo');
		// set response format: xml or json, default json
		$this->nexmo->set_format('json');

		$from = get_assets('from_email_name','DateTix');
		$to         = $mobile_number;
		$body       = translate_phrase("Your ").get_assets('name','DateTix').translate_phrase(" verification code is: ").$verification_code;
		$message    = array('text' => $body );
		$response   = $this->nexmo->send_message($from, $to, $message);
		if($response->messages['0']->status==0){
			return true;
		}else{
			return false;
		}
	}

	public function get_user_field($user_id,$field){
		$this->db->select($field);
		$this->db->where('user_id', $user_id);
		$result      = $this->db->get('user');
		$result_row  = "";
		if($result->num_rows()>0){
			$row         = $result->result_array();
			$result_row  = $row ['0'][$field];
		}
		return $result_row;
	}
	public function check_sms_verified($verification_code,$code,$user_id){
		//if($verification_code==$code){
		if(strcasecmp($verification_code,$code) == 0){
			echo "1";
			$this->update_user($user_id,array('mobile_phone_is_verified'=>'1'));
		}
		else
		echo '<p style="width: 494px;color:#FD2080;">'.translate_phrase("Invalid verification code").'</p>';
	}
	public function get_school_count($user_id){
		$this->db->where('user_id',$user_id);
		$count   = $this->db->count_all_results('user_school');
		return $count;
	}
	public function get_company_count($user_id){
		$this->db->where('user_id',$user_id);
		$count   = $this->db->count_all_results('user_job');
		return $count;
	}
	public function getCountryByCity($city_id)
	{
		$this->db->select('country.*');
		$this->db->where('city_id', $city_id);
		$this->db->join('province', 'province.country_id = country.country_id', 'inner');
		$this->db->join('city', 'city.province_id = province.province_id', 'inner');
		$q = $this->db->get('country', 1);
		return ($q->num_rows() > 0) ? $q->row() : NULL ;
	}
	
	public function get_interest($language_id,$interest){
		$this->db->select('interest_id,description');
		$this->db->where('display_language_id',$language_id);
		$this->db->Like('description',$interest);
		$this->db->order_by('view_order','ASC');
		$result = $this->db->get('interest');
		$results        = array();
		if($result->num_rows()>0){
			foreach( $result->result_array() as $row){
				$results[]=$row['description'];
			}
		}
		return $results;
	}
	public function get_heared_abou_us($language_id,$city_id){
		$this->db->select('how_you_heard_about_us_id,description,show_other,show_text');
		$this->db->where('display_language_id',$language_id);
		//$this->db->where('city_id',$city_id);
		$this->db->order_by('view_order','ASC');
		$result       = $this->db->get('how_you_heard_about_us');
		//$heared_about = array(''=>translate_phrase('Select source'));
		$heared_about = array();
		if($result->num_rows()>0){
			$heared_about=$result->result_array();
		}
		return $heared_about;
	}
	public function create_folder($user_id,$photo_for){
		$session            = $this->session->all_userdata();
		$current_session    = $session['session_id'];
		$folderName         = "user_".$user_id;
		$pathToUpload       = './user_photos/' . $folderName.'/';

		if ( ! file_exists($pathToUpload) )
		{
			$create = mkdir($pathToUpload, 0777);
			if ( ! $create)
			return;

		}
		if ( file_exists($pathToUpload) )
		{
			if($photo_for=='photo_diploma'){
				$pathToUpload = $pathToUpload."school_".$current_session.'/';
			} else if($photo_for=='photo_business_card'){
				$pathToUpload = $pathToUpload."company_".$current_session.'/';
			}

			// checking for edu & career path
			if (!file_exists($pathToUpload)) {
				mkdir($pathToUpload, 0777);
			}

			return  $pathToUpload;
		}

	}
	public function get_city_by_id($city_id)
	{
		$this->db->where('city_id', $city_id);
		$q = $this->db->get('city', 1);
		return ($q->num_rows() > 0) ? $q->row() : NULL ;
	}
	public function get_city_by_user_id($user_id)
	{
		$fieldName = "current_city_id";
		
		$this->db->select($fieldName);
		$this->db->where('user_id', $user_id);
		
		$q = $this->db->get('user', 1);
		$result = ($q->num_rows() > 0) ? $q->row() : NULL ;
		if(!empty($result))
			return $result->$fieldName;
		
	}
	public function check_verification_mail_sent($user_id,$user_school_id,$email,$mail_for){
		//send verification email
		if($email!=""){

			if($mail_for=="company"){
				$user_company       = $this->get_user_company_by_id($user_school_id);
				$verification_code  = $user_company['verification_code']?$user_company['verification_code']:$this->verification_code();
				$send               = $this->model_user->send_company_verification_mail($email,$verification_code);
			}
			if($mail_for=="school"){
				$user_school        = $this->get_user_school_by_id($user_school_id);
				$verification_code  = $user_school['verification_code']?$user_school['verification_code']:$this->verification_code();
				$send               = $this->model_user->send_school_verification_mail($email,$verification_code);
			}
			if($send=="1"){
				if($mail_for=="company")
				$this->update_user_company($user_school_id,array('verification_code'=>$verification_code));
				if($mail_for=="school")
				$this->update_user_school($user_school_id,array('verification_code'=>$verification_code));
				echo "1";
			}else
			echo translate_phrase('Failed to send email.Please try again');
		}
	}
	public function send_new_password(){
		$email    =  $this->input->post('email');
		$exist    = $this->check_user_email_exit($email);
		if($exist>0){
			//send mail
			$this->send_forgot_password_mail($email);
			return "1";

		}else{
			return "0";
		}
	}
	public function check_user_email_exit($email){
		$this->db->where('is_verified','1');
		$this->db->where('email_address',$email);
		$result = $this->db->count_all_results('user_email');
		return $result;
	}

	public function send_forgot_password_mail($user_email){
		$this->load->library('utility');
		$user_name  = $this->get_user_by_email($user_email);
		$first_name = $user_name ? $user_name->first_name : '';
		$last_name  = $user_name ? $user_name->last_name : '';
		$user_id    = $user_name ? $user_name->user_id : '';
		$encoded_id = htmlentities($this->utility->encode($user_id));
		$from_email = INFO_EMAIL;//"hongkong@datetix.com";
		$team       = translate_phrase("The ").get_assets('name','DateTix').translate_phrase(" Team");
		$subject    = translate_phrase("Reset password");
		$body       = translate_phrase("Hi "). $first_name . ' ' . $last_name . "\n\r";
		$body       .= translate_phrase("We received a password reset request for your ").get_assets('name','DateTix').translate_phrase("  account. To reset your password, click on the link below: ")."\n\r";
		$body       .= base_url().url_city_name()."/password-reset.html/".$encoded_id."\n\r";
		$body       .= translate_phrase("If you didn't request a password reset, send us an email at security@datetix.com. Your password will not be change if you ignore this email.");
		$body       .= "\n\r".translate_phrase("Thanks,")."\n\r";
		$body       .= $team;
		$res        = $this->send_email($from_email,$user_email,$subject,$body);
		return $res;
	}

	public function get_user_by_email($email){
		$this->db->select('user.first_name,user.last_name,user.user_id,user.current_city_id, user.facebook_id,user.approved_date,user.completed_application_step,user.password,user.last_display_language_id,user_email.is_verified,user.ad_id');
		$this->db->join('user_email','user.user_id=user_email.user_id');
		$this->db->where('user_email.email_address',$email);
		$q  = $this->db->get('user');
		return ($q->num_rows() > 0) ? $q->row() : NULL ;
	}

	public function delete_profile_photo($photo_id, $user_id)
	{
		$this->db->where('user_id', $user_id);
		$this->db->from('user_photo');
		$user_photos = $this->db->count_all_results();
		if($user_photos > 1)
		{
			$this->db->where('user_photo_id', $photo_id);
			$q = $this->db->get('user_photo', 1);
			
			if ($q->num_rows() > 0) {
				$user_photo = $q->row_array();
				
				// remove image from stroage
				unlink('./user_photos/user_' . $user_id . '/' . $user_photo['photo']);
				
				// remove database entry
				$this->db->limit(1);
				$this->db->where('user_photo_id', $photo_id);
				$this->db->delete('user_photo');
	
				return '1';
			}
		}
		return "0";
	}

	public function delete_edu_temp_photo($folder, $user_id)
	{
		$path = "./user_photos/user_$user_id/$folder";
		delete_files($path);
		rmdir($path);
	}

	public function delete_card_passport($user_id)
	{
		$this->db->where('user_id', $user_id);
		$this->db->select('photo_id');
		$q = $this->db->get('user');

		if ($q->num_rows() > 0) {
			$user = $q->row_array();

			// remove image from stroage
			$picDeletionStatus = unlink('./user_photos/user_' . $user_id . '/' . $user['photo_id']);

			// remove database entry
			$this->db->limit(1);
			$this->db->where('user_id', $user_id);
			$updObj = $this->db->update('user', array('photo_id' => ''));

			//below line added and commented by Hannan Munshi.Commented because we are using old code only.
			//return array('unlink'=>$picDeletionStatus,'update'=>$updObj);
		}
	}

	public function send_email_invites($invites, $user_id, $subject, $body)
	{

		$user = $this->get_user($user_id);
		$from_email = 'invite@datetix.com';

		for ($i=0; $i < count($invites['email']); $i++) {
			if (filter_var($invites['email'][$i], FILTER_VALIDATE_EMAIL)) {
				$body= str_replace('#Name#', $invites['first_name'][$i] . ' ' . $invites['last_name'][$i], $body);
				$this->send_email($from_email,$invites['email'][$i],$subject,$body);
			}
		}
	}

	public function forgot_password($email)
	{
		if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
			if ($this->check_user_email_exit($email)) {
				$this->db->where('user_email.email_address', $email);
				$this->db->where('user.approved_date IS NOT NULL');
				$this->db->join('user_email', 'user.user_id = user_email.user_id', 'inner');
				if ($this->db->count_all_results('user')) {
					// user approved by admin
					$this->send_forgot_password_mail($email);
					$data = array(
                        'success' => 1,
                        'message' => translate_phrase('Your password reset email has been sent to ') . $email
					);
				} else {
					// user has not been approved by admin
					$data = array(
                        'success' => 0,
                        'message' => translate_phrase('Your application is still pending approval.')
					);
				}
			} else {
				$data = array(
                    'success' => 0,
                    'message' => translate_phrase('E-mail address not found.')
				);
			}
		} else {
			$data = array(
                'success' => 0,
                'message' => translate_phrase('Please enter a valid email address.')
			);
		}
		return $data;
	}
	public function is_signup_process(){
		
		$user_id = $this->session->userdata('user_id');
		$sign_up_id = $this->session->userdata('sign_up_id');
		
		if(!$user_id && !$sign_up_id){
			redirect('/');
		}
	}
	public function is_active_city($city_name,$return_to){
		$this->load->model('model_city');
		$this->load->model('model_home');
		$city = $this->model_city->getByName($city_name);
		if (!isset($city->city_id)) {
			$city->city_id = 0;
			$fb_user_data  = $this->session->userdata('fb_user_data');
			$fb_user_data['location']['city']   = 'Hong Kong';
			$fb_user_data['location']['country']   = 'Hong Kong';
			$this->session->set_userdata('fb_user_data',$fb_user_data);
			$this->model_home->redirect_to_city($city->city_id,$return_to);
		}else{
			//$this->model_home->redirect_to_city($city->city_id,$return_to);
		}
	}
	
	
	/**
	 * apply_coupon :: Common function for update user membership data based on coupon data from session
	 * @params  user_id
	 * @return true/false.
	 * @author by Rajnish
	 */
	public function apply_coupon($user_id)
	{
		$flag = 0;				
		//Apply Copon code to user account
		if($coupon_details = $this->session->userdata('coupon_details'))
		{
			
			//if membership ids...
			if($coupon_details['membership_option_ids'])
			{
				$coupon_membership_options = explode(',', $coupon_details['membership_option_ids']);
				if ($coupon_membership_options) {
					$this -> general -> set_table('user_membership_option');
					$membership_options_data['user_id'] = $user_id;
					
					foreach ($coupon_membership_options as $value) {
						$membership_options_data['membership_option_id'] = $value;
						
						if ($user_member_data = $this -> general -> get("", $membership_options_data))
						{
							if ($user_member_data['0']['expiry_date'] && $user_member_data['0']['expiry_date'] >= date('Y-m-d'))
							{
								$membership_options_update_data['expiry_date'] = date('Y-m-d', strtotime($coupon_details['membership_duration_months'] . " month", strtotime($user_member_data['0']['expiry_date'])));
							}
							else 
							{
								$membership_options_update_data['expiry_date'] = date('Y-m-d', strtotime($coupon_details['membership_duration_months'] . " month"));
							}
							if ($this -> general -> update($membership_options_update_data, $membership_options_data))
							{
								$flag = 1;
							}
						}
						else
						{
							$membership_options_data['expiry_date'] = date('Y-m-d', strtotime($coupon_details['membership_duration_months'] . " month"));
							if ($this -> general -> save($membership_options_data))
							{
								$flag = 1;
							}
						}
					}
				}
			}
			
			//If coupon have tickets
			if($coupon_details['num_date_tix'])
			{
				$this -> general -> set_table('user');
				$user_data = $this -> general -> get("num_date_tix", array('user_id' => $user_id));
				$user = $user_data['0'];

				//Update User Tickets
				$update_data['num_date_tix'] = $user['num_date_tix'] + $coupon_details['num_date_tix'];
				if ($this -> general -> update($update_data, array('user_id' => $user_id))){
					$flag = 1;
				}
			}
			
			if($flag)
			{
				// add coupon id in user table
				$this -> general -> set_table('user');
				$this -> general -> update(array('coupon_promo_id'=>$coupon_details['coupon_promo_id']),array('user_id'=>$user_id));
			}
			
			//Remove coupon data from session otherwise each time membership will be updated:
			$this->session->unset_userdata('coupon_details');
		}
		return $flag;
	}
	
	public function is_current_signup_process($user_id){
		$session_redirect_url = $this->session->userdata('return_url');
		$this->session->unset_userdata('return_url');
		
		//echo "<pre>";print_r($this->session->all_userdata());exit;
		
		$user = $this->get_user($user_id);
		$step     = $user->completed_application_step;
		
		if($step==0){
			if(return_url()!= "signup-step-1.html")
				redirect( base_url() . url_city_name()."/signup-step-1.html");
		}else{
			if($step >= 5)
			{
				//save_date_id
				$date_condition['requested_user_id'] = 0;
				$date_condition['session_id'] = $this->session->userdata('session_id');
				$this->load->model('general_model');
				$this->general_model->set_table('date');
				if($guest_date_datas = $this->general_model->get("",$date_condition,array('date_time'=>'asc')))
				{
					$date_id = $this -> session -> set_userdata('save_date_id',$guest_date_datas['0']['date_id']);
					redirect('dates/new_date_step4');
				}
				//echo "<pre>";print_r($guest_date_datas);exit;
			}
			
			if($step >= LAST_SIGN_UP_STEP){
				
				if($user_id == $this->session->userdata('user_id'))
				{
					$this->apply_coupon($user_id);
				}
								
				if(isset($event_info))
				//if($this->session->userdata('event_ticket_id'))
				{
					redirect( base_url() . url_city_name()."/signup-confirmation.html");
				}
				else if(return_url() != "signup-confirmation.html" && return_url()!= "invite-friends.html")
				{
					
					//Free RSVP User - Register/Login
					if ($post_data = $this -> session -> userdata('post_data')) {
						
						$post_data['user_id'] = $user_id;
						$this -> session -> set_userdata('post_data',$post_data);
						
						//Insert data in event user table
						$this -> general -> set_table('event_user');
						$condtion_event_user = array('user_id' => $post_data['user_id'],'event_id' => $post_data['event_id']);
						if(!$this -> general -> checkDuplicate($condtion_event_user))
						{
							$post_data['rsvp_time'] = SQL_DATETIME;
							
							//Newly Added fields
							$post_data['agent_string'] = $this->agent->agent_string();
							 
							$this -> general -> save($post_data);
						}
						if($session_redirect_url)
						{
							redirect($session_redirect_url);
						}
						else {
							$sql =  'SELECT user_intro_id FROM user_intro WHERE (user1_id = "'.$user_id.'" OR user2_id = "'.$user_id.'") AND DATE(intro_expiry_time) >= DATE(CURDATE())';
							$query = $this->db->query($sql);
							if($total_intro =  $query->num_rows())
							{
								redirect('dates/find_dates');
								//redirect( base_url() . url_city_name()."/my-intros.html");
								//redirect('events/confirm_rsvp');								
							}
							else
							{
								redirect( base_url() . url_city_name()."/edit-profile.html");							
								//redirect('events/confirm_rsvp');
							}	
						}
						
					}
					
					$this -> session -> set_userdata('fb_user_data', '');
					//If user comes though ads
					$event_id = $this->session->userdata('event_id');
					if(isset($event_info))
					//if($event_id && $event_id > 0)
					{
						//$url = base_url() . url_city_name()."/event.html?id=".$event_id;
						
						//if($ad_id == $this->session->userdata('ad_id'))
						//{
						//	$url .= "&src=".$this->session->userdata('ad_id');
						//}
						//$url .= "&src=12";
						redirect('dates/new_date_step1');
						
						//redirect( base_url() . url_city_name()."/signup-confirmation.html");
						
					}
					else if($session_redirect_url)
					{
						redirect($session_redirect_url);
					}
					else
					{
						$sql =  'SELECT user_intro_id FROM user_intro WHERE (user1_id = "'.$user_id.'" OR user2_id = "'.$user_id.'") AND DATE(intro_expiry_time) >= DATE(CURDATE())';
						$query = $this->db->query($sql);
						if($total_intro =  $query->num_rows())
						{
							redirect('dates/find_dates');
							//redirect( base_url() . url_city_name()."/my-intros.html");
						}
						else
						{
                            /* $user_data = $this->get_user($user_id);																								
							$this->general->set_table('reward');
							$signin_reward = $this->general->get("",array('reward_id'=>1));	
							
							$this->general->set_table('user_reward');
							$user_reward = $this->general->get("",array('reward_id'=>1,'user_id'=>$user_id,'DATE(reward_time)'=>SQL_DATE));
							//echo $this->db->last_query();
							//print_r($user_reward );exit;
							
							if($signin_reward && !$user_reward)
							{
								$data['num_date_tix']= $user_data->num_date_tix+$signin_reward[0]['num_date_tix'];
								$this->update_user($user_id,$data);
								
								$user_reward_data = array(
									'user_id'=>$user_id,
									'reward_id'=>1,
									'reward_time'=>SQL_DATETIME,
									'num_date_tix'=>$signin_reward[0]['num_date_tix'],
								);
								
								$this->general->set_table('user_reward');
								$this->general->save($user_reward_data);
								
								$this->session->set_userdata('singup_reward','1');
							}
							*/                             
							if($user->approved_date==null){
								redirect('dates/new_date_step1');
						
								//redirect( base_url() . url_city_name()."/signup-confirmation.html");
							}
							else {
								redirect('dates/find_dates');
								//redirect( base_url() . url_city_name()."/edit-profile.html");
							}
						}
					}					
				}
			}else{
				
                if($step==4 || $step==5){
                	$next_step=7;
                }else{
                    $next_step = $step+1;
                }
				
				if(return_url()!= "signup-step-$next_step.html")
					redirect( base_url() . url_city_name()."/signup-step-$next_step.html");
			}
		}
	}
	public function remove_school_by_user($user_id){
		$user_school_id = $this->get_user_school_id($user_id);
		foreach($user_school_id as $id){
			$this->delete_majors($id);
			$this->delete_minors($id);
		}
		$this->db->where('user_id',$user_id);
		$this->db->delete('user_school');
	}
	public function remove_company_by_user($user_id){
		$this->db->where('user_id',$user_id);
		$this->db->delete('user_job');
	}

	public function get_user_school_by_id($user_school_id){
		$this->db->where('user_school_id',$user_school_id);
		$result        = $this->db->get('user_school');
		$user_school[0] = array();
		if($result->num_rows()>0){
			$user_school = $result->result_array();
		}
		return $user_school[0];
	}

	public function get_user_school_by_condition($condition){
		$this->db->where('user_school_name',$user_school_name);
		$result        = $this->db->get('user_school');
		$user_school[0] = array();
		if($result->num_rows()>0){
			$user_school = $result->result_array();
		}
		return $user_school[0];
	}

	public function get_user_company_by_id($user_company_id){
		$this->db->where('user_company_id',$user_company_id);
		$result        = $this->db->get('user_job');
		$user_school[0] = array();
		if($result->num_rows()>0){
			$user_school = $result->result_array();
		}
		return $user_school[0];

	}
	public function get_city_list($language_id,$city){
		$this->db->where('display_language_id',$language_id);
		$this->db->Like('description',$city);
		$this->db->order_by('view_order','ASC');
		$result = $this->db->get('city');
		$results        = array();
		if($result->num_rows()>0){
			foreach( $result->result_array() as $row){
				$results[]=trim($row['description']);
			}
		}
		return $results;
	}
	public function is_mobile_verified($user_id){
		$this->db->where('mobile_phone_verification_code !=','');
		$this->db->where('mobile_phone_is_verified !=','1');
		$this->db->where('user_id',$user_id);
		return $this->db->count_all_results('user');
	}
	public function is_hear_about_us_placeholder_exist($id){
		$this->db->select('show_text');
		$this->db->where("how_you_heard_about_us_id",$id);
		$this->db->where("show_other","1");
		$q   = $this->db->get('how_you_heard_about_us');
		return ($q->num_rows() > 0) ? $q->row() : NULL ;
	}
	public function change_url_by_current_city($city_id,$user_id){
		$this->load->model('model_home');
		$current_city_id = $this->get_current_living_city($user_id);
		if($current_city_id!=$city_id)
		$this->model_home->redirect_to_city($current_city_id,return_url());
	}
	public function get_current_living_city($user_id){
		$this->db->select('current_city_id');
		$this->db->where('user_id',$user_id);
		$q       = $this->db->get('user');
		if($q->num_rows() > 0)
		$result  = $q->row() ;
		$current_city_id  =  isset($result->current_city_id)?$result->current_city_id:'';
		return $current_city_id;
	}

	public function get_user_data($user_id)
	{
		$sql = 'SELECT user.*,
				CASE
					WHEN
						birth_date != "0000-00-00" 	
					THEN 
						TIMESTAMPDIFF(YEAR,`birth_date`,CURDATE())
					END as age
				FROM user 
				WHERE user_id = '.$user_id;

		$query = $this->db->query($sql);
		$res = $query->result_array();
		if($res)
		{
			return $res['0'];
		}
		else
		return null;

	}

	public function get_data($table,$condition)
	{
		$query = $this->db->get_where($table,$condition);
		return $query->result_array();
	}

	/**
	 * Function check record is exist or not.
	 * @access public
	 * @param array  - result
	 * @return boolean true if have dublicate record and false doen't dublicate record
	 * @author Rajnish Savaliya
	 */
	public function checkDuplicate($condition,$table=''){
		if($table == '')
		$table = $this->_table;

		$query = $this->db->get_where($table,$condition);
		if($query->num_rows()>=1){
			return true;
		}else{
			return false;
		}
	}

	/**
	 * Count Number of record from table
	 * @access public
	 * @Optional = table name
	 * @author Rajnish Savaliya
	 * @return array()
	 */
	function count_record($condition,$table='')
	{
		if($table == '')
		$table = $this->_table;

		$query = $this->db->get_where($table,$condition);
		return $query->num_rows();
	}

	/**
	 * Join Two Table
	 * @access public
	 * @param array  - result
	 * @return stdClass
	 *  @author Rajnish Savaliya
	 */
	public function singleJoin($parentTable,$childTable,$select,$condition,$where=array()){
		$this->db->select($select);
		$this->db->from($parentTable);
		$this->db->where($where);
		$this->db->join($childTable,$condition);
		return $this->db->get()->result();
	}

	/**
	 * Join Two or More Table : mulitple joins with multiple where condition and multiple like condition
	 * @access public
	 * @param array  - result
	 * @return array   - result
	 * @author Rajnish Savaliya
	 */

	public function multijoins($fields,$from,$joins,$where,$likes=NULL,$ordersby='',$num=NULL,$offset=NULL,$action='',$wheretype='where',$groupby='')
	{
		$this->db->select($fields);

		if($wheretype == 'where'){
			$this->db->where($where);
		}

		if($wheretype == 'where_in'){
			$this->db->where($where);
		}

		if($groupby != ''){
		 $this->db->group_by($groupby);
		}

		foreach($joins as $key => $value){
			$this->db->join($key, $value[0], $value[1]);
		}

		if($likes != NULL){
			foreach($likes as $field =>$like){
				$this->db->like($field, $like);
			}
		}
		if($ordersby != ''){
			$this->db->order_by(''.$ordersby.'');
		}
		if($action == 'count'){
			return	$this->db->get($from,$num,$offset)->num_rows();
		}else{
			return $this->db->get($from,$num,$offset)->result_array();
		}
	}


	public function getInterests($languageId = '')
	{
		//if language id is not supplied. default to english.
		$languageId = (empty($languageId)) ? $this->session->userdata('sess_language_id') : $languageId;

		$instance = get_instance();
		//$query = "SELECT interest_category_id,description FROM interest_category WHERE display_language_id ='".$languageId."'";
		$query = "SELECT a.interest_category_id,a.description as parentDescription, b.description, b.interest_id
                      FROM interest_category a
                      JOIN interest b ON a.interest_category_id = b.interest_category_id
                      AND a.display_language_id = b.display_language_id
                      WHERE a.display_language_id ='".$languageId."'";
		$result = $instance->db->query($query)->result();
		if(!empty($result))
		{
			$finalAray = array();
			$parentKeys = array();

			//create a seperate array of parent cat id and name.
			foreach($result as $key => $categoryDetails)
			{
				if(!array_key_exists($categoryDetails->interest_category_id, $parentKeys))
				{
					$parentKeys[$categoryDetails->interest_category_id] = $categoryDetails->parentDescription;
				}
			}

			//create a tree structure.
			foreach ($parentKeys as $id => $categoryName)
			{
				$finalAray[$id] = array();
				foreach ($result as $key => $value)
				{
					if($id == $value->interest_category_id)
					{
						array_push($finalAray[$id], $value);
					}
				}
			}

			return array('parentDetails' => $parentKeys,'childDetails' => $finalAray);
		}

		//return false if resultSet from database was empty.
		return false;
	}
	
	public function get_website_by_user_email($user_email=""){
		$this->db->select('website.*');
		$this->db->join('user','user.website_id = website.website_id');
		$this->db->join('user_email','user_email.user_id = user.user_id');
		
		$this->db->where('user_email.email_address',$user_email);
		$this->db->limit(1);
		$result = $this->db->get('website');
		if($data = $result->result_array() )
		{
			$data = $data['0'];
		}
		return $data;
	}
        
        public function saveUserFollowing($insert_array){
             $this -> db -> insert('user_follow_user', $insert_array);
                return $this -> db -> insert_id();
        }
}
?>
