<?php
session_start();
require_once 'database.php';
$db = new Database();
$pdo = $db->connect();

$id_SP = $_POST['id_SP'];
$id_ND = $_SESSION['user']['id_ND'];
$id_BL_cha = $_POST['id_BL_cha'];
$noi_Dung = $_POST['noi_Dung'];
$so_Sao = $_POST['so_Sao'];

// Thêm bình luận trả lời
$stmt = $pdo->prepare("INSERT INTO binh_luan (id_SP, id_ND, noi_Dung, so_Sao, ngay_Binh_Luan, id_BL_cha) 
VALUES (?, ?, ?, ?, NOW(), ?)");
$stmt->execute([$id_SP, $id_ND, $noi_Dung, $so_Sao, $id_BL_cha]);

echo "<script>alert('Trả lời bình luận thành công!'); window.location.href='aohoodie.php?id=$id_SP';</script>";
?>
