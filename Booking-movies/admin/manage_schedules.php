<?php
include '../includes/header.php';
require_once '../app/models/Room.php';
require_once '../app/common/Pagination.php';

// Khởi tạo các model
$roomModel = new Room();

// Kiểm tra đã login hay chưa
if (!isset($_SESSION['isLoggedIn']) || $_SESSION['isLoggedIn'] === false) {
    header("Location: /Project_Be1/Booking-movies/");
}

// Kiểm tra quyền admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: /Project_Be1/Booking-movies/");
    exit();
}

// Xử lý thêm/sửa lịch chiếu
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $movie_id = $_POST['movie_id'];
    $room_id = $_POST['room_id'];
    $show_date = $_POST['show_date'];
    $show_time = $_POST['show_time'];
    $price = $_POST['price'];

    if (isset($_POST['id'])) {
        // Cập nhật lịch chiếu
        $existingSchedule = $scheduleModel->getScheduleByMovieRoomDateTime($movie_id, $room_id, $show_date, $show_time);
        if ($existingSchedule && $existingSchedule[0]['id'] != $_POST['id']) {
            $error = "Lịch chiếu này đã tồn tại, không thể sửa!";
        } else if ($scheduleModel->updateSchedule($_POST['id'], $movie_id, $room_id, $show_date, $show_time, $price)) {
            $success = "Cập nhật lịch chiếu thành công!";
        } else {
            $error = "Có lỗi xảy ra khi cập nhật lịch chiếu!";
        }
    } else {
        // Kiểm tra lịch chiếu đã tồn tại chưa
        $existingSchedule = $scheduleModel->getScheduleByMovieRoomDateTime($movie_id, $room_id, $show_date, $show_time);
        if ($existingSchedule) {
            $error = "Lịch chiếu này đã tồn tại!";
        } else {
            // Thêm lịch chiếu mới
            if ($scheduleModel->addSchedule($movie_id, $room_id, $show_date, $show_time, $price)) {
                $success = "Thêm lịch chiếu mới thành công!";
            } else {
                $error = "Có lỗi xảy ra khi thêm lịch chiếu!";
            }
        }
    }
}

// Xử lý xóa lịch chiếu
if (isset($_GET['delete'])) {
    if ($scheduleModel->deleteSchedule($_GET['delete'])) {
        $success = "Xóa lịch chiếu thành công!";
    } else {
        $error = "Có lỗi xảy ra khi xóa lịch chiếu!";
    }
}

// Lấy danh sách phim đang chiếu và sắp chiếu
$movies = $movieModel->getAllMovies();
// Lấy danh sách phòng chiếu
$rooms = $roomModel->getAllRooms();

// Thêm vào sau khi khởi tạo các biến cần thiết
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$itemsPerPage = 10;

$totalSchedules = $scheduleModel->getTotalSchedules();
$pagination = new Pagination($totalSchedules, $itemsPerPage, $page);
$schedules = $scheduleModel->getSchedulesByPagination($pagination->getOffset(), $pagination->getLimit());
?>

<div class="container mt-4">
    <h2>Quản lý lịch chiếu</h2>

    <div class="toast-container">
        <?php if (isset($success)): ?>
            <div class="toast custom-toast align-items-center text-white bg-success border-0" role="alert">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="fas fa-check-circle me-2"></i>
                        <?php echo $success; ?>
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        <?php endif; ?>

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

    <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#scheduleModal">
        Thêm lịch chiếu mới
    </button>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Phim</th>
                    <th>Rạp</th>
                    <th>Phòng</th>
                    <th>Ngày chiếu</th>
                    <th>Giờ chiếu</th>
                    <th>Giá vé</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($schedules as $schedule): ?>
                    <tr>
                        <td><?php echo $schedule['id']; ?></td>
                        <td><?php echo $schedule['movie_title']; ?></td>
                        <td><?php echo $schedule['theater_name']; ?></td>
                        <td><?php echo $schedule['room_name']; ?></td>
                        <td><?php echo date('d/m/Y', strtotime($schedule['show_date'])); ?></td>
                        <td><?php echo date('H:i', strtotime($schedule['show_time'])); ?></td>
                        <td><?php echo number_format($schedule['price']); ?>đ</td>
                        <td>
                            <button class="btn btn-sm btn-info edit-schedule"
                                data-schedule='<?php echo json_encode($schedule); ?>'>
                                Sửa
                            </button>
                            <a href="?delete=<?php echo $schedule['id']; ?>"
                                class="btn btn-sm btn-danger"
                                onclick="return confirm('Bạn có chắc muốn xóa lịch chiếu này?')">
                                Xóa
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php echo $pagination->createLinks('manage_schedules.php'); ?>
    </div>
</div>

<!-- Modal thêm/sửa lịch chiếu -->
<div class="modal fade" id="scheduleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thêm/Sửa lịch chiếu</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST" id="scheduleForm">
                    <input type="hidden" name="id" id="schedule-id">
                    <div class="mb-3">
                        <label>Phim</label>
                        <select name="movie_id" class="form-control" required>
                            <?php foreach ($movies as $movie): ?>
                                <option value="<?php echo $movie['id']; ?>">
                                    <?php echo $movie['title']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Phòng chiếu</label>
                        <select name="room_id" class="form-control" required>
                            <?php foreach ($rooms as $room): ?>
                                <option value="<?php echo $room['id']; ?>">
                                    <?php echo $room['name'] . ' - ' . $room['theater_name']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Ngày chiếu</label>
                        <input type="date" name="show_date" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Giờ chiếu</label>
                        <input type="time" name="show_time" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Giá vé</label>
                        <input type="number" name="price" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Lưu</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Xử lý thông báo tự động ẩn sau 2s
        const successAlert = document.getElementById('successAlert');
        if (successAlert) {
            setTimeout(function() {
                const alert = bootstrap.Alert.getOrCreateInstance(successAlert);
                alert.close();
            }, 2000);
        }

        const errorAlert = document.getElementById('errorAlert');
        if (errorAlert) {
            setTimeout(function() {
                const alert = bootstrap.Alert.getOrCreateInstance(errorAlert);
                alert.close();
            }, 2000);
        }

        // Xử lý form sửa lịch chiếu
        const editButtons = document.querySelectorAll('.edit-schedule');
        const scheduleModal = new bootstrap.Modal(document.getElementById('scheduleModal'));
        const scheduleForm = document.getElementById('scheduleForm');

        editButtons.forEach(button => {
            button.addEventListener('click', function() {
                const schedule = JSON.parse(this.dataset.schedule);

                scheduleForm.id.value = schedule.id;
                scheduleForm.movie_id.value = schedule.movie_id;
                scheduleForm.room_id.value = schedule.room_id;
                scheduleForm.show_date.value = schedule.show_date;
                scheduleForm.show_time.value = schedule.show_time;
                scheduleForm.price.value = schedule.price;

                scheduleModal.show();
            });
        });

        // Reset form khi thêm mới
        document.querySelector('[data-bs-target="#scheduleModal"]').addEventListener('click', function() {
            scheduleForm.reset();
            scheduleForm.id.value = '';
        });


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