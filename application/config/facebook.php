<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/*
|-----------------------------------
|  FACEBOOK APP ID/API KEY [ Rajnish ]
|-----------------------------------
|
| Your app id/api key generated.
|
*/
$config['fb_scope'] = 'public_profile,user_friends,email,user_about_me,user_birthday,user_education_history,user_hometown,user_location,user_photos,user_relationships,user_relationship_details,user_work_history';

$config['facebook_login_type']          = 'web';
$config['facebook_login_redirect_url']  = 'fb_login/fb_success';
$config['facebook_logout_redirect_url'] = '';

$config['page_id'] = '332162926881078';
if($_SERVER['SERVER_NAME'] == 'localhost')
{
	//localhost	
	$config['appId'] = '583807995019661';
	$config['secret'] = '6cd843ec35975190a5fe1bd447293aca';		
}
elseif($_SERVER['SERVER_NAME'] == '10.16.16.5' || $_SERVER['SERVER_NAME'] == '122.170.119.109')
{
	//demo server
	$config['appId'] = '385715091558247';
	$config['secret'] = 'c494fbd603975489442116236b3dfd3a';	
}
elseif($_SERVER['SERVER_NAME'] == 'datetix.com' || $_SERVER['SERVER_NAME'] == 'www.datetix.com' || $_SERVER['SERVER_NAME'] == 'datetix.hk' || $_SERVER['SERVER_NAME'] == 'www.datetix.hk')
{
	//print $_SERVER['SERVER_NAME'] ;
	$config['appId'] = '171435559687111';
	$config['secret'] = 'a1118ff01f224b814353412da0482cad';	
	//$config['appId']    = '60314897637xxxx';
	//$config['secret']    = '380c2697b979b039ef3452336b2xxxxx';
	
	//$config['appId'] = get_assets('facebook_app_id','171435559687111');
	//$config['secret'] = get_assets('facebook_app_secret','a1118ff01f224b814353412da0482cad');	
			
}
elseif($_SERVER['SERVER_NAME'] == 'smartimematching.hk' || $_SERVER['SERVER_NAME'] == 'www.smartimematching.hk')
{
	//print $_SERVER['SERVER_NAME'] ;
	//$config['appId'] = '718901118221145';
	//$config['secret'] = 'c6cc514acd1c9b345dc3aed6adee0803';	

	$config['appId'] = '171435559687111';
	$config['secret'] = 'a1118ff01f224b814353412da0482cad';	
}
else
{
	//print $_SERVER['SERVER_NAME'] ;
	$config['appId'] = '171435559687111';
	$config['secret'] = 'a1118ff01f224b814353412da0482cad';	
}


