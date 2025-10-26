<?php
// BẮT ĐẦU PHẦN PHP: KẾT NỐI VÀ TRUY XUẤT DỮ LIỆU

session_start();

// 2. Kiểm tra xem người dùng đã đăng nhập chưa.
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    // 3. Nếu chưa đăng nhập, chuyển hướng đến trang đăng nhập.
    header("Location: login.php"); 
    exit; // Dừng việc thực thi script hiện tại.
}

// Bổ sung: Kiểm tra quyền (Authorization)
if (!isset($_SESSION['vai_tro']) || ($_SESSION['vai_tro'] !== 'admin' && $_SESSION['vai_tro'] !== 'nhan_vien')) {
     header("Location: unauthorized.php"); // Chuyển hướng đến trang không có quyền
     exit; 
}

// --- Cấu hình Database ---
$servername = "localhost"; 
$username = "root"; 
$password = ""; 
$dbname = "shop_thoi_trang"; 

// Tạo kết nối
$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
     die("Kết nối database thất bại: " . $conn->connect_error);
}

// Khởi tạo biến dữ liệu
$total_revenue = 0;
$total_customers = 0;
$total_orders = 0;
$total_employees = 0; // Biến này sẽ được dùng cho NHÂN VIÊN
$total_comments = 0;
$total_promotions = 0;

// --- 1. Truy vấn Tổng Khách Hàng (WHERE vai_tro = 'khach_hang') ---
$sql_customers = "SELECT COUNT(*) AS customer_count FROM nguoi_dung WHERE vai_tro = 'khach_hang'";
$result_customers = $conn->query($sql_customers);

if ($result_customers && $result_customers->num_rows > 0) {
    $row = $result_customers->fetch_assoc();
   $total_customers = number_format($row['customer_count']);
}

// --- 2. Truy vấn Tổng Nhân Viên (WHERE vai_tro = 'nhan_vien') ---
$sql_employees = "SELECT COUNT(*) AS employee_count FROM nguoi_dung WHERE vai_tro = 'nhan_vien'";
$result_employees = $conn->query($sql_employees);

if ($result_employees && $result_employees->num_rows > 0) {
   $row  = $result_employees->fetch_assoc();
   $total_employees = number_format($row['employee_count']);
}

// --- 3. Truy vấn Tổng Doanh thu ---
$sql_revenue = "SELECT SUM(tong_tien) AS total_amount FROM don_hang WHERE trang_thai = 'Đã hoàn thành'"; 
$result_revenue = $conn->query($sql_revenue);

if ($result_revenue && $result_revenue->num_rows > 0) {
 
    $row = $result_revenue->fetch_assoc();
    $total_revenue = number_format($row['total_amount']);
}

// --- 4. Truy vấn Tổng Đơn Hàng ---
$sql_orders = "SELECT COUNT(*) AS order_count FROM don_hang"; 
$result_orders = $conn->query($sql_orders);

if ($result_orders && $result_orders->num_rows > 0) {
 $row = $result_orders->fetch_assoc();
   $total_orders = number_format($row['order_count']);

}

// --- 5. Truy vấn Tổng Bình luận sản phẩm ---
$sql_comments = "SELECT COUNT(*) AS comment_count FROM binh_luan"; 
$result_comments = $conn->query($sql_comments);

if ($result_comments && $result_comments->num_rows > 0) {
 $row = $result_comments->fetch_assoc();
   $total_comments = number_format($row['comment_count']);

}
// --- 6. Truy vấn Tổng Khuyến mãi ---
$sql_promotions = "SELECT COUNT(*) AS promotion_count FROM khuyen_mai"; 
$result_promotions = $conn->query($sql_promotions);

if ($result_promotions && $result_promotions->num_rows > 0) {
 $row = $result_promotions->fetch_assoc();
   $total_promotions = number_format($row['promotion_count']);

}

// Đóng kết nối database
$conn->close();

// KẾT THÚC PHẦN PHP
?>

<!DOCTYPE html>
<html lang="vi">
<head>
      <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <meta name="description" content="Đăng nhập shop quần áo 160Store.">
     <meta name="keywords" content="đăng nhập 160s, đăng kí 160s">
      <title>Hệ Thống shop 160S</title>
      <link rel="stylesheet" href="typytrangchu.css"> 
       <link rel="stylesheet" href="login.php"> 
      <link rel="icon" href="logo160s.jpg" type="image/x-icon">
 
</head>
<body>

<div class="sidebar">
    <div class="user-profile">
         <img src="ảnh đại diện trang chủ..jpg" alt="Avatar" style="border-radius: 50%; margin-bottom: 10px;">
         <h4>Trần Vũ Phương Thùy </h4>
       <small>Quản trị viên</small>
        </div>

        <div class="menu">
         <a href="#" class="active">🏠 Trang chủ</a>
            <a href="#">👥 Người dùng</a>
              <a href="#">👕 Sản phẩm </a>
              <a href="#">🎁 Khuyến mãi </a>
             <a href="#">🧾 Đơn hàng </a>
 </div>
<a href="logout.php" class="logout-btn">🔴 Đăng xuất</a>
</div>
 <div class="main-content">
    <h1 class="welcome-header">Chào mừng đến với hệ thống shop 160S </h1>

    <div class="card-container">
        <div class="card card-revenue">
            <div class="card-title">Tổng doanh thu</div>
            <div class="card-value text-revenue"><?php echo $total_revenue; ?> VNĐ</div>
        </div>
        
                <div class="card card-customers"> 
            <div class="card-title">Tổng khách hàng</div>
            <div class="card-value text-customers"><?php echo $total_customers; ?></div>
        </div>
        
        <div class="card card-employees">
            <div class="card-title">Tổng nhân viên</div>
            <div class="card-value text-employees"><?php echo $total_employees; ?></div>
        </div>

                <div class="card card-orders">
            <div class="card-title">Tổng đơn hàng</div>
            <div class="card-value text-orders"><?php echo $total_orders; ?></div>
        </div>
        
                <div class="card card-comments">
            <div class="card-title">Tổng bình luận </div>
            <div class="card-value text-comments"><?php echo $total_comments; ?></div>
        </div>
         
                <div class="card card-promotions">
            <div class="card-title">Tổng khuyến mãi </div>
            <div class="card-value text-promotions"><?php echo $total_promotions; ?></div>
        </div>

 </div> <footer class="admin-footer-multi">
    <div class="footer-row">
        <div class="footer-col">
            <h4>Lưu Thị Kim Ngân </h4>
            <p> Nhóm Trưởng </p>
        </div>
        
        <div class="footer-col">
            <h4> Trần Vũ Phương Thùy</h4>
            <p> Thành Viên </p>
        </div>
        
        <div class="footer-col">
            <h4>Võ Đoàn Trọng Phú </h4>
            <p>Thành Viên </p>
        </div>

        <div class="footer-col">
            <h4> Thông tin liên hệ </h4>
            <p> Nhóm 8</p>
        </div>
    </div>
</footer>
</body>
</html>