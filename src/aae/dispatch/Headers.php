<?php
/**
 *
 */
namespace aae\dispatch {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\dispatch
	 */
    class Header {
        public $name, $value, $replace;
        public function __construct($name, $value, $replace = true) {
            $this->name    = $name;
            $this->value   = $value;
            $this->replace = $replace;
        }
    }
	class Headers {
        private $_headers = [];
		public function set($headerName, $headerValue, $replace = true) {
            $this->_headers[] = new Header($headerName, $headerValue, $replace);
        }
        public function setMultiple($headerArray) {
            foreach ($headerArray as $headerName => $value) {
                $this->set($headerName, $value);
            }
        }
        public function setHttp($header) {
            throw new \Exception("not implemented!", 1);

        }
        public function send() {
            foreach ($this->_headers as $header) {
                $headerString = $header->name.": ".$header->value;
                header($headerString, $header->replace);
            }
        }
	}
}