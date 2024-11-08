<?php
    namespace Models;
    //Imports
    require_once(__DIR__ . '/database.php');

    class CharacterDatabase {
        /*
            queryDatabase is a helper function that simplifies query logic for UserDatabase static functions.
            It returns either the completed query, or false.
        */
        private static function queryDatabase($query) {
            $db = new Database();
            $conn = $db->getConnection();

            if (is_object($conn)) {
                return $conn->query($query);
            }
            else return false;
        }
        //Fetches all characters by a given userId
        public static function getAllUserCharacters($user) {
            $query = "
                SELECT * FROM characters
                WHERE characters.UserId = '$user'
            ";
            return self::queryDatabase($query);
        }
        //Fetches given character by characterId && userId
        public static function getCharacterById($characterId, $userId) {
            $query = "
                SELECT * FROM characters
                WHERE characters.UserId = '$userId'
                    AND characters.CharacterId = '$characterId'
            ";
            return self::queryDatabase($query)->fetch_assoc();
        }
        //Fetches characters that match filter
        public static function getCharactersByNameSearch($filter, $user) {
            $query = "
                SELECT * FROM characters
                WHERE (characters.FirstName LIKE '%$filter%'
                OR characters.LastName LIKE '%$filter%')
                AND characters.UserId = '$user'
            ";
            return self::queryDatabase($query);
        }
        //Adds a character
        public static function addCharacter(
            $charId,
            $firstName,
            $lastName,
            $filePath,
            $imagePath,
            $userId
        ) {
            $query = "
                INSERT INTO characters (CharacterId, FirstName, LastName, CharacterObject, ProfileImage, UserId)
                    VALUES ('$charId', '$firstName', '$lastName', '$filePath', '$imagePath', '$userId')
            ";
            return self::queryDatabase($query) === true;
        }
        //Update a character
        public static function updateCharacter(
            $charId,
            $firstName,
            $lastName,
            $filePath,
            $imagePath,
            $userId,
            $charNo
        ) {
            $query = "
                UPDATE characters SET
                    CharacterId = '$charId',
                    FirstName = '$firstName',
                    LastName = '$lastName',
                    CharacterObject = '$filePath',
                    ProfileImage = '$imagePath',
                    UserId = '$userId'
                WHERE CharacterNo = '$charNo'
            ";
            return self::queryDatabase($query) === true;
        }
        //Delete a character
        public static function deleteCharacter($charId, $userId) {
            $query = "
                DELETE FROM characters
                WHERE UserId = '$userId'
                    AND CharacterId = '$charId'
            ";
            return self::queryDatabase($query) === true;
        }
    }