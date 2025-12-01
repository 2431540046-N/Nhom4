<?php

include 'DbAdmin.php';
$db = new DbAdmin();
session_start();
$thongbao = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  
    $ho_ten = $_POST['ho_ten'];
    $email = $_POST['email'];
    $mat_khau = $_POST['mat_khau'];
    $so_dien_thoai = $_POST['so_dien_thoai'];
    $email_safe = $db->db->real_escape_string($email);
    $check = $db->db->query("SELECT * FROM khach_hang WHERE email = '$email_safe'");

    if ($check->num_rows > 0) {
        $thongbao = "<p style='color:red;'>❌ Email đã tồn tại.</p>";
    } else {
        $ket_qua = $db->DangKyKhachHang($ho_ten, $email, $mat_khau, $so_dien_thoai, ""); 
        
        if ($ket_qua) {
            header("Location: dangnhap.php");
            exit;
        } else {
            $thongbao = "<p style='color:red;'>❌ Lỗi đăng ký. Vui lòng thử lại.</p>";
   
        }
    }
}
?>
<h2>📝 Đăng ký tài khoản khách hàng</h2>
<?= $thongbao ?>
<form method="POST">
    <input type="text" name="ho_ten" placeholder="Họ tên" required><br>
    <input type="email" name="email" placeholder="Email" required><br>
    <input type="password" name="mat_khau" placeholder="Mật khẩu" required><br>
    <input type="text" name="so_dien_thoai" placeholder="Số điện thoại" required><br>
    <button type="submit">Đăng ký</button>
</form>
<p>Đã có tài khoản? <a href="dangnhap.php">Đăng nhập</a></p>