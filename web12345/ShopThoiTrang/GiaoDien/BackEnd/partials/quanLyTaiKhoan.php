<?php
require_once '../database.php';
session_start();

// Kiểm tra quyền admin
if (!isset($_SESSION['user']) || $_SESSION['user']['vai_Tro'] !== 'admin') {
    header("Location: ../dangNhap_DangKy.php");
    exit;
}

$db = new Database();
$conn = $db->connect();
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$msg = "";

// Xóa tài khoản
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    try {
        $stmt = $conn->prepare("DELETE FROM nguoi_dung WHERE id_ND=?");
        $stmt->execute([$id]);
        $msg = "<div class='msg success'><i class='fas fa-trash'></i> Đã xóa tài khoản ID <strong>$id</strong>!</div>";
    } catch (Exception $e) {
        $msg = "<div class='msg error'><i class='fas fa-times-circle'></i> Lỗi: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}

// Cập nhật vai trò
if (isset($_POST['update_role'])) {
    $id = intval($_POST['id_ND']);
    $role = in_array($_POST['vai_Tro'], ['admin', 'khach_hang']) ? $_POST['vai_Tro'] : 'khach_hang';
    try {
        $stmt = $conn->prepare("UPDATE nguoi_dung SET vai_Tro=? WHERE id_ND=?");
        $stmt->execute([$role, $id]);
        $msg = "<div class='msg success'><i class='fas fa-check-circle'></i> Cập nhật vai trò thành công!</div>";
    } catch (Exception $e) {
        $msg = "<div class='msg error'><i class='fas fa-bug'></i> Lỗi: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}

// Tìm kiếm
$search = trim($_GET['search'] ?? '');
$sql = "SELECT * FROM nguoi_dung 
        WHERE ten_Dang_Nhap LIKE :s 
           OR ho_Ten LIKE :s 
           OR email LIKE :s 
        ORDER BY ngay_Tao DESC";
$stmt = $conn->prepare($sql);
$stmt->execute(['s' => "%$search%"]);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Tài khoản | 160STORE Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #00d4ff;
            --secondary: #00ff88;
            --danger: #ff4d4d;
            --bg: #0a0a1a;
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
            padding: 20px;
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            animation: fadeIn 0.6s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }

        h2 {
            text-align: center;
            font-size: 1.9rem;
            margin: 20px 0 30px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-weight: 700;
        }

        /* ===== TÌM KIẾM ===== */
        .search-bar {
            display: flex;
            gap: 12px;
            margin-bottom: 25px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .search-input {
            flex: 1;
            min-width: 280px;
            padding: 14px 20px;
            background: rgba(255,255,255,0.08);
            border: 1.5px solid var(--border);
            border-radius: 12px;
            color: white;
            font-size: 1rem;
            transition: all 0.3s;
        }

        .search-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 15px rgba(0,212,255,0.3);
        }

        .btn-search, .btn-refresh {
            padding: 14px 20px;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
        }

        .btn-search {
            background: var(--primary);
            color: white;
        }

        .btn-search:hover {
            background: #00bfff;
            transform: translateY(-2px);
        }

        .btn-refresh {
            background: rgba(255,255,255,0.1);
            color: #aaa;
        }

        .btn-refresh:hover {
            background: rgba(255,255,255,0.2);
            color: white;
        }

        /* ===== BẢNG ===== */
        .table-container {
            background: var(--card);
            backdrop-filter: blur(15px);
            border-radius: 16px;
            overflow: hidden;
            border: 1.5px solid var(--border);
            box-shadow: var(--shadow);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: rgba(0,212,255,0.1);
            color: var(--primary);
            padding: 16px;
            text-align: left;
            font-weight: 600;
            font-size: 0.95rem;
        }

        td {
            padding: 14px 16px;
            border-bottom: 1px solid rgba(255,255,255,0.05);
            vertical-align: middle;
        }

        tr:hover {
            background: var(--hover);
        }

        .role-select {
            padding: 8px 12px;
            background: rgba(255,255,255,0.1);
            border: 1.5px solid var(--border);
            border-radius: 10px;
            color: white;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.3s;
        }

        .role-select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 10px rgba(0,212,255,0.3);
        }

        .role-admin {
            background: rgba(0,255,136,0.2);
            color: var(--secondary);
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .role-user {
            background: rgba(255,255,255,0.1);
            color: #aaa;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .actions {
            display: flex;
            gap: 10px;
        }

        .btn-delete {
            background: rgba(255,77,77,0.2);
            color: var(--danger);
            border: none;
            padding: 8px 14px;
            border-radius: 10px;
            font-size: 0.85rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s;
            font-weight: 600;
        }

        .btn-delete:hover {
            background: var(--danger);
            color: white;
        }

        /* ===== THÔNG BÁO ===== */
        .msg {
            padding: 14px 18px;
            border-radius: 12px;
            margin: 15px 0;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: slideDown 0.4s ease;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
        }

        .msg.success {
            background: rgba(0,255,136,0.15);
            color: var(--secondary);
            border: 1px solid rgba(0,255,136,0.3);
        }

        .msg.error {
            background: rgba(255,77,77,0.15);
            color: var(--danger);
            border: 1px solid rgba(255,77,77,0.3);
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 992px) {
            th, td { font-size: 0.85rem; padding: 10px; }
            .search-bar { flex-direction: column; }
            .search-input { min-width: 100%; }
            .actions { flex-direction: column; gap: 6px; }
        }

        @media (max-width: 600px) {
            table { font-size: 0.8rem; }
            .role-select { width: 100%; }
        }
    </style>
</head>
<body>

<div class="container">

    <h2>Quản lý Tài khoản Người dùng</h2>
    <?= $msg ?>

    <!-- TÌM KIẾM -->
    <form method="GET" class="search-bar">
        <input type="text" name="search" class="search-input" placeholder="Tìm kiếm tên, email, số điện thoại..." 
               value="<?= htmlspecialchars($search) ?>">
        <button type="submit" class="btn-search">
            <i class="fas fa-search"></i> Tìm kiếm
        </button>
        <a href="quanLyTaiKhoan.php" class="btn-refresh">
            <i class="fas fa-sync"></i> Làm mới
        </a>
    </form>

    <!-- BẢNG DANH SÁCH -->
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tên đăng nhập</th>
                    <th>Họ tên</th>
                    <th>Email</th>
                    <th>SĐT</th>
                    <th>Địa chỉ</th>
                    <th>Vai trò</th>
                    <th>Ngày tạo</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="9" style="text-align:center; color:#888; padding:30px;">
                            <i class="fas fa-inbox"></i> Không tìm thấy người dùng nào.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($users as $u): ?>
                        <tr>
                            <td><strong>#<?= $u['id_ND'] ?></strong></td>
                            <td><?= htmlspecialchars($u['ten_Dang_Nhap']) ?></td>
                            <td><?= htmlspecialchars($u['ho_Ten']) ?></td>
                            <td><?= htmlspecialchars($u['email']) ?></td>
                            <td><?= htmlspecialchars($u['sdt'] ?: '-') ?></td>
                            <td><?= htmlspecialchars($u['dia_Chi'] ?: '-') ?></td>
                            <td>
                                <form method="POST" style="margin:0;">
                                    <input type="hidden" name="id_ND" value="<?= $u['id_ND'] ?>">
                                    <select name="vai_Tro" class="role-select" onchange="this.form.submit()">
                                        <option value="khach_hang" <?= $u['vai_Tro'] === 'khach_hang' ? 'selected' : '' ?>>Khách hàng</option>
                                        <option value="admin" <?= $u['vai_Tro'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                                    </select>
                                    <input type="hidden" name="update_role" value="1">
                                </form>
                            </td>
                            <td><?= date('d/m/Y', strtotime($u['ngay_Tao'])) ?></td>
                            <td class="actions">
                                <button class="btn-delete" onclick="deleteUser(<?= $u['id_ND'] ?>)">
                                    <i class="fas fa-trash"></i> Xóa
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>

<script>
function deleteUser(id) {
    if (!confirm(`Xóa tài khoản ID #${id}?`)) return;
    loadContent(`quanLyTaiKhoan.php?delete=${id}`);
}

function loadContent(url) {
    fetch(url)
        .then(r => r.text())
        .then(html => {
            const frame = document.querySelector('#contentFrame')?.contentDocument || document.body;
            frame.innerHTML = html;
        })
        .catch(err => alert('Lỗi: ' + err.message));
}
</script>

</body>
</html>