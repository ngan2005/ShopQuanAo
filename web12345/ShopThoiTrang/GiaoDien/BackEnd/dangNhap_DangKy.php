<?php
ob_start();                     // Bắt đầu output buffering
session_start();                // Phải ở đầu file, không có khoảng trắng trước <?php
require_once 'database.php';

$db   = new Database();
$conn = $db->connect();
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$msg = '';

/* ==== ĐĂNG NHẬP ==== */
if (isset($_POST['dangNhap'])) {
    $user = trim($_POST['ten_Dang_Nhap']);
    $pass = trim($_POST['mat_Khau']);

    $stmt = $conn->prepare("SELECT * FROM nguoi_dung WHERE ten_Dang_Nhap = ? AND mat_Khau = ?");
    $stmt->execute([$user, $pass]);
    $u = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($u) {
        $_SESSION['user'] = $u;

        // Chuẩn hoá vai trò (loại bỏ khoảng trắng, chuyển về chữ thường)
        $role = trim(strtolower($u['vai_Tro']));

        if ($role === 'admin') {
            header("Location: admin.php");
        } else {
            header("Location: TrangChu.php");   // <-- Đảm bảo file tồn tại
        }
        exit;                                   // DỪNG HOÀN TOÀN
    } else {
        $msg = "<div class='msg error'>Sai tài khoản hoặc mật khẩu!</div>";
    }
}

/* ==== ĐĂNG KÝ ==== */
if (isset($_POST['dangKy'])) {
    $ten   = trim($_POST['ten_Dang_Nhap']);
    $pass  = trim($_POST['mat_Khau']);
    $hoTen = trim($_POST['ho_Ten']);
    $email = trim($_POST['email']);
    $vaiTro = 'khach_hang';
    $ngayTao = date('Y-m-d H:i:s');

    $check = $conn->prepare("SELECT id_ND FROM nguoi_dung WHERE ten_Dang_Nhap = ?");
    $check->execute([$ten]);
    if ($check->fetch()) {
        $msg = "<div class='msg error'>Tên đăng nhập đã tồn tại!</div>";
    } else {
        $stmt = $conn->prepare("INSERT INTO nguoi_dung 
                (ten_Dang_Nhap, mat_Khau, ho_Ten, email, vai_Tro, ngay_Tao)
                VALUES (?,?,?,?,?,?)");
        $stmt->execute([$ten, $pass, $hoTen, $email, $vaiTro, $ngayTao]);
        $msg = "<div class='msg success'>Đăng ký thành công! Hãy đăng nhập.</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Đăng nhập / Đăng ký</title>
<style>
:root {
    --black: #000;
    --gold: #FFD700;
    --dark: #1a1a1a;
    --text: #fff;
}

body {
    background: var(--black);
    color: var(--text);
    font-family: Arial;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
}

.container {
    width: 400px;
    background: var(--dark);
    padding: 20px;
    border-radius: 10px;
}

input, button {
    width: 100%;
    padding: 10px;
    margin: 6px 0;
    border: none;
    border-radius: 6px;
    box-sizing: border-box;
    background: #333;
    color: var(--text);
}

button {
    background: var(--gold);
    color: var(--black);
    font-weight: bold;
}

.msg.success {
    background: rgba(255, 215, 0, 0.2);
    color: var(--gold);
    padding: 8px;
    border-radius: 6px;
    margin: 10px 0;
}

.msg.error {
    background: rgba(255, 85, 85, 0.2);
    color: #ff5555;
    padding: 8px;
    border-radius: 6px;
    margin: 10px 0;
}

.tab button {
    background: none;
    color: var(--gold);
    border: none;
    cursor: pointer;
    margin-right: 10px;
    font-size: 1.1em;
}

.tab button.active {
    color: var(--gold);
    font-weight: bold;
}

.hidden {
    display: none;
}

.input-wrap {
    position: relative;
}

.toggle-pass {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
    font-size: 1.2em;
    color: var(--text);
}
</style>
</head>
<body>
<div class="container">
  <h2 id="form-title">Đăng nhập</h2>
  <?= $msg ?>
  <div class="tab">
    <button type="button" onclick="showForm('login')" id="btnLogin" class="active">Đăng nhập</button>
    <button type="button" onclick="showForm('register')" id="btnRegister">Đăng ký</button>
  </div>

  <!-- FORM ĐĂNG NHẬP -->
  <form method="POST" id="form-login">
    <input type="text" name="ten_Dang_Nhap" placeholder="Tên đăng nhập" autocomplete="username" required>
    <div class="input-wrap">
      <input type="password" name="mat_Khau" id="pass-login" placeholder="Mật khẩu" autocomplete="current-password" required>
      <span class="toggle-pass" onclick="togglePass('pass-login')">Mắt</span>
    </div>
    <button type="submit" name="dangNhap">Đăng nhập</button>
  </form>

  <!-- FORM ĐĂNG KÝ -->
  <form method="POST" id="form-register" class="hidden">
    <input type="text" name="ho_Ten" placeholder="Họ tên" autocomplete="name" required>
    <input type="text" name="ten_Dang_Nhap" placeholder="Tên đăng nhập" autocomplete="username" required>
    <div class="input-wrap">
      <input type="password" name="mat_Khau" id="pass-reg" placeholder="Mật khẩu" autocomplete="new-password" required>
      <span class="toggle-pass" onclick="togglePass('pass-reg')">Mắt</span>
    </div>
    <input type="email" name="email" placeholder="Email" autocomplete="email">
    <button type="submit" name="dangKy">Đăng ký</button>
  </form>
</div>

<script>
function showForm(name) {
    document.querySelectorAll('form').forEach(f => f.classList.add('hidden'));
    document.querySelectorAll('.tab button').forEach(b => b.classList.remove('active'));
    document.getElementById('form-' + name).classList.remove('hidden');
    document.getElementById('btn' + name.charAt(0).toUpperCase() + name.slice(1)).classList.add('active');
    document.getElementById('form-title').innerText = name === 'login' ? 'Đăng nhập' : 'Đăng ký';
}
function togglePass(id) {
    const el = document.getElementById(id);
    el.type = (el.type === 'password') ? 'text' : 'password';
}
</script>
</body>
</html>
<?php
ob_end_flush();   // Kết thúc buffering, gửi HTML
?>