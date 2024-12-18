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

    $result = $userModel->login($username, $password);

    if (is_array($result) && isset($result['error'])) {
        $error = $result['error'];
    } else if ($result) {
        $_SESSION['user_id'] = $result['id'];
        $_SESSION['username'] = $result['username'];
        $_SESSION['role'] = $result['role'];
        $_SESSION['isLoggedIn'] = true;

        header("Location: index.php");
        exit();
    } else {
        $error = "Tên đăng nhập hoặc mật khẩu không đúng!";
    }
}
?>

<div class="container mt-4">
    <?php
    if (!empty($_SESSION['notification'])) :
    ?>
        <div class="alert alert-danger" role="alert">
            <?php echo $_SESSION['notification'] ?>
        </div>
    <?php
        $_SESSION['notification'] = '';
    endif;
    ?>
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Đăng nhập</h4>
                </div>
                <div class="card-body">
                    <?php if (isset($_SESSION['register_success'])): ?>
                        <div class="alert alert-success">
                            Đăng ký thành công! Vui lòng đăng nhập.
                        </div>
                        <?php unset($_SESSION['register_success']); ?>
                    <?php endif; ?>

                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label>Tên đăng nhập</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label>Mật khẩu</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>

                        <button type="submit" class="btn btn-primary">Đăng nhập</button>
                        <a href="register.php" class="btn btn-link">Chưa có tài khoản? Đăng ký</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>