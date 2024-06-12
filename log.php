<?php
    session_start();
    $conn = mysqli_connect('localhost', 'root', '', 'garasz_forum');

    function filtring($input) {
        if(!empty($input)) {
            $input = stripslashes($input);
            return htmlspecialchars(trim($input));
        } else {
            return 0;
        }
    }

    $msg1 = $msg2 = "";
    $inpCount = 0;

    if(isset($_POST['logBtn'])) {
        $user = $_POST['userInp'];
        $isUserExists = mysqli_query($conn, "SELECT login FROM userinfo WHERE login = '$user'");

        if(mysqli_num_rows($isUserExists) == 1 && filtring($_POST['userInp']) !== 0) {
                $inpCount += 1;

                if(filtring($_POST['passwordInp']) !== 0) {
                    $password = filtring($_POST['passwordInp']);
                    $isPasswordSame = mysqli_fetch_row(mysqli_query($conn, "SELECT password FROM userinfo WHERE login = '$user'"));
                    if(password_verify($password, $isPasswordSame[0])) {
                        $inpCount += 1;
                    } else {
                        $msg2 = "Podane hasło jest niepoprawne";
                    }       
                }
        } else {
            $msg1 = "Podany użytkownik nie istnieje";
        }

        if($inpCount == 2) {
            $_SESSION['user'] = $user;
            $_SESSION['id'] = mysqli_fetch_row(mysqli_query($conn, "SELECT id FROM userInfo WHERE login = '$user'"));

            if($_SESSION['id'][0] == '1') {
                header("Location: indexAdmin.php");
            } else {
                header("Location: indexUser.php");
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
                    <h1>Logowanie</h1>
                </div>
                <div class="body">
                    <form action="" method="post" class="inputContainer">
                    <div class="input">
                            <p class="name">Nazwa użytkownika</p>
                            <input type="text" id="userInp" name="userInp" value="<?php if(isset($_POST['logBtn'])){echo $_POST['userInp'];}?>">
                            <p class="info"><?php echo $msg1;?></p>
                        </div>
                        <div class="input">
                            <p class="name">Hasło</p>
                            <div class="passwordBox">
                                <input type="password" id="passwordInp" name="passwordInp" value="<?php if(isset($_POST['logBtn'])){echo $_POST['passwordInp'];}?>">
                                <button id="showPassword1" type="button"><i class="fa-regular fa-eye-slash" id="eye1"></i></button>
                            </div>      
                            <p class="info"><?php echo $msg2;?></p>
                        </div>
                    </div>
                    <div class="btnFooter">
                        <input type="submit" value="Zaloguj się" id="logBtn" name="logBtn">
                    </div>
                    </form>
                    <p class="link">Nie posiadasz jeszcze konta? <a href="register.php">Zarejestruj się</a></p>
                    <a class="back" href="index.php">Wróć</a>
            </div>
        </div>
    </div>
    <script src="scripts/scriptLog.js"></script>
</body>
</html>