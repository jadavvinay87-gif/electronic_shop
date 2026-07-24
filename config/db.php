<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "electronic_shop";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function getProductImageSrc($image) {
    if (!$image) {
        return '';
    }
    return preg_match('/^https?:\/\//i', $image) ? $image : '../uploads/products/' . $image;
}
?>
