<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '../database.php';

$db = new Database();
$conn = $db->connect();
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$msg = '';
$editing = false;
$product = [];
$variants = [];

/* === Lấy danh sách mã giảm giá === */
$discounts = [];
try {
    $stmt = $conn->query("SELECT ma_Giam_Gia, mo_Ta FROM ma_giam_gia ORDER BY ngay_Bat_Dau DESC");
    $discounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log("Lỗi khi tải mã giảm giá: " . $e->getMessage());
}

/* === Lấy danh mục === */
$categories = $conn->query("SELECT id_DM, ten_Danh_Muc FROM danh_muc ORDER BY id_DM ASC")->fetchAll(PDO::FETCH_ASSOC);

/* === TÌM KIẾM === */
$search = trim($_GET['search'] ?? '');
$searchQuery = $search !== '' ? "WHERE sp.id_SP LIKE ? OR sp.ten_San_Pham LIKE ?" : '';
$searchParam = $search !== '' ? "%$search%" : '';

/* === Nếu sửa === */
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $sql = "SELECT * FROM san_pham WHERE id_SP = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    $editing = true;

    $stmt = $conn->prepare("SELECT mau_Sac, kich_Thuoc FROM bien_the_san_pham WHERE id_SP = ?");
    $stmt->execute([$id]);
    $variants = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/* === Xóa sản phẩm === */
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    try {
        $conn->beginTransaction();
        $stmt = $conn->prepare("DELETE FROM bien_the_san_pham WHERE id_SP=?");
        $stmt->execute([$id]);
        if ($stmt->rowCount() === 0) {
            error_log("Không tìm thấy biến thể để xóa cho id_SP: $id");
        }

        $stmt = $conn->prepare("DELETE FROM san_pham WHERE id_SP=?");
        $stmt->execute([$id]);
        if ($stmt->rowCount() > 0) {
            $conn->commit();
            $msg = "<div class='msg success'>Đã xóa sản phẩm <strong>$id</strong>!</div>";
        } else {
            throw new Exception("Không tìm thấy sản phẩm để xóa.");
        }
    } catch (Exception $e) {
        $conn->rollBack();
        $msg = "<div class='msg error'>Lỗi: " . htmlspecialchars($e->getMessage()) . "</div>";
        error_log("Lỗi xóa sản phẩm: " . $e->getMessage());
    }
}

/* === Thêm hoặc Sửa === */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_SP = trim($_POST['id_SP'] ?? '');
    $ten_San_Pham = trim($_POST['ten_San_Pham'] ?? '');
    $gia_Ban = floatval($_POST['gia_Ban'] ?? 0);
    $gia_Goc = floatval($_POST['gia_Goc'] ?? 0);
    $mo_Ta = trim($_POST['mo_Ta'] ?? '');
    $id_DM = intval($_POST['id_DM'] ?? 0);
    $thuong_Hieu = trim($_POST['thuong_Hieu'] ?? '');
    $so_Luong_Ton = intval($_POST['so_Luong_Ton'] ?? 0);
    $trang_Thai = trim($_POST['trang_Thai'] ?? 'Còn hàng');
    $ma_Giam_Gia = trim($_POST['ma_Giam_Gia'] ?? '');
    $mau_Sac_arr = $_POST['mau_Sac'] ?? [];
    $kich_Thuoc_arr = $_POST['kich_Thuoc'] ?? [];
    $hinh_Anh = trim($_POST['link_hinh'] ?? '');

    // Xử lý upload file
    if (isset($_FILES['file_hinh']) && $_FILES['file_hinh']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../../uploads/';
        if (!is_dir($uploadDir) && !mkdir($uploadDir, 0777, true)) {
            error_log("Không thể tạo thư mục uploads: $uploadDir");
        }
        $fileName = basename($_FILES['file_hinh']['name']);
        $targetPath = $uploadDir . $fileName;
        if (move_uploaded_file($_FILES['file_hinh']['tmp_name'], $targetPath)) {
            $hinh_Anh = "uploads/" . $fileName;
        } else {
            error_log("Lỗi upload file: " . $targetPath);
        }
    }

    $errors = [];
    if ($id_SP === '') $errors[] = "Mã sản phẩm không được để trống.";
    if ($ten_San_Pham === '') $errors[] = "Tên sản phẩm không được để trống.";
    if ($gia_Ban <= 0) $errors[] = "Giá bán phải lớn hơn 0.";
    if ($id_DM <= 0) $errors[] = "Vui lòng chọn danh mục hợp lệ.";

    // Kiểm tra trùng id_SP khi thêm
    if (isset($_POST['them'])) {
        $stmt = $conn->prepare("SELECT COUNT(*) FROM san_pham WHERE id_SP = ?");
        $stmt->execute([$id_SP]);
        if ($stmt->fetchColumn() > 0) {
            $errors[] = "Mã sản phẩm $id_SP đã tồn tại.";
        }
    }

    if (!empty($errors)) {
        $msg = "<div class='msg error'><ul><li>" . implode("</li><li>", $errors) . "</li></ul></div>";
    } else {
        try {
            $conn->beginTransaction();

            if (isset($_POST['them'])) {
                $stmt = $conn->prepare("INSERT INTO san_pham 
                    (id_SP, ten_San_Pham, gia_Ban, gia_Goc, mo_Ta, hinh_Anh, id_DM, thuong_Hieu, so_Luong_Ton, trang_Thai, ngay_Tao, ngay_Cap_Nhat, ma_Giam_Gia)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW(), ?)");
                $stmt->execute([
                    $id_SP, $ten_San_Pham, $gia_Ban, $gia_Goc, $mo_Ta, $hinh_Anh,
                    $id_DM, $thuong_Hieu, $so_Luong_Ton, $trang_Thai,
                    $ma_Giam_Gia !== '' ? $ma_Giam_Gia : null
                ]);
            } elseif (isset($_POST['sua'])) {
                $stmt = $conn->prepare("UPDATE san_pham 
                    SET ten_San_Pham=?, gia_Ban=?, gia_Goc=?, mo_Ta=?, hinh_Anh=?, id_DM=?, thuong_Hieu=?, so_Luong_Ton=?, trang_Thai=?, ma_Giam_Gia=?, ngay_Cap_Nhat=NOW()
                    WHERE id_SP=?");
                $stmt->execute([
                    $ten_San_Pham, $gia_Ban, $gia_Goc, $mo_Ta, $hinh_Anh,
                    $id_DM, $thuong_Hieu, $so_Luong_Ton, $trang_Thai,
                    $ma_Giam_Gia !== '' ? $ma_Giam_Gia : null, $id_SP
                ]);

                $conn->prepare("DELETE FROM bien_the_san_pham WHERE id_SP=?")->execute([$id_SP]);
            }

            foreach ($mau_Sac_arr as $i => $mau) {
                $mau = trim($mau);
                $kich = trim($kich_Thuoc_arr[$i] ?? '');
                if ($mau !== '' || $kich !== '') {
                    $stmt = $conn->prepare("INSERT INTO bien_the_san_pham (id_SP, mau_Sac, kich_Thuoc) VALUES (?, ?, ?)");
                    $stmt->execute([$id_SP, $mau ?: null, $kich ?: null]);
                }
            }

            $conn->commit();
            $msg = "<div class='msg success'>Lưu sản phẩm thành công!</div>";
            $editing = false;
        } catch (Exception $e) {
            $conn->rollBack();
            $msg = "<div class='msg error'>Lỗi: " . htmlspecialchars($e->getMessage()) . "</div>";
            error_log("Lỗi giao dịch: " . $e->getMessage());
        }
    }
}

/* === Danh sách sản phẩm (có tìm kiếm) === */
$sql = "SELECT sp.*, dm.ten_Danh_Muc FROM san_pham sp 
        LEFT JOIN danh_muc dm ON sp.id_DM = dm.id_DM
        $searchQuery
        ORDER BY sp.ngay_Tao DESC";

$stmt = $conn->prepare($sql);
if ($search !== '') {
    $stmt->execute([$searchParam, $searchParam]);
} else {
    $stmt->execute();
}
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Sản phẩm | 160STORE Admin</title>
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
            --border: rgba(179, 255, 0, 0.2);
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

        /* ===== TÌM KIẾM MỚI (GIỮ MÀU CŨ) ===== */
        .search-bar {
            margin-bottom: 25px;
            display: flex;
            gap: 12px;
            align-items: center;
            background: var(--card);
            padding: 14px 18px;
            border-radius: 16px;
            border: 1.5px solid var(--border);
            max-width: 600px;
            backdrop-filter: blur(10px);
        }

        .search-bar input {
            flex: 1;
            background: transparent;
            border: none;
            color: white;
            font-size: 1rem;
            outline: none;
        }

        .search-bar input::placeholder {
            color: #888;
        }

        .search-bar button {
            background: linear-gradient(135deg, var(--secondary), var(--primary));
            color: white;
            border: none;
            width: 44px;
            height: 44px;
            border-radius: 12px;
            cursor: pointer;
            font-size: 1.1rem;
            transition: 0.3s;
        }

        .search-bar button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(64, 255, 0, 0.3);
        }

        .search-bar .clear-btn {
            background: transparent;
            color: #aaa;
            font-size: 1rem;
            cursor: pointer;
        }

        .search-bar .clear-btn:hover {
            color: var(--danger);
        }

        /* ===== FORM CARD (GIỮ NGUYÊN) ===== */
        .form-card {
            background: var(--card);
            backdrop-filter: blur(15px);
            border-radius: 16px;
            padding: 28px;
            margin-bottom: 30px;
            border: 1.5px solid var(--border);
            box-shadow: var(--shadow);
        }

        .form-title {
            font-size: 1.35rem;
            margin-bottom: 22px;
            color: var(--secondary);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group.full { grid-column: span 2; }

        .form-group label {
            margin-bottom: 8px;
            font-weight: 500;
            color: #ccccccff;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            padding: 14px 16px;
            background: rgba(255,255,255,0.08);
            border: 1.5px solid var(--border);
            border-radius: 12px;
            color: white;
            font-size: 1rem;
            transition: all 0.3s;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 15px rgba(157, 255, 0, 0.3);
        }

        .variant-row {
            display: flex;
            gap: 10px;
            margin-bottom: 10px;
            align-items: center;
        }

        .variant-row input { flex: 1; }

        .btn-remove {
            background: var(--danger);
            color: white;
            border: none;
            width: 36px; height: 36px;
            border-radius: 10px;
            cursor: pointer;
            font-size: 0.9rem;
        }

        .btn-remove:hover {
            background: #ff6b6b;
        }

        .btn-add-variant {
            background: rgba(255,255,255,0.1);
            color: #aaa;
            border: none;
            padding: 10px 16px;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 10px;
        }

        .btn-add-variant:hover {
            background: var(--hover);
            color: white;
        }

        .form-actions {
            display: flex;
            gap: 12px;
            margin-top: 25px;
            grid-column: span 2;
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
            flex: 1;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--secondary), var(--primary));
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(145, 255, 0, 0.3);
        }

        .btn-cancel {
            background: rgba(255,255,255,0.1);
            color: #aaa;
        }

        .btn-cancel:hover {
            background: var(--danger);
            color: white;
        }

        /* ===== TABLE (GIỮ NGUYÊN) ===== */
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
            background: rgba(246, 255, 0, 0.1);
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

        .product-thumb {
            width: 60px; height: 60px;
            object-fit: cover;
            border-radius: 10px;
            border: 2px solid var(--border);
        }

        .actions {
            display: flex;
            gap: 8px;
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

        .msg {
            padding: 14px 18px;
            border-radius: 12px;
            margin: 15px 0;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: slideDown 0.4s ease;
            max-width: 800px;
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

        @media (max-width: 992px) {
            .form-grid { grid-template-columns: 1fr; }
            .search-bar { max-width: 100%; }
        }
    </style>
</head>
<body>

<div class="container">

    <h2>Quản lý Sản phẩm</h2>

    <!-- TÌM KIẾM MỚI -->
    <div class="search-bar">
        <form method="GET" style="display:flex; width:100%; gap:10px;">
            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Tìm theo mã hoặc tên sản phẩm..." autofocus>
            <button type="submit"><i class="fas fa-search"></i></button>
            <?php if ($search !== ''): ?>
                <a href="themSanPham.php" class="clear-btn"><i class="fas fa-times"></i></a>
            <?php endif; ?>
        </form>
    </div>

    <?= $msg ?>

    <!-- FORM THÊM/SỬA (GIỮ NGUYÊN) -->
    <div class="form-card">
        <h3 class="form-title">
            <?= $editing ? 'Sửa sản phẩm' : 'Thêm sản phẩm mới' ?>
        </h3>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-grid">
                <div class="form-group">
                    <label>Mã sản phẩm *</label>
                    <input type="text" name="id_SP" value="<?= htmlspecialchars($product['id_SP'] ?? '') ?>" 
                           <?= $editing ? 'readonly' : 'required' ?> placeholder="VD: SP001">
                </div>

                <div class="form-group">
                    <label>Tên sản phẩm *</label>
                    <input type="text" name="ten_San_Pham" value="<?= htmlspecialchars($product['ten_San_Pham'] ?? '') ?>" required placeholder="Áo thun nam cổ tròn">
                </div>

                <div class="form-group">
                    <label>Giá bán (VNĐ) *</label>
                    <input type="number" name="gia_Ban" value="<?= htmlspecialchars($product['gia_Ban'] ?? '') ?>" required placeholder="199000">
                </div>

                <div class="form-group">
                    <label>Giá gốc (VNĐ)</label>
                    <input type="number" name="gia_Goc" value="<?= htmlspecialchars($product['gia_Goc'] ?? '') ?>" placeholder="299000">
                </div>

                <div class="form-group full">
                    <label>Mô tả sản phẩm</label>
                    <textarea name="mo_Ta" rows="3" placeholder="Chất liệu cotton, thoáng mát..."><?= htmlspecialchars($product['mo_Ta'] ?? '') ?></textarea>
                </div>

                <div class="form-group full">
                    <label>Hình ảnh sản phẩm</label>
                    <input type="text" name="link_hinh" placeholder="nhập link ảnh (uploads/ hoặc https://...)" 
                           value="<?= htmlspecialchars($product['hinh_Anh'] ?? '') ?>">
                    <?php if ($editing && !empty($product['hinh_Anh'])): ?>
                        <small style="color:#aaa;">Hiện tại: <a href="<?= htmlspecialchars($product['hinh_Anh']) ?>" target="_blank">Xem ảnh</a></small>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label>Danh mục *</label>
                    <select name="id_DM" required>
                        <option value="">-- Chọn danh mục --</option>
                        <?php foreach($categories as $c): ?>
                            <option value="<?= $c['id_DM'] ?>" <?= ($product['id_DM'] ?? '') == $c['id_DM'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($c['ten_Danh_Muc']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Thương hiệu</label>
                    <input type="text" name="thuong_Hieu" value="<?= htmlspecialchars($product['thuong_Hieu'] ?? '') ?>" placeholder="160STORE">
                </div>

                <div class="form-group">
                    <label>Số lượng tồn</label>
                    <input type="number" name="so_Luong_Ton" value="<?= htmlspecialchars($product['so_Luong_Ton'] ?? 0) ?>" placeholder="100">
                </div>

                <div class="form-group">
                    <label>Mã giảm giá</label>
                    <select name="ma_Giam_Gia">
                        <option value="">-- Không áp dụng --</option>
                        <?php foreach ($discounts as $d): ?>
                            <option value="<?= htmlspecialchars($d['ma_Giam_Gia']) ?>" 
                                <?= ($product['ma_Giam_Gia'] ?? '') == $d['ma_Giam_Gia'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($d['ma_Giam_Gia']) ?> - <?= htmlspecialchars($d['mo_Ta']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group full">
                    <label>Biến thể sản phẩm (Màu + Size)</label>
                    <div id="variants-container">
                        <?php if (!empty($variants)): ?>
                            <?php foreach ($variants as $v): ?>
                                <div class="variant-row">
                                    <input type="text" name="mau_Sac[]" value="<?= htmlspecialchars($v['mau_Sac']) ?>" placeholder="Màu sắc (VD: Đen)">
                                    <input type="text" name="kich_Thuoc[]" value="<?= htmlspecialchars($v['kich_Thuoc']) ?>" placeholder="Kích thước (VD: M)">
                                    <button type="button" class="btn-remove" onclick="this.parentElement.remove()">Xóa</button>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="variant-row">
                                <input type="text" name="mau_Sac[]" placeholder="Màu sắc">
                                <input type="text" name="kich_Thuoc[]" placeholder="Kích thước">
                                <button type="button" class="btn-remove" onclick="this.parentElement.remove()">Xóa</button>
                            </div>
                        <?php endif; ?>
                    </div>
                    <button type="button" class="btn-add-variant" onclick="addVariant()">
                        Thêm biến thể
                    </button>
                </div>

                <div class="form-actions">
                    <?php if ($editing): ?>
                        <button type="submit" name="sua" class="btn btn-primary">
                            Cập nhật
                        </button>
                        <a href="themSanPham.php" class="btn btn-cancel">
                            Hủy
                        </a>
                    <?php else: ?>
                        <button type="submit" name="them" class="btn btn-primary">
                            Thêm sản phẩm
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </form>
    </div>

    <!-- BẢNG DANH SÁCH -->
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Mã SP</th>
                    <th>Tên sản phẩm</th>
                    <th>Ảnh</th>
                    <th>Giá bán</th>
                    <th>Tồn</th>
                    <th>Danh mục</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($products)): ?>
                    <tr>
                        <td colspan="7" style="text-align:center; color:#888; padding:30px;">
                            Không tìm thấy sản phẩm nào.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach($products as $p): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($p['id_SP']) ?></strong></td>
                            <td><?= htmlspecialchars($p['ten_San_Pham']) ?></td>
                            <td>
                                <?php if (!empty($p['hinh_Anh'])): ?>
                                    <img src="<?= htmlspecialchars($p['hinh_Anh']) ?>" class="product-thumb" alt="">
                                <?php else: ?>
                                    <div style="width:60px;height:60px;background:#444;border-radius:10px;"></div>
                                <?php endif; ?>
                            </td>
                            <td><?= number_format($p['gia_Ban'], 0, ',', '.') ?>đ</td>
                            <td><?= $p['so_Luong_Ton'] ?></td>
                            <td><?= htmlspecialchars($p['ten_Danh_Muc'] ?? '-') ?></td>
                            <td class="actions">
                                <button class="btn-edit" onclick="editProduct('<?= urlencode($p['id_SP']) ?>')">
                                    Sửa
                                </button>
                                <button class="btn-delete" onclick="deleteProduct('<?= urlencode($p['id_SP']) ?>')">
                                    Xóa
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
function addVariant() {
    const container = document.getElementById('variants-container');
    const div = document.createElement('div');
    div.className = 'variant-row';
    div.innerHTML = `
        <input type="text" name="mau_Sac[]" placeholder="Màu sắc">
        <input type="text" name="kich_Thuoc[]" placeholder="Kích thước">
        <button type="button" class="btn-remove" onclick="this.parentElement.remove()">Xóa</button>
    `;
    container.appendChild(div);
}

function editProduct(id) {
    window.location.href = `themSanPham.php?edit=${encodeURIComponent(id)}&search=<?= urlencode($search) ?>`;
}

function deleteProduct(id) {
    if (!confirm(`Xóa sản phẩm ${id}?`)) return;
    window.location.href = `themSanPham.php?delete=${encodeURIComponent(id)}&search=<?= urlencode($search) ?>`;
}
</script>

</body>
</html>