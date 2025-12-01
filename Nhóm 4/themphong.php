<?php
include 'DbAdmin.php';
$db = new DbAdmin();
$ds_loai = $db->LayTenLoaiPhong();
$thongbao = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ten_phong   = $_POST['ten_phong'];
    $id_loai     = $_POST['id_loai'];
    $gia         = $_POST['gia'];
    $mo_ta       = $_POST['mo_ta'];
    $trang_thai  = $_POST['trang_thai'];
    $hinh_anh    = $_POST['hinh_anh'];

    if ($db->Themphong($ten_phong, $id_loai, $gia, $mo_ta, $trang_thai, $hinh_anh)) {
        header("Location: index.php?page=danhsachphong.php");
        exit;
    }
}
?>
<h2>➕ Thêm phòng</h2>
<?= $thongbao ?>
<form method="POST">
    <input type="text" name="ten_phong" placeholder="Tên phòng" required><br>
    <select name="id_loai">
        <?php while ($loai = $ds_loai->fetch_assoc()): ?>
        <option value="<?= $loai['id_loai'] ?>"><?= $loai['ten_loai'] ?></option>
        <?php endwhile; ?>
    </select><br>
    <input type="number" name="gia" placeholder="Giá" required><br>
    <textarea name="mo_ta" placeholder="Mô tả"></textarea><br>
    <select name="trang_thai">
        <option value="trống">Trống</option>
        <option value="đã đặt">Đã đặt</option>
        <option value="đang sửa chữa">Đang sửa chữa</option>
    </select><br>
    <input type="text" name="hinh_anh" placeholder="Đường dẫn ảnh (vd: img/phong101.jpg)" required><br>
    <button type="submit">Thêm phòng</button>
</form>