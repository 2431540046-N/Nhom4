<?php

include 'DbAdmin.php'; 

session_start(); 


if (!isset($_SESSION['khach'])) {

    header("Location: dangnhap.php");
    exit;
}


$khach_hien_tai = $_SESSION['khach'];

$ten_khach = htmlspecialchars($khach_hien_tai['ho_ten'] ?? $khach_hien_tai['email']);


if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_unset();
    session_destroy();
    header("Location: dangnhap.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Trang Chủ Khách Hàng</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; background-color: #f4f4f4; text-align: center; }
        .header { 
            background-color: #007bff; 
            color: white; 
            padding: 15px 30px; 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
        }
        .header a { 
            color: white; 
            text-decoration: none; 
            margin-left: 20px;
            padding: 5px 10px; 
            border-radius: 4px;
            transition: background-color 0.3s;
        }
  
        .header a.user-link {
            background-color: #dc3545; 
            font-weight: bold;
        }
        .header a.user-link:hover {
            background-color: #c82333;
        }
        .container { max-width: 800px; margin: 50px auto; padding: 30px; background-color: white; border-radius: 12px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); }
        
   
        .message-prompt { margin-bottom: 30px; font-size: 1.2em; color: #333; }

        .action-buttons { display: flex; justify-content: space-around; margin-top: 40px; }
        .action-buttons a {
            display: block;
            width: 45%;
            padding: 20px;
            text-decoration: none;
            color: white;
            font-size: 1.1em;
            font-weight: bold;
            border-radius: 8px;
            transition: background-color 0.3s;
        }
        .btn-rooms { background-color: #28a745; }
        .btn-rooms:hover { background-color: #1e7e34; }
        .btn-cart { background-color: #ffc107; color: #333; }
        .btn-cart:hover { background-color: #e0a800; }
    </style>
</head>
<body>

    <div class="header">
        <h2>🏠 Hệ Thống Đặt Phòng</h2>
        <div>
            <a href="tai_khoan_ca_nhan.php" class="user-link">👤 <?= $ten_khach ?></a>
            <a href="index_khachhang.php?action=logout">🚪 Đăng Xuất</a>
        </div>
    </div>

    <div class="container">
        
        <div class="message-prompt">
    
        </div>
        
        <div class="action-buttons">
            <a href="danhsachphongkhach.php" class="btn-rooms">
                🏨 Danh Sách Phòng
                <p style="font-size: 0.8em; font-weight: normal;">Tìm kiếm, xem thông tin và đặt phòng.</p>
            </a>

            <a href="gio_hang.php" class="btn-cart">
                🛒 Giỏ Hàng Của Tôi
                <p style="font-size: 0.8em; font-weight: normal; color: #333;">Xem lại các phòng đã chọn và tiến hành thanh toán.</p>
            </a>
        </div>
    </div>

</body>
</html>