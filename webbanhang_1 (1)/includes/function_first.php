<?php
// function Upload hình ảnh- dùng chung
function uploadImage(array $fileInput, string $tableName): string {
    // Định nghĩa thư mục gốc cho tất cả các file upload
    define('UPLOAD_BASE_PATH', 'assets/uploads/');

    // === XÁC ĐỊNH THƯ MỤC VÀ TIỀN TỐ CHO FILE
    $subDir = strtolower($tableName); // Tên thư mục con là tên bảng viết thường
    $targetDir = UPLOAD_BASE_PATH . $subDir . '/';

    // Xác định tiền tố cho tên file để dễ nhận biết
    $fileNamePrefix = 'file_';
    switch ($subDir) {
        case 'users':
            $fileNamePrefix = 'avatars';
            break;
        case 'productimages':
            $fileNamePrefix = 'products';
            break;
        case 'categories':
            $fileNamePrefix = 'categorys';
            break;
    }

    // Tạo thư mục nếu nó chưa tồn tại
    if (!file_exists($targetDir)) {
        // Tham số thứ 3 (true) cho phép tạo lồng các thư mục
        mkdir($targetDir, 0777, true);
    }

    // 1. Kiểm tra lỗi upload 
    if ($fileInput['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('Có lỗi xảy ra trong quá trình upload file.');
    }

    // 2. Kiểm tra loại file và kích thước (bảo mật)
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime_type = $finfo->file($fileInput['tmp_name']);
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    $max_size = 5 * 1024 * 1024; // 5 MB

    if (!in_array($mime_type, $allowed_types)) {
        throw new Exception('Lỗi: Chỉ cho phép upload file JPG, PNG, GIF.');
    }
    if ($fileInput['size'] > $max_size) {
        throw new Exception('Lỗi: Kích thước file không được vượt quá 5MB.');
    }

    // 3. tạo tên file mới và đường dẫn đích
    $file_extension = pathinfo($fileInput['name'], PATHINFO_EXTENSION);
    $new_filename = $fileNamePrefix . uniqid('', true) . '.' . $file_extension;
    $target_path = $targetDir . $new_filename;

    // 4. Di chuyển file vào thư mục đích
    if (move_uploaded_file($fileInput['tmp_name'], $target_path)) {
        return $target_path; // Trả về đường dẫn file nếu thành công
    } else {
        throw new Exception('Không thể di chuyển file đã upload. Vui lòng kiểm tra quyền ghi của thư mục.');
    }
}

?>