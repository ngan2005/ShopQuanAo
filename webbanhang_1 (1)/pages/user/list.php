<?php
    require_once 'function/user_list.php'; 
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
        <div class="card">
            <div class="card-header">
                <div class="col-md-12" style="float: left;">
                    <form action="index.php" method="GET" >
                        <input type="hidden" name="page" value="user-list">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="search_keyword">Tìm kiếm</label>
                                    <input type="text" name="search_keyword" id="search_keyword" class="form-control" placeholder="Nhập họ tên hoặc email..." value="<?php echo htmlspecialchars($_GET['search_keyword'] ?? ''); ?>">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="filter_role">Vai trò</label>
                                    <select name="filter_role" id="filter_role" class="form-control">
                                        <option value="">-- Tất cả vai trò --</option>
                                        <?php foreach ($roles as $role): ?>
                                            <option value="<?php echo $role['role_id']; ?>" <?php if (isset($_GET['filter_role']) && $_GET['filter_role'] == $role['role_id']) echo 'selected'; ?>>
                                                <?php echo htmlspecialchars($role['role_name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="filter_status">Trạng thái nhân viên</label>
                                    <select name="filter_status" id="filter_status" class="form-control">
                                        <option value="">-- Tất cả trạng thái --</option>
                                        <option value="active" <?php if (isset($_GET['filter_status']) && $_GET['filter_status'] == 'active') echo 'selected'; ?>>Đang làm</option>
                                        <option value="inactive" <?php if (isset($_GET['filter_status']) && $_GET['filter_status'] == 'inactive') echo 'selected'; ?>>Đã nghỉ</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <div class="form-group w-100">
                                    <div class="row">
                                        <div class="col-md-6" style="float: left;">
                                            <button type="submit" class="btn btn-primary w-100"><i class="fas fa-filter"></i> Lọc </button>
                                        </div>
                                        <div class="col-md-6">
                                            <a href="index.php?page=user-list" class="btn btn-secondary w-100"><i class="fas fa-sync-alt"></i> Reset</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card-body">               

                <table id="usersTable" class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Img</th>
                            <th>Họ tên</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>SĐT</th>
                            <th>Địa chỉ</th>
                            <th>Trạng thái</th>
                            <th>Đăng nhập lần cuối</th>
                            <th>
                                <a href="index.php?page=user-add" class="btn btn-success w-100">
                                    <i class="fas fa-plus"></i> Thêm mới
                                </a>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?php echo $user['user_id']; ?></td>
                                <td>
                                    <?php $avatar_url = !empty($user['image_url']) ? $user['image_url'] : 'assets/dist/img/avatar.png'; ?>
                                    <img src="<?php echo $avatar_url; ?>" alt="<?php echo htmlspecialchars($user['full_name']); ?>" width="50" class="img-circle">
                                </td>
                                <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td><?php echo htmlspecialchars($user['role_name']); ?></td>
                                <td><?php echo htmlspecialchars($user['phone_number'] ?? 'Chưa có'); ?></td>
                                <td><?php echo htmlspecialchars($user['address'] ?? 'Chưa có'); ?></td>
                                <td>
                                    <?php if ($user['role_id'] == 2):?>
                                        <?php if ($user['employee_status'] === 'active'): ?>
                                            <span class="badge badge-success">Đang làm</span>
                                        <?php else: ?>
                                            <span class="badge badge-secondary">Đã nghỉ</span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        —
                                    <?php endif; ?>
                                </td>
                                <td>                                    
                                    <?php if (!empty($user['last_login'])): ?>
                                        <?php echo (new DateTime($user['last_login']))->format('H:i:s d-m-Y'); ?>
                                    <?php else: ?>
                                        <span class="text-muted">Chưa đăng nhập</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="index.php?page=user-edit&id=<?php echo $user['user_id']; ?>" class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit"></i> Sửa
                                    </a>
                                    <?php if ($user['role_id'] == 2 || $user['role_id'] == 3):?>
                                        <a href="pages/user/delete.php?id=<?php echo $user['user_id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa người dùng này không?');">
                                            <i class="fas fa-trash"></i> Xóa
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="card-footer clearfix">
                <?php if (!empty($pagination_html)) {
                    echo $pagination_html;
                } ?>
            </div>
        </div>
    </div>
</section>