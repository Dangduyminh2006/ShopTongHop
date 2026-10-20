<?php
/**
 * ShopXpress - Vietnamese Text Resource Service
 * Hệ thống sử dụng 100% Tiếng Việt thuần túy (Đã loại bỏ chức năng chuyển ngữ)
 */

class Lang {
    private static array $translations = [];

    /**
     * Khởi tạo tài nguyên tiếng Việt
     */
    public static function init(): void {
        self::loadTranslations();
    }

    /**
     * Ngôn ngữ cố định của hệ thống: Luôn là Tiếng Việt
     */
    public static function current(): string {
        return 'vi';
    }

    /**
     * Nạp từ điển tiếng Việt toàn diện cho toàn hệ thống
     */
    private static function loadTranslations(): void {
        self::$translations = [
            // Thanh điều hướng & Header
            'nav_home'            => 'Trang Chủ',
            'nav_products'        => 'Sản Phẩm',
            'nav_categories'      => 'Danh Mục',
            'nav_flash_sale'      => 'Flash Sale',
            'nav_news'            => 'Tin Tức',
            'nav_cart'            => 'Giỏ Hàng',
            'nav_login'           => 'Đăng Nhập',
            'nav_register'        => 'Đăng Ký',
            'nav_account'         => 'Tài Khoản',
            'nav_admin'           => 'Quản Trị',
            'nav_logout'          => 'Đăng Xuất',
            'search_placeholder'  => 'Tìm kiếm sản phẩm, thương hiệu, danh mục...',
            'search_button'       => 'Tìm Kiếm',
            
            // Khối Trang chủ
            'hero_subtitle'       => 'Sàn Thương Mại Điện Tử Bán Hàng Tổng Hợp Đỉnh Cao',
            'shop_now'            => 'Mua Sắm Ngay',
            'view_all'            => 'Xem Tất Cả',
            'flash_sale_title'    => 'GIỜ VÀNG FLASH SALE',
            'flash_sale_end_in'   => 'Kết thúc trong:',
            'featured_products'   => 'Sản Phẩm Nổi Bật',
            'new_arrivals'        => 'Hàng Mới Về',
            'top_categories'      => 'Danh Mục Nổi Bật',
            'latest_news'         => 'Cẩm Nang Mua Sắm & Tin Tức',
            'sold'                => 'Đã bán',
            'in_stock'            => 'Còn hàng',
            'out_of_stock'        => 'Hết hàng',
            'add_to_cart'         => 'Thêm Vào Giỏ',
            'buy_now'             => 'Mua Ngay',

            // Chi tiết sản phẩm & Catalog
            'product_detail'      => 'Chi Tiết Sản Phẩm',
            'specifications'      => 'Thông Số Kỹ Thuật',
            'description'         => 'Mô Tả Sản Phẩm',
            'customer_reviews'    => 'Đánh Giá Của Khách Hàng',
            'write_review'        => 'Viết Đánh Giá',
            'related_products'    => 'Sản Phẩm Liên Quan',
            'filter_by_price'     => 'Lọc Theo Giá',
            'filter_by_brand'     => 'Thương Hiệu',
            'filter_by_category'  => 'Danh Mục',
            'sort_by'             => 'Sắp Xếp Theo',
            'sort_newest'         => 'Mới Nhất',
            'sort_price_asc'      => 'Giá: Thấp Đến Cao',
            'sort_price_desc'     => 'Giá: Cao Đến Thấp',
            'sort_popular'        => 'Bán Chạy Nhất',

            // Giỏ hàng & Thanh toán
            'cart_title'          => 'Giỏ Hàng Của Bạn',
            'cart_empty'          => 'Giỏ hàng của bạn đang trống!',
            'continue_shopping'   => 'Tiếp Tục Mua Sắm',
            'product'             => 'Sản Phẩm',
            'price'               => 'Đơn Giá',
            'quantity'            => 'Số Lượng',
            'subtotal'            => 'Tạm Tính',
            'total'               => 'Tổng Tiền',
            'voucher_code'        => 'Mã Giảm Giá',
            'apply_voucher'       => 'Áp Dụng',
            'checkout'            => 'Tiến Hành Đặt Hàng',
            'shipping_info'       => 'Thông Tin Giao Hàng',
            'payment_method'      => 'Phương Thức Thanh Toán',
            'payment_cod'         => 'Thanh toán khi nhận hàng (COD)',
            'payment_vietqr'      => 'Chuyển khoản ngân hàng qua mã VietQR',
            'place_order'         => 'Hoàn Tất Đặt Hàng',
            'order_success'       => 'Đặt Hàng Thành Công!',
            'order_code'          => 'Mã Đơn Hàng',

            // Chân trang & Thông tin
            'footer_about'        => 'ShopXpress - Hệ thống bán lẻ thương mại điện tử đa ngành hàng hàng đầu.',
            'customer_support'    => 'Chăm Sóc Khách Hàng',
            'hotline'             => 'Hotline: 1900 8899',
            'rights_reserved'     => 'Bản quyền thuộc về ShopXpress.',
        ];
    }

    /**
     * Lấy từ điển tiếng Việt theo khóa (Key)
     */
    public static function get(string $key): string {
        if (empty(self::$translations)) {
            self::loadTranslations();
        }
        return self::$translations[$key] ?? $key;
    }
}

/**
 * Hàm toàn cục hiển thị văn bản Tiếng Việt
 */
function t(string $key): string {
    return Lang::get($key);
}
