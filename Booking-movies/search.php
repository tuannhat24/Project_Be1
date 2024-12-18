<?php
include 'includes/header.php';

$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
$movies = array();

if ($keyword) {
    $movies = $movieModel->searchMovies($keyword);
}
?>

<div class="container mt-5 mb-5">
    <h2 class="mb-5">Tìm Kiếm Phim</h2>

    <form method="GET" class="mb-4">
        <div class="input-group">
            <input type="text" name="keyword" class="form-control"
                placeholder="Nhập tên phim cần tìm..."
                value="<?php echo htmlspecialchars($keyword); ?>" required>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search"></i> Tìm kiếm
            </button>
        </div>
    </form>

    <?php if ($keyword): ?>
        <?php if (empty($movies)): ?>
            <div class="alert alert-info">
                Không tìm thấy phim nào phù hợp với từ khóa "<?php echo htmlspecialchars($keyword); ?>".
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
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>