<?php
/**
 *
 */
namespace aae\track {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\track
	 */
    class Activity {
        protected $_id;
        protected $_dateTime;
        protected $_track = NULL;
        protected $_userId = NULL;

        public function __construct($id, $dateTime) {
            $this->_id = $id;
            $this->_dateTime = $dateTime;
        }
        public function getId() {
            return $this->_id;
        }
        public function addTrack($track) {
            $this->_track = $track;
        }
        public function getTrack() {
            return $this->_track;
        }
        public function setUserId($id) {
            $this->_userId = $id;
        }
        public function getUserId() {
            return $this->_userId;
        }
        public function setTime(\DateTime $dateTime) {
            $this->_dateTime = $dateTime;
        }
        public function getTime() {
            return $this->_dateTime;
        }
    }
}