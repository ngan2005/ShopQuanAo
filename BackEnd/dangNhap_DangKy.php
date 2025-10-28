<?php
session_start();
require_once 'database.php';
$db = new Database();
$conn = $db->connect();

$msg = '';
$isLogin = isset($_GET['action']) && $_GET['action'] === 'login';

// ==== Xử lý Đăng ký ====
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['dangKy'])) {
    $tenDN = trim($_POST['ten_Dang_Nhap']);
    $matKhau = trim($_POST['mat_Khau']);
    $hoTen = trim($_POST['ho_Ten']);
    $email = trim($_POST['email']);
    $sdt = trim($_POST['sdt']);
    $diaChi = trim($_POST['dia_Chi']);
    $vaiTro = $_POST['vai_Tro'] ?? 'user';
    $ngayTao = date('Y-m-d H:i:s');

    // Kiểm tra trùng tên đăng nhập hoặc email
    $check = $conn->prepare("SELECT * FROM nguoi_dung WHERE ten_Dang_Nhap = ? OR email = ?");
    $check->execute([$tenDN, $email]);
    if ($check->rowCount() > 0) {
        $msg = "<p class='error'>⚠️ Tên đăng nhập hoặc email đã tồn tại!</p>";
    } else {
        $stmt = $conn->prepare("INSERT INTO nguoi_dung 
            (ten_Dang_Nhap, mat_Khau, ho_Ten, email, sdt, dia_Chi, vai_Tro, ngay_Tao)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$tenDN, $matKhau, $hoTen, $email, $sdt, $diaChi, $vaiTro, $ngayTao]);
        $msg = "<p class='success'>✅ Đăng ký thành công! Vui lòng đăng nhập.</p>";
        $isLogin = true;
    }
}

// ==== Xử lý Đăng nhập (dùng tên đăng nhập) ====
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['dangNhap'])) {
    $tenDN = trim($_POST['ten_Dang_Nhap']);
    $matKhau = trim($_POST['mat_Khau']);

    // 🔹 Chỉ đăng nhập bằng tên đăng nhập (KHÔNG phải email)
    $stmt = $conn->prepare("SELECT * FROM nguoi_dung WHERE ten_Dang_Nhap = ? AND mat_Khau = ?");
    $stmt->execute([$tenDN, $matKhau]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $_SESSION['user'] = $user;

        if ($user['vai_Tro'] === 'admin') {
            header("Location: admin.php");
        } else {
            header("Location: TrangChu.php");
        }
        exit;
    } else {
        $msg = "<p class='error'>❌ Sai tên đăng nhập hoặc mật khẩu!</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Đăng nhập / Đăng ký - 160STORE</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
body {
  background: #111;
  color: #fff;
  font-family: Arial, sans-serif;
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100vh;
}
.container {
  background: #1e1e1e;
  padding: 30px;
  border-radius: 12px;
  width: 370px;
  box-shadow: 0 0 20px rgba(0,0,0,0.6);
}
h2 {
  text-align: center;
  color: #00ff88;
  margin-bottom: 10px;
}
form {
  display: flex;
  flex-direction: column;
  gap: 10px;
}
input, select {
  padding: 10px;
  border: none;
  border-radius: 6px;
  background: #333;
  color: white;
}
button {
  padding: 10px;
  background: linear-gradient(90deg,#00ff88,#00bfff);
  border: none;
  border-radius: 8px;
  color: #111;
  font-weight: bold;
  cursor: pointer;
  margin-top: 10px;
}
p.switch {
  text-align: center;
  font-size: 14px;
}
a { color: #00bfff; text-decoration: none; }
a:hover { text-decoration: underline; }
p.error { color: #ff6666; text-align: center; }
p.success { color: #00ff88; text-align: center; }
label { font-size: 13px; color: #aaa; }
</style>
</head>
<body>

<div class="container">
  <h2><?= $isLogin ? 'Đăng nhập' : 'Đăng ký' ?></h2>
  <?= $msg ?>

  <?php if ($isLogin): ?>
  <!-- Form Đăng nhập -->
  <form method="POST">
    <input type="text" name="ten_Dang_Nhap" placeholder="Tên đăng nhập" required>
    <input type="password" name="mat_Khau" placeholder="Mật khẩu" required>
    <button type="submit" name="dangNhap"><i class="fas fa-sign-in-alt"></i> Đăng nhập</button>
  </form>
  <p class="switch">Chưa có tài khoản? <a href="?action=register">Đăng ký ngay</a></p>

  <?php else: ?>
  <!-- Form Đăng ký -->
  <form method="POST">
    <input type="text" name="ten_Dang_Nhap" placeholder="Tên đăng nhập" required>
    <input type="text" name="ho_Ten" placeholder="Họ tên" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="text" name="sdt" placeholder="Số điện thoại" required>
    <input type="text" name="dia_Chi" placeholder="Địa chỉ" required>
    <input type="password" name="mat_Khau" placeholder="Mật khẩu (tối đa 6 ký tự)" maxlength="6" required>

    <label for="vai_Tro">Đăng ký với tư cách:</label>
    <select name="vai_Tro" id="vai_Tro" required>
      <option value="user">Khách hàng</option>
      <option value="admin">Quản trị viên</option>
    </select>

    <button type="submit" name="dangKy"><i class="fas fa-user-plus"></i> Đăng ký</button>
  </form>
  <p class="switch">Đã có tài khoản? <a href="?action=login">Đăng nhập</a></p>
  <?php endif; ?>
</div>

</body>
</html>
