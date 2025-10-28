<?php
// DEBUG TEMP: bật hiển thị lỗi PHP + PDO exception mode (bật khi debug, tắt sau khi xong)
error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once '../database.php';
$db = new Database();
$conn = $db->connect();
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$msg = '';
$editing = false;
$editId = null;
$editProduct = [];
$editVariant = []; // giữ biến để form không lỗi, nhưng không đọc/ghi DB biến thể

/* Load categories */
$categories = [];
try {
    $stmt = $conn->query("SELECT id_DM, ten_Danh_Muc FROM danh_muc ORDER BY id_DM ASC");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log("Load categories error: " . $e->getMessage());
}

/* If GET edit=ID */
if (isset($_GET['edit'])) {
    $editId = $_GET['edit'];
    try {
        $p = $conn->prepare("SELECT * FROM san_pham WHERE id_SP = ?");
        $p->execute([$editId]);
        $editProduct = $p->fetch(PDO::FETCH_ASSOC) ?: [];
        if ($editProduct) {
            $editing = true;
            // LƯU Ý: không đọc bảng bien_the_san_pham ở đây (xóa phần biến thể)
        }
    } catch (Exception $e) {
        error_log("Load edit product error: " . $e->getMessage());
    }
}

/* Handle delete */
if (isset($_GET['delete'])) {
    $delId = $_GET['delete'];
    try {
        $conn->beginTransaction();
        // XÓA: không xóa bảng bien_the_san_pham ở đây — bạn sẽ xử lý sau nếu cần
        $d = $conn->prepare("DELETE FROM san_pham WHERE id_SP = ?");
        $d->execute([$delId]);
        $conn->commit();
        $msg = "<div class='msg success'>Đã xóa sản phẩm.</div>";
    } catch (Exception $e) {
        $conn->rollBack();
        error_log("Delete error: ".$e->getMessage());
        $msg = "<div class='msg error'>Lỗi khi xóa sản phẩm.</div>";
    }
}

/* Handle update (edit) */
/* Handle update (edit) */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sua_san_pham'])) {
    $editing = true;
    $original_id  = trim($_POST['original_id'] ?? '');
    $id_SP        = trim($_POST['id_SP'] ?? '');
    $ten_San_Pham = trim($_POST['ten_San_Pham'] ?? '');
    $gia_Ban      = floatval($_POST['gia_Ban'] ?? 0);
    $gia_Goc      = floatval($_POST['gia_Goc'] ?? 0);
    $mo_Ta        = trim($_POST['mo_Ta'] ?? '');
    $hinh_Anh     = trim($_POST['hinh_Anh'] ?? '');
    $id_DM        = intval($_POST['id_DM'] ?? 0);
    $thuong_Hieu  = trim($_POST['thuong_Hieu'] ?? '');
    $so_Luong_Ton = intval($_POST['so_Luong_Ton'] ?? 0);
    $trang_Thai   = trim($_POST['trang_Thai'] ?? 'Còn hàng');
    $ma_Giam_Gia  = trim($_POST['ma_Giam_Gia'] ?? '');
    $mau_Sac      = trim($_POST['mau_Sac'] ?? '');
    $kich_Thuoc   = trim($_POST['kich_Thuoc'] ?? '');

    $errors = [];
    if ($original_id === '') $errors[] = 'ID gốc không hợp lệ.';
    if ($id_SP === '') $errors[] = 'Mã sản phẩm không được để trống.';
    if ($ten_San_Pham === '') $errors[] = 'Tên sản phẩm không được để trống.';
    if ($gia_Ban <= 0) $errors[] = 'Giá bán phải lớn hơn 0.';
    if ($id_DM <= 0) $errors[] = 'Vui lòng chọn danh mục hợp lệ.';

    if (empty($errors)) {
        try {
            $conn->beginTransaction();

            // ✅ Cập nhật bảng san_pham
            $sql = "UPDATE san_pham SET 
                        id_SP = ?, ten_San_Pham = ?, gia_Ban = ?, gia_Goc = ?, mo_Ta = ?, 
                        hinh_Anh = ?, id_DM = ?, thuong_Hieu = ?, so_Luong_Ton = ?, trang_Thai = ?, 
                        ngay_Cap_Nhat = NOW(), ma_Giam_Gia = ?
                    WHERE id_SP = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                $id_SP, $ten_San_Pham, $gia_Ban, $gia_Goc, $mo_Ta, $hinh_Anh,
                $id_DM, $thuong_Hieu, $so_Luong_Ton, $trang_Thai,
                $ma_Giam_Gia !== '' ? $ma_Giam_Gia : null,
                $original_id
            ]);

            // ✅ Cập nhật hoặc thêm biến thể sản phẩm
            $checkVariant = $conn->prepare("SELECT id_Bien_The FROM bien_the_san_pham WHERE id_SP = ?");
            $checkVariant->execute([$original_id]);
            $variant = $checkVariant->fetch(PDO::FETCH_ASSOC);

            if ($variant) {
                // Đã có biến thể → cập nhật
                $updateVariant = $conn->prepare("
                    UPDATE bien_the_san_pham 
                    SET id_SP = ?, mau_Sac = ?, kich_Thuoc = ? 
                    WHERE id_Bien_The = ?");
                $updateVariant->execute([
                    $id_SP,
                    $mau_Sac !== '' ? $mau_Sac : null,
                    $kich_Thuoc !== '' ? $kich_Thuoc : null,
                    $variant['id_Bien_The']
                ]);
            } else {
                // Chưa có → thêm mới
                $insertVariant = $conn->prepare("
                    INSERT INTO bien_the_san_pham (id_SP, mau_Sac, kich_Thuoc)
                    VALUES (?, ?, ?)");
                $insertVariant->execute([
                    $id_SP,
                    $mau_Sac !== '' ? $mau_Sac : null,
                    $kich_Thuoc !== '' ? $kich_Thuoc : null
                ]);
            }

            $conn->commit();
            $msg = "<div class='msg success'>✅ Cập nhật sản phẩm thành công!</div>";
        } catch (Exception $e) {
            $conn->rollBack();
            $msg = "<div class='msg error'>❌ Lỗi khi cập nhật sản phẩm: " . htmlspecialchars($e->getMessage()) . "</div>";
        }
    } else {
        $msg = '<div class="msg error"><ul>';
        foreach ($errors as $er) $msg .= '<li>' . htmlspecialchars($er) . '</li>';
        $msg .= '</ul></div>';
    }
}

/* Handle add product */
/* Handle add product */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['them_san_pham'])) {
    $id_SP        = trim($_POST['id_SP'] ?? '');
    $ten_San_Pham = trim($_POST['ten_San_Pham'] ?? '');
    $gia_Ban      = floatval($_POST['gia_Ban'] ?? 0);
    $gia_Goc      = floatval($_POST['gia_Goc'] ?? 0);
    $mo_Ta        = trim($_POST['mo_Ta'] ?? '');
    $hinh_Anh     = trim($_POST['hinh_Anh'] ?? '');
    $id_DM        = intval($_POST['id_DM'] ?? 0);
    $thuong_Hieu  = trim($_POST['thuong_Hieu'] ?? '');
    $so_Luong_Ton = intval($_POST['so_Luong_Ton'] ?? 0);
    $trang_Thai   = trim($_POST['trang_Thai'] ?? 'Còn hàng');
    $ma_Giam_Gia  = trim($_POST['ma_Giam_Gia'] ?? '');
    $mau_Sac      = trim($_POST['mau_Sac'] ?? '');
    $kich_Thuoc   = trim($_POST['kich_Thuoc'] ?? '');

    $errors = [];
    if ($id_SP === '') $errors[] = 'Mã sản phẩm không được để trống.';
    if ($ten_San_Pham === '') $errors[] = 'Tên sản phẩm không được để trống.';
    if ($gia_Ban <= 0) $errors[] = 'Giá bán phải lớn hơn 0.';
    if ($id_DM <= 0) $errors[] = 'Vui lòng chọn danh mục hợp lệ.';

    if (empty($errors)) {
        $checkSP = $conn->prepare("SELECT COUNT(*) FROM san_pham WHERE id_SP = ?");
        $checkSP->execute([$id_SP]);
        if ($checkSP->fetchColumn() > 0) $errors[] = 'Mã sản phẩm đã tồn tại.';
    }

    if (!empty($errors)) {
        $msg = '<div class="msg error"><ul>';
        foreach ($errors as $er) $msg .= '<li>' . htmlspecialchars($er) . '</li>';
        $msg .= '</ul></div>';
    } else {
        try {
            $conn->beginTransaction();

            // ✅ Thêm sản phẩm
            $sql = "INSERT INTO san_pham 
                    (id_SP, ten_San_Pham, gia_Ban, gia_Goc, mo_Ta, hinh_Anh, id_DM, thuong_Hieu, so_Luong_Ton, trang_Thai, ngay_Tao, ngay_Cap_Nhat, ma_Giam_Gia)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW(), ?)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                $id_SP, $ten_San_Pham, $gia_Ban, $gia_Goc, $mo_Ta, $hinh_Anh,
                $id_DM, $thuong_Hieu, $so_Luong_Ton, $trang_Thai,
                $ma_Giam_Gia !== '' ? $ma_Giam_Gia : null
            ]);

            // ✅ Thêm biến thể nếu có
            if ($mau_Sac !== '' || $kich_Thuoc !== '') {
                $vstmt = $conn->prepare("
                    INSERT INTO bien_the_san_pham (id_SP, mau_Sac, kich_Thuoc)
                    VALUES (?, ?, ?)");
                $vstmt->execute([
                    $id_SP,
                    $mau_Sac !== '' ? $mau_Sac : null,
                    $kich_Thuoc !== '' ? $kich_Thuoc : null
                ]);
            }

            $conn->commit();
            $msg = "<div class='msg success'>✅ Thêm sản phẩm thành công!</div>";
        } catch (Exception $e) {
            $conn->rollBack();
            $msg = "<div class='msg error'>❌ Lỗi khi thêm sản phẩm: " . htmlspecialchars($e->getMessage()) . "</div>";
        }
    }
}

/* Load products */
$products = [];
try {
    $pstmt = $conn->query("SELECT * FROM san_pham ORDER BY ngay_Tao DESC");
    $products = $pstmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log("Load products error: " . $e->getMessage());
    $msg = "<div class='msg error'>Lỗi khi tải danh sách sản phẩm.</div>";
}
?>
<style>
/* giữ style như trước */
.them-sp {
  background: #1e1e1e;
  color: #fff;
  padding: 25px;
  border-radius: 12px;
  max-width: 1100px;
  margin: 0 auto 30px;
  box-shadow: 0 0 20px rgba(0,0,0,0.4);
  font-family: Arial;
}
.them-sp h3 { color: #00ff88; margin-bottom: 15px; text-align: center; }
.form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
input, textarea, select { width:100%; padding:10px; border:none; border-radius:6px; background:#333; color:#fff; }
textarea { resize:vertical; min-height:80px; }
button, .btn { grid-column:1 / -1; background: linear-gradient(90deg,#00ff88,#00bfff); color:#111; border:none; border-radius:8px; padding:12px; cursor:pointer; font-weight:bold; font-size:16px; }
.msg { padding:10px; border-radius:6px; margin-bottom:10px; }
.msg.success { background:#e7f9ee; color:#116938; border:1px solid #c7f0d1; }
.msg.error { background:#fff5f5; color:#8a1c1c; border:1px solid #fccaca; }
.table-wrap { max-width:1100px; margin: 0 auto 50px; background:#121212; border-radius:10px; padding:12px; }
table { width:100%; border-collapse: collapse; color:#fff; }
th, td { padding:10px; border-bottom:1px solid #222; text-align:left; font-size:14px; }
th { background:#151515; color:#ddd; }
tr:nth-child(even){ background:#0f1315; }
a.delete, a.edit { color:#ff6666; text-decoration:none; margin-right:8px; }
a.edit { color:#00bfff; }
.img-thumb { width:80px; height:80px; object-fit:cover; border-radius:6px; background:#222; display:inline-block; }
</style>

<div class="them-sp">
  <h3><?= $editing ? "✏️ Chỉnh sửa sản phẩm" : "🟢 Thêm sản phẩm mới" ?></h3>
  <?= $msg ?>

  <form id="addProductForm" class="ajax-form" data-ajax="true" action="partials/themSanPham.php<?= $editing ? '?edit=' . rawurlencode($editId) : '' ?>" method="POST">
    <div class="form-grid">
      <!-- original_id dùng để update -->
      <?php if ($editing): ?>
        <input type="hidden" name="original_id" value="<?= htmlspecialchars($editProduct['id_SP'] ?? $editId) ?>">
      <?php endif; ?>

      <input type="text" name="id_SP" placeholder="Mã sản phẩm (VD: SP001)" required
             value="<?= htmlspecialchars($_POST['id_SP'] ?? ($editing ? ($editProduct['id_SP'] ?? '') : '')) ?>"
             <?= $editing ? 'readonly' : '' ?>>

      <input type="text" name="ten_San_Pham" placeholder="Tên sản phẩm" required
             value="<?= htmlspecialchars($_POST['ten_San_Pham'] ?? ($editing ? ($editProduct['ten_San_Pham'] ?? '') : '')) ?>">

      <input type="number" step="0.01" name="gia_Ban" placeholder="Giá bán (VNĐ)" min="0" required
             value="<?= htmlspecialchars($_POST['gia_Ban'] ?? ($editing ? ($editProduct['gia_Ban'] ?? '') : '')) ?>">

      <input type="number" step="0.01" name="gia_Goc" placeholder="Giá gốc (VNĐ)" min="0"
             value="<?= htmlspecialchars($_POST['gia_Goc'] ?? ($editing ? ($editProduct['gia_Goc'] ?? '') : '')) ?>">

      <textarea name="mo_Ta" placeholder="Mô tả sản phẩm"><?= htmlspecialchars($_POST['mo_Ta'] ?? ($editing ? ($editProduct['mo_Ta'] ?? '') : '')) ?></textarea>

      <input type="text" name="hinh_Anh" placeholder="Đường dẫn hình ảnh (URL hoặc tên file)"
             value="<?= htmlspecialchars($_POST['hinh_Anh'] ?? ($editing ? ($editProduct['hinh_Anh'] ?? '') : '')) ?>">

      <select name="id_DM" required>
        <option value="">-- Chọn danh mục --</option>
        <?php foreach ($categories as $c):
            $postedIdDM = isset($_POST['id_DM']) ? intval($_POST['id_DM']) : null;
            if ($postedIdDM !== null) {
                $sel = $postedIdDM === (int)$c['id_DM'];
            } else {
                $sel = $editing ? ((int)($editProduct['id_DM'] ?? 0) === (int)$c['id_DM']) : false;
            }
        ?>
          <option value="<?= (int)$c['id_DM'] ?>" <?= $sel ? 'selected' : '' ?>>
            <?= htmlspecialchars($c['ten_Danh_Muc']) ?> (ID: <?= (int)$c['id_DM'] ?>)
          </option>
        <?php endforeach; ?>
      </select>

      <input type="text" name="thuong_Hieu" placeholder="Thương hiệu"
             value="<?= htmlspecialchars($_POST['thuong_Hieu'] ?? ($editing ? ($editProduct['thuong_Hieu'] ?? '') : '')) ?>">

      <!-- Form vẫn có mau_Sac / kich_Thuoc inputs nhưng hiện tại không được lưu vào DB -->
      <input type="text" name="mau_Sac" placeholder="Màu sắc (VD: Đen, Trắng, Xanh)"
             value="<?= htmlspecialchars($_POST['mau_Sac'] ?? '') ?>">

      <input type="text" name="kich_Thuoc" placeholder="Kích thước (VD: S, M, L, XL)"
             value="<?= htmlspecialchars($_POST['kich_Thuoc'] ?? '') ?>">

      <input type="number" name="so_Luong_Ton" placeholder="Số lượng tồn kho" min="0"
             value="<?= htmlspecialchars($_POST['so_Luong_Ton'] ?? ($editing ? ($editProduct['so_Luong_Ton'] ?? 0) : 0)) ?>">

      <input type="text" name="trang_Thai" placeholder="Trạng thái (VD: Còn hàng / active)"
             value="<?= htmlspecialchars($_POST['trang_Thai'] ?? ($editing ? ($editProduct['trang_Thai'] ?? 'Còn hàng') : 'Còn hàng')) ?>">

      <input type="text" name="ma_Giam_Gia" placeholder="Mã giảm giá (nếu có)"
             value="<?= htmlspecialchars($_POST['ma_Giam_Gia'] ?? ($editing ? ($editProduct['ma_Giam_Gia'] ?? '') : '')) ?>">

      <?php if ($editing): ?>
        <button type="submit" name="sua_san_pham"><i class="fas fa-save"></i> Lưu thay đổi</button>
        <button type="button" onclick="loadContent('themSanPham')" style="background:#ff6666;color:#fff;border:none;padding:10px;border-radius:6px;cursor:pointer">Hủy</button>
      <?php else: ?>
        <button type="submit" name="them_san_pham"><i class="fas fa-plus-circle"></i> Thêm sản phẩm</button>
      <?php endif; ?>
    </div>
  </form>
</div>

<div class="table-wrap">
  <h3 style="color:#00ff88;margin:10px 0">📦 Danh sách sản phẩm</h3>
  <table>
    <thead>
      <tr>
        <th width="90">ID</th>
        <th>Tên</th>
        <th>Ảnh</th>
        <th>Giá bán</th>
        <th>Danh mục</th>
        <th>Tồn kho</th>
        <th>Trạng thái</th>
        <th>Màu sắc</th>
        <th>Kích thước</th>
        <th>Thao tác</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($products)): ?>
        <tr><td colspan="10">Chưa có sản phẩm nào.</td></tr>
      <?php else: ?>
        <?php foreach ($products as $row): ?>
          <tr>
            <td><?= htmlspecialchars($row['id_SP']) ?></td>
            <td><?= htmlspecialchars($row['ten_San_Pham']) ?></td>
            <td>
              <?php $thumb = $row['hinh_Anh'] ?? ''; ?>
              <?php if (!empty($thumb)): ?>
                <img src="<?= htmlspecialchars($thumb) ?>" alt="" class="img-thumb">
              <?php else: ?>
                <div class="img-thumb"></div>
              <?php endif; ?>
            </td>
            <td><?= number_format($row['gia_Ban'], 0, ',', '.') ?> ₫</td>
            <td><?= htmlspecialchars($row['id_DM']) ?></td>
            <td><?= (int)$row['so_Luong_Ton'] ?></td>
            <td><?= htmlspecialchars($row['trang_Thai']) ?></td>
            <!-- Vì không còn đọc bien_the_san_pham, hiển thị placeholder -->
            <td>-</td>
            <td>-</td>
            <td>
              <a href="#" class="edit" onclick="return editProduct('<?= rawurlencode($row['id_SP']) ?>')">Sửa</a>
              <a href="#" class="delete" onclick="return deleteProduct('<?= rawurlencode($row['id_SP']) ?>')">Xóa</a>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<script>
(function(){
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
      if (main) {
        main.innerHTML = html;
        const scripts = main.getElementsByTagName('script');
        Array.from(scripts).forEach(s => {
          const ns = document.createElement('script');
          Array.from(s.attributes).forEach(a => ns.setAttribute(a.name, a.value));
          ns.text = s.textContent;
          s.parentNode.replaceChild(ns, s);
        });
      } else location.reload();
    })
    .catch(err => { alert('Lỗi khi gửi form: ' + err.message); console.error(err); })
    .finally(() => { if (submitBtn) submitBtn.disabled = false; });
  });

  window.deleteProduct = function(id) {
    if (!confirm('Bạn có chắc muốn xóa sản phẩm này?')) return false;
    const url = 'partials/themSanPham.php?delete=' + encodeURIComponent(id);
    fetch(url, { method: 'GET', credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' } })
      .then(r => { if (!r.ok) throw new Error(r.status + ' ' + r.statusText); return r.text(); })
      .then(html => { const main = document.getElementById('mainContent'); if (main) main.innerHTML = html; })
      .catch(err => { alert('Lỗi khi xóa sản phẩm: ' + err.message); console.error(err); });
    return false;
  };

  window.editProduct = function(id) {
    const url = 'partials/themSanPham.php?edit=' + encodeURIComponent(id);
    fetch(url, { method: 'GET', credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' } })
      .then(r => { if (!r.ok) throw new Error(r.status + ' ' + r.statusText); return r.text(); })
      .then(html => { const main = document.getElementById('mainContent'); if (main) main.innerHTML = html; })
      .catch(err => { alert('Lỗi khi tải form sửa: ' + err.message); console.error(err); });
    return false;
  };
})();
</script>


