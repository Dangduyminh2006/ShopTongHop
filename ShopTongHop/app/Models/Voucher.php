<?php
/**
 * ShopXpress - Voucher Model
 * Quản lý mã giảm giá coupon
 */

class Voucher extends Model {

    /**
     * Lấy toàn bộ danh sách mã giảm giá cho Admin
     */
    public function getAll(): array {
        $sql = "SELECT * FROM vouchers ORDER BY id DESC";
        return $this->fetchAll($sql);
    }

    /**
     * Lấy chi tiết voucher theo ID
     */
    public function getById(int $id): ?array {
        $sql = "SELECT * FROM vouchers WHERE id = ?";
        return $this->fetch($sql, [$id]);
    }

    /**
     * Tìm mã voucher hợp lệ theo Code
     */
    public function getValidByCode(string $code, float $orderAmount = 0): ?array {
        $now = date('Y-m-d H:i:s');
        $sql = "SELECT * FROM vouchers 
                WHERE code = ? AND status = 1 
                  AND start_date <= ? AND end_date >= ? 
                  AND used_count < usage_limit 
                  AND min_order_amount <= ?";
        return $this->fetch($sql, [$code, $now, $now, $orderAmount]);
    }

    /**
     * Tạo mã giảm giá mới
     */
    public function create(array $data): int {
        $sql = "INSERT INTO vouchers (
                    code, discount_type, discount_value, min_order_amount, 
                    usage_limit, used_count, start_date, end_date, status
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $this->execute($sql, [
            strtoupper(trim($data['code'])),
            $data['discount_type'] ?? 'percent',
            $data['discount_value'],
            $data['min_order_amount'] ?? 0,
            $data['usage_limit'] ?? 100,
            0,
            $data['start_date'],
            $data['end_date'],
            $data['status'] ?? 1
        ]);
        return (int)$this->lastInsertId();
    }

    /**
     * Cập nhật mã giảm giá
     */
    public function update(int $id, array $data): bool {
        $sql = "UPDATE vouchers SET 
                    code = ?, discount_type = ?, discount_value = ?, min_order_amount = ?, 
                    usage_limit = ?, start_date = ?, end_date = ?, status = ? 
                WHERE id = ?";
        return $this->execute($sql, [
            strtoupper(trim($data['code'])),
            $data['discount_type'] ?? 'percent',
            $data['discount_value'],
            $data['min_order_amount'] ?? 0,
            $data['usage_limit'] ?? 100,
            $data['start_date'],
            $data['end_date'],
            $data['status'] ?? 1,
            $id
        ]);
    }

    /**
     * Tăng số lần sử dụng của voucher
     */
    public function incrementUsed(string $code): bool {
        $sql = "UPDATE vouchers SET used_count = used_count + 1 WHERE code = ?";
        return $this->execute($sql, [$code]);
    }

    /**
     * Xóa voucher
     */
    public function delete(int $id): bool {
        $sql = "DELETE FROM vouchers WHERE id = ?";
        return $this->execute($sql, [$id]);
    }

    /**
     * Đếm tổng số voucher
     */
    public function countAll(): int {
        $sql = "SELECT COUNT(*) AS total FROM vouchers";
        $row = $this->fetch($sql);
        return (int)($row['total'] ?? 0);
    }
}
