<?php
namespace aae\util {
    require_once strstr(__FILE__, 'Test', true).'/aae/autoload/AutoLoader.php';
    \aae\autoload\Autoloader::addDir(dirname(__FILE__).DIRECTORY_SEPARATOR."BootstrapperTestData");

    /**
     * @group database
     */
    // class BootstrapperTest extends \aae\unitTesting\DbTestCase {
    //     public $sut;

    //     public function setUp() {
    //         parent::setUp();
    //         $this->sut = new Bootstrapper($this->getTestDataPath('config.cnf'));
    //     }

    //     /*public function test_intallMySQLComponent() {
    //         # Given
    //         $className = '\a\b\ClassB';
    //         # When
    //         $result = $this->sut->intallMySQLComponent($className);

    //         # Then
    //         $this->assertTableHas("works2",["id"=>123]);
    //     }*/
    //     public function test_intallMySQLComponent_with_setup_json() {
    //         # Given
    //         $className = '\a\b\c\ClassA';
    //         # When
    //         $result = $this->sut->intallMySQLComponent($className);

    //         # Then
    //         $this->assertTableHas("works",["id"=>5]);
    //         $this->assertTableHas("works2",["id"=>999]);
    //         $this->assertTableHas("works2",["id"=>123]);
    //     }
    //     public function test_intallMySQLComponent_run_file_twice_should_not_be_possible() {
    //         # Given
    //         $className = '\a\b\ClassB';
    //         # When
    //         $result = $this->sut->intallMySQLComponent($className);
    //         $this->assertTrue($result);
    //         $result = $this->sut->intallMySQLComponent($className);
    //         $this->assertFalse($result);
    //     }
    //     public function test_unIntallMySQLComponent_with_setup_json() {
    //         # Given
    //         $className = '\a\b\c\ClassA';
    //         # When
    //         $result = $this->sut->intallMySQLComponent($className);

    //         # Then
    //         $this->assertTableHas("works",["id"=>5]);
    //         $this->assertTableHas("works2",["id"=>999]);
    //         $this->assertTableHas("works2",["id"=>123]);

    //         $result = $this->sut->unIntallMySQLComponent($className);
    //         $removed1 = false;
    //         $removed2 = false;
    //         try {
    //             $this->showTable("works");
    //         } catch (\Exception $e) {
    //             $removed1 = true;
    //         }
    //         try {
    //             $this->showTable("works2");
    //         } catch (\Exception $e) {
    //             $removed2 = true;
    //         }
    //         if (!($removed1 && $removed2)) $this->fail("remove.sql script not called.");
    //     }
    // }
}