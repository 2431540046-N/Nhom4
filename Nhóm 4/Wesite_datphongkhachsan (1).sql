CREATE DATABASE IF NOT EXISTS website_dat_phong_khach_san
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE website_dat_phong_khach_san;



DROP TABLE IF EXISTS gio_hang;
DROP TABLE IF EXISTS hoa_don;
DROP TABLE IF EXISTS don_hang;
DROP TABLE IF EXISTS phong;
DROP TABLE IF EXISTS loai_phong;
DROP TABLE IF EXISTS khach_hang;
DROP TABLE IF EXISTS admin;


CREATE TABLE admin (
    id_admin INT(11) AUTO_INCREMENT PRIMARY KEY,
    ten_dang_nhap VARCHAR(50) UNIQUE NOT NULL,
    mat_khau VARCHAR(255) NOT NULL, 
    ho_ten VARCHAR(100),
    email VARCHAR(100) UNIQUE
);


CREATE TABLE loai_phong (
    id_loai INT(11) AUTO_INCREMENT PRIMARY KEY,
    ten_loai VARCHAR(100) NOT NULL UNIQUE,
    mo_ta TEXT
);



CREATE TABLE phong (
    id_phong INT(11) AUTO_INCREMENT PRIMARY KEY,
    ten_phong VARCHAR(100) NOT NULL UNIQUE,
    id_loai INT(11),
    gia DECIMAL(10,2) NOT NULL,
    mo_ta TEXT,
    hinh_anh VARCHAR(255), 
    trang_thai ENUM('trống', 'đã đặt', 'đang sửa chữa') DEFAULT 'trống',
    FOREIGN KEY (id_loai) REFERENCES loai_phong(id_loai)
   
);

CREATE TABLE khach_hang (
    id_khach INT(11) AUTO_INCREMENT PRIMARY KEY,
    ho_ten VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    mat_khau VARCHAR(255) NOT NULL, 
    so_dien_thoai VARCHAR(20)
);


CREATE TABLE don_hang (
    id_dat INT(11) AUTO_INCREMENT PRIMARY KEY,
    id_khach INT(11),
    id_phong INT(11),
    ngay_dat DATE NOT NULL,
    ngay_nhan DATE NOT NULL,
    ngay_tra DATE NOT NULL,
    ghi_chu TEXT,
    trang_thai ENUM('chờ xác nhận', 'đã xác nhận', 'đã hủy', 'đã check-in', 'hoàn thành') DEFAULT 'chờ xác nhận',
    
    FOREIGN KEY (id_khach) REFERENCES khach_hang(id_khach),
    FOREIGN KEY (id_phong) REFERENCES phong(id_phong),
    

    CONSTRAINT check_ngay_thue CHECK (ngay_nhan <= ngay_tra)
);


CREATE TABLE hoa_don (
    id_thanh_toan INT(11) AUTO_INCREMENT PRIMARY KEY,
    id_dat INT(11) UNIQUE, 
    tong_tien DECIMAL(10,2) NOT NULL,
    so_tien_coc DECIMAL(10,2) DEFAULT 0.00,
    phuong_thuc ENUM('tiền mặt', 'chuyển khoản', 'ví điện tử', 'thẻ tín dụng') NOT NULL,
    ngay_thanh_toan DATE,
    trang_thai_tt ENUM('chưa thanh toán', 'đã cọc', 'đã thanh toán đủ', 'hoàn tiền') DEFAULT 'chưa thanh toán',
    
    FOREIGN KEY (id_dat) REFERENCES don_hang(id_dat)
);


CREATE TABLE gio_hang (
    id_gio_hang INT(11) AUTO_INCREMENT PRIMARY KEY,
    id_khach INT(11) NOT NULL,
    ngay_tao DATETIME NOT NULL,
    trang_thai ENUM('open', 'closed', 'ordered') DEFAULT 'open', 
    FOREIGN KEY (id_khach) REFERENCES khach_hang(id_khach)
);

CREATE TABLE chi_tiet_gio_hang (
    id_chi_tiet INT(11) AUTO_INCREMENT PRIMARY KEY,
    id_gio_hang INT(11) NOT NULL,                  
    id_phong INT(11) NOT NULL,                     
    so_luong INT(11) NOT NULL DEFAULT 1,
    ngay_nhan DATE NOT NULL, 
    ngay_tra DATE NOT NULL,  
    
    FOREIGN KEY (id_gio_hang) REFERENCES gio_hang(id_gio_hang),
    FOREIGN KEY (id_phong) REFERENCES phong(id_phong),
    

    UNIQUE KEY (id_gio_hang, id_phong, ngay_nhan, ngay_tra) 
);

INSERT INTO admin (ten_dang_nhap, mat_khau, ho_ten, email) VALUES
('ketoan_vp', 'ketoan', 'Đỗ Thị F', 'ketoan@ks.vn'),
('letan_ca1', 'letan_ca1', 'Hoàng Văn G', 'letan1@ks.vn'),
('letan_ca2', 'letan_ca2', 'Trần Hữu H', 'letan2@ks.vn');

INSERT INTO loai_phong (ten_loai, mo_ta) VALUES
('Phòng đơn', 'Phòng tiêu chuẩn dành cho một khách, trang bị giường đơn hoặc giường đôi nhỏ.'),
('Phòng đôi', 'Phòng cao cấp dành cho hai khách, diện tích rộng rãi, nội thất hiện đại, có tầm nhìn đẹp.'),
('Phòng gia đình', 'Phòng rộng rãi dành cho gia đình, nằm ở tầng trệt với lối đi riêng ra hồ bơi.');



INSERT INTO phong (ten_phong, id_loai, gia, mo_ta, trang_thai) VALUES
('P101', 1, 500000.00, 'Tầng 1, view sân vườn', 'trống'),      
('P102', 1, 500000.00, 'Tầng 1, gần sảnh', 'trống'),           
('P201', 1, 500000.00, 'Tầng 2, view nội bộ', 'đã đặt'),         
('P205', 2, 850000.00, 'Tầng 2, view thành phố', 'trống'),     
('P206', 2, 850000.00, 'Tầng 2, view nội bộ', 'đã đặt'),         
('P303', 2, 850000.00, 'Tầng 3, view thành phố', 'trống'),     
('P301', 3, 2000000.00, 'Tầng 3, có bồn tắm Jacuzzi', 'trống'), 
('P401', 3, 2000000.00, 'Phòng Suite lớn, tầng cao', 'đang sửa chữa'), 
('P402', 3, 2000000.00, 'Phòng lớn cho gia đình 4 người', 'trống'), 
('P502', 3, 2000000.00, 'Phòng gia đình view đẹp', 'trống');



INSERT INTO khach_hang (ho_ten, email, mat_khau, so_dien_thoai) VALUES
('Phạm Minh Khôi', 'khoi.pm@mail.com', 'pass_kh1', '0911223344'), 
('Võ Thị Ánh', 'anh.vo@mail.com', 'pass_kh2', '0988776655'),     
('Nguyễn Đức Tài', 'tai.nguyen@mail.com', 'pass_kh3', '0900112233'),
('Đặng Phương Thảo', 'thao.dang@mail.com', 'pass_kh4', '0944556677'),
('Bùi Văn F', 'buif@example.com', 'mkabcdef', '0977889900'), 
('Lê Hoàng Long', 'long.le@mail.com', 'pass_kh5', '0933445566'),   
('Mai Thu Hiền', 'hien.mai@mail.com', 'pass_kh6', '0977889900'),   
('Trần Văn Kiên', 'kien.tran@mail.com', 'pass_kh7', '0966554433'), 
('Nguyễn Thị Lý', 'ly.nguyen@mail.com', 'pass_kh8', '0922113300'), 
('Phan Đình Tùng', 'tung.phan@mail.com', 'pass_kh9', '0909090909');


INSERT INTO don_hang (id_khach, id_phong, ngay_dat, ngay_nhan, ngay_tra, ghi_chu, trang_thai) VALUES
(1, 1, '2025-10-15', '2025-10-20', '2025-10-22', 'Yêu cầu tầng thấp', 'chờ xác nhận'),  
(2, 2, '2025-10-16', '2025-10-21', '2025-10-23', 'Đi công tác', 'đã xác nhận'),      
(3, 3, '2025-10-18', '2025-10-25', '2025-10-28', 'Du lịch cá nhân', 'chờ xác nhận'),  
(4, 4, '2025-10-18', '2025-10-26', '2025-10-29', 'Phòng yên tĩnh', 'đã xác nhận'),   
(5, 5, '2025-10-19', '2025-10-27', '2025-10-30', 'Có bếp riêng', 'đã hủy'),          
(6, 7, '2025-11-04', '2025-11-05', '2025-11-06', 'Phòng gần thang máy', 'đã check-in'),
(7, 8, '2025-11-05', '2025-11-07', '2025-11-10', 'Thanh toán trọn gói', 'hoàn thành'),
(8, 9, '2025-11-06', '2025-11-25', '2025-11-26', 'Đi công tác ngắn', 'chờ xác nhận'),
(9, 1, '2025-11-07', '2025-11-28', '2025-12-02', 'Yêu cầu 2 bữa sáng', 'đã xác nhận'),
(10, 6, '2025-11-08', '2025-12-05', '2025-12-06', NULL, 'đã hủy');                   



INSERT INTO hoa_don (id_dat, tong_tien, so_tien_coc, phuong_thuc, ngay_thanh_toan, trang_thai_tt) VALUES
(1, 1000000.00, 200000.00, 'chuyển khoản', '2025-10-16', 'đã cọc'),
(2, 1500000.00, 200000.00, 'tiền mặt', '2025-10-17', 'đã cọc'),
(3, 3000000.00, 150000.00, 'ví điện tử', '2025-10-18', 'đã cọc'),
(4, 3600000.00, 300000.00, 'chuyển khoản', '2025-10-18', 'đã cọc'),
(5, 2850000.00, 120000.00, 'tiền mặt', '2025-10-19', 'hoàn tiền'),
(6, 850000.00, 850000.00, 'tiền mặt', '2025-11-05', 'đã thanh toán đủ'),
(7, 1500000.00, 150000.00, 'chuyển khoản', '2025-11-05', 'đã cọc'),
(8, 850000.00, 100000.00, 'ví điện tử', '2025-11-06', 'đã cọc'),
(9, 2000000.00, 500000.00, 'thẻ tín dụng', '2025-11-07', 'đã cọc'),
(10, 500000.00, 50000.00, 'tiền mặt', '2025-11-08', 'hoàn tiền');


INSERT INTO gio_hang (id_khach, ngay_tao, trang_thai) VALUES
(1, NOW(), 'open'), 
(3, NOW(), 'open'); 


UPDATE phong 
SET hinh_anh = 'images/P101.jpg' 
WHERE id_phong = 1;


UPDATE phong 
SET hinh_anh = 'images/P102.jpg' 
WHERE id_phong = 2;

UPDATE phong 
SET hinh_anh = 'images/P201.jpg' 
WHERE id_phong = 3;

UPDATE phong 
SET hinh_anh = 'images/P205.jpg' 
WHERE id_phong = 4;


UPDATE phong 
SET hinh_anh = 'images/P206.jpg' 
WHERE id_phong = 5;


UPDATE phong 
SET hinh_anh = 'images/P303.jpg' 
WHERE id_phong = 6;

UPDATE phong 
SET hinh_anh = 'images/P301.jpg' 
WHERE id_phong = 7;


UPDATE phong 
SET hinh_anh = 'images/P401.jpg' 
WHERE id_phong = 8;

UPDATE phong 
SET hinh_anh = 'images/P402.jpg' 
WHERE id_phong = 9;

UPDATE phong 
SET hinh_anh = 'images/P502.jpg' 
WHERE id_phong = 10;