<!-- 4 Thẻ Thống Kê KPI Vàng -->
<div class="admin-kpi-grid">
    <div class="admin-kpi-card">
        <div class="admin-kpi-icon kpi-blue">
            <i class="fa-solid fa-coins"></i>
        </div>
        <div class="admin-kpi-info">
            <h4>Tổng Doanh Thu</h4>
            <div class="kpi-value"><?= format_currency($stats['total_revenue'] ?? 0) ?></div>
        </div>
    </div>

    <div class="admin-kpi-card">
        <div class="admin-kpi-icon kpi-green">
            <i class="fa-solid fa-bag-shopping"></i>
        </div>
        <div class="admin-kpi-info">
            <h4>Tổng Đơn Hàng</h4>
            <div class="kpi-value"><?= number_format($stats['total_orders'] ?? 0) ?></div>
        </div>
    </div>

    <div class="admin-kpi-card">
        <div class="admin-kpi-icon kpi-orange">
            <i class="fa-solid fa-boxes-stacked"></i>
        </div>
        <div class="admin-kpi-info">
            <h4>Tổng Sản Phẩm</h4>
            <div class="kpi-value"><?= number_format($stats['total_products'] ?? 0) ?></div>
        </div>
    </div>

    <div class="admin-kpi-card">
        <div class="admin-kpi-icon kpi-purple">
            <i class="fa-solid fa-users"></i>
        </div>
        <div class="admin-kpi-info">
            <h4>Khách Hàng Đăng Ký</h4>
            <div class="kpi-value"><?= number_format($stats['total_customers'] ?? 0) ?></div>
        </div>
    </div>
</div>

<!-- Lưới 2 Cột: Bảng Đơn Hàng Mới Nhất & Thống Kê Hoạt Động -->
<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
    
    <!-- Cột Trái: Đơn Hàng Mới Nhất -->
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-title">
                <i class="fa-solid fa-clock-rotate-left" style="color: var(--admin-primary);"></i>
                Đơn Hàng Mới Nhất
            </div>
            <a href="<?= BASE_URL ?>/admin/orders" class="btn btn-outline-primary btn-sm">Xem tất cả ➔</a>
        </div>
        <div class="admin-table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Mã Đơn</th>
                        <th>Khách Hàng</th>
                        <th>Tổng Tiền</th>
                        <th>Trạng Thái</th>
                        <th>Thời Gian</th>
                        <th>Hành Động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($recentOrders)): ?>
                        <?php foreach ($recentOrders as $o): ?>
                            <tr>
                                <td>
                                    <strong style="color: var(--admin-primary); font-family: monospace;">
                                        #<?= e($o['order_code']) ?>
                                    </strong>
                                </td>
                                <td>
                                    <div><?= e($o['customer_name']) ?></div>
                                    <small style="color: var(--admin-text-muted);"><?= e($o['customer_phone']) ?></small>
                                </td>
                                <td><strong><?= format_currency($o['total_amount']) ?></strong></td>
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
                                <td style="font-size: 12.5px; color: var(--admin-text-muted);">
                                    <?= date('d/m/Y H:i', strtotime($o['created_at'])) ?>
                                </td>
                                <td>
                                    <a href="<?= BASE_URL ?>/admin/orders/detail?id=<?= $o['id'] ?>" class="btn btn-secondary btn-sm">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center; color: var(--admin-text-muted); padding: 30px;">
                                Chưa có đơn hàng nào trong hệ thống.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Cột Phải: Lối Tắt Nhanh & Trạng Thái Xử Lý -->
    <div>
        <div class="admin-card">
            <div class="admin-card-header">
                <div class="admin-card-title">
                    <i class="fa-solid fa-bolt" style="color: #f59e0b;"></i>
                    Thao Tác Nhanh
                </div>
            </div>
            <div class="admin-card-body" style="display: flex; flex-direction: column; gap: 10px;">
                <a href="<?= BASE_URL ?>/admin/products/create" class="btn btn-primary" style="justify-content: flex-start;">
                    <i class="fa-solid fa-plus-circle"></i> Thêm Sản Phẩm Mới
                </a>
                <a href="<?= BASE_URL ?>/admin/vouchers" class="btn btn-secondary" style="justify-content: flex-start;">
                    <i class="fa-solid fa-ticket"></i> Tạo Mã Giảm Giá
                </a>
                <a href="<?= BASE_URL ?>/admin/flash-sales" class="btn btn-secondary" style="justify-content: flex-start;">
                    <i class="fa-solid fa-bolt"></i> Cài Đặt Flash Sale
                </a>
            </div>
        </div>

        <div class="admin-card">
            <div class="admin-card-header">
                <div class="admin-card-title">
                    <i class="fa-solid fa-bell" style="color: #3b82f6;"></i>
                    Cần Xử Lý Ngay
                </div>
            </div>
            <div class="admin-card-body" style="padding: 16px 22px;">
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid var(--admin-border);">
                    <span style="font-size: 13.5px;">Đơn chờ duyệt:</span>
                    <span class="badge badge-warning" style="font-size: 13px; font-weight: 700;">
                        <?= (int)($stats['pending_orders'] ?? 0) ?> đơn
                    </span>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid var(--admin-border);">
                    <span style="font-size: 13.5px;">Đơn hoàn tất:</span>
                    <span class="badge badge-success" style="font-size: 13px; font-weight: 700;">
                        <?= (int)($stats['completed_orders'] ?? 0) ?> đơn
                    </span>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px 0;">
                    <span style="font-size: 13.5px;">Tổng danh mục:</span>
                    <span class="badge badge-secondary" style="font-size: 13px; font-weight: 700;">
                        <?= (int)($stats['total_categories'] ?? 0) ?> mục
                    </span>
                </div>
            </div>
        </div>
    </div>

</div>
