<?php
/**
 * All rights reserved Axel Ancona Esselmann
 */
namespace aae\db {
	/**
	 * Maps function calls from application layer to stored function
	 * and stored procedure calls in storage layer.
	 *
	 * @author Axel Ancona Esselmann
	 * @package aae\db
	 */
	class FunctionAPI /*implements StorageAPI*/ {
		private $_dbName, $_pdo, $_fetchMode = 0, $_debug = false, $_evalSubstitutions = array('aae\app\User' => "getId", 'DateTime' => ["format", 'Y-m-d H:i:s']);
		public $echoQuery = false;

		const RESET           = 0, //notmulrow  | is array | is array or []
		      FETCH_NUM_ARRAY = 3, // 0         |  1       |   1
		      FETCH_ASS_ARRAY = 1, // 0         |  0       |   1
		      FETCH_ONE_ROW   = 4, // 1         |  0       |   0
		      //---------------------------------------------------------\\
		      IS_ARRAY        = 1, // 0         |  0       |   1
		      IS_NUM_ARRAY    = 2; // 0         |  1       |   0

		/**
		 * Accepts a bit flag. Combine class constants:
		 *     RESET
		 *     FETCH_NUM_ARRAY
		 *     FETCH_ASS_ARRAY
		 *     FETCH_ONE_ROW
		 *
		 * Make sure to reset the fetch mode when done.
		 *
		 * @param bitFlag $fetchMode
		 */
		public function setFetchMode($fetchMode) {
			$this->_fetchMode = $fetchMode;
		}
		public function setFetchAssoc() {
			$this->_fetchMode = self::FETCH_ASS_ARRAY;
		}

		public function resetFetchMode() {
			$this->_fetchMode = self::RESET;
		}

		public function __construct(\PDO $pdo, $options = NULL) {
			$this->_pdo    = $pdo;
			$this->_dbName = (is_array($options) && array_key_exists("dbName", $options)) ? $options["dbName"]      : NULL;
			$this->_debug  = (is_array($options) && array_key_exists("debug",  $options)) ? (bool)$options["debug"] : false;
			$this->echoQuery = (is_array($options) && array_key_exists("echoQuery",  $options)) ? (bool)$options["echoQuery"] : false;
		}

		public function __call($functionName, $arguments) {
			$callString = ($this->_fetchMode & 1) ? "CALL" : "SELECT";
			$result     = $this->_makeCall($functionName, $arguments, $callString);
			return $result;
		}
		/**
		 * An eval substitution allows for passing of objects to the function api. When it is time to pass the instance
		 * to the database, an evaluation callback is called on the object to get database representation for the instance.
		 * @param string $className    the class name for the object that supports eval substitution
		 * @param string $callbackName the name of the function that is called on the instance
		 */
		public function addEvalSubstitution($className, $callbackName) {
			$this->_evalSubstitutions[$className] = $callbackName;
		}
		private function _performArgumentSubstitution($argument) {
			if      (is_string($argument)) $argument = $this->_pdo->quote($argument);
			else if (is_null(  $argument)) $argument = "NULL";
			else if ($argument === true)   $argument = 'TRUE';
			else if ($argument === false)  $argument = 'FALSE';
			else if (is_object($argument)) {
				if (array_key_exists(get_class($argument), $this->_evalSubstitutions)) {
					$evalFunctionName = $this->_evalSubstitutions[get_class($argument)];
					$className        = $argument;
					if (is_array($evalFunctionName)) {
						$evalArguments    = $evalFunctionName;
						$evalFunctionName = array_shift($evalArguments);
						$argument         = call_user_func_array([$className, $evalFunctionName], $evalArguments);
					} else {
						$argument = $className->$evalFunctionName();
					}
					$argument = $this->_performArgumentSubstitution($argument);
				} else throw new StorageAPIException("FunctionAPI has no eval substitution for an object of type '".get_class($arguments[$i])."'.", 226151433);
			}
			return $argument;
		}
		private function _makeCall($functionName, $arguments, $callType) {
			$this->_pdo->beginTransaction();
			$dbName = (!is_null($this->_dbName)) ? $this->_dbName."." : "";
			$query  = $callType." ".$dbName.$functionName."(";
			for ($i=0; $i < count($arguments); $i++) {
				$arguments[$i] = $this->_performArgumentSubstitution($arguments[$i]);
			}
			$query .= implode(", ", $arguments).")";
			// @codeCoverageIgnoreStart
			if ($this->echoQuery) echo "Last query:<br /><span style='color:red;font-weight: bold;'>$query</span><br /><br />";
			// @codeCoverageIgnoreEnd
			try {
				$pdoStatement = $this->_pdo->query($query);
				$result       = $this->_fetch($pdoStatement);
			} catch (\Exception $e) {
				if ($this->_debug) {
					$querySummary = "<br /><br />Last query:<br /><span style='color:red;font-weight: bold;'>$query</span><br />";
				} else $querySummary = "";
				throw new StorageAPIException("Database Error with message:\n".$e->getMessage().$querySummary, 1015141104);
			}
			$pdoStatement->closeCursor();
			$this->_pdo->commit();
			return $result;
		}

		private function _fetch($pdoStatement) {
			if (!$pdoStatement) {
				if ($this->_debug) {
					$errorinfo = ": <br />\n".implode($this->_pdo->errorInfo(), "<br />\n");
				} else $errorinfo = "";
				throw new StorageAPIException("Database Error".$errorinfo, 1015141105);
			}
			if ($this->_fetchMode & self::IS_ARRAY) {
				$fetchMode = ($this->_fetchMode & self::IS_NUM_ARRAY)
					? \PDO::FETCH_NUM
					: \PDO::FETCH_ASSOC;
				$pdoStatement->setFetchMode($fetchMode);
				$result = array();

				$row = $pdoStatement->fetch();
				if ($this->_fetchMode & self::FETCH_ONE_ROW) {
					$result = $row;
				} else {
					if (count($row) == 1 && ($this->_fetchMode & self::IS_NUM_ARRAY)) {
						while ($row) {
							$result[] = $row[0];
							$row      = $pdoStatement->fetch();
						}
					} else {
						while ($row) {
							$result[] = $row;
							$row      = $pdoStatement->fetch();
						}
					}
				}
			} else {
				$result = $pdoStatement->fetchColumn();
			}

			return $result;
		}
	}
}