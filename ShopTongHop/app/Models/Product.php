<?php
/**
 * ShopXpress - Product Model
 * Xử lý dữ liệu sản phẩm, thư viện ảnh và thông số kỹ thuật JSON
 */

class Product extends Model {

    /**
     * Lấy danh sách sản phẩm nổi bật hiển thị trang chủ
     */
    public function getFeatured(int $limit = 8): array {
        $sql = "SELECT p.*, c.name AS category_name, b.name AS brand_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                LEFT JOIN brands b ON p.brand_id = b.id 
                WHERE p.status = 1 AND p.is_featured = 1 
                ORDER BY p.id DESC 
                LIMIT " . (int)$limit;
        return $this->fetchAll($sql);
    }

    /**
     * Lấy danh sách sản phẩm mới về
     */
    public function getNewArrivals(int $limit = 8): array {
        $sql = "SELECT p.*, c.name AS category_name, b.name AS brand_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                LEFT JOIN brands b ON p.brand_id = b.id 
                WHERE p.status = 1 AND p.is_new = 1 
                ORDER BY p.id DESC 
                LIMIT " . (int)$limit;
        return $this->fetchAll($sql);
    }

    /**
     * Lấy toàn bộ sản phẩm cho trang quản trị Admin (hỗ trợ tìm kiếm & lọc)
     */
    public function getAllAdmin(?string $keyword = null, ?int $categoryId = null, ?int $brandId = null): array {
        $sql = "SELECT p.*, c.name AS category_name, b.name AS brand_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                LEFT JOIN brands b ON p.brand_id = b.id 
                WHERE 1=1";
        $params = [];

        if (!empty($keyword)) {
            $sql .= " AND (p.name LIKE ? OR p.sku LIKE ?)";
            $params[] = "%$keyword%";
            $params[] = "%$keyword%";
        }
        if (!empty($categoryId)) {
            $sql .= " AND p.category_id = ?";
            $params[] = $categoryId;
        }
        if (!empty($brandId)) {
            $sql .= " AND p.brand_id = ?";
            $params[] = $brandId;
        }

        $sql .= " ORDER BY p.id DESC";
        return $this->fetchAll($sql, $params);
    }

    /**
     * Lấy thông tin chi tiết một sản phẩm theo ID
     */
    public function getById(int $id): ?array {
        $sql = "SELECT p.*, c.name AS category_name, c.slug AS category_slug, b.name AS brand_name, b.slug AS brand_slug 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                LEFT JOIN brands b ON p.brand_id = b.id 
                WHERE p.id = ?";
        return $this->fetch($sql, [$id]);
    }

    /**
     * Lấy thông tin chi tiết một sản phẩm theo Slug
     */
    public function getBySlug(string $slug): ?array {
        $sql = "SELECT p.*, c.name AS category_name, c.slug AS category_slug, b.name AS brand_name, b.slug AS brand_slug 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                LEFT JOIN brands b ON p.brand_id = b.id 
                WHERE p.slug = ? AND p.status = 1";
        return $this->fetch($sql, [$slug]);
    }

    /**
     * Tạo sản phẩm mới
     */
    public function create(array $data): int {
        $sql = "INSERT INTO products (
                    category_id, brand_id, name, slug, sku, price, discount_percent, 
                    stock_quantity, thumbnail, summary, description, specifications, 
                    is_featured, is_new, status
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $this->execute($sql, [
            $data['category_id'],
            $data['brand_id'] ?? null,
            $data['name'],
            $data['slug'],
            $data['sku'],
            $data['price'],
            $data['discount_percent'] ?? 0,
            $data['stock_quantity'] ?? 0,
            $data['thumbnail'],
            $data['summary'] ?? null,
            $data['description'] ?? null,
            $data['specifications'] ?? null,
            $data['is_featured'] ?? 0,
            $data['is_new'] ?? 0,
            $data['status'] ?? 1
        ]);

        return (int)$this->lastInsertId();
    }

    /**
     * Cập nhật thông tin sản phẩm
     */
    public function update(int $id, array $data): bool {
        $sql = "UPDATE products SET 
                    category_id = ?, brand_id = ?, name = ?, slug = ?, sku = ?, 
                    price = ?, discount_percent = ?, stock_quantity = ?, thumbnail = ?, 
                    summary = ?, description = ?, specifications = ?, is_featured = ?, 
                    is_new = ?, status = ? 
                WHERE id = ?";
        
        return $this->execute($sql, [
            $data['category_id'],
            $data['brand_id'] ?? null,
            $data['name'],
            $data['slug'],
            $data['sku'],
            $data['price'],
            $data['discount_percent'] ?? 0,
            $data['stock_quantity'] ?? 0,
            $data['thumbnail'],
            $data['summary'] ?? null,
            $data['description'] ?? null,
            $data['specifications'] ?? null,
            $data['is_featured'] ?? 0,
            $data['is_new'] ?? 0,
            $data['status'] ?? 1,
            $id
        ]);
    }

    /**
     * Xóa sản phẩm
     */
    public function delete(int $id): bool {
        $sql = "DELETE FROM products WHERE id = ?";
        return $this->execute($sql, [$id]);
    }

    /**
     * Đếm tổng số sản phẩm trong hệ thống
     */
    public function countAll(): int {
        $sql = "SELECT COUNT(*) AS total FROM products";
        $row = $this->fetch($sql);
        return (int)($row['total'] ?? 0);
    }

    /**
     * Lấy thư viện nhiều ảnh của sản phẩm
     */
    public function getImages(int $productId): array {
        $sql = "SELECT * FROM product_images WHERE product_id = ? ORDER BY sort_order ASC, id ASC";
        return $this->fetchAll($sql, [$productId]);
    }

    /**
     * Thêm ảnh vào thư viện sản phẩm
     */
    public function addImage(int $productId, string $imageUrl, int $sortOrder = 0): int {
        $sql = "INSERT INTO product_images (product_id, image_url, sort_order) VALUES (?, ?, ?)";
        $this->execute($sql, [$productId, $imageUrl, $sortOrder]);
        return (int)$this->lastInsertId();
    }

    /**
     * Xóa ảnh khỏi thư viện
     */
    public function deleteImage(int $imageId): bool {
        $sql = "DELETE FROM product_images WHERE id = ?";
        return $this->execute($sql, [$imageId]);
    }

    /**
     * Lấy danh sách sản phẩm liên quan (cùng danh mục)
     */
    public function getRelated(int $categoryId, int $excludeId, int $limit = 4): array {
        $sql = "SELECT p.*, c.name AS category_name, b.name AS brand_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                LEFT JOIN brands b ON p.brand_id = b.id 
                WHERE p.status = 1 AND p.category_id = ? AND p.id != ? 
                ORDER BY p.view_count DESC 
                LIMIT " . (int)$limit;
        return $this->fetchAll($sql, [$categoryId, $excludeId]);
    }

    /**
     * Tăng số lượt xem của sản phẩm
     */
    public function incrementViews(int $id): void {
        $sql = "UPDATE products SET view_count = view_count + 1 WHERE id = ?";
        $this->execute($sql, [$id]);
    }

    /**
     * Cập nhật số lượng tồn kho (Hỗ trợ tăng hoặc trừ)
     */
    public function updateStock(int $id, int $quantityDelta): bool {
        $sql = "UPDATE products SET stock_quantity = stock_quantity + ? WHERE id = ? AND stock_quantity + ? >= 0";
        return $this->execute($sql, [$quantityDelta, $id, $quantityDelta]);
    }

    /**
     * Lọc sản phẩm nâng cao cho Trang Catalog (Danh mục, Thương hiệu, Khoảng giá, Từ khóa, Sắp xếp, Phân trang)
     */
    public function filterCatalog(array $filters = [], int $page = 1, int $perPage = 12): array {
        $conditions = ["p.status = 1"];
        $params = [];

        // 1. Lọc theo từ khóa tìm kiếm
        if (!empty($filters['keyword'])) {
            $conditions[] = "(p.name LIKE ? OR p.sku LIKE ? OR p.summary LIKE ?)";
            $kw = '%' . trim($filters['keyword']) . '%';
            $params[] = $kw;
            $params[] = $kw;
            $params[] = $kw;
        }

        // 2. Lọc theo Danh mục (Hỗ trợ 1 ID hoặc mảng ID danh mục con)
        if (!empty($filters['category_id'])) {
            if (is_array($filters['category_id'])) {
                $placeholders = implode(',', array_fill(0, count($filters['category_id']), '?'));
                $conditions[] = "p.category_id IN ($placeholders)";
                foreach ($filters['category_id'] as $cid) {
                    $params[] = (int)$cid;
                }
            } else {
                $conditions[] = "p.category_id = ?";
                $params[] = (int)$filters['category_id'];
            }
        }

        // 3. Lọc theo Thương hiệu (Hỗ trợ 1 hoặc nhiều ID)
        if (!empty($filters['brand_id'])) {
            if (is_array($filters['brand_id'])) {
                $placeholders = implode(',', array_fill(0, count($filters['brand_id']), '?'));
                $conditions[] = "p.brand_id IN ($placeholders)";
                foreach ($filters['brand_id'] as $bid) {
                    $params[] = (int)$bid;
                }
            } else {
                $conditions[] = "p.brand_id = ?";
                $params[] = (int)$filters['brand_id'];
            }
        }

        // 4. Lọc theo khoảng giá (Tính theo giá thực tế sau khi giảm)
        if (isset($filters['min_price']) && is_numeric($filters['min_price']) && $filters['min_price'] > 0) {
            $conditions[] = "(p.price * (1 - p.discount_percent / 100)) >= ?";
            $params[] = (float)$filters['min_price'];
        }
        if (isset($filters['max_price']) && is_numeric($filters['max_price']) && $filters['max_price'] > 0) {
            $conditions[] = "(p.price * (1 - p.discount_percent / 100)) <= ?";
            $params[] = (float)$filters['max_price'];
        }

        // 5. Lọc sản phẩm nổi bật / mới về
        if (!empty($filters['is_featured'])) {
            $conditions[] = "p.is_featured = 1";
        }
        if (!empty($filters['is_new'])) {
            $conditions[] = "p.is_new = 1";
        }

        $whereClause = implode(" AND ", $conditions);

        // Đếm tổng số sản phẩm thỏa mãn điều kiện
        $countSql = "SELECT COUNT(*) AS total 
                     FROM products p 
                     LEFT JOIN categories c ON p.category_id = c.id 
                     LEFT JOIN brands b ON p.brand_id = b.id 
                     WHERE $whereClause";
        $totalRow = $this->fetch($countSql, $params);
        $total = (int)($totalRow['total'] ?? 0);

        // Xác định thứ tự sắp xếp
        $sort = $filters['sort'] ?? 'newest';
        switch ($sort) {
            case 'price_asc':
                $orderBy = "(p.price * (1 - p.discount_percent / 100)) ASC, p.id DESC";
                break;
            case 'price_desc':
                $orderBy = "(p.price * (1 - p.discount_percent / 100)) DESC, p.id DESC";
                break;
            case 'best_selling':
                $orderBy = "p.view_count DESC, p.id DESC";
                break;
            case 'name_asc':
                $orderBy = "p.name ASC";
                break;
            case 'name_desc':
                $orderBy = "p.name DESC";
                break;
            case 'newest':
            default:
                $orderBy = "p.id DESC";
                break;
        }

        // Phân trang
        $page = max(1, $page);
        $perPage = max(1, $perPage);
        $offset = ($page - 1) * $perPage;
        $totalPages = $total > 0 ? (int)ceil($total / $perPage) : 1;

        $querySql = "SELECT p.*, c.name AS category_name, c.slug AS category_slug, 
                            b.name AS brand_name, b.slug AS brand_slug,
                            (p.price * (1 - p.discount_percent / 100)) AS final_price
                     FROM products p 
                     LEFT JOIN categories c ON p.category_id = c.id 
                     LEFT JOIN brands b ON p.brand_id = b.id 
                     WHERE $whereClause 
                     ORDER BY $orderBy 
                     LIMIT $perPage OFFSET $offset";

        $items = $this->fetchAll($querySql, $params);

        return [
            'items'       => $items,
            'total'       => $total,
            'page'        => $page,
            'perPage'     => $perPage,
            'totalPages'  => $totalPages,
            'sort'        => $sort
        ];
    }

    /**
     * Lấy khoảng giá nhỏ nhất và lớn nhất của toàn bộ sản phẩm đang bán
     */
    public function getPriceRange(): array {
        $sql = "SELECT MIN(price * (1 - discount_percent / 100)) AS min_price, 
                       MAX(price * (1 - discount_percent / 100)) AS max_price 
                FROM products 
                WHERE status = 1";
        $row = $this->fetch($sql);
        return [
            'min' => (float)($row['min_price'] ?? 0),
            'max' => (float)($row['max_price'] ?? 50000000)
        ];
    }
}
