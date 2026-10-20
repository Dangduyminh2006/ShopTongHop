<!-- HERO BANNER SECTION -->
<section class="hero-section">
    <div class="container hero-grid">
        <!-- Main Banner Slider -->
        <div class="hero-slider">
            <?php if (!empty($banners)): ?>
                <?php foreach ($banners as $index => $banner): ?>
                    <div class="hero-slide <?= $index === 0 ? 'active' : '' ?>" style="background: linear-gradient(135deg, rgba(16,21,30,0.82) 0%, rgba(28,35,49,0.7) 100%), url('<?= get_banner_image_url($banner['image_url']) ?>') center/cover no-repeat;">
                        <div class="slide-content">
                            <span class="badge-promo"><i class="fa-solid fa-sparkles"></i> Ưu Đãi Độc Quyền</span>
                            <h1 class="slide-title"><?= e($banner['title']) ?></h1>
                            <p class="slide-subtitle"><?= e($banner['subtitle']) ?></p>
                            <a href="<?= BASE_URL ?>/products" class="btn btn-primary btn-lg">
                                <i class="fa-solid fa-cart-shopping"></i> <?= t('shop_now') ?>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="hero-slide active">
                    <div class="slide-content">
                        <span class="badge-promo"><i class="fa-solid fa-bolt"></i> Mega Sale 2026</span>
                        <h1 class="slide-title">MUA SẮM THÔNG MINH - NHẬN HÀNG NHANH CHÓNG</h1>
                        <p class="slide-subtitle">Hàng ngàn sản phẩm chính hãng với mức giá ưu đãi tốt nhất thị trường.</p>
                        <a href="<?= BASE_URL ?>/products" class="btn btn-primary btn-lg"><?= t('shop_now') ?></a>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Nút điều hướng Slider Trước / Sau -->
            <button class="slider-control-btn slider-prev" aria-label="Slide trước"><i class="fa-solid fa-chevron-left"></i></button>
            <button class="slider-control-btn slider-next" aria-label="Slide sau"><i class="fa-solid fa-chevron-right"></i></button>
        </div>

        <!-- Side Promo Cards -->
        <div class="hero-promos">
            <div class="promo-card promo-card-1">
                <div class="promo-icon"><i class="fa-solid fa-truck-fast"></i></div>
                <div class="promo-info">
                    <h4>Giao Hàng Siêu Tốc</h4>
                    <p>Nhận hàng trong 2h tại Hà Nội & TP.HCM</p>
                </div>
            </div>
            <div class="promo-card promo-card-2">
                <div class="promo-icon"><i class="fa-solid fa-ticket"></i></div>
                <div class="promo-info">
                    <h4>Mã Giảm Giá 50K</h4>
                    <p>Nhập mã <strong>WELCOME</strong> cho đơn đầu</p>
                </div>
            </div>
            <div class="promo-card promo-card-3">
                <div class="promo-icon"><i class="fa-solid fa-shield-heart"></i></div>
                <div class="promo-info">
                    <h4>Bảo Hành 12 Tháng</h4>
                    <p>Đổi mới 30 ngày nếu có lỗi sản xuất</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FLASH SALE SECTION -->
<?php if (!empty($flashSale) && !empty($flashSale['items'])): ?>
<section class="section-container flash-sale-section" id="flashSaleSection">
    <div class="container">
        <div class="section-header flash-header">
            <div class="flash-title-wrap">
                <h2 class="section-title"><i class="fa-solid fa-bolt"></i> <?= t('flash_sale_title') ?></h2>
                <div class="countdown-timer" id="flashCountdown" data-endtime="<?= e($flashSale['end_time']) ?>">
                    <span class="countdown-label"><?= t('flash_sale_end_in') ?></span>
                    <span class="time-box hours" id="cdHours">00</span>
                    <span class="time-sep">:</span>
                    <span class="time-box minutes" id="cdMinutes">00</span>
                    <span class="time-sep">:</span>
                    <span class="time-box seconds" id="cdSeconds">00</span>
                </div>
            </div>
            <a href="<?= BASE_URL ?>/products" class="view-all-link"><?= t('view_all') ?> <i class="fa-solid fa-arrow-right"></i></a>
        </div>

        <div class="product-grid">
            <?php foreach ($flashSale['items'] as $item): ?>
                <div class="product-card flash-card">
                    <div class="badge-discount">-<?= $item['discount_percent'] ?>%</div>
                    <a href="<?= BASE_URL ?>/product/detail/<?= $item['product_id'] ?>" class="product-thumb">
                        <img src="<?= get_product_thumb_url($item['thumbnail']) ?>" 
                             alt="<?= e($item['name']) ?>" 
                             onerror="this.onerror=null; this.src='<?= get_product_fallback_url($item['thumbnail']) ?>';">
                    </a>
                    <div class="product-body">
                        <h3 class="product-name">
                            <a href="<?= BASE_URL ?>/product/detail/<?= $item['product_id'] ?>"><?= e($item['name']) ?></a>
                        </h3>
                        <div class="product-prices">
                            <span class="price-sale"><?= format_currency($item['sale_price']) ?></span>
                            <span class="price-original"><?= format_currency($item['original_price']) ?></span>
                        </div>
                        <!-- Progress bar -->
                        <?php 
                            $sold = (int)$item['sold_quantity'];
                            $total = (int)$item['sale_quantity'];
                            $pct = $total > 0 ? min(100, round(($sold / $total) * 100)) : 0;
                        ?>
                        <div class="flash-progress">
                            <div class="progress-bar" style="width: <?= $pct ?>%;"></div>
                            <span class="progress-text"><i class="fa-solid fa-fire"></i> Đã bán <?= $sold ?>/<?= $total ?></span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- TOP CATEGORIES -->
<?php if (!empty($categories)): ?>
<section class="section-container categories-section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title"><i class="fa-solid fa-layer-group"></i> <?= t('top_categories') ?></h2>
            <a href="<?= BASE_URL ?>/products" class="view-all-link"><?= t('view_all') ?> <i class="fa-solid fa-arrow-right"></i></a>
        </div>
        <div class="category-grid">
            <?php foreach ($categories as $cat): ?>
                <a href="<?= BASE_URL ?>/products?cat=<?= $cat['id'] ?>" class="category-card">
                    <div class="cat-icon-box"><i class="fa-solid <?= e($cat['icon'] ?? 'fa-folder') ?>"></i></div>
                    <h4 class="cat-name"><?= e($cat['name']) ?></h4>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- FEATURED PRODUCTS -->
<section class="section-container featured-section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title"><i class="fa-solid fa-star"></i> <?= t('featured_products') ?></h2>
            <a href="<?= BASE_URL ?>/products" class="view-all-link"><?= t('view_all') ?> <i class="fa-solid fa-arrow-right"></i></a>
        </div>

        <div class="product-grid">
            <?php foreach ($featuredProducts as $product): ?>
                <?php 
                    $salePrice = calculate_sale_price($product['price'], (int)$product['discount_percent']);
                ?>
                <div class="product-card">
                    <?php if ($product['discount_percent'] > 0): ?>
                        <div class="badge-discount">-<?= $product['discount_percent'] ?>%</div>
                    <?php endif; ?>
                    <a href="<?= BASE_URL ?>/product/detail/<?= $product['id'] ?>" class="product-thumb">
                        <img src="<?= get_product_thumb_url($product['thumbnail']) ?>" 
                             alt="<?= e($product['name']) ?>" 
                             onerror="this.onerror=null; this.src='<?= get_product_fallback_url($product['thumbnail']) ?>';">
                    </a>
                    <div class="product-body">
                        <div class="product-meta">
                            <span class="meta-cat"><?= e($product['category_name'] ?? 'ShopXpress') ?></span>
                            <?php if (!empty($product['brand_name'])): ?>
                                <span class="meta-brand">• <?= e($product['brand_name']) ?></span>
                            <?php endif; ?>
                        </div>
                        <h3 class="product-name">
                            <a href="<?= BASE_URL ?>/product/detail/<?= $product['id'] ?>"><?= e($product['name']) ?></a>
                        </h3>
                        <div class="product-rating">
                            <span class="stars"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i></span>
                            <span class="rating-views">(<?= number_format($product['view_count']) ?> lượt xem)</span>
                        </div>
                        <div class="product-prices">
                            <span class="price-sale"><?= format_currency($salePrice) ?></span>
                            <?php if ($product['discount_percent'] > 0): ?>
                                <span class="price-original"><?= format_currency($product['price']) ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="product-actions">
                            <a href="<?= BASE_URL ?>/product/detail/<?= $product['id'] ?>" class="btn btn-outline btn-sm">Xem Chi Tiết</a>
                            <button class="btn btn-primary btn-sm btn-add-cart" data-id="<?= $product['id'] ?>" title="Thêm vào giỏ">
                                <i class="fa-solid fa-cart-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- NEW ARRIVALS -->
<?php if (!empty($newArrivals)): ?>
<section class="section-container new-arrivals-section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title"><i class="fa-solid fa-sparkles"></i> <?= t('new_arrivals') ?></h2>
            <a href="<?= BASE_URL ?>/products" class="view-all-link"><?= t('view_all') ?> <i class="fa-solid fa-arrow-right"></i></a>
        </div>

        <div class="product-grid">
            <?php foreach ($newArrivals as $product): ?>
                <?php 
                    $salePrice = calculate_sale_price($product['price'], (int)$product['discount_percent']);
                ?>
                <div class="product-card">
                    <span class="badge-new">MỚI</span>
                    <a href="<?= BASE_URL ?>/product/detail/<?= $product['id'] ?>" class="product-thumb">
                        <img src="<?= get_product_thumb_url($product['thumbnail']) ?>" 
                             alt="<?= e($product['name']) ?>" 
                             onerror="this.onerror=null; this.src='<?= get_product_fallback_url($product['thumbnail']) ?>';">
                    </a>
                    <div class="product-body">
                        <div class="product-meta">
                            <span class="meta-cat"><?= e($product['category_name'] ?? 'ShopXpress') ?></span>
                        </div>
                        <h3 class="product-name">
                            <a href="<?= BASE_URL ?>/product/detail/<?= $product['id'] ?>"><?= e($product['name']) ?></a>
                        </h3>
                        <div class="product-prices">
                            <span class="price-sale"><?= format_currency($salePrice) ?></span>
                            <?php if ($product['discount_percent'] > 0): ?>
                                <span class="price-original"><?= format_currency($product['price']) ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- SERVICE GUARANTEES -->
<section class="section-container guarantees-section">
    <div class="container guarantee-grid">
        <div class="guarantee-item">
            <div class="guarantee-icon"><i class="fa-solid fa-award"></i></div>
            <h4>100% Chính Hãng</h4>
            <p>Cam kết bồi thường 200% nếu phát hiện hàng giả</p>
        </div>
        <div class="guarantee-item">
            <div class="guarantee-icon"><i class="fa-solid fa-truck-ramp-box"></i></div>
            <h4>Giao Hàng Siêu Tốc</h4>
            <p>Vận chuyển nhanh chóng toàn quốc có bảo hiểm</p>
        </div>
        <div class="guarantee-item">
            <div class="guarantee-icon"><i class="fa-solid fa-rotate-left"></i></div>
            <h4>Đổi Trả Dễ Dàng</h4>
            <p>Đổi mới miễn phí trong 7 ngày nếu lỗi</p>
        </div>
        <div class="guarantee-item">
            <div class="guarantee-icon"><i class="fa-solid fa-comments"></i></div>
            <h4>Hỗ Trợ 24/7</h4>
            <p>Tư vấn chuyên nghiệp, tận tình mọi lúc</p>
        </div>
    </div>
</section>
