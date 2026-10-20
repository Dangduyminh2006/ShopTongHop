<div class="admin-card">
    <div class="admin-card-header">
        <div class="admin-card-title">
            <i class="fa-solid fa-pen-to-square" style="color: var(--admin-primary);"></i>
            Chỉnh Sửa Sản Phẩm: <?= e($product['name']) ?>
        </div>
        <a href="<?= BASE_URL ?>/admin/products" class="btn btn-secondary btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Quay Lại
        </a>
    </div>

    <div class="admin-card-body">
        <form method="POST" action="<?= BASE_URL ?>/admin/products/edit?id=<?= $product['id'] ?>">
            
            <div class="form-row">
                <div class="form-group" style="grid-column: span 2;">
                    <label class="form-label">Tên Sản Phẩm <span style="color:red">*</span></label>
                    <input type="text" name="name" class="form-control" value="<?= e($product['name']) ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Mã Quản Lý (SKU) <span style="color:red">*</span></label>
                    <input type="text" name="sku" class="form-control" value="<?= e($product['sku']) ?>" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Danh Mục Ngành Hàng <span style="color:red">*</span></label>
                    <select name="category_id" class="form-select" required>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= ($product['category_id'] == $cat['id']) ? 'selected' : '' ?>>
                                <?= e($cat['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Thương Hiệu</label>
                    <select name="brand_id" class="form-select">
                        <option value="">-- Chọn thương hiệu --</option>
                        <?php foreach ($brands as $b): ?>
                            <option value="<?= $b['id'] ?>" <?= ($product['brand_id'] == $b['id']) ? 'selected' : '' ?>>
                                <?= e($b['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Giá Bán Niêm Yết (VNĐ) <span style="color:red">*</span></label>
                    <input type="number" name="price" class="form-control" value="<?= (int)$product['price'] ?>" min="0" step="1000" required>
                </div>

                <div class="form-group">
                    <label class="form-label">% Giảm Giá</label>
                    <input type="number" name="discount_percent" class="form-control" value="<?= (int)$product['discount_percent'] ?>" min="0" max="100">
                </div>

                <div class="form-group">
                    <label class="form-label">Số Lượng Tồn Kho</label>
                    <input type="number" name="stock_quantity" class="form-control" value="<?= (int)$product['stock_quantity'] ?>" min="0">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Ảnh đại diện (Tên file hoặc link ảnh)</label>
                <input type="text" name="thumbnail" class="form-control" value="<?= e($product['thumbnail']) ?>" required>
            </div>

            <div class="form-group">
                <label class="form-label">Tóm Tắt Ngắn Sản Phẩm</label>
                <textarea name="summary" class="form-control" style="min-height: 60px;"><?= e($product['summary'] ?? '') ?></textarea>
            </div>

            <div class="form-group">
                <label class="form-label">Mô Tả Chi Tiết</label>
                <textarea name="description" class="form-control" style="min-height: 120px;"><?= e($product['description'] ?? '') ?></textarea>
            </div>

            <!-- Khung Thông Số Kỹ Thuật Động (JSON Specs) -->
            <div style="background: #f8fafc; border: 1px solid var(--admin-border); border-radius: var(--admin-radius); padding: 18px; margin-bottom: 20px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                    <div>
                        <strong style="color: var(--admin-text-main); font-size: 14px;">
                            <i class="fa-solid fa-microchip" style="color: var(--admin-primary);"></i>
                            Thông Số Kỹ Thuật Động (Dynamic Specifications)
                        </strong>
                        <div style="font-size: 12px; color: var(--admin-text-muted);">Đã giải mã từ trường specifications JSON</div>
                    </div>
                    <button type="button" class="btn btn-secondary btn-sm" onclick="addSpecRow()">
                        <i class="fa-solid fa-plus"></i> Thêm Dòng
                    </button>
                </div>

                <div id="specsContainer">
                    <?php if (!empty($specs)): ?>
                        <?php foreach ($specs as $k => $v): ?>
                            <div class="spec-row" style="display: flex; gap: 10px; margin-bottom: 8px;">
                                <input type="text" name="spec_key[]" class="form-control" value="<?= e($k) ?>" style="flex: 1;">
                                <input type="text" name="spec_val[]" class="form-control" value="<?= e($v) ?>" style="flex: 2;">
                                <button type="button" class="btn btn-danger btn-sm" onclick="this.parentElement.remove()" style="padding: 0 12px;">×</button>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="spec-row" style="display: flex; gap: 10px; margin-bottom: 8px;">
                            <input type="text" name="spec_key[]" class="form-control" placeholder="Tên thông số" style="flex: 1;">
                            <input type="text" name="spec_val[]" class="form-control" placeholder="Giá trị thông số" style="flex: 2;">
                            <button type="button" class="btn btn-danger btn-sm" onclick="this.parentElement.remove()" style="padding: 0 12px;">×</button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div style="display: flex; gap: 24px; margin-bottom: 24px; padding: 12px 16px; background: #ffffff; border: 1px solid var(--admin-border); border-radius: 8px;">
                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-size: 13.5px;">
                    <input type="checkbox" name="is_featured" value="1" <?= $product['is_featured'] ? 'checked' : '' ?>>
                    <strong>Sản phẩm Nổi bật</strong>
                </label>
                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-size: 13.5px;">
                    <input type="checkbox" name="is_new" value="1" <?= $product['is_new'] ? 'checked' : '' ?>>
                    <strong>Hàng mới về</strong>
                </label>
                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-size: 13.5px;">
                    <input type="checkbox" name="status" value="1" <?= $product['status'] ? 'checked' : '' ?>>
                    <strong>Đang bán</strong>
                </label>
            </div>

            <div style="display: flex; gap: 12px;">
                <button type="submit" class="btn btn-primary" style="padding: 10px 24px;">
                    <i class="fa-solid fa-floppy-disk"></i> Lưu Thay Đổi
                </button>
                <a href="<?= BASE_URL ?>/admin/products" class="btn btn-secondary">Hủy Bỏ</a>
            </div>

        </form>
    </div>
</div>

<script>
function addSpecRow() {
    const container = document.getElementById('specsContainer');
    const row = document.createElement('div');
    row.className = 'spec-row';
    row.style = 'display: flex; gap: 10px; margin-bottom: 8px;';
    row.innerHTML = `
        <input type="text" name="spec_key[]" class="form-control" placeholder="Tên thông số" style="flex: 1;">
        <input type="text" name="spec_val[]" class="form-control" placeholder="Giá trị thông số" style="flex: 2;">
        <button type="button" class="btn btn-danger btn-sm" onclick="this.parentElement.remove()" style="padding: 0 12px;">×</button>
    `;
    container.appendChild(row);
}
</script>
