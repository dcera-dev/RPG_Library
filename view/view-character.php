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

    //Character
    $currChar = \Controllers\CharacterController::getCharacterById($_GET['cId'], $currentUser->getUserId());

    //Return
    if (isset($_POST['back'])) {
        header("Location: ./home.php?uId=" . $currentUser->getUserId());
    }

    //Edit/Delete
    $showConfirm = false;

    if (isset($_POST['delete'])) {
        $showConfirm = true;
    }

    if (isset($_POST['conf-del'])) {
        $dir = '../db_storage/' . $currentUser->getUserId() . '/' . $currChar->getCharId();
        if (is_dir($dir)) {
            if ($currChar->getProfilePath() != '../db_storage/placeholder.png') {
                unlink($currChar->getProfilePath());
            }
            unlink($currChar->getCharObjPath());
            rmdir($dir);
            \Controllers\CharacterController::deleteCharacter($currChar->getCharId(), $currentUser->getUserId());
            header("Location: ./home.php?uId=" . $currentUser->getUserId());
        }
    }
    else if (isset($_POST['edit'])) {
        header("Location: ./edit-character.php?uId=" . $currentUser->getUserId() . '&cId=' . $currChar->getCharId());
    }
?>

<html>
    <head>
        <title>RPG Library - <?php echo $currChar->getFirstName(); ?> <?php echo $currChar->getLastName(); ?></title>
        <link rel="stylesheet" type="text/css" href="./styles/character-card.css" />
        <link rel="stylesheet" type="text/css" href="./styles/popup.css" />
        <link rel="stylesheet" type="text/css" href="./styles/utils.css" />
        <link rel="stylesheet" type="text/css" href="./styles/resets.css" />
    </head>
    <body class=solid-backing>
        <div class=backing-img>
            <div class=blurred-backing></div>
        </div>
        <!-- Confirm Delete Popup -->
         <?php if ($showConfirm) : ?>
            <div class="blurred-backing">
                <form class="popup-cont centered-full small-box" method=POST>
                    <p>Really delete <?php echo $currChar->getFirstName(); ?>?</p>
                    <div class=popup-opts>
                        <input class=rounded-button type=submit name=conf-del value=Confirm />
                        <input class=rounded-button type=submit name=can-del value=Cancel />
                    </div>
                </form>
            </div>
        <?php endif; ?>
        <!-- Character Info -->
        <div class=cont>
            <table id=main>
                <tr id=mainData>
                    <td id=mainDesc>
                        <h3><b><?php echo $currChar->getFirstName(); ?> <?php echo $currChar->getLastName(); ?></b></h3>
                        <p><b>Gender:</b> <?php echo $currChar->getCharInfo()->getGender(); ?></p>
                        <p><b>Race:</b> <?php echo $currChar->getCharInfo()->getSpecies(); ?></p>
                        <p><b>Archetype:</b> <?php echo $currChar->getCharInfo()->getArchetype(); ?></p>
                        <h4>Personality Traits</h4>
                        <p>
                            <?php echo $currChar->getCharInfo()->getPersonalityTraits(); ?>
                        </p>
                        <h4>Description</h4>
                        <p>
                            <?php echo $currChar->getCharInfo()->getDescription(); ?>
                        </p>
                    </td>
                    <td id=mainImg>
                        <img src="<?php echo $currChar->getProfilePath(); ?>" id=imgPreview />
                    </td>
                </tr>
            </table>
            <form method=POST>
                <input class=rounded-button type=submit value=Edit name=edit />
                <input class=rounded-button type=submit value=Delete name=delete />
                <input class=rounded-button type=submit value=Back name=back />
            </form>
        </div>
    </body>
</html>