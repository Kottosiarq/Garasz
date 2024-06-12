<?php
    include('main.php');

    $sqlGames = 'SELECT * FROM game';
    $resultGames = mysqli_query($conn, $sqlGames);
    $games = mysqli_fetch_all($resultGames, MYSQLI_ASSOC);

    $sqlEntry = "SELECT * FROM entry WHERE idUser = $userId ORDER BY date desc";
    $resultEntry = mysqli_query($conn, $sqlEntry);
    $entries = mysqli_fetch_all($resultEntry, MYSQLI_ASSOC);

    if(isset($_GET['delete'])) {
        $idEntry = $_GET['idEntry'];
        mysqli_query($conn, "DELETE FROM entry WHERE id = $idEntry");
        header("Location: userEntries.php");
    }
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Strona główna</title>
    <link rel="stylesheet" href="styles/style.css">
    <link rel="stylesheet" href="styles/userEntries.css">
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
                        <li><a href="#">Ogłoszenia</a></li>
                        <li><a href="settings.php">Ustawienia</a></li>
                        <li><a href="index.php?logout" class="logout">Wyloguj</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="main">
        <h1>TWOJE OGŁOSZENIA</h1>

        <div class="entriesContainer">
            <?php if(!empty($entries)):?>
                <?php foreach($entries as $entry):?>
                <div class="entry">
                    <div class="postMain">
                        <h2 class="postTitle"><?php echo $entry['title'];?></h2>
                        <p class="postDesc"><?php echo $entry['content'];?></p>  
                    </div>
                    <p class="more">. . .</p>
                    <div class="postHeader">
                        <div class="postUser">
                            <p class="date"><?php echo date('G:i Y-m-d', $entry['date']);?></p>
                        </div>
                        <div class="postGame">
                            <p class="gameName"><?php $idG = $entry['idGame']; echo mysqli_fetch_row(mysqli_query($conn, "SELECT title FROM game WHERE id = $idG"))[0];?></p>
                        </div>
                    </div>
                    <div class="postBtns">
                        <a href="edit.php?idEntry=<?php echo $entry['id'];?>" class="editBtn">EDYTUJ</a>
                        <div class="doBox">
                            <button type="button" class="deleteUser">USUŃ</button>
                            <div class="trueBox">
                                <a href="userEntries.php?delete=1&idEntry=<?php echo $entry['id'];?>" class="trueDelete">T</a>
                                <button type="button" class="keepUser">N</button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach;?>
                <?php else:?>
                    <p style="text-align: center;"><?php echo "Nie posiadasz żadnych wpisów <br><a href='add.php' class='addLink'>Dodaj ogłoszenie!</a>" ?></p>
            <?php endif;?>
        </div>
    </div>

    <script src="scripts/userEntries.js"></script>
</body>
</html>