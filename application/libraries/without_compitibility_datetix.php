<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Datetix {

	public $CI;
	var $language_id = '1';
	public function __construct()
	{
		$this->CI =& get_instance();
		$this->CI->load->model('model_user');
		$this->CI->load->model('general_model','general');

		if(!$this->CI->session->userdata('sess_language_id'))
		{
			$this->CI->session->set_userdata('sess_language_id','1');
		}

		$this->language_id  = $this->CI->session->userdata('sess_language_id');
	}

	/**
	 * Sent Mail function
	 * @access public
	 * @return true or false
	 * @author Rajnish
	 */
	public function mail_to_user_debug($to,$subject,$message)
	{
		
		//return true;
		$to = 'mikeye27@gmail.com';

		$from = INFO_EMAIL;
		$config['mailtype']  = 'html';
		$config['charset']   = 'UTF-8';
		/*
		 $config['smtp_host'] = "";
		 $config['smtp_user'] = "";
		 $config['smtp_pass'] = "";
		 $config['smtp_port'] = "";
		 */

		$this->CI->load->library('email',$config);
		$this->CI->email->from($from, 'DateTix');
		$this->CI->email->to($to);
		$this->CI->email->subject($subject);
		
		//Unsubscribe mail code
		$unsubscribe_link = base_url('home/unsubscribe/'.$this->CI->utility -> encode($to));		
		$message = str_replace('DATETIX_UNSUBSCRIBE_LINK', $unsubscribe_link, $message);
		
		
		$this->CI->email->message($message);

		if($this->CI->email->send())
		{
			return true;
		}
		else
		return false;
	}

	/**
	 * Sent Mail function
	 * @access public
	 * @return true or false
	 * @author Rajnish
	 */
	public function mail_to_user($to,$subject,$message)
	{
		$from = INFO_EMAIL;
		$config['mailtype']  = 'html';
		$config['charset']   = 'UTF-8';
		/*
		 $config['smtp_host'] = "";
		 $config['smtp_user'] = "";
		 $config['smtp_pass'] = "";
		 $config['smtp_port'] = "";
		 */

		$this->CI->load->library('email',$config);
		$this->CI->email->from($from, 'DateTix');
		$this->CI->email->to($to);
		$this->CI->email->subject($subject);
		
		//Unsubscribe mail code
		$unsubscribe_link = base_url('home/unsubscribe/'.$this->CI->utility -> encode($to));		
		$message = str_replace('DATETIX_UNSUBSCRIBE_LINK', $unsubscribe_link, $message);
		
		$this->CI->email->message($message);

		if($this->CI->email->send())
		{
			return true;
		}
		else
		return false;
	}

	public function get_my_company_data($user_id)
	{
		$language_id = $this->language_id;
		$tmp_cmp_data = array();
		$list_companies = '';

		$this->CI->general->set_table('user_job');
		$companyCondition = array('user_id'=>$user_id);
		$companyFields = array('user_company_id','company_id','company_name','years_worked_start','years_worked_end');
		$companyOrderBy   = array('years_worked_end'=>'desc','years_worked_start'=>'desc','company_name'=>'asc');
		$company = $this->CI->general->get($companyFields,$companyCondition,$companyOrderBy);

		if($company)
		{
			$this->CI->general->set_table('company');
			foreach ($company as $r=>$cmp_val)
			{
				if($cmp_val['company_id'])
				{
					$company_name = $this->CI->general->get("",array('display_language_id'=>$language_id,'company_id'=>$cmp_val['company_id']));
					$company[$r]['company_name'] = isset($company_name['0']['company_name'])?$company_name['0']['company_name']:'';
				}
			}
		}

		/*============================Newly added code for sorting===========================*/
		$neededKey = 0;
		$newarray = array();
		if($company)
		{
			foreach ($company as $key => $value) {

				if($key == 0 || $key>=$neededKey)
				{
					$keyThreshold = $key;
					$outerLoopYearStart = $value['years_worked_start'];
					$outerLoopYearEnd   = $value['years_worked_end'];
					$sameYearsData = array();
					foreach ($company as $k => $v) {

						if($v['years_worked_start'] == $outerLoopYearStart  && $v['years_worked_end'] == $outerLoopYearEnd)
						{
							$sameYearsData[] = $v;
						}
					}

					if(!empty($sameYearsData) && count($sameYearsData)>1)
					{

						$companyNameArray = array();
						uasort($sameYearsData, array($this,'uasort_company_name'));
						$neededKey = $keyThreshold;
						foreach ($sameYearsData as $ke => $val) {
							//$company[$neededKey] = $val;
							$newarray[$neededKey] = $val;
							$neededKey++;
						}
					}
					else
					{
						$neededKey++;
						$newarray[] = $value;
					}
				}

			}
		}

		$company = $newarray;
		foreach ($company as $key => $value)
		{
			$companyIndexes[$value['user_company_id']] = $value['user_company_id'];
		}

		$tmp_cmp_data = array();
		if(isset($companyIndexes) && $companyIndexes)
		{
			foreach ($companyIndexes as $row)
			{
				$tmp_cmp_data[] = $this->CI->model_user->get_company_details($row, $language_id);
			}
		}
		if($tmp_cmp_data)
		{
			foreach ($tmp_cmp_data as $key=>$cmp)
			{
				//----------------------------------------------------------------------------------//
				//If company id then fetch from db.
				if($cmp['company_id'])
				{
					$fields = array('cmp.*','ind.description as industry_description');

					$from = 'company as cmp';
					$joins = array(
					'industry as ind'=>array('cmp.industry_id = ind.industry_id ','LEFT')
					);

					$condition['cmp.display_language_id'] = $language_id;
					$condition['ind.display_language_id'] = $language_id;
					$condition['cmp.company_id'] = $cmp['company_id'];
					$temp = $this->CI->model_user->multijoins($fields,$from,$joins,$condition);

					if($temp)
					{
						$tmp_cmp_data[$key]['company_data'] = $temp['0'];
						$tmp_cmp_data[$key]['industry_description'] = $temp['0']['industry_description'];
						unset($temp);
					}
					unset($condition);
				}

				else if($cmp['industry_id'])
				{
					$this->CI->general->set_table('industry');
					if($comp_industry = $this->CI->general->get("",array('industry_id'=>$cmp['industry_id'],'display_language_id'=>$language_id)))
					{
						$tmp_cmp_data[$key]['industry_description'] = $comp_industry ['0']['description'];
					}
				}

				//----------------------------------------------------------------------------------//
				//If Job Function id
				if($tmp_cmp_data[$key]['job_function_id'])
				{
					$get_condition['job_function_id'] = $tmp_cmp_data[$key]['job_function_id'];
					$get_condition['display_language_id'] = $language_id;

					$tmp_cmp_info = $this->CI->model_user->get_data('job_function',$get_condition);
					if($tmp_cmp_info )
					{
						$tmp_cmp_data[$key]['job_function_data'] = $tmp_cmp_info['0'];
					}
					unset($tmp_cmp_info);
					unset($get_condition);
				}

				//----------------------------------------------------------------------------------//
				//If Job City id
				if($tmp_cmp_data[$key]['job_city_id'])
				{
					$fields = array('ct.*',
						'prvnce.description as province_description',
						'cntry.description as country_description',
						'cntry.country_code','cntry.flag_url');

					$from = 'city as ct';
					$joins = array(
					'province as prvnce'=>array('ct.province_id = prvnce.province_id','LEFT'),
					'country as cntry'=>array('prvnce.country_id = cntry.country_id','LEFT')
					);

					$condition['ct.display_language_id'] = $language_id;
					$condition['prvnce.display_language_id'] = $language_id;
					$condition['cntry.display_language_id'] = $language_id;
					$condition['ct.city_id'] = $tmp_cmp_data[$key]['job_city_id'];
					$temp = $this->CI->model_user->multijoins($fields,$from,$joins,$condition);
					if($temp)
					{
						$tmp_cmp_data[$key]['job_city_data'] = $temp['0'];
						unset($temp);
					}
					unset($condition);
					//echo "<pre>";print_r($temp );exit;
				}

			}//End foreach: Company Data
			return $tmp_cmp_data;
		}//End if: Company Data
		return false;
	}

	public function uasort_company_name($a, $b)
	{
		return strcasecmp($a['company_name'] , $b['company_name']);
	}


	/**
	 * Calculate Score ::
	 * @access Private - Use only this Controller
	 * @param $tablename : Primary Table Name
	 * @param $user_id: Userid
	 * @return Array - Fetch data
	 * @author Rajnish Savaliya
	 */
	public function calculate_score($user_id=3, $intro_id=1)
	{
		$match = array();
		$slightly = array();
		$not_matched = array();

		$obtain_score = 0;
		$total_score = 0;
		$user = $this->CI->model_user->get_user_data($user_id);
		$cmp_user = $this->CI->model_user->get_user_data($intro_id);

		$this->CI->general->set_table('filter');
		$filters = $this->CI->general->get(array('*'),array('language_id'=>1),array('view_order'=>'asc'));
		if($filters)
		{
			foreach ($filters as $filter)
			{
				switch ($filter['id_to_map'])
				{

					case "age":
						if($user['want_age_range_upper'] && $user['want_age_range_lower'])
						{
							$tmp_obtain_score  = 0;
							$tmp_total_score = 0;

							$tmp_total_score++;
							$tmp_total_score = $this->multiply_importance($tmp_total_score, $user['want_age_range_importance']);


							//Calculate Birthday
							$cmp_user_age = 0;
							$birthDate = $cmp_user['birth_date'];
							if($birthDate != '0000-00-00')
							{
								//explode the date to get month, day and year
								$birthDate = explode("-", $birthDate);
								//get age from date or birthdate
								$cmp_user_age = (date("md", date("U", mktime(0, 0, 0, $birthDate[0], $birthDate[1], $birthDate[2]))) > date("md") ? ((date("Y")-$birthDate[0])-1):(date("Y")-$birthDate[0]));
							}

							if($cmp_user_age <= $user['want_age_range_upper'] && $cmp_user_age >= $user['want_age_range_lower'])
							{
								$match[] = $filter['description'];
								$tmp_obtain_score ++;
								$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $user['want_age_range_importance']);
							}
							else
							{
								$slightly_diff = '-2';
								$tmp_score = $cmp_user_age - $slightly_diff;
								$new_tmp_score = $cmp_user_age + $slightly_diff;
								if(($tmp_score <= $user['want_age_range_upper'] && $tmp_score >= $user['want_age_range_lower']) || ($new_tmp_score <= $user['want_age_range_upper'] && $new_tmp_score >= $user['want_age_range_lower']))
								{
									$slightly[] = $filter['description'];
									$tmp_obtain_score = $tmp_obtain_score + 0.5;
									$tmp_obtain_score = $this->multiply_importance($tmp_obtain_score , $user['want_age_range_importance']);
								}
								else
								{
									$not_matched[] = $filter['description'];
									//*michael* $tmp_obtain_score --;
									$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $user['want_age_range_importance']);
								}
							}

							$obtain_score += $tmp_obtain_score ;
							$total_score += $tmp_total_score;
						}
						break;
							
					case "ethnicity":
						$crite_area = "ethnicity";
						$user_want_crite_area = $this->user_want($user['user_id'],$crite_area);

						$tmp_obtain_score  = 0;
						$tmp_total_score = 0;
						if($user_want_crite_area)
						{
							//if user required
							$tmp_total_score++;
							$tmp_total_score =  $this->multiply_importance($tmp_total_score, $user['want_'.$crite_area.'_importance']);

							if($cmp_user[$crite_area.'_id'])
							{
								$is_found = false;
								foreach ($user_want_crite_area  as $key=>$value)
								{
									if($value[$crite_area.'_id'] == $cmp_user[$crite_area.'_id'] )
									{
										$is_found = true;
									}
								}

								if($is_found)
								{
									$match[] = $filter['description'];
									$tmp_obtain_score ++;
									$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $user['want_'.$crite_area.'_importance']);

								}
								else
								{
									$not_matched[] = $filter['description'];
									//*michael* $tmp_obtain_score --;
									$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $user['want_'.$crite_area.'_importance']);

									//$slightly[] = $crite_area;
								}
							}

							//echo "User Want:: <pre>";print_r($user_want_crite_area);
							//echo 'user importance :'.$user['want_'.$crite_area.'_importance'].'<br/>';
							//echo "Intro Have:: ";print_r($cmp_user[$crite_area.'_id']);
							//echo '<hr/>';
							//echo '<br/> Score Before ='.$obtain_score.'/'.$total_score.'<br/>';

							$obtain_score += $tmp_obtain_score ;
							$total_score += $tmp_total_score;

							//echo '<br/> Score After ='.$obtain_score.'/'.$total_score.'<br/>';
						}
						break;
					case "relationshipGoal":
						$crite_area = "relationship_type";
						$user_want_descirptive_crite_area = $this->user_want($user['user_id'],$crite_area);
						$tmp_obtain_score  = 0;
						$tmp_total_score = 0;
						if($user_want_descirptive_crite_area)
						{
							$tmp_total_score++;

							$want_field = $user['want_looking_for_importance'];
							$cmp_user_descirptive_crite_area = $this->CI->model_user->multijoins("usr.*,prsnality.*",
					"user_want_".$crite_area." as usr",array($crite_area.' as prsnality'=>array('usr.'.$crite_area.'_id = prsnality.'.$crite_area.'_id ','INNER')),
							array('prsnality.display_language_id'=>$this->language_id,'usr.user_id'=>$intro_id));

							$tmp_total_score =  $this->multiply_importance($tmp_total_score, $want_field);

							if($cmp_user_descirptive_crite_area)
							{
								$is_found = false;
								$descirptive_crite_area_total_match = 0;

								foreach ($cmp_user_descirptive_crite_area as $key=>$value)
								{
									foreach ($user_want_descirptive_crite_area as $descirptive_crite_area)
									{
										if($value[$crite_area.'_id'] == $descirptive_crite_area[$crite_area.'_id'])
										{
											$descirptive_crite_area_total_match++;
											$is_found = true;
										}
									}
								}

								if($is_found)
								{
									$match[] = $filter['description'];
									$tmp_obtain_score ++;
									$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $want_field);
								}
								else
								{
									//*michael* $tmp_obtain_score --;
									$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $want_field);
									$not_matched[] = $filter['description'];
									//$slightly[] = $crite_area;
								}
							}
							else
							{
								//*michael* $tmp_obtain_score --;
								$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $want_field);
								$not_matched[] = $filter['description'];
							}

							$obtain_score += $tmp_obtain_score ;
							$total_score += $tmp_total_score;
						}
						break;
					case "common_interest":
						$want_field = $user['want_common_interest_importance'];
						$this->CI->general->set_table('user_interest');
						$interestJoins = array('user_interest'=>array('interest.interest_id = user_interest.interest_id','inner'));
						$interestCondition = array('interest.display_language_id'=>$this->language_id,'user_interest.user_id'=>$user_id);
						$user_interests = $this->CI->general->multijoins_arr('interest.* ','interest',$interestJoins,$interestCondition,'','interest.view_order asc');

						if($want_field && $user_interests)
						{
							$tmp_total_score++;
							$tmp_total_score =  $this->multiply_importance($tmp_total_score, $want_field);

							$this->CI->general->set_table('user_interest');
							$interestJoins = array('user_interest'=>array('interest.interest_id = user_interest.interest_id','inner'));
							$interestCondition = array('interest.display_language_id'=>$this->language_id,'user_interest.user_id'=>$intro_id);
							$intro_interests = $this->CI->general->multijoins_arr('interest.* ','interest',$interestJoins,$interestCondition,'','interest.view_order asc');

							if($intro_interests)
							{
								$is_found = false;
								$crite_area_total_match = 0;

								foreach ($user_interests as $key=>$value)
								{
									foreach ( $intro_interests as $descirptive_crite_area)
									{
										if($value['interest_id'] == $descirptive_crite_area['interest_id'])
										{
											$crite_area_total_match++;
											$is_found = true;
										}
									}
								}

								if($is_found)
								{
									$match[] = $filter['description'];
									$tmp_obtain_score ++;
									$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $want_field);
								}
								else
								{
									//*michael* $tmp_obtain_score --;
									$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $want_field);
									$not_matched[] = $filter['description'];
									//$slightly[] = $crite_area;
								}
							}
							else
							{
								//*michael* $tmp_obtain_score --;
								$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $want_field);
								$not_matched[] = $filter['description'];
							}

							$obtain_score += $tmp_obtain_score ;
							$total_score += $tmp_total_score;
						}
						break;
							
					case "looks":
						//For Look Range
						if($user['want_looks_range_higher_id'] && $user['want_looks_range_lower_id'])
						{
							$tmp_obtain_score  = 0;
							$tmp_total_score = 0;

							$tmp_total_score++;
							$tmp_total_score =  $this->multiply_importance($tmp_total_score, $user['want_looks_importance']);

							//database records so reverse condition in auto increment id
							if($cmp_user['looks_id'] >= $user['want_looks_range_higher_id'] && $cmp_user['looks_id'] <= $user['want_looks_range_lower_id'])
							{
								$match[] = $filter['description'];
								$tmp_obtain_score ++;
								$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $user['want_looks_importance']);
									
							}
							else
							{
								$slightly_diff = '-1';
								$tmp_score = $cmp_user['looks_id'] - $slightly_diff;
								$new_tmp_score = $cmp_user['looks_id'] + $slightly_diff;

								if(($tmp_score  >= $user['want_looks_range_higher_id'] && $tmp_score  <= $user['want_looks_range_lower_id']) || $new_tmp_score  >= $user['want_looks_range_higher_id'] && $new_tmp_score  <= $user['want_looks_range_lower_id'])
								{
									$slightly[] = $filter['description'];
									$tmp_obtain_score = $tmp_obtain_score + 0.5;
									$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $user['want_looks_importance']);
								}
								else
								{
									$not_matched[] = $filter['description'];
									//*michael* $tmp_obtain_score --;
									$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $user['want_looks_importance']);
								}
							}
							$obtain_score += $tmp_obtain_score ;
							$total_score += $tmp_total_score;
						}
						break;
							
					case "height":
						//For Height
						if($user['want_height_range_upper'] && $user['want_height_range_lower'])
						{
							$tmp_obtain_score  = 0;
							$tmp_total_score = 0;

							$tmp_total_score++;
							$tmp_total_score =  $this->multiply_importance($tmp_total_score, $user['want_height_range_importance']);


							if($cmp_user['height'] <= $user['want_height_range_upper'] && $cmp_user['height'] >= $user['want_height_range_lower'])
							{
								$match[] = $filter['description'];
								$tmp_obtain_score ++;
								$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $user['want_height_range_importance']);
							}
							else
							{
								$slightly_diff = '-5';
								$tmp_score = $cmp_user['height'] - $slightly_diff;
								$new_tmp_score = $cmp_user['height'] + $slightly_diff;

								if(($tmp_score  <= $user['want_height_range_upper'] && $tmp_score  >= $user['want_height_range_lower']) || ($new_tmp_score    <= $user['want_height_range_upper'] && $new_tmp_score  >= $user['want_height_range_lower']))
								{
									$slightly[] = $filter['description'];
									$tmp_obtain_score = $tmp_obtain_score + 0.5;
									$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $user['want_height_range_importance']);
								}
								else
								{
									$not_matched[] = $filter['description'];
									//*michael* $tmp_obtain_score --;
									$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $user['want_height_range_importance']);
								}
							}

							$obtain_score += $tmp_obtain_score ;
							$total_score += $tmp_total_score;
						}
						break;
							
					case "bodyType":
						$crite_area = "body_type";
						$user_want_crite_area = $this->user_want($user['user_id'],$crite_area);

						$tmp_obtain_score  = 0;
						$tmp_total_score = 0;
						if($user_want_crite_area)
						{
							//if user required
							$tmp_total_score++;
							$tmp_total_score =  $this->multiply_importance($tmp_total_score, $user['want_'.$crite_area.'_importance']);

							if($cmp_user[$crite_area.'_id'])
							{
								$is_found = false;
								foreach ($user_want_crite_area  as $key=>$value)
								{
									if($value[$crite_area.'_id'] == $cmp_user[$crite_area.'_id'] )
									{
										$is_found = true;
									}
								}

								if($is_found)
								{
									$match[] = $filter['description'];
									$tmp_obtain_score ++;
									$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $user['want_'.$crite_area.'_importance']);

								}
								else
								{
									$not_matched[] = $filter['description'];
									//*michael* $tmp_obtain_score --;
									$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $user['want_'.$crite_area.'_importance']);

									//$slightly[] = $crite_area;
								}
							}

							$obtain_score += $tmp_obtain_score ;
							$total_score += $tmp_total_score;
						}
						break;
					case "personality":
						$crite_area = "descriptive_word";
						$user_want_descirptive_crite_area = $this->user_want($user['user_id'],$crite_area);
						$tmp_obtain_score  = 0;
						$tmp_total_score = 0;
						if($user_want_descirptive_crite_area)
						{
							$tmp_total_score++;

							$want_field = $user['want_personality_importance'];
							$cmp_user_descirptive_crite_area = $this->CI->model_user->multijoins("usr.*,prsnality.*",
					"user_".$crite_area." as usr",array($crite_area.' as prsnality'=>array('usr.'.$crite_area.'_id = prsnality.'.$crite_area.'_id ','INNER')),
							array('prsnality.display_language_id'=>$this->language_id,'usr.user_id'=>$intro_id));

							$tmp_total_score =  $this->multiply_importance($tmp_total_score, $want_field);

							if($cmp_user_descirptive_crite_area)
							{
								$is_found = false;
								$descirptive_crite_area_total_match = 0;

								foreach ($cmp_user_descirptive_crite_area as $key=>$value)
								{
									foreach ($user_want_descirptive_crite_area as $descirptive_crite_area)
									{
										if($value[$crite_area.'_id'] == $descirptive_crite_area[$crite_area.'_id'])
										{
											$descirptive_crite_area_total_match++;
											$is_found = true;
										}
									}
								}

								if($is_found)
								{
									$match[] = $filter['description'];
									$tmp_obtain_score ++;
									$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $want_field);
								}
								else
								{
									//*michael* $tmp_obtain_score --;
									$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $want_field);
									$not_matched[] = $filter['description'];
									//$slightly[] = $crite_area;
								}
							}
							else
							{
								//*michael* $tmp_obtain_score --;
								$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $want_field);
								$not_matched[] = $filter['description'];
							}

							$obtain_score += $tmp_obtain_score ;
							$total_score += $tmp_total_score;
						}
						break;
							
					case "school_major":

						$user_want_school_subject = $this->user_want($user['user_id'],'school_subject');
						if($user_want_school_subject)
						{
							$this->CI->db->group_by("sc_sub.school_subject_id");
							$cmp_user_school_subject = $this->CI->model_user->multijoins("sc_sub.*,us.*","user_school as us",array('user_school_major as sub_mjr'=>array('us.user_school_id = sub_mjr.user_school_id','INNER'),'school_subject as sc_sub'=>array('sc_sub.school_subject_id = sub_mjr.major_id ','INNER')),array('sc_sub.display_language_id'=>$this->language_id,'us.user_id'=>$intro_id));

							$tmp_obtain_score  = 0;
							$tmp_total_score = 0;

							$tmp_total_score++;
							$tmp_total_score =  $this->multiply_importance($tmp_total_score, $user['want_school_subject_importance']);

							if($cmp_user_school_subject)
							{
								$is_found = false;
								$descirptive_crite_area_total_match = 0;

								foreach ($cmp_user_school_subject as $key=>$value)
								{
									foreach ($user_want_school_subject as $descirptive_crite_area)
									{
										if($value['school_subject_id'] == $descirptive_crite_area['school_subject_id'])
										{
											$descirptive_crite_area_total_match++;
											$is_found = true;
										}
									}
								}

								if($is_found)
								{
									$match[] = $filter['description'];
									$tmp_obtain_score ++;
									$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $user['want_school_subject_importance']);

								}
								else
								{
									//*michael* $tmp_obtain_score --;
									$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $user['want_school_subject_importance']);

									$not_matched[] = $filter['description'];
									//$slightly[] = $crite_area;
								}

							}
							else
							{
								//*michael* $tmp_obtain_score --;
								$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $user['want_school_subject_importance']);

								$not_matched[] = $filter['description'];
							}
							$obtain_score += $tmp_obtain_score ;
							$total_score += $tmp_total_score;
						}
						break;
							
					case "education_level":
							
						$crite_area = "education_level";
						$user_want_descirptive_crite_area = $this->user_want($user['user_id'],$crite_area);
						$tmp_obtain_score  = 0;
						$tmp_total_score = 0;
						if($user_want_descirptive_crite_area)
						{
							$tmp_total_score++;

							$want_field = $user['want_'.$crite_area.'_importance'];

							$cmp_user_descirptive_crite_area = $this->CI->model_user->multijoins("usr.*,prsnality.*",
					"user_".$crite_area." as usr",array($crite_area.' as prsnality'=>array('usr.'.$crite_area.'_id = prsnality.'.$crite_area.'_id ','INNER')),
							array('prsnality.display_language_id'=>$this->language_id,'usr.user_id'=>$intro_id));

							$tmp_total_score =  $this->multiply_importance($tmp_total_score, $want_field);

							if($cmp_user_descirptive_crite_area)
							{
								$is_found = false;
								$descirptive_crite_area_total_match = 0;

								foreach ($cmp_user_descirptive_crite_area as $key=>$value)
								{
									foreach ($user_want_descirptive_crite_area as $descirptive_crite_area)
									{
										if($value[$crite_area.'_id'] == $descirptive_crite_area[$crite_area.'_id'])
										{
											$descirptive_crite_area_total_match++;
											$is_found = true;
										}
									}
								}

								if($is_found)
								{
									$match[] = $filter['description'];
									$tmp_obtain_score ++;
									$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $want_field);
								}
								else
								{
									//*michael* $tmp_obtain_score --;
									$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $want_field);
									$not_matched[] = $filter['description'];
									//$slightly[] = $crite_area;
								}
							}
							else
							{
								//*michael* $tmp_obtain_score --;
								$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $want_field);
								$not_matched[] = $filter['description'];
							}

							$obtain_score += $tmp_obtain_score ;
							$total_score += $tmp_total_score;
						}
						break;
					case "job_function":
						$user_want_job_function = $this->user_want($user['user_id'],'job_function');
						if($user_want_job_function)
						{
							$tmp_obtain_score  = 0;
							$tmp_total_score = 0;

							$tmp_total_score++;
							$tmp_total_score =  $this->multiply_importance($tmp_total_score, $user['want_job_function_importance']);


							$this->CI->general->set_table('user_job');
							$job_function['user_id'] = $intro_id;
							$job_function['job_function_id != '] = '';

							$cmp_user_job= $this->CI->general->get('job_function_id',$job_function);
							unset($job_function);

							if($cmp_user_job)
							{
								$is_found = false;
								$descirptive_crite_area_total_match = 0;

								foreach ($cmp_user_job as $key=>$value)
								{
									foreach ($user_want_job_function as $descirptive_crite_area)
									{
										if($value['job_function_id'] == $descirptive_crite_area['job_function_id'])
										{
											$descirptive_crite_area_total_match++;
											$is_found = true;
										}
									}
								}

								if($is_found)
								{
									$match[] = $filter['description'];
									$tmp_obtain_score ++;
									$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $user['want_job_function_importance']);

								}
								else
								{
									//*michael* $tmp_obtain_score --;
									$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $user['want_job_function_importance']);

									$not_matched[] = $filter['description'];
									//$slightly[] = $crite_area;
								}
							}
							else
							{
								//*michael* $tmp_obtain_score --;
								$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $user['want_job_function_importance']);
								$not_matched[] = $filter['description'];
								//$slightly[] = $crite_area;
							}
							$obtain_score += $tmp_obtain_score ;
							$total_score += $tmp_total_score;

						}
						break;
					case "income_level":
						if($user['want_annual_income'])
						{
							$tmp_obtain_score  = 0;
							$tmp_total_score = 0;

							$tmp_total_score++;
							$tmp_total_score =  $this->multiply_importance($tmp_total_score, $user['want_annual_income_importance']);
							$income_range_temp = $this->CI->model_user->multijoins(array('user.user_id','currency.description as currency_desc','currency.*','want_income.*'),'annual_income_range as want_income',
							array(
								'user'=>array('user.annual_income_range_id = want_income.annual_income_range_id','inner'),
								'country'=>array('country.country_id = want_income.country_id','inner'),
								'currency'=>array('currency.currency_id=country.currency_id','inner')
							),
							array(
								'want_income.display_language_id'=>$this->language_id, 
								'country.display_language_id'=>$this->language_id,
								'currency.display_language_id'=>$this->language_id,
								'user_id'=>$intro_id)
							);

							if($income_range_temp)
							{
								$income_range_temp = $income_range_temp['0'];

								if(strrpos($income_range_temp['description'],'-') !== false)
								{
									$tmp = explode('-',$income_range_temp['description']);
									$lower_limit = preg_replace('/[^\d]/','',$tmp[0]);
									$higher_limit = preg_replace('/[^\d]/','',$tmp[1]);
								}
								else if(strrpos($income_range_temp['description'],'<') !== false)
								{
									$tmp = explode('<',$income_range_temp['description']);
									$lower_limit = 0;
									$higher_limit = preg_replace('/[^\d]/','',$tmp[1]);
								}
								else if(strrpos($income_range_temp['description'],'>') !== false)
								{
									$tmp = explode('>',$income_range_temp['description']);
									$lower_limit = preg_replace('/[^\d]/','',$tmp[1]);
									$higher_limit = 999999999999;
								}
								if($user['want_annual_income_currency_id'] != $income_range_temp['currency_id'])
								{
									$lower_limit /= $income_range_temp['rate'];
									$higher_limit /= $income_range_temp['rate'] ;

									$this->CI->general->set_table('currency');
									if($want_currency_data = $this->CI->general->get("",array('display_language_id'=>$this->language_id,'currency_id'=>$user['want_annual_income_currency_id'])))
									{
										$user['want_annual_income'] /= $want_currency_data['0']['rate'];
									}
								}

								if($user['want_annual_income'] <= $higher_limit && $lower_limit > 0)
								//if( ($user['want_annual_income'] <= $higher_limit || $higher_limit == '') && ($user['want_annual_income'] >= $lower_limit && $lower_limit != ''))
								{
									$match[] = $filter['description'];
									$tmp_obtain_score ++;
									$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $user['want_annual_income_importance']);
								}
								elseif($user['want_annual_income'] <= $higher_limit * 1.2 && $lower_limit > 0)
								{
									$slightly[] = $filter['description'];
									$tmp_obtain_score = $tmp_obtain_score + 0.5;
									$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $user['want_annual_income_importance']);
								}
								else
								{
									$not_matched[] = $filter['description'];
									//*michael* $tmp_obtain_score --;
									$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $user['want_annual_income_importance']);
								}
							}
							else
							{
								//*michael* $tmp_obtain_score --;
								$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $user['want_annual_income_importance']);
								$not_matched[] = $filter['description'];
							}

							$obtain_score += $tmp_obtain_score ;
							$total_score += $tmp_total_score;
						}

						break;
					case "job_industry":
						$user_want_industry = $this->user_want($user['user_id'],'industry');
						if($user_want_industry)
						{
							$this->CI->general->set_table('user_job');
							$industy_cond['user_id'] = $intro_id;
							$cmp_user_industry = $this->CI->general->get('industry_id',$industy_cond);
							unset($industy_cond);

							$tmp_obtain_score  = 0;
							$tmp_total_score = 0;

							$tmp_total_score++;
							$tmp_total_score =  $this->multiply_importance($tmp_total_score, $user['want_industry_importance']);

							if($cmp_user_industry)
							{
								$is_found = false;
								$descirptive_crite_area_total_match = 0;

								foreach ($cmp_user_industry as $key=>$value)
								{
									foreach ($user_want_industry as $descirptive_crite_area)
									{
										if($value['industry_id'] == $descirptive_crite_area['industry_id'])
										{
											$descirptive_crite_area_total_match++;
											$is_found = true;
										}
									}
								}

								if($is_found)
								{
									$match[] = $filter['description'];
									$tmp_obtain_score ++;
									$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $user['want_industry_importance']);

								}
								else
								{
									//*michael* $tmp_obtain_score --;
									$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $user['want_industry_importance']);

									$not_matched[] = $filter['description'];
									//$slightly[] = $crite_area;
								}
							}
							else
							{
								//*michael* $tmp_obtain_score --;
								$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $user['want_industry_importance']);

								$not_matched[] = $filter['description'];
								//$slightly[] = $crite_area;
							}
							$obtain_score += $tmp_obtain_score ;
							$total_score += $tmp_total_score;
						}

						break;
					case "career_stage":
						$crite_area = "career_stage";
						$user_want_crite_area = $this->user_want($user['user_id'],$crite_area);

						$tmp_obtain_score  = 0;
						$tmp_total_score = 0;
						if($user_want_crite_area)
						{
							//if user required
							$tmp_total_score++;
							$tmp_total_score =  $this->multiply_importance($tmp_total_score, $user['want_'.$crite_area.'_importance']);

							if($cmp_user[$crite_area.'_id'])
							{
								$is_found = false;
								foreach ($user_want_crite_area  as $key=>$value)
								{
									if($value[$crite_area.'_id'] == $cmp_user[$crite_area.'_id'] )
									{
										$is_found = true;
									}
								}

								if($is_found)
								{
									$match[] = $filter['description'];
									$tmp_obtain_score ++;
									$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $user['want_'.$crite_area.'_importance']);

								}
								else
								{
									$not_matched[] = $filter['description'];
									//*michael* $tmp_obtain_score --;
									$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $user['want_'.$crite_area.'_importance']);

									//$slightly[] = $crite_area;
								}
							}

							$obtain_score += $tmp_obtain_score ;
							$total_score += $tmp_total_score;
						}
						break;
							
					case "relationshipStatus":
						$crite_area = "relationship_status";
						$user_want_crite_area = $this->user_want($user['user_id'],$crite_area);

						$tmp_obtain_score  = 0;
						$tmp_total_score = 0;
						if($user_want_crite_area)
						{
							//if user required
							$tmp_total_score++;
							$tmp_total_score =  $this->multiply_importance($tmp_total_score, $user['want_'.$crite_area.'_importance']);

							if($cmp_user[$crite_area.'_id'])
							{
								$is_found = false;
								foreach ($user_want_crite_area  as $key=>$value)
								{
									if($value[$crite_area.'_id'] == $cmp_user[$crite_area.'_id'] )
									{
										$is_found = true;
									}
								}

								if($is_found)
								{
									$match[] = $filter['description'];
									$tmp_obtain_score ++;
									$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $user['want_'.$crite_area.'_importance']);

								}
								else
								{
									$not_matched[] = $filter['description'];
									//*michael* $tmp_obtain_score --;
									$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $user['want_'.$crite_area.'_importance']);

									//$slightly[] = $crite_area;
								}
							}

							$obtain_score += $tmp_obtain_score ;
							$total_score += $tmp_total_score;
						}
						break;
					case "religion":
						$crite_area = "religious_belief";
						$user_want_crite_area = $this->user_want($user['user_id'],$crite_area);

						$tmp_obtain_score  = 0;
						$tmp_total_score = 0;
						if($user_want_crite_area)
						{
							//if user required
							$tmp_total_score++;
							$tmp_total_score =  $this->multiply_importance($tmp_total_score, $user['want_'.$crite_area.'_importance']);

							if($cmp_user[$crite_area.'_id'])
							{
								$is_found = false;
								foreach ($user_want_crite_area  as $key=>$value)
								{
									if($value[$crite_area.'_id'] == $cmp_user[$crite_area.'_id'] )
									{
										$is_found = true;
									}
								}

								if($is_found)
								{
									$match[] = $filter['description'];
									$tmp_obtain_score ++;
									$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $user['want_'.$crite_area.'_importance']);

								}
								else
								{
									$not_matched[] = $filter['description'];
									//*michael* $tmp_obtain_score --;
									$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $user['want_'.$crite_area.'_importance']);

									//$slightly[] = $crite_area;
								}
							}

							$obtain_score += $tmp_obtain_score ;
							$total_score += $tmp_total_score;
						}
						break;
							
					case "smokingStatus":
						$crite_area = "smoking_status";
						$user_want_crite_area = $this->user_want($user['user_id'],$crite_area);

						$tmp_obtain_score  = 0;
						$tmp_total_score = 0;
						if($user_want_crite_area)
						{
							//if user required
							$tmp_total_score++;
							$tmp_total_score =  $this->multiply_importance($tmp_total_score, $user['want_'.$crite_area.'_importance']);

							if($cmp_user[$crite_area.'_id'])
							{
								$is_found = false;
								foreach ($user_want_crite_area  as $key=>$value)
								{
									if($value[$crite_area.'_id'] == $cmp_user[$crite_area.'_id'] )
									{
										$is_found = true;
									}
								}

								if($is_found)
								{
									$match[] = $filter['description'];
									$tmp_obtain_score ++;
									$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $user['want_'.$crite_area.'_importance']);

								}
								else
								{
									$not_matched[] = $filter['description'];
									//*michael* $tmp_obtain_score --;
									$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $user['want_'.$crite_area.'_importance']);

									//$slightly[] = $crite_area;
								}
							}

							$obtain_score += $tmp_obtain_score ;
							$total_score += $tmp_total_score;
						}
						break;
					case "drinkingStatus":
						$crite_area = "drinking_status";
						$user_want_crite_area = $this->user_want($user['user_id'],$crite_area);

						$tmp_obtain_score  = 0;
						$tmp_total_score = 0;
						if($user_want_crite_area)
						{
							//if user required
							$tmp_total_score++;
							$tmp_total_score =  $this->multiply_importance($tmp_total_score, $user['want_'.$crite_area.'_importance']);

							if($cmp_user[$crite_area.'_id'])
							{
								$is_found = false;
								foreach ($user_want_crite_area  as $key=>$value)
								{
									if($value[$crite_area.'_id'] == $cmp_user[$crite_area.'_id'] )
									{
										$is_found = true;
									}
								}

								if($is_found)
								{
									$match[] = $filter['description'];
									$tmp_obtain_score ++;
									$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $user['want_'.$crite_area.'_importance']);

								}
								else
								{
									$not_matched[] = $filter['description'];
									//*michael* $tmp_obtain_score --;
									$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $user['want_'.$crite_area.'_importance']);

									//$slightly[] = $crite_area;
								}
							}

							$obtain_score += $tmp_obtain_score ;
							$total_score += $tmp_total_score;
						}
						break;
							
					case "exerciseStatus":
						$crite_area = "exercise_frequency";
						$user_want_crite_area = $this->user_want($user['user_id'],$crite_area);

						$tmp_obtain_score  = 0;
						$tmp_total_score = 0;
						if($user_want_crite_area)
						{
							//if user required
							$tmp_total_score++;
							$tmp_total_score =  $this->multiply_importance($tmp_total_score, $user['want_'.$crite_area.'_importance']);

							if($cmp_user[$crite_area.'_id'])
							{
								$is_found = false;
								foreach ($user_want_crite_area  as $key=>$value)
								{
									if($value[$crite_area.'_id'] == $cmp_user[$crite_area.'_id'] )
									{
										$is_found = true;
									}
								}

								if($is_found)
								{
									$match[] = $filter['description'];
									$tmp_obtain_score ++;
									$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $user['want_'.$crite_area.'_importance']);

								}
								else
								{
									$not_matched[] = $filter['description'];
									//*michael* $tmp_obtain_score --;
									$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $user['want_'.$crite_area.'_importance']);

									//$slightly[] = $crite_area;
								}
							}

							$obtain_score += $tmp_obtain_score ;
							$total_score += $tmp_total_score;
						}
						break;
					case "childPlans":
						$crite_area = "child_plan";
						$user_want_crite_area = $this->user_want($user['user_id'],$crite_area);

						$tmp_obtain_score  = 0;
						$tmp_total_score = 0;
						if($user_want_crite_area)
						{
							//if user required
							$tmp_total_score++;
							$tmp_total_score =  $this->multiply_importance($tmp_total_score, $user['want_'.$crite_area.'_importance']);

							if($cmp_user[$crite_area.'_id'])
							{
								$is_found = false;
								foreach ($user_want_crite_area  as $key=>$value)
								{
									if($value[$crite_area.'_id'] == $cmp_user[$crite_area.'_id'] )
									{
										$is_found = true;
									}
								}

								if($is_found)
								{
									$match[] = $filter['description'];
									$tmp_obtain_score ++;
									$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $user['want_'.$crite_area.'_importance']);

								}
								else
								{
									$not_matched[] = $filter['description'];
									//*michael* $tmp_obtain_score --;
									$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $user['want_'.$crite_area.'_importance']);

									//$slightly[] = $crite_area;
								}
							}

							$obtain_score += $tmp_obtain_score ;
							$total_score += $tmp_total_score;
						}
						break;
					case "residenceType":
						//resident type hasn't a proper formate like residence_type_id so need to separate query.
						$user_want_resident_type = $this->user_want($user['user_id'],'residence_type');
						if($user_want_resident_type)
						{
							$tmp_obtain_score  = 0;
							$tmp_total_score = 0;

							$tmp_total_score++;
							$tmp_total_score =  $this->multiply_importance($tmp_total_score, $user['want_residence_type_importance']);

							$is_found = false;
							foreach ($user_want_resident_type  as $key=>$value)
							{
								if($value['residence_type_id'] == $cmp_user['residence_type'] )
								{
									$is_found = true;
								}
							}

							if($is_found)
							{
								$match[] = $filter['description'];
								$tmp_obtain_score ++;
								$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $user['want_residence_type_importance']);
							}
							else
							{
								$not_matched[] = $filter['description'];
								//*michael* $tmp_obtain_score --;
								$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $user['want_residence_type_importance']);

								//$slightly[] = "residence_type";
							}
							$obtain_score += $tmp_obtain_score ;
							$total_score += $tmp_total_score;
						}
						break;
					case "existing_children":
						$comp_crite_area = array('child_status');
						//residence_type, personality, education_level,school_subject, industry
						foreach ($comp_crite_area as $crite_area)
						{
							//relationship_status
							$user_want_crite_area = $this->user_want($user['user_id'],$crite_area);

							$tmp_obtain_score  = 0;
							$tmp_total_score = 0;
							if($user_want_crite_area)
							{
								//if user required
								$tmp_total_score++;
								$tmp_total_score =  $this->multiply_importance($tmp_total_score, $user['want_'.$crite_area.'_importance']);

								if($cmp_user[$crite_area.'_id'])
								{
									$is_found = false;
									foreach ($user_want_crite_area  as $key=>$value)
									{
										if($value[$crite_area.'_id'] == $cmp_user[$crite_area.'_id'] )
										{
											$is_found = true;
										}
									}

									if($is_found)
									{
										$match[] = $filter['description'];
										$tmp_obtain_score ++;
										$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $user['want_'.$crite_area.'_importance']);
											
									}
									else
									{
										$not_matched[] = $filter['description'];
										//*michael* $tmp_obtain_score --;
										$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $user['want_'.$crite_area.'_importance']);
											
										//$slightly[] = $crite_area;
									}
								}

								$obtain_score += $tmp_obtain_score ;
								$total_score += $tmp_total_score;
							}
						}
						break;
					case 'school_name':

						$crite_area = 'school';
						$user_want_crite_area = $this->user_want($user['user_id'],'school');
						$tmp_obtain_score  = 0;
						$tmp_total_score = 0;
						if($user_want_crite_area)
						{

							//if user required
							$tmp_total_score++;
							$tmp_total_score =  $this->multiply_importance($tmp_total_score, $user['want_'.$crite_area.'_importance']);
							$fields = array('sc.school_id','sc.school_name','sc.logo_url','ct.*');
							$from = "user_school as uc";
							$joins = array(
														'school as sc'=>array('uc.school_id = sc.school_id','INNER'),
														'city as ct'=>array('ct.city_id = sc.city_id','INNER')
							);
							unset($condition);
							$condition['uc.user_id'] = $intro_id;
							$condition['sc.display_language_id'] = $this->language_id;
							$condition['ct.display_language_id'] = $this->language_id;
							$ordersby = 'sc.school_id asc';

							$user_school_datas = $this->CI->general->multijoins($fields,$from,$joins,$condition,$ordersby,'array');
							if($user_school_datas)
							{
								$is_found = false;
								$crite_area_total_match = 0;

								foreach ($user_school_datas as $key=>$value)
								{
									foreach ($user_want_crite_area as $crite_area_val)
									{
										if($value[$crite_area.'_id'] == $crite_area_val[$crite_area.'_id'])
										{
											$crite_area_total_match ++;
											$is_found = true;
										}
									}
								}

								if($is_found)
								{
									$match[] = $filter['description'];
									$tmp_obtain_score ++;
									$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $user['want_'.$crite_area.'_importance']);
								}
								else
								{
									//*michael* $tmp_obtain_score --;
									$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $user['want_'.$crite_area.'_importance']);
									$not_matched[] = $filter['description'];
									//$slightly[] = $crite_area;
								}
							}
							else
							{
								//*michael* $tmp_obtain_score --;
								$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $user['want_'.$crite_area.'_importance']);
								$not_matched[] = $filter['description'];
								//$slightly[] = $crite_area;
							}

							$obtain_score += $tmp_obtain_score ;
							$total_score += $tmp_total_score;
							//echo $tmp_obtain_score.' / '.$tmp_total_score;exit;
						}

						break;
					case 'company_name':

						$crite_area = 'company';
						$selected_company = $this->user_want($user['user_id'],'company');
						$this->CI->general->set_table('user_want_company');
						$user_custom_company_required = $this->CI->general->get("user_want_company_id, user_id,CONCAT('_', `company_name`,'_') as company_id, company_name",array('user_id'=>$user_id,'company_name !='=>''));
						$user_want_crite_area = array_merge($selected_company,$user_custom_company_required);

						$tmp_obtain_score  = 0;
						$tmp_total_score = 0;
						if($user_want_crite_area)
						{
							//if user required
							$tmp_total_score++;
							$tmp_total_score =  $this->multiply_importance($tmp_total_score, $user['want_'.$crite_area.'_importance']);

							$this->CI->general->set_table('user_job');
							$companyCondition = array('user_id'=>$intro_id);
							$companyFields = array('user_company_id','company_id','company_name','years_worked_start','years_worked_end');
							$companyOrderBy   = array('years_worked_end'=>'desc','years_worked_start'=>'desc','company_name'=>'asc');
							$company = $this->CI->general->get($companyFields,$companyCondition,$companyOrderBy);
							if($company)
							{
								$this->CI->general->set_table('company');
								foreach ($company as $r=>$cmp_val)
								{
									if($cmp_val['company_id'])
									{
										$company_name = $this->CI->general->get("",array('display_language_id'=>$this->language_id,'company_id'=>$cmp_val['company_id']));
										$company[$r]['company_name'] = $company_name['0']['company_name'];
									}
								}

								$is_found = false;
								$crite_area_total_match = 0;

								foreach ($company as $key=>$value)
								{
									foreach ($user_want_crite_area as $crite_area_val)
									{
										if($value[$crite_area.'_id'] == $crite_area_val[$crite_area.'_id'] || $value[$crite_area.'_name'] == $crite_area_val[$crite_area.'_name'])
										{
											$crite_area_total_match ++;
											$is_found = true;
										}
									}
								}
								if($is_found)
								{
									$match[] = $filter['description'];
									$tmp_obtain_score ++;
									$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $user['want_'.$crite_area.'_importance']);
								}
								else
								{
									//*michael* $tmp_obtain_score --;
									$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $user['want_'.$crite_area.'_importance']);
									$not_matched[] = $filter['description'];
									//$slightly[] = $crite_area;
								}
							}
							else
							{
								//*michael* $tmp_obtain_score --;
								$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $user['want_'.$crite_area.'_importance']);
								$not_matched[] = $filter['description'];
								//$slightly[] = $crite_area;
							}
							$obtain_score += $tmp_obtain_score ;
							$total_score += $tmp_total_score;
							//echo $tmp_obtain_score.' / '.$tmp_total_score;exit;
						}

						break;
					default:
						break;
				}
			}
		}

		if($obtain_score > 0 && $total_score > 0)
		return array('score'=>round((($obtain_score/$total_score)*100)),'match_data'=>$match,'not_matched'=>$not_matched)  ;
		else
		return array('score'=>0,'match_data'=>$match,'not_matched'=>$not_matched);
	}


	/**
	 * Calculate Score :: DEBUG
	 * @access Private - Use only this Controller
	 * @param $tablename : Primary Table Name
	 * @param $user_id: Userid
	 * @return Array - Fetch data
	 * @author Rajnish Savaliya
	 */
	public function calculate_score_debug($user_id=3, $intro_id=1)
	{
		$match = array();
		$slightly = array();
		$not_matched = array();

		$obtain_score = 0;
		$total_score = 0;
		$user = $this->CI->model_user->get_user_data($user_id);
		$cmp_user = $this->CI->model_user->get_user_data($intro_id);

		echo '<br/><hr/> Score Calculation ---->> '.$user['first_name'].' with '.$cmp_user['first_name'].'  <<-----<hr/>';
		$this->CI->general->set_table('filter');
		$filters = $this->CI->general->get(array('*'),array('language_id'=>1),array('view_order'=>'asc'));
		if($filters)
		{
			foreach ($filters as $filter)
			{
				switch ($filter['id_to_map'])
				{

					case "age":
						if($user['want_age_range_upper'] && $user['want_age_range_lower'])
						{
							$tmp_obtain_score  = 0;
							$tmp_total_score = 0;

							$tmp_total_score++;
							$tmp_total_score = $this->multiply_importance($tmp_total_score, $user['want_age_range_importance']);


							//Calculate Birthday
							$cmp_user_age = 0;
							$birthDate = $cmp_user['birth_date'];
							if($birthDate != '0000-00-00')
							{
								//explode the date to get month, day and year
								$birthDate = explode("-", $birthDate);
								//get age from date or birthdate
								$cmp_user_age = (date("md", date("U", mktime(0, 0, 0, $birthDate[0], $birthDate[1], $birthDate[2]))) > date("md") ? ((date("Y")-$birthDate[0])-1):(date("Y")-$birthDate[0]));
							}

							if($cmp_user_age <= $user['want_age_range_upper'] && $cmp_user_age >= $user['want_age_range_lower'])
							{
								$match[] = $filter['description'];
								$tmp_obtain_score ++;
								$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $user['want_age_range_importance']);
							}
							else
							{
								$slightly_diff = '-2';
								$tmp_score = $cmp_user_age - $slightly_diff;
								$new_tmp_score = $cmp_user_age + $slightly_diff;
								if(($tmp_score <= $user['want_age_range_upper'] && $tmp_score >= $user['want_age_range_lower']) || ($new_tmp_score <= $user['want_age_range_upper'] && $new_tmp_score >= $user['want_age_range_lower']))
								{
									$slightly[] = $filter['description'];
									$tmp_obtain_score = $tmp_obtain_score + 0.5;
									$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $user['want_age_range_importance']);
								}
								else
								{
									$not_matched[] = $filter['description'];
									//*michael* $tmp_obtain_score --;
									$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $user['want_age_range_importance']);
								}
							}

							echo "<br/><hr/> User Want Age:: <pre>";print_r( $user['want_age_range_lower'].' To '.$user['want_age_range_upper']);
							echo '<br/>user importance :'.$user['want_age_range_importance'].'<br/></pre>';
							echo "<br/> Intro Have :: ".$cmp_user_age;
							echo '<br/> Score Before ='.$obtain_score.'/'.$total_score.'<br/>';

							$obtain_score += $tmp_obtain_score ;
							$total_score += $tmp_total_score;

							echo '<br/> Score After ='.$obtain_score.'/'.$total_score.'<br/>';
						}
						break;
							
					case "ethnicity":
						$crite_area = "ethnicity";
						$user_want_crite_area = $this->user_want($user['user_id'],$crite_area);

						$tmp_obtain_score  = 0;
						$tmp_total_score = 0;
						if($user_want_crite_area)
						{
							//if user required
							$tmp_total_score++;
							$tmp_total_score =  $this->multiply_importance($tmp_total_score, $user['want_'.$crite_area.'_importance']);

							if($cmp_user[$crite_area.'_id'])
							{
								$is_found = false;
								foreach ($user_want_crite_area  as $key=>$value)
								{
									if($value[$crite_area.'_id'] == $cmp_user[$crite_area.'_id'] )
									{
										$is_found = true;
									}
								}

								if($is_found)
								{
									$match[] = $filter['description'];
									$tmp_obtain_score ++;
									$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $user['want_'.$crite_area.'_importance']);

								}
								else
								{
									$not_matched[] = $filter['description'];
									//*michael* $tmp_obtain_score --;
									$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $user['want_'.$crite_area.'_importance']);

									//$slightly[] = $crite_area;
								}
							}

							//echo "User Want:: <pre>";print_r($user_want_crite_area);
							//echo 'user importance :'.$user['want_'.$crite_area.'_importance'].'<br/>';
							//echo "Intro Have:: ";print_r($cmp_user[$crite_area.'_id']);
							//echo '<hr/>';
							//echo '<br/> Score Before ='.$obtain_score.'/'.$total_score.'<br/>';

							$obtain_score += $tmp_obtain_score ;
							$total_score += $tmp_total_score;

							//echo '<br/> Score After ='.$obtain_score.'/'.$total_score.'<br/>';
						}
						break;
					case "relationshipGoal":
						$crite_area = "relationship_type";
						$user_want_descirptive_crite_area = $this->user_want($user['user_id'],$crite_area);
						$tmp_obtain_score  = 0;
						$tmp_total_score = 0;
						if($user_want_descirptive_crite_area)
						{
							$tmp_total_score++;

							$want_field = $user['want_looking_for_importance'];
							$cmp_user_descirptive_crite_area = $this->CI->model_user->multijoins("usr.*,prsnality.*",
					"user_want_".$crite_area." as usr",array($crite_area.' as prsnality'=>array('usr.'.$crite_area.'_id = prsnality.'.$crite_area.'_id ','INNER')),
							array('prsnality.display_language_id'=>$this->language_id,'usr.user_id'=>$intro_id));

							$tmp_total_score =  $this->multiply_importance($tmp_total_score, $want_field);

							if($cmp_user_descirptive_crite_area)
							{
								$is_found = false;
								$descirptive_crite_area_total_match = 0;

								foreach ($cmp_user_descirptive_crite_area as $key=>$value)
								{
									foreach ($user_want_descirptive_crite_area as $descirptive_crite_area)
									{
										if($value[$crite_area.'_id'] == $descirptive_crite_area[$crite_area.'_id'])
										{
											$descirptive_crite_area_total_match++;
											$is_found = true;
										}
									}
								}

								if($is_found)
								{
									$match[] = $filter['description'];
									$tmp_obtain_score ++;
									$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $want_field);
								}
								else
								{
									//*michael* $tmp_obtain_score --;
									$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $want_field);
									$not_matched[] = $filter['description'];
									//$slightly[] = $crite_area;
								}
							}
							else
							{
								//*michael* $tmp_obtain_score --;
								$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $want_field);
								$not_matched[] = $filter['description'];
							}

							echo "<br/><hr/> user want ".$filter['description'].":: <pre>";print_r($user_want_descirptive_crite_area);
							echo '<br/>user importance :'.$want_field.'<br/></pre>';
							echo "Intro Have:: ";print_r($cmp_user_descirptive_crite_area);
							echo '<br/> Score Before ='.$obtain_score.'/'.$total_score.'<br/>';

							$obtain_score += $tmp_obtain_score ;
							$total_score += $tmp_total_score;

							echo '<br/> Score After ='.$obtain_score.'/'.$total_score.'<br/>';

						}
						break;
					case "common_interest":
						$want_field = $user['want_common_interest_importance'];
						$this->CI->general->set_table('user_interest');
						$interestJoins = array('user_interest'=>array('interest.interest_id = user_interest.interest_id','inner'));
						$interestCondition = array('interest.display_language_id'=>$this->language_id,'user_interest.user_id'=>$user_id);
						$user_interests = $this->CI->general->multijoins_arr('interest.* ','interest',$interestJoins,$interestCondition,'','interest.view_order asc');

						if($want_field && $user_interests)
						{
							$tmp_total_score++;
							$tmp_total_score =  $this->multiply_importance($tmp_total_score, $want_field);

							$this->CI->general->set_table('user_interest');
							$interestJoins = array('user_interest'=>array('interest.interest_id = user_interest.interest_id','inner'));
							$interestCondition = array('interest.display_language_id'=>$this->language_id,'user_interest.user_id'=>$intro_id);
							$intro_interests = $this->CI->general->multijoins_arr('interest.* ','interest',$interestJoins,$interestCondition,'','interest.view_order asc');

							if($intro_interests)
							{
								$is_found = false;
								$crite_area_total_match = 0;

								foreach ($user_interests as $key=>$value)
								{
									foreach ( $intro_interests as $descirptive_crite_area)
									{
										if($value['interest_id'] == $descirptive_crite_area['interest_id'])
										{
											$crite_area_total_match++;
											$is_found = true;
										}
									}
								}

								if($is_found)
								{
									$match[] = $filter['description'];
									$tmp_obtain_score ++;
									$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $want_field);
								}
								else
								{
									//*michael* $tmp_obtain_score --;
									$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $want_field);
									$not_matched[] = $filter['description'];
									//$slightly[] = $crite_area;
								}
							}
							else
							{
								//*michael* $tmp_obtain_score --;
								$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $want_field);
								$not_matched[] = $filter['description'];
							}

							echo "<br/><hr/> user want ".$filter['description'].":: <pre>";print_r($user_interests);
							echo '<br/>user importance :'.$want_field.'<br/></pre>';
							echo "Intro Have:: ";print_r($intro_interests);
							echo '<br/> Score Before ='.$obtain_score.'/'.$total_score.'<br/>';

							$obtain_score += $tmp_obtain_score ;
							$total_score += $tmp_total_score;

							echo '<br/> Score After ='.$obtain_score.'/'.$total_score.'<br/>';
						}
						break;
							
					case "looks":
						//For Look Range
						if($user['want_looks_range_higher_id'] && $user['want_looks_range_lower_id'])
						{
							$tmp_obtain_score  = 0;
							$tmp_total_score = 0;

							$tmp_total_score++;
							$tmp_total_score =  $this->multiply_importance($tmp_total_score, $user['want_looks_importance']);

							//database records so reverse condition in auto increment id
							if($cmp_user['looks_id'] >= $user['want_looks_range_higher_id'] && $cmp_user['looks_id'] <= $user['want_looks_range_lower_id'])
							{
								$match[] = $filter['description'];
								$tmp_obtain_score ++;
								$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $user['want_looks_importance']);
									
							}
							else
							{
								$slightly_diff = '-1';
								$tmp_score = $cmp_user['looks_id'] - $slightly_diff;
								$new_tmp_score = $cmp_user['looks_id'] + $slightly_diff;

								if(($tmp_score  >= $user['want_looks_range_higher_id'] && $tmp_score  <= $user['want_looks_range_lower_id']) || $new_tmp_score  >= $user['want_looks_range_higher_id'] && $new_tmp_score  <= $user['want_looks_range_lower_id'])
								{
									$slightly[] = $filter['description'];
									$tmp_obtain_score = $tmp_obtain_score + 0.5;
									$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $user['want_looks_importance']);
								}
								else
								{
									$not_matched[] = $filter['description'];
									//*michael* $tmp_obtain_score --;
									$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $user['want_looks_importance']);
								}
							}

							echo "<br/><hr/> User Want ".$filter['description'].":: <pre>";print_r($user['want_looks_range_higher_id'] .' - '. $user['want_looks_range_lower_id']);
							echo '<br/>user importance :'.$user['want_looks_importance'].'<br/></pre>';
							echo "Intro Have:: ";print_r($cmp_user['looks_id']);
							echo '<br/> Score Before ='.$obtain_score.'/'.$total_score.'<br/>';

							$obtain_score += $tmp_obtain_score ;
							$total_score += $tmp_total_score;

							echo '<br/> Score After ='.$obtain_score.'/'.$total_score.'<br/>';
						}
						break;
							
					case "height":
						//For Height
						if($user['want_height_range_upper'] && $user['want_height_range_lower'])
						{
							$tmp_obtain_score  = 0;
							$tmp_total_score = 0;

							$tmp_total_score++;
							$tmp_total_score =  $this->multiply_importance($tmp_total_score, $user['want_height_range_importance']);


							if($cmp_user['height'] <= $user['want_height_range_upper'] && $cmp_user['height'] >= $user['want_height_range_lower'])
							{
								$match[] = $filter['description'];
								$tmp_obtain_score ++;
								$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $user['want_height_range_importance']);
							}
							else
							{
								$slightly_diff = '-5';
								$tmp_score = $cmp_user['height'] - $slightly_diff;
								$new_tmp_score = $cmp_user['height'] + $slightly_diff;

								if(($tmp_score  <= $user['want_height_range_upper'] && $tmp_score  >= $user['want_height_range_lower']) || ($new_tmp_score    <= $user['want_height_range_upper'] && $new_tmp_score  >= $user['want_height_range_lower']))
								{
									$slightly[] = $filter['description'];
									$tmp_obtain_score = $tmp_obtain_score + 0.5;
								}
								else
								{
									$not_matched[] = $filter['description'];
									//*michael* $tmp_obtain_score --;
									$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $user['want_height_range_importance']);
								}
							}

							echo "<br/><hr/> User Want ".$filter['description'].":: <pre>";print_r($user['want_height_range_upper'] .' to '.$user['want_height_range_lower']);
							echo '<br/>user importance :'.$user['want_height_range_importance'].'<br/>';
							echo "<br/> Intro Have :: ".$cmp_user['height'];
							echo '<br/> Score Before ='.$obtain_score.'/'.$total_score.'</pre>';
							$obtain_score += $tmp_obtain_score ;
							$total_score += $tmp_total_score;

							echo '<br/> Score After ='.$obtain_score.'/'.$total_score.'<br/>';
						}
						break;
							
					case "bodyType":
						$crite_area = "body_type";
						$user_want_crite_area = $this->user_want($user['user_id'],$crite_area);

						$tmp_obtain_score  = 0;
						$tmp_total_score = 0;
						if($user_want_crite_area)
						{
							//if user required
							$tmp_total_score++;
							$tmp_total_score =  $this->multiply_importance($tmp_total_score, $user['want_'.$crite_area.'_importance']);

							if($cmp_user[$crite_area.'_id'])
							{
								$is_found = false;
								foreach ($user_want_crite_area  as $key=>$value)
								{
									if($value[$crite_area.'_id'] == $cmp_user[$crite_area.'_id'] )
									{
										$is_found = true;
									}
								}

								if($is_found)
								{
									$match[] = $filter['description'];
									$tmp_obtain_score ++;
									$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $user['want_'.$crite_area.'_importance']);

								}
								else
								{
									$not_matched[] = $filter['description'];
									//*michael* $tmp_obtain_score --;
									$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $user['want_'.$crite_area.'_importance']);

									//$slightly[] = $crite_area;
								}
							}

							echo "<br/><hr/> user want ".$filter['description'].":: <pre>";print_r($user_want_crite_area);
							echo '<br/>user importance :'. $user['want_'.$crite_area.'_importance'].'<br/></pre>';
							echo "Intro Have:: ";print_r($cmp_user[$crite_area.'_id']);
							echo '<br/> Score Before ='.$obtain_score.'/'.$total_score.'<br/>';

							$obtain_score += $tmp_obtain_score ;
							$total_score += $tmp_total_score;

							echo '<br/> Score After ='.$obtain_score.'/'.$total_score.'<br/>';
						}
						break;
					case "personality":
						$crite_area = "descriptive_word";
						$user_want_descirptive_crite_area = $this->user_want($user['user_id'],$crite_area);
						$tmp_obtain_score  = 0;
						$tmp_total_score = 0;
						if($user_want_descirptive_crite_area)
						{
							$tmp_total_score++;

							$want_field = $user['want_personality_importance'];
							$cmp_user_descirptive_crite_area = $this->CI->model_user->multijoins("usr.*,prsnality.*",
					"user_".$crite_area." as usr",array($crite_area.' as prsnality'=>array('usr.'.$crite_area.'_id = prsnality.'.$crite_area.'_id ','INNER')),
							array('prsnality.display_language_id'=>$this->language_id,'usr.user_id'=>$intro_id));

							$tmp_total_score =  $this->multiply_importance($tmp_total_score, $want_field);

							if($cmp_user_descirptive_crite_area)
							{
								$is_found = false;
								$descirptive_crite_area_total_match = 0;

								foreach ($cmp_user_descirptive_crite_area as $key=>$value)
								{
									foreach ($user_want_descirptive_crite_area as $descirptive_crite_area)
									{
										if($value[$crite_area.'_id'] == $descirptive_crite_area[$crite_area.'_id'])
										{
											$descirptive_crite_area_total_match++;
											$is_found = true;
										}
									}
								}

								if($is_found)
								{
									$match[] = $filter['description'];
									$tmp_obtain_score ++;
									$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $want_field);
								}
								else
								{
									//*michael* $tmp_obtain_score --;
									$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $want_field);
									$not_matched[] = $filter['description'];
									//$slightly[] = $crite_area;
								}
							}
							else
							{
								//*michael* $tmp_obtain_score --;
								$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $want_field);
								$not_matched[] = $filter['description'];
							}

							echo "<br/><hr/> user want ".$filter['description'].":: <pre>";print_r($user_want_descirptive_crite_area);
							echo '<br/>user importance :'. $want_field.'<br/></pre>';
							echo "Intro Have:: ";print_r($cmp_user_descirptive_crite_area);
							echo '<br/> Score Before ='.$obtain_score.'/'.$total_score.'<br/>';

							$obtain_score += $tmp_obtain_score ;
							$total_score += $tmp_total_score;

							echo '<br/> Score After ='.$obtain_score.'/'.$total_score.'<br/>';
						}
						break;
							
					case "school_major":

						$user_want_school_subject = $this->user_want($user['user_id'],'school_subject');
						if($user_want_school_subject)
						{
							$this->CI->db->group_by("sc_sub.school_subject_id");
							$cmp_user_school_subject = $this->CI->model_user->multijoins("sc_sub.*,us.*","user_school as us",array('user_school_major as sub_mjr'=>array('us.user_school_id = sub_mjr.user_school_id','INNER'),'school_subject as sc_sub'=>array('sc_sub.school_subject_id = sub_mjr.major_id ','INNER')),array('sc_sub.display_language_id'=>$this->language_id,'us.user_id'=>$intro_id));

							$tmp_obtain_score  = 0;
							$tmp_total_score = 0;

							$tmp_total_score++;
							$tmp_total_score =  $this->multiply_importance($tmp_total_score, $user['want_school_subject_importance']);

							if($cmp_user_school_subject)
							{
								$is_found = false;
								$descirptive_crite_area_total_match = 0;

								foreach ($cmp_user_school_subject as $key=>$value)
								{
									foreach ($user_want_school_subject as $descirptive_crite_area)
									{
										if($value['school_subject_id'] == $descirptive_crite_area['school_subject_id'])
										{
											$descirptive_crite_area_total_match++;
											$is_found = true;
										}
									}
								}

								if($is_found)
								{
									$match[] = $filter['description'];
									$tmp_obtain_score ++;
									$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $user['want_school_subject_importance']);

								}
								else
								{
									//*michael* $tmp_obtain_score --;
									$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $user['want_school_subject_importance']);

									$not_matched[] = $filter['description'];
									//$slightly[] = $crite_area;
								}

							}
							else
							{
								//*michael* $tmp_obtain_score --;
								$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $user['want_school_subject_importance']);

								$not_matched[] = $filter['description'];
							}
							echo "<br/><hr/> user want ".$filter['description'].":: <pre>";print_r($user_want_school_subject);
							echo '<br/>user importance :'. $user['want_school_subject_importance'].'<br/></pre>';
							echo "Intro Have:: ";print_r($cmp_user_school_subject);
							echo '<br/> Score Before ='.$obtain_score.'/'.$total_score.'<br/>';

							$obtain_score += $tmp_obtain_score ;
							$total_score += $tmp_total_score;

							echo '<br/> Score After ='.$obtain_score.'/'.$total_score.'<br/>';
						}
						break;
							
					case "education_level":
							
						$crite_area = "education_level";
						$user_want_descirptive_crite_area = $this->user_want($user['user_id'],$crite_area);
						$tmp_obtain_score  = 0;
						$tmp_total_score = 0;
						if($user_want_descirptive_crite_area)
						{
							$tmp_total_score++;

							$want_field = $user['want_'.$crite_area.'_importance'];

							$cmp_user_descirptive_crite_area = $this->CI->model_user->multijoins("usr.*,prsnality.*",
					"user_".$crite_area." as usr",array($crite_area.' as prsnality'=>array('usr.'.$crite_area.'_id = prsnality.'.$crite_area.'_id ','INNER')),
							array('prsnality.display_language_id'=>$this->language_id,'usr.user_id'=>$intro_id));

							$tmp_total_score =  $this->multiply_importance($tmp_total_score, $want_field);

							if($cmp_user_descirptive_crite_area)
							{
								$is_found = false;
								$descirptive_crite_area_total_match = 0;

								foreach ($cmp_user_descirptive_crite_area as $key=>$value)
								{
									foreach ($user_want_descirptive_crite_area as $descirptive_crite_area)
									{
										if($value[$crite_area.'_id'] == $descirptive_crite_area[$crite_area.'_id'])
										{
											$descirptive_crite_area_total_match++;
											$is_found = true;
										}
									}
								}

								if($is_found)
								{
									$match[] = $filter['description'];
									$tmp_obtain_score ++;
									$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $want_field);
								}
								else
								{
									//*michael* $tmp_obtain_score --;
									$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $want_field);
									$not_matched[] = $filter['description'];
									//$slightly[] = $crite_area;
								}
							}
							else
							{
								//*michael* $tmp_obtain_score --;
								$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $want_field);
								$not_matched[] = $filter['description'];
							}

							echo "<br/><hr/> user want ".$filter['description'].":: <pre>";print_r($user_want_descirptive_crite_area);
							echo '<br/>user importance :'. $want_field.'<br/></pre>';
							echo "Intro Have:: ";print_r($cmp_user_descirptive_crite_area);
							echo '<br/> Score Before ='.$obtain_score.'/'.$total_score.'<br/>';

							$obtain_score += $tmp_obtain_score ;
							$total_score += $tmp_total_score;

							echo '<br/> Score After ='.$obtain_score.'/'.$total_score.'<br/>';
						}
						break;
					case "job_function":
						$user_want_job_function = $this->user_want($user['user_id'],'job_function');
						if($user_want_job_function)
						{
							$tmp_obtain_score  = 0;
							$tmp_total_score = 0;

							$tmp_total_score++;
							$tmp_total_score =  $this->multiply_importance($tmp_total_score, $user['want_job_function_importance']);


							$this->CI->general->set_table('user_job');
							$job_function['user_id'] = $intro_id;
							$job_function['job_function_id != '] = '';

							$cmp_user_job= $this->CI->general->get('job_function_id',$job_function);
							unset($job_function);

							if($cmp_user_job)
							{
								$is_found = false;
								$descirptive_crite_area_total_match = 0;

								foreach ($cmp_user_job as $key=>$value)
								{
									foreach ($user_want_job_function as $descirptive_crite_area)
									{
										if($value['job_function_id'] == $descirptive_crite_area['job_function_id'])
										{
											$descirptive_crite_area_total_match++;
											$is_found = true;
										}
									}
								}

								if($is_found)
								{
									$match[] = $filter['description'];
									$tmp_obtain_score ++;
									$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $user['want_job_function_importance']);

								}
								else
								{
									//*michael* $tmp_obtain_score --;
									$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $user['want_job_function_importance']);

									$not_matched[] = $filter['description'];
									//$slightly[] = $crite_area;
								}
							}
							else
							{
								//*michael* $tmp_obtain_score --;
								$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $user['want_job_function_importance']);
								$not_matched[] = $filter['description'];
								//$slightly[] = $crite_area;
							}

							echo "<br/><hr/> user want ".$filter['description'].":: <pre>";print_r($user_want_job_function);
							echo '<br/>user importance :'. $user['want_job_function_importance'].'<br/></pre>';
							echo "Intro Have:: ";print_r($cmp_user_job);
							echo '<br/> Score Before ='.$obtain_score.'/'.$total_score.'<br/>';

							$obtain_score += $tmp_obtain_score ;
							$total_score += $tmp_total_score;

							echo '<br/> Score After ='.$obtain_score.'/'.$total_score.'<br/>';

						}
						break;
					case "income_level":
						if($user['want_annual_income'])
						{
							echo '<h1> INCOME LEVEL</h1>';
							$tmp_obtain_score  = 0;
							$tmp_total_score = 0;

							$tmp_total_score++;
							$tmp_total_score =  $this->multiply_importance($tmp_total_score, $user['want_annual_income_importance']);
							$income_range_temp = $this->CI->model_user->multijoins(array('user.user_id','currency.description as currency_desc','currency.*','want_income.*'),'annual_income_range as want_income',
							array(
								'user'=>array('user.annual_income_range_id = want_income.annual_income_range_id','inner'),
								'country'=>array('country.country_id = want_income.country_id','inner'),
								'currency'=>array('currency.currency_id=country.currency_id','inner')
							),
							array(
								'want_income.display_language_id'=>$this->language_id, 
								'country.display_language_id'=>$this->language_id,
								'currency.display_language_id'=>$this->language_id,
								'user_id'=>$intro_id)
							);

							if($income_range_temp)
							{
								$income_range_temp = $income_range_temp['0'];

								if(strrpos($income_range_temp['description'],'-') !== false)
								{
									$tmp = explode('-',$income_range_temp['description']);
									$lower_limit = preg_replace('/[^\d]/','',$tmp[0]);
									$higher_limit = preg_replace('/[^\d]/','',$tmp[1]);
								}
								else if(strrpos($income_range_temp['description'],'<') !== false)
								{
									$tmp = explode('<',$income_range_temp['description']);
									$lower_limit = 0;
									$higher_limit = preg_replace('/[^\d]/','',$tmp[1]);
								}
								else if(strrpos($income_range_temp['description'],'>') !== false)
								{
									$tmp = explode('>',$income_range_temp['description']);
									$lower_limit = preg_replace('/[^\d]/','',$tmp[1]);
									$higher_limit = 999999999999;
								}
								if($user['want_annual_income_currency_id'] != $income_range_temp['currency_id'])
								{
									$lower_limit /= $income_range_temp['rate'];
									$higher_limit /= $income_range_temp['rate'] ;

									$this->CI->general->set_table('currency');
									if($want_currency_data = $this->CI->general->get("",array('display_language_id'=>$this->language_id,'currency_id'=>$user['want_annual_income_currency_id'])))
									{
										$user['want_annual_income'] /= $want_currency_data['0']['rate'];
									}
								}

								if($user['want_annual_income'] <= $higher_limit && $lower_limit > 0)
								//if( ($user['want_annual_income'] <= $higher_limit || $higher_limit == '') && ($user['want_annual_income'] >= $lower_limit && $lower_limit != ''))
								{
									echo 'match..<br/>';
									$match[] = $filter['description'];
									$tmp_obtain_score ++;
									$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $user['want_annual_income_importance']);
								}
								elseif($user['want_annual_income'] <= $higher_limit * 1.2 && $lower_limit > 0)
								{
									echo 'slightly match..<br/>';
									$slightly[] = $filter['description'];
									$tmp_obtain_score = $tmp_obtain_score + 0.5;
									$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $user['want_annual_income_importance']);
								}
								else
								{
									echo 'not match..<br/>';
									$not_matched[] = $filter['description'];
									//*michael* $tmp_obtain_score --;
									$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $user['want_annual_income_importance']);
								}
								echo '<br/>Currency convert : '.$lower_limit.' - '.$higher_limit;
							}
							else
							{
								echo 'not match..<br/>';
								//*michael* $tmp_obtain_score --;
								$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $user['want_annual_income_importance']);
								$not_matched[] = $filter['description'];
							}

							echo "<br/><hr/> user want ".$filter['description'].":: <pre>";print_r($user['want_annual_income']);
							echo '<br/>user importance :'. $user['want_annual_income_importance'].'<br/></pre>';
							echo "Intro Have:: <pre>";print_r($income_range_temp);
							echo '</pre><br/> Score Before ='.$obtain_score.'/'.$total_score.'<br/>';

							$obtain_score += $tmp_obtain_score ;
							$total_score += $tmp_total_score;

							echo '<br/> Score After ='.$obtain_score.'/'.$total_score.'<br/>';
						}

						break;
					case "job_industry":
						$user_want_industry = $this->user_want($user['user_id'],'industry');
						if($user_want_industry)
						{
							$this->CI->general->set_table('user_job');
							$industy_cond['user_id'] = $intro_id;
							$cmp_user_industry = $this->CI->general->get('industry_id',$industy_cond);
							unset($industy_cond);

							$tmp_obtain_score  = 0;
							$tmp_total_score = 0;

							$tmp_total_score++;
							$tmp_total_score =  $this->multiply_importance($tmp_total_score, $user['want_industry_importance']);

							if($cmp_user_industry)
							{
								$is_found = false;
								$descirptive_crite_area_total_match = 0;

								foreach ($cmp_user_industry as $key=>$value)
								{
									foreach ($user_want_industry as $descirptive_crite_area)
									{
										if($value['industry_id'] == $descirptive_crite_area['industry_id'])
										{
											$descirptive_crite_area_total_match++;
											$is_found = true;
										}
									}
								}

								if($is_found)
								{
									$match[] = $filter['description'];
									$tmp_obtain_score ++;
									$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $user['want_industry_importance']);

								}
								else
								{
									//*michael* $tmp_obtain_score --;
									$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $user['want_industry_importance']);

									$not_matched[] = $filter['description'];
									//$slightly[] = $crite_area;
								}
							}
							else
							{
								//*michael* $tmp_obtain_score --;
								$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $user['want_industry_importance']);

								$not_matched[] = $filter['description'];
								//$slightly[] = $crite_area;
							}

							echo "<br/><hr/> user want ".$filter['description'].":: <pre>";print_r($user_want_industry);
							echo '<br/>user importance :'. $user['want_industry_importance'].'<br/></pre>';
							echo "Intro Have:: ";print_r($cmp_user_industry);
							echo '<br/> Score Before ='.$obtain_score.'/'.$total_score.'<br/>';

							$obtain_score += $tmp_obtain_score ;
							$total_score += $tmp_total_score;

							echo '<br/> Score After ='.$obtain_score.'/'.$total_score.'<br/>';
						}

						break;
					case "career_stage":
						$crite_area = "career_stage";
						$user_want_crite_area = $this->user_want($user['user_id'],$crite_area);

						$tmp_obtain_score  = 0;
						$tmp_total_score = 0;
						if($user_want_crite_area)
						{
							//if user required
							$tmp_total_score++;
							$tmp_total_score =  $this->multiply_importance($tmp_total_score, $user['want_'.$crite_area.'_importance']);

							if($cmp_user[$crite_area.'_id'])
							{
								$is_found = false;
								foreach ($user_want_crite_area  as $key=>$value)
								{
									if($value[$crite_area.'_id'] == $cmp_user[$crite_area.'_id'] )
									{
										$is_found = true;
									}
								}

								if($is_found)
								{
									$match[] = $filter['description'];
									$tmp_obtain_score ++;
									$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $user['want_'.$crite_area.'_importance']);

								}
								else
								{
									$not_matched[] = $filter['description'];
									//*michael* $tmp_obtain_score --;
									$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $user['want_'.$crite_area.'_importance']);

									//$slightly[] = $crite_area;
								}
							}

							echo "<br/><hr/> user want ".$filter['description'].":: <pre>";print_r($user_want_crite_area);
							echo '<br/>user importance :'.  $user['want_'.$crite_area.'_importance'].'<br/></pre>';
							echo "Intro Have:: ";print_r($cmp_user[$crite_area.'_id']);
							echo '<br/> Score Before ='.$obtain_score.'/'.$total_score.'<br/>';

							$obtain_score += $tmp_obtain_score ;
							$total_score += $tmp_total_score;

							echo '<br/> Score After ='.$obtain_score.'/'.$total_score.'<br/>';
						}
						break;
							
					case "relationshipStatus":
						$crite_area = "relationship_status";
						$user_want_crite_area = $this->user_want($user['user_id'],$crite_area);

						$tmp_obtain_score  = 0;
						$tmp_total_score = 0;
						if($user_want_crite_area)
						{
							//if user required
							$tmp_total_score++;
							$tmp_total_score =  $this->multiply_importance($tmp_total_score, $user['want_'.$crite_area.'_importance']);

							if($cmp_user[$crite_area.'_id'])
							{
								$is_found = false;
								foreach ($user_want_crite_area  as $key=>$value)
								{
									if($value[$crite_area.'_id'] == $cmp_user[$crite_area.'_id'] )
									{
										$is_found = true;
									}
								}

								if($is_found)
								{
									$match[] = $filter['description'];
									$tmp_obtain_score ++;
									$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $user['want_'.$crite_area.'_importance']);

								}
								else
								{
									$not_matched[] = $filter['description'];
									//*michael* $tmp_obtain_score --;
									$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $user['want_'.$crite_area.'_importance']);

									//$slightly[] = $crite_area;
								}
							}

							echo "<br/><hr/> user want ".$filter['description'].":: <pre>";print_r($user_want_crite_area);
							echo '<br/>user importance :'.  $user['want_'.$crite_area.'_importance'].'<br/></pre>';
							echo "Intro Have:: ";print_r($cmp_user[$crite_area.'_id']);
							echo '<br/> Score Before ='.$obtain_score.'/'.$total_score.'<br/>';

							$obtain_score += $tmp_obtain_score ;
							$total_score += $tmp_total_score;

							echo '<br/> Score After ='.$obtain_score.'/'.$total_score.'<br/>';
						}
						break;
					case "religion":
						$crite_area = "religious_belief";
						$user_want_crite_area = $this->user_want($user['user_id'],$crite_area);

						$tmp_obtain_score  = 0;
						$tmp_total_score = 0;
						if($user_want_crite_area)
						{
							//if user required
							$tmp_total_score++;
							$tmp_total_score =  $this->multiply_importance($tmp_total_score, $user['want_'.$crite_area.'_importance']);

							if($cmp_user[$crite_area.'_id'])
							{
								$is_found = false;
								foreach ($user_want_crite_area  as $key=>$value)
								{
									if($value[$crite_area.'_id'] == $cmp_user[$crite_area.'_id'] )
									{
										$is_found = true;
									}
								}

								if($is_found)
								{
									$match[] = $filter['description'];
									$tmp_obtain_score ++;
									$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $user['want_'.$crite_area.'_importance']);

								}
								else
								{
									$not_matched[] = $filter['description'];
									//*michael* $tmp_obtain_score --;
									$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $user['want_'.$crite_area.'_importance']);

									//$slightly[] = $crite_area;
								}
							}

							echo "<br/><hr/> user want ".$filter['description'].":: <pre>";print_r($user_want_crite_area);
							echo '<br/>user importance :'.  $user['want_'.$crite_area.'_importance'].'<br/></pre>';
							echo "Intro Have:: ";print_r($cmp_user[$crite_area.'_id']);
							echo '<br/> Score Before ='.$obtain_score.'/'.$total_score.'<br/>';

							$obtain_score += $tmp_obtain_score ;
							$total_score += $tmp_total_score;

							echo '<br/> Score After ='.$obtain_score.'/'.$total_score.'<br/>';
						}
						break;
							
					case "smokingStatus":
						$crite_area = "smoking_status";
						$user_want_crite_area = $this->user_want($user['user_id'],$crite_area);

						$tmp_obtain_score  = 0;
						$tmp_total_score = 0;
						if($user_want_crite_area)
						{
							//if user required
							$tmp_total_score++;
							$tmp_total_score =  $this->multiply_importance($tmp_total_score, $user['want_'.$crite_area.'_importance']);

							if($cmp_user[$crite_area.'_id'])
							{
								$is_found = false;
								foreach ($user_want_crite_area  as $key=>$value)
								{
									if($value[$crite_area.'_id'] == $cmp_user[$crite_area.'_id'] )
									{
										$is_found = true;
									}
								}

								if($is_found)
								{
									$match[] = $filter['description'];
									$tmp_obtain_score ++;
									$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $user['want_'.$crite_area.'_importance']);

								}
								else
								{
									$not_matched[] = $filter['description'];
									//*michael* $tmp_obtain_score --;
									$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $user['want_'.$crite_area.'_importance']);

									//$slightly[] = $crite_area;
								}
							}

							echo "<br/><hr/> user want ".$filter['description'].":: <pre>";print_r($user_want_crite_area);
							echo '<br/>user importance :'.  $user['want_'.$crite_area.'_importance'].'<br/></pre>';
							echo "Intro Have:: ";print_r($cmp_user[$crite_area.'_id']);
							echo '<br/> Score Before ='.$obtain_score.'/'.$total_score.'<br/>';

							$obtain_score += $tmp_obtain_score ;
							$total_score += $tmp_total_score;

							echo '<br/> Score After ='.$obtain_score.'/'.$total_score.'<br/>';
						}
						break;
					case "drinkingStatus":
						$crite_area = "drinking_status";
						$user_want_crite_area = $this->user_want($user['user_id'],$crite_area);

						$tmp_obtain_score  = 0;
						$tmp_total_score = 0;
						if($user_want_crite_area)
						{
							//if user required
							$tmp_total_score++;
							$tmp_total_score =  $this->multiply_importance($tmp_total_score, $user['want_'.$crite_area.'_importance']);

							if($cmp_user[$crite_area.'_id'])
							{
								$is_found = false;
								foreach ($user_want_crite_area  as $key=>$value)
								{
									if($value[$crite_area.'_id'] == $cmp_user[$crite_area.'_id'] )
									{
										$is_found = true;
									}
								}

								if($is_found)
								{
									$match[] = $filter['description'];
									$tmp_obtain_score ++;
									$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $user['want_'.$crite_area.'_importance']);

								}
								else
								{
									$not_matched[] = $filter['description'];
									//*michael* $tmp_obtain_score --;
									$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $user['want_'.$crite_area.'_importance']);

									//$slightly[] = $crite_area;
								}
							}

							echo "<br/><hr/> user want ".$filter['description'].":: <pre>";print_r($user_want_crite_area);
							echo '<br/>user importance :'.  $user['want_'.$crite_area.'_importance'].'<br/></pre>';
							echo "Intro Have:: ";print_r($cmp_user[$crite_area.'_id']);
							echo '<br/> Score Before ='.$obtain_score.'/'.$total_score.'<br/>';

							$obtain_score += $tmp_obtain_score ;
							$total_score += $tmp_total_score;

							echo '<br/> Score After ='.$obtain_score.'/'.$total_score.'<br/>';
						}
						break;
							
					case "exerciseStatus":
						$crite_area = "exercise_frequency";
						$user_want_crite_area = $this->user_want($user['user_id'],$crite_area);

						$tmp_obtain_score  = 0;
						$tmp_total_score = 0;
						if($user_want_crite_area)
						{
							//if user required
							$tmp_total_score++;
							$tmp_total_score =  $this->multiply_importance($tmp_total_score, $user['want_'.$crite_area.'_importance']);

							if($cmp_user[$crite_area.'_id'])
							{
								$is_found = false;
								foreach ($user_want_crite_area  as $key=>$value)
								{
									if($value[$crite_area.'_id'] == $cmp_user[$crite_area.'_id'] )
									{
										$is_found = true;
									}
								}

								if($is_found)
								{
									$match[] = $filter['description'];
									$tmp_obtain_score ++;
									$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $user['want_'.$crite_area.'_importance']);

								}
								else
								{
									$not_matched[] = $filter['description'];
									//*michael* $tmp_obtain_score --;
									$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $user['want_'.$crite_area.'_importance']);

									//$slightly[] = $crite_area;
								}
							}

							echo "<br/><hr/> user want ".$filter['description'].":: <pre>";print_r($user_want_crite_area);
							echo '<br/>user importance :'.  $user['want_'.$crite_area.'_importance'].'<br/></pre>';
							echo "Intro Have:: ";print_r($cmp_user[$crite_area.'_id']);
							echo '<br/> Score Before ='.$obtain_score.'/'.$total_score.'<br/>';

							$obtain_score += $tmp_obtain_score ;
							$total_score += $tmp_total_score;

							echo '<br/> Score After ='.$obtain_score.'/'.$total_score.'<br/>';
						}
						break;
					case "childPlans":
						$crite_area = "child_plan";
						$user_want_crite_area = $this->user_want($user['user_id'],$crite_area);

						$tmp_obtain_score  = 0;
						$tmp_total_score = 0;
						if($user_want_crite_area)
						{
							//if user required
							$tmp_total_score++;
							$tmp_total_score =  $this->multiply_importance($tmp_total_score, $user['want_'.$crite_area.'_importance']);

							if($cmp_user[$crite_area.'_id'])
							{
								$is_found = false;
								foreach ($user_want_crite_area  as $key=>$value)
								{
									if($value[$crite_area.'_id'] == $cmp_user[$crite_area.'_id'] )
									{
										$is_found = true;
									}
								}

								if($is_found)
								{
									$match[] = $filter['description'];
									$tmp_obtain_score ++;
									$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $user['want_'.$crite_area.'_importance']);

								}
								else
								{
									$not_matched[] = $filter['description'];
									//*michael* $tmp_obtain_score --;
									$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $user['want_'.$crite_area.'_importance']);

									//$slightly[] = $crite_area;
								}
							}

							echo "<br/><hr/> user want ".$filter['description'].":: <pre>";print_r($user_want_crite_area);
							echo '<br/>user importance :'.  $user['want_'.$crite_area.'_importance'].'<br/></pre>';
							echo "Intro Have:: ";print_r($cmp_user[$crite_area.'_id']);
							echo '<br/> Score Before ='.$obtain_score.'/'.$total_score.'<br/>';

							$obtain_score += $tmp_obtain_score ;
							$total_score += $tmp_total_score;

							echo '<br/> Score After ='.$obtain_score.'/'.$total_score.'<br/>';
						}
						break;
					case "residenceType":
						//resident type hasn't a proper formate like residence_type_id so need to separate query.
						$user_want_resident_type = $this->user_want($user['user_id'],'residence_type');
						if($user_want_resident_type)
						{
							$tmp_obtain_score  = 0;
							$tmp_total_score = 0;

							$tmp_total_score++;
							$tmp_total_score =  $this->multiply_importance($tmp_total_score, $user['want_residence_type_importance']);

							$is_found = false;
							foreach ($user_want_resident_type  as $key=>$value)
							{
								if($value['residence_type_id'] == $cmp_user['residence_type'] )
								{
									$is_found = true;
								}
							}

							if($is_found)
							{
								$match[] = $filter['description'];
								$tmp_obtain_score ++;
								$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $user['want_residence_type_importance']);
							}
							else
							{
								$not_matched[] = $filter['description'];
								//*michael* $tmp_obtain_score --;
								$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $user['want_residence_type_importance']);

								//$slightly[] = "residence_type";
							}

							echo "<br/><hr/> user want ".$filter['description'].":: <pre>";print_r($user_want_resident_type);
							echo '<br/>user importance :'.  $user['want_residence_type_importance'].'<br/></pre>';
							echo "Intro Have:: ";print_r($cmp_user['residence_type']);
							echo '<br/> Score Before ='.$obtain_score.'/'.$total_score.'<br/>';

							$obtain_score += $tmp_obtain_score ;
							$total_score += $tmp_total_score;

							echo '<br/> Score After ='.$obtain_score.'/'.$total_score.'<br/>';
						}
						break;
					case "existing_children":

						$crite_area = "child_status";//relationship_status
						$user_want_crite_area = $this->user_want($user['user_id'],$crite_area);

						$tmp_obtain_score  = 0;
						$tmp_total_score = 0;
						if($user_want_crite_area)
						{
							//if user required
							$tmp_total_score++;
							$tmp_total_score =  $this->multiply_importance($tmp_total_score, $user['want_'.$crite_area.'_importance']);

							if($cmp_user[$crite_area.'_id'])
							{
								$is_found = false;
								foreach ($user_want_crite_area  as $key=>$value)
								{
									if($value[$crite_area.'_id'] == $cmp_user[$crite_area.'_id'] )
									{
										$is_found = true;
									}
								}

								if($is_found)
								{
									$match[] = $filter['description'];
									$tmp_obtain_score ++;
									$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $user['want_'.$crite_area.'_importance']);

								}
								else
								{
									$not_matched[] = $filter['description'];
									//*michael* $tmp_obtain_score --;
									$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $user['want_'.$crite_area.'_importance']);

									//$slightly[] = $crite_area;
								}
							}

							echo "<br/><hr/> user want ".$filter['description'].":: <pre>";print_r($user_want_crite_area);
							echo '<br/>user importance :'.  $user['want_'.$crite_area.'_importance'].'<br/></pre>';
							echo "Intro Have:: ";print_r($cmp_user[$crite_area.'_id']);
							echo '<br/> Score Before ='.$obtain_score.'/'.$total_score.'<br/>';

							$obtain_score += $tmp_obtain_score ;
							$total_score += $tmp_total_score;

							echo '<br/> Score After ='.$obtain_score.'/'.$total_score.'<br/>';
						}
						break;
					case 'school_name':

						$crite_area = 'school';
						$user_want_crite_area = $this->user_want($user['user_id'],'school');
						$tmp_obtain_score  = 0;
						$tmp_total_score = 0;
						if($user_want_crite_area)
						{

							//if user required
							$tmp_total_score++;
							$tmp_total_score =  $this->multiply_importance($tmp_total_score, $user['want_'.$crite_area.'_importance']);
							$fields = array('sc.school_id','sc.school_name','sc.logo_url','ct.*');
							$from = "user_school as uc";
							$joins = array(
														'school as sc'=>array('uc.school_id = sc.school_id','INNER'),
														'city as ct'=>array('ct.city_id = sc.city_id','INNER')
							);
							unset($condition);
							$condition['uc.user_id'] = $intro_id;
							$condition['sc.display_language_id'] = $this->language_id;
							$condition['ct.display_language_id'] = $this->language_id;
							$ordersby = 'sc.school_id asc';

							$user_school_datas = $this->CI->general->multijoins($fields,$from,$joins,$condition,$ordersby,'array');
							if($user_school_datas)
							{
								$is_found = false;
								$crite_area_total_match = 0;

								foreach ($user_school_datas as $key=>$value)
								{
									foreach ($user_want_crite_area as $crite_area_val)
									{
										if($value[$crite_area.'_id'] == $crite_area_val[$crite_area.'_id'])
										{
											$crite_area_total_match ++;
											$is_found = true;
										}
									}
								}

								if($is_found)
								{
									$match[] = $filter['description'];
									$tmp_obtain_score ++;
									$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $user['want_'.$crite_area.'_importance']);
								}
								else
								{
									//*michael* $tmp_obtain_score --;
									$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $user['want_'.$crite_area.'_importance']);
									$not_matched[] = $filter['description'];
									//$slightly[] = $crite_area;
								}
							}
							else
							{
								//*michael* $tmp_obtain_score --;
								$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $user['want_'.$crite_area.'_importance']);
								$not_matched[] = $filter['description'];
								//$slightly[] = $crite_area;
							}

							echo "<br/><hr/> user want ".$filter['description'].":: <pre>";print_r($user_want_crite_area);
							echo '<br/>user importance :'.  $user['want_'.$crite_area.'_importance'].'<br/></pre>';
							echo "Intro Have:: ";print_r($user_school_datas);
							echo '<br/> Score Before ='.$obtain_score.'/'.$total_score.'<br/>';

							$obtain_score += $tmp_obtain_score ;
							$total_score += $tmp_total_score;

							echo '<br/> Score After ='.$obtain_score.'/'.$total_score.'<br/>';
						}

						break;
					case 'company_name':

						$crite_area = 'company';
						$selected_company = $this->user_want($user['user_id'],'company');
						$this->CI->general->set_table('user_want_company');
						$user_custom_company_required = $this->CI->general->get("user_want_company_id, user_id,CONCAT('_', `company_name`,'_') as company_id, company_name",array('user_id'=>$user_id,'company_name !='=>''));
						$user_want_crite_area = array_merge($selected_company,$user_custom_company_required);

						$tmp_obtain_score  = 0;
						$tmp_total_score = 0;
						if($user_want_crite_area)
						{
							//if user required
							$tmp_total_score++;
							$tmp_total_score =  $this->multiply_importance($tmp_total_score, $user['want_'.$crite_area.'_importance']);

							$this->CI->general->set_table('user_job');
							$companyCondition = array('user_id'=>$intro_id);
							$companyFields = array('user_company_id','company_id','company_name','years_worked_start','years_worked_end');
							$companyOrderBy   = array('years_worked_end'=>'desc','years_worked_start'=>'desc','company_name'=>'asc');
							$company = $this->CI->general->get($companyFields,$companyCondition,$companyOrderBy);
							if($company)
							{
								$this->CI->general->set_table('company');
								foreach ($company as $r=>$cmp_val)
								{
									if($cmp_val['company_id'])
									{
										$company_name = $this->CI->general->get("",array('display_language_id'=>$this->language_id,'company_id'=>$cmp_val['company_id']));
										$company[$r]['company_name'] = $company_name['0']['company_name'];
									}
								}

								$is_found = false;
								$crite_area_total_match = 0;

								foreach ($company as $key=>$value)
								{
									foreach ($user_want_crite_area as $crite_area_val)
									{
										if($value[$crite_area.'_id'] == $crite_area_val[$crite_area.'_id'] || $value[$crite_area.'_name'] == $crite_area_val[$crite_area.'_name'])
										{
											$crite_area_total_match ++;
											$is_found = true;
										}
									}
								}
								if($is_found)
								{
									$match[] = $filter['description'];
									$tmp_obtain_score ++;
									$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $user['want_'.$crite_area.'_importance']);
								}
								else
								{
									//*michael* $tmp_obtain_score --;
									$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $user['want_'.$crite_area.'_importance']);
									$not_matched[] = $filter['description'];
									//$slightly[] = $crite_area;
								}
							}
							else
							{
								//*michael* $tmp_obtain_score --;
								$tmp_obtain_score  = $this->multiply_importance($tmp_obtain_score , $user['want_'.$crite_area.'_importance']);
								$not_matched[] = $filter['description'];
								//$slightly[] = $crite_area;
							}

							echo "<br/><hr/> user want ".$filter['description'].":: <pre>";print_r($user_want_crite_area);
							echo '<br/>user importance :'.  $user['want_'.$crite_area.'_importance'].'<br/></pre>';
							echo "Intro Have:: ";print_r($company);
							echo '<br/> Score Before ='.$obtain_score.'/'.$total_score.'<br/>';

							$obtain_score += $tmp_obtain_score ;
							$total_score += $tmp_total_score;

							echo '<br/> Score After ='.$obtain_score.'/'.$total_score.'<br/>';
						}

						break;
					default:
						break;
				}
			}
		}

		if($obtain_score > 0 && $total_score > 0)
		return array('score'=>round((($obtain_score/$total_score)*100)),'match_data'=>$match,'not_matched'=>$not_matched)  ;
		else
		return array('score'=>0,'match_data'=>$match,'not_matched'=>$not_matched);
	}

	/**
	 * Multiply Score with importance level [mandatory,important,very important]
	 * @param score
	 * @param importance
	 * @return score
	 * @author Rajnish Savaliya
	 */
	public function multiply_importance($score,$field)
	{

		switch ($field)
		{
			case 1:
				$total = $score*3;
				break;
			case 2:
				$total = $score*2;
				break;
			case 3:
				$total = $score*1;
				break;
			default:
				$total = $score;
				break;
		}

		return $total;
	}

	/**
	 * Check User Subscription :: if user is premium user or free
	 * @access private
	 * @return TRUE/FALSE.
	 * @author Rajnish Savaliya
	 */
	public function is_premium_user($user_id,$membership_option_id)
	{
		$is_premium_user = false;
		$prem_user_cond = "user_id = ".$user_id." AND membership_option_id = ".$membership_option_id." AND expiry_date IS NOT NULL AND expiry_date >= CURDATE()";
		if($this->CI->general->count_record($prem_user_cond,'user_membership_option'))
		{
			$is_premium_user = true;
		}
		return $is_premium_user;
	}

	/**
	 * is_app_user() :: Check whether user is used facebook app
	 * @param $user_id: Userid
	 * @return TRUE/FALSE
	 * @author Rajnish Savaliya
	 */
	public function is_app_user($fb_id)
	{
		$prem_user_cond['facebook_id'] = $fb_id;
		if($this->CI->general->count_record($prem_user_cond,'user'))
		{
			//free user
			return true;
		}
		else {
			//premium user have a expiration_date in plan [ != null]
			return false;
		}
	}

	
	public function intro_attendance_upcoming_event($intro_id,$gender_id=1)
	{
		//Check whether user attending event or not
		$fields = array('e.event_id','e.event_name','e.event_start_time');
		$from = 'event as e';
		$joins = array('event_user as eu' => array('eu.event_id  = e.event_id', 'INNER'));
		$where['DATE(e.event_start_time) >='] = SQL_DATE;
		$where['eu.user_id'] = $intro_id;		
		$attend_txt = "";
		if($event_data = $this->CI -> model_user -> multijoins($fields, $from, $joins, $where,'','e.event_start_time asc',1))
		{
			$attend_txt .= $gender_id ==1 ?translate_phrase("He"):translate_phrase("She");
			$attend_txt .= translate_phrase(" will be attending ").'<a href='.base_url() . url_city_name().'/event.html?id='.$event_data['0']['event_id'].'&src=6>'.$event_data['0']['event_name'].translate_phrase(" on ").date(DATE_FORMATE,strtotime($event_data['0']['event_start_time'])).'</a>';
		}
		return $attend_txt;	
	}
	/**
	 * Function pull data through id.
	 * @access Private - Use only this Controller
	 * @param $p_table : Primary Table Name
	 * @param $fk: Foregin Key Value
	 * @return Array - Fetch data
	 * @author Rajnish Savaliya
	 */
	public function load_data_by_id($p_table,$fk=null)
	{
		if($fk)
		{
			$get_condition[$p_table.'_id'] = $fk;
			$get_condition['display_language_id'] = $this->CI->session->userdata('sess_language_id');

			$tmp_info = $this->CI->model_user->get_data($p_table,$get_condition);
			if($tmp_info)
			{
				return $tmp_info['0']['description'];
			}
			else
			return null;
		}
		else
		return null;
	}

	/**
	 * Function find mutual friends from fb_friends table
	 * @access Private - Use only this Controller
	 * @param $user_id: Userid
	 * @return Array - Fetch data
	 * @author Rajnish Savaliya
	 */

	public function datetix_mutual_friend($user_id,$friend_id)
	{
		$user_friends_with_datetix = $this->CI->model_user->get_fb_friends_with_datetix($user_id);
		$cmp_user_friends_with_datetix = $this->CI->model_user->get_fb_friends_with_datetix($friend_id);

		$mutual_friend = array();

		if($cmp_user_friends_with_datetix && $cmp_user_friends_with_datetix)
		{
			foreach ($cmp_user_friends_with_datetix as $friend)
			{
				foreach ($user_friends_with_datetix as $fb_friend)
				{
					if($friend['user_id'] == $fb_friend['user_id'])
					{
						$mutual_friend[] = $friend;
					}
				}
			}
		}

		return $mutual_friend;
	}

	public function fb_mutual_friend($user_id,$friend_id)
	{
		$this->CI->general->set_table('user_fb_friend');
		$friend_list1 = $this->CI->general->get("",array('user_id'=>$user_id));
		$friend_list2 = $this->CI->general->get("",array('user_id'=>$friend_id));

		$mutual_friend = array();

		if($friend_list1 && $friend_list2)
		{
			foreach ($friend_list2 as $friend)
			{
				foreach ($friend_list1 as $fb_friend)
				{
					if($friend['facebook_id'] == $fb_friend['facebook_id'])
					{
						$mutual_friend[] = $friend;
					}
				}
			}
		}
		return $mutual_friend;
	}

	/**
	 * Function is join two tables - chld table and parent table.
	 * @access Private - Use only this Controller
	 * @param $tablename : Primary Table Name
	 * @param $user_id: Userid
	 * @return Array - Fetch data
	 * @author Rajnish Savaliya
	 */
	public function user_want($user_id,$tablename)
	{
		$fields = array('ptble.*','uw.'.$tablename.'_id');
		$from = 'user_want_'.$tablename.' as uw';

		$joins = array(
		$tablename.' as ptble'=>array('ptble.'.$tablename.'_id = uw.'.$tablename.'_id','LEFT')
		);

		//	$this->CI->session->userdata('sess_language_id')	/
		$where['ptble.display_language_id'] = $this->language_id;
		$where['uw.user_id'] = $user_id;

		//ethnicity
		$not_view_order = array('school','company');
		if(in_array($tablename , $not_view_order))
		{
			$temp = $this->CI->model_user->multijoins($fields,$from,$joins,$where,NULL,'ptble.'.$tablename.'_name asc');
		}
		else
		{
			$temp = $this->CI->model_user->multijoins($fields,$from,$joins,$where,NULL,'ptble.view_order asc');
		}

		if($temp)
		{
			return $temp;
		}
		else
		{
			return array();
		}

	}

	/**
	 * Function is send intro mail to user.
	 * @access Private - Use only this Controller
	 * @param $tablename : Primary Table Name
	 * @param $user_id: Userid
	 * @return Array - Fetch data
	 * @author Rajnish Savaliya
	 */
	function intro_mail($user_id=3, $intro_id=1, $match_criteara = array('looks'),$mail_to= '')
	{
		//$set_intros_data['intro_created_time'] = date('Y-m-d h:i:s');
		//$set_intros_data['intro_expiry_time'] =  date("Y-m-d h:i:s", strtotime("+1 month"));

		$this->CI->general->set_table('user_intro');
		if($user_intros = $this->CI->general->custom_get("*",'(user1_id = "'.$user_id.'" AND user2_id="'.$intro_id.'") OR (user1_id="'.$intro_id.'" AND user2_id = "'.$user_id.'") '))
		{
			$set_intros_data = $user_intros['0'];
		}
		else
		{
			echo 'sorry, No such intros';exit;
		}

		$this->language_id = $this->CI->session->userdata('sess_language_id');

		//get data
		$this->CI->general->set_table('user');
		$intros_data = $this->CI->general->get(
		'user.*,CASE
					WHEN
						birth_date != "0000-00-00" 	
					THEN 
						TIMESTAMPDIFF(YEAR,`birth_date`,CURDATE())
				END as intro_age
		',array('user_id'=>$intro_id));
		$intro_user = $intros_data['0'];
		$data['intro_age'] = isset($intro_user['intro_age'])?$intro_user['intro_age']:'';

		$user_data = $this->CI->general->get("",array('user_id'=>$user_id));
		$user = $user_data['0'];

		$user_email_data = $this->CI->model_user->get_user_email($user['user_id']);

		$data['user'] = $user['first_name'];
		$data['intro_with'] = $intro_user['first_name'];
		$pro_noun = '';
		if($intro_user['gender_id'] == 1)
		{
			$data['intro_gender'] = 'He';
			$pro_noun = 'his';
		}
		else
		{
			$data['intro_gender'] = 'She';
			$pro_noun = 'her';
		}
		$subject_line = '';

		$fields = array('sc.school_id','sc.school_name','sc.logo_url','ct.*');
		$from = "user_school as uc";
		$joins = array(
									'school as sc'=>array('uc.school_id = sc.school_id','INNER'),
									'city as ct'=>array('ct.city_id = sc.city_id','INNER')
		);
		unset($condition);
		$condition['uc.user_id'] = $intro_user['user_id'];
		$condition['sc.display_language_id'] = $this->language_id;
		$condition['ct.display_language_id'] = $this->language_id;
		$ordersby = 'sc.school_id asc';

		$school_datas = $this->CI->general->multijoins($fields,$from,$joins,$condition,$ordersby,'array',NULL,2,NULL,'where','school_id');
		if($school_datas)
		{
			$school_arr = array();
			foreach ($school_datas as $value) {
				$school_arr[] = $value['school_name'];
			}

			$data['intro_study'] = 'Studied at '.implode(' and ', $school_arr);
			$subject_line .= 'studied at '.implode(' and ', $school_arr);
		}

		$job_data = $this->get_my_company_data($intro_id);
		if($job_data)
		{
			$data['intro_works'] = translate_phrase('Works');
			$subject_line .= ', works';
			if($job_data['0']['job_function_id'] && isset($job_data['0']['job_function_data']))
			{
				$data['intro_works'] .= translate_phrase(' in ').$job_data['0']['job_function_data']['description'];
				$subject_line .= translate_phrase(' in ').$job_data['0']['job_function_data']['description'];
			}
			else
			{
				$data['intro_works'] .=translate_phrase(' as '). $job_data['0']['job_title'];
				$subject_line .= translate_phrase(' as '). $job_data['0']['job_title'];
			}
			if($job_data['0']['show_company_name'])
			{
				$data['intro_works'] .= translate_phrase(' at ').$job_data['0']['company_name'];
				$subject_line .= translate_phrase(' at ').$job_data['0']['company_name'];
			}
			elseif($job_data['0']['industry_id'] && isset($job_data['0']['industry_description']) && $job_data['0']['industry_description'])
			{
				$data['intro_works'] .= translate_phrase(' in ').$job_data['0']['industry_description'].' '.translate_phrase('industry');
				$subject_line .= translate_phrase(' in ').$job_data['0']['industry_description'].' '.translate_phrase('industry');
			}
			else
			{

			}
		}

		$interestJoins = array('user_interest'=>array('interest.interest_id = user_interest.interest_id','inner'));
		
		 //User Likes
		 $interestJoins = array('user_interest'=>array('interest.interest_id = user_interest.interest_id','inner'));
		 $interestCondition = array('interest.display_language_id'=>1,'user_interest.user_id'=>$user_id);
		 $userInterests = $this->CI->general->multijoins_arr('interest.description as interest','interest',$interestJoins,$interestCondition,'','interest.view_order asc');
		 $consolidatedUsersInterest = array();
		 if(!empty($userInterests))
		 {
			foreach ($userInterests as $key => $value)
			{
			$consolidatedUsersInterest[] = $value['interest'];
			}
			}
			
		
		
		$introCondition = array('interest.display_language_id'=>1,'user_interest.user_id'=>$intro_user['user_id']);
		$introInterests = $this->CI->general->multijoins_arr('interest.description as interest','interest',$interestJoins,$introCondition,'','interest.view_order asc');
		$consolidatedIntroInterest = array();
		if(!empty($introInterests))
		{
			foreach ($introInterests as $key => $value)
			{
				$consolidatedIntroInterest [] = $value['interest'];
			}
		}
		
		$common_interst = array_intersect($consolidatedUsersInterest, $consolidatedIntroInterest);
		if($common_interst)
		{
			$data['intro_common_interest'] = 'Common interest '. implode(', ', $common_interst);
		}
		
		if($consolidatedIntroInterest)
		{
			$common_interest = array_slice($consolidatedIntroInterest,0,5);
			$data['intro_likes'] = 'Likes '. implode(', ', $common_interest);
			if(count($consolidatedIntroInterest) > 5)
			{
				$data['intro_likes'] .='...';
			}
		}

		//Fetch Mutual Friend
		if($user['facebook_id'] && $intro_user['facebook_id'])
		{
			if($mutual_friends  = $this->fb_mutual_friend($user['user_id'],$intro_user['user_id']))
			{
				if (count($mutual_friends)>1)
				{
					$data['intro_fb_friend'] = translate_phrase('Has ').count($mutual_friends).translate_phrase(' common friends with you on Facebook');
				}
				else
				{
					$data['intro_fb_friend'] = translate_phrase('Has ').count($mutual_friends).translate_phrase(' common friend with you on Facebook');
				}
			}
		}

		//Find Photos..
		if($photo_cnt = $this->CI->general->count_record(array('user_id'=>$intro_user['user_id']),"user_photo"))
		{
			$data['intro_photo'] = translate_phrase('Has '.$photo_cnt.' photos in '.$pro_noun.' profile');
		}

		$age_string = '';
		if($data['intro_age'] != '')
		$age_string = $data['intro_age'].translate_phrase(' years old');

		if($this->CI->session->userdata('type') == 'expire_intro_reminder')
		{
			$subject = $user['first_name'].', '.translate_phrase('Your introduction with ').$intro_user['first_name'].translate_phrase(' is expiring in less than 24 hours');
			$data['first_line'] = translate_phrase('Your introduction with ').$intro_user['first_name'].translate_phrase(' is expiring in less than 24 hours');
			
		}
		else{
			$subject = $user['first_name'].', meet '.$intro_user['first_name'].', '.$age_string.', '.$subject_line ;
			$data['first_line'] = translate_phrase('We are excited to introduce ').$intro_user['first_name'].translate_phrase(' to you:');
		}
		
		$data['backLink'] = base_url().'user/user_info/'.$this->CI->utility->encode($intro_user['user_id']).'/'.$this->CI->utility->encode($user['user_id']).'/'.$user['password'];
		$data['email'] = $user_email_data['email_address'];
		//$data['email'] = 'mikeye27@gmail.com';
		$data['match_with_intro'] = $match_criteara;

		$this->CI->load->model('model_city');
		$user_city = $this->CI->model_city->get($user['current_city_id'], $this->language_id );

		$data['intro_expiry_date'] = ' at '.date('g:ia',strtotime($set_intros_data['intro_expiry_time'])).' '.$user_city->time_zone_desc.' on '.date('F j, Y',strtotime($set_intros_data['intro_expiry_time']));

		$this->CI->general->set_table('user_intro');
		if($is_intro_already_exist = $this->CI -> general -> custom_get("*", '(user1_id = "' . $user_id . '" AND user2_id = "' . $intro_id . '") OR (user1_id = "' . $intro_id . '" AND user2_id = "' . $user_id . '")'))
		{
			if($this->CI->general->update(array('intro_email_sent'=>'1'),array('user_intro_id'=>$is_intro_already_exist['0']['user_intro_id'])))
			{
				$email_template = $this->CI->load->view('email/user_intro',$data,true);
				$this->mail_to_user($data['email'],$subject,$email_template);				
			}
		}
	}

	//////////////////////////////////////////////////
	//												//
	//		Delete IT 								//
	//												//
	//////////////////////////////////////////////////


	function intro_mail_debug($user_id=3, $intro_id=1, $match_criteara = array('looks'),$mail_to = '')
	{
		//$set_intros_data['intro_created_time'] = date('Y-m-d h:i:s');
		//$set_intros_data['intro_expiry_time'] =  date("Y-m-d h:i:s", strtotime("+1 month"));

		$this->CI->general->set_table('user_intro');
		if($user_intros = $this->CI->general->custom_get("*",'(user1_id = "'.$user_id.'" AND user2_id="'.$intro_id.'") OR (user1_id="'.$intro_id.'" AND user2_id = "'.$user_id.'") '))
		{
			$set_intros_data = $user_intros['0'];
		}
		else
		{
			echo 'sorry, No such intros';exit;
		}

		//get data
		$this->CI->general->set_table('user');
		$intros_data = $this->CI->general->get(
		'user.*,CASE
					WHEN
						birth_date != "0000-00-00" 	
					THEN 
						TIMESTAMPDIFF(YEAR,`birth_date`,CURDATE())
				END as intro_age
		',array('user_id'=>$intro_id));
		$intro_user = $intros_data['0'];
		$data['intro_age'] = isset($intro_user['intro_age'])?$intro_user['intro_age']:'';

		$user_data = $this->CI->general->get("",array('user_id'=>$user_id));
		$user = $user_data['0'];

		$user_email_data = $this->CI->model_user->get_user_email($user['user_id']);

		$data['user'] = $user['first_name'];
		$data['intro_with'] = $intro_user['first_name'];
		if($intro_user['gender_id'] == 1)
		{
			$data['intro_gender'] = 'He';
			$pro_noun = 'his';
		}
		else
		{
			$data['intro_gender'] = 'She';
			$pro_noun = 'her';
		}
		
		$subject_line = '';
		$fields = array('sc.school_id','sc.school_name','sc.logo_url','ct.*');
		$from = "user_school as uc";
		$joins = array(
									'school as sc'=>array('uc.school_id = sc.school_id','INNER'),
									'city as ct'=>array('ct.city_id = sc.city_id','INNER')
		);
		unset($condition);
		$condition['uc.user_id'] = $intro_user['user_id'];
		$condition['sc.display_language_id'] = $this->language_id;
		$condition['ct.display_language_id'] = $this->language_id;
		$ordersby = 'sc.school_id asc';

		$school_datas = $this->CI->general->multijoins($fields,$from,$joins,$condition,$ordersby,'array',NULL,2,NULL,'where','school_id');
		if($school_datas)
		{
			$school_arr = array();
			foreach ($school_datas as $value) {
				$school_arr[] = $value['school_name'];
			}

			$data['intro_study'] = 'Studied at '.implode(' and ', $school_arr);
			$subject_line .= 'studied at '.implode(' and ', $school_arr);
		}

		$job_data = $this->get_my_company_data($intro_id);
		if($job_data)
		{
			$data['intro_works'] = translate_phrase('Works as ');
			if($job_data['0']['job_function_id'] && isset($job_data['0']['job_function_data']))
			{
				$data['intro_works'] .= $job_data['0']['job_function_data']['description'];
			}
			else
			{
				$data['intro_works'] .= $job_data['0']['job_title'];
			}
			if($job_data['0']['show_company_name'])
			{
				$data['intro_works'] .= translate_phrase(' at ').$job_data['0']['company_name'];
			}
			elseif($job_data['0']['industry_id'] && isset($job_data['0']['industry_description']) && $job_data['0']['industry_description'])
			{
				$data['intro_works'] .= translate_phrase(' in ').$job_data['0']['industry_description'].' '.translate_phrase('industry');
			}
			else
			{

			}
			$subject_line .= ', '.$data['intro_works'];
		}

		 //User Likes
		 $interestJoins = array('user_interest'=>array('interest.interest_id = user_interest.interest_id','inner'));
		 $interestCondition = array('interest.display_language_id'=>1,'user_interest.user_id'=>$user_id);
		 $userInterests = $this->CI->general->multijoins_arr('interest.description as interest','interest',$interestJoins,$interestCondition,'','interest.view_order asc');
		 $consolidatedUsersInterest = array();
		 if(!empty($userInterests))
		 {
			foreach ($userInterests as $key => $value)
			{
			$consolidatedUsersInterest[] = $value['interest'];
			}
			}
		$interestJoins = array('user_interest'=>array('interest.interest_id = user_interest.interest_id','inner'));
		$introCondition = array('interest.display_language_id'=>1,'user_interest.user_id'=>$intro_user['user_id']);
		$introInterests = $this->CI->general->multijoins_arr('interest.description as interest','interest',$interestJoins,$introCondition,'','interest.view_order asc');


		$consolidatedIntroInterest = array();
		if(!empty($introInterests))
		{
			foreach ($introInterests as $key => $value)
			{
				$consolidatedIntroInterest [] = $value['interest'];
			}
		}
		
		/*
		echo "<pre> common :: ";print_r();
		echo "<pre> userInterest:";print_r($consolidatedUsersInterest);
		echo "<pre> introInterests ";print_r($consolidatedIntroInterest );
		*/
		$common_interst = array_intersect($consolidatedUsersInterest, $consolidatedIntroInterest);
		if($common_interst)
		{
			$data['intro_common_interest'] = 'Common interest '. implode(', ', $common_interst);
		}
		if($consolidatedIntroInterest)
		{
			$common_interest = array_slice($consolidatedIntroInterest,0,5);
			$data['intro_likes'] = 'Likes '. implode(', ', $common_interest);
			if(count($consolidatedIntroInterest) > 5)
			{
				$data['intro_likes'] .='...';
			}
		}

		//Fetch Mutual Friend
		if($user['facebook_id'] && $intro_user['facebook_id'])
		{
			if($mutual_friends  = $this->fb_mutual_friend($user['user_id'],$intro_user['user_id']))
			{
				$data['intro_fb_friend'] = 'Has '.count($mutual_friends).' common Facebook friends with you';
			}

			/*
			 $mutual_friends = $this->CI->facebook->api('/'.$user['facebook_id'].'/mutualfriends/'.$intro_user['facebook_id']);
			 if($mutual_friends['data'])
			 {
				$data['intro_fb_friend'] = 'Has '.count($mutual_friends['data']).' common Facebook friends with you';
				}
				*/
		}

		//Find Photos..
		if($photo_cnt = $this->CI->general->count_record(array('user_id'=>$intro_user['user_id']),"user_photo"))
		{
			$data['intro_photo'] = translate_phrase('Has '.$photo_cnt.' photos in '.$pro_noun.' profile');
		}

		$age_string = '';
		if($data['intro_age'] != '')
		$age_string = $data['intro_age'].translate_phrase(' years old');

		if($this->CI->session->userdata('type') == 'expire_intro_reminder')
		{
			$subject = $user['first_name'].', '.translate_phrase('Your introduction with ').$intro_user['first_name'].translate_phrase(' is expiring in less than 24 hours');
			$data['first_line'] = translate_phrase('Your introduction with ').$intro_user['first_name'].translate_phrase(' is expiring in less than 24 hours');
			
		}
		else{
			$subject = $user['first_name'].', meet '.$intro_user['first_name'].', '.$age_string.', '.$subject_line ;
			$data['first_line'] = translate_phrase('We are excited to introduce ').$intro_user['first_name'].translate_phrase(' to you:');
		}
		echo '<br/>Subject Line : '.$subject;
		$data['backLink'] = base_url().'user/user_info/'.$this->CI->utility->encode($intro_user['user_id']).'/'.$this->CI->utility->encode($user['user_id']).'/'.$user['password'];

		$data['email'] = $user_email_data['email_address'];
		$data['match_with_intro'] = $match_criteara;

		$this->CI->load->model('model_city');
		$user_city = $this->CI->model_city->get($user['current_city_id'], $this->language_id );

		$data['intro_expiry_date'] = ' at '.date('g:ia',strtotime($set_intros_data['intro_expiry_time'])).' '.$user_city->time_zone_desc.' on '.date('F j, Y',strtotime($set_intros_data['intro_expiry_time']));
		echo $set_intros_data['intro_expiry_time'];
			
		$this->CI->general->set_table('user_intro');
			
		if($is_intro_already_exist = $this->CI -> general -> custom_get("*", '(user1_id = "' . $user_id . '" AND user2_id = "' . $intro_id . '") OR (user1_id = "' . $intro_id . '" AND user2_id = "' . $user_id . '")'))
		{
			
			echo $email_template = $this->CI->load->view('email/user_intro',$data,true);		
			if($this->mail_to_user_debug($data['email'],$subject,$email_template))
			{
				echo '<br/><strong>'.$user['first_name'].' '.$user['last_name'].' is introduced to '.$intro_user['first_name'].' '.$intro_user['last_name'].'</strong>';
			}
				
			if($this->CI->general->update(array('intro_email_sent'=>'1'),array('user_intro_id'=>$is_intro_already_exist['0']['user_intro_id'])))
			{
				
			}				
		}
		else
		{
			echo "<br/> Sorry Intro already exist.";
		}	
	}
}
// END Datetix class
/* End of file Datetix.php */
/* Location: ./application/libraries/Datetix */
?>
