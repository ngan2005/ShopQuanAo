<?php
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'shopqlndvagiohang';

// Kết nối đến cơ sở dữ liệu
$conn = mysqli_connect($host, $username, $password, $database);

// Kiểm tra kết nối
if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}

// Đặt charset là utf8
mysqli_set_charset($conn, "utf8");
?>