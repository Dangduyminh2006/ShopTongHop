<?php
/**
 * ShopXpress Commerce Suite - Admin Master Layout
 * Thiết kế giao diện Quản Trị Thương Mại Đa Kênh đẳng cấp, hiện đại
 */
$currentPage = $activeMenu ?? 'dashboard';
$flash = $this->getFlash();
$currentUser = $this->currentUser() ?? ['fullname' => 'Quản Trị Viên', 'username' => 'admin'];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'Bảng Điều Khiển') ?> | ShopXpress Commerce Suite</title>
    <!-- Google Fonts: Plus Jakarta Sans & Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- Admin CSS -->
    <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/admin.css">
</head>
<body>

    <!-- SIDEBAR QUẢN TRỊ COMMERCE SUITE -->
    <aside class="admin-sidebar">
        <!-- Brand Header -->
        <div class="admin-sidebar-brand-box">
            <a href="<?= BASE_URL ?>/admin" class="admin-brand-link">
                <div class="admin-brand-icon">
                    <i class="fa-solid fa-bolt-lightning"></i>
                </div>
                <div class="admin-brand-text">
                    <div class="brand-name">ShopXpress</div>
                    <div class="brand-sub">COMMERCE SUITE</div>
                </div>
            </a>
        </div>

        <!-- Navigation Menus -->
        <div class="admin-sidebar-scroll">
            <div class="admin-menu-label">
                <span class="menu-dot"></span> TỔNG QUAN HỆ THỐNG
            </div>
            <ul class="admin-nav-list">
                <li class="admin-nav-item">
                    <a href="<?= BASE_URL ?>/admin/dashboard" class="admin-nav-link <?= $currentPage === 'dashboard' ? 'active' : '' ?>">
                        <span class="nav-icon-wrap"><i class="fa-solid fa-chart-line"></i></span>
                        <span class="nav-title">Bảng Điều Khiển</span>
                    </a>
                </li>
            </ul>

            <div class="admin-menu-label">
                <span class="menu-dot"></span> PHÂN HỆ BÁN HÀNG
            </div>
            <ul class="admin-nav-list">
                <li class="admin-nav-item">
                    <a href="<?= BASE_URL ?>/admin/products" class="admin-nav-link <?= in_array($currentPage, ['products', 'products_create', 'products_edit']) ? 'active' : '' ?>">
                        <span class="nav-icon-wrap"><i class="fa-solid fa-boxes-stacked"></i></span>
                        <span class="nav-title">Quản Lý Sản Phẩm</span>
                    </a>
                </li>
                <li class="admin-nav-item">
                    <a href="<?= BASE_URL ?>/admin/categories" class="admin-nav-link <?= $currentPage === 'categories' ? 'active' : '' ?>">
                        <span class="nav-icon-wrap"><i class="fa-solid fa-sitemap"></i></span>
                        <span class="nav-title">Danh Mục Hàng Hóa</span>
                    </a>
                </li>
                <li class="admin-nav-item">
                    <a href="<?= BASE_URL ?>/admin/brands" class="admin-nav-link <?= $currentPage === 'brands' ? 'active' : '' ?>">
                        <span class="nav-icon-wrap"><i class="fa-solid fa-award"></i></span>
                        <span class="nav-title">Thương Hiệu Hãng</span>
                    </a>
                </li>
                <li class="admin-nav-item">
                    <a href="<?= BASE_URL ?>/admin/orders" class="admin-nav-link <?= in_array($currentPage, ['orders', 'order_detail']) ? 'active' : '' ?>">
                        <span class="nav-icon-wrap"><i class="fa-solid fa-truck-ramp-box"></i></span>
                        <span class="nav-title">Đơn Đặt Hàng</span>
                    </a>
                </li>
            </ul>

            <div class="admin-menu-label">
                <span class="menu-dot"></span> MARKETING & TIẾP THỊ
            </div>
            <ul class="admin-nav-list">
                <li class="admin-nav-item">
                    <a href="<?= BASE_URL ?>/admin/vouchers" class="admin-nav-link <?= $currentPage === 'vouchers' ? 'active' : '' ?>">
                        <span class="nav-icon-wrap"><i class="fa-solid fa-ticket-simple"></i></span>
                        <span class="nav-title">Mã Giảm Giá Voucher</span>
                    </a>
                </li>
                <li class="admin-nav-item">
                    <a href="<?= BASE_URL ?>/admin/flash-sales" class="admin-nav-link <?= $currentPage === 'flash_sales' ? 'active' : '' ?>">
                        <span class="nav-icon-wrap"><i class="fa-solid fa-fire-flame-curved"></i></span>
                        <span class="nav-title">Chiến Dịch Flash Sale</span>
                    </a>
                </li>
                <li class="admin-nav-item">
                    <a href="<?= BASE_URL ?>/admin/banners" class="admin-nav-link <?= $currentPage === 'banners' ? 'active' : '' ?>">
                        <span class="nav-icon-wrap"><i class="fa-solid fa-panorama"></i></span>
                        <span class="nav-title">Banner Quảng Cáo</span>
                    </a>
                </li>
            </ul>

            <div class="admin-menu-label">
                <span class="menu-dot"></span> QUẢN TRỊ HỆ THỐNG
            </div>
            <ul class="admin-nav-list">
                <li class="admin-nav-item">
                    <a href="<?= BASE_URL ?>/admin/users" class="admin-nav-link <?= $currentPage === 'users' ? 'active' : '' ?>">
                        <span class="nav-icon-wrap"><i class="fa-solid fa-user-shield"></i></span>
                        <span class="nav-title">Tài Khoản & Phân Quyền</span>
                    </a>
                </li>
            </ul>
        </div>

        <!-- User Profile Card in Sidebar Footer -->
        <div class="admin-sidebar-footer">
            <div class="admin-profile-card">
                <div class="admin-profile-avatar">
                    <i class="fa-solid fa-user-astronaut"></i>
                    <span class="status-online-dot"></span>
                </div>
                <div class="admin-profile-info">
                    <div class="profile-name"><?= e($currentUser['fullname'] ?? 'Admin') ?></div>
                    <div class="profile-role">
                        <i class="fa-solid fa-shield-halved"></i> Super Administrator
                    </div>
                </div>
                <a href="<?= BASE_URL ?>/logout" class="profile-logout-btn" title="Đăng Xuất Tài Khoản">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i>
                </a>
            </div>
        </div>
    </aside>

    <!-- MAIN EXECUTIVE WORKSPACE -->
    <div class="admin-main">
        <!-- Top Executive Header -->
        <header class="admin-header">
            <div class="admin-header-left">
                <!-- Breadcrumb Path -->
                <nav class="admin-breadcrumbs">
                    <a href="<?= BASE_URL ?>/admin" class="crumb-link"><i class="fa-solid fa-house-chimney"></i> Admin</a>
                    <span class="crumb-sep">/</span>
                    <span class="crumb-active"><?= e($pageTitle ?? 'Bảng Điều Khiển') ?></span>
                </nav>
                <h1 class="admin-page-title"><?= e($pageTitle ?? 'Bảng Điều Khiển') ?></h1>
            </div>

            <div class="admin-header-right">
                <!-- Nút xem Storefront ngoài sàn -->
                <a href="<?= BASE_URL ?>" target="_blank" class="btn-storefront-preview">
                    <i class="fa-solid fa-store"></i>
                    <span>Xem Storefront ↗</span>
                </a>

                <!-- Admin Status Tag -->
                <div class="admin-status-pill">
                    <span class="ping-circle"></span>
                    <span>Hệ Thống Sẵn Sàng</span>
                </div>
            </div>
        </header>

        <!-- Main Content Area -->
        <main class="admin-content">
            <!-- Flash Message Banner -->
            <?php if ($flash): ?>
                <div class="admin-alert admin-alert-<?= e($flash['type'] ?? 'info') ?>">
                    <div class="alert-icon">
                        <?php if (($flash['type'] ?? '') === 'success'): ?>
                            <i class="fa-solid fa-circle-check"></i>
                        <?php elseif (($flash['type'] ?? '') === 'error' || ($flash['type'] ?? '') === 'danger'): ?>
                            <i class="fa-solid fa-circle-exclamation"></i>
                        <?php else: ?>
                            <i class="fa-solid fa-circle-info"></i>
                        <?php endif; ?>
                    </div>
                    <div class="alert-body">
                        <?= e($flash['message']) ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- View Con -->
            <?= $content ?? '' ?>
        </main>
    </div>

    <!-- Admin JS -->
    <script src="<?= BASE_URL ?>/assets/js/admin.js"></script>
</body>
</html>
