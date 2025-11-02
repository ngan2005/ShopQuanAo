<?php
require_once 'database.php';

session_start();
session_destroy();
header("Location: dangNhap_DangKy.php");
exit;
?>