<?php
/**
 * ShopXpress - User Model
 * Quản lý tài khoản người dùng, xác thực đăng nhập và băm mật khẩu BCrypt
 */

class User extends Model {

    /**
     * Tìm tài khoản theo username
     */
    public function findByUsername(string $username): ?array {
        $sql = "SELECT * FROM users WHERE username = ?";
        return $this->fetch($sql, [$username]);
    }

    /**
     * Tìm tài khoản theo email
     */
    public function findByEmail(string $email): ?array {
        $sql = "SELECT * FROM users WHERE email = ?";
        return $this->fetch($sql, [$email]);
    }

    /**
     * Tìm tài khoản theo ID
     */
    public function findById(int $id): ?array {
        $sql = "SELECT * FROM users WHERE id = ?";
        return $this->fetch($sql, [$id]);
    }

    /**
     * Lấy toàn bộ danh sách người dùng cho Admin
     */
    public function getAll(): array {
        $sql = "SELECT id, username, fullname, email, phone, address, role, status, isAdmin, isDelete, created_at, 
                       (SELECT COUNT(*) FROM orders WHERE user_id = users.id) AS order_count 
                FROM users 
                ORDER BY isAdmin DESC, isDelete ASC, id DESC";
        return $this->fetchAll($sql);
    }

    /**
     * Tạo tài khoản mới có băm mật khẩu an toàn
     */
    public function create(array $data): int {
        $sql = "INSERT INTO users (username, password, fullname, email, phone, address, role, status, isAdmin, isDelete) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 0)";
        
        $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT);
        $role = $data['role'] ?? 'customer';
        $isAdmin = ($role === 'admin' || !empty($data['isAdmin'])) ? 1 : 0;
        
        $this->execute($sql, [
            $data['username'],
            $hashedPassword,
            $data['fullname'],
            $data['email'],
            $data['phone'] ?? null,
            $data['address'] ?? null,
            $role,
            $data['status'] ?? 'active',
            $isAdmin
        ]);

        return (int)$this->lastInsertId();
    }

    /**
     * Xóa mềm tài khoản (isDelete = 1). Không được xóa tài khoản có isAdmin = 1!
     */
    public function softDelete(int $id): bool {
        $user = $this->findById($id);
        if (!$user) return false;

        // Quy tắc: isAdmin thì không được xóa
        if (!empty($user['isAdmin']) || $user['role'] === 'admin' || $id == 1) {
            return false;
        }

        $sql = "UPDATE users SET isDelete = 1 WHERE id = ?";
        return $this->execute($sql, [$id]);
    }

    /**
     * Khôi phục tài khoản đã xóa mềm
     */
    public function restore(int $id): bool {
        $sql = "UPDATE users SET isDelete = 0 WHERE id = ?";
        return $this->execute($sql, [$id]);
    }

    /**
     * Cập nhật quyền hạn người dùng (admin / customer)
     */
    public function updateRole(int $id, string $role): bool {
        $isAdmin = ($role === 'admin') ? 1 : 0;
        $sql = "UPDATE users SET role = ?, isAdmin = ? WHERE id = ?";
        return $this->execute($sql, [$role, $isAdmin, $id]);
    }

    /**
     * Khóa hoặc mở khóa tài khoản người dùng (active <-> banned)
     */
    public function toggleStatus(int $id): bool {
        $sql = "UPDATE users SET status = IF(status = 'active', 'banned', 'active') WHERE id = ?";
        return $this->execute($sql, [$id]);
    }

    /**
     * Đếm tổng số khách hàng đăng ký
     */
    public function countCustomers(): int {
        $sql = "SELECT COUNT(*) AS total FROM users WHERE role = 'customer'";
        $row = $this->fetch($sql);
        return (int)($row['total'] ?? 0);
    }

    /**
     * Cập nhật thông tin hồ sơ cá nhân
     */
    public function updateProfile(int $id, array $data): bool {
        $sql = "UPDATE users SET fullname = ?, phone = ?, address = ? WHERE id = ?";
        return $this->execute($sql, [
            $data['fullname'],
            $data['phone'] ?? null,
            $data['address'] ?? null,
            $id
        ]);
    }

    /**
     * Cập nhật mật khẩu mới
     */
    public function updatePassword(int $id, string $hashedPassword): bool {
        $sql = "UPDATE users SET password = ? WHERE id = ?";
        return $this->execute($sql, [$hashedPassword, $id]);
    }

    /**
     * Xác thực mật khẩu linh hoạt (BCrypt chuẩn + Fallback đối sánh)
     */
    public function verifyPassword(string $password, string $hash): bool {
        if (password_verify($password, $hash)) {
            return true;
        }
        if ($password === $hash) {
            return true;
        }
        return false;
    }
}
