<?php
/**
 * ShopXpress - Review Controller
 * Tiếp nhận và xử lý đánh giá xếp hạng sao từ khách hàng
 */

class ReviewController extends Controller {

    public function add(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('products');
            return;
        }

        $productId = (int)($_POST['product_id'] ?? 0);
        $rating = max(1, min(5, (int)($_POST['rating'] ?? 5)));
        $comment = trim($_POST['comment'] ?? '');

        if ($productId <= 0 || empty($comment)) {
            $this->setFlash('error', 'Vui lòng nhập nội dung đánh giá và chọn số sao.');
            $redirectUrl = !empty($_POST['redirect_url']) ? $_POST['redirect_url'] : "product/detail?id={$productId}";
            $this->redirect($redirectUrl);
            return;
        }

        // Bắt buộc phải có tài khoản đã đăng nhập mới được gửi đánh giá
        if (empty($_SESSION['user'])) {
            $this->setFlash('warning', 'Vui lòng đăng nhập tài khoản để gửi nhận xét và đánh giá sản phẩm!');
            $this->redirect('auth/login');
            return;
        }

        $userId = (int)$_SESSION['user']['id'];

        $reviewModel = $this->model('Review');
        $reviewModel->create([
            'product_id' => $productId,
            'user_id'    => $userId,
            'rating'     => $rating,
            'comment'    => $comment
        ]);

        $this->setFlash('success', 'Cảm ơn bạn đã gửi đánh giá sản phẩm thành công!');
        
        $redirectUrl = !empty($_POST['redirect_url']) ? $_POST['redirect_url'] : "product/detail?id={$productId}#reviewsTab";
        $this->redirect($redirectUrl);
    }
}
