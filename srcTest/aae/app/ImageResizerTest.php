<?php
namespace aae\app {
    require_once strstr(__FILE__, 'Test', true).'/aae/autoload/AutoLoader.php';
    class ImageResizerTest extends \PHPUnit_Framework_TestCase {
        use \aae\unitTesting\TestFilesTrait;
        public function test___construct() {
            $obj = new ImageResizer();
        }

        public function setUp() {
            $this->sut = new ImageResizer();
        }

        public function test_resize() {
            # Given
            $path = $this->getTestDataPath();
            $oldFile = $path.'/large.jpg';
            $newFile = $path.'/small.jpg';
            $maxWidth = 50;
            # When resize is called
            $result = $this->sut->resize($oldFile, $newFile, $maxWidth);

            list($width, $height) = getimagesize($newFile);
            # Then
            $this->assertEquals($maxWidth, $width);
            $this->assertEquals(48, $height);
        }

    }
}