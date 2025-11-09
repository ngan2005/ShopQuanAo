<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 1. Lấy dữ liệu từ form
    $category_name = trim($_POST['category_name']);
    $description = trim($_POST['description']);
    $image_url = $_POST['password'];
    $role_id = $_POST['role_id'];
    $status = $_POST['status'];

    // 2. Validate dữ liệu
    if (empty($category_name)) { $errors[] = "Họ tên không được để trống."; }
    if (empty($email)) { $errors[] = "Email không được để trống."; }
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) { $errors[] = "Định dạng email không hợp lệ."; }
    if (empty($password)) { $errors[] = "Mật khẩu không được để trống."; }
    elseif (strlen($password) < 6) { $errors[] = "Mật khẩu phải có ít nhất 6 ký tự."; }

    // Kiểm tra email đã tồn tại chưa
    $db->query('SELECT user_id FROM Users WHERE email = :email && pass = :pass');
    $db->bind(':email', $email);
    $db->bind(':pass', $password);
    if ($db->single()) {
        $errors[] = "Email này đã được sử dụng.";
    }

    // Thêm vào database
    if (empty($errors)) {
        try {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            $db->query('INSERT INTO Users (category_name, email, password, role_id, employee_status, created_at) VALUES (:category_name, :email, :password, :role_id, :status, NOW())');
            $db->bind(':category_name', $category_name);
            $db->bind(':email', $email);
            $db->bind(':password', $hashed_password);
            $db->bind(':role_id', $role_id);
            $db->bind(':status', $status);

            if ($db->execute()) {
                // Lưu thông báo thành công vào session và chuyển hướng
                $_SESSION['success_message'] = "Thêm người dùng thành công!";
                header('Location: index.php?page=user-list');

                exit();
            } else {
                $errors[] = "Có lỗi xảy ra, không thể thêm người dùng.";
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
        <h1>Thêm người dùng mới</h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="index.php">Trang chủ</a></li>
          <li class="breadcrumb-item active">Thêm người dùng mới</li>
        </ol>
      </div>
    </div>
  </div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Thông tin người dùng</h3>
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
                        <label for="category_name">Họ và tên</label>
                        <input type="text" class="form-control" id="category_name" name="category_name" placeholder="Nhập họ tên" value="<?php echo htmlspecialchars($category_name); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="Nhập email" value="<?php echo htmlspecialchars($email); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Mật khẩu</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Nhập mật khẩu (tối thiểu 6 ký tự)" required>
                    </div>
                    <div class="form-group">
                        <label for="role_id">Vai trò</label>
                        <select class="form-control" id="role_id" name="role_id">
                            <?php foreach ($roles as $role): ?>
                                <option value="<?php echo $role['role_id']; ?>"><?php echo htmlspecialchars($role['role_name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="status">Trạng thái</label>
                        <select class="form-control" id="status" name="status">
                            <option value="active">Hoạt động</option>
                            <option value="inactive">Không hoạt động</option>
                        </select>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Thêm mới</button>
                    <a href="index.php?page=user-list" class="btn btn-secondary">Hủy bỏ</a>
                </div>
            </form>
        </div>
    </div>
</section>