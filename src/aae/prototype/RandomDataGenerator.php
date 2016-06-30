<?php
/**
 *
 */
namespace aae\prototype {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\prototype
	 */
	class RandomDataGenerator {
        private $_dict = [
            "a","ac","accumsan","adipiscing","aliquam","aliquet","amet","ante",
            "arcu","at","auctor","augue","bibendum","blandit","commodo","condimentum",
            "consectetur","consequat","cubilia","cum","curabitur","curae","cursus",
            "dapibus","diam","dictum","dictumst","dignissim","dis","dolor","donec",
            "dui","duis","egestas","eget","eleifend","elementum","elit","enim",
            "erat","eros","et","etiam","eu","facilisi","facilisis","faucibus",
            "felis","feugiat","fringilla","fusce","gravida","habitasse","hac",
            "hendrerit","iaculis","id","imperdiet","in","integer","interdum","ipsum",
            "justo","lacinia","lacus","laoreet","lectus","leo","libero","ligula",
            "lobortis","lorem","luctus","maecenas","magna","magnis","malesuada",
            "massa","mattis","mauris","metus","mi","molestie","mollis","montes",
            "mus","nascetur","natoque","nec","neque","nibh","nisi","nisl","non",
            "nulla","nullam","nunc","odio","orci","ornare","parturient","pede",
            "pellentesque","penatibus","pharetra","phasellus","placerat","platea",
            "posuere","potenti","praesent","pretium","primis","proin","pulvinar",
            "purus","quam","quis","rhoncus","ridiculus","risus","rutrum","sagittis",
            "sapien","sed","sem","semper","sit","sociis","sodales","suscipit",
            "suspendisse","tellus","tempor","tempus","tincidunt","tristique",
            "turpis","ultrices","urna","ut","varius","vehicula","vel","velit",
            "venenatis","vestibulum","vitae","vivamus","viverra","volutpat","vulputate"
        ];
        private $_averageSentenceLength  = 14;
        private $_sentenceLengthStdDev   = 3;
        private $_averageParagraphLength = 5;
        private $_paragraphLengthStdDev  = 3;
        private $_averageTextLength      = 5;
        private $_textLengthStdDev       = 3;
        private $_randNbrGenerator       = null;
        private $_wordsSinceLastCommata  = 0;
        private $_timeLow;

        public function __construct($randNbrGenerator = null) {
            $this->_timeLow = rand(946684800,946684800);
            $this->_randNbrGenerator = $randNbrGenerator;
        }
        private function _getRandNormal($average, $stdDev) {
            if ($this->_randNbrGenerator == null) {
                return rand(3, $average * 2 + 3);;
            } else {
                $result = $this->_randNbrGenerator->normalInt($average, $stdDev);
                if ($result < 2) return $average;
                else return $result;
            }
        }
        private function _commata($sentLength) {
            if ($this->_wordsSinceLastCommata > 1) {
                $randVar = rand(0, 3);
                if ($randVar == 0) {
                    $this->_wordsSinceLastCommata = 0;
                    return ',';
                }
            }
            $this->_wordsSinceLastCommata++;
            return '';
        }
        public function getWords($nbrWords) {
            $result = "";
            for ($i=0; $i < $nbrWords; $i++) {
                $randId = rand(0, count($this->_dict) - 1);
                $result .= ' '.$this->_dict[$randId];
            }
            return trim($result);
        }
        public function getSentence($average = null, $stdDev = null) {
            if ($average == null) {
                $average = $this->_averageSentenceLength;
                $stdDev  = $this->_sentenceLengthStdDev;
            }
            $sentLength = $this->_getRandNormal($average, $stdDev);
            $result = "";
            for ($i=0; $i < $sentLength; $i++) {
                $randId = rand(0, count($this->_dict) - 1);
                $word = $this->_dict[$randId];
                if ($i == 0) $word = ucfirst($word);
                $result .= ' '.$word;
                if ($i < $sentLength - 3) {
                    $commata = $this->_commata($sentLength);
                    $result .= $commata;
                }
            }
            return trim($result).".";
        }
        public function getParagraph($averageP = null, $stdDevP = null, $averageS = null, $stdDevS = null) {
            if ($averageP == null) {
                $averageP = $this->_averageParagraphLength;
                $stdDevP  = $this->_paragraphLengthStdDev;
            }
            if ($averageS == null) {
                $averageS = $this->_averageSentenceLength;
                $stdDevS  = $this->_sentenceLengthStdDev;
            }
            $nbrSentences = $this->_getRandNormal($averageP, $stdDevP);
            $result = "";
            for ($i=0; $i < $nbrSentences; $i++) {
                $result .= " ".$this->getSentence($averageS, $stdDevS);
            }
            return trim($result);
        }
        public function getSentences($nbrSentences, $average, $stdDev) {
            $result = '';
            for ($i=0; $i < $nbrSentences; $i++) $result .= ' '.$this->getSentence($average, $stdDev);
            return trim($result);
        }
        public function getText($averageT = null, $stdDevT = null, $averageP = null, $stdDevP = null, $averageS = null, $stdDevS = null) {
            if ($averageP == null) {
                $averageP = $this->_averageParagraphLength;
                $stdDevP  = $this->_paragraphLengthStdDev;
            }
            if ($averageS == null) {
                $averageS = $this->_averageSentenceLength;
                $stdDevS  = $this->_sentenceLengthStdDev;
            }
            if ($averageT == null) {
                $averageT = $this->_averageTextLength;
                $stdDevT  = $this->_textLengthStdDev;
            }
            $nbrParagraphs = $this->_getRandNormal($averageT, $stdDevT);
            $result = "";
            for ($i=0; $i < $nbrParagraphs; $i++) {
                $result .= "\n".$this->getParagraph($averageP, $stdDevP, $averageS, $stdDevS);
            }
            return trim($result);
        }
        public function getMySQLTimeStamp($from, $to) {
            if ($from == null) {
                $from = $this->_timeLow;
                $to   = time();
            }
            $time = rand($from,$to);
            return date("Y-m-d H:i:s", $time);
        }
    }
}