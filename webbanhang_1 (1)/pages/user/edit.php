<?php
// File: pages/user-edit.php

$user_id = $_GET['id'] ?? null;
if (!$user_id) {
    header('Location: index.php?page=user-list');
    exit();
}

// Lấy thông tin người dùng hiện tại
$db->query('SELECT * FROM Users WHERE user_id = :id');
$db->bind(':id', $user_id);
$user = $db->single();

if (!$user) {
    // Người dùng không tồn tại
    header('Location: index.php?page=user-list');
    exit();
}

// Lấy danh sách role
$db->query('SELECT * FROM Roles');
$roles = $db->resultSet();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 1. Lấy dữ liệu
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $password = $_POST['password']; // Mật khẩu có thể để trống
    $role_id = $_POST['role_id'];
    $status = $_POST['status'];

    // 2. Validate
    if (empty($full_name)) { $errors[] = "Họ tên không được để trống."; }
    if (empty($email)) { $errors[] = "Email không được để trống."; }
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) { $errors[] = "Định dạng email không hợp lệ."; }

    // Kiểm tra email đã tồn tại 
    $db->query('SELECT user_id FROM Users WHERE email = :email AND user_id != :id');
    $db->bind(':email', $email);
    $db->bind(':id', $user_id);
    if ($db->single()) {
        $errors[] = "Email này đã được người dùng khác sử dụng.";
    }

    if (!empty($password) && strlen($password) < 6) {
        $errors[] = "Mật khẩu mới phải có ít nhất 6 ký tự.";
    }

    // 3. Nếu không có lỗi, tiến hành cập nhật
    if (empty($errors)) {
        try {
            if (!empty($password)) {
                // Nếu có nhập mật khẩu mới
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $db->query('UPDATE Users SET full_name = :full_name, email = :email, password = :password, role_id = :role_id, employee_status = :status WHERE user_id = :id');
                $db->bind(':password', $hashed_password);
            } else {
                // Nếu không nhập mật khẩu mới
                $db->query('UPDATE Users SET full_name = :full_name, email = :email, role_id = :role_id, employee_status = :status WHERE user_id = :id');
            }
            
            $db->bind(':full_name', $full_name);
            $db->bind(':email', $email);
            $db->bind(':role_id', $role_id);
            $db->bind(':status', $status);
            $db->bind(':id', $user_id);

            if ($db->execute()) {
                $_SESSION['success_message'] = "Cập nhật thông tin người dùng thành công!";
                header('Location: index.php?page=user-list');
                exit();
            } else {
                $errors[] = "Có lỗi xảy ra, không thể cập nhật.";
            }
        } catch (PDOException $e) {
            $errors[] = "Lỗi CSDL: " . $e->getMessage();
        }
    }
}
?>

<section class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1>Chỉnh sửa thông tin</h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="index.php">Trang chủ</a></li>
          <li class="breadcrumb-item active">Chỉnh sửa thông tin</li>
        </ol>
      </div>
    </div>
  </div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Thông tin người dùng #<?php echo $user['user_id']; ?></h3>
            </div>
            <form method="post">
                <div class="card-body">
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <?php foreach ($errors as $error): ?>
                                <p><?php echo $error; ?></p>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <div class="form-group">
                        <label for="full_name">Họ và tên</label>
                        <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Mật khẩu mới</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Để trống nếu không muốn thay đổi">
                    </div>
                    <div class="form-group">
                        <label for="role_id">Vai trò</label>
                        <select class="form-control" id="role_id" name="role_id">
                            <?php foreach ($roles as $role): ?>
                                <option value="<?php echo $role['role_id']; ?>" <?php echo ($user['role_id'] == $role['role_id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($role['role_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="status">Trạng thái</label>
                        <select class="form-control" id="status" name="status">
                            <option value="active" <?php echo ($user['employee_status'] == 'active') ? 'selected' : ''; ?>>Hoạt động</option>
                            <option value="inactive" <?php echo ($user['employee_status'] == 'inactive') ? 'selected' : ''; ?>>Không hoạt động</option>
                        </select>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Cập nhật</button>
                    <a href="index.php?page=user-list" class="btn btn-secondary">Hủy bỏ</a>
                </div>
            </form>
        </div>
    </div>
</section>