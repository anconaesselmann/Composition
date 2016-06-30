<?php
namespace aae\util {
	require_once strstr(__FILE__, 'Test', true).'/aae/autoload/AutoLoader.php';
	class ControllerHelperTest extends \PHPUnit_Framework_TestCase {
        use \aae\unitTesting\TestFilesTrait;
        public $sut;

        public function setUp() {
            parent::setUp();
            $serializer = new \aae\serialize\JSON();
            $fileSerializer = new \aae\serialize\FileSerializer($serializer);
            $this->sut = new ControllerHelper($fileSerializer);
        }

        public function test_getActions() {
            # Given
            $fileName = $this->getTestDataPath("templateControllers".DIRECTORY_SEPARATOR."SimpleTemplateController.php");
            $this->sut->setFileName($fileName);

            # When
            $result = $this->sut->getActions();

            # Then
            $expected = [
                "Action" => ["func1", "func2"],
                "AuthenticatedAction" => [],
            ];
            $this->assertEquals($expected, $result);
        }
        public function test_getActions_andAuthenticated() {
            # Given
            $fileName = $this->getTestDataPath("templateControllers".DIRECTORY_SEPARATOR."AuthenticatedTemplateController.php");
            $this->sut->setFileName($fileName);

            # When
            $result = $this->sut->getActions();

            # Then
            $expected = [
                "Action" => ["func1", "func2"],
                "AuthenticatedAction" => ["a1", "a2"],
            ];
            $this->assertEquals($expected, $result);
        }
        public function test_getLanguageFileNames() {
            # Given
            $fileName = $this->getTestDataPath("templateControllers".DIRECTORY_SEPARATOR."ATemplateController.php");
            $this->sut->setFileName($fileName);

            # When
            $result = $this->sut->getLanguageFileNames();

            # Then
            $relativeResults = [];
            foreach ($result as $item) {
                $relativeResults[] = strstr($item, "I18n");
            }

            $expected = ["I18n/eng/a.json","I18n/esp/a.json","I18n/fr/a.json","I18n/ger/a.json"];
            $this->assertEquals($expected, $relativeResults);
        }
        public function test_getLanguages() {
            # Given
            $fileName = $this->getTestDataPath("templateControllers".DIRECTORY_SEPARATOR."ATemplateController.php");
            $this->sut->setFileName($fileName);

            # When
            $result = $this->sut->getLanguages();

            # Then
            $expected = ["eng","esp","fr","ger"];
            $this->assertEquals($expected, $result);
        }
        public function test_getLocalizedVarNames() {
            # Given
            $fileName = $this->getTestDataPath("templateControllers".DIRECTORY_SEPARATOR."ATemplateController.php");
            $this->sut->setFileName($fileName);

            # When
            $result = $this->sut->getLocalizedVarNames();

            # Then
            $expected = ["a", "b", "c", "d", "e"];
            $this->assertEquals($expected, $result);
        }

        public function test_getLanguagesVarValues() {
            # Given
            $fileName = $this->getTestDataPath("templateControllers".DIRECTORY_SEPARATOR."ATemplateController.php");
            $this->sut->setFileName($fileName);

            # When
            $result = $this->sut->getLanguagesVarValues();

            # Then
            $expected = [
                "eng" => ["a" => "1", "b" => "2", "c" => "3", "d" => "4", "e" => "5"],
                "esp" => ["a" => "6", "b" => "7", "c" => "8", "d" => "9", "e" => "10"],
                "fr"  => ["a" => "", "b" => "11", "c" => "", "d" => "", "e" => ""],
                "ger" => ["a" => "", "b" => "", "c" => "", "d" => "", "e" => ""]

            ];
            $this->assertEquals($expected, $result);
        }
        public function test_getConfigurations() {
            # Given
            $fileName = $this->getTestDataPath("bootstrap1".DIRECTORY_SEPARATOR);
            $this->sut->setFileName($fileName);

            # When
            $result = $this->sut->getConfigurations();

            # Then
            $this->assertEquals(2, count($result));
            $this->assertEquals($result[0]["name"], "dev");
            $this->assertEquals($result[1]["name"], "web");
            $this->assertEquals($result[0]["content"]["serializer"]["class"], "aae/serialize/Json");
        }
        public function test_getMasterConfiguration() {
            # Given
            $fileName = $this->getTestDataPath("bootstrap2");
            $this->sut->setFileName($fileName);

            # When
            $result = $this->sut->getMasterConfiguration();

            # Then
            $res1 = $result["dep1"]["class/def/1.php"][1]->json();
            $expected = '{
    "class": "class\/def\/1.php",
    "static": false,
    "args": [
        {
            "dep": "dep3"
        },
        true
    ]
}';
            $this->assertEquals($expected, $res1);
        }
        public function test_toggleDependency_remove() {
            # Given
            $fileName = $this->getTestDataPath("bootstrap3".DIRECTORY_SEPARATOR);
            $this->sut->setFileName($fileName);

            $envFileName = $fileName."dev".DIRECTORY_SEPARATOR."config.json";

            file_put_contents($envFileName, '{
    "serializer": {
        "class": "aae/serialize/Json",
        "static": true
    },
    "dep2": "other"
}');

            # When
            $environment = "dev";
            $depName     = "serializer";
            $this->sut->toggleDependency($environment, $depName);

            # Then
            $expected = '{
    "dep2": "other"
}';
            $this->assertEquals($expected, file_get_contents($envFileName));
        }
        /*public function test_toggleDependency_add() {
            # Given
            $fileName = $this->getTestDataPath("bootstrap3".DIRECTORY_SEPARATOR);
            $this->sut->setFileName($fileName);

            $envFileName = $fileName."web".DIRECTORY_SEPARATOR."config.json";

            file_put_contents($envFileName, '{"dep3": "something"}');

            # When
            $environment = "dev";
            $depName     = "dep1";
            $result = $this->sut->toggleDependency($environment, $depName);

            # Then
            $expected = "?";
            $this->assertEquals($expected, $result);
        }*/

	}
}