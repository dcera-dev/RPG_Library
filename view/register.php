<?php
    session_start();

    //Imports
    require_once(__DIR__ . '/../utils/security.php');
    require_once(__DIR__ . '/../utils/validation.php');
    require_once(__DIR__ . '/../controller/user-controller.php');

    //Check HTTPS
    if (!Utilities\Security::checkHTTPS()) {
        header("Location: ./errors/error-http.php");
    }

    $err_msg = '';

    //Registration
    if (isset($_POST['reg'])) {
        $errs = array();

        //Username Validation
        $user = $_POST['user'];
        if (\Controllers\UserController::getUserByUsername($user)) {
            //Username is in database already
            $errs[] = $user . " is already taken.";
        }
        
        //Password Validation
        if (($_POST['pass'] != $_POST['pass-conf'])) {
            //Passwords don't match
            $errs[] = "Passwords don't match.";
        }
        else {
            $errs[] = \Utilities\Validator::validateRegex($_POST['pass'], 
            '/^.*(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*]).{8,20}$/',
            "Password must be between 8 and 20 characters, and contain an uppercase letter, a digit, and a special character in this range: [!@#$%^&*]",
            true);
        }

        //EMail Validation
        if (\Controllers\UserController::getUserByEmail($_POST['email'])) {
            //Email is in database
            $errs[] = "E-Mail has already been registered.";
        }
        else $errs[] = \Utilities\Validator::validateFilter($_POST['email'], FILTER_VALIDATE_EMAIL, "You must enter a valid email.", true);

        //User Registration
        if (\Utilities\Validator::checkErrors($errs)) {
            //No Errs
            //Generate UserId
            $id = -1;
            while (\Controllers\UserController::getUserById($id) || $id === -1) {
                $id = rand(1, 2147483647);
            }

            $newUser = new \Controllers\User($id, $_POST['user'], $_POST['pass'], $_POST['email']);
            $msg = \Controllers\UserController::addUser($newUser);

            $_SESSION['logout-msg'] = ($msg) ? 'Successfully Registered' : 'Registration Failed, please try again.';
            Header('Location: ./login.php');
        }
        else {
            //Display Errors
            $err_msg = '';
            foreach ($errs as $err) {
                $err_msg .= $err . "<br /><br />";
            }
        }
    }
?>

<html>
    <head>
        <title>RPG Library - Register</title>
        <link rel="stylesheet" type="text/css" href="./styles/login.css" />
        <link rel="stylesheet" type="text/css" href="./styles/utils.css" />
        <link rel="stylesheet" type="text/css" href="./styles/resets.css" />
    </head>
    <body>
        <div class=backing-img></div>
        <div class="blurred-backing">
            <div class="background-container centered-full shadowed-box">
                <p class="text-font shadowed-text header">Register</p>
                <form method=POST>
                    <input class=text-input type=text name=user placeholder=Username required />
                    <input class=text-input type=password name=pass placeholder=Password required />
                    <input class=text-input type=password name=pass-conf placeholder="Re-Enter Password" required />
                    <input class=text-input type=text name=email placeholder="E-Mail" required />
                    <input class="rounded-button sub-btn" type=submit value=Register name=reg />
                    <div class=err-div>
                        <p class=err-msg><?php echo $err_msg; ?></p>
                    </div>
                </form>
            </div>
        </div>
    </body>
</html>