<?php
include 'includes/header.php';
require_once 'app/models/TheaterModel.php';
require_once 'app/models/Schedule.php';

$scheduleModel = new Schedule();
$theaterModel = new TheaterModel();

$theaters = $theaterModel->getAllTheaters();

// Lấy ngày được chọn, mặc định là hôm nay
$selected_date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
$selected_theater = isset($_GET['theater']) ? $_GET['theater'] : null;

// Lấy danh sách lịch chiếu theo ngày và rạp
$schedules = $scheduleModel->getSchedulesByDate($selected_date, $selected_theater);

// Tạo mảng ngày để hiển thị (7 ngày tính từ hôm nay)
$dates = array();
for ($i = 0; $i < 7; $i++) {
    $date = date('Y-m-d', strtotime("+$i days"));
    $dates[] = $date;
}
?>

<div class="container mt-4">
    <h2 class="mb-4">Lịch Chiếu Phim</h2>

    <!-- Thanh chọn ngày -->
    <div class="date-nav mb-4">
        <div class="row">
            <?php foreach ($dates as $date): ?>
                <div class="col">
                    <a href="?date=<?php echo $date; ?><?php echo $selected_theater ? '&theater=' . $selected_theater : ''; ?>"
                        class="btn btn-outline-primary w-100 <?php echo $date == $selected_date ? 'active' : ''; ?>">
                        <?php
                        echo date('d/m', strtotime($date));
                        if ($date == date('Y-m-d')) echo ' (Hôm nay)';
                        ?>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Thanh chọn rạp -->
    <div class="theater-nav mb-4">
        <div class="btn-group">
            <a href="?date=<?php echo $selected_date; ?>"
                class="btn btn-outline-secondary <?php echo !$selected_theater ? 'active' : ''; ?>">
                Tất cả rạp
            </a>
            <?php foreach ($theaters as $theater): ?>
                <a href="?date=<?php echo $selected_date; ?>&theater=<?php echo $theater['id']; ?>"
                    class="btn btn-outline-secondary <?php echo $selected_theater == $theater['id'] ? 'active' : ''; ?>">
                    <?php echo $theater['name']; ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Danh sách lịch chiếu -->
    <?php if (empty($schedules)): ?>
        <div class="alert alert-info">
            Không có suất chiếu nào trong ngày này.
        </div>
    <?php else: ?>
        <?php
        // Nhóm lịch chiếu theo movie để phục vụ hiển thị
        $grouped_schedules = [];
        foreach ($schedules as $schedule) {
            $movie_id = $schedule['movie_id'];
            if (!isset($grouped_schedules[$movie_id])) {
                $grouped_schedules[$movie_id] = [
                    'title' => $schedule['title'],
                    'poster' => $schedule['poster'],
                    'schedules' => []
                ];
            }
            $grouped_schedules[$movie_id]['schedules'][] = $schedule;
        }

        foreach ($grouped_schedules as $movie_id => $movie_data):
        ?>
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <img src="./assets/img/<?php echo htmlspecialchars($movie_data['poster']); ?>" class="img-fluid rounded"
                                alt="<?php echo htmlspecialchars($movie_data['title']); ?>" style="height: 200px;">
                        </div>
                        <div class="col-md-9">
                            <h4><?php echo htmlspecialchars($movie_data['title']); ?></h4>
                            <strong>Loại phòng:</strong>
                            <?php
                            // Nhóm lịch chiếu theo loại phòng
                            $room_group = [];
                            foreach ($movie_data['schedules'] as $sch) {
                                $room = $sch['room_type'];
                                if (!isset($room_group[$room])) {
                                    $room_group[$room] = [];
                                }
                                $room_group[$room][] = $sch;
                            }

                            foreach ($room_group as $room_type => $room_schedules):
                            ?>
                                <div class="mt-2">
                                    <strong><?php echo htmlspecialchars($room_type); ?>:</strong>
                                    <?php foreach ($room_schedules as $sch): ?>
                                        <a href="user/booking.php?schedule_id=<?php echo $sch['id']; ?>" class="btn btn-outline-primary me-2 mb-2">
                                            <?php echo date('H:i', strtotime($sch['show_time'])); ?>
                                            - <?php echo htmlspecialchars($sch['theater_name']); ?>
                                            (<?php echo number_format($sch['price']); ?>đ)
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>