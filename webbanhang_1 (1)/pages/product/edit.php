<?php
// Nạp các file cần thiết
require_once 'includes/function.php'; // Nạp file chứa hàm uploadImage

$product_id = $_GET['id'] ?? null;
if (!$product_id) {
    header('Location: index.php?page=product-list');
    exit();
}

// Lấy thông tin sản phẩm và các bảng liên quan
$db->query('SELECT * FROM Products WHERE product_id = :id'); $db->bind(':id', $product_id); $product = $db->single();
if (!$product) { header('Location: index.php?page=product-list'); exit(); }
$db->query('SELECT * FROM Categories ORDER BY category_name ASC'); $categories = $db->resultSet();
$db->query('SELECT * FROM ProductVariants WHERE product_id = :id ORDER BY variant_id ASC'); $db->bind(':id', $product_id); $variants = $db->resultSet();
$db->query('SELECT * FROM ProductImages WHERE product_id = :id ORDER BY image_id ASC'); $db->bind(':id', $product_id); $images = $db->resultSet();

$errors = [];

// XỬ LÝ KHI NGƯỜI DÙNG SUBMIT FORM
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $db->beginTransaction();
    try {
        // 1. Cập nhật thông tin chung và biến thể
        $db->query('UPDATE Products SET product_name = :name, category_id = :cat_id, description = :desc, updated_at = NOW(), updated_by = :user_id WHERE product_id = :pid');
        $db->bind(':name', trim($_POST['product_name']));
        $db->bind(':cat_id', $_POST['category_id']);
        $db->bind(':desc', trim($_POST['description']));
        $db->bind(':user_id', $_SESSION['user_id']);
        $db->bind(':pid', $product_id);
        $db->execute();

        // 2. Xử lý xóa ảnh đã được chọn
        if (!empty($_POST['delete_images'])) {
            foreach ($_POST['delete_images'] as $image_id_to_delete) {
                $db->query('SELECT image_url FROM ProductImages WHERE image_id = :id AND product_id = :pid');
                $db->bind(':id', $image_id_to_delete);
                $db->bind(':pid', $product_id);
                $old_image = $db->single();
                if ($old_image && file_exists($old_image['image_url'])) {
                    unlink($old_image['image_url']);
                }
                $db->query('DELETE FROM ProductImages WHERE image_id = :id');
                $db->bind(':id', $image_id_to_delete);
                $db->execute();
            }
        }

        // 3. XỬ LÝ UPLOAD ẢNH MỚI TỪ MÁY TÍNH
        if (isset($_FILES['new_images_upload'])) {
            foreach ($_FILES['new_images_upload']['name'] as $key => $name) {
                if ($_FILES['new_images_upload']['error'][$key] === UPLOAD_ERR_OK) {
                    $image_file = [
                        'name' => $name, 'type' => $_FILES['new_images_upload']['type'][$key],
                        'tmp_name' => $_FILES['new_images_upload']['tmp_name'][$key], 'error' => $_FILES['new_images_upload']['error'][$key],
                        'size' => $_FILES['new_images_upload']['size'][$key],
                    ];
                    $alt_text = trim($_POST['new_images_upload_alt'][$key] ?? '');
                    
                    // Gọi hàm uploadImage dùng chung
                    $uploaded_path = uploadImage($image_file, 'ProductImages');
                    
                    // Lưu đường dẫn vào CSDL
                    $db->query('INSERT INTO ProductImages (product_id, image_url, alt_text) VALUES (:pid, :url, :alt)');
                    $db->bind(':pid', $product_id);
                    $db->bind(':url', $uploaded_path);
                    $db->bind(':alt', $alt_text);
                    $db->execute();
                }
            }
        }

        $db->commit();
        $_SESSION['success_message'] = "Cập nhật sản phẩm thành công!";
        header('Location: index.php?page=product-edit&id=' . $product_id);
        exit();

    } catch (Exception $e) {
        $db->rollBack();
        $errors[] = "Lỗi cập nhật: " . $e->getMessage();
    }
}
?>

<section class="content-header">
  <div class="container-fluid"><h1>Chỉnh sửa sản phẩm: <b><?php echo htmlspecialchars($product['product_name']); ?></b></h1></div>
</section>

<section class="content">
    <div class="container-fluid">
        <form method="post" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-8">
                    <div class="card card-primary">
                        <div class="card-header"><h3 class="card-title">Thông tin chung</h3></div>
                        <div class="card-body">
                            <div class="form-group"><label for="product_name">Tên sản phẩm</label><input type="text" class="form-control" id="product_name" name="product_name" value="<?php echo htmlspecialchars($product['product_name']); ?>" required></div>
                            <div class="form-group"><label for="description">Mô tả sản phẩm</label><textarea class="form-control" id="description" name="description" rows="5"><?php echo htmlspecialchars($product['description']); ?></textarea></div>
                        </div>
                    </div>
                    <div class="card card-info">
                        <div class="card-header"><h3 class="card-title">Biến thể sản phẩm</h3></div>
                        <div class="card-body">
                            <div id="variant-container">
                                <?php foreach ($variants as $index => $v): ?>
                                <div class="row mb-2 align-items-center variant-row"><input type="hidden" name="variants[<?php echo $index; ?>][id]" value="<?php echo $v['variant_id']; ?>"><div class="col-md-4"><input type="text" name="variants[<?php echo $index; ?>][size]" class="form-control" placeholder="Size" value="<?php echo htmlspecialchars($v['size']); ?>"></div><div class="col-md-3"><input type="number" name="variants[<?php echo $index; ?>][price]" class="form-control" placeholder="Giá" value="<?php echo $v['price']; ?>" required></div><div class="col-md-3"><input type="number" name="variants[<?php echo $index; ?>][stock]" class="form-control" placeholder="Kho" value="<?php echo $v['stock_quantity']; ?>" required></div><div class="col-md-2"><button type="button" class="btn btn-sm btn-danger remove-variant-btn w-100"><i class="fas fa-trash"></i></button></div></div>
                                <?php endforeach; ?>
                            </div>
                            <button type="button" id="add-variant-btn" class="btn btn-sm btn-success mt-2"><i class="fas fa-plus"></i> Thêm biến thể</button>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card card-secondary">
                        <div class="card-header"><h3 class="card-title">Phân loại</h3></div>
                        <div class="card-body">
                            <div class="form-group"><label for="category_id">Danh mục sản phẩm</label><select class="form-control" id="category_id" name="category_id" required><?php foreach ($categories as $cat): ?><option value="<?php echo $cat['category_id']; ?>" <?php echo ($product['category_id'] == $cat['category_id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($cat['category_name']); ?></option><?php endforeach; ?></select></div>
                        </div>
                    </div>
                    <div class="card card-info">
                        <div class="card-header"><h3 class="card-title">Hình ảnh</h3></div>
                        <div class="card-body">
                            <?php if (!empty($images)): ?>
                            <label>Ảnh hiện tại</label>
                            <div class="mb-3" style="max-height: 250px; overflow-y: auto; border: 1px solid #ddd; padding: 10px;">
                                <?php foreach ($images as $img): ?>
                                <div class="d-flex align-items-center mb-2"><img src="<?php echo htmlspecialchars($img['image_url']); ?>" style="width: 40px; height: 40px; object-fit: cover; margin-right: 10px;"><span class="flex-grow-1 text-truncate" style="font-size: 0.9em;"><?php echo basename($img['image_url']); ?></span><div class="form-check ml-2"><input class="form-check-input" type="checkbox" name="delete_images[]" value="<?php echo $img['image_id']; ?>" id="del_img_<?php echo $img['image_id']; ?>"><label class="form-check-label" for="del_img_<?php echo $img['image_id']; ?>" title="Chọn để xóa ảnh này"><i class="fas fa-trash text-danger"></i></label></div></div>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>

                            <hr>
                            <label class="mt-2">Thêm ảnh mới</label>
                            <div id="new-image-upload-container">
                                </div>
                            <button type="button" id="add-new-image-upload-btn" class="btn btn-sm btn-success mt-2">
                                <i class="fas fa-plus"></i> Thêm hình ảnh
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Cập nhật</button>
                <a href="index.php?page=product-list" class="btn btn-secondary">Hủy bỏ</a>
            </div>
        </form>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Logic cho biến thể
    let variantIndex = <?php echo count($variants); ?>;
    const variantContainer = document.getElementById('variant-container');
    document.getElementById('add-variant-btn').addEventListener('click', function() { const r = document.createElement('div'); r.className = 'row mb-2 align-items-center variant-row'; r.innerHTML = `<input type="hidden" name="variants[${variantIndex}][id]" value=""><div class="col-md-4"><input type="text" name="variants[${variantIndex}][size]" class="form-control" placeholder="Size"></div><div class="col-md-3"><input type="number" name="variants[${variantIndex}][price]" class="form-control" placeholder="Giá" required></div><div class="col-md-3"><input type="number" name="variants[${variantIndex}][stock]" class="form-control" placeholder="Kho" required></div><div class="col-md-2"><button type="button" class="btn btn-sm btn-danger remove-variant-btn w-100"><i class="fas fa-trash"></i></button></div>`; variantContainer.appendChild(r); variantIndex++; });
    variantContainer.addEventListener('click', function(e) { if (e.target.closest('.remove-variant-btn')) { e.target.closest('.variant-row').remove(); }});

    // Logic cho upload ảnh mới
    const newImageUploadContainer = document.getElementById('new-image-upload-container');
    document.getElementById('add-new-image-upload-btn').addEventListener('click', function() {
        const r = document.createElement('div');
        r.className = 'new-image-upload-row mb-3';
        r.innerHTML = `<div class="row"><div class="col-10"><div class="custom-file"><input type="file" class="custom-file-input" name="new_images_upload[]" required><label class="custom-file-label">Chọn tệp...</label></div><input type="text" name="new_images_upload_alt[]" class="form-control mt-2" placeholder="Văn bản thay thế (ALT)"></div><div class="col-2 d-flex align-items-center"><button type="button" class="btn btn-sm btn-danger remove-new-image-upload-btn w-100"><i class="fas fa-trash"></i></button></div></div>`;
        newImageUploadContainer.appendChild(r);
    });
    newImageUploadContainer.addEventListener('click', function(e) { if (e.target.closest('.remove-new-image-upload-btn')) { e.target.closest('.new-image-upload-row').remove(); }});

    // Hiển thị tên file khi chọn
    document.body.addEventListener('change', function(e) { if (e.target.matches('.custom-file-input') && e.target.files.length > 0) { e.target.nextElementSibling.textContent = e.target.files[0].name; }});
});
</script>