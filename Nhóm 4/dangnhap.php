<?php
include 'DbAdmin.php';
$db = new DbAdmin();
session_start();
$thongbao = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $mat_khau = $_POST['mat_khau'];
    $result = $db->Dangnhapkhach($email, $mat_khau);

    if ($result->num_rows === 1) {
        $_SESSION['khach'] = $result->fetch_assoc();
        header("Location: index_khachhang.php");
        exit;
    } else {
        $thongbao = "<p style='color:red;'>❌ Sai email hoặc mật khẩu.</p>";
    }
}
?>
<h2>🔐 Đăng nhập khách hàng</h2>
<?= $thongbao ?>
<form method="POST">
    <input type="email" name="email" placeholder="Email" required><br>
    <input type="password" name="mat_khau" placeholder="Mật khẩu" required><br>
    <button type="submit">Đăng nhập</button>
</form>
<p>Chưa có tài khoản? <a href="dangky.php">Đăng ký ngay</a></p>