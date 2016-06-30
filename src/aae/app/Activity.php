<?php
/**
 *
 */
namespace aae\app {
    use \aae\db\FunctionAPI as FAPI;
    use \aae\std\std as std;
    /**
     * @author Axel Ancona Esselmann
     * @package aae\app
     */
    class Activity implements ConnectionInterface {
        private $_storageAPI;

        public function __construct(\aae\db\FunctionAPI $storageAPI) {
            $this->_storageAPI = $storageAPI;
        }
        public function getActivity($user) {
            $this->_storageAPI->setFetchMode(FAPI::FETCH_ASS_ARRAY);
            $activity = $this->_storageAPI->getActivity($user->getEmail());
            $this->_storageAPI->setFetchMode(FAPI::RESET);
            return $activity;
        }
        /*public function getConnectionsActivity($user) {
            $this->_storageAPI->setFetchMode(FAPI::FETCH_ASS_ARRAY);
            $activity = $this->_storageAPI->getConnectionsActivity($user->getEmail());
            $this->_storageAPI->setFetchMode(FAPI::RESET);
            return $activity;
        }

        public function getMessagesActivity($user) {
            $this->_storageAPI->setFetchMode(FAPI::FETCH_ASS_ARRAY);
            $activity = $this->_storageAPI->getMessagesActivity($user->getEmail());
            $this->_storageAPI->setFetchMode(FAPI::RESET);
            return $activity;
        }*/
    }
}