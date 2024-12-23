<?php
include '../includes/header.php';

// Nếu đã đăng nhập thì chuyển về trang chủ
if (isset($_SESSION['user_id'])) {
    header("Location: /Project_Be1/Booking-movies/");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $email = $_POST['email'];
    $fullname = $_POST['fullname'];
    $phone = $_POST['phone'];

    // Kiểm tra mật khẩu xác nhận
    if ($password !== $confirm_password) {
        $error = "Mật khẩu xác nhận không khớp!";
    } else {
        // Thực hiện đăng ký
        if ($userModel->register($username, $password, $email, $fullname, $phone)) {
            $_SESSION['register_success'] = true;
            header("Location: login.php");
            exit();
        } else {
            $error = "Tên đăng nhập hoặc email đã tồn tại!";
        }
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

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Đăng ký tài khoản</h4>
                </div>
                <div class="card-body">
                    <form method="POST" class="needs-validation" novalidate>
                        <div class="mb-3">
                            <label>Tên đăng nhập</label>
                            <input type="text" name="username" class="form-control" required
                                pattern="[a-zA-Z0-9]+" minlength="4">
                            <div class="invalid-feedback">
                                Tên đăng nhập phải có ít nhất 4 ký tự và chỉ chứa chữ cái và số
                            </div>
                        </div>

                        <div class="mb-3">
                            <label>Mật khẩu</label>
                            <input type="password" name="password" class="form-control" required
                                minlength="6">
                            <div class="invalid-feedback">
                                Mật khẩu phải có ít nhất 6 ký tự
                            </div>
                        </div>

                        <div class="mb-3">
                            <label>Xác nhận mật khẩu</label>
                            <input type="password" name="confirm_password" class="form-control" required>
                            <div class="invalid-feedback">
                                Vui lòng xác nhận mật khẩu
                            </div>
                        </div>

                        <div class="mb-3">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" required>
                            <div class="invalid-feedback">
                                Email không hợp lệ
                            </div>
                        </div>

                        <div class="mb-3">
                            <label>Họ tên</label>
                            <input type="text" name="fullname" class="form-control" required>
                            <div class="invalid-feedback">
                                Vui lòng nhập họ tên
                            </div>
                        </div>

                        <div class="mb-3">
                            <label>Số điện thoại</label>
                            <input type="tel" name="phone" class="form-control" required
                                pattern="[0-9]{10}">
                            <div class="invalid-feedback">
                                Số điện thoại không hợp lệ
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Đăng ký</button>
                        <a href="login.php" class="btn btn-link">Đã có tài khoản? Đăng nhập</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Kích hoạt validation của Bootstrap
    (function() {
        'use strict'
        var forms = document.querySelectorAll('.needs-validation')
        Array.prototype.slice.call(forms)
            .forEach(function(form) {
                form.addEventListener('submit', function(event) {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }
                    form.classList.add('was-validated')
                }, false)
            })
    })()

    document.addEventListener('DOMContentLoaded', function() {
        const toastElList = document.querySelectorAll('.toast');
        const toastList = [...toastElList].map(toastEl => {
            const toast = new bootstrap.Toast(toastEl, {
                autohide: true,
                delay: 3000
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