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
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Thông tin cá nhân</h5>
                </div>
                <div class="card-body">
                    <?php if(isset($success)): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                    <?php endif; ?>
                    
                    <?php if(isset($error)): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>

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
                    <?php if(isset($password_success)): ?>
                        <div class="alert alert-success"><?php echo $password_success; ?></div>
                    <?php endif; ?>
                    
                    <?php if(isset($password_error)): ?>
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
                                    <th>Suất chiếu</th>
                                    <th>Ghế</th>
                                    <th>Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($bookings as $booking): ?>
                                <tr>
                                    <td>#<?php echo $booking['id']; ?></td>
                                    <td><?php echo $booking['title']; ?></td>
                                    <td><?php echo $booking['theater_name']; ?></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($booking['show_time'])); ?></td>
                                    <td><?php echo $booking['seat_number']; ?></td>
                                    <td>
                                        <?php
                                        $status_text = [
                                            'pending' => 'Đang chờ',
                                            'confirmed' => 'Đã xác nhận',
                                            'cancelled' => 'Đã hủy'
                                        ];
                                        echo $status_text[$booking['status']];
                                        ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?> 