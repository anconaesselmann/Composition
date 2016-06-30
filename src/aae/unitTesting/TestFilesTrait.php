<?php
/**
 *
 */
namespace aae\unitTesting {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\unitTesting
	 */
	trait TestFilesTrait {
		private $_mockTemplate = NULL;
		protected function getTestDataPath($fileName = false) {
			$reflectedClass = new \ReflectionObject($this);
			$reflectedClassFileName = $reflectedClass->getFileName();
			$path = dirname($reflectedClassFileName)."/".(substr(strrchr(get_class($this), "\\"), 1))."Data";
			if (is_string($fileName)) $path .= "/".$fileName;
			return $path;
		}
		protected function getCommonTestDataPath($fileName = false) {
			$speciffic = $this->getTestDataPath($fileName);
			$path = dirname(dirname($speciffic)).DIRECTORY_SEPARATOR."TestData";
			if (is_string($fileName)) $path .= "/".$fileName;
			return $path;
		}
		protected function getCommonTestDataContent($fileName) {
			return file_get_contents($this->getCommonTestDataPath($fileName));
		}
		protected function getTestDataContent($fileName) {
			return file_get_contents($this->getTestDataPath($fileName));
		}
		protected function getClassDataPath($fileName = false) {
			$testDataPath = $this->getTestDataPath($fileName);
			return str_replace("Test", "", $testDataPath);
		}
		protected function getClassDataContent($fileName) {
			return file_get_contents($this->getClassDataPath($fileName));
		}
		public function assertAssocContainsAssoc($assoc1, $assoc2) {
			$errorMessage = "";
			foreach ($assoc2 as $key => $value) {
				if (!array_key_exists($key, $assoc1)) $errorMessage .= "The associative array does not contain key '$key'.\n";
				if ($value != $assoc1[$key]) $errorMessage .= "\"$key\"=>$value does not match expected \"$key\"=>{$assoc1[$key]}.\n";
			}
			if (strlen($errorMessage) > 0) $this->fail($errorMessage);
		}
		public function assertTemplateContains($varName, $value) {
			if (is_null($this->_mockTemplate)) throw new \Exception("Pass mock template to controller. Call getMockTemplate to get an instance.", 209151225);
			$this->assertEquals($value, $this->_mockTemplate[$varName]);
		}
		public function getMockTemplate() {
			if (is_null($this->_mockTemplate)) {
				$this->_mockTemplate = new MockTemplate();
			}
			return $this->_mockTemplate;
		}
		public function authenticatedViewControllerSetUp() {
            $this->_doc        = $this->getMockTemplate();
            $this->_user       = $this->getMockBuilder('\aae\app\User')
                ->disableOriginalConstructor()
                ->getMock();
            $this->_session    = $this->getMockBuilder('\aae\app\Session')
                ->disableOriginalConstructor()
                ->getMock();
            $className = strstr(get_class($this), "Test", true);
            $this->sut = new $className(
                $this->_doc,
                $this->_user,
                $this->_session
            );
        }
        public function userIsLoggedIn($status = true) {
        	$this->_user
                ->expects($this->once())
                ->method('isLoggedIn')
                ->willReturn($status);
        }

        public function assertConnection(
            $actionName,
            $actionVars,
            $templateVar,
            $resourceName,
            $functionName,
            $expectedFunctionArgs = NULL
        ) {
        	$testValue = "aString1234";
            $this->_assertConnectionHelper($testValue, $actionName, $actionVars, $resourceName, $functionName, $expectedFunctionArgs);
            if ($testValue !== $this->_mockTemplate[$templateVar]) $this->fail("Template resource '$templateVar' received no value.");
            $this->assertEquals($testValue, $this->_mockTemplate[$templateVar]);
        }
        public function assertArrayConnection(
            $actionName,
            $actionVars,
            $resourceName,
            $functionName,
            $expectedFunctionArgs = NULL
        ) {
        	$templateVar = "var";
            $testValue = [$templateVar => "aString1234"];
            $this->_assertConnectionHelper($testValue, $actionName, $actionVars, $resourceName, $functionName, $expectedFunctionArgs);
            if ($testValue[$templateVar] !== $this->_mockTemplate[$templateVar]) $this->fail("Template resource '$templateVar' received no value.");
            $this->assertEquals($testValue[$templateVar], $this->_mockTemplate[$templateVar]);
        }

        private function _assertConnectionHelper(
        	$testValue,
            $actionName,
            $actionVars,
            $resourceName,
            $functionName,
            $expectedFunctionArgs = NULL
        ) {
        	$resource = $this->$resourceName;
            if (is_null($resource)) $this->fail("Resource '$resourceName' does not exist.");
            if (!method_exists($resource, $functionName)) $this->fail("Resource '$resourceName' has no function named '$functionName.'");
            $method = $resource
                ->expects($this->once())
                ->method($functionName);
            if (!is_null($expectedFunctionArgs)) {
                if (!is_array($expectedFunctionArgs)) $expectedFunctionArgs = [$expectedFunctionArgs];
                call_user_func_array(array($method, "with"), $expectedFunctionArgs);
            }
            $method->willReturn($testValue);

            if (count($actionVars) < 1) $this->sut->$actionName();
            else call_user_func_array(array($this->sut, $actionName), $actionVars);

            if (is_null($this->_mockTemplate)) throw new \Exception("Pass mock template to controller. Call getMockTemplate to get an instance.", 209151225);
        }
	}
	class MockTemplate implements \ArrayAccess, \aae\ui\TemplateInterface {
		private $_outputAssoc = [];

		public function offsetSet($offset, $value) {
            if (is_null($offset)) throw new \Exception("Provide an offset add to the template.", 1208141630);
            $this->_outputAssoc[$offset] = $value;
        }
        public function offsetExists($offset) {
            return isset($this->_outputAssoc[$offset]);
        }
        public function offsetUnset($offset) {
            unset($this->_outputAssoc[$offset]);
        }
        public function offsetGet($offset) {
            return $this->_outputAssoc[$offset];
        }
        public function arraySet($array) {
            if (!is_array($array)) throw new \Exception("arraySet expects an array of arguments", 204151756);
            foreach ($array as $key => $value) {
                $this[$key] = $value;
            }
        }

        public function load($templateName = null) {}
        public function loadFromFile($path) {}
        public function setLocalizer($localizer) {}
        public function getBaseDir() {}
	}
}