<?php
/**
 * ShopXpress - User Address Model
 * Quản lý sổ địa chỉ nhận hàng của người dùng
 */

class UserAddress extends Model {

    /**
     * Lấy toàn bộ danh sách địa chỉ của người dùng
     */
    public function getByUserId(int $userId): array {
        $sql = "SELECT * FROM user_addresses WHERE user_id = ? ORDER BY is_default DESC, id DESC";
        return $this->fetchAll($sql, [$userId]);
    }

    /**
     * Lấy địa chỉ mặc định của người dùng
     */
    public function getDefaultAddress(int $userId): ?array {
        $sql = "SELECT * FROM user_addresses WHERE user_id = ? AND is_default = 1 LIMIT 1";
        $addr = $this->fetch($sql, [$userId]);
        if (!$addr) {
            // Nếu chưa có địa chỉ nào là mặc định, lấy địa chỉ đầu tiên
            $sqlFirst = "SELECT * FROM user_addresses WHERE user_id = ? ORDER BY id ASC LIMIT 1";
            $addr = $this->fetch($sqlFirst, [$userId]);
        }
        return $addr;
    }

    /**
     * Lấy chi tiết một địa chỉ theo ID
     */
    public function getById(int $id, ?int $userId = null): ?array {
        if ($userId !== null) {
            $sql = "SELECT * FROM user_addresses WHERE id = ? AND user_id = ?";
            return $this->fetch($sql, [$id, $userId]);
        }
        $sql = "SELECT * FROM user_addresses WHERE id = ?";
        return $this->fetch($sql, [$id]);
    }

    /**
     * Thêm địa chỉ mới
     */
    public function add(array $data): int {
        $userId = (int)$data['user_id'];
        $isDefault = !empty($data['is_default']) ? 1 : 0;

        // Nếu đây là địa chỉ đầu tiên của user hoặc được set is_default = 1
        $countSql = "SELECT COUNT(*) AS total FROM user_addresses WHERE user_id = ?";
        $res = $this->fetch($countSql, [$userId]);
        if (($res['total'] ?? 0) == 0) {
            $isDefault = 1;
        } elseif ($isDefault === 1) {
            // Bỏ default của các địa chỉ cũ
            $this->execute("UPDATE user_addresses SET is_default = 0 WHERE user_id = ?", [$userId]);
        }

        $street = trim($data['street_address'] ?? '');
        $ward = trim($data['ward'] ?? '');
        $district = trim($data['district'] ?? '');
        $province = trim($data['province'] ?? '');
        
        $fullAddress = trim($data['full_address'] ?? '');
        if (empty($fullAddress)) {
            $parts = array_filter([$street, $ward, $district, $province]);
            $fullAddress = implode(', ', $parts);
        }

        $sql = "INSERT INTO user_addresses (user_id, recipient_name, recipient_phone, province, district, ward, street_address, full_address, is_default)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $this->execute($sql, [
            $userId,
            $data['recipient_name'],
            $data['recipient_phone'],
            $province ?: null,
            $district ?: null,
            $ward ?: null,
            $street ?: $fullAddress,
            $fullAddress,
            $isDefault
        ]);

        return (int)$this->lastInsertId();
    }

    /**
     * Đặt địa chỉ làm mặc định
     */
    public function setDefault(int $id, int $userId): bool {
        $this->execute("UPDATE user_addresses SET is_default = 0 WHERE user_id = ?", [$userId]);
        return $this->execute("UPDATE user_addresses SET is_default = 1 WHERE id = ? AND user_id = ?", [$id, $userId]);
    }

    /**
     * Cập nhật địa chỉ
     */
    public function update(int $id, int $userId, array $data): bool {
        $street = trim($data['street_address'] ?? '');
        $ward = trim($data['ward'] ?? '');
        $district = trim($data['district'] ?? '');
        $province = trim($data['province'] ?? '');
        
        $fullAddress = trim($data['full_address'] ?? '');
        if (empty($fullAddress)) {
            $parts = array_filter([$street, $ward, $district, $province]);
            $fullAddress = implode(', ', $parts);
        }

        $sql = "UPDATE user_addresses 
                SET recipient_name = ?, recipient_phone = ?, province = ?, district = ?, ward = ?, street_address = ?, full_address = ?
                WHERE id = ? AND user_id = ?";
        return $this->execute($sql, [
            $data['recipient_name'],
            $data['recipient_phone'],
            $province ?: null,
            $district ?: null,
            $ward ?: null,
            $street ?: $fullAddress,
            $fullAddress,
            $id,
            $userId
        ]);
    }

    /**
     * Xóa địa chỉ (không cho xóa địa chỉ mặc định nếu còn địa chỉ khác)
     */
    public function delete(int $id, int $userId): bool {
        $addr = $this->getById($id, $userId);
        if (!$addr) return false;

        $deleted = $this->execute("DELETE FROM user_addresses WHERE id = ? AND user_id = ?", [$id, $userId]);
        
        // Nếu vừa xóa địa chỉ mặc định, set địa chỉ kế tiếp làm mặc định
        if ($addr['is_default'] == 1) {
            $next = $this->fetch("SELECT id FROM user_addresses WHERE user_id = ? ORDER BY id DESC LIMIT 1", [$userId]);
            if ($next) {
                $this->execute("UPDATE user_addresses SET is_default = 1 WHERE id = ?", [$next['id']]);
            }
        }

        return $deleted;
    }
}
