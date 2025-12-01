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

$check_in_date = $_GET['check_in'] ?? date('Y-m-d');

$check_out_date = $_GET['check_out'] ?? date('Y-m-d', strtotime('+1 day'));

$so_khach = $_GET['so_khach'] ?? 1;

// - XỬ LÝ THÊM VÀO GIỎ HÀNG ---

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['them_gio_hang'])) {



    $id_phong = (int)$_POST['id_phong'];

    $result = $db->ThemPhongVaoGioHang($id_khach, $id_phong, 1, $check_in_date, $check_out_date); // Thêm 1 phòng
    if ($result) {

        $thong_bao = "<div class='success-msg'>✅ Đã thêm phòng vào giỏ hàng thành công!</div>";

    } else {

        $thong_bao = "<div class='error-msg'>❌ Lỗi khi thêm vào giỏ hàng. Vui lòng thử lại.</div>";

    }
}

$danh_sach_phong = $db->LayDanhSachPhongTrong($check_in_date, $check_out_date, $so_khach);

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Danh Sách Phòng Khách Sạn</title>
  <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; background-color: #f4f4f4; }
        .header { background-color: #007bff; color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; }
        .header a { color: white; text-decoration: none; margin-left: 20px; padding: 5px 10px; border-radius: 4px; }
        .header a.user-link { background-color: #dc3545; font-weight: bold; }
        .container { max-width: 1200px; margin: 20px auto; padding: 20px; }
        .search-form {
            background-color: #e9ecef; padding: 20px; border-radius: 8px; margin-bottom: 25px;
            display: flex; flex-wrap: wrap; gap: 10px; align-items: center; justify-content: space-between;
        }
        .search-form label { font-weight: bold; margin-right: 5px; }
        .search-form input, .search-form button { padding: 10px; border: 1px solid #ccc; border-radius: 4px; }
        .search-form button { background-color: #28a745; color: white; cursor: pointer; border: none; font-weight: bold; }
        .room-list { display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 20px; }
        .room-card {
            background-color: white; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); overflow: hidden;
            text-align: left; display: flex;
        }
        .room-image { min-width: 150px; height: 150px; background-color: #ddd; display: flex; align-items: center; justify-content: center; font-style: italic; color: #555; }
        .room-info { padding: 15px; flex-grow: 1; }
        .room-info h4 { margin-top: 0; color: #0056b3; font-size: 1.3em; }
        .room-info .price { font-size: 1.2em; color: #dc3545; font-weight: bold; margin-bottom: 10px; display: block; }
        .room-info button { background-color: #007bff; color: white; border: none; padding: 10px 15px; border-radius: 4px; cursor: pointer; margin-top: 10px; }
        .success-msg { padding: 10px; margin-bottom: 15px; background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; border-radius: 4px; text-align: center; }
        .error-msg { padding: 10px; margin-bottom: 15px; background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 4px; text-align: center; }
    </style>

</head>

<body>

    <div class="header">
        <a href="index_khachhang.php" style="margin-left: 0;">⬅️ Về Trang Chủ</a>
        <div>

            <a href="gio_hang.php">🛒 Giỏ Hàng</a>

            <a href="tai_khoan_ca_nhan.php" class="user-link">👤 <?= $ten_khach ?></a>

        </div>

    </div>

    <div class="container">
        <h2>Danh Sách Phòng Khách Sạn</h2>

        <?= $thong_bao ?>
        <div class="search-form">

            <form action="danhsachphongkhach.php" method="GET" style="display: contents;">


            </form>

        </div>


        <div class="room-list">
<?php
            if (empty($danh_sach_phong)): ?>
                <p style="grid-column: 1 / -1; text-align: center; font-size: 1.1em;">Không tìm thấy phòng trống phù hợp trong khoảng thời gian này.</p>
            <?php else:
                foreach ($danh_sach_phong as $phong):
            ?>
                <div class="room-card">
                    <div class="room-image">
                        <?php if (!empty($phong['hinh_anh'])): ?>
                            <img 
                                src="<?= htmlspecialchars($phong['hinh_anh']) ?>" 
                                alt="<?= htmlspecialchars($phong['ten_phong']) ?>"
                                style="width: 100%; height: 100%; object-fit: cover;"
                            >
                        <?php else: ?>
                            [Ảnh đang cập nhật]
                        <?php endif; ?>
                        </div>

                    <div class="room-info">

                        <h4><?= ($phong['ten_phong']) ?> (<?= ($phong['ten_loai']) ?>)</h4>



                        <span class="price"><?= number_format($phong['gia'], 0, ',', '.') ?> VNĐ / đêm</span>



                        <p style="font-size: 0.9em;"><?= ($phong['mo_ta']) ?></p>


                        <form method="POST" style="display: inline;">

                            <input type="hidden" name="id_phong" value="<?= $phong['id_phong'] ?>">

                            <input type="hidden" name="them_gio_hang" value="1">

                            <button type="submit">➕ Thêm vào Giỏ hàng</button>

                        </form>

                    </div>

                </div>
            <?php

                endforeach;
            endif;
            ?>

        </div>

    </div>

</body>

</html>