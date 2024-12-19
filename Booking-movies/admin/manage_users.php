<?php
include '../includes/header.php';

// Kiểm tra đăng nhập và quyền admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: /Project_Be1/Booking-movies/");
    exit();
}

// Xử lý khóa/mở khóa tài khoản
if (isset($_POST['update_status'])) {
    $user_id = $_POST['user_id'];
    $status = $_POST['status'];
    
    // Debug
    error_log("Received update status request: User ID=$user_id, Status=$status");
    
    if ($userModel->updateUserStatus($user_id, $status)) {
        $success = "Cập nhật trạng thái tài khoản thành công!";
    } else {
        $error = "Có lỗi xảy ra khi cập nhật trạng thái tài khoản!";
    }
}

// Xử lý cập nhật thông tin user
if (isset($_POST['update_user'])) {
    $user_id = $_POST['user_id'];
    $email = $_POST['email'];
    $fullname = $_POST['fullname'];
    $phone = $_POST['phone'];
    $role = $_POST['role'];
    
    if ($userModel->updateUserByAdmin($user_id, $email, $fullname, $phone, $role)) {
        $success = "Cập nhật thông tin người dùng thành công!";
    } else {
        $error = "Có lỗi xảy ra khi cập nhật thông tin người dùng!";
    }
}

// Lấy danh sách users
$users = $userModel->getAllUsers();
?>

<div class="container mt-4">
    <h2 class="mb-4">Quản lý người dùng</h2>

    <?php if (isset($success)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert" id="successAlert">
            <?php echo $success; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert" id="errorAlert">
            <?php echo $error; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tên đăng nhập</th>
                    <th>Email</th>
                    <th>Họ tên</th>
                    <th>Số điện thoại</th>
                    <th>Vai trò</th>
                    <th>Trạng thái</th>
                    <th>Ngày tạo</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo $user['id']; ?></td>
                        <td><?php echo $user['username']; ?></td>
                        <td><?php echo $user['email']; ?></td>
                        <td><?php echo $user['fullname']; ?></td>
                        <td><?php echo $user['phone']; ?></td>
                        <td>
                            <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="update_user">
                                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                    <select name="role" class="form-select form-select-sm" onchange="this.form.submit()">
                                        <option value="user" <?php echo $user['role'] == 'user' ? 'selected' : ''; ?>>
                                            Người dùng
                                        </option>
                                        <option value="admin" <?php echo $user['role'] == 'admin' ? 'selected' : ''; ?>>
                                            Quản trị viên
                                        </option>
                                    </select>
                                </form>
                            <?php else: ?>
                                <?php echo $user['role'] == 'admin' ? 'Quản trị viên' : 'Người dùng'; ?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($user['id'] != $_SESSION['user_id'] && $user['role'] != 'admin'): ?>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="update_status" value="1">
                                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                        <option value="active" <?php echo $user['status'] == 'active' ? 'selected' : ''; ?>>
                                            Hoạt động
                                        </option>
                                        <option value="blocked" <?php echo $user['status'] == 'blocked' ? 'selected' : ''; ?>>
                                            Đã khóa
                                        </option>
                                    </select>
                                </form>
                            <?php else: ?>
                                <span class="text-success">Hoạt động</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo date('d/m/Y H:i', strtotime($user['created_at'])); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Xử lý thông báo success
    const successAlert = document.getElementById('successAlert');
    if (successAlert) {
        setTimeout(function() {
            const alert = bootstrap.Alert.getOrCreateInstance(successAlert);
            alert.close();
        }, 2000);
    }

    // Xử lý thông báo error
    const errorAlert = document.getElementById('errorAlert');
    if (errorAlert) {
        setTimeout(function() {
            const alert = bootstrap.Alert.getOrCreateInstance(errorAlert);
            alert.close();
        }, 2000);
    }
});
</script> 