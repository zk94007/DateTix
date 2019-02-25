<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 
if ( session_status() == PHP_SESSION_NONE ) {
    session_start();
}
 
require_once __DIR__ .'/facebook-v4-5.0/src/Facebook/autoload.php';
 
class Facebook_four {
    var $fb;
	var $ci;
 	public function __construct() {
        $this->ci =& get_instance();

		$this->fb = new Facebook\Facebook([
		  'app_id' => $this->ci->config->item('appId'),
		  'app_secret' => $this->ci->config->item('secret'),
		  'default_graph_version' => 'v2.2',
		  ]);		
    }
	
	public function login_url($redirect_url = "")
	{
		$helper = $this->fb->getRedirectLoginHelper();
		$permissions = explode(',', $this->ci->config->item('fb_scope')); // optional
		if($redirect_url == "")
			$redirect_url  = base_url($this->ci->config->item('facebook_login_redirect_url'));

		$loginUrl = $helper->getLoginUrl($redirect_url, $permissions);
		return $loginUrl;		
	}
	
	public function getAccessToken()
	{
		$helper = $this->fb->getRedirectLoginHelper();

		try {
		  $accessToken = $helper->getAccessToken();
		} catch(Facebook\Exceptions\FacebookResponseException $e) {
		  // When Graph returns an error
		  $msg = 'Graph returned an error: ' . $e->getMessage();
		  return $this->fb_response(201,$msg);
		  
		} catch(Facebook\Exceptions\FacebookSDKException $e) {
		  // When validation fails or other local issues
		  $msg = 'Facebook SDK returned an error: ' . $e->getMessage();
		  return $this->fb_response(201,$msg);
		  
		}
		
		if (! isset($accessToken)) {
		  if ($helper->getError()) {
		    header('HTTP/1.0 401 Unauthorized');
		    echo "Error: " . $helper->getError() . "\n";
		    echo "Error Code: " . $helper->getErrorCode() . "\n";
		    echo "Error Reason: " . $helper->getErrorReason() . "\n";
		    echo "Error Description: " . $helper->getErrorDescription() . "\n";
		  } else {
		    header('HTTP/1.0 400 Bad Request');
		    echo 'Bad request';
		  }
		  exit;
		}
		
		
		// The OAuth 2.0 client handler helps us manage access tokens
		$oAuth2Client = $this->fb->getOAuth2Client();
		
		// Get the access token metadata from /debug_token
		$tokenMetadata = $oAuth2Client->debugToken($accessToken);
		
		// Validation (these will throw FacebookSDKException's when they fail)
		$tokenMetadata->validateAppId($this->ci->config->item('appId'));
		
		//$tokenMetadata->validateUserId('123');
		$tokenMetadata->validateExpiration();
		
		if (! $accessToken->isLongLived()) {
		  // Exchanges a short-lived access token for a long-lived one
		  try {
		    $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
		  } catch (Facebook\Exceptions\FacebookSDKException $e) {
		    $msg = "<p>Error getting long-lived access token: " . $helper->getMessage() . "</p>\n\n";
		    return $this->fb_response(201,$msg);		  
		  }		
		}
		
		$this->ci->session->set_userdata('fb_access_token',(string) $accessToken);
	}
	
	function setAccessTocken()
	{
		$token = $this->ci ->session->userdata('fb_access_token');

		if(!$token)
		{
			$this->getAccessToken();
		}
		else {
		
			$this->fb->setDefaultAccessToken($token );
		}
	}
	public function is_login()
	{
		return $this->ci ->session->userdata('fb_access_token')?true:false;
	}
	public function getUser()
	{
		// Sets the default fallback access token so we don't have to pass it to each request
		$this->setAccessTocken();
		try {
		  	
		  $response = $this->fb->get('/me');	
		  $arr = $response->getDecodedBody();	  
		  return $this->fb_response(200,'success',$arr);
		  
		} catch(Facebook\Exceptions\FacebookResponseException $e) {
		  // When Graph returns an error
		  $msg = 'Get User Graph returned an error: ' . $e->getMessage();
		  return $this->fb_response(201,$msg);
		  
		} catch(Facebook\Exceptions\FacebookSDKException $e) {
		  // When validation fails or other local issues
		  $msg = 'Get User FB SDK returned an error: ' . $e->getMessage();
		  return $this->fb_response(201,$msg);		  
		}
	}
	public function get_user_fb_id()
	{
		// Sets the default fallback access token so we don't have to pass it to each request
		$this->setAccessTocken();
		try {
		  	
		  	$response = $this->fb->get('/me');	
			
		  	$node = $response->getGraphObject();
			return $node->getProperty('id');

		} catch(Facebook\Exceptions\FacebookResponseException $e) {
		  // When Graph returns an error
		  $msg = 'Get User Graph returned an error: ' . $e->getMessage();
		  return $this->fb_response(201,$msg);
		  
		} catch(Facebook\Exceptions\FacebookSDKException $e) {
		  // When validation fails or other local issues
		  $msg = 'Get User FB SDK returned an error: ' . $e->getMessage();
		  return $this->fb_response(201,$msg);		  
		}
	}
	
	public function api($getStr)
	{
		// Sets the default fallback access token so we don't have to pass it to each request
		$this->setAccessTocken();
		try {
			  $response = $this->fb->get($getStr);	
			  $arr = $response->getDecodedBody();
			  return $this->fb_response(200,'success',$arr);		  
		} catch(Facebook\Exceptions\FacebookResponseException $e) {
		  // When Graph returns an error
		  $msg = 'Get User Graph returned an error: ' . $e->getMessage();
		  return $this->fb_response(201,$msg);
		  
		} catch(Facebook\Exceptions\FacebookSDKException $e) {
		  // When validation fails or other local issues
		  $msg = 'Get User FB SDK returned an error: ' . $e->getMessage();
		  return $this->fb_response(201,$msg);		  
		}
	}
	
	public function fb_response($code='200',$message="error",$data=array()){
		// Return ID
        $response = array(
            'code'    => $code,
            'message' => $message,
            'data'    => $data
        );
		//echo "<pre>";print_r($response);exit;
		//return $response;
		return $data;
	}
}
 
