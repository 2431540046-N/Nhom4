<?php
// Bắt buộc phải có file kết nối và các hàm xử lý Database
include 'DbAdmin.php'; 
$db = new DbAdmin();
session_start();

// --- 1. KIỂM TRA ĐĂNG NHẬP ---
if (!isset($_SESSION['khach'])) {
    header("Location: dangnhap.php");
    exit;
}

$khach_hien_tai = $_SESSION['khach'];
$id_khach = $khach_hien_tai['id_khach'];

// --- 2. XỬ LÝ THÊM GIỎ HÀNG KHI NHẬN DỮ LIỆU POST ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Lấy thông tin phòng và thời gian đặt từ form (Giả định)
    $id_phong_can_them = (int)($_POST['id_phong'] ?? 0);
    $so_luong_phong = (int)($_POST['so_luong'] ?? 1); 
    $ngay_nhan = $_POST['ngay_nhan'] ?? null; 
    $ngay_tra = $_POST['ngay_tra'] ?? null;

    // 3. Kiểm tra dữ liệu đầu vào
    if ($id_phong_can_them > 0 && $so_luong_phong > 0 && $ngay_nhan && $ngay_tra) {
        

        $them_thanh_cong = $db->ThemPhongVaoGioHang(
            $id_khach, 
            $id_phong_can_them, 
            $so_luong_phong, 
            $ngay_nhan, 
            $ngay_tra
        );

        if ($them_thanh_cong) {
            // ✅ THÊM THÀNH CÔNG: Chuyển hướng ngay lập tức đến trang giỏ hàng
            header("Location: giohang.php"); 
            exit; // Rất quan trọng để dừng script và thực thi chuyển hướng
        } else {
            // ❌ THÊM THẤT BẠI: Chuyển hướng về trang danh sách phòng kèm lỗi
            header("Location: danhsachphongkhach.php?error=add_fail");
            exit;
        }
    } else {
        // ❌ LỖI DỮ LIỆU: Chuyển hướng về trang danh sách phòng kèm lỗi
        header("Location: danhsachphongkhach.php?error=invalid_data");
        exit;
    }
} else {
    // Nếu truy cập trực tiếp bằng GET mà không có POST
    header("Location: danhsachphongkhach.php");
    exit;
}
?>