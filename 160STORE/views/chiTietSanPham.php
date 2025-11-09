<?php
// Kết nối cơ sở dữ liệu
require_once('../config.php');
require_once('../database.php');

// Tạo đối tượng Database và kết nối
$db = new Database();
$pdo = $db->connect();

// Lấy id sản phẩm từ URL
$id = isset($_GET['id']) ? $_GET['id'] : 0;
$product = null;

if ($id) {
    // THAY ĐỔI: Phải truy vấn JOIN để lấy tên danh mục cho Breadcrumb
    $sql = "SELECT sp.*, dm.ten_Danh_Muc, dm.id_DM
            FROM san_pham sp
            LEFT JOIN danh_muc dm ON sp.id_DM = dm.id_DM
            WHERE sp.id_SP = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    $product = $stmt->fetch(); // $product đã có đầy đủ thông tin sản phẩm và Tên Danh Mục

    if (!$product) {
        echo "<p>Sản phẩm không tồn tại.</p>";
    }
} else {
    echo "<p>ID sản phẩm không hợp lệ.</p>";
}
?>

<!DOCTYPE html>
<html lang="vi">
    <head>
    <meta charset="UTF-8">
    <title>Chi Tiết Sản Phẩm | 160Store</title>
    <script src="https://kit.fontawesome.com/1147679ae7.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../public/css/stylee.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../Admin_css/Breadcrumb.css">
    </head>

<body>
    <?php include('../layouts/header.php'); ?>
    <?php include('../layouts/navbar.php'); ?>
    <?php include('../layouts/breadcrumb_detail.php'); ?> 
    <main>
        <div class="product-detail">
            <?php if ($product): ?>
                <h1><?= htmlspecialchars($product['ten_San_Pham']) ?></h1>
                <img src="<?= htmlspecialchars($product['hinh_Anh']) ?>" alt="<?= htmlspecialchars($product['ten_San_Pham']) ?>" width="300">
                <p><strong>Giá bán:</strong> <?= number_format($product['gia_Ban'], 0, ',', '.') ?>đ</p>
                <p><strong>Giá gốc:</strong> ~<?= number_format($product['gia_Goc'], 0, ',', '.') ?>đ</p>
                <p><strong>Mô tả:</strong> <?= htmlspecialchars($product['mo_Ta']) ?></p>
            <?php else: ?>
                <p>Sản phẩm không tìm thấy!</p>
            <?php endif; ?>
        </div>
    </main>
    </body>
    <?php include('../layouts/footer.php'); ?>
</html>
