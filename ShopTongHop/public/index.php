<?php
/**
 * ShopXpress - Single Entry Point (Front Controller)
 * Khởi động toàn bộ ứng dụng Pure PHP MVC
 */

// 1. Khởi động phiên làm việc (Session) với cờ bảo mật
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    session_start();
}

// 2. Nạp tệp cấu hình hệ thống
require_once dirname(__DIR__) . '/app/Config/config.php';

// 3. Cơ chế tự động nạp lớp (Autoloading) không dùng Composer
spl_autoload_register(function ($className) {
    $directories = [
        APP_PATH . '/Core/',
        APP_PATH . '/Controllers/',
        APP_PATH . '/Models/',
    ];

    foreach ($directories as $directory) {
        $file = $directory . $className . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

header('Content-Type: text/html; charset=UTF-8');

// 4. Hệ thống thuần Tiếng Việt (Loại bỏ chức năng chuyển ngữ)
unset($_SESSION['lang']);
Lang::init();

// 5. Khởi chạy bộ định tuyến ứng dụng
$app = new App();
