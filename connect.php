<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "QLHocPhan";

$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die("Kết nối CSDL thất bại: " . mysqli_connect_error());
}
mysqli_set_charset($conn, "utf8mb4");
?>