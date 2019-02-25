<?php
function get_assets($type="",$url="")
{
/* TESTING ALL DATA */

	if($type!="")
	{
		//$domain_name = $_SERVER['SERVER_NAME'];
$domain_name = strtolower(preg_replace('#^www\.(.+\.)#i', '$1', $_SERVER['SERVER_NAME']));
		//echo $domain_name;exit;

		if ($domain_name=='datetix.hk')
		{
			$domain_name=='datetix.com';
		}

		$CI =& get_instance();
		$CI->load->model('general_model');
		$CI->general_model->set_table('website');		
		if($website_data = $CI->general_model->get($type,array('domain'=>$domain_name)))
		{
			if($website_data['0'][$type])
				$url  = $website_data['0'][$type];
		}
	}
	return $url;	
}
function page_name($name = '') {
	$realname = $name;
	$seoname = preg_replace('/\%/', ' percentage', $realname);
	$seoname = preg_replace('/\@/', ' at ', $seoname);
	$seoname = preg_replace('/\&/', ' and ', $seoname);
	$seoname = preg_replace('/\s[\s]+/', '-', $seoname);  // Strip off multiple spaces
	$seoname = preg_replace('/[\s\W]+/', '-', $seoname);  // Strip off spaces and non-alpha-numeric
	$seoname = preg_replace('/^[\-]+/', '', $seoname);  // Strip off the starting hyphens
	$seoname = preg_replace('/[\-]+$/', '', $seoname);  // Strip off the ending hyphens
	$seoname = strtolower($seoname);
	return $seoname;
}
function getStarSign($date="")
{
	$date = date('md',$date);

	$sign = '';
	if(321 <= $date && $date <= 419)
	{//March 21 to April 19
		$sign = 'Aries';
		//echo date('F d',$date);exit;
	}

	if(420 <= $date && $date <= 520)
	{//April 20 to May 20
		$sign = 'Taurus';
		//echo date('F d',$date);exit;
	}

	if(521<= $date && $date <= 620)
	{//May 21 to June 20
		$sign = 'Gemini';
	}

	if(621<= $date && $date <= 722)
	{//June 21 to July 22
		$sign = 'Cancer';
	}

	if(723<= $date && $date <= 822)
	{//July 23 to August 22
		$sign = 'Leo';
	}

	if(823<= $date && $date <= 922)
	{//August 23 to Sept. 22
		$sign = 'Virgo';
	}

	if(923<= $date && $date <= 1022)
	{//Sept. 23 to October 22
		$sign = 'Libra';
	}
	if(1023<= $date && $date <= 1121)
	{//October 23 to Nov. 21
		$sign = 'Scorpio';
		//echo date('F d',$date);exit;
	}


	if(1122<= $date && $date <= 1221)
	{//Nov. 22 to Dec. 21
		$sign = 'Sagittarius';
		//echo date('F d',$date);exit;
	}

	if(1222<= $date && $date <= 119)
	{//Dec. 22 to January 19
		$sign = 'Capricorn';
		//echo date('F d',$date);exit;
	}

	if(120<= $date && $date <= 218)
	{
		$sign = 'Aquarius';
		//echo date('F d',$date);exit;
	}

	if(219<= $date && $date <= 320)
	{
		$sign = 'Pisces';
		//echo date('F d',$date);exit;
	}
	return $sign;

	//
	/*
	echo date('F d',$date);exit;
	echo "<pre>";print_r($date);exit;
	$zodiac[356] = "Capricorn";
	$zodiac[326] = "Sagittarius";
	$zodiac[296] = "Scorpio";
	$zodiac[266] = "Libra";
	$zodiac[235] = "Virgo";
	$zodiac[203] = "Leo";
	$zodiac[172] = "Cancer";
	$zodiac[140] = "Gemini";
	$zodiac[111] = "Taurus";
	$zodiac[78]  = "Aries";
	$zodiac[51]  = "Pisces";
	$zodiac[20]  = "Aquarius";
	$zodiac[0]   = "Capricorn";
	if (!$date) $date = time();
	$dayOfTheYear = date("z",$date);

	$isLeapYear = date("L",$date);

	if ($isLeapYear && ($dayOfTheYear > 59))
	$dayOfTheYear = $dayOfTheYear - 1;

	foreach($zodiac as $day => $sign)
	{
	if ($dayOfTheYear > $day)
	break;
	}
	return $sign;
	*/
}
function get_sections() {
	$section_array = array("page_content" => "Page content",
        "box_content" => "Box content",
        "article" => "Article",
        "news" => "News",
        "blog" => "Blog");
	return $section_array;
}

function get_section_input_fields() {
	$common_input_fields = array("title", "meta_description", "meta_keywords", "content");
	$section_input_fields["page_content"] = $common_input_fields;
	$section_input_fields["box_content"] = array("title", "content");
	$section_input_fields["article"] = $common_input_fields;
	$section_input_fields["news"] = array("title", "content");
	$section_input_fields["blog"] = array("title", "content");
	return $section_input_fields;
}

function get_languages() {
	$language_array = array("en" => "English",
        "zh-CN" => "中文(繁體)",
        "zh-CN" => "中文(简体)",
        "ja" => "日本語",
        "ko" => "한국어",
        "fr"=>"Français",
        "de"=>"Deutsch",
        "it"=>"Italiano");
	return $language_array;
}

function get_all_languages() {
	$language_array = array("en" => "English",
        "zh-CN" => "中文(繁體)",
        "zh-CN" => "中文(简体)",
        "ja" => "日本語",
        "ko" => "한국어",
        "fr"=>"Français",
        "de"=>"Deutsch",
        "it"=>"Italiano");
	return $language_array;
}

function get_root_path() {
	$root_path = $_SERVER["DOCUMENT_ROOT"] . "/common/";
	//    $root_path = $_SERVER["DOCUMENT_ROOT"]."/";
	return $root_path;
}

function translate_phrase($phrase) {

	$ci = &get_instance();
	$language_id = $ci->session->userdata('sess_language_id');
	if (!$language_id)
	$language_id = 1;
	if ($language_id == 1)
	return $phrase;
	if (!$phrase)
	return $phrase;

	$ci->load->model('model_translation');
	$translation = $ci->model_translation->get_translation($language_id, $phrase);
	return $translation;
}

function query_string() {
	$data = array();
	foreach ($_GET as $key => $value) {
		if ($value != '') {
			$data[$key] = $value;
		}
	}
	$query_string = http_build_query($data);
	return ($query_string == '') ? '' : '?' . $query_string;
}
function is_active($link, $page){
	return ($link==$page) ? 'class="active_menu"' : '';
}
/**
 * To display languages in lanugage bar
 * @return string
 */
function language_bar()
{
	$data = '';

	$CI =& get_instance();
	$CI->load->model('model_language');
	$CI->load->model('model_city');

	$country  = $CI->model_city->getCountryByCity(current_city_id());
	$lanugage = array();
	if($display_lang_ids = get_assets('display_language_ids'))
	{
		$CI->db->select('l.*');
         	$CI->db->order_by('l.view_order', 'asc');
		$CI->db->where_in('display_language_id',explode(",", $display_lang_ids));
	    $q = $CI->db->get('display_language l', 3);

		$lanugage = ($q->num_rows() > 0) ? $q->result() : array() ;
		
	}
	
	if(empty($lanugage))
	{
		$lanugage = $CI->model_language->get_lanuages_by_country($country['country_id']);
	}
 	
	$url_city_name = url_city_name();
	
	$base_url  = base_url() . $url_city_name;
	$return_to = return_url();
	if($return_to == $url_city_name)
	{
		$return_to = 'index.html';
	}
	
	if(count($lanugage) > 1)
	{
		foreach ($lanugage as $lang) {
			$data .= '<a href="' . $base_url . '/switch-language.html?lang_id=' . $lang->display_language_id . '&return_to=' . $return_to . '">' . $lang->description . '</a>';
		}
		return $data;
	}
	else
	{
		return "";
	}
}

function user_country_id()
{
	$CI =& get_instance();
	$CI->load->model('model_city');

	$country  = $CI->model_city->getCountryByCity(current_city_id());
	return $country['currency_id'];
}
function url_city_name($city = '')
{
	if (!$city) {
		$city = get_current_city(1);
	}

	$city = strtolower($city);
	//$city = str_replace('&', '-and-', $city);
	$city = str_replace('&', 'and', $city);
	$city = trim(preg_replace('/[^\w\d_ -]/si', '', $city));//remove all illegal chars
	//$city = str_replace(' ', '-', $city);
	$city = str_replace(' ', '', $city);
	//$city = str_replace('--', '-', $city);
	$city = str_replace('--', '', $city);

	$normChars = array(
        'Š'=>'S', 'š'=>'s', 'Ð'=>'Dj','Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 
        'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E', 'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 
        'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U', 
        'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss','à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 
        'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 
        'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 
        'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y', 'ƒ'=>'f'
        );

        return strtr($city, $normChars);
}

function get_current_city($lang_id = '')
{
	$CI =& get_instance();
	$CI->load->model('model_city');

	if (!$lang_id) {
		$lang_id = $CI->session->userdata('sess_language_id');
	}

	$city = $CI->model_city->get(current_city_id(), $lang_id);
	return $city->description;
}

/**
 * calculate_current_time() :: calculatate time based on provided input
 * @access public
 * @return SQL FORMATE DATE AND TIME
 * @author Rajnish Savaliya
 */
function calculate_current_time($val=0,$extra = '')
{
	$sign = '+';
	if($val < 0)
	{
		$sign = '-';
	}
	$val = abs($val);
	$zone_difference = '';
	if($arr = explode('.', $val ))
	{
		if(isset($arr['0']) && $arr['0'])
		{
			$zone_difference .= $sign.$arr['0'].' hour ';
		}

		if(isset($arr['1']) && $arr['1'])
		{
			$zone_difference .= $sign.($arr['1']*6).' minute';
		}
	}

	return  date("Y-m-d H:i:s", strtotime($extra.' '.$zone_difference , time()));

}
function current_city_id()
{
	$CI =& get_instance();

	if (!$CI->session->userdata('sess_city_id')) {
		$CI->session->set_userdata('sess_city_id', $CI->config->item('default_city'));
	}
	$CI->session->userdata('sess_city_id');
	return $CI->session->userdata('sess_city_id');
}

function get_currency_in_usd($amount = 0, $currency_id="0")
{
	$usd_amount = $amount;
	$CI =& get_instance();
	$CI->load->model('general_model');

	$CI->general_model->set_table('currency');
	if($currency_data = $CI->general_model->get("*",array('display_language_id'=>1,'currency_id'=>$currency_id)))
	{
		$usd_amount *= $currency_data['0']['rate'];
	}
	return $usd_amount;
}

function return_url()
{

	$CI =& get_instance();
	$CI->session->set_userdata('extra_para',$_SERVER['QUERY_STRING']);

	$uri = uri_string();
	$split = explode('/', $uri, 2);

	return urlencode(isset($split[1])?$split[1]:$split[0]);
}

function get_query_string()
{
	substr(strrchr($_SERVER['REQUEST_URI'], "?"), 1);
}

function get_query_str_array()
{
	parse_str(substr(strrchr($_SERVER['REQUEST_URI'], "?"), 1), $_GET);
	return $_GET;
}
function formate_mobile_number($number = 0)
{
	if(ctype_digit($number) && strlen($number) == 8) {
	  $number = substr($number, 0, 4) .'-'.
	            substr($number, 4, 4) ;
	}
	return $number ;
}

function format_chat_time($timestamp=""){
	$formated_timestam = $timestamp;
	if(date('Ymd') == date('Ymd', strtotime($timestamp)))
	{
		$formated_timestam = date('H:i', strtotime($timestamp));
	}
	elseif(date('Ymd',strtotime('yesterday')) == date('Ymd',strtotime($timestamp))){
		$formated_timestam = 'Yesterday';
		
	}
	elseif(strtotime('yesterday') > strtotime($timestamp) && strtotime($timestamp) > (strtotime('today')-(6*24* 60 * 60))){
		$formated_timestam = date('D', strtotime($timestamp));		
	}
	else{
		$formated_timestam = date('m/d/Y', strtotime($timestamp));
	}
	
	/*
	 * if user_intro_chat.message_sent_time = today {
	   [time]='hh:mm';
	}
	else if user_intro_chat.message_sent_time = yesterday {
	   [time]='Yesterday';
	}
	else if user_intro_chat.message_sent_time <= 6 days ago {
	  [time]='Mon|Tue|Wed|Thu|Fri|Sat|Sun';
	}
	else
	{
	  [time]='mm/dd/yyyy';
	}
	 */
	 return $formated_timestam;
}
function generate_link($intro_id)
{
	$CI =& get_instance();
	$CI ->general->set_table('user');
	$user_id = $CI->session->userdata('user_id');
	$user_data = $CI ->general->get("user_id,password",array('user_id'=>$user_id));
	$user_data = $user_data['0'];

	$user_link = $CI ->utility->encode($user_data['user_id']);
	if($user_data['password']){
		$user_link.'/'.$user_data['password'];
	}
	return base_url().'home/mail_action/'.$user_link.'?return_to='.base_url().url_city_name().'/my-date.html&type=message&redirect_intro_id='.$intro_id;
}
