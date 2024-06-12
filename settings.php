<?php
session_start();
$conn = mysqli_connect('localhost', 'root', '', 'garasz_forum');

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
}

$userId = $_SESSION['id'][0];

$userResult = mysqli_query($conn, "SELECT * FROM user WHERE idUserInfo = $userId");
$userInfo = mysqli_fetch_all($userResult, MYSQLI_ASSOC);

function filtring($input) {
    if(!empty($input)) {
        $input = stripslashes($input);
        return htmlspecialchars(trim($input));
    } else {
        return NULL;
    }

}

if (isset($_POST['settingsBtn'])) {

    $name = filtring($_POST['name']);
    $surName = filtring($_POST['surName']);
    $about = filtring($_POST['about']);
    $gender = filtring($_POST['gender']);
    $birthDate = filtring($_POST['birthDate']);

    $stmt = $conn->prepare("UPDATE user SET name = ?, surName = ?, about = ?, gender = ?, birthDate = ? WHERE idUserInfo = ?");

    $stmt->bind_param('sssssi', $name, $surName, $about, $gender, $birthDate, $userId);
    $stmt->execute();
    $stmt->close();

    if (isset($_FILES['profPicture']) && $_FILES['profPicture']['error'] == UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['profPicture']['tmp_name'];
        $fileName = $_FILES['profPicture']['name'];
        $fileSize = $_FILES['profPicture']['size'];
        $fileType = $_FILES['profPicture']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        $allowedfileExtensions = array('jpg', 'gif', 'png', 'jpeg');

        if (in_array($fileExtension, $allowedfileExtensions)) {
            $uploadFileDir = './upload/';
            $dest_path = $uploadFileDir . $fileName;

            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                $query = "UPDATE user SET profilePicture = '$fileName' WHERE idUserInfo = $userId";
                mysqli_query($conn, $query);

                $msg = "Plik został pomyślnie przesłany.";
            } else {
                $msg = "Wystąpił błąd podczas przesyłania pliku.";
            }
        } else {
            $msg = "Nieprawidłowy typ pliku. Proszę przesłać plik typu: " . implode(', ', $allowedfileExtensions);
        }
    }

    header("Location: settings.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Strona główna</title>
    <link rel="stylesheet" href="styles/style.css">
    <link rel="stylesheet" href="styles/other.css">
    <script src="https://kit.fontawesome.com/7c17143538.js" crossorigin="anonymous"></script>
</head>
<body>
    <div class="header">
        <div class="btnBox">
            <div class="leftBtnBox">
                <?php if($userId === '1'):?>
                <a href="indexAdmin.php?users">Użytkownicy</a>
                <a href="indexAdmin.php?entries">Ogłoszenia</a>
                <?php endif;?>
            </div>
            <a href="index<?php if($userId === '1'){echo "Admin";}else{echo "User";}?>.php"><img src="images/garasz_logo_white.png" alt="logo GARASZ" class="logo"></a>
            <div class="rightBtnBox">
                    <?php if($userId === '1'):?>
                    <a href="indexAdmin.php?games">Gry</a>
                    <a style="visibility: hidden">Tekst niewidzialny</a>
                    <?php endif;?>
            </div>
        </div>

        <div class="logOrUser">
            <div class="userContainer">
                <div class="userBox" id="userBox">
                    <span class="userNick"><?php echo $_SESSION['user']; ?></span>
                    <img src="./upload/<?php echo $userInfo[0]['profilePicture']; ?>" alt="logo użytkownika" class="userLogo">
                </div>

                <div class="userOptions" id="userOptions">
                    <ul>
                        <li><a href="profile.php?id=<?php echo $_SESSION['id'][0]; ?>">Mój profil</a></li>
                        <li><a href="userEntries.php">Ogłoszenia</a></li>
                        <li><a href="settings.php">Ustawienia</a></li>
                        <li><a href="index.php?logout" class="logout">Wyloguj</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="main">
        <div class="settingsContainer">
            <h1>USTAWIENIA</h1>
            
            <form action="" method="post" enctype="multipart/form-data">
                <div class="userProf">
                    <h2>Zdjęcie profilowe</h2>
                    <input type="file" name="profPicture">
                </div>

                <div>
                    <h2>Dane osobowe</h3>
                    <div class="userData">
                        <?php foreach ($userInfo as $info): ?>
                            <div class="box">
                                <label class="name">Imię</label>
                                <input type="text" name="name" class="input" value="<?php echo $info['name']; ?>">
                            </div>
                            <div class="box">
                                <label class="name">Nazwisko</label>
                                <input type="text" name="surName" class="input" value="<?php echo $info['surName']; ?>">
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div>
                    <h2>Dodatkowe</h2>
                    <div class="userData">
                        <?php foreach ($userInfo as $info) : ?>
                            <div class="box">
                                <label class="name">Data urodzenia</label>
                                <input type="date" name="birthDate" value="<?php echo $info['birthDate']; ?>" class="input">
                            </div>
                            <div class="box">
                                <label class="name">Płeć</label>
                                <div class="input d">
                                    <div><input type="radio" name="gender" value="Mężczyzna" <?php if ($info['gender'] == "Mężczyzna") {echo "checked";}?>>Mężczyzna</div>
                                    <div><input type="radio" name="gender" value="Kobieta" <?php if ($info['gender'] == "Kobieta") {echo "checked";} ?>> Kobieta</div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div>
                    <h2>O mnie</h2>
                    <div class="boxA">
                        <label class="name">Opis</label>
                        <textarea name="about" class="about"><?php echo $info['about']; ?></textarea>
                    </div>
                </div>

                <div class="btnBox">
                    <input type="submit" value="Aktualizuj" name="settingsBtn" class="settingsBtn">
                </div>
            </form>
        </div>
    </div>

    <script src="scripts/scriptUser.js"></script>
</body>
</html>
