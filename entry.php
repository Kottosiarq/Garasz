<?php
    include('main.php');

    $user = $_SESSION['user'];
    $idUser = mysqli_fetch_row(mysqli_query($conn, "SELECT id FROM userInfo WHERE login = '$user'"))[0][0];

    $sqlGames = 'SELECT * FROM game';
    $resultGames = mysqli_query($conn, $sqlGames);
    $games = mysqli_fetch_all($resultGames, MYSQLI_ASSOC);

    if(isset($_GET['entryId'])) {
        $idEntry = $_GET['entryId'];
        $sqlEntry = "SELECT * FROM entry WHERE id = $idEntry";
        $resultEntry = mysqli_query($conn, $sqlEntry);
        $entries = mysqli_fetch_all($resultEntry, MYSQLI_ASSOC);
    

    $idG = $entries[0]['idGame'];

    $allEntries = mysqli_fetch_all(mysqli_query($conn, "SELECT * FROM entry WHERE idGame = $idG AND id <> $idEntry"), MYSQLI_ASSOC);
    

    $sqlComment = "SELECT * FROM comment WHERE idEntry = $idEntry";
    $resultComment = mysqli_query($conn, $sqlComment);
    $comments = mysqli_fetch_all($resultComment, MYSQLI_ASSOC);
    }

    if(isset($_POST['commentBtn'])) {
        if(filtring($_POST['commentInp']) !== 0) {
            $commentInp = $_POST['commentInp'];
            $date = time();

            mysqli_query($conn, "INSERT INTO comment (idUser, idEntry, content, date) VALUES ($userId, $idEntry, '$commentInp', $date)");
            header("Location: entry.php?entryId=$idEntry");
        }
    }

    if(isset($_GET['deleteComment'])) {
        $deleteId = $_GET['deleteComment'];
        mysqli_query($conn, "DELETE FROM comment WHERE id = $deleteId");
        header("Location: " . $_SERVER['HTTP_REFERER']);
    }

?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Strona główna</title>
    <link rel="stylesheet" href="styles/style.css">
    <link rel="stylesheet" href="styles/entry.css">
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
        <div class="gridContainer">
            <div class="postBox">
                <div class="post">
                    <?php foreach($entries as $entry):?>
                        <?php $idU = $entry['idUser'];?>
                    <div class="postHeader">
                        <a href="profile.php?id=<?php echo $idU;?>" class="postUser">
                            <img src="./upload/<?php echo mysqli_fetch_row(mysqli_query($conn, "SELECT profilePicture FROM user WHERE idUserInfo = $idU"))[0];?>" alt="user logo" class="userPic">
                            <p class="userName"><?php echo mysqli_fetch_row(mysqli_query($conn, "SELECT login FROM userinfo WHERE id = $idU"))[0];?> <span class="date">| <?php echo date('G:i Y-m-d', $entry['date']);?></span></p>
                        </a>
                        <div class="postGame">
                            <p class="gameName"><?php  echo mysqli_fetch_row(mysqli_query($conn, "SELECT title FROM game WHERE id = $idG"))[0];?></p>
                        </div>
                    </div>
                    <div class="postMain">
                        <h2 class="postTitle"><?php echo $entry['title'];?></h2>
                        <p class="postContent"><?php echo nl2br($entry['content']);?></p>
                    </div>
                    <?php endforeach;?>
                </div>

                <div class="commentWrite">
                    <h3>Komentarze</h3>
                    <form action="" method="post" class="inputBox">
                        <input type="text" name="commentInp" class="commentInp">
                        <input type="submit" value="Skomentuj" name="commentBtn" class="commentBtn">
                    </form>
                </div>

                <div class="commentBox">
                    <?php foreach($comments as $comment):?>
                    <?php $idU = $comment['idUser'];?>
                    <div class="commentAll">
                        <div class="comment">
                            <div class="commentUser">
                                <img src="upload/<?php echo mysqli_fetch_row(mysqli_query($conn, "SELECT profilePicture FROM user WHERE idUserInfo = $idU"))[0];?>" alt="user logo" class="userPic">
                                <p class="userName"><?php echo mysqli_fetch_row(mysqli_query($conn, "SELECT login FROM userinfo WHERE id = $idU"))[0];?> <span class="date">| <?php echo date('G:i Y-m-d', $comment['date']);?></span></p>
                            </div>
                            <p><?php echo $comment['content']?></p>
                        </div>
                        <?php if($userId === '1'):?>
                        <div class="delete">
                            <button type="button" class="deleteComment">Usuń</button>
                            <div class="trueBox">
                                <a href="entry.php?deleteComment=<?php echo $comment['id'];?>" class="trueDelete">T</a>
                                <button type="button" class="keepComment">N</button>
                            </div>
                        </div>
                        <?php endif;?>
                    </div>
                    <?php endforeach;?>
                </div>
            </div>

            <div class="otherBox">
                <h3>Inne wpisy | <span style="font-weight: 200; font-size: 0.8em;"><?php echo mysqli_fetch_row(mysqli_query($conn, "SELECT title FROM game WHERE id = $idG"))[0];?></span></h3>
                <?php if(!empty($allEntries)):?>
                <?php foreach($allEntries as $entry):?>
                <a href="entry.php?entryId=<?php echo $entry['id'];?>" class="otherEntry">
                    <?php echo $entry['title'];?>
                </a>
                <?php endforeach;?>
                <?php else:?>
                    <p style="text-align: center;"><?php echo "Brak wpisów";?></p>
                <?php endif;?>
            </div>
        </div>
    </div>

    <script src="scripts/entry.js"></script>
</body>
</html>