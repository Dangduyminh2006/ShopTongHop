<div class="breadcrumb-container">
    <div class="container">
        <ul class="breadcrumb">
            <li><a href="<?= BASE_URL ?>/"><i class="fa-solid fa-house"></i> Trang Chủ</a></li>
            <li><i class="fa-solid fa-chevron-right sep"></i></li>
            <li><a href="<?= BASE_URL ?>/order/history">Lịch Sử Mua Hàng</a></li>
            <li><i class="fa-solid fa-chevron-right sep"></i></li>
            <li class="active">Đơn Hàng #<?= e($order['order_code']) ?></li>
        </ul>
    </div>
</div>

<div class="container order-detail-page">
    <div class="detail-top-nav">
        <a href="<?= BASE_URL ?>/order/history" class="back-link">
            <i class="fa-solid fa-arrow-left"></i> Quay lại Danh Sách Đơn Hàng
        </a>
        <div class="top-actions">
            <a href="<?= BASE_URL ?>/order/invoice/<?= $order['order_code'] ?>" target="_blank" class="btn btn-outline btn-sm">
                <i class="fa-solid fa-print"></i> Xem & In Hóa Đơn
            </a>
        </div>
    </div>

    <!-- ORDER SUMMARY HERO BANNER -->
    <div class="order-hero-card">
        <div class="hero-left">
            <div class="order-icon-badge"><i class="fa-solid fa-box-open"></i></div>
            <div class="hero-meta">
                <h2>Đơn Hàng #<?= e($order['order_code']) ?></h2>
                <p>Đặt vào lúc <strong><?= date('d/m/Y - H:i:s', strtotime($order['created_at'])) ?></strong></p>
            </div>
        </div>
        <div class="hero-right">
            <?php 
                $statusLabels = [
                    'pending'   => 'Chờ Xác Nhận',
                    'confirmed' => 'Đã Xác Nhận',
                    'shipping'  => 'Đang Giao Hàng',
                    'completed' => 'Giao Hàng Thành Công',
                    'cancelled' => 'Đã Hủy'
                ];
                $statusText = $statusLabels[$order['order_status']] ?? $order['order_status'];
                $orderStatusClass = 'status-' . $order['order_status'];
            ?>
            <span class="badge-order-status <?= $orderStatusClass ?> lg-badge">
                <i class="fa-solid fa-circle-dot"></i> <?= $statusText ?>
            </span>
        </div>
    </div>

    <!-- TRACKING TIMELINE STEPPER -->
    <?php if ($order['order_status'] !== 'cancelled'): ?>
        <?php 
            $steps = ['pending' => 1, 'confirmed' => 2, 'shipping' => 3, 'completed' => 4];
            $currentStep = $steps[$order['order_status']] ?? 1;
        ?>
        <div class="tracking-stepper-card">
            <div class="stepper-step <?= ($currentStep >= 1) ? 'step-done' : '' ?> <?= ($currentStep === 1) ? 'step-active' : '' ?>">
                <div class="step-circle"><i class="fa-solid fa-file-invoice"></i></div>
                <div class="step-label">Đặt Hàng</div>
                <div class="step-time"><?= date('d/m/Y', strtotime($order['created_at'])) ?></div>
            </div>
            <div class="stepper-line <?= ($currentStep >= 2) ? 'line-done' : '' ?>"></div>

            <div class="stepper-step <?= ($currentStep >= 2) ? 'step-done' : '' ?> <?= ($currentStep === 2) ? 'step-active' : '' ?>">
                <div class="step-circle"><i class="fa-solid fa-clipboard-check"></i></div>
                <div class="step-label">Đã Xác Nhận</div>
                <div class="step-time"><?= ($currentStep >= 2) ? 'Đã kiểm kho' : 'Chờ duyệt' ?></div>
            </div>
            <div class="stepper-line <?= ($currentStep >= 3) ? 'line-done' : '' ?>"></div>

            <div class="stepper-step <?= ($currentStep >= 3) ? 'step-done' : '' ?> <?= ($currentStep === 3) ? 'step-active' : '' ?>">
                <div class="step-circle"><i class="fa-solid fa-truck-fast"></i></div>
                <div class="step-label">Đang Giao Hàng</div>
                <div class="step-time"><?= ($currentStep >= 3) ? 'Đơn vị vận chuyển' : 'Chuẩn bị hàng' ?></div>
            </div>
            <div class="stepper-line <?= ($currentStep >= 4) ? 'line-done' : '' ?>"></div>

            <div class="stepper-step <?= ($currentStep >= 4) ? 'step-done' : '' ?> <?= ($currentStep === 4) ? 'step-active' : '' ?>">
                <div class="step-circle"><i class="fa-solid fa-circle-check"></i></div>
                <div class="step-label">Hoàn Tất</div>
                <div class="step-time"><?= ($currentStep >= 4) ? 'Đã nhận hàng' : 'Dự kiến 2-3 ngày' ?></div>
            </div>
        </div>
    <?php else: ?>
        <div class="order-cancelled-alert">
            <i class="fa-solid fa-triangle-exclamation"></i>
            <div>
                <h4>Đơn hàng đã bị hủy</h4>
                <p>Đơn hàng này đã được hủy theo yêu cầu hoặc do không thể xử lý. Nếu bạn có thắc mắc, vui lòng liên hệ Hotline 1900 8899.</p>
            </div>
        </div>
    <?php endif; ?>

    <!-- MAIN TWO-COLUMN DETAILS -->
    <div class="order-detail-grid">
        <!-- LEFT: PRODUCTS LIST & FINANCIALS -->
        <div class="detail-left-col">
            <div class="order-box-card">
                <div class="card-header-styled">
                    <h3><i class="fa-solid fa-cubes"></i> Danh Sách Sản Phẩm Đã Mua (<?= count($items) ?>)</h3>
                </div>
                <div class="order-items-table-wrap">
                    <table class="order-items-table">
                        <thead>
                            <tr>
                                <th>Sản Phẩm</th>
                                <th class="text-right">Đơn Giá</th>
                                <th class="text-center">Số Lượng</th>
                                <th class="text-right">Tạm Tính</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                                $calculatedSubtotal = 0;
                                foreach ($items as $item): 
                                    $calculatedSubtotal += (float)$item['subtotal'];
                            ?>
                                <tr>
                                    <td>
                                        <div class="table-product-cell">
                                            <div class="product-mini-thumb">
                                                <img src="<?= get_product_thumb_url($item['thumbnail'] ?? '') ?>" 
                                                     alt="<?= e($item['product_name']) ?>"
                                                     onerror="this.onerror=null; this.src='<?= get_product_fallback_url($item['thumbnail'] ?? '') ?>';">
                                            </div>
                                            <div class="product-cell-info">
                                                <a href="<?= BASE_URL ?>/product/detail/<?= $item['product_id'] ?>" class="cell-name">
                                                    <?= e($item['product_name']) ?>
                                                </a>
                                                <?php if (!empty($item['sku'])): ?>
                                                    <span class="cell-sku">SKU: <?= e($item['sku']) ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-right font-weight-600">
                                        <?= format_currency($item['product_price']) ?>
                                    </td>
                                    <td class="text-center">
                                        <span class="qty-bubble">x<?= (int)$item['quantity'] ?></span>
                                    </td>
                                    <td class="text-right font-weight-700 text-primary">
                                        <?= format_currency($item['subtotal']) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Financial Calculation Breakdown -->
                <div class="order-financial-breakdown">
                    <div class="calc-row">
                        <span class="label">Tổng tiền hàng:</span>
                        <span class="val"><?= format_currency($calculatedSubtotal) ?></span>
                    </div>
                    <?php if ((float)$order['discount_amount'] > 0): ?>
                        <div class="calc-row discount-row">
                            <span class="label">
                                <i class="fa-solid fa-tag"></i> Giảm giá Voucher (<strong><?= e($order['voucher_code']) ?></strong>):
                            </span>
                            <span class="val text-success">-<?= format_currency($order['discount_amount']) ?></span>
                        </div>
                    <?php endif; ?>
                    <div class="calc-row">
                        <span class="label">Phí vận chuyển:</span>
                        <span class="val text-success">Miễn phí giao hàng</span>
                    </div>
                    <div class="calc-row total-row">
                        <span class="label">Tổng cộng thanh toán:</span>
                        <span class="val total-accent"><?= format_currency($order['total_amount']) ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT: RECIPIENT & PAYMENT INFO -->
        <div class="detail-right-col">
            <!-- Recipient Information Card -->
            <div class="order-box-card">
                <div class="card-header-styled">
                    <h3><i class="fa-solid fa-location-dot"></i> Thông Tin Nhận Hàng</h3>
                </div>
                <div class="card-body-padded">
                    <div class="info-group">
                        <span class="info-label">Người nhận hàng:</span>
                        <strong class="info-val"><?= e($order['customer_name']) ?></strong>
                    </div>
                    <div class="info-group">
                        <span class="info-label">Số điện thoại:</span>
                        <span class="info-val"><a href="tel:<?= e($order['customer_phone']) ?>"><?= e($order['customer_phone']) ?></a></span>
                    </div>
                    <div class="info-group">
                        <span class="info-label">Email liên lạc:</span>
                        <span class="info-val"><?= e($order['customer_email']) ?></span>
                    </div>
                    <div class="info-group">
                        <span class="info-label">Địa chỉ giao hàng:</span>
                        <span class="info-val"><?= e($order['shipping_address']) ?></span>
                    </div>
                    <?php if (!empty($order['notes'])): ?>
                        <div class="info-group notes-group">
                            <span class="info-label">Ghi chú đơn hàng:</span>
                            <p class="notes-text">"<?= e($order['notes']) ?>"</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Payment Information Card -->
            <div class="order-box-card">
                <div class="card-header-styled">
                    <h3><i class="fa-solid fa-credit-card"></i> Phương Thức Thanh Toán</h3>
                </div>
                <div class="card-body-padded">
                    <?php if ($order['payment_method'] === 'vietqr'): ?>
                        <div class="payment-method-box method-vietqr">
                            <div class="method-header">
                                <span class="badge-method"><i class="fa-solid fa-qrcode"></i> Chuyển Khoản Ngân Hàng (VietQR)</span>
                                <?php if ($order['payment_status'] === 'paid'): ?>
                                    <span class="badge-status-paid"><i class="fa-solid fa-circle-check"></i> Đã Thanh Toán</span>
                                <?php else: ?>
                                    <span class="badge-status-unpaid"><i class="fa-solid fa-clock"></i> Chưa Thanh Toán</span>
                                <?php endif; ?>
                            </div>

                            <?php if ($order['payment_status'] !== 'paid' && $order['order_status'] !== 'cancelled'): ?>
                                <div class="qr-payment-prompt">
                                    <p class="qr-guide-text">Quét mã VietQR bên dưới để thanh toán tức thì qua ứng dụng Mobile Banking:</p>
                                    <div class="qr-image-container">
                                        <img src="<?= generate_vietqr_url($order['total_amount'], $order['order_code']) ?>" 
                                             alt="Mã VietQR Đơn Hàng #<?= e($order['order_code']) ?>" class="vietqr-img">
                                    </div>
                                    <div class="qr-bank-details">
                                        <div>Ngân hàng: <strong><?= VIETQR_BANK_ID ?> Bank</strong></div>
                                        <div>Số tài khoản: <strong><?= VIETQR_ACCOUNT_NO ?></strong></div>
                                        <div>Chủ tài khoản: <strong><?= VIETQR_ACCOUNT_NAME ?></strong></div>
                                        <div>Số tiền: <strong><?= format_currency($order['total_amount']) ?></strong></div>
                                        <div>Nội dung CK: <strong class="text-primary"><?= e($order['order_code']) ?></strong></div>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="paid-confirmation">
                                    <i class="fa-solid fa-shield-check text-success"></i>
                                    <p>Đơn hàng này đã được xác nhận thanh toán thành công qua cổng VietQR.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="payment-method-box method-cod">
                            <div class="method-header">
                                <span class="badge-method"><i class="fa-solid fa-money-bill-wave"></i> Thanh Toán Khi Nhận Hàng (COD)</span>
                                <span class="badge-status-cod"><i class="fa-solid fa-truck"></i> Thu tiền tận nơi</span>
                            </div>
                            <p class="cod-note">Bạn sẽ thanh toán số tiền <strong><?= format_currency($order['total_amount']) ?></strong> trực tiếp cho nhân viên giao hàng khi nhận và kiểm tra gói hàng.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Customer Service Assistance Card -->
            <div class="order-box-card help-card">
                <div class="card-body-padded">
                    <div class="help-inner">
                        <i class="fa-solid fa-headset help-icon"></i>
                        <div class="help-text">
                            <h4>Cần Hỗ Trợ Đơn Hàng?</h4>
                            <p>Hotline: <strong>1900 8899</strong> (8h00 - 21h00)<br>Email: <strong>support@shoptonghop.vn</strong></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
