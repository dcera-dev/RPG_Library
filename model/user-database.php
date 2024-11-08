<?php
    namespace Models;
    //Imports
    require_once(__DIR__ . '/database.php');

    class UserDatabase {
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
        //Fetches user by username, returns false if not found.
        public static function getUserByUsername($user) {
            $query = "
                SELECT * FROM users
                WHERE users.Username = '$user'
            ";
            return self::queryDatabase($query)->fetch_assoc();
        }
        //Fetches user by userId, returns false if not found.
        public static function getUserById($id) {
            $query = "
                SELECT * FROM users
                WHERE users.UserId = '$id'
            ";
            return self::queryDatabase($query)->fetch_assoc();
        }
        //Fetches user by email, returns false if not found.
        public static function getUserByEmail($email) {
            $query = "
                SELECT * FROM users
                WHERE users.EMail = '$email'
            ";
            return self::queryDatabase($query)->fetch_assoc();
        }
        //Adds a user, returns true on success, false on failure.
        public static function addUser(
            $id,
            $user,
            $pass,
            $email
        ) {
            $query = "
                INSERT INTO users (UserId, Username, Password, EMail)
                    VALUES ('$id', '$user', '$pass', '$email')
            ";
            return self::queryDatabase($query) === true;
        }
        //Update a user, returns true on success, false on failure
        public static function updateUser(
            $id,
            $user,
            $pass,
            $email,
            $uNo
        ) {
            $query = "
                UPDATE users SET
                    UserId = '$id',
                    Username = '$user',
                    Password = '$pass',
                    EMail = '$email'
                WHERE UserNo = '$uNo'
            ";
            return self::queryDatabase($query) === true;
        }
        //Delete a user, returns true on success, false on failure
        public static function deleteUser($userNo) {
            $query = "
                DELETE FROM users
                WHERE UserNo = '$userNo'
            ";
            return self::queryDatabase($query) === true;
        }
    }