<?php
class DbAdmin {
    public $host = 'localhost';
    public $dbname = 'website_dat_phong_khach_san';
    public $username = 'root';
    public $password = '';
    public $db;

    function __construct() {
        $this->db = new mysqli($this->host, $this->username, $this->password, $this->dbname);
        $this->db->set_charset('utf8');
    }

    function Dangnhapadmin($ten_dang_nhap, $mat_khau) {
        $sql = "SELECT * FROM admin WHERE ten_dang_nhap = '$ten_dang_nhap' AND mat_khau = '$mat_khau'";
        return $this->db->query($sql);
    }

    function Laydanhsachphong() {
        $sql = "SELECT * FROM phong JOIN loai_phong ON phong.id_loai = loai_phong.id_loai";
        return $this->db->query($sql);
    }

    function Themphong($ten_phong, $id_loai, $gia, $mo_ta, $trang_thai, $hinh_anh) {
    $sql = "INSERT INTO phong (ten_phong, id_loai, gia, mo_ta, trang_thai, hinh_anh)
            VALUES ('$ten_phong', $id_loai, $gia, '$mo_ta', '$trang_thai', '$hinh_anh')";
    return $this->db->query($sql);
    }

    function Dangnhapkhach($email, $mat_khau) {
    $sql = "SELECT * FROM khach_hang WHERE email = '$email' AND mat_khau = '$mat_khau'";
    return $this->db->query($sql);
    }

    function LayTenLoaiPhong() {
        $sql = "SELECT id_loai, ten_loai FROM loai_phong ORDER BY ten_loai ASC";
        return $this->db->query($sql);
    }
    function Xoaphong($id_phong) {
        $sql = "DELETE FROM phong WHERE id_phong = $id_phong";
        return $this->db->query($sql);
    }
    public function Laydanhsachhoadon() {
    $sql = "SELECT * FROM hoa_don";
    return $this->db->query($sql);
    }
    function layphongbangid($id_phong) {
        $id = (int) $id_phong;
        $sql = "SELECT p.*, l.ten_loai FROM phong p JOIN loai_phong l ON p.id_loai = l.id_loai WHERE p.id_phong = $id";
        $result = $this->db->query($sql);
        return $result ? $result->fetch_assoc() : null;
    }
    function danhsachloaiphong(){
        $sql="select * from loai_phong";
        $kq = $this->db->query($sql);
        return $kq;
    }

    function themMoiPhongVaoGioHang($id_phong){
       $sql = "SELECT * FROM phong WHERE id_phong = '{$id_phong} '";
       $kq = $this -> db -> query($sql);
       return $kq;
    }

    function DangKyKhachHang($ho_ten, $email, $mat_khau, $sdt, $dia_chi) {
    $ho_ten_safe = $this->db->real_escape_string($ho_ten);
    $email_safe = $this->db->real_escape_string($email);
    $mat_khau_safe = $this->db->real_escape_string($mat_khau); 
    $sdt_safe = $this->db->real_escape_string($sdt);


    $sql = "INSERT INTO khach_hang (ho_ten, email, mat_khau, so_dien_thoai)
            VALUES ('$ho_ten_safe', '$email_safe', '$mat_khau_safe', '$sdt_safe')";

    return $this->db->query($sql);
}
public function Suaphong($id_phong, $ten_phong, $id_loai, $gia, $mo_ta, $trang_thai, $hinh_anh) {
    $sql = "UPDATE phong 
            SET ten_phong='$ten_phong', 
                id_loai='$id_loai', 
                gia='$gia', 
                mo_ta='$mo_ta', 
                trang_thai='$trang_thai', 
                hinh_anh='$hinh_anh' 
            WHERE id_phong=$id_phong";
    return $this->db->query($sql);
}
function LayDanhSachPhongTrong($ngay_nhan, $ngay_tra, $so_khach) {
        $ngay_nhan_safe = $this->db->real_escape_string($ngay_nhan);
        $ngay_tra_safe = $this->db->real_escape_string($ngay_tra);

        $sql = "
            SELECT 
                p.id_phong, p.ten_phong, lp.ten_loai, p.gia, p.mo_ta, p.hinh_anh
            FROM 
                phong p 
            JOIN 
                loai_phong lp ON p.id_loai = lp.id_loai
            WHERE 
                p.id_phong NOT IN (
                    SELECT 
                        id_phong
                    FROM 
                        don_hang
                    WHERE  (< '$ngay_tra_safe' AND ngay_tra > '$ngay_nhan_safe')  
                        
                )
            AND p.trang_thai = 'trống'
            ORDER BY p.id_phong
        ";

        $result = $this->db->query($sql);
   
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
function ThemPhongVaoGioHang($id_khach, $id_phong, $so_luong, $ngay_nhan, $ngay_tra) {
 
        $so_luong = (int)$so_luong; 

        return $this->ThemPhongVaoDBGioHang($id_khach, $id_phong, $so_luong);
    }



    function ThemPhongVaoDBGioHang($id_khach_hang, $id_phong, $so_luong = 1) {
       
        $id_gio_hang = $this->LayHoacTaoGioHang($id_khach_hang);
        if (!$id_gio_hang) return false;

        $id_phong = (int)$id_phong;
        $so_luong = (int)$so_luong;

  
        $sql_check = "SELECT id_chi_tiet, so_luong FROM chi_tiet_gio_hang WHERE id_gio_hang = $id_gio_hang AND id_phong = $id_phong";
        $result_check = $this->db->query($sql_check);

        if ($result_check && $result_check->num_rows > 0) {
         
            $row = $result_check->fetch_assoc();
            $new_so_luong = $row['so_luong'] + $so_luong;
            $sql_update = "UPDATE chi_tiet_gio_hang SET so_luong = $new_so_luong WHERE id_chi_tiet = " . (int)$row['id_chi_tiet'];
            
            return $this->db->query($sql_update);
        } else {
         
            $sql_insert = "INSERT INTO chi_tiet_gio_hang (id_gio_hang, id_phong, so_luong) 
                            VALUES ($id_gio_hang, $id_phong, $so_luong)";
            return $this->db->query($sql_insert);
        }
    }
    
 
function LayDanhSachGioHang($id_khach) {
        $id_khach_hang = (int) $id_khach;
        
        $sql_gio_hang = "SELECT id_gio_hang FROM gio_hang WHERE id_khach = $id_khach_hang AND trang_thai = 'open'";
        $result_gio_hang = $this->db->query($sql_gio_hang);

        if (!$result_gio_hang || $result_gio_hang->num_rows == 0) {
            return []; 
        }

        $id_gio_hang = (int)$result_gio_hang->fetch_assoc()['id_gio_hang'];

       
        $sql = "SELECT 
                    ctg.id_chi_tiet, ctg.id_phong, ctg.so_luong, 
                    ctg.ngay_nhan, ctg.ngay_tra, 
                    p.ten_phong, p.gia, 
                    lp.ten_loai AS loai_phong
                FROM 
                    chi_tiet_gio_hang ctg
                JOIN 
                    phong p ON ctg.id_phong = p.id_phong
                JOIN 
                    loai_phong lp ON p.id_loai = lp.id_loai
                WHERE 
                    ctg.id_gio_hang = $id_gio_hang
                ORDER BY ctg.ngay_nhan, ctg.id_chi_tiet";
            
        $result = $this->db->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }


function CapNhatGioHang($id_chi_tiet, $so_luong_moi) {
        $id_chi_tiet = (int) $id_chi_tiet;
        $so_luong_moi = (int) $so_luong_moi;

        $sql = "UPDATE chi_tiet_gio_hang 
                SET so_luong = $so_luong_moi 
                WHERE id_chi_tiet = $id_chi_tiet";
        
        return $this->db->query($sql);
    }


     function XoaPhongKhoiGioHang($id_chi_tiet) {
        $id_chi_tiet = (int) $id_chi_tiet;

        $sql = "DELETE FROM chi_tiet_gio_hang 
                WHERE id_chi_tiet = $id_chi_tiet";
        
        return $this->db->query($sql);
    }
    private function LayHoacTaoGioHang($id_khach_hang) {
        $id_khach_hang = (int)$id_khach_hang;
        $sql_select = "SELECT id_gio_hang FROM gio_hang WHERE id_khach = $id_khach_hang AND trang_thai = 'open'";
        $result = $this->db->query($sql_select);

        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc()['id_gio_hang'];
        } else {
            $sql_insert = "INSERT INTO gio_hang (id_khach, ngay_tao, trang_thai) VALUES ($id_khach_hang, NOW(), 'open')";
            if ($this->db->query($sql_insert)) {
                return $this->db->insert_id; 
            } else {
                return false;
            }
        }
    }
    function TaoDonHangTuGioHang($id_khach, $phuong_thuc, $tong_tien, $ghi_chu) {
    $this->db->begin_transaction();
    
    try {
        $id_khach = (int) $id_khach;
        $tong_tien = (float) $tong_tien;
        $phuong_thuc_safe = $this->db->real_escape_string($phuong_thuc);
        $ghi_chu_safe = $this->db->real_escape_string($ghi_chu);
        
        $sql_gio = "SELECT id_gio_hang FROM gio_hang WHERE id_khach = $id_khach AND trang_thai = 'open'";
        $result_gio = $this->db->query($sql_gio);
        if (!$result_gio || $result_gio->num_rows == 0) {
            $this->db->rollback();
            return false; 
        }
        $id_gio_hang = (int)$result_gio->fetch_assoc()['id_gio_hang'];

        $sql_insert_don = "
            INSERT INTO don_hang (id_khach, ngay_dat, tong_tien, trang_thai, phuong_thuc_thanh_toan, ghi_chu)
            VALUES ($id_khach, NOW(), $tong_tien, 'chờ xác nhận', '$phuong_thuc_safe', '$ghi_chu_safe')
        ";
        if (!$this->db->query($sql_insert_don)) {
            $this->db->rollback();
            return false;
        }
        $id_don_hang = $this->db->insert_id;

    
        $sql_chuyen_chi_tiet = "
            INSERT INTO chi_tiet_don_hang (id_don_hang, id_phong, so_luong, ngay_nhan, ngay_tra, gia_tai_thoi_diem_dat)
            SELECT 
                $id_don_hang, 
                ctg.id_phong, 
                ctg.so_luong, 
                ctg.ngay_nhan, 
                ctg.ngay_tra, 
                p.gia -- Lấy giá phòng hiện tại
            FROM chi_tiet_gio_hang ctg
            JOIN phong p ON ctg.id_phong = p.id_phong
            WHERE ctg.id_gio_hang = $id_gio_hang
        ";
        if (!$this->db->query($sql_chuyen_chi_tiet)) {
            $this->db->rollback();
            return false;
        }

     
        $sql_xoa_chi_tiet = "DELETE FROM chi_tiet_gio_hang WHERE id_gio_hang = $id_gio_hang";
        $this->db->query($sql_xoa_chi_tiet);
        
        $sql_dong_gio = "UPDATE gio_hang SET trang_thai = 'completed' WHERE id_gio_hang = $id_gio_hang";
        $this->db->query($sql_dong_gio);

        $this->db->commit();
        return true;

    } catch (Exception $e) {
        $this->db->rollback();
   
        return false;
    }
}

function LayTatCaDanhSachGioHang() {
    $sql = "
        SELECT 
            ctg.id_chi_tiet, 
            ctg.so_luong, 
            ctg.ngay_nhan, 
            ctg.ngay_tra, 
            p.ten_phong,           
            p.gia,                 
            lp.ten_loai AS loai_phong,
            kh.ho_ten AS ten_khach,  
            gh.id_gio_hang,
            gh.trang_thai
        FROM 
            chi_tiet_gio_hang ctg
        JOIN 
            gio_hang gh ON ctg.id_gio_hang = gh.id_gio_hang
        JOIN 
            khach_hang kh ON gh.id_khach = kh.id_khach
        JOIN 
            phong p ON ctg.id_phong = p.id_phong
        JOIN 
            loai_phong lp ON p.id_loai = lp.id_loai
        WHERE 
            gh.trang_thai = 'open'  -- Chỉ lấy các giỏ hàng đang hoạt động
        ORDER BY 
            kh.ho_ten, ctg.ngay_nhan, ctg.id_chi_tiet
    ";
            
    $result = $this->db->query($sql);
    
    return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}


function TaoDonDatPhong($id_khach, $phuong_thuc, $tong_tien, $ghi_chu) {
    $id_khach = (int)$id_khach;
    $tong_tien = (float)$tong_tien;
    $phuong_thuc_safe = $this->db->real_escape_string($phuong_thuc);
    $ghi_chu_safe = $this->db->real_escape_string($ghi_chu);
    $ngay_dat = date('Y-m-d');
    $master_id_dat = 0; 
    

    $this->db->begin_transaction(); 

    try {
        $sql_gio_hang = "
            SELECT ctg.*, p.gia FROM chi_tiet_gio_hang ctg
            JOIN gio_hang gh ON ctg.id_gio_hang = gh.id_gio_hang
            JOIN phong p ON ctg.id_phong = p.id_phong
            WHERE gh.id_khach = $id_khach AND gh.trang_thai = 'open'
        ";
        $result_ct_gio = $this->db->query($sql_gio_hang);

        if (!$result_ct_gio || $result_ct_gio->num_rows == 0) {
            $this->db->rollback(); 
            return 0; 
        }
        
        $count = 0;
  
        while ($ct_gio = $result_ct_gio->fetch_assoc()) {
    
            $sql_insert_don = "
                INSERT INTO don_hang 
                (id_khach, id_phong, ngay_dat, ngay_nhan, ngay_tra, ghi_chu, trang_thai)
                VALUES (
                    $id_khach, 
                    {$ct_gio['id_phong']}, 
                    '$ngay_dat', 
                    '{$ct_gio['ngay_nhan']}', 
                    '{$ct_gio['ngay_tra']}', 
                    '$ghi_chu_safe', 
                    'chờ xác nhận'
                )
            ";
            
            if (!$this->db->query($sql_insert_don)) {
                $this->db->rollback(); 
                return 0; 
            }
            
            $id_dat_vua_tao = $this->db->insert_id;
            
            if ($count == 0) {
                $master_id_dat = $id_dat_vua_tao; 
            }
            $count++;
        }
 
        $sql_hoa_don = "
            INSERT INTO hoa_don (id_dat, tong_tien, phuong_thuc, ngay_thanh_toan, trang_thai_tt)
            VALUES ($master_id_dat, $tong_tien, '$phuong_thuc_safe', '$ngay_dat', 'chưa thanh toán')
        ";
        if (!$this->db->query($sql_hoa_don)) {
             $this->db->rollback(); 
             return 0; 
        }
        
        $sql_get_gh = "SELECT id_gio_hang FROM gio_hang WHERE id_khach = $id_khach AND trang_thai = 'open'";
        $result_gh = $this->db->query($sql_get_gh);
        $id_gio_hang = $result_gh ? $result_gh->fetch_assoc()['id_gio_hang'] : 0;
        
        $this->db->query("DELETE FROM chi_tiet_gio_hang WHERE id_gio_hang = $id_gio_hang");
        $this->db->query("UPDATE gio_hang SET trang_thai = 'closed' WHERE id_gio_hang = $id_gio_hang"); 

      
        $this->db->commit();
        return $master_id_dat; 
        
    } catch (Exception $e) {
        $this->db->rollback();

        return 0;
    }
}

function CapNhatChiTietGioHang($id_chi_tiet, $so_luong_moi, $ngay_nhan_moi, $ngay_tra_moi) {
    $id_chi_tiet = (int)$id_chi_tiet;
    $so_luong_moi = (int)$so_luong_moi;
    
    $ngay_nhan_safe = $this->db->real_escape_string($ngay_nhan_moi);
    $ngay_tra_safe = $this->db->real_escape_string($ngay_tra_moi);

    if (strtotime($ngay_tra_safe) <= strtotime($ngay_nhan_safe)) {
        return false; 
    }
    

    $sql = "UPDATE chi_tiet_gio_hang 
            SET 
                ngay_nhan = '$ngay_nhan_safe', 
                ngay_tra = '$ngay_tra_safe',
                so_luong = $so_luong_moi  
            WHERE id_chi_tiet = $id_chi_tiet";

    return $this->db->query($sql);
}



function formatVND($amount) {
    return number_format(round($amount), 0, ',', '.') . ' VNĐ';
}


function tinhSoDem($ngay_nhan, $ngay_tra) {
    $ngay_nhan_ts = strtotime($ngay_nhan);
    $ngay_tra_ts = strtotime($ngay_tra);

    if (!$ngay_nhan_ts || !$ngay_tra_ts || $ngay_tra_ts <= $ngay_nhan_ts) {
        return 0; 
    }

    $so_dem_sec = $ngay_tra_ts - $ngay_nhan_ts;
    $so_dem = max(0, round($so_dem_sec / (60 * 60 * 24)));
    
    if ($so_dem == 0 && $so_dem_sec > 0) {
         $so_dem = 1; 
    }
    
    return $so_dem;
}
function ThemChiTietDatPhong($id_dat, $id_phong, $ngay_nhan, $ngay_tra, $gia, $so_dem) {
        $id_dat = (int)$id_dat;
        $id_phong = (int)$id_phong;
        $so_dem = (int)$so_dem;
        $gia = (float)$gia;


        $ngay_nhan_safe = $this->db->real_escape_string($ngay_nhan);
        $ngay_tra_safe = $this->db->real_escape_string($ngay_tra);
        
        $so_luong_phong = 1; 

        $sql = "INSERT INTO chi_tiet_dat_phong (id_don, id_phong, so_luong, ngay_nhan, ngay_tra, gia_luc_dat, so_dem) 
                VALUES ($id_dat, $id_phong, $so_luong_phong, '$ngay_nhan_safe', '$ngay_tra_safe', $gia, $so_dem)";

        return $this->db->query($sql);
    }
    

    public function XoaToanBoGioHang($id_khach) {
        $id_khach = (int)$id_khach;

        $sql_gio = "SELECT id_gio_hang FROM gio_hang WHERE id_khach = $id_khach AND trang_thai = 'open'";
        $result_gio = $this->db->query($sql_gio);

        if (!$result_gio || $result_gio->num_rows == 0) {
            return true; 
        }
        $id_gio_hang = (int)$result_gio->fetch_assoc()['id_gio_hang'];

      
        $this->db->begin_transaction();
        
        try {
         
            $sql_xoa_chi_tiet = "DELETE FROM chi_tiet_gio_hang WHERE id_gio_hang = $id_gio_hang";
            $this->db->query($sql_xoa_chi_tiet);
            
      
            $sql_dong_gio = "UPDATE gio_hang SET trang_thai = 'completed' WHERE id_gio_hang = $id_gio_hang";
            $result = $this->db->query($sql_dong_gio);

            $this->db->commit();
            return $result;
            
        } catch (Exception $e) {
            $this->db->rollback();

            return false;
        }
    }

   
 
    function LayThongTinDonHang($id_dat_chinh, $id_khach_hien_tai) {
    $id_dat_chinh = (int)$id_dat_chinh;
    $id_khach_hien_tai = (int)$id_khach_hien_tai;

    $sql_chinh = "
        SELECT 
            hd.tong_tien, hd.phuong_thuc, hd.trang_thai_tt, hd.ngay_thanh_toan,
            dh.ngay_dat, dh.ghi_chu, dh.trang_thai as trang_thai_don,
            kh.ho_ten, kh.email, kh.so_dien_thoai
        FROM 
            hoa_don hd
        JOIN 
            don_hang dh ON hd.id_dat = dh.id_dat 
        JOIN 
            khach_hang kh ON dh.id_khach = kh.id_khach
        WHERE 
            hd.id_dat = $id_dat_chinh AND dh.id_khach = $id_khach_hien_tai
    ";
    
    $result_chinh = $this->db->query($sql_chinh);
    
    if (!$result_chinh || $result_chinh->num_rows == 0) {
        return null; 
    }

    $don_hang = $result_chinh->fetch_assoc();


    $ngay_dat = $this->db->real_escape_string($don_hang['ngay_dat']);
    $ghi_chu = $this->db->real_escape_string($don_hang['ghi_chu']);

    $sql_chi_tiet = "
        SELECT 
            dh.ngay_nhan, dh.ngay_tra, dh.trang_thai, dh.id_phong, 
            p.ten_phong, p.gia, lp.ten_loai
        FROM 
            don_hang dh
        JOIN 
            phong p ON dh.id_phong = p.id_phong
        JOIN 
            loai_phong lp ON p.id_loai = lp.id_loai
        WHERE 
            dh.id_khach = $id_khach_hien_tai
            AND dh.ngay_dat = '$ngay_dat'
            AND dh.ghi_chu = '$ghi_chu'
        ORDER BY dh.id_dat ASC
    ";
    
    $result_chi_tiet = $this->db->query($sql_chi_tiet);
    
    $chi_tiet = [];
    if ($result_chi_tiet) {
        while ($row = $result_chi_tiet->fetch_assoc()) {
            $so_dem = $this->tinhSoDem($row['ngay_nhan'], $row['ngay_tra']);
    
            $thanh_tien = $row['gia'] * $so_dem * 1; 
            $row['so_dem'] = $so_dem;
            $row['thanh_tien'] = $thanh_tien;
            $chi_tiet[] = $row;
        }
    }


    $don_hang['chi_tiet_phong'] = $chi_tiet;

    return $don_hang;
}

}




?>
