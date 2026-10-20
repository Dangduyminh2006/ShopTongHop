<div class="admin-card">
    <div class="admin-card-header">
        <div class="admin-card-title">
            <i class="fa-solid fa-truck-fast" style="color: var(--admin-primary);"></i>
            Quản Lý Toàn Bộ Đơn Hàng (<?= count($orders) ?>)
        </div>
    </div>

    <!-- Thanh Tìm Kiếm & Lọc Theo Trạng Thái -->
    <div style="padding: 16px 22px; background: #f8fafc; border-bottom: 1px solid var(--admin-border);">
        <form method="GET" action="<?= BASE_URL ?>/admin/orders" style="display: flex; gap: 12px; flex-wrap: wrap;">
            <div style="flex: 2; min-width: 220px;">
                <input type="text" name="keyword" class="form-control" placeholder="Tìm theo mã đơn, họ tên, số điện thoại..." value="<?= e($keyword ?? '') ?>">
            </div>
            <div style="flex: 1; min-width: 180px;">
                <select name="status" class="form-select">
                    <option value="">-- Tất cả trạng thái --</option>
                    <option value="pending" <?= ($curStatus === 'pending') ? 'selected' : '' ?>>Chờ xác nhận</option>
                    <option value="confirmed" <?= ($curStatus === 'confirmed') ? 'selected' : '' ?>>Đã xác nhận</option>
                    <option value="shipping" <?= ($curStatus === 'shipping') ? 'selected' : '' ?>>Đang giao hàng</option>
                    <option value="completed" <?= ($curStatus === 'completed') ? 'selected' : '' ?>>Hoàn tất thành công</option>
                    <option value="cancelled" <?= ($curStatus === 'cancelled') ? 'selected' : '' ?>>Đã hủy</option>
                </select>
            </div>
            <button type="submit" class="btn btn-secondary">
                <i class="fa-solid fa-filter"></i> Lọc Đơn
            </button>
            <?php if (!empty($keyword) || !empty($curStatus)): ?>
                <a href="<?= BASE_URL ?>/admin/orders" class="btn btn-secondary" title="Đặt lại">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            <?php endif; ?>
        </form>
    </div>

    <div class="admin-table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Mã Đơn Hàng</th>
                    <th>Khách Hàng</th>
                    <th>Liên Hệ</th>
                    <th>Hình Thức</th>
                    <th>Thanh Toán</th>
                    <th>Trạng Thái Đơn</th>
                    <th>Tổng Tiền</th>
                    <th>Ngày Đặt</th>
                    <th style="width: 80px; text-align: center;">Chi Tiết</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($orders)): ?>
                    <?php foreach ($orders as $o): ?>
                        <tr>
                            <td>
                                <strong style="color: var(--admin-primary); font-family: monospace; font-size: 14px;">
                                    #<?= e($o['order_code']) ?>
                                </strong>
                            </td>
                            <td>
                                <strong><?= e($o['customer_name']) ?></strong>
                            </td>
                            <td>
                                <div><?= e($o['customer_phone']) ?></div>
                                <small style="color: var(--admin-text-muted);"><?= e($o['customer_email']) ?></small>
                            </td>
                            <td>
                                <span class="badge badge-secondary"><?= strtoupper($o['payment_method']) ?></span>
                            </td>
                            <td>
                                <?php if ($o['payment_status'] === 'paid'): ?>
                                    <span class="badge badge-success">Đã thanh toán</span>
                                <?php else: ?>
                                    <span class="badge badge-warning">Chưa thanh toán</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php
                                $st = $o['order_status'];
                                $badgeClass = match($st) {
                                    'completed' => 'badge-success',
                                    'shipping'  => 'badge-info',
                                    'confirmed' => 'badge-primary',
                                    'cancelled' => 'badge-danger',
                                    default     => 'badge-warning'
                                };
                                $stLabels = [
                                    'pending'   => 'Chờ xác nhận',
                                    'confirmed' => 'Đã xác nhận',
                                    'shipping'  => 'Đang giao',
                                    'completed' => 'Hoàn tất',
                                    'cancelled' => 'Đã hủy'
                                ];
                                ?>
                                <span class="badge <?= $badgeClass ?>"><?= $stLabels[$st] ?? $st ?></span>
                            </td>
                            <td>
                                <strong style="color: #ef4444; font-size: 14px;">
                                    <?= format_currency($o['total_amount']) ?>
                                </strong>
                            </td>
                            <td style="font-size: 12.5px; color: var(--admin-text-muted);">
                                <?= date('d/m/Y H:i', strtotime($o['created_at'])) ?>
                            </td>
                            <td style="text-align: center;">
                                <a href="<?= BASE_URL ?>/admin/orders/detail?id=<?= $o['id'] ?>" class="btn btn-primary btn-sm">
                                    <i class="fa-solid fa-eye"></i> Xem
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" style="text-align: center; color: var(--admin-text-muted); padding: 40px;">
                            Không tìm thấy đơn hàng nào.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
