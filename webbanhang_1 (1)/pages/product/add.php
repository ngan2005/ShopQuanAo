<?php
// Nạp các file cần thiết
require_once 'includes/function.php'; // Nạp file chứa hàm uploadImage

// Lấy danh sách danh mục
$db->query('SELECT * FROM Categories ORDER BY category_name ASC');
$categories = $db->resultSet();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Bắt đầu transaction
    $db->beginTransaction();

    try {
        // 1. Lấy và Validate dữ liệu bảng Products
        $product_name = trim($_POST['product_name']);
        $category_id = $_POST['category_id'];
        $description = trim($_POST['description']);
        $created_by = $_SESSION['user_id'];

        if (empty($product_name)) $errors[] = "Tên sản phẩm không được để trống.";
        if (empty($category_id)) $errors[] = "Vui lòng chọn danh mục.";
        if (!empty($errors)) throw new Exception("Dữ liệu không hợp lệ.");

        // 2. Insert vào bảng Products
        $db->query('INSERT INTO Products (product_name, category_id, description, created_at, created_by) VALUES (:name, :cat_id, :desc, NOW(), :user_id)');
        $db->bind(':name', $product_name);
        $db->bind(':cat_id', $category_id);
        $db->bind(':desc', $description);
        $db->bind(':user_id', $created_by);
        $db->execute();
        $product_id = $db->lastInsertId();

        // 3. Xử lý và Insert vào bảng ProductVariants
        if (!empty($_POST['variants'])) {
            foreach ($_POST['variants'] as $variant) {
                $size = trim($variant['size']);
                $price = filter_var($variant['price'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                $stock = filter_var($variant['stock'], FILTER_SANITIZE_NUMBER_INT);
                if ($price === '' || $stock === '') continue;
                $db->query('INSERT INTO ProductVariants (product_id, size, price, stock_quantity, created_at, created_by) VALUES (:pid, :size, :price, :stock, NOW(), :user_id)');
                $db->bind(':pid', $product_id); $db->bind(':size', $size); $db->bind(':price', $price); $db->bind(':stock', $stock); $db->bind(':user_id', $created_by);
                $db->execute();
            }
        }

        // 4. Xử lý upload hình ảnh bằng hàm dùng chung
        if (isset($_FILES['images'])) {
            // Sắp xếp lại mảng $_FILES cho dễ xử lý
            foreach ($_FILES['images']['name'] as $key => $name) {
                if ($_FILES['images']['error'][$key] === UPLOAD_ERR_OK) {
                    $image_file = [
                        'name' => $name,
                        'type' => $_FILES['images']['type'][$key],
                        'tmp_name' => $_FILES['images']['tmp_name'][$key],
                        'error' => $_FILES['images']['error'][$key],
                        'size' => $_FILES['images']['size'][$key]
                    ];
                    $alt_text = trim($_POST['images_alt'][$key] ?? '');

                    // GỌI HÀM DÙNG CHUNG VÀ TRUYỀN TÊN BẢNG
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
        
        // Nếu mọi thứ thành công, commit transaction
        $db->commit();
        $_SESSION['success_message'] = "Thêm sản phẩm thành công!";
        header('Location: index.php?page=product-list');
        exit();

    } catch (Exception $e) {
        // Nếu có lỗi, rollback lại
        $db->rollBack();
        $errors[] = "Đã xảy ra lỗi: " . $e->getMessage();
    }
}
?>

<section class="content-header">
  <div class="container-fluid"><h1>Thêm sản phẩm mới</h1></div>
</section>

<section class="content">
    <div class="container-fluid">
        <form method="post" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-8">
                    <div class="card card-primary">
                        <div class="card-header"><h3 class="card-title">Thông tin chung</h3></div>
                        <div class="card-body">
                            <?php if (!empty($errors)): ?>
                                <div class="alert alert-danger">
                                    <?php foreach ($errors as $error): ?><p><?php echo $error; ?></p><?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                            <div class="form-group">
                                <label for="product_name">Tên sản phẩm</label>
                                <input type="text" class="form-control" id="product_name" name="product_name" required>
                            </div>
                            <div class="form-group">
                                <label for="description">Mô tả sản phẩm</label>
                                <textarea class="form-control" id="description" name="description" rows="5"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="card card-info">
                        <div class="card-header"><h3 class="card-title">Biến thể sản phẩm</h3></div>
                        <div class="card-body">
                            <div id="variant-container"></div>
                            <button type="button" id="add-variant-btn" class="btn btn-sm btn-success mt-2"><i class="fas fa-plus"></i> Thêm biến thể</button>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-secondary">
                        <div class="card-header"><h3 class="card-title">Phân loại</h3></div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="category_id">Danh mục sản phẩm</label>
                                <select class="form-control" id="category_id" name="category_id" required>
                                    <option value="">-- Chọn danh mục --</option>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?php echo $cat['category_id']; ?>"><?php echo htmlspecialchars($cat['category_name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="card card-info">
                        <div class="card-header"><h3 class="card-title">Hình ảnh sản phẩm</h3></div>
                        <div class="card-body">
                            <div id="image-container"></div>
                            <button type="button" id="add-image-btn" class="btn btn-sm btn-success mt-2"><i class="fas fa-plus"></i> Thêm hình ảnh</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Thêm mới</button>
                <a href="index.php?page=product-list" class="btn btn-secondary">Hủy bỏ</a>
            </div>
        </form>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Quản lý Biến thể
    const variantContainer = document.getElementById('variant-container');
    const addVariantBtn = document.getElementById('add-variant-btn');
    let variantIndex = 0;
    function addVariantRow() {
        const row = document.createElement('div');
        row.className = 'row mb-2 align-items-center variant-row';
        row.innerHTML = `<div class="col-md-4"><input type="text" name="variants[${variantIndex}][size]" class="form-control" placeholder="Size (VD: S, M, L)"></div><div class="col-md-3"><input type="number" name="variants[${variantIndex}][price]" class="form-control" placeholder="Giá bán" required></div><div class="col-md-3"><input type="number" name="variants[${variantIndex}][stock]" class="form-control" placeholder="Tồn kho" required></div><div class="col-md-2"><button type="button" class="btn btn-sm btn-danger remove-variant-btn"><i class="fas fa-trash"></i></button></div>`;
        variantContainer.appendChild(row);
        variantIndex++;
    }
    addVariantBtn.addEventListener('click', addVariantRow);
    variantContainer.addEventListener('click', function(e) {
        if (e.target.closest('.remove-variant-btn')) { e.target.closest('.variant-row').remove(); }
    });

    // Quản lý Hình ảnh
    const imageContainer = document.getElementById('image-container');
    const addImageBtn = document.getElementById('add-image-btn');
    function addImageRow() {
        const row = document.createElement('div');
        row.className = 'image-row mb-3';
        row.innerHTML = `<div class="row"><div class="col-10"><div class="custom-file"><input type="file" class="custom-file-input" name="images[]" required><label class="custom-file-label">Chọn file...</label></div><input type="text" name="images_alt[]" class="form-control mt-2" placeholder="Văn bản thay thế (ALT)"></div><div class="col-2 d-flex align-items-center"><button type="button" class="btn btn-sm btn-danger remove-image-btn"><i class="fas fa-trash"></i></button></div></div>`;
        imageContainer.appendChild(row);
    }
    addImageBtn.addEventListener('click', addImageRow);
    imageContainer.addEventListener('click', function(e) {
        if (e.target.closest('.remove-image-btn')) { e.target.closest('.image-row').remove(); }
    });
    imageContainer.addEventListener('change', function(e) {
        if (e.target.matches('.custom-file-input') && e.target.files.length > 0) { e.target.nextElementSibling.textContent = e.target.files[0].name; }
    });

    // Thêm sẵn 1 dòng
    addVariantRow();
    addImageRow();
});
</script>