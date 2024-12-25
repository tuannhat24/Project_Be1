<?php
include 'includes/header.php';
require_once 'app/models/CommentModel.php';
require_once 'app/models/RatingModel.php';


// Khởi tạo model
$commentModel = new CommentModel();
$ratingModel = new RatingModel();


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

// Xử lý bình luận
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment'])) {
    // Kiểm tra đã login hay chưa
    if (!isset($_SESSION['isLoggedIn']) || $_SESSION['isLoggedIn'] === false) {
        header("Location: /Project_Be1/Booking-movies/");
        exit();
    }

    $comment = trim($_POST['comment']);
    $user_id = $_SESSION['user_id'] ?? null; 
    if (!empty($comment) && $user_id !== null) { 
        $commentModel->addComment($movie_id, $user_id, $comment); 
        header("Location: movie.php?id=$movie_id");
        exit();
    } else {
        $error = "Bình luận không được để trống hoặc bạn chưa đăng nhập.";
    }
}

// Xử lý đánh giá
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['rating'])) {
    $rating = $_POST['rating'];
    $user_id = $_SESSION['user_id'] ?? null; 

    if (!empty($rating) && $user_id !== null) { 
        $ratingModel->addOrUpdateRating($movie_id, $user_id, $rating); 
        header("Location: movie.php?id=$movie_id");
        exit();
    } else {
        $error = "Bạn chưa đăng nhập.";
    }
}

// Lấy bình luận và đánh giá
$comments = $commentModel->getComments($movie_id);
$ratings = $ratingModel->getAverageRating($movie_id);
$average_rating = is_array($ratings) ? $ratings['average_rating'] : $ratings;
?>

<div class="container mt-4 movie-details">
    <!-- Chi tiết phim -->
    <div class="row">
        <div class="col-md-4">
            <img src="./assets/img/<?php echo $movie['poster']; ?>" class="img-fluid rounded" alt="<?php echo $movie['title']; ?>">
        </div>
        <div class="col-md-8">
            <h1><?php echo $movie['title']; ?></h1>

            <div class="mb-3">
                <strong>Thời lượng:</strong> <?php echo $movie['duration']; ?> phút<br>
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

            <!-- Hiển thị Rating -->
            <div class="mb-3">
                <strong>Đánh giá trung bình:</strong>
                <span class="rating">
                    <?php echo $movie['rates']; ?>/10
                    <img src="./assets/img/star-solid.svg" alt="rating star" style="height: 24px; margin-left: 5px;">
                </span>
            </div>

            <div class="mb-4">
                <h5>Nội dung phim:</h5>
                <p><?php echo nl2br($movie['description']); ?></p>
            </div>
            <strong>Khởi chiếu:</strong> <?php echo date('d/m/Y', strtotime($movie['release_date'])); ?><br>
            <a href="trailer.php?movie_id=<?php echo $movie_id; ?>" title="Xem Trailer">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512" width="14" height="14">
                    <path fill="#ff1f1f" d="M73 39c-14.8-9.1-33.4-9.4-48.5-.9S0 62.6 0 80L0 432c0 17.4 9.4 33.4 24.5 41.9s33.7 8.1 48.5-.9L361 297c14.3-8.7 23-24.2 23-41s-8.7-32.2-23-41L73 39z" />
                </svg> Xem trailer
            </a>

            <div class="mb-4">
                <h5>Đánh giá phim:</h5>
                <div class="rating">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <span class="star" data-value="<?php echo $i; ?>">
                            <img src="./assets/img/star-empty.svg" alt="star" class="star-icon" />
                        </span>
                    <?php endfor; ?>
                </div>
                <input type="hidden" name="rating" id="rating" value="0" />
                <button type="button" class="btn btn-primary mt-2" id="submit-rating">Gửi đánh giá</button>

            </div>

            <!-- Phần lịch chiếu -->
            <?php if ($movie['status'] == 'now_showing' && !empty($schedules)): ?>
                <div class="card mt-4">
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
                                <h6 class="mb-3"><?php echo date('d/m/Y', strtotime($schedule_date)); ?></h6>
                                <div class="mb-4">
                                <?php endif; ?>

                                <a href="user/booking.php?schedule_id=<?php echo $schedule['id']; ?>" class="btn btn-outline-primary me-2 mb-2">
                                    <?php echo date('H:i', strtotime($schedule['show_time'])); ?>
                                    - <?php echo $schedule['theater_name']; ?>
                                    (<?php echo number_format($schedule['price']); ?>đ) -
                                    <strong>Phòng:</strong> <?php echo isset($schedule['room_type']) ? htmlspecialchars($schedule['room_type']) : 'Không xác định'; ?>
                                </a>

                            <?php endforeach; ?>
                                </div>
                    </div>
                </div>
            <?php endif; ?>
            
            <div class="mb-4">
                <h5>Bình luận:</h5>
                <form action="" method="POST">
                    <textarea name="comment" class="form-control" rows="3" required></textarea>
                    <button type="submit" class="btn btn-primary mt-2">Gửi bình luận</button>
                </form>
            </div>

            <div class="mb-4">
                <h5>Các bình luận:</h5>
                <ul class="list-unstyled">
                    <?php if (count($comments) > 0): ?>
                        <?php foreach ($comments as $comment): ?>
                            <?php
                            $user = $userModel->find($comment['user_id']);
                            ?>
                            <li>
                                <strong><?php echo htmlspecialchars($user['username']); ?>:</strong>
                                <?php echo htmlspecialchars($comment['comment']); ?>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li class="empty text-center text-muted">Chưa có bình luận nào</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const stars = document.querySelectorAll('.star');
        const ratingInput = document.getElementById('rating');

        stars.forEach(star => {
            star.addEventListener('click', function() {
                const value = this.getAttribute('data-value');
                ratingInput.value = value;

                // Cập nhật hình ảnh ngôi sao
                stars.forEach(s => {
                    s.querySelector('.star-icon').src = '/assets/img/star-empty.svg';
                });
                for (let i = 0; i < value; i++) {
                    stars[i].querySelector('.star-icon').src = './assets/img/star-solid.svg';
                }
            });
        });

        document.getElementById('submit-rating').addEventListener('click', function() {
            const rating = ratingInput.value;
            if (rating > 0) {
                alert('Đánh giá của bạn là: ' + rating + ' sao');
            } else {
                alert('Vui lòng chọn đánh giá!');
            }
        });
    });
</script>

<?php include 'includes/footer.php'; ?>