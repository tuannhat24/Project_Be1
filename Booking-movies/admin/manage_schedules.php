<?php
// session_start();
include '../includes/header.php';

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
    $theater_id = $_POST['theater_id'];
    $show_time = $_POST['show_time'];
    $price = $_POST['price'];

    if ($_POST['id']) {
        // Cập nhật lịch chiếu
        $existingSchedule = $scheduleModel->getScheduleByMovieTheaterTime($movie_id, $theater_id, $show_time, $price);
        if ($existingSchedule) {
            $error = "Lịch chiếu này đã tồn tại, không thể sửa!";
        } else if ($scheduleModel->updateSchedule($_POST['id'], $movie_id, $theater_id, $show_time, $price)) {
            $success = "Cập nhật lịch chiếu thành công!";
        } else {
            $error = "Có lỗi xảy ra khi cập nhật lịch chiếu!";
        }
    } else {
        // Kiểm tra lịch chiếu đã tồn tại chưa
        $existingSchedule = $scheduleModel->getScheduleByMovieTheaterTime($movie_id, $theater_id, $show_time, $price);
        if ($existingSchedule) {
            $error = "Lịch chiếu này đã tồn tại!";
        } else {
            // Thêm lịch chiếu mới
            if ($scheduleModel->addSchedule($movie_id, $theater_id, $show_time, $price)) {
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
// Lấy danh sách rạp
$theaters = $theaterModel->getAllTheaters();
// Lấy danh sách lịch chiếu
$schedules = $scheduleModel->getAllSchedules();
?>

<div class="container mt-4">
    <h2>Quản lý lịch chiếu</h2>

    <?php if (isset($success)): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

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
                    <th>Thời gian chiếu</th>
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
                        <td><?php echo date('d/m/Y H:i', strtotime($schedule['show_time'])); ?></td>
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
                        <label>Rạp</label>
                        <select name="theater_id" class="form-control" required>
                            <?php foreach ($theaters as $theater): ?>
                                <option value="<?php echo $theater['id']; ?>">
                                    <?php echo $theater['name']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Thời gian chiếu</label>
                        <input type="datetime-local" name="show_time" class="form-control" required>
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
        const editButtons = document.querySelectorAll('.edit-schedule');
        const scheduleModal = new bootstrap.Modal(document.getElementById('scheduleModal'));
        const scheduleForm = document.getElementById('scheduleForm');

        editButtons.forEach(button => {
            button.addEventListener('click', function() {
                const schedule = JSON.parse(this.dataset.schedule);

                scheduleForm.id.value = schedule.id;
                scheduleForm.movie_id.value = schedule.movie_id;
                scheduleForm.theater_id.value = schedule.theater_id;
                scheduleForm.show_time.value = schedule.show_time.slice(0, 16);
                scheduleForm.price.value = schedule.price;

                scheduleModal.show();
            });
        });

        document.querySelector('[data-bs-target="#scheduleModal"]').addEventListener('click', function() {
            scheduleForm.reset();
            scheduleForm.id.value = '';
        });
    });
</script>

<?php include '../includes/footer.php'; ?>