<?php
include 'includes/header.php';

// Lấy danh sách phim đang chiếu
$showing_movies = $movieModel->getAllMovies('now_showing');

// Lấy danh sách phim sắp chiếu
$coming_movies = $movieModel->getAllMovies('coming_soon');

// Lấy 5 phim nổi bật (có rating cao nhất)
$featured_movies = array_slice($showing_movies, 0, 5);
?>

<div class="container-fluid p-0">
    <div class="curtains">
        <div class="curtain curtain-left"></div>
        <div class="curtain curtain-right"></div>
        <!-- Slider phim nổi bật -->
        <div id="featuredMovies" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-indicators">
                <?php for ($i = 0; $i < count($featured_movies); $i++): ?>
                    <button type="button" data-bs-target="#featuredMovies" data-bs-slide-to="<?php echo $i; ?>"
                        <?php echo $i === 0 ? 'class="active"' : ''; ?>></button>
                <?php endfor; ?>
            </div>

            <div class="carousel-inner">
                <?php foreach ($featured_movies as $index => $movie): ?>
                    <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                        <div class="featured-movie-slide" style="background-image: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('./assets/img/<?php echo $movie['banner'] ?? $movie['poster']; ?>');">
                            <div class="container">
                                <div class="featured-movie-content">
                                    <h1><?php echo $movie['title']; ?></h1>
                                    <p class="movie-description"><?php echo substr($movie['description'], 0, 200); ?>...</p>
                                    <div class="movie-info">
                                        <span class="rating">
                                            <i class="fas fa-star"></i> <?php echo $movie['rates']; ?>
                                        </span>
                                        <span class="duration"><?php echo $movie['duration']; ?> phút</span>
                                    </div>
                                    <a href="movie.php?id=<?php echo $movie['id']; ?>" class="btn btn-primary mt-3">
                                        Đặt vé ngay
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

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

    document.addEventListener('DOMContentLoaded', function() {
        const curtains = document.querySelector('.curtains');

        if (curtains) {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        curtains.classList.add('active');
                    }
                });
            }, {
                threshold: 0.5
            });

            observer.observe(curtains);
        }
    });
</script>

<?php include 'includes/footer.php'; ?>