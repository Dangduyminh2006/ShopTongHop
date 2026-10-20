<div class="admin-card">
    <div class="admin-card-header">
        <div class="admin-card-title">
            <i class="fa-solid fa-boxes-stacked" style="color: var(--admin-primary);"></i>
            Danh Sách Sản Phẩm Trong Kho (<?= count($products) ?>)
        </div>
        <a href="<?= BASE_URL ?>/admin/products/create" class="btn btn-primary">
            <i class="fa-solid fa-plus-circle"></i> Thêm Sản Phẩm Mới
        </a>
    </div>

    <!-- Thanh Tìm Kiếm & Bộ Lọc Nhanh -->
    <div style="padding: 16px 22px; background: #f8fafc; border-bottom: 1px solid var(--admin-border);">
        <form method="GET" action="<?= BASE_URL ?>/admin/products" style="display: flex; gap: 12px; flex-wrap: wrap;">
            <div style="flex: 2; min-width: 200px;">
                <input type="text" name="keyword" class="form-control" placeholder="Tìm theo tên sản phẩm hoặc mã SKU..." value="<?= e($keyword ?? '') ?>">
            </div>
            <div style="flex: 1; min-width: 160px;">
                <select name="category_id" class="form-select">
                    <option value="">-- Tất cả danh mục --</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= ($categoryId == $cat['id']) ? 'selected' : '' ?>>
                            <?= e($cat['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div style="flex: 1; min-width: 160px;">
                <select name="brand_id" class="form-select">
                    <option value="">-- Tất cả thương hiệu --</option>
                    <?php foreach ($brands as $b): ?>
                        <option value="<?= $b['id'] ?>" <?= ($brandId == $b['id']) ? 'selected' : '' ?>>
                            <?= e($b['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-secondary">
                <i class="fa-solid fa-filter"></i> Lọc
            </button>
            <?php if (!empty($keyword) || !empty($categoryId) || !empty($brandId)): ?>
                <a href="<?= BASE_URL ?>/admin/products" class="btn btn-secondary" title="Xóa bộ lọc">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            <?php endif; ?>
        </form>
    </div>

    <div class="admin-table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th style="width: 60px;">Ảnh</th>
                    <th>Tên Sản Phẩm</th>
                    <th>Mã SKU</th>
                    <th>Danh Mục</th>
                    <th>Thương Hiệu</th>
                    <th>Giá Bán</th>
                    <th>Giảm Giá</th>
                    <th>Tồn Kho</th>
                    <th>Trạng Thái</th>
                    <th style="width: 110px; text-align: center;">Thao Tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($products)): ?>
                    <?php foreach ($products as $p): ?>
                        <tr>
                            <td>
                                <img src="<?= get_product_thumb_url($p['thumbnail']) ?>" 
                                     alt="<?= e($p['name']) ?>" 
                                     class="admin-thumb"
                                     style="width: 52px; height: 52px; object-fit: cover; border-radius: 8px; border: 1px solid #E2E8F0; display: block;"
                                     onerror="this.src='<?= get_product_fallback_url($p['thumbnail']) ?>'">
                            </td>
                            <td>
                                <div style="font-weight: 600; color: var(--admin-text-main); max-width: 260px;">
                                    <?= e($p['name']) ?>
                                </div>
                                <?php if ($p['is_featured']): ?>
                                    <span class="badge badge-warning" style="font-size: 10px; margin-top: 4px;">Nổi bật</span>
                                <?php endif; ?>
                                <?php if ($p['is_new']): ?>
                                    <span class="badge badge-info" style="font-size: 10px; margin-top: 4px;">Mới</span>
                                <?php endif; ?>
                            </td>
                            <td><code style="font-size: 12px;"><?= e($p['sku']) ?></code></td>
                            <td><?= e($p['category_name'] ?? 'Chưa phân loại') ?></td>
                            <td><?= e($p['brand_name'] ?? 'Khác') ?></td>
                            <td><strong><?= format_currency($p['price']) ?></strong></td>
                            <td>
                                <?php if ($p['discount_percent'] > 0): ?>
                                    <span class="badge badge-danger">-<?= $p['discount_percent'] ?>%</span>
                                <?php else: ?>
                                    <span style="color: var(--admin-text-muted);">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($p['stock_quantity'] > 5): ?>
                                    <span class="badge badge-success"><?= $p['stock_quantity'] ?></span>
                                <?php elseif ($p['stock_quantity'] > 0): ?>
                                    <span class="badge badge-warning"><?= $p['stock_quantity'] ?> (Sắp hết)</span>
                                <?php else: ?>
                                    <span class="badge badge-danger">Hết hàng</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($p['status'] == 1): ?>
                                    <span class="badge badge-success">Đang bán</span>
                                <?php else: ?>
                                    <span class="badge badge-secondary">Tạm ẩn</span>
                                <?php endif; ?>
                            </td>
                            <td style="text-align: center;">
                                <div style="display: flex; gap: 6px; justify-content: center;">
                                    <a href="<?= BASE_URL ?>/admin/products/edit?id=<?= $p['id'] ?>" class="btn btn-secondary btn-sm" title="Chỉnh sửa">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                    <form method="POST" action="<?= BASE_URL ?>/admin/products/delete" onsubmit="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này?');" style="display: inline;">
                                        <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                        <button type="submit" class="btn btn-danger btn-sm" title="Xóa">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="10" style="text-align: center; color: var(--admin-text-muted); padding: 40px;">
                            Không tìm thấy sản phẩm nào phù hợp với điều kiện tìm kiếm.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
