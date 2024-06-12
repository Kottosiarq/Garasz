<?php
    $conn = mysqli_connect('localhost', 'root', '', 'garasz_forum');

    function filtring($input) {
        if(!empty($input)) {
            $input = stripslashes($input);
            return htmlspecialchars(trim($input));
        } else {
            return 0;
        }
    }

    $msg1 = $msg2 = $msg3 = $msg4 = "";
    $inpCount = 0;

    if(isset($_POST['logBtn'])) {
        if(filtring($_POST['userInp']) !== 0 && strlen(filtring($_POST['userInp'])) <= 15) {
            $user = filtring($_POST['userInp']);
            $inpCount += 1;
        } else {
            $msg1 = "Wpisz prawidłową nazwę użytkownika (maks. 15 znaków)";
        }

        if(filtring($_POST['emailInp']) !== 0) {
            $sample = '/^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$/i';

            if(preg_match($sample, filtring($_POST['emailInp']))) {
                $email = filtring($_POST['emailInp']);
                $inpCount += 1;
            } else {
                $msg2 = "Wpisz prawidłowy adres e-mail";
            }

        } else {
            $msg2 = "Wypełnij pole adresu e-mail";
        }

        if(filtring($_POST['passwordInp']) !== 0 && filtring($_POST['passwordInpA']) !== 0) {

            if(preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/' , filtring($_POST['passwordInp']))) {
                $password1 = filtring($_POST['passwordInp']);
                $password2 = filtring($_POST['passwordInpA']);
                $inpCount += 1;
            } else {
                $msg3 = "Twoje hasło powinno posiadać >= 8 znaków, przynajmniej 1 małą i 1 duzą literę, 1 cyfrę";
            }

        } else {
            $msg3 = "Wpisz prawidłowe hasło";
        }

        if($inpCount == 3) {
            if(mysqli_num_rows(mysqli_query($conn, "SELECT login FROM userInfo WHERE login = '$user'")) == 0) {
                if ($password1 == $password2) {
                    $hashedPassword = password_hash($password1, PASSWORD_BCRYPT);
                    mysqli_query($conn, "INSERT INTO userInfo (login, password, eMail) VALUES ('$user', '$hashedPassword', '$email')");
                    mysqli_query($conn, "INSERT INTO user (name) VALUES (NULL)");
                    // mysqli_query($conn, "INSERT INTO user (idUserInfo) VALUES ((SELECT id FROM userInfo WHERE login = '$user'));");
                    $inpCount = 0;
                    header("Location: log.php");
                } else {
                    $msg4 = "Wpisane hasła się nie zgadzają";
                }
            } else {
                $msg1 = "Podany użytkownik już istnieje";
            }
        }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GARASZ | Logowanie</title>
    <link rel="stylesheet" href="styles/log.css">
    <script src="https://kit.fontawesome.com/7c17143538.js" crossorigin="anonymous"></script>
</head>
<body>
    <div class="container">
        <div class="boxContainer">
            <div class="box">
                <div class="header">
                    <img src="images/garasz_logo_white.png" alt="logo GARASZ" class="garaszImg">
                </div>
                <div class="title">
                    <h1>Rejestracja</h1>
                </div>
                <div class="body">
                    <form action="" method="post" class="inputContainer">
                        <div class="input">
                            <p class="name">Nazwa użytkownika</p>
                            <input type="text" id="userInp" name="userInp" value="<?php if(isset($_POST['logBtn'])){echo $_POST['userInp'];}?>">
                            <p class="info"><?php echo $msg1;?></p>
                        </div>
                        <div class="input">
                            <p class="name">E-mail</p>
                            <input type="text" id="emailInp" name="emailInp" value="<?php if(isset($_POST['logBtn'])){echo $_POST['emailInp'];}?>">
                            <p class="info"><?php echo $msg2;?></p>
                        </div>
                        <div class="input">
                            <p class="name">Hasło</p>
                            <div class="passwordBox">
                                <input type="password" id="passwordInp" name="passwordInp" value="<?php if(isset($_POST['logBtn'])){echo $_POST['passwordInp'];}?>">
                                <button id="showPassword1" type="button"><i class="fa-regular fa-eye-slash" id="eye1"></i></button>
                            </div>
                            <p class="info"><?php echo $msg3;?></p>
                        </div>
                        <div class="input">
                            <p class="name">Powtórz hasło</p>
                            <div class="passwordBox">
                                <input type="password" id="passwordInpA" name="passwordInpA" value="<?php if(isset($_POST['logBtn'])){echo $_POST['passwordInpA'];}?>">
                                <button id="showPassword2" type="button"><i class="fa-regular fa-eye-slash" id="eye2"></i></button>
                            </div>      
                            <p class="info"><?php echo $msg4;?></p>
                        </div>
                    </div>
                    <div class="btnFooter">
                        <input type="submit" value="Zarejestruj się" id="logBtn" name="logBtn">
                    </div>
                    </form>
                    <p class="link">Posiadasz już konto? <a href="log.php">Zaloguj się</a></p>
                    <a class="back" href="index.php">Wróć</a>
            </div>
        </div>
    </div>
    <script src="scripts/scriptLog.js"></script>
</body>
</html>