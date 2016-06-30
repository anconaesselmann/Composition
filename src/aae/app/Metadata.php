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
	/*class Metadata {
        public function __construct(FAPI $storageAPI, $dataName) {
            $this->_storageAPI = $storageAPI;
            $this->_dataName   = $dataName;
        }
        public function createMetadataTable() {
            $this->_storageAPI->setFetchMode(FAPI::FETCH_ASS_ARRAY | FAPI::FETCH_ONE_ROW);
            $result = $this->_storageAPI->createMetadataTable($this->_dataName);
            $this->_storageAPI->setFetchMode(FAPI::RESET);
            return $result;
        }
        public function create($user, $other) {
            $tableName = $this->_dataName."_metadata";
            $tableName = $this->_dataName."_metadata";
            // try {
                $result = $this->_storageAPI->createMetadata($user, $tableName, $other);
            // } catch (\Exception $e) {
            //     $this->createMetadataTable();
            //     $result = $this->_storageAPI->createMetadata($user, $tableName, $other);
            // }
            if (array_key_exists("meta_id", $result)) {
                return $result["meta_id"];
            }

        }
        public function get($user, $id) {
            $tableName = $this->_dataName."_metadata";
            return $this->_storageAPI->getMetadata($user, $tableName, $id);
        }
    }*/
}