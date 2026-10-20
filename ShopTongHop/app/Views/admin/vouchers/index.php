<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
    
    <!-- Cột Trái: Bảng Danh Sách Mã Giảm Giá -->
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-title">
                <i class="fa-solid fa-ticket" style="color: var(--admin-primary);"></i>
                Danh Sách Mã Giảm Giá Voucher (<?= count($vouchers) ?>)
            </div>
        </div>
        <div class="admin-table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Mã Code</th>
                        <th>Loại Giảm</th>
                        <th>Mức Giảm</th>
                        <th>Đơn Tối Thiểu</th>
                        <th>Lượt Dùng</th>
                        <th>Thời Hạn</th>
                        <th style="width: 60px; text-align: center;">Xóa</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($vouchers)): ?>
                        <?php foreach ($vouchers as $v): ?>
                            <tr>
                                <td>
                                    <span style="font-family: monospace; font-weight: 800; color: #b45309; background: #fef3c7; padding: 3px 8px; border-radius: 4px; border: 1px dashed #f59e0b;">
                                        <?= e($v['code']) ?>
                                    </span>
                                </td>
                                <td>
                                    <?= $v['discount_type'] === 'percent' ? 'Giảm %' : 'Tiền mặt' ?>
                                </td>
                                <td>
                                    <strong style="color: #16a34a;">
                                        <?= $v['discount_type'] === 'percent' ? (int)$v['discount_value'] . '%' : format_currency($v['discount_value']) ?>
                                    </strong>
                                </td>
                                <td><?= format_currency($v['min_order_amount']) ?></td>
                                <td>
                                    <span class="badge badge-info"><?= $v['used_count'] ?> / <?= $v['usage_limit'] ?></span>
                                </td>
                                <td style="font-size: 11.5px; color: var(--admin-text-muted);">
                                    <?= date('d/m/Y', strtotime($v['start_date'])) ?> ➔ <?= date('d/m/Y', strtotime($v['end_date'])) ?>
                                </td>
                                <td style="text-align: center;">
                                    <form method="POST" action="<?= BASE_URL ?>/admin/vouchers/delete" onsubmit="return confirm('Bạn có chắc chắn muốn xóa mã giảm giá này?');" style="display: inline;">
                                        <input type="hidden" name="id" value="<?= $v['id'] ?>">
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align: center; color: var(--admin-text-muted); padding: 30px;">
                                Chưa có mã giảm giá nào.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Cột Phải: Form Tạo Mã Giảm Giá -->
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-title">
                <i class="fa-solid fa-plus-circle" style="color: var(--admin-primary);"></i>
                Tạo Mã Giảm Giá Mới
            </div>
        </div>
        <div class="admin-card-body">
            <form method="POST" action="<?= BASE_URL ?>/admin/vouchers/create">
                
                <div class="form-group">
                    <label class="form-label">Mã Giảm Giá (Code) <span style="color:red">*</span></label>
                    <input type="text" name="code" class="form-control" placeholder="Ví dụ: SALE20, FREESHIP..." style="text-transform: uppercase;" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Loại Giảm Giá</label>
                        <select name="discount_type" class="form-select">
                            <option value="percent">Giảm theo %</option>
                            <option value="fixed">Giảm tiền mặt (₫)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Mức Giảm Giá <span style="color:red">*</span></label>
                        <input type="number" name="discount_value" class="form-control" placeholder="Ví dụ: 10 hoặc 50000" min="1" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Đơn Hàng Tối Thiểu (₫)</label>
                        <input type="number" name="min_order_amount" class="form-control" value="0" min="0" step="10000">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Giới Hạn Lượt Dùng</label>
                        <input type="number" name="usage_limit" class="form-control" value="100" min="1">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Ngày Bắt Đầu Áp Dụng</label>
                    <input type="datetime-local" name="start_date" class="form-control" value="<?= date('Y-m-d\TH:i') ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Ngày Kết Thúc Hạn Dùng</label>
                    <input type="datetime-local" name="end_date" class="form-control" value="<?= date('Y-m-d\TH:i', strtotime('+30 days')) ?>" required>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 10px;">
                    <i class="fa-solid fa-plus"></i> Kích Hoạt Mã Giảm Giá
                </button>
            </form>
        </div>
    </div>

</div>
