<?php

class Paypal {
   /**
    * Last error message(s)
    * @var array
    */
   protected $_errors = array();

   /**
    * API Credentials
    * Use the correct credentials for the environment in use (Live / Sandbox)
    * @var array
    */
   protected $_credentials = array(
      'USER' => '',
      'PWD' => '',
      'SIGNATURE' => '',
      'APPID' => ''
   );

   /**
    * API endpoint
    * Live - https://api-3t.paypal.com/nvp
    * Sandbox - https://api-3t.sandbox.paypal.com/nvp
    * @var string
    */
   protected $_endPoint = '';

   /**
    * API Version
    * @var string
    */
   protected $_version = '74.0';


   
   public function __construct($apiUsername, $apiPassword, $apiSignature, $sandboxMode = TRUE, $endPoint = FALSE, $appID = FALSE) {
   		$this->_credentials['USER'] = $apiUsername;
   		$this->_credentials['PWD'] = $apiPassword;
   		$this->_credentials['SIGNATURE'] = $apiSignature;
		$this->_credentials['APPID'] = ($appID ? $appID : '');
		
   		if (!$endPoint) {
	   		if ($sandboxMode) {
	   			$this->_endPoint = 'https://api-3t.sandbox.paypal.com/nvp';
	   		} else {
	   			$this->_endPoint = 'https://api-3t.paypal.com/nvp';
	   		}
   		} else {
   			$this->_endPoint = $endPoint;
   		}
   		
   		if ($sandboxMode) {
   			$this->_sandboxMode = true;
   		} else {
   			$this->_sandboxMode = false;
   		}
   }

   /**
    * Make API request
    *
    * @param string $method string API method to request
    * @param array $params Additional request parameters
    * @return array / boolean Response array / boolean false on failure
    */
   public function request($method,$params = array()) {
      $this -> _errors = array();


	  if ($this->_credentials['APPID']) {
	  	// adaptive
	      if( empty($method) ) { //Check if API method is not empty
	         $this -> _errors = array('API method is missing');
	         return false;
	      } else {
	      	$this->_endPoint .= $method;
		  }	  	
	  	$request = http_build_query($params);
	  } else {
	  	  // not adaptive
	      if( empty($method) ) { //Check if API method is not empty
	         $this -> _errors = array('API method is missing');
	         return false;
	      }	  	
	      //Our request parameters
	      $requestParams = array(
	         'METHOD' => $method,
	         'VERSION' => $this -> _version
	      ) + $this -> _credentials;
	
	      //Building our NVP string
	      $request = http_build_query($requestParams + $params);
	  }
	  
      //cURL settings
      $curlOptions = array (
      	 CURLOPT_HTTPHEADER => ($this->_credentials['APPID'] ? array("X-PAYPAL-SECURITY-USERID: {$this->_credentials['USER']}", "X-PAYPAL-SECURITY-PASSWORD: {$this->_credentials['PWD']}", "X-PAYPAL-SECURITY-SIGNATURE: {$this->_credentials['SIGNATURE']}", "X-PAYPAL-APPLICATION-ID: {$this->_credentials['APPID']}", 'X-PAYPAL-REQUEST-DATA-FORMAT: NV' , 'X-PAYPAL-RESPONSE-DATA-FORMAT: NV') : ''),
         CURLOPT_URL => $this -> _endPoint,
         CURLOPT_VERBOSE => 1,
         //CURLOPT_SSL_VERIFYPEER => true,
         //CURLOPT_SSL_VERIFYHOST => 2,
         //CURLOPT_CAINFO => dirname(__FILE__) . '/cacert.pem', //CA cert file
         CURLOPT_RETURNTRANSFER => 1,
         CURLOPT_POST => 1,
         CURLOPT_POSTFIELDS => $request
      );

      if (!$this->_credentials['APPID']) unset($curlOptions[CURLOPT_HTTPHEADER]);

      $ch = curl_init();
      curl_setopt_array($ch,$curlOptions);

      //Sending our request - $response will hold the API response
      $response = curl_exec($ch);

      //Checking for cURL errors
      if (curl_errno($ch)) {
         $this -> _errors = curl_error($ch);
         curl_close($ch);
         return false;
         //Handle errors
      } else  {
         curl_close($ch);
         $responseArray = array();
         parse_str($response,$responseArray); // Break the NVP string to an array
         return $responseArray;
      }
   }

   public function validateIPN() {
		if ($this->_sandboxMode) {
			$this->_endPoint = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
		} else {
			$this->_endPoint = 'https://www.paypal.com/cgi-bin/webscr';
		}
		
      $curlOptions = array (
      	CURLOPT_HEADER => 0,
      	CURLOPT_TIMEOUT => 30,
         CURLOPT_URL => $this -> _endPoint,
         CURLOPT_VERBOSE => 1,
         CURLOPT_SSL_VERIFYPEER => 0,
         //CURLOPT_SSL_VERIFYHOST => 2,
         //CURLOPT_CAINFO => dirname(__FILE__) . '/cacert.pem', //CA cert file
         CURLOPT_RETURNTRANSFER => 1,
         CURLOPT_POST => 1,
         CURLOPT_POSTFIELDS => 'cmd=_notify-validate&' . @file_get_contents('php://input')
      );

      $ch = curl_init();
      curl_setopt_array($ch,$curlOptions);
      $response = curl_exec($ch);

      $log = new Log("paypal.log");
	  $log->write('Notify-validate: ' . print_r($curlOptions, true) . print_r($response, true));

      if (curl_errno($ch)) {
         $this->_errors = curl_error($ch);
         curl_close($ch);
         return false;
      } else  {
         curl_close($ch);
         if ($response == 'VERIFIED') {
         	return true;
         } else {
         	return false;
         }
      }		
   }
   
   public function getErrors() {
      return $this->_errors;
   }
   
   
	function decodePayPalIPN($raw_post) {
		if (empty($raw_post)) return array();
	
		$post = array();
		$pairs = explode('&', $raw_post);
		
		foreach ($pairs as $pair) {
			list($key, $value) = explode('=', $pair, 2);
			$key = urldecode($key);
			$value = urldecode($value);
			preg_match('/(\w+)(?:\[(\d+)\])?(?:\.(\w+))?/', $key, $key_parts);
			
			switch (count($key_parts)) {
				case 4:
					if (!isset($post[$key_parts[1]])) {
					$post[$key_parts[1]] = array($key_parts[2] => array($key_parts[3] => $value));
					} else if (!isset($post[$key_parts[1]][$key_parts[2]])) {
					$post[$key_parts[1]][$key_parts[2]] = array($key_parts[3] => $value);
					} else {
					$post[$key_parts[1]][$key_parts[2]][$key_parts[3]] = $value;
					}
					break;
					
				case 3:
					if (!isset($post[$key_parts[1]])) {
					$post[$key_parts[1]] = array();
					}
					$post[$key_parts[1]][$key_parts[2]] = $value;
					break;
					
				default:
					$post[$key] = $value;
					break;
			}
		}
		return $post;
	}
}

?>