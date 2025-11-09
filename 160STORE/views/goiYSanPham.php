<?php
require_once '../config.php';
require_once '../database.php';

// Tạo đối tượng Database và kết nối
$db = new Database();
$pdo = $db->connect();

// Lấy id sản phẩm từ URL
$id_SP = isset($_GET['id_SP']) ? $_GET['id_SP'] : '';

// Kiểm tra nếu id_SP hợp lệ
if (empty($id_SP)) {
    echo "ID sản phẩm không hợp lệ."; // Nếu không có id_SP, hiển thị thông báo lỗi
    exit; // Dừng chương trình
}

// Truy vấn lấy thông tin sản phẩm từ cơ sở dữ liệu
$product_stmt = $pdo->prepare("SELECT id_SP, ten_San_Pham, gia_Ban, hinh_Anh, id_DM FROM san_pham WHERE id_SP = ?");
$product_stmt->execute([$id_SP]);

// Lấy thông tin sản phẩm
$product = $product_stmt->fetch(PDO::FETCH_ASSOC);

// Kiểm tra nếu không tìm thấy sản phẩm
if (!$product) {
    echo "Sản phẩm không tồn tại."; // Nếu không có sản phẩm, hiển thị thông báo lỗi
    exit; // Dừng chương trình
}

// === 6. SẢN PHẨM GỢI Ý ===
$suggest_stmt = $pdo->prepare("
    SELECT id_SP, ten_San_Pham, gia_Ban, hinh_Anh
    FROM san_pham
    WHERE id_DM = ? AND id_SP <> ?
    ORDER BY RAND()
    LIMIT 4
");
$suggest_stmt->execute([$product['id_DM'], $id_SP]);
$suggest_products = $suggest_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- GỢI Ý SẢN PHẨM TƯƠNG TỰ -->
<div class="suggest-section">
    <h3>Gợi ý sản phẩm tương tự</h3>
    <div class="suggest-list">
        <?php
        if (empty($suggest_products)): ?>
            <p style="color:#aaa; text-align:center; width:100%;">Chưa có sản phẩm tương tự.</p>
        <?php else: ?>
            <?php foreach ($suggest_products as $s): ?>
                <div class="suggest-item">
                    <a href="chiTietSanPham.php?id=<?= urlencode($s['id_SP']) ?>">
                        <img src="<?= htmlspecialchars($s['hinh_Anh']) ?>" alt="<?= htmlspecialchars($s['ten_San_Pham']) ?>">
                        <h4><?= htmlspecialchars($s['ten_San_Pham']) ?></h4>
                        <p><?= number_format($s['gia_Ban'], 0, ',', '.') ?>đ</p>
                    </a>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
