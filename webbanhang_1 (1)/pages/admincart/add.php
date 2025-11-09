<?php
require_once 'includes/function.php'; // Nạp file chứa các hàm giỏ hàng

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_id = (int)($_POST['product_id'] ?? 0);
    $variant_id = (int)($_POST['variant_id'] ?? 0);
    $quantity = (int)($_POST['quantity'] ?? 1);
    
    // Lấy user_id từ session ===
    $user_id = (int)($_SESSION['user_id'] ?? 0);

    // hêm $user_id vào điều kiện kiểm tra ===
    if ($variant_id > 0 && $product_id > 0 && $quantity > 0 && $user_id > 0) {
        try {
            // Lấy thông tin đầy đủ của biến thể (giữ nguyên)
            $db->query('
                SELECT 
                    p.product_name, 
                    pv.size, 
                    pv.price,
                    (SELECT image_url FROM ProductImages pi 
                     WHERE pi.product_id = p.product_id 
                     ORDER BY pi.image_id ASC LIMIT 1) as image_url
                FROM ProductVariants pv
                JOIN Products p ON pv.product_id = p.product_id
                WHERE pv.variant_id = :variant_id AND p.product_id = :product_id
            ');
            $db->bind(':variant_id', $variant_id);
            $db->bind(':product_id', $product_id);
            $item_data = $db->single();

            if ($item_data) {
                // Truyền $db và $user_id vào hàm ===
                add_to_cart(
                    $db, 
                    $user_id,
                    $variant_id,
                    $product_id,
                    $quantity,
                    $item_data['product_name'],
                    $item_data['size'],
                    (float)$item_data['price'],
                    $item_data['image_url'] ?? 'assets/dist/img/default-product.png'
                );
                
                $_SESSION['success_message'] = 'Đã thêm sản phẩm vào giỏ hàng!';
            } else {
                $_SESSION['error_message'] = 'Không tìm thấy sản phẩm hoặc biến thể.';
            }

        } catch (PDOException $e) {
            $_SESSION['error_message'] = 'Lỗi CSDL: ' . $e->getMessage();
        }
    } else {
        if ($user_id == 0) {
             $_SESSION['error_message'] = 'Lỗi phiên đăng nhập, không thể thêm giỏ hàng.';
        } else {
             $_SESSION['error_message'] = 'Dữ liệu không hợp lệ (Vui lòng chọn Size).';
        }
    }

    // Chuyển hướng trở lại trang chi tiết sản phẩm
    header('Location: index.php?page=product-detail&id=' . $product_id);
    exit;

} else {
    // Nếu không phải POST, chuyển về trang chủ
    header('Location: index.php');
    exit;
}
?>