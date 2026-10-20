<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
    
    <!-- Cột Trái: Bảng Danh Sách Đợt Flash Sale -->
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-title">
                <i class="fa-solid fa-bolt" style="color: #f59e0b;"></i>
                Chiến Dịch Giờ Vàng Flash Sale (<?= count($sales) ?>)
            </div>
        </div>
        <div class="admin-table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Tên Chiến Dịch</th>
                        <th>Thời Gian Diễn Ra</th>
                        <th>Sản Phẩm Tham Gia</th>
                        <th>Trạng Thái</th>
                        <th style="width: 60px; text-align: center;">Xóa</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($sales)): ?>
                        <?php foreach ($sales as $s): ?>
                            <tr>
                                <td>
                                    <strong style="color: var(--admin-text-main); font-size: 14px;">
                                        <?= e($s['title']) ?>
                                    </strong>
                                </td>
                                <td style="font-size: 12px; color: var(--admin-text-muted);">
                                    <?= date('d/m/Y H:i', strtotime($s['start_time'])) ?> ➔ <?= date('d/m/Y H:i', strtotime($s['end_time'])) ?>
                                </td>
                                <td>
                                    <span class="badge badge-info"><?= $s['item_count'] ?> sản phẩm</span>
                                    <?php if (!empty($s['items'])): ?>
                                        <div style="font-size: 11px; margin-top: 4px; color: var(--admin-text-muted);">
                                            <?= e($s['items'][0]['name']) ?><?= count($s['items']) > 1 ? '...' : '' ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php
                                    $now = time();
                                    $st = strtotime($s['start_time']);
                                    $et = strtotime($s['end_time']);
                                    if ($now >= $st && $now <= $et): ?>
                                        <span class="badge badge-danger">Đang chạy</span>
                                    <?php elseif ($now < $st): ?>
                                        <span class="badge badge-warning">Sắp diễn ra</span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary">Đã kết thúc</span>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align: center;">
                                    <form method="POST" action="<?= BASE_URL ?>/admin/flash-sales/delete" onsubmit="return confirm('Bạn có chắc chắn muốn xóa đợt Flash Sale này?');" style="display: inline;">
                                        <input type="hidden" name="id" value="<?= $s['id'] ?>">
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align: center; color: var(--admin-text-muted); padding: 30px;">
                                Chưa có đợt Flash Sale nào.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Cột Phải: Form Tạo Đợt Flash Sale Mới -->
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-title">
                <i class="fa-solid fa-plus-circle" style="color: var(--admin-primary);"></i>
                Tạo Đợt Flash Sale Mới
            </div>
        </div>
        <div class="admin-card-body">
            <form method="POST" action="<?= BASE_URL ?>/admin/flash-sales/create">
                
                <div class="form-group">
                    <label class="form-label">Tên Chiến Dịch <span style="color:red">*</span></label>
                    <input type="text" name="title" class="form-control" placeholder="Ví dụ: Giờ Vàng Giá Sốc Hôm Nay..." required>
                </div>

                <div class="form-group">
                    <label class="form-label">Thời Gian Bắt Đầu</label>
                    <input type="datetime-local" name="start_time" class="form-control" value="<?= date('Y-m-d\TH:i') ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Thời Gian Kết Thúc</label>
                    <input type="datetime-local" name="end_time" class="form-control" value="<?= date('Y-m-d\TH:i', strtotime('+24 hours')) ?>" required>
                </div>

                <hr style="border: none; border-top: 1px dashed var(--admin-border); margin: 16px 0;">

                <div class="form-group">
                    <label class="form-label">Chọn Sản Phẩm Giảm Giá</label>
                    <select name="product_id" class="form-select">
                        <option value="">-- Chọn sản phẩm tham gia --</option>
                        <?php foreach ($products as $p): ?>
                            <option value="<?= $p['id'] ?>">
                                <?= e($p['name']) ?> (Gốc: <?= format_currency($p['price']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Giá Bán Flash Sale (₫)</label>
                        <input type="number" name="sale_price" class="form-control" placeholder="Ví dụ: 19900000" min="1000" step="1000">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Số Lượng Bán Sale</label>
                        <input type="number" name="sale_quantity" class="form-control" value="20" min="1">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 10px;">
                    <i class="fa-solid fa-bolt"></i> Khởi Tạo Flash Sale
                </button>
            </form>
        </div>
    </div>

</div>
