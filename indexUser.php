<?php
    include('main.php');
    
    $sqlGames = 'SELECT * FROM game';
    $resultGames = mysqli_query($conn, $sqlGames);
    $games = mysqli_fetch_all($resultGames, MYSQLI_ASSOC);

    if(isset($_GET['choseGame'])) {
        $choseGame = $_GET['choseGame'];
        $sqlEntry = "SELECT * FROM entry WHERE idGame = $choseGame ORDER BY date desc";
    } else {
        $sqlEntry = "SELECT * FROM entry ORDER BY date desc";
    }

    $resultEntry = mysqli_query($conn, $sqlEntry);
    $entries = mysqli_fetch_all($resultEntry, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Strona główna</title>
    <link rel="stylesheet" href="styles/style.css">
    <link rel="stylesheet" href="styles/styleUser.css">
    <script src="https://kit.fontawesome.com/7c17143538.js" crossorigin="anonymous"></script>
</head>
<body>
    <div class="header">
        <div class="btnBox">
            <div class="leftBtnBox">

            </div>
            <a href="indexUser.php" class="logoBox"><img src="images/garasz_logo_white.png" alt="logo GARASZ" class="logo"></a>
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
        <div class="addPostContainer">
            <h2>Stwórz nowe ogłoszenie</h2>
            <a href="add.php" class="addBtn">Dodaj wpis</a>
        </div>
        <div class="gridContainer">
            <div class="postContainer">
                <h1>Najnowsze wpisy</h1>
                <?php if(!empty($entries)):?>
                <?php foreach($entries as $entry):?>
                <a class="post" href="entry.php?entryId=<?php echo $entry['id'];?>">
                    <div class="postHeader">
                        <div class="postUser">
                            <img src="./upload/<?php $idU = $entry['idUser']; echo mysqli_fetch_row(mysqli_query($conn, "SELECT profilePicture FROM user WHERE idUserInfo = $idU"))[0];?>" alt="user logo" class="userPic">
                            <p class="userName"><?php $idU = $entry['idUser']; echo mysqli_fetch_row(mysqli_query($conn, "SELECT login FROM userinfo WHERE id = $idU"))[0];?> <span class="date">| <?php echo date('G:i Y-m-d', $entry['date']);?></span></p>
                        </div>
                        <div class="postGame">
                            <p class="gameName"><?php $idG = $entry['idGame']; echo mysqli_fetch_row(mysqli_query($conn, "SELECT title FROM game WHERE id = $idG"))[0];?></p>
                        </div>
                    </div>
                    <div class="postMain">
                        <h2 class="postTitle"><?php echo $entry['title'];?></h2>
                    </div>
                </a>
                <?php endforeach;?>
                <?php else:?>
                <p><?php echo "<b>Brak wpisów o tematyce:</b> " . mysqli_fetch_row(mysqli_query($conn, "SELECT title FROM game WHERE id = $choseGame"))[0];?></p>
                <?php endif;?>
            </div>

            <div class="gamesContainer">
                <h3>Temat wpisu</h3>
                <div class="input">
                    <label class="name">Wyszukaj</label>
                    <input type="text" id="gameSearch" class="gameSearch">
                </div>
                <div class="gamesBox">
                        <a href="indexUser.php" class="gameBtn o">
                            <div class="allGames"></div>
                            <p class="gameTitle">Wszystkie gry</p>
                        </a>
                    <?php foreach($games as $game):?>
                        <a href="indexUser.php?choseGame=<?php echo $game['id']?>" class="gameBtn">
                            <img src="images/<?php echo $game['logo']?>" alt="game image">
                            <p class="gameTitle"><?php echo $game['title'];?></p>
                        </a>
                    <?php endforeach;?>
                </div>
            </div>
        </div>
    </div>

    <div class="footer">
        
    </div>

    <script src="scripts/scriptUser.js"></script>
</body>
</html>