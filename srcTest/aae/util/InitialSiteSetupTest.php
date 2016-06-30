<?php
namespace aae\util {
	require_once strstr(__FILE__, 'Test', true).'/aae/autoload/AutoLoader.php';
	class InitialSiteSetupTest extends \PHPUnit_Framework_TestCase {
        use \aae\unitTesting\TestFilesTrait;

        public function setUp() {
            $dir = $this->getTestDataPath();
            $siteName = 'mySite';
            $this->sut = new InitialSiteSetup($dir, $siteName);
        }
        public function test_appendHostsFile() {
            # Given

            # When
            // $result = $this->sut->appendVirtualHosts("/a/dir", "siteName", "axel@anconaesselmann.com", "aPassword");

            # Then
            // $expected = ;
            // $this->assertEquals($expected, $result);


        }
		// public function test_createFolders() {
  //           # Given


  //           # When createFolders is called
  //           $result = $this->sut->createFolders();

  //           # Then EXPECTED_CONDITIONS
  //           $this->assertEquals($expected, $result);
  //       }
  //       public function tearDown() {
  //           $dir = $this->getTestDataPath();
  //           $dirPath = $dir.DIRECTORY_SEPARATOR."protected";
  //           foreach(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dirPath, \FilesystemIterator::SKIP_DOTS), \RecursiveIteratorIterator::CHILD_FIRST) as $path) {
  //                   $path->isDir() && !$path->isLink() ? rmdir($path->getPathname()) : unlink($path->getPathname());
  //           }
  //           rmdir($dirPath);
  //           $dirPath = $dir.DIRECTORY_SEPARATOR."public";
  //           foreach(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dirPath, \FilesystemIterator::SKIP_DOTS), \RecursiveIteratorIterator::CHILD_FIRST) as $path) {
  //                   $path->isDir() && !$path->isLink() ? rmdir($path->getPathname()) : unlink($path->getPathname());
  //           }
  //           rmdir($dirPath);
  //           $dirPath = $dir.DIRECTORY_SEPARATOR."logs";
  //           foreach(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dirPath, \FilesystemIterator::SKIP_DOTS), \RecursiveIteratorIterator::CHILD_FIRST) as $path) {
  //                   $path->isDir() && !$path->isLink() ? rmdir($path->getPathname()) : unlink($path->getPathname());
  //           }
  //           rmdir($dirPath);
  //       }
	}
}