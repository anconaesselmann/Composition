<?php
/**
 *
 */
namespace aae\connect {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\connect
	 */
	class CURL implements TransmitterInterface {
		private $_ch = null;

		public function __construct($url) {
			//initialize and setup the curl handler
	        $this->_ch = curl_init();
	        curl_setopt($this->_ch, CURLOPT_RETURNTRANSFER, true);
	        curl_setopt($this->_ch,      CURLOPT_USERAGENT, "Mozilla/5.0 (AAE cURL; 1.0)");
	        curl_setopt($this->_ch,            CURLOPT_URL, $url);
		}
		public function setOpt($optionFlag) {

		}
		public function transmit($params) {
	        curl_setopt($this->_ch,           CURLOPT_POST, count($params));
	        curl_setopt($this->_ch,     CURLOPT_POSTFIELDS, $params);	

	        $response = curl_exec($this->_ch);
	        if (!$response) {
	        	throw new TransmissionException("The Transmission failed", 218141511);
	        }
	        return $response;
		}
	}
}