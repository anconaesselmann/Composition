<?php
/**
 *
 */
namespace aae\ui {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\ui
	 */
	class TimeLocalizer {
        private $_vocab = null;
        private static $_vocabEng = [
            's'  => 'second',
            'sp' => 'seconds',
            'i'  => 'minute',
            'ip' => 'minutes',
            'h'  => 'hour',
            'hp' => 'hours',
            'd'  => 'day',
            'dp' => 'days',
            'y'  => 'year',
            'yp' => 'years'
        ];
        private static $_vocabGer = [
            's'  => 'Sekunde',
            'sp' => 'Sekunden',
            'i'  => 'Minute',
            'ip' => 'Minuten',
            'h'  => 'Stunde',
            'hp' => 'Stunden',
            'd'  => 'Tag',
            'dp' => 'Tage',
            'y'  => 'Jahr',
            'yp' => 'Jahre'
        ];
        public function __construct($lang) {
            if ($this->_vocab == null) {
                $vocabVarName = "_vocab".ucfirst($lang);
                $className    = "\\aae\\ui\\TimeLocalizer";
                $this->_vocab = $className::$$vocabVarName;
            }
        }

        public function localizeTimeSpan($timeSpan) {
            $out = "";


            if ($timeSpan->y > 0) {
                $out .= $timeSpan->y;
                if ($timeSpan->y === 1) {
                    $out .= " ".$this->_vocab['y'];
                } else {
                    $out .= " ".$this->_vocab['yp'];
                }
            }

            if ($timeSpan->d > 0) {
                if (strlen($out) > 0) $out .= ', ';
                $out .= $timeSpan->d;
                if ($timeSpan->d === 1) {
                    $out .= " ".$this->_vocab['d'];
                } else {
                    $out .= " ".$this->_vocab['dp'];
                }
            }

            if ($timeSpan->h > 0) {
                if (strlen($out) > 0) $out .= ', ';
                $out .= $timeSpan->h;
                if ($timeSpan->h === 1) {
                    $out .= " ".$this->_vocab['h'];
                } else {
                    $out .= " ".$this->_vocab['hp'];
                }
            }

            if ($timeSpan->i > 0) {
                if (strlen($out) > 0) $out .= ', ';
                $out .= $timeSpan->i;
                if ($timeSpan->i === 1) {
                    $out .= " ".$this->_vocab['i'];
                } else {
                    $out .= " ".$this->_vocab['ip'];
                }
            }

            if ($timeSpan->s > 0) {
                if (strlen($out) > 0) $out .= ', ';
                $out .= $timeSpan->s;
                if ($timeSpan->s === 1) {
                    $out .= " ".$this->_vocab['s'];
                } else {
                    $out .= " ".$this->_vocab['sp'];
                }
            }

            return $out;
        }
    }
}