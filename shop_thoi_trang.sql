-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th10 04, 2025 lúc 05:40 AM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `shop_thoi_trang`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `bien_the_san_pham`
--

CREATE TABLE `bien_the_san_pham` (
  `id_SP` char(50) NOT NULL COMMENT 'khóa ngoại, id sản phẩm',
  `id_Bien_The` int(11) NOT NULL,
  `mau_Sac` varchar(100) DEFAULT NULL COMMENT 'màu sắc sản phẩm',
  `kich_Thuoc` varchar(100) DEFAULT NULL COMMENT 'kích thước sản phẩm'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `bien_the_san_pham`
--

INSERT INTO `bien_the_san_pham` (`id_SP`, `id_Bien_The`, `mau_Sac`, `kich_Thuoc`) VALUES
('SP001', 4, 'Đen', 'S'),
('SP001', 5, 'Đen', 'M'),
('SP001', 6, 'Đen', 'XL'),
('SP002', 7, 'Đen', 'L'),
('SP002', 8, 'Đen', 'M'),
('SP002', 9, 'Đen', 'S'),
('SP002', 10, 'Đen', 'XL'),
('SP003', 11, 'Trắng', 'S'),
('SP003', 12, 'Trắng', 'M'),
('SP003', 13, 'Trắng', 'L'),
('SP003', 14, 'Trắng', 'XL'),
('SP004', 16, 'Đen', 'S'),
('SP004', 17, 'Đen', 'L'),
('SP004', 18, 'Đen', 'M'),
('SP004', 19, 'Đen', 'XL'),
('SP005', 20, 'Nâu', 'S'),
('SP005', 21, 'Nâu', 'M'),
('SP005', 22, 'Nâu', 'L'),
('SP005', 23, 'Nâu', 'XL'),
('SP006', 24, 'Đen', 'S'),
('SP006', 25, 'Đen', 'L'),
('SP006', 26, 'Đen', 'M'),
('SP006', 27, 'Đen', 'XL'),
('SP006', 28, 'Trắng', 'S'),
('SP006', 29, 'Trắng', 'L'),
('SP006', 30, 'Trắng', 'XL'),
('SP006', 31, 'Trắng', 'M'),
('SP007', 32, 'Đen', 'S'),
('SP007', 33, 'Đen', 'L'),
('SP007', 34, 'Đen', 'M'),
('SP007', 35, 'Đen', 'XL'),
('SP007', 36, 'Nâu', 'S'),
('SP007', 37, 'Nâu', 'M'),
('SP007', 38, 'Nâu', 'L'),
('SP007', 39, 'Nâu', 'XL'),
('SP008', 40, 'Đen', 'S'),
('SP008', 41, 'Đen', 'L'),
('SP008', 42, 'Đen', 'M'),
('SP008', 43, 'Đen', 'XL'),
('SP011', 48, 'Đen', '39'),
('SP011', 49, 'Đen', '40'),
('SP011', 50, 'Đen', '41'),
('SP011', 51, 'Đen', '42'),
('SP012', 52, 'Đen', NULL),
('SP013', 53, 'Đen', NULL),
('SP014', 54, 'Trắng', 'S'),
('SP014', 55, 'Trắng', 'L'),
('SP014', 56, 'Trắng', 'M'),
('SP014', 57, 'Trắng', 'XL'),
('SP015', 58, 'Trắng', NULL),
('SP016', 59, 'Đen', 'S'),
('SP016', 60, 'Đen', 'L'),
('SP016', 61, 'Đen', 'XL'),
('SP016', 62, 'Đen', 'M'),
('SP017', 63, 'Trắng', NULL),
('SP018', 64, 'Đen', NULL),
('SP019', 70, 'Xanh', NULL),
('SP020', 71, 'Xám', 'S'),
('SP020', 72, 'Xám', 'L'),
('SP020', 73, 'Xám', 'M'),
('SP020', 74, 'Xám', 'XL'),
('SP021', 75, 'Trắng', 'S'),
('SP021', 76, 'Trắng', 'L'),
('SP021', 77, 'Trắng', 'M'),
('SP021', 78, 'Trắng', 'XL'),
('SP022', 79, 'Xanh', 'S'),
('SP022', 80, 'Xanh', 'L'),
('SP022', 81, 'Xanh', 'M'),
('SP022', 82, 'Xanh', 'XL'),
('SP023', 87, 'Xanh', 'S'),
('SP023', 88, 'Xanh', 'L'),
('SP023', 89, 'Xanh', 'M'),
('SP023', 90, 'Xanh', 'XL'),
('SP024', 91, 'Đen', 'S'),
('SP024', 92, 'Đen', 'L'),
('SP024', 93, 'Đen', 'XL'),
('SP024', 94, 'Đen', 'M');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `binh_luan`
--

CREATE TABLE `binh_luan` (
  `id_BL` int(11) NOT NULL COMMENT 'khóa chính, id bình luận',
  `id_BL_cha` int(11) DEFAULT NULL,
  `id_SP` char(50) NOT NULL COMMENT 'khóa ngoại, id sản phẩm',
  `id_ND` int(11) UNSIGNED NOT NULL COMMENT 'khóa ngoại, id người dùng',
  `noi_Dung` varchar(255) NOT NULL COMMENT 'nội dung',
  `so_Sao` int(11) NOT NULL COMMENT 'số sao ',
  `ngay_Binh_Luan` datetime NOT NULL COMMENT 'ngày bình luận'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `binh_luan`
--

INSERT INTO `binh_luan` (`id_BL`, `id_BL_cha`, `id_SP`, `id_ND`, `noi_Dung`, `so_Sao`, `ngay_Binh_Luan`) VALUES
(1, NULL, 'SP001', 10, 'tốt', 5, '2025-11-01 18:20:14'),
(2, NULL, 'SP002', 10, 'cx đc', 3, '2025-11-01 18:24:15'),
(3, NULL, 'SP002', 10, 'tốt', 5, '2025-11-01 18:30:38'),
(9, 2, 'SP002', 12, 'hehe', 5, '2025-11-01 22:32:34'),
(10, 2, 'SP002', 12, 'ok', 5, '2025-11-01 22:35:59'),
(11, 9, 'SP002', 7, 'cảm ơn bạn', 5, '2025-11-01 23:21:01'),
(12, 1, 'SP001', 7, 'cảm ơn bạn nha', 5, '2025-11-02 14:00:30'),
(13, NULL, 'SP022', 7, 'TỐT', 5, '2025-11-02 17:06:36'),
(14, 12, 'SP001', 10, 'ok shop', 5, '2025-11-03 22:27:00');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chi_tiet_hoa_don`
--

CREATE TABLE `chi_tiet_hoa_don` (
  `id_CTHD` int(11) NOT NULL COMMENT 'khóa chính, id chi tiết hóa đơn',
  `id_DH` int(11) NOT NULL COMMENT 'khóa ngoại, id hóa đơn',
  `so_Luong` int(11) NOT NULL COMMENT 'số lượng',
  `gia_Ban` int(11) NOT NULL COMMENT 'giá bán',
  `id_SP` char(50) NOT NULL COMMENT 'khóa ngoại, id sản phẩm'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `danh_muc`
--

CREATE TABLE `danh_muc` (
  `id_DM` int(11) NOT NULL COMMENT 'khóa chính, id danh mục',
  `ten_Danh_Muc` varchar(100) NOT NULL COMMENT 'tên danh mục'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `danh_muc`
--

INSERT INTO `danh_muc` (`id_DM`, `ten_Danh_Muc`) VALUES
(1, 'ÁO NAM'),
(2, 'QUẦN NAM'),
(3, 'PHỤ KIỆN'),
(5, 'COMBO');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `dia_chi_giao_hang`
--

CREATE TABLE `dia_chi_giao_hang` (
  `id_ND` int(10) UNSIGNED NOT NULL COMMENT 'khóa ngoại, khóa chính, id người dùng',
  `ho_Ten_Nguoi_Nhan` varchar(100) NOT NULL COMMENT 'họ tên người nhận',
  `so_Dien_Thoai` int(11) NOT NULL COMMENT 'số điện thoại người nhận',
  `dia_Chi` varchar(255) NOT NULL COMMENT 'địa chỉ người nhận'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `don_hang`
--

CREATE TABLE `don_hang` (
  `id_DH` int(11) NOT NULL COMMENT 'khóa chính, id đơn hàng',
  `id_ND` int(11) UNSIGNED NOT NULL,
  `ngay_Dat` datetime NOT NULL,
  `tong_Tien` int(11) NOT NULL,
  `trang_Thai` varchar(255) NOT NULL,
  `dia_Chi_Giao` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `giao_dich_thanh_toan`
--

CREATE TABLE `giao_dich_thanh_toan` (
  `id_GDTT` int(11) NOT NULL COMMENT 'khóa chính, id giao dịch thanh toán',
  `id_DH` int(11) NOT NULL COMMENT 'khóa ngoại, id đơn hàng',
  `hinh_Thuc_Thanh_Toan` varchar(100) NOT NULL COMMENT 'hình thức thanh toán',
  `trang_Thai` varchar(100) NOT NULL COMMENT 'trạng thái giao dich',
  `thoi_Gian` datetime NOT NULL COMMENT 'thời gian giao dich'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `gio_hang`
--

CREATE TABLE `gio_hang` (
  `id_GH` int(11) NOT NULL COMMENT 'khóa chính, id giỏ hàng',
  `id_ND` int(11) UNSIGNED NOT NULL COMMENT 'khóa ngoại, id người dùng',
  `ngay_Tao` datetime NOT NULL COMMENT 'ngày tạo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `gio_hang`
--

INSERT INTO `gio_hang` (`id_GH`, `id_ND`, `ngay_Tao`) VALUES
(0, 10, '2025-10-31 18:08:51');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `gio_hang_chi_tiet`
--

CREATE TABLE `gio_hang_chi_tiet` (
  `id_GHCT` int(11) NOT NULL COMMENT 'khóa chính, id chi tiết giỏ hàng',
  `id_GH` int(11) NOT NULL COMMENT 'khóa ngoại, id giỏ hàng',
  `id_SP` char(50) NOT NULL COMMENT 'khóa ngoại, id sản phẩm',
  `so_Luong` int(11) NOT NULL COMMENT 'số lượng',
  `ten_san_pham` varchar(255) NOT NULL COMMENT 'tên sản phẩm được thêm vào giỏ hàng',
  `mau_sac` varchar(50) DEFAULT NULL COMMENT 'màu sắc sản phẩm ',
  `kich_Thuoc` varchar(100) DEFAULT NULL COMMENT 'kích thước sản phẩm',
  `ma_Giam_Gia` varchar(255) DEFAULT NULL COMMENT 'khóa ngoại, mã giảm giá'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `lich_su_don_hang`
--

CREATE TABLE `lich_su_don_hang` (
  `id_LSDH` int(11) NOT NULL COMMENT 'khóa chính, id lịch sử đơn hàng',
  `id_DH` int(11) NOT NULL COMMENT 'id lịch sử đơn hàng',
  `trang_Thai_Cu` varchar(255) DEFAULT NULL COMMENT 'trạng thái mới ',
  `trang_Thai_Moi` varchar(255) DEFAULT NULL COMMENT 'trạng thái cũ',
  `id_ND` int(11) UNSIGNED NOT NULL COMMENT 'id người dùng ',
  `thoi_Gian` datetime NOT NULL COMMENT 'thời gian lịch sử hóa đơn'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `lich_su_tim_kiem`
--

CREATE TABLE `lich_su_tim_kiem` (
  `id_LSTT` int(11) UNSIGNED NOT NULL COMMENT 'khóa chính, id lịch sử tìm kiếm',
  `id_ND` int(11) UNSIGNED NOT NULL COMMENT 'khóa ngoại, id người dùng',
  `tu_Khoa` varchar(255) NOT NULL COMMENT 'từ khóa tìm ',
  `ngay_Tim_Kiem` datetime NOT NULL COMMENT 'ngày tìm kiếm'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `lich_su_tim_kiem`
--

INSERT INTO `lich_su_tim_kiem` (`id_LSTT`, `id_ND`, `tu_Khoa`, `ngay_Tim_Kiem`) VALUES
(1, 10, 'áo', '2025-11-02 23:26:43'),
(2, 10, 'Áo Hoodie Nam ICONDENIM Stronger Life', '2025-11-02 23:26:49'),
(3, 10, 'quần', '2025-11-02 23:30:25'),
(4, 10, 'Set đồ Nam ICONDENIM New York Cozy', '2025-11-02 23:30:44');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `ma_giam_gia`
--

CREATE TABLE `ma_giam_gia` (
  `ma_Giam_Gia` varchar(255) NOT NULL COMMENT 'khóa chính, mã giảm giá',
  `mo_Ta` varchar(255) NOT NULL COMMENT 'mô tả sản phẩm',
  `gia_Tri_Giam` decimal(10,2) NOT NULL COMMENT 'giá trị ',
  `dieu_Kien` varchar(100) NOT NULL COMMENT 'điều kiện áp dụng mã giảm ',
  `ngay_Bat_Dau` datetime NOT NULL COMMENT 'ngày bắt đầu mã giảm',
  `ngay_Ket_Thuc` datetime NOT NULL COMMENT 'ngày kết thúc mã giảm',
  `trang_Thai` varchar(255) NOT NULL COMMENT 'trạng thái mã giảm giá',
  `gia_Tri_Toi_Thieu` decimal(10,2) NOT NULL COMMENT 'giá trị tối thiểu',
  `loai_Giam` varchar(100) NOT NULL COMMENT 'loại giảm'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `ma_giam_gia`
--

INSERT INTO `ma_giam_gia` (`ma_Giam_Gia`, `mo_Ta`, `gia_Tri_Giam`, `dieu_Kien`, `ngay_Bat_Dau`, `ngay_Ket_Thuc`, `trang_Thai`, `gia_Tri_Toi_Thieu`, `loai_Giam`) VALUES
('SEP01', 'mã giảm giá áp dụng cho phụ kiện', 50000.00, 'phụ kiện', '2025-12-12 00:12:00', '2026-12-12 12:12:00', 'Đang hoạt động', 5000000.00, 'tien_mat'),
('SEP05', 'mã giảm giá dành cho sản phẩm quần áo, phụ kiện', 50000.00, 'quần áo', '2025-12-12 12:12:00', '2026-12-12 12:12:00', 'Đang hoạt động', 500000.00, 'phan_tram'),
('SEP30', 'mã giảm giá dành cho sản phẩm quần áo', 50000.00, 'quần, áo nam', '2025-12-12 12:00:00', '2026-12-05 12:30:00', 'Đang hoạt động', 500000.00, 'phan_tram');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `nguoi_dung`
--

CREATE TABLE `nguoi_dung` (
  `id_ND` int(11) UNSIGNED NOT NULL COMMENT 'khóa chính, id người dùng',
  `ten_Dang_Nhap` varchar(50) NOT NULL COMMENT 'tên đăng nhập',
  `mat_Khau` char(6) NOT NULL COMMENT 'mật khẩu',
  `ho_Ten` varchar(50) NOT NULL COMMENT 'họ tên người dùng',
  `email` char(100) NOT NULL COMMENT 'email người dùng',
  `sdt` int(10) UNSIGNED NOT NULL COMMENT 'số điện thoại',
  `dia_Chi` varchar(255) NOT NULL COMMENT 'địa chỉ người dùng',
  `vai_Tro` varchar(50) NOT NULL COMMENT 'vai trò người dùng',
  `ngay_Tao` datetime NOT NULL COMMENT 'ngày tạo tài khoản '
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `nguoi_dung`
--

INSERT INTO `nguoi_dung` (`id_ND`, `ten_Dang_Nhap`, `mat_Khau`, `ho_Ten`, `email`, `sdt`, `dia_Chi`, `vai_Tro`, `ngay_Tao`) VALUES
(7, 'admin', '123456', 'Quản trị viên', 'admin@160store.com', 367196252, 'TP.HCM', 'admin', '2025-10-26 23:31:05'),
(10, 'KHÔI', '123456', 'NGÔ MINH KHÔI', '2345677890@gmail.com', 367196252, 'TIỀN GIANG', 'khach_hang', '2025-10-30 19:24:57'),
(11, 'HẰNG', '098765', 'PHAN THỊ THÚY HẰNG', '12345678@gmail.com', 0, '', 'khach_hang', '2025-10-31 07:38:45'),
(12, 'NGÂN', '', 'LƯU THỊ KIM NGÂN', '123@gmail.com', 0, '', 'khach_hang', '2025-11-01 12:32:53'),
(13, 'TRẦN VŨ PHƯƠNG THÙY', '110320', 'THÙY', 'tranvuphuongthuy48@gmail.com', 0, '', 'khach_hang', '2025-11-03 16:25:10');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `san_pham`
--

CREATE TABLE `san_pham` (
  `id_SP` char(50) NOT NULL COMMENT 'khóa chính, id sản phẩm',
  `ten_San_Pham` varchar(100) NOT NULL COMMENT 'tên sản phẩm',
  `gia_Ban` int(11) NOT NULL COMMENT 'giá bán sản phẩm',
  `gia_Goc` int(11) NOT NULL COMMENT 'giá gốc sản phẩm ',
  `mo_Ta` varchar(255) NOT NULL COMMENT 'mô tả sản phẩm',
  `hinh_Anh` blob NOT NULL COMMENT 'hình ảnh sản phẩm',
  `id_DM` int(11) NOT NULL COMMENT 'khóa ngoại, id danh mục',
  `thuong_Hieu` varchar(255) NOT NULL COMMENT 'thương hiệu',
  `so_Luong_Ton` int(11) NOT NULL COMMENT 'số lượng hàng tồn',
  `trang_Thai` varchar(100) NOT NULL COMMENT 'trạng thái sản phẩm',
  `ngay_Tao` datetime NOT NULL COMMENT 'ngày tạo sản ',
  `ngay_Cap_Nhat` datetime NOT NULL COMMENT 'ngày cập nhật',
  `ma_Giam_Gia` varchar(100) NOT NULL COMMENT 'khóa ngoại, mã giảm giá'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `san_pham`
--

INSERT INTO `san_pham` (`id_SP`, `ten_San_Pham`, `gia_Ban`, `gia_Goc`, `mo_Ta`, `hinh_Anh`, `id_DM`, `thuong_Hieu`, `so_Luong_Ton`, `trang_Thai`, `ngay_Tao`, `ngay_Cap_Nhat`, `ma_Giam_Gia`) VALUES
('SP001', 'Áo Thun Nam ICONDENIM  ICDN Basket', 299000, 399000, 'ÁO THUN NAN ĐẸP', 0x68747470733a2f2f63646e2e687374617469632e6e65742f70726f64756374732f313030303235333737352f3136305f7365745f626f5f3030362d315f61636365396336616439396634666561616535633264333266303966623736645f6c617267652e6a7067, 1, 'WAYFARER', 60, 'Còn hàng', '2025-10-29 20:38:34', '2025-10-29 21:01:28', 'SEP30'),
('SP002', 'Áo Polo Nam ICONDENIM Shoulder Line', 379000, 500000, 'áo polo', 0x68747470733a2f2f63646e2e687374617469632e6e65742f70726f64756374732f313030303235333737352f3136305f616f5f706f6c6f5f3233372d315f35636262323162333166626234623032623465376232336332636665353062625f6c617267652e6a7067, 1, 'WAYFARER', 30, 'Còn hàng', '2025-10-28 17:40:30', '2025-10-29 21:33:27', 'SEP30'),
('SP003', 'Áo Thun Nam ICONDENIM pocket Edge', 211000, 300000, 'Áo Thun Nam ICONDENIM Pocket Edge, thiết kế trắng đen, túi ngực edge cá tính, chất cotton thoáng mát.', 0x68747470733a2f2f63646e2e687374617469632e6e65742f70726f64756374732f313030303235333737352f3136305f616f5f7468756e5f3631382d31315f30303933373836623965326534666431383532363934306639313436316339615f3130323478313032342e6a7067, 1, 'ICONDENIM', 30, 'Còn hàng', '2025-10-30 19:33:48', '2025-10-30 19:33:48', 'SEP30'),
('SP004', 'Quần Short Jean Nam ICONDENIM Dark Grey', 350000, 390000, 'Quần Short Jean Nam ICONDENIM Dark Grey, màu xám đen vintage, form Smart Fit co giãn thoải mái.', 0x68747470733a2f2f63646e2e687374617469632e6e65742f70726f64756374732f313030303235333737352f3136305f73686f72745f3239382d5f31345f65623062613565383837623534383761626361646134623163666136626536365f6c617267652e6a7067, 2, 'ICONDENIM', 100, 'Còn hàng', '2025-10-30 19:49:33', '2025-10-30 19:51:43', 'SEP30'),
('SP005', 'Áo Thun Nam ICONDENIM ICONDENIM Canyon', 350000, 390000, 'Áo Thun Nam ICONDENIM Canyon - màu nâu đất sang trọng, thiết kế tối giản tinh tế.', 0x68747470733a2f2f63646e2e687374617469632e6e65742f70726f64756374732f313030303235333737352f3136305f616f5f7468756e5f3538332d31335f30346239363836336236353434636639613439376531323433393333393833315f6c617267652e6a7067, 1, 'ICONDENIM', 100, 'Còn hàng', '2025-10-30 19:55:51', '2025-10-30 19:55:51', 'SEP30'),
('SP006', 'Quần Short Kaki Nam ICONDENIM Corduroy OG Form Regular', 350000, 390000, 'Quần Short Kaki Nam ICONDENIM Corduroy OG, form Regular thoải mái, chất vải corduroy bền bỉ, thoáng mát.', 0x68747470733a2f2f63646e2e687374617469632e6e65742f70726f64756374732f313030303235333737352f3136305f73686f72745f3232312d31315f33646330333934346534653034363962613061356536326134383966313262625f3130323478313032342e6a7067, 2, 'ICONDENIM', 100, 'Còn hàng', '2025-10-30 20:00:47', '2025-10-30 20:00:47', 'SEP30'),
('SP007', 'Quần Khaki Nam Dài Wash ICONDENIM ID', 310000, 390000, 'Quần Khaki Nam Dài Wash ICONDENIM ID, form Regular thoải mái, chất kaki wash mềm mại, thoáng khí.', 0x68747470733a2f2f63646e2e687374617469632e6e65742f70726f64756374732f313030303235333737352f3136305f6b616b695f3035332d375f61656139393265656431333234333232393331396234626432663666366435315f3130323478313032342e6a7067, 2, 'ICONDENIM', 100, 'Còn hàng', '2025-10-30 20:25:53', '2025-10-30 20:25:53', 'SEP05'),
('SP008', 'Áo Sơ Mi Nam Tay Ngắn ICONDENIM Crinkle Crest Shirt', 300000, 390000, 'Áo Sơ Mi Nam Tay Ngắn ICONDENIM Crinkle Crest, chất vải crinkle thoáng mát, form Regular tinh tế.', 0x68747470733a2f2f63646e2e687374617469632e6e65742f70726f64756374732f313030303235333737352f3136305f736f6d695f3330312d315f35656335373564366366353634363631626530346139363264666565383036365f6c617267652e6a7067, 1, 'ICONDENIM', 100, 'Còn hàng', '2025-10-30 20:31:27', '2025-10-30 20:31:27', 'SEP05'),
('SP009', 'Túi Tote Denim Nam ICONDENIM VIỆT NAM TRONG TIM', 99000, 120000, 'Túi tote denim in chữ “VIỆT NAM TRONG TIM”, chất vải bền chắc, quai đeo chắc chắn, dung tích rộng.', 0x68747470733a2f2f63646e2e687374617469632e6e65742f70726f64756374732f313030303235333737352f7a363931323333313530363639345f39623761616539366634643963323335626635316531306464363763333138315f64663531396161663964623134353634623938383462323633393264313639385f6c617267652e6a7067, 3, 'ICONDENIM', 100, 'Còn hàng', '2025-10-30 20:36:41', '2025-10-30 20:36:41', 'SEP01'),
('SP010', 'Thắt Lưng Nam ICONDENIM Classic Leather Belt', 56000, 150000, 'Thắt Lưng Nam ICONDENIM Classic Leather Belt, da thật cao cấp, khóa kim loại chắc chắn, phong cách cổ điển.', 0x68747470733a2f2f70726f647563742e687374617469632e6e65742f313030303235333737352f70726f647563742f746861742d6c756e672d69636f6e64656e696d2d757262616e2d657373656e7469616c5f5f31315f5f32393364376636633537616234363665613133613131313238313534383930615f6c617267652e6a7067, 3, 'ICONDENIM', 100, 'Còn hàng', '2025-10-30 20:49:34', '2025-10-30 20:49:34', 'SEP01'),
('SP011', 'Dép Quai Ngang Nam ICONDENIM Shade Flow', 99000, 149000, 'Dép quai ngang thiết kế hiện đại, đế cao su chống trượt, quai mềm êm chân, thoáng khí.', 0x68747470733a2f2f70726f647563742e687374617469632e6e65742f313030303235333737352f70726f647563742f6465702d717561692d6e67616e672d6e616d2d69636f6e64656e696d2d73686164652d666c6f775f5f325f5f37643435383961333830363334636133393639623436656332376239616234385f6c617267652e6a7067, 3, 'ICONDENIM', 100, 'Còn hàng', '2025-10-30 20:55:26', '2025-10-30 20:56:22', 'SEP01'),
('SP012', 'Mắt Kính Nam ICONDENIM BRONZE VIEW', 99000, 149000, 'Kính mát gọng kim loại bronze, tròng chống UV, thiết kế thời thượng, nhẹ mặt.', 0x68747470733a2f2f63646e2e687374617469632e6e65742f70726f64756374732f313030303235333737352f696d675f303736395f39303937326362353133313534343539393461363164663338306535306661345f3130323478313032342e6a7067, 3, 'ICONDENIM', 100, 'Còn hàng', '2025-10-30 20:59:16', '2025-10-30 20:59:16', 'SEP01'),
('SP013', 'Mắt Kính Nam ICONDENIM BRONZE VIEW', 99000, 159000, 'Kính mát gọng bronze thời thượng, tròng chống UV400, nhẹ và bền chắc.', 0x68747470733a2f2f63646e2e687374617469632e6e65742f70726f64756374732f313030303235333737352f3136305f6b696e685f3033332d315f31383733636330626437356134383530383732356538616633636362346534665f3130323478313032342e6a7067, 3, 'ICONDENIM', 100, 'Còn hàng', '2025-10-30 21:03:23', '2025-10-30 21:03:23', 'SEP01'),
('SP014', 'Vớ Nam Yellowsoc High-Cut', 99000, 149000, 'Vớ cao cổ Yellowsoc, chất cotton thoáng khí, co giãn tốt, thiết kế thể thao.', 0x68747470733a2f2f70726f647563742e687374617469632e6e65742f313030303235333737352f70726f647563742f3136305f766f5f3030312d31305f32363637363666656162383134663832613331346164343137613634313165615f6c617267652e6a7067, 3, 'ICONDENIM', 100, 'Còn hàng', '2025-10-30 21:07:32', '2025-10-30 21:07:32', 'SEP01'),
('SP015', 'Mũ Lưỡi Trai Nam ICONDENIM Urban Essential', 99000, 149000, 'Mũ lưỡi trai form chuẩn, chất cotton thoáng mát, logo thêu tinh tế, điều chỉnh size linh hoạt.', 0x68747470733a2f2f70726f647563742e687374617469632e6e65742f313030303235333737352f70726f647563742f3136305f6e6f6e5f3033372d395f39313565346130326362663234356532393263333533653166626566656134315f6c617267652e6a7067, 3, 'ICONDENIM', 100, 'Còn hàng', '2025-10-30 21:16:07', '2025-10-30 21:16:07', 'SEP01'),
('SP016', 'Áo Hoodie Nam ICONDENIM Stronger Life', 299000, 399900, 'Hoodie form rộng, chất cotton pha mềm mại, thoáng khí, mũ dây rút, in slogan mạnh mẽ.', 0x68747470733a2f2f70726f647563742e687374617469632e6e65742f313030303235333737352f70726f647563742f3136305f616f5f686f6f6469655f3033362d31305f36396435313533353839323034656430386161666333316566636331306136645f3130323478313032342e6a7067, 1, 'ICONDENIM', 60, 'Còn hàng', '2025-11-02 14:48:25', '2025-11-02 14:48:25', 'SEP05'),
('SP017', 'Túi Tote Nam ICONDENIM Canvas Disney Cheer Up', 87000, 99000, 'Túi tote canvas bền chắc, in họa tiết Disney vui nhộn, dung tích rộng, quai đeo thoải mái.', 0x68747470733a2f2f63646e2e687374617469632e6e65742f70726f64756374732f313030303235333737352f3136305f7475695f3035302d355f63656439633765613763636534373665623562626238666334396638633264635f3130323478313032342e6a7067, 3, 'ICONDENIM', 60, 'Còn hàng', '2025-11-02 14:56:31', '2025-11-02 14:56:31', 'SEP01'),
('SP018', 'Ví Da Nam ICONDENIM Multi-Functionalt', 199000, 299000, 'Ví da cao cấp, thiết kế đa năng với nhiều ngăn tiện lợi, chất liệu bền đẹp, đường may tinh tế.', 0x68747470733a2f2f70726f647563742e687374617469632e6e65742f313030303235333737352f70726f647563742f76692d64612d69636f6e64656e696d2d6d756c74692d66756e6374696f6e616c745f5f335f5f61613361613838353631303034373438623139333262633165616364363931655f3130323478313032342e6a7067, 3, 'ICONDENIM', 40, 'Còn hàng', '2025-11-02 15:00:32', '2025-11-02 15:00:32', 'SEP01'),
('SP019', 'Mũ Bucket Nam ICONDENIM Sticker ID', 279000, 400000, 'Mũ bucket form tròn thời thượng, chất cotton pha thoáng mát, in sticker ID cá tính.', 0x68747470733a2f2f70726f647563742e687374617469632e6e65742f313030303235333737352f70726f647563742f3136305f6e6f6e5f3035392d395f65303131323330383631616534303765383434656432656365626635333762305f3130323478313032342e6a7067, 3, 'ICONDENIM', 100, 'Còn hàng', '2025-11-02 15:06:43', '2025-11-02 15:34:18', 'SEP01'),
('SP020', 'Combo Áo Thun + Quần Short ICONDENIM', 599000, 1500000, 'Combo áo thun xám cotton thoáng mát và quần short form chuẩn, phong cách casual trẻ trung.', 0x68747470733a2f2f66696c652e687374617469632e6e65742f313030303235333737352f66696c652f30315f34353935613538353362643334366632383964636261326337646431626164322e6a7067, 5, 'ICONDENIM', 60, 'Còn hàng', '2025-11-02 15:43:42', '2025-11-02 15:43:42', 'SEP05'),
('SP021', 'Combo Áo Thun + Quần Jean ICONDENIM', 570000, 1300000, 'Combo áo thun trắng cotton mềm mại và quần jean form chuẩn, phong cách năng động, tinh tế.', 0x68747470733a2f2f66696c652e687374617469632e6e65742f313030303235333737352f66696c652f30315f39373864653661626232623934356131613963623265393837326239316533392e6a7067, 5, 'ICONDENIM', 109, 'Còn hàng', '2025-11-02 15:55:52', '2025-11-02 15:55:52', 'SEP30'),
('SP022', 'Set đồ Nam ICONDENIM New York Cozy', 679000, 2900000, 'Set áo thun và quần đen cotton pha, form thoải mái, phong cách New York năng động, cá tính.', 0x68747470733a2f2f70726f647563742e687374617469632e6e65742f313030303235333737352f70726f647563742f7a363135313734363837393037375f36303631393638353330613538333331663139313962626636386438626434365f62643666326431363034343334373634616465396235366164383264326466305f3130323478313032342e6a7067, 5, 'ICONDENIM', 40, 'Còn hàng', '2025-11-02 16:01:22', '2025-11-02 16:01:22', 'SEP01'),
('SP023', 'Áo Khoác Bomber Nam ICONDENIM Freshman Varsity', 129000, 145000, 'Áo khoác bomber form varsity cổ điển, chất vải cotton pha thoáng mát, chi tiết thêu tinh tế.', 0x68747470733a2f2f63646e2e687374617469632e6e65742f70726f64756374732f313030303235333737352f3136305f616f5f6b686f61635f3232382d335f32343962383732356437386234383564396234343637383464663737343139325f3130323478313032342e6a7067, 1, 'ICONDENIM', 60, 'Còn hàng', '2025-11-02 16:59:01', '2025-11-02 16:59:01', 'SEP30'),
('SP024', 'Áo Khoác Denim Nam ICONDENIM Dust Black Trucker', 267000, 700000, 'Áo khoác denim trucker đen bụi vintage, form chuẩn, chất vải denim bền chắc, thoáng mát.', 0x68747470733a2f2f63646e2e687374617469632e6e65742f70726f64756374732f313030303235333737352f616f2d6b686f61632d64656e696d2d6e616d2d69636f6e64656e696d2d647573742d626c61636b2d747275636b6572345f65653130646436316138313734643530623032643663363735386532303131355f3130323478313032342e6a7067, 1, 'ICONDENIM', 40, 'Còn hàng', '2025-11-02 17:28:04', '2025-11-02 17:28:04', 'SEP30');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `bien_the_san_pham`
--
ALTER TABLE `bien_the_san_pham`
  ADD PRIMARY KEY (`id_Bien_The`),
  ADD KEY `fk_BTSP_sanPham` (`id_SP`);

--
-- Chỉ mục cho bảng `binh_luan`
--
ALTER TABLE `binh_luan`
  ADD PRIMARY KEY (`id_BL`),
  ADD KEY `fk_binhLuan_nguoiDung` (`id_ND`),
  ADD KEY `fk_binhLuan_sanPham` (`id_SP`);

--
-- Chỉ mục cho bảng `chi_tiet_hoa_don`
--
ALTER TABLE `chi_tiet_hoa_don`
  ADD PRIMARY KEY (`id_CTHD`),
  ADD KEY `fk_CTHD_donHang` (`id_DH`),
  ADD KEY `fk_CTHD_sanPham` (`id_SP`);

--
-- Chỉ mục cho bảng `danh_muc`
--
ALTER TABLE `danh_muc`
  ADD PRIMARY KEY (`id_DM`);

--
-- Chỉ mục cho bảng `dia_chi_giao_hang`
--
ALTER TABLE `dia_chi_giao_hang`
  ADD PRIMARY KEY (`id_ND`);

--
-- Chỉ mục cho bảng `don_hang`
--
ALTER TABLE `don_hang`
  ADD PRIMARY KEY (`id_DH`),
  ADD KEY `fk_donHang_nguoiDung1` (`id_ND`);

--
-- Chỉ mục cho bảng `giao_dich_thanh_toan`
--
ALTER TABLE `giao_dich_thanh_toan`
  ADD PRIMARY KEY (`id_GDTT`),
  ADD KEY `fk_GDTT_donHang` (`id_DH`);

--
-- Chỉ mục cho bảng `gio_hang`
--
ALTER TABLE `gio_hang`
  ADD PRIMARY KEY (`id_GH`),
  ADD KEY `fk_gioHang_nguoiDung` (`id_ND`);

--
-- Chỉ mục cho bảng `gio_hang_chi_tiet`
--
ALTER TABLE `gio_hang_chi_tiet`
  ADD PRIMARY KEY (`id_GHCT`),
  ADD KEY `fk_GHCT_gioHang` (`id_GH`),
  ADD KEY `fk_GHCT_sanPham` (`id_SP`),
  ADD KEY `fk_maGiamGia_GHCT` (`ma_Giam_Gia`);

--
-- Chỉ mục cho bảng `lich_su_don_hang`
--
ALTER TABLE `lich_su_don_hang`
  ADD PRIMARY KEY (`id_LSDH`),
  ADD KEY `fk_LSDH_donHang` (`id_DH`),
  ADD KEY `fk_LSDH_nguoiDung` (`id_ND`);

--
-- Chỉ mục cho bảng `lich_su_tim_kiem`
--
ALTER TABLE `lich_su_tim_kiem`
  ADD PRIMARY KEY (`id_LSTT`),
  ADD KEY `fk_LSTT_nguoiDung` (`id_ND`);

--
-- Chỉ mục cho bảng `ma_giam_gia`
--
ALTER TABLE `ma_giam_gia`
  ADD PRIMARY KEY (`ma_Giam_Gia`);

--
-- Chỉ mục cho bảng `nguoi_dung`
--
ALTER TABLE `nguoi_dung`
  ADD PRIMARY KEY (`id_ND`);

--
-- Chỉ mục cho bảng `san_pham`
--
ALTER TABLE `san_pham`
  ADD PRIMARY KEY (`id_SP`),
  ADD KEY `fk_sanPham_danhMuc` (`id_DM`),
  ADD KEY `fk_sanPham_maGiamGia` (`ma_Giam_Gia`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `bien_the_san_pham`
--
ALTER TABLE `bien_the_san_pham`
  MODIFY `id_Bien_The` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=95;

--
-- AUTO_INCREMENT cho bảng `binh_luan`
--
ALTER TABLE `binh_luan`
  MODIFY `id_BL` int(11) NOT NULL AUTO_INCREMENT COMMENT 'khóa chính, id bình luận', AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT cho bảng `danh_muc`
--
ALTER TABLE `danh_muc`
  MODIFY `id_DM` int(11) NOT NULL AUTO_INCREMENT COMMENT 'khóa chính, id danh mục', AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT cho bảng `lich_su_tim_kiem`
--
ALTER TABLE `lich_su_tim_kiem`
  MODIFY `id_LSTT` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'khóa chính, id lịch sử tìm kiếm', AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `nguoi_dung`
--
ALTER TABLE `nguoi_dung`
  MODIFY `id_ND` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'khóa chính, id người dùng', AUTO_INCREMENT=14;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `bien_the_san_pham`
--
ALTER TABLE `bien_the_san_pham`
  ADD CONSTRAINT `fk_BTSP_sanPham` FOREIGN KEY (`id_SP`) REFERENCES `san_pham` (`id_SP`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `binh_luan`
--
ALTER TABLE `binh_luan`
  ADD CONSTRAINT `fk_binhLuan_nguoiDung` FOREIGN KEY (`id_ND`) REFERENCES `nguoi_dung` (`id_ND`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_binhLuan_sanPham` FOREIGN KEY (`id_SP`) REFERENCES `san_pham` (`id_SP`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `chi_tiet_hoa_don`
--
ALTER TABLE `chi_tiet_hoa_don`
  ADD CONSTRAINT `fk_CTHD_donHang` FOREIGN KEY (`id_DH`) REFERENCES `don_hang` (`id_DH`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_CTHD_sanPham` FOREIGN KEY (`id_SP`) REFERENCES `san_pham` (`id_SP`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `dia_chi_giao_hang`
--
ALTER TABLE `dia_chi_giao_hang`
  ADD CONSTRAINT `fk_DCGH_nguoiDung` FOREIGN KEY (`id_ND`) REFERENCES `nguoi_dung` (`id_ND`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `don_hang`
--
ALTER TABLE `don_hang`
  ADD CONSTRAINT `fk_donHang_nguoiDung1` FOREIGN KEY (`id_ND`) REFERENCES `nguoi_dung` (`id_ND`);

--
-- Các ràng buộc cho bảng `giao_dich_thanh_toan`
--
ALTER TABLE `giao_dich_thanh_toan`
  ADD CONSTRAINT `fk_GDTT_donHang` FOREIGN KEY (`id_DH`) REFERENCES `don_hang` (`id_DH`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `gio_hang`
--
ALTER TABLE `gio_hang`
  ADD CONSTRAINT `fk_gioHang_nguoiDung` FOREIGN KEY (`id_ND`) REFERENCES `nguoi_dung` (`id_ND`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `gio_hang_chi_tiet`
--
ALTER TABLE `gio_hang_chi_tiet`
  ADD CONSTRAINT `fk_GHCT_gioHang` FOREIGN KEY (`id_GH`) REFERENCES `gio_hang` (`id_GH`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_GHCT_sanPham` FOREIGN KEY (`id_SP`) REFERENCES `san_pham` (`id_SP`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_maGiamGia_GHCT` FOREIGN KEY (`ma_Giam_Gia`) REFERENCES `ma_giam_gia` (`ma_Giam_Gia`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `lich_su_don_hang`
--
ALTER TABLE `lich_su_don_hang`
  ADD CONSTRAINT `fk_LSDH_donHang` FOREIGN KEY (`id_DH`) REFERENCES `don_hang` (`id_DH`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_LSDH_nguoiDung` FOREIGN KEY (`id_ND`) REFERENCES `nguoi_dung` (`id_ND`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `lich_su_tim_kiem`
--
ALTER TABLE `lich_su_tim_kiem`
  ADD CONSTRAINT `fk_LSTT_nguoiDung` FOREIGN KEY (`id_ND`) REFERENCES `nguoi_dung` (`id_ND`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `san_pham`
--
ALTER TABLE `san_pham`
  ADD CONSTRAINT `fk_sanPham_danhMuc` FOREIGN KEY (`id_DM`) REFERENCES `danh_muc` (`id_DM`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_sanPham_maGiamGia` FOREIGN KEY (`ma_Giam_Gia`) REFERENCES `ma_giam_gia` (`ma_Giam_Gia`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
