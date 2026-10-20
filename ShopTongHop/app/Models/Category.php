<?php
/**
 * ShopXpress - Category Model
 * Xử lý danh mục sản phẩm đa tầng (Cha - Con) và CRUD Admin
 */

class Category extends Model {

    /**
     * Lấy toàn bộ danh mục cho trang Admin (kèm tên danh mục cha)
     */
    public function getAll(): array {
        $sql = "SELECT c.*, p.name AS parent_name, 
                       (SELECT COUNT(*) FROM products WHERE category_id = c.id) AS product_count 
                FROM categories c 
                LEFT JOIN categories p ON c.parent_id = p.id 
                ORDER BY c.parent_id IS NULL DESC, c.display_order ASC, c.id ASC";
        return $this->fetchAll($sql);
    }

    /**
     * Lấy toàn bộ danh mục cha (parent_id IS NULL)
     */
    public function getParents(): array {
        $sql = "SELECT * FROM categories WHERE parent_id IS NULL AND status = 1 ORDER BY display_order ASC, id ASC";
        return $this->fetchAll($sql);
    }

    /**
     * Lấy danh mục con theo ID danh mục cha
     */
    public function getChildren(int $parentId): array {
        $sql = "SELECT * FROM categories WHERE parent_id = ? AND status = 1 ORDER BY display_order ASC, id ASC";
        return $this->fetchAll($sql, [$parentId]);
    }

    /**
     * Lấy toàn bộ cây danh mục (Cha kèm danh sách con)
     */
    public function getTree(): array {
        $parents = $this->getParents();
        foreach ($parents as &$parent) {
            $parent['children'] = $this->getChildren((int)$parent['id']);
        }
        return $parents;
    }

    /**
     * Lấy thông tin danh mục theo Slug
     */
    public function getBySlug(string $slug): ?array {
        $sql = "SELECT * FROM categories WHERE slug = ? AND status = 1";
        return $this->fetch($sql, [$slug]);
    }

    /**
     * Lấy thông tin danh mục theo ID
     */
    public function getById(int $id): ?array {
        $sql = "SELECT * FROM categories WHERE id = ?";
        return $this->fetch($sql, [$id]);
    }

    /**
     * Tạo danh mục mới
     */
    public function create(array $data): int {
        $sql = "INSERT INTO categories (name, slug, parent_id, icon, image, display_order, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $this->execute($sql, [
            $data['name'],
            $data['slug'],
            !empty($data['parent_id']) ? $data['parent_id'] : null,
            $data['icon'] ?? 'fa-folder',
            $data['image'] ?? null,
            $data['display_order'] ?? 0,
            $data['status'] ?? 1
        ]);
        return (int)$this->lastInsertId();
    }

    /**
     * Cập nhật danh mục
     */
    public function update(int $id, array $data): bool {
        $sql = "UPDATE categories SET 
                    name = ?, slug = ?, parent_id = ?, icon = ?, image = ?, 
                    display_order = ?, status = ? 
                WHERE id = ?";
        return $this->execute($sql, [
            $data['name'],
            $data['slug'],
            !empty($data['parent_id']) ? $data['parent_id'] : null,
            $data['icon'] ?? 'fa-folder',
            $data['image'] ?? null,
            $data['display_order'] ?? 0,
            $data['status'] ?? 1,
            $id
        ]);
    }

    /**
     * Xóa danh mục (Chỉ cho phép xóa nếu không còn sản phẩm và danh mục con)
     */
    public function delete(int $id): bool {
        // Cập nhật các danh mục con thành null parent
        $this->execute("UPDATE categories SET parent_id = NULL WHERE parent_id = ?", [$id]);
        $sql = "DELETE FROM categories WHERE id = ?";
        return $this->execute($sql, [$id]);
    }

    /**
     * Đếm tổng số danh mục
     */
    public function countAll(): int {
        $sql = "SELECT COUNT(*) AS total FROM categories";
        $row = $this->fetch($sql);
        return (int)($row['total'] ?? 0);
    }
}
