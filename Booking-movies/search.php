<?php
include 'includes/header.php';

$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
$movies = array();

if ($keyword) {
    $movies = $movieModel->searchMovies($keyword);
}
?>

<link rel="stylesheet" href="assets/css/search.css">
<div class="container mt-5 mb-5">
    <div class="search-container text-center">
        <h2 class="search-title mb-4">Tìm Kiếm Phim</h2>

        <form method="GET" class="search-form mb-4">
            <div class="input-group">
                <input type="text" name="keyword" class="form-control search-input"
                    placeholder="Nhập tên phim cần tìm..."
                    value="<?php echo htmlspecialchars($keyword); ?>">
                <button type="submit" class="btn btn-primary search-button">
                    <i class="fas fa-search"></i> Tìm kiếm
                </button>
            </div>
        </form>

        <?php if (!$keyword): ?>
            <div class="empty-search-message">
                <img src="assets/img/search-icon.png" alt="Search" class="search-icon mb-2">
                <p>Bạn chưa tìm kiếm phim nào...</p>
            </div>
        <?php elseif (empty($movies)): ?>
            <div class="no-results-message">
                <p>Không tìm thấy phim nào phù hợp với từ khóa "<?php echo htmlspecialchars($keyword); ?>"</p>
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($movies as $movie): ?>
                    <div class="col-md-3 mb-4">
                        <div class="card h-100">
                            <img src="./assets/img/<?php echo $movie['poster']; ?>" class="card-img-top"
                                alt="<?php echo $movie['title']; ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $movie['title']; ?></h5>
                                <p class="card-text">
                                    <small>
                                        Thời lượng: <?php echo $movie['duration']; ?> phút<br>
                                        Trạng thái:
                                        <?php
                                        $status_text = [
                                            'now_showing' => 'Đang chiếu',
                                            'coming_soon' => 'Sắp chiếu',
                                            'ended' => 'Đã kết thúc'
                                        ];
                                        echo $status_text[$movie['status']];
                                        ?>
                                    </small>
                                </p>
                                <a href="movie.php?id=<?php echo $movie['id']; ?>"
                                    class="btn btn-primary">Chi tiết</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>