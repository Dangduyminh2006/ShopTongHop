<div class="container" style="max-width: 960px;">
    <!-- Breadcrumb -->
    <div class="breadcrumb-wrapper">
        <ul class="breadcrumb-list">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>"><i class="fa-solid fa-house"></i> Trang chủ</a></li>
            <li class="breadcrumb-separator"><i class="fa-solid fa-chevron-right"></i></li>
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/cart">Giỏ hàng</a></li>
            <li class="breadcrumb-separator"><i class="fa-solid fa-chevron-right"></i></li>
            <li class="breadcrumb-item active">Đặt hàng thành công</li>
        </ul>
    </div>

    <!-- 1. Hero Confirmation Box -->
    <div class="success-hero-box">
        <div class="success-icon-large">
            <i class="fa-solid fa-check"></i>
        </div>
        <h1 style="font-size: 28px; font-weight: 800; font-family: 'Outfit', sans-serif; margin-bottom: 8px;">
            Cảm Ơn Bạn Đã Đặt Hàng!
        </h1>
        <p style="color: var(--text-muted); font-size: 15px; margin-bottom: 20px;">
            Đơn hàng của bạn đã được tiếp nhận và đang trong quá trình xử lý đóng gói.
        </p>

        <div style="display: inline-flex; align-items: center; gap: 12px; background: var(--bg-surface-elevated); border: 1px dashed var(--primary); padding: 10px 20px; border-radius: 30px; font-size: 15px;">
            <span>Mã đơn hàng: <strong style="color: var(--primary); letter-spacing: 0.5px;"><?= e($order['order_code']) ?></strong></span>
            <button type="button" class="btn-copy" onclick="copyToClipboard('<?= e($order['order_code']) ?>')">
                <i class="fa-regular fa-copy"></i> Sao chép
            </button>
        </div>
    </div>

    <!-- 2. Khối Thanh Toán VietQR Tự Động (Nếu chọn phương thức VietQR) -->
    <?php if ($order['payment_method'] === 'vietqr'): ?>
        <div class="vietqr-card">
            <!-- Cột trái: Ảnh mã QR chuẩn -->
            <div class="vietqr-image-frame">
                <div class="vietqr-timer-pill" id="qrTimer">
                    <i class="fa-regular fa-clock"></i> Hết hạn sau: <span id="countdownTime">15:00</span>
                </div>
                <img src="<?= e($vietqr['qr_url']) ?>" alt="Mã VietQR Thanh Toán <?= e($order['order_code']) ?>">
                <div style="font-size: 12px; color: #475569; margin-top: 8px; font-weight: 500;">
                    Quét bằng ứng dụng Ngân hàng hoặc MoMo
                </div>
            </div>

            <!-- Cột phải: Thông tin tài khoản thụ hưởng -->
            <div>
                <h3 style="font-size: 20px; font-weight: 800; font-family: 'Outfit', sans-serif; margin-bottom: 6px; color: var(--text-primary);">
                    <i class="fa-solid fa-qrcode" style="color: var(--primary);"></i> Chuyển Khoản Qua VietQR
                </h3>
                <p style="color: var(--text-muted); font-size: 13.5px; margin-bottom: 18px;">
                    Vui lòng mở ứng dụng ngân hàng bất kỳ để quét mã QR hoặc chuyển khoản thủ công theo thông tin bên dưới:
                </p>

                <div class="copy-field-row">
                    <span style="color: var(--text-secondary);">Ngân hàng thụ hưởng:</span>
                    <strong style="color: var(--text-primary);"><?= e($vietqr['bank_name']) ?></strong>
                </div>

                <div class="copy-field-row">
                    <span style="color: var(--text-secondary);">Số tài khoản:</span>
                    <div>
                        <strong><?= e($vietqr['account_no']) ?></strong>
                        <button type="button" class="btn-copy" onclick="copyToClipboard('<?= e($vietqr['account_no']) ?>')" style="margin-left: 8px;">
                            <i class="fa-regular fa-copy"></i>
                        </button>
                    </div>
                </div>

                <div class="copy-field-row">
                    <span style="color: var(--text-secondary);">Chủ tài khoản:</span>
                    <strong style="color: var(--text-primary);"><?= e($vietqr['account_name']) ?></strong>
                </div>

                <div class="copy-field-row">
                    <span style="color: var(--text-secondary);">Số tiền cần thanh toán:</span>
                    <div>
                        <strong style="color: #FF0055; font-size: 17px;"><?= number_format($vietqr['amount']) ?> đ</strong>
                        <button type="button" class="btn-copy" onclick="copyToClipboard('<?= (int)$vietqr['amount'] ?>')" style="margin-left: 8px;">
                            <i class="fa-regular fa-copy"></i>
                        </button>
                    </div>
                </div>

                <div class="copy-field-row" style="background: rgba(255, 107, 53, 0.08); border-color: var(--primary);">
                    <span style="color: var(--text-secondary); font-weight: 600;">Nội dung chuyển khoản (bắt buộc):</span>
                    <div>
                        <strong><?= e($vietqr['memo']) ?></strong>
                        <button type="button" class="btn-copy" onclick="copyToClipboard('<?= e($vietqr['memo']) ?>')" style="margin-left: 8px;">
                            <i class="fa-regular fa-copy"></i>
                        </button>
                    </div>
                </div>

                <div style="margin-top: 14px; font-size: 12.5px; color: var(--text-muted); display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-circle-check" style="color: var(--success);"></i> 
                    Hệ thống tự động kích hoạt trạng thái đơn hàng ngay khi nhận được tín hiệu ngân hàng.
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- 3. Chi Tiết Đơn Hàng & Thông Tin Nhận Hàng -->
    <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 28px; margin-bottom: 32px; box-shadow: var(--shadow-sm);">
        <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 1px solid var(--border-color);">
            <i class="fa-solid fa-box-archive" style="color: var(--primary);"></i> Chi Tiết Mặt Hàng Đã Đặt
        </h3>

        <!-- Bảng sản phẩm -->
        <table class="cart-table" style="margin-bottom: 20px;">
            <thead>
                <tr>
                    <th>Sản phẩm</th>
                    <th style="width: 140px; text-align: center;">Đơn giá</th>
                    <th style="width: 100px; text-align: center;">Số lượng</th>
                    <th style="width: 140px; text-align: right;">Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orderItems as $item): ?>
                    <tr>
                        <td>
                            <div class="cart-product-cell">
                                <img src="<?= e($item['thumbnail'] ?? 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=100&auto=format&fit=crop') ?>" alt="<?= e($item['product_name']) ?>" class="cart-thumb" style="width: 54px; height: 54px;">
                                <div class="cart-product-info">
                                    <h4 style="font-size: 14.5px;"><?= e($item['product_name']) ?></h4>
                                    <?php if (!empty($item['sku'])): ?>
                                        <div class="cart-product-meta">SKU: <?= e($item['sku']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                        <td style="text-align: center; color: var(--text-primary); font-weight: 600;">
                            <?= number_format($item['product_price']) ?> đ
                        </td>
                        <td style="text-align: center; font-weight: 700; color: var(--text-primary);">
                            <?= (int)$item['quantity'] ?>
                        </td>
                        <td style="text-align: right; font-weight: 700; color: var(--primary);">
                            <?= number_format($item['subtotal']) ?> đ
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Khung 2 cột: Thông tin giao hàng & Tổng tiền -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; padding-top: 18px; border-top: 1px solid var(--border-color);">
            <div>
                <h4 style="font-size: 15px; font-weight: 700; margin-bottom: 10px; color: var(--text-primary);">
                    <i class="fa-solid fa-truck"></i> Thông Tin Giao Nhận:
                </h4>
                <div style="font-size: 13.5px; color: var(--text-secondary); line-height: 1.8;">
                    <div>Người nhận: <strong><?= e($order['customer_name']) ?></strong></div>
                    <div>Số điện thoại: <strong><?= e($order['customer_phone']) ?></strong></div>
                    <div>Địa chỉ: <?= e($order['shipping_address']) ?></div>
                    <div>Hình thức: <strong><?= $order['payment_method'] === 'vietqr' ? 'Chuyển khoản VietQR' : 'Thanh toán COD' ?></strong></div>
                    <?php if (!empty($order['notes'])): ?>
                        <div>Ghi chú: <em>"<?= e($order['notes']) ?>"</em></div>
                    <?php endif; ?>
                </div>
            </div>

            <div style="background: var(--bg-surface-elevated); border: 1px solid var(--border-color); border-radius: 8px; padding: 16px 20px;">
                <div class="summary-row">
                    <span>Giảm giá voucher:</span>
                    <strong style="color: var(--success);">-<?= number_format($order['discount_amount'] ?? 0) ?> đ</strong>
                </div>
                <div class="summary-row total-row" style="margin-top: 10px; padding-top: 10px;">
                    <span>Tổng đơn hàng:</span>
                    <span class="total-price" style="font-size: 22px;"><?= number_format($order['total_amount']) ?> đ</span>
                </div>
                <div style="margin-top: 8px; font-size: 13px; color: var(--text-muted); text-align: right;">
                    Trạng thái: <span class="meta-pill" style="color: var(--warning); border-color: rgba(255, 179, 0, 0.3); background: rgba(255, 179, 0, 0.1);">Chờ xác nhận</span>
                </div>
            </div>
        </div>
    </div>

    <!-- 4. Thanh Nút Thao Tác -->
    <div style="display: flex; justify-content: center; gap: 16px; flex-wrap: wrap; margin-bottom: 60px;">
        <a href="<?= BASE_URL ?>/order/detail?code=<?= urlencode($order['order_code']) ?>" class="btn btn-secondary" style="padding: 12px 24px; font-weight: 600;">
            <i class="fa-solid fa-timeline"></i> Theo Dõi Đơn Hàng
        </a>
        <a href="<?= BASE_URL ?>/order/invoice?code=<?= urlencode($order['order_code']) ?>" target="_blank" class="btn btn-secondary" style="padding: 12px 24px; font-weight: 600;">
            <i class="fa-solid fa-print"></i> In Hóa Đơn A4
        </a>
        <a href="<?= BASE_URL ?>/products" class="btn btn-primary" style="padding: 12px 28px; font-weight: 600;">
            <i class="fa-solid fa-store"></i> Tiếp Tục Mua Sắm
        </a>
    </div>
</div>

<script>
// Hàm sao chép dữ liệu vào clipboard
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        if (window.showToast) {
            window.showToast('Đã sao chép: ' + text, 'success');
        } else {
            alert('Đã sao chép: ' + text);
        }
    }).catch(() => {
        alert('Đã sao chép: ' + text);
    });
}

// Đồng hồ đếm ngược 15 phút cho mã VietQR
<?php if ($order['payment_method'] === 'vietqr'): ?>
document.addEventListener('DOMContentLoaded', () => {
    let timeLeft = 15 * 60; // 15 phút
    const timerElem = document.getElementById('countdownTime');
    if (!timerElem) return;

    const timerInterval = setInterval(() => {
        timeLeft--;
        if (timeLeft <= 0) {
            clearInterval(timerInterval);
            timerElem.textContent = 'Đã hết hạn';
            timerElem.style.color = 'var(--danger)';
            return;
        }

        const minutes = Math.floor(timeLeft / 60);
        const seconds = timeLeft % 60;
        timerElem.textContent = `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
    }, 1000);
});
<?php endif; ?>
</script>
