<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/Project_Be1/Booking-movies/config/database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Project_Be1/Booking-movies/app/models/db.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Project_Be1/Booking-movies/app/models/User.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Project_Be1/Booking-movies/app/models/Movie.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Project_Be1/Booking-movies/app/models/Schedule.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Project_Be1/Booking-movies/app/models/Booking.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Project_Be1/Booking-movies/app/models/TheaterModel.php';

// Khởi tạo các đối tượng model
$userModel = new User();
$movieModel = new Movie();
$theaterModel = new TheaterModel();
$scheduleModel = new Schedule();
$bookingModel = new Booking();
?>

<?php
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $user_details = $userModel->getUserById($user_id);
    $user = $user_details[0];
} ?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Movies</title>
    <link rel="stylesheet" href="/Project_Be1/Booking-movies/assets/css/style.css">
    <link rel="stylesheet" href="/Project_Be1/Booking-movies/assets/css/pagination.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="/Project_Be1/Booking-movies/">Booking Movies</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/Project_Be1/Booking-movies/schedule.php">Lịch Chiếu</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/Project_Be1/Booking-movies/search.php">Tìm Kiếm</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <?php if ($_SESSION['role'] == 'admin'): ?>
                            <li class="nav-item">
                                <?php $current_page = $_SERVER['REQUEST_URI']; ?>
                                <select class="form-select" onchange="window.location.href=this.value">
                                    <option value="/Project_Be1/Booking-movies/admin/manage_movies.php"
                                        <?php if ($current_page == '/Project_Be1/Booking-movies/admin/manage_movies.php') echo 'selected'; ?>>Manage Movies</option>
                                    <option value="/Project_Be1/Booking-movies/admin/manage_schedules.php"
                                        <?php if ($current_page == '/Project_Be1/Booking-movies/admin/manage_schedules.php') echo 'selected'; ?>>Manage Schedules</option>
                                    <option value="/Project_Be1/Booking-movies/admin/manage_tickets.php"
                                        <?php if ($current_page == '/Project_Be1/Booking-movies/admin/manage_tickets.php') echo 'selected'; ?>>Manage Tickets</option>
                                    <option value="/Project_Be1/Booking-movies/admin/manage_users.php"
                                        <?php if ($current_page == '/Project_Be1/Booking-movies/admin/manage_users.php') echo 'selected'; ?>>Manage Users</option>
                                </select>
                            </li>
                        <?php endif; ?>

                        <li class="nav-item">
                            <a class="nav-link" href="/Project_Be1/Booking-movies/user/profile.php">
                                <?php
                                if ($user) {
                                    echo 'Hello, ' . $user['username'];
                                } else {
                                    echo 'Tài khoản';
                                }
                                ?>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/Project_Be1/Booking-movies/user/logout.php">Đăng Xuất</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/Project_Be1/Booking-movies/user/login.php">Đăng Nhập</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/Project_Be1/Booking-movies/user/register.php">Đăng Ký</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
</body>

</html>