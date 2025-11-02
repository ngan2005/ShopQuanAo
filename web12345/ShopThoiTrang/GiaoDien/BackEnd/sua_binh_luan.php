<?php
session_start();
require_once 'database.php';

// === KẾT NỐI DATABASE ===
$db = new Database();
$pdo = $db->connect();

if (!$pdo) {
    die("<h2 style='color:red;text-align:center;'>Lỗi kết nối database!</h2>");
}

// === LẤY THAM SỐ ===
$id_BL = $_GET['id_BL'] ?? null;
$id_SP = $_GET['id'] ?? null; // Đồng bộ với chiTietSanPham.php

if (!$id_BL || !$id_SP) {
    die("<h2 style='color:red;text-align:center;'>Thiếu thông tin bình luận hoặc sản phẩm!</h2>");
}

// === LẤY THÔNG TIN BÌNH LUẬN HIỆN TẠI ===
try {
    $stmt = $pdo->prepare("SELECT noi_Dung, so_Sao, id_ND FROM binh_luan WHERE id_BL = ?");
    $stmt->execute([$id_BL]);
    $bl = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$bl) {
        die("<h2 style='color:red;text-align:center;'>Bình luận không tồn tại!</h2>");
    }

    // === KIỂM TRA QUYỀN SỬA ===
    if (!isset($_SESSION['user']) || $_SESSION['user']['id_ND'] != $bl['id_ND']) {
        die("<h2 style='color:red;text-align:center;'>Bạn không có quyền sửa bình luận này!</h2>");
    }

    // === XỬ LÝ FORM CẬP NHẬT ===
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $noi_Dung = trim($_POST['noi_Dung'] ?? '');
        $so_Sao = intval($_POST['so_Sao'] ?? 0);

        if (empty($noi_Dung) || $so_Sao < 1 || $so_Sao > 5) {
            die("<h2 style='color:red;text-align:center;'>Nội dung hoặc số sao không hợp lệ!</h2>");
        }

        $stmt = $pdo->prepare("UPDATE binh_luan SET noi_Dung = ?, so_Sao = ?, ngay_Binh_Luan = NOW() WHERE id_BL = ?");
        $stmt->execute([$noi_Dung, $so_Sao, $id_BL]);

        // Chuyển hướng về trang chi tiết sản phẩm
        header("Location: aohoodie.php?id=" . urlencode($id_SP));
        exit();
    }
} catch (PDOException $e) {
    die("<h2 style='color:red;text-align:center;'>Lỗi khi sửa: " . htmlspecialchars($e->getMessage()) . "</h2>");
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sửa Bình Luận - 160STORE</title>
    <link rel="stylesheet" href="https://kit.fontawesome.com/1147679ae7.js" crossorigin="anonymous">
    <script src="https://kit.fontawesome.com/1147679ae7.js" crossorigin="anonymous"></script>
    <style>
        :root {
            --gold: #FFD700;
            --black: #000;
            --dark: #1a1a1a;
            --gray: #222;
            --text: #fff;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--black);
            color: var(--text);
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background: var(--gray);
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.5);
        }

        h3 {
            color: var(--gold);
            text-align: center;
            margin-bottom: 20px;
            position: relative;
        }

        h3::after {
            content: '';
            width: 50px;
            height: 3px;
            background: var(--gold);
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
        }

        textarea, select {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border-radius: 8px;
            border: 1px solid #444;
            background: #1a1a1a;
            color: white;
            font-size: 1rem;
            resize: vertical;
        }

        button {
            background: var(--gold);
            color: #000;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.3s;
            width: 100%;
        }

        button:hover {
            background: #e6c200;
        }

        @media (max-width: 768px) {
            .container {
                padding: 15px;
            }
            button {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h3>Sửa Bình Luận</h3>
        <form method="POST">
            <textarea name="noi_Dung" rows="3" required><?= htmlspecialchars($bl['noi_Dung']) ?></textarea><br>
            <select name="so_Sao" required>
                <?php for ($i = 1; $i <= 5; $i++): ?>
                    <option value="<?= $i ?>" <?= $bl['so_Sao'] == $i ? 'selected' : '' ?>>⭐ <?= $i ?> - <?= $i == 5 ? 'Rất tốt' : ($i == 4 ? 'Tốt' : ($i == 3 ? 'Trung bình' : ($i == 2 ? 'Kém' : 'Rất kém'))) ?></option>
                <?php endfor; ?>
            </select><br><br>
            <button type="submit">Cập nhật</button>
        </form>
    </div>
</body>
</html>