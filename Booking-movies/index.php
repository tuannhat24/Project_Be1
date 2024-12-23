<?php
include 'includes/header.php';

// Lấy danh sách phim đang chiếu
$showing_movies = $movieModel->getAllMovies('now_showing');

// Lấy danh sách phim sắp chiếu
$coming_movies = $movieModel->getAllMovies('coming_soon');

// Lấy banners (tối đa 10 banner)
$banners = $bannerModel->getAllActiveBanners(10);

// Nếu không có banner nào, lấy 5 phim nổi bật
if (empty($banners)) {
    $banners = array_slice($showing_movies, 0, 5);
}
?>

<div class="container-fluid p-0">
    <!-- Slider phim nổi bật -->
    <div id="featuredMovies" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-indicators">
            <?php for ($i = 0; $i < count($banners); $i++): ?>
                <button type="button" data-bs-target="#featuredMovies" data-bs-slide-to="<?php echo $i; ?>"
                    <?php echo $i === 0 ? 'class="active"' : ''; ?>></button>
            <?php endfor; ?>
        </div>

        <div class="carousel-inner">
            <?php foreach ($banners as $index => $banner): ?>
                <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                    <div class="featured-movie-slide" style="background-image: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), 
                        url('./assets/img/<?php echo $banner['image']; ?>');">
                        <div class="container">
                            <div class="featured-movie-content">
                                <h1><?php echo $banner['title']; ?></h1>
                                <p class="movie-description">
                                    <?php echo substr($banner['description'], 0, 200) . '...'; ?>
                                </p>
                                <div class="movie-info">
                                    <?php if (isset($banner['rates'])): ?>
                                        <span class="rating">
                                            <i class="fas fa-star"></i> <?php echo $banner['rates']; ?>
                                        </span>
                                    <?php endif; ?>
                                    <?php if (isset($banner['duration'])): ?>
                                        <span class="duration"><?php echo $banner['duration']; ?> phút</span>
                                    <?php endif; ?>
                                </div>
                                <?php if (isset($banner['movie_id'])): ?>
                                    <a href="movie.php?id=<?php echo $banner['movie_id']; ?>" class="btn btn-primary mt-3">
                                        Đặt vé ngay
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Carousel controls -->
        <button class="carousel-control-prev" type="button" data-bs-target="#featuredMovies" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#featuredMovies" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>
    </div>
</div>

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
</div>

<!-- Thêm script cho auto slide -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var myCarousel = new bootstrap.Carousel(document.getElementById('featuredMovies'), {
            interval: 5000, // Thời gian chuyển slide (5 giây)
            wrap: true
        });
    });
</script>

<?php include 'includes/footer.php'; ?>