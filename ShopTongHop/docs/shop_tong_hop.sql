-- ===================================================================
-- ShopXpress - Co So Du Lieu Thuong Mai Dien Tu Ban Hang Tong Hop
-- Phien ban: 1.0.0
-- Kien truc: Chuẩn hóa 3NF (12 bảng quan hệ)
-- Bảng mã: utf8mb4_unicode_ci | Storage Engine: InnoDB
-- ===================================================================

CREATE DATABASE IF NOT EXISTS `shop_tong_hop` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `shop_tong_hop`;

-- Đảm bảo toàn bộ câu lệnh nạp dữ liệu tiếng Việt được mã hóa UTF-8 chuẩn xác
SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci;
SET CHARACTER SET utf8mb4;
SET character_set_client = utf8mb4;
SET character_set_connection = utf8mb4;
SET character_set_results = utf8mb4;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `order_items`;
DROP TABLE IF EXISTS `orders`;
DROP TABLE IF EXISTS `flash_sale_items`;
DROP TABLE IF EXISTS `flash_sales`;
DROP TABLE IF EXISTS `vouchers`;
DROP TABLE IF EXISTS `news`;
DROP TABLE IF EXISTS `banners`;
DROP TABLE IF EXISTS `reviews`;
DROP TABLE IF EXISTS `product_images`;
DROP TABLE IF EXISTS `products`;
DROP TABLE IF EXISTS `brands`;
DROP TABLE IF EXISTS `categories`;
DROP TABLE IF EXISTS `user_addresses`;
DROP TABLE IF EXISTS `users`;
SET FOREIGN_KEY_CHECKS = 1;

-- --------------------------------------------------------
-- 1. Bảng users (Tài khoản người dùng và Quản trị viên)
-- --------------------------------------------------------
CREATE TABLE `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `fullname` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `phone` VARCHAR(20) DEFAULT NULL,
  `address` TEXT DEFAULT NULL,
  `role` ENUM('admin', 'customer') NOT NULL DEFAULT 'customer',
  `isAdmin` TINYINT(1) NOT NULL DEFAULT 0,
  `isDelete` TINYINT(1) NOT NULL DEFAULT 0,
  `status` ENUM('active', 'banned') NOT NULL DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 1.1 Bảng user_addresses (Sổ địa chỉ nhận hàng của người dùng)
-- --------------------------------------------------------
CREATE TABLE `user_addresses` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `recipient_name` VARCHAR(100) NOT NULL,
  `recipient_phone` VARCHAR(20) NOT NULL,
  `province` VARCHAR(100) DEFAULT NULL,
  `district` VARCHAR(100) DEFAULT NULL,
  `ward` VARCHAR(100) DEFAULT NULL,
  `street_address` VARCHAR(255) DEFAULT NULL,
  `full_address` VARCHAR(500) NOT NULL,
  `is_default` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_address_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 2. Bảng categories (Danh mục sản phẩm đa cấp Cha - Con)
-- --------------------------------------------------------
CREATE TABLE `categories` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `slug` VARCHAR(100) NOT NULL UNIQUE,
  `parent_id` INT DEFAULT NULL,
  `icon` VARCHAR(50) DEFAULT 'fa-folder',
  `image` VARCHAR(255) DEFAULT NULL,
  `display_order` INT NOT NULL DEFAULT 0,
  `status` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_category_parent` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 3. Bảng brands (Thương hiệu phân phối chính hãng)
-- --------------------------------------------------------
CREATE TABLE `brands` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `slug` VARCHAR(100) NOT NULL UNIQUE,
  `logo` VARCHAR(255) DEFAULT NULL,
  `status` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 4. Bảng products (Thông tin sản phẩm trung tâm)
-- --------------------------------------------------------
CREATE TABLE `products` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `category_id` INT NOT NULL,
  `brand_id` INT DEFAULT NULL,
  `name` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL UNIQUE,
  `sku` VARCHAR(50) NOT NULL UNIQUE,
  `price` DECIMAL(12, 2) NOT NULL,
  `discount_percent` INT NOT NULL DEFAULT 0,
  `stock_quantity` INT NOT NULL DEFAULT 0,
  `thumbnail` VARCHAR(500) NOT NULL,
  `summary` TEXT DEFAULT NULL,
  `description` LONGTEXT DEFAULT NULL,
  `specifications` JSON DEFAULT NULL,
  `is_featured` TINYINT(1) NOT NULL DEFAULT 0,
  `is_new` TINYINT(1) NOT NULL DEFAULT 1,
  `view_count` INT NOT NULL DEFAULT 0,
  `status` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_product_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `fk_product_brand` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 5. Bảng product_images (Thư viện nhiều hình ảnh sản phẩm)
-- --------------------------------------------------------
CREATE TABLE `product_images` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `product_id` INT NOT NULL,
  `image_url` VARCHAR(500) NOT NULL,
  `sort_order` INT NOT NULL DEFAULT 0,
  CONSTRAINT `fk_image_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 6. Bảng reviews (Đánh giá và xếp hạng sao sản phẩm)
-- --------------------------------------------------------
CREATE TABLE `reviews` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `product_id` INT NOT NULL,
  `user_id` INT NOT NULL,
  `rating` TINYINT NOT NULL DEFAULT 5,
  `comment` TEXT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_review_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_review_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 7. Bảng banners (Banner trình chiếu quảng cáo trang chủ)
-- --------------------------------------------------------
CREATE TABLE `banners` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `subtitle` VARCHAR(255) DEFAULT NULL,
  `image_url` VARCHAR(500) NOT NULL,
  `link_url` VARCHAR(255) DEFAULT NULL,
  `sort_order` INT NOT NULL DEFAULT 0,
  `status` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 8. Bảng news (Tin tức & bài viết cẩm nang mua sắm)
-- --------------------------------------------------------
CREATE TABLE `news` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL UNIQUE,
  `summary` TEXT DEFAULT NULL,
  `content` LONGTEXT NOT NULL,
  `thumbnail` VARCHAR(500) NOT NULL,
  `author_id` INT NOT NULL,
  `view_count` INT NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_news_author` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 9. Bảng vouchers (Mã coupon giảm giá thông minh)
-- --------------------------------------------------------
CREATE TABLE `vouchers` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `code` VARCHAR(50) NOT NULL UNIQUE,
  `discount_type` ENUM('percent', 'fixed') NOT NULL DEFAULT 'percent',
  `discount_value` DECIMAL(12, 2) NOT NULL,
  `min_order_amount` DECIMAL(12, 2) NOT NULL DEFAULT 0,
  `usage_limit` INT NOT NULL DEFAULT 100,
  `used_count` INT NOT NULL DEFAULT 0,
  `start_date` DATETIME NOT NULL,
  `end_date` DATETIME NOT NULL,
  `status` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 10. Bảng flash_sales (Chiến dịch giảm giá chớp nhoáng)
-- --------------------------------------------------------
CREATE TABLE `flash_sales` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `start_time` DATETIME NOT NULL,
  `end_time` DATETIME NOT NULL,
  `status` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 11. Bảng flash_sale_items (Mặt hàng nằm trong Flash Sale)
-- --------------------------------------------------------
CREATE TABLE `flash_sale_items` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `flash_sale_id` INT NOT NULL,
  `product_id` INT NOT NULL,
  `sale_price` DECIMAL(12, 2) NOT NULL,
  `sale_quantity` INT NOT NULL DEFAULT 10,
  `sold_quantity` INT NOT NULL DEFAULT 0,
  CONSTRAINT `fk_fsi_sale` FOREIGN KEY (`flash_sale_id`) REFERENCES `flash_sales` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_fsi_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 12. Bảng orders (Đơn đặt hàng khách hàng)
-- --------------------------------------------------------
CREATE TABLE `orders` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `order_code` VARCHAR(50) NOT NULL UNIQUE,
  `user_id` INT DEFAULT NULL,
  `customer_name` VARCHAR(100) NOT NULL,
  `customer_email` VARCHAR(100) NOT NULL,
  `customer_phone` VARCHAR(20) NOT NULL,
  `shipping_address` TEXT NOT NULL,
  `voucher_code` VARCHAR(50) DEFAULT NULL,
  `discount_amount` DECIMAL(12, 2) NOT NULL DEFAULT 0,
  `total_amount` DECIMAL(12, 2) NOT NULL,
  `payment_method` ENUM('cod', 'vietqr') NOT NULL DEFAULT 'cod',
  `payment_status` ENUM('pending', 'paid', 'failed') NOT NULL DEFAULT 'pending',
  `order_status` ENUM('pending', 'confirmed', 'shipping', 'completed', 'cancelled') NOT NULL DEFAULT 'pending',
  `notes` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_order_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 13. Bảng order_items (Chi tiết mặt hàng trong từng đơn)
-- --------------------------------------------------------
CREATE TABLE `order_items` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `order_id` INT NOT NULL,
  `product_id` INT NOT NULL,
  `product_name` VARCHAR(255) NOT NULL,
  `product_price` DECIMAL(12, 2) NOT NULL,
  `quantity` INT NOT NULL DEFAULT 1,
  `subtotal` DECIMAL(12, 2) NOT NULL,
  CONSTRAINT `fk_item_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_item_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================================
-- DỮ LIỆU KHỞI TẠO MẪU (INITIAL SEED DATA)
-- ========================================================

-- Mật khẩu mặc định là: 123456
INSERT INTO `users` (`id`, `username`, `password`, `fullname`, `email`, `phone`, `address`, `role`, `isAdmin`, `isDelete`, `status`) VALUES
(1, 'admin', '$2y$10$SV4ZdzAgxiDg9XGam1D67eLbLD.zxOKgtV5ufnlq/YZGlz.OWGnne', 'Quản Trị Viên Hệ Thống', 'admin@shoptonghop.vn', '0988888888', 'Hà Nội, Việt Nam', 'admin', 1, 0, 'active'),
(2, 'testuser', '$2y$10$SV4ZdzAgxiDg9XGam1D67eLbLD.zxOKgtV5ufnlq/YZGlz.OWGnne', 'Nguyễn Văn Khách Hàng', 'testuser@gmail.com', '0912345678', 'Số 123 Đường Cầu Giấy, Hà Nội', 'customer', 0, 0, 'active'),
(3, 'tranhoa', '$2y$10$SV4ZdzAgxiDg9XGam1D67eLbLD.zxOKgtV5ufnlq/YZGlz.OWGnne', 'Trần Thị Hoa', 'tranhoa@gmail.com', '0934567890', 'Số 45 Lê Lợi, Quận 1, TP. Hồ Chí Minh', 'customer', 0, 0, 'active');

-- Sổ địa chỉ nhận hàng mặc định cho người dùng
INSERT INTO `user_addresses` (`id`, `user_id`, `recipient_name`, `recipient_phone`, `province`, `district`, `ward`, `street_address`, `full_address`, `is_default`) VALUES
(1, 1, 'Quản Trị Viên Hệ Thống', '0988888888', 'Hà Nội', 'Cầu Giấy', 'Dịch Vọng Hậu', 'Số 1 Tôn Thất Thuyết', 'Số 1 Tôn Thất Thuyết, Dịch Vọng Hậu, Cầu Giấy, Hà Nội', 1),
(2, 2, 'Nguyễn Văn Khách Hàng', '0912345678', 'Hà Nội', 'Cầu Giấy', 'Quan Hoa', 'Số 123 Đường Cầu Giấy', 'Số 123 Đường Cầu Giấy, Quan Hoa, Cầu Giấy, Hà Nội', 1),
(3, 2, 'Nguyễn Văn Khách Hàng (Văn phòng)', '0912345678', 'Hà Nội', 'Nam Từ Liêm', 'Mỹ Đình 1', 'Tòa nhà Keangnam', 'Tòa nhà Keangnam, Phạm Hùng, Mỹ Đình 1, Nam Từ Liêm, Hà Nội', 0),
(4, 3, 'Trần Thị Hoa', '0934567890', 'TP. Hồ Chí Minh', 'Quận 1', 'Bến Nghé', 'Số 45 Lê Lợi', 'Số 45 Lê Lợi, Bến Nghé, Quận 1, TP. Hồ Chí Minh', 1);

-- Danh mục sản phẩm (Cha & Con)
INSERT INTO `categories` (`id`, `name`, `slug`, `parent_id`, `icon`, `image`, `display_order`, `status`) VALUES
(1, 'Điện Tử & Công Nghệ', 'dien-tu-cong-nghe', NULL, 'fa-laptop', 'cat_electronics.jpg', 1, 1),
(2, 'Thời Trang & Phụ Kiện', 'thoi-trang-phu-kien', NULL, 'fa-tshirt', 'cat_fashion.jpg', 2, 1),
(3, 'Đồ Gia Dụng & Đời Sống', 'gia-dung-doi-song', NULL, 'fa-blender', 'cat_appliances.jpg', 3, 1),
(4, 'Sách & Văn Phòng Phẩm', 'sach-van-phong-pham', NULL, 'fa-book', 'cat_books.jpg', 4, 1),
(5, 'Thể Thao & Dã Ngoại', 'the-thao-da-ngoai', NULL, 'fa-running', 'cat_sports.jpg', 5, 1),
(6, 'Sức Khỏe & Làm Đẹp', 'suc-khoe-lam-dep', NULL, 'fa-spa', 'cat_beauty.jpg', 6, 1),
-- Danh mục con
(7, 'Điện Thoại & Máy Tính Bảng', 'dien-thoai-may-tinh-bang', 1, 'fa-mobile-alt', NULL, 1, 1),
(8, 'Laptop & Máy Tính Để Bàn', 'laptop-may-tinh-de-ban', 1, 'fa-laptop', NULL, 2, 1),
(9, 'Thiết Bị Âm Thanh & Tai Nghe', 'thiet-bi-am-thanh', 1, 'fa-headphones', NULL, 3, 1),
(10, 'Đồng Hồ Thông Minh', 'dong-ho-thong-minh', 1, 'fa-clock', NULL, 4, 1),
(11, 'Thời Trang Nam', 'thoi-trang-nam', 2, 'fa-male', NULL, 1, 1),
(12, 'Thời Trang Nữ', 'thoi-trang-nu', 2, 'fa-female', NULL, 2, 1),
(13, 'Thiết Bị Nhà Bếp', 'thiet-bi-nha-bep', 3, 'fa-utensils', NULL, 1, 1);

-- Thương hiệu
INSERT INTO `brands` (`id`, `name`, `slug`, `logo`, `status`) VALUES
(1, 'Apple', 'apple', 'apple_logo.png', 1),
(2, 'Samsung', 'samsung', 'samsung_logo.png', 1),
(3, 'Sony', 'sony', 'sony_logo.png', 1),
(4, 'ASUS', 'asus', 'asus_logo.png', 1),
(5, 'Nike', 'nike', 'nike_logo.png', 1),
(6, 'Philips', 'philips', 'philips_logo.png', 1),
(7, 'NXB Trẻ', 'nxb-tre', 'nxbtre_logo.png', 1);

-- Sản phẩm phong phú kèm thông số JSON linh hoạt
INSERT INTO `products` (`id`, `category_id`, `brand_id`, `name`, `slug`, `sku`, `price`, `discount_percent`, `stock_quantity`, `thumbnail`, `summary`, `description`, `specifications`, `is_featured`, `is_new`, `view_count`, `status`) VALUES
(1, 7, 2, 'Điện thoại Samsung Galaxy S24 Ultra 5G 256GB', 'samsung-galaxy-s24-ultra-5g', 'SP-SS-S24U', 28990000.00, 15, 45, 'https://images.unsplash.com/photo-1610945265064-0e34e5519bbf?auto=format&fit=crop&w=800&q=80', 
 'Flagship đỉnh cao trang bị Galaxy AI thông minh, khung viền Titan siêu bền bỉ và camera 200MP đột phá.',
 '<p>Samsung Galaxy S24 Ultra đánh dấu bước ngoặt mới về trí tuệ nhân tạo trên di động. Khung viền Titan sang trọng cùng màn hình phẳng 6.8 inch Dynamic AMOLED 2X 120Hz mang đến trải nghiệm hiển thị mãn nhãn và độ bền ấn tượng.</p><p>Hệ thống camera zoom quang học 5x cùng độ phân giải 200MP tái hiện chân thực từng chi tiết ngay cả trong điều kiện thiếu sáng.</p>',
 '{"ManHinh": "6.8 inch Dynamic AMOLED 2X 120Hz", "Chipset": "Snapdragon 8 Gen 3 for Galaxy", "RAM": "12 GB", "BoNhoTrong": "256 GB", "CameraSau": "Chính 200 MP & Phụ 50 MP, 12 MP, 10 MP", "Pin": "5000 mAh (Sạc nhanh 45W)"}',
 1, 1, 1250, 1),

(2, 10, 1, 'Đồng hồ thông minh Apple Watch Series 9 GPS 45mm', 'apple-watch-series-9-gps-45mm', 'SP-AP-AW9', 10490000.00, 10, 30, 'https://images.unsplash.com/photo-1508685096489-7aacd43bd3b1?auto=format&fit=crop&w=800&q=80',
 'Chiếc đồng hồ thông minh thế hệ mới với chip S9 SiP mạnh mẽ, thao tác chạm hai lần Double Tap kỳ diệu.',
 '<p>Apple Watch Series 9 sở hữu màn hình sáng gấp đôi thế hệ trước (lên tới 2000 nits), hỗ trợ theo dõi sức khỏe chuyên sâu như đo nồng độ oxy trong máu SpO2, điện tâm đồ ECG và phát hiện va chạm tự động.</p>',
 '{"KichThuoc": "45 mm", "MatKinh": "Ion-X strengthened glass", "ThoiLuongPin": "18 giờ (Chế độ tiết kiệm 36 giờ)", "ChongNuoc": "50 mét (WR50)", "KetNoi": "Bluetooth 5.3, Wi-Fi 4"}',
 1, 1, 890, 1),

(3, 8, 4, 'Laptop ASUS Vivobook 15 OLED i5-13500H', 'laptop-asus-vivobook-15-oled', 'SP-AS-VB15', 17990000.00, 12, 25, 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?auto=format&fit=crop&w=800&q=80',
 'Laptop mỏng nhẹ thời trang sở hữu màn hình OLED rực rỡ, hiệu năng vượt trội từ vi xử lý Intel Core i5 thế hệ 13.',
 '<p>ASUS Vivobook 15 OLED là sự lựa chọn lý tưởng cho sinh viên và dân văn phòng cần màn hình đẹp để thiết kế đồ họa hoặc giải trí. Máy nặng chỉ 1.7kg, bản lề mở phẳng 180 độ tiện lợi.</p>',
 '{"CPU": "Intel Core i5-13500H (12 nhân 16 luồng)", "RAM": "16 GB DDR4 3200MHz", "OChua": "512 GB SSD NVMe M.2", "ManHinh": "15.6 inch OLED Full HD 600 nits", "TrongLuong": "1.7 kg"}',
 1, 1, 720, 1),

(4, 9, 3, 'Tai nghe không dây Sony WH-1000XM5 Chống Ồn Cao Cấp', 'tai-nghe-sony-wh-1000xm5', 'SP-SN-XM5', 8490000.00, 18, 50, 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=800&q=80',
 'Đỉnh cao công nghệ chống ồn với bộ xử lý tích hợp V1, 8 micro thu âm và thiết kế đeo êm ái suốt cả ngày dài.',
 '<p>Sony WH-1000XM5 tiếp tục khẳng định vị thế dẫn đầu trong dòng tai nghe chống ồn chủ động. Thời lượng pin ấn tượng lên đến 30 giờ và hỗ trợ sạc nhanh 3 phút nghe được 3 giờ.</p>',
 '{"LoaiTaiNghe": "Over-ear Chống ồn chủ động (ANC)", "Driver": "30 mm Carbon Fiber", "ThoiLuongPin": "30 giờ (bật ANC)", "CongNghe": "LDAC, DSEE Extreme, Hi-Res Audio", "TrongLuong": "250 gram"}',
 1, 0, 960, 1),

(5, 13, 6, 'Nồi chiên không dầu điện tử Philips HD9280/90 6.2L', 'noi-chien-khong-dau-philips-hd9280', 'SP-PL-AF9280', 3290000.00, 25, 60, 'https://images.unsplash.com/photo-1584269600464-37b1b58a9fe7?auto=format&fit=crop&w=800&q=80',
 'Dung tích lớn 6.2L chiên gà nguyên con, công nghệ Rapid Air giảm đến 90% lượng dầu mỡ, kết nối Wifi thông minh.',
 '<p>Nồi chiên không dầu Philips HD9280 size XL giúp chuẩn bị bữa ăn ngon lành mạnh cho cả gia đình từ 5-6 người. Có thể điều khiển và theo dõi quá trình nấu thông qua ứng dụng NutriU trên điện thoại.</p>',
 '{"DungTich": "6.2 Lít (Giỏ chứa 1.2 kg)", "CongSuat": "2000 W", "CongNghe": "Rapid Air tuần hoàn khí nóng", "BangDieuKhien": "Cảm ứng điện tử 7 chế độ cài sẵn", "XuatXu": "Chính hãng Philips"}',
 1, 0, 1100, 1),

(6, 5, 5, 'Giày Thể Thao Nam Nike Air Max 270 Chính Hãng', 'giay-nike-air-max-270', 'SP-NK-AM270', 3850000.00, 10, 80, 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?auto=format&fit=crop&w=800&q=80',
 'Đệm khí Air Max gót chân siêu dày 32mm mang lại cảm giác êm ái vượt trội cho từng bước chạy và vận động thể thao.',
 '<p>Nike Air Max 270 mang phong cách hiện đại với phần thân dệt vải co giãn thoáng khí ôm sát bàn chân, đế cao su chống trượt bền bỉ phù hợp cho cả chạy bộ và dạo phố thường ngày.</p>',
 '{"ChatLieu": "Vải lưới dệt Knit thoáng khí", "BoDem": "Air Max 270 độ đàn hồi cao", "Size": "40, 41, 42, 43, 44", "MoiTruongSuDung": "Chạy bộ, gym, thời trang thể thao", "XuatXu": "Chính hãng Nike"}',
 0, 1, 640, 1),

(7, 11, 5, 'Áo Khoác Bomber Nam Phối Màu Trẻ Trung Cao Cấp', 'ao-khoac-bomber-nam-unisex', 'SP-TT-BB01', 450000.00, 20, 120, 'https://images.unsplash.com/photo-1551028719-00167b16eac5?auto=format&fit=crop&w=800&q=80',
 'Thiết kế form suông Unisex thời thượng, chất liệu gió tráng bạc 2 lớp chống gió, chống thấm nước nhẹ hiệu quả.',
 '<p>Áo khoác bomber là item không thể thiếu trong tủ đồ giới trẻ. Bo thun cổ áo, cổ tay và gấu áo dệt kim mềm mại giữ ấm hoàn hảo cho những ngày giao mùa.</p>',
 '{"ChatLieu": "Gió chéo 2 lớp mềm mại", "KieuDang": "Bomber Unisex trẻ trung", "Size": "M (45-55kg), L (55-65kg), XL (65-78kg)", "MauSac": "Đen phối Kem, Xanh Navy phối Trắng"}',
 0, 1, 430, 1),

(8, 4, 7, 'Combo 5 Cuốn Sách Tư Duy & Phát Triển Bản Thân Bán Chạy', 'combo-5-cuon-sach-tu-duy', 'SP-SACH-CB5', 399000.00, 30, 150, 'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?auto=format&fit=crop&w=800&q=80',
 'Bộ sách tinh hoa rèn luyện kỹ năng giao tiếp, quản lý tài chính và tư duy tích cực giúp bạn bứt phá thành công.',
 '<p>Bộ sách gồm 5 tác phẩm kinh điển: Đắc Nhân Tâm, Quẳng Gánh Lo Đi Và Vui Sống, Nghĩ Giàu Làm Giàu, Dạy Con Làm Giàu Tập 1 và Nhà Giả Kim. Sách in bìa mềm giấy vàng chống lóa bảo vệ mắt.</p>',
 '{"NhaXuatBan": "NXB Trẻ & NXB Tổng Hợp", "SoTrang": "Khoảng 1500 trang trọn bộ", "HinhThuc": "Bìa mềm tay gập", "NgonNgu": "Tiếng Việt dịch chuẩn"}',
 0, 0, 850, 1);

-- Thư viện hình ảnh sản phẩm bổ sung
INSERT INTO `product_images` (`product_id`, `image_url`, `sort_order`) VALUES
(1, 'https://images.unsplash.com/photo-1610945265064-0e34e5519bbf?auto=format&fit=crop&w=800&q=80', 1),
(2, 'https://images.unsplash.com/photo-1508685096489-7aacd43bd3b1?auto=format&fit=crop&w=800&q=80', 1),
(3, 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?auto=format&fit=crop&w=800&q=80', 1),
(4, 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=800&q=80', 1),
(5, 'https://images.unsplash.com/photo-1584269600464-37b1b58a9fe7?auto=format&fit=crop&w=800&q=80', 1),
(6, 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?auto=format&fit=crop&w=800&q=80', 1),
(7, 'https://images.unsplash.com/photo-1551028719-00167b16eac5?auto=format&fit=crop&w=800&q=80', 1),
(8, 'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?auto=format&fit=crop&w=800&q=80', 1);

-- Đánh giá sản phẩm mẫu
INSERT INTO `reviews` (`product_id`, `user_id`, `rating`, `comment`) VALUES
(1, 2, 5, 'Máy cầm đầm tay, màn hình phẳng rất đẹp, camera zoom 5x chụp nét căng cả trong điều kiện trời tối!'),
(2, 2, 5, 'Tính năng Double Tap cực kỳ tiện lợi khi đang bận tay nấu ăn hoặc lái xe, pin dùng thoải mái 1 ngày rưỡi.'),
(4, 3, 5, 'Chống ồn của Sony không có gì để chê, ngồi trong quán cà phê ồn ào mà bật nhạc lên là yên tĩnh tuyệt đối.'),
(5, 3, 4, 'Nồi to chiên nguyên con gà 1.8kg vừa in, giòn rụm bên ngoài mọng nước bên trong. Rất đáng tiền!');

-- Banners trình chiếu trang chủ
INSERT INTO `banners` (`title`, `subtitle`, `image_url`, `link_url`, `sort_order`, `status`) VALUES
('ĐẠI TIỆC CÔNG NGHỆ 2026', 'Giảm sốc tới 50% toàn bộ phụ kiện và thiết bị thông minh', 'https://images.unsplash.com/photo-1550009158-9ebf69173e03?auto=format&fit=crop&w=1200&q=80', '/products?cat=1', 1, 1),
('BỘ SƯU TẬP THỜI TRANG HÈ', 'Xu hướng phong cách năng động cho giới trẻ', 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?auto=format&fit=crop&w=1200&q=80', '/products?cat=2', 2, 1),
('GIA DỤNG THÔNG MINH - BẾP XINH', 'Nâng tầm chất lượng bữa ăn gia đình', 'https://images.unsplash.com/photo-1556911220-e15b29be8c8f?auto=format&fit=crop&w=1200&q=80', '/products?cat=3', 3, 1);

-- Tin tức bài viết
INSERT INTO `news` (`id`, `title`, `slug`, `summary`, `content`, `thumbnail`, `author_id`, `view_count`) VALUES
(1, 'Đánh giá chi tiết Samsung Galaxy S24 Ultra: Kỷ nguyên mới của trí tuệ nhân tạo', 'danh-gia-samsung-galaxy-s24-ultra',
 'Galaxy S24 Ultra không chỉ sở hữu cấu hình mạnh mẽ nhất thị trường mà còn mang đến trải nghiệm AI đột phá cho công việc.',
 '<p>Samsung Galaxy S24 Ultra là một trong những chiếc smartphone toàn diện nhất năm 2026. Với thiết kế viền Titan sang trọng, màn hình chống chói vượt bậc và hệ thống camera 200MP xuất sắc, máy đáp ứng trọn vẹn cả nhu cầu sáng tạo lẫn giải trí đỉnh cao.</p><p>Các tính năng AI như phiên dịch cuộc gọi trực tiếp, khoanh vùng tìm kiếm thông minh Circle to Search đã chứng minh tính hữu dụng thực tế trong đời sống hàng ngày.</p>',
 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?auto=format&fit=crop&w=800&q=80', 1, 450),

(2, 'Apple Watch Series 9 và những nâng cấp đáng giá cho sức khỏe', 'apple-watch-series-9-nang-cap-suc-khoe',
 'Tìm hiểu chip S9 SiP và các tính năng cảm biến sinh trắc học mới nhất giúp theo dõi sức khỏe toàn diện.',
 '<p>Chiếc đồng hồ thông minh của Apple ngày càng trở thành một trợ lý sức khỏe cá nhân không thể thiếu. Từ khả năng đo điện tâm đồ ECG chính xác, phát hiện va chạm xe đến theo dõi chu kỳ giấc ngủ chuyên sâu.</p>',
 'https://images.unsplash.com/photo-1510017803434-a899398421b3?auto=format&fit=crop&w=800&q=80', 1, 380),

(3, 'Xu hướng thời trang dạo phố năng động và phóng khoáng năm 2026', 'xu-huong-thoi-trang-streetwear-2026',
 'Gợi ý phối đồ với áo bomber, quần túi hộp và giày sneaker cá tính đón đầu trào lưu.',
 '<p>Thời trang đường phố năm nay hướng đến sự tối giản nhưng giàu điểm nhấn cá nhân. Sự kết hợp giữa các tông màu trung tính cùng phom dáng rộng rãi mang lại cảm giác thoải mái tối đa cho người mặc.</p>',
 'https://images.unsplash.com/photo-1483985988355-763728e1935b?auto=format&fit=crop&w=800&q=80', 1, 290);

-- Mã giảm giá thông minh (Vouchers)
INSERT INTO `vouchers` (`code`, `discount_type`, `discount_value`, `min_order_amount`, `usage_limit`, `used_count`, `start_date`, `end_date`, `status`) VALUES
('WELCOME', 'fixed', 50000.00, 200000.00, 500, 15, '2026-01-01 00:00:00', '2026-12-31 23:59:59', 1),
('SALE10', 'percent', 10.00, 500000.00, 200, 42, '2026-01-01 00:00:00', '2026-12-31 23:59:59', 1),
('FREESHIP', 'fixed', 30000.00, 300000.00, 1000, 120, '2026-01-01 00:00:00', '2026-12-31 23:59:59', 1),
('VIPMEMBER', 'percent', 15.00, 2000000.00, 50, 8, '2026-01-01 00:00:00', '2026-12-31 23:59:59', 1);

-- Chiến dịch Flash Sale (Đang diễn ra)
INSERT INTO `flash_sales` (`id`, `title`, `start_time`, `end_time`, `status`) VALUES
(1, 'GIỜ VÀNG GIÁ SỐC - DEAL HỜI CUỐI TUẦN', DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_ADD(NOW(), INTERVAL 7 DAY), 1);

-- Mặt hàng Flash Sale
INSERT INTO `flash_sale_items` (`flash_sale_id`, `product_id`, `sale_price`, `sale_quantity`, `sold_quantity`) VALUES
(1, 1, 24990000.00, 20, 14),
(1, 4, 6990000.00, 30, 22),
(1, 5, 2490000.00, 40, 29);

-- Đơn hàng mẫu
INSERT INTO `orders` (`id`, `order_code`, `user_id`, `customer_name`, `customer_email`, `customer_phone`, `shipping_address`, `voucher_code`, `discount_amount`, `total_amount`, `payment_method`, `payment_status`, `order_status`, `notes`) VALUES
(1, 'SX2026-8801', 2, 'Nguyễn Văn Khách Hàng', 'testuser@gmail.com', '0912345678', 'Số 123 Đường Cầu Giấy, Hà Nội', 'WELCOME', 50000.00, 24590000.00, 'vietqr', 'paid', 'completed', 'Giao giờ hành chính giúp tôi'),
(2, 'SX2026-8802', 3, 'Trần Thị Hoa', 'tranhoa@gmail.com', '0934567890', 'Số 45 Lê Lợi, Quận 1, TP. Hồ Chí Minh', 'FREESHIP', 30000.00, 420000.00, 'cod', 'pending', 'pending', 'Giao sau 18h tối');

-- Mặt hàng trong đơn mẫu
INSERT INTO `order_items` (`order_id`, `product_id`, `product_name`, `product_price`, `quantity`, `subtotal`) VALUES
(1, 1, 'Điện thoại Samsung Galaxy S24 Ultra 5G 256GB', 24640000.00, 1, 24640000.00),
(2, 7, 'Áo Khoác Bomber Nam Phối Màu Trẻ Trung Cao Cấp', 450000.00, 1, 450000.00);
