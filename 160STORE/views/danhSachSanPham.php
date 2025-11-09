<?php
// Bao gồm các file cấu hình và kết nối cơ sở dữ liệu
// Sử dụng __DIR__ để đảm bảo đường dẫn tuyệt đối chính xác
require_once(__DIR__ . '/../config.php');
require_once(__DIR__ . '/../database.php');

// Tạo đối tượng Database và kết nối
$db = new Database();
$pdo = $db->connect();

// Lấy danh sách sản phẩm từ cơ sở dữ liệu
$sql = "SELECT * FROM san_pham";  // Truy vấn lấy tất cả sản phẩm
$stmt = $pdo->prepare($sql);
$stmt->execute();
$products = $stmt->fetchAll();  // Trả về tất cả sản phẩm dưới dạng mảng
?>
<div class="product-list">
    <?php
    // Kiểm tra nếu biến $products không được khởi tạo hoặc rỗng
    if (empty($products)) {
        echo "<p>Không có sản phẩm nào.</p>";
    } else {
        // Hiển thị danh sách sản phẩm
        foreach ($products as $p):
    ?>
            <div class="product-card">
                <a href="views/chiTietSanPham.php?id=<?= urlencode($p['id_SP']) ?>">
                    <img src="<?= htmlspecialchars($p['hinh_Anh']) ?>" 
                         alt="<?= htmlspecialchars($p['ten_San_Pham']) ?>" 
                         width="100">
                    <div class="product-info">
                        <h4><?= htmlspecialchars($p['ten_San_Pham']) ?></h4>
                        <p class="price">
                            Giá: 
                            <span class="new-price"><?= number_format($p['gia_Ban'], 0, ',', '.') ?>đ</span>
                            <span class="old-price">~<?= number_format($p['gia_Goc'], 0, ',', '.') ?>đ</span>
                        </p>
                    </div>
                </a>
            </div>
    <?php
        endforeach;
    }
    ?>
</div>

