<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../database.php';
$db = new Database();
$conn = $db->connect();

$msg = '';
$editRow = null;

/* ================================
   XÓA DANH MỤC
   ================================ */
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    try {
        $stmt = $conn->prepare("DELETE FROM danh_muc WHERE id_DM = ?");
        $stmt->execute([$id]);
        $msg = "<div class='msg success'><i class='fas fa-trash'></i> Đã xóa danh mục ID <strong>$id</strong>!</div>";
    } catch (Exception $e) {
        $msg = "<div class='msg error'><i class='fas fa-times-circle'></i> Lỗi: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}

/* ================================
   LẤY DỮ LIỆU KHI SỬA
   ================================ */
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $stmt = $conn->prepare("SELECT * FROM danh_muc WHERE id_DM = ?");
    $stmt->execute([$id]);
    $editRow = $stmt->fetch(PDO::FETCH_ASSOC);
}

/* ================================
   THÊM HOẶC CẬP NHẬT DANH MỤC
   ================================ */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ten = trim($_POST['ten_Danh_Muc'] ?? '');
    $id_DM = intval($_POST['id_DM'] ?? 0);

    if ($ten === '') {
        $msg = "<div class='msg error'><i class='fas fa-exclamation-triangle'></i> Tên danh mục không được để trống!</div>";
    } else {
        try {
            if (isset($_POST['sua_danh_muc'])) {
                $stmt = $conn->prepare("UPDATE danh_muc SET ten_Danh_Muc = ? WHERE id_DM = ?");
                $stmt->execute([$ten, $id_DM]);
                $msg = "<div class='msg success'><i class='fas fa-save'></i> Cập nhật danh mục thành công!</div>";
            } else {
                $stmt = $conn->prepare("INSERT INTO danh_muc (ten_Danh_Muc) VALUES (?)");
                $stmt->execute([$ten]);
                $msg = "<div class='msg success'><i class='fas fa-plus-circle'></i> Đã thêm danh mục <strong>" . htmlspecialchars($ten) . "</strong>!</div>";
            }
        } catch (Exception $e) {
            $msg = "<div class='msg error'><i class='fas fa-bug'></i> Lỗi SQL: " . htmlspecialchars($e->getMessage()) . "</div>";
        }
    }
}

/* ================================
   LẤY DANH SÁCH DANH MỤC
   ================================ */
$stmt = $conn->query("SELECT * FROM danh_muc ORDER BY id_DM ASC");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Danh mục | 160STORE Admin</title>
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
            max-width: 900px;
            margin: 0 auto;
            animation: fadeIn 0.6s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }

        h2 {
            text-align: center;
            font-size: 1.8rem;
            margin: 20px 0 30px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-weight: 700;
        }

        /* ===== FORM CARD ===== */
        .form-card {
            background: var(--card);
            backdrop-filter: blur(15px);
            border-radius: 16px;
            padding: 25px;
            margin-bottom: 30px;
            border: 1.5px solid var(--border);
            box-shadow: var(--shadow);
        }

        .form-title {
            font-size: 1.3rem;
            margin-bottom: 20px;
            color: var(--secondary);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-row {
            display: flex;
            gap: 12px;
            align-items: center;
            flex-wrap: wrap;
        }

        .form-input {
            flex: 1;
            min-width: 280px;
            padding: 14px 18px;
            background: rgba(255,255,255,0.08);
            border: 1.5px solid var(--border);
            border-radius: 12px;
            color: white;
            font-size: 1rem;
            transition: all 0.3s;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 15px rgba(0,212,255,0.3);
        }

        .form-input[readonly] {
            background: rgba(255,255,255,0.05);
            color: #888;
            cursor: not-allowed;
        }

        .btn {
            padding: 14px 20px;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.3s;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--secondary), var(--primary));
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0,212,255,0.3);
        }

        .btn-cancel {
            background: rgba(255,255,255,0.1);
            color: #aaa;
        }

        .btn-cancel:hover {
            background: var(--danger);
            color: white;
        }

        /* ===== TABLE ===== */
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
        }

        tr:hover {
            background: var(--hover);
        }

        .actions {
            display: flex;
            gap: 10px;
        }

        .btn-edit, .btn-delete {
            padding: 8px 14px;
            border: none;
            border-radius: 10px;
            font-size: 0.85rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s;
            font-weight: 600;
        }

        .btn-edit {
            background: rgba(0,212,255,0.2);
            color: var(--primary);
        }

        .btn-edit:hover {
            background: var(--primary);
            color: white;
        }

        .btn-delete {
            background: rgba(255,77,77,0.2);
            color: var(--danger);
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
        @media (max-width: 768px) {
            .form-row { flex-direction: column; align-items: stretch; }
            .form-input { min-width: 100%; }
            .btn { width: 100%; }
            th, td { font-size: 0.85rem; padding: 10px; }
            .actions { flex-direction: column; gap: 6px; }
        }
    </style>
</head>
<body>

<div class="container">

    <h2>Quản lý Danh mục Sản phẩm</h2>
    <?= $msg ?>

    <!-- FORM THÊM / SỬA -->
    <div class="form-card">
        <h3 class="form-title">
            <?= $editRow ? '<i class="fas fa-edit"></i> Sửa danh mục' : '<i class="fas fa-plus-circle"></i> Thêm danh mục mới' ?>
        </h3>
        <form method="POST" class="form-row">
            <?php if ($editRow): ?>
                <input type="hidden" name="id_DM" value="<?= htmlspecialchars($editRow['id_DM']) ?>">
            <?php endif; ?>

            <input type="text" name="ten_Danh_Muc" class="form-input" 
                   placeholder="Nhập tên danh mục (VD: Áo thun, Quần jeans...)"
                   value="<?= htmlspecialchars($editRow['ten_Danh_Muc'] ?? '') ?>" required>

            <?php if ($editRow): ?>
                <button type="submit" name="sua_danh_muc" class="btn btn-primary">
                    <i class="fas fa-save"></i> Cập nhật
                </button>
                <button type="button" class="btn btn-cancel" onclick="loadContent('quanLyDanhMuc.php')">
                    <i class="fas fa-times"></i> Hủy
                </button>
            <?php else: ?>
                <button type="submit" name="them_danh_muc" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Thêm mới
                </button>
            <?php endif; ?>
        </form>
    </div>

    <!-- BẢNG DANH SÁCH -->
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th width="80">ID</th>
                    <th>Tên danh mục</th>
                    <th width="140">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($rows)): ?>
                    <tr>
                        <td colspan="3" style="text-align:center; color:#888; padding:30px;">
                            <i class="fas fa-inbox"></i> Chưa có danh mục nào.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($rows as $r): ?>
                        <tr>
                            <td><strong>#<?= $r['id_DM'] ?></strong></td>
                            <td><?= htmlspecialchars($r['ten_Danh_Muc']) ?></td>
                            <td class="actions">
                                <button class="btn-edit" onclick="editDm(<?= $r['id_DM'] ?>)">
                                    <i class="fas fa-edit"></i> Sửa
                                </button>
                                <button class="btn-delete" onclick="deleteDm(<?= $r['id_DM'] ?>)">
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
function loadContent(url) {
    fetch(url)
        .then(r => r.text())
        .then(html => {
            const main = document.querySelector('#contentFrame')?.contentDocument || document.body;
            main.innerHTML = html;
        })
        .catch(err => alert('Lỗi tải: ' + err.message));
}

function editDm(id) {
    loadContent('../partials/quanLyDanhMuc.php?edit=' + id);
    return false;
}

function deleteDm(id) {
    if (!confirm(`Xóa danh mục ID #${id}?`)) return false;
    loadContent('../partials/quanLyDanhMuc.php?delete=' + id);
    return false;
}
</script>

</body>
</html>