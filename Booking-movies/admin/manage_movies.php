<?php
include '../includes/header.php';

// Kiểm tra đã login hay chưa
if (!isset($_SESSION['isLoggedIn']) || $_SESSION['isLoggedIn'] === false) {
    header("Location: /Project_Be1/Booking-movies/");
}

// Kiểm tra quyền admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: /Project_Be1/Booking-movies/");
    exit();
}

// Lấy danh sách thể loại
$allGenres = $genreModel->getAllGenres();

// Xử lý thêm/sửa phim
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'] ?? null;
    $title = $_POST['title'];
    $description = $_POST['description'];
    $duration = $_POST['duration'];
    $release_date = $_POST['release_date'];
    $status = $_POST['status'];

    $poster = null;
    if (is_uploaded_file($_FILES['poster']['tmp_name'])) {
        $poster = hash('sha256', time() . rand()) . '.' . pathinfo($_FILES['poster']['name'], PATHINFO_EXTENSION);
        $path = '../assets/img/' . $poster;

        if (!move_uploaded_file($_FILES['poster']['tmp_name'], $path)) {
            $error = "Không thể tải tệp hình ảnh lên.";
            return;
        }
    } else if ($id) {
        $movie = $movieModel->getMovieById($id);
    }

    $selectedGenres = $_POST['genres'] ?? [];

    if ($id) {
        // Xử lý CẬP NHẬT phim
        $updateResult = $movieModel->updateMovie($id, $title, $description, $duration, $release_date, $status, $poster);
        if ($updateResult) {
            $genreModel->updateMovieGenres($id, $selectedGenres);
            $success = "Cập nhật phim thành công!";
        } else {
            $error = "Có lỗi xảy ra khi cập nhật phim!";
        }
    } else {
        // Xử lý THÊM phim mới
        if (!$poster) {
            $error = "Vui lòng tải lên poster phim!";
        } else {
            $addResult = $movieModel->addMovie($title, $description, $duration, $release_date, $status, $poster);
            if ($addResult) {
                $newMovieId = self::$connection->insert_id;
                $genreModel->updateMovieGenres($newMovieId, $selectedGenres);
                $_SESSION['notification'] = "Thêm phim thành công!";
                header("Location: /Project_Be1/Booking-movies/admin/manage_movies.php");
                exit();
            } else {
                $error = "Có lỗi xảy ra khi thêm phim!";
            }
        }
    }
}

// Xử lý xóa phim
if (isset($_GET['delete'])) {
    if ($movieModel->deleteMovie($_GET['delete'])) {
        $success = "Xóa phim thành công!";
    } else {
        $error = "Có lỗi xảy ra khi xóa phim!";
    }
}

// Lấy danh sách phim
$movies = $movieModel->getAllMovies();
?>

<div class="container mt-4">
    <h2>Quản lý phim</h2>

    <?php if (isset($success)): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#movieModal">
        Thêm phim mới
    </button>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Poster</th>
                    <th>Tên phim</th>
                    <th>Thời lượng</th>
                    <th>Ngày khởi chiếu</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($movies as $movie): ?>
                    <tr>
                        <td><?php echo $movie['id']; ?></td>
                        <td>
                            <?php if ($movie['poster']): ?>
                                <img src="../assets/img/<?php echo $movie['poster']; ?>" height="50">
                            <?php endif; ?>
                        </td>
                        <td><?php echo $movie['title']; ?></td>
                        <td><?php echo $movie['duration']; ?> phút</td>
                        <td><?php echo date('d/m/Y', strtotime($movie['release_date'])); ?></td>
                        <td>
                            <?php
                            $status_text = [
                                'now_showing' => 'Đang chiếu',
                                'coming_soon' => 'Sắp chiếu',
                                'ended' => 'Đã kết thúc'
                            ];
                            echo $status_text[$movie['status']];
                            ?>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-info edit-movie"
                                data-movie='<?php echo json_encode($movie); ?>'>
                                Sửa
                            </button>
                            <a href="?delete=<?php echo $movie['id']; ?>"
                                class="btn btn-sm btn-danger"
                                onclick="return confirm('Bạn có chắc muốn xóa phim này?')">
                                Xóa
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal thêm/sửa phim -->
<div class="modal fade" id="movieModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thêm/Sửa Phim</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="movieForm" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id" id="movie-id">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label>Tên phim</label>
                                <input type="text" name="title" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label>Thời lượng (phút)</label>
                                <input type="number" name="duration" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label>Ngày khởi chiếu</label>
                                <input type="date" name="release_date" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label>Trạng thái</label>
                                <select name="status" class="form-control">
                                    <option value="now_showing">Đang chiếu</option>
                                    <option value="coming_soon">Sắp chiếu</option>
                                    <option value="ended">Đã kết thúc</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label>Poster</label>
                                <input type="file" name="poster" class="form-control" accept="image/*">
                                <img id="poster-preview" src="#" alt="Preview" style="max-width: 200px; margin-top: 10px; display: none;">
                            </div>
                            <div class="mb-3">
                                <label>Mô tả</label>
                                <textarea name="description" class="form-control" rows="4"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Phần thể loại -->
                    <div class="mb-3">
                        <label>Thể loại</label>
                        <div class="row">
                            <?php 
                            $allGenres = $genreModel->getAllGenres();
                            $totalGenres = count($allGenres);
                            $halfCount = ceil($totalGenres / 2);
                            ?>
                            
                            <!-- Cột trái -->
                            <div class="col-md-6">
                                <?php for($i = 0; $i < $halfCount; $i++): ?>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" 
                                               name="genres[]" 
                                               value="<?php echo $allGenres[$i]['id']; ?>" 
                                               id="genre<?php echo $allGenres[$i]['id']; ?>">
                                        <label class="form-check-label" for="genre<?php echo $allGenres[$i]['id']; ?>">
                                            <?php echo $allGenres[$i]['name']; ?>
                                        </label>
                                    </div>
                                <?php endfor; ?>
                            </div>
                            
                            <!-- Cột phải -->
                            <div class="col-md-6">
                                <?php for($i = $halfCount; $i < $totalGenres; $i++): ?>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" 
                                               name="genres[]" 
                                               value="<?php echo $allGenres[$i]['id']; ?>" 
                                               id="genre<?php echo $allGenres[$i]['id']; ?>">
                                        <label class="form-check-label" for="genre<?php echo $allGenres[$i]['id']; ?>">
                                            <?php echo $allGenres[$i]['name']; ?>
                                        </label>
                                    </div>
                                <?php endfor; ?>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Lưu</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Thêm script để xử lý preview ảnh và chọn thể loại khi sửa -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Xử lý preview ảnh
    const posterInput = document.querySelector('input[name="poster"]');
    const posterPreview = document.getElementById('poster-preview');
    
    posterInput.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                posterPreview.src = e.target.result;
                posterPreview.style.display = 'block';
            }
            reader.readAsDataURL(this.files[0]);
        }
    });

    // Xử lý khi click nút sửa
    const editButtons = document.querySelectorAll('.edit-movie');
    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            const movie = JSON.parse(this.dataset.movie);
            const form = document.getElementById('movieForm');
            
            // Điền thông tin phim
            form.id.value = movie.id;
            form.title.value = movie.title;
            form.description.value = movie.description;
            form.duration.value = movie.duration;
            form.release_date.value = movie.release_date.split(' ')[0];
            form.status.value = movie.status;

            // Hiển thị poster hiện tại
            if (movie.poster) {
                posterPreview.src = '../assets/img/' + movie.poster;
                posterPreview.style.display = 'block';
            }

            // Đánh dấu các thể loại đã chọn
            const movieGenres = <?php echo json_encode($movieModel->getMovieGenres($movie['id'] ?? 0)); ?>;
            const genreIds = movieGenres.map(g => g.id);
            
            document.querySelectorAll('input[name="genres[]"]').forEach(checkbox => {
                checkbox.checked = genreIds.includes(parseInt(checkbox.value));
            });
        });
    });
});
</script>

<?php include '../includes/footer.php'; ?>