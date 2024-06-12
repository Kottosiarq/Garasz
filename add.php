<?php  
    include('main.php');

    $sqlGames = 'SELECT * FROM game';
    $resultGames = mysqli_query($conn, $sqlGames);
    $games = mysqli_fetch_all($resultGames, MYSQLI_ASSOC);

    $msg1 = $msg2 = "";
    $inpCount = 0;

    if(isset($_POST['entryBtn'])) {
        if(filtring($_POST['entryTitle']) !== 0 && filtring($_POST['entryContent']) !== 0) {
            $date = time();
            $title = $_POST['entryTitle'];
            $content = $_POST['entryContent'];
            $gameId = $_POST['entryGame'];

            if(strlen($title) <= 10) {
                $msg1 = "Tytuł powinien zawierać > 10 znaków";
            } else {
                $inpCount += 1;
            }
            
            if(strlen($content) <= 20) {
                $msg2 = "Treść powinna zawierać > 20 znaków";
            } else {
                $inpCount += 1;
            }
        }

        if($inpCount == 2) {
            mysqli_query($conn, "INSERT INTO entry (idGame, idUser, date, title, content) VALUES ($gameId, $userId, $date, '$title', '$content')");
            header("Location: indexUser.php");
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
    <link rel="stylesheet" href="styles/add.css">
    <script src="https://kit.fontawesome.com/7c17143538.js" crossorigin="anonymous"></script>
</head>
<body>
    <div class="header">
        <div class="btnBox">
            <div class="leftBtnBox">
                
            </div>
            <a href="indexUser.php"><img src="images/garasz_logo_white.png" alt="logo GARASZ" class="logo"></a>
            <div class="rightBtnBox">
                  
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
       <div class="addEntryContainer">
            <h1>DODAJ WPIS</h1>
            <form action="" method="post">
                <div class="entryBox">
                    <label class="name">Wybierz grę</label>
                    <select name="entryGame" class="entryGame">
                        <?php foreach($games as $game):?>
                        <option value="<?php echo $game['id']?>"><?php echo $game['title']?></option>
                        <?php endforeach;?>
                    </select>
                </div>
                
                <div class="entryBox">
                    <label class="name">Tytuł wpisu</label>
                    <input type="text" name="entryTitle" value="<?php if(isset($_POST['entryBtn'])){echo $_POST['entryTitle'];}?>">  
                    <p class="info"><?php echo $msg1;?></p>                  
                </div>

                <div class="entryBox">
                    <label class="name">Treść wpisu</label>
                    <textarea name="entryContent" class="entryContent"><?php if(isset($_POST['entryBtn'])){echo $_POST['entryContent'];}?></textarea>
                    <p class="info"><?php echo $msg2;?></p>                  
                </div>

                <input type="submit" value="Utwórz wpis" name="entryBtn" id="entryBtn">
            </form>
       </div>
    </div>

    <script src="scripts/scriptUser.js"></script>
</body>
</html>