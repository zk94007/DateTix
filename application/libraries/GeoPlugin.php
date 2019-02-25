<?php
/*
 This PHP class is free software: you can redistribute it and/or modify
 the code under the terms of the GNU General Public License as published by
 the Free Software Foundation, either version 3 of the License, or
 (at your option) any later version.

 However, the license header, copyright and author credits
 must not be modified in any form and always be displayed.

 This class is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 @author geoPlugin (gp_support@geoplugin.com)
 @copyright Copyright geoPlugin (gp_support@geoplugin.com)
 $version 1.01


 This PHP class uses the PHP Webservice of http://www.geoplugin.com/ to geolocate IP addresses

 Geographical location of the IP address (visitor) and locate currency (symbol, code and exchange rate) are returned.

 See http://www.geoplugin.com/webservices/php for more specific details of this free service

 */

class GeoPlugin {

	//the geoPlugin server
	var $host = 'http://www.geoplugin.net/php.gp?ip={IP}';

	//the default base currency
	var $currency = 'USD';

	//initiate the geoPlugin vars
	var $ip = null;
	var $city = null;
	var $region = null;
	var $areaCode = null;
	var $dmaCode = null;
	var $countryCode = null;
	var $countryName = null;
	var $continentCode = null;
	var $latitute = null;
	var $longitude = null;
	var $currencyCode = null;
	var $currencySymbol = null;
	var $currencyConverter = null;
	/**
	 * Construct variables
	 *
	 */
	function __construct() {

	}
	function geoPlugin() {

	}

	function locate($ip = null) {

		global $_SERVER;
		if ( is_null( $ip ) ) {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		//$ip = '122.170.119.109';
		$host = str_replace( '{IP}', $ip, $this->host );
		//$host = str_replace( '{CURRENCY}', $this->currency, $host );
		/*
		$data = array();

		$response = $this->fetch($host);
		$data = unserialize($response);

		//set the geoPlugin vars
		$this->ip = $ip;
		$this->city = isset($data['geoplugin_city']) ? $data['geoplugin_city'] : null;
		$this->region = isset($data['geoplugin_region']) ? $data['geoplugin_region'] : null;
		$this->areaCode = isset($data['geoplugin_areaCode']) ? $data['geoplugin_areaCode'] : null;
		$this->dmaCode = isset($data['geoplugin_dmaCode']) ? $data['geoplugin_dmaCode'] : null;
		$this->countryCode = isset($data['geoplugin_countryCode']) ? $data['geoplugin_countryCode'] : null;
		$this->countryName = isset($data['geoplugin_countryName']) ? $data['geoplugin_countryName'] : null;
		$this->continentCode = isset($data['geoplugin_continentCode']) ? $data['geoplugin_continentCode'] : null;
		$this->latitude = isset($data['geoplugin_latitude']) ? $data['geoplugin_latitude'] : null;
		$this->longitude = isset($data['geoplugin_longitude']) ? $data['geoplugin_longitude'] : null;
		$this->currencyCode = isset($data['geoplugin_currencyCode']) ? $data['geoplugin_currencyCode'] : null;
		$this->currencySymbol = isset($data['geoplugin_currencySymbol']) ? $data['geoplugin_currencySymbol'] : null;
		$this->currencyConverter = isset($data['geoplugin_currencyConverter']) ? $data['geoplugin_currencyConverter'] : null;
		*/
		//open tag

		$tags = get_meta_tags('http://www.geobytes.com/IpLocator.htm?GetLocation&template=php3.txt&IpAddress='.$ip);
		$this->city = isset($tags['city'])?$tags['city']:'';
		$this->countryName = isset($tags['country'])?$tags['country']:'';
		$this->latitude = isset($tags['latitude'])?$tags['latitude']:'';
		$this->longitude = isset($tags['longitude'])?$tags['longitude']:'';

	}

	function fetch($host) {

		if ( function_exists('curl_init') ) {

			//use cURL to fetch data
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $host);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_USERAGENT, 'geoPlugin PHP Class v1.0');
			$response = curl_exec($ch);
			curl_close ($ch);

		} else if ( ini_get('allow_url_fopen') ) {

			//fall back to fopen()
			$response = file_get_contents($host, 'r');

		} else {

			trigger_error ('geoPlugin class Error: Cannot retrieve data. Either compile PHP with cURL support or enable allow_url_fopen in php.ini ', E_USER_ERROR);
			return;

		}

		return $response;
	}

	function convert($amount, $float=2, $symbol=true) {

		//easily convert amounts to geolocated currency.
		if ( !is_numeric($this->currencyConverter) || $this->currencyConverter == 0 ) {
			trigger_error('geoPlugin class Notice: currencyConverter has no value.', E_USER_NOTICE);
			return $amount;
		}
		if ( !is_numeric($amount) ) {
			trigger_error ('geoPlugin class Warning: The amount passed to geoPlugin::convert is not numeric.', E_USER_WARNING);
			return $amount;
		}
		if ( $symbol === true ) {
			return $this->currencySymbol . round( ($amount * $this->currencyConverter), $float );
		} else {
			return round( ($amount * $this->currencyConverter), $float );
		}
	}
	function latitudelongitude($radius=100, $limit=null,$limit=200) {

		return $this->longitude."|".$this->latitude;

	}
	function nearby($radius=100, $limit=null,$limit=200) {
		if ( !is_numeric($this->latitude) || !is_numeric($this->longitude) ) {
			$this->latitude=9.879389;
			$this->longitude=76.283569;
			/*trigger_error ('geoPlugin class Warning: Incorrect latitude or longitude values.', E_USER_NOTICE);
			 return array( array() );*/
		}
			
		$host = "http://www.geoplugin.net/extras/nearby.gp?lat=" . $this->latitude . "&long=" . $this->longitude . "&limit={$limit}&radius=";

		if ( is_numeric($limit) )
		$host .= "&limit={$limit}";
			
		return unserialize( $this->fetch($host) );

	}

}

?>
