<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <div>
        <h2 style="font-size: 20px; font-weight: 700; color: var(--admin-text-main); margin-bottom: 4px;">
            Chi Tiết Đơn Hàng #<?= e($order['order_code']) ?>
        </h2>
        <span style="font-size: 13px; color: var(--admin-text-muted);">
            Đặt lúc: <?= date('d/m/Y H:i:s', strtotime($order['created_at'])) ?>
        </span>
    </div>
    <div style="display: flex; gap: 10px;">
        <a href="<?= BASE_URL ?>/order/invoice?code=<?= urlencode($order['order_code']) ?>&phone=<?= urlencode($order['customer_phone']) ?>" target="_blank" class="btn btn-secondary btn-sm">
            <i class="fa-solid fa-print"></i> Xem & In Hóa Đơn A4
        </a>
        <a href="<?= BASE_URL ?>/admin/orders" class="btn btn-secondary btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Quay Lại
        </a>
    </div>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">

    <!-- Cột Trái: Danh Sách Sản Phẩm Trong Đơn -->
    <div>
        <div class="admin-card">
            <div class="admin-card-header">
                <div class="admin-card-title">
                    <i class="fa-solid fa-box" style="color: var(--admin-primary);"></i>
                    Danh Sách Mặt Hàng (<?= count($items) ?> sản phẩm)
                </div>
            </div>
            <div class="admin-table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th style="width: 50px;">Ảnh</th>
                            <th>Tên Mặt Hàng</th>
                            <th>Đơn Giá</th>
                            <th style="text-align: center;">SL</th>
                            <th style="text-align: right;">Thành Tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $it): ?>
                            <tr>
                                <td>
                                    <img src="<?= get_product_thumb_url($it['thumbnail']) ?>" 
                                         class="admin-thumb" 
                                         alt="<?= e($it['product_name']) ?>"
                                         onerror="this.src='<?= get_product_fallback_url($it['thumbnail']) ?>'">
                                </td>
                                <td>
                                    <strong><?= e($it['product_name']) ?></strong>
                                    <?php if (!empty($it['sku'])): ?>
                                        <div style="font-size: 11px; color: var(--admin-text-muted);">SKU: <?= e($it['sku']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td><?= format_currency($it['product_price']) ?></td>
                                <td style="text-align: center;">x<?= $it['quantity'] ?></td>
                                <td style="text-align: right; font-weight: 700; color: var(--admin-text-main);">
                                    <?= format_currency($it['subtotal']) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Tổng Kết Tiền -->
            <div style="padding: 18px 22px; background: #f8fafc; border-top: 1px solid var(--admin-border);">
                <div style="display: flex; justify-content: flex-end; margin-bottom: 6px;">
                    <span style="width: 140px; color: var(--admin-text-muted);">Tạm tính:</span>
                    <strong style="width: 140px; text-align: right;"><?= format_currency($order['total_amount'] + $order['discount_amount']) ?></strong>
                </div>
                <?php if ($order['discount_amount'] > 0): ?>
                    <div style="display: flex; justify-content: flex-end; margin-bottom: 6px; color: #16a34a;">
                        <span style="width: 140px;">Giảm giá (<?= e($order['voucher_code'] ?? 'Coupon') ?>):</span>
                        <strong style="width: 140px; text-align: right;">-<?= format_currency($order['discount_amount']) ?></strong>
                    </div>
                <?php endif; ?>
                <div style="display: flex; justify-content: flex-end; font-size: 16px; margin-top: 10px; padding-top: 10px; border-top: 1px dashed var(--admin-border);">
                    <span style="width: 140px; font-weight: 700;">Tổng thanh toán:</span>
                    <strong style="width: 140px; text-align: right; color: #ef4444; font-size: 18px;">
                        <?= format_currency($order['total_amount']) ?>
                    </strong>
                </div>
            </div>
        </div>

        <!-- Thông Tin Giao Nhận Hàng Hóa -->
        <div class="admin-card">
            <div class="admin-card-header">
                <div class="admin-card-title">
                    <i class="fa-solid fa-location-dot" style="color: #ef4444;"></i>
                    Địa Chỉ Giao Nhận Hàng
                </div>
            </div>
            <div class="admin-card-body">
                <p><strong>Người nhận:</strong> <?= e($order['customer_name']) ?></p>
                <p><strong>Số điện thoại:</strong> <?= e($order['customer_phone']) ?></p>
                <p><strong>Email:</strong> <?= e($order['customer_email']) ?></p>
                <p><strong>Địa chỉ chi tiết:</strong> <?= e($order['shipping_address']) ?></p>
                <?php if (!empty($order['notes'])): ?>
                    <p style="margin-top: 10px; padding: 10px; background: #fffbeb; border-radius: 6px; font-size: 13px; color: #b45309;">
                        <strong>Ghi chú đơn hàng:</strong> <?= e($order['notes']) ?>
                    </p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Cột Phải: Cập Nhật Trạng Thái Đơn Hàng -->
    <div>
        <div class="admin-card">
            <div class="admin-card-header">
                <div class="admin-card-title">
                    <i class="fa-solid fa-sliders" style="color: var(--admin-primary);"></i>
                    Xử Lý Đơn Hàng
                </div>
            </div>
            <div class="admin-card-body">
                <form method="POST" action="<?= BASE_URL ?>/admin/orders/update-status">
                    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">

                    <div class="form-group">
                        <label class="form-label">Trạng Thái Xử Lý Đơn</label>
                        <select name="order_status" class="form-select">
                            <option value="pending" <?= ($order['order_status'] === 'pending') ? 'selected' : '' ?>>Chờ xác nhận</option>
                            <option value="confirmed" <?= ($order['order_status'] === 'confirmed') ? 'selected' : '' ?>>Đã xác nhận</option>
                            <option value="shipping" <?= ($order['order_status'] === 'shipping') ? 'selected' : '' ?>>Đang giao hàng</option>
                            <option value="completed" <?= ($order['order_status'] === 'completed') ? 'selected' : '' ?>>Hoàn tất thành công</option>
                            <option value="cancelled" <?= ($order['order_status'] === 'cancelled') ? 'selected' : '' ?>>Hủy đơn hàng</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Tình Trạng Thanh Toán</label>
                        <select name="payment_status" class="form-select">
                            <option value="pending" <?= ($order['payment_status'] === 'pending') ? 'selected' : '' ?>>Chưa thanh toán</option>
                            <option value="paid" <?= ($order['payment_status'] === 'paid') ? 'selected' : '' ?>>Đã thanh toán</option>
                            <option value="failed" <?= ($order['payment_status'] === 'failed') ? 'selected' : '' ?>>Thanh toán thất bại</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Phương Thức Thanh Toán</label>
                        <div style="font-weight: 700; color: var(--admin-primary); font-size: 14px;">
                            <i class="fa-solid <?= $order['payment_method'] === 'vietqr' ? 'fa-qrcode' : 'fa-hand-holding-dollar' ?>"></i>
                            <?= strtoupper($order['payment_method']) === 'VIETQR' ? 'Chuyển Khoản Ngân Hàng VietQR Napas247' : 'Thanh Toán Khi Nhận Hàng (COD)' ?>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%; padding: 10px;">
                        <i class="fa-solid fa-floppy-disk"></i> Cập Nhật Trạng Thái
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>
