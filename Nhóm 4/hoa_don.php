<?php
include 'DbAdmin.php';
$db = new DbAdmin();
session_start();

// --- 1. LẤY VÀ XÓA THÔNG BÁO THÀNH CÔNG TỪ SESSION ---
$thong_bao_dat_phong = ""; 
if (isset($_SESSION['thong_bao_dat_phong'])) {
    $thong_bao_dat_phong = $_SESSION['thong_bao_dat_phong'];
    unset($_SESSION['thong_bao_dat_phong']); 
}

// --- 2. KIỂM TRA ĐĂNG NHẬP VÀ LẤY ID ---
if (!isset($_SESSION['khach'])) {
    $_SESSION['thong_bao_loi'] = "Vui lòng đăng nhập để xem hóa đơn.";
    header("Location: dangnhap.php");
    exit;
}
$id_khach = $_SESSION['khach']['id_khach'];
$id_dat = (int)($_GET['id'] ?? 0);

if ($id_dat <= 0) {
    $_SESSION['thong_bao_loi'] = "ID đơn hàng không hợp lệ.";
    header("Location: trang_chu.php");
    exit;
}

// --- 3. LẤY DỮ LIỆU ĐƠN HÀNG TỪ CSDL ---
$don_hang = $db->LayThongTinDonHang($id_dat, $id_khach); 

if (!$don_hang) {
    // Nếu không tìm thấy đơn hàng hoặc không đúng ID khách
    $_SESSION['thong_bao_loi'] = "Không tìm thấy hóa đơn hoặc bạn không có quyền truy cập.";
    header("Location: trang_chu.php"); 
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Hóa Đơn Đặt Phòng #<?= htmlspecialchars($id_dat) ?></title>
    <style>
        .success-msg { 
            background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; 
            padding: 15px; margin-bottom: 20px; border-radius: 5px; 
            text-align: center; font-weight: bold;
        }
        .invoice-container { max-width: 800px; margin: 30px auto; padding: 25px; border: 1px solid #ccc; border-radius: 8px; background-color: white; }
        .summary p { margin: 8px 0; font-size: 1.1em; }
        .total { font-size: 1.8em; font-weight: bold; color: #dc3545; margin-top: 20px; border-top: 1px solid #ccc; padding-top: 15px; }
    </style>
</head>
<body>
    <div class="invoice-container">
        
        <?php if (!empty($thong_bao_dat_phong)): ?>
            <div class="success-msg"><?= $thong_bao_dat_phong ?></div>
        <?php endif; ?>
        
        <h2>🧾 Chi Tiết Hóa Đơn </h2>
        
        <div class="summary">
            <h3>Thông tin Khách</h3>
            <p>Khách hàng: **<?= htmlspecialchars($don_hang['ho_ten']) ?>**</p>
            <p>Email: **<?= htmlspecialchars($don_hang['email']) ?>**</p>
            <p>Ngày đặt: **<?= date('d/m/Y', strtotime($don_hang['ngay_dat'])) ?>**</p>
          
        </div>

        <div class="total">
            Tổng thanh toán: <?= $db->formatVND($don_hang['tong_tien']) ?>
        </div>
        
        <p style="margin-top: 30px; border-top: 1px solid #ccc; padding-top: 15px; text-align: center;">
            Quý khách vui lòng kiểm tra email để nhận xác nhận chi tiết. Cảm ơn!
        </p>
    </div>
</body>
</html>