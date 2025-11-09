<?php
require_once 'includes/function.php'; // Nạp file (vẫn giữ để dùng uploadImage sau này nếu cần)

try {
    // Lấy danh sách Categories để đổ vào dropdown filter
    $db->query('SELECT category_id, category_name FROM Categories');
    $categories = $db->resultSet();

    // === BƯỚC 1: XÂY DỰNG CÂU SQL GỐC VÀ CÁC THAM SỐ LỌC DỮ LIỆU 
    $base_sql = 'SELECT 
                p.product_id, p.product_name, p.created_at, c.category_name, u.full_name, 
                (SELECT COUNT(pv.variant_id) FROM ProductVariants pv WHERE pv.product_id = p.product_id) as variant_count,
                (SELECT SUM(pv.stock_quantity) FROM ProductVariants pv WHERE pv.product_id = p.product_id) as total_stock,
                (SELECT MIN(pv.price) FROM ProductVariants pv WHERE pv.product_id = p.product_id) as min_price,
                
                -- 1. Giữ lại ảnh chính để hiển thị thumbnail
                (SELECT pi.image_url FROM ProductImages pi WHERE pi.product_id = p.product_id ORDER BY pi.image_id ASC LIMIT 1) as main_image,
                
                -- 2. Thêm cột tất cả ảnh của bạn (dưới dạng chuỗi)
                (SELECT GROUP_CONCAT(pi.image_url ORDER BY pi.image_id ASC SEPARATOR ",") 
                 FROM ProductImages pi WHERE pi.product_id = p.product_id) as all_images,
                
                -- 3. Truy vấn size 
                (SELECT GROUP_CONCAT(pv.size ORDER BY pv.size ASC SEPARATOR ", ") 
                 FROM ProductVariants pv WHERE pv.product_id = p.product_id AND pv.size IS NOT NULL) as available_sizes
                
             FROM Products p 
             JOIN Categories c ON p.category_id = c.category_id
             LEFT JOIN Users u ON p.created_by = u.user_id';

    $where_conditions = [];
    $params = [];

    // Xử lý filter
    if (!empty($_GET['search_keyword'])) {
        $keyword = '%' . $_GET['search_keyword'] . '%';
        $where_conditions[] = 'p.product_name LIKE :keyword';
        $params[':keyword'] = $keyword;
    }
    if (!empty($_GET['filter_category'])) {
        $where_conditions[] = 'p.category_id = :filter_category';
        $params[':filter_category'] = $_GET['filter_category'];
    }

    if (!empty($where_conditions)) {
        $base_sql .= ' WHERE ' . implode(' AND ', $where_conditions);
    }

    // === BƯỚC 2: LẤY DỮ LIỆU CHO TRANG HIỆN TẠI (ĐÃ BỎ LIMIT VÀ OFFSET)
    $data_sql = $base_sql . " ORDER BY p.product_id DESC"; // Bỏ LIMIT và OFFSET
    $db->query($data_sql);
    if (!empty($params)) {
        foreach ($params as $key => $param) {
            $db->bind($key, $param);
        }
    }

    $products = $db->resultSet();

} catch (PDOException $e) {
    echo '<script>console.error("Lỗi truy vấn SQL: ", ' . json_encode($e->getMessage()) . ');</script>';
    $products = [];

}

// Xử lý thông báo thành công từ session
$success_message = '';
if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}