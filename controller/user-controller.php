<?php
    namespace Controllers;

    //Imports
    require_once(__DIR__ . '/../model/user-database.php');

    //User Object -- Represents a single entry in the users table.
    class User {
        //Props
        private $userNo;
        private $userId;
        private $username;
        private $password;
        private $email;

        //Construct
        public function __construct(
            $userId,
            $username,
            $password,
            $email,
            $userNo = null
        ) {
            $this->userId = $userId;
            $this->username = $username;
            $this->password = $password;
            $this->email = $email;
            $this->userNo = $userNo;
        }

        //Getters
        public function getUserNo() {
            return $this->userNo;
        }
        public function getUserId() {
            return $this->userId;
        }
        public function getUsername() {
            return $this->username;
        }
        public function getPassword() {
            return $this->password;
        }
        public function getEmail() {
            return $this->email;
        }

        //Setters
        public function setUserNo($val) {
            $this->userNo = $val;
        }
        public function setUserId($val) {
            $this->userId = $val;
        }
        public function setUsername($val) {
            $this->username = $val;
        }
        public function setPassword($val) {
            $this->password = $val;
        }
        public function setEmail($val) {
            $this->email = $val;
        }
    }

    class UserController {
        /*
            parseUser converts an assoc array from a database query into a User object.
        */
        private static function parseUser($data) {
            $user = new User(
                $data['UserId'],
                $data['Username'],
                $data['Password'],
                $data['EMail'],
            );
            $user->setUserNo($data['UserNo']);
            return $user;
        }
        //Get User by UserId
        public static function getUserById($id) {
            $res = \Models\UserDatabase::getUserById($id);
            if ($res) {
                return self::parseUser($res);
            }
            else return false;
        }
        //Get User by Username
        public static function getUserByUsername($user) {
            $res = \Models\UserDatabase::getUserByUsername($user);
            if ($res) {
                return self::parseUser($res);
            }
            else return false;
        }
        //Get User by EMail
        public static function getUserByEmail($email) {
            $res = \Models\UserDatabase::getUserByEmail($email);
            if ($res) {
                return self::parseUser($res);
            }
            else return false;
        }
        //Add a User
        public static function addUser($user) {
            return \Models\UserDatabase::addUser(
                $user->getUserId(),
                $user->getUsername(),
                $user->getPassword(),
                $user->getEmail()
            );
        }
        //Update a User
        public static function updateUser($user) {
            return \Models\UserDatabase::updateUser(
                $user->getUserId(),
                $user->getUsername(),
                $user->getPassword(),
                $user->getEmail(),
                $user->getUserNo()
            );
        }
        //Delete a User
        public static function deleteUser($userNo) {
            return \Models\UserDatabase::deleteUser($userNo);
        }
        //Validate a Login attempt against the database
        public static function validateLogin($user, $pass) {
            $res = \Models\UserDatabase::getUserByUsername($user);

            if ($res) {
                $user = self::parseUser($res);
                if ($user->getPassword() === $pass) {
                    return $user->getUserId();
                }
            }
            return false;
        }
    }