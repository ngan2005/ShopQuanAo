<?php
session_start();
require_once 'database.php';
$db = new Database();
$pdo = $db->connect();

// 🟦 1️⃣ Lấy mã sản phẩm từ URL
$id_SP = $_GET['id'] ?? 'SP016';

// 🟩 2️⃣ Lấy thông tin sản phẩm
$stmt = $pdo->prepare("
    SELECT id_SP, ten_San_Pham, gia_Ban, gia_Goc, hinh_Anh, mo_Ta, id_DM
    FROM san_pham
    WHERE id_SP = ?
");
$stmt->execute([$id_SP]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) die("<h2 style='color:red;text-align:center;'>⚠️ Sản phẩm không tồn tại!</h2>");

// 🟨 3️⃣ Lấy danh sách biến thể (màu sắc, kích thước)
$variant_stmt = $pdo->prepare("
    SELECT DISTINCT mau_Sac, kich_Thuoc
    FROM bien_the_san_pham
    WHERE id_SP = ?
");
$variant_stmt->execute([$id_SP]);
$variants = $variant_stmt->fetchAll(PDO::FETCH_ASSOC);

// 🟦 4️⃣ Khởi tạo giỏ hàng nếu chưa có
if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

// 🟩 5️⃣ Thêm vào giỏ hàng
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'add_to_cart') {
    $size = $_POST['size'] ?? '';
    $color = $_POST['color'] ?? '';
    $quantity = (int)($_POST['quantity'] ?? 1);

    if ($size && $color) {
        $cart_item = [
            'id' => $product['id_SP'],
            'name' => $product['ten_San_Pham'],
            'price' => (int)$product['gia_Ban'],
            'img' => $product['hinh_Anh'],
            'size' => $size,
            'color' => $color,
            'quantity' => $quantity
        ];

        $found = false;
        foreach ($_SESSION['cart'] as &$item) {
            if ($item['id'] == $cart_item['id'] && 
                $item['size'] == $size &&
                $item['color'] == $color) {
                $item['quantity'] += $quantity;
                $found = true;
                break;
            }
        }
        if (!$found) $_SESSION['cart'][] = $cart_item;

        echo "<script>alert('✅ Đã thêm vào giỏ hàng!');</script>";
    } else {
        echo "<script>alert('⚠️ Vui lòng chọn màu và size!');</script>";
    }
}

// 🟨 6️⃣ Lấy sản phẩm gợi ý cùng danh mục
$suggest_stmt = $pdo->prepare("
    SELECT id_SP, ten_San_Pham, gia_Ban, hinh_Anh
    FROM san_pham
    WHERE id_DM = ? AND id_SP <> ?
    LIMIT 4
");
$suggest_stmt->execute([$product['id_DM'], $id_SP]);
$suggest_products = $suggest_stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($product['ten_San_Pham']) ?> - 160Store</title>
  <script src="https://kit.fontawesome.com/1147679ae7.js" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="type.css">
  <style>
    body { font-family: Arial, sans-serif; }
    .product-page { display: flex; gap: 30px; padding: 40px; }
    .product-gallery img { border-radius: 10px; }
    .product-info h2 { margin-bottom: 10px; }
    .price { font-size: 22px; color: #e63946; font-weight: bold; }
    .add-cart { background: #28a745; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; }
    .add-cart:hover { background: #218838; }
    .suggest-list { display: flex; gap: 20px; flex-wrap: wrap; }
    .color-dot { display: inline-block; width: 20px; height: 20px; border-radius: 50%; border: 1px solid #ccc; margin-right: 8px; cursor: pointer; }
    <st>
  body {
    font-family: Arial, sans-serif;
    color: white; /* 👈 Thêm dòng này */
  }
.product-info p { 
        color: blue;     /* ✅ chữ mô tả trắng */
        line-height: 1.6; /* cho dễ đọc hơn */
    }
/* --- Sửa màu chữ xám thành trắng --- */
.product-info h1,
.product-info h2,
.product-info h3,
.product-info p {
  color: #fff !important;  /* ép màu trắng */
}

/* Nếu bạn có phần giá màu xám cũng muốn trắng */
.price span {
  color: #bbb !important;  /* giá gạch có thể hơi sáng hơn */
}

/* Toàn bộ phần thông tin sản phẩm nền tối */
.product-info {
  color: #fff;
}
  
  </style>
  
</head>
<body>
<header class="top-header">  
    <!-- Logo -->
    <div class="logo">
      <img src="https://file.hstatic.net/1000253775/file/logo_no_bf-05_3e6797f31bda4002a22464d6f2787316.png" alt="Logo">
    </div>
    <!-- Thanh tìm kiếm -->
      <div class="search-box">
        <input type="text" placeholder="Bạn đang tìm gì...">
        <button><i class="fas fa-search"></i></button>
      </div>
      <!-- Icon -->
      <div class="header-icons">
        <a href="#" onclick="location.reload(); return false;"><i class="fas fa-store"></i> Cửa hàng</a>
        <a href="dangKy.php"><i class="fas fa-user"></i> Đăng nhập</a>
        <a href="gioHang.html"><i class="fas fa-shopping-cart"></i> Giỏ hàng</a>
      </div>
  </header>
  <nav>
      <!-- Menu -->
      <ul class="menu">
        <li><a href="#hangmoi">Hàng Mới</a>
    <!--- -->
        </li>
        <li><a href="#">Sản Phẩm</a>
          <ul class ="submenu">
            <li><a href="#">Combo</a>
              <ul>
                <li><a href="danhSachCombo.html">Combo Mix & Match</a></li>
              </ul>
            </li>
            <!--- -->
            <li><a href="danhSachAoNam.php">Áo</a>
            <!--- -->
            <li><a href="danhSachQuanNam.php">Quần</a>
            </li>
            <!--- -->
            </li>
            <li><a href="danhSachPhuKien.php">Phụ Kiện</a>
            
            </li>
        <!--- -->
      </ul>   
      <!--- -->   
      <li><a href="danhSachAoNam.php">Áo Nam</a>
    <!--- -->
      <li><a href="danhSachQuanNam.php">Quần Nam</a>
       <!--- --> 
      <li><a href="danhSachPhuKien.php">Phụ Kiện</a>
      <!--- --> 
</nav>
<div class="product-page">
  <div class="product-gallery">
    <img src="<?= htmlspecialchars($product['hinh_Anh']) ?>" width="400">
  </div>

  <div class="product-info">
    <h2><?= htmlspecialchars($product['ten_San_Pham']) ?></h2>
    <p class="price"><?= number_format($product['gia_Ban'], 0, ',', '.') ?>đ 
      <span style="color:gray;text-decoration:line-through;">~<?= number_format($product['gia_Goc'], 0, ',', '.') ?>đ</span>
    </p>
    <h1>Mô tả sản phẩm:</h1>
    <p><?= nl2br(htmlspecialchars($product['mo_Ta'])) ?></p>

    <!-- 🧩 FORM chọn màu + size -->
    <form method="POST">
      <input type="hidden" name="action" value="add_to_cart">

      <label>Màu sắc:</label><br>
      <select name="color" required>
        <option value="">-- Chọn màu --</option>
        <?php 
          $colors = array_unique(array_column($variants, 'mau_Sac'));
          foreach ($colors as $color): ?>
            <option value="<?= htmlspecialchars($color) ?>"><?= htmlspecialchars($color) ?></option>
        <?php endforeach; ?>
      </select><br><br>

      <label>Size:</label><br>
      <select name="size" required>
        <option value="">-- Chọn size --</option>
        <?php 
          $sizes = array_unique(array_column($variants, 'kich_Thuoc'));
          foreach ($sizes as $size): ?>
            <option value="<?= htmlspecialchars($size) ?>"><?= htmlspecialchars($size) ?></option>
        <?php endforeach; ?>
      </select><br><br>

      <label>Số lượng:</label>
      <input type="number" name="quantity" value="1" min="1" style="width:80px;"><br><br>

      <button type="submit" class="add-cart">🛒 Thêm vào giỏ hàng</button>
    </form>
  </div>
</div>

<!-- 🛍 Gợi ý -->
<div class="suggest-section">
  <h3>Gợi ý sản phẩm tương tự</h3>
  <div class="suggest-list">
    <?php foreach ($suggest_products as $s): ?>
      <div class="suggest-item">
        <a href="aohoodie.php?id=<?= $s['id_SP'] ?>">
          <img src="<?= htmlspecialchars($s['hinh_Anh']) ?>" width="180">
          <h4><?= htmlspecialchars($s['ten_San_Pham']) ?></h4>
          <p style="color:red;"><?= number_format($s['gia_Ban'], 0, ',', '.') ?>đ</p>
        </a>
      </div>
    <?php endforeach; ?>
  </div>
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