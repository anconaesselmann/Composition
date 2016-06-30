<?php
/**
 *
 */
namespace aae\examples {
	class Human {
		public function __construct($name) {
			$this->name = $name;
		}
		public function __toString() {
			return $this->name;
		}
	}

	class Man extends Human {

	}

	class Woman extends Human {

	}

	class Person {
		public function __construct(Human $human) {
			$this->$human = $human;
		}
	}

	class Driver {
		public function __construct(Person $person) {
			$this->person = $person;
		}
	}

	class Car {
		public function __construct(Driver $driver, Person $passenger) {
			$this->driver = $driver;
			$this->passenger = $passenger;
		}
	}
}
namespace aae\dic {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\dic
	 */
	
	class DynamicContainer {
		public function __call($methodName, $args) {
			if(is_callable(array($this, $methodName))) {
	            return call_user_func_array($this->$methodName, $args);
	        }
		}

		public function getService($serviceName) {

		}
	}
	


	class ConfigFileContainer {
		public function __construct($path) {	
			//print($path);		

			$container = $this->getSpecifficContainer();


			$driver = $container->getCarDriver("Axel");

			$passenger = $container->getCarPerson("Sam");

			$car = New \aae\examples\Car(
				$container->getCarDriver("Axel"),
				$container->getCarPerson("Sam")
			);

			#print_r($car);
		}

		public function getSpeciffic() {
			return array(
				"car" => array(
					"aae\examples\Car" => array(
						"driver1" => array(
							"aae\examples\Driver" => "/person1"
						),
						"driver2" => array(
							"aae\examples\Driver" => "/person2"
						),
						"passenger1" => "/person1",
						"passenger2" => "/person2"
					)
				),

				"person1" => array(
					"aae\examples\Person" => array(
						"man" => array(
							"aae\examples\Man" => "Axel"
						)
					)
				),
				"person2" => array(
					"aae\examples\Person" => array(
						"man" => array(
							"aae\examples\Woman" => "Sam"
						)
					)
				)
			);
		}

		public function getSpecifficContainer() {
			$c = new DynamicContainer();

			$c->getCarDriverPersonMan = function ($name) {
				return new \aae\examples\Man($name);
			};

			$c->getCarDriverPerson = function ($name) use ($c) {
				return new \aae\examples\Person($c->getCarDriverPersonMan($name));
			};

			$c->getCarDriver = function ($name) use ($c) {
				return new \aae\examples\Driver($c->getCarDriverPerson($name));
			};


			$c->getCarPersonWoman = function ($name) {
				return new \aae\examples\Woman($name);
			};

			$c->getCarPerson = function ($name) use ($c) {
				return new \aae\examples\Person($c->getCarPersonWoman($name));
			};



			/*$c->getPerson = function (\Human $manOrWoman) {
				return new \Person($manOrWoman);
			};
			$c->getDriverPerson = function (\Human $manOrWoman) use ($c) {
				return new \Driver($c->getPerson($manOrWoman));
			};*/
			return $c;
		}
		
	}
}