<?php
// Luôn luôn khởi động session đầu tiên
session_start();

// Kiểm tra nếu người dùng chưa đăng nhập, chuyển hướng về trang login
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

// Nạp các file cấu hình và CSDL cần thiết
require_once 'config.php';
require_once 'includes/db.php';

// Khởi tạo đối tượng Database để sử dụng ở các trang con
$db = new Database();

// === PHẦN LOGIC XỬ LÝ TRANG CON (ĐƯỢC ĐƯA LÊN ĐẦU) ===

// 1. Xác định trang cần hiển thị

$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
$path = str_replace('-', '/', $page);
$page_path = "pages/{$path}.php";

//======= PHÂN QUYỀN NGƯỜI DÙNG ========= 
$user_role_id = $_SESSION['user_role_id'];

// Dựa trên cấu trúc vai trò Admin/Staff (1: Admin, 2: Staff, 3: Customer)
$permissions = [
    'dashboard' => [1, 2], // Admin, Staff
    // Quản lý sản phẩm & đơn hàng (cho cả Admin và Staff)
    'product-list' => [1, 2],
    'product-add' => [1, 2],
    'product-edit' => [1, 2],
    'product-detail' => [1, 2],
    'category-list' => [1, 2],
    'category-add' => [1, 2],
    'category-edit' => [1, 2],
    'order-list' => [1, 2],
    'order-status-list' => [1, 2],
    // Các trang chỉ dành cho Admin
    'promotion-list' => [1],
    'user-list' => [1],
    'user-add' => [1],
    'user-edit' => [1],
    'role-list' => [1],
    'feedback-list' => [1],
    'report-revenue' => [1],
    'report-products' => [1],
    // Trang cá nhân cho tất cả mọi người
    'profile' => [1, 2, 3],
    // Phần giỏ hàng cho User
    'admincart-view' => [1, 2, 3],
    'admincart-add' => [1, 2, 3],
    'admincart-update' => [1, 2, 3],
    'admincart-remove' => [1, 2, 3]
];
// Đặt măc định không có quyền truy cập 
$has_permission = false;

// Kiểm tra xem trang hiện tại có trong danh sách phân quyền không
if (isset($permissions[$page])) {
    // Kiểm tra vai trò của người dùng có nằm trong danh sách được phép không
    if (in_array($user_role_id, $permissions[$page])) {
        $has_permission = true;
    }
}

//======= PHÂN QUYỀN NGƯỜI DÙNG ========= 
// 2. Bắt đầu bộ đệm đầu ra để "bắt" lại nội dung của trang con
ob_start(); // Bắt đầu bộ đệm đầu ra

// 3. Nạp file của trang con nếu người dung có quyền, nếu không hiển thị lỗi 403
if ($has_permission && file_exists($page_path)) {
    include $page_path;
} else if (!$has_permission) {
    echo '<section class="content">
            <div class="error-page">
                <h2 class="headline text-danger"> 403</h2>
                <div class="error-content">
                    <h3><i class="fas fa-exclamation-triangle text-danger"></i> Oops! Access Denied.</h3>
                    <p>You do not have permission to access this page.</p>
                </div>
            </div>
          </section>';
} else {     // Nếu không tìm thấy file, hiển thị trang lỗi 404
    echo '<section class="content">
            <div class="error-page">
                <h2 class="headline text-warning"> 404</h2>
                <div class="error-content">
                    <h3><i class="fas fa-exclamation-triangle text-warning"></i> Oops! Page not found.</h3>
                    <p>We could not find the page you were looking for.</p>
                </div>
            </div>
          </section>';
}

// 4. Lấy nội dung đã được "bắt" từ bộ đệm và lưu vào biến, sau đó xóa bộ đệm.
$page_content = ob_get_clean(); 

// === PHẦN HIỂN THỊ GIAO DIỆN CHÍNH (HTML) ===
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard</title>

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="assets/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="assets/dist/css/adminlte.min.css">
</head>

<body class="hold-transition sidebar-mini layout-fixed">

    <div class="wrapper">

        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                        <i class="fas fa-bars"></i>
                    </a>
                </li>
            </ul>

            <ul class="navbar-nav ml-auto">
                 <!-- === PHẦN GIỎ HÀNG HIỂN THỊ  -->
                <li class="nav-item">
                    <a class="nav-link" href="index.php?page=admincart-view" title="Giỏ hàng cá nhân">
                        <i class="fas fa-shopping-cart"></i>
                        <?php
                            // logic đếm số lượng giỏ hàng user_cart
                            $user_cart_count = 0;
                            if (!empty($_SESSION['user_cart'])) {
                                $user_cart_count = count($_SESSION['user_cart']);
                            }
                            $badge_style = ($user_cart_count > 0) ? '' : 'style="display: none;"';
                        ?>
                        <span id="cart-count-badge" class="badge badge-danger navbar-badge" <?php echo $badge_style; ?>>
                            <?php echo $user_cart_count > 0 ? $user_cart_count : ''; ?>
                        </span>
                    </a>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link" data-toggle="dropdown" href="#">
                        <img src="<?php echo !empty($_SESSION['user_image_url']) ? htmlspecialchars($_SESSION['user_image_url']) : 'assets/dist/img/avatar.png'; ?>"
                            class="img-circle"
                            alt="User Image"
                            style="width: 25px; height: 25px; object-fit: cover; margin-top: -3px; margin-right: 5px;">
                        <span><?php echo htmlspecialchars($_SESSION['user_fullname']); ?></span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                        <a href="index.php?page=profile" class="dropdown-item">
                            <i class="fas fa-user-cog mr-2"></i> Profile
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="logout.php" class="dropdown-item">
                            <i class="fas fa-sign-out-alt mr-2"></i> Logout
                        </a>
                    </div>
                </li>
            </ul>
        </nav>
        <?php
        // Gọi file sidebar.php từ thư mục layouts
        require_once 'layouts/sidebar.php';
        ?>

        <div class="content-wrapper">
            <?php
            // 5. In nội dung của trang con đã được xử lý ở trên vào đúng vị trí
            echo $page_content;
            ?>
        </div>
        <?php
        // Gọi file footer.php từ thư mục layouts
        require_once 'layouts/footer.php';
        ?>

    </div>
    <script> 
        $(document).ready(function() { // các đoạn code jquery sử dụng trong trang con

            // --- LOGIC CHUYỂN ẢNH ---
            // Chỉ chạy khi có các phần tử thumbnail tồn tại trên trang
            if ($('.product-image-thumb').length) {
                $('.product-image-thumb').on('click', function() {
                    var image_element = $(this).find('img');
                    // Thay đổi ảnh chính
                    $('.product-image').prop('src', image_element.attr('src'));
                    // Cập nhật trạng thái active
                    $('.product-image-thumb.active').removeClass('active');
                    $(this).addClass('active');
                });
            }

            // --- LOGIC THAY ĐỔI GIÁ KHI CHỌN SIZE ---
            // Chỉ chạy khi có các nút chọn size tồn tại
            if ($('.btn-group[data-toggle="buttons"] label').length) {
                $('.btn-group[data-toggle="buttons"] label').on('click', function() {
                    // Lấy giá từ thuộc tính data-price
                    var newPrice = $(this).data('price');

                    if (newPrice !== undefined) {
                        // Định dạng lại giá theo kiểu tiền tệ Việt Nam
                        var formattedPrice = new Intl.NumberFormat('vi-VN').format(newPrice) + ' VNĐ';

                        // Cập nhật giá hiển thị
                        $('#product-price').text(formattedPrice);
                    }
                });
            }           
        });


    </script>
</body>

</html>