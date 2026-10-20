/**
 * ShopXpress - Admin Portal Scripts
 * Tiện ích hỗ trợ quản trị viên: Auto dismiss alert, Preview image, Confirm dialogs
 */

document.addEventListener('DOMContentLoaded', () => {
    // 1. Tự động ẩn thông báo Flash Alert sau 4 giây
    const alerts = document.querySelectorAll('.admin-alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-10px)';
            setTimeout(() => alert.remove(), 500);
        }, 4000);
    });

    // 2. Tự động gắn xác nhận xác thực cho các nút xóa nguy hiểm
    const deleteButtons = document.querySelectorAll('a[onclick*="confirm"], button[onclick*="confirm"]');
    deleteButtons.forEach(btn => {
        // Đã có inline confirm, hỗ trợ thêm hiệu ứng nhấn
        btn.addEventListener('mouseenter', () => {
            btn.style.filter = 'brightness(1.1)';
        });
        btn.addEventListener('mouseleave', () => {
            btn.style.filter = 'none';
        });
    });

    // 3. Xem trước ảnh khi chọn tệp upload
    const imageInputs = document.querySelectorAll('input[type="file"][accept*="image"]');
    imageInputs.forEach(input => {
        input.addEventListener('change', function(e) {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(evt) {
                    let preview = input.parentElement.querySelector('.image-preview-thumb');
                    if (!preview) {
                        preview = document.createElement('img');
                        preview.className = 'image-preview-thumb';
                        preview.style = 'max-width: 120px; max-height: 120px; object-fit: cover; border-radius: 6px; margin-top: 8px; border: 1px solid #cbd5e1; display: block;';
                        input.parentElement.appendChild(preview);
                    }
                    preview.src = evt.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    });
});
