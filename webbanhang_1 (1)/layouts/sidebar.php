<?php
// Lấy role_id của người dùng để kiểm tra, nếu không có thì mặc định là 0 (không có quyền)
$user_role_id = isset($_SESSION['user_role_id']) ? $_SESSION['user_role_id'] : 0;
?>
<aside class="main-sidebar sidebar-dark-primary elevation-4">
  <a href="index.php" class="brand-link">
    <img src="assets/dist/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
    <span class="brand-text font-weight-light">TRANG QUẢN TRỊ</span>
  </a>

  <div class="sidebar">
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <!-- Điều kiện hiển thị chung cho Staff và Admin -->
        <?php if ($user_role_id == 1 || $user_role_id == 2): ?> 
          
          <li class="nav-item">
            <a href="index.php" class="nav-link">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>Dashboard</p>
            </a>
          </li>

          <li class="nav-header">QUẢN LÝ SẢN PHẨM</li>
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-box-open"></i>
              <p>
                Quản lý Sản phẩm
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="index.php?page=product-list" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Danh sách Sản phẩm</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="index.php?page=category-list" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Quản lý Danh mục</p>
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-file-invoice-dollar"></i>
              <p>
                Quản lý Đơn hàng
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="index.php?page=order-list" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Danh sách Đơn hàng</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="index.php?page=order-status-list" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Trạng thái Đơn hàng</p>
                </a>
              </li>
            </ul>
          </li>        
        <li class="nav-item">
          <a href="index.php?page=promotion-list" class="nav-link">
            <i class="nav-icon fas fa-tags"></i>
            <p>Quản lý Khuyến mại</p>
          </a>
        </li>
        <?php endif; ?> <!-- kêt thúc điều kiện hiển thị chung cho Staff và Admin-->

        <!-- Chỉ hiển thị nếu là Admin -->
        <?php if ($user_role_id == 1): ?>
          <li class="nav-header">QUẢN LÝ HỆ THỐNG</li>
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-users"></i>
              <p>
                Quản lý User
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="index.php?page=user-list" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Danh sách User</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="index.php?page=role-list" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Quản lý Vai trò</p>
                </a>
              </li>
            </ul>
          </li>

          <li class="nav-item">
            <a href="index.php?page=feedback-list" class="nav-link">
              <i class="nav-icon fas fa-comments"></i>
              <p>Quản lý Comment</p>
            </a>
          </li>

          <li class="nav-header">THỐNG KÊ & BÁO CÁO</li>
          <li class="nav-item">
            <a href="index.php?page=report-revenue" class="nav-link">
              <i class="nav-icon fas fa-chart-line"></i>
              <p>Báo cáo Doanh thu</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="index.php?page=report-products" class="nav-link">
              <i class="nav-icon fas fa-chart-bar"></i>
              <p>Sản phẩm bán chạy</p>
            </a>
          </li>
        <?php endif; ?> <!-- Kết thúc điều kiện hiển thị cho Admin -->
      </ul>
    </nav>
  </div>
</aside>