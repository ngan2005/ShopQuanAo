<?php
// Đường dẫn: /src/Views/admin/quanLySanPham.php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Kế thừa Model đã được tạo ra
// Đường dẫn từ Views/admin ra Models/
require_once __DIR__ . '/../../Models/SanPhamModel.php'; 

$model = new SanPhamModel();

$msg = '';
$editing = false;
$product = [];
$variants = [];

// Lấy danh sách hỗ trợ (Categories, Discounts)
$categories = $model->getCategories();
$discounts = $model->getDiscounts();

/* === TÌM KIẾM VÀ LẤY DANH SÁCH TỪ MODEL === */
$search = trim($_GET['search'] ?? '');
$products = $model->getAll($search);

/* === XỬ LÝ GET (Sửa hoặc Xóa) === */
if (isset($_GET['edit'])) {
    $id = trim($_GET['edit']);
    $product = $model->getById($id);
    if ($product) {
        $editing = true;
        $variants = $model->getVariantsByProductId($id);
    } else {
        $msg = "<div class='msg error'>Không tìm thấy sản phẩm cần sửa.</div>";
    }
}

if (isset($_GET['delete'])) {
    $id = trim($_GET['delete']);
    try {
        if ($model->deleteProduct($id)) {
            $msg = "<div class='msg success'><i class='fas fa-trash'></i> Đã xóa sản phẩm <strong>$id</strong>!</div>";
            $products = $model->getAll($search); 
        }
    } catch (Exception $e) {
        $msg = "<div class='msg error'><i class='fas fa-times-circle'></i> Lỗi xóa: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}

/* === XỬ LÝ POST (Thêm hoặc Sửa) === */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_SP = trim($_POST['id_SP'] ?? '');
    $data = [
        'id_SP'          => $id_SP,
        'ten_San_Pham'   => trim($_POST['ten_San_Pham'] ?? ''),
        'gia_Ban'        => floatval($_POST['gia_Ban'] ?? 0),
        'gia_Goc'        => floatval($_POST['gia_Goc'] ?? 0),
        'mo_Ta'          => trim($_POST['mo_Ta'] ?? ''),
        'id_DM'          => intval($_POST['id_DM'] ?? 0),
        'thuong_Hieu'    => trim($_POST['thuong_Hieu'] ?? ''),
        'so_Luong_Ton'   => intval($_POST['so_Luong_Ton'] ?? 0),
        'trang_Thai'     => trim($_POST['trang_Thai'] ?? 'Còn hàng'),
        'ma_Giam_Gia'    => trim($_POST['ma_Giam_Gia'] ?? ''),
        'mau_Sac_arr'    => $_POST['mau_Sac'] ?? [],
        'kich_Thuoc_arr' => $_POST['kich_Thuoc'] ?? [],
        'hinh_Anh'       => trim($_POST['link_hinh'] ?? '')
    ];

    $isUpdating = isset($_POST['sua']);
    $errors = [];

    // Validation (giữ nguyên)
    if ($data['id_SP'] === '') $errors[] = "Mã sản phẩm không được để trống.";
    if ($data['ten_San_Pham'] === '') $errors[] = "Tên sản phẩm không được để trống.";
    if ($data['gia_Ban'] <= 0) $errors[] = "Giá bán phải lớn hơn 0.";
    if ($data['id_DM'] <= 0) $errors[] = "Vui lòng chọn danh mục hợp lệ.";
    if (!$isUpdating && $model->checkIdExists($data['id_SP'])) {
        $errors[] = "Mã sản phẩm **{$data['id_SP']}** đã tồn tại.";
    }

    // Xử lý Upload File (giữ nguyên)
    if (isset($_FILES['file_hinh']) && $_FILES['file_hinh']['error'] === UPLOAD_ERR_OK) {
        // Cần tạo thư mục uploads ở ngang hàng với public/src
        $uploadDir = 'uploads/'; 
        if (!is_dir($uploadDir) && !mkdir($uploadDir, 0777, true)) {
             $errors[] = "Không thể tạo thư mục uploads.";
        } else {
            $fileName = basename($_FILES['file_hinh']['name']);
            $targetPath = $uploadDir . $fileName;
            if (move_uploaded_file($_FILES['file_hinh']['tmp_name'], $targetPath)) {
                $data['hinh_Anh'] = "uploads/" . $fileName; 
            } else {
                $errors[] = "Lỗi khi di chuyển file đã upload.";
            }
        }
    }

    if (!empty($errors)) {
        $msg = "<div class='msg error'><ul><li>" . implode("</li><li>", $errors) . "</li></ul></div>";
        $product = array_merge($product, $data); 
        $editing = $isUpdating; // Giữ trạng thái editing để hiển thị nút Sửa
    } else {
        try {
            $model->saveProduct($data, $isUpdating);
            $msg = "<div class='msg success'>Lưu sản phẩm thành công!</div>";
            
            // Redirect về trang danh sách sau khi lưu thành công
            header("Location: admin.php?page=quanLySanPham"); 
            exit(); 
        } catch (Exception $e) {
            $msg = "<div class='msg error'>Lỗi giao dịch: " . htmlspecialchars($e->getMessage()) . "</div>";
            error_log("Lỗi giao dịch SP: " . $e->getMessage());
        }
    }
    // Cập nhật lại danh sách sau khi lưu (nếu không redirect)
    $products = $model->getAll($search); 
}

// Bắt đầu phần HTML/VIEW (giống file cũ, sửa đường dẫn action)
?>

<div class="container">

    <h2>Quản lý Sản phẩm</h2>

    <div class="search-bar">
        <form method="GET" action="admin.php" style="display:flex; width:100%; gap:10px;">
            <input type="hidden" name="page" value="quanLySanPham"> 
            
            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Tìm theo mã hoặc tên sản phẩm..." autofocus>
            <button type="submit"><i class="fas fa-search"></i></button>
            <?php if ($search !== ''): ?>
                <a href="admin.php?page=quanLySanPham" class="clear-btn"><i class="fas fa-times"></i></a>
            <?php endif; ?>
        </form>
    </div>

    <?= $msg ?>

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
                    <input type="file" name="file_hinh" accept="image/*">
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
                        <a href="admin.php?page=quanLySanPham" class="btn btn-cancel">
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

// SỬA: Hàm JS trỏ đến Router Admin
function editProduct(id) {
    const searchParam = '<?= urlencode($search) ?>';
    let url = `admin.php?page=quanLySanPham&edit=${encodeURIComponent(id)}`;
    if (searchParam) {
         url += `&search=${searchParam}`;
    }
    window.location.href = url;
}

// XÓA: Hàm JS trỏ đến Router Admin
function deleteProduct(id) {
    if (!confirm(`Xóa sản phẩm ${id}?`)) return;
    const searchParam = '<?= urlencode($search) ?>';
    let url = `admin.php?page=quanLySanPham&delete=${encodeURIComponent(id)}`;
     if (searchParam) {
         url += `&search=${searchParam}`;
    }
    window.location.href = url;
}
</script>