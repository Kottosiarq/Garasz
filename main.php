<?php

session_start();
$conn = mysqli_connect('localhost', 'root', '', 'garasz_forum');

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

function filtring($input) {
    if(!empty($input) && strlen($input)) {
        $input = stripslashes($input);
        return htmlspecialchars(trim($input));
    } else {
        return 0;
    }
}

if(isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
}

$userId = $_SESSION['id'][0];

$userResult = mysqli_query($conn, "SELECT * FROM user WHERE idUserInfo = $userId");
$userInfo = mysqli_fetch_all($userResult, MYSQLI_ASSOC);