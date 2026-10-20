<?php
// Helper tạo URL giữ lại query parameters hiện tại
function buildFilterUrl($newParams = []) {
    $params = $_GET;
    unset($params['url']); // Bỏ param router nếu có
    foreach ($newParams as $k => $v) {
        if ($v === null || $v === '') {
            unset($params[$k]);
        } else {
            $params[$k] = $v;
        }
    }
    // Khi đổi filter, reset page về 1 trừ khi đang chủ động chuyển page
    if (!isset($newParams['page'])) {
        unset($params['page']);
    }
    return BASE_URL . '/products' . (!empty($params) ? '?' . http_build_query($params) : '');
}
?>

<div class="container">
    <!-- 1. BREADCRUMBS -->
    <div class="breadcrumb-wrapper">
        <ul class="breadcrumb-list">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>"><i class="fa-solid fa-house"></i> Trang chủ</a></li>
            <li class="breadcrumb-separator"><i class="fa-solid fa-chevron-right"></i></li>
            <li class="breadcrumb-item <?= empty($selectedCategory) && empty($selectedBrand) && empty($keyword) ? 'active' : '' ?>">
                <a href="<?= BASE_URL ?>/products">Tất cả sản phẩm</a>
            </li>
            <?php if (!empty($selectedCategory)): ?>
                <li class="breadcrumb-separator"><i class="fa-solid fa-chevron-right"></i></li>
                <li class="breadcrumb-item active"><?= e($selectedCategory['name']) ?></li>
            <?php elseif (!empty($selectedBrand)): ?>
                <li class="breadcrumb-separator"><i class="fa-solid fa-chevron-right"></i></li>
                <li class="breadcrumb-item active">Thương hiệu: <?= e($selectedBrand['name']) ?></li>
            <?php elseif (!empty($keyword)): ?>
                <li class="breadcrumb-separator"><i class="fa-solid fa-chevron-right"></i></li>
                <li class="breadcrumb-item active">Tìm kiếm: "<?= e($keyword) ?>"</li>
            <?php endif; ?>
        </ul>
    </div>

    <!-- 2. LAYOUT CATALOG CHÍNH (SIDEBAR FILTER + CONTENT GRID) -->
    <div class="catalog-page-layout">
        
        <!-- CỘT TRÁI: SIDEBAR BỘ LỌC ĐA NĂNG -->
        <aside class="catalog-sidebar">
            <div class="sidebar-heading">
                <h3><i class="fa-solid fa-filter" style="color: var(--primary);"></i> Bộ Lọc Tìm Kiếm</h3>
                <?php if (!empty($selectedCategory) || !empty($selectedBrand) || !empty($minPrice) || !empty($maxPrice) || !empty($keyword)): ?>
                    <a href="<?= BASE_URL ?>/products" style="font-size: 12.5px; color: var(--sale); font-weight: 600;">
                        <i class="fa-solid fa-rotate-left"></i> Đặt lại
                    </a>
                <?php endif; ?>
            </div>

            <!-- 1. Lọc theo Danh mục sản phẩm -->
            <div class="filter-section">
                <div class="filter-title">
                    <span>Ngành Hàng / Danh Mục</span>
                    <i class="fa-solid fa-chevron-down" style="font-size: 11px; color: var(--text-muted);"></i>
                </div>
                <div class="filter-list">
                    <a href="<?= buildFilterUrl(['category' => null]) ?>" class="filter-item-label" style="<?= empty($selectedCategory) ? 'color: var(--primary); font-weight: 700;' : '' ?>">
                        <span><i class="fa-solid fa-border-all" style="width: 18px;"></i> Tất cả danh mục</span>
                    </a>
                    <?php if (!empty($categories)): ?>
                        <?php foreach ($categories as $cat): 
                            $isCatActive = !empty($selectedCategory) && ($selectedCategory['id'] == $cat['id'] || $selectedCategory['parent_id'] == $cat['id']);
                        ?>
                            <div>
                                <a href="<?= buildFilterUrl(['category' => $cat['slug']]) ?>" class="filter-item-label" style="<?= (!empty($selectedCategory) && $selectedCategory['id'] == $cat['id']) ? 'color: var(--primary); font-weight: 700;' : '' ?>">
                                    <span>
                                        <i class="fa-solid <?= e($cat['icon'] ?? 'fa-folder') ?>" style="width: 18px; color: var(--primary);"></i> 
                                        <?= e($cat['name']) ?>
                                    </span>
                                </a>

                                <!-- Danh mục con (nếu có) -->
                                <?php if (!empty($cat['children'])): ?>
                                    <div style="padding-left: 20px; display: flex; flex-direction: column; gap: 4px; margin-top: 4px; margin-bottom: 6px;">
                                        <?php foreach ($cat['children'] as $child): 
                                            $isChildActive = !empty($selectedCategory) && $selectedCategory['id'] == $child['id'];
                                        ?>
                                            <a href="<?= buildFilterUrl(['category' => $child['slug']]) ?>" class="filter-item-label" style="font-size: 12.5px; <?= $isChildActive ? 'color: var(--primary); font-weight: 700;' : '' ?>">
                                                <span>• <?= e($child['name']) ?></span>
                                            </a>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- 2. Lọc theo Thương hiệu -->
            <div class="filter-section">
                <div class="filter-title">
                    <span>Thương Hiệu Chính Hãng</span>
                    <i class="fa-solid fa-chevron-down" style="font-size: 11px; color: var(--text-muted);"></i>
                </div>
                <div class="filter-list">
                    <a href="<?= buildFilterUrl(['brand' => null]) ?>" class="filter-item-label" style="<?= empty($selectedBrand) ? 'color: var(--primary); font-weight: 700;' : '' ?>">
                        <span>Tất cả thương hiệu</span>
                    </a>
                    <?php if (!empty($brands)): ?>
                        <?php foreach ($brands as $brand): 
                            $isBrandActive = !empty($selectedBrand) && $selectedBrand['id'] == $brand['id'];
                        ?>
                            <a href="<?= buildFilterUrl(['brand' => $brand['slug']]) ?>" class="filter-item-label" style="<?= $isBrandActive ? 'color: var(--primary); font-weight: 700;' : '' ?>">
                                <span><?= e($brand['name']) ?></span>
                                <?php if (!empty($brand['product_count'])): ?>
                                    <span class="filter-badge"><?= $brand['product_count'] ?></span>
                                <?php endif; ?>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- 3. Lọc theo Khoảng giá -->
            <div class="filter-section">
                <div class="filter-title">
                    <span>Khoảng Giá Phù Hợp</span>
                    <i class="fa-solid fa-chevron-down" style="font-size: 11px; color: var(--text-muted);"></i>
                </div>

                <!-- Nút chọn nhanh khoảng giá -->
                <div class="price-quick-tags">
                    <a href="<?= buildFilterUrl(['min_price' => null, 'max_price' => 2000000]) ?>" class="price-pill-btn <?= (isset($maxPrice) && $maxPrice == 2000000 && empty($minPrice)) ? 'active' : '' ?>">
                        Dưới 2 triệu
                    </a>
                    <a href="<?= buildFilterUrl(['min_price' => 2000000, 'max_price' => 5000000]) ?>" class="price-pill-btn <?= ($minPrice == 2000000 && $maxPrice == 5000000) ? 'active' : '' ?>">
                        2 triệu - 5 triệu
                    </a>
                    <a href="<?= buildFilterUrl(['min_price' => 5000000, 'max_price' => 15000000]) ?>" class="price-pill-btn <?= ($minPrice == 5000000 && $maxPrice == 15000000) ? 'active' : '' ?>">
                        5 triệu - 15 triệu
                    </a>
                    <a href="<?= buildFilterUrl(['min_price' => 15000000, 'max_price' => 30000000]) ?>" class="price-pill-btn <?= ($minPrice == 15000000 && $maxPrice == 30000000) ? 'active' : '' ?>">
                        15 triệu - 30 triệu
                    </a>
                    <a href="<?= buildFilterUrl(['min_price' => 30000000, 'max_price' => null]) ?>" class="price-pill-btn <?= ($minPrice == 30000000 && empty($maxPrice)) ? 'active' : '' ?>">
                        Trên 30 triệu
                    </a>
                </div>

                <!-- Form nhập khoảng giá tùy chọn -->
                <form action="<?= BASE_URL ?>/products" method="GET" style="margin-top: 14px;">
                    <?php if (!empty($selectedCategory)): ?>
                        <input type="hidden" name="category" value="<?= e($selectedCategory['slug']) ?>">
                    <?php endif; ?>
                    <?php if (!empty($selectedBrand)): ?>
                        <input type="hidden" name="brand" value="<?= e($selectedBrand['slug']) ?>">
                    <?php endif; ?>
                    <?php if (!empty($keyword)): ?>
                        <input type="hidden" name="keyword" value="<?= e($keyword) ?>">
                    <?php endif; ?>
                    <?php if (!empty($sort)): ?>
                        <input type="hidden" name="sort" value="<?= e($sort) ?>">
                    <?php endif; ?>

                    <div style="display: flex; gap: 8px; align-items: center; margin-bottom: 8px;">
                        <input type="number" name="min_price" value="<?= e($minPrice ?? '') ?>" placeholder="Từ đ" class="form-control" style="flex: 1; padding: 6px 10px; font-size: 13px; background: var(--bg-surface-elevated); border: 1px solid var(--border-color); border-radius: 6px; color: var(--text-primary);">
                        <span style="color: var(--text-muted);">-</span>
                        <input type="number" name="max_price" value="<?= e($maxPrice ?? '') ?>" placeholder="Đến đ" class="form-control" style="flex: 1; padding: 6px 10px; font-size: 13px; background: var(--bg-surface-elevated); border: 1px solid var(--border-color); border-radius: 6px; color: var(--text-primary);">
                    </div>
                    <button type="submit" class="btn btn-secondary btn-sm" style="width: 100%; justify-content: center;">
                        <i class="fa-solid fa-check"></i> Áp dụng giá
                    </button>
                </form>
            </div>

        </aside>

        <!-- CỘT PHẢI: LƯỚI SẢN PHẨM & THANH CÔNG CỤ -->
        <main class="catalog-content">
            
            <!-- Toolbar: Đếm số lượng & Sắp xếp -->
            <div class="catalog-toolbar">
                <div class="toolbar-counter">
                    Tìm thấy <strong><?= number_format($total) ?></strong> sản phẩm
                    <?php if (!empty($selectedCategory)): ?>
                        trong <strong><?= e($selectedCategory['name']) ?></strong>
                    <?php endif; ?>
                </div>

                <div class="toolbar-sort">
                    <label for="sortSelect" style="font-size: 13.5px; color: var(--text-secondary);">Sắp xếp theo:</label>
                    <select id="sortSelect" class="sort-select" onchange="window.location.href=this.value">
                        <option value="<?= buildFilterUrl(['sort' => 'newest']) ?>" <?= $sort === 'newest' ? 'selected' : '' ?>>Mới nhất</option>
                        <option value="<?= buildFilterUrl(['sort' => 'best_selling']) ?>" <?= $sort === 'best_selling' ? 'selected' : '' ?>>Bán chạy nhất</option>
                        <option value="<?= buildFilterUrl(['sort' => 'price_asc']) ?>" <?= $sort === 'price_asc' ? 'selected' : '' ?>>Giá: Thấp đến Cao</option>
                        <option value="<?= buildFilterUrl(['sort' => 'price_desc']) ?>" <?= $sort === 'price_desc' ? 'selected' : '' ?>>Giá: Cao đến Thấp</option>
                        <option value="<?= buildFilterUrl(['sort' => 'name_asc']) ?>" <?= $sort === 'name_asc' ? 'selected' : '' ?>>Tên: A → Z</option>
                    </select>
                </div>
            </div>

            <!-- Các chip bộ lọc đang kích hoạt -->
            <?php if (!empty($selectedCategory) || !empty($selectedBrand) || !empty($minPrice) || !empty($maxPrice) || !empty($keyword)): ?>
                <div class="active-filters-row">
                    <span style="font-size: 13px; color: var(--text-muted);">Đang lọc theo:</span>

                    <?php if (!empty($keyword)): ?>
                        <div class="active-filter-chip">
                            <span>Từ khóa: "<?= e($keyword) ?>"</span>
                            <a href="<?= buildFilterUrl(['keyword' => null]) ?>"><i class="fa-solid fa-xmark"></i></a>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($selectedCategory)): ?>
                        <div class="active-filter-chip">
                            <span>Danh mục: <?= e($selectedCategory['name']) ?></span>
                            <a href="<?= buildFilterUrl(['category' => null]) ?>"><i class="fa-solid fa-xmark"></i></a>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($selectedBrand)): ?>
                        <div class="active-filter-chip">
                            <span>Hãng: <?= e($selectedBrand['name']) ?></span>
                            <a href="<?= buildFilterUrl(['brand' => null]) ?>"><i class="fa-solid fa-xmark"></i></a>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($minPrice) || !empty($maxPrice)): ?>
                        <div class="active-filter-chip">
                            <span>
                                Giá: 
                                <?= !empty($minPrice) ? number_format($minPrice) . 'đ' : '0đ' ?> 
                                - 
                                <?= !empty($maxPrice) ? number_format($maxPrice) . 'đ' : '∞' ?>
                            </span>
                            <a href="<?= buildFilterUrl(['min_price' => null, 'max_price' => null]) ?>"><i class="fa-solid fa-xmark"></i></a>
                        </div>
                    <?php endif; ?>

                    <a href="<?= BASE_URL ?>/products" style="font-size: 12.5px; color: var(--sale); text-decoration: underline; margin-left: 6px;">
                        Xóa tất cả
                    </a>
                </div>
            <?php endif; ?>

            <!-- Lưới Sản Phẩm -->
            <?php if (!empty($products)): ?>
                <div class="product-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 20px;">
                    <?php foreach ($products as $p): 
                        $discount = (int)($p['discount_percent'] ?? 0);
                        $origPrice = (float)($p['price'] ?? 0);
                        $finalP = $origPrice * (1 - $discount / 100);
                    ?>
                        <div class="product-card">
                            <?php if ($discount > 0): ?>
                                <div class="badge-discount">-<?= $discount ?>%</div>
                            <?php elseif (!empty($p['is_new'])): ?>
                                <div class="badge-new">Mới</div>
                            <?php endif; ?>

                            <a href="<?= BASE_URL ?>/product/detail?slug=<?= urlencode($p['slug']) ?>" class="product-thumb">
                                <img src="<?= e($p['thumbnail']) ?>" alt="<?= e($p['name']) ?>" loading="lazy" onerror="this.onerror=null; this.src='<?= BASE_URL ?>/public/assets/images/default_product.jpg';">
                            </a>

                            <div class="product-body">
                                <div class="product-meta">
                                    <span class="meta-cat"><?= e($p['category_name'] ?? 'Sản phẩm') ?></span>
                                    <?php if (!empty($p['brand_name'])): ?>
                                        <span class="meta-brand">• <?= e($p['brand_name']) ?></span>
                                    <?php endif; ?>
                                </div>

                                <h3 class="product-name">
                                    <a href="<?= BASE_URL ?>/product/detail?slug=<?= urlencode($p['slug']) ?>"><?= e($p['name']) ?></a>
                                </h3>

                                <div class="product-rating">
                                    <span class="stars"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i></span>
                                    <span class="rating-views">(<?= number_format($p['view_count'] ?? 0) ?> lượt xem)</span>
                                </div>

                                <div class="product-prices">
                                    <span class="price-sale"><?= number_format($finalP) ?> đ</span>
                                    <?php if ($discount > 0): ?>
                                        <span class="price-original"><?= number_format($origPrice) ?> đ</span>
                                    <?php endif; ?>
                                </div>

                                <div class="product-actions">
                                    <a href="<?= BASE_URL ?>/product/detail?slug=<?= urlencode($p['slug']) ?>" class="btn btn-outline btn-sm">Xem Chi Tiết</a>
                                    <button class="btn btn-primary btn-sm btn-add-cart" data-id="<?= (int)$p['id'] ?>" title="Thêm vào giỏ">
                                        <i class="fa-solid fa-cart-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Phân trang thông minh (Pagination) -->
                <?php if ($totalPages > 1): ?>
                    <div class="pagination-wrapper">
                        <!-- Nút trang trước -->
                        <?php if ($page > 1): ?>
                            <a href="<?= buildFilterUrl(['page' => $page - 1]) ?>" class="page-btn" title="Trang trước">
                                <i class="fa-solid fa-chevron-left"></i>
                            </a>
                        <?php endif; ?>

                        <!-- Các trang -->
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <a href="<?= buildFilterUrl(['page' => $i]) ?>" class="page-btn <?= $i === $page ? 'active' : '' ?>">
                                <?= $i ?>
                            </a>
                        <?php endfor; ?>

                        <!-- Nút trang sau -->
                        <?php if ($page < $totalPages): ?>
                            <a href="<?= buildFilterUrl(['page' => $page + 1]) ?>" class="page-btn" title="Trang sau">
                                <i class="fa-solid fa-chevron-right"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

            <?php else: ?>
                <!-- Trạng thái trống khi lọc không có kết quả -->
                <div style="text-align: center; padding: 60px 20px; background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-lg); margin-top: 10px;">
                    <i class="fa-solid fa-box-open" style="font-size: 54px; color: var(--text-muted); margin-bottom: 16px; opacity: 0.6;"></i>
                    <h3 style="font-size: 20px; margin-bottom: 8px;">Không tìm thấy sản phẩm phù hợp</h3>
                    <p style="color: var(--text-muted); font-size: 14px; margin-bottom: 24px; max-width: 460px; margin-left: auto; margin-right: auto;">
                        Rất tiếc, hiện tại không có sản phẩm nào khớp với các tiêu chí lọc của bạn. Hãy thử thay đổi khoảng giá hoặc chọn thương hiệu khác.
                    </p>
                    <a href="<?= BASE_URL ?>/products" class="btn btn-primary" style="padding: 10px 24px;">
                        <i class="fa-solid fa-rotate-left"></i> Xem Tất Cả Sản Phẩm
                    </a>
                </div>
            <?php endif; ?>

        </main>
    </div>
</div>
