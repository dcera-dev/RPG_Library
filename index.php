<?php
    //Imports
    require_once(__DIR__ . '/utils/security.php');

    session_start();

    if (!Utilities\Security::checkHTTPS()) {
        header('Location: view/errors/error-http.php');
    }
    else {
        $_SESSION['logout-msg'] = '';
        $_SESSION['working-dir'] = getcwd();
        header('Location: view/login.php');
    }