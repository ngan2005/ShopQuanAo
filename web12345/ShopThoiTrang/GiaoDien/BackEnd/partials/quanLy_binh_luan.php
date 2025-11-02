<?php
session_start();
require_once '../database.php';
$db = new Database();
$pdo = $db->connect();

// Lấy danh sách bình luận và tính sao trung bình cho mỗi sản phẩm
$stmt = $pdo->prepare("
    SELECT 
        bl.id_BL, 
        bl.noi_Dung, 
        bl.so_Sao, 
        bl.ngay_Binh_Luan, 
        bl.id_BL_cha, 
        sp.ten_San_Pham, 
        nd.ten_Dang_Nhap, 
        sp.id_SP,
        (SELECT AVG(so_Sao) FROM binh_luan bl2 WHERE bl2.id_SP = sp.id_SP) as avg_rating,
        (SELECT COUNT(*) FROM binh_luan bl3 WHERE bl3.id_SP = sp.id_SP) as rating_count
    FROM 
        binh_luan bl
    JOIN 
        san_pham sp ON bl.id_SP = sp.id_SP
    JOIN 
        nguoi_dung nd ON bl.id_ND = nd.id_ND
    ORDER BY bl.ngay_Binh_Luan DESC
");
$stmt->execute();
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

$msg = ""; // Khởi tạo thông báo
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Bình luận | 160STORE Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #00d4ff;
            --secondary: #00ff88;
            --danger: #ff4d4d;
            --bg: #0a0a1a;
            --card: rgba(30, 30, 50, 0.7);
            --text: #e0e0ff;
            --border: rgba(0, 212, 255, 0.2);
            --hover: rgba(0, 212, 255, 0.15);
            --shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            background: var(--bg);
            color: var(--text);
            font-family: 'Inter', sans-serif;
            padding: 20px;
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            animation: fadeIn 0.6s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }

        h2 {
            text-align: center;
            font-size: 1.9rem;
            margin: 20px 0 30px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-weight: 700;
        }

        /* ===== BẢNG ===== */
        .table-container {
            background: var(--card);
            backdrop-filter: blur(15px);
            border-radius: 16px;
            overflow: hidden;
            border: 1.5px solid var(--border);
            box-shadow: var(--shadow);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: rgba(0,212,255,0.1);
            color: var(--primary);
            padding: 16px;
            text-align: left;
            font-weight: 600;
            font-size: 0.95rem;
        }

        td {
            padding: 14px 16px;
            border-bottom: 1px solid rgba(255,255,255,0.05);
            vertical-align: middle;
        }

        tr:hover {
            background: var(--hover);
        }

        .stars {
            color: var(--secondary); /* Sao màu xanh lá nhạt */
            font-size: 1rem;
        }

        .avg-rating {
            color: var(--secondary);
            font-weight: 500;
        }

        /* ===== THÔNG BÁO ===== */
        .msg {
            padding: 14px 18px;
            border-radius: 12px;
            margin: 15px 0;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: slideDown 0.4s ease;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
        }

        .msg.success {
            background: rgba(0,255,136,0.15);
            color: var(--secondary);
            border: 1px solid rgba(0,255,136,0.3);
        }

        .msg.error {
            background: rgba(255,77,77,0.15);
            color: var(--danger);
            border: 1px solid rgba(255,77,77,0.3);
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 992px) {
            th, td { font-size: 0.85rem; padding: 10px; }
        }

        @media (max-width: 600px) {
            table { font-size: 0.8rem; }
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Quản lý Bình luận Sản phẩm</h2>
    <?= $msg ?>

    <!-- BẢNG DANH SÁCH -->
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID Bình luận</th>
                    <th>Tên người dùng</th>
                    <th>Sản phẩm</th>
                    <th>Nội dung</th>
                    <th>Số sao</th>
                    <th>Ngày bình luận</th>
                    <th>Sao trung bình</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($comments)): ?>
                    <tr>
                        <td colspan="7" style="text-align:center; color:#888; padding:30px;">
                            <i class="fas fa-comment-slash"></i> Không có bình luận nào.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($comments as $comment): ?>
                        <tr>
                            <td><strong>#<?= htmlspecialchars($comment['id_BL']) ?></strong></td>
                            <td><?= htmlspecialchars($comment['ten_Dang_Nhap']) ?></td>
                            <td><?= htmlspecialchars($comment['ten_San_Pham']) ?></td>
                            <td><?= nl2br(htmlspecialchars($comment['noi_Dung'])) ?></td>
                            <td class="stars"><?= htmlspecialchars($comment['so_Sao']) ?>⭐</td>
                            <td><?= date('d/m/Y H:i', strtotime($comment['ngay_Binh_Luan'])) ?></td>
                            <td class="avg-rating">
                                <?php
                                $avg = $comment['avg_rating'];
                                $count = $comment['rating_count'];
                                echo $count > 0 ? number_format($avg, 1) . '⭐' : '0⭐';
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>