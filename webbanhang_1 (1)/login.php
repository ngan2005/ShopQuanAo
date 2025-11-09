<?php
// -- KHOI TAO --
// Bat dau session
session_start();

// Nap cac file can thiet
require_once 'config.php';
require_once 'includes/db.php';

// // Neu da dang nhap, chuyen huong ve trang chu
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
  header('location: index.php');
  exit;
}

// -- KHOI TAO BIEN --
$db = new Database(); // Tao ket noi CSDL
$email = '';
$error = '';

// -- XU LY KHI FORM DUOC GUI --
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  // 1. Lay va lam sach du lieu
  $email = trim($_POST['email']);
  $password = $_POST['password'];

  // 2. Kiem tra
  if (empty($email) || empty($password)) {
    $error = 'Vui long nhap email va mat khau.';
  } else {
    // 3. Tim nguoi dung trong CSDL
    $db->query('SELECT user_id, full_name, email, password, role_id,image_url  FROM users WHERE email = :email');
    $db->bind(':email', $email);
    $user = $db->single();

    // 4. Xac thuc email và mat khau
    if (!$user) {
      //  Email khong ton tai trong CSDL
      $error = 'Email này không tồn tại trong hệ thống.';
    } else if (!password_verify($password, $user['password'])) {
      // Email ton tai, nhung sai mat khau
      // Su dung password_verify de so sanh mat khau nguoi dung nhap voi hash trong CSDL 
      $error = 'Mật khẩu bạn nhập không chính xác.';
    } else {
      // DANG NHAP THANH CONG
      // Luu thong tin vao session
      $_SESSION['loggedin'] = true;
      $_SESSION['user_id'] = $user['user_id'];
      $_SESSION['user_fullname'] = $user['full_name'];
      $_SESSION['user_role_id'] = $user['role_id'];
      $_SESSION['user_image_url'] = $user['image_url'];

      // Khởi tạo giỏ hàng cá nhân rỗng cho người dùng
      $_SESSION['user_cart'] = []; // Khởi tạo rỗng trước
      try {
        // 1. Lấy các sản phẩm trong giỏ của user
        $db->query('
        SELECT 
            ci.variant_id, ci.quantity,
            p.product_id, p.product_name,
            pv.size, pv.price,
            (SELECT image_url FROM ProductImages pi 
             WHERE pi.product_id = p.product_id 
             ORDER BY pi.image_id ASC LIMIT 1) as image_url
        FROM CartItems ci
        JOIN ProductVariants pv ON ci.variant_id = pv.variant_id
        JOIN Products p ON pv.product_id = p.product_id
        WHERE ci.user_id = :user_id
    ');
        $db->bind(':user_id', $user['user_id']);
        $saved_items = $db->resultSet();

        // 2. Nạp các sản phẩm đã lưu vào $_SESSION['user_cart']
        if ($saved_items) {
          foreach ($saved_items as $item) {
            $variant_id = $item['variant_id'];
            $_SESSION['user_cart'][$variant_id] = [
              'product_id' => $item['product_id'],
              'variant_id' => $variant_id,
              'product_name' => $item['product_name'],
              'size' => $item['size'],
              'price' => (float)$item['price'],
              'image_url' => $item['image_url'] ?? 'assets/dist/img/default-product.png',
              'quantity' => (int)$item['quantity']
            ];
          }
        }
      } catch (PDOException $e) {
        // Nếu lỗi CSDL, cứ cho người dùng vào với giỏ hàng rỗng
        $_SESSION['user_cart'] = [];
        error_log('Lỗi không thể load giỏ hàng: ' . $e->getMessage());
      }

      // Cap nhat thoi gian dang nhap
      try {
        $db->query('UPDATE Users SET last_login = NOW() WHERE user_id = :user_id');
        $db->bind(':user_id', $user['user_id']);
        $db->execute();
      } catch (PDOException $e) {
        // error_log('Failed to update last_login: ' . $e->getMessage());

      }
      // Chuyen huong den trang quan tri
      switch ($_SESSION['user_role_id']) {
        case '1':
        case '2':
          header('Location: index.php');
          break;
        case '3':
          header('Location: /webbanhang/index.php?page=profile'); // trang người dùng
          break;
        default:
          header('Location: /');
          break;
      }
      exit;
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>AdminLTE 3 | Log in</title>

  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <link rel="stylesheet" href="assets/plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <link rel="stylesheet" href="assets/dist/css/adminlte.min.css">
</head>

<body class="hold-transition login-page">
  <div class="login-box">
    <div class="login-logo">
      <a href="index.php"><b>Admin</b>LTE</a>
    </div>
    <div class="card">
      <div class="card-body login-card-body">
        <p class="login-box-msg">Sign in to start your session</p>

        <?php if (!empty($error)): ?>
          <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
          <div class="input-group mb-3">
            <input type="email" class="form-control" name="email" placeholder="Email">
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
          <div class="row">
            <div class="col-8">
            </div>
            <div class="col-4">
              <button type="submit" class="btn btn-primary btn-block">Sign In</button>
            </div>
          </div>
        </form>

        <p class="mb-0">
          <a href="register.php" class="text-center">Register a new membership</a>
        </p>
      </div>
    </div>
  </div>
  <script src="assets/plugins/jquery/jquery.min.js"></script>
  <script src="assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/dist/js/adminlte.min.js"></script>
</body>

</html>