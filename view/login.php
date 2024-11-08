<?php
    session_start();

    //Imports
    require_once(__DIR__ . '/../utils/security.php');
    require_once(__DIR__ . '/../controller/user-controller.php');

    //HTTPS
    if (!Utilities\Security::checkHTTPS()) {
        header("Location: ./errors/error-http.php");
    }

    //Login
    $login_msg = isset($_SESSION['logout-msg']) ? $_SESSION['logout-msg'] : '';

    if (isset($_POST['user']) && isset($_POST['pass'])) {
        $userId = \Controllers\UserController::validateLogin($_POST['user'], $_POST['pass']);

        if ($userId) {
            $_SESSION['user'] = \Controllers\UserController::getUserById($userId)->getUserId();
            header("Location: ./home.php?uId=" . $userId);
        }
        else $login_msg = "User Not Found. Have you registered?";
    }
?>

<html>
    <head>
        <title>RPG Library - Login</title>
        <link rel="stylesheet" type="text/css" href="./styles/login.css" />
        <link rel="stylesheet" type="text/css" href="./styles/utils.css" />
        <link rel="stylesheet" type="text/css" href="./styles/resets.css" />
    </head>
    <body>
        <div class=backing-img></div>
        <div class="blurred-backing">
            <div class="background-container centered-full shadowed-box">
                <p class="text-font shadowed-text header">Login</p>
                <form method=POST>
                    <input class="text-input text-font" type=text name=user placeholder=Username required />
                    <input class="text-input text-font" type=password name=pass placeholder=Password required />
                    <input class="rounded-button sub-btn" type=submit value=Login name=login />
                </form>
                <a href='./register.php'>Register</a>
                <h3 class="login_msg text-font shadowed-text"><?php echo $login_msg; ?></h3>
            </div>
        </div>
    </body>
</html>