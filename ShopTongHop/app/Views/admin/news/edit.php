<div class="admin-card">
    <div class="admin-card-header">
        <div class="admin-card-title">
            <i class="fa-solid fa-pen-to-square" style="color: var(--admin-primary);"></i>
            Chỉnh Sửa Bài Viết Tin Tức
        </div>
        <a href="<?= BASE_URL ?>/admin/news" class="btn btn-secondary btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Quay Lại
        </a>
    </div>

    <div class="admin-card-body">
        <form method="POST" action="<?= BASE_URL ?>/admin/news/edit?id=<?= $news['id'] ?>">
            
            <div class="form-group">
                <label class="form-label">Tiêu Đề Bài Viết <span style="color:red">*</span></label>
                <input type="text" name="title" class="form-control" value="<?= e($news['title']) ?>" required>
            </div>

            <div class="form-group">
                <label class="form-label">Ảnh Đại Diện Bài Viết</label>
                <input type="text" name="thumbnail" class="form-control" value="<?= e($news['thumbnail']) ?>" required>
            </div>

            <div class="form-group">
                <label class="form-label">Tóm Tắt Ngắn</label>
                <textarea name="summary" class="form-control" style="min-height: 70px;"><?= e($news['summary'] ?? '') ?></textarea>
            </div>

            <div class="form-group">
                <label class="form-label">Nội Dung Chi Tiết</label>
                <textarea name="content" class="form-control" style="min-height: 250px;" required><?= e($news['content']) ?></textarea>
            </div>

            <div style="display: flex; gap: 12px;">
                <button type="submit" class="btn btn-primary" style="padding: 10px 24px;">
                    <i class="fa-solid fa-floppy-disk"></i> Lưu Thay Đổi
                </button>
                <a href="<?= BASE_URL ?>/admin/news" class="btn btn-secondary">Hủy Bỏ</a>
            </div>

        </form>
    </div>
</div>
