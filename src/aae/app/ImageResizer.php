<?php
/**
 * ImageResizer changes the resolution of an image file.
 */
namespace aae\app {
	/**
     *
	 * @author Axel Ancona Esselmann
	 * @package aae\app
	 */
	class ImageResizer {
        /**
         * Algorithm adapted from http://php.net/manual/en/function.imagecopyresampled.php
         *
         * @param  str $fileName    Old Image file
         * @param  str $newFileName New Image file name
         * @param  int $maxWidth    maximum new width
         * @param  int $maxHeight   maximum new height
         */
		public function resize($fileName, $newFileName, $maxWidth, $maxHeight = null) {
            if (is_null($maxHeight)) $maxHeight = $maxWidth;
            if (!file_exists($fileName)) throw new \Exception("File $fileName does not exist", 918151627);
            list($width, $height) = getimagesize($fileName);
            $ratio = $width / $height;
            if ($maxWidth / $maxHeight > $ratio) $maxWidth = $maxHeight * $ratio;
            else $maxHeight = $maxWidth / $ratio;

            $fileHandler = imagecreatetruecolor($maxWidth, $maxHeight);
            $image       = imagecreatefromjpeg($fileName);
            imagecopyresampled(
                $fileHandler,
                $image,
                0,
                0,
                0,
                0,
                $maxWidth,
                $maxHeight,
                $width,
                $height);
            imagejpeg($fileHandler, $newFileName);
        }
	}
}