<?php
require_once 'includes/function.php'; // Nạp file chứa các hàm giỏ hàng

// gọi đến các hàm lấy nội dung giỏ hàng và tổng tiền trong file function.php
$cart_items = get_cart_contents();
$cart_total = get_cart_total();

$success_message = $_SESSION['success_message'] ?? null;
unset($_SESSION['success_message']);
?>

<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Giỏ hàng của tôi</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="index.php">Trang chủ</a></li>
                    <li class="breadcrumb-item active">Giỏ hàng</li>
                </ol>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Chi tiết giỏ hàng</h3>
            </div>
            <div class="card-body p-0">                
                <?php if ($success_message): ?>
                    <div class="alert alert-success m-3"><?php echo $success_message; ?></div>
                <?php endif; ?>

                <?php if (empty($cart_items)): ?>
                    <p class="text-center p-4">Giỏ hàng của bạn đang trống.</p>
                <?php else: ?>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th style="width: 10%">Ảnh</th>
                                <th>Sản phẩm</th>
                                <th>Size</th>
                                <th style="width: 15%">Giá</th>
                                <th style="width: 15%">Số lượng</th>
                                <th style="width: 15%">Thành tiền</th>
                                <th style="width: 10%">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cart_items as $item): ?>
                                <tr>
                                    <td>
                                        <img src="<?php echo htmlspecialchars($item['image_url']); ?>" 
                                             alt="<?php echo htmlspecialchars($item['product_name']); ?>" 
                                             width="60" 
                                             style="object-fit: cover; height: 60px; border-radius: 4px;">
                                    </td>
                                    <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                                    <td><?php echo htmlspecialchars($item['size']); ?></td>
                                    <td><?php echo number_format($item['price'], 0, ',', '.'); ?> ₫</td>
                                    <td>
                                        <form action="index.php?page=admincart-update" method="POST" class="d-flex">
                                            <input type="hidden" name="variant_id" value="<?php echo $item['variant_id']; ?>">
                                            <input type="number" 
                                                   name="quantity" 
                                                   class="form-control form-control-sm" 
                                                   value="<?php echo $item['quantity']; ?>" 
                                                   min="0" 
                                                   style="width: 70px;">
                                            <button type="submit" class="btn btn-sm btn-outline-primary ml-1" title="Cập nhật">
                                                <i class="fas fa-sync-alt"></i>
                                            </button>
                                        </form>
                                    </td>
                                    <td>
                                        <strong>
                                            <?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?> ₫
                                        </strong>
                                    </td>
                                    <td>
                                        <a href="index.php?page=admincart-remove&variant_id=<?php echo $item['variant_id']; ?>" 
                                           class="btn btn-sm btn-danger" 
                                           onclick="return confirm('Bạn có chắc muốn xóa sản phẩm này khỏi giỏ hàng?');"
                                           title="Xóa">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="5" class="text-right"><strong>Tổng cộng:</strong></td>
                                <td colspan="2">
                                    <h4 class="text-danger">
                                        <strong><?php echo number_format($cart_total, 0, ',', '.'); ?> ₫</strong>
                                    </h4>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                <?php endif; ?>
            </div>
            
            <?php if (!empty($cart_items)): ?>
            <div class="card-footer d-flex justify-content-between">
                <div>
                    <a href="index.php?page=product-list" class="btn btn-secondary">
                        <i class="fas fa-plus"></i> Thêm sản phẩm khác
                    </a>
                    <a href="index.php?page=admincart-remove&action=clearall" class="btn btn-outline-danger ml-2" onclick="return confirm('Bạn có chắc muốn xóa TOÀN BỘ giỏ hàng?');">
                        <i class="fas fa-trash-alt"></i> Xóa hết giỏ hàng
                    </a>
                </div>
                <a href="index.php?page=order-create" class="btn btn-success btn-lg">
                    <i class="fas fa-check"></i> Tạo đơn hàng
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>