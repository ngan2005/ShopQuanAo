<?php
// Lấy user_id từ session
$user_id = $_SESSION['user_id'];
$message = '';
$message_type = '';

//Include hàm uploadImage
require_once 'includes/function.php';

// XỬ LÝ KHI NGƯỜI DÙNG GỬI FORM CẬP NHẬT THÔNG TIN
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update_info') {
    // 1. Lấy và làm sạch dữ liệu
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone_number = trim($_POST['phone_number']);
    $address = trim($_POST['address']);

    // 2. Validate dữ liệu
    if (empty($full_name) || empty($email)) {
        $message = 'Họ tên và Email là bắt buộc.';
        $message_type = 'danger';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'Định dạng email không hợp lệ.';
        $message_type = 'danger';
    } else {
        // 3. Kiểm tra xem email mới có bị trùng với người dùng khác không
        $db->query('SELECT user_id FROM Users WHERE email = :email AND user_id != :user_id');
        $db->bind(':email', $email);
        $db->bind(':user_id', $user_id);
        $db->execute();
        
        if ($db->rowCount() > 0) {
            $message = 'Email này đã được sử dụng bởi một tài khoản khác.';
            $message_type = 'danger';
        } else {
            // 4. Cập nhật vào cơ sở dữ liệu
            try {
                $db->query('UPDATE Users SET full_name = :full_name, email = :email, phone_number = :phone_number, address = :address WHERE user_id = :user_id');
                $db->bind(':full_name', $full_name);
                $db->bind(':email', $email);
                $db->bind(':phone_number', $phone_number);
                $db->bind(':address', $address);
                $db->bind(':user_id', $user_id);
                
                if ($db->execute()) {
                    $message = 'Cập nhật thông tin thành công!';
                    $message_type = 'success';
                    // Cập nhật lại tên trong session
                    $_SESSION['user_fullname'] = $full_name;
                } else {
                    $message = 'Đã có lỗi xảy ra, không thể cập nhật.';
                    $message_type = 'danger';
                }
            } catch (PDOException $e) {
                $message = 'Lỗi hệ thống: ' . $e->getMessage();
                $message_type = 'danger';
            }
        }
    }
}

// XỬ LÝ thay đổi ẢNH ĐẠI DIỆN
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update_avatar') {
    try {
        if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
            throw new Exception('Vui lòng chọn một tệp ảnh hợp lệ.');
        }

        // Xóa ảnh cũ (logic không đổi)
        $db->query('SELECT image_url FROM Users WHERE user_id = :user_id');
        $db->bind(':user_id', $user_id);
        $old_image = $db->single()['image_url'];
        if ($old_image && file_exists($old_image)) {
            unlink($old_image);
        }

        // Gọi hàm uploadImage từ includes/function.php
        $target_path = uploadImage($_FILES['avatar'], 'Users');

        // Cập nhật CSDL (logic không đổi)
        $db->query('UPDATE Users SET image_url = :image_url WHERE user_id = :user_id');
        $db->bind(':image_url', $target_path);
        $db->bind(':user_id', $user_id);
        if ($db->execute()) {
            $message = 'Cập nhật ảnh đại diện thành công!';
            $_SESSION['user_image_url'] = $target_path;
            $message_type = 'success';
        } else {
            throw new Exception('Lỗi khi cập nhật cơ sở dữ liệu.');
        }
    } catch (Exception $e) {
        $message = $e->getMessage();
        $message_type = 'danger';
    }
}
// LẤY Thông tin ng duungf
$db->query('SELECT u.*, r.role_name FROM Users u LEFT JOIN Roles r ON u.role_id = r.role_id WHERE u.user_id = :user_id');
$db->bind(':user_id', $user_id);
$user = $db->single();

// Lấy 5 đơn hàng gần nhất để hiển thị ở mục Activity
$db->query('SELECT order_id, order_date, total_amount, status_name FROM Orders o JOIN Order_Status os ON o.status_id = os.status_id WHERE o.user_id = :user_id ORDER BY order_date DESC LIMIT 5');
$db->bind(':user_id', $user_id);
$recent_orders = $db->resultSet();
?>

<section class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1>Hồ sơ cá nhân</h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="index.php">Trang chủ</a></li>
          <li class="breadcrumb-item active">Hồ sơ cá nhân</li>
        </ol>
      </div>
    </div>
  </div>
</section>
<section class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-3">

        <div class="card card-primary card-outline">
          <div class="card-body box-profile">
            <div class="text-center">
              <img class="profile-user-img img-fluid img-circle"
                   src="<?php echo !empty($user['image_url']) ? htmlspecialchars($user['image_url']) : 'assets/dist/img/avatar.png'; ?>"
                   alt="User profile picture">
            </div>

            <h3 class="profile-username text-center"><?php echo htmlspecialchars($user['full_name']); ?></h3>

            <p class="text-muted text-center"><?php echo htmlspecialchars($user['role_name'] ?? 'Khách hàng'); ?></p>

            <button data-toggle="modal" data-target="#avatarModal" class="btn btn-primary btn-block"><b>Đổi ảnh đại diện</b></button>
          </div>
        </div>

        <div class="card card-primary">
          <div class="card-header">
            <h3 class="card-title">Thông tin thêm</h3>
          </div>
          <div class="card-body">
            <strong><i class="fas fa-map-marker-alt mr-1"></i> Địa chỉ</strong>
            <p class="text-muted"><?php echo htmlspecialchars($user['address'] ?? 'Chưa cập nhật'); ?></p>
            <hr>
            <strong><i class="fas fa-phone-alt mr-1"></i> Số điện thoại</strong>
            <p class="text-muted"><?php echo htmlspecialchars($user['phone_number'] ?? 'Chưa cập nhật'); ?></p>
            <hr>
            <strong><i class="far fa-clock mr-1"></i> Đăng nhập lần cuối</strong>
            <p class="text-muted"><?php echo !empty($user['last_login']) ? date('H:i d/m/Y', strtotime($user['last_login'])) : 'Chưa đăng nhập'; ?></p>
          </div>
        </div>
      </div>

      <div class="col-md-9">
        <?php if ($message): ?>
        <div class="alert alert-<?php echo $message_type; ?> alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <?php echo $message; ?>
        </div>
        <?php endif; ?>
        <div class="card">
          <div class="card-header p-2">
            <ul class="nav nav-pills">
              <li class="nav-item"><a class="nav-link active" href="#activity" data-toggle="tab">Hoạt động</a></li>
              <li class="nav-item"><a class="nav-link" href="#settings" data-toggle="tab">Cài đặt</a></li>
            </ul>
          </div>
          <div class="card-body">
            <div class="tab-content">
              <div class="active tab-pane" id="activity">
                <?php if (!empty($recent_orders)): ?>
                    <?php foreach($recent_orders as $order): ?>
                    <div class="post">
                      <div class="user-block">
                        <span class="username ml-0">
                          <a href="#">Đơn hàng #<?php echo $order['order_id']; ?></a>
                        </span>
                        <span class="description ml-0">Đặt lúc - <?php echo date('H:i d/m/Y', strtotime($order['order_date'])); ?></span>
                      </div>
                      <p>
                        Tổng tiền: <strong><?php echo number_format($order['total_amount'], 0, ',', '.'); ?> VNĐ</strong>
                        <br>
                        Trạng thái: <span class="badge badge-info"><?php echo htmlspecialchars($order['status_name']); ?></span>
                      </p>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Chưa có hoạt động nào.</p>
                <?php endif; ?>
              </div>

              <div class="tab-pane" id="settings">
                <form class="form-horizontal" method="POST" action="">
                  <input type="hidden" name="action" value="update_info">
                  
                  <div class="form-group row">
                    <label for="inputName" class="col-sm-2 col-form-label">Họ và tên</label>
                    <div class="col-sm-10">
                      <input type="text" class="form-control" id="inputName" name="full_name" placeholder="Họ và tên" value="<?php echo htmlspecialchars($user['full_name']); ?>">
                    </div>
                  </div>
                  
                  <div class="form-group row">
                    <label for="inputEmail" class="col-sm-2 col-form-label">Email</label>
                    <div class="col-sm-10">
                      <input type="email" class="form-control" id="inputEmail" name="email" placeholder="Email" value="<?php echo htmlspecialchars($user['email']); ?>">
                    </div>
                  </div>

                  <div class="form-group row">
                    <label for="inputPhone" class="col-sm-2 col-form-label">Số điện thoại</label>
                    <div class="col-sm-10">
                      <input type="text" class="form-control" id="inputPhone" name="phone_number" placeholder="Số điện thoại" value="<?php echo htmlspecialchars($user['phone_number']); ?>">
                    </div>
                  </div>

                  <div class="form-group row">
                    <label for="inputAddress" class="col-sm-2 col-form-label">Địa chỉ</label>
                    <div class="col-sm-10">
                      <textarea class="form-control" id="inputAddress" name="address" placeholder="Địa chỉ"><?php echo htmlspecialchars($user['address']); ?></textarea>
                    </div>
                  </div>
                  
                  <div class="form-group row">
                    <div class="offset-sm-2 col-sm-10">
                      <button type="submit" class="btn btn-danger">Lưu thay đổi</button>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<div class="modal fade" id="avatarModal" tabindex="-1" role="dialog" aria-labelledby="avatarModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST" action="" enctype="multipart/form-data">
                <input type="hidden" name="action" value="update_avatar">
                <div class="modal-header">
                    <h5 class="modal-title" id="avatarModalLabel">Cập nhật ảnh đại diện</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="avatar">Chọn ảnh mới</label>
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="avatar" name="avatar" required>
                                <label class="custom-file-label" for="avatar">Chọn tệp</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Script để hiển thị tên file khi được chọn trong custom file input
document.addEventListener('DOMContentLoaded', function () {
    var fileInput = document.querySelector('.custom-file-input');
    if(fileInput) {
        fileInput.addEventListener('change', function(e) {
            var fileName = e.target.files[0].name;
            var nextSibling = e.target.nextElementSibling;
            nextSibling.innerText = fileName;
        });
    }
});
</script>