<?php
session_start();
require_once 'database.php';
$db = new Database();
$pdo = $db->connect();

// === 1. LẤY MÃ SẢN PHẨM TỪ URL ===
$id_SP = $_GET['id'] ?? null;
if (!$id_SP) {
    die("<h2 style='color:red;text-align:center;'>Sản phẩm không tồn tại!</h2>");
}

// === 2. LẤY THÔNG TIN SẢN PHẨM ===
$stmt = $pdo->prepare("
    SELECT id_SP, ten_San_Pham, gia_Ban, gia_Goc, hinh_Anh, mo_Ta, id_DM
    FROM san_pham
    WHERE id_SP = ?
");
$stmt->execute([$id_SP]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    die("<h2 style='color:red;text-align:center;'>Sản phẩm không tồn tại!</h2>");
}

// === 3. LẤY BIẾN THỂ (MÀU + SIZE) ===
$variant_stmt = $pdo->prepare("
    SELECT DISTINCT mau_Sac, kich_Thuoc
    FROM bien_the_san_pham
    WHERE id_SP = ?
    ORDER BY mau_Sac, kich_Thuoc
");
$variant_stmt->execute([$id_SP]);
$variants = $variant_stmt->fetchAll(PDO::FETCH_ASSOC);

// === 4. KHỞI TẠO GIỎ HÀNG ===
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// === 5. XỬ LÝ THÊM VÀO GIỎ HÀNG ===
$alert = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_to_cart') {
    $color = trim($_POST['color'] ?? '');
    $size = trim($_POST['size'] ?? '');
    $quantity = max(1, (int)($_POST['quantity'] ?? 1));

    if ($color && $size) {
        // Kiểm tra biến thể tồn tại
        $valid_variant = false;
        foreach ($variants as $v) {
            if ($v['mau_Sac'] === $color && $v['kich_Thuoc'] === $size) {
                $valid_variant = true;
                break;
            }
        }

        if ($valid_variant) {
            $cart_item = [
                'id' => $product['id_SP'],
                'name' => $product['ten_San_Pham'],
                'price' => (int)$product['gia_Ban'],
                'img' => $product['hinh_Anh'],
                'color' => $color,
                'size' => $size,
                'quantity' => $quantity
            ];

            $found = false;
            foreach ($_SESSION['cart'] as &$item) {
                if ($item['id'] === $cart_item['id'] &&
                    $item['color'] === $color &&
                    $item['size'] === $size) {
                    $item['quantity'] += $quantity;
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                $_SESSION['cart'][] = $cart_item;
            }

            $alert = "<script>alert('Đã thêm vào giỏ hàng!'); window.location.href='gioHang.php';</script>";
        } else {
            $alert = "<script>alert('Biến thể không tồn tại!');</script>";
        }
    } else {
        $alert = "<script>alert('Vui lòng chọn màu và size!');</script>";
    }
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
// === 6. SẢN PHẨM GỢI Ý (lặp lại, có thể xóa dòng trên nếu không cần)
$suggest_stmt->execute([$product['id_DM'], $id_SP]);
$suggest_products = $suggest_stmt->fetchAll(PDO::FETCH_ASSOC);

/* === 7. BÌNH LUẬN SẢN PHẨM === */
$id_SP = $_GET['id'] ?? '';

// Lấy danh sách bình luận
$comment_stmt = $pdo->prepare("
    SELECT bl.id_BL, bl.noi_Dung, bl.so_Sao, bl.ngay_Binh_Luan, bl.id_BL_cha,
           nd.ten_Dang_Nhap, nd.id_ND
    FROM binh_luan bl
    JOIN nguoi_dung nd ON bl.id_ND = nd.id_ND
    WHERE bl.id_SP = ?
    ORDER BY COALESCE(bl.id_BL_cha, bl.id_BL), bl.ngay_Binh_Luan ASC
");
$comment_stmt->execute([$id_SP]);
$comments = $comment_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($product['ten_San_Pham']) ?> - 160STORE</title>
    <link rel="stylesheet" href="https://kit.fontawesome.com/1147679ae7.js" crossorigin="anonymous">
    <script src="https://kit.fontawesome.com/1147679ae7.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="type.css">
    <style>
        :root {
            --gold: #FFD700;
            --black: #000;
            --dark: #1a1a1a;
            --gray: #222;
            --text: #fff;
            --success: #28a745;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--black);
            color: var(--text);
            margin: 0;
            padding: 0;
        }
        /* PRODUCT PAGE */
        .product-page {
            display: flex;
            gap: 40px;
            padding: 100px 5% 40px;
            max-width: 1200px;
            margin: 0 auto;
            flex-wrap: wrap;
        }
        .product-gallery img {
            width: 100%;
            max-width: 500px;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        }
        .product-info {
            flex: 1;
            min-width: 300px;
        }
        .product-info h2 {
            font-size: 2rem;
            margin-bottom: 10px;
            color: var(--gold);
        }
        .price {
            font-size: 1.8rem;
            color: #e63946;
            font-weight: bold;
            margin: 15px 0;
        }
        .price span {
            color: #aaa;
            text-decoration: line-through;
            font-size: 1.2rem;
            margin-left: 10px;
        }
        .product-info p {
            line-height: 1.7;
            margin: 15px 0;
            color: #ddd;
        }

        /* FORM */
        form select, form input[type="number"] {
            padding: 12px;
            margin: 10px 0;
            width: 100%;
            border-radius: 10px;
            border: 1px solid #444;
            background: #222;
            color: white;
            font-size: 1rem;
        }
        .add-cart {
            background: var(--gold);
            color: #000;
            padding: 15px 30px;
            border: none;
            border-radius: 12px;
            font-weight: 700;
            font-size: 1.1rem;
            cursor: pointer;
            width: 100%;
            margin-top: 20px;
            transition: 0.3s;
        }
        .add-cart:hover {
            background: #e6c200;
            transform: translateY(-2px);
        }

        /* COMMENT SECTION */
        .comment-section {
            padding: 40px 5%;
            background: var(--dark);
            margin: 40px 0;
        }
        .comment-section h3 {
            color: var(--gold);
            font-size: 1.8rem;
            margin-bottom: 20px;
            text-align: center;
            position: relative;
        }
        .comment-section h3::after {
            content: '';
            width: 50px;
            height: 3px;
            background: var(--gold);
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
        }
        .comment-list {
            max-width: 1200px;
            margin: 0 auto;
        }
        .comment-item {
            background: var(--gray);
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .comment-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(255, 215, 0, 0.2);
        }
        .comment-item .user {
            font-weight: 600;
            color: var(--gold);
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .comment-item .stars {
            color: var(--gold);
            font-size: 0.9rem;
            margin-left: 5px;
        }
        .comment-item .content {
            color: #ddd;
            line-height: 1.6;
            margin-bottom: 10px;
        }
        .comment-item small {
            color: #aaa;
            font-size: 0.8rem;
        }
        .comment-item .actions {
            margin-top: 10px;
        }
        .comment-item .actions a {
            font-size: 0.9rem;
            color: #007bff;
            text-decoration: none;
            margin-right: 15px;
            transition: color 0.3s;
        }
        .comment-item .actions a:hover {
            color: var(--gold);
            text-decoration: underline;
        }
        .reply-form {
            display: none;
            margin-top: 15px;
            padding: 15px;
            background: #1a1a1a;
            border-radius: 8px;
        }
        .comment-form, .reply-form {
            background: var(--gray);
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 20px;
        }
        .comment-form label, .reply-form label {
            display: block;
            margin-bottom: 5px;
            color: #ccc;
        }
        .comment-form select, .comment-form textarea,
        .reply-form select, .reply-form textarea {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #444;
            background: #1a1a1a;
            color: white;
            font-size: 1rem;
            margin-bottom: 10px;
            resize: vertical;
        }
        .comment-form button, .reply-form button {
            background: var(--gold);
            color: #000;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.3s;
        }
        .comment-form button:hover, .reply-form button:hover {
            background: #e6c200;
        }

        /* SUGGEST */
        .suggest-section {
            padding: 40px 5%;
            background: var(--dark);
            text-align: center;
        }
        .suggest-list {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 20px;
        }
        .suggest-item {
            background: var(--gray);
            border-radius: 12px;
            overflow: hidden;
            width: 220px;
            text-align: center;
            transition: 0.3s;
        }
        .suggest-item:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 20px rgba(255,215,0,0.2);
        }
        .suggest-item img {
            width: 100%;
            height: 180px;
            object-fit: cover;
        }
        .suggest-item h4 {
            padding: 10px;
            font-size: 1rem;
            color: white;
        }
        .suggest-item p {
            color: #e63946;
            font-weight: bold;
            padding-bottom: 10px;
        }
        
        /* FOOTER */
        footer {
            background: var(--dark);
            color: #aaa;
            padding: 40px 5% 20px;
            font-size: 0.9rem;
        }
        .footer-container {
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
            justify-content: space-between;
            max-width: 1200px;
            margin: 0 auto;
        }
        .footer-col h3 { color: white; margin-bottom: 15px; }
        .social {
            text-align: center;
            margin: 30px 0;
        }
        .social a {
            color: white;
            font-size: 1.5rem;
            margin: 0 10px;
        }
        .phone-float {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #25D366;
            color: white;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
            z-index: 1000;
        }

        @media (max-width: 768px) {
            .product-page { flex-direction: column; }
            .top-header { flex-direction: column; gap: 10px; }
            .search-box { max-width: 100%; }
            .comment-form, .comment-item, .reply-form { padding: 15px; }
            .comment-form button, .reply-form button { width: 100%; }
        }
    </style>
</head>
<body>
<header class="top-header">  
    <!-- Logo -->
    <div class="logo">
      <img src="https://file.hstatic.net/1000253775/file/logo_no_bf-05_3e6797f31bda4002a22464d6f2787316.png" alt="Logo">
    </div>
      <!-- Icon -->
      <div class="header-icons">
        <li><a href="TrangCaNhan.php" target="contentFrame"><i class="fa fa-user"></i><span>Trang cá nhân</span></a></li>
        <a href="dangNhap_DangKy.php"><i class="fas fa-user"></i> Đăng nhập</a>
        <a href="gioHang.php"><i class="fas fa-shopping-cart"></i> Giỏ hàng</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a>
      </div>
  </header>
  <nav>
      <!-- Menu -->
      <ul class="menu">
        <li><a href="#hangmoi">Hàng Mới</a>
    <!--- -->
        </li>
        <li><a href="#">Sản Phẩm</a>
          <ul class="submenu">
            <li><a href="danhSachCombo.php">Combo</a>
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
        <img src="<?= htmlspecialchars($product['hinh_Anh']) ?>" alt="<?= htmlspecialchars($product['ten_San_Pham']) ?>">
    </div>

    <div class="product-info">
        <h2><?= htmlspecialchars($product['ten_San_Pham']) ?></h2>
        <p class="price">
            <?= number_format($product['gia_Ban'], 0, ',', '.') ?>đ
            <span>~<?= number_format($product['gia_Goc'], 0, ',', '.') ?>đ</span>
        </p>

        <h3>Mô tả sản phẩm:</h3>
        <p><?= nl2br(htmlspecialchars($product['mo_Ta'])) ?></p>

        <form method="POST">
            <input type="hidden" name="action" value="add_to_cart">

            <label>Màu sắc:</label>
            <select name="color" required>
                <option value="">-- Chọn màu --</option>
                <?php 
                $colors = array_filter(array_unique(array_column($variants, 'mau_Sac')));
                foreach ($colors as $color): ?>
                    <option value="<?= htmlspecialchars($color) ?>"><?= htmlspecialchars($color) ?></option>
                <?php endforeach; ?>
            </select>

            <label>Size:</label>
            <select name="size" required>
                <option value="">-- Chọn size --</option>
                <?php 
                $sizes = array_filter(array_unique(array_column($variants, 'kich_Thuoc')));
                foreach ($sizes as $size): ?>
                    <option value="<?= htmlspecialchars($size) ?>"><?= htmlspecialchars($size) ?></option>
                <?php endforeach; ?>
            </select>

            <label>Số lượng:</label>
            <input type="number" name="quantity" value="1" min="1" style="width:100px;">

            <button type="submit" class="add-cart">
                THÊM VÀO GIỎ HÀNG
            </button>
        </form>
    </div>
</div>

<div class="comment-section">
    <h3>💬 Bình luận sản phẩm</h3>
    <div class="comment-list">
        <?php
        // Gom nhóm bình luận theo id_BL_cha
        $grouped = [];
        foreach ($comments as $c) {
            $grouped[$c['id_BL_cha'] ? $c['id_BL_cha'] : 'root'][] = $c;
        }

        function renderComments($parent_id, $grouped, $id_SP) {
            if (!isset($grouped[$parent_id ? $parent_id : 'root'])) return;

            foreach ($grouped[$parent_id ? $parent_id : 'root'] as $row): ?>
                <div class="comment-item" style="margin-left: <?= $parent_id ? 40 : 0 ?>px;">
                    <div class="user">
                        <?= htmlspecialchars($row['ten_Dang_Nhap']) ?>
                        <span class="stars">⭐ <?= $row['so_Sao'] ?>/5</span>
                    </div>
                    <div class="content">
                        <?= nl2br(htmlspecialchars($row['noi_Dung'])) ?>
                    </div>
                    <small>🕒 <?= date('d/m/Y H:i', strtotime($row['ngay_Binh_Luan'])) ?></small>

                    <div class="actions">
                         <a href="#" onclick="showReplyForm(<?= $row['id_BL'] ?>)">💬 Trả lời</a>
                             <?php if (isset($_SESSION['user']) && ($_SESSION['user']['id_ND'] == $row['id_ND'] || isset($_SESSION['admin']))) : ?>
                                  | <a href="sua_binh_luan.php?id_BL=<?= $row['id_BL'] ?>&id=<?= $id_SP ?>">✏️ Sửa</a>
                                  | <a href="xoa_binh_luan.php?id_BL=<?= $row['id_BL'] ?>&id=<?= $id_SP ?>" onclick="return confirm('Xóa bình luận này?')">🗑️ Xóa</a>
                              <?php endif; ?>
                    </div>

                    <!-- Form trả lời -->
                    <div class="reply-form" id="reply-form-<?= $row['id_BL'] ?>">
                        <form action="them_binh_luan.php" method="POST">
                            <input type="hidden" name="id_SP" value="<?= htmlspecialchars($id_SP) ?>">
                            <input type="hidden" name="id_ND" value="<?= $_SESSION['user']['id_ND'] ?>">
                            <input type="hidden" name="id_BL_cha" value="<?= $row['id_BL'] ?>">

                            <label for="so_Sao">Đánh giá:</label>
                            <select name="so_Sao" required>
                                <option value="5">⭐ 5 - Rất tốt</option>
                                <option value="4">⭐ 4 - Tốt</option>
                                <option value="3">⭐ 3 - Trung bình</option>
                                <option value="2">⭐ 2 - Kém</option>
                                <option value="1">⭐ 1 - Rất kém</option>
                            </select>

                            <label for="noi_Dung">Nội dung trả lời:</label>
                            <textarea name="noi_Dung" rows="3" required></textarea>
                            <button type="submit">Gửi trả lời</button>
                            <button type="button" onclick="hideReplyForm(<?= $row['id_BL'] ?>)">Hủy</button>
                        </form>
                    </div>

                    <!-- Hiển thị phản hồi con -->
                    <?php renderComments($row['id_BL'], $grouped, $id_SP); ?>
                </div>
            <?php endforeach;
        }
        renderComments(null, $grouped, $id_SP);
        ?>
    </div>

    <div class="comment-form">
        <?php if (isset($_SESSION['user'])): ?>
            <form action="them_binh_luan.php" method="POST">
                <input type="hidden" name="id_SP" value="<?= htmlspecialchars($id_SP) ?>">
                <input type="hidden" name="id_ND" value="<?= $_SESSION['user']['id_ND'] ?>">

                <label for="so_Sao">Đánh giá:</label>
                <select name="so_Sao" required>
                    <option value="5">⭐ 5 - Rất tốt</option>
                    <option value="4">⭐ 4 - Tốt</option>
                    <option value="3">⭐ 3 - Trung bình</option>
                    <option value="2">⭐ 2 - Kém</option>
                    <option value="1">⭐ 1 - Rất kém</option>
                </select>

                <label for="noi_Dung">Nội dung bình luận:</label>
                <textarea name="noi_Dung" rows="3" required></textarea>
                <button type="submit">Gửi bình luận</button>
            </form>
        <?php else: ?>
            <p style="color: #ff4d4d; text-align: center;">⚠️ <a href="dangNhap_DangKy.php" style="color: #007bff;">Đăng nhập</a> để bình luận.</p>
        <?php endif; ?>
    </div>
</div>
<!-- GỢI Ý SẢN PHẨM TƯƠNG TỰ (IN RA HẾT CÙNG DANH MỤC) -->
<div class="suggest-section">
    <h3>Gợi ý sản phẩm tương tự</h3>
    <div class="suggest-list">
        <?php
        // Lấy tất cả sản phẩm cùng danh mục (trừ sản phẩm hiện tại)
        $suggest_stmt = $pdo->prepare("
            SELECT id_SP, ten_San_Pham, gia_Ban, hinh_Anh
            FROM san_pham
            WHERE id_DM = ? AND id_SP <> ?
            ORDER BY ngay_Tao DESC
        ");
        $suggest_stmt->execute([$product['id_DM'], $id_SP]);
        $suggest_products = $suggest_stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($suggest_products)): ?>
            <p style="color:#aaa; text-align:center; width:100%;">Chưa có sản phẩm tương tự.</p>
        <?php else: ?>
            <?php foreach ($suggest_products as $s): ?>
                <div class="suggest-item">
                    <a href="aohoodie.php?id=<?= urlencode($s['id_SP']) ?>">
                        <img src="<?= htmlspecialchars($s['hinh_Anh']) ?>" alt="<?= htmlspecialchars($s['ten_San_Pham']) ?>">
                        <h4><?= htmlspecialchars($s['ten_San_Pham']) ?></h4>
                        <p><?= number_format($s['gia_Ban'], 0, ',', '.') ?>đ</p>
                    </a>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
</div>

<!-- FOOTER -->
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

<?= $alert ?>

<script>
    function showReplyForm(id) {
        document.getElementById('reply-form-' + id).style.display = 'block';
    }

    function hideReplyForm(id) {
        document.getElementById('reply-form-' + id).style.display = 'none';
    }
</script>

</body>
</html>