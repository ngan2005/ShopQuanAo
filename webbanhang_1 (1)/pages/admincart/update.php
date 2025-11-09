<?php
require_once 'includes/function.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $variant_id = (int)($_POST['variant_id'] ?? 0);
    $quantity = (int)($_POST['quantity'] ?? 0);
    
    //  Lấy user_id từ session 
    $user_id = (int)($_SESSION['user_id'] ?? 0);

    // Thêm $user_id vào điều kiện kiểm tra 
    if ($variant_id > 0 && $user_id > 0) {
        // Truyền $db và $user_id vào hàm 
        update_cart_quantity($db, $user_id, $variant_id, $quantity);
    }
}

// Chuyển hướng về trang xem giỏ hàng
header('Location: index.php?page=admincart-view');
exit;
?>