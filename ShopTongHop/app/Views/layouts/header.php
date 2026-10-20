<?php
$user = $_SESSION['user'] ?? null;
$cartCount = isset($_SESSION['cart']) ? array_sum(array_column($_SESSION['cart'], 'quantity')) : 0;
?>
<!DOCTYPE html>
<html lang="vi" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? APP_NAME) ?></title>
    <meta name="description" content="<?= APP_SLOGAN ?>">
    
    <!-- Google Fonts: Outfit & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/main.css">
    <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/storefront.css">
</head>
<body>

<!-- TOPBAR -->
<div class="topbar">
    <div class="container topbar-inner">
        <div class="topbar-left">
            <span><i class="fa-solid fa-headset"></i> <?= t('hotline') ?></span>
            <span class="divider">|</span>
            <span><i class="fa-solid fa-truck-fast"></i> Miễn phí giao hàng đơn từ 300K</span>
        </div>
        <div class="topbar-right">
            <!-- Dark / Light Theme Toggle -->
            <button id="themeToggleBtn" class="theme-toggle-btn" title="Chuyển đổi giao diện Sáng / Tối">
                <i class="fa-solid fa-moon icon-moon"></i>
                <i class="fa-solid fa-sun icon-sun"></i>
            </button>
            <span class="divider">|</span>
            <!-- User Menu -->
            <?php if (!empty($user)): ?>
                <div class="user-dropdown">
                    <span class="user-greeting">
                        <i class="fa-solid fa-user-circle"></i> <?= e($user['fullname']) ?>
                    </span>
                    <div class="dropdown-menu">
                        <?php if (($user['role'] ?? '') === 'admin'): ?>
                            <a href="<?= BASE_URL ?>/admin" class="dropdown-item admin-link"><i class="fa-solid fa-gauge-high"></i> <?= t('nav_admin') ?></a>
                        <?php endif; ?>
                        <a href="<?= BASE_URL ?>/profile" class="dropdown-item"><i class="fa-solid fa-id-card"></i> <?= t('nav_account') ?></a>
                        <a href="<?= BASE_URL ?>/order/history" class="dropdown-item"><i class="fa-solid fa-receipt"></i> Lịch Sử Mua Hàng</a>
                        <a href="<?= BASE_URL ?>/logout" class="dropdown-item logout-link"><i class="fa-solid fa-right-from-bracket"></i> <?= t('nav_logout') ?></a>
                    </div>
                </div>
            <?php else: ?>
                <div class="auth-links">
                    <a href="<?= BASE_URL ?>/login"><i class="fa-solid fa-arrow-right-to-bracket"></i> <?= t('nav_login') ?></a>
                    <span class="divider">/</span>
                    <a href="<?= BASE_URL ?>/register"><?= t('nav_register') ?></a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- MAIN HEADER -->
<header class="main-header">
    <div class="container header-inner">
        <!-- Logo -->
        <a href="<?= BASE_URL ?>" class="brand-logo">
            <span class="logo-box"><i class="fa-solid fa-bag-shopping"></i></span>
            <span class="logo-text">SHOP<span class="logo-accent">XPRESS</span></span>
        </a>

        <!-- Search Form -->
        <form action="<?= BASE_URL ?>/products" method="GET" class="header-search">
            <input type="text" name="q" placeholder="<?= t('search_placeholder') ?>" value="<?= e($_GET['q'] ?? ($_GET['keyword'] ?? '')) ?>" autocomplete="off">
            <button type="submit" class="search-btn"><i class="fa-solid fa-magnifying-glass"></i></button>
        </form>

        <!-- Right Quick Actions -->
        <div class="header-actions">
            <!-- Order Lookup Quick Action -->
            <a href="<?= BASE_URL ?>/order/history" class="header-action-btn orders-btn" title="Lịch sử mua hàng & Tra cứu hóa đơn">
                <i class="fa-solid fa-receipt"></i>
                <span class="action-label">Đơn Hàng</span>
            </a>
            <!-- Cart Icon -->
            <a href="<?= BASE_URL ?>/cart" class="header-action-btn cart-btn">
                <i class="fa-solid fa-cart-shopping"></i>
                <span class="badge" id="cartBadge"><?= $cartCount ?></span>
                <span class="action-label"><?= t('nav_cart') ?></span>
            </a>
        </div>
    </div>
</header>

<!-- NAVIGATION BAR -->
<nav class="main-nav">
    <div class="container nav-inner">
        <div class="nav-links">
            <a href="<?= BASE_URL ?>" class="nav-link <?= empty($_GET['url']) || $_GET['url'] === 'home' ? 'active' : '' ?>">
                <i class="fa-solid fa-house"></i> <?= t('nav_home') ?>
            </a>
            <a href="<?= BASE_URL ?>/products" class="nav-link <?= (($_GET['url'] ?? '') === 'products') ? 'active' : '' ?>">
                <i class="fa-solid fa-boxes-stacked"></i> <?= t('nav_products') ?>
            </a>
            <a href="<?= BASE_URL ?>/#flashSaleSection" class="nav-link flash-link">
                <i class="fa-solid fa-bolt"></i> <?= t('nav_flash_sale') ?>
            </a>
            <a href="<?= BASE_URL ?>/order/history" class="nav-link <?= (strpos($_GET['url'] ?? '', 'order') === 0) ? 'active' : '' ?>">
                <i class="fa-solid fa-receipt"></i> Tra Cứu Đơn Hàng
            </a>
        </div>
        <div class="nav-support">
            <i class="fa-solid fa-shield-halved"></i> 100% Chính Hãng & Đổi Trả 7 Ngày
        </div>
    </div>
</nav>

<!-- MAIN CONTENT WRAPPER -->
<main class="site-main">
