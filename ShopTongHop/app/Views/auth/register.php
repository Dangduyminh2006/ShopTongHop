<?php
/**
 * ShopXpress - Trang Đăng Ký Tài Khoản Mới
 */
$form = $formData ?? [];
?>

<div class="container" style="padding: 60px 20px; min-height: 80vh; display: flex; align-items: center; justify-content: center;">
    <div class="auth-card" style="width: 100%; max-width: 540px; background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 40px 36px; box-shadow: var(--shadow-lg); position: relative; overflow: hidden;">
        
        <!-- Hiệu ứng trang trí góc phát sáng -->
        <div style="position: absolute; top: -60px; right: -60px; width: 140px; height: 140px; background: radial-gradient(circle, var(--primary-glow) 0%, transparent 70%); border-radius: 50%; pointer-events: none;"></div>

        <!-- Tiêu đề & Logo -->
        <div style="text-align: center; margin-bottom: 28px;">
            <div style="display: inline-flex; align-items: center; justify-content: center; width: 64px; height: 64px; border-radius: 16px; background: linear-gradient(135deg, var(--primary) 0%, #ff8c5a 100%); color: #fff; font-size: 28px; margin-bottom: 16px; box-shadow: 0 8px 24px var(--primary-glow);">
                <i class="fa-solid fa-user-plus"></i>
            </div>
            <h1 style="font-size: 26px; font-weight: 800; font-family: 'Outfit', sans-serif; color: var(--text-primary); margin-bottom: 8px;">
                Đăng Ký Tài Khoản
            </h1>
            <p style="font-size: 14px; color: var(--text-muted); line-height: 1.5;">
                Tạo tài khoản để nhận ngập tràn voucher giảm giá và theo dõi đơn hàng
            </p>
        </div>

        <!-- Form Đăng ký -->
        <form action="<?= BASE_URL ?>/register" method="POST" id="registerForm">
            <!-- Họ và tên -->
            <div style="margin-bottom: 16px;">
                <label for="regFullname" style="display: block; font-size: 13.5px; font-weight: 600; color: var(--text-secondary); margin-bottom: 6px;">
                    <i class="fa-solid fa-id-badge" style="color: var(--primary); margin-right: 6px;"></i> Họ và tên <span style="color: #ff0055;">*</span>
                </label>
                <input type="text" 
                       id="regFullname" 
                       name="fullname" 
                       value="<?= e($form['fullname'] ?? '') ?>" 
                       placeholder="Ví dụ: Nguyễn Văn An" 
                       required 
                       autofocus
                       style="width: 100%; padding: 12px 16px; background: var(--bg-surface-elevated); border: 1px solid var(--border-color); border-radius: 10px; color: var(--text-primary); font-size: 14.5px; font-family: inherit; outline: none;">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 16px;">
                <!-- Tên đăng nhập -->
                <div>
                    <label for="regUsername" style="display: block; font-size: 13.5px; font-weight: 600; color: var(--text-secondary); margin-bottom: 6px;">
                        <i class="fa-solid fa-user" style="color: var(--primary); margin-right: 6px;"></i> Tên đăng nhập <span style="color: #ff0055;">*</span>
                    </label>
                    <input type="text" 
                           id="regUsername" 
                           name="username" 
                           value="<?= e($form['username'] ?? '') ?>" 
                           placeholder="username viết liền" 
                           required 
                           style="width: 100%; padding: 12px 16px; background: var(--bg-surface-elevated); border: 1px solid var(--border-color); border-radius: 10px; color: var(--text-primary); font-size: 14px; font-family: inherit; outline: none;">
                </div>

                <!-- Số điện thoại -->
                <div>
                    <label for="regPhone" style="display: block; font-size: 13.5px; font-weight: 600; color: var(--text-secondary); margin-bottom: 6px;">
                        <i class="fa-solid fa-phone" style="color: var(--primary); margin-right: 6px;"></i> Số điện thoại
                    </label>
                    <input type="tel" 
                           id="regPhone" 
                           name="phone" 
                           value="<?= e($form['phone'] ?? '') ?>" 
                           placeholder="0912345678" 
                           style="width: 100%; padding: 12px 16px; background: var(--bg-surface-elevated); border: 1px solid var(--border-color); border-radius: 10px; color: var(--text-primary); font-size: 14px; font-family: inherit; outline: none;">
                </div>
            </div>

            <!-- Email -->
            <div style="margin-bottom: 16px;">
                <label for="regEmail" style="display: block; font-size: 13.5px; font-weight: 600; color: var(--text-secondary); margin-bottom: 6px;">
                    <i class="fa-solid fa-envelope" style="color: var(--primary); margin-right: 6px;"></i> Địa chỉ Email <span style="color: #ff0055;">*</span>
                </label>
                <input type="email" 
                       id="regEmail" 
                       name="email" 
                       value="<?= e($form['email'] ?? '') ?>" 
                       placeholder="email@example.com" 
                       required 
                       style="width: 100%; padding: 12px 16px; background: var(--bg-surface-elevated); border: 1px solid var(--border-color); border-radius: 10px; color: var(--text-primary); font-size: 14.5px; font-family: inherit; outline: none;">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 16px;">
                <!-- Mật khẩu -->
                <div>
                    <label for="regPassword" style="display: block; font-size: 13.5px; font-weight: 600; color: var(--text-secondary); margin-bottom: 6px;">
                        <i class="fa-solid fa-lock" style="color: var(--primary); margin-right: 6px;"></i> Mật khẩu <span style="color: #ff0055;">*</span>
                    </label>
                    <input type="password" 
                           id="regPassword" 
                           name="password" 
                           placeholder="Tối thiểu 6 ký tự..." 
                           required 
                           style="width: 100%; padding: 12px 16px; background: var(--bg-surface-elevated); border: 1px solid var(--border-color); border-radius: 10px; color: var(--text-primary); font-size: 14px; font-family: inherit; outline: none;">
                </div>

                <!-- Xác nhận Mật khẩu -->
                <div>
                    <label for="regConfirmPassword" style="display: block; font-size: 13.5px; font-weight: 600; color: var(--text-secondary); margin-bottom: 6px;">
                        <i class="fa-solid fa-shield-halved" style="color: var(--primary); margin-right: 6px;"></i> Xác nhận mật khẩu <span style="color: #ff0055;">*</span>
                    </label>
                    <input type="password" 
                           id="regConfirmPassword" 
                           name="confirm_password" 
                           placeholder="Nhập lại mật khẩu..." 
                           required 
                           style="width: 100%; padding: 12px 16px; background: var(--bg-surface-elevated); border: 1px solid var(--border-color); border-radius: 10px; color: var(--text-primary); font-size: 14px; font-family: inherit; outline: none;">
                </div>
            </div>

            <!-- Địa chỉ nhận hàng -->
            <div style="margin-bottom: 22px;">
                <label for="regAddress" style="display: block; font-size: 13.5px; font-weight: 600; color: var(--text-secondary); margin-bottom: 6px;">
                    <i class="fa-solid fa-location-dot" style="color: var(--primary); margin-right: 6px;"></i> Địa chỉ nhận hàng mặc định (Tùy chọn)
                </label>
                <input type="text" 
                       id="regAddress" 
                       name="address" 
                       value="<?= e($form['address'] ?? '') ?>" 
                       placeholder="Số nhà, tên đường, phường/xã, quận/huyện..." 
                       style="width: 100%; padding: 12px 16px; background: var(--bg-surface-elevated); border: 1px solid var(--border-color); border-radius: 10px; color: var(--text-primary); font-size: 14px; font-family: inherit; outline: none;">
            </div>

            <!-- Điều khoản dịch vụ -->
            <div style="font-size: 13px; color: var(--text-muted); line-height: 1.5; margin-bottom: 22px;">
                Bằng việc bấm đăng ký, bạn đã đồng ý với 
                <a href="javascript:void(0)" style="color: var(--primary); text-decoration: underline;">Điều khoản sử dụng</a> và 
                <a href="javascript:void(0)" style="color: var(--primary); text-decoration: underline;">Chính sách bảo mật</a> của ShopXpress.
            </div>

            <!-- Nút Đăng ký -->
            <button type="submit" 
                    class="btn btn-primary" 
                    style="width: 100%; padding: 14px; font-size: 15.5px; font-weight: 700; border-radius: 10px; display: flex; align-items: center; justify-content: center; gap: 10px; box-shadow: 0 4px 16px var(--primary-glow); cursor: pointer;">
                <i class="fa-solid fa-user-plus"></i> Hoàn Tất Đăng Ký
            </button>
        </form>

        <!-- Chuyển hướng Đăng nhập -->
        <div style="text-align: center; margin-top: 24px; font-size: 14px; color: var(--text-secondary);">
            Đã có tài khoản? 
            <a href="<?= BASE_URL ?>/login" style="color: var(--primary); font-weight: 700; text-decoration: none;">
                Đăng nhập ngay <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>
    </div>
</div>
