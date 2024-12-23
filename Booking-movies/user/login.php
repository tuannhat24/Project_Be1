<?php
include '../includes/header.php';

// Nếu đã đăng nhập thì chuyển về trang chủ
if (isset($_SESSION['isLoggedIn']) && $_SESSION['isLoggedIn'] === true) {
    header("Location: /Project_Be1/Booking-movies/");
    exit();
}

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $result = $userModel->login($username, $password);

    if (is_array($result) && isset($result['error'])) {
        $error = $result['error'];
    } else if ($result) {
        $_SESSION['user_id'] = $result['id'];
        $_SESSION['username'] = $result['username'];
        $_SESSION['role'] = $result['role'];
        $_SESSION['isLoggedIn'] = true;

        // Redirect về trang chủ
        header("Location: /Project_Be1/Booking-movies/");
        exit();
    } else {
        $error = "Tên đăng nhập hoặc mật khẩu không đúng!";
    }
}
?>

<div class="toast-container">
    <?php if (isset($error)): ?>
        <div class="toast custom-toast align-items-center text-white bg-danger border-0" role="alert">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <?php echo $error; ?>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    <?php endif; ?>
</div>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="text-center">Đăng nhập</h4>
                </div>
                <div class="card-body">

                    <form method="POST">
                        <div class="mb-3">
                            <label>Tên đăng nhập</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Mật khẩu</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <button type="submit" name="login" class="btn btn-primary w-100">Đăng nhập</button>
                    </form>

                    <div class="text-center mt-3">
                        <p>Chưa có tài khoản? <a href="register.php">Đăng ký ngay</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toastElList = document.querySelectorAll('.toast');
        const toastList = [...toastElList].map(toastEl => {
            const toast = new bootstrap.Toast(toastEl, {
                autohide: true,
                delay: 2000
            });
            toast.show();
            return toast;
        });

        setTimeout(() => {
            document.querySelectorAll('.custom-toast').forEach(toast => {
                toast.classList.add('show');
            });
        }, 100);

        toastElList.forEach(toastEl => {
            toastEl.addEventListener('hide.bs.toast', function() {
                this.classList.remove('show');
            });
        });
    });
</script>

<?php include '../includes/footer.php'; ?>