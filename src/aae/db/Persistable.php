<?php
/**
 *
 */
namespace aae\db {
    /**
     * @author Axel Ancona Esselmann
     * @package aae\db
     */
    use \aae\db\FunctionAPI as FAPI;
    use \aae\std\std as std;
    abstract class Persistable {
        private static $_propertyMapping;
        private static $_updateArray;
        private $_className;
        protected $_db;
        public function __construct(FAPI $storageAPI) {
            $this->_db = $storageAPI;
            $this->_className = std::classFromNSClassName($this);
        }
        public function __destruct() {
            if (!method_exists($this,"create{$this->_className}")) {
                throw new \Exception("'".get_class($this)."' inherits from '".get_class()."' and has to implement create{$this->_className}()", 304162032);
            }
            if (!method_exists($this,"update{$this->_className}")) {
                throw new \Exception("'".get_class($this)."' inherits from '".get_class()."' and has to implement update{$this->_className}()", 304162033);
            }
        }
        abstract public function toArray();
        public function getUpdateArray() {
            if (is_null(self::$_updateArray)) {
                $contRefl = new \ReflectionMethod($this, "update{$this->_className}");
                $params   = $contRefl->getParameters();
                self::$_updateArray = [];
                foreach ($params as $param) self::$_updateArray[$param->name] = NULL;
            }
            return self::$_updateArray;
        }
        public function loadFromId($id) {
            $className    = std::classFromNSClassName($this);
            $functionName = "load{$className}FromId";
            $this->_db->setFetchMode(FAPI::FETCH_ASS_ARRAY | FAPI::FETCH_ONE_ROW);
            $results = $this->_db->$functionName($id);
            $this->hydrate($results);
            return $this;
        }
        public function __call($functionName, $arguments) {
            if (strpos($functionName, "get") !== false) {
                $propertyName = lcfirst(substr($functionName, 3));
                if (property_exists($this, $propertyName)) {
                    return $this->$propertyName;
                }
            }
            if(method_exists($this,$functionName)) {
                return call_user_func_array(array($this,$functionName), $arguments);
            }
            throw new \Exception("Fatal error: Call to undefined method ".get_class($this)."::{$functionName}()".__FILE__." on line", 309161550);
        }
        public function hydrate(&$assoc) {
            if (!is_array($assoc)) $assoc = [];
            if (array_key_exists(0, $assoc)) {
                foreach ($assoc as $key => $hydrateArray) {
                    $obj = clone $this;
                    $obj->hydrate($hydrateArray);
                    $assoc[$key] = $obj;
                }
                return $assoc;
            }
            if (is_null(self::$_propertyMapping)) {
                foreach ($assoc as $colName => $value) {
                    $camel = std::snakeToCamel($colName, true);
                    if (property_exists($this, $camel)) self::$_propertyMapping[$colName] = $camel;
                }
            }
            foreach ($assoc as $colName => $value) {
                if (array_key_exists($colName, self::$_propertyMapping)) {
                    $camel = self::$_propertyMapping[$colName];
                    $this->$camel = $value;
                }
            }
        }
        public function updateWithArray($updatesArgs) {
            $updates = $this->getUpdateArray();
            foreach ($updatesArgs as $key => $value) $updates[$key] = $value;
            return call_user_func_array([$this,"update{$this->_className}"], $updates);
        }
        protected function _setNonNullProperties(/* arguments in same order as in update */) {
            $args = func_get_args();
            $updateArray = $this->getUpdateArray();
            $argNbr = 0;
            foreach ($updateArray as $propertyName => $value) {
                $newValue = $args[$argNbr];
                $this->$propertyName = (!is_null($newValue)) ? $newValue : $this->$propertyName;
                $argNbr++;
            }
        }
        protected function create($args) {
            $functionName = "create{$this->_className}";
            if ($id = (int)call_user_func_array([$this->_db, $functionName], $args)) {
                call_user_func_array([$this, "_setNonNullProperties"], array_merge([$id],$args));
            }
            return $this;
        }
        protected function update($args) {
            $functionName = "update{$this->_className}";
            if ($nbrUpdated = (int)call_user_func_array([$this->_db, $functionName], $args)) {
                call_user_func_array([$this, "_setNonNullProperties"], $args);
            }
            return $this;
        }
        public function delete($postId, $userId) {
            $functionName = "delete{$this->_className}";
            return (bool) $this->_db->$functionName($postId, $userId);
        }
    }
}