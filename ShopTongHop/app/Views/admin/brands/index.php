<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
    
    <!-- Cột Trái: Bảng Danh Sách Thương Hiệu -->
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-title">
                <i class="fa-solid fa-tags" style="color: var(--admin-primary);"></i>
                Thương Hiệu Đối Tác Chính Hãng (<?= count($brands) ?>)
            </div>
        </div>
        <div class="admin-table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width: 50px;">ID</th>
                        <th>Tên Thương Hiệu</th>
                        <th>Đường Dẫn Slug</th>
                        <th>Số Sản Phẩm</th>
                        <th>Trạng Thái</th>
                        <th style="width: 70px; text-align: center;">Thao Tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($brands)): ?>
                        <?php foreach ($brands as $b): ?>
                            <tr>
                                <td>#<?= $b['id'] ?></td>
                                <td>
                                    <strong style="color: var(--admin-text-main); font-size: 14px;">
                                        <?= e($b['name']) ?>
                                    </strong>
                                </td>
                                <td><code style="font-size: 12px;"><?= e($b['slug']) ?></code></td>
                                <td>
                                    <span class="badge badge-info"><?= $b['product_count'] ?? 0 ?> SP</span>
                                </td>
                                <td>
                                    <span class="badge badge-success">Hoạt động</span>
                                </td>
                                <td style="text-align: center;">
                                    <form method="POST" action="<?= BASE_URL ?>/admin/brands/delete" onsubmit="return confirm('Bạn có chắc chắn muốn xóa thương hiệu này?');" style="display: inline;">
                                        <input type="hidden" name="id" value="<?= $b['id'] ?>">
                                        <button type="submit" class="btn btn-danger btn-sm" title="Xóa">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center; color: var(--admin-text-muted); padding: 30px;">
                                Chưa có thương hiệu nào.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Cột Phải: Form Thêm Thương Hiệu Mới -->
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-title">
                <i class="fa-solid fa-plus-circle" style="color: var(--admin-primary);"></i>
                Thêm Thương Hiệu Mới
            </div>
        </div>
        <div class="admin-card-body">
            <form method="POST" action="<?= BASE_URL ?>/admin/brands/create">
                
                <div class="form-group">
                    <label class="form-label">Tên Thương Hiệu <span style="color:red">*</span></label>
                    <input type="text" name="name" class="form-control" placeholder="Ví dụ: Apple, Samsung, Sony, Nike..." required>
                </div>

                <div class="form-group">
                    <label class="form-label">Tên file Logo hoặc link ảnh</label>
                    <input type="text" name="logo" class="form-control" placeholder="Ví dụ: logo_apple.png hoặc https://...">
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 10px;">
                    <i class="fa-solid fa-plus"></i> Thêm Thương Hiệu
                </button>
            </form>
        </div>
    </div>

</div>
