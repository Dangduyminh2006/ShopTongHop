<?php
/**
 * ShopXpress - Cart Controller
 * Xử lý toàn bộ luồng mua sắm: Giỏ hàng Session, Mã giảm giá, Thanh toán COD & VietQR
 */

class CartController extends Controller {

    /**
     * Helper tính phí vận chuyển: Miễn phí cho đơn >= 300,000đ, dưới 300k phí 30,000đ
     */
    private function calculateShipping(float $subtotal): float {
        return ($subtotal >= 300000 || $subtotal == 0) ? 0 : 30000;
    }

    /**
     * 1. Hiển thị trang giỏ hàng (Bắt buộc đăng nhập tài khoản)
     */
    public function index(): void {
        if (empty($_SESSION['user'])) {
            $this->setFlash('warning', 'Vui lòng đăng nhập tài khoản để xem giỏ hàng và đặt mua sản phẩm!');
            $this->redirect('login?redirect=cart');
            return;
        }

        $cart = $_SESSION['cart'] ?? [];
        $totalAmount = 0;
        foreach ($cart as $item) {
            $totalAmount += (float)$item['price'] * (int)$item['quantity'];
        }

        $voucher = $_SESSION['voucher'] ?? null;
        $discountAmount = 0;

        // Tự động kiểm tra lại điều kiện voucher với giá trị giỏ hàng hiện tại
        if ($voucher) {
            if ($totalAmount < (float)$voucher['min_order_amount']) {
                unset($_SESSION['voucher']);
                $voucher = null;
                $this->setFlash('warning', 'Mã giảm giá đã tự động gỡ bỏ do giá trị đơn hàng chưa đạt mức tối thiểu ' . number_format($voucher['min_order_amount'] ?? 0) . ' đ.');
            } else {
                if ($voucher['discount_type'] === 'percent') {
                    $discountAmount = ($totalAmount * (float)$voucher['discount_value']) / 100;
                } else {
                    $discountAmount = (float)$voucher['discount_value'];
                }
            }
        }

        $shippingFee = $this->calculateShipping($totalAmount);
        $finalAmount = max(0, $totalAmount - $discountAmount + $shippingFee);

        $this->view('cart/index', [
            'pageTitle'      => 'Giỏ Hàng Của Bạn | ' . APP_NAME,
            'cart'           => $cart,
            'totalAmount'    => $totalAmount,
            'discountAmount' => $discountAmount,
            'shippingFee'    => $shippingFee,
            'finalAmount'    => $finalAmount,
            'voucher'        => $voucher
        ]);
    }

    /**
     * 2. Thêm sản phẩm vào giỏ hàng (Hỗ trợ AJAX & Form POST thông thường)
     */
    public function add(): void {
        // Yêu cầu đăng nhập tài khoản trước khi thêm vào giỏ hàng
        if (empty($_SESSION['user'])) {
            if ($this->isAjax()) {
                $this->json([
                    'success'       => false,
                    'require_login' => true,
                    'message'       => 'Vui lòng đăng nhập tài khoản để thêm sản phẩm vào giỏ hàng!'
                ], 200);
            } else {
                $this->setFlash('warning', 'Vui lòng đăng nhập tài khoản để thêm sản phẩm vào giỏ hàng!');
                $this->redirect('login?redirect=products');
            }
            return;
        }

        $productId = (int)($_POST['product_id'] ?? 0);
        $quantity = max(1, (int)($_POST['quantity'] ?? 1));

        if ($productId <= 0) {
            if ($this->isAjax()) {
                $this->json(['success' => false, 'message' => 'Sản phẩm không hợp lệ.'], 400);
            } else {
                $this->setFlash('error', 'Sản phẩm không hợp lệ.');
                $this->redirect('products');
            }
            return;
        }

        $productModel = $this->model('Product');
        $product = $productModel->getById($productId);

        if (!$product || $product['status'] != 1) {
            if ($this->isAjax()) {
                $this->json(['success' => false, 'message' => 'Sản phẩm không tồn tại hoặc đã ngừng kinh doanh.'], 404);
            } else {
                $this->setFlash('error', 'Sản phẩm không tồn tại hoặc đã ngừng kinh doanh.');
                $this->redirect('products');
            }
            return;
        }

        // Kiểm tra tồn kho
        $currentInCart = isset($_SESSION['cart'][$productId]) ? (int)$_SESSION['cart'][$productId]['quantity'] : 0;
        if ($currentInCart + $quantity > (int)$product['stock_quantity']) {
            $msg = "Số lượng trong kho chỉ còn {$product['stock_quantity']} sản phẩm (bạn đã có {$currentInCart} trong giỏ).";
            if ($this->isAjax()) {
                $this->json(['success' => false, 'message' => $msg]);
            } else {
                $this->setFlash('error', $msg);
                $this->redirect('cart');
            }
            return;
        }

        // Tính giá bán sau khuyến mãi
        $discountPercent = (int)($product['discount_percent'] ?? 0);
        $rawPrice = (float)($product['price'] ?? 0);
        $unitPrice = $rawPrice * (1 - $discountPercent / 100);

        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        if (isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId]['quantity'] += $quantity;
        } else {
            $_SESSION['cart'][$productId] = [
                'id'               => (int)$product['id'],
                'name'             => $product['name'],
                'slug'             => $product['slug'],
                'sku'              => $product['sku'],
                'price'            => $unitPrice,
                'raw_price'        => $rawPrice,
                'discount_percent' => $discountPercent,
                'thumbnail'        => $product['thumbnail'],
                'stock_quantity'   => (int)$product['stock_quantity'],
                'quantity'         => $quantity
            ];
        }

        $cartCount = array_sum(array_column($_SESSION['cart'], 'quantity'));

        if ($this->isAjax()) {
            $this->json([
                'success'   => true,
                'message'   => "Đã thêm '{$product['name']}' vào giỏ hàng!",
                'cartCount' => $cartCount
            ]);
        } else {
            $this->setFlash('success', "Đã thêm '{$product['name']}' vào giỏ hàng!");
            $this->redirect('cart');
        }
    }

    /**
     * 3. Cập nhật số lượng mặt hàng trong giỏ
     */
    public function update(): void {
        $productId = (int)($_POST['product_id'] ?? 0);
        $quantity = (int)($_POST['quantity'] ?? 1);

        if ($productId > 0 && isset($_SESSION['cart'][$productId])) {
            $productModel = $this->model('Product');
            $product = $productModel->getById($productId);
            $maxStock = (int)($product['stock_quantity'] ?? 999);

            if ($quantity <= 0) {
                unset($_SESSION['cart'][$productId]);
                $msg = 'Đã xóa sản phẩm khỏi giỏ hàng.';
            } else {
                if ($quantity > $maxStock) {
                    $quantity = $maxStock;
                    $this->setFlash('warning', "Chỉ có thể đặt tối đa {$maxStock} sản phẩm có sẵn trong kho.");
                }
                $_SESSION['cart'][$productId]['quantity'] = $quantity;
                $msg = 'Đã cập nhật số lượng sản phẩm.';
            }

            if ($this->isAjax()) {
                $totalAmount = 0;
                foreach ($_SESSION['cart'] as $item) {
                    $totalAmount += (float)$item['price'] * (int)$item['quantity'];
                }
                $shippingFee = $this->calculateShipping($totalAmount);
                $cartCount = array_sum(array_column($_SESSION['cart'], 'quantity'));
                $itemSubtotal = isset($_SESSION['cart'][$productId]) ? (float)$_SESSION['cart'][$productId]['price'] * $quantity : 0;

                $this->json([
                    'success'      => true,
                    'message'      => $msg,
                    'quantity'     => $quantity,
                    'itemSubtotal' => number_format($itemSubtotal) . ' đ',
                    'totalAmount'  => number_format($totalAmount) . ' đ',
                    'shippingFee'  => $shippingFee == 0 ? 'Miễn phí' : number_format($shippingFee) . ' đ',
                    'cartCount'    => $cartCount
                ]);
                return;
            }
        }

        $this->redirect('cart');
    }

    /**
     * 4. Xóa mặt hàng khỏi giỏ
     */
    public function remove(): void {
        $productId = (int)($_POST['product_id'] ?? ($_GET['id'] ?? 0));
        if ($productId > 0 && isset($_SESSION['cart'][$productId])) {
            $name = $_SESSION['cart'][$productId]['name'];
            unset($_SESSION['cart'][$productId]);

            if (empty($_SESSION['cart'])) {
                unset($_SESSION['voucher']);
            }

            if ($this->isAjax()) {
                $cartCount = array_sum(array_column($_SESSION['cart'], 'quantity'));
                $this->json([
                    'success'   => true,
                    'message'   => "Đã xóa '{$name}' khỏi giỏ hàng.",
                    'cartCount' => $cartCount
                ]);
                return;
            }
            $this->setFlash('success', "Đã xóa '{$name}' khỏi giỏ hàng.");
        }
        $this->redirect('cart');
    }

    /**
     * 5. Xóa toàn bộ giỏ hàng
     */
    public function clear(): void {
        $_SESSION['cart'] = [];
        unset($_SESSION['voucher']);
        $this->setFlash('info', 'Đã dọn sạch giỏ hàng.');
        $this->redirect('cart');
    }

    /**
     * 6. Áp dụng mã giảm giá Voucher
     */
    public function applyVoucher(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('cart');
            return;
        }

        $code = strtoupper(trim($_POST['voucher_code'] ?? ''));
        $cart = $_SESSION['cart'] ?? [];

        if (empty($cart)) {
            $this->setFlash('error', 'Giỏ hàng của bạn đang trống.');
            $this->redirect('cart');
            return;
        }

        if (empty($code)) {
            $this->setFlash('error', 'Vui lòng nhập mã giảm giá.');
            $this->redirect('cart');
            return;
        }

        $totalAmount = 0;
        foreach ($cart as $item) {
            $totalAmount += (float)$item['price'] * (int)$item['quantity'];
        }

        $voucherModel = $this->model('Voucher');
        $voucher = $voucherModel->getValidByCode($code, $totalAmount);

        if (!$voucher) {
            // Kiểm tra lý do không hợp lệ để thông báo chi tiết
            $checkAll = $this->model('Voucher')->fetch("SELECT * FROM vouchers WHERE code = ?", [$code]);
            if (!$checkAll) {
                $this->setFlash('error', "Mã giảm giá '{$code}' không tồn tại trong hệ thống.");
            } elseif ($checkAll['status'] != 1) {
                $this->setFlash('error', "Mã giảm giá '{$code}' hiện đang bị tạm khóa.");
            } elseif ($totalAmount < (float)$checkAll['min_order_amount']) {
                $this->setFlash('warning', "Mã '{$code}' chỉ áp dụng cho đơn hàng từ " . number_format($checkAll['min_order_amount']) . " đ trở lên (Đơn hiện tại: " . number_format($totalAmount) . " đ).");
            } elseif ($checkAll['used_count'] >= $checkAll['usage_limit']) {
                $this->setFlash('error', "Mã '{$code}' đã hết số lượt sử dụng khuyến mãi.");
            } else {
                $this->setFlash('error', "Mã giảm giá '{$code}' chưa đến hạn hoặc đã hết hạn áp dụng.");
            }
        } else {
            $_SESSION['voucher'] = $voucher;
            $discountText = $voucher['discount_type'] === 'percent' 
                ? "{$voucher['discount_value']}%" 
                : (number_format($voucher['discount_value']) . " đ");
            $this->setFlash('success', "Áp dụng thành công mã '{$code}', bạn được giảm {$discountText}!");
        }

        // Điều hướng lại trang gửi yêu cầu (cart hoặc checkout)
        $referer = $_SERVER['HTTP_REFERER'] ?? '';
        if (strpos($referer, 'checkout') !== false) {
            $this->redirect('checkout');
        } else {
            $this->redirect('cart');
        }
    }

    /**
     * 7. Gỡ bỏ mã giảm giá
     */
    public function removeVoucher(): void {
        unset($_SESSION['voucher']);
        $this->setFlash('info', 'Đã gỡ bỏ mã giảm giá.');
        $referer = $_SERVER['HTTP_REFERER'] ?? '';
        if (strpos($referer, 'checkout') !== false) {
            $this->redirect('checkout');
        } else {
            $this->redirect('cart');
        }
    }

    /**
     * 8. Trang Thanh toán Đặt hàng (Bắt buộc đăng nhập & tải sổ địa chỉ người dùng)
     */
    public function checkout(): void {
        // Yêu cầu đăng nhập tài khoản trước khi thanh toán
        if (empty($_SESSION['user'])) {
            $this->setFlash('warning', 'Vui lòng đăng nhập tài khoản để tiến hành thanh toán đơn hàng!');
            $this->redirect('login?redirect=cart/checkout');
            return;
        }

        $cart = $_SESSION['cart'] ?? [];

        if (empty($cart)) {
            $this->setFlash('warning', 'Giỏ hàng của bạn đang trống. Vui lòng chọn sản phẩm trước khi thanh toán.');
            $this->redirect('cart');
            return;
        }

        $totalAmount = 0;
        foreach ($cart as $item) {
            $totalAmount += (float)$item['price'] * (int)$item['quantity'];
        }

        $voucher = $_SESSION['voucher'] ?? null;
        $discountAmount = 0;
        if ($voucher) {
            if ($voucher['discount_type'] === 'percent') {
                $discountAmount = ($totalAmount * (float)$voucher['discount_value']) / 100;
            } else {
                $discountAmount = (float)$voucher['discount_value'];
            }
        }

        $shippingFee = $this->calculateShipping($totalAmount);
        $finalAmount = max(0, $totalAmount - $discountAmount + $shippingFee);

        $currentUser = $_SESSION['user'] ?? null;
        $userId = (int)($currentUser['id'] ?? 0);

        // Lấy danh sách địa chỉ từ bảng user_addresses
        $userAddressModel = $this->model('UserAddress');
        $addresses = $userAddressModel->getByUserId($userId);
        $defaultAddress = $userAddressModel->getDefaultAddress($userId);

        $this->view('cart/checkout', [
            'pageTitle'      => 'Thanh Toán Đơn Hàng | ' . APP_NAME,
            'cart'           => $cart,
            'totalAmount'    => $totalAmount,
            'discountAmount' => $discountAmount,
            'shippingFee'    => $shippingFee,
            'finalAmount'    => $finalAmount,
            'voucher'        => $voucher,
            'currentUser'    => $currentUser,
            'addresses'      => $addresses,
            'defaultAddress' => $defaultAddress
        ]);
    }

    /**
     * 9. Xử lý tạo đơn hàng (Process Checkout)
     */
    public function processCheckout(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('checkout');
            return;
        }

        // Bắt buộc đăng nhập tài khoản để đặt hàng
        if (empty($_SESSION['user'])) {
            $this->setFlash('warning', 'Vui lòng đăng nhập tài khoản để tiến hành đặt hàng!');
            $this->redirect('login?redirect=cart/checkout');
            return;
        }

        $cart = $_SESSION['cart'] ?? [];
        if (empty($cart)) {
            $this->setFlash('error', 'Giỏ hàng của bạn đang trống.');
            $this->redirect('cart');
            return;
        }

        $userId = (int)$_SESSION['user']['id'];
        $userAddressModel = $this->model('UserAddress');

        // Lấy thông tin từ Form
        $name = trim($_POST['customer_name'] ?? '');
        $phone = trim($_POST['customer_phone'] ?? '');
        $email = trim($_POST['customer_email'] ?? '');
        $address = trim($_POST['shipping_address'] ?? '');
        $addressId = (int)($_POST['address_id'] ?? 0);
        $notes = trim($_POST['notes'] ?? '');
        $paymentMethod = $_POST['payment_method'] ?? 'cod';
        if (!in_array($paymentMethod, ['cod', 'vietqr'])) {
            $paymentMethod = 'cod';
        }

        // Nếu người dùng chọn địa chỉ đã lưu trong sổ địa chỉ user_addresses
        if ($addressId > 0) {
            $savedAddr = $userAddressModel->getById($addressId, $userId);
            if ($savedAddr) {
                $address = $savedAddr['full_address'];
                if (empty($name)) $name = $savedAddr['recipient_name'];
                if (empty($phone)) $phone = $savedAddr['recipient_phone'];
            }
        }

        // Kiểm tra dữ liệu bắt buộc: Họ tên, Số điện thoại và ĐẶC BIỆT LÀ ĐỊA CHỈ KHÔNG ĐƯỢC ĐỂ NULL / RỖNG
        if (empty($name) || empty($phone) || empty($address)) {
            $this->setFlash('error', 'Vui lòng điền đầy đủ Họ tên, Số điện thoại và Địa chỉ giao hàng rõ ràng (không được để trống)!');
            $this->redirect('checkout');
            return;
        }

        // Tự động lưu địa chỉ mới vào bảng user_addresses nếu người dùng chọn lưu hoặc chưa từng có địa chỉ
        $existingAddresses = $userAddressModel->getByUserId($userId);
        if (!empty($_POST['save_as_new_address']) || empty($existingAddresses)) {
            $userAddressModel->add([
                'user_id'         => $userId,
                'recipient_name'  => $name,
                'recipient_phone' => $phone,
                'full_address'    => $address,
                'street_address'  => $address,
                'is_default'      => (!empty($_POST['set_default_address']) || empty($existingAddresses)) ? 1 : 0
            ]);
        }

        // Kiểm tra tồn kho của từng sản phẩm trước khi chốt đơn
        $productModel = $this->model('Product');
        foreach ($cart as $productId => $item) {
            $prod = $productModel->getById($productId);
            if (!$prod || (int)$prod['stock_quantity'] < (int)$item['quantity']) {
                $avail = $prod ? (int)$prod['stock_quantity'] : 0;
                $this->setFlash('error', "Sản phẩm '{$item['name']}' trong kho chỉ còn {$avail} chiếc. Vui lòng cập nhật lại giỏ hàng.");
                $this->redirect('cart');
                return;
            }
        }

        // Tính toán tổng tiền
        $totalAmount = 0;
        foreach ($cart as $item) {
            $totalAmount += (float)$item['price'] * (int)$item['quantity'];
        }

        $voucher = $_SESSION['voucher'] ?? null;
        $discountAmount = 0;
        $voucherCode = null;

        if ($voucher) {
            $voucherCode = $voucher['code'];
            if ($voucher['discount_type'] === 'percent') {
                $discountAmount = ($totalAmount * (float)$voucher['discount_value']) / 100;
            } else {
                $discountAmount = (float)$voucher['discount_value'];
            }
        }

        $shippingFee = $this->calculateShipping($totalAmount);
        $finalAmount = max(0, $totalAmount - $discountAmount + $shippingFee);

        // Sinh mã đơn hàng ngẫu nhiên duy nhất (Ví dụ: ORD261016A1B2)
        $orderCode = 'ORD' . date('ymd') . strtoupper(substr(uniqid(), -4));

        $orderModel = $this->model('Order');

        try {
            $userId = isset($_SESSION['user']['id']) ? (int)$_SESSION['user']['id'] : null;

            $orderData = [
                'order_code'       => $orderCode,
                'user_id'          => $userId,
                'customer_name'    => $name,
                'customer_email'   => !empty($email) ? $email : ($phone . '@shoptonghop.vn'),
                'customer_phone'   => $phone,
                'shipping_address' => $address,
                'voucher_code'     => $voucherCode,
                'discount_amount'  => $discountAmount,
                'total_amount'     => $finalAmount,
                'payment_method'   => $paymentMethod,
                'payment_status'   => 'pending',
                'notes'            => !empty($notes) ? $notes : null
            ];

            // Tạo đơn hàng, lưu chi tiết order_items và tự động trừ tồn kho sản phẩm
            $orderId = $orderModel->create($orderData, array_values($cart));

            // Tăng số lượt sử dụng voucher nếu có áp dụng
            if (!empty($voucherCode)) {
                $this->model('Voucher')->incrementUsed($voucherCode);
            }

            // Dọn sạch giỏ hàng và voucher sau khi đặt hàng thành công
            $_SESSION['cart'] = [];
            unset($_SESSION['voucher']);

            // Lưu mã đơn hàng vào Session để hỗ trợ tra cứu
            $_SESSION['last_order_code'] = $orderCode;

            $this->redirect('checkout/success?code=' . urlencode($orderCode));
        } catch (\Exception $e) {
            $this->setFlash('error', 'Có lỗi xảy ra khi tạo đơn hàng: ' . $e->getMessage());
            $this->redirect('checkout');
        }
    }

    /**
     * 10. Trang Đặt hàng thành công & Mã VietQR tự động
     */
    public function success(): void {
        $orderCode = trim($_GET['code'] ?? ($_SESSION['last_order_code'] ?? ''));

        if (empty($orderCode)) {
            $this->redirect('products');
            return;
        }

        $orderModel = $this->model('Order');
        $order = $orderModel->getByCode($orderCode);

        if (!$order) {
            $this->setFlash('error', 'Không tìm thấy thông tin đơn hàng.');
            $this->redirect('products');
            return;
        }

        $orderItems = $orderModel->getItems((int)$order['id']);

        // Cấu hình tài khoản VietQR thụ hưởng
        $vietqr = [
            'bank_id'      => 'MB', // Ngân hàng Quân Đội MBBank
            'bank_name'    => 'MBBank (Ngân hàng TMCP Quân Đội)',
            'account_no'   => '0988888888',
            'account_name' => 'SHOPXPRESS VIETNAM',
            'amount'       => (int)$order['total_amount'],
            'memo'         => $order['order_code']
        ];

        // Link sinh ảnh VietQR chuẩn tự động từ api vietqr.io
        $qrUrl = "https://img.vietqr.io/image/{$vietqr['bank_id']}-{$vietqr['account_no']}-compact2.png" . 
                 "?amount={$vietqr['amount']}&addInfo=" . urlencode($vietqr['memo']) . 
                 "&accountName=" . urlencode($vietqr['account_name']);
        $vietqr['qr_url'] = $qrUrl;

        $this->view('cart/success', [
            'pageTitle'  => 'Đặt Hàng Thành Công | ' . APP_NAME,
            'order'      => $order,
            'orderItems' => $orderItems,
            'vietqr'     => $vietqr
        ]);
    }
}
