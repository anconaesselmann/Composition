<?php
namespace aae\persistence {
	require_once strstr(__FILE__, 'Test', true).'/aae/autoload/AutoLoader.php';
	class DataPersisterTest extends \PHPUnit_Framework_TestCase {
		public function test___construct_with_invalid_assArray() {
			$configFileDir = array();
			$authenticationObj = null;
			try {
				$obj = new DataPersister($configFileDir, $authenticationObj);
			} catch (\Exception $e) {
				$this->assertEquals(209141706, $e->getCode());
				return;
			}
			$this->fail("An Exception should have been raised, since the configuration array did not have a 'persistence' array");
		}

		public function test___construct_with_assArray() {
			$configFileDir = array("persistence" => array());
			$authenticationObj = null;
			$obj = new DataPersister($configFileDir, $authenticationObj);
		}

		/**
		 * TEST_DESCRIPTION
		 */
		public function test_persist_adapter_not_defined_in_configuration() {
			// Setup
			$data = $this->getMock("\\aae\\MockData");
			$configArray = array(
				'persistence' => array()
				);
			$authenticationObj = null;
			$obj = new DataPersister($configArray, $authenticationObj);

			$expected = "";
			
		
			// Testing
			try {
				$result = $obj->persist($data);
			} catch (\Exception $e) {
				#print($e->getMessage());
				$this->assertEquals(209141719, $e->getCode());
				return;
			}
			$this->fail("An Exception should have been thrown, since no adapter was specified in the configurations for the data type.");
		}

		public function test_persist_factory_does_not_return_an_adapter() {
			// Setup
			$mockData = $this->getMock("\\aae\\MockData");
			
			$faultyMockAdapter = $this->getMock("\\aae\\persistence\\NOT_AN_AdapterInterface");
    		
			$mockAdapterFactory = $this->getMock('\\aae\\persistence\\AdapterFactory');
			$mockAdapterFactory->expects($this->any())->method('build')->will($this->returnValue($faultyMockAdapter));
    
			$configArray = array(
				'persistence' => array(
					get_class($mockData) => get_class($faultyMockAdapter)
					)
				);
			$authenticationObj = null;
			$obj = new DataPersister($configArray, $authenticationObj);
			$obj->adapterFactory = $mockAdapterFactory;


			// Testing
			try {
				$result = $obj->persist($mockData);
			} catch (\Exception $e) {
				$this->assertEquals(209141836, $e->getCode());
				return;
			}
			$this->fail("Exception shout have been thrown because factory did not return a valid adapter.");
		}

		public function test_persist_without_settings() {
			// Setup
			$mockData = $this->getMock("\\aae\\MockData");
			
			$mockAdapter = $this->getMock("\\aae\\persistence\\AdapterInterface");
			$mockAdapter->expects($this->any())
				->method('persist')
				->with($mockData)
				->will($this->returnValue(true));
    		
			$mockAdapterFactory = $this->getMock('\\aae\\persistence\\AdapterFactory');
			$mockAdapterFactory->expects($this->any())->method('build')->will($this->returnValue($mockAdapter));
    
			$configArray = array(
				'persistence' => array(
					get_class($mockData) => get_class($mockAdapter)
					)
				);
			$authenticationObj = null;
			$obj = new DataPersister($configArray, $authenticationObj);
			$obj->adapterFactory = $mockAdapterFactory;

			$expected = true;
			
		
			// Testing
			$result = $obj->persist($mockData);
			
			// Verification
			$this->assertEquals($expected, $result);
		}

		public function test_persist_with_settings() {
			// Setup
			$mockData = $this->getMock("\\aae\\MockData");
			$mockAdapterSettings = array(
				'dbConfig'        => '/some/folder/dbConfig.ini',
				'someOtherConfig' => true
			);
			$mockAdapter = $this->getMock("\\aae\\persistence\\AdapterInterface");
			$mockAdapter->expects($this->any())
				->method('persist')
				->with($mockData, $mockAdapterSettings)
				->will($this->returnValue(true));
    		
			$mockAdapterFactory = $this->getMock('\\aae\\persistence\\AdapterFactory');
			$mockAdapterFactory->expects($this->any())->method('build')->will($this->returnValue($mockAdapter));
    
			$configArray = array(
				'persistence' => array(
					get_class($mockData) => array(
						'adapter' => array(
							'class'    => get_class($mockAdapter),
							'settings' => $mockAdapterSettings
						)
					)
				)
			);
			$authenticationObj = null;
			$obj = new DataPersister($configArray, $authenticationObj);
			$obj->adapterFactory = $mockAdapterFactory;

			$expected = true;
			
			// Testing
			$result = $obj->persist($mockData);
			
			// Verification
			$this->assertEquals($expected, $result);
		}
		
	}
}