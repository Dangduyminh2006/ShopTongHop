<div class="admin-card">
    <div class="admin-card-header">
        <div class="admin-card-title">
            <i class="fa-solid fa-pen-nib" style="color: var(--admin-primary);"></i>
            Soạn Thảo Bài Viết Tin Tức Mới
        </div>
        <a href="<?= BASE_URL ?>/admin/news" class="btn btn-secondary btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Quay Lại
        </a>
    </div>

    <div class="admin-card-body">
        <form method="POST" action="<?= BASE_URL ?>/admin/news/create">
            
            <div class="form-group">
                <label class="form-label">Tiêu Đề Bài Viết <span style="color:red">*</span></label>
                <input type="text" name="title" class="form-control" placeholder="Ví dụ: Top 5 điện thoại thông minh đáng mua nhất năm 2026..." required>
            </div>

            <div class="form-group">
                <label class="form-label">Ảnh Đại Diện Bài Viết (Tên file hoặc Link ảnh) <span style="color:red">*</span></label>
                <input type="text" name="thumbnail" class="form-control" placeholder="Ví dụ: news_samsung.jpg hoặc link https://..." required>
            </div>

            <div class="form-group">
                <label class="form-label">Tóm Tắt Ngắn (Mô tả hiển thị danh sách)</label>
                <textarea name="summary" class="form-control" style="min-height: 70px;" placeholder="Đoạn văn ngắn tóm tắt nội dung chính của bài viết..."></textarea>
            </div>

            <div class="form-group">
                <label class="form-label">Nội Dung Chi Tiết Bài Viết <span style="color:red">*</span></label>
                <textarea name="content" class="form-control" style="min-height: 250px;" placeholder="Nội dung bài viết đầy đủ..." required></textarea>
            </div>

            <div style="display: flex; gap: 12px;">
                <button type="submit" class="btn btn-primary" style="padding: 10px 24px;">
                    <i class="fa-solid fa-check"></i> Đăng Bài Viết
                </button>
                <a href="<?= BASE_URL ?>/admin/news" class="btn btn-secondary">Hủy Bỏ</a>
            </div>

        </form>
    </div>
</div>
