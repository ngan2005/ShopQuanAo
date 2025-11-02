<?php
session_start();
require_once 'database.php';

if (!isset($_SESSION['user'])) {
    header("Location: dangNhap_DangKy.php");
    exit;
}
if ($_SESSION['user']['vai_Tro'] !== 'admin') {
    header("Location: TrangChu.php");
    exit;
}

// Lấy thông tin admin mới nhất
$db = new Database();
$conn = $db->connect();
$stmt = $conn->prepare("SELECT ho_Ten, email FROM nguoi_dung WHERE id_ND = ?");
$stmt->execute([$_SESSION['user']['id_ND']]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | 160STORE</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #00d4ff;
            --secondary: #00ff88;
            --danger: #ff4d4d;
            --warning: #ffaa00;
            --bg: #0a0a1a;
            --sidebar: rgba(20, 20, 40, 0.9);
            --card: rgba(30, 30, 50, 0.7);
            --text: #e0e0ff;
            --border: rgba(0, 212, 255, 0.2);
            --hover: rgba(0, 212, 255, 0.15);
            --shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            background: var(--bg);
            color: var(--text);
            font-family: 'Inter', sans-serif;
            display: flex;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* ===== SIDEBAR ===== */
        .sidebar {
            width: 260px;
            background: var(--sidebar);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            padding: 25px 20px;
            position: fixed;
            top: 0; left: 0; bottom: 0;
            border-right: 1px solid var(--border);
            z-index: 1000;
            transition: width 0.3s ease;
            overflow-y: auto;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 30px;
            padding: 0 10px;
        }

        .logo img {
            width: 45px;
            height: 45px;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(0, 212, 255, 0.4);
        }

        .logo h2 {
            font-size: 1.4rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin: 0;
        }

        .admin-info {
            text-align: center;
            margin-bottom: 30px;
            padding: 15px;
            background: rgba(255,255,255,0.05);
            border-radius: 16px;
            border: 1px solid var(--border);
        }

        .admin-avatar {
            width: 70px; height: 70px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            margin: 0 auto 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            color: white;
            box-shadow: 0 0 20px rgba(0, 212, 255, 0.5);
        }

        .admin-info h3 {
            font-size: 1.1rem;
            margin: 8px 0 4px;
            color: white;
        }

        .admin-info p {
            font-size: 0.85rem;
            color: #aaa;
        }

        .nav-menu {
            list-style: none;
        }

        .nav-item {
            margin-bottom: 6px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 14px 18px;
            color: #ccc;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 500;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .nav-link::before {
            content: '';
            position: absolute;
            left: 0; top: 0; bottom: 0;
            width: 4px;
            background: var(--primary);
            transform: scaleY(0);
            transition: transform 0.3s ease;
        }

        .nav-link:hover,
        .nav-link.active {
            background: var(--hover);
            color: var(--primary);
            transform: translateX(4px);
        }

        .nav-link.active::before {
            transform: scaleY(1);
        }

        .nav-link i {
            font-size: 1.1rem;
            width: 20px;
            text-align: center;
        }

        /* ===== MAIN CONTENT ===== */
        .main-content {
            margin-left: 260px;
            width: calc(100% - 260px);
            padding: 25px;
            transition: all 0.3s ease;
        }

        .topbar {
            background: var(--card);
            backdrop-filter: blur(10px);
            padding: 18px 25px;
            border-radius: 16px;
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
        }

        .page-title {
            font-size: 1.5rem;
            font-weight: 600;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .user-actions {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .notification-bell {
            position: relative;
            color: #aaa;
            font-size: 1.3rem;
            cursor: pointer;
            transition: 0.3s;
        }

        .notification-bell:hover { color: var(--primary); }

        .notification-badge {
            position: absolute;
            top: -6px; right: -6px;
            background: var(--danger);
            color: white;
            font-size: 0.65rem;
            width: 18px; height: 18px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }

        .logout-btn {
            background: linear-gradient(135deg, var(--danger), #ff6b6b);
            color: white;
            border: none;
            padding: 10px 18px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.9rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(255, 77, 77, 0.3);
        }

        .logout-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(255, 77, 77, 0.4);
        }

        /* ===== IFRAME ===== */
        #contentFrame {
            width: 100%;
            height: calc(100vh - 180px);
            border: none;
            background: var(--card);
            border-radius: 16px;
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            backdrop-filter: blur(10px);
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 992px) {
            .sidebar { width: 70px; }
            .sidebar .logo h2, .admin-info h3, .admin-info p, .nav-link span { display: none; }
            .nav-link { justify-content: center; padding: 16px; }
            .main-content { margin-left: 70px; width: calc(100% - 70px); }
        }

        @media (max-width: 576px) {
            .topbar { flex-direction: column; gap: 15px; text-align: center; }
            .user-actions { justify-content: center; }
        }

        /* ===== ANIMATION ===== */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .main-content > * {
            animation: fadeIn 0.5s ease-out;
        }
    </style>
</head>
<body>

<!-- ===== SIDEBAR ===== -->
<aside class="sidebar">
    <div class="logo">
        <img src="https://file.hstatic.net/1000253775/file/logo_no_bf-05_3e6797f31bda4002a22464d6f2787316.png" alt="160STORE">
        <h2>160STORE</h2>
    </div>

    <div class="admin-info">
        <div class="admin-avatar">
            <i class="fas fa-user-shield"></i>
        </div>
        <h3><?= htmlspecialchars($admin['ho_Ten']) ?></h3>
        <p><?= htmlspecialchars($admin['email']) ?></p>
    </div>

    <nav>
        <ul class="nav-menu">
            <li class="nav-item">
                <a href="partials/themSanPham.php" target="contentFrame" class="nav-link active">
                    <i class="fas fa-plus-circle"></i>
                    <span>Thêm sản phẩm</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="partials/quanLyDanhMuc.php" target="contentFrame" class="nav-link">
                    <i class="fas fa-folder-open"></i>
                    <span>Quản lý danh mục</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="partials/themMaGiamGia.php" target="contentFrame" class="nav-link">
                    <i class="fas fa-ticket-alt"></i>
                    <span>Mã giảm giá</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="partials/quanLyDonHang.php" target="contentFrame" class="nav-link">
                    <i class="fas fa-receipt"></i>
                    <span>Quản lý đơn hàng</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="partials/quanLyTaiKhoan.php" target="contentFrame" class="nav-link">
                    <i class="fas fa-users-cog"></i>
                    <span>Quản lý tài khoản</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="partials/quanLy_binh_luan.php" target="contentFrame" class="nav-link">
                    <i class="fas fa-comments"></i>
                    <span>Quản lý bình luận</span>
                </a>
        </ul>
    </nav>
</aside>

<!-- ===== MAIN CONTENT ===== -->
<main class="main-content">
    <div class="topbar">
        <h1 class="page-title">
            <i class="fas fa-tachometer-alt"></i> Bảng Điều Khiển
        </h1>
        <div class="user-actions">
            <div class="notification-bell">
                <i class="fas fa-bell"></i>
                <span class="notification-badge">3</span>
            </div>
            <button class="logout-btn" onclick="if(confirm('Đăng xuất ngay?')) window.location='logout.php'">
                <i class="fas fa-sign-out-alt"></i> Đăng xuất
            </button>
        </div>
    </div>

    <iframe id="contentFrame" name="contentFrame" src="partials/themSanPham.php"></iframe>
</main>

<script>
    // Active menu item
    document.querySelectorAll('.nav-link').forEach(link => {
        link.addEventListener('click', function() {
            document.querySelectorAll('.nav-link').forEach(el => el.classList.remove('active'));
            this.classList.add('active');
        });
    });

    // Responsive sidebar toggle (nếu cần)
    // Có thể thêm nút hamburger cho mobile sau
</script>

</body>
</html>