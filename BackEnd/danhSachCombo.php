<?php
require_once 'database.php';
$db = new Database();
$conn = $db->connect();
// Lấy sản phẩm thuộc danh mục Áo Thun (id_DM = 1)
$query = "SELECT * FROM san_pham WHERE id_DM = 4 AND trang_Thai = 'active'";
$stmt = $conn->prepare($query);
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Áo Thun Nam - 160Store</title>
  <link rel="stylesheet" href="type.css">
</head>
<body>
  <header>
    <h2 style="text-align:center; margin-top: 20px;">PHỤ KIỆN NAM MỚI NHẤT</h2>
  </header>

  <div class="product-list" style="display:flex; flex-wrap:wrap; justify-content:center;">
    <?php foreach ($products as $p): ?>
      <div class="product-card" style="margin:15px;">
        <span class="new-icon">Hàng Mới</span>
        <a href="chitietsanpham.php?id=<?= $p['id_SP'] ?>">
          <img src="<?= htmlspecialchars($p['hinh_Anh']) ?>" 
               alt="<?= htmlspecialchars($p['ten_San_Pham']) ?>" 
               width="300">
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
    <?php endforeach; ?>
  </div>
  <!-- logo vận chuyển  hàng hóa-->
 <footer>
  <div class="footer-container">
    <!-- Giới thiệu -->
    <div class="footer-col">
      <h3>Giới thiệu</h3>
      <p>160STORE - Chuỗi Phân Phối Thời Trang Nam Chuẩn Hiệu</p>
      <p>📞 0367196252</p>
      <p>📧 luu.kimngan205@gmail.com</p>
      <p>🕒 08:30 - 22:00</p>
      <p>🎧 Hỗ trợ tin nhắn đến 24:00</p>
      <img src="https://file.hstatic.net/200000397757/file/dathongbao_48067cd02fae41b68bf0294777c39c94_compact.png" alt="Bộ Công Thương" width="100">
      <img src="https://images.dmca.com/Badges/dmca_protected_16_120.png?ID=9049de26-d97b-48dc-ab97-1e5fcb221fba" alt="DMCA" width="100">
    </div>

    <!-- Chính sách -->
    <div class="footer-col">
      <h3>Chính sách</h3>
      <ul>
        <li><a href="#">Hướng dẫn đặt hàng</a></li>
        <li><a href="#">Chính sách</a></li>
      </ul>
    </div>

    <!-- Địa chỉ cửa hàng -->
    <div class="footer-col">
      <h3>Địa chỉ cửa hàng (23 CH)</h3>
      <p><img src="https://file.hstatic.net/1000253775/file/location_a1e4d2d625914daa90748f218350e7b7.svg" alt="Địa chỉ" width="20">
        Hồ Chí Minh (12 CH): 401 Phan Xích Long...</p>
      <p><img src="https://file.hstatic.net/1000253775/file/location_a1e4d2d625914daa90748f218350e7b7.svg" alt="Địa chỉ" width="20">
        Hà Nội (2 CH): Số 26 Phố Lê Đại Hành...</p>
    </div>

    <!-- Thanh toán -->
    <div class="footer-col">
      <h3>Phương thức thanh toán</h3>
      <img src="https://file.hstatic.net/1000253775/file/shoppy_new_18600e8ad9f64537b7e31d009457e215.jpg" alt="ShopeePay" width="50">
      <img src="https://file.hstatic.net/1000253775/file/vnpay_new_ec21c03b2f4c49689d180c8a485c8d5b.jpg" alt="VNPay" width="50">
      <img src="https://file.hstatic.net/1000253775/file/cod_new_2316cf8e29bd4a858810a3d7b9eb39e4.jpg" alt="COD" width="50">
    </div>
  </div>
  <!-- Icon mạng xã hội -->
  <div class="social">
    <a href="#"><i class="fab fa-facebook"></i></a>
    <a href="#"><i class="fab fa-instagram"></i></a>
    <a href="#"><i class="fab fa-youtube"></i></a>
    <a href="#"><i class="fab fa-zalo"></i></a>
  </div>
</footer>
<a href="tel:0367196252" class="phone-float" title="Gọi ngay">
  <i class="fas fa-phone"></i>
</a>
</body>
</html>
