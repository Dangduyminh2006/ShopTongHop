# ShopXpress - Website Thương Mại Điện Tử Bán Hàng Tổng Hợp

> **Dự án:** Website Thương Mại Điện Tử Bán Hàng Tổng Hợp — Báo cáo Đồ án Phát triển Ứng dụng Web  
> **Kiến trúc:** Pure PHP MVC | MySQL PDO Singleton | Vanilla CSS & Modern JavaScript | XAMPP  
> **Cảm hứng thiết kế:** Shopee, Tiki, Amazon (Giao diện hiện đại, hỗ trợ Dark / Light Theme & Song ngữ VI/EN)  

---

## 1. Yêu Cầu Môi Trường & Hệ Thống

- **Hệ điều hành:** Windows 10/11 (khuyến nghị) hoặc Linux/macOS
- **Phần mềm máy chủ:** XAMPP (Apache + MySQL/MariaDB)
- **Phiên bản PHP:** PHP >= 8.0 (kích hoạt các extension `pdo`, `pdo_mysql`, `mbstring`)
- **Hệ quản trị CSDL:** MySQL >= 8.0 hoặc MariaDB >= 10.4

---

## 2. Hướng Dẫn Cài Đặt Nhanh

1. Đặt thư mục dự án `ShopTongHop` vào thư mục gốc web của XAMPP:
   ```text
   C:\xampp\htdocs\ShopTongHop
   ```
2. Khởi động dịch vụ **Apache** và **MySQL** trên phần mềm **XAMPP Control Panel**.
3. Mở trình duyệt web và truy cập phpMyAdmin:
   ```text
   http://localhost/phpmyadmin
   ```
4. Tạo mới cơ sở dữ liệu có tên:
   - Tên database: `shop_tong_hop`
   - Bảng mã (Collation): `utf8mb4_unicode_ci`
5. Chọn tab **Import** và tải lên tệp CSDL:
   ```text
   docs/shop_tong_hop.sql
   ```
6. Kiểm tra cấu hình kết nối CSDL tại:
   ```text
   app/Config/config.php
   ```
7. Mở trình duyệt và truy cập hệ thống:
   - **Giao diện Khách hàng (Storefront):** [http://localhost/ShopTongHop](http://localhost/ShopTongHop)
   - **Giao diện Quản trị viên (Admin Portal):** [http://localhost/ShopTongHop/admin](http://localhost/ShopTongHop/admin)

---

## 3. Tài Khoản Đăng Nhập Mẫu

| Vai trò người dùng | Tên đăng nhập (Username) | Mật khẩu (Password) | Quyền hạn trong hệ thống |
|:---|:---|:---|:---|
| **Quản trị viên (Admin)** | `admin` | `123456` | Toàn quyền kiểm soát hệ thống, CRUD dữ liệu, duyệt đơn hàng |
| **Khách hàng mẫu (Customer)** | `testuser` | `123456` | Đặt hàng, áp voucher, đánh giá sản phẩm, quản lý lịch sử đơn |

---

## 4. Hệ Thống Tài Liệu Kỹ Thuật & Đồ Án

Các tài liệu kỹ thuật chi tiết bằng tiếng Việt đã được chuẩn hóa và đặt tên không dấu:

1. **[docs/THIET_KE_HE_THONG.md](docs/THIET_KE_HE_THONG.md)**  
   *Tài liệu Thiết Kế Kiến Trúc Hệ Thống:* Thiết kế chi tiết Pure PHP MVC độc lập, sơ đồ cấu trúc thư mục, thiết kế CSDL 12 bảng đạt chuẩn 3NF, đặc tả 7 Controllers, 12 Models, thiết kế bảo mật OWASP và hệ thống UI/UX design tokens.

2. **[docs/LO_TRINH_PHAT_TRIEN.md](docs/LO_TRINH_PHAT_TRIEN.md)**  
   *Kế hoạch Triển khai theo Giai đoạn:* Chi tiết lộ trình 6 giai đoạn phát triển, nguyên tắc kiểm thử bắt buộc (Create - Update - Delete Test với tiền tố `[TEST]`) và tiêu chí nghiệm thu từng module.

3. **[docs/YEU_CAU_BAO_CAO_DO_AN.md](docs/YEU_CAU_BAO_CAO_DO_AN.md)**  
   *Quy chuẩn & Đề cương Báo cáo Đồ án (Chuẩn HUBT):* Dựa trên kiến trúc mục lục mẫu chuẩn mực của đồ án môn học, phân bổ chi tiết 4 chương (25+ trang), dàn ý chi tiết từng mục con từ Lời cảm ơn, Danh mục từ viết tắt, Lời mở đầu, 4 Chương nội dung, Tổng kết, Kết luận và Tài liệu tham khảo.

---

## 5. Nguyên Tắc Thiết Kế Kỹ Thuật Cốt Lõi

- **Kiến trúc Pure PHP MVC tinh gọn:** Toàn bộ cấu trúc hệ thống, cơ sở dữ liệu quan hệ 12 bảng và bộ khung Pure PHP MVC hướng đối tượng được xây dựng bài bản, tối ưu hóa hiệu năng máy chủ và kiểm soát 100% luồng dữ liệu mà không cần framework cồng kềnh.
- **Tuân thủ chuẩn mực kỹ thuật cao:** Phân tách 3 tầng MVC rõ rệt, chuẩn hóa dữ liệu 3NF, bảo mật 100% PDO Prepared Statements chống SQL Injection, mã hóa mật khẩu an toàn bằng BCrypt và quy trình kiểm thử nghiêm ngặt trước khi bàn giao.
