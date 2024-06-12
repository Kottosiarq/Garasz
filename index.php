<?php
    session_start();
    $conn = mysqli_connect('localhost', 'root', '', 'garasz_forum');
    
    $sqlGames = 'SELECT title, description, logo FROM game';
    $resultGames = mysqli_query($conn, $sqlGames);
    $games = mysqli_fetch_all($resultGames, MYSQLI_ASSOC);

    $sqlEntry = "SELECT * FROM entry ORDER BY date desc LIMIT 2";
    $resultEntry = mysqli_query($conn, $sqlEntry);
    $entries = mysqli_fetch_all($resultEntry, MYSQLI_ASSOC);

    $sqlComment = "SELECT * FROM comment LIMIT 3";
    $resultComment = mysqli_query($conn, $sqlComment);
    $comments = mysqli_fetch_all($resultComment, MYSQLI_ASSOC);

    $countGames = mysqli_fetch_row(mysqli_query($conn, "SELECT count(*) FROM game"))[0];

    if(isset($_GET['logout'])) {
        session_destroy();
        header("Location: index.php");
    }

    $gamesArray = [];
    foreach($games as $key => $game) {
        $gamesArray[$key] = $game['logo'];
    }


?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Strona główna</title>
    <link rel="stylesheet" href="styles/style.css">
    <link rel="stylesheet" href="styles/main.css">
    <script src="https://kit.fontawesome.com/7c17143538.js" crossorigin="anonymous"></script>
</head>
<body>
    <div class="header">
        <div class="btnBox">
            <div class="leftBtnBox">
                
            </div>
            <img src="images/garasz_logo_white.png" alt="logo GARASZ" class="logo">
            <div class="rightBtnBox">
                  
            </div>
        </div>

        <div class="logOrUser">
            <div class="logContainer">
                <a href="log.php" class="logA">Zaloguj</a> | <a href="register.php" class="registerA">Zarejestruj</a>
            </div>
        </div>
    </div>

    <div class="main">
        <div class="mainInfo">
            <div class="flyingItemsContainer">
                <img src="images/f_pc.png" alt="computer" class="img1">
                <img src="images/f_handgame.png" alt="handgame" class="img2">
                <img src="images/f_mushroom.png" alt="mushroom" class="img3">
                <img src="images/f_light-bulb.png" alt="light-bulb" class="img4">
                <img src="images/f_society.png" alt="society" class="img5">
                <div class="rocketBox">
                    <img src="images/rocket.png" alt="rocket" class="rocket">
                </div>
            </div>
            <div class="mainInfoText">
                Dziel się swoimi pomysłami, <br> zdobywaj wiedzę
            </div>
        </div>

        <div class="gamesInfo">
            <h1>Wybieraj spośród <?php echo $countGames;?> gier!</h1>
            <div class="gamesOrganiser">
                <button type="button" id="btnLeft"><i class="fa-solid fa-caret-left"></i></button>
                <div id="showGames">
                    <?php foreach($games as $game):?>
                        <?php echo "<img class='games' id='".$game['title']."' src='images/".$game['logo']."'>";?>
                        <?php endforeach;?>
                    </div>
                    <button type="button" id="btnRight"><i class="fa-solid fa-caret-right"></i></button> 
                </div>
                <p id="par"></p>  
        </div>

        <div class="newEntries">
            <h1>Twórz nowe ogłoszenia!</h1>
            <?php foreach($entries as $entry):?>
                <div class="post">
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
                </div>
            <?php endforeach;?>
        </div>

        <div class="newComments">
            <h1>A także komentuj posty!</h1>
            <?php foreach($comments as $comment):?>
                    <div class="commentAll">
                        <div class="comment">
                            <div class="commentUser">
                                <img src="upload/<?php echo mysqli_fetch_row(mysqli_query($conn, "SELECT profilePicture FROM user WHERE idUserInfo = $idU"))[0];?>" alt="user logo" class="userPic">
                                <p class="userName"><?php echo mysqli_fetch_row(mysqli_query($conn, "SELECT login FROM userinfo WHERE id = $idU"))[0];?> <span class="date">| <?php echo date('G:i Y-m-d', $comment['date']);?></span></p>
                            </div>
                            <p><?php echo $comment['content']?></p>
                        </div>
                    </div>
            <?php endforeach;?>
        </div>

        <div class="joinUs">
            <h1>Dołącz do naszej społeczności już dziś!</h1>
            <a href="register.php" class="join">DOŁĄCZ</a>
        </div>
    </div>
    <script src="scripts/script.js"></script>
</body>
</html>