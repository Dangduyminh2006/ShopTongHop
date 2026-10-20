<div class="admin-card">
    <div class="admin-card-header">
        <div class="admin-card-title">
            <i class="fa-solid fa-users-gear" style="color: var(--admin-primary);"></i>
            Quản Lý Tài Khoản Người Dùng & Phân Quyền (<?= count($users) ?>)
        </div>
        <div style="font-size: 13px; color: var(--admin-text-muted);">
            <i class="fa-solid fa-shield-halved" style="color: var(--admin-primary);"></i> Tài khoản <code>isAdmin</code> được bảo vệ nghiêm ngặt (Không được phép xóa).
        </div>
    </div>

    <div class="admin-table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th style="width: 50px;">ID</th>
                    <th>Tên Đăng Nhập</th>
                    <th>Họ Và Tên</th>
                    <th>Email Liên Hệ</th>
                    <th>Số Điện Thoại</th>
                    <th>Vai Trò & Quyền (isAdmin)</th>
                    <th style="text-align: center;">Đơn Hàng</th>
                    <th>Trạng Thái (isDelete)</th>
                    <th>Ngày Tham Gia</th>
                    <th style="width: 170px; text-align: center;">Thao Tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($users)): ?>
                    <?php foreach ($users as $u): ?>
                        <tr style="<?= !empty($u['isDelete']) ? 'opacity: 0.6; background: rgba(239, 68, 68, 0.04);' : '' ?>">
                            <td>#<?= $u['id'] ?></td>
                            <td>
                                <strong style="color: var(--admin-primary); font-family: monospace; font-size: 14px;">
                                    <?= e($u['username']) ?>
                                </strong>
                            </td>
                            <td>
                                <strong><?= e($u['fullname']) ?></strong>
                            </td>
                            <td><?= e($u['email']) ?></td>
                            <td><?= e($u['phone'] ?? '-') ?></td>
                            <td>
                                <?php if (!empty($u['isAdmin']) || $u['role'] === 'admin'): ?>
                                    <span class="badge badge-primary" style="display: inline-flex; align-items: center; gap: 4px;">
                                        <i class="fa-solid fa-crown" style="color: #ffb703;"></i> Quản trị viên (isAdmin)
                                    </span>
                                <?php else: ?>
                                    <span class="badge badge-secondary" style="display: inline-flex; align-items: center; gap: 4px;">
                                        <i class="fa-solid fa-user"></i> Khách hàng
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td style="text-align: center;">
                                <span class="badge badge-info"><?= $u['order_count'] ?? 0 ?> đơn</span>
                            </td>
                            <td>
                                <?php if (!empty($u['isDelete'])): ?>
                                    <span class="badge badge-danger" style="display: inline-flex; align-items: center; gap: 4px;">
                                        <i class="fa-solid fa-user-xmark"></i> Đã xóa (isDelete=1)
                                    </span>
                                <?php elseif ($u['status'] === 'active'): ?>
                                    <span class="badge badge-success" style="display: inline-flex; align-items: center; gap: 4px;">
                                        <i class="fa-solid fa-circle-check"></i> Hoạt động
                                    </span>
                                <?php else: ?>
                                    <span class="badge badge-warning" style="display: inline-flex; align-items: center; gap: 4px;">
                                        <i class="fa-solid fa-lock"></i> Đã khóa
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td style="font-size: 12px; color: var(--admin-text-muted);">
                                <?= date('d/m/Y', strtotime($u['created_at'])) ?>
                            </td>
                            <td style="text-align: center;">
                                <div style="display: flex; gap: 6px; justify-content: center; align-items: center;">
                                    
                                    <!-- Đổi vai trò Admin <-> Customer -->
                                    <form method="POST" action="<?= BASE_URL ?>/admin/users/update-role" style="display: inline;">
                                        <input type="hidden" name="id" value="<?= $u['id'] ?>">
                                        <input type="hidden" name="role" value="<?= $u['role'] === 'admin' ? 'customer' : 'admin' ?>">
                                        <button type="submit" class="btn btn-secondary btn-sm" title="Chuyển vai trò sang <?= $u['role'] === 'admin' ? 'Khách hàng' : 'Admin' ?>" <?= ($u['id'] == 1 || !empty($u['isAdmin'])) ? 'disabled style="opacity: 0.5; cursor: not-allowed;"' : '' ?>>
                                            <i class="fa-solid fa-user-pen"></i>
                                        </button>
                                    </form>

                                    <!-- Khóa / Mở khóa tài khoản -->
                                    <?php if (empty($u['isDelete'])): ?>
                                        <form method="POST" action="<?= BASE_URL ?>/admin/users/toggle-status" onsubmit="return confirm('Bạn có chắc muốn đổi trạng thái tài khoản này?');" style="display: inline;">
                                            <input type="hidden" name="id" value="<?= $u['id'] ?>">
                                            <button type="submit" class="btn <?= $u['status'] === 'active' ? 'btn-warning' : 'btn-success' ?> btn-sm" title="<?= $u['status'] === 'active' ? 'Khóa tài khoản' : 'Mở khóa tài khoản' ?>" <?= ($u['id'] == 1 || !empty($u['isAdmin'])) ? 'disabled style="opacity: 0.5; cursor: not-allowed;"' : '' ?>>
                                                <i class="fa-solid <?= $u['status'] === 'active' ? 'fa-lock' : 'fa-lock-open' ?>"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>

                                    <!-- Xóa mềm / Khôi phục tài khoản -->
                                    <?php if (!empty($u['isAdmin']) || $u['role'] === 'admin' || $u['id'] == 1): ?>
                                        <!-- Không được xóa tài khoản có isAdmin = 1 -->
                                        <button type="button" class="btn btn-sm" disabled title="Tài khoản Quản trị viên (isAdmin) được bảo vệ vĩnh viễn, KHÔNG ĐƯỢC PHÉP XÓA!" style="opacity: 0.35; cursor: not-allowed; background: var(--admin-border); color: var(--admin-text-muted);">
                                            <i class="fa-solid fa-shield-slash"></i>
                                        </button>
                                    <?php elseif (!empty($u['isDelete'])): ?>
                                        <!-- Khôi phục tài khoản -->
                                        <form method="POST" action="<?= BASE_URL ?>/admin/users/restore" onsubmit="return confirm('Bạn có chắc muốn khôi phục tài khoản này?');" style="display: inline;">
                                            <input type="hidden" name="id" value="<?= $u['id'] ?>">
                                            <button type="submit" class="btn btn-success btn-sm" title="Khôi phục tài khoản (isDelete = 0)">
                                                <i class="fa-solid fa-rotate-left"></i> Khôi phục
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <!-- Xóa mềm (isDelete = 1) -->
                                        <form method="POST" action="<?= BASE_URL ?>/admin/users/delete" onsubmit="return confirm('Bạn có chắc muốn xóa tài khoản này? (Tài khoản sẽ được chuyển sang trạng thái isDelete = 1)');" style="display: inline;">
                                            <input type="hidden" name="id" value="<?= $u['id'] ?>">
                                            <button type="submit" class="btn btn-danger btn-sm" title="Xóa tài khoản (Chuyển isDelete = 1)">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>

                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="10" style="text-align: center; color: var(--admin-text-muted); padding: 30px;">
                            Chưa có người dùng nào.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
