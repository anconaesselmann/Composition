<?php
/**
 *
 */
namespace aae\persistence\adapters\db\connections {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\persistence\adapters\db\connections
	 */
	interface DBConnectionInterface {
		public function getConnection($dbConfig);
		public function setLogger($loggerInstance);
	}
}