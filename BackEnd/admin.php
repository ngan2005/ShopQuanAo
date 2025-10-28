<?php
session_start();
require_once 'database.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user'])) {
    header("Location: dangNhap_DangKy.php");
    exit;
}

// Kiểm tra quyền admin
if ($_SESSION['user']['vai_Tro'] !== 'admin') {
    header("Location: TrangChu.php"); 
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản trị - 160STORE</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body { margin: 0; font-family: Arial; background: #111; color: #fff; }
        .container { display: flex; min-height: 100vh; }
        .sidebar {
            width: 250px;
            background: #1a1a1a;
            padding: 20px;
        }
        .content {
            flex: 1;
            padding: 20px;
            background: #222;
        }
        .nav-btn {
            display: block;
            width: 100%;
            padding: 12px 15px;
            margin: 5px 0;
            border: none;
            background: #333;
            color: #fff;
            text-align: left;
            border-radius: 6px;
            cursor: pointer;
        }
        .nav-btn:hover {
            background: #444;
        }
        .nav-btn i {
            margin-right: 8px;
        }
        .logout {
            display: block;
            margin-top: 20px;
            padding: 12px;
            color: #ff4757;
            text-align: center;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <h3>👋 Xin chào Admin</h3>
            <p><?= htmlspecialchars($_SESSION['user']['ho_Ten']) ?></p>

            <button class="nav-btn" onclick="loadContent('themSanPham')">
                <i class="fas fa-plus-circle"></i> Thêm sản phẩm
            </button>
            
            <button class="nav-btn" onclick="loadContent('quanLyDanhMuc')">
                <i class="fas fa-folder"></i> Quản lý danh mục
            </button>
            
            <button class="nav-btn" onclick="loadContent('themMaGiamGia')">
                <i class="fas fa-ticket-alt"></i> Mã giảm giá
            </button>

            <a href="logout.php" class="logout">
                <i class="fas fa-sign-out-alt"></i> Đăng xuất
            </a>
        </div>

        <div id="mainContent" class="content">
            <!-- Nội dung sẽ được load vào đây -->
            <h2>Chọn chức năng từ menu bên trái</h2>
        </div>
    </div>
    <script>
    function loadContent(page) {
        const mainContent = document.getElementById('mainContent');
        mainContent.innerHTML = '<div style="text-align:center">Đang tải...</div>';

        fetch(`partials/${page}.php`)
            .then(response => response.text())
            .then(html => {
                mainContent.innerHTML = html;
            })
            .catch(error => {
                mainContent.innerHTML = `
                    <div style="color:red;text-align:center">
                        <p>Lỗi: ${error.message}</p>
                        <button onclick="loadContent('${page}')">Thử lại</button>
                    </div>`;
            });
    }
    </script>
</body>
</html>
