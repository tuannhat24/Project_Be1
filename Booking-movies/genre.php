<?php
include 'includes/header.php';

// Lấy các tham số lọc từ URL
$genre = isset($_GET['genre']) ? $_GET['genre'] : '';
$year = isset($_GET['year']) ? $_GET['year'] : '';
$country = isset($_GET['country']) ? $_GET['country'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';

// Lấy danh sách phim theo bộ lọc
$movies = $movieModel->getFilteredMovies($genre, $year, $country, $sort);

// Lấy danh sách năm (từ phim cũ nhất đến mới nhất)
$years = $movieModel->getMovieYears();

// Lấy danh sách quốc gia
$countries = $movieModel->getMovieCountries();

// Thêm vào đầu file
$genreModel = new GenreModel();
$allGenres = $genreModel->getAllGenres();
?>

<div class="container mt-4">
    <!-- Phần bộ lọc -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="genre-filter-section">
                <form action="" method="GET" class="row g-3">
                    <!-- Bộ lọc thể loại -->
                    <div class="col-md-3">
                        <label class="form-label">Thể loại</label>
                        <select name="genre" class="form-select">
                            <option value="">Tất cả</option>
                            <?php foreach ($allGenres as $g): ?>
                                <option value="<?php echo $g['slug']; ?>" 
                                        <?php echo $genre == $g['slug'] ? 'selected' : ''; ?>>
                                    <?php echo $g['name']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Bộ lọc năm -->
                    <div class="col-md-3">
                        <label class="form-label">Năm</label>
                        <select name="year" class="form-select">
                            <option value="">Tất cả</option>
                            <?php foreach ($years as $y): ?>
                                <option value="<?php echo $y; ?>" <?php echo $year == $y ? 'selected' : ''; ?>>
                                    <?php echo $y; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Bộ lọc quốc gia -->
                    <div class="col-md-3">
                        <label class="form-label">Quốc gia</label>
                        <select name="country" class="form-select">
                            <option value="">Tất cả</option>
                            <?php foreach ($countries as $c): ?>
                                <option value="<?php echo $c; ?>" <?php echo $country == $c ? 'selected' : ''; ?>>
                                    <?php echo $c; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Sắp xếp -->
                    <div class="col-md-3">
                        <label class="form-label">Sắp xếp</label>
                        <select name="sort" class="form-select">
                            <option value="newest" <?php echo $sort == 'newest' ? 'selected' : ''; ?>>Mới nhất</option>
                            <option value="oldest" <?php echo $sort == 'oldest' ? 'selected' : ''; ?>>Cũ nhất</option>
                            <option value="rating" <?php echo $sort == 'rating' ? 'selected' : ''; ?>>Đánh giá cao</option>
                            <option value="name" <?php echo $sort == 'name' ? 'selected' : ''; ?>>Tên A-Z</option>
                        </select>
                    </div>

                    <div class="col-12 d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">Lọc phim</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Hiển thị kết quả -->
    <div class="genre-movies-grid">
        <?php if (empty($movies)): ?>
            <div class="genre-no-results">
                <p>Không tìm thấy phim phù hợp.</p>
            </div>
        <?php else: ?>
            <?php foreach ($movies as $movie): ?>
                <div class="genre-movie-card">
                    <a href="movie.php?id=<?php echo $movie['id']; ?>" class="card-link">
                        <img src="./assets/img/<?php echo $movie['poster']; ?>" 
                             class="genre-movie-img" 
                             alt="<?php echo $movie['title']; ?>">
                        <div class="genre-movie-body">
                            <h5 class="genre-movie-title"><?php echo $movie['title']; ?></h5>
                            <p class="genre-movie-text">
                                <small>Rating: <?php echo $movie['rates']; ?> ⭐</small><br>
                                <small>Năm: <?php echo date('Y', strtotime($movie['release_date'])); ?></small>
                            </p>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?> 