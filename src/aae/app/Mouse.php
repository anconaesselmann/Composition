<?php
/**
 *
 */
namespace aae\app {
    use \aae\db\FunctionAPI as FAPI;
    /**
     * @author Axel Ancona Esselmann
     * @package aae\app
     */
    class Mouse {
        const SEX_MALE = 1, SEX_FEMALE = 2, SEX_UNDEFINED = 0;
        public function __construct(FAPI $storageAPI) {
            $this->_storageAPI = $storageAPI;
        }
        public function addCage($user, $cageName = NULL) {
            $userId = $user->getId();
            if (is_null($cageName)) $cageName = "UNTITLED_".($this->_storageAPI->cageCount($userId) + 1);
            return (int)$this->_storageAPI->addCage($user, $cageName);
        }
        public function getCages($user) {
            $this->_storageAPI->setFetchMode(FAPI::FETCH_ASS_ARRAY);
            $result = $this->_storageAPI->getCages($user);
            $this->_storageAPI->resetFetchMode();
            return $result;
        }
        public function getCagesWithOccupants($user, $replacements = null) {
            $cages = $this->getCages($user);
            for ($i=0; $i < count($cages); $i++) {
                $occupants              = $this->getCageOccupants($user, $cages[$i]["cage_id"]);
                if (is_array($replacements)) $occupants = $this->formatMice($occupants, $replacements);
                $cages[$i]["occupants"] = $occupants;
            }
            return $cages;
        }
        public function getCage($user, $cageNbr) {
            $this->_storageAPI->setFetchMode(FAPI::FETCH_ASS_ARRAY | FAPI::FETCH_ONE_ROW);
            $result = $this->_storageAPI->getCage($user, $cageNbr);
            $this->_storageAPI->resetFetchMode();
            return $result;
        }
        public function getCageWithOccupants($user, $cageNbr, $replacements = null) {
            $cage              = $this->getCage($user, $cageNbr);
            $occupants         = $this->getCageOccupants($user, $cageNbr);
            if (is_array($replacements)) $occupants = $this->formatMice($occupants, $replacements);
            $cage["occupants"] = $occupants;
            return $cage;
        }
        public function newMouse($user, $sex, $litter, $cage, $genotype) {
            $sex      = $this->_validateSex($sex);
            $cage     = ((int)$cage < 1)     ? NULL: $cage;
            $genotype = ((int)$genotype < 1) ? NULL: $genotype;
            $litter   = ((int)$litter < 1)   ? NULL: $litter;

            return (int)$this->_storageAPI->newMouse($user, $sex, $litter, $cage, $genotype);
        }
        private function _validateSex($sex) {
            $sex      = (int)$sex;
            switch ($sex) {
                case 0:
                case 1:
                case 2:
                case 9: break;
                default: throw new \Exception("Not a valid sex", 222152042);
            }
            return $sex;
        }
        public function cageCount($user) {
            return (int)$this->_storageAPI->cageCount($user);
        }
        public function getCageOccupants($user, $cageId) {
            $this->_storageAPI->setFetchMode(FAPI::FETCH_ASS_ARRAY);
            $result = $this->_storageAPI->getCageOccupants($user, $cageId);
            $this->_storageAPI->resetFetchMode();
            return $result;
        }
        public function formatMouse($mouse, $replacements) {
            if (array_key_exists("sex".$mouse["sex"], $replacements)) $mouse["sexTranslated"] = $replacements["sex".$mouse["sex"]];
            return $mouse;
        }
        public function formatMice($mice, $replacements) {
            for ($i=0; $i < count($mice); $i++) {
                $mice[$i] = $this->formatMouse($mice[$i], $replacements);
            }
            return $mice;
        }
        public function removeCage($user, $cageId) {
            return (bool)$this->_storageAPI->removeCage($user, $cageId);
        }
        public function getCagesWithoutGender($user, $gender) {
            $this->_storageAPI->setFetchMode(FAPI::FETCH_ASS_ARRAY);
            $result = $this->_storageAPI->getCagesWithoutGender($user, $gender);
            $this->_storageAPI->resetFetchMode();
            return $result;
        }
        public function getCagesOppositeGender($user, $mouseId) {
            $gender = $this->_storageAPI->getGender($user, $mouseId);
            return $this->getCagesWithoutGender($user, $gender);
        }
        public function getGender($user, $mouseId) {
            return $this->_storageAPI->getGender($user, $mouseId);
        }
        public function moveMouseToCage($user, $mouseId, $cageId) {
            return $this->_storageAPI->moveMouseToCage($user, $mouseId, $cageId);
        }
        public function deleteMouse($user, $mouseId) {
            return (bool)$this->_storageAPI->deleteMouse($user, $mouseId);
        }
        private function _getDateArrayFromString($dateString) {
            return date_parse($dateString);
        }
        public function getMouse($user, $mouseId, $replacements) {
            $this->_storageAPI->setFetchMode(FAPI::FETCH_ASS_ARRAY | FAPI::FETCH_ONE_ROW);
            $result = $this->_storageAPI->getMouse($user, $mouseId);
            $result["birth_date"] = $this->_getDateArrayFromString($result["birth_date"]);
            $result["time_deceased"] = $this->_getDateArrayFromString($result["time_deceased"]);
            $this->_storageAPI->resetFetchMode();
            return $this->formatMouse($result, $replacements);
        }
        public function createLitter($user, $motherId, $fatherId, $birthMonth = null, $birthDay = null, $birthYear = null) {
            if (is_null($birthMonth)) $birthDate = null;
            else $birthDate = date('Y-m-d G:i:s', mktime(0, 0, 0, $birthMonth, $birthDay, $birthYear));
            return (int)$this->_storageAPI->createLitter($user, (int)$motherId, (int)$fatherId, $birthDate);
        }
        public function getFemalesFromCage($user, $cageId) {
            $this->_storageAPI->setFetchMode(FAPI::FETCH_ASS_ARRAY);
            $result = $this->_storageAPI->getGenderFromCage($user, $cageId, Mouse::SEX_FEMALE);
            $this->_storageAPI->resetFetchMode();
            return $result;
        }
        public function getMalesFromCage($user, $cageId) {
            $this->_storageAPI->setFetchMode(FAPI::FETCH_ASS_ARRAY);
            $result = $this->_storageAPI->getGenderFromCage($user, $cageId, Mouse::SEX_MALE);
            $this->_storageAPI->resetFetchMode();
            return $result;
        }
        public function createNewLitter($user, $cageId, $motherId, $fatherId, $nbrPups, $birthMonth = null, $birthDay = null, $birthYear = null, $replacements = NULL) {
            $result = [];
            if ($cageId > 0) {
                $litterId  = $this->createLitter($user, $motherId, $fatherId, $birthMonth, $birthDay, $birthYear);
                for ($i=0; $i < $nbrPups; $i++) {
                    $newMouseId = $this->newMouse($user, Mouse::SEX_UNDEFINED, $litterId, $cageId, NULL);
                    $result[]   = $this->getMouse($user, $newMouseId, $replacements);
                }
            }
            return $result;
        }
        public function editMouse($user, $mouseId, $sex, $genotype) {
            $genotype = ((int)$genotype < 1) ? NULL: $genotype;
            $sex      = $this->_validateSex($sex);
            return (bool)$this->_storageAPI->editMouse($user, $mouseId, $sex, $genotype);
        }
        public function mouseDeceased($user, $mouseId) {
            return (bool)$this->_storageAPI->mouseDeceased($user, $mouseId);
        }
        public function getAllMice($user, $replacements = NULL) {
            $this->_storageAPI->setFetchMode(FAPI::FETCH_ASS_ARRAY);
            $mice = $this->_storageAPI->getAllMice($user);
            $this->_storageAPI->resetFetchMode();
            if (is_array($replacements)) $mice = $this->formatMice($mice, $replacements);
            for ($i=0; $i < count($mice); $i++) {
                $mice[$i]["birth_date"] = $this->_getDateArrayFromString($mice[$i]["birth_date"]);
                $mice[$i]["time_deceased"] = $this->_getDateArrayFromString($mice[$i]["time_deceased"]);
            }
            return $mice;
        }
        public function createGenotype($user, $genotypeName, $color) {
            return (int)$this->_storageAPI->createGenotype($user, $genotypeName, $color);
        }
        public function getGenotypes($user) {
            $this->_storageAPI->setFetchMode(FAPI::FETCH_ASS_ARRAY);
            $result = $this->_storageAPI->getGenotypes($user);
            $this->_storageAPI->resetFetchMode();
            return $result;
        }
    }
}