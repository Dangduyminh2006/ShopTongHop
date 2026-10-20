<?php
/**
 * ShopXpress - Brand Model
 * Quản lý thương hiệu phân phối chính hãng
 */

class Brand extends Model {

    /**
     * Lấy toàn bộ thương hiệu kèm số lượng sản phẩm
     */
    public function getAll(): array {
        $sql = "SELECT b.*, 
                       (SELECT COUNT(*) FROM products WHERE brand_id = b.id) AS product_count 
                FROM brands b 
                ORDER BY b.name ASC";
        return $this->fetchAll($sql);
    }

    /**
     * Lấy danh sách thương hiệu đang hoạt động
     */
    public function getActive(): array {
        $sql = "SELECT * FROM brands WHERE status = 1 ORDER BY name ASC";
        return $this->fetchAll($sql);
    }

    /**
     * Lấy chi tiết thương hiệu theo ID
     */
    public function getById(int $id): ?array {
        $sql = "SELECT * FROM brands WHERE id = ?";
        return $this->fetch($sql, [$id]);
    }

    /**
     * Lấy chi tiết thương hiệu theo Slug
     */
    public function getBySlug(string $slug): ?array {
        $sql = "SELECT * FROM brands WHERE slug = ? AND status = 1";
        return $this->fetch($sql, [$slug]);
    }

    /**
     * Tạo thương hiệu mới
     */
    public function create(array $data): int {
        $sql = "INSERT INTO brands (name, slug, logo, status) VALUES (?, ?, ?, ?)";
        $this->execute($sql, [
            $data['name'],
            $data['slug'],
            $data['logo'] ?? null,
            $data['status'] ?? 1
        ]);
        return (int)$this->lastInsertId();
    }

    /**
     * Cập nhật thương hiệu
     */
    public function update(int $id, array $data): bool {
        $sql = "UPDATE brands SET name = ?, slug = ?, logo = ?, status = ? WHERE id = ?";
        return $this->execute($sql, [
            $data['name'],
            $data['slug'],
            $data['logo'] ?? null,
            $data['status'] ?? 1,
            $id
        ]);
    }

    /**
     * Xóa thương hiệu
     */
    public function delete(int $id): bool {
        // Gán null cho các sản phẩm thuộc thương hiệu này
        $this->execute("UPDATE products SET brand_id = NULL WHERE brand_id = ?", [$id]);
        $sql = "DELETE FROM brands WHERE id = ?";
        return $this->execute($sql, [$id]);
    }

    /**
     * Đếm tổng số thương hiệu
     */
    public function countAll(): int {
        $sql = "SELECT COUNT(*) AS total FROM brands";
        $row = $this->fetch($sql);
        return (int)($row['total'] ?? 0);
    }
}
