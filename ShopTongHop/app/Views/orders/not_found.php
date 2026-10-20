<div class="breadcrumb-container">
    <div class="container">
        <ul class="breadcrumb">
            <li><a href="<?= BASE_URL ?>/"><i class="fa-solid fa-house"></i> Trang Chủ</a></li>
            <li><i class="fa-solid fa-chevron-right sep"></i></li>
            <li><a href="<?= BASE_URL ?>/order/history">Lịch Sử Mua Hàng</a></li>
            <li><i class="fa-solid fa-chevron-right sep"></i></li>
            <li class="active">Không Tìm Thấy Đơn Hàng</li>
        </ul>
    </div>
</div>

<div class="container" style="padding: 60px 15px; text-align: center;">
    <div style="max-width: 500px; margin: 0 auto; background: var(--bg-surface); padding: 40px; border-radius: 12px; border: 1px solid var(--border-color);">
        <div style="font-size: 56px; color: var(--accent); margin-bottom: 20px;">
            <i class="fa-solid fa-magnifying-glass-arrow-right"></i>
        </div>
        <h2 style="font-size: 22px; margin-bottom: 12px; color: var(--text-primary);">Không Tìm Thấy Đơn Hàng</h2>
        <p style="color: var(--text-secondary); margin-bottom: 25px; font-size: 14px;">
            Mã đơn hàng hoặc ID <strong>#<?= e($query ?? '') ?></strong> không tồn tại trong hệ thống hoặc bạn không có quyền xem đơn hàng này.
        </p>
        <div style="display: flex; gap: 12px; justify-content: center;">
            <a href="<?= BASE_URL ?>/order/history" class="btn btn-outline">
                <i class="fa-solid fa-arrow-left"></i> Về Lịch Sử Đơn Hàng
            </a>
            <a href="<?= BASE_URL ?>/products" class="btn btn-primary">
                <i class="fa-solid fa-bag-shopping"></i> Khám Phá Sản Phẩm
            </a>
        </div>
    </div>
</div>
