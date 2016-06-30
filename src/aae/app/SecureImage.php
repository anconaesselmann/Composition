<?php
/**
 *
 */
namespace aae\app {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\app
	 */
	class SecureImage extends Image {
        public $headers;
        protected $_baseUrl = "";

		public function __construct(\aae\db\FunctionAPI $storageAPI, $location, $headers) {
            parent::__construct($storageAPI, $location);
            $this->headers = $headers;
        }

        public function get($fileName) {
            $completeFileName = null;
            try {
                $completeFileName = $this->getFullPath($fileName);
            } catch (\Exception $e) {
                if (!is_file($completeFileName)) $completeFileName = dirname(__FILE__).DIRECTORY_SEPARATOR.basename(__FILE__, ".php")."Data".DIRECTORY_SEPARATOR."resourceUnavailable.jpeg";
            }
            if (!is_readable($completeFileName)) $completeFileName = dirname(__FILE__).DIRECTORY_SEPARATOR.basename(__FILE__, ".php")."Data".DIRECTORY_SEPARATOR."permissionDenied.jpeg";

            $this->headers->set("Expires", "Mon, 26 Jul 1997 05:00:00 GMT");
            $this->headers->set("Cache-Control", "no-store, no-cache,must-revalidate");
            $this->headers->set("Cache-Control", "post-check=0, pre-check=0", false);
            $this->headers->set("Pragma", "no-cache");
            $this->headers->set("Content-type", "image/jpeg");
            $this->headers->send();

            readfile($completeFileName);
        }

        public function setBaseUrl($baseUrl) {
            $this->_baseUrl = $baseUrl;
        }
        public function getUrl($imageId) {
            return $this->_baseUrl.$imageId.".jpg";
        }
    }
}