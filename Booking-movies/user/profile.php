<?php
include '../includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_details = $userModel->getUserById($user_id);
$user = $user_details[0];

// Xử lý cập nhật thông tin
if (isset($_POST['update_profile'])) {
    $email = $_POST['email'];
    $fullname = $_POST['fullname'];
    $phone = $_POST['phone'];

    if ($userModel->updateProfile($user_id, $email, $fullname, $phone)) {
        $success = "Cập nhật thông tin thành công!";
        $user['email'] = $email;
        $user['fullname'] = $fullname;
        $user['phone'] = $phone;
    } else {
        $error = "Có lỗi xảy ra khi cập nhật thông tin!";
    }
}

// Xử lý đổi mật khẩu
if (isset($_POST['change_password'])) {
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password !== $confirm_password) {
        $password_error = "Mật khẩu xác nhận không khớp!";
    } else if ($userModel->changePassword($user_id, $old_password, $new_password)) {
        $password_success = "Đổi mật khẩu thành công!";
    } else {
        $password_error = "Mật khẩu cũ không đúng!";
    }
}

// Lấy lịch sử đặt vé
$bookings = $bookingModel->getUserBookings($user_id);

if (isset($_POST['cancel_booking'])) {
    $booking_id = $_POST['cancel_booking'];
    if ($bookingModel->updateBookingStatus($booking_id, 'cancelled')) {
        $cancel_success = "Hủy vé thành công!";
        // Cập nhật lại danh sách đặt vé
        $bookings = $bookingModel->getUserBookings($user_id);
    } else {
        $cancel_error = "Có lỗi xảy ra khi hủy vé!";
    }
}

?>

<div class="toast-container">
    <?php if (isset($cancel_success)): ?>
        <div class="toast custom-toast align-items-center text-white bg-success border-0" role="alert">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fas fa-check-circle me-2"></i>
                    <?php echo $cancel_success; ?>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    <?php endif; ?>

    <?php if (isset($cancel_error)): ?>
        <div class="toast custom-toast align-items-center text-white bg-danger border-0" role="alert">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <?php echo $cancel_error; ?>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    <?php endif; ?>
</div>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Thông tin cá nhân</h5>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <input type="hidden" name="update_profile">
                        <div class="mb-3">
                            <label>Tên đăng nhập</label>
                            <input type="text" class="form-control" value="<?php echo $user['username']; ?>" readonly>
                        </div>
                        <div class="mb-3">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control"
                                value="<?php echo $user['email']; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label>Họ tên</label>
                            <input type="text" name="fullname" class="form-control"
                                value="<?php echo $user['fullname']; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label>Số điện thoại</label>
                            <input type="tel" name="phone" class="form-control"
                                value="<?php echo $user['phone']; ?>" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Cập nhật thông tin</button>
                    </form>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">Đổi mật khẩu</h5>
                </div>
                <div class="card-body">
                    <?php if (isset($password_success)): ?>
                        <div class="alert alert-success"><?php echo $password_success; ?></div>
                    <?php endif; ?>

                    <?php if (isset($password_error)): ?>
                        <div class="alert alert-danger"><?php echo $password_error; ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <input type="hidden" name="change_password">
                        <div class="mb-3">
                            <label>Mật khẩu cũ</label>
                            <input type="password" name="old_password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Mật khẩu mới</label>
                            <input type="password" name="new_password" class="form-control"
                                minlength="6" required>
                        </div>
                        <div class="mb-3">
                            <label>Xác nhận mật khẩu mới</label>
                            <input type="password" name="confirm_password" class="form-control"
                                minlength="6" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Đổi mật khẩu</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Lịch sử đặt vé</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Mã vé</th>
                                    <th>Phim</th>
                                    <th>Rạp</th>
                                    <th>Phòng</th>
                                    <th>Suất chiếu</th>
                                    <th>Ghế</th>
                                    <th>Giá vé</th>
                                    <th>Trạng thái</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($bookings as $booking): ?>
                                    <tr>
                                        <td>#<?php echo $booking['id']; ?></td>
                                        <td><?php echo $booking['title']; ?></td>
                                        <td><?php echo $booking['theater_name']; ?></td>
                                        <td><?php echo $booking['room_name']; ?></td>
                                        <td>
                                            <?php
                                            $show_datetime = date('d/m/Y', strtotime($booking['show_date'])) . ' ' .
                                                date('H:i', strtotime($booking['show_time']));
                                            echo $show_datetime;
                                            ?>
                                        </td>
                                        <td><?php echo $booking['seat_codes']; ?></td>
                                        <td><?php echo number_format($booking['price']); ?>đ</td>
                                        <td>
                                            <?php
                                            $status_text = [
                                                'pending' => 'Đang chờ',
                                                'confirmed' => 'Đã xác nhận',
                                                'cancelled' => 'Đã hủy'
                                            ];
                                            $status_class = [
                                                'pending' => 'text-warning',
                                                'confirmed' => 'text-success',
                                                'cancelled' => 'text-danger'
                                            ];
                                            ?>
                                            <span class="<?php echo $status_class[$booking['status']]; ?>">
                                                <?php echo $status_text[$booking['status']]; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($booking['status'] != 'cancelled'): ?>
                                                <form method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn hủy vé này?');">
                                                    <input type="hidden" name="cancel_booking" value="<?php echo $booking['id']; ?>">
                                                    <button type="submit" class="btn btn-danger btn-sm">
                                                        <i class="fas fa-times"></i> Hủy
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php if (empty($bookings)): ?>
                        <div class="text-center py-3">
                            <p class="text-muted">Bạn chưa có lịch sử đặt vé nào.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Khởi tạo tất cả toast
    const toastElList = document.querySelectorAll('.toast');
    const toastList = [...toastElList].map(toastEl => {
        const toast = new bootstrap.Toast(toastEl, {
            autohide: true,
            delay: 3000
        });
        toast.show();
        return toast;
    });

    // Thêm class show sau khi toast được khởi tạo
    setTimeout(() => {
        document.querySelectorAll('.custom-toast').forEach(toast => {
            toast.classList.add('show');
        });
    }, 100);

    // Xử lý animation khi đóng toast
    toastElList.forEach(toastEl => {
        toastEl.addEventListener('hide.bs.toast', function() {
            this.classList.remove('show');
        });
    });
});
</script>

<?php include '../includes/footer.php'; ?>