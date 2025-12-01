<?php
include 'DbAdmin.php';
$db = new DbAdmin();
session_start();

if (!isset($_SESSION['khach'])) {
    header("Location: dangnhap.php");
    exit;
}

$khach_hien_tai = $_SESSION['khach'];
$id_khach = $khach_hien_tai['id_khach'];
$ten_khach = ($khach_hien_tai['ho_ten'] ?? $khach_hien_tai['email']);
$thong_bao = "";

// ---  XỬ LÝ CÁC THAO TÁC (Cập nhật hoặc Xóa) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_chi_tiet = (int)($_POST['id_chi_tiet'] ?? 0); 

    // LOGIC CẬP NHẬT NGÀY THÁNG
    if (isset($_POST['cap_nhat_chi_tiet']) && $id_chi_tiet > 0) {
        
        $ngay_nhan_moi = $_POST['ngay_nhan_moi'] ?? '';
        $ngay_tra_moi = $_POST['ngay_tra_moi'] ?? '';
        $so_luong_moi = 1; 
        
        if (!empty($ngay_nhan_moi) && !empty($ngay_tra_moi)) {
    
            $cap_nhat = $db->CapNhatChiTietGioHang($id_chi_tiet, $so_luong_moi, $ngay_nhan_moi, $ngay_tra_moi);
            
            if ($cap_nhat) {
                $thong_bao = "<div class='success-msg'>✅ Đã cập nhật chi tiết phòng thành công!</div>";
            } else {
                $thong_bao = "<div class='error-msg'>❌ Lỗi khi cập nhật. Ngày Trả phải sau Ngày Nhận.</div>";
            }
        } else {
            $thong_bao = "<div class='error-msg'>❌ Lỗi: Vui lòng chọn đầy đủ ngày nhận và ngày trả.</div>";
        }
    }
        
    //  XÓA PHÒNG
    if (isset($_POST['xoa_phong']) && $id_chi_tiet > 0) {

        if ($db->XoaPhongKhoiGioHang($id_chi_tiet)) { 
            $thong_bao = "<div class='success-msg'>✅ Đã xóa phòng khỏi giỏ hàng.</div>";
        } else {
            $thong_bao = "<div class='error-msg'>❌ Lỗi khi xóa phòng.</div>";
        }
    }
    
 
    if (isset($_POST['thanh_toan'])) {
        header("Location: thanh_toan.php");
        exit;
    }
    
 
    if (isset($_POST['cap_nhat_chi_tiet']) || isset($_POST['xoa_phong'])) {
        $_SESSION['thong_bao_gio_hang'] = $thong_bao; 
        header("Location: gio_hang.php"); 
        exit;
    }
}


if (isset($_SESSION['thong_bao_gio_hang'])) {
    $thong_bao = $_SESSION['thong_bao_gio_hang'];
    unset($_SESSION['thong_bao_gio_hang']); 
}


$danh_sach_gio_hang = $db->LayDanhSachGioHang($id_khach); 
$tong_tien = 0; 

if (!empty($danh_sach_gio_hang)) {

    foreach ($danh_sach_gio_hang as &$item) {
        
  
        $so_dem = $db->tinhSoDem($item['ngay_nhan'], $item['ngay_tra']);
        

        $thanh_tien_item = $item['gia'] * $so_dem;
        
        $item['so_dem'] = $so_dem;
        $item['thanh_tien'] = $thanh_tien_item;
        
        $tong_tien += $thanh_tien_item; 
    }
    unset($item); 
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Giỏ Hàng Của Bạn</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; background-color: #f4f4f4; }
        .header { background-color: #007bff; color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; }
        .header a { color: white; text-decoration: none; margin-left: 20px; padding: 5px 10px; border-radius: 4px; }
        .container { max-width: 1000px; margin: 20px auto; padding: 20px; background-color: white; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h2 { border-bottom: 2px solid #ccc; padding-bottom: 10px; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; border: 1px solid #ddd; text-align: left; vertical-align: top; }
        th { background-color: #f8f8f8; }
        .detail-form { display: flex; flex-direction: column; gap: 5px; max-width: 250px; }
        .btn-update { background-color: #ffc107; color: #333; border: none; padding: 5px 10px; cursor: pointer; border-radius: 4px; margin-top: 5px; }
        .btn-delete { background-color: #dc3545; color: white; border: none; padding: 5px 10px; cursor: pointer; border-radius: 4px; }
        .cart-summary { margin-top: 30px; text-align: right; }
        .btn-checkout { background-color: #28a745; color: white; padding: 15px 30px; font-size: 1.2em; border: none; border-radius: 6px; cursor: pointer; }
        .success-msg { padding: 10px; margin-bottom: 15px; background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; border-radius: 4px; text-align: center; }
        .error-msg { padding: 10px; margin-bottom: 15px; background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 4px; text-align: center; }
    </style>
    
    
</head>
<body>

    <div class="header">
        <a href="danhsachphongkhach.php" style="margin-left: 0;">⬅️ Tiếp tục xem phòng</a>
        <a href="tai_khoan_ca_nhan.php">👤 <?= $ten_khach ?></a>
        <a href="index_khachhang.php?action=logout">🚪 Đăng Xuất</a>
    </div>

    <div class="container">
        <h2>🛒 Giỏ Hàng Của Bạn</h2>
        
        <?= $thong_bao ?>

        <?php if (empty($danh_sach_gio_hang)): ?>
            <p style="text-align: center; font-size: 1.1em;">Giỏ hàng của bạn đang trống.</p>
        <?php else: ?>

        <table>
            <thead>
                <tr>
                    <th style="width: 30%;">Phòng</th>
                    <th style="width: 15%;">Giá/đêm</th>
                    <th style="width: 40%;">Chi tiết Đặt (Ngày nhận/trả)</th>
                    <th style="width: 10%;">Thành tiền</th>
                    <th style="width: 5%;">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($danh_sach_gio_hang as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['ten_phong']) ?> (<?= htmlspecialchars($item['loai_phong']) ?>)</td>
                    <td>
                        <input type="hidden" id="price_per_night_<?= $item['id_chi_tiet'] ?>" value="<?= $item['gia'] ?>"> 
                        <?= $db->formatVND($item['gia']) ?>
                    </td>
                    
                    <td>
                        <form method="POST" class="detail-form">
                            <input type="hidden" name="id_chi_tiet" value="<?= $item['id_chi_tiet'] ?>">
                            
                            <label style="font-weight: bold; font-size: 0.9em;">Ngày Nhận:</label>
                            <input type="date" name="ngay_nhan_moi" id="ngay_nhan_moi_<?= $item['id_chi_tiet'] ?>"
                                value="<?= $item['ngay_nhan'] ? date('Y-m-d', strtotime($item['ngay_nhan'])) : '' ?>" required>
                            
                            <label style="font-weight: bold; font-size: 0.9em;">Ngày Trả:</label>
                            <input type="date" name="ngay_tra_moi" id="ngay_tra_moi_<?= $item['id_chi_tiet'] ?>"
                                value="<?= $item['ngay_tra'] ? date('Y-m-d', strtotime($item['ngay_tra'])) : '' ?>" required>
                            
                            <p style="margin: 0; font-size: 0.9em; font-weight: bold;">(Số đêm: **<span id="so_dem_display_<?= $item['id_chi_tiet'] ?>"><?= $item['so_dem'] ?></span>** đêm)</p>

                            <button type="submit" name="cap_nhat_chi_tiet" class="btn-update">Cập nhật Chi tiết</button>
                        </form>
                    </td>
                    
                    <td>
                        **<span id="thanh_tien_display_<?= $item['id_chi_tiet'] ?>"><?= $db->formatVND($item['thanh_tien']) ?></span>**
                    </td>
                    
                    <td>
                        <form method="POST">
                            <input type="hidden" name="id_chi_tiet" value="<?= $item['id_chi_tiet'] ?>">
                            <button type="submit" name="xoa_phong" class="btn-delete" onclick="return confirm('Bạn có chắc muốn xóa phòng này khỏi giỏ hàng?');">Xóa</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="cart-summary">
            <h3 class="overall-total">Tổng cộng: **<?= $db->formatVND($tong_tien) ?>**</h3>
            <form method="POST">
                <button type="submit" name="thanh_toan" class="btn-checkout">💳 Tiến hành Thanh toán</button>
            </form>
        </div>

        <?php endif; ?>
    </div>
    





















































































































































































































    <script>
        /** Hàm định dạng số thành chuỗi tiền tệ VNĐ. (Lặp lại logic PHP cho client-side) */
        function formatVND(amount) {
            const numericAmount = Math.round(amount); 
            return numericAmount.toLocaleString('vi-VN') + ' VNĐ';
        }

        /** Hàm tính tổng tiền của tất cả các mặt hàng và cập nhật Tổng cộng. */
        function updateOverallTotal() {
            let newTotal = 0;
            document.querySelectorAll('[id^="thanh_tien_display_"]').forEach(span => {
                const priceText = span.textContent.replace('VNĐ', '').trim();
                const priceValue = parseInt(priceText.replace(/\./g, ''));
                if (!isNaN(priceValue)) {
                    newTotal += priceValue;
                }
            });

            const totalDisplay = document.querySelector('.overall-total');
            if (totalDisplay) {
                totalDisplay.innerHTML = `Tổng cộng: **${formatVND(newTotal)}**`;
            }
        }

        /** * Hàm tính toán và hiển thị Số đêm, Thành tiền (client-side) 
         * Lặp lại logic tinhSoDem() của PHP trong JS.
         */
        function updateSoDem(id, pricePerNight) {
            const ngayNhanInput = document.getElementById(`ngay_nhan_moi_${id}`);
            const ngayTraInput = document.getElementById(`ngay_tra_moi_${id}`);
            const soDemDisplay = document.getElementById(`so_dem_display_${id}`);
            const thanhTienDisplay = document.getElementById(`thanh_tien_display_${id}`);

            const checkInValue = ngayNhanInput.value; 
            const checkOutValue = ngayTraInput.value;
            
            // Chuyển đổi sang đối tượng Date (dùng UTC để tránh lỗi múi giờ)
            const ngayNhan = new Date(checkInValue + 'T00:00:00'); 
            const ngayTra = new Date(checkOutValue + 'T00:00:00');

            let diffDays = 0;
            let finalPrice = 0;
            const oneDay = 1000 * 60 * 60 * 24; 

            if (!isNaN(ngayNhan.getTime()) && !isNaN(ngayTra.getTime()) && ngayTra.getTime() > ngayNhan.getTime()) {
                
                const diffTime = ngayTra.getTime() - ngayNhan.getTime();
                diffDays = Math.round(diffTime / oneDay);
                
                if (datediff === 0 && diffTime > 0) {
                    datediff = 1;
                }
                
                finalPrice = diffDays * pricePerNight;
                
            } else {
                diffDays = 0; 
                finalPrice = 0;
            }
            
            if (soDemDisplay) soDemDisplay.textContent = diffDays;
            if (thanhTienDisplay) thanhTienDisplay.textContent = formatVND(finalPrice);
            
            updateOverallTotal(); 
        }
        
        // Khởi tạo và gán sự kiện khi trang tải xong
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.detail-form').forEach(form => {
                const id_chi_tiet = form.querySelector('input[name="id_chi_tiet"]').value;
                const priceInput = document.getElementById(`price_per_night_${id_chi_tiet}`);
                const pricePerNight = priceInput ? parseInt(priceInput.value) : 0;
                
                if(isNaN(pricePerNight)) return;

                const ngayNhanInput = document.getElementById(`ngay_nhan_moi_${id_chi_tiet}`);
                const ngayTraInput = document.getElementById(`ngay_tra_moi_${id_chi_tiet}`);

                const boundUpdate = () => updateSoDem(id_chi_tiet, pricePerNight);
                
                if (ngayNhanInput) ngayNhanInput.onchange = boundUpdate;
                if (ngayTraInput) ngayTraInput.onchange = boundUpdate;

                // Cập nhật giá trị ban đầu (quan trọng cho JavaScript)
                boundUpdate(); 
            });

            // Cập nhật tổng tiền cuối cùng khi tải trang
            updateOverallTotal();
        });
    </script>
</body>
</html>