<?php
// ...existing code...
// Bật hiển thị lỗi tạm thời (chỉ dùng khi dev)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Nếu ketmoi.php (login handler) đang lưu $_SESSION['admin_id'] hoặc $_SESSION['user_id']
// Ta chấp nhận cả hai để tránh redirect sai
if (!isset($_SESSION['admin_id']) && !isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Cấu hình DB — chỉnh $dbname nếu khác
$servername = "localhost:8100";
$dbport = 8100; // MySQL port 
$username = "root";
$password = "";
$dbname = "shop_thoi_trang"; // kiểm tra lại tên database trong phpMyAdmin

// Kết nối mysqli với port rõ ràng
$conn = new mysqli($servername, $username, $password, $dbname, $dbport);
if ($conn->connect_error) {
    // Hiển thị lỗi kết nối để debug
    die("Kết nối database thất bại: (" . $conn->connect_errno . ") " . $conn->connect_error);
}

// ...existing code...

// ...existing code...
// sau khi xác thực thành công:
session_start();
$_SESSION['admin_id'] = $user['id'];       // hoặc dùng $_SESSION['user_id'] nếu bạn muốn
$_SESSION['admin_name'] = $user['display_name'] ?? $user['username'];
header('Location: trangChu.php');
exit;
?>
