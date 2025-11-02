<?php
session_start();
require_once 'database.php'; // đường dẫn đến file kết nối DB

$db = new Database();
$conn = $db->connect();
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$id_ND = $_SESSION['user']['id_ND'] ?? 1; // tạm lấy người dùng 1 nếu chưa đăng nhập
$msg = '';

/* ===== 1. Lấy giỏ hàng hiện tại ===== */
$stmt = $conn->prepare("SELECT id_GH FROM gio_hang WHERE id_ND = ?");
$stmt->execute([$id_ND]);
$gioHang = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$gioHang) {
    $conn->prepare("INSERT INTO gio_hang (id_ND, ngay_Tao) VALUES (?, NOW())")->execute([$id_ND]);
    $id_GH = $conn->lastInsertId();
} else {
    $id_GH = $gioHang['id_GH'];
}

/* ===== 2. Xử lý thêm sản phẩm ===== */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['them_vao_gio'])) {
    $id_SP = $_POST['id_SP'];
    $so_Luong = (int)$_POST['so_Luong'];
    $ma_Giam_Gia = trim($_POST['ma_Giam_Gia'] ?? ''); // Thêm mã giảm giá từ form
    
    try {
        // Kiểm tra mã giảm giá nếu có
        if ($ma_Giam_Gia) {
            $stmt = $conn->prepare("SELECT * FROM ma_giam_gia WHERE ma_Giam_Gia = ? AND ngay_Het_Han >= CURDATE()");
            $stmt->execute([$ma_Giam_Gia]);
            if (!$stmt->fetch()) {
                throw new Exception("Mã giảm giá không hợp lệ hoặc đã hết hạn");
            }
        }

        // Kiểm tra tồn kho
        $stmt = $conn->prepare("SELECT so_Luong_Ton FROM san_pham WHERE id_SP = ?");
        $stmt->execute([$id_SP]);
        $sp = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$sp) {
            throw new Exception("Sản phẩm không tồn tại");
        }

        if ($sp['so_Luong_Ton'] < $so_Luong) {
            throw new Exception("Số lượng trong kho không đủ");
        }

        // Kiểm tra và cập nhật giỏ hàng
        $stmt = $conn->prepare("SELECT so_Luong FROM chi_tiet_gio_hang WHERE id_GH = ? AND id_SP = ?");
        $stmt->execute([$id_GH, $id_SP]);
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existing) {
            // Cập nhật số lượng và mã giảm giá nếu đã có
            $stmt = $conn->prepare("UPDATE chi_tiet_gio_hang 
                                  SET so_Luong = so_Luong + ?, ma_Giam_Gia = ? 
                                  WHERE id_GH = ? AND id_SP = ?");
            $stmt->execute([$so_Luong, $ma_Giam_Gia, $id_GH, $id_SP]);
        } else {
            // Thêm mới với mã giảm giá
            $stmt = $conn->prepare("INSERT INTO chi_tiet_gio_hang (id_GH, id_SP, so_Luong, ma_Giam_Gia) 
                                  VALUES (?, ?, ?, ?)");
            $stmt->execute([$id_GH, $id_SP, $so_Luong, $ma_Giam_Gia]);
        }

        $msg = "Đã thêm sản phẩm vào giỏ hàng" . ($ma_Giam_Gia ? " với mã giảm giá" : "");
    } catch (Exception $e) {
        $msg = "Lỗi: " . $e->getMessage();
    }
}

/* ===== 3. Hiển thị giỏ hàng ===== */
$cart_items = [];
try {
$sql = "SELECT ghct.*, sp.hinh_Anh 
        FROM gio_hang_chi_tiet ghct
        JOIN san_pham sp ON ghct.id_SP = sp.id_SP
        WHERE ghct.id_GH = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$id_GH]);
$cart = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* ===== 7. Tính tổng tiền ===== */
$total = 0;
foreach ($cart as $item) $total += $item['thanh_Tien'];

if (isset($_SESSION['ma_Giam_Gia'])) {
    $mgg = $conn->prepare("SELECT * FROM ma_giam_gia WHERE ma_Giam_Gia = ?");
    $mgg->execute([$_SESSION['ma_Giam_Gia']]);
    $d = $mgg->fetch(PDO::FETCH_ASSOC);
    if ($d) {
        if ($d['loai_Giam'] == 'phan_tram') {
            $total = $total * (1 - $d['gia_Tri_Giam'] / 100);
        } else {
            $total = $total - $d['gia_Tri_Giam'];
        }
    }
}
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>🛒 Giỏ hàng</title>
<style>
body { font-family: Arial; background:#1a1a1a; color:#fff; }
table { width:100%; border-collapse:collapse; background:#222; margin-top:20px; }
th,td { padding:10px; text-align:center; border-bottom:1px solid #333; }
th { background:#333; color:#00ff88; }
img { width:60px; border-radius:6px; }
button,a.btn { padding:8px 12px; border:none; border-radius:6px; font-weight:bold; cursor:pointer; }
.add { background:#00bfff; color:#111; }
.del { background:#ff5555; color:#fff; }
.msg.success{background:#00ff8820;color:#00ff88;padding:8px;margin:8px 0;border-radius:8px;}
.msg.error{background:#ff555520;color:#ff5555;padding:8px;margin:8px 0;border-radius:8px;}
.total { text-align:right; font-size:18px; margin-top:10px; }
form.inline{display:inline;}
</style>
</head>
<body>
<h2 align="center">🛒 Giỏ hàng của bạn</h2>
<?= $msg ?>

<table>
<tr>
  <th>Ảnh</th><th>Tên SP</th><th>Màu</th><th>Size</th><th>Số lượng</th><th>Đơn giá</th><th>Thành tiền</th><th>Hành động</th>
</tr>
<?php foreach($cart as $item): ?>
<tr>
  <td><img src="<?= htmlspecialchars($item['hinh_Anh']) ?>"></td>
  <td><?= htmlspecialchars($item['ten_san_pham']) ?></td>
  <td><?= htmlspecialchars($item['mau_sac'] ?? '-') ?></td>
  <td><?= htmlspecialchars($item['kich_Thuoc'] ?? '-') ?></td>
  <td>
    <form method="POST" class="inline">
      <input type="hidden" name="id_GHCT" value="<?= $item['id_GHCT'] ?>">
      <input type="number" name="so_Luong" value="<?= $item['so_Luong'] ?>" min="1" style="width:60px;">
      <button name="capnhat" class="add">Cập nhật</button>
    </form>
  </td>
  <td><?= number_format($item['don_Gia'],0,',','.') ?>₫</td>
  <td><?= number_format($item['thanh_Tien'],0,',','.') ?>₫</td>
  <td><a href="?delete=<?= $item['id_GHCT'] ?>" class="btn del" onclick="return confirm('Xóa sản phẩm này?')">Xóa</a></td>
</tr>
<?php endforeach; ?>
</table>

<form method="POST" style="margin-top:20px;">
  <input type="text" name="ma_Giam_Gia" placeholder="Nhập mã giảm giá..." required>
  <button type="submit" name="apDungMa" class="add">Áp dụng</button>
</form>

<p class="total">💰 <b>Tổng cộng: <?= number_format(max(0,$total),0,',','.') ?>₫</b></p>
</body>
</html>
