<?php
include 'DbAdmin.php';
$db = new DbAdmin();
$ds_hoadon = $db->Laydanhsachhoadon();
?>
<h2>📦 Danh sách hóa đơn</h2>
<table border="1" cellpadding="8" cellspacing="0" style="width:100%; background:white;">
    <tr style="background:#007bff; color:white;">
        <th>ID Đặt</th>
        <th>Tổng tiền</th>
        <th>Số tiền cọc</th>
        <th>Phương thức</th>
        <th>Ngày thanh toán</th>
        <th>Trạng thái</th>
    </tr>
    <?php while ($row = $ds_hoadon->fetch_assoc()): ?>
    <tr>
        <td><?= $row['id_dat'] ?></td>
        <td><?= $row['tong_tien'] ?> VND</td>
        <td><?= $row['so_tien_coc'] ?> VND</td>
        <td><?= $row['phuong_thuc'] ?></td>
        <td><?= $row['ngay_thanh_toan'] ?></td>
        <td><?= $row['trang_thai_tt'] ?></td>
    </tr>
    <?php endwhile; ?>
</table>