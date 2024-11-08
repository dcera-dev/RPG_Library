<?php
    namespace Controllers;

    //Imports
    require_once(__DIR__ . '/../model/character-database.php');
    require_once(__DIR__ . '/../utils/json-utils.php');

    //Character Object -- Represents a single entry in the Characters table
    class Character {
        //Props
        private $charNo;
        private $charId;
        private $firstName;
        private $lastName;
        private $filePath;
        private $characterInfo;
        private $profileImage;
        private $userId;

        //Construct
        public function __construct(
            $charId,
            $firstName,
            $lastName,
            $filePath,
            $profileImage,
            $userId,
            $charNo = null
        ) {
            $this->charId = $charId;
            $this->firstName = $firstName;
            $this->lastName = $lastName;
            $this->filePath = $filePath;
            $this->profileImage = $profileImage;
            $this->userId = $userId;
            $this->charNo = $charNo;

            $this->characterInfo = \Utilities\JSONUtils::readFileToObj($this->filePath);
        }

        //Getters
        public function getCharNo() {
            return $this->charNo;
        }
        public function getCharId() {
            return $this->charId;
        }
        public function getFirstName() {
            return $this->firstName;
        }
        public function getLastName() {
            return $this->lastName;
        }
        public function getCharObjPath() {
            return $this->filePath;
        }
        public function getCharInfo() {
            return $this->characterInfo;
        }
        public function getProfilePath() {
            return $this->profileImage;
        }
        public function getUserId() {
            return $this->userId;
        }
        //Setters
        public function setCharNo($val) {
            $this->charNo = $val;
        }
        public function setCharId($val) {
            $this->charId = $val;
        }
        public function setFirstName($val) {
            $this->firstName = $val;
        }
        public function setLastName($val) {
            $this->lastName = $val;
        }
        public function setCharObjPath($val) {
            $this->filePath = $val;
            $this->characterInfo = \Utilities\JSONUtils::readFileToObj($this->filePath);
        }
        public function setProfilePath($val) {
            $this->profileImage = $val;
        }
        public function setUserId($val) {
            $this->userId = $val;
        }
    }
    //Class to hold descriptive character info
    class CharacterInfo {
        private $gender;
        private $species;
        private $archetype;
        private $personalityTraits;
        private $description;

        //Constructor
        public function __construct(
            $gender,
            $species,
            $archetype,
            $personalityTraits,
            $description
        ) {
            $this->gender = $gender;
            $this->species = $species;
            $this->archetype = $archetype;
            $this->personalityTraits = $personalityTraits;
            $this->description = $description;
        }

        //Getters
        public function getGender() {
            return $this->gender;
        }
        public function getSpecies() {
            return $this->species;
        }
        public function getArchetype() {
            return $this->archetype;
        }
        public function getPersonalityTraits() {
            return $this->personalityTraits;
        }
        public function getDescription() {
            return $this->description;
        }

        //Setters
        public function setGender($val) {
            $this->gender = $val;
        }
        public function setSpecies($val) {
            $this->species = $val;
        }
        public function setArchetype($val) {
            $this->archetype = $val;
        }
        public function setPersonalityTraits($val) {
            $this->personalityTraits = $val;
        }
        public function setDescription($val) {
            $this->description = $val;
        }
    }
    class CharacterController {
        //parseCharacter converts and assoc array from a query into a Character Object
        private static function parseCharacter($data) {
            $character = new Character(
                $data['CharacterId'],
                $data['FirstName'],
                $data['LastName'],
                $data['CharacterObject'],
                $data['ProfileImage'],
                $data['UserId']
            );
            $character->setCharNo($data['CharacterNo']);
            return $character;
        }
        //getAllUserCharacters returns a list of all characters associated to a user
        public static function getAllUserCharacters($user) {
            $res = \Models\CharacterDatabase::getAllUserCharacters($user);
            if ($res) {
                $chars = array();
                foreach ($res as $char) {
                    $chars[] = self::parseCharacter($char);
                }
                return $chars;
            }
            else return false;
        }
        //getCharacterById returns a given character by id and user id
        public static function getCharacterById($id, $user) {
            $res = \Models\CharacterDatabase::getCharacterById($id, $user);
            if ($res) {
                return self::parseCharacter($res);
            }
            else return false;
        }
        //Character Name Search
        public static function getCharactersByNameSearch($filter, $user) {
            $res = \Models\CharacterDatabase::getCharactersByNameSearch($filter, $user);
            if ($res) {
                $chars = array();
                foreach ($res as $char) {
                    $chars[] = self::parseCharacter($char);
                }
                return $chars;
            }
            else return false;
        }
        public static function addCharacter($char) {
            return \Models\CharacterDatabase::addCharacter(
                $char->getCharId(),
                $char->getFirstName(),
                $char->getLastName(),
                $char->getCharObjPath(),
                $char->getProfilePath(),
                $char->getUserId()
            );
        }
        public static function updateCharacter($char) {
            return \Models\CharacterDatabase::updateCharacter(
                $char->getCharId(),
                $char->getFirstName(),
                $char->getLastName(),
                $char->getCharObjPath(),
                $char->getProfilePath(),
                $char->getUserId(),
                $char->getCharNo()
            );
        }
        public static function deleteCharacter($id, $user) {
            return \Models\CharacterDatabase::deleteCharacter($id, $user);
        }
    }