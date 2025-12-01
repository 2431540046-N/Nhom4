<?php
include 'DbAdmin.php';
$db = new DbAdmin();
$ds_phong = $db->Laydanhsachphong(); 
?>
<h2>📋 Danh sách phòng</h2>
<table border="1" cellpadding="8" cellspacing="0" style="width:100%; background:white;">
    <tr style="background:#007bff; color:white;">
        <th>Ảnh phòng</th>
        <th>Tên phòng</th>
        <th>Loại phòng</th>
        <th>Giá</th>
        <th>Mô tả</th>
        <th>Trạng thái</th>
        <th>Hành động</th>
    </tr>
    <?php while ($row = $ds_phong->fetch_assoc()): ?>
    <tr>
        <td>
            <?php if (!empty($row['hinh_anh'])): ?>
                <img src="<?= $row['hinh_anh'] ?>" width="100" style="object-fit:cover; border-radius:4px;">
            <?php else: ?>
                <span>Không có ảnh</span>
            <?php endif; ?>
        </td>
        <td><?= $row['ten_phong'] ?></td>
        <td><?= $row['ten_loai'] ?></td>
        <td><?= $row['gia'] ?> VND</td>
        <td><?= $row['mo_ta'] ?></td>
        <td><?= $row['trang_thai'] ?></td>
        <td>
            <a href="suaphong.php?id=<?= $row['id_phong'] ?>">
                <button style="background:#ffc107; border:none; padding:5px;">Sửa</button>
            </a>
            <a href="xoaphong.php?id=<?= $row['id_phong'] ?>" onclick="return confirm('Bạn có chắc muốn xóa phòng này?')">
                <button style="background:#dc3545; color:white; border:none; padding:5px;">Xóa</button>
            </a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>