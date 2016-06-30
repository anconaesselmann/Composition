<?php
/**
 *
 */
namespace aae\log {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\log
	 */
	class ScreenLogger extends \aae\log\AbstractLogger implements \aae\log\Loggable {
		private $_htmlOutput;
		protected $_newLine = "<br />";

		public function __construct($htmlOutput = true) {
			$this->_htmlOutput = $htmlOutput;
		}

		public function log($message, $eventType = ScreenLogger::NORMAL) {
			$timeString = $this->_getTimeString();
			if ($this->_htmlOutput) {
				echo $this->_getHTMLString($message, $timeString, $eventType);
			} else {
				echo $this->_getString($message, $timeString, $eventType);
			}
		}

		private function _getHTMLString($message, $timeString, $eventType) {
			$titlStyle = "<span style=\"color:black; font-style:italic\">";
			$mssgStyle = "<span style=\"color:blue; font-weight:bold\">";
			$timeStyle = "<span style=\"color:green; font-weight:bold\">";
			$typeStyle = "<span style=\"color:red; font-weight:bold\">";
			$styleEnd = "</span> ";
			return $titlStyle.
				 	"Type:".
				 $styleEnd.
				 $typeStyle.
				 	$eventType.
				 $styleEnd.
				 ", ".$titlStyle.
				 	"Time:".
				 $styleEnd.
				 $timeStyle.
				 	$timeString.
				 $styleEnd.
				 ", ".$titlStyle.
				 	"Message:".
				 $styleEnd.
				 $mssgStyle.
				 	"'".$message."'".
				 $styleEnd.
				 $this->_newLine;
		}
	}
}