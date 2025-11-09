<?php
// File: register.php

// -- KHOI TAO --
// Bat dau session
session_start();

// Nap cac file can thiet
require_once 'config.php';
require_once 'includes/db.php';

// Neu da dang nhap, chuyen huong ve trang chu
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header('location: index.php');
    exit;
}

// -- KHOI TAO BIEN --
$db = new Database(); // Tao ket noi CSDL
$full_name = "";
$email = "";
$error = "";
$success = "";

// -- XU LY KHI FORM DUOC GUI --
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 1. Lay va lam sach du lieu
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // 2. Kiem tra tinh hop le
    if (empty($full_name) || empty($email) || empty($password)) {
        $error = "Vui long dien day du thong tin.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Dinh dang email khong hop le.";
    } elseif (strlen($password) < 6) {
        $error = "Mat khau phai co it nhat 6 ky tu.";
    } elseif ($password !== $confirm_password) {
        $error = "Mat khau xac nhan khong khop.";
    } else {
        // 3. Kiem tra email co ton tai khong
        $db->query('SELECT user_id FROM users WHERE email = :email');
        $db->bind(':email', $email);
        $db->execute();
        
        if ($db->rowCount() > 0) {
            $error = "Email nay da duoc su dung.";
        } else {
            // 4. Ma hoa mat khau bang phuong phap an toan nhat
            $password_hashed = password_hash($password, PASSWORD_DEFAULT);

            // 5. Luu vao CSDL
            try {
                $db->query('INSERT INTO users (full_name, email, password, role_id, created_at) VALUES (:full_name, :email, :password, :role_id, NOW())');
                $db->bind(':full_name', $full_name);
                $db->bind(':email', $email);
                $db->bind(':password', $password_hashed);
                $db->bind(':role_id', 2); // Mac dinh la Customer

                if ($db->execute()) {
                    $success = "Dang ky thanh cong! Chuyen den trang dang nhap...";
                    header("refresh:3;url=login.php");
                } else {
                    $error = "Da co loi xay ra, vui long thu lai.";
                }
            } catch (PDOException $e) {
                $error = "Loi he thong: " . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>AdminLTE 3 | Registration Page</title>

  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <link rel="stylesheet" href="assets/plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <link rel="stylesheet" href="assets/dist/css/adminlte.min.css">
</head>
<body class="hold-transition register-page">
<div class="register-box">
  <div class="register-logo">
    <a href="index.php"><b>Admin</b>LTE</a>
  </div>

  <div class="card">
    <div class="card-body register-card-body">
      <p class="login-box-msg">Register a new membership</p>

      <?php if(!empty($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
      <?php endif; ?>
      <?php if(!empty($success)): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
      <?php endif; ?>

      <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
        <div class="input-group mb-3">
          <input type="text" class="form-control" name="full_name" placeholder="Full name" value="<?php echo htmlspecialchars($full_name); ?>">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-user"></span>
            </div>
          </div>
        </div>
        <div class="input-group mb-3">
          <input type="email" class="form-control" name="email" placeholder="Email" value="<?php echo htmlspecialchars($email); ?>">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-envelope"></span>
            </div>
          </div>
        </div>
        <div class="input-group mb-3">
          <input type="password" class="form-control" name="password" placeholder="Password">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>
        <div class="input-group mb-3">
          <input type="password" class="form-control" name="confirm_password" placeholder="Retype password">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-8">
            </div>
          <div class="col-4">
            <button type="submit" class="btn btn-primary btn-block">Register</button>
          </div>
          </div>
      </form>

      <a href="login.php" class="text-center">I already have a membership</a>
    </div>
    </div></div>
<script src="assets/plugins/jquery/jquery.min.js"></script>
<script src="assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/dist/js/adminlte.min.js"></script>
</body>
</html>