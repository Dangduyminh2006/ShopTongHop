</main>

<!-- FOOTER -->
<footer class="site-footer">
    <div class="container footer-top">
        <div class="footer-col brand-col">
            <div class="footer-logo">
                <span class="logo-box"><i class="fa-solid fa-bag-shopping"></i></span>
                <span class="logo-text">SHOP<span class="logo-accent">XPRESS</span></span>
            </div>
            <p class="footer-desc"><?= t('footer_about') ?></p>
            <div class="contact-info">
                <p><i class="fa-solid fa-location-dot"></i> Trụ sở: Tòa nhà ShopXpress, Cầu Giấy, Hà Nội</p>
                <p><i class="fa-solid fa-phone"></i> Hotline: 1900 8899 (8h00 - 21h00)</p>
                <p><i class="fa-solid fa-envelope"></i> Email: support@shoptonghop.vn</p>
            </div>
        </div>

        <div class="footer-col">
            <h4 class="col-title"><?= t('customer_support') ?></h4>
            <ul class="footer-links">
                <li><a href="#">Hướng Dẫn Mua Hàng</a></li>
                <li><a href="#">Chính Sách Giao Hàng & Đổi Trả</a></li>
                <li><a href="#">Hình Thức Thanh Toán VietQR / COD</a></li>
                <li><a href="#">Chính Sách Bảo Hành Chính Hãng</a></li>
                <li><a href="#">Câu Hỏi Thường Gặp (FAQ)</a></li>
            </ul>
        </div>

        <div class="footer-col">
            <h4 class="col-title"><?= t('nav_categories') ?></h4>
            <ul class="footer-links">
                <li><a href="<?= BASE_URL ?>/products?cat=1">Điện Tử & Công Nghệ</a></li>
                <li><a href="<?= BASE_URL ?>/products?cat=2">Thời Trang & Phụ Kiện</a></li>
                <li><a href="<?= BASE_URL ?>/products?cat=3">Đồ Gia Dụng & Đời Sống</a></li>
                <li><a href="<?= BASE_URL ?>/products?cat=4">Sách & Văn Phòng Phẩm</a></li>
                <li><a href="<?= BASE_URL ?>/products?cat=5">Thể Thao & Dã Ngoại</a></li>
            </ul>
        </div>

        <div class="footer-col">
            <h4 class="col-title">Thanh Toán An Toàn</h4>
            <div class="payment-methods">
                <span class="payment-badge"><i class="fa-solid fa-qrcode"></i> VietQR</span>
                <span class="payment-badge"><i class="fa-solid fa-money-bill-wave"></i> COD</span>
                <span class="payment-badge"><i class="fa-solid fa-building-columns"></i> Banking</span>
            </div>
            <h4 class="col-title" style="margin-top: 20px;">Đơn Vị Vận Chuyển</h4>
            <div class="shipping-partners">
                <span class="shipping-badge">GHN Express</span>
                <span class="shipping-badge">GHTK</span>
                <span class="shipping-badge">Viettel Post</span>
            </div>
        </div>
    </div>

    <div class="footer-bottom">
        <div class="container footer-bottom-inner">
            <p>&copy; <?= date('Y') ?> <?= APP_NAME ?>. <?= t('rights_reserved') ?> Đồ án Chuyên ngành Lập trình Web.</p>
            <div class="footer-tags">
                <span>Pure PHP MVC</span>
                <span class="divider">•</span>
                <span>MySQL PDO</span>
                <span class="divider">•</span>
                <span>VietQR Ready</span>
            </div>
        </div>
    </div>
</footer>

<!-- Custom JavaScript -->
<script>
    const BASE_URL = "<?= BASE_URL ?>";
</script>
<script src="<?= ASSETS_URL ?>/js/app.js"></script>
</body>
</html>
