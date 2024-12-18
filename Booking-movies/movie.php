<?php
include 'includes/header.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$movie_id = $_GET['id'];
$movie_details = $movieModel->getMovieById($movie_id);

if (empty($movie_details)) {
    header("Location: index.php");
    exit();
}

$movie = $movie_details[0];
$schedules = $scheduleModel->getSchedulesByMovie($movie_id);
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-4">
            <img src="./assets/img/<?php echo $movie['poster']; ?>" class="img-fluid rounded"
                alt="<?php echo $movie['title']; ?>">
        </div>
        <div class="col-md-8">
            <h1><?php echo $movie['title']; ?></h1>

            <div class="mb-3">
                <strong>Thời lượng:</strong> <?php echo $movie['duration']; ?> phút<br>
                <strong>Khởi chiếu:</strong> <?php echo date('d/m/Y', strtotime($movie['release_date'])); ?><br>
                <strong>Trạng thái:</strong>
                <?php
                $status_text = [
                    'now_showing' => 'Đang chiếu',
                    'coming_soon' => 'Sắp chiếu',
                    'ended' => 'Đã kết thúc'
                ];
                echo $status_text[$movie['status']];
                ?>
            </div>

            <div class="mb-4">
                <h5>Nội dung phim:</h5>
                <p><?php echo nl2br($movie['description']); ?></p>
            </div>

            <?php if ($movie['status'] == 'now_showing' && !empty($schedules)): ?>
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Lịch chiếu</h5>
                    </div>
                    <div class="card-body">
                        <?php
                        $current_date = '';
                        foreach ($schedules as $schedule):
                            $schedule_date = date('Y-m-d', strtotime($schedule['show_time']));
                            if ($schedule_date != $current_date):
                                if ($current_date != '') echo '</div>';
                                $current_date = $schedule_date;
                        ?>
                                <h6 class="mb-3">
                                    <?php echo date('d/m/Y', strtotime($schedule_date)); ?>
                                </h6>
                                <div class="mb-4">
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
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>