<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <!-- Using a more reliable Font Awesome CDN link -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link rel="stylesheet" href="type.css">
  <title>160STORE - Chuỗi Phân Phối Thời Trang Nam Chuẩn Hiệu</title>
</head>
<body>
  <?php 
    require_once 'data.php'; 
  ?>
  <header class="top-header">  
     
    <!-- Logo -->

    <div class="logo">
      <img src="https://file.hstatic.net/1000253775/file/logo_no_bf-05_3e6797f31bda4002a22464d6f2787316.png" alt="Logo">
    </div>
    <!-- Thanh tìm kiếm -->
      <div class="search-box">
        <input type="text" placeholder="Bạn đang tìm gì...">
        <button><i class="fas fa-search"></i></button>
      </div>
      <!-- Icon -->
      <div class="header-icons">
        <a href="#" onclick="location.reload(); return false;"><i class="fas fa-store"></i> Cửa hàng</a>
        <a href="/ShopThoiTrang/GiaoDien/dangNhap.html"><i class="fas fa-user"></i> Đăng nhập</a>
        <a href="/ShopThoiTrang/GiaoDien/gioHang.html"><i class="fas fa-shopping-cart"></i> Giỏ hàng</a>
      </div>
  </header>
<nav>
      <!-- Menu -->
      <ul class="menu">
        <li><a href="#hangmoi">Hàng Mới</a>
    <!--- -->
        </li>
        <li><a href="#">Sản Phẩm</a>
          <ul class ="submenu">
            <li><a href="#">Combo</a>
              <ul>
                <li><a href="/ShopThoiTrang/GiaoDien/danhSachCombo.html">Combo Mix & Match</a></li>
              </ul>
            </li>
            <!--- -->
            <li><a href="#">Áo</a>
              <ul>
                <li><a href="/ShopThoiTrang/GiaoDien/danhSachAoThun.html">Áo thun</a></li>
                <li><a href="/ShopThoiTrang/GiaoDien/aoPoLo.html">Áo Polo</a></li>
                <li><a href="/ShopThoiTrang/GiaoDien/soMiTayNgan.html">Áo sơ mi</a></li>
              </ul>
            </li>
            <!--- -->
            <li><a href="#">Quần</a>
              <ul>
                <li><a href="/ShopThoiTrang/GiaoDien/quanShort_Jean.html">Quần short jean</a></li>
                <li><a href="/ShopThoiTrang/GiaoDien/quanDaiNau.html">Quần dài kaki</a></li>
                <li><a href="/ShopThoiTrang/GiaoDien/quanShort_kaki.html">Quần short kaki</a></li>
              </ul>
            </li>
            <!--- -->
            </li>
            <li><a href="#">Phụ Kiện</a>
              <ul>
                <li><a href="/ShopThoiTrang/GiaoDien/vo1.html">Vớ</a></li>
                <li><a href="/ShopThoiTrang/GiaoDien/danhSachNon.html">Mũ</a></li>
                <li><a href="/ShopThoiTrang/GiaoDien/danhSachMatKinh.html">Mắt kính</a></li>
                <li><a href="/ShopThoiTrang/GiaoDien/depQuaiNgang.html">Giày</a></li>
                <li><a href="/ShopThoiTrang/GiaoDien/thatLung.html">Thắt lưng</a></li>
                <li><a href="/ShopThoiTrang/GiaoDien/danhSachTui.html">Túi xách</a></li>
              </ul>
            </li>
        <!--- -->
      </ul>   
      <!--- -->   
      <li><a href="#">Áo Nam</a>
        <ul>    
          <li><a href="/ShopThoiTrang/GiaoDien/danhSachAoThun.html">Áo thun</a></li>
          <li><a href="/ShopThoiTrang/GiaoDien/aoPoLo.html">Áo Polo</a></li>
          <li><a href="/ShopThoiTrang/GiaoDien/soMiTayNgan.html">Áo sơ mi</a></li>
        </ul>
    <!--- -->
      <li><a href="#">Quần Nam</a>
        <ul>
          <li><a href="/ShopThoiTrang/GiaoDien/quanShort_Jean.html">Quần short jean</a></li>
          <li><a href="/ShopThoiTrang/GiaoDien/quanDaiNau.html">Quần dài kaki</a></li>
          <li><a href="/ShopThoiTrang/GiaoDien/quanShort_kaki.html">Quần short kaki</a></li>
        </ul>
       <!--- --> 
      <li><a href="#">Phụ Kiện</a>
        <ul>
          <li><a href="/ShopThoiTrang/GiaoDien/vo1.html">Vớ</a></li>
          <li><a href="/ShopThoiTrang/GiaoDien/danhSachNon.html">Mũ</a></li>
          <li><a href="/ShopThoiTrang/GiaoDien/danhSachMatKinh.html">Mắt kính</a></li>
          <li><a href="/ShopThoiTrang/GiaoDien/depQuaiNgang.html">Giày</a></li>
          <li><a href="/ShopThoiTrang/GiaoDien/thatLung.html">Thắt lưng</a></li>
          <li><a href="/ShopThoiTrang/GiaoDien/danhSachTui.html">Túi xách</a></li>
        </ul>
      <!--- --> 
</nav>
    <div class="slider">
       <div class="slides">
          <div class="slide"><img src="https://file.hstatic.net/1000253775/file/banner_pc_3688a7ee993a48a3aa2ceda425abfa7b.jpg" alt="" width="1150"></div>
          <div class="slide"><img src="https://cdn.hstatic.net/files/1000253775/file/store_160_dk.jpg" alt="" width="1150"></div>
      </div>
      <button class="prev" onclick="moveSlide(-1)">❮</button>
      <button class="next" onclick="moveSlide(1)">❯</button>
    </div>

  <!-- Slider Script chuyển tag hình -->
  <script>
    let currentSlide = 0;
    function moveSlide(step) {
      const slides = document.querySelectorAll(".slide");
      currentSlide = (currentSlide + step + slides.length) % slides.length;
      document.querySelector(".slides").style.transform =`translateX(-${currentSlide * 100}%)`;
    }

    // Tự động chuyển slide sau mỗi 3 giây
    setInterval(function() {
      moveSlide(1);
    }, 3000);
  </script>  
  <!--- -->

<blockquote><h3>Ưu Đãi Dành Cho Bạn</h3></blockquote>

<div class="voucher-list">
  <div class="voucher">
    <div class="voucher-code"><span>Mã giảm giá:</span> <strong>SEP9</strong></div>
    <div class="voucher-info">Giảm 0đ cho đơn hàng từ 9.000đ<br><small>Áp dụng đến 30/09/2025</small></div>
    <button class="copy-btn" onclick="copyVoucher('SEP9')">Sao chép mã</button>
  </div>
  <div class="voucher">
    <div class="voucher-code"><span>Mã giảm giá:</span> <strong>SEP30</strong></div>
    <div class="voucher-info">Giảm 30k cho đơn từ 299.000đ<br><small>Áp dụng đến 30/09/2025</small></div>
    <button class="copy-btn" onclick="copyVoucher('FREESHIP')">Sao chép mã</button>
  </div>
  <div class="voucher">
    <div class="voucher-code"><span>Mã giảm giá:</span> <strong>SEP50</strong></div>
    <div class="voucher-info">Giảm 50K cho đơn từ 599.000đ<br><small>Áp dụng đến 30/09/2025</small></div>
    <button class="copy-btn" onclick="copyVoucher('SALE20')">Sao chép mã</button>
  </div>
  <div class="voucher">
    <div class="voucher-code"><span>Mã giảm giá:</span> <strong>NEWMEM</strong></div>
    <div class="voucher-info">Giảm 50.000đ cho khách mới<br><small>Áp dụng đến 30/09/2025</small></div>
    <button class="copy-btn" onclick="copyVoucher('NEWMEM')">Sao chép mã</button>
  </div>
  <div class="voucher">
    <div class="voucher-code"><span>Mã giảm giá:</span> <strong>HOTDEAL</strong></div>
    <div class="voucher-info">Giảm 15% cho sản phẩm HOT<br><small>Áp dụng đến 30/09/2025</small></div>
    <button class="copy-btn" onclick="copyVoucher('HOTDEAL')">Sao chép mã</button>
  </div>
  <div class="voucher">
    <div class="voucher-code"><span>Mã giảm giá:</span> <strong>DISNEY10</strong></div>
    <div class="voucher-info">Giảm 10% cho sản phẩm Disney<br><small>Áp dụng đến 30/09/2025</small></div>
    <button class="copy-btn" onclick="copyVoucher('DISNEY10')">Sao chép mã</button>
  </div>
</div>
<script>
  function copyVoucher(code) {
    navigator.clipboard.writeText(code);
    alert('Đã sao chép mã giảm giá: ' + code);
  }
</script>
  <!--- -->
  <div class="center-image" id="hangmoi">
  <img src="https://file.hstatic.net/1000253775/file/banner_h_ng_m_i_6__1_.jpg" alt="hình mới" width="1150">
  </div>
  <!--- -->
  <div class="product-list">
    <?php
      $products = array_slice(get_products(), 0, 8); // Get first 8 products for demo
      foreach ($products as $product):
    ?>
      <div class="product-card">
        <?php if ($product['is_new']): ?>
          <span class="new-icon">Hàng Mới</span>
        <?php endif; ?>
        <a href="<?php echo htmlspecialchars($product['url']); ?>">
          <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" width="300">
          <div class="product-info">
            <h4><?php echo htmlspecialchars($product['name']); ?></h4>
            <p class="price">Giá: 
              <span class="new-price"><?php echo format_price($product['price']); ?></span> 
              <span class="old-price">~<?php echo format_price($product['old_price']); ?></span>
            </p>
          </div>
        </a>
      </div>
    <?php endforeach; ?>
</div>

    <!-- combo mix & match -->
    <h2>COMBO MIX & MATCH ĐÚNG CHUẨN</h2>
    <div class="product-list">
      <?php
        $combos = get_combos();
        foreach ($combos as $combo):
      ?>
        <div class="product-card">
          <?php if ($combo['is_new']): ?>
            <span class="new-icon">Hàng Mới</span>
          <?php endif; ?>
          <a href="<?php echo htmlspecialchars($combo['url']); ?>">
            <img src="<?php echo htmlspecialchars($combo['image']); ?>" alt="<?php echo htmlspecialchars($combo['name']); ?>" width="300">
            <div class="product-info">
              <h4><?php echo htmlspecialchars($combo['name']); ?></h4>
              <p class="price">Giá: 
                <span class="new-price"><?php echo format_price($combo['price']); ?></span> 
                <span class="old-price">~<?php echo format_price($combo['old_price']); ?></span>
              </p>
            </div>
          </a>
        </div>
      <?php endforeach; ?>
    </div>
<!-- combo mix -->

<!-- logo vận chuyển  hàng hóa-->
 <footer>
  <div class="newsletter">
    <h3>Đăng ký nhận bản tin</h3>
    <input type="email" placeholder="Email">
    <button>
      <i class="fas fa-plane"></i>
      Đăng ký</button>
  </div>

  <div class="footer-container">
    <!-- Giới thiệu -->
    <div class="footer-col">
      <h3>Giới thiệu</h3>
      <p>160STORE - Chuỗi Phân Phối Thời Trang Nam Chuẩn Hiệu</p>
      <p>📞 0367196252</p>
      <p>📧 luu.kimngan205@gmail.com</p>
      <p>🕒 08:30 - 22:00</p>
      <p>🎧 Hỗ trợ tin nhắn đến 24:00</p>
      <img src="https://file.hstatic.net/200000397757/file/dathongbao_48067cd02fae41b68bf0294777c39c94_compact.png" alt="Bộ Công Thương" width="100">
      <img src="https://images.dmca.com/Badges/dmca_protected_16_120.png?ID=9049de26-d97b-48dc-ab97-1e5fcb221fba" alt="DMCA" width="100">
    </div>

    <!-- Chính sách -->
    <div class="footer-col">
      <h3>Chính sách</h3>
      <ul>
        <li><a href="#">Hướng dẫn đặt hàng</a></li>
        <li><a href="#">Chính sách</a></li>
      </ul>
    </div>

    <!-- Địa chỉ cửa hàng -->
    <div class="footer-col">
      <h3>Địa chỉ cửa hàng (23 CH)</h3>
      <p><img src="https://file.hstatic.net/1000253775/file/location_a1e4d2d625914daa90748f218350e7b7.svg" alt="Địa chỉ" width="20">
        Hồ Chí Minh (12 CH): 401 Phan Xích Long...</p>
      <p><img src="https://file.hstatic.net/1000253775/file/location_a1e4d2d625914daa90748f218350e7b7.svg" alt="Địa chỉ" width="20">
        Hà Nội (2 CH): Số 26 Phố Lê Đại Hành...</p>
    </div>

    <!-- Thanh toán -->
    <div class="footer-col">
      <h3>Phương thức thanh toán</h3>
      <img src="https://file.hstatic.net/1000253775/file/shoppy_new_18600e8ad9f64537b7e31d009457e215.jpg" alt="ShopeePay" width="50">
      <img src="https://file.hstatic.net/1000253775/file/vnpay_new_ec21c03b2f4c49689d180c8a485c8d5b.jpg" alt="VNPay" width="50">
      <img src="https://file.hstatic.net/1000253775/file/cod_new_2316cf8e29bd4a858810a3d7b9eb39e4.jpg" alt="COD" width="50">
    </div>
  </div>
  <!-- Icon mạng xã hội -->
  <div class="social">
    <a href="#"><i class="fab fa-facebook"></i></a>
    <a href="#"><i class="fab fa-instagram"></i></a>
    <a href="#"><i class="fab fa-youtube"></i></a>
    <a href="#"><i class="fab fa-zalo"></i></a>
  </div>
</footer>
<a href="tel:0367196252" class="phone-float" title="Gọi ngay">
  <i class="fas fa-phone"></i>
</a>
</body>
</html>
