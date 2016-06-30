<?php
/**
 *
 */
namespace aae\time {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\time
	 */
	class TimeSpan {
        private $_smallestDisplay = 0;
        private $_largestDisplay  = 5;
        private $_roundToLargest  = false;

        public $s = 0;
        public $i = 0;
        public $h = 0;
        public $d = 0;
        public $m = 0;
        public $y = 0;

        public function __construct($format = null) {
            if (!is_null($format)) $this->format($format);
        }

        private function _timeUnitStrToInt($string) {
            switch ($string) {
                case 'largest':return -1;
                case 's':return 0;
                case 'm':return 1;
                case 'h':return 2;
                case 'd':return 3;
                case 'y':return 4;
                default: throw new TimeSpanException("Unknown time format string: '$string'", 929151745);
            }
        }

        public function format($formatString) {
            $parts = explode('-', $formatString);
            $formatInt = $this->_timeUnitStrToInt($parts[0]);
            if (count($parts) === 1) {
                if ($formatInt == -1) {
                    $this->_smallestDisplay = 0;
                    $this->_largestDisplay  = 4;
                    $this->_roundToLargest = true;
                } else $this->_smallestDisplay = $this->_largestDisplay = $formatInt;
            } elseif (count($parts) === 2) {
                $this->_smallestDisplay = $this->_timeUnitStrToInt($parts[0]);
                $this->_largestDisplay = $this->_timeUnitStrToInt($parts[1]);
                if ($this->_smallestDisplay > $this->_largestDisplay) throw new TimeSpanException("The first date string '".$parts[0]."' is larger than the second date string '".$parts[1]."'.", 929151752);
            } else throw new TimeSpanException("Wrong time string format: '$formatString'. Supported strings are: 's' for seconds, 'm' for minutes, 'd' for days, 'm' for months, 'y' for years, and any combination of two of those with '-' as a delimiter. The first value has to be smaller than or equal to the second value.", 929151753);
        }
        public function set($seconds) {
            $result = [];
            if ($this->_largestDisplay === 0)  return $this->s = $seconds;
            $minutes = (int)floor($seconds / 60);
            $seconds = (int)($seconds % 60);
            if ($this->_smallestDisplay <= 0) $this->s = $seconds;
            if ($this->_largestDisplay === 1)  return $this->i = (int)($minutes + round($seconds / 60));
            $hours   = (int)floor($minutes / 60);
            $minutes = (int)($minutes % 60);
            if ($this->_smallestDisplay <= 1) $this->i = $minutes;
            if ($this->_largestDisplay === 2)  return $this->h = (int)($hours   + round($minutes / 60));
            $days    = (int)floor($hours  / 24);
            $hours   = (int)($hours % 24);
            if ($this->_smallestDisplay <= 2) $this->h = $hours;
            if ($this->_largestDisplay === 3)  return $this->d = (int)($days    + round($hours  / 24));
            $years   = (int)floor($days  / 360);
            $days    = (int)($days % 360);
            if ($this->_smallestDisplay <= 3) $this->d = $days;
            if ($this->_smallestDisplay === 4) return $this->y = (int)($years   + round($days   / 360));
            $this->y = $years;

            if ($this->_roundToLargest) {
                if ($this->y > 0) {
                    $years = (int)round($this->y + $this->d / 360);
                    $this->_reset();
                    $this->y = $years;
                } elseif ($this->d > 0) {
                    $days = (int)round($this->d + $this->h / 24);
                    $this->_reset();
                    $this->d = $days;
                } elseif ($this->h > 0) {
                    $hours = (int)round($this->h + $this->i / 60);
                    $this->_reset();
                    $this->h = $hours;
                } elseif ($this->i > 0) {
                    $minutes = (int)round($this->i + $this->s / 60);
                    $this->_reset();
                    $this->i = $minutes;
                }
            }
            return;
        }
        private function _reset() {
            $this->s = 0;
            $this->i = 0;
            $this->h = 0;
            $this->d = 0;
            $this->m = 0;
            $this->y = 0;
        }
        public function getAssoc() {
            return [
                's' => $this->s,
                'm' => $this->i,
                'h' => $this->h,
                'd' => $this->d,
                'y' => $this->y
            ];
        }
    }
    class TimeSpanException extends \Exception {}
}