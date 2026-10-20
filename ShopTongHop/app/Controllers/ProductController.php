<?php
/**
 * ShopXpress - Product Controller
 * Điều phối hiển thị Trang Catalog Bộ Lọc và Trang Chi Tiết Sản Phẩm
 */

class ProductController extends Controller {

    /**
     * Trang Catalog Danh sách Sản phẩm kết hợp Bộ lọc đa năng & Phân trang
     */
    public function index(): void {
        $productModel = $this->model('Product');
        $categoryModel = $this->model('Category');
        $brandModel = $this->model('Brand');

        // 1. Thu thập các tham số lọc từ GET request
        $keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : (isset($_GET['q']) ? trim($_GET['q']) : '');
        $categoryParam = $_GET['category'] ?? ($_GET['category_id'] ?? null);
        $brandParam = $_GET['brand'] ?? ($_GET['brand_id'] ?? null);
        $minPrice = isset($_GET['min_price']) && is_numeric($_GET['min_price']) ? (float)$_GET['min_price'] : null;
        $maxPrice = isset($_GET['max_price']) && is_numeric($_GET['max_price']) ? (float)$_GET['max_price'] : null;
        $sort = $_GET['sort'] ?? 'newest';
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $perPage = 12;

        // Xử lý danh mục: Có thể là ID hoặc Slug
        $categoryId = null;
        $categoryListIds = null;
        $selectedCategory = null;

        if (!empty($categoryParam)) {
            if (is_numeric($categoryParam)) {
                $selectedCategory = $categoryModel->getById((int)$categoryParam);
            } else {
                $selectedCategory = $categoryModel->getBySlug($categoryParam);
            }

            if ($selectedCategory) {
                $categoryId = (int)$selectedCategory['id'];
                // Nếu là danh mục cha, lấy toàn bộ danh mục con để lọc gộp
                $children = $categoryModel->getChildren($categoryId);
                if (!empty($children)) {
                    $categoryListIds = array_merge([$categoryId], array_column($children, 'id'));
                } else {
                    $categoryListIds = [$categoryId];
                }
            }
        }

        // Xử lý thương hiệu: Có thể là ID hoặc Slug
        $brandId = null;
        $selectedBrand = null;
        if (!empty($brandParam)) {
            if (is_numeric($brandParam)) {
                $selectedBrand = $brandModel->getById((int)$brandParam);
            } else {
                $selectedBrand = $brandModel->getBySlug($brandParam);
            }
            if ($selectedBrand) {
                $brandId = (int)$selectedBrand['id'];
            }
        }

        // Đóng gói mảng điều kiện lọc
        $filters = [
            'keyword'     => $keyword,
            'category_id' => $categoryListIds ?? $categoryId,
            'brand_id'    => $brandId,
            'min_price'   => $minPrice,
            'max_price'   => $maxPrice,
            'sort'        => $sort
        ];

        // Thực thi lọc sản phẩm
        $result = $productModel->filterCatalog($filters, $page, $perPage);

        // Lấy dữ liệu hỗ trợ bộ lọc sidebar
        $categories = $categoryModel->getTree();
        $brands = $brandModel->getAll();
        $priceRange = $productModel->getPriceRange();

        $pageTitle = 'Tất Cả Sản Phẩm';
        if ($selectedCategory) {
            $pageTitle = $selectedCategory['name'];
        } elseif ($selectedBrand) {
            $pageTitle = 'Thương hiệu ' . $selectedBrand['name'];
        } elseif (!empty($keyword)) {
            $pageTitle = 'Kết quả tìm kiếm cho "' . $keyword . '"';
        }

        $this->view('product/index', [
            'pageTitle'        => $pageTitle . ' | ' . APP_NAME,
            'products'         => $result['items'],
            'total'            => $result['total'],
            'page'             => $result['page'],
            'perPage'          => $result['perPage'],
            'totalPages'       => $result['totalPages'],
            'sort'             => $sort,
            'categories'       => $categories,
            'brands'           => $brands,
            'priceRange'       => $priceRange,
            'selectedCategory' => $selectedCategory,
            'selectedBrand'    => $selectedBrand,
            'keyword'          => $keyword,
            'minPrice'         => $minPrice,
            'maxPrice'         => $maxPrice
        ]);
    }

    /**
     * Trang Chi Tiết Sản Phẩm (Product Gallery, Buy Box, Dynamic Specs JSON, Reviews, Related Products)
     */
    public function detail(?string $identifier = null): void {
        $productModel = $this->model('Product');
        $reviewModel = $this->model('Review');

        $idOrSlug = $identifier ?? ($_GET['slug'] ?? ($_GET['id'] ?? null));

        if (empty($idOrSlug)) {
            $this->redirect('products');
            return;
        }

        // Tìm sản phẩm theo Slug hoặc theo ID
        $product = null;
        if (is_numeric($idOrSlug)) {
            $product = $productModel->getById((int)$idOrSlug);
        } else {
            $product = $productModel->getBySlug($idOrSlug);
            if (!$product && is_numeric($idOrSlug)) {
                $product = $productModel->getById((int)$idOrSlug);
            }
        }

        if (!$product) {
            $this->setFlash('error', 'Sản phẩm bạn đang tìm kiếm không tồn tại hoặc đã ngừng kinh doanh.');
            $this->redirect('products');
            return;
        }

        // Tăng số lượt xem của sản phẩm
        $productModel->incrementViews((int)$product['id']);

        // 1. Thư viện ảnh sản phẩm (Product Gallery)
        $gallery = $productModel->getImages((int)$product['id']);

        // 2. Sản phẩm liên quan (cùng danh mục)
        $relatedProducts = $productModel->getRelated((int)$product['category_id'], (int)$product['id'], 4);

        // 3. Đánh giá và xếp hạng sao
        $reviews = $reviewModel->getByProductId((int)$product['id']);
        $reviewStats = $reviewModel->getStatsByProductId((int)$product['id']);

        // 4. Giải mã thông số kỹ thuật chi tiết từ trường JSON
        $specifications = [];
        if (!empty($product['specifications'])) {
            $decoded = json_decode($product['specifications'], true);
            if (is_array($decoded)) {
                $specifications = $decoded;
            }
        }

        $this->view('product/detail', [
            'pageTitle'       => $product['name'] . ' | ' . APP_NAME,
            'product'         => $product,
            'gallery'         => $gallery,
            'relatedProducts' => $relatedProducts,
            'reviews'         => $reviews,
            'reviewStats'     => $reviewStats,
            'specifications'  => $specifications
        ]);
    }
}
