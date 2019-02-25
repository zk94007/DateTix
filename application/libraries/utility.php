<?php
ini_set('default_charset', 'UTF-8');
class Utility{

	//default construction
	var $skey 	= "ncodetechnologies.com/rajnish"; // you can change it

	public function __construct(){
		$CI =& get_instance();
	}

	public  function safe_b64encode($string) {

		$data = base64_encode($string);
		$data = str_replace(array('+','/','='),array('-','_',''),$data);
		return $data;
	}

	public function safe_b64decode($string) {
		$data = str_replace(array('-','_'),array('+','/'),$string);
		$mod4 = strlen($data) % 4;
		if ($mod4) {
			$data .= substr('====', $mod4);
		}
		return base64_decode($data);
	}

	public  function encode($value){

		if(!$value){return false;}
		$text = $value;
		$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
		$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
		$crypttext = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $this->skey, $text, MCRYPT_MODE_ECB, $iv);
		return trim($this->safe_b64encode($crypttext));
	}

	public function decode($value){

		if(!$value){return false;}
		$crypttext = $this->safe_b64decode($value);
		$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
		$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
		$decrypttext = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $this->skey, $crypttext, MCRYPT_MODE_ECB, $iv);
		return trim($decrypttext);
	}

	//encrypt values
	function encryptWord($word){
		return addslashes($word);
	}
	//descrypt values
	function decryptWord($word){
		return stripcslashes($word);
	}
	function dateFromat($date,$format)
	{
		return date($format,strtotime($date));
	}

	//	public function uniqueCode($tableName,$fieldName){
	public function uniqueCode($random_id_length = 10){
		$stamp = date("Ymdhis");
		$ip = $_SERVER['REMOTE_ADDR'];
		$orderid = "$stamp-$ip";
		$orderid = str_replace(".", "", "$orderid");//CODE--------1
		
		//set the random id length
		
		//generate a random id encrypt it and store it in $rnd_id
		$rnd_id = crypt(uniqid(rand(),1));
		
		//to remove any slashes that might have come
		$rnd_id = strip_tags(stripslashes($rnd_id));
		
		//Removing any . or / and reversing the string
		$rnd_id = str_replace(".","",$rnd_id);
		$rnd_id = strrev(str_replace("/","",$rnd_id));
		
		//finally I take the first 10 characters from the $rnd_id
		$rnd_id = substr($rnd_id,0,$random_id_length);//CODE--------2;
		
		//addition of the two code (CODE 1 + CODE 2);
		return $code = $orderid.$rnd_id;
	}

	function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}
}
?>