<?php
// session_start();
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


    if ($id) {
        // Xử lý CẬP NHẬT phim
        $updateResult = $movieModel->updateMovie($id, $title, $description, $duration, $release_date, $status, $poster);

        if ($updateResult) {
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
                $_SESSION['notification'] = "Thêm phim thành công!";
                header("Location: http://localhost/Project_Be1/Booking-movies/admin/manage_movies.php");
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
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thêm/Sửa phim</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST" enctype="multipart/form-data" id="movieForm">
                    <input type="hidden" name="id" id="movie-id">
                    <div class="mb-3">
                        <label>Tên phim</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Mô tả</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
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
                        <label>Poster</label>
                        <div class="mb-2">
                            <img id="poster-preview" src="#" alt="Poster Preview" style="max-height: 150px;">
                        </div>
                        <input type="file" name="poster" class="form-control" accept="image/*">
                    </div>
                    <div class="mb-3">
                        <label>Trạng thái</label>
                        <select name="status" class="form-control">
                            <option value="now_showing">Đang chiếu</option>
                            <option value="coming_soon">Sắp chiếu</option>
                            <option value="ended">Đã kết thúc</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Lưu</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const editButtons = document.querySelectorAll('.edit-movie');
        const movieModal = new bootstrap.Modal(document.getElementById('movieModal'));
        const movieForm = document.getElementById('movieForm');

        editButtons.forEach(button => {
            button.addEventListener('click', function() {
                const movie = JSON.parse(this.dataset.movie);

                movieForm.id.value = movie.id;
                movieForm.title.value = movie.title;
                movieForm.description.value = movie.description;
                movieForm.duration.value = movie.duration;
                movieForm.release_date.value = movie.release_date.split(' ')[0];
                movieForm.status.value = movie.status;

                // Hiển thị hình ảnh poster, nếu có
                const posterPreview = document.getElementById('poster-preview');
                if (movie.poster) {
                    posterPreview.src = '../assets/img/' + movie.poster;
                    posterPreview.style.display = 'block';
                } else {
                    posterPreview.src = '#';
                    posterPreview.style.display = 'none';
                }

                movieModal.show();
            });
        });

        document.querySelector('[data-bs-target="#movieModal"]').addEventListener('click', function() {
            movieForm.reset();
            movieForm.id.value = '';
            const posterPreview = document.getElementById('poster-preview');
            posterPreview.src = '#';
            posterPreview.style.display = 'none';
        });
    });
</script>

<?php include '../includes/footer.php'; ?>