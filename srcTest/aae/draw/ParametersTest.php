<?php
namespace aae\draw {
	require_once strstr(__FILE__, 'Test', true).'/aae/autoload/AutoLoader.php';
    use aae\draw\parameters;
	class ParametersTest extends \PHPUnit_Framework_TestCase {
        public function test___construct() {
            $obj = new Parameters();

            UnitParameter::setUnit("pt");

            $width  = new parameters\StrokeWidth(5);
            $id     = new parameters\Id("abc");
            $class  = new parameters\Group("def");
            $fill   = new parameters\Fill(new Color());
            $stroke = new parameters\StrokeColor(new Color(0,0,0));

            $obj->set($width);
            $obj->set($id);
            $obj->set($class);
            $obj->set($fill);
            $obj->set($stroke);

            $string = $obj->format("\\aae\\draw\\svg\\", " ", "=");
            echo $string."\n";
            $string = $obj->format("\\aae\\draw\\svg\\", ";\n", ":");


            echo $string;
        }

    }
}