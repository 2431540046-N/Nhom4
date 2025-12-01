<?php
include 'DbAdmin.php';
$db = new DbAdmin();

if (isset($_GET['id'])) {
    $id_phong = $_GET['id'];

    if ($db->Xoaphong($id_phong)) {
        header("Location: index.php?page=danhsachphong.php");
        exit;
    } else {
        echo "<p style='color:red;'>❌ Lỗi khi xóa phòng: " . $db->db->error . "</p>";
    }
} else {
    echo "<p style='color:red;'>Không có ID phòng để xóa.</p>";
}
?>