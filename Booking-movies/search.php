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
            <div class="row g-4">
                <?php foreach ($movies as $movie): ?>
                    <div class="col-lg-3 col-md-4 col-sm-6">
                        <div class="card shadow-sm h-100 border-0">
                            <img src="./assets/img/<?php echo $movie['poster']; ?>" class="card-img-top rounded-top"
                                alt="<?php echo $movie['title']; ?>">
                            <div class="card-body">
                                <h5 class="card-title text-dark text-truncate"><?php echo $movie['title']; ?></h5>
                                <p class="card-text text-muted">
                                    <small>
                                        Thời lượng: <?php echo $movie['duration']; ?> phút<br>
                                        Trạng thái:
                                        <?php
                                        $status_text = [
                                            'now_showing' => '<span class=\"badge bg-success\">Đang chiếu</span>',
                                            'coming_soon' => '<span class=\"badge bg-warning\">Sắp chiếu</span>',
                                            'ended' => '<span class=\"badge bg-secondary\">Đã kết thúc</span>'
                                        ];
                                        echo $status_text[$movie['status']];
                                        ?>
                                    </small>
                                </p>
                                <a href="movie.php?id=<?php echo $movie['id']; ?>"
                                    class="btn btn-outline-primary btn-sm w-100">Chi tiết</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>