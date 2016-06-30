<?php
namespace aae\ui {
	require_once strstr(__FILE__, 'Test', true).'/aae/autoload/AutoLoader.php';
	class TimeLocalizerTest extends \PHPUnit_Framework_TestCase {
        public function provider_localizeTimeSpan() {
            return array(
                array(new \aae\time\TimeSpan(), 's', 1, '1 second'),
                array(new \aae\time\TimeSpan(), 's', 10, '10 seconds'),
                array(new \aae\time\TimeSpan(), 'm', 60, '1 minute'),
                array(new \aae\time\TimeSpan(), 'm', 60 * 2, '2 minutes'),
                array(new \aae\time\TimeSpan(), 'h', 60 * 60, '1 hour'),
                array(new \aae\time\TimeSpan(), 'h', 2 * 60 * 60, '2 hours'),
                array(new \aae\time\TimeSpan(), 'd', 24 * 60 * 60, '1 day'),
                array(new \aae\time\TimeSpan(), 'd', 2 * 24 * 60 * 60, '2 days'),
                array(new \aae\time\TimeSpan(), 'y', 360 * 24 * 60 * 60, '1 year'),
                array(new \aae\time\TimeSpan(), 'y', 2 * 360 * 24 * 60 * 60, '2 years'),
                array(new \aae\time\TimeSpan(), 's-y', 777600000 + 13737600 + 11 * 60 * 60 + 59 * 60 + 59, '25 years, 159 days, 11 hours, 59 minutes, 59 seconds'),
            );
        }

        /**
         * @dataProvider provider_localizeTimeSpan
         */
        public function test_localizeTimeSpan($timeSpan, $formatString, $seconds, $expected) {
            # Given
            $obj = new TimeLocalizer('eng');

            $timeSpan->format($formatString);
            $timeSpan->set($seconds);

            # When
            $result = $obj->localizeTimeSpan($timeSpan);

            # Then
            $this->assertEquals($expected, $result);
        }

	}
}