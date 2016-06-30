<?php
namespace aae\ui {
	require_once strstr(__FILE__, 'Test', true).'/aae/autoload/AutoLoader.php';
	class LocalizerTest extends \PHPUnit_Framework_TestCase {
		use \aae\unitTesting\TestFilesTrait;

		public function test___construct_with_serializer_and_directory() {
			$serializer = new \aae\serialize\FileSerializer(new \aae\serialize\Json());
			$baseDir = $this->getTestDataPath();
			$obj = new Localizer($serializer, $baseDir);
		}

		public function test_localize() {
			# Given the string name of a localization string
			$serializer = new \aae\serialize\FileSerializer(new \aae\serialize\Json());
			$baseDir = $this->getTestDataPath();
			$language = "eng";
			$fileName = "file1.json";
			$stringName = "string1";
			$obj = new Localizer($serializer, $baseDir);

			# When localize is called
			$result = $obj->localize($stringName, $fileName, $language);

			# Then the localized string is returned
			$expected = "eng localized string";
			$this->assertEquals($expected, $result);
		}

		public function test_localize_extension_unknown() {
			# Given the string name of a localization string
			$serializer = new \aae\serialize\FileSerializer(new \aae\serialize\Json());
			$baseDir = $this->getTestDataPath();
			$language = "eng";
			$fileName = "file1";
			$stringName = "string1";
			$obj = new Localizer($serializer, $baseDir);

			# When localize is called
			$result = $obj->localize($stringName, $fileName, $language);

			# Then the localized string is returned
			$expected = "eng localized string";
			$this->assertEquals($expected, $result);
		}

		public function test_log_unlocalized_string_requests() {
			# Given the string name of a string with no matching localization
			$serializer = new \aae\serialize\FileSerializer(new \aae\serialize\Json());
			$baseDir = $this->getTestDataPath();
			$language = "eng";
			$fileName = "file1.json";
			$stringName = "noLocalization";
			$logger = new \aae\log\StringLogger();
			$obj = new Localizer($serializer, $baseDir, $logger);

			# When localize is called
			$result = $obj->localize($stringName, $fileName, $language);

			# Then the the string UNLOCALIZED STRING is returned
			# And log-entry has been made
			$expected = "UNLOCALIZED_STRING";
			$this->assertEquals($expected, $result);
			$this->assertRegExp("/No localization for string $stringName in language $language for file $fileName/", $logger->getLog());
		}

	}
}