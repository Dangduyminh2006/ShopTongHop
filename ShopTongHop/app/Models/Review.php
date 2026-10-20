<?php
/**
 * ShopXpress - Review Model
 * Quản lý đánh giá và nhận xét sản phẩm
 */

class Review extends Model {

    /**
     * Lấy danh sách đánh giá của sản phẩm kèm thông tin người gửi
     */
    public function getByProductId(int $productId): array {
        $sql = "SELECT r.*, u.fullname, u.username 
                FROM reviews r 
                LEFT JOIN users u ON r.user_id = u.id 
                WHERE r.product_id = ? 
                ORDER BY r.id DESC";
        return $this->fetchAll($sql, [$productId]);
    }

    /**
     * Lấy thống kê đánh giá (Điểm trung bình và số lượng theo từng sao)
     */
    public function getStatsByProductId(int $productId): array {
        $sql = "SELECT 
                    COUNT(*) AS total_reviews,
                    AVG(rating) AS avg_rating,
                    SUM(CASE WHEN rating = 5 THEN 1 ELSE 0 END) AS star_5,
                    SUM(CASE WHEN rating = 4 THEN 1 ELSE 0 END) AS star_4,
                    SUM(CASE WHEN rating = 3 THEN 1 ELSE 0 END) AS star_3,
                    SUM(CASE WHEN rating = 2 THEN 1 ELSE 0 END) AS star_2,
                    SUM(CASE WHEN rating = 1 THEN 1 ELSE 0 END) AS star_1
                FROM reviews 
                WHERE product_id = ?";
        $row = $this->fetch($sql, [$productId]);

        return [
            'total'      => (int)($row['total_reviews'] ?? 0),
            'avg_rating' => round((float)($row['avg_rating'] ?? 5.0), 1),
            'star_5'     => (int)($row['star_5'] ?? 0),
            'star_4'     => (int)($row['star_4'] ?? 0),
            'star_3'     => (int)($row['star_3'] ?? 0),
            'star_2'     => (int)($row['star_2'] ?? 0),
            'star_1'     => (int)($row['star_1'] ?? 0),
        ];
    }

    /**
     * Thêm đánh giá mới
     */
    public function create(array $data): int {
        $sql = "INSERT INTO reviews (product_id, user_id, rating, comment) VALUES (?, ?, ?, ?)";
        $this->execute($sql, [
            $data['product_id'],
            $data['user_id'],
            max(1, min(5, (int)$data['rating'])),
            trim($data['comment'])
        ]);
        return (int)$this->lastInsertId();
    }
}
