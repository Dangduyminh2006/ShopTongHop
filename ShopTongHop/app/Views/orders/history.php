<div class="breadcrumb-container">
    <div class="container">
        <ul class="breadcrumb">
            <li><a href="<?= BASE_URL ?>/"><i class="fa-solid fa-house"></i> Trang Chủ</a></li>
            <li><i class="fa-solid fa-chevron-right sep"></i></li>
            <li class="active">Lịch Sử Mua Hàng</li>
        </ul>
    </div>
</div>

<div class="container order-history-page">
    <div class="order-page-header">
        <div class="header-titles">
            <h1 class="page-title"><i class="fa-solid fa-receipt"></i> Lịch Sử Mua Hàng & Hóa Đơn</h1>
            <p class="page-subtitle">Theo dõi hành trình đơn hàng, trạng thái thanh toán và xem lại hóa đơn điện tử của bạn</p>
        </div>
        <?php if (!empty($user)): ?>
            <div class="user-quick-profile">
                <span class="user-avatar-mini"><i class="fa-solid fa-user"></i></span>
                <div class="user-meta">
                    <strong><?= e($user['fullname']) ?></strong>
                    <span class="text-muted"><?= e($user['email']) ?></span>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <?php if (empty($user)): ?>
        <!-- GUEST LOOKUP & LOGIN PROMPT -->
        <div class="guest-order-container">
            <div class="guest-prompt-banner">
                <div class="prompt-icon"><i class="fa-solid fa-user-lock"></i></div>
                <div class="prompt-content">
                    <h3>Bạn chưa đăng nhập tài khoản?</h3>
                    <p>Đăng nhập để tự động lưu trữ, đồng bộ hóa đơn và theo dõi lịch sử tích điểm cho các lần mua tiếp theo.</p>
                </div>
                <a href="<?= BASE_URL ?>/login" class="btn btn-primary"><i class="fa-solid fa-arrow-right-to-bracket"></i> Đăng Nhập Ngay</a>
            </div>

            <div class="order-lookup-card">
                <div class="card-header">
                    <h3><i class="fa-solid fa-magnifying-glass"></i> Tra Cứu Nhanh Đơn Hàng Không Cần Đăng Nhập</h3>
                    <p>Nhập Mã đơn hàng và Số điện thoại bạn đã dùng khi đặt hàng để kiểm tra trạng thái</p>
                </div>

                <?php if (!empty($lookupError)): ?>
                    <div class="alert alert-danger">
                        <i class="fa-solid fa-circle-exclamation"></i> <?= e($lookupError) ?>
                    </div>
                <?php endif; ?>

                <form action="<?= BASE_URL ?>/order/lookup" method="POST" class="lookup-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="order_code"><i class="fa-solid fa-barcode"></i> Mã Đơn Hàng:</label>
                            <input type="text" id="order_code" name="order_code" placeholder="Ví dụ: SX2026-8801" required>
                        </div>
                        <div class="form-group">
                            <label for="phone"><i class="fa-solid fa-phone"></i> Số Điện Thoại Đặt Hàng:</label>
                            <input type="tel" id="phone" name="phone" placeholder="Ví dụ: 0912345678" required>
                        </div>
                        <div class="form-group form-action">
                            <label>&nbsp;</label>
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fa-solid fa-search"></i> Tra Cứu Ngay
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    <?php else: ?>
        <!-- LOGGED IN USER ORDER HISTORY -->
        
        <!-- Filter Tabs -->
        <div class="order-filter-tabs">
            <a href="<?= BASE_URL ?>/order/history?status=all" class="tab-item <?= ($currentTab === 'all') ? 'active' : '' ?>">
                Tất Cả <span class="tab-badge"><?= $counts['all'] ?></span>
            </a>
            <a href="<?= BASE_URL ?>/order/history?status=pending" class="tab-item <?= ($currentTab === 'pending') ? 'active' : '' ?>">
                Chờ Xử Lý <span class="tab-badge"><?= $counts['pending'] ?></span>
            </a>
            <a href="<?= BASE_URL ?>/order/history?status=confirmed" class="tab-item <?= ($currentTab === 'confirmed') ? 'active' : '' ?>">
                Đã Xác Nhận <span class="tab-badge"><?= $counts['confirmed'] ?></span>
            </a>
            <a href="<?= BASE_URL ?>/order/history?status=shipping" class="tab-item <?= ($currentTab === 'shipping') ? 'active' : '' ?>">
                Đang Giao Hàng <span class="tab-badge"><?= $counts['shipping'] ?></span>
            </a>
            <a href="<?= BASE_URL ?>/order/history?status=completed" class="tab-item <?= ($currentTab === 'completed') ? 'active' : '' ?>">
                Hoàn Tất <span class="tab-badge"><?= $counts['completed'] ?></span>
            </a>
            <a href="<?= BASE_URL ?>/order/history?status=cancelled" class="tab-item <?= ($currentTab === 'cancelled') ? 'active' : '' ?>">
                Đã Hủy <span class="tab-badge"><?= $counts['cancelled'] ?></span>
            </a>
        </div>

        <?php if (empty($orders)): ?>
            <!-- Empty State -->
            <div class="order-empty-state">
                <div class="empty-icon"><i class="fa-regular fa-folder-open"></i></div>
                <h3>Chưa có đơn hàng nào trong mục này</h3>
                <p>Khám phá ngay hàng ngàn sản phẩm chính hãng với mức giá hấp dẫn tại ShopXpress</p>
                <a href="<?= BASE_URL ?>/products" class="btn btn-primary"><i class="fa-solid fa-bag-shopping"></i> Mua Sắm Ngay</a>
            </div>
        <?php else: ?>
            <!-- Order List Cards -->
            <div class="order-cards-list">
                <?php foreach ($orders as $o): ?>
                    <?php 
                        $orderStatusClass = 'status-' . $o['order_status'];
                        $statusLabels = [
                            'pending'   => 'Chờ Xác Nhận',
                            'confirmed' => 'Đã Xác Nhận',
                            'shipping'  => 'Đang Giao Hàng',
                            'completed' => 'Giao Hàng Thành Công',
                            'cancelled' => 'Đã Hủy'
                        ];
                        $statusText = $statusLabels[$o['order_status']] ?? $o['order_status'];

                        $paymentStatusClass = ($o['payment_status'] === 'paid') ? 'badge-paid' : 'badge-unpaid';
                        $paymentText = ($o['payment_status'] === 'paid') ? 'Đã Thanh Toán' : 'Chưa Thanh Toán';
                        $paymentMethodName = ($o['payment_method'] === 'vietqr') ? 'Chuyển Khoản VietQR' : 'Tiền Mặt (COD)';
                    ?>
                    <div class="order-card-box">
                        <div class="order-card-header">
                            <div class="order-code-group">
                                <span class="order-code-label">MÃ ĐƠN HÀNG:</span>
                                <span class="order-code-value">#<?= e($o['order_code']) ?></span>
                                <span class="order-date"><i class="fa-regular fa-clock"></i> <?= date('d/m/Y H:i', strtotime($o['created_at'])) ?></span>
                            </div>
                            <div class="order-badges-group">
                                <span class="badge-payment <?= $paymentStatusClass ?>">
                                    <i class="fa-solid <?= ($o['payment_status'] === 'paid') ? 'fa-circle-check' : 'fa-hourglass-half' ?>"></i> <?= $paymentText ?>
                                </span>
                                <span class="badge-order-status <?= $orderStatusClass ?>">
                                    <?= $statusText ?>
                                </span>
                            </div>
                        </div>

                        <div class="order-card-body">
                            <div class="order-item-snippet">
                                <div class="item-thumb-box">
                                    <img src="<?= get_product_thumb_url($o['first_product_thumb'] ?? '') ?>" 
                                         alt="<?= e($o['first_product_name'] ?? 'Sản phẩm') ?>"
                                         onerror="this.onerror=null; this.src='<?= get_product_fallback_url($o['first_product_thumb'] ?? '') ?>';">
                                </div>
                                <div class="item-snippet-details">
                                    <h4 class="item-title"><?= e($o['first_product_name'] ?? 'Mặt hàng trong đơn') ?></h4>
                                    <div class="item-count-info">
                                        <?php if ((int)$o['total_items'] > 1): ?>
                                            <span class="text-muted">và <strong><?= (int)$o['total_items'] - 1 ?></strong> sản phẩm khác</span>
                                        <?php else: ?>
                                            <span class="text-muted">Tổng cộng: 1 sản phẩm</span>
                                        <?php endif; ?>
                                        <span class="dot-sep">•</span>
                                        <span class="payment-method-tag"><i class="fa-solid fa-credit-card"></i> <?= $paymentMethodName ?></span>
                                    </div>
                                    <div class="shipping-address-snippet">
                                        <i class="fa-solid fa-location-dot"></i> Giao đến: <?= e($o['shipping_address']) ?>
                                    </div>
                                </div>
                            </div>
                            <div class="order-card-pricing">
                                <span class="total-label">Tổng thanh toán:</span>
                                <span class="total-price-value"><?= format_currency($o['total_amount']) ?></span>
                            </div>
                        </div>

                        <div class="order-card-footer">
                            <div class="footer-left-info">
                                <?php if (!empty($o['voucher_code'])): ?>
                                    <span class="voucher-applied-tag"><i class="fa-solid fa-ticket"></i> Mã giảm: <strong><?= e($o['voucher_code']) ?></strong> (-<?= format_currency($o['discount_amount']) ?>)</span>
                                <?php endif; ?>
                            </div>
                            <div class="footer-actions">
                                <a href="<?= BASE_URL ?>/order/invoice/<?= $o['order_code'] ?>" target="_blank" class="btn btn-outline btn-sm">
                                    <i class="fa-solid fa-print"></i> In Hóa Đơn
                                </a>
                                <a href="<?= BASE_URL ?>/order/detail/<?= $o['order_code'] ?>" class="btn btn-primary btn-sm">
                                    <i class="fa-solid fa-eye"></i> Xem Chi Tiết
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>
