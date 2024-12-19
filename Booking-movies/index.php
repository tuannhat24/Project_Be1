<?php
include 'includes/header.php';

// Lấy danh sách phim đang chiếu
$showing_movies = $movieModel->getAllMovies('now_showing');

// Lấy danh sách phim sắp chiếu
$coming_movies = $movieModel->getAllMovies('coming_soon');
?>

<div class="container mt-4 home-page">
    <!-- Phim đang chiếu -->
    <section>
        <h2 class="mb-4">Phim Đang Chiếu</h2>
        <div class="stars">
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
        </div>
        <div class="row">
            <?php foreach ($showing_movies as $movie): ?>
                <div class="col-md-3 mb-5 mt-5">
                    <a href="movie.php?id=<?php echo $movie['id']; ?>" class="card-link">
                        <div class="card h-100 movie-card">
                            <img src="./assets/img/<?php echo $movie['poster']; ?>" class="card-img-top movie-img"
                                alt="<?php echo $movie['title']; ?>">
                            <div class="card-body movie-card-body">
                                <h5 class="card-title"><?php echo $movie['title']; ?></h5>
                                <p class="card-text">
                                    <small>Rating: <?php echo $movie['rates']; ?><img src="./assets/img/star-solid.svg" alt="" style="height: 12px; margin: 0 2px 3px;"></small>
                                </p>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Phim sắp chiếu -->
    <h2 class="mb-4 mt-5">Phim Sắp Chiếu</h2>
    <div class="row">
        <?php foreach ($coming_movies as $movie): ?>
            <div class="col-md-3 mb-4">
                <a href="movie.php?id=<?php echo $movie['id']; ?>" class="card-link">
                    <div class="card h-100">
                        <img src="./assets/img/<?php echo $movie['poster']; ?>" class="card-img-top movie-img"
                            alt="<?php echo $movie['title']; ?>">
                        <div class="card-body">
                            <h5 class="card-title" style="color: #222;"><?php echo $movie['title']; ?></h5>
                            <p class="card-text" style="color: #888;">
                                <small>Khởi chiếu: <?php echo date('d/m/Y', strtotime($movie['release_date'])); ?></small>
                            </p>
                        </div>
                    </div>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>