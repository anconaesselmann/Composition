<?php
/*namespace aae\ui {
	require_once strstr(__FILE__, 'Test', true).'/aae/autoload/AutoLoader.php';
	class TemplateTest extends \PHPUnit_Framework_TestCase {
		use \aae\unitTesting\TestFilesTrait;

		public function test_insertTextAtClass_replace_content() {
			$templateDir = $this->getTestDataPath("template.html");
			$string = "test string";

			$template = new Template();
			$template->loadFromFile($templateDir);

			$template->insertTextAtClass($string, "item");


			$result = strval($template);
			$this->assertRegExp("/<div.*class=\".*\sitem\s.*>test string<\/div>/",$result);
		}
		public function test_insertTextAtClass_append_new_content() {
			$templateDir = $this->getTestDataPath("template.html");
			$string = "test string";

			$template = new Template();
			$template->loadFromFile($templateDir);

			$template->insertTextAtClass($string, "item", false);


			$result = strval($template);
			$this->assertRegExp("/<div.*class=\".*\sitem\s.*>old stringtest string<\/div>/",$result);
		}
		public function test_insertTextAtClass_exception_class_name_does_not_exist() {
			$templateDir = $this->getTestDataPath("template.html");
			$string = "test string";

			$template = new Template();
			$template->loadFromFile($templateDir);
			#echo $template;

			try {
				$template->insertTextAtClass($string, "itm", false);
			} catch (\Exception $e) {
				$this->assertEquals(221141011, $e->getCode());
				return;
			}
			$this->fail("An exception should have been thrown, since the class name does not exist.");
		}


	}
}*/