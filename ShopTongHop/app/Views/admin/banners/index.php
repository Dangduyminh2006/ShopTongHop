<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
    
    <!-- Cột Trái: Bảng Danh Sách Banner -->
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-title">
                <i class="fa-solid fa-images" style="color: var(--admin-primary);"></i>
                Banner Trình Chiếu Slider Trang Chủ (<?= count($banners) ?>)
            </div>
        </div>
        <div class="admin-table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width: 120px;">Ảnh Banner</th>
                        <th>Tiêu Đề</th>
                        <th>Phụ Đề</th>
                        <th>Thứ Tự</th>
                        <th>Trạng Thái</th>
                        <th style="width: 60px; text-align: center;">Xóa</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($banners)): ?>
                        <?php foreach ($banners as $b): ?>
                            <tr>
                                <td>
                                    <img src="<?= get_banner_image_url($b['image_url']) ?>" 
                                         alt="<?= e($b['title']) ?>" 
                                         style="width: 110px; height: 50px; object-fit: cover; border-radius: 6px; border: 1px solid var(--admin-border);"
                                         onerror="this.src='<?= get_banner_fallback_url($b['image_url']) ?>'">
                                </td>
                                <td>
                                    <strong><?= e($b['title']) ?></strong>
                                    <?php if (!empty($b['link_url'])): ?>
                                        <div style="font-size: 11px; color: var(--admin-primary);">
                                            <?= e($b['link_url']) ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td><?= e($b['subtitle'] ?? '-') ?></td>
                                <td style="text-align: center;"><?= $b['sort_order'] ?></td>
                                <td>
                                    <span class="badge badge-success">Hiển thị</span>
                                </td>
                                <td style="text-align: center;">
                                    <form method="POST" action="<?= BASE_URL ?>/admin/banners/delete" onsubmit="return confirm('Bạn có chắc chắn muốn xóa banner này?');" style="display: inline;">
                                        <input type="hidden" name="id" value="<?= $b['id'] ?>">
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center; color: var(--admin-text-muted); padding: 30px;">
                                Chưa có banner nào.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Cột Phải: Form Thêm Banner Mới -->
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-title">
                <i class="fa-solid fa-plus-circle" style="color: var(--admin-primary);"></i>
                Thêm Banner Mới
            </div>
        </div>
        <div class="admin-card-body">
            <form method="POST" action="<?= BASE_URL ?>/admin/banners/create">
                
                <div class="form-group">
                    <label class="form-label">Tiêu Đề Banner <span style="color:red">*</span></label>
                    <input type="text" name="title" class="form-control" placeholder="Ví dụ: Đại Tiệc Mua Sắm Siêu Sale..." required>
                </div>

                <div class="form-group">
                    <label class="form-label">Phụ Đề Banner</label>
                    <input type="text" name="subtitle" class="form-control" placeholder="Ví dụ: Giảm giá lên đến 50% toàn bộ phụ kiện...">
                </div>

                <div class="form-group">
                    <label class="form-label">Tên tệp ảnh hoặc URL ảnh Online <span style="color:red">*</span></label>
                    <input type="text" name="image_url" class="form-control" placeholder="Ví dụ: banner_mega_sale.jpg hoặc link https://..." required>
                </div>

                <div class="form-group">
                    <label class="form-label">Liên Kết Đích (Link URL)</label>
                    <input type="text" name="link_url" class="form-control" placeholder="Ví dụ: /products hoặc link ngoài">
                </div>

                <div class="form-group">
                    <label class="form-label">Thứ Tự Sắp Xếp</label>
                    <input type="number" name="sort_order" class="form-control" value="0">
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 10px;">
                    <i class="fa-solid fa-plus"></i> Thêm Banner
                </button>
            </form>
        </div>
    </div>

</div>
