<?php
/**
 * ShopXpress - Front Controller Router
 * Phân tích URL và điều hướng chính xác đến Controller & Action
 * Tương thích cả Clean URL (.htaccess) và Query Parameter (?url=...)
 */

class App {
    protected string $controller = DEFAULT_CONTROLLER;
    protected string $action = DEFAULT_ACTION;
    protected array $params = [];

    /**
     * Bảng ánh xạ các tuyến đường tĩnh (Explicit Routing Table)
     */
    private array $routes = [
        // Khách hàng (Storefront)
        ''                          => ['HomeController', 'index'],
        'home'                      => ['HomeController', 'index'],
        'products'                  => ['ProductController', 'index'],
        'catalog'                   => ['ProductController', 'index'],
        'product/detail'            => ['ProductController', 'detail'],
        'products/detail'           => ['ProductController', 'detail'],
        'cart'                      => ['CartController', 'index'],
        'cart/add'                  => ['CartController', 'add'],
        'cart/update'               => ['CartController', 'update'],
        'cart/remove'               => ['CartController', 'remove'],
        'cart/clear'                => ['CartController', 'clear'],
        'cart/apply-voucher'        => ['CartController', 'applyVoucher'],
        'cart/remove-voucher'       => ['CartController', 'removeVoucher'],
        'checkout'                  => ['CartController', 'checkout'],
        'checkout/process'          => ['CartController', 'processCheckout'],
        'checkout/success'          => ['CartController', 'success'],
        'login'                     => ['AuthController', 'login'],
        'auth/login'                => ['AuthController', 'login'],
        'register'                  => ['AuthController', 'register'],
        'auth/register'             => ['AuthController', 'register'],
        'logout'                    => ['AuthController', 'logout'],
        'auth/logout'               => ['AuthController', 'logout'],
        'profile'                   => ['AuthController', 'profile'],
        'auth/profile'              => ['AuthController', 'profile'],
        'review/add'                => ['ReviewController', 'add'],
        'orders'                    => ['OrderController', 'history'],
        'order/history'             => ['OrderController', 'history'],
        'order/detail'              => ['OrderController', 'detail'],
        'order/invoice'             => ['OrderController', 'invoice'],
        'order/lookup'              => ['OrderController', 'lookup'],

        // Quản trị viên (Admin Portal)
        'admin'                     => ['AdminController', 'dashboard'],
        'admin/dashboard'           => ['AdminController', 'dashboard'],
        'admin/products'            => ['AdminController', 'products'],
        'admin/products/create'     => ['AdminController', 'createProduct'],
        'admin/products/edit'       => ['AdminController', 'editProduct'],
        'admin/products/delete'     => ['AdminController', 'deleteProduct'],
        'admin/categories'          => ['AdminController', 'categories'],
        'admin/categories/create'   => ['AdminController', 'createCategory'],
        'admin/categories/edit'     => ['AdminController', 'editCategory'],
        'admin/categories/delete'   => ['AdminController', 'deleteCategory'],
        'admin/brands'              => ['AdminController', 'brands'],
        'admin/brands/create'       => ['AdminController', 'createBrand'],
        'admin/brands/edit'         => ['AdminController', 'editBrand'],
        'admin/brands/delete'       => ['AdminController', 'deleteBrand'],
        'admin/orders'              => ['AdminController', 'orders'],
        'admin/orders/detail'       => ['AdminController', 'orderDetail'],
        'admin/orders/update-status'=> ['AdminController', 'updateOrderStatus'],
        'admin/vouchers'            => ['AdminController', 'vouchers'],
        'admin/vouchers/create'     => ['AdminController', 'createVoucher'],
        'admin/vouchers/edit'       => ['AdminController', 'editVoucher'],
        'admin/vouchers/delete'     => ['AdminController', 'deleteVoucher'],
        'admin/flash-sales'         => ['AdminController', 'flashSales'],
        'admin/flash_sales'         => ['AdminController', 'flashSales'],
        'admin/flash-sales/create'  => ['AdminController', 'createFlashSale'],
        'admin/flash_sales/create'  => ['AdminController', 'createFlashSale'],
        'admin/flash-sales/delete'  => ['AdminController', 'deleteFlashSale'],
        'admin/flash_sales/delete'  => ['AdminController', 'deleteFlashSale'],
        'admin/banners'             => ['AdminController', 'banners'],
        'admin/banners/create'      => ['AdminController', 'createBanner'],
        'admin/banners/delete'      => ['AdminController', 'deleteBanner'],
        'admin/news'                => ['AdminController', 'news'],
        'admin/news/create'         => ['AdminController', 'createNews'],
        'admin/news/edit'           => ['AdminController', 'editNews'],
        'admin/news/delete'         => ['AdminController', 'deleteNews'],
        'admin/users'               => ['AdminController', 'users'],
        'admin/users/update-role'   => ['AdminController', 'updateUserRole'],
        'admin/users/toggle-status' => ['AdminController', 'toggleUserStatus'],
        'admin/users/delete'        => ['AdminController', 'deleteUser'],
        'admin/users/restore'       => ['AdminController', 'restoreUser'],
    ];

    public function __construct() {
        $url = $this->parseUrl();
        $matched = false;

        // 1. Kiểm tra đối sánh trong Bảng định tuyến tĩnh
        if (isset($this->routes[$url])) {
            [$this->controller, $this->action] = $this->routes[$url];
            $matched = true;
        } else {
            // 2. Thử phân giải động dạng: controller/action/params
            $segments = explode('/', $url);
            if (!empty($segments[0])) {
                $possibleController = ucfirst($segments[0]) . 'Controller';
                $controllerFile = APP_PATH . DIRECTORY_SEPARATOR . 'Controllers' . DIRECTORY_SEPARATOR . $possibleController . '.php';

                if (file_exists($controllerFile)) {
                    $this->controller = $possibleController;
                    unset($segments[0]);

                    if (isset($segments[1]) && !empty($segments[1])) {
                        $this->action = $segments[1];
                        unset($segments[1]);
                    }
                    $this->params = $segments ? array_values($segments) : [];
                    $matched = true;
                }
            }
        }

        // 3. Khởi tạo và điều phối Controller
        $this->dispatch();
    }

    /**
     * Phân tích và chuẩn hóa chuỗi URL yêu cầu
     */
    private function parseUrl(): string {
        $url = '';

        if (isset($_GET['url'])) {
            $url = trim($_GET['url'], '/');
        } else {
            $requestUri = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
            $scriptName = dirname($_SERVER['SCRIPT_NAME'] ?? '');

            if ($scriptName !== '/' && strpos($requestUri, $scriptName) === 0) {
                $url = substr($requestUri, strlen($scriptName));
            } else {
                $baseFolder = '/ShopTongHop';
                if (strpos($requestUri, $baseFolder) === 0) {
                    $url = substr($requestUri, strlen($baseFolder));
                } else {
                    $url = $requestUri;
                }
            }

            // Bỏ tiếp đầu ngữ 'public/' nếu còn sót
            $url = preg_replace('#^/?public/#i', '', $url);
            $url = trim($url, '/');
        }

        return filter_var($url, FILTER_SANITIZE_URL);
    }

    /**
     * Khởi tạo Controller và gọi Action
     */
    private function dispatch(): void {
        $controllerFile = APP_PATH . DIRECTORY_SEPARATOR . 'Controllers' . DIRECTORY_SEPARATOR . $this->controller . '.php';

        if (!file_exists($controllerFile)) {
            $this->render404("Không tìm thấy tệp Controller: " . htmlspecialchars($this->controller));
            return;
        }

        require_once $controllerFile;

        if (!class_exists($this->controller)) {
            $this->render404("Không tìm thấy Class Controller: " . htmlspecialchars($this->controller));
            return;
        }

        $controllerInstance = new $this->controller();

        if (!method_exists($controllerInstance, $this->action)) {
            $this->render404("Không tìm thấy Action <code>{$this->action}</code> trong Controller <code>{$this->controller}</code>");
            return;
        }

        // Thực thi Action với mảng tham số động
        call_user_func_array([$controllerInstance, $this->action], $this->params);
    }

    /**
     * Hiển thị giao diện 404 thân thiện
     */
    private function render404(string $message): void {
        http_response_code(404);
        ?>
        <!DOCTYPE html>
        <html lang="vi">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>404 - Trang Không Tồn Tại | ShopXpress</title>
            <style>
                body {
                    margin: 0;
                    padding: 0;
                    background-color: #0D1117;
                    color: #E6EDF3;
                    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    min-height: 100vh;
                    text-align: center;
                }
                .error-container {
                    max-width: 550px;
                    padding: 40px;
                    background: #161B22;
                    border: 1px solid #30363D;
                    border-radius: 16px;
                    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);
                }
                h1 {
                    font-size: 80px;
                    margin: 0;
                    color: #FF6B35;
                    font-weight: 800;
                    letter-spacing: -2px;
                }
                h2 {
                    margin: 10px 0 15px;
                    font-size: 22px;
                    color: #FFFFFF;
                }
                p {
                    color: #8B949E;
                    font-size: 14px;
                    line-height: 1.6;
                    margin-bottom: 25px;
                }
                .btn-home {
                    display: inline-block;
                    padding: 12px 30px;
                    background: #FF6B35;
                    color: #FFFFFF;
                    text-decoration: none;
                    font-weight: 600;
                    border-radius: 8px;
                    transition: transform 0.2s, background 0.2s;
                }
                .btn-home:hover {
                    background: #ff5414;
                    transform: translateY(-2px);
                }
            </style>
        </head>
        <body>
            <div class="error-container">
                <h1>404</h1>
                <h2>TRANG KHÔNG TỒN TẠI</h2>
                <p><?= $message ?></p>
                <a href="<?= BASE_URL ?>" class="btn-home">QUAY VỀ TRANG CHỦ</a>
            </div>
        </body>
        </html>
        <?php
        exit;
    }
}
