<?php
    session_start();

    //Imports
    require_once(__DIR__ . '/../utils/security.php');
    require_once(__DIR__ . '/../controller/user-controller.php');
    require_once(__DIR__ . '/../controller/character-controller.php');

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

    //Get Character List To Render
    $chars = \Controllers\CharacterController::getAllUserCharacters($currentUser->getUserId());

    //Search Handling
    if (isset($_POST['search-sub'])) {
        $filter = $_POST['search'];
        $filter = str_replace('*', '%', $filter);
        $chars = \Controllers\CharacterController::getCharactersByNameSearch($filter, $currentUser->getUserId());
    }

    //Add Character Handling
    if (isset($_POST['add'])) {
        header("Location: ./edit-character.php?uId=" . $currentUser->getUserId());
    }

    //View Character Handling
    if (isset($_POST['view'])) {
        $charId = key($_POST['view']);
        header("Location: ./view-character.php?uId=" . $currentUser->getUserId() . '&cId=' . $charId);
    }
?>

<html>
    <head>
        <title>RPG Library - <?php echo $currentUser->getUsername(); ?></title>
        <link rel="stylesheet" type="text/css" href="./styles/library.css" />
        <link rel="stylesheet" type="text/css" href="./styles/nav.css" />
        <link rel="stylesheet" type="text/css" href="./styles/utils.css" />
        <link rel="stylesheet" type="text/css" href="./styles/resets.css" />
    </head>
    <body class=solid-backing>
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
                    <input class="dropdown-opt text-font" type=submit name=home value=Home disabled />
                    <input class="dropdown-opt text-font" type=submit name=settings value=Settings />
                    <input class="dropdown-opt text-font" type=submit name=logout value=Logout />
                </form>
            </div>
         </div>
        <!-- Search -->
         <form class=searchBox method=POST>
            <input class="text-input" type=text placeholder="Character Name..." name=search />
            <input class=rounded-button type=submit value=Search name=search-sub />
         </form>
        <!-- Character Tiles -->
        <div>
            <form method=POST>
                <div class="backing-img">
                    <div class="blurred-backing"></div>
                </div>
                <div class=tile-container>
                    <?php foreach ($chars as $char) : ?>
                        <button class="characterTile contrast-box" name='view[<?php echo $char->getCharId(); ?>]' >
                            <div class=imgCont>
                                <img src="<?php echo $char->getProfilePath(); ?>" />
                            </div>
                            <div class="textCont text-contrast">
                                <b><?php echo $char->getFirstName(); ?> <?php echo $char->getLastName(); ?></b>
                            </div>
                        </button>
                    <?php endforeach; ?>
                    <button class="characterTile contrast-box" name='add' type='submit' >
                        <div class=imgCont>
                            <img src='../assets/add.png' />
                        </div>
                        <div class="textCont text-contrast">
                            <b>New Character</b>
                        </div>
                    </button>
                </div>
            </form>
        </div>
    </body>
</html>