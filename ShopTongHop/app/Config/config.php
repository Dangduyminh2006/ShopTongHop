<?php
/**
 * ShopXpress - System Configuration File
 * Pure PHP MVC System Configuration
 * Tương thích 100% môi trường XAMPP trên Windows
 */

// 1. Cấu hình báo lỗi & mã hóa ký tự UTF-8 Tiếng Việt chuẩn mực
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

ini_set('default_charset', 'UTF-8');
if (function_exists('mb_internal_encoding')) {
    mb_internal_encoding('UTF-8');
}
if (function_exists('mb_http_output')) {
    mb_http_output('UTF-8');
}

// 2. Thiết lập múi giờ Việt Nam
date_default_timezone_set('Asia/Ho_Chi_Minh');

// 3. Định nghĩa các đường dẫn thư mục tuyệt đối trên máy chủ
define('ROOT_PATH', dirname(__DIR__, 2));
define('APP_PATH', ROOT_PATH . DIRECTORY_SEPARATOR . 'app');
define('PUBLIC_PATH', ROOT_PATH . DIRECTORY_SEPARATOR . 'public');
define('UPLOADS_PATH', PUBLIC_PATH . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'uploads');

// 4. Tự động nhận diện Base URL linh hoạt cho mọi máy chủ và thư mục trên XAMPP
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
$baseFolder = trim(preg_replace('#/public$#', '', $scriptDir), '/');

if (!empty($baseFolder)) {
    define('BASE_URL', $protocol . $host . '/' . $baseFolder);
} else {
    define('BASE_URL', $protocol . $host);
}

// 5. Đường dẫn tài nguyên tĩnh (Static Assets & Uploads)
define('ASSETS_URL', BASE_URL . '/public/assets');
define('UPLOADS_URL', BASE_URL . '/public/assets/uploads');

// 6. Thông tin hệ thống ứng dụng
define('APP_NAME', 'ShopXpress');
define('APP_SLOGAN', 'Mua sắm thông minh - Nhận hàng nhanh chóng');
define('APP_VERSION', '1.0.0');

// 7. Cấu hình Cơ sở dữ liệu MySQL (PDO)
// Sử dụng IP 127.0.0.1 để kết nối socket TCP ổn định trên Windows
define('DB_HOST', '127.0.0.1');
define('DB_PORT', '3306');
define('DB_NAME', 'shop_tong_hop');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');
define('DB_COLLATION', 'utf8mb4_unicode_ci');

// 8. Cấu hình Controller và Action mặc định
define('DEFAULT_CONTROLLER', 'HomeController');
define('DEFAULT_ACTION', 'index');

// 9. Cấu hình thông tin tài khoản ngân hàng nhận thanh toán VietQR (Mô phỏng)
define('VIETQR_BANK_ID', 'MB');             // Mã ngân hàng: MB Bank
define('VIETQR_ACCOUNT_NO', '0988888888');   // Số tài khoản
define('VIETQR_ACCOUNT_NAME', 'SHOPXPRESS VIET NAM'); // Chủ tài khoản
define('VIETQR_TEMPLATE', 'compact2');       // Mẫu giao diện QR

// ====================================================================
// CÁC HÀM TIỆN ÍCH DÙNG CHUNG TOÀN HỆ THỐNG (GLOBAL HELPERS)
// ====================================================================

/**
 * Escape chuỗi an toàn chống lỗ hổng XSS
 */
function e(?string $value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

/**
 * Định dạng tiền tệ VND chuẩn (Ví dụ: 24.500.000 ₫)
 */
function format_currency(float|int|string|null $amount): string {
    $numericAmount = floatval($amount ?? 0);
    return number_format($numericAmount, 0, ',', '.') . ' ₫';
}

/**
 * Tính giá bán sau khi đã trừ % giảm giá
 */
function calculate_sale_price(float|int $originalPrice, int $discountPercent): float {
    if ($discountPercent <= 0) return (float)$originalPrice;
    return (float)($originalPrice * (100 - $discountPercent) / 100);
}

/**
 * Lấy URL ảnh sản phẩm (ƯU TIÊN LINK MẠNG ONLINE, dùng ảnh local khi null hoặc rỗng)
 */
function get_product_thumb_url(?string $thumbnail, ?string $fallbackLocal = null): string {
    if (empty($thumbnail)) {
        return get_product_fallback_url($fallbackLocal);
    }
    // Nếu là URL mạng từ bên ngoài
    if (strpos($thumbnail, 'http://') === 0 || strpos($thumbnail, 'https://') === 0) {
        return $thumbnail;
    }
    
    // Bảng ánh xạ tệp cục bộ sang link ảnh mạng chất lượng cao
    $onlineMap = [
        'samsung_s24.jpg'      => 'https://images.unsplash.com/photo-1610945265064-0e34e5519bbf?auto=format&fit=crop&w=800&q=80',
        'apple_watch.jpg'      => 'https://images.unsplash.com/photo-1508685096489-7aacd43bd3b1?auto=format&fit=crop&w=800&q=80',
        'asus_vivobook.jpg'    => 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?auto=format&fit=crop&w=800&q=80',
        'sony_headphone.jpg'   => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=800&q=80',
        'philips_airfryer.jpg' => 'https://images.unsplash.com/photo-1584269600464-37b1b58a9fe7?auto=format&fit=crop&w=800&q=80',
        'nike_airmax.jpg'      => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?auto=format&fit=crop&w=800&q=80',
        'bomber_jacket.jpg'    => 'https://images.unsplash.com/photo-1551028719-00167b16eac5?auto=format&fit=crop&w=800&q=80',
        'books_combo.jpg'      => 'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?auto=format&fit=crop&w=800&q=80',
    ];

    if (isset($onlineMap[$thumbnail])) {
        return $onlineMap[$thumbnail];
    }
    
    return UPLOADS_URL . '/products/' . $thumbnail;
}

/**
 * Lấy URL ảnh sản phẩm local dự phòng (sử dụng khi ảnh mạng bị lỗi hiển thị onerror hoặc null)
 */
function get_product_fallback_url(?string $thumbnail = null): string {
    static $onlineMap = [
        'samsung_s24.jpg'      => 'photo-1610945265064',
        'apple_watch.jpg'      => 'photo-1508685096489',
        'asus_vivobook.jpg'    => 'photo-1496181133206',
        'sony_headphone.jpg'   => 'photo-1505740420928',
        'philips_airfryer.jpg' => 'photo-1584269600464',
        'nike_airmax.jpg'      => 'photo-1542291026',
        'bomber_jacket.jpg'    => 'photo-1551028719',
        'books_combo.jpg'      => 'photo-1544716278',
    ];

    if (!empty($thumbnail)) {
        // Trường hợp 1: Nếu truyền tên tệp local
        if (strpos($thumbnail, 'http://') !== 0 && strpos($thumbnail, 'https://') !== 0) {
            $localFile = UPLOADS_PATH . DIRECTORY_SEPARATOR . 'products' . DIRECTORY_SEPARATOR . $thumbnail;
            if (file_exists($localFile)) {
                return UPLOADS_URL . '/products/' . $thumbnail;
            }
        } else {
            // Trường hợp 2: Nếu truyền link online gặp sự cố mạng (onerror), truy vết ngược về ảnh local tương ứng
            foreach ($onlineMap as $localFile => $uniqueId) {
                if (strpos($thumbnail, $uniqueId) !== false) {
                    $targetPath = UPLOADS_PATH . DIRECTORY_SEPARATOR . 'products' . DIRECTORY_SEPARATOR . $localFile;
                    if (file_exists($targetPath)) {
                        return UPLOADS_URL . '/products/' . $localFile;
                    }
                }
            }
        }
    }
    return ASSETS_URL . '/images/default_product.jpg';
}

/**
 * Lấy URL ảnh bài viết tin tức (ƯU TIÊN LINK MẠNG ONLINE, dùng ảnh local khi null hoặc rỗng)
 */
function get_news_thumb_url(?string $thumbnail, ?string $fallbackLocal = null): string {
    if (empty($thumbnail)) {
        return get_news_fallback_url($fallbackLocal);
    }
    if (strpos($thumbnail, 'http://') === 0 || strpos($thumbnail, 'https://') === 0) {
        return $thumbnail;
    }

    $onlineNewsMap = [
        'news_samsung.jpg' => 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?auto=format&fit=crop&w=800&q=80',
        'news_apple.jpg'   => 'https://images.unsplash.com/photo-1510017803434-a899398421b3?auto=format&fit=crop&w=800&q=80',
        'news_fashion.jpg' => 'https://images.unsplash.com/photo-1483985988355-763728e1935b?auto=format&fit=crop&w=800&q=80',
    ];

    if (isset($onlineNewsMap[$thumbnail])) {
        return $onlineNewsMap[$thumbnail];
    }
    return UPLOADS_URL . '/news/' . $thumbnail;
}

/**
 * Lấy URL ảnh tin tức local dự phòng (sử dụng khi ảnh mạng bị lỗi hiển thị onerror hoặc null)
 */
function get_news_fallback_url(?string $thumbnail = null): string {
    static $onlineNewsMap = [
        'news_samsung.jpg' => 'photo-1511707171634',
        'news_apple.jpg'   => 'photo-1510017803434',
        'news_fashion.jpg' => 'photo-1483985988355',
    ];

    if (!empty($thumbnail)) {
        if (strpos($thumbnail, 'http://') !== 0 && strpos($thumbnail, 'https://') !== 0) {
            $localFile = UPLOADS_PATH . DIRECTORY_SEPARATOR . 'news' . DIRECTORY_SEPARATOR . $thumbnail;
            if (file_exists($localFile)) {
                return UPLOADS_URL . '/news/' . $thumbnail;
            }
        } else {
            foreach ($onlineNewsMap as $localFile => $uniqueId) {
                if (strpos($thumbnail, $uniqueId) !== false) {
                    $targetPath = UPLOADS_PATH . DIRECTORY_SEPARATOR . 'news' . DIRECTORY_SEPARATOR . $localFile;
                    if (file_exists($targetPath)) {
                        return UPLOADS_URL . '/news/' . $localFile;
                    }
                }
            }
        }
    }
    return ASSETS_URL . '/images/default_news.jpg';
}

/**
 * Lấy URL ảnh banner quảng cáo (ƯU TIÊN LINK MẠNG ONLINE)
 */
function get_banner_image_url(?string $image): string {
    if (empty($image)) {
        return get_banner_fallback_url($image);
    }
    if (strpos($image, 'http://') === 0 || strpos($image, 'https://') === 0) {
        return $image;
    }
    $onlineBannerMap = [
        'banner_mega_sale.jpg' => 'https://images.unsplash.com/photo-1550009158-9ebf69173e03?auto=format&fit=crop&w=1200&q=80',
        'banner_fashion.jpg'   => 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?auto=format&fit=crop&w=1200&q=80',
        'banner_home.jpg'      => 'https://images.unsplash.com/photo-1556911220-e15b29be8c8f?auto=format&fit=crop&w=1200&q=80',
    ];
    return $onlineBannerMap[$image] ?? (UPLOADS_URL . '/banners/' . $image);
}

/**
 * Lấy URL ảnh banner local dự phòng
 */
function get_banner_fallback_url(?string $image = null): string {
    if (!empty($image) && strpos($image, 'http') !== 0) {
        $localFile = UPLOADS_PATH . DIRECTORY_SEPARATOR . 'banners' . DIRECTORY_SEPARATOR . $image;
        if (file_exists($localFile)) {
            return UPLOADS_URL . '/banners/' . $image;
        }
    }
    return ASSETS_URL . '/images/default_product.jpg';
}

/**
 * Sinh URL ảnh mã VietQR tự động theo chuẩn VietQR NAPAS247
 */
function generate_vietqr_url(float|int $amount, string $orderCode): string {
    $amountInt = (int)round($amount);
    $descEncoded = urlencode($orderCode);
    return "https://img.vietqr.io/image/" . VIETQR_BANK_ID . "-" . VIETQR_ACCOUNT_NO . "-" . VIETQR_TEMPLATE . ".png?amount=" . $amountInt . "&addInfo=" . $descEncoded . "&accountName=" . urlencode(VIETQR_ACCOUNT_NAME);
}

/**
 * Chuẩn hóa nhãn thông số kỹ thuật sang tiếng Việt có dấu, không dính từ
 */
function format_spec_label(string $key): string {
    $dictionary = [
        'ManHinh'            => 'Màn hình',
        'Chipset'            => 'Vi xử lý (CPU/Chipset)',
        'CPU'                => 'Bộ vi xử lý (CPU)',
        'RAM'                => 'Bộ nhớ RAM',
        'BoNhoTrong'         => 'Bộ nhớ trong (ROM)',
        'OChua'              => 'Ổ cứng / Lưu trữ',
        'CameraSau'          => 'Camera sau',
        'CameraTruoc'        => 'Camera trước',
        'Pin'                => 'Dung lượng pin',
        'ThoiLuongPin'       => 'Thời lượng pin',
        'KichThuoc'          => 'Kích thước',
        'KichThuocMat'       => 'Kích thước mặt',
        'TrongLuong'         => 'Trọng lượng',
        'MatKinh'            => 'Chất liệu mặt kính',
        'ChongNuoc'          => 'Chỉ số kháng nước',
        'KetNoi'             => 'Kết nối không dây',
        'LoaiTaiNghe'        => 'Kiểu tai nghe',
        'Driver'             => 'Kích thước Driver',
        'CongNghe'           => 'Công nghệ nổi bật',
        'DungTich'           => 'Dung tích lòng nồi',
        'CongSuat'           => 'Công suất định mức',
        'BangDieuKhien'      => 'Bảng điều khiển',
        'XuatXu'             => 'Xuất xứ thương hiệu',
        'ChatLieu'           => 'Chất liệu sản phẩm',
        'BoDem'              => 'Công nghệ đệm đế',
        'Size'               => 'Kích cỡ (Size)',
        'MoiTruongSuDung'    => 'Phù hợp sử dụng',
        'KieuDang'           => 'Kiểu dáng thiết kế',
        'MauSac'             => 'Màu sắc lựa chọn',
        'NhaXuatBan'         => 'Nhà xuất bản',
        'SoTrang'            => 'Số trang sách',
        'HinhThuc'           => 'Hình thức đóng sách',
        'NgonNgu'            => 'Ngôn ngữ phát hành',
        'BaoHanh'            => 'Thời gian bảo hành',
        'HeDieuHanh'         => 'Hệ điều hành',
        'CongKetNoi'         => 'Cổng kết nối',
        'CamBien'            => 'Cảm biến sức khỏe',
        'TinhNang'           => 'Tính năng nổi bật'
    ];

    if (isset($dictionary[$key])) {
        return $dictionary[$key];
    }

    // Nếu là dạng CamelCase/PascalCase dính nhau, tách thành từ có khoảng trống
    $spaced = preg_replace('/([a-z0-9])([A-Z])/', '$1 $2', $key);
    return trim($spaced);
}

/**
 * Hiển thị nội dung mô tả an toàn: cho phép các thẻ HTML mô tả chuẩn (p, br, strong, ul, li...)
 */
function render_safe_html(?string $content): string {
    if (empty($content)) return '';
    
    // Nếu nội dung chứa thẻ HTML thì lọc qua strip_tags giữ lại các thẻ an toàn
    if (strip_tags($content) !== $content) {
        return strip_tags($content, '<p><br><b><strong><i><em><u><ul><ol><li><h3><h4><h5><h6><span><div><img><table><tr><td><th><tbody>');
    }
    
    // Nếu là văn bản thuần thì dùng nl2br và escape
    return nl2br(e($content));
}
