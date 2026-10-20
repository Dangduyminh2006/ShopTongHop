<?php
/**
 * ShopXpress - Admin Controller
 * Điều phối toàn bộ cổng quản trị Back-Office (Full CRUD & Dashboard)
 */

class AdminController extends Controller {

    public function __construct() {
        if (!isset($_SESSION['user'])) {
            $this->setFlash('warning', 'Vui lòng đăng nhập tài khoản Quản Trị Viên để tiếp tục.');
            $this->redirect('login?redirect=admin');
            exit;
        }

        if (($_SESSION['user']['role'] ?? '') !== 'admin') {
            $this->setFlash('error', 'Tài khoản của bạn không có quyền truy cập vào Khu Vực Quản Trị.');
            $this->redirect('home');
            exit;
        }
    }

    // ====================================================================
    // 1. TỔNG QUAN HỆ THỐNG (DASHBOARD)
    // ====================================================================
    public function dashboard(): void {
        $orderModel = $this->model('Order');
        $productModel = $this->model('Product');
        $userModel = $this->model('User');
        $categoryModel = $this->model('Category');

        $stats = $orderModel->getStats();
        $stats['total_products'] = $productModel->countAll();
        $stats['total_customers'] = $userModel->countCustomers();
        $stats['total_categories'] = $categoryModel->countAll();

        $recentOrders = $orderModel->getRecentOrders(6);

        $this->view('admin/dashboard', [
            'pageTitle'    => 'Bảng Điều Khiển Quản Trị',
            'activeMenu'   => 'dashboard',
            'stats'        => $stats,
            'recentOrders' => $recentOrders
        ], 'admin');
    }

    // ====================================================================
    // 2. QUẢN LÝ SẢN PHẨM (PRODUCTS CRUD)
    // ====================================================================
    public function products(): void {
        $productModel = $this->model('Product');
        $categoryModel = $this->model('Category');
        $brandModel = $this->model('Brand');

        $keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : null;
        $categoryId = !empty($_GET['category_id']) ? (int)$_GET['category_id'] : null;
        $brandId = !empty($_GET['brand_id']) ? (int)$_GET['brand_id'] : null;

        $products = $productModel->getAllAdmin($keyword, $categoryId, $brandId);
        $categories = $categoryModel->getAll();
        $brands = $brandModel->getAll();

        $this->view('admin/products/index', [
            'pageTitle'   => 'Quản Lý Sản Phẩm',
            'activeMenu'  => 'products',
            'products'    => $products,
            'categories'  => $categories,
            'brands'      => $brands,
            'keyword'     => $keyword,
            'categoryId'  => $categoryId,
            'brandId'     => $brandId
        ], 'admin');
    }

    public function createProduct(): void {
        $categoryModel = $this->model('Category');
        $brandModel = $this->model('Brand');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $sku = trim($_POST['sku'] ?? '');
            $price = floatval($_POST['price'] ?? 0);
            $categoryId = (int)($_POST['category_id'] ?? 0);
            $brandId = !empty($_POST['brand_id']) ? (int)$_POST['brand_id'] : null;
            $discount = intval($_POST['discount_percent'] ?? 0);
            $quantity = intval($_POST['stock_quantity'] ?? 0);
            $thumbnail = trim($_POST['thumbnail'] ?? '');
            $summary = trim($_POST['summary'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $isFeatured = isset($_POST['is_featured']) ? 1 : 0;
            $isNew = isset($_POST['is_new']) ? 1 : 0;
            $status = isset($_POST['status']) ? 1 : 0;

            // Xử lý thông số kỹ thuật JSON
            $specs = [];
            if (!empty($_POST['spec_key']) && is_array($_POST['spec_key'])) {
                foreach ($_POST['spec_key'] as $idx => $key) {
                    $key = trim($key);
                    $val = trim($_POST['spec_val'][$idx] ?? '');
                    if ($key !== '' && $val !== '') {
                        $specs[$key] = $val;
                    }
                }
            }

            // Tự động tạo slug
            $slug = $this->createSlug($name) . '-' . strtolower($sku);

            if (empty($thumbnail)) {
                $thumbnail = 'default_product.jpg';
            }

            $productModel = $this->model('Product');
            $id = $productModel->create([
                'category_id'      => $categoryId,
                'brand_id'         => $brandId,
                'name'             => $name,
                'slug'             => $slug,
                'sku'              => $sku,
                'price'            => $price,
                'discount_percent' => $discount,
                'stock_quantity'   => $quantity,
                'thumbnail'        => $thumbnail,
                'summary'          => $summary,
                'description'      => $description,
                'specifications'   => !empty($specs) ? json_encode($specs, JSON_UNESCAPED_UNICODE) : null,
                'is_featured'      => $isFeatured,
                'is_new'           => $isNew,
                'status'           => $status
            ]);

            $this->setFlash('success', "Thêm mới sản phẩm '{$name}' thành công!");
            $this->redirect('admin/products');
        }

        $this->view('admin/products/create', [
            'pageTitle'  => 'Thêm Mới Sản Phẩm',
            'activeMenu' => 'products_create',
            'categories' => $categoryModel->getAll(),
            'brands'     => $brandModel->getAll()
        ], 'admin');
    }

    public function editProduct(int $id = 0): void {
        $id = $id > 0 ? $id : (int)($_GET['id'] ?? 0);
        $productModel = $this->model('Product');
        $product = $productModel->getById($id);

        if (!$product) {
            $this->setFlash('danger', 'Không tìm thấy sản phẩm yêu cầu.');
            $this->redirect('admin/products');
        }

        $categoryModel = $this->model('Category');
        $brandModel = $this->model('Brand');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $sku = trim($_POST['sku'] ?? '');
            $price = floatval($_POST['price'] ?? 0);
            $categoryId = (int)($_POST['category_id'] ?? 0);
            $brandId = !empty($_POST['brand_id']) ? (int)$_POST['brand_id'] : null;
            $discount = intval($_POST['discount_percent'] ?? 0);
            $quantity = intval($_POST['stock_quantity'] ?? 0);
            $thumbnail = trim($_POST['thumbnail'] ?? '');
            $summary = trim($_POST['summary'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $isFeatured = isset($_POST['is_featured']) ? 1 : 0;
            $isNew = isset($_POST['is_new']) ? 1 : 0;
            $status = isset($_POST['status']) ? 1 : 0;

            $specs = [];
            if (!empty($_POST['spec_key']) && is_array($_POST['spec_key'])) {
                foreach ($_POST['spec_key'] as $idx => $key) {
                    $key = trim($key);
                    $val = trim($_POST['spec_val'][$idx] ?? '');
                    if ($key !== '' && $val !== '') {
                        $specs[$key] = $val;
                    }
                }
            }

            $slug = $this->createSlug($name) . '-' . strtolower($sku);
            if (empty($thumbnail)) {
                $thumbnail = $product['thumbnail'];
            }

            $productModel->update($id, [
                'category_id'      => $categoryId,
                'brand_id'         => $brandId,
                'name'             => $name,
                'slug'             => $slug,
                'sku'              => $sku,
                'price'            => $price,
                'discount_percent' => $discount,
                'stock_quantity'   => $quantity,
                'thumbnail'        => $thumbnail,
                'summary'          => $summary,
                'description'      => $description,
                'specifications'   => !empty($specs) ? json_encode($specs, JSON_UNESCAPED_UNICODE) : null,
                'is_featured'      => $isFeatured,
                'is_new'           => $isNew,
                'status'           => $status
            ]);

            $this->setFlash('success', "Cập nhật sản phẩm '{$name}' thành công!");
            $this->redirect('admin/products');
        }

        $specsDecoded = !empty($product['specifications']) ? json_decode($product['specifications'], true) : [];

        $this->view('admin/products/edit', [
            'pageTitle'    => 'Chỉnh Sửa Sản Phẩm: ' . $product['name'],
            'activeMenu'   => 'products_edit',
            'product'      => $product,
            'specs'        => $specsDecoded ?: [],
            'categories'   => $categoryModel->getAll(),
            'brands'       => $brandModel->getAll()
        ], 'admin');
    }

    public function deleteProduct(): void {
        $id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
        if ($id > 0) {
            $productModel = $this->model('Product');
            $productModel->delete($id);
            $this->setFlash('success', 'Đã xóa sản phẩm thành công.');
        }
        $this->redirect('admin/products');
    }

    // ====================================================================
    // 3. QUẢN LÝ DANH MỤC (CATEGORIES CRUD)
    // ====================================================================
    public function categories(): void {
        $categoryModel = $this->model('Category');
        $categories = $categoryModel->getAll();
        $parentCategories = $categoryModel->getParents();

        $this->view('admin/categories/index', [
            'pageTitle'        => 'Quản Lý Danh Mục Ngành Hàng',
            'activeMenu'       => 'categories',
            'categories'       => $categories,
            'parentCategories' => $parentCategories
        ], 'admin');
    }

    public function createCategory(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $parentId = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;
            $icon = trim($_POST['icon'] ?? 'fa-folder');
            $slug = $this->createSlug($name);

            $categoryModel = $this->model('Category');
            $categoryModel->create([
                'name'          => $name,
                'slug'          => $slug,
                'parent_id'     => $parentId,
                'icon'          => $icon,
                'image'         => null,
                'display_order' => (int)($_POST['display_order'] ?? 0),
                'status'        => 1
            ]);

            $this->setFlash('success', "Thêm danh mục '{$name}' thành công!");
        }
        $this->redirect('admin/categories');
    }

    public function deleteCategory(): void {
        $id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
        if ($id > 0) {
            $categoryModel = $this->model('Category');
            $categoryModel->delete($id);
            $this->setFlash('success', 'Đã xóa danh mục.');
        }
        $this->redirect('admin/categories');
    }

    // ====================================================================
    // 4. QUẢN LÝ THƯƠNG HIỆU (BRANDS CRUD)
    // ====================================================================
    public function brands(): void {
        $brandModel = $this->model('Brand');
        $brands = $brandModel->getAll();

        $this->view('admin/brands/index', [
            'pageTitle'  => 'Quản Lý Thương Hiệu Chính Hãng',
            'activeMenu' => 'brands',
            'brands'     => $brands
        ], 'admin');
    }

    public function createBrand(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $logo = trim($_POST['logo'] ?? '');
            $slug = $this->createSlug($name);

            $brandModel = $this->model('Brand');
            $brandModel->create([
                'name'   => $name,
                'slug'   => $slug,
                'logo'   => $logo ?: null,
                'status' => 1
            ]);

            $this->setFlash('success', "Thêm thương hiệu '{$name}' thành công!");
        }
        $this->redirect('admin/brands');
    }

    public function deleteBrand(): void {
        $id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
        if ($id > 0) {
            $brandModel = $this->model('Brand');
            $brandModel->delete($id);
            $this->setFlash('success', 'Đã xóa thương hiệu.');
        }
        $this->redirect('admin/brands');
    }

    // ====================================================================
    // 5. QUẢN LÝ ĐƠN ĐẶT HÀNG (ORDERS)
    // ====================================================================
    public function orders(): void {
        $orderModel = $this->model('Order');
        $status = $_GET['status'] ?? null;
        $keyword = $_GET['keyword'] ?? null;

        $orders = $orderModel->getAllAdmin($status, $keyword);

        $this->view('admin/orders/index', [
            'pageTitle'  => 'Quản Lý Đơn Đặt Hàng',
            'activeMenu' => 'orders',
            'orders'     => $orders,
            'curStatus'  => $status,
            'keyword'    => $keyword
        ], 'admin');
    }

    public function orderDetail(int $id = 0): void {
        $id = $id > 0 ? $id : (int)($_GET['id'] ?? 0);
        $orderModel = $this->model('Order');
        $order = $orderModel->getById($id);

        if (!$order) {
            $this->setFlash('danger', 'Không tìm thấy đơn hàng.');
            $this->redirect('admin/orders');
        }

        $items = $orderModel->getItems($id);

        $this->view('admin/orders/detail', [
            'pageTitle'  => 'Chi Tiết Đơn Hàng #' . $order['order_code'],
            'activeMenu' => 'orders',
            'order'      => $order,
            'items'      => $items
        ], 'admin');
    }

    public function updateOrderStatus(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $orderId = (int)($_POST['order_id'] ?? 0);
            $orderStatus = $_POST['order_status'] ?? 'pending';
            $paymentStatus = $_POST['payment_status'] ?? 'pending';

            if ($orderId > 0) {
                $orderModel = $this->model('Order');
                $orderModel->updateStatus($orderId, $orderStatus, $paymentStatus);
                $this->setFlash('success', 'Cập nhật trạng thái đơn hàng thành công!');
                $this->redirect('admin/orders/detail?id=' . $orderId);
            }
        }
        $this->redirect('admin/orders');
    }

    // ====================================================================
    // 6. QUẢN LÝ MÃ GIẢM GIÁ (VOUCHERS CRUD)
    // ====================================================================
    public function vouchers(): void {
        $voucherModel = $this->model('Voucher');
        $vouchers = $voucherModel->getAll();

        $this->view('admin/vouchers/index', [
            'pageTitle'  => 'Quản Lý Mã Giảm Giá Voucher',
            'activeMenu' => 'vouchers',
            'vouchers'   => $vouchers
        ], 'admin');
    }

    public function createVoucher(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $code = strtoupper(trim($_POST['code'] ?? ''));
            $discountType = $_POST['discount_type'] ?? 'percent';
            $discountValue = floatval($_POST['discount_value'] ?? 0);
            $minOrder = floatval($_POST['min_order_amount'] ?? 0);
            $limit = intval($_POST['usage_limit'] ?? 100);
            $start = $_POST['start_date'] ?? date('Y-m-d H:i:s');
            $end = $_POST['end_date'] ?? date('Y-m-d H:i:s', strtotime('+30 days'));

            $voucherModel = $this->model('Voucher');
            $voucherModel->create([
                'code'             => $code,
                'discount_type'    => $discountType,
                'discount_value'   => $discountValue,
                'min_order_amount' => $minOrder,
                'usage_limit'      => $limit,
                'start_date'       => $start,
                'end_date'         => $end,
                'status'           => 1
            ]);

            $this->setFlash('success', "Thêm mã giảm giá '{$code}' thành công!");
        }
        $this->redirect('admin/vouchers');
    }

    public function deleteVoucher(): void {
        $id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
        if ($id > 0) {
            $voucherModel = $this->model('Voucher');
            $voucherModel->delete($id);
            $this->setFlash('success', 'Đã xóa mã giảm giá.');
        }
        $this->redirect('admin/vouchers');
    }

    // ====================================================================
    // 7. QUẢN LÝ FLASH SALE
    // ====================================================================
    public function flashSales(): void {
        $flashSaleModel = $this->model('FlashSale');
        $productModel = $this->model('Product');

        $sales = $flashSaleModel->getAllAdmin();
        $products = $productModel->getAllAdmin();

        $this->view('admin/flash_sales/index', [
            'pageTitle'  => 'Quản Lý Giờ Vàng Flash Sale',
            'activeMenu' => 'flash_sales',
            'sales'      => $sales,
            'products'   => $products
        ], 'admin');
    }

    public function createFlashSale(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = trim($_POST['title'] ?? '');
            $start = $_POST['start_time'] ?? date('Y-m-d H:i:s');
            $end = $_POST['end_time'] ?? date('Y-m-d H:i:s', strtotime('+24 hours'));
            $productId = (int)($_POST['product_id'] ?? 0);
            $salePrice = floatval($_POST['sale_price'] ?? 0);
            $saleQuantity = intval($_POST['sale_quantity'] ?? 10);

            $flashSaleModel = $this->model('FlashSale');
            $saleId = $flashSaleModel->create([
                'title'      => $title,
                'start_time' => $start,
                'end_time'   => $end,
                'status'     => 1
            ]);

            if ($saleId > 0 && $productId > 0 && $salePrice > 0) {
                $flashSaleModel->addItem($saleId, $productId, $salePrice, $saleQuantity);
            }

            $this->setFlash('success', "Khởi tạo đợt Flash Sale '{$title}' thành công!");
        }
        $this->redirect('admin/flash-sales');
    }

    public function deleteFlashSale(): void {
        $id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
        if ($id > 0) {
            $flashSaleModel = $this->model('FlashSale');
            $flashSaleModel->delete($id);
            $this->setFlash('success', 'Đã xóa chiến dịch Flash Sale.');
        }
        $this->redirect('admin/flash-sales');
    }

    // ====================================================================
    // 8. QUẢN LÝ BANNERS
    // ====================================================================
    public function banners(): void {
        $bannerModel = $this->model('Banner');
        $banners = $bannerModel->getAllAdmin();

        $this->view('admin/banners/index', [
            'pageTitle'  => 'Quản Lý Banner Trình Chiếu',
            'activeMenu' => 'banners',
            'banners'    => $banners
        ], 'admin');
    }

    public function createBanner(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = trim($_POST['title'] ?? '');
            $subtitle = trim($_POST['subtitle'] ?? '');
            $imageUrl = trim($_POST['image_url'] ?? '');
            $linkUrl = trim($_POST['link_url'] ?? '');
            $sortOrder = intval($_POST['sort_order'] ?? 0);

            $bannerModel = $this->model('Banner');
            $bannerModel->create([
                'title'      => $title,
                'subtitle'   => $subtitle ?: null,
                'image_url'  => $imageUrl,
                'link_url'   => $linkUrl ?: null,
                'sort_order' => $sortOrder,
                'status'     => 1
            ]);

            $this->setFlash('success', "Thêm banner '{$title}' thành công!");
        }
        $this->redirect('admin/banners');
    }

    public function deleteBanner(): void {
        $id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
        if ($id > 0) {
            $bannerModel = $this->model('Banner');
            $bannerModel->delete($id);
            $this->setFlash('success', 'Đã xóa banner.');
        }
        $this->redirect('admin/banners');
    }

    // ====================================================================
    // 9. QUẢN LÝ TIN TỨC (NEWS CRUD)
    // ====================================================================
    public function news(): void {
        $newsModel = $this->model('News');
        $newsList = $newsModel->getAllAdmin();

        $this->view('admin/news/index', [
            'pageTitle'  => 'Quản Lý Tin Tức & Cẩm Nang',
            'activeMenu' => 'news',
            'newsList'   => $newsList
        ], 'admin');
    }

    public function createNews(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = trim($_POST['title'] ?? '');
            $summary = trim($_POST['summary'] ?? '');
            $content = trim($_POST['content'] ?? '');
            $thumbnail = trim($_POST['thumbnail'] ?? 'default_news.jpg');
            $slug = $this->createSlug($title) . '-' . time();

            $newsModel = $this->model('News');
            $newsModel->create([
                'title'     => $title,
                'slug'      => $slug,
                'summary'   => $summary,
                'content'   => $content,
                'thumbnail' => $thumbnail,
                'author_id' => $_SESSION['user']['id'] ?? 1
            ]);

            $this->setFlash('success', "Đăng bài viết '{$title}' thành công!");
            $this->redirect('admin/news');
        }

        $this->view('admin/news/create', [
            'pageTitle'  => 'Viết Bài Tin Tức Mới',
            'activeMenu' => 'news_create'
        ], 'admin');
    }

    public function editNews(int $id = 0): void {
        $id = $id > 0 ? $id : (int)($_GET['id'] ?? 0);
        $newsModel = $this->model('News');
        $news = $newsModel->getById($id);

        if (!$news) {
            $this->setFlash('danger', 'Không tìm thấy bài viết.');
            $this->redirect('admin/news');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = trim($_POST['title'] ?? '');
            $summary = trim($_POST['summary'] ?? '');
            $content = trim($_POST['content'] ?? '');
            $thumbnail = trim($_POST['thumbnail'] ?? $news['thumbnail']);
            $slug = $this->createSlug($title) . '-' . $id;

            $newsModel->update($id, [
                'title'     => $title,
                'slug'      => $slug,
                'summary'   => $summary,
                'content'   => $content,
                'thumbnail' => $thumbnail
            ]);

            $this->setFlash('success', "Cập nhật bài viết '{$title}' thành công!");
            $this->redirect('admin/news');
        }

        $this->view('admin/news/edit', [
            'pageTitle'  => 'Chỉnh Sửa Bài Viết',
            'activeMenu' => 'news_edit',
            'news'       => $news
        ], 'admin');
    }

    public function deleteNews(): void {
        $id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
        if ($id > 0) {
            $newsModel = $this->model('News');
            $newsModel->delete($id);
            $this->setFlash('success', 'Đã xóa bài viết.');
        }
        $this->redirect('admin/news');
    }

    // ====================================================================
    // 10. QUẢN LÝ NGƯỜI DÙNG (USERS)
    // ====================================================================
    public function users(): void {
        $userModel = $this->model('User');
        $users = $userModel->getAll();

        $this->view('admin/users/index', [
            'pageTitle'  => 'Quản Lý Người Dùng & Phân Quyền',
            'activeMenu' => 'users',
            'users'      => $users
        ], 'admin');
    }

    public function updateUserRole(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int)($_POST['id'] ?? 0);
            $role = $_POST['role'] === 'admin' ? 'admin' : 'customer';
            if ($id > 0) {
                $userModel = $this->model('User');
                $userModel->updateRole($id, $role);
                $this->setFlash('success', 'Đã cập nhật vai trò người dùng thành công.');
            }
        }
        $this->redirect('admin/users');
    }

    public function toggleUserStatus(): void {
        $id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
        if ($id > 0) {
            $userModel = $this->model('User');
            $userModel->toggleStatus($id);
            $this->setFlash('success', 'Đã đổi trạng thái tài khoản.');
        }
        $this->redirect('admin/users');
    }

    /**
     * Xóa mềm tài khoản người dùng (isDelete = 1). Không thể xóa Quản trị viên (isAdmin = 1)
     */
    public function deleteUser(): void {
        $id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
        if ($id > 0) {
            $userModel = $this->model('User');
            if ($userModel->softDelete($id)) {
                $this->setFlash('success', 'Đã chuyển tài khoản người dùng sang trạng thái Đã Xóa.');
            } else {
                $this->setFlash('error', 'Không thể xóa tài khoản Quản trị viên (isAdmin) hoặc tài khoản gốc của hệ thống!');
            }
        }
        $this->redirect('admin/users');
    }

    /**
     * Khôi phục tài khoản người dùng đã xóa mềm
     */
    public function restoreUser(): void {
        $id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
        if ($id > 0) {
            $userModel = $this->model('User');
            $userModel->restore($id);
            $this->setFlash('success', 'Đã khôi phục tài khoản người dùng thành công.');
        }
        $this->redirect('admin/users');
    }

    // ====================================================================
    // HELPER: HÀM TẠO SLUG TIẾNG VIỆT CHUẨN
    // ====================================================================
    private function createSlug(string $str): string {
        $str = trim(mb_strtolower($str, 'UTF-8'));
        $search = [
            '#(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)#iu',
            '#(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)#iu',
            '#(ì|í|ị|ỉ|ĩ)#iu',
            '#(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)#iu',
            '#(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)#iu',
            '#(ỳ|ý|ỵ|ỷ|ỹ)#iu',
            '#(đ)#iu',
            '#[^a-z0-9\s-]#i',
            '#[\s-]+#'
        ];
        $replace = ['a', 'e', 'i', 'o', 'u', 'y', 'd', '', '-'];
        $slug = preg_replace($search, $replace, $str);
        return trim($slug, '-');
    }
}
