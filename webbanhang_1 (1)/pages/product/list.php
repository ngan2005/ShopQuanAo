<?php
require_once 'function/product_list.php';
?>
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Quản lý sản phẩm</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="index.php">Trang chủ</a></li>
                    <li class="breadcrumb-item active">Danh sách sản phẩm</li>
                </ol>
            </div>
        </div>
    </div>
</section>
<section class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <form action="index.php" method="GET" class="mb-0">
                    <input type="hidden" name="page" value="product-list">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="search_keyword">Tìm kiếm sản phẩm</label>
                                <input type="text" name="search_keyword" id="search_keyword" class="form-control" placeholder="Nhập tên sản phẩm..." value="<?php echo htmlspecialchars($_GET['search_keyword'] ?? ''); ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="filter_category">Danh mục</label>
                                <select name="filter_category" id="filter_category" class="form-control">
                                    <option value="">-- Tất cả danh mục --</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?php echo $category['category_id']; ?>" <?php if (isset($_GET['filter_category']) && $_GET['filter_category'] == $category['category_id']) echo 'selected'; ?>>
                                            <?php echo htmlspecialchars($category['category_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <div class="form-group w-100">
                                <button type="submit" class="btn btn-primary w-50"><i class="fas fa-filter"></i> Lọc</button>
                                <a href="index.php?page=product-list" class="btn btn-secondary w-auto"><i class="fas fa-sync-alt"></i> Reset</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="card-body">
                <?php if ($success_message): ?>
                    <div class="alert alert-success"><?php echo $success_message; ?></div>
                <?php endif; ?>

                <table id="productsTable" class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Ảnh</th>
                            <th>Tên sản phẩm</th>
                            <th>Danh mục</th>
                            <th>Biến thể</th>
                            <th>Tổng tồn</th>
                            <th>Giá từ</th>
                            <th>Người tạo</th>
                            <th>
                                <a href="index.php?page=product-add" class="btn btn-success w-100">
                                    <i class="fas fa-plus"></i> Thêm mới
                                </a>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($products)): ?>
                            <tr>
                                <td colspan="9" class="text-center">Không tìm thấy sản phẩm nào.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($products as $product): ?>
                                <tr>
                                    <td><?php echo $product['product_id']; ?></td>
                                    <td>
                                        <?php
                                        $images = []; // Khởi tạo mảng rỗng

                                        // Kiểm tra xem $product['all_images'] có tồn tại và không rỗng không
                                        if (!empty($product['all_images'])) {
                                            // Tách chuỗi hình ảnh (phân cách bằng dấu phẩy) thành một mảng
                                            $images = explode(',', $product['all_images']);
                                        }

                                        if (empty($images)) {
                                            // TRƯỜNG HỢP 1: Không có ảnh, hiển thị ảnh mặc định MỚI
                                        ?>
                                            <img src="assets/dist/img/prod-1.jpg"
                                                alt="<?php echo htmlspecialchars($product['product_name']); ?>"
                                                width="60"
                                                style="object-fit: cover; height: 60px; border-radius: 4px;">
                                            <?php
                                        } else {
                                            // TRƯỜNG HỢP 2: Có ảnh, lấy tối đa 3 ảnh
                                            $images_to_show = array_slice($images, 0, 3);

                                            // Lặp qua 3 ảnh (hoặc ít hơn) và hiển thị
                                            foreach ($images_to_show as $img_url) {
                                            ?>
                                                <img src="<?php echo htmlspecialchars(trim($img_url)); ?>"
                                                    alt="<?php echo htmlspecialchars($product['product_name']); ?>"
                                                    width="60"
                                                    style="object-fit: cover; height: 60px; margin-right: 5px; margin-bottom: 5px; border-radius: 4px;">
                                        <?php
                                            }
                                        }
                                        ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($product['product_name']); ?></td>
                                    <td><?php echo htmlspecialchars($product['category_name']); ?></td>
                                    <td><?php echo $product['variant_count'] . '<br>' . htmlspecialchars($product['available_sizes'] ?? 'N/A'); ?></td>
                                    <td><?php echo $product['total_stock']; ?></td>
                                    <td><?php echo number_format($product['min_price'] ?? 0, 0, ',', '.') . ' ₫'; ?></td>
                                    <td><?php echo $product['full_name'] . '<br>' . (new DateTime($product['created_at']))->format('d-m-Y'); ?></td>
                                    <td>
                                        <a href="index.php?page=product-edit&id=<?php echo $product['product_id']; ?>" class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i> Sửa
                                        </a>
                                        <a href="index.php?page=product-detail&id=<?php echo $product['product_id']; ?>" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> Xem
                                        </a>
                                        <a href="pages/product/delete.php?id=<?php echo $product['product_id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này và tất cả các biến thể liên quan không?');">
                                            <i class="fas fa-trash"></i> Xóa
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            </div>
    </div>
</section>