<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
    
    <!-- Cột Trái: Bảng Danh Sách Danh Mục -->
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-title">
                <i class="fa-solid fa-layer-group" style="color: var(--admin-primary);"></i>
                Cây Danh Mục Hàng Hóa (<?= count($categories) ?>)
            </div>
        </div>
        <div class="admin-table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width: 40px;">Icon</th>
                        <th>Tên Danh Mục</th>
                        <th>Đường Dẫn Slug</th>
                        <th>Cấp Danh Mục</th>
                        <th>Sản Phẩm</th>
                        <th style="width: 70px; text-align: center;">Thao Tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($categories)): ?>
                        <?php foreach ($categories as $cat): ?>
                            <tr>
                                <td>
                                    <i class="fa-solid <?= e($cat['icon'] ?? 'fa-folder') ?>" style="font-size: 16px; color: var(--admin-primary);"></i>
                                </td>
                                <td>
                                    <strong style="color: var(--admin-text-main);">
                                        <?= e($cat['name']) ?>
                                    </strong>
                                </td>
                                <td><code style="font-size: 12px;"><?= e($cat['slug']) ?></code></td>
                                <td>
                                    <?php if (empty($cat['parent_id'])): ?>
                                        <span class="badge badge-primary">Danh mục Gốc</span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary">Con: <?= e($cat['parent_name'] ?? '') ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge badge-info"><?= $cat['product_count'] ?? 0 ?> SP</span>
                                </td>
                                <td style="text-align: center;">
                                    <form method="POST" action="<?= BASE_URL ?>/admin/categories/delete" onsubmit="return confirm('Bạn có chắc chắn muốn xóa danh mục này?');" style="display: inline;">
                                        <input type="hidden" name="id" value="<?= $cat['id'] ?>">
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
                                Chưa có danh mục nào.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Cột Phải: Form Thêm Nhanh Danh Mục -->
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-title">
                <i class="fa-solid fa-plus-circle" style="color: var(--admin-primary);"></i>
                Thêm Danh Mục Mới
            </div>
        </div>
        <div class="admin-card-body">
            <form method="POST" action="<?= BASE_URL ?>/admin/categories/create">
                
                <div class="form-group">
                    <label class="form-label">Tên Danh Mục <span style="color:red">*</span></label>
                    <input type="text" name="name" class="form-control" placeholder="Ví dụ: Thiết bị số, Thời trang nữ..." required>
                </div>

                <div class="form-group">
                    <label class="form-label">Danh Mục Cha (Để trống nếu là mục gốc)</label>
                    <select name="parent_id" class="form-select">
                        <option value="">-- Là danh mục gốc (Level 1) --</option>
                        <?php foreach ($parentCategories as $pcat): ?>
                            <option value="<?= $pcat['id'] ?>"><?= e($pcat['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Class Icon FontAwesome</label>
                    <input type="text" name="icon" class="form-control" value="fa-folder" placeholder="Ví dụ: fa-mobile-screen, fa-shirt...">
                </div>

                <div class="form-group">
                    <label class="form-label">Thứ Tự Sắp Xếp</label>
                    <input type="number" name="display_order" class="form-control" value="0">
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 10px;">
                    <i class="fa-solid fa-plus"></i> Thêm Danh Mục
                </button>
            </form>
        </div>
    </div>

</div>
