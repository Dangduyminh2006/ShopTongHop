<?php
$discount = (int)($product['discount_percent'] ?? 0);
$price = (float)($product['price'] ?? 0);
$finalPrice = $price * (1 - $discount / 100);
$stock = (int)($product['stock_quantity'] ?? 0);
$imagesList = [];
if (!empty($product['thumbnail'])) {
    $imagesList[] = $product['thumbnail'];
}
if (!empty($gallery)) {
    foreach ($gallery as $img) {
        if (!in_array($img['image_url'], $imagesList)) {
            $imagesList[] = $img['image_url'];
        }
    }
}
if (empty($imagesList)) {
    $imagesList[] = 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=600&auto=format&fit=crop&q=80';
}
?>

<div class="container">
    <!-- 1. BREADCRUMBS -->
    <div class="breadcrumb-wrapper">
        <ul class="breadcrumb-list">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>"><i class="fa-solid fa-house"></i> Trang chủ</a></li>
            <li class="breadcrumb-separator"><i class="fa-solid fa-chevron-right"></i></li>
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/products">Sản phẩm</a></li>
            <?php if (!empty($product['category_name'])): ?>
                <li class="breadcrumb-separator"><i class="fa-solid fa-chevron-right"></i></li>
                <li class="breadcrumb-item">
                    <a href="<?= BASE_URL ?>/products?category=<?= urlencode($product['category_slug'] ?? $product['category_id']) ?>">
                        <?= e($product['category_name']) ?>
                    </a>
                </li>
            <?php endif; ?>
            <li class="breadcrumb-separator"><i class="fa-solid fa-chevron-right"></i></li>
            <li class="breadcrumb-item active"><?= e($product['name']) ?></li>
        </ul>
    </div>

    <!-- 2. PRODUCT DETAIL MAIN CARD (GALLERY + BUY BOX) -->
    <div class="product-detail-layout">
        <!-- Khung Trái: Product Gallery -->
        <div class="product-gallery">
            <div class="gallery-main-frame">
                <?php if ($discount > 0): ?>
                    <span class="gallery-badge" style="background: #FF0055; color: #fff;">-<?= $discount ?>%</span>
                <?php elseif (!empty($product['is_new'])): ?>
                    <span class="gallery-badge" style="background: var(--accent); color: #000;">Mới về</span>
                <?php endif; ?>

                <img id="mainGalleryImg" src="<?= e($imagesList[0]) ?>" alt="<?= e($product['name']) ?>">
            </div>

            <!-- Thư viện ảnh thu nhỏ -->
            <?php if (count($imagesList) > 1): ?>
                <div class="gallery-thumbs-strip">
                    <?php foreach ($imagesList as $idx => $imgUrl): ?>
                        <div class="thumb-item <?= $idx === 0 ? 'active' : '' ?>" onclick="switchGalleryThumb(this, '<?= e($imgUrl) ?>')">
                            <img src="<?= e($imgUrl) ?>" alt="Thumb <?= $idx + 1 ?>">
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Khung Phải: Buy Box -->
        <div class="product-buy-box">
            <!-- Meta & Tags -->
            <div class="buy-box-meta">
                <?php if (!empty($product['brand_name'])): ?>
                    <a href="<?= BASE_URL ?>/products?brand=<?= urlencode($product['brand_slug'] ?? $product['brand_id']) ?>" class="meta-pill">
                        <i class="fa-solid fa-tag"></i> <?= e($product['brand_name']) ?>
                    </a>
                <?php endif; ?>
                <?php if (!empty($product['sku'])): ?>
                    <span class="meta-pill"><i class="fa-solid fa-barcode"></i> SKU: <?= e($product['sku']) ?></span>
                <?php endif; ?>
            </div>

            <!-- Tên sản phẩm -->
            <h1 class="product-detail-title"><?= e($product['name']) ?></h1>

            <!-- Đánh giá sao & lượt xem -->
            <div class="product-rating-row">
                <div class="rating-stars">
                    <?php 
                    $score = $reviewStats['avg_rating'] ?? 5.0;
                    for ($s = 1; $s <= 5; $s++): 
                    ?>
                        <i class="fa-<?= ($s <= round($score)) ? 'solid' : 'regular' ?> fa-star"></i>
                    <?php endfor; ?>
                </div>
                <span class="rating-score"><?= number_format($score, 1) ?>/5.0</span>
                <span class="rating-divider">|</span>
                <span class="rating-count-link" onclick="openReviewTab()"><?= $reviewStats['total'] ?? 0 ?> đánh giá</span>
                <span class="rating-divider">|</span>
                <span class="views-pill"><i class="fa-regular fa-eye"></i> <?= number_format($product['view_count'] ?? 0) ?> lượt xem</span>
            </div>

            <!-- Khung Giá & Khuyến mãi -->
            <div class="detail-price-box">
                <span class="price-current-large"><?= number_format($finalPrice) ?> <small style="font-size: 20px;">đ</small></span>
                <?php if ($discount > 0): ?>
                    <span class="price-original-detail"><?= number_format($price) ?> đ</span>
                    <span class="price-save-badge">Tiết kiệm <?= number_format($price - $finalPrice) ?> đ (-<?= $discount ?>%)</span>
                <?php endif; ?>
            </div>

            <!-- Tình trạng tồn kho -->
            <?php if ($stock > 0): ?>
                <div class="stock-indicator in-stock">
                    <span class="stock-dot"></span>
                    <span>Còn hàng <strong>(<?= $stock ?> sản phẩm có sẵn trong kho)</strong></span>
                </div>
            <?php else: ?>
                <div class="stock-indicator out-of-stock">
                    <span class="stock-dot"></span>
                    <span>Tạm hết hàng</span>
                </div>
            <?php endif; ?>

            <!-- Tóm tắt ngắn -->
            <?php if (!empty($product['summary'])): ?>
                <div class="product-summary-box">
                    <?= nl2br(e($product['summary'])) ?>
                </div>
            <?php endif; ?>

            <!-- Bộ chọn số lượng & Nút đặt mua -->
            <div class="purchase-action-row">
                <div class="quantity-stepper">
                    <button type="button" class="stepper-btn" onclick="stepQuantity(-1)">-</button>
                    <input type="number" id="buyQuantity" class="stepper-input" value="1" min="1" max="<?= max(1, $stock) ?>" readonly>
                    <button type="button" class="stepper-btn" onclick="stepQuantity(1)">+</button>
                </div>

                <button type="button" class="btn-add-to-cart-lg" <?= $stock <= 0 ? 'disabled style="opacity: 0.5; cursor: not-allowed;"' : '' ?> onclick="addToCartDetail(<?= (int)$product['id'] ?>)">
                    <i class="fa-solid fa-cart-plus"></i> Thêm Vào Giỏ
                </button>

                <button type="button" class="btn-buy-now-lg" <?= $stock <= 0 ? 'disabled style="opacity: 0.5; cursor: not-allowed;"' : '' ?> onclick="buyNow(<?= (int)$product['id'] ?>)">
                    <i class="fa-solid fa-bolt"></i> Mua Ngay
                </button>
            </div>

            <!-- Cam kết dịch vụ & Quyền lợi khách hàng -->
            <div class="detail-perks-grid">
                <div class="perk-item">
                    <div class="perk-icon-wrap">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                    <div class="perk-text">
                        <strong>100% Chính Hãng</strong>
                        <span>Nguồn gốc xuất xứ rõ ràng</span>
                    </div>
                </div>
                <div class="perk-item">
                    <div class="perk-icon-wrap">
                        <i class="fa-solid fa-truck-fast"></i>
                    </div>
                    <div class="perk-text">
                        <strong>Giao Toàn Quốc</strong>
                        <span>Freeship đơn từ 300K</span>
                    </div>
                </div>
                <div class="perk-item">
                    <div class="perk-icon-wrap">
                        <i class="fa-solid fa-arrow-rotate-left"></i>
                    </div>
                    <div class="perk-text">
                        <strong>Đổi Trả 7 Ngày</strong>
                        <span>Linh hoạt và nhanh chóng</span>
                    </div>
                </div>
                <div class="perk-item">
                    <div class="perk-icon-wrap">
                        <i class="fa-solid fa-medal"></i>
                    </div>
                    <div class="perk-text">
                        <strong>Bảo Hành Uy Tín</strong>
                        <span>Chính hãng 12-24 tháng</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. TABS CHI TIẾT (MÔ TẢ, THÔNG SỐ JSON, ĐÁNH GIÁ SAO) -->
    <div class="product-tabs-wrapper" id="productTabsWrapper">
        <div class="tab-nav-bar">
            <button type="button" class="tab-btn active" onclick="switchProductTab('descTab', this)">
                <i class="fa-solid fa-file-lines"></i> Mô Tả Chi Tiết
            </button>
            <button type="button" class="tab-btn" onclick="switchProductTab('specsTab', this)">
                <i class="fa-solid fa-sliders"></i> Thông Số Kỹ Thuật
                <?php if (!empty($specifications)): ?>
                    <span class="tab-btn-badge"><?= count($specifications) ?></span>
                <?php endif; ?>
            </button>
            <button type="button" class="tab-btn" id="reviewsTabBtn" onclick="switchProductTab('reviewsTab', this)">
                <i class="fa-solid fa-star"></i> Đánh Giá & Nhận Xét
                <span class="tab-btn-badge"><?= $reviewStats['total'] ?? 0 ?></span>
            </button>
        </div>

        <div class="tab-panes-body">
            <!-- TAB 1: MÔ TẢ CHI TIẾT -->
            <div id="descTab" class="tab-pane active">
                <?php if (!empty($product['description'])): ?>
                    <div class="product-description-content" style="font-size: 15px; line-height: 1.8; color: var(--text-secondary);">
                        <?= render_safe_html($product['description']) ?>
                    </div>
                <?php else: ?>
                    <p style="color: var(--text-muted); font-style: italic;">Chưa có bài viết mô tả chi tiết cho sản phẩm này.</p>
                <?php endif; ?>
            </div>

            <!-- TAB 2: THÔNG SỐ KỸ THUẬT CHI TIẾT (JSON DYNAMIC TABLE) -->
            <div id="specsTab" class="tab-pane">
                <?php if (!empty($specifications)): ?>
                    <table class="specs-table">
                        <tbody>
                            <?php foreach ($specifications as $key => $val): ?>
                                <tr>
                                    <td class="specs-key"><?= e(format_spec_label($key)) ?></td>
                                    <td class="specs-val"><?= is_array($val) ? e(implode(', ', $val)) : e($val) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div style="text-align: center; padding: 40px 0; color: var(--text-muted);">
                        <i class="fa-solid fa-microchip" style="font-size: 36px; margin-bottom: 12px; opacity: 0.5;"></i>
                        <p>Thông số kỹ thuật đang được cập nhật.</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- TAB 3: ĐÁNH GIÁ & NHẬN XÉT -->
            <div id="reviewsTab" class="tab-pane">
                <!-- Thống kê điểm số sao -->
                <div class="reviews-summary-card">
                    <div class="reviews-score-big">
                        <div class="score-num"><?= number_format($score, 1) ?></div>
                        <div class="rating-stars" style="font-size: 18px; margin-bottom: 6px;">
                            <?php for ($s = 1; $s <= 5; $s++): ?>
                                <i class="fa-<?= ($s <= round($score)) ? 'solid' : 'regular' ?> fa-star"></i>
                            <?php endfor; ?>
                        </div>
                        <div style="font-size: 13.5px; color: var(--text-muted);"><?= $reviewStats['total'] ?? 0 ?> nhận xét thực tế</div>
                    </div>

                    <div class="star-bars-col">
                        <?php 
                        $totalRev = max(1, $reviewStats['total'] ?? 0);
                        for ($star = 5; $star >= 1; $star--): 
                            $count = $reviewStats["star_$star"] ?? 0;
                            $percent = round(($count / $totalRev) * 100);
                        ?>
                            <div class="star-bar-row">
                                <span style="width: 50px;"><?= $star ?> sao</span>
                                <div class="star-bar-track">
                                    <div class="star-bar-fill" style="width: <?= $percent ?>%;"></div>
                                </div>
                                <span style="width: 40px; text-align: right; color: var(--text-muted);"><?= $count ?></span>
                            </div>
                        <?php endfor; ?>
                    </div>
                </div>

                <!-- Danh sách bình luận -->
                <?php if (!empty($reviews)): ?>
                    <div class="reviews-list">
                        <?php foreach ($reviews as $rev): ?>
                            <div class="review-item-card">
                                <div class="review-card-header">
                                    <div class="reviewer-info">
                                        <div class="reviewer-avatar">
                                            <?= mb_substr(e($rev['fullname'] ?: 'Khách'), 0, 1) ?>
                                        </div>
                                        <div>
                                            <div style="font-weight: 700; color: var(--text-primary); font-size: 14.5px;">
                                                <?= e($rev['fullname'] ?: 'Khách hàng') ?>
                                            </div>
                                            <div style="font-size: 12px; color: var(--text-muted);">
                                                <?= date('d/m/Y H:i', strtotime($rev['created_at'])) ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="rating-stars">
                                        <?php for ($s = 1; $s <= 5; $s++): ?>
                                            <i class="fa-<?= ($s <= $rev['rating']) ? 'solid' : 'regular' ?> fa-star"></i>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                                <div class="review-comment">
                                    <?= nl2br(e($rev['comment'])) ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p style="text-align: center; color: var(--text-muted); margin-bottom: 30px;">
                        Chưa có đánh giá nào cho sản phẩm này. Hãy là người đầu tiên chia sẻ cảm nhận!
                    </p>
                <?php endif; ?>

                <!-- Form gửi đánh giá mới (Chỉ dành cho thành viên đã đăng nhập) -->
                <?php if (!empty($_SESSION['user'])): ?>
                    <div class="review-form-box">
                        <h4 style="font-size: 17px; margin-bottom: 14px; font-weight: 700;">Gửi Đánh Giá Của Bạn (Tài khoản: <?= e($_SESSION['user']['fullname']) ?>)</h4>
                        <form action="<?= BASE_URL ?>/review/add" method="POST">
                            <input type="hidden" name="product_id" value="<?= (int)$product['id'] ?>">
                            <input type="hidden" name="rating" id="reviewRatingInput" value="5">

                            <div style="margin-bottom: 12px;">
                                <label style="font-size: 13.5px; color: var(--text-secondary); display: block; margin-bottom: 6px;">
                                    Đánh giá của bạn về chất lượng sản phẩm:
                                </label>
                                <div class="star-picker" id="starPicker">
                                    <i class="fa-solid fa-star" data-val="1"></i>
                                    <i class="fa-solid fa-star" data-val="2"></i>
                                    <i class="fa-solid fa-star" data-val="3"></i>
                                    <i class="fa-solid fa-star" data-val="4"></i>
                                    <i class="fa-solid fa-star" data-val="5"></i>
                                </div>
                            </div>

                            <div style="margin-bottom: 16px;">
                                <label style="font-size: 13.5px; color: var(--text-secondary); display: block; margin-bottom: 6px;">
                                    Cảm nhận chi tiết:
                                </label>
                                <textarea name="comment" rows="4" class="form-control" placeholder="Chia sẻ cảm nhận về đóng gói, chất lượng sản phẩm, dịch vụ giao hàng..." required style="width: 100%; padding: 12px 16px; background: var(--bg-surface-elevated); border: 1px solid var(--border-color); border-radius: 8px; color: var(--text-primary); font-family: inherit; font-size: 14px; resize: vertical;"></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary" style="padding: 10px 24px; font-weight: 600;">
                                <i class="fa-solid fa-paper-plane"></i> Gửi Đánh Giá
                            </button>
                        </form>
                    </div>
                <?php else: ?>
                    <div class="review-login-required" style="text-align: center; padding: 36px 20px; background: var(--bg-surface-elevated); border: 1px dashed var(--border-color); border-radius: 12px; margin-top: 24px;">
                        <i class="fa-solid fa-shield-halved" style="font-size: 36px; color: var(--primary); margin-bottom: 12px; display: block;"></i>
                        <h4 style="font-size: 16px; font-weight: 700; margin-bottom: 6px; color: var(--text-primary);">Đăng nhập để gửi nhận xét và đánh giá</h4>
                        <p style="font-size: 14px; color: var(--text-muted); margin-bottom: 18px;">Để đảm bảo tính xác thực và khách quan, chỉ khách hàng có tài khoản mới có thể gửi đánh giá cho sản phẩm.</p>
                        <a href="<?= BASE_URL ?>/auth/login" class="btn btn-primary" style="padding: 10px 24px; font-weight: 600; display: inline-flex; align-items: center; gap: 8px;">
                            <i class="fa-solid fa-right-to-bracket"></i> Đăng Nhập Tài Khoản
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- 4. KHỐI SẢN PHẨM TƯƠNG TỰ (RELATED PRODUCTS) -->
    <?php if (!empty($relatedProducts)): ?>
        <section style="margin-bottom: 60px;">
            <div class="section-header" style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px;">
                <h3 style="font-size: 22px; font-weight: 800; font-family: 'Outfit', sans-serif;">
                    <i class="fa-solid fa-shapes" style="color: var(--primary);"></i> Sản Phẩm Tương Tự
                </h3>
                <a href="<?= BASE_URL ?>/products?category=<?= urlencode($product['category_slug'] ?? $product['category_id']) ?>" style="color: var(--primary); font-size: 14px; font-weight: 600;">
                    Xem tất cả <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>

            <div class="product-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 20px;">
                <?php foreach ($relatedProducts as $rel): 
                    $relDiscount = (int)($rel['discount_percent'] ?? 0);
                    $relPrice = (float)($rel['price'] ?? 0);
                    $relFinal = $relPrice * (1 - $relDiscount / 100);
                ?>
                    <div class="product-card">
                        <?php if ($relDiscount > 0): ?>
                            <div class="badge-discount">-<?= $relDiscount ?>%</div>
                        <?php endif; ?>
                        <a href="<?= BASE_URL ?>/product/detail?slug=<?= urlencode($rel['slug']) ?>" class="product-thumb">
                            <img src="<?= e($rel['thumbnail']) ?>" alt="<?= e($rel['name']) ?>" loading="lazy" onerror="this.onerror=null; this.src='<?= BASE_URL ?>/public/assets/images/default_product.jpg';">
                        </a>
                        <div class="product-body">
                            <div class="product-meta">
                                <span class="meta-cat"><?= e($rel['category_name'] ?? 'Sản phẩm') ?></span>
                            </div>
                            <h3 class="product-name">
                                <a href="<?= BASE_URL ?>/product/detail?slug=<?= urlencode($rel['slug']) ?>"><?= e($rel['name']) ?></a>
                            </h3>
                            <div class="product-prices">
                                <span class="price-sale"><?= number_format($relFinal) ?> đ</span>
                                <?php if ($relDiscount > 0): ?>
                                    <span class="price-original"><?= number_format($relPrice) ?> đ</span>
                                <?php endif; ?>
                            </div>
                            <div class="product-actions">
                                <a href="<?= BASE_URL ?>/product/detail?slug=<?= urlencode($rel['slug']) ?>" class="btn btn-outline btn-sm">Xem Chi Tiết</a>
                                <button class="btn btn-primary btn-sm btn-add-cart" data-id="<?= (int)$rel['id'] ?>" title="Thêm vào giỏ">
                                    <i class="fa-solid fa-cart-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>
</div>

<!-- Scripts tương tác trang Chi Tiết Sản Phẩm -->
<script>
// 1. Chuyển đổi ảnh trong Product Gallery
function switchGalleryThumb(elem, imgSrc) {
    document.querySelectorAll('.thumb-item').forEach(el => el.classList.remove('active'));
    elem.classList.add('active');
    const mainImg = document.getElementById('mainGalleryImg');
    mainImg.style.opacity = '0.3';
    setTimeout(() => {
        mainImg.src = imgSrc;
        mainImg.style.opacity = '1';
    }, 150);
}

// 2. Tăng giảm số lượng mua hàng
function stepQuantity(delta) {
    const input = document.getElementById('buyQuantity');
    let val = parseInt(input.value) || 1;
    const max = parseInt(input.getAttribute('max')) || 999;
    val += delta;
    if (val < 1) val = 1;
    if (val > max) val = max;
    input.value = val;
}

// 3. Thêm vào giỏ từ trang Chi tiết (kèm số lượng được chọn)
function addToCartDetail(productId) {
    const qty = parseInt(document.getElementById('buyQuantity').value) || 1;
    fetch('<?= BASE_URL ?>/cart/add', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: `product_id=${productId}&quantity=${qty}`
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            const badge = document.getElementById('cartBadge');
            if (badge && data.cartCount !== undefined) {
                badge.textContent = data.cartCount;
                badge.classList.remove('bounce');
                void badge.offsetWidth;
                badge.classList.add('bounce');
            }
            if (typeof showToast === 'function') {
                showToast('success', `Đã thêm ${qty} sản phẩm vào giỏ hàng!`);
            } else {
                alert(`Đã thêm ${qty} sản phẩm vào giỏ hàng!`);
            }
        } else {
            if (data.require_login) {
                if (typeof showToast === 'function') {
                    showToast('warning', data.message || 'Vui lòng đăng nhập tài khoản!');
                }
                setTimeout(() => {
                    const currentUrl = encodeURIComponent(window.location.pathname + window.location.search);
                    window.location.href = '<?= BASE_URL ?>/login?redirect=' + currentUrl;
                }, 1000);
            } else {
                if (typeof showToast === 'function') {
                    showToast('error', data.message || 'Không thể thêm vào giỏ hàng.');
                } else {
                    alert(data.message || 'Không thể thêm vào giỏ hàng.');
                }
            }
        }
    })
    .catch((err) => {
        console.error('Cart add error:', err);
        if (typeof showToast === 'function') {
            showToast('error', 'Có lỗi xảy ra khi thêm vào giỏ hàng.');
        } else {
            alert('Có lỗi xảy ra khi thêm vào giỏ hàng.');
        }
    });
}

// 4. Mua ngay (Thêm vào giỏ và chuyển thẳng sang trang giỏ hàng)
function buyNow(productId) {
    const qty = parseInt(document.getElementById('buyQuantity').value) || 1;
    fetch('<?= BASE_URL ?>/cart/add', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: `product_id=${productId}&quantity=${qty}`
    })
    .then(res => res.json())
    .then(data => {
        if (data.require_login) {
            if (typeof showToast === 'function') {
                showToast('warning', data.message || 'Vui lòng đăng nhập tài khoản để mua hàng!');
            }
            setTimeout(() => {
                const currentUrl = encodeURIComponent('cart/checkout');
                window.location.href = '<?= BASE_URL ?>/login?redirect=' + currentUrl;
            }, 800);
            return;
        }
        window.location.href = '<?= BASE_URL ?>/cart';
    })
    .catch(() => {
        window.location.href = '<?= BASE_URL ?>/cart';
    });
}

// 5. Chuyển đổi Tab (Mô tả, Thông số, Đánh giá)
function switchProductTab(tabId, btnElem) {
    document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
    document.querySelectorAll('.tab-pane').forEach(pane => pane.classList.remove('active'));

    btnElem.classList.add('active');
    const targetPane = document.getElementById(tabId);
    if (targetPane) {
        targetPane.classList.add('active');
    }
}

function openReviewTab() {
    const btn = document.getElementById('reviewsTabBtn');
    if (btn) {
        btn.click();
        const wrapper = document.getElementById('productTabsWrapper');
        if (wrapper) {
            wrapper.scrollIntoView({ behavior: 'smooth' });
        }
    }
}

// 6. Xử lý bộ chọn sao đánh giá
document.addEventListener('DOMContentLoaded', () => {
    const starPicker = document.getElementById('starPicker');
    if (starPicker) {
        const stars = starPicker.querySelectorAll('i');
        const input = document.getElementById('reviewRatingInput');

        stars.forEach(star => {
            star.addEventListener('click', function() {
                const val = parseInt(this.getAttribute('data-val'));
                input.value = val;
                stars.forEach(s => {
                    const sVal = parseInt(s.getAttribute('data-val'));
                    if (sVal <= val) {
                        s.classList.remove('fa-regular');
                        s.classList.add('fa-solid');
                    } else {
                        s.classList.remove('fa-solid');
                        s.classList.add('fa-regular');
                    }
                });
            });
        });
    }
});
</script>
