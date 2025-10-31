<?php
require_once 'config.php';

// Lấy dữ liệu từ POST request
$data = json_decode(file_get_contents('php://input'), true);

$response = array();

switch($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        // Lấy danh sách người dùng
        $search = isset($_GET['search']) ? $_GET['search'] : '';
        $query = "SELECT * FROM users WHERE 
                username LIKE '%$search%' OR 
                email LIKE '%$search%' OR 
                fullname LIKE '%$search%' OR 
                role LIKE '%$search%' OR 
                status LIKE '%$search%'";
        
        $result = mysqli_query($conn, $query);
        $users = array();
        
        while($row = mysqli_fetch_assoc($result)) {
            $users[] = $row;
        }
        
        $response['status'] = 'success';
        $response['data'] = $users;
        break;

    case 'POST':
        // Thêm người dùng mới
        $username = mysqli_real_escape_string($conn, $data['username']);
        $password = mysqli_real_escape_string($conn, $data['password']);
        $fullname = mysqli_real_escape_string($conn, $data['fullname']);
        $email = mysqli_real_escape_string($conn, $data['email']);
        $address = mysqli_real_escape_string($conn, $data['address']);
        $phone = mysqli_real_escape_string($conn, $data['phone']);
        $role = mysqli_real_escape_string($conn, $data['role']);
        $status = mysqli_real_escape_string($conn, $data['status']);

        // Kiểm tra username đã tồn tại chưa
        $check = mysqli_query($conn, "SELECT id FROM users WHERE username = '$username'");
        if(mysqli_num_rows($check) > 0) {
            $response['status'] = 'error';
            $response['message'] = 'Tên người dùng đã tồn tại!';
        } else {
            $query = "INSERT INTO users (username, password, fullname, email, address, phone, role, status) 
                    VALUES ('$username', '$password', '$fullname', '$email', '$address', '$phone', '$role', '$status')";
            
            if(mysqli_query($conn, $query)) {
                $response['status'] = 'success';
                $response['message'] = 'Thêm người dùng thành công!';
                $response['id'] = mysqli_insert_id($conn);
            } else {
                $response['status'] = 'error';
                $response['message'] = 'Lỗi khi thêm người dùng!';
            }
        }
        break;

    case 'PUT':
        // Cập nhật người dùng
        $id = mysqli_real_escape_string($conn, $data['id']);
        $username = mysqli_real_escape_string($conn, $data['username']);
        $password = mysqli_real_escape_string($conn, $data['password']);
        $fullname = mysqli_real_escape_string($conn, $data['fullname']);
        $email = mysqli_real_escape_string($conn, $data['email']);
        $address = mysqli_real_escape_string($conn, $data['address']);
        $phone = mysqli_real_escape_string($conn, $data['phone']);
        $role = mysqli_real_escape_string($conn, $data['role']);
        $status = mysqli_real_escape_string($conn, $data['status']);

        $query = "UPDATE users SET 
                username = '$username',
                password = '$password',
                fullname = '$fullname',
                email = '$email',
                address = '$address',
                phone = '$phone',
                role = '$role',
                status = '$status'
                WHERE id = $id";

        if(mysqli_query($conn, $query)) {
            $response['status'] = 'success';
            $response['message'] = 'Cập nhật thành công!';
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Lỗi khi cập nhật!';
        }
        break;

    case 'DELETE':
        // Xóa người dùng
        $id = mysqli_real_escape_string($conn, $_GET['id']);
        $query = "DELETE FROM users WHERE id = $id";
        
        if(mysqli_query($conn, $query)) {
            $response['status'] = 'success';
            $response['message'] = 'Xóa thành công!';
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Lỗi khi xóa!';
        }
        break;
}

// Trả về response dạng JSON
header('Content-Type: application/json');
echo json_encode($response);

// Đóng kết nối
mysqli_close($conn);
?>