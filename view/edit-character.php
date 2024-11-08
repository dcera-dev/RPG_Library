<?php
    session_start();

    //Imports
    require_once(__DIR__ . '/../utils/security.php');
    require_once(__DIR__ . '/../utils/image-utils.php');
    require_once(__DIR__ . '/../utils/json-utils.php');
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

    $editing = false;

    //Character
    if (isset($_GET['cId'])) {
        $currChar = \Controllers\CharacterController::getCharacterById($_GET['cId'], $currentUser->getUserId());
        $editing = true;
    }

    //Format Handling
    $genderList = ['Male', 'Female', 'Other'];
    
    //Add Character
    if (isset($_POST['save']) && !$editing) {
        //Ensure dir exists for user
        $userDir = '../db_storage/' . $currentUser->getUserId();
        if (!is_dir($userDir)) {
            mkdir($userDir);
        }

        //Generate Character Id
        $id = -1;
        while (\Controllers\CharacterController::getCharacterById($id, $currentUser->getUserId()) || $id === -1) {
            $id = rand(1, 2147483647);
        }

        //Make character dir
        $charDir = $userDir . '/' . $id;
        mkdir($charDir);

        //Create Character Object
        $charObj = new \Controllers\CharacterInfo(
            $_POST['gender'],
            $_POST['species'],
            $_POST['archetype'],
            $_POST['personalityTraits'],
            $_POST['description']
        );

        //Serialize Character Object
        $objPath = $charDir . '/info.json';
        \Utilities\JSONUtils::writeObjToFile($objPath, $charObj);

        //Upload Profile Image
        $img = $_FILES['profileImage']['name'];
        if ($img === '') {
            $imgPath = '../db_storage/placeholder.png';
        }
        else {
            $target = '../db_storage/' . $img;
            move_uploaded_file($_FILES['profileImage']['tmp_name'], $target);
            $imgPath = \Utilities\ImageUtils::processImage($target, $charDir);
        }

        //Create Character Object
        $newChar = new \Controllers\Character($id, $_POST['firstName'], $_POST['lastName'], $objPath, $imgPath, $currentUser->getUserId());

        //Upload to DB
        \Controllers\CharacterController::addCharacter($newChar);

        header("Location: ./home.php?uId=" . $currentUser->getUserId());
    }

    //Edit Character
    else if (isset($_POST['save']) && $editing) {
        $errorStr = '';
        //Ensure dirs exist
        $userDir = '../db_storage/' . $currentUser->getUserId();
        if (!is_dir($userDir)) {
            mkdir($userDir);
        }
        
        $charDir = $userDir . '/' . $currChar->getCharId();
        if (!is_dir($charDir)) {
            mkdir($charDir);
        }

        //Create Character Object
        $charObj = new \Controllers\CharacterInfo(
            $_POST['gender'],
            $_POST['species'],
            $_POST['archetype'],
            $_POST['personalityTraits'],
            $_POST['description']
        );

        //Serialize Character Object
        $objPath = $charDir . '/info.json';
        \Utilities\JSONUtils::writeObjToFile($objPath, $charObj);
        

        //Upload Profile Image
        $img = $_FILES['profileImage']['name'];
        if ($img === '') {
            $imgPath = $currChar->getProfilePath();
        }
        else {
            $target = '../db_storage/' . $img;
            move_uploaded_file($_FILES['profileImage']['tmp_name'], $target);
            $imgPath = \Utilities\ImageUtils::processImage($target, $charDir);
        }

        //Update Character Object
        $currChar->setCharObjPath($objPath);
        $currChar->setProfilePath($imgPath);
        $currChar->setFirstName($_POST['firstName']);
        $currChar->setLastName($_POST['lastName']);

        //Update DB
        \Controllers\CharacterController::updateCharacter($currChar);

        header("Location: ./home.php?uId=" . $currentUser->getUserId());
    }

    //Cancel
    if (isset($_POST['cancel'])) {
        header("Location: ./home.php?uId=" . $currentUser->getUserId());
    }

?>

<html>
    <head>
        <?php if ($editing) : ?>
            <title>RPG Library - <?php echo $currChar->getFirstName(); ?> <?php echo $currChar->getLastName(); ?></title>
        <?php else : ?>
            <title>RPG Library - New Character</title>
        <?php endif; ?>
        <link rel="stylesheet" type="text/css" href="./styles/character-card.css" />
        <link rel="stylesheet" type="text/css" href="./styles/utils.css" />
        <link rel="stylesheet" type="text/css" href="./styles/resets.css" />
    </head>
    <body class=solid-backing>
        <!--Character Info-->
        <div class="backing-img">
            <div class="blurred-backing"></div>
        </div>
        <div class="cont">
            <?php if ($editing) : ?>
            <form method=POST enctype=multipart/form-data>
                <table id=main>
                    <tr id=mainData>
                        <td id=mainDesc>
                            <h3><b>Name: <input class="text-input" type=text name=firstName value="<?php echo $currChar->getFirstName(); ?>" placeholder='First Name' required/> <input class="text-input" type=text name=lastName value="<?php echo $currChar->getLastName(); ?>" placeholder='Last Name' /></b></h3>
                            <p><b>Gender:</b> <select name=gender>
                                <?php foreach ($genderList as $gender) : ?>
                                    <?php if ($currChar->getCharInfo()->getGender() == $gender) :?>
                                        <option value="<?php echo $gender; ?>" selected><?php echo $gender; ?></option>
                                    <?php else: ?>
                                        <option value="<?php echo $gender; ?>"><?php echo $gender; ?></option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select> </p>
                            <p><b>Race:</b> <input class="text-input" type=text name=species value="<?php echo $currChar->getCharInfo()->getSpecies(); ?>" placeholder='Race' /></p>
                            <p><b>Archetype:</b> <input class="text-input" type=text name=archetype value="<?php echo $currChar->getCharInfo()->getArchetype(); ?>" placeholder=Archetype ></p>
                            <h4>Personality Traits</h4>
                            <p>
                                <textarea class="text-input" type=text name=personalityTraits placeholder="Personality Traits"><?php echo $currChar->getCharInfo()->getPersonalityTraits(); ?></textarea>
                            </p>
                            <h4>Description</h4>
                            <p>
                                <textarea class="text-input" type=text name=description placeholder=Description ><?php echo $currChar->getCharInfo()->getDescription(); ?></textarea>
                            </p>
                        </td>
                        <td id=mainImg>
                            <img src="<?php echo $currChar->getProfilePath(); ?>" id=imgPreview />
                            <input id=imgUpload accept="image/png,image/jpeg" type=file name=profileImage />
                            <p><?php echo $errorStr; ?></p>
                        </td>
                </table>
                <input class="rounded-button" type=submit name=save value=Save />
            </form>
            <form method=POST>
                <input class="rounded-button" type=submit name=cancel value=Cancel />
            </form>
            <?php else : ?>
            <form method=POST enctype=multipart/form-data>
                <table id=main>
                    <tr id=mainData>
                        <td id=mainDesc>
                            <h3><b>Name: <input class="text-input" type=text name=firstName placeholder='First Name' required/> <input class="text-input" type=text name=lastName placeholder='Last Name' /></b></h3>
                            <p><b>Gender:</b> <select name=gender>
                                <?php foreach ($genderList as $gender) : ?>
                                    <option value="<?php echo $gender; ?>"><?php echo $gender; ?></option>
                                <?php endforeach; ?>
                            </select> </p>
                            <p><b>Race:</b> <input class="text-input" type=text name=species placeholder=Race /></p>
                            <p><b>Archetype:</b> <input class="text-input" type=text name=archetype placeholder=Archetype ></p>
                            <h4>Personality Traits</h4>
                            <p>
                                <textarea class="text-input" type=text name=personalityTraits placeholder="Personality Traits"></textarea>
                            </p>
                            <h4>Description</h4>
                            <p>
                                <textarea class="text-input" type=text name=description placeholder=Description ></textarea>
                            </p>
                        </td>
                        <td id=mainImg>
                            <img src="../assets/add.png" id=imgPreview />
                            <input id=imgUpload accept="image/png,image/jpeg" type=file name=profileImage />
                            <p><?php echo $errorStr; ?></p>
                        </td>
                    </tr>
                </table>
                <input class="rounded-button" type=submit name=save value=Save />
            </form>
            <form method=POST>
                <input class="rounded-button" type=submit name=cancel value=Cancel />
            </form>
            <?php endif; ?>
        </div>
        <script src="../utils/imagePreview.js"></script>
    </body>
</html>