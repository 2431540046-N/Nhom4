<?php
session_start();
include 'DbAdmin.php';
$db = new DbAdmin();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ten_dang_nhap = $_POST['username'];
    $mat_khau = $_POST['password'];
    $result = $db->Dangnhapadmin($ten_dang_nhap, $mat_khau);

    if ($result->num_rows == 1) {
        $_SESSION['admin'] = $result->fetch_assoc();
        header('Location: index.php');
        exit;
    } else {
        $error = "Sai tên đăng nhập hoặc mật khẩu!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Đăng nhập Admin</title>
</head>
<body>
    <h2>Đăng nhập</h2>
    <?php if (isset($error)) : ?>
    <p style="color:red;"><?php echo $error; ?></p>
    <?php endif; ?>
    <form method="POST">
        <input type="text" name="username" placeholder="Tên đăng nhập" required><br>
        <input type="password" name="password" placeholder="Mật khẩu" required><br>
        <button type="submit">Đăng nhập</button>
    </form>
</body>
</html>