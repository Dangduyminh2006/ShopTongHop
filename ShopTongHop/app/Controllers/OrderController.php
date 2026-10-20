<?php
/**
 * ShopXpress - Order Controller
 * Điều phối phân hệ Lịch sử mua hàng, Chi tiết đơn hàng và Xuất hóa đơn
 */

class OrderController extends Controller {
    private Order $orderModel;

    public function __construct() {
        $this->orderModel = $this->model('Order');
    }

    /**
     * Danh sách lịch sử mua hàng của người dùng
     */
    public function history(): void {
        $user = $_SESSION['user'] ?? null;
        $statusFilter = $_GET['status'] ?? 'all';
        $orders = [];
        $counts = [
            'all' => 0,
            'pending' => 0,
            'confirmed' => 0,
            'shipping' => 0,
            'completed' => 0,
            'cancelled' => 0
        ];

        if ($user) {
            $orders = $this->orderModel->getByUserId((int)$user['id'], $statusFilter);
            $counts = $this->orderModel->countByStatus((int)$user['id']);
        }

        $this->view('orders/history', [
            'pageTitle'    => 'Lịch Sử Mua Hàng - ' . APP_NAME,
            'user'         => $user,
            'orders'       => $orders,
            'currentTab'   => $statusFilter,
            'counts'       => $counts,
            'lookupError'  => $_SESSION['lookup_error'] ?? null
        ]);

        unset($_SESSION['lookup_error']);
    }

    /**
     * Chi tiết một đơn hàng kèm tiến trình vận chuyển và thông tin thanh toán
     */
    public function detail(?string $idOrCode = null): void {
        if (empty($idOrCode)) {
            $this->redirect(BASE_URL . '/order/history');
            return;
        }

        $user = $_SESSION['user'] ?? null;
        $order = null;

        // Tìm theo ID (nếu là số) hoặc theo Mã đơn hàng
        if (is_numeric($idOrCode)) {
            $order = $this->orderModel->getById((int)$idOrCode);
        } else {
            $order = $this->orderModel->getByCode($idOrCode);
        }

        if (!$order) {
            $this->view('orders/not_found', [
                'pageTitle' => 'Không Tìm Thấy Đơn Hàng - ' . APP_NAME,
                'query'     => $idOrCode
            ]);
            return;
        }

        // Kiểm tra quyền truy cập: Người dùng sở hữu đơn, Admin, hoặc khách vừa tra cứu khớp mã
        $isOwner = false;
        if ($user && ((int)$user['id'] === (int)$order['user_id'] || ($user['role'] ?? '') === 'admin')) {
            $isOwner = true;
        } elseif (isset($_SESSION['verified_order_code']) && $_SESSION['verified_order_code'] === $order['order_code']) {
            $isOwner = true;
        } elseif (!$order['user_id']) {
            // Khách vãng lai xem đơn vừa tạo
            $isOwner = true;
        }

        $items = $this->orderModel->getItems((int)$order['id']);

        $this->view('orders/detail', [
            'pageTitle' => 'Chi Tiết Đơn Hàng #' . $order['order_code'] . ' - ' . APP_NAME,
            'order'     => $order,
            'items'     => $items,
            'isOwner'   => $isOwner
        ]);
    }

    /**
     * Xem và in Hóa đơn bán lẻ điện tử chuẩn mực
     */
    public function invoice(?string $idOrCode = null): void {
        if (empty($idOrCode)) {
            $this->redirect(BASE_URL . '/order/history');
            return;
        }

        $order = is_numeric($idOrCode) 
            ? $this->orderModel->getById((int)$idOrCode) 
            : $this->orderModel->getByCode($idOrCode);

        if (!$order) {
            $this->redirect(BASE_URL . '/order/history');
            return;
        }

        $items = $this->orderModel->getItems((int)$order['id']);

        // Hóa đơn sử dụng layout null để tối ưu cho việc in ấn (print)
        $this->view('orders/invoice', [
            'pageTitle' => 'Hóa Đơn Bán Hàng #' . $order['order_code'],
            'order'     => $order,
            'items'     => $items
        ], null);
    }

    /**
     * Tra cứu nhanh đơn hàng dành cho khách chưa đăng nhập
     */
    public function lookup(): void {
        if (!$this->isPost()) {
            $this->redirect(BASE_URL . '/order/history');
            return;
        }

        $orderCode = trim($_POST['order_code'] ?? '');
        $phone = trim($_POST['phone'] ?? '');

        if (empty($orderCode) || empty($phone)) {
            $_SESSION['lookup_error'] = 'Vui lòng nhập đầy đủ Mã đơn hàng và Số điện thoại đặt hàng.';
            $this->redirect(BASE_URL . '/order/history');
            return;
        }

        $order = $this->orderModel->lookup($orderCode, $phone);

        if ($order) {
            $_SESSION['verified_order_code'] = $order['order_code'];
            $this->redirect(BASE_URL . '/order/detail/' . $order['order_code']);
        } else {
            $_SESSION['lookup_error'] = 'Không tìm thấy đơn hàng với thông tin đã cung cấp. Vui lòng kiểm tra lại.';
            $this->redirect(BASE_URL . '/order/history');
        }
    }
}
