<?php
include 'DbAdmin.php';
$db = new DbAdmin();
session_start();

// --- 1. KIỂM TRA ĐĂNG NHẬP ---
if (!isset($_SESSION['khach'])) {
    $_SESSION['thong_bao_loi'] = "Vui lòng đăng nhập để tiến hành thanh toán.";
    header("Location: dangnhap.php");
    exit;
}

$khach_hien_tai = $_SESSION['khach'];
$id_khach = $khach_hien_tai['id_khach'];
$tong_tien = 0; 

// Lấy danh sách phòng từ giỏ hàng
$gio_hang = $db->LayDanhSachGioHang($id_khach);

if (empty($gio_hang)) {
    $_SESSION['thong_bao_loi'] = "Giỏ hàng của bạn đang trống.";
    header("Location: gio_hang.php"); 
    exit;
}

// Tính toán Tổng tiền
foreach ($gio_hang as $item) {
    // Hàm tinhSoDem phải được định nghĩa trong DbAdmin.php
    $so_dem = $db->tinhSoDem($item['ngay_nhan'], $item['ngay_tra']); 
    $thanh_tien = $item['gia'] * $so_dem * $item['so_luong'];
    $tong_tien += $thanh_tien;
}

// Khối lệnh xử lý POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    if (isset($_POST['dat_phong_ngay'])) {
        $phuong_thuc_thanh_toan = $_POST['phuong_thuc_tt'] ?? 'Thanh toán tại quầy'; 
        $ghi_chu = $_POST['ghi_chu'] ?? '';
        
        // Khởi tạo biến $id_dat trước khi dùng (Tránh lỗi Undefined variable)
        $id_dat = 0; 
        
        // Gọi hàm tạo đơn hàng đã được sửa lỗi CSDL
        $id_dat = $db->TaoDonDatPhong($id_khach, $phuong_thuc_thanh_toan, $tong_tien, $ghi_chu); 
        
        // Kiểm tra kết quả tạo đơn hàng
        if ($id_dat > 0) {
            // ✅ THÀNH CÔNG: Chuyển hướng kèm ID đơn hàng
            $_SESSION['thong_bao_dat_phong'] = "<div class='success-msg'>🎉 Đặt phòng thành công!</div>";
            header("Location: hoa_don.php?id=" . $id_dat); 
            exit;
        } else {
            // ❌ THẤT BẠI: Chuyển về giỏ hàng
            $_SESSION['thong_bao_loi'] = "Đặt phòng thất bại. Vui lòng kiểm tra lại giỏ hàng và kết nối CSDL.";
            header("Location: gio_hang.php"); 
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thanh Toán Đặt Phòng</title>
    <style>
        /* CSS rút gọn để hiển thị */
        .checkout-container { max-width: 700px; margin: 50px auto; padding: 20px; border: 1px solid #ccc; border-radius: 8px; background-color: white; }
        .cart-summary table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .cart-summary th, .cart-summary td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        .total-row td { font-weight: bold; background-color: #f8f8f8; }
        .total-row .total-amount { color: #dc3545; font-size: 1.2em; }
        .payment-form input[type="text"], .payment-form textarea, .payment-form select { width: 100%; padding: 8px; margin-top: 5px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        .btn-submit { display: block; width: 100%; padding: 15px; background-color: #28a745; color: white; border: none; border-radius: 4px; font-size: 1.1em; cursor: pointer; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="checkout-container">
        <h2>💳 Xác Nhận Thanh Toán</h2>
        
        <h3>Thông tin Khách hàng</h3>
        <p>Tên: **<?= htmlspecialchars($khach_hien_tai['ho_ten']) ?>**</p>
        <p>Email: **<?= htmlspecialchars($khach_hien_tai['email']) ?>**</p>
        
        ---

        <div class="cart-summary">
            <h3>Chi tiết Đơn hàng</h3>
            <table>
                <thead>
                    <tr>
                        <th>Phòng</th>
                        <th>Ngày nhận</th>
                        <th>Ngày trả</th>
                        <th>Số đêm</th>
                        <th>Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($gio_hang as $item): 
                        $so_dem = $db->tinhSoDem($item['ngay_nhan'], $item['ngay_tra']);
                        $thanh_tien = $item['gia'] * $so_dem * $item['so_luong'];
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($item['ten_phong']) ?> (<?= htmlspecialchars($item['loai_phong']) ?>)</td>
                        <td><?= date('d/m/Y', strtotime($item['ngay_nhan'])) ?></td>
                        <td><?= date('d/m/Y', strtotime($item['ngay_tra'])) ?></td>
                        <td><?= $so_dem ?></td>
                        <td><?= $db->formatVND($thanh_tien) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr class="total-row">
                        <td colspan="4" style="text-align: right;">Tổng cộng:</td>
                        <td class="total-amount"><?= $db->formatVND($tong_tien) ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        
        ---

        <div class="payment-form">
            <h3>Phương thức Thanh toán</h3>
            <form action="thanh_toan.php" method="POST">
                <label for="phuong_thuc_tt">Chọn phương thức:</label>
                <select id="phuong_thuc_tt" name="phuong_thuc_tt" required>
                    <option value="Thanh toán tại quầy">Thanh toán tại quầy</option>
                    <option value="Chuyển khoản Ngân hàng">Chuyển khoản Ngân hàng</option>
                </select>

                <label for="ghi_chu">Ghi chú thêm (Tùy chọn):</label>
                <textarea id="ghi_chu" name="ghi_chu" rows="3"></textarea>
                
                <button type="submit" name="dat_phong_ngay" class="btn-submit">HOÀN TẤT ĐẶT PHÒNG</button>
            </form>
        </div>
    </div>
</body>
</html>