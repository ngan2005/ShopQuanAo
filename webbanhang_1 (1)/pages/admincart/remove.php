<?php
require_once 'includes/function.php'; // Nạp file chứa các hàm giỏ hàng

// Lấy action và user_id 
$action = $_GET['action'] ?? 'single'; // Mặc định là xóa 1 sản phẩm
$user_id = (int)($_SESSION['user_id'] ?? 0); // Lấy user_id

if ($user_id > 0) {
    if ($action == 'clearall') {
        // Xử lý xóa hết giỏ hàng
        clear_cart($db, $user_id);
        $_SESSION['success_message'] = 'Đã xóa toàn bộ giỏ hàng.';

    } else {
        // Xử lý xóa 1 sản phẩm khỏi giỏ hàng theo variant_id và user_id
        $variant_id = (int)($_GET['variant_id'] ?? 0);
        if ($variant_id > 0) {
            remove_from_cart($db, $user_id, $variant_id);
            $_SESSION['success_message'] = 'Đã xóa sản phẩm khỏi giỏ hàng.';
        }
    }
}

// Chuyển hướng về trang xem giỏ hàng
header('Location: index.php?page=admincart-view');
exit;
?>