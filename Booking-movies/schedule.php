<?php
include 'includes/header.php';
require_once 'app/models/TheaterModel.php';

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
            <?php foreach($dates as $date): ?>
            <div class="col">
                <a href="?date=<?php echo $date; ?><?php echo $selected_theater ? '&theater='.$selected_theater : ''; ?>" 
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
            <?php foreach($theaters as $theater): ?>
            <a href="?date=<?php echo $selected_date; ?>&theater=<?php echo $theater['id']; ?>" 
               class="btn btn-outline-secondary <?php echo $selected_theater == $theater['id'] ? 'active' : ''; ?>">
                <?php echo $theater['name']; ?>
            </a>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Danh sách lịch chiếu -->
    <?php if(empty($schedules)): ?>
        <div class="alert alert-info">
            Không có suất chiếu nào trong ngày này.
        </div>
    <?php else: ?>
        <?php
        $current_movie = '';
        foreach($schedules as $schedule):
            if ($schedule['title'] != $current_movie):
                if ($current_movie != '') echo '</div></div></div>';
                $current_movie = $schedule['title'];
        ?>
        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <img src="<?php echo $schedule['poster']; ?>" class="img-fluid rounded" 
                             alt="<?php echo $schedule['title']; ?>">
                    </div>
                    <div class="col-md-9">
                        <h4><?php echo $schedule['title']; ?></h4>
                        <div class="showtimes">
        <?php endif; ?>
                            
        <a href="user/booking.php?schedule_id=<?php echo $schedule['id']; ?>" 
           class="btn btn-outline-primary me-2 mb-2">
            <?php echo date('H:i', strtotime($schedule['show_time'])); ?>
            - <?php echo $schedule['theater_name']; ?>
            (<?php echo number_format($schedule['price']); ?>đ)
        </a>
                            
        <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
.date-nav .btn {
    padding: 10px;
    margin-bottom: 5px;
}

.theater-nav {
    overflow-x: auto;
    white-space: nowrap;
    padding-bottom: 5px;
}

.theater-nav .btn-group {
    display: inline-flex;
}

.showtimes {
    margin-top: 15px;
}
</style>

<?php include 'includes/footer.php'; ?> 