<?php
require_once 'config.php';
require_once 'database.php';

session_start();
session_destroy();
header("Location: login.php");
exit;
?>