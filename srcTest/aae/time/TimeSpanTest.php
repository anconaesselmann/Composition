<?php
namespace aae\time {
	require_once strstr(__FILE__, 'Test', true).'/aae/autoload/AutoLoader.php';
	class TimeSpanTest extends \PHPUnit_Framework_TestCase {
		public function setUp() {
            $this->sut = new TimeSpan();
        }
        public function provider_getAssoc() {
            return array(
                array(
                    's',
                    1296000, // exactly 15 days
                    [
                        's' => 1296000,
                        'm' => 0,
                        'h' => 0,
                        'd' => 0,
                        'y' => 0
                    ]
                ),
                array(
                    'm',
                    1296000, // exactly 15 days
                    [
                        's' => 0,
                        'm' => 21600,
                        'h' => 0,
                        'd' => 0,
                        'y' => 0
                    ]
                ),
                array(
                    'h',
                    1296000, // exactly 15 days
                    [
                        's' => 0,
                        'm' => 0,
                        'h' => 360,
                        'd' => 0,
                        'y' => 0
                    ]
                ),
                array(
                    'd',
                    1296000, // exactly 15 days
                    [
                        's' => 0,
                        'm' => 0,
                        'h' => 0,
                        'd' => 15,
                        'y' => 0
                    ]
                ),
                array(
                    'y',
                    777600000, // exactly 25 years
                    [
                        's' => 0,
                        'm' => 0,
                        'h' => 0,
                        'd' => 0,
                        'y' => 25
                    ]
                ),
                // round down to hours
                array(
                    'h',
                     11 * 60 * 60 + 29 * 60 + 59, // exactly 11 hours, 59 minutes, 59 seconds
                    [
                        's' => 0,
                        'm' => 0,
                        'h' => 11,
                        'd' => 0,
                        'y' => 0
                    ]
                ),
                // round down to days
                array(
                    'd',
                    1296000 + 11 * 60 * 60 + 59 * 60 + 59, // exactly 15 days 11 hours, 59 minutes, 59 seconds
                    [
                        's' => 0,
                        'm' => 0,
                        'h' => 0,
                        'd' => 15,
                        'y' => 0
                    ]
                ),
                // round down to years
                array(
                    'y',
                    777600000 + 13737600 + 11 * 60 * 60 + 59 * 60 + 59, // exactly 25 years, 159 days 11 hours, 59 minutes, 59 seconds
                    [
                        's' => 0,
                        'm' => 0,
                        'h' => 0,
                        'd' => 0,
                        'y' => 25
                    ]
                ),
                // round up to minutes
                array(
                    'm',
                    29 * 60 +
                         30, // 29 minutes and 30 seconds
                    [
                        's' => 0,
                        'm' => 30,
                        'h' => 0,
                        'd' => 0,
                        'y' => 0
                    ]
                ),
                // round up to hours
                array(
                    'h',
                    11 * 60 * 60 + 30 * 60, // exactly 11 hours and 30 minutes
                    [
                        's' => 0,
                        'm' => 0,
                        'h' => 12,
                        'd' => 0,
                        'y' => 0
                    ]
                ),
                // round up to days
                array(
                    'd',
                    1296000 + 12 * 60 * 60, // exactly 15 days 12 hours
                    [
                        's' => 0,
                        'm' => 0,
                        'h' => 0,
                        'd' => 16,
                        'y' => 0
                    ]
                ),
                // round up to years
                array(
                    'y',
                    24 * 360 * 24 * 60 * 60 +
                         180 * 24 * 60 * 60, // exactly 24 years and 180 days
                    [
                        's' => 0,
                        'm' => 0,
                        'h' => 0,
                        'd' => 0,
                        'y' => 25
                    ]
                ),
                array(
                    's-y',
                    777600000 + 13737600 + 11 * 60 * 60 + 59 * 60 + 59, // exactly 25 years, 159 days 11 hours, 59 minutes, 59 seconds
                    [
                        's' => 59,
                        'm' => 59,
                        'h' => 11,
                        'd' => 159,
                        'y' => 25
                    ]
                ),

                // auto format to years, round up
                array(
                    'largest',
                    24 * 360 * 24 * 60 * 60 +
                         180 * 24 * 60 * 60 +
                               12 * 60 * 60 +
                                    30 * 60 +
                                         30,
                    [
                        's' => 0,
                        'm' => 0,
                        'h' => 0,
                        'd' => 0,
                        'y' => 25
                    ]
                ),
                // auto format to days, round up
                array(
                    'largest',
                         180 * 24 * 60 * 60 +
                               12 * 60 * 60 +
                                    30 * 60 +
                                         30,
                    [
                        's' => 0,
                        'm' => 0,
                        'h' => 0,
                        'd' => 181,
                        'y' => 0
                    ]
                ),
                // auto format to hours, round up
                array(
                    'largest',
                               12 * 60 * 60 +
                                    30 * 60 +
                                         30,
                    [
                        's' => 0,
                        'm' => 0,
                        'h' => 13,
                        'd' => 0,
                        'y' => 0
                    ]
                ),
                // auto format to minutes, round up
                array(
                    'largest',
                                    30 * 60 +
                                         30,
                    [
                        's' => 0,
                        'm' => 31,
                        'h' => 0,
                        'd' => 0,
                        'y' => 0
                    ]
                ),
                // auto format to seconds
                array(
                    'largest',
                                         30,
                    [
                        's' => 30,
                        'm' => 0,
                        'h' => 0,
                        'd' => 0,
                        'y' => 0
                    ]
                ),
                // auto format to years, round down
                array(
                    'largest',
                    24 * 360 * 24 * 60 * 60 +
                         179 * 24 * 60 * 60 +
                               11 * 60 * 60 +
                                    29 * 60 +
                                         29,
                    [
                        's' => 0,
                        'm' => 0,
                        'h' => 0,
                        'd' => 0,
                        'y' => 24
                    ]
                ),
                // auto format to days, round down
                array(
                    'largest',
                         179 * 24 * 60 * 60 +
                               11 * 60 * 60 +
                                    29 * 60 +
                                         29,
                    [
                        's' => 0,
                        'm' => 0,
                        'h' => 0,
                        'd' => 179,
                        'y' => 0
                    ]
                ),
                // auto format to hours, round down
                array(
                    'largest',
                               11 * 60 * 60 +
                                    29 * 60 +
                                         29,
                    [
                        's' => 0,
                        'm' => 0,
                        'h' => 11,
                        'd' => 0,
                        'y' => 0
                    ]
                ),
                // auto format to minutes, round down
                array(
                    'largest',
                                    29 * 60 +
                                         29,
                    [
                        's' => 0,
                        'm' => 29,
                        'h' => 0,
                        'd' => 0,
                        'y' => 0
                    ]
                )
            );
        }

        /**
         * @dataProvider provider_getAssoc
         */
        public function test_getAssoc($format, $seconds, $expected) {
            # When
            $this->sut->format($format);
            $this->sut->set($seconds);
            $result = $this->sut->getAssoc();

            # Then
            $this->assertEquals($expected, $result);
        }
	}
}