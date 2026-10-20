<?php
/**
 * ShopXpress - Auth Controller
 * Xử lý xác thực người dùng: Đăng nhập, Đăng ký, Đăng xuất và Quản lý hồ sơ cá nhân
 */

class AuthController extends Controller {

    /**
     * 1. Đăng nhập tài khoản (Khách hàng & Quản trị viên)
     */
    public function login(): void {
        // Nếu đã đăng nhập trước đó
        if (!empty($_SESSION['user'])) {
            $redirect = $_GET['redirect'] ?? ($_POST['redirect'] ?? '');
            if (!empty($redirect)) {
                $this->redirect($redirect);
            } elseif (($_SESSION['user']['role'] ?? '') === 'admin') {
                $this->redirect('admin');
            } else {
                $this->redirect('home');
            }
            return;
        }

        $redirect = $_GET['redirect'] ?? ($_POST['redirect'] ?? '');
        $username = '';
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';
            $remember = !empty($_POST['remember']);

            if (empty($username) || empty($password)) {
                $this->setFlash('error', 'Vui lòng nhập đầy đủ tên đăng nhập (hoặc email) và mật khẩu.');
            } else {
                $userModel = $this->model('User');
                // Tìm kiếm tài khoản qua username hoặc email
                $user = $userModel->findByUsername($username);
                if (!$user && filter_var($username, FILTER_VALIDATE_EMAIL)) {
                    $user = $userModel->findByEmail($username);
                }

                if (!$user) {
                    $this->setFlash('error', 'Tài khoản hoặc mật khẩu không chính xác.');
                } elseif (!empty($user['isDelete'])) {
                    $this->setFlash('error', 'Tài khoản này đã bị xóa hoặc ngừng kích hoạt trên hệ thống.');
                } elseif (($user['status'] ?? 'active') !== 'active') {
                    $this->setFlash('error', 'Tài khoản của bạn hiện đang bị tạm khóa. Vui lòng liên hệ quản trị viên.');
                } elseif (!$userModel->verifyPassword($password, $user['password'])) {
                    $this->setFlash('error', 'Tài khoản hoặc mật khẩu không chính xác.');
                } else {
                    // Đăng nhập thành công -> Lưu thông tin vào Session
                    $_SESSION['user'] = [
                        'id'       => (int)$user['id'],
                        'username' => $user['username'],
                        'fullname' => $user['fullname'],
                        'email'    => $user['email'],
                        'phone'    => $user['phone'] ?? '',
                        'address'  => $user['address'] ?? '',
                        'role'     => $user['role'] ?? 'customer',
                        'isAdmin'  => (int)($user['isAdmin'] ?? ($user['role'] === 'admin' ? 1 : 0))
                    ];

                    $this->setFlash('success', 'Đăng nhập thành công! Chào mừng ' . $user['fullname'] . ' đã quay trở lại.');

                    if (!empty($redirect)) {
                        $this->redirect($redirect);
                    } elseif ($user['role'] === 'admin') {
                        $this->redirect('admin');
                    } else {
                        $this->redirect('home');
                    }
                    return;
                }
            }
        }

        $this->view('auth/login', [
            'pageTitle' => 'Đăng Nhập Tài Khoản | ' . APP_NAME,
            'username'  => $username,
            'redirect'  => $redirect
        ]);
    }

    /**
     * 2. Đăng ký tài khoản mới cho khách hàng
     */
    public function register(): void {
        if (!empty($_SESSION['user'])) {
            $this->redirect('home');
            return;
        }

        $formData = [
            'fullname' => '',
            'username' => '',
            'email'    => '',
            'phone'    => '',
            'address'  => ''
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $formData['fullname'] = trim($_POST['fullname'] ?? '');
            $formData['username'] = trim($_POST['username'] ?? '');
            $formData['email']    = trim($_POST['email'] ?? '');
            $formData['phone']    = trim($_POST['phone'] ?? '');
            $formData['address']  = trim($_POST['address'] ?? '');
            $password             = $_POST['password'] ?? '';
            $confirmPassword      = $_POST['confirm_password'] ?? '';

            // Kiểm tra các trường bắt buộc
            if (empty($formData['fullname']) || empty($formData['username']) || empty($formData['email']) || empty($password)) {
                $this->setFlash('error', 'Vui lòng điền đầy đủ các thông tin bắt buộc.');
            } elseif (!filter_var($formData['email'], FILTER_VALIDATE_EMAIL)) {
                $this->setFlash('error', 'Địa chỉ email không đúng định dạng.');
            } elseif (strlen($password) < 6) {
                $this->setFlash('error', 'Mật khẩu phải chứa ít nhất 6 ký tự để đảm bảo an toàn.');
            } elseif ($password !== $confirmPassword) {
                $this->setFlash('error', 'Xác nhận mật khẩu không trùng khớp.');
            } else {
                $userModel = $this->model('User');
                // Kiểm tra trùng username
                if ($userModel->findByUsername($formData['username'])) {
                    $this->setFlash('error', "Tên đăng nhập '{$formData['username']}' đã được sử dụng. Vui lòng chọn tên khác.");
                } elseif ($userModel->findByEmail($formData['email'])) {
                    $this->setFlash('error', "Địa chỉ email '{$formData['email']}' đã được đăng ký tài khoản.");
                } else {
                    // Tạo tài khoản mới
                    $newUserId = $userModel->create([
                        'username' => $formData['username'],
                        'password' => $password,
                        'fullname' => $formData['fullname'],
                        'email'    => $formData['email'],
                        'phone'    => $formData['phone'],
                        'address'  => $formData['address'],
                        'role'     => 'customer',
                        'status'   => 'active'
                    ]);

                    if ($newUserId > 0) {
                        // Tự động thêm vào sổ địa chỉ nếu có điền địa chỉ
                        if (!empty($formData['address'])) {
                            $this->model('UserAddress')->add([
                                'user_id'         => $newUserId,
                                'recipient_name'  => $formData['fullname'],
                                'recipient_phone' => $formData['phone'] ?: '0988888888',
                                'full_address'    => $formData['address'],
                                'street_address'  => $formData['address'],
                                'is_default'      => 1
                            ]);
                        }

                        // Tự động đăng nhập
                        $_SESSION['user'] = [
                            'id'       => $newUserId,
                            'username' => $formData['username'],
                            'fullname' => $formData['fullname'],
                            'email'    => $formData['email'],
                            'phone'    => $formData['phone'],
                            'address'  => $formData['address'],
                            'role'     => 'customer',
                            'isAdmin'  => 0
                        ];

                        $this->setFlash('success', 'Đăng ký tài khoản thành công! Chào mừng bạn đến với ShopXpress.');
                        $this->redirect('home');
                        return;
                    } else {
                        $this->setFlash('error', 'Không thể tạo tài khoản lúc này. Vui lòng thử lại sau.');
                    }
                }
            }
        }

        $this->view('auth/register', [
            'pageTitle' => 'Đăng Ký Tài Khoản Mới | ' . APP_NAME,
            'formData'  => $formData
        ]);
    }

    /**
     * 3. Đăng xuất tài khoản
     */
    public function logout(): void {
        unset($_SESSION['user']);
        $this->setFlash('success', 'Bạn đã đăng xuất tài khoản thành công.');
        $this->redirect('home');
    }

    /**
     * 4. Xem và cập nhật Hồ sơ cá nhân & Quản lý Sổ địa chỉ
     */
    public function profile(): void {
        if (empty($_SESSION['user'])) {
            $this->setFlash('warning', 'Vui lòng đăng nhập để xem thông tin hồ sơ.');
            $this->redirect('login?redirect=profile');
            return;
        }

        $userModel = $this->model('User');
        $userAddressModel = $this->model('UserAddress');
        $userId = (int)$_SESSION['user']['id'];
        $user = $userModel->findById($userId);

        if (!$user) {
            $this->redirect('logout');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['profile_action'] ?? 'update_info';

            if ($action === 'update_info') {
                $fullname = trim($_POST['fullname'] ?? '');
                $phone = trim($_POST['phone'] ?? '');
                $address = trim($_POST['address'] ?? '');

                if (empty($fullname)) {
                    $this->setFlash('error', 'Họ và tên không được để trống.');
                } else {
                    $userModel->updateProfile($userId, [
                        'fullname' => $fullname,
                        'phone'    => $phone,
                        'address'  => $address
                    ]);

                    $_SESSION['user']['fullname'] = $fullname;
                    $_SESSION['user']['phone'] = $phone;
                    $_SESSION['user']['address'] = $address;

                    $this->setFlash('success', 'Cập nhật thông tin cá nhân thành công!');
                    $this->redirect('profile');
                    return;
                }
            } elseif ($action === 'change_password') {
                $currentPass = $_POST['current_password'] ?? '';
                $newPass = $_POST['new_password'] ?? '';
                $confirmPass = $_POST['confirm_new_password'] ?? '';

                if (!$userModel->verifyPassword($currentPass, $user['password'])) {
                    $this->setFlash('error', 'Mật khẩu hiện tại không chính xác.');
                } elseif (strlen($newPass) < 6) {
                    $this->setFlash('error', 'Mật khẩu mới phải có độ dài ít nhất 6 ký tự.');
                } elseif ($newPass !== $confirmPass) {
                    $this->setFlash('error', 'Xác nhận mật khẩu mới không trùng khớp.');
                } else {
                    $hashed = password_hash($newPass, PASSWORD_BCRYPT);
                    $userModel->updatePassword($userId, $hashed);
                    $this->setFlash('success', 'Đổi mật khẩu thành công! Vui lòng ghi nhớ mật khẩu mới.');
                    $this->redirect('profile');
                    return;
                }
            } elseif ($action === 'add_address') {
                $recipientName = trim($_POST['recipient_name'] ?? '');
                $recipientPhone = trim($_POST['recipient_phone'] ?? '');
                $fullAddress = trim($_POST['full_address'] ?? '');
                $isDefault = !empty($_POST['is_default']) ? 1 : 0;

                if (empty($recipientName) || empty($recipientPhone) || empty($fullAddress)) {
                    $this->setFlash('error', 'Vui lòng nhập đầy đủ Tên người nhận, Số điện thoại và Địa chỉ chi tiết.');
                } else {
                    $userAddressModel->add([
                        'user_id'         => $userId,
                        'recipient_name'  => $recipientName,
                        'recipient_phone' => $recipientPhone,
                        'full_address'    => $fullAddress,
                        'street_address'  => $fullAddress,
                        'is_default'      => $isDefault
                    ]);
                    $this->setFlash('success', 'Đã thêm địa chỉ giao hàng mới vào sổ địa chỉ!');
                    $this->redirect('profile');
                    return;
                }
            } elseif ($action === 'set_default_address') {
                $addrId = (int)($_POST['address_id'] ?? 0);
                if ($addrId > 0) {
                    $userAddressModel->setDefault($addrId, $userId);
                    $this->setFlash('success', 'Đã đặt địa chỉ làm mặc định thành công.');
                }
                $this->redirect('profile');
                return;
            } elseif ($action === 'delete_address') {
                $addrId = (int)($_POST['address_id'] ?? 0);
                if ($addrId > 0) {
                    $userAddressModel->delete($addrId, $userId);
                    $this->setFlash('success', 'Đã xóa địa chỉ khỏi sổ địa chỉ.');
                }
                $this->redirect('profile');
                return;
            }
        }

        // Lấy thống kê đơn hàng và danh sách địa chỉ của tài khoản
        $orderModel = $this->model('Order');
        $orderCounts = $orderModel->countByStatus($userId);
        $addresses = $userAddressModel->getByUserId($userId);

        $this->view('auth/profile', [
            'pageTitle'   => 'Hồ Sơ Cá Nhân | ' . APP_NAME,
            'user'        => $user,
            'orderCounts' => $orderCounts,
            'addresses'   => $addresses
        ]);
    }
}
