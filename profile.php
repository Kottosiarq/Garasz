<?php
    include('main.php');

    $otherId = $_GET['id'];
    
    $user = mysqli_fetch_all(mysqli_query($conn, "SELECT * FROM user WHERE idUserInfo = $otherId"), MYSQLI_ASSOC);
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
       <div class="profileContainer">
            <h1>PROFIL GRACZA</h1>

            <?php foreach($user as $row):?>
            <div class="user">
                <img src="./upload/<?php echo $user[0]['profilePicture']; ?>" alt="user profile picture" class="profilePic">
                <h2><?php $idUserInfo = $row['idUserInfo']; echo mysqli_fetch_row(mysqli_query($conn, "SELECT login FROM userInfo WHERE id = $idUserInfo"))[0];?><br><span class="aka"><?php if(isset($row['name'])){ echo "aka ".$row['name']." ".$row['surName'];};?></span></h2>
            </div>

            <div class="description">
                <p class="descP">OPIS</p>
                <p><?php if(isset($row['about'])): echo nl2br($row['about']); else: echo "Użytkownik nie posiada opisu";?></p>
                <?php endif;?>
            </div>
            <?php endforeach;?>

            <?php if($otherId === $userId):?>
            <div class="btnBox">
                <a href="settings.php" class="editBtn">Edytuj profil</a>
            </div>
            <?php endif;?>
            
       </div>
    </div>
    <script src="scripts/scriptUser.js"></script>
</body>
</html>