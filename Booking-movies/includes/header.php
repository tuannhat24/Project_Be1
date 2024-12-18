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
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Movies</title>
    <link rel="stylesheet" href="/Project_Be1/Booking-movies/assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
                        <li class="nav-item">
                            <a class="nav-link" href="/Project_Be1/Booking-movies/user/profile.php">Tài Khoản</a>
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