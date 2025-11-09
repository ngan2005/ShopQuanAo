<?php
    require_once 'includes/function.php'; // Nạp file chứa hàm generate_pagination

    try {
        // Lấy danh sách Roles để đổ vào dropdown filter
        $db->query('SELECT role_id, role_name FROM Roles');
        $roles = $db->resultSet();

        // === SỬA LỖI: CHUYỂN SANG DÙNG NAMED PLACEHOLDERS (VD: :keyword) ===

        $base_sql = 'SELECT u.*, r.role_name FROM Users u JOIN Roles r ON u.role_id = r.role_id';
        $where_conditions = [];
        $params = []; // Sẽ dùng mảng kết hợp (associative array)

        // 1. Filter theo từ khóa (tên hoặc email hoặc điện thoại)
        if (!empty($_GET['search_keyword'])) {
            $keyword = '%' . $_GET['search_keyword'] . '%';

            // THAY ĐỔI: Dùng :keyword1, :keyword2, :keyword3 thay vì ?
            $where_conditions[] = '(u.full_name LIKE :keyword1 OR u.email LIKE :keyword2 OR u.phone_number LIKE :keyword3 )';
            // THAY ĐỔI: Gán giá trị vào mảng $params với key là tên placeholder
            $params[':keyword1'] = $keyword;
            $params[':keyword2'] = $keyword;
            $params[':keyword3'] = $keyword;
        }

        // 2. Filter theo vai trò (role)
        if (!empty($_GET['filter_role'])) {
            // THAY ĐỔI: Dùng :role_id thay vì ?
            $where_conditions[] = 'u.role_id = :role_id';
            $params[':role_id'] = $_GET['filter_role'];
        }

        // 3. Filter theo trạng thái nhân viên
        if (!empty($_GET['filter_status'])) {
            // THAY ĐỔI: Dùng :status thay vì ?
            $where_conditions[] = 'u.employee_status = :status';
            $params[':status'] = $_GET['filter_status'];
        }
        // ====================================================================

        // Nối các điều kiện WHERE nếu có
        if (!empty($where_conditions)) {
            $base_sql .= ' WHERE ' . implode(' AND ', $where_conditions);
        }
        $base_sql .= ' ORDER BY u.user_id DESC';

        // === BƯỚC 2: ĐẾM TỔNG SỐ BẢN GHI 
        $count_sql = "SELECT COUNT(*) as total FROM (" . $base_sql . ") as count_table";
        $db->query($count_sql);

        // THAY ĐỔI: Vòng lặp bind_param giờ sẽ chạy đúng
        // vì $key_name giờ là ':keyword1', ':role_id'...
        if (!empty($params)) {
            foreach ($params as $key_name => $param_value) {
                $db->bind($key_name, $param_value);
            }
        }

        $total_items = $db->single()['total'];

        // === BƯỚC 3: TÍNH TOÁN CÁC THÔNG SỐ PHÂN TRANG 
        $items_per_page = 10;
        $current_page = isset($_GET['p']) ? (int)$_GET['p'] : 1;

        // Hàm này đã xử lý đúng việc giữ lại các filter (truyền $_GET)
        $pagination_data = generate_pagination(
            $total_items,
            $items_per_page,
            $current_page,
            'index.php',
            $_GET
        );

        $pagination_html = $pagination_data['html'];
        $offset = $pagination_data['offset'];
        $limit = $pagination_data['limit'];

        // === BƯỚC 4: LẤY DỮ LIỆU CHO TRANG HIỆN TẠI ===

        // Câu lệnh này giờ đã HOÀN TOÀN HỢP LỆ vì chỉ dùng 1 kiểu placeholder
        $data_sql = $base_sql . " LIMIT :limit OFFSET :offset";

        $db->query($data_sql);

        // THAY ĐỔI: Bind các tham số filter (giống BƯỚC 2)
        if (!empty($params)) {
            foreach ($params as $key_name => $param_value) {
                $db->bind($key_name, $param_value);
            }
        }

        // Bind các tham số của pagination
        $db->bind(':limit', $items_per_page, PDO::PARAM_INT);
        $db->bind(':offset', $offset, PDO::PARAM_INT);

        $users = $db->resultSet();
    } catch (PDOException $e) {
        echo '<script>console.error("Lỗi truy vấn SQL: ", ' . json_encode($e->getMessage()) . ');</script>';
        $users = [];
        $pagination_html = '';
    }

    $success_message = '';
    if (isset($_SESSION['success_message'])) {
        $success_message = $_SESSION['success_message'];
        unset($_SESSION['success_message']);
    }

?>
