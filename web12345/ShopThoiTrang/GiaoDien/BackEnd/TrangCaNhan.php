<?php
session_start();
require_once 'database.php';

if (!isset($_SESSION['user'])) {
    header("Location: ../dangNhap_DangKy.php");
    exit;
}

$db = new Database();
$conn = $db->connect();
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$user = $_SESSION['user'];
$msg = "";

// LẤY THÔNG TIN MỚI NHẤT
$stmt = $conn->prepare("SELECT * FROM nguoi_dung WHERE id_ND = ?");
$stmt->execute([$user['id_ND']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// CẬP NHẬT THÔNG TIN
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ho_Ten = trim($_POST['ho_Ten']);
    $email = trim($_POST['email']);
    $sdt = trim($_POST['sdt']);
    $dia_Chi = trim($_POST['dia_Chi']);
    $mat_Khau = trim($_POST['mat_Khau']);

    try {
        $stmt = $conn->prepare("UPDATE nguoi_dung SET ho_Ten=?, email=?, sdt=?, dia_Chi=?, mat_Khau=? WHERE id_ND=?");
        $stmt->execute([$ho_Ten, $email, $sdt, $dia_Chi, $mat_Khau, $user['id_ND']]);
        $msg = "<div class='msg success'>Cập nhật thành công!</div>";

        $_SESSION['user']['ho_Ten'] = $ho_Ten;
        $_SESSION['user']['email'] = $email;
        $_SESSION['user']['sdt'] = $sdt;
        $_SESSION['user']['dia_Chi'] = $dia_Chi;
    } catch (Exception $e) {
        $msg = "<div class='msg error'>Lỗi: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông tin cá nhân | 160STORE</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="type.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #FFD700;
            --text: #fff;
            --bg: #000;
            --card: #1a1a1a;
            --border: #333;
            --input-bg: #222;
            --input-border: #444;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
        }

        /* ===== HEADER (giống trang chủ) ===== */
        header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: rgba(0,0,0,0.95);
            backdrop-filter: blur(10px);
            z-index: 1000;
            padding: 15px 5%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid rgba(255,215,0,0.2);
        }

        .logo {
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--primary);
            text-decoration: none;
        }

        .logo span { color: #fff; font-weight: 400; }

        .nav-icons a {
            color: white;
            font-size: 1.4rem;
            margin-left: 20px;
            position: relative;
            text-decoration: none;
        }

        .nav-icons a span {
            position: absolute;
            top: -8px;
            right: -8px;
            background: var(--primary);
            color: #000;
            font-size: 0.7rem;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }

        /* ===== MAIN CONTENT ===== */
        .main-content {
            margin-top: 80px;
            padding: 40px 5%;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        .profile-card {
            background: var(--card);
            border-radius: 16px;
            padding: 30px;
            border: 1px solid var(--border);
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }

        .profile-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .avatar {
            width: 90px;
            height: 90px;
            background: var(--primary);
            color: #000;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            font-size: 2rem;
            font-weight: bold;
        }

        .profile-header h2 {
            font-size: 1.6rem;
            margin-bottom: 8px;
        }

        .profile-header p {
            color: #ccc;
            font-size: 0.95rem;
        }

        .profile-header .role {
            display: inline-block;
            background: var(--primary);
            color: #000;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-top: 8px;
        }

        /* ===== FORM ===== */
        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #ccc;
            font-size: 0.95rem;
        }

        .form-group input {
            width: 100%;
            padding: 14px 16px;
            background: var(--input-bg);
            border: 1.5px solid var(--input-border);
            border-radius: 10px;
            color: white;
            font-size: 1rem;
            transition: all 0.3s;
        }

        .form-group input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(255,215,0,0.2);
        }

        .form-group input[readonly] {
            background: #111;
            color: #666;
            cursor: not-allowed;
        }

        .btn {
            width: 100%;
            padding: 14px;
            background: var(--primary);
            color: #000;
            border: none;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            transition: 0.3s;
            margin-top: 10px;
        }

        .btn:hover {
            background: #e6c200;
            transform: translateY(-2px);
        }

        .msg {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            font-weight: 500;
        }

        .msg.success {
            background: rgba(255,215,0,0.15);
            color: var(--primary);
            border: 1px solid rgba(255,215,0,0.3);
        }

        .msg.error {
            background: rgba(255,0,0,0.15);
            color: #ff6b6b;
            border: 1px solid rgba(255,0,0,0.3);
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .main-content { padding: 20px; }
            header { flex-direction: column; gap: 15px; }
            .nav-icons { order: -1; }
        }
    </style>
</head>
<!-- HEADER (giống trang chủ) -->
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
        <li><a href="TrangCaNhan.php" target="contentFrame"><i class="fa fa-user"></i><span>Trang cá nhân</span></a></li>
        <a href="dangNhap_DangKy.php"><i class="fas fa-user"></i> Đăng nhập</a>
        <a href="gioHang.html"><i class="fas fa-shopping-cart"></i> Giỏ hàng</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a>
      </div>
  </header>

<!-- MAIN CONTENT -->
<div class="main-content">
    <div class="profile-card">
        <div class="profile-header">
            <div class="avatar">
                <?= strtoupper(substr($user['ho_Ten'], 0, 1)) ?>
            </div>
            <h2><?= htmlspecialchars($user['ho_Ten']) ?></h2>
            <p><?= htmlspecialchars($user['email']) ?></p>
            <div class="role">
                <?= $user['vai_Tro'] === 'admin' ? 'QUẢN TRỊ VIÊN' : 'KHÁCH HÀNG' ?>
            </div>
        </div>

        <?= $msg ?>

        <form method="POST">
            <div class="form-group">
                <label>Tên đăng nhập</label>
                <input type="text" value="<?= htmlspecialchars($user['ten_Dang_Nhap']) ?>" readonly>
            </div>

            <div class="form-group">
                <label>Họ và tên</label>
                <input type="text" name="ho_Ten" value="<?= htmlspecialchars($user['ho_Ten']) ?>" required>
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
            </div>

            <div class="form-group">
                <label>Số điện thoại</label>
                <input type="text" name="sdt" value="<?= htmlspecialchars($user['sdt']) ?>">
            </div>

            <div class="form-group">
                <label>Địa chỉ nhận hàng</label>
                <input type="text" name="dia_Chi" value="<?= htmlspecialchars($user['dia_Chi']) ?>">
            </div>

            <div class="form-group">
                <label>Mật khẩu mới (để trống nếu không đổi)</label>
                <input type="password" name="mat_Khau" placeholder="Nhập mật khẩu mới...">
            </div>

            <button type="submit" class="btn">
                LƯU THAY ĐỔI
            </button>
        </form>

        <a href="trangChu.php" class="back-link">
            Quay lại trang chủ
        </a>
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