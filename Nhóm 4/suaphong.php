<?php
include 'DbAdmin.php';
$db = new DbAdmin();

$phong = null;

if (isset($_GET['id'])) {
    $id_phong = (int)$_GET['id'];
    $result = $db->db->query("SELECT * FROM phong WHERE id_phong = $id_phong");
    if ($result && $result->num_rows > 0) {
        $phong = $result->fetch_assoc();
    } else {
        echo "<p style='color:red;'>Không tìm thấy phòng với ID $id_phong.</p>";
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_phong   = $_POST['id_phong'];
    $ten_phong  = $_POST['ten_phong'];
    $id_loai    = $_POST['id_loai'];
    $gia        = $_POST['gia'];
    $mo_ta      = $_POST['mo_ta'];
    $trang_thai = $_POST['trang_thai'];
    $hinh_anh   = $_POST['hinh_anh'];

    $ket_qua = $db->Suaphong($id_phong, $ten_phong, $id_loai, $gia, $mo_ta, $trang_thai, $hinh_anh);

    if ($ket_qua) {
        header("Location: index.php?page=danhsachphong.php");
        exit;
    } else {
        echo "<p style='color:red;'>❌ Lỗi cập nhật phòng. Vui lòng kiểm tra lại dữ liệu.</p>";
    }
}
?>

<h2>✏️ Sửa thông tin phòng</h2>
<?php if ($phong): ?>
<form method="POST">
    <input type="hidden" name="id_phong" value="<?= $phong['id_phong'] ?>">

    <label>Tên phòng:</label>
    <input type="text" name="ten_phong" value="<?= $phong['ten_phong'] ?>" required><br>

    <label>ID loại phòng:</label>
    <input type="number" name="id_loai" value="<?= $phong['id_loai'] ?>" required><br>

    <label>Giá:</label>
    <input type="number" name="gia" value="<?= $phong['gia'] ?>" required><br>

    <label>Mô tả:</label>
    <textarea name="mo_ta"><?= $phong['mo_ta'] ?></textarea><br>

    <label>Trạng thái:</label>
    <select name="trang_thai">
        <option value="trống" <?= $phong['trang_thai'] == 'trống' ? 'selected' : '' ?>>Trống</option>
        <option value="đã đặt" <?= $phong['trang_thai'] == 'đã đặt' ? 'selected' : '' ?>>Đã đặt</option>
        <option value="đang sửa chữa" <?= $phong['trang_thai'] == 'đang sửa chữa' ? 'selected' : '' ?>>Đang sửa chữa</option>
    </select><br>

    <label>Ảnh phòng (link):</label>
    <input type="text" name="hinh_anh" value="<?= $phong['hinh_anh'] ?>"><br><br>

    <button type="submit">Cập nhật phòng</button>
</form>
<?php endif; ?>