<?php
/**
 * ShopXpress - Base Controller
 * Quản lý kết nối giữa Model và View, nạp layout và phản hồi HTTP
 */

abstract class Controller {

    /**
     * Nạp và render giao diện View kèm Layout
     * @param string $viewPath Đường dẫn view tính từ app/Views (ví dụ: 'home/index' hoặc 'product/detail')
     * @param array $data Dữ liệu truyền từ Controller sang View
     * @param string|null $layout Tên layout ('main', 'admin' hoặc null nếu không dùng layout)
     */
    protected function view(string $viewPath, array $data = [], ?string $layout = 'main'): void {
        // Trích xuất mảng dữ liệu thành các biến cục bộ
        extract($data);

        $viewFile = APP_PATH . DIRECTORY_SEPARATOR . 'Views' . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $viewPath) . '.php';

        if (!file_exists($viewFile)) {
            die("
                <div style='font-family:sans-serif; max-width:600px; margin:50px auto; padding:25px; border:1px solid #ff4757; border-radius:8px; background:#fff5f5; color:#2f3542;'>
                    <h2 style='color:#ff4757; margin-top:0;'>Lỗi Render View</h2>
                    <p>Không tìm thấy tệp giao diện: <code>" . htmlspecialchars($viewFile) . "</code></p>
                </div>
            ");
        }

        // Bắt đầu vùng đệm bộ nhớ (Output Buffering) để render View con
        ob_start();
        require $viewFile;
        $content = ob_get_clean();

        // Nếu có layout bao ngoài
        if ($layout !== null) {
            $layoutFile = APP_PATH . DIRECTORY_SEPARATOR . 'Views' . DIRECTORY_SEPARATOR . 'layouts' . DIRECTORY_SEPARATOR . $layout . '.php';
            if (file_exists($layoutFile)) {
                require $layoutFile;
            } else {
                // Fallback nếu chưa tạo layout: in trực tiếp nội dung view
                echo $content;
            }
        } else {
            echo $content;
        }
    }

    /**
     * Khởi tạo và nạp Model
     * @param string $modelName Tên Model (ví dụ: 'Product', 'Category', 'User')
     */
    protected function model(string $modelName): object {
        $modelFile = APP_PATH . DIRECTORY_SEPARATOR . 'Models' . DIRECTORY_SEPARATOR . $modelName . '.php';

        if (file_exists($modelFile)) {
            require_once $modelFile;
            if (class_exists($modelName)) {
                return new $modelName();
            }
        }

        die("
            <div style='font-family:sans-serif; max-width:600px; margin:50px auto; padding:25px; border:1px solid #ff4757; border-radius:8px; background:#fff5f5; color:#2f3542;'>
                <h2 style='color:#ff4757; margin-top:0;'>Lỗi Nạp Model</h2>
                <p>Không tìm thấy Model: <code>" . htmlspecialchars($modelName) . "</code> tại đường dẫn: <code>" . htmlspecialchars($modelFile) . "</code></p>
            </div>
        ");
    }

    /**
     * Phản hồi định dạng JSON (phục vụ AJAX hoặc API)
     */
    protected function json(mixed $data, int $statusCode = 200): void {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    /**
     * Chuyển hướng trang an toàn (Redirect)
     */
    protected function redirect(string $path): void {
        if (strpos($path, 'http://') === 0 || strpos($path, 'https://') === 0) {
            header("Location: " . $path);
        } else {
            $url = BASE_URL . '/' . ltrim($path, '/');
            header("Location: " . $url);
        }
        exit;
    }

    /**
     * Kiểm tra xem yêu cầu hiện tại có phải AJAX/Fetch API không
     */
    protected function isAjax(): bool {
        return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
            || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)
            || (isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false)
            || (isset($_SERVER['HTTP_SEC_FETCH_DEST']) && $_SERVER['HTTP_SEC_FETCH_DEST'] === 'empty')
            || isset($_POST['ajax']) || isset($_GET['ajax']);
    }

    /**
     * Kiểm tra trạng thái đã đăng nhập hay chưa
     */
    protected function isLoggedIn(): bool {
        return isset($_SESSION['user']) && !empty($_SESSION['user']['id']);
    }

    /**
     * Kiểm tra quyền Quản trị viên Admin
     */
    protected function isAdmin(): bool {
        return $this->isLoggedIn() && ($_SESSION['user']['role'] ?? '') === 'admin';
    }

    /**
     * Lấy thông tin user hiện tại trong phiên Session
     */
    protected function currentUser(): ?array {
        return $_SESSION['user'] ?? null;
    }

    /**
     * Lưu thông báo nhanh (Flash Message)
     */
    protected function setFlash(string $type, string $message): void {
        $_SESSION['flash'] = [
            'type' => $type, // 'success', 'danger', 'warning', 'info'
            'message' => $message
        ];
    }

    /**
     * Lấy và xóa thông báo nhanh Flash Message
     */
    protected function getFlash(): ?array {
        if (isset($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            unset($_SESSION['flash']);
            return $flash;
        }
        return null;
    }
}
