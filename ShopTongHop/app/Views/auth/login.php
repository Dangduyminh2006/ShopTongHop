<?php
/**
 * ShopXpress - Trang Đăng Nhập Tài Khoản
 */
?>

<div class="container" style="padding: 60px 20px; min-height: 75vh; display: flex; align-items: center; justify-content: center;">
    <div class="auth-card" style="width: 100%; max-width: 480px; background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 40px 36px; box-shadow: var(--shadow-lg); position: relative; overflow: hidden;">
        
        <!-- Hiệu ứng trang trí góc phát sáng -->
        <div style="position: absolute; top: -60px; right: -60px; width: 140px; height: 140px; background: radial-gradient(circle, var(--primary-glow) 0%, transparent 70%); border-radius: 50%; pointer-events: none;"></div>

        <!-- Tiêu đề & Logo -->
        <div style="text-align: center; margin-bottom: 32px;">
            <div style="display: inline-flex; align-items: center; justify-content: center; width: 64px; height: 64px; border-radius: 16px; background: linear-gradient(135deg, var(--primary) 0%, #ff8c5a 100%); color: #fff; font-size: 28px; margin-bottom: 16px; box-shadow: 0 8px 24px var(--primary-glow);">
                <i class="fa-solid fa-user-lock"></i>
            </div>
            <h1 style="font-size: 26px; font-weight: 800; font-family: 'Outfit', sans-serif; color: var(--text-primary); margin-bottom: 8px;">
                Đăng Nhập Tài Khoản
            </h1>
            <p style="font-size: 14px; color: var(--text-muted); line-height: 1.5;">
                Chào mừng bạn quay lại với <strong><?= APP_NAME ?></strong>
            </p>
        </div>

        <!-- Form Đăng nhập -->
        <form action="<?= BASE_URL ?>/login" method="POST" id="loginForm">
            <?php if (!empty($redirect)): ?>
                <input type="hidden" name="redirect" value="<?= e($redirect) ?>">
            <?php endif; ?>

            <!-- Tên đăng nhập hoặc Email -->
            <div style="margin-bottom: 20px;">
                <label for="usernameInput" style="display: block; font-size: 13.5px; font-weight: 600; color: var(--text-secondary); margin-bottom: 8px;">
                    <i class="fa-solid fa-user" style="color: var(--primary); margin-right: 6px;"></i> Tên đăng nhập hoặc Email
                </label>
                <div style="position: relative;">
                    <input type="text" 
                           id="usernameInput" 
                           name="username" 
                           value="<?= e($username ?? '') ?>" 
                           placeholder="Nhập username hoặc email của bạn..." 
                           required 
                           autofocus
                           style="width: 100%; padding: 13px 16px 13px 44px; background: var(--bg-surface-elevated); border: 1px solid var(--border-color); border-radius: 10px; color: var(--text-primary); font-size: 14.5px; font-family: inherit; transition: all 0.25s ease; outline: none;">
                    <i class="fa-solid fa-envelope" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 15px; pointer-events: none;"></i>
                </div>
            </div>

            <!-- Mật khẩu -->
            <div style="margin-bottom: 22px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                    <label for="passwordInput" style="font-size: 13.5px; font-weight: 600; color: var(--text-secondary);">
                        <i class="fa-solid fa-lock" style="color: var(--primary); margin-right: 6px;"></i> Mật khẩu
                    </label>
                    <a href="javascript:void(0)" onclick="alert('Vui lòng liên hệ Hotline 1900 8899 để được cấp lại mật khẩu nhanh chóng.')" style="font-size: 12.5px; color: var(--primary); text-decoration: none;">
                        Quên mật khẩu?
                    </a>
                </div>
                <div style="position: relative;">
                    <input type="password" 
                           id="passwordInput" 
                           name="password" 
                           placeholder="Nhập mật khẩu..." 
                           required 
                           style="width: 100%; padding: 13px 44px 13px 44px; background: var(--bg-surface-elevated); border: 1px solid var(--border-color); border-radius: 10px; color: var(--text-primary); font-size: 14.5px; font-family: inherit; transition: all 0.25s ease; outline: none;">
                    <i class="fa-solid fa-key" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 15px; pointer-events: none;"></i>
                    <button type="button" 
                            id="togglePasswordBtn" 
                            onclick="togglePasswordVisibility()" 
                            style="position: absolute; right: 14px; top: 50%; transform: translateY(-50%); background: transparent; border: none; color: var(--text-muted); font-size: 16px; cursor: pointer; padding: 4px;">
                        <i class="fa-regular fa-eye" id="eyeIcon"></i>
                    </button>
                </div>
            </div>

            <!-- Ghi nhớ đăng nhập -->
            <div style="display: flex; align-items: center; margin-bottom: 24px;">
                <label style="display: flex; align-items: center; gap: 8px; font-size: 13.5px; color: var(--text-secondary); cursor: pointer; user-select: none;">
                    <input type="checkbox" name="remember" value="1" checked style="width: 16px; height: 16px; accent-color: var(--primary); cursor: pointer;">
                    <span>Ghi nhớ trạng thái đăng nhập</span>
                </label>
            </div>

            <!-- Nút Đăng nhập -->
            <button type="submit" 
                    class="btn btn-primary" 
                    style="width: 100%; padding: 14px; font-size: 15.5px; font-weight: 700; border-radius: 10px; display: flex; align-items: center; justify-content: center; gap: 10px; box-shadow: 0 4px 16px var(--primary-glow); cursor: pointer;">
                <i class="fa-solid fa-right-to-bracket"></i> Đăng Nhập
            </button>
        </form>

        <!-- Hộp Tài khoản mẫu thử nghiệm (Quick Fill Helper) -->
        <div style="margin-top: 28px; padding: 18px; background: var(--bg-surface-elevated); border: 1px dashed var(--border-color); border-radius: 12px;">
            <div style="font-size: 12.5px; font-weight: 700; text-transform: uppercase; color: var(--primary); letter-spacing: 0.5px; margin-bottom: 10px; display: flex; align-items: center; gap: 6px;">
                <i class="fa-solid fa-wand-magic-sparkles"></i> Tài khoản mẫu thử nghiệm (Bấm để điền):
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                <button type="button" 
                        onclick="quickFillLogin('admin', '123456')" 
                        style="padding: 8px 10px; background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; color: var(--text-primary); font-size: 12.5px; text-align: left; cursor: pointer; transition: all 0.2s ease;">
                    <div style="font-weight: 700; color: #ff0055;"><i class="fa-solid fa-crown"></i> Quản Trị Viên</div>
                    <div style="font-size: 11.5px; color: var(--text-muted);">admin / 123456</div>
                </button>
                <button type="button" 
                        onclick="quickFillLogin('testuser', '123456')" 
                        style="padding: 8px 10px; background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; color: var(--text-primary); font-size: 12.5px; text-align: left; cursor: pointer; transition: all 0.2s ease;">
                    <div style="font-weight: 700; color: var(--primary);"><i class="fa-solid fa-user-check"></i> Khách Hàng</div>
                    <div style="font-size: 11.5px; color: var(--text-muted);">testuser / 123456</div>
                </button>
            </div>
        </div>

        <!-- Chuyển hướng Đăng ký -->
        <div style="text-align: center; margin-top: 24px; font-size: 14px; color: var(--text-secondary);">
            Chưa có tài khoản? 
            <a href="<?= BASE_URL ?>/register" style="color: var(--primary); font-weight: 700; text-decoration: none;">
                Đăng ký ngay <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>
    </div>
</div>

<script>
function togglePasswordVisibility() {
    const input = document.getElementById('passwordInput');
    const icon = document.getElementById('eyeIcon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

function quickFillLogin(username, password) {
    document.getElementById('usernameInput').value = username;
    document.getElementById('passwordInput').value = password;
    if (window.showToast) {
        window.showToast('info', `Đã điền tài khoản mẫu: ${username}`);
    }
}
</script>
