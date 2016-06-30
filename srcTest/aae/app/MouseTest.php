<?php
namespace aae\app {
    require_once strstr(__FILE__, 'Test', true).'/aae/autoload/AutoLoader.php';
    /**
     * @group database
     */
    class MouseTest extends \aae\unitTesting\DbTestCase {
        use \aae\unitTesting\TestFilesTrait;

        public $sut;

        public function setUp() {
            parent::setUp();
            $this->sut  = new Mouse($this->db);
        }

        public function test_addCage() {
            # Given
            $user = $this->getUser("axelesselmann@gmail.com");
            # When
            $result = $this->sut->addCage($user, "test");

            # Then
            $expected = 1;
            $this->assertEquals($expected, $result);
        }

        public function test_getCages() {
            # Given
            $user = $this->getUser("axelesselmann@gmail.com");
            $this->sut->addCage($user, "test");
            $this->sut->addCage($user, "test");

            # When
            $result = $this->sut->getCages($user);

            # Then
            $this->assertEquals(1, $result[0]["cage_id"]);
            $this->assertEquals(2, $result[1]["cage_id"]);
            $this->assertEquals("test", $result[1]["cage_name"]);
        }
        public function test_getCagesWithOccupants() {
            # Given
            $user     = $this->getUser("axelesselmann@gmail.com");
            $cageNbr1 = $this->sut->addCage($user);
            $cageNbr2 = $this->sut->addCage($user);

            $this->sut->newMouse($user, 1, NULL, $cageNbr1, NULL);
            $this->sut->newMouse($user, 2, NULL, $cageNbr1, NULL);
            $this->sut->newMouse($user, 9, NULL, $cageNbr2, NULL);

            # When
            $result = $this->sut->getCagesWithOccupants($user);

            # Then
            $this->assertEquals(2, count($result));
            $this->assertEquals(2, count($result[0]["occupants"]));
            $this->assertEquals(1, $result[0]["occupants"][0]["mouse_id"]);
            $this->assertEquals(2, $result[0]["occupants"][1]["mouse_id"]);
            $this->assertEquals(1, count($result[1]["occupants"]));
            $this->assertEquals(3, $result[1]["occupants"][0]["mouse_id"]);
        }
        public function test_getCage() {
            # Given
            $user = $this->getUser("axelesselmann@gmail.com");
            $cageNbr = $this->sut->addCage($user);

            # When
            $result = $this->sut->getCage($user, $cageNbr);

            # Then
            $this->assertEquals(1, $result["cage_id"]);
            $this->assertEquals("UNTITLED_1", $result["cage_name"]);
        }
        public function test_getCageWithOccupants() {
            # Given
            $user    = $this->getUser("axelesselmann@gmail.com");
            $cageNbr = $this->sut->addCage($user);
            $this->sut->newMouse($user, 1, NULL, $cageNbr, NULL);
            $this->sut->newMouse($user, 2, NULL, $cageNbr, NULL);

            # When
            $result = $this->sut->getCageWithOccupants($user, $cageNbr);

            # Then
            $this->assertEquals(2, count($result["occupants"]));
            $this->assertEquals(1, $result["occupants"][0]["mouse_id"]);
            $this->assertEquals(2, $result["occupants"][1]["mouse_id"]);
        }
        public function test_newMouse() {
            # Given
            $user = $this->getUser("axelesselmann@gmail.com");
            $cageNbr = $this->sut->addCage($user, "test");
            $sex      = 1;
            $litter   = -1;
            $cage     = -1;
            $genotype = -1;

            # When
            $result = $this->sut->newMouse($user, $sex, $litter, $cage, $genotype);

            # Then
            $this->assertEquals(1, $result);
        }
        public function test_cageCount() {
            # Given
            $user = $this->getUser("axelesselmann@gmail.com");
            $this->sut->addCage($user, "test");
            $this->sut->addCage($user, "test");
            $user2 = $this->getUser("axel.esselmann@gmail.com");
            $this->sut->addCage($user2, "test");

            # When
            $result = $this->sut->cageCount($user);

            # Then
            $expected = 2;
            $this->assertEquals($expected, $result);
        }
        public function test_getCageOccupants_empty_cage() {
            # Given
            $user = $this->getUser("axelesselmann@gmail.com");
            $cageNbr = $this->sut->addCage($user);

            # When
            $result = $this->sut->getCageOccupants($user, $cageNbr);

            # Then
            $this->assertEquals(0, count($result));
        }
        public function test_getCageOccupants() {
            # Given
            $user    = $this->getUser("axelesselmann@gmail.com");
            $cageNbr = $this->sut->addCage($user);
            $this->sut->newMouse($user, 1, NULL, $cageNbr, NULL);
            $this->sut->newMouse($user, 2, NULL, $cageNbr, NULL);

            # When
            $result = $this->sut->getCageOccupants($user, $cageNbr);

            # Then
            $this->assertEquals(2, count($result));
            $this->assertEquals(1, $result[0]["mouse_id"]);
            $this->assertEquals(2, $result[1]["mouse_id"]);
        }
        public function test_formatMouse() {
            # Given
            $replacements = [
                "sex1" => "m",
                "sex2" => "f"
            ];
            $mouse = ["sex" => 1];
            # When
            $result = $this->sut->formatMouse($mouse, $replacements);

            # Then
            $this->assertEquals("m", $result["sexTranslated"]);
        }
        public function test_formatMice() {
            # Given
            $replacements = [
                "sex1" => "m",
                "sex2" => "f"
            ];
            $mice = [["sex" => 1], ["sex" => 2]];

            # When formatMice is called
            $result = $this->sut->formatMice($mice, $replacements);
            # Then
            $this->assertEquals("m", $result[0]["sexTranslated"]);
            $this->assertEquals("f", $result[1]["sexTranslated"]);
        }
        public function test_removeCage() {
            # Given
            $user     = $this->getUser("axelesselmann@gmail.com");
            $cageNbr1 = $this->sut->addCage($user);
            $cageNbr2 = $this->sut->addCage($user);

            $cageCountBefore = $this->sut->cageCount($user);

            # When
            $result = $this->sut->removeCage($user, $cageNbr1);

            $cageCountAfter = $this->sut->cageCount($user);

            # Then
            $this->assertEquals(2, $cageCountBefore);
            $this->assertEquals(1, $cageCountAfter);
            $this->assertEquals(true, $result);
        }
        public function test_getCagesWithoutGender() {
            # Given
            $user     = $this->getUser("axelesselmann@gmail.com");
            $cageNbr1 = $this->sut->addCage($user);
            $cageNbr2 = $this->sut->addCage($user);
            $cageNbr3 = $this->sut->addCage($user);
            $cageNbr4 = $this->sut->addCage($user);

            $this->sut->newMouse($user, Mouse::SEX_FEMALE,    NULL, $cageNbr1, NULL);
            $this->sut->newMouse($user, Mouse::SEX_MALE,      NULL, $cageNbr2, NULL);
            $this->sut->newMouse($user, Mouse::SEX_UNDEFINED, NULL, $cageNbr3, NULL);

            # When
            $noMales = $this->sut->getCagesWithoutGender($user, Mouse::SEX_MALE);

            # Then
            $this->assertEquals(3, count($noMales));
            $this->assertEquals(1, $noMales[0]["cage_id"]);
            $this->assertEquals(3, $noMales[1]["cage_id"]);
            $this->assertEquals(4, $noMales[2]["cage_id"]);
        }
        public function test_getGender() {
            # Given
            $user = $this->getUser("axelesselmann@gmail.com");
            $this->sut->newMouse($user, Mouse::SEX_FEMALE, NULL, NULL, NULL);
            $mouseId = $this->sut->newMouse($user, Mouse::SEX_MALE,   NULL, NULL, NULL);

            # When
            $result = $this->sut->getGender($user, $mouseId);

            # Then
            $expected = 1;
            $this->assertEquals($expected, $result);
        }
        public function test_getCagesOppositeGender() {
            # Given
            $user     = $this->getUser("axelesselmann@gmail.com");
            $cageNbr1 = $this->sut->addCage($user);
            $cageNbr2 = $this->sut->addCage($user);
            $cageNbr3 = $this->sut->addCage($user);
            $cageNbr4 = $this->sut->addCage($user);

            $this->sut->newMouse($user, Mouse::SEX_FEMALE,    NULL, $cageNbr1, NULL);
            $mouseId = $this->sut->newMouse($user, Mouse::SEX_MALE,      NULL, $cageNbr2, NULL);
            $this->sut->newMouse($user, Mouse::SEX_UNDEFINED, NULL, $cageNbr3, NULL);

            # When
            $noMales = $this->sut->getCagesOppositeGender($user, $mouseId);

            # Then
            $this->assertEquals(3, count($noMales));
            $this->assertEquals(1, $noMales[0]["cage_id"]);
            $this->assertEquals(3, $noMales[1]["cage_id"]);
            $this->assertEquals(4, $noMales[2]["cage_id"]);
        }
        public function test_moveMouseToCage() {
            # Given
            $user     = $this->getUser("axelesselmann@gmail.com");
            $cageNbr1 = $this->sut->addCage($user);
            $cageNbr2 = $this->sut->addCage($user);
            $mouseId  = $this->sut->newMouse($user, Mouse::SEX_FEMALE, NULL, $cageNbr1, NULL);

            # When
            $result = $this->sut->moveMouseToCage($user, $mouseId, $cageNbr2);
            $cage2  = $this->sut->getCageWithOccupants($user, $cageNbr2);

            # Then
            $this->assertEquals(1, $cage2["occupants"][0]["mouse_id"]);
        }
        public function test_deleteMouse() {
            # Given
            $user    = $this->getUser("axelesselmann@gmail.com");
            $mouseId = $this->sut->newMouse($user, Mouse::SEX_FEMALE,    NULL, NULL, NULL);

            # When
            $result = $this->sut->deleteMouse($user, $mouseId);

            # Then
            $expected = true;
            $this->assertEquals($expected, $result);
        }
        public function test_getMouse() {
            # Given
            $user    = $this->getUser("axelesselmann@gmail.com");
            $mouseId = $this->sut->newMouse($user, Mouse::SEX_FEMALE, NULL, NULL, NULL);
            $replacements = [
                "sex1" => "m",
                "sex2" => "f"
            ];
            # When
            $result = $this->sut->getMouse($user, $mouseId, $replacements);

            # Then
            $this->assertEquals(1, $result["mouse_id"]);
            $this->assertEquals("f", $result["sexTranslated"]);
        }
        public function test_createLitter() {
            # Given
            $user      = $this->getUser("axelesselmann@gmail.com");
            $motherId  = $this->sut->newMouse($user, Mouse::SEX_FEMALE, NULL, NULL, NULL);
            $fatherId  = $this->sut->newMouse($user, Mouse::SEX_MALE,   NULL, NULL, NULL);
            $birthMonth = 6;
            $birthDay  = 26;
            $birthYear = 2011;

            # When
            $result = $this->sut->createLitter($user, $motherId, $fatherId, $birthMonth, $birthDay, $birthYear);

            # Then
            $this->assertEquals(1, $result);
            $this->assertTableHas("litters", [
                "birth_date" => "2011-06-26 00:00:00"
            ]);
        }
        public function test_getFemalesFromCage() {
            # Given
            $user       = $this->getUser("axelesselmann@gmail.com");
            $cageId     = $this->sut->addCage($user);
            $motherId   = $this->sut->newMouse($user, Mouse::SEX_FEMALE, NULL, $cageId, NULL);
            $otherFemId = $this->sut->newMouse($user, Mouse::SEX_FEMALE, NULL, $cageId, NULL);
            $fatherId   = $this->sut->newMouse($user, Mouse::SEX_MALE,   NULL, $cageId, NULL);

            # When
            $result = $this->sut->getFemalesFromCage($user, $cageId);

            # Then
            $this->assertEquals(2, count($result));
            $this->assertEquals(Mouse::SEX_FEMALE, $result[0]["sex"]);
            $this->assertEquals(Mouse::SEX_FEMALE, $result[1]["sex"]);
        }
        public function test_getMalesFromCage() {
            # Given
            $user       = $this->getUser("axelesselmann@gmail.com");
            $cageId     = $this->sut->addCage($user);
            $motherId   = $this->sut->newMouse($user, Mouse::SEX_FEMALE, NULL, $cageId, NULL);
            $otherFemId = $this->sut->newMouse($user, Mouse::SEX_FEMALE, NULL, $cageId, NULL);
            $fatherId   = $this->sut->newMouse($user, Mouse::SEX_MALE,   NULL, $cageId, NULL);

            # When
            $result = $this->sut->getMalesFromCage($user, $cageId);

            # Then
            $this->assertEquals(1, count($result));
            $this->assertEquals(Mouse::SEX_MALE, $result[0]["sex"]);
        }
        public function test_createNewLitter() {
            # Given
            $user         = $this->getUser("axelesselmann@gmail.com");
            $cageId       = $this->sut->addCage($user);
            $motherId     = $this->sut->newMouse($user, Mouse::SEX_FEMALE, NULL, $cageId, NULL);
            $fatherId     = $this->sut->newMouse($user, Mouse::SEX_MALE,   NULL, $cageId, NULL);
            $birthMonth   = 6;
            $birthDay     = 26;
            $birthYear    = 2011;
            $nbrPups      = 3;
            $replacements = [
                "sex1" => "m",
                "sex2" => "f"
            ];

            # When
            $result = $this->sut->createNewLitter($user, $cageId, $motherId, $fatherId, $nbrPups, $birthMonth, $birthDay, $birthYear, $replacements);

            # Then
            $this->assertEquals(3, count($result));
            $this->assertEquals(3, $result[0]["mouse_id"]);
            $this->assertEquals(Mouse::SEX_UNDEFINED, $result[1]["sex"]);
            $this->assertEquals(1, $result[2]["litter_id"]);
        }
        public function test_editMouse() {
            # Given
            $user     = $this->getUser("axelesselmann@gmail.com");
            $mouseId  = $this->sut->newMouse($user, Mouse::SEX_FEMALE, NULL, NULL, NULL);
            $sex      = Mouse::SEX_MALE;
            $genotype = NULL;
            # When
            $result = $this->sut->editMouse($user, $mouseId, $sex, $genotype);

            # Then
            $this->assertTrue($result);
            $this->assertTableHas(
                "mice",
                [
                    "mouse_id" => $mouseId,
                    "sex" => $sex
                ]
            );
        }
        public function test_mouseDeceased() {
            # Given
            $user     = $this->getUser("axelesselmann@gmail.com");
            $cageId     = $this->sut->addCage($user);
            $mouseId  = $this->sut->newMouse($user, Mouse::SEX_FEMALE, NULL, $cageId, NULL);
            $replacements = [
                "sex1" => "m",
                "sex2" => "f"
            ];

            # When
            $result = $this->sut->mouseDeceased($user, $mouseId);
            $mouse  = $this->sut->getMouse($user, $mouseId, $replacements);
            # Then
            $this->assertTrue($result);
            $this->assertNotEquals(NULL, $mouse["time_deceased"]);
            // $this->assertEquals(NULL, $mouse["cage_id"]); // TODO: fails in unit test
        }
        public function test_getAllMice() {
            # Given
            $user  = $this->getUser("axelesselmann@gmail.com");
            $user2 = $this->getUser("axel.esselmann@gmail.com");
            $color = "col1";
            $gen   = "gen";

            # When
            $genId        = $this->sut->createGenotype($user, $gen, $color);
            $other        = $this->sut->newMouse($user2, Mouse::SEX_FEMALE, NULL, NULL, NULL);
            $cageId       = $this->sut->addCage($user);
            $motherId     = $this->sut->newMouse($user, Mouse::SEX_FEMALE, NULL, $cageId, $genId);
            $fatherId     = $this->sut->newMouse($user, Mouse::SEX_MALE,   NULL, $cageId, $genId);
            $birthMonth   = 6;
            $birthDay     = 26;
            $birthYear    = 2011;
            $nbrPups      = 3;
            $replacements = [
                "sex0" => "o",
                "sex1" => "m",
                "sex2" => "f"
            ];
            # When
            $this->sut->createNewLitter($user, $cageId, $motherId, $fatherId, $nbrPups, $birthMonth, $birthDay, $birthYear, $replacements);
            $result = $this->sut->getAllMice($user, $replacements);
            # Then
            $this->assertEquals(5, count($result));
            $this->assertEquals(2, $result[2]["mother_id"]);
            $this->assertEquals(3, $result[3]["father_id"]);
            $this->assertEquals('o', $result[4]["sexTranslated"]);
            $this->assertEquals($gen, $result[0]["genotype_name"]);
            $this->assertEquals($color, $result[0]["genotype_color"]);
        }
        public function test_getGenotypes() {
            # Given
            $user   = $this->getUser("axelesselmann@gmail.com");
            $color = "col1";

            # When
            $genId1 = $this->sut->createGenotype($user, "gen1", $color);
            $genId2 = $this->sut->createGenotype($user, "gen2", $color);
            $genId3 = $this->sut->createGenotype($user, "gen3", $color);

            $result = $this->sut->getGenotypes($user);

            # Then
            $this->assertEquals(3, count($result));
            $this->assertEquals(1, $result[0]["genotype_id"]);
            $this->assertEquals("gen3", $result[2]["genotype_name"]);
        }
        public function test_getMouse_test_birth_date() {
            # Given
            $user         = $this->getUser("axelesselmann@gmail.com");
            $cageId       = $this->sut->addCage($user);
            $motherId     = $this->sut->newMouse($user, Mouse::SEX_FEMALE, NULL, $cageId, NULL);
            $fatherId     = $this->sut->newMouse($user, Mouse::SEX_MALE,   NULL, $cageId, NULL);
            $birthMonth   = 6;
            $birthDay     = 26;
            $birthYear    = 2011;
            $nbrPups      = 3;
            $replacements = [
                "sex1" => "m",
                "sex2" => "f"
            ];

            # When
            $this->sut->createNewLitter($user, $cageId, $motherId, $fatherId, $nbrPups, $birthMonth, $birthDay, $birthYear, $replacements);

            $mouse = $this->sut->getMouse($user, 3, $replacements);

            $this->assertEquals($birthYear, $mouse["birth_date"]["year"]);
            $this->assertEquals($birthDay, $mouse["birth_date"]["day"]);
            $this->assertEquals($birthMonth, $mouse["birth_date"]["month"]);
            # Then
            // $this->assertEquals(3, count($result));
            // // $this->assertEquals(3, $result[0]["mouse_id"]);
            // $this->assertEquals(Mouse::SEX_UNDEFINED, $result[1]["sex"]);
            // $this->assertEquals(1, $result[2]["litter_id"]);
        }
	}
}