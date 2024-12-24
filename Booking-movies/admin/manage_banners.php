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

// Xử lý thêm banner
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $movie_id = $_POST['movie_id'];
    $status = $_POST['status'];

    $data = [
        'movie_id' => $movie_id,
        'status' => $status
    ];

    // Xử lý upload ảnh mới
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image = hash('sha256', time() . rand()) . '.' . pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $path = '../assets/img/' . $image;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $path)) {
            $data['image'] = $image;
        } else {
            $_SESSION['error'] = "Không thể tải tệp hình ảnh lên.";
            header('Location: manage_banners.php');
            exit();
        }
    } else {
        $_SESSION['error'] = "Vui lòng chọn hình ảnh cho banner!";
        header('Location: manage_banners.php');
        exit();
    }

    // Thêm banner
    if ($bannerModel->addBanner($data)) {
        $_SESSION['success'] = "Thêm banner thành công!";
    } else {
        $_SESSION['error'] = "Có lỗi xảy ra khi thêm banner!";
    }

    header('Location: manage_banners.php');
    exit();
}

// Xử lý xóa banner
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $oldBanner = $bannerModel->getBannerById($id); // Lấy thông tin banner cũ

    if ($oldBanner && $oldBanner['image']) {
        $oldImagePath = '../assets/img/' . $oldBanner['image'];
        if (file_exists($oldImagePath)) {
            unlink($oldImagePath);
        }
    }

    if ($bannerModel->deleteBanner($id)) {
        $_SESSION['success'] = "Xóa banner thành công!";
    } else {
        $_SESSION['error'] = "Có lỗi xảy ra!";
    }
    header('Location: manage_banners.php');
    exit();
}

// Xử lý cập nhật trạng thái
if (isset($_GET['toggle_status'])) {
    $id = $_GET['toggle_status'];
    $status = $_GET['status'] == 'active' ? 'inactive' : 'active';
    if ($bannerModel->updateBannerStatus($id, $status)) {
        $_SESSION['success'] = "Cập nhật trạng thái thành công!";
    }
    header('Location: manage_banners.php');
    exit();
}

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$itemsPerPage = 10;

$totalBanners = $bannerModel->getTotalBanners();
$pagination = new Pagination($totalBanners, $itemsPerPage, $page);
$banners = $bannerModel->getBannersByPagination($pagination->getOffset(), $pagination->getLimit());

$movies = $movieModel->getAllMovies();
?>

<div class="container mt-4">
    <h2>Quản lý Banner</h2>

    <div class="toast-container">
        <?php if (isset($_SESSION['success'])): ?>
            <div class="toast custom-toast align-items-center text-white bg-success border-0" role="alert">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="fas fa-check-circle me-2"></i>
                        <?php echo $_SESSION['success']; ?>
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="toast custom-toast align-items-center text-white bg-danger border-0" role="alert">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <?php echo $_SESSION['error']; ?>
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addBannerModal">
        Thêm banner mới
    </button>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Hình Ảnh</th>
                    <th>Phim</th>
                    <th style="min-width: 100px;">Trạng Thái</th>
                    <th>Thao Tác</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($banners as $banner): ?>
                    <tr>
                        <td><?php echo $banner['id']; ?></td>
                        <td>
                            <img src="../assets/img/<?php echo $banner['image']; ?>"
                                alt="Banner" style="max-height: 50px;">
                        </td>
                        <td><?php echo $banner['movie_id']; ?></td>
                        <td>
                            <a href="?toggle_status=<?php echo $banner['id']; ?>&status=<?php echo $banner['status']; ?>"
                                class="btn btn-sm <?php echo $banner['status'] == 'active' ? 'btn-success' : 'btn-secondary'; ?>">
                                <?php echo $banner['status'] == 'active' ? 'Hiện' : 'Ẩn'; ?>
                            </a>
                        </td>
                        <td>
                            <a href="?delete=<?php echo $banner['id']; ?>"
                                class="btn btn-sm btn-danger"
                                onclick="return confirm('Bạn có chắc muốn xóa banner này?')">
                                Xóa
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php echo $pagination->createLinks('manage_banners.php'); ?>
    </div>
</div>

<!-- Modal thêm banner mới -->
<div class="modal fade" id="addBannerModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thêm Banner Mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label>Trạng Thái</label>
                                <select name="status" class="form-control">
                                    <option value="active">Hiện</option>
                                    <option value="inactive">Ẩn</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label>Hình Ảnh Banner</label>
                                <input type="file" name="image" class="form-control" accept="image/*" required>
                                <img id="add-banner-preview" src="#" alt="Preview"
                                    style="max-width: 200px; margin-top: 10px; display: none;">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label>Phim</label>
                        <select name="movie_id" class="form-control" required>
                            <?php foreach ($movies as $movie): ?>
                                <option value="<?php echo $movie['id']; ?>"><?php echo $movie['title']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" name="add_banner" class="btn btn-primary">Thêm Banner</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
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

        // Xử lý sự kiện khi modal đóng
        const modals = document.querySelectorAll('.modal');
        modals.forEach(modal => {
            modal.addEventListener('hidden.bs.modal', function() {
                // Xóa backdrop khi modal đóng
                const backdrop = document.querySelector('.modal-backdrop');
                if (backdrop) {
                    backdrop.remove();
                }
            });
        });
    });
</script>
<?php include '../includes/footer.php'; ?>