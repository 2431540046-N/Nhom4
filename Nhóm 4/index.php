<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}
$admin = $_SESSION['admin'];
$page = $_GET['page'] ?? 'danhsachphong.php';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Quản lý Khách Sạn</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f4f6f8;
        }
        .container {
            display: flex;
            height: 100vh;
        }
        .sidebar {
            width: 260px;
            background: #2c3e50;
            color: white;
            padding: 20px;
            box-sizing: border-box;
        }
        .sidebar h3 {
            margin: 0 0 5px;
            font-size: 18px;
        }
        .sidebar p {
            margin: 0 0 20px;
            font-size: 14px;
            color: #ccc;
        }
        .sidebar a {
            display: block;
            color: white;
            text-decoration: none;
            margin: 12px 0;
            font-weight: bold;
            padding: 8px 12px;
            border-radius: 4px;
        }
        .sidebar a:hover {
            background: #34495e;
        }
        .content {
            flex: 1;
            padding: 30px;
            overflow-y: auto;
            box-sizing: border-box;
        }
        .content h2 {
            margin-top: 0;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="sidebar">
        <h3>Xin chào, <?= $admin['ho_ten'] ?></h3>
        <p><?= $admin['email'] ?></p>
        <a href="index.php?page=danhsachphong.php">📋 Danh Sách Phòng</a>
        <a href="index.php?page=themphong.php">➕ Thêm Phòng</a>
        <a href="index.php?page=hoadon.php">📦 Xem Đơn Hàng</a>
        <a href="logout.php">🚪 Đăng xuất</a>
    </div>
    <div class="content">
        <?php include $page; ?>
    </div>
</div>
</body>
</html>