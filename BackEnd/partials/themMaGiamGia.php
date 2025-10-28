<?php
require_once '../database.php';
$db = new Database();
$conn = $db->connect();

$message = '';

/* === Xử lý thêm mã giảm giá (POST) === */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['them_mgg'])) {
  $ma = trim($_POST['ma_Giam_Gia'] ?? '');
  $moTa = trim($_POST['mo_Ta'] ?? '');
  $giaTri = floatval($_POST['gia_Tri_Giam'] ?? 0);
  $dieuKien = trim($_POST['dieu_Kien'] ?? '');
  $ngayBD = $_POST['ngay_Bat_Dau'] ?? null;
  $ngayKT = $_POST['ngay_Ket_Thuc'] ?? null;
  $trangThai = trim($_POST['trang_Thai'] ?? 'inactive');
  $giaTriToiThieu = floatval($_POST['gia_Tri_Toi_Thieu'] ?? 0);
  $loaiGiam = trim($_POST['loai_Giam'] ?? 'cash');

  if ($ma === '' || $giaTri <= 0) {
    $message = "<div class='msg error'>⚠️ Mã hoặc giá trị giảm không hợp lệ.</div>";
  } else {
    try {
      $check = $conn->prepare("SELECT COUNT(*) FROM ma_giam_gia WHERE ma_Giam_Gia = ?");
      $check->execute([$ma]);
      if ($check->fetchColumn() > 0) {
        $message = "<div class='msg error'>⚠️ Mã giảm giá đã tồn tại.</div>";
      } else {
        $stmt = $conn->prepare("
          INSERT INTO ma_giam_gia 
          (ma_Giam_Gia, mo_Ta, gia_Tri_Giam, dieu_Kien, ngay_Bat_Dau, ngay_Ket_Thuc, trang_Thai, gia_Tri_Toi_Thieu, loai_Giam)
          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$ma, $moTa, $giaTri, $dieuKien, $ngayBD, $ngayKT, $trangThai, $giaTriToiThieu, $loaiGiam]);
        $message = "<div class='msg success'>✅ Đã thêm mã giảm giá <b>" . htmlspecialchars($ma) . "</b> thành công!</div>";
      }
    } catch (Exception $e) {
      $message = "<div class='msg error'>❌ Lỗi khi thêm: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
  }
}

/* === Xử lý xóa (GET) === */
if (isset($_GET['delete'])) {
  $ma = $_GET['delete'];
  try {
    $conn->prepare("DELETE FROM ma_giam_gia WHERE ma_Giam_Gia = ?")->execute([$ma]);
    $message = "<div class='msg success'>🗑️ Đã xóa mã giảm giá <b>" . htmlspecialchars($ma) . "</b></div>";
  } catch (Exception $e) {
    $message = "<div class='msg error'>❌ Lỗi khi xóa: " . htmlspecialchars($e->getMessage()) . "</div>";
  }
}

/* === Lấy danh sách === */
try {
  $list = $conn->query("SELECT * FROM ma_giam_gia ORDER BY ngay_Bat_Dau DESC")->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
  $list = [];
  $message = "<div class='msg error'>❌ Lỗi khi tải danh sách: " . htmlspecialchars($e->getMessage()) . "</div>";
}
?>
<style>
.qly-mgg { max-width:1000px;margin:20px auto;background:#1c1c1c;border-radius:10px;padding:20px;color:#fff;font-family:Arial; }
.qly-mgg h3 { color:#00ff88;text-align:center;margin-bottom:15px; }
form.add-mgg { display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:14px; }
form.add-mgg .full { grid-column:1 / -1; }
input, select, textarea { width:100%;padding:10px;border-radius:6px;border:none;background:#333;color:#fff; }
button { background:linear-gradient(90deg,#00ff88,#00bfff);border:none;padding:10px 15px;border-radius:6px;color:#111;font-weight:700;cursor:pointer; }
.msg { padding:10px;border-radius:6px;margin-bottom:10px; }
.msg.success { background:#e7f9ee;color:#116938;border:1px solid #c7f0d1; }
.msg.error { background:#fff5f5;color:#8a1c1c;border:1px solid #fccaca; }
table { width:100%;border-collapse:collapse;margin-top:10px;background:#222;border-radius:6px;overflow:hidden; }
th, td { padding:8px;text-align:left;border-bottom:1px solid #333; }
th { background:#333; }
a.delete { color:#ff6666;text-decoration:none; }
</style>

<div class="qly-mgg">
  <h3>🎟️ Quản lý mã giảm giá</h3>
  <?= $message ?>

  <!-- Form thêm mã giảm giá: action trỏ chính xác tới file này, data-ajax để admin.js bắt -->
  <form id="addMggForm" class="add-mgg ajax-form" method="POST" action="partials/themMaGiamGia.php" data-ajax="true">
    <input type="text" name="ma_Giam_Gia" placeholder="Mã giảm giá (VD: SEP30)" required>
    <input type="text" name="mo_Ta" placeholder="Mô tả ngắn">
    <input type="number" name="gia_Tri_Giam" placeholder="Giá trị giảm" step="0.01" required>
    <input type="number" name="gia_Tri_Toi_Thieu" placeholder="Giá trị tối thiểu" step="0.01" required>
    <input type="text" name="dieu_Kien" placeholder="Điều kiện áp dụng">
    <select name="loai_Giam">
      <option value="percent">Phần trăm (%)</option>
      <option value="cash">Giảm tiền (VNĐ)</option>
    </select>
    <input type="datetime-local" name="ngay_Bat_Dau" required>
    <input type="datetime-local" name="ngay_Ket_Thuc" required>
    <select name="trang_Thai">
      <option value="active">Hoạt động</option>
      <option value="inactive">Ngưng</option>
    </select>
    <div class="full">
      <button type="submit" name="them_mgg"><i class="fas fa-plus"></i> Thêm mã</button>
    </div>
  </form>

  <!-- Danh sách mã giảm giá -->
  <table>
    <tr>
      <th>Mã</th>
      <th>Giá trị</th>
      <th>Loại</th>
      <th>Bắt đầu</th>
      <th>Kết thúc</th>
      <th>Trạng thái</th>
      <th>Thao tác</th>
    </tr>
    <?php if (empty($list)): ?>
      <tr><td colspan="7">Chưa có mã giảm giá nào.</td></tr>
    <?php else: ?>
      <?php foreach ($list as $row): ?>
      <tr>
        <td><?= htmlspecialchars($row['ma_Giam_Gia']) ?></td>
        <td><?= htmlspecialchars($row['gia_Tri_Giam']) ?></td>
        <td><?= htmlspecialchars($row['loai_Giam']) ?></td>
        <td><?= htmlspecialchars($row['ngay_Bat_Dau']) ?></td>
        <td><?= htmlspecialchars($row['ngay_Ket_Thuc']) ?></td>
        <td><?= htmlspecialchars($row['trang_Thai']) ?></td>
        <td>
          <a href="#" class="delete" onclick="return deleteMgg('<?= rawurlencode($row['ma_Giam_Gia']) ?>')"><i class="fas fa-trash"></i> Xóa</a>
        </td>
      </tr>
      <?php endforeach; ?>
    <?php endif; ?>
  </table>
</div>

<script>
/*
  AJAX behavior giống quanLyDanhMuc:
  - form có attribute data-ajax="true" sẽ được submit bằng fetch tới action
  - delete link gọi partial với ?delete=MA
  - response HTML thay thế #mainContent
*/
(function(){
  // submit form ajax
  document.addEventListener('submit', function(e){
    const form = e.target;
    if (!(form instanceof HTMLFormElement)) return;
    if (!form.matches('form[data-ajax="true"], form.ajax-form')) return;

    e.preventDefault();
    const submitBtn = form.querySelector('button[type="submit"], input[type="submit"]');
    if (submitBtn) submitBtn.disabled = true;

    fetch(form.action || window.location.href, {
      method: form.method || 'POST',
      body: new FormData(form),
      credentials: 'same-origin',
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => { if (!r.ok) throw new Error(r.status + ' ' + r.statusText); return r.text(); })
    .then(html => {
      const main = document.getElementById('mainContent');
      if (main) main.innerHTML = html;
    })
    .catch(err => {
      alert('Lỗi khi thêm mã giảm giá: ' + err.message);
      console.error(err);
    })
    .finally(() => {
      if (submitBtn) submitBtn.disabled = false;
    });
  });

  // delete via AJAX
  window.deleteMgg = function(ma) {
    if (!confirm('Bạn có chắc muốn xóa mã giảm giá này?')) return false;
    const url = 'partials/themMaGiamGia.php?delete=' + ma;
    fetch(url, { method: 'GET', credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' } })
      .then(r => { if (!r.ok) throw new Error(r.status + ' ' + r.statusText); return r.text(); })
      .then(html => {
        const main = document.getElementById('mainContent');
        if (main) main.innerHTML = html;
      })
      .catch(err => {
        alert('Lỗi khi xóa: ' + err.message);
        console.error(err);
      });
    return false;
  };
})();
</script>
