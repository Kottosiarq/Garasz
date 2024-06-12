<?php  
    include('main.php');

    if($_SESSION['id'][0] !== '1') {
        session_destroy();
        header("Location: index.php");
    }

    $sqlUsers = 'SELECT * FROM user INNER JOIN userinfo ON user.idUserInfo = userinfo.id WHERE id <> 1';
    $resultUsers = mysqli_query($conn, $sqlUsers);
    $users = mysqli_fetch_all($resultUsers, MYSQLI_ASSOC);

    $sqlEntry = "SELECT * FROM entry ORDER BY date desc";
    $resultEntry = mysqli_query($conn, $sqlEntry);
    $entries = mysqli_fetch_all($resultEntry, MYSQLI_ASSOC);

    $sqlGames = 'SELECT * FROM game';
    $resultGames = mysqli_query($conn, $sqlGames);
    $games = mysqli_fetch_all($resultGames, MYSQLI_ASSOC);

    if(isset($_GET['deleteUser'])) {
        $deleteId = $_GET['deleteUser'];
        mysqli_query($conn, "DELETE FROM user WHERE idUserInfo = $deleteId");
        mysqli_query($conn, "DELETE FROM userinfo WHERE id = $deleteId");
        mysqli_query($conn, "DELETE FROM entry WHERE idUser = $deleteId");
        mysqli_query($conn, "DELETE FROM comment WHERE idUser = $deleteId");
        header("Location: " . $_SERVER['HTTP_REFERER']);
    }

    if(isset($_GET['deleteEntry'])) {
        $deleteId = $_GET['deleteEntry'];
        mysqli_query($conn, "DELETE FROM entry WHERE id = $deleteId");
        mysqli_query($conn, "DELETE FROM comment WHERE idEntry = $deleteId");
        header("Location: " . $_SERVER['HTTP_REFERER']);
    }

    if(isset($_GET['deleteGame'])) {
        $deleteId = $_GET['deleteGame'];
        mysqli_query($conn, "DELETE FROM game WHERE id = $deleteId");
        mysqli_query($conn, "DELETE FROM comment WHERE idEntry IN (SELECT id FROM entry WHERE idGame = $deleteId)");
        mysqli_query($conn, "DELETE FROM entry WHERE idGame = $deleteId");
        header("Location: " . $_SERVER['HTTP_REFERER']);
    }

    $msg = '';
    $msg1 = '';
    $msg2 = '';
    $fileName = '';
    $inpCount = 0;
    
    if (isset($_POST['addGame'])) {
        if (isset($_FILES['profPicture']) && $_FILES['profPicture']['error'] == UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['profPicture']['tmp_name'];
            $fileName = $_FILES['profPicture']['name'];
            $fileSize = $_FILES['profPicture']['size'];
            $fileType = $_FILES['profPicture']['type'];
            $fileNameCmps = explode(".", $fileName);
            $fileExtension = strtolower(end($fileNameCmps));
    
            $allowedfileExtensions = array('jpg', 'gif', 'png', 'jpeg');
    
            if (in_array($fileExtension, $allowedfileExtensions)) {
                $uploadFileDir = './images/';
                $dest_path = $uploadFileDir . $fileName;
    
                if (move_uploaded_file($fileTmpPath, $dest_path)) {
                    $inpCount += 1;
                } else {
                    $msg = "Wystąpił błąd podczas przesyłania pliku.";
                }
            } else {
                $msg = "Nieprawidłowy typ pliku. Proszę przesłać plik typu: " . implode(', ', $allowedfileExtensions);
            }
        } else {
            $msg = "Wystąpił błąd podczas przesyłania pliku.";
        }
    
        if (filtring($_POST['gameTitle']) !== 0 && filtring($_POST['gameContent']) !== 0) {
            $title = $_POST['gameTitle'];
            $content = $_POST['gameContent'];
    
            if (strlen($title) > 50) {
                $msg1 = "Tytuł powinien zawierać < 50 znaków";
            } else {
                $inpCount += 1;
            }
    
            if (strlen($content) > 200) {
                $msg2 = "Treść powinna zawierać < 200 znaków";
            } else {
                $inpCount += 1;
            }
        } else {
            if (filtring($_POST['gameTitle']) === 0) {
                $msg1 = "Tytuł jest wymagany.";
            }
            if (filtring($_POST['gameContent']) === 0) {
                $msg2 = "Treść jest wymagana.";
            }
        }
    
        if ($inpCount === 3) {
            $stmt = $conn->prepare("INSERT INTO game (title, description, logo) VALUES (?, ?, ?)");
            if ($stmt === false) {
                die('Prepare failed: ' . htmlspecialchars($conn->error));
            }
    
            $bind = $stmt->bind_param("sss", $title, $content, $fileName);
            if ($bind === false) {
                die('Bind param failed: ' . htmlspecialchars($stmt->error));
            }
    
            $exec = $stmt->execute();
            if ($exec === false) {
                die('Execute failed: ' . htmlspecialchars($stmt->error));
            }
    
            $stmt->close();
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit();
        } else {
            echo $msg;
            echo $msg1;
            echo $msg2;
        }
    }
    


?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Strona główna</title>
    <link rel="stylesheet" href="styles/style.css">
    <link rel="stylesheet" href="styles/admin.css">
    <script src="https://kit.fontawesome.com/7c17143538.js" crossorigin="anonymous"></script>
</head>
<body>
    <div class="header">
        <div class="btnBox">
            <div class="leftBtnBox">
                <a href="indexAdmin.php?users">Użytkownicy</a>
                <a href="indexAdmin.php?entries">Ogłoszenia</a>
            </div>
            <a href="indexAdmin.php"><img src="images/garasz_logo_white.png" alt="logo GARASZ" class="logo"></a>
            <div class="rightBtnBox">
                  <a href="indexAdmin.php?games">Gry</a>
                  <a style="visibility: hidden">Tekst niewidzialny</a>
            </div>
        </div>

        <div class="logOrUser">
            <div class="userContainer">
                <div class="userBox" id="userBox">
                    <span class="userNick"><?php echo $_SESSION['user']?></span>
                    <img src="./upload/<?php echo $userInfo[0]['profilePicture']; ?>" alt="logo użytkownika" class="userLogo">
                </div>

                <div class="userOptions" id="userOptions">
                    <ul>
                        <li><a href="profile.php?id=<?php echo $_SESSION['id'][0];?>">Mój profil</a></li>
                        <li><a href="userEntries.php">Ogłoszenia</a></li>
                        <li><a href="settings.php">Ustawienia</a></li>
                        <li><a href="index.php?logout" class="logout">Wyloguj</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="main">
        <div class="containerMain" style="display: <?php echo !empty($_GET) ? 'none' : 'block'; ?>">
            <h2>Witaj, Administratorze!</h2>
            <p>Cieszymy się, że jesteś z nami. Twoja rola jako administratora jest kluczowa dla utrzymania porządku, bezpieczeństwa i dynamiki naszej platformy. Dzięki Tobie nasze forum może funkcjonować bez zakłóceń, a użytkownicy mogą cieszyć się najlepszymi doświadczeniami.</p>
            
            <b>Jako administrator masz dostęp do szerokiego zakresu narzędzi i funkcji, które pozwalają na skuteczne zarządzanie treścią, użytkownikami oraz wszelkimi aspektami technicznymi naszego serwisu. Oto, co możesz zrobić:</b>
            
            <ul>
                <li><strong>Zarządzanie użytkownikami:</strong> Możesz dodawać, usuwać i modyfikować konta użytkowników, nadawać im odpowiednie uprawnienia oraz monitorować ich aktywność. Dzięki temu zapewnisz, że nasza społeczność będzie bezpieczna i przyjazna dla wszystkich.</li>
                <li><strong>Moderacja treści:</strong> Masz możliwość przeglądania i moderowania postów, komentarzy oraz zgłoszeń użytkowników. Twoja czujność pozwala na szybkie reagowanie na wszelkie nieodpowiednie treści oraz rozwiązywanie konfliktów.</li>
                <li><strong>Zarządzanie ogłoszeniami i grami:</strong> Możesz dodawać, edytować oraz usuwać ogłoszenia i gry. Dbając o aktualność i jakość publikowanych materiałów, przyczyniasz się do rozwoju naszego serwisu.</li>
                <li><strong>Analiza danych:</strong> Dostęp do statystyk i raportów pozwala Ci na monitorowanie aktywności użytkowników oraz efektywności podejmowanych działań. Dzięki temu możesz lepiej planować przyszłe działania i usprawnienia.</li>
                <li><strong>Wsparcie techniczne:</strong> Jesteś pierwszą linią wsparcia dla użytkowników, którzy napotkają problemy techniczne. Twoja wiedza i umiejętności pozwalają na szybkie i skuteczne rozwiązywanie zgłoszeń, co zwiększa zadowolenie naszych użytkowników.</li>
                <li><strong>Rozwój i innowacje:</strong> Masz możliwość proponowania i wdrażania nowych funkcji oraz usprawnień, które przyczynią się do rozwoju naszej platformy. Twoje pomysły są dla nas niezwykle cenne!</li>
            </ul>
            
            <p>Dziękujemy za Twoje zaangażowanie i poświęcony czas. Twoja praca jest fundamentem naszej społeczności. Razem tworzymy miejsce, które jest nie tylko funkcjonalne, ale przede wszystkim przyjazne dla użytkowników.</p>
        </div>

        <div class="containerUsers" style="display: <?php if(isset($_GET['users'])){echo "block";};?>">
            <h1>UŻYTKOWNICY</h1>
            <?php foreach($users as $user):?>
            <div class="user">
                <a href="profile.php?id=<?php echo $user['id']?>" class="mainBox">
                    <img src="upload/<?php echo $user['profilePicture']?>" alt="user profile picture" class="userProfPic">
                    <p class="userLogin"><?php echo $user['login'];?></p>
                    <p class="userName"><?php echo $user['name'] . " " . $user['surName'];?></p>
                </a>
                <div class="doBox">
                    <button type="button" class="deleteUser">Usuń</button>
                    <div class="trueBox">
                        <a href="indexAdmin.php?deleteUser=<?php echo $user['id'];?>" class="trueDelete">T</a>
                        <button type="button" class="keepUser">N</button>
                    </div>
                </div>
            </div>
            <?php endforeach;?>
        </div>

        <div class="containerEntries" style="display: <?php if(isset($_GET['entries'])){echo "block";};?>">
            <h1>OGŁOSZENIA</h1>
            <?php foreach($entries as $entry):?>
            <div class="entry">
                <div class="mainBox">
                    <a href="entry.php?entryId=<?php echo $entry['id'];?>" class="entryName"><?php echo $entry['title'];?></a>
                </div>
                <div class="doBox">
                    <button type="button" class="deleteEntry">Usuń</button>
                    <div class="trueBox">
                        <a href="indexAdmin.php?deleteEntry=<?php echo $entry['id'];?>" class="trueDelete">T</a>
                        <button type="button" class="keepEntry">N</button>
                    </div>
                </div>
            </div>
            <?php endforeach;?>
        </div>

        <div class="containerGames" style="display: <?php if(isset($_GET['games'])){echo "block";};?>">
            <h1>GRY</h1>
            <h2>Dodaj grę</h2>
            <div class="addGameContainer">
                <form action="" method="post" enctype="multipart/form-data">
                <div class="gameLogo">
                    <h3>Zdjęcie profilowe</h3>
                    <input type="file" name="profPicture">
                </div>
                <p class="info"><?php echo $msg;?></p>                  

                <div class="gameBox">
                    <label class="name">Tytuł gry</label>
                    <input type="text" name="gameTitle" value="<?php if(isset($_POST['entryBtn'])){echo $_POST['entryTitle'];}?>">  
                    <p class="info"><?php echo $msg1;?></p>                  
                </div>

                <div class="gameBox">
                    <label class="name">Opis gry</label>
                    <textarea name="gameContent" class="gameContentInp"><?php if(isset($_POST['entryBtn'])){echo $_POST['entryContent'];}?></textarea>
                    <p class="info"><?php echo $msg2;?></p>                  
                </div>

                <input type="submit" value="Dodaj grę" name="addGame" id="addGame">
                </form>
            </div>
            
            <h2>Wyszukaj / Usuń</h2>
            <div class="gamesContainer">
                <div class="input">
                    <label class="name sec">Wyszukaj</label>
                    <input type="text" id="gameSearch" class="gameSearch">
                </div>
                <div class="gamesBox">
                    <?php foreach($games as $game):?>
                        <div class="gameBtn">
                            <div class="mainBox">
                                <img src="images/<?php echo $game['logo']?>" alt="game image">
                                <p class="gameTitle"><?php echo $game['title'];?></p>
                            </div>
                            <div class="doBox">
                                <button type="button" class="deleteEntry">Usuń</button>
                                <div class="trueBox">
                                    <a href="indexAdmin.php?deleteGame=<?php echo $game['id'];?>" class="trueDelete">T</a>
                                    <button type="button" class="keepEntry">N</button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach;?>
                </div>
            </div>
        </div>
    </div>
    <script src="scripts/admin.js"></script>
</body>
</html>