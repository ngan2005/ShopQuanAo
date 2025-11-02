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
$id_SP = $_GET['id'] ?? null;

if (!$id_BL || !$id_SP) {
    die("<h2 style='color:red;text-align:center;'>Thiếu thông tin bình luận hoặc sản phẩm!</h2>");
}

// === KIỂM TRA QUYỀN XÓA ===
if (!isset($_SESSION['user']) && !isset($_SESSION['admin'])) {
    die("<h2 style='color:red;text-align:center;'>Bạn cần đăng nhập để xóa bình luận!</h2>");
}

// Kiểm tra quyền của người dùng (có thể là Admin hoặc người dùng)
if (isset($_SESSION['admin']) || (isset($_SESSION['user']) && $_SESSION['user']['id_ND'] == $_GET['id_ND'])) {
    $id_ND = $_SESSION['user']['id_ND'] ?? null; // ID người dùng từ session nếu là user, nếu là admin thì không cần
    
    // Kiểm tra bình luận có tồn tại và thuộc về người dùng hoặc Admin
    $stmt = $pdo->prepare("
        SELECT id_ND FROM binh_luan 
        WHERE id_BL = ? AND id_SP = ?
    ");
    $stmt->execute([$id_BL, $id_SP]);
    $comment = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$comment) {
        die("<h2 style='color:red;text-align:center;'>Bình luận không tồn tại!</h2>");
    }

    // Kiểm tra nếu là Admin hoặc bình luận của người dùng hiện tại
    if ($_SESSION['admin'] || $comment['id_ND'] == $id_ND) {

        // === XÓA BÌNH LUẬN + CẢ PHẢN HỒI CON ===
        try {
            $pdo->beginTransaction();

            // Xóa phản hồi con trước
            $pdo->prepare("DELETE FROM binh_luan WHERE id_BL_cha = ?")->execute([$id_BL]);

            // Xóa bình luận gốc
            $pdo->prepare("DELETE FROM binh_luan WHERE id_BL = ?")->execute([$id_BL]);

            $pdo->commit();

            // Chuyển hướng về trang chi tiết sản phẩm
            header("Location: aohoodie.php?id=" . urlencode($id_SP));
            exit();
        } catch (Exception $e) {
            $pdo->rollBack();
            die("<h2 style='color:red;text-align:center;'>Lỗi khi xóa: " . htmlspecialchars($e->getMessage()) . "</h2>");
        }

    } else {
        die("<h2 style='color:red;text-align:center;'>Bạn không có quyền xóa bình luận này!</h2>");
    }

} else {
    die("<h2 style='color:red;text-align:center;'>Bạn không có quyền xóa bình luận này!</h2>");
}
?>
