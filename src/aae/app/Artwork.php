<?php
/**
 *
 */
namespace aae\app {
    use \aae\db\FunctionAPI as FAPI;
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\app
	 */
	class Artwork {
		protected $_storageAPI;

        public function __construct(\aae\db\FunctionAPI $storageAPI) {
            $this->_storageAPI = $storageAPI;
        }
        public function setArtwork($id, $title, $materials, $date) {
            return (int)$this->_storageAPI->setArtwork($id, $title, $materials, $date);
        }
        public function getTitle($id) {
            $result = $this->_storageAPI->getArtworkTitle($id);
            return (is_null($result)) ? "" : $result;
        }
        public function getMaterials($id) {
            $result = $this->_storageAPI->getArtworkMaterials($id);
            return (is_null($result)) ? "" : $result;
        }
        public function getDate($id) {
            $result = $this->_storageAPI->getArtworkDate($id);
            return (is_null($result)) ? "" : $result;
        }
        public function getAll() {
            $this->_storageAPI->setFetchMode(FAPI::FETCH_ASS_ARRAY);
            $result = $this->_storageAPI->getAll();
            $this->_storageAPI->setFetchMode(FAPI::RESET);
            return $result;
        }
	}
}