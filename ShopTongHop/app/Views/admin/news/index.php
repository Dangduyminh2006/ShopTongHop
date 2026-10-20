<div class="admin-card">
    <div class="admin-card-header">
        <div class="admin-card-title">
            <i class="fa-solid fa-newspaper" style="color: var(--admin-primary);"></i>
            Quản Lý Tin Tức & Cẩm Nang Tiêu Dùng (<?= count($newsList) ?>)
        </div>
        <a href="<?= BASE_URL ?>/admin/news/create" class="btn btn-primary">
            <i class="fa-solid fa-plus-circle"></i> Viết Bài Mới
        </a>
    </div>

    <div class="admin-table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th style="width: 70px;">Ảnh</th>
                    <th>Tiêu Đề Bài Viết</th>
                    <th>Đường Dẫn Slug</th>
                    <th>Tác Giả</th>
                    <th>Lượt Xem</th>
                    <th>Ngày Đăng</th>
                    <th style="width: 100px; text-align: center;">Thao Tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($newsList)): ?>
                    <?php foreach ($newsList as $n): ?>
                        <tr>
                            <td>
                                <img src="<?= get_news_thumb_url($n['thumbnail']) ?>" 
                                     alt="<?= e($n['title']) ?>" 
                                     class="admin-thumb"
                                     onerror="this.src='<?= get_news_fallback_url($n['thumbnail']) ?>'">
                            </td>
                            <td>
                                <strong style="color: var(--admin-text-main); font-size: 14px;">
                                    <?= e($n['title']) ?>
                                </strong>
                                <?php if (!empty($n['summary'])): ?>
                                    <div style="font-size: 12px; color: var(--admin-text-muted); max-width: 380px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                        <?= e($n['summary']) ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td><code style="font-size: 11.5px;"><?= e($n['slug']) ?></code></td>
                            <td><?= e($n['author_name'] ?? 'Admin') ?></td>
                            <td style="text-align: center;">
                                <span class="badge badge-info"><?= $n['view_count'] ?></span>
                            </td>
                            <td style="font-size: 12px; color: var(--admin-text-muted);">
                                <?= date('d/m/Y H:i', strtotime($n['created_at'])) ?>
                            </td>
                            <td style="text-align: center;">
                                <div style="display: flex; gap: 6px; justify-content: center;">
                                    <a href="<?= BASE_URL ?>/admin/news/edit?id=<?= $n['id'] ?>" class="btn btn-secondary btn-sm" title="Sửa">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                    <form method="POST" action="<?= BASE_URL ?>/admin/news/delete" onsubmit="return confirm('Bạn có chắc chắn muốn xóa bài viết này?');" style="display: inline;">
                                        <input type="hidden" name="id" value="<?= $n['id'] ?>">
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
                        <td colspan="7" style="text-align: center; color: var(--admin-text-muted); padding: 40px;">
                            Chưa có bài viết nào trong hệ thống.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
