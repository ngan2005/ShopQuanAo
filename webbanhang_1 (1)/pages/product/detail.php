<?php
// File: pages/product/detail.php

// 1. LẤY DỮ LIỆU TỪ URL VÀ DATABASE
$product_id = $_GET['id'] ?? null;
if (!$product_id) {
    header('Location: index.php?page=product-list');
    exit();
}

// Lấy thông tin sản phẩm chính
$db->query('
    SELECT p.*, c.category_name 
    FROM Products p
    JOIN Categories c ON p.category_id = c.category_id
    WHERE p.product_id = :id
');
$db->bind(':id', $product_id);
$product = $db->single();

if (!$product) {
    header('Location: index.php?page=product-list');
    exit();
}

// Lấy hình ảnh
$db->query('SELECT * FROM ProductImages WHERE product_id = :id ORDER BY image_id ASC');
$db->bind(':id', $product_id);
$images = $db->resultSet();

// Lấy biến thể
$db->query('SELECT * FROM ProductVariants WHERE product_id = :id ORDER BY price ASC');
$db->bind(':id', $product_id);
$variants = $db->resultSet();

// Lấy feedbacks
$db->query('
    SELECT f.*, u.full_name 
    FROM Feedbacks f
    JOIN Users u ON f.user_id = u.user_id
    WHERE f.product_id = :id AND f.parent_feedback_id IS NULL
    ORDER BY f.feedback_date DESC
');
$db->bind(':id', $product_id);
$feedbacks = $db->resultSet();

// hiển thị giá mặc định
$display_price = 0;
if (!empty($variants)) {
    $display_price = $variants[0]['price']; 
}

// (Tùy chọn) Hiển thị thông báo nếu có
$success_message = $_SESSION['success_message'] ?? null;
$error_message = $_SESSION['error_message'] ?? null;
unset($_SESSION['success_message'], $_SESSION['error_message']);
?>

<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Chi tiết sản phẩm</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="index.php">Trang chủ</a></li>
                    <li class="breadcrumb-item"><a href="index.php?page=product-list">Sản phẩm</a></li>
                    <li class="breadcrumb-item active"><?php echo htmlspecialchars($product['product_name']); ?></li>
                </ol>
            </div>
        </div>
    </div>
</section>

<section class="content">

    <?php if ($success_message): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo $success_message; ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>
    <?php if ($error_message): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo $error_message; ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>


    <div class="card card-solid">
        <div class="card-body">
            <div class="row">
                <div class="col-12 col-sm-6">
                    <div class="col-12"><img src="<?php echo !empty($images) ? htmlspecialchars($images[0]['image_url']) : 'assets/dist/img/default-product.png'; ?>" class="product-image" alt="Product Image"></div>
                    <div class="col-12 product-image-thumbs mt-2">
                        <?php if (!empty($images) && count($images) > 1): ?>
                            <?php foreach ($images as $index => $image): ?>
                                <div class="product-image-thumb <?php echo $index === 0 ? 'active' : ''; ?>"><img src="<?php echo htmlspecialchars($image['image_url']); ?>" alt="<?php echo htmlspecialchars($image['alt_text']); ?>"></div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="col-12 col-sm-6">
                    <h3 class="my-3"><?php echo htmlspecialchars($product['product_name']); ?></h3>
                    <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
                    <hr>

                    <div class="mt-4">
                        <a href="index.php?page=product-edit&id=<?php echo $product['product_id']; ?>" class="btn btn-info btn-lg btn-flat">
                            <i class="fas fa-edit fa-lg mr-2"></i>Chỉnh sửa sản phẩm
                        </a>
                    </div>
                    <hr>

                    <form action="index.php?page=admincart-add" method="POST">
                        <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">

                        <?php if (!empty($variants)): ?>
                            <h4 class="mt-3">Size <small>Vui lòng chọn một</small></h4>
                            <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                <?php foreach ($variants as $index => $variant): ?>
                                    <label class="btn btn-default text-center <?php echo $index === 0 ? 'active' : ''; ?>" data-price="<?php echo $variant['price']; ?>">
                                        <input type="radio" name="variant_id" value="<?php echo $variant['variant_id']; ?>" autocomplete="off" <?php echo $index === 0 ? 'checked' : ''; ?> required>
                                        <span class="text-xl"><?php echo htmlspecialchars($variant['size']); ?></span>
                                        <br>
                                        Tồn kho: <?php echo $variant['stock_quantity']; ?>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-danger">Sản phẩm này chưa có biến thể (size/giá). Không thể thêm vào giỏ.</p>
                        <?php endif; ?>

                        <div class="bg-gray py-2 px-3 mt-4">
                            <h2 class="mb-0" id="product-price">
                                <?php echo number_format($display_price, 0, ',', '.'); ?> VNĐ
                            </h2>
                        </div>

                        <div class="mt-4 row align-items-center">
                            <div class="col-md-5">
                                <label for="quantity">Số lượng:</label>
                                <input type="number" name="quantity" id="quantity" class="form-control" value="1" min="1">
                            </div>
                            <div class="col-md-7">
                                <?php if (!empty($variants)): ?>
                                <button type="submit" class="btn btn-primary btn-lg btn-flat mt-3">
                                    <i class="fas fa-cart-plus fa-lg mr-2"></i>
                                    Thêm vào giỏ
                                </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </form>
                    </div>
            </div>

            <div class="row mt-4">
                <nav class="w-100">
                    <div class="nav nav-tabs" id="product-tab" role="tablist">
                        <a class="nav-item nav-link active" id="product-desc-tab" data-toggle="tab" href="#product-desc" role="tab" aria-controls="product-desc" aria-selected="true">Mô tả chi tiết</a>
                        <a class="nav-item nav-link" id="product-comments-tab" data-toggle="tab" href="#product-comments" role="tab" aria-controls="product-comments" aria-selected="false">Bình luận (<?php echo count($feedbacks); ?>)</a>
                    </div>
                </nav>
                <div class="tab-content p-3" id="nav-tabContent">
                    <div class="tab-pane fade show active" id="product-desc" role="tabpanel" aria-labelledby="product-desc-tab">
                        <?php echo !empty($product['description']) ? nl2br(htmlspecialchars($product['description'])) : 'Sản phẩm này chưa có mô tả chi tiết.'; ?>
                    </div>
                    <div class="tab-pane fade" id="product-comments" role="tabpanel" aria-labelledby="product-comments-tab">
                        <?php if (!empty($feedbacks)): ?>
                        <?php foreach($feedbacks as $feedback): ?>
                        <div class="post">
                            <div class="user-block">
                                <span class="username ml-0"><a href="#"><?php echo htmlspecialchars($feedback['full_name']); ?></a></span>
                                <span class="description ml-0">Đã đăng lúc - <?php echo date('H:i d/m/Y', strtotime($feedback['feedback_date'])); ?></span>
                            </div>
                            <p><?php echo nl2br(htmlspecialchars($feedback['comment'])); ?></p>
                            <p>
                                <strong>Đánh giá: </strong>
                                <?php for($i = 0; $i < $feedback['rating']; $i++): ?><i class="fas fa-star text-warning"></i><?php endfor; ?>
                                <?php for($i = $feedback['rating']; $i < 5; $i++): ?><i class="far fa-star text-warning"></i><?php endfor; ?>
                            </p>
                        </div>
                        <hr>
                        <?php endforeach; ?>
                        <?php else: ?>
                        <p>Chưa có bình luận nào cho sản phẩm này.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>



<style>
  .product-image { max-width: 100%; height: auto; max-height: 500px; object-fit: contain; margin: 0 auto; display: block; }
  .product-image-thumb img { height: 80px; width: 100%; object-fit: cover; }
  .product-image-thumb { cursor: pointer; }
</style>