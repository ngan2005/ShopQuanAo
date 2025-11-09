<?php
    try {
        // Xây dựng câu lệnh SQL động dựa trên filter
        // $base_sql = ;
        $db->query('SELECT * FROM Categories ORDER BY category_id DESC');
        $categories = $db->resultSet();
    
        // Gửi dữ liệu ra console để debug
        echo '<script>';
        echo 'console.log("Dữ liệu người dùng: ", ' . json_encode($categories) . ');';
        echo '</script>';

    } catch (PDOException $e) {
        // Gửi thông báo lỗi ra console
        echo '<script>';
        echo 'console.error("Lỗi truy vấn SQL: ", ' . json_encode($e->getMessage()) . ');';
        echo '</script>';
    }

    $success_message = '';
    if (isset($_SESSION['success_message'])) {
        $success_message = $_SESSION['success_message'];
        unset($_SESSION['success_message']);
    }
?>