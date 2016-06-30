<?php
namespace aae\util {
	require_once strstr(__FILE__, 'Test', true).'/aae/autoload/AutoLoader.php';
	class PhpClassHelperTest extends \PHPUnit_Framework_TestCase {
        use \aae\unitTesting\TestFilesTrait;
        public $sut;

        public function setUp() {
            parent::setUp();
            $this->sut = new PhpClassHelper();
        }

        public function test_getFunctions() {
            # Given
            $fileName = $this->getTestDataPath("SimplePhpClass.php");
            $this->sut->setFileName($fileName);

            # When getFunctions is called
            $result = $this->sut->getFunctions();

            # Then
            $expected = ["__construct", "__toString", "func1", "func2"];
            $this->assertEquals($expected, $result);
        }

	}
}