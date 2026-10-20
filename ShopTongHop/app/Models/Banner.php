<?php
/**
 * ShopXpress - Banner Model
 * Quản lý banner trình chiếu trang chủ
 */

class Banner extends Model {

    /**
     * Lấy danh sách banner kích hoạt để hiển thị slider
     */
    public function getActive(): array {
        $sql = "SELECT * FROM banners WHERE status = 1 ORDER BY sort_order ASC, id ASC";
        return $this->fetchAll($sql);
    }

    /**
     * Lấy toàn bộ banner cho trang quản trị Admin
     */
    public function getAllAdmin(): array {
        $sql = "SELECT * FROM banners ORDER BY sort_order ASC, id DESC";
        return $this->fetchAll($sql);
    }

    /**
     * Lấy chi tiết banner theo ID
     */
    public function getById(int $id): ?array {
        $sql = "SELECT * FROM banners WHERE id = ?";
        return $this->fetch($sql, [$id]);
    }

    /**
     * Tạo banner mới
     */
    public function create(array $data): int {
        $sql = "INSERT INTO banners (title, subtitle, image_url, link_url, sort_order, status) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $imageUrl = $data['image_url'] ?? $data['image'] ?? '';
        $linkUrl = $data['link_url'] ?? $data['link'] ?? null;
        $sortOrder = $data['sort_order'] ?? $data['display_order'] ?? 0;

        $this->execute($sql, [
            $data['title'],
            $data['subtitle'] ?? null,
            $imageUrl,
            $linkUrl,
            $sortOrder,
            $data['status'] ?? 1
        ]);
        return (int)$this->lastInsertId();
    }

    /**
     * Cập nhật banner
     */
    public function update(int $id, array $data): bool {
        $sql = "UPDATE banners SET title = ?, subtitle = ?, image_url = ?, link_url = ?, sort_order = ?, status = ? WHERE id = ?";
        return $this->execute($sql, [
            $data['title'],
            $data['subtitle'] ?? null,
            $data['image_url'],
            $data['link_url'] ?? null,
            $data['sort_order'] ?? 0,
            $data['status'] ?? 1,
            $id
        ]);
    }

    /**
     * Xóa banner
     */
    public function delete(int $id): bool {
        $sql = "DELETE FROM banners WHERE id = ?";
        return $this->execute($sql, [$id]);
    }
}
