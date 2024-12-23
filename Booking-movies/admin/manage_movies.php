<?php
include '../includes/header.php';
require_once '../app/common/Pagination.php';

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
$totalGenres = count($allGenres);
$halfCount = ceil($totalGenres / 2);

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
                $newMovieId = $movieModel->getLastInsertId();
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

// Thêm vào sau khi khởi tạo các biến cần thiết
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$itemsPerPage = 10; // Số phim trên mỗi trang

// Lấy tổng số phim
$totalMovies = $movieModel->getTotalMovies();

// Khởi tạo đối tượng phân trang
$pagination = new Pagination($totalMovies, $itemsPerPage, $page);

// Lấy danh sách phim theo phân trang
$movies = $movieModel->getMoviesByPagination($pagination->getOffset(), $pagination->getLimit());
?>

<div class="container mt-4">
    <h2>Quản lý phim</h2>

    <div class="toast-container">
        <?php if (isset($success)): ?>
            <div class="toast custom-toast align-items-center text-white bg-success border-0" role="alert">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="fas fa-check-circle me-2"></i>
                        <?php echo $success; ?>
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div class="toast custom-toast align-items-center text-white bg-danger border-0" role="alert">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <?php echo $error; ?>
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addMovieModal">
        Thêm phim mới
    </button>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Poster</th>
                    <th>Tên phim</th>
                    <th>Thể loại</th>
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
                        <td>
                            <?php
                            $movieGenres = $genreModel->getGenresByMovieId($movie['id']);
                            $genreNames = array_map(function ($genre) {
                                return $genre['name'];
                            }, $movieGenres);
                            echo implode(', ', $genreNames);
                            ?>
                        </td>
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
                                data-movie='<?php echo json_encode($movie); ?>'
                                data-genres='<?php echo json_encode($movieGenres); ?>'>
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
        <?php echo $pagination->createLinks('manage_movies.php'); ?>
    </div>
</div>

<!-- Modal thêm phim mới -->
<div class="modal fade" id="addMovieModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thêm Phim Mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addMovieForm" method="POST" enctype="multipart/form-data">
                    <!-- Phần thể loại -->
                    <div class="mb-3">
                        <label>Thể loại</label>
                        <div class="row">
                            <div class="col-md-6">
                                <?php for ($i = 0; $i < $halfCount; $i++): ?>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox"
                                            name="genres[]"
                                            value="<?php echo $allGenres[$i]['id']; ?>"
                                            id="add-genre<?php echo $allGenres[$i]['id']; ?>">
                                        <label class="form-check-label" for="add-genre<?php echo $allGenres[$i]['id']; ?>">
                                            <?php echo $allGenres[$i]['name']; ?>
                                        </label>
                                    </div>
                                <?php endfor; ?>
                            </div>
                            <div class="col-md-6">
                                <?php for ($i = $halfCount; $i < $totalGenres; $i++): ?>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox"
                                            name="genres[]"
                                            value="<?php echo $allGenres[$i]['id']; ?>"
                                            id="add-genre<?php echo $allGenres[$i]['id']; ?>">
                                        <label class="form-check-label" for="add-genre<?php echo $allGenres[$i]['id']; ?>">
                                            <?php echo $allGenres[$i]['name']; ?>
                                        </label>
                                    </div>
                                <?php endfor; ?>
                            </div>
                        </div>
                    </div>
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
                                <input type="file" name="poster" class="form-control" accept="image/*" required>
                                <img id="add-poster-preview" src="#" alt="Preview"
                                    style="max-width: 200px; margin-top: 10px; display: none;">
                            </div>
                            <div class="mb-3">
                                <label>Mô tả</label>
                                <textarea name="description" class="form-control" rows="4"></textarea>
                            </div>
                        </div>
                    </div>



                    <button type="submit" name="add_movie" class="btn btn-primary">Thêm Phim</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal sửa phim -->
<div class="modal fade" id="editMovieModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Sửa Phim</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editMovieForm" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id" id="edit-movie-id">
                    <!-- Phần thể loại -->
                    <div class="mb-3">
                        <label>Thể loại</label>
                        <div class="row">
                            <div class="col-md-6">
                                <?php for ($i = 0; $i < $halfCount; $i++): ?>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox"
                                            name="genres[]"
                                            value="<?php echo $allGenres[$i]['id']; ?>"
                                            id="edit-genre<?php echo $allGenres[$i]['id']; ?>">
                                        <label class="form-check-label" for="edit-genre<?php echo $allGenres[$i]['id']; ?>">
                                            <?php echo $allGenres[$i]['name']; ?>
                                        </label>
                                    </div>
                                <?php endfor; ?>
                            </div>
                            <div class="col-md-6">
                                <?php for ($i = $halfCount; $i < $totalGenres; $i++): ?>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox"
                                            name="genres[]"
                                            value="<?php echo $allGenres[$i]['id']; ?>"
                                            id="edit-genre<?php echo $allGenres[$i]['id']; ?>">
                                        <label class="form-check-label" for="edit-genre<?php echo $allGenres[$i]['id']; ?>">
                                            <?php echo $allGenres[$i]['name']; ?>
                                        </label>
                                    </div>
                                <?php endfor; ?>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label>Tên phim</label>
                                <input type="text" name="title" id="edit-title" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label>Thời lượng (phút)</label>
                                <input type="number" name="duration" id="edit-duration" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label>Ngày khởi chiếu</label>
                                <input type="date" name="release_date" id="edit-release-date" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label>Trạng thái</label>
                                <select name="status" id="edit-status" class="form-control">
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
                                <small class="text-muted">Để trống nếu không muốn thay đổi poster</small>
                                <img id="edit-poster-preview" src="#" alt="Preview"
                                    style="max-width: 200px; margin-top: 10px;">
                            </div>
                            <div class="mb-3">
                                <label>Mô tả</label>
                                <textarea name="description" id="edit-description" class="form-control" rows="4"></textarea>
                            </div>
                        </div>
                    </div>
                    <button type="submit" name="edit_movie" class="btn btn-primary">Cập Nhật</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Thêm script để xử lý preview ảnh và chọn thể loại khi sửa -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Xử lý preview ảnh khi thêm mới
        const addPosterInput = document.querySelector('#addMovieModal input[name="poster"]');
        const addPosterPreview = document.getElementById('add-poster-preview');

        addPosterInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    addPosterPreview.src = e.target.result;
                    addPosterPreview.style.display = 'block';
                }
                reader.readAsDataURL(this.files[0]);
            }
        });

        // Xử lý preview ảnh khi sửa
        const editPosterInput = document.querySelector('#editMovieModal input[name="poster"]');
        const editPosterPreview = document.getElementById('edit-poster-preview');

        editPosterInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    editPosterPreview.src = e.target.result;
                    editPosterPreview.style.display = 'block';
                }
                reader.readAsDataURL(this.files[0]);
            }
        });

        // Xử lý khi click nút sửa
        const editButtons = document.querySelectorAll('.edit-movie');
        editButtons.forEach(button => {
            button.addEventListener('click', function() {
                const movie = JSON.parse(this.dataset.movie);
                const movieGenres = JSON.parse(this.dataset.genres);
                
                // Điền thông tin cơ bản của phim
                document.getElementById('edit-movie-id').value = movie.id;
                document.getElementById('edit-title').value = movie.title;
                document.getElementById('edit-description').value = movie.description;
                document.getElementById('edit-duration').value = movie.duration;
                document.getElementById('edit-release-date').value = movie.release_date.split(' ')[0];
                document.getElementById('edit-status').value = movie.status;

                // Hiển thị poster hiện tại
                if (movie.poster) {
                    const editPosterPreview = document.getElementById('edit-poster-preview');
                    editPosterPreview.src = '../assets/img/' + movie.poster;
                    editPosterPreview.style.display = 'block';
                }

                // Reset và đánh dấu các thể loại đã chọn
                document.querySelectorAll('#editMovieModal input[name="genres[]"]').forEach(checkbox => {
                    checkbox.checked = false;
                });

                movieGenres.forEach(genre => {
                    const checkbox = document.querySelector(`#edit-genre${genre.id}`);
                    if (checkbox) {
                        checkbox.checked = true;
                    }
                });

                // Hiển thị modal
                new bootstrap.Modal(document.getElementById('editMovieModal')).show();
            });
        });

        const toastElList = document.querySelectorAll('.toast');
        const toastList = [...toastElList].map(toastEl => {
            const toast = new bootstrap.Toast(toastEl, {
                autohide: true,
                delay: 3000
            });
            toast.show();
            return toast;
        });

        setTimeout(() => {
            document.querySelectorAll('.custom-toast').forEach(toast => {
                toast.classList.add('show');
            });
        }, 100);

        toastElList.forEach(toastEl => {
            toastEl.addEventListener('hide.bs.toast', function() {
                this.classList.remove('show');
            });
        });
    });
</script>


<?php include '../includes/footer.php'; ?>