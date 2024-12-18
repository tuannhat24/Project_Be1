<?php 
include 'includes/header.php';

// Lấy danh sách phim đang chiếu
$showing_movies = $movieModel->getAllMovies('showing');

// Lấy danh sách phim sắp chiếu
$coming_movies = $movieModel->getAllMovies('coming_soon');
?>

<div class="container mt-4">
    <!-- Phim đang chiếu -->
    <h2 class="mb-4">Phim Đang Chiếu</h2>
    <div class="row">
        <?php foreach($showing_movies as $movie): ?>
        <div class="col-md-3 mb-4">
            <div class="card h-100">
                <img src="<?php echo $movie['poster']; ?>" class="card-img-top" 
                     alt="<?php echo $movie['title']; ?>">
                <div class="card-body">
                    <h5 class="card-title"><?php echo $movie['title']; ?></h5>
                    <p class="card-text">
                        <small>Thời lượng: <?php echo $movie['duration']; ?> phút</small>
                    </p>
                    <a href="movie.php?id=<?php echo $movie['id']; ?>" 
                       class="btn btn-primary">Chi tiết</a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Phim sắp chiếu -->
    <h2 class="mb-4 mt-5">Phim Sắp Chiếu</h2>
    <div class="row">
        <?php foreach($coming_movies as $movie): ?>
        <div class="col-md-3 mb-4">
            <div class="card h-100">
                <img src="<?php echo $movie['poster']; ?>" class="card-img-top" 
                     alt="<?php echo $movie['title']; ?>">
                <div class="card-body">
                    <h5 class="card-title"><?php echo $movie['title']; ?></h5>
                    <p class="card-text">
                        <small>Khởi chiếu: <?php echo date('d/m/Y', strtotime($movie['release_date'])); ?></small>
                    </p>
                    <a href="movie.php?id=<?php echo $movie['id']; ?>" 
                       class="btn btn-outline-primary">Chi tiết</a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<style>
.card-img-top {
    height: 400px;
    object-fit: cover;
}

.card-title {
    height: 48px;
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
}
</style>

<?php include 'includes/footer.php'; ?> 