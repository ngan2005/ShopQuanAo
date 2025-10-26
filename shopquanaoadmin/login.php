<?php
session_start();

// Nếu đã đăng nhập thì chuyển về trang chủ
if (isset($_SESSION['user_id'])) {
    header('Location: trangChu.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - Shop 160S</title>
    <link rel="icon" href="logo160s.jpg" type="image/x-icon">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .login-container {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
        }
        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .login-header img {
            width: 150px;
            height: auto;
            margin-bottom: 1rem;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        label {
            display: block;
            margin-bottom: 0.5rem;
            color: #333;
        }
        input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .login-btn {
            width: 100%;
            padding: 0.75rem;
            background: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
        }
        .login-btn:hover {
            background: #45a049;
        }
        .error {
            color: #dc3545;
            padding: 0.5rem;
            margin-bottom: 1rem;
            border-radius: 4px;
            background: #ffe6e6;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <img src="logo160s.jpg" alt="Logo Shop 160S">
            <h2>Đăng nhập quản trị</h2>
        </div>

        <?php if (isset($_GET['error'])): ?>
            <div class="error">
                <?php 
                switch($_GET['error']) {
                    case 'empty':
                        echo 'Vui lòng nhập đầy đủ thông tin!';
                        break;
                    case 'invalid':
                        echo 'Tên đăng nhập hoặc mật khẩu không đúng!';
                        break;
                    default:
                        echo 'Có lỗi xảy ra, vui lòng thử lại!';
                }
                ?>
            </div>
        <?php endif; ?>

        <form action="ketmoi.php" method="POST">
            <div class="form-group">
                <label for="username">Tên đăng nhập</label>
                <input type="text" id="username" name="username" required>
            </div>
            
            <div class="form-group">
                <label for="password">Mật khẩu</label>
                <input type="password" id="password" name="password" required>
            </div>

            <button type="submit" class="login-btn" name="login_submit">Đăng nhập</button>
        </form>
    </div>
</body>
</html>