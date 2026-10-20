<?php
/**
 * ShopXpress - FlashSale Model
 * Quản lý chiến dịch Flash Sale và các sản phẩm giảm giá chớp nhoáng
 */

class FlashSale extends Model {

    /**
     * Lấy chiến dịch Flash Sale đang diễn ra
     */
    public function getActive(): ?array {
        $sql = "SELECT * FROM flash_sales 
                WHERE status = 1 AND NOW() BETWEEN start_time AND end_time 
                ORDER BY id DESC LIMIT 1";
        $sale = $this->fetch($sql);
        if ($sale) {
            $sale['items'] = $this->getItems((int)$sale['id']);
        }
        return $sale;
    }

    /**
     * Lấy toàn bộ chiến dịch Flash Sale cho trang quản trị Admin
     */
    public function getAllAdmin(): array {
        $sql = "SELECT fs.*, 
                       (SELECT COUNT(*) FROM flash_sale_items WHERE flash_sale_id = fs.id) AS item_count 
                FROM flash_sales fs 
                ORDER BY fs.id DESC";
        $sales = $this->fetchAll($sql);
        foreach ($sales as &$sale) {
            $sale['items'] = $this->getItems((int)$sale['id']);
        }
        return $sales;
    }

    /**
     * Lấy chi tiết chiến dịch Flash Sale theo ID
     */
    public function getById(int $id): ?array {
        $sql = "SELECT * FROM flash_sales WHERE id = ?";
        $sale = $this->fetch($sql, [$id]);
        if ($sale) {
            $sale['items'] = $this->getItems($id);
        }
        return $sale;
    }

    /**
     * Tạo chiến dịch Flash Sale mới
     */
    public function create(array $data): int {
        $sql = "INSERT INTO flash_sales (title, start_time, end_time, status) VALUES (?, ?, ?, ?)";
        $this->execute($sql, [
            $data['title'],
            $data['start_time'],
            $data['end_time'],
            $data['status'] ?? 1
        ]);
        return (int)$this->lastInsertId();
    }

    /**
     * Xóa chiến dịch Flash Sale
     */
    public function delete(int $id): bool {
        $sql = "DELETE FROM flash_sales WHERE id = ?";
        return $this->execute($sql, [$id]);
    }

    /**
     * Lấy danh sách sản phẩm nằm trong chiến dịch Flash Sale
     */
    public function getItems(int $flashSaleId): array {
        $sql = "SELECT fsi.*, p.name, p.slug, p.price AS original_price, p.thumbnail, p.stock_quantity,
                       ROUND(((p.price - fsi.sale_price) / p.price) * 100) AS discount_percent
                FROM flash_sale_items fsi
                JOIN products p ON fsi.product_id = p.id
                WHERE fsi.flash_sale_id = ?
                ORDER BY fsi.id ASC";
        return $this->fetchAll($sql, [$flashSaleId]);
    }

    /**
     * Thêm sản phẩm vào Flash Sale
     */
    public function addItem(int $flashSaleId, int $productId, float $salePrice, int $saleQuantity = 10): bool {
        $sql = "INSERT INTO flash_sale_items (flash_sale_id, product_id, sale_price, sale_quantity, sold_quantity) 
                VALUES (?, ?, ?, ?, 0)";
        return $this->execute($sql, [$flashSaleId, $productId, $salePrice, $saleQuantity]);
    }

    /**
     * Xóa sản phẩm khỏi Flash Sale
     */
    public function deleteItem(int $itemId): bool {
        $sql = "DELETE FROM flash_sale_items WHERE id = ?";
        return $this->execute($sql, [$itemId]);
    }
}
