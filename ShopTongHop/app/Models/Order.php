<?php
/**
 * ShopXpress - Order Model
 * Quản lý đơn đặt hàng, chi tiết đơn hàng, lịch sử mua sắm và hóa đơn
 */

class Order extends Model {

    /**
     * Lấy danh sách đơn hàng của một người dùng kèm thông tin tổng quan
     */
    public function getByUserId(int $userId, ?string $status = null): array {
        $sql = "SELECT o.*, 
                       (SELECT COUNT(*) FROM order_items WHERE order_id = o.id) AS total_items,
                       (SELECT product_name FROM order_items WHERE order_id = o.id LIMIT 1) AS first_product_name,
                       (SELECT p.thumbnail FROM order_items oi LEFT JOIN products p ON oi.product_id = p.id WHERE oi.order_id = o.id LIMIT 1) AS first_product_thumb
                FROM orders o 
                WHERE o.user_id = ?";
        $params = [$userId];

        if (!empty($status) && $status !== 'all') {
            $sql .= " AND o.order_status = ?";
            $params[] = $status;
        }

        $sql .= " ORDER BY o.created_at DESC";
        return $this->fetchAll($sql, $params);
    }

    /**
     * Lấy toàn bộ đơn hàng cho trang quản trị Admin
     */
    public function getAllAdmin(?string $status = null, ?string $keyword = null): array {
        $sql = "SELECT o.*, 
                       (SELECT COUNT(*) FROM order_items WHERE order_id = o.id) AS total_items 
                FROM orders o 
                WHERE 1=1";
        $params = [];

        if (!empty($status) && $status !== 'all') {
            $sql .= " AND o.order_status = ?";
            $params[] = $status;
        }

        if (!empty($keyword)) {
            $sql .= " AND (o.order_code LIKE ? OR o.customer_name LIKE ? OR o.customer_phone LIKE ?)";
            $params[] = "%$keyword%";
            $params[] = "%$keyword%";
            $params[] = "%$keyword%";
        }

        $sql .= " ORDER BY o.id DESC";
        return $this->fetchAll($sql, $params);
    }

    /**
     * Thống kê KPI doanh thu và đơn hàng cho Admin Dashboard
     */
    public function getStats(): array {
        $stats = [
            'total_revenue' => 0,
            'total_orders' => 0,
            'pending_orders' => 0,
            'completed_orders' => 0,
            'today_orders' => 0,
            'today_revenue' => 0
        ];

        // Doanh thu từ các đơn đã thanh toán hoặc đã hoàn tất
        $revRow = $this->fetch("SELECT SUM(total_amount) AS rev FROM orders WHERE payment_status = 'paid' OR order_status = 'completed'");
        $stats['total_revenue'] = (float)($revRow['rev'] ?? 0);

        // Tổng số đơn
        $cntRow = $this->fetch("SELECT COUNT(*) AS total FROM orders");
        $stats['total_orders'] = (int)($cntRow['total'] ?? 0);

        // Đơn chờ xử lý
        $pendRow = $this->fetch("SELECT COUNT(*) AS pend FROM orders WHERE order_status = 'pending'");
        $stats['pending_orders'] = (int)($pendRow['pend'] ?? 0);

        // Đơn hoàn tất
        $compRow = $this->fetch("SELECT COUNT(*) AS comp FROM orders WHERE order_status = 'completed'");
        $stats['completed_orders'] = (int)($compRow['comp'] ?? 0);

        // Đơn hôm nay
        $todayRow = $this->fetch("SELECT COUNT(*) AS today_cnt, SUM(total_amount) AS today_rev FROM orders WHERE DATE(created_at) = CURDATE()");
        $stats['today_orders'] = (int)($todayRow['today_cnt'] ?? 0);
        $stats['today_revenue'] = (float)($todayRow['today_rev'] ?? 0);

        return $stats;
    }

    /**
     * Lấy các đơn hàng mới nhất
     */
    public function getRecentOrders(int $limit = 6): array {
        $sql = "SELECT o.*, 
                       (SELECT COUNT(*) FROM order_items WHERE order_id = o.id) AS total_items 
                FROM orders o 
                ORDER BY o.id DESC 
                LIMIT " . (int)$limit;
        return $this->fetchAll($sql);
    }

    /**
     * Lấy chi tiết đơn hàng theo ID
     */
    public function getById(int $id, ?int $userId = null): ?array {
        $sql = "SELECT o.*, u.username, u.email AS account_email 
                FROM orders o 
                LEFT JOIN users u ON o.user_id = u.id 
                WHERE o.id = ?";
        $params = [$id];

        if ($userId !== null) {
            $sql .= " AND o.user_id = ?";
            $params[] = $userId;
        }

        return $this->fetch($sql, $params);
    }

    /**
     * Lấy chi tiết đơn hàng theo Mã đơn hàng (order_code)
     */
    public function getByCode(string $code, ?int $userId = null): ?array {
        $sql = "SELECT o.*, u.username, u.email AS account_email 
                FROM orders o 
                LEFT JOIN users u ON o.user_id = u.id 
                WHERE o.order_code = ?";
        $params = [$code];

        if ($userId !== null) {
            $sql .= " AND o.user_id = ?";
            $params[] = $userId;
        }

        return $this->fetch($sql, $params);
    }

    /**
     * Lấy toàn bộ danh sách mặt hàng trong đơn hàng kèm ảnh sản phẩm
     */
    public function getItems(int $orderId): array {
        $sql = "SELECT oi.*, p.slug, p.thumbnail, p.sku 
                FROM order_items oi 
                LEFT JOIN products p ON oi.product_id = p.id 
                WHERE oi.order_id = ? 
                ORDER BY oi.id ASC";
        return $this->fetchAll($sql, [$orderId]);
    }

    /**
     * Tra cứu nhanh đơn hàng qua Mã đơn & Số điện thoại (dành cho khách không đăng nhập)
     */
    public function lookup(string $orderCode, string $phone): ?array {
        $sql = "SELECT * FROM orders WHERE order_code = ? AND customer_phone = ?";
        return $this->fetch($sql, [trim($orderCode), trim($phone)]);
    }

    /**
     * Tạo đơn hàng mới kèm danh sách chi tiết mặt hàng (Sử dụng Transaction an toàn)
     */
    public function create(array $orderData, array $items): int {
        $db = Database::getInstance()->getConnection();
        $db->beginTransaction();

        try {
            $sqlOrder = "INSERT INTO orders (order_code, user_id, customer_name, customer_email, customer_phone, 
                                             shipping_address, voucher_code, discount_amount, total_amount, 
                                             payment_method, payment_status, order_status, notes) 
                         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', ?)";
            
            $stmt = $db->prepare($sqlOrder);
            $stmt->execute([
                $orderData['order_code'],
                $orderData['user_id'] ?? null,
                $orderData['customer_name'],
                $orderData['customer_email'],
                $orderData['customer_phone'],
                $orderData['shipping_address'],
                $orderData['voucher_code'] ?? null,
                $orderData['discount_amount'] ?? 0,
                $orderData['total_amount'],
                $orderData['payment_method'] ?? 'cod',
                $orderData['payment_status'] ?? 'pending',
                $orderData['notes'] ?? null
            ]);

            $orderId = (int)$db->lastInsertId();

            // Lưu các mặt hàng vào bảng order_items & trừ tồn kho
            $sqlItem = "INSERT INTO order_items (order_id, product_id, product_name, product_price, quantity, subtotal) 
                        VALUES (?, ?, ?, ?, ?, ?)";
            $stmtItem = $db->prepare($sqlItem);

            $sqlStock = "UPDATE products SET stock_quantity = GREATEST(0, stock_quantity - ?) WHERE id = ?";
            $stmtStock = $db->prepare($sqlStock);

            foreach ($items as $item) {
                $subtotal = (float)$item['price'] * (int)$item['quantity'];
                $stmtItem->execute([
                    $orderId,
                    $item['id'],
                    $item['name'],
                    $item['price'],
                    $item['quantity'],
                    $subtotal
                ]);

                // Trừ tồn kho tự động
                $stmtStock->execute([(int)$item['quantity'], $item['id']]);
            }

            $db->commit();
            return $orderId;
        } catch (\Exception $e) {
            $db->rollBack();
            error_log("ShopXpress Order Creation Error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Cập nhật trạng thái đơn hàng và thanh toán
     */
    public function updateStatus(int $id, string $orderStatus, ?string $paymentStatus = null): bool {
        if ($paymentStatus !== null) {
            $sql = "UPDATE orders SET order_status = ?, payment_status = ? WHERE id = ?";
            return $this->execute($sql, [$orderStatus, $paymentStatus, $id]);
        }
        $sql = "UPDATE orders SET order_status = ? WHERE id = ?";
        return $this->execute($sql, [$orderStatus, $id]);
    }

    /**
     * Đếm số lượng đơn theo từng trạng thái của người dùng
     */
    public function countByStatus(int $userId): array {
        $sql = "SELECT order_status, COUNT(*) as count FROM orders WHERE user_id = ? GROUP BY order_status";
        $rows = $this->fetchAll($sql, [$userId]);
        $counts = [
            'all' => 0,
            'pending' => 0,
            'confirmed' => 0,
            'shipping' => 0,
            'completed' => 0,
            'cancelled' => 0
        ];

        foreach ($rows as $r) {
            $status = $r['order_status'];
            $cnt = (int)$r['count'];
            $counts[$status] = $cnt;
            $counts['all'] += $cnt;
        }

        return $counts;
    }
}
