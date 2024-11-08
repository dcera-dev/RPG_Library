<?php
    session_start();

    //Imports
    require_once(__DIR__ . '/../utils/security.php');
    require_once(__DIR__ . '/../utils/validation.php');
    require_once(__DIR__ . '/../controller/user-controller.php');

    //Security
    //HTTPS
    if (!Utilities\Security::checkHTTPS()) {
        header("Location: ./errors/error-http.php");
    }

    //User
    $currentUser = \Controllers\UserController::getUserById($_SESSION['user']);
    if ($currentUser->getUserId() != $_GET['uId']) {
        header("Location: ./errors/error-unauth.php");
    }

    //User Management Options Handling
    if (isset($_POST['logout'])) {
        \Utilities\Security::logout();
    }

    if (isset($_POST['settings'])) {
        header("Location: ./user-settings.php?uId=" . $currentUser->getUserId());
    }

    if (isset($_POST['home'])) {
        header("Location: ./home.php?uId=" . $currentUser->getUserId());
    }

    //Globals
    $err_msg = '';
    $changePass;

    //Settings Flags

    if (isset($_POST['change-pass'])) {
        $changePass = true;
    }
    if (isset($_POST['can-pass'])) {
        $changePass = false;
    }
    if (isset($_POST['submit-pass'])) {
        //Validation
        $changePass = true;
        $errs = array();

        //Old Password
        if ($_POST['old-pass'] != $currentUser->getPassword()) {
            $errs[] = 'Password Incorrect. Try Again';
        }

        //New Password
        if ($_POST['new-pass'] != $_POST['new-pass-conf']) {
            $errs[] = 'Password doesn\'t match.';
        }
        else {
            $errs[] = \Utilities\Validator::validateRegex($_POST['new-pass'], 
            '/^.*(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*]).{4,20}$/',
            "Password must be between 4 and 20 characters, and contain an uppercase letter, a digit, and a special character in this range: [!@#$%^&*]",
            true);
        }

        if (\Utilities\Validator::checkErrors($errs)) {
            //Password valid
            $currentUser->setPassword($_POST['new-pass']);
            $msg = \Controllers\UserController::updateUser($currentUser);
            $changePass = false;
        }
        else {
            //Show Errors
            foreach ($errs as $err) {
                $err_msg .= $err . "<br /><br />";
            }
        }
    }
?>

<html>
    <head>
        <title>RPG Library - <?php echo $currentUser->getUsername(); ?>'s Settings</title>
        <link rel="stylesheet" type="text/css" href="./styles/nav.css" />
        <link rel="stylesheet" type="text/css" href="./styles/library.css" />
        <link rel="stylesheet" type="text/css" href="./styles/utils.css" />
        <link rel="stylesheet" type="text/css" href="./styles/resets.css" />
    </head>
    <body class=solid-backing>
        <div class="backing-img backing-reset">
            <div class=blurred-backing></div>
        </div>
        <!-- User Management Interface -->
         <!-- User Management Interaction -->
        <div class="nav-container text-font">
            Welcome,
            <div class=dropdown>
                <button class="nav-button text-font text-bold">
                    <b><?php echo $currentUser->getUsername(); ?></b>
                    <div class=img-cont>
                        <img class="caret-down" src="../assets/caret-down-icon.png"/>
                    </div>
                </button>
                <form class="dropdown-content small-box shadowed-text" method=POST>
                    <input class="dropdown-opt text-font" type=submit name=home value=Home />
                    <input class="dropdown-opt text-font" type=submit name=settings value=Settings disabled />
                    <input class="dropdown-opt text-font" type=submit name=logout value=Logout />
                </form>
            </div>
         </div>
         <!-- User Settings -->
          <div class=cont>
            <?php if ($changePass) : ?>
                <div class="formCont centered-full shadowed-box">
                    <form method=POST>
                        <input class=text-input type=password name=new-pass placeholder="New Password" required />
                        <input class=text-input type=password name=new-pass-conf placeholder="Re-Type New Password" required />
                        <input class=text-input type=password name=old-pass placeholder="Old Password" required />
                        <input class=rounded-button type=submit value="Change Password" name=submit-pass />
                    </form>
                    <form method=POST>
                        <input class=rounded-button type=submit value=Cancel name=can-pass />
                    </form>
                    <div class=err-div>
                        <p class="text-font err-msg">
                            <?php echo $err_msg; ?>
                        </p>
                    </div>
                </div>
            <?php else : ?>
                <div class="formCont centered-full shadowed-box">
                    <p class="text-font header">User Settings</p>
                    <form method=POST>
                        <input class=rounded-button type=submit value="Change Password" name=change-pass />
                    </form>
                </div>
            <?php endif; ?>
          </div>
    </body>
</html>