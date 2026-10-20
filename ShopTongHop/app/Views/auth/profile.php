<?php
/**
 * ShopXpress - Trang Hồ Sơ Cá Nhân & Quản Lý Tài Khoản
 */
$counts = $orderCounts ?? [];
?>

<div class="container" style="padding: 40px 20px 80px;">
    <!-- Breadcrumb -->
    <div class="breadcrumb-wrapper">
        <ul class="breadcrumb-list">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>"><i class="fa-solid fa-house"></i> Trang chủ</a></li>
            <li class="breadcrumb-separator"><i class="fa-solid fa-chevron-right"></i></li>
            <li class="breadcrumb-item active">Hồ Sơ Cá Nhân</li>
        </ul>
    </div>

    <div style="display: grid; grid-template-columns: 320px 1fr; gap: 30px; align-items: start;">
        <!-- Cột Trái: Thẻ tóm tắt thông tin tài khoản -->
        <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 32px 24px; box-shadow: var(--shadow-sm); text-align: center;">
            <div style="width: 80px; height: 80px; border-radius: 50%; background: linear-gradient(135deg, var(--primary) 0%, #ff8c5a 100%); color: #fff; font-size: 32px; font-weight: 800; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 16px; box-shadow: 0 6px 20px var(--primary-glow);">
                <?= mb_substr(e($user['fullname'] ?? 'U'), 0, 1) ?>
            </div>
            
            <h2 style="font-size: 20px; font-weight: 700; color: var(--text-primary); margin-bottom: 4px;">
                <?= e($user['fullname']) ?>
            </h2>
            <div style="font-size: 13.5px; color: var(--text-muted); margin-bottom: 14px;">
                @<?= e($user['username']) ?>
            </div>

            <?php if (($user['role'] ?? '') === 'admin'): ?>
                <span style="display: inline-block; padding: 4px 14px; background: rgba(255, 0, 85, 0.15); border: 1px solid rgba(255, 0, 85, 0.4); color: #ff0055; font-size: 12px; font-weight: 700; border-radius: 20px; margin-bottom: 20px;">
                    <i class="fa-solid fa-crown"></i> Quản Trị Viên Hệ Thống
                </span>
            <?php else: ?>
                <span style="display: inline-block; padding: 4px 14px; background: rgba(0, 200, 83, 0.15); border: 1px solid rgba(0, 200, 83, 0.4); color: #00c853; font-size: 12px; font-weight: 700; border-radius: 20px; margin-bottom: 20px;">
                    <i class="fa-solid fa-circle-check"></i> Khách Hàng Thân Thiết
                </span>
            <?php endif; ?>

            <div style="border-top: 1px solid var(--border-color); padding-top: 20px; text-align: left; display: flex; flex-direction: column; gap: 12px; font-size: 13.5px;">
                <div>
                    <span style="color: var(--text-muted);"><i class="fa-solid fa-envelope" style="width: 20px;"></i> Email:</span>
                    <strong style="color: var(--text-primary); display: block; margin-top: 2px;"><?= e($user['email']) ?></strong>
                </div>
                <div>
                    <span style="color: var(--text-muted);"><i class="fa-solid fa-phone" style="width: 20px;"></i> Số điện thoại:</span>
                    <strong style="color: var(--text-primary); display: block; margin-top: 2px;"><?= e($user['phone'] ?: 'Chưa cập nhật') ?></strong>
                </div>
                <div>
                    <span style="color: var(--text-muted);"><i class="fa-solid fa-location-dot" style="width: 20px;"></i> Địa chỉ:</span>
                    <span style="color: var(--text-secondary); display: block; margin-top: 2px;"><?= e($user['address'] ?: 'Chưa cập nhật') ?></span>
                </div>
            </div>

            <div style="border-top: 1px solid var(--border-color); margin-top: 24px; padding-top: 20px; display: flex; flex-direction: column; gap: 10px;">
                <a href="<?= BASE_URL ?>/order/history" class="btn btn-outline" style="width: 100%; justify-content: center; font-size: 13.5px;">
                    <i class="fa-solid fa-receipt"></i> Lịch Sử Đơn Hàng (<?= $counts['all'] ?? 0 ?>)
                </a>
                <?php if (($user['role'] ?? '') === 'admin'): ?>
                    <a href="<?= BASE_URL ?>/admin" class="btn btn-primary" style="width: 100%; justify-content: center; font-size: 13.5px;">
                        <i class="fa-solid fa-gauge-high"></i> Vào Trang Quản Trị
                    </a>
                <?php endif; ?>
                <a href="<?= BASE_URL ?>/logout" class="btn" style="width: 100%; justify-content: center; font-size: 13.5px; background: rgba(255, 0, 85, 0.1); color: #ff0055; border: 1px solid rgba(255, 0, 85, 0.3);">
                    <i class="fa-solid fa-right-from-bracket"></i> Đăng Xuất
                </a>
            </div>
        </div>

        <!-- Cột Phải: Các tab Cập nhật thông tin & Đổi mật khẩu -->
        <div style="display: flex; flex-direction: column; gap: 30px;">
            <!-- Form 1: Cập nhật thông tin cá nhân -->
            <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 32px 30px; box-shadow: var(--shadow-sm);">
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 24px; border-bottom: 1px solid var(--border-color); padding-bottom: 16px;">
                    <div style="width: 40px; height: 40px; border-radius: 10px; background: var(--bg-surface-elevated); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 18px;">
                        <i class="fa-solid fa-user-pen"></i>
                    </div>
                    <div>
                        <h3 style="font-size: 18px; font-weight: 700; color: var(--text-primary); margin-bottom: 2px;">Cập Nhật Thông Tin Cá Nhân</h3>
                        <p style="font-size: 13px; color: var(--text-muted);">Quản lý họ tên, số điện thoại và địa chỉ giao hàng</p>
                    </div>
                </div>

                <form action="<?= BASE_URL ?>/profile" method="POST">
                    <input type="hidden" name="profile_action" value="update_info">

                    <div style="margin-bottom: 18px;">
                        <label style="display: block; font-size: 13.5px; font-weight: 600; color: var(--text-secondary); margin-bottom: 6px;">
                            Họ và tên
                        </label>
                        <input type="text" name="fullname" value="<?= e($user['fullname']) ?>" required class="form-control" style="width: 100%; padding: 12px 16px; background: var(--bg-surface-elevated); border: 1px solid var(--border-color); border-radius: 8px; color: var(--text-primary); font-size: 14px;">
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 18px;">
                        <div>
                            <label style="display: block; font-size: 13.5px; font-weight: 600; color: var(--text-secondary); margin-bottom: 6px;">
                                Tên đăng nhập (Cố định)
                            </label>
                            <input type="text" value="<?= e($user['username']) ?>" disabled class="form-control" style="width: 100%; padding: 12px 16px; background: var(--bg-surface-elevated); border: 1px solid var(--border-color); border-radius: 8px; color: var(--text-muted); font-size: 14px; opacity: 0.7; cursor: not-allowed;">
                        </div>

                        <div>
                            <label style="display: block; font-size: 13.5px; font-weight: 600; color: var(--text-secondary); margin-bottom: 6px;">
                                Địa chỉ Email (Cố định)
                            </label>
                            <input type="email" value="<?= e($user['email']) ?>" disabled class="form-control" style="width: 100%; padding: 12px 16px; background: var(--bg-surface-elevated); border: 1px solid var(--border-color); border-radius: 8px; color: var(--text-muted); font-size: 14px; opacity: 0.7; cursor: not-allowed;">
                        </div>
                    </div>

                    <div style="margin-bottom: 18px;">
                        <label style="display: block; font-size: 13.5px; font-weight: 600; color: var(--text-secondary); margin-bottom: 6px;">
                            Số điện thoại nhận hàng
                        </label>
                        <input type="tel" name="phone" value="<?= e($user['phone'] ?? '') ?>" placeholder="0912345678" class="form-control" style="width: 100%; padding: 12px 16px; background: var(--bg-surface-elevated); border: 1px solid var(--border-color); border-radius: 8px; color: var(--text-primary); font-size: 14px;">
                    </div>

                    <div style="margin-bottom: 24px;">
                        <label style="display: block; font-size: 13.5px; font-weight: 600; color: var(--text-secondary); margin-bottom: 6px;">
                            Địa chỉ giao hàng mặc định
                        </label>
                        <textarea name="address" rows="3" placeholder="Số nhà, đường, phường/xã, quận/huyện, tỉnh/thành..." class="form-control" style="width: 100%; padding: 12px 16px; background: var(--bg-surface-elevated); border: 1px solid var(--border-color); border-radius: 8px; color: var(--text-primary); font-size: 14px; resize: vertical;"><?= e($user['address'] ?? '') ?></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary" style="padding: 12px 24px; font-weight: 700; border-radius: 8px;">
                        <i class="fa-solid fa-floppy-disk"></i> Lưu Thay Đổi
                    </button>
                </form>
            </div>

            <!-- Form 2: Sổ Địa Chỉ Nhận Hàng (Bảng user_addresses) -->
            <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 32px 30px; box-shadow: var(--shadow-sm);">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; border-bottom: 1px solid var(--border-color); padding-bottom: 16px; flex-wrap: wrap; gap: 12px;">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div style="width: 40px; height: 40px; border-radius: 10px; background: var(--bg-surface-elevated); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 18px;">
                            <i class="fa-solid fa-address-book"></i>
                        </div>
                        <div>
                            <h3 style="font-size: 18px; font-weight: 700; color: var(--text-primary); margin-bottom: 2px;">Sổ Địa Chỉ Nhận Hàng (<?= count($addresses ?? []) ?>)</h3>
                            <p style="font-size: 13px; color: var(--text-muted);">Quản lý danh sách các địa chỉ giao hàng và địa chỉ mặc định của bạn</p>
                        </div>
                    </div>
                    <button type="button" onclick="document.getElementById('addAddressModal').style.display='block'" class="btn btn-outline btn-sm" style="font-size: 13px;">
                        <i class="fa-solid fa-plus"></i> Thêm Địa Chỉ Mới
                    </button>
                </div>

                <!-- Danh sách địa chỉ đã lưu -->
                <?php if (!empty($addresses)): ?>
                    <div style="display: flex; flex-direction: column; gap: 14px; margin-bottom: 20px;">
                        <?php foreach ($addresses as $addr): ?>
                            <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 16px; padding: 18px 20px; background: var(--bg-surface-elevated); border: 1px solid <?= $addr['is_default'] ? 'var(--primary)' : 'var(--border-color)' ?>; border-radius: 10px; flex-wrap: wrap;">
                                <div style="flex: 1; min-width: 260px;">
                                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 6px; flex-wrap: wrap;">
                                        <strong style="color: var(--text-primary); font-size: 15px;"><?= e($addr['recipient_name']) ?></strong>
                                        <span style="color: var(--text-muted); font-size: 13px;">(<?= e($addr['recipient_phone']) ?>)</span>
                                        <?php if ($addr['is_default']): ?>
                                            <span style="padding: 2px 8px; border-radius: 4px; background: rgba(255, 107, 0, 0.15); color: var(--primary); font-size: 11px; font-weight: 700; border: 1px solid rgba(255, 107, 0, 0.3);">
                                                <i class="fa-solid fa-check"></i> Mặc Định
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    <div style="font-size: 13.5px; color: var(--text-secondary); line-height: 1.5;">
                                        <i class="fa-solid fa-location-dot" style="color: var(--primary); margin-right: 6px;"></i>
                                        <?= e($addr['full_address']) ?>
                                    </div>
                                </div>

                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <?php if (!$addr['is_default']): ?>
                                        <form action="<?= BASE_URL ?>/profile" method="POST" style="display: inline;">
                                            <input type="hidden" name="profile_action" value="set_default_address">
                                            <input type="hidden" name="address_id" value="<?= $addr['id'] ?>">
                                            <button type="submit" class="btn btn-outline btn-sm" style="font-size: 12px; padding: 6px 12px;">
                                                Đặt Mặc Định
                                            </button>
                                        </form>

                                        <form action="<?= BASE_URL ?>/profile" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa địa chỉ này?');" style="display: inline;">
                                            <input type="hidden" name="profile_action" value="delete_address">
                                            <input type="hidden" name="address_id" value="<?= $addr['id'] ?>">
                                            <button type="submit" class="btn btn-sm" style="font-size: 12px; padding: 6px 10px; background: rgba(255,0,85,0.1); color: #ff0055; border: 1px solid rgba(255,0,85,0.3);">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p style="color: var(--text-muted); font-size: 14px; margin-bottom: 20px;">Bạn chưa lưu địa chỉ nào trong sổ địa chỉ.</p>
                <?php endif; ?>

                <!-- Form thêm địa chỉ mới ẩn/hiện -->
                <div id="addAddressModal" style="display: none; padding: 20px; background: rgba(255,255,255,0.02); border: 1px solid var(--border-color); border-radius: 10px;">
                    <h4 style="font-size: 15px; font-weight: 700; color: var(--text-primary); margin-bottom: 14px;">
                        <i class="fa-solid fa-plus-circle" style="color: var(--primary);"></i> Thêm Địa Chỉ Mới
                    </h4>
                    <form action="<?= BASE_URL ?>/profile" method="POST">
                        <input type="hidden" name="profile_action" value="add_address">

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 14px;">
                            <div>
                                <label style="display: block; font-size: 13px; font-weight: 600; color: var(--text-secondary); margin-bottom: 4px;">
                                    Tên người nhận <span style="color: var(--danger);">*</span>
                                </label>
                                <input type="text" name="recipient_name" required placeholder="Họ và tên..." class="form-control" style="width: 100%; padding: 10px 14px; background: var(--bg-surface-elevated); border: 1px solid var(--border-color); border-radius: 6px; color: var(--text-primary); font-size: 13.5px;">
                            </div>
                            <div>
                                <label style="display: block; font-size: 13px; font-weight: 600; color: var(--text-secondary); margin-bottom: 4px;">
                                    Số điện thoại <span style="color: var(--danger);">*</span>
                                </label>
                                <input type="tel" name="recipient_phone" required placeholder="Số điện thoại nhận hàng..." class="form-control" style="width: 100%; padding: 10px 14px; background: var(--bg-surface-elevated); border: 1px solid var(--border-color); border-radius: 6px; color: var(--text-primary); font-size: 13.5px;">
                            </div>
                        </div>

                        <div style="margin-bottom: 14px;">
                            <label style="display: block; font-size: 13px; font-weight: 600; color: var(--text-secondary); margin-bottom: 4px;">
                                Địa chỉ chi tiết <span style="color: var(--danger);">*</span>
                            </label>
                            <input type="text" name="full_address" required placeholder="Số nhà, tên đường, phường/xã, quận/huyện, tỉnh/thành..." class="form-control" style="width: 100%; padding: 10px 14px; background: var(--bg-surface-elevated); border: 1px solid var(--border-color); border-radius: 6px; color: var(--text-primary); font-size: 13.5px;">
                        </div>

                        <div style="margin-bottom: 16px;">
                            <label style="font-size: 13px; color: var(--text-secondary); display: flex; align-items: center; gap: 6px; cursor: pointer;">
                                <input type="checkbox" name="is_default" value="1" style="accent-color: var(--primary);">
                                Đặt làm địa chỉ giao hàng mặc định
                            </label>
                        </div>

                        <div style="display: flex; gap: 10px;">
                            <button type="submit" class="btn btn-primary btn-sm" style="padding: 10px 20px;">
                                <i class="fa-solid fa-check"></i> Lưu Địa Chỉ
                            </button>
                            <button type="button" onclick="document.getElementById('addAddressModal').style.display='none'" class="btn btn-secondary btn-sm" style="padding: 10px 16px;">
                                Hủy Bỏ
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Form 3: Đổi mật khẩu -->
            <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 32px 30px; box-shadow: var(--shadow-sm);">
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 24px; border-bottom: 1px solid var(--border-color); padding-bottom: 16px;">
                    <div style="width: 40px; height: 40px; border-radius: 10px; background: var(--bg-surface-elevated); color: #ff0055; display: flex; align-items: center; justify-content: center; font-size: 18px;">
                        <i class="fa-solid fa-key"></i>
                    </div>
                    <div>
                        <h3 style="font-size: 18px; font-weight: 700; color: var(--text-primary); margin-bottom: 2px;">Đổi Mật Khẩu Đăng Nhập</h3>
                        <p style="font-size: 13px; color: var(--text-muted);">Bảo vệ tài khoản bằng mật khẩu có độ dài từ 6 ký tự trở lên</p>
                    </div>
                </div>

                <form action="<?= BASE_URL ?>/profile" method="POST">
                    <input type="hidden" name="profile_action" value="change_password">

                    <div style="margin-bottom: 18px;">
                        <label style="display: block; font-size: 13.5px; font-weight: 600; color: var(--text-secondary); margin-bottom: 6px;">
                            Mật khẩu hiện tại
                        </label>
                        <input type="password" name="current_password" required placeholder="Nhập mật khẩu đang sử dụng..." class="form-control" style="width: 100%; padding: 12px 16px; background: var(--bg-surface-elevated); border: 1px solid var(--border-color); border-radius: 8px; color: var(--text-primary); font-size: 14px;">
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 24px;">
                        <div>
                            <label style="display: block; font-size: 13.5px; font-weight: 600; color: var(--text-secondary); margin-bottom: 6px;">
                                Mật khẩu mới
                            </label>
                            <input type="password" name="new_password" required minlength="6" placeholder="Tối thiểu 6 ký tự..." class="form-control" style="width: 100%; padding: 12px 16px; background: var(--bg-surface-elevated); border: 1px solid var(--border-color); border-radius: 8px; color: var(--text-primary); font-size: 14px;">
                        </div>

                        <div>
                            <label style="display: block; font-size: 13.5px; font-weight: 600; color: var(--text-secondary); margin-bottom: 6px;">
                                Xác nhận mật khẩu mới
                            </label>
                            <input type="password" name="confirm_new_password" required minlength="6" placeholder="Nhập lại mật khẩu mới..." class="form-control" style="width: 100%; padding: 12px 16px; background: var(--bg-surface-elevated); border: 1px solid var(--border-color); border-radius: 8px; color: var(--text-primary); font-size: 14px;">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary" style="padding: 12px 24px; font-weight: 700; border-radius: 8px;">
                        <i class="fa-solid fa-lock"></i> Cập Nhật Mật Khẩu Mới
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
