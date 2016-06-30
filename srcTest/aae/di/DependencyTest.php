<?php
namespace aae\di {
	require_once strstr(__FILE__, 'Test', true).'/aae/autoload/AutoLoader.php';
	class DependencyTest extends \PHPUnit_Framework_TestCase {
		use \aae\unitTesting\TestFilesTrait;
        public $sut;

        public function setUp() {
            parent::setUp();
            // $serializer = new \aae\serialize\JSON();
            // $fileSerializer = new \aae\serialize\FileSerializer($serializer);
            $this->sut = new Dependency();
        }
        public function test_set_no_args() {
            # Given
            $depName    = "serializer";
            $definition = '{
    "class": "aae\/serialize\/Json",
    "static": true
}';
            $json       = '{"'.$depName.'":'.$definition.'}';
            $assoc      = json_decode($json, true);
            # When
            $result = $this->sut->set($depName, $assoc)->json();

            # Then
            $this->assertEquals($definition, $result);
        }
        public function test_set_direct_args() {
            # Given
            $depName    = "serializer";
            $definition = '{
    "class": "aae\/serialize\/Json",
    "static": true,
    "args": [
        false,
        true
    ]
}';
            $json       = '{"'.$depName.'":'.$definition.'}';
            $assoc      = json_decode($json, true);
            # When
            $result = $this->sut->set($depName, $assoc)->json();

            # Then
            $this->assertEquals($definition, $result);
        }
        public function test_set_dependencies_as_args() {
            # Given
            $depName    = "serializer";
            $definition = '{
    "class": "aae\/serialize\/Json",
    "static": true,
    "args": [
        {
            "dep": "template"
        },
        true
    ]
}';
            $json       = '{"'.$depName.'":'.$definition.',"template": {"class": "another"}}';
            $assoc      = json_decode($json, true);
            # When
            $result = $this->sut->set($depName, $assoc)->json();

            # Then
            $this->assertEquals($definition, $result);
        }

        public function test_set_dep_is_string() {
            # Given
            $depName    = "aDir";
            $definition = "protected/anconaesselmann/ui";
            $json       = '{"'.$depName.'":"'.$definition.'"}';
            $assoc      = json_decode($json, true);
            # When
            $result = $this->sut->set($depName, $assoc)->json();

            # Then
            $this->assertEquals($definition, $result);
        }
	}
}