<?php
require_once(__DIR__ . '/../config.php');
require_once(__DIR__ . '/../database.php');
$db = new Database();
$conn = $db->connect();
// Lấy sản phẩm thuộc danh mục Áo Thun (id_DM = 1)
$query = "SELECT * FROM san_pham WHERE id_DM = 3 AND trang_Thai = 'Còn hàng'";
$stmt = $conn->prepare($query);
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Phụ Kiện Nam- 160Store</title>
  <script src="https://kit.fontawesome.com/1147679ae7.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../public/css/stylee.css">
</head>
<body>
  <?php include('../layouts/header.php'); ?>
  <?php include('../layouts/navbar.php'); ?>
  <h2 style="text-align:center; margin:20px 0;">Danh Sách Phụ Kiện</h2>
  <div class="product-list" style="display:flex; flex-wrap:wrap; justify-content:center;">
    <?php foreach ($products as $p): ?>
      <div class="product-card" style="margin:15px;">
        <span class="new-icon">Hàng Mới</span>
        <a href="chiTietSanPham.php?id=<?= $p['id_SP'] ?>">
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
  <?php include('../layouts/footer.php'); ?>
</body>
</html>
