<?php
session_start();
require_once 'database.php';
$db = new Database();
$conn = $db->connect();

// Lấy từ khóa tìm kiếm từ URL
$search = trim($_GET['query'] ?? '');

// Lấy kết quả tìm kiếm từ session (nếu có nhiều sản phẩm)
$results = $_SESSION['search_results'] ?? [];
unset($_SESSION['search_results']); // Xóa session sau khi dùng

// Lấy thông báo (nếu không tìm thấy)
$message = $_SESSION['search_message'] ?? null;
unset($_SESSION['search_message']);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <script src="https://kit.fontawesome.com/1147679ae7.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="type.css">
    <title>Kết quả tìm kiếm "<?= htmlspecialchars($search) ?>" - 160STORE</title>
</head>
<body>
    <!-- Header giống index.php -->
    <header class="top-header">
        <!-- Copy header từ index.php -->
    </header>

    <div class="container" style="padding: 20px; max-width: 1200px; margin: 0 auto;">
        <h2>Kết quả tìm kiếm cho: "<?= htmlspecialchars($search) ?>"</h2>
        
        <?php if ($message): ?>
            <div style="background: #ff4444; color: white; padding: 20px; border-radius: 8px; text-align: center; margin-bottom: 20px;">
                <?= $message ?>
                <br><br>
                <a href="TrangChu.php" style="color: white; text-decoration: underline;">← Quay lại trang chủ</a>
            </div>
        <?php elseif (!empty($results)): ?>
            <div class="product-list">
                <?php foreach ($results as $p): ?>
                    <div class="product-card">
                        <span class="new-icon">Hàng Mới</span>
                        <a href="aohoodie.php?id=<?= urlencode($p['id_SP']) ?>">
                            <img src="<?= htmlspecialchars($p['hinh_Anh']) ?>" alt="<?= htmlspecialchars($p['ten_San_Pham']) ?>" width="300">
                            <div class="product-info">
                                <h4><?= htmlspecialchars($p['ten_San_Pham']) ?></h4>
                                <p class="price">
                                    Giá: <span class="new-price"><?= number_format($p['gia_Ban'], 0, ',', '.') ?>đ</span>
                                    <span class="old-price">~<?= number_format($p['gia_Goc'], 0, ',', '.') ?>đ</span>
                                </p>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
            <div style="text-align: center; margin-top: 20px;">
                <a href="TrangChu.php">← Quay lại trang chủ</a>
            </div>
        <?php else: ?>
            <div style="background: #aaa; color: #333; padding: 20px; border-radius: 8px; text-align: center; margin-bottom: 20px;">
                Không có kết quả nào để hiển thị.
                <br><br>
                <a href="TrangChu.php" style="color: #333; text-decoration: underline;">← Quay lại trang chủ</a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Footer giống index.php -->
    <footer>
        <!-- Copy footer từ index.php -->
    </footer>

    <a href="tel:0367196252" class="phone-float" title="Gọi ngay">
        <i class="fas fa-phone"></i>
    </a>
</body>
</html>