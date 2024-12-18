<?php
session_start(); // Khởi động session

// Hủy tất cả session
session_unset();
session_destroy();

// Chuyển hướng người dùng về trang đăng nhập
header("Location: /Project_Be1/Booking-movies/user/login.php");
exit();
