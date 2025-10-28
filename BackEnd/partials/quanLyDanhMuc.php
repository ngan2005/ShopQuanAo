<?php
require_once '../database.php';
$db = new Database();
$conn = $db->connect();

$message = '';

/* === Xử lý xóa danh mục (GET) === */
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    try {
        $stmt = $conn->prepare("DELETE FROM danh_muc WHERE id_DM = ?");
        $stmt->execute([$id]);
        $message = "<div class='msg success'>🗑️ Đã xóa danh mục ID $id!</div>";
    } catch (Exception $e) {
        $message = "<div class='msg error'>❌ Lỗi khi xóa: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}

/* === Xử lý thêm danh mục (POST) === */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['them_danh_muc'])) {
    $ten = trim($_POST['ten_Danh_Muc'] ?? '');

    if ($ten === '') {
        $message = "<div class='msg error'>⚠️ Tên danh mục không được để trống!</div>";
    } else {
        try {
            // ✅ KHÔNG CHÈN ID (id_DM tự tăng trong MySQL)
            $stmt = $conn->prepare("INSERT INTO danh_muc (ten_Danh_Muc) VALUES (?)");
            $stmt->execute([$ten]);
            $message = "<div class='msg success'>✅ Đã thêm danh mục <b>" . htmlspecialchars($ten) . "</b>!</div>";
        } catch (Exception $e) {
            $message = "<div class='msg error'>❌ Lỗi khi thêm: " . htmlspecialchars($e->getMessage()) . "</div>";
        }
    }
}

/* === Lấy danh sách danh mục === */
try {
    $stmt = $conn->query("SELECT * FROM danh_muc ORDER BY id_DM ASC");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $rows = [];
    $message = "<div class='msg error'>❌ Lỗi khi tải danh mục: " . htmlspecialchars($e->getMessage()) . "</div>";
}
?>

<style>
.quanly-dm {
  max-width: 800px;
  margin: 18px auto;
  background: #1c1c1c;
  border-radius: 10px;
  padding: 20px;
  color: #fff;
  font-family: Arial;
}
.quanly-dm h3 {
  color: #00ff88;
  margin-bottom: 15px;
  text-align: center;
}
form.add-dm {
  display: flex;
  gap: 10px;
  margin-bottom: 20px;
}
form.add-dm input {
  flex: 1;
  padding: 10px;
  border: none;
  border-radius: 6px;
  background: #333;
  color: #fff;
}
form.add-dm button {
  padding: 10px 15px;
  border: none;
  border-radius: 6px;
  background: linear-gradient(90deg,#00ff88,#00bfff);
  color: #111;
  font-weight: bold;
  cursor: pointer;
}
table {
  width: 100%;
  border-collapse: collapse;
  background: #222;
  border-radius: 8px;
  overflow: hidden;
}
th, td {
  padding: 10px;
  text-align: left;
}
th {
  background: #333;
}
tr:nth-child(even) { background: #2a2a2a; }
a.delete {
  color: #ff5555;
  text-decoration: none;
}
.msg {
  padding: 10px;
  border-radius: 6px;
  margin-bottom: 10px;
}
.msg.success { background: #e7f9ee; color: #116938; border: 1px solid #c7f0d1; }
.msg.error { background: #fff5f5; color: #8a1c1c; border: 1px solid #fccaca; }
</style>

<div class="quanly-dm">
  <h3>📂 Quản lý danh mục sản phẩm</h3>
  <?= $message ?>

  <!-- Form thêm danh mục -->
  <form id="addDmForm" class="add-dm" method="POST" action="partials/quanLyDanhMuc.php">
    <!-- ✅ KHÔNG CÓ Ô NHẬP ID -->
    <input type="text" name="ten_Danh_Muc" placeholder="Nhập tên danh mục mới" required>
    <button type="submit" name="them_danh_muc"><i class="fas fa-plus"></i> Thêm</button>
  </form>

  <!-- Bảng danh mục -->
  <table>
    <tr>
      <th width="80">ID</th>
      <th>Tên danh mục</th>
      <th width="120">Thao tác</th>
    </tr>
    <?php if (empty($rows)): ?>
      <tr><td colspan="3">Chưa có danh mục nào.</td></tr>
    <?php else: ?>
      <?php foreach ($rows as $row): ?>
      <tr>
        <td><?= htmlspecialchars($row['id_DM']) ?></td>
        <td><?= htmlspecialchars($row['ten_Danh_Muc']) ?></td>
        <td>
          <a href="#" class="delete" onclick="return deleteDm(<?= intval($row['id_DM']) ?>)">
            <i class="fas fa-trash"></i> Xóa
          </a>
        </td>
      </tr>
      <?php endforeach; ?>
    <?php endif; ?>
  </table>
</div>

<script>
// submit thêm danh mục bằng AJAX
document.addEventListener('DOMContentLoaded', function() {
  const form = document.getElementById('addDmForm');
  if (!form) return;

  form.addEventListener('submit', function(e) {
    e.preventDefault();
    const btn = form.querySelector('button[type="submit"]');
    if (btn) btn.disabled = true;

    fetch(form.action, {
      method: 'POST',
      body: new FormData(form),
      credentials: 'same-origin',
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => {
      if (!r.ok) throw new Error('Network response was not ok');
      return r.text();
    })
    .then(html => {
      const main = document.getElementById('mainContent');
      if (main) main.innerHTML = html;
    })
    .catch(err => {
      alert('Lỗi khi thêm danh mục: ' + err.message);
      console.error(err);
    })
    .finally(() => {
      if (btn) btn.disabled = false;
    });
  });
});

// xóa danh mục bằng AJAX
function deleteDm(id) {
  if (!confirm('Bạn có chắc muốn xóa danh mục này?')) return false;
  const url = `partials/quanLyDanhMuc.php?delete=${id}`;

  fetch(url, { method: 'GET', credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' } })
    .then(r => {
      if (!r.ok) throw new Error('Network response was not ok');
      return r.text();
    })
    .then(html => {
      const main = document.getElementById('mainContent');
      if (main) main.innerHTML = html;
    })
    .catch(err => {
      alert('Lỗi khi xóa danh mục: ' + err.message);
      console.error(err);
    });

  return false;
}
</script>
