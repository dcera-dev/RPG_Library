<?php
    namespace Utilities;
    class Security {
        //Returns boolean: true if https, false if http
        public static function checkHTTPS() {
            if (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === "off") {
                $location = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
                header("HTTP/1.1 301 Moved Permanently");
                header('Location: ' . $location);
                exit;
            }
            else {
                //HTTPS is on
                return true;
            }
        }

        //Handles User Logout, Clears Session Data
        public static function logout() {
            //Clear Session Data
            $cwd = $_SESSION['working-dir'];
            session_destroy();
            unset($_POST);

            //Re-Create Session
            session_start();

            //Setting Logout Message
            $_SESSION['logout-msg'] = 'Successfully logged out.';
            $_SESSION['working-dir'] = $cwd;

            //Handing off to login.php
            header('Location: ../view/login.php');
            exit();
        }
    }