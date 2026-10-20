<?php
/**
 * ShopXpress - News Model
 * Quản lý bài viết tin tức và cẩm nang mua sắm
 */

class News extends Model {

    /**
     * Lấy bài viết tin tức mới nhất
     */
    public function getLatest(int $limit = 3): array {
        $sql = "SELECT n.*, u.fullname AS author_name 
                FROM news n 
                LEFT JOIN users u ON n.author_id = u.id 
                ORDER BY n.id DESC 
                LIMIT " . (int)$limit;
        return $this->fetchAll($sql);
    }

    /**
     * Lấy toàn bộ bài viết tin tức cho trang quản trị Admin
     */
    public function getAllAdmin(): array {
        $sql = "SELECT n.*, u.fullname AS author_name 
                FROM news n 
                LEFT JOIN users u ON n.author_id = u.id 
                ORDER BY n.id DESC";
        return $this->fetchAll($sql);
    }

    /**
     * Lấy chi tiết bài viết theo ID
     */
    public function getById(int $id): ?array {
        $sql = "SELECT n.*, u.fullname AS author_name 
                FROM news n 
                LEFT JOIN users u ON n.author_id = u.id 
                WHERE n.id = ?";
        return $this->fetch($sql, [$id]);
    }

    /**
     * Lấy chi tiết bài viết theo Slug
     */
    public function getBySlug(string $slug): ?array {
        $sql = "SELECT n.*, u.fullname AS author_name 
                FROM news n 
                LEFT JOIN users u ON n.author_id = u.id 
                WHERE n.slug = ?";
        return $this->fetch($sql, [$slug]);
    }

    /**
     * Tạo bài viết tin tức mới
     */
    public function create(array $data): int {
        $sql = "INSERT INTO news (title, slug, summary, content, thumbnail, author_id, view_count) 
                VALUES (?, ?, ?, ?, ?, ?, 0)";
        $this->execute($sql, [
            $data['title'],
            $data['slug'],
            $data['summary'] ?? null,
            $data['content'],
            $data['thumbnail'],
            $data['author_id']
        ]);
        return (int)$this->lastInsertId();
    }

    /**
     * Cập nhật bài viết
     */
    public function update(int $id, array $data): bool {
        $sql = "UPDATE news SET title = ?, slug = ?, summary = ?, content = ?, thumbnail = ? WHERE id = ?";
        return $this->execute($sql, [
            $data['title'],
            $data['slug'],
            $data['summary'] ?? null,
            $data['content'],
            $data['thumbnail'],
            $id
        ]);
    }

    /**
     * Xóa bài viết
     */
    public function delete(int $id): bool {
        $sql = "DELETE FROM news WHERE id = ?";
        return $this->execute($sql, [$id]);
    }

    /**
     * Đếm tổng số bài viết
     */
    public function countAll(): int {
        $sql = "SELECT COUNT(*) AS total FROM news";
        $row = $this->fetch($sql);
        return (int)($row['total'] ?? 0);
    }
}
