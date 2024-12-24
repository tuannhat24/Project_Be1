<?php
include '../includes/header.php';
require_once '../app/common/Pagination.php';

// Kiểm tra đã login hay chưa
if (!isset($_SESSION['isLoggedIn']) || $_SESSION['isLoggedIn'] === false) {
    header("Location: /Project_Be1/Booking-movies/");
    exit();
}

// Kiểm tra quyền admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: /Project_Be1/Booking-movies/");
    exit();
}

// Xử lý thêm/sửa theaters
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'] ?? null;
    $name = $_POST['name'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $total_seats = $_POST['total_seats'];
    $data = [
        'name' => $name,
        'address' => $address,
        'phone' => $phone,
        'total_seats' => $total_seats
    ];

    if ($id) {
        // Cập nhật phòng chiếu
        if ($theaterModel->updateTheater($id, $data)) {
            $_SESSION['success'] = "Cập nhật phòng chiếu thành công!";
        } else {
            $_SESSION['error'] = "Có lỗi xảy ra khi cập nhật phòng chiếu!";
        }
    } else {
        // Thêm phòng chiếu mới
        if ($theaterModel->addTheater($data)) {
            $_SESSION['success'] = "Thêm phòng chiếu thành công!";
        } else {
            $_SESSION['error'] = "Có lỗi xảy ra khi thêm phòng chiếu!";
        }
    }
    header('Location: manage_theaters.php');
    exit();
}

// Xử lý xóa phòng
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    if ($theaterModel->deleteTheater($id)) {
        $_SESSION['success'] = "Xóa phòng chiếu thành công!";
    } else {
        $_SESSION['error'] = "Có lỗi xảy ra!";
    }
    header('Location: manage_theaters.php');
    exit();
}

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$itemsPerPage = 10;
$totalTheaters = $theaterModel->getTotalTheaters();
$pagination = new Pagination($totalTheaters, $itemsPerPage, $page);
$theaters = $theaterModel->getTheatersByPagination($pagination->getOffset(), $pagination->getLimit());

?>

<div class="container mt-4">
    <h2>Quản lý các rạp chiếu phim</h2>

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
            <?php unset($_SESSION['success']); ?>
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
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
    </div>

    <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addTheaterModal">
        Thêm rạp phim mới
    </button>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Address</th>
                    <th>Phone</th>
                    <th>Total_seats</th>
                    <th>Created At</th>
                    <th>Updated At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($theaters as $theater): ?>
                    <tr>
                        <td><?php echo $theater['id']; ?></td>
                        <td><?php echo $theater['name']; ?></td>
                        <td><?php echo $theater['address']; ?></td>
                        <td><?php echo $theater['phone']; ?></td>
                        <td><?php echo $theater['total_seats']; ?></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($theater['created_at'])); ?></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($theater['updated_at'])); ?></td>
                        <td>
                            <button class="btn btn-sm btn-info edit-theater"
                                data-theater='<?php echo json_encode($theater); ?>'
                                data-bs-toggle="modal" data-bs-target="#editTheaterModal">
                                Sửa
                            </button>
                            <a href="?delete=<?php echo $theater['id']; ?>"
                                class="btn btn-sm btn-danger"
                                onclick="return confirm('Bạn có chắc muốn xóa rạp này?')">
                                Xóa
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php echo $pagination->createLinks('manage_theaters.php'); ?>
    </div>
</div>

<!-- Modal Thêm rạp chiếu -->
<div class="modal fade" id="addTheaterModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thêm Rạp Chiếu Mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="manage_theaters.php" method="POST">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="name">Tên rạp chiếu</label>
                        <input type="text" name="name" class="form-control" required />
                    </div>
                    <div class="form-group">
                        <label for="address">Địa chỉ</label>
                        <input type="text" name="address" class="form-control" required />
                    </div>
                    <div class="form-group">
                        <label for="phone">Số điện thoại</label>
                        <input type="tel" name="phone" class="form-control" required />
                    </div>
                    <div class="form-group">
                        <label for="total_seats">Số ghế tổng</label>
                        <input type="number" name="total_seats" class="form-control" required />
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Thêm</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Sửa rạp chiếu -->
<div class="modal fade" id="editTheaterModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Sửa Rạp Chiếu</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="manage_theaters.php" method="POST">
                <input type="hidden" name="id" id="edit_id" />
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit_name">Tên rạp chiếu</label>
                        <input type="text" name="name" id="edit_name" class="form-control" required />
                    </div>
                    <div class="form-group">
                        <label for="edit_address">Địa chỉ</label>
                        <input type="text" name="address" id="edit_address" class="form-control" required />
                    </div>
                    <div class="form-group">
                        <label for="edit_phone">Số điện thoại</label>
                        <input type="tel" name="phone" id="edit_phone" class="form-control" required />
                    </div>
                    <div class="form-group">
                        <label for="total_seats">Số ghế tổng</label>
                        <input type="number" name="total_seats" id="edit_total_seats" class="form-control" required />
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Cập nhật</button>
                </div>
            </form>
        </div>
    </div>
</div>


<script>
    const editButtons = document.querySelectorAll('.edit-theater');

    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            const theater = JSON.parse(this.getAttribute('data-theater'));

            document.getElementById('edit_id').value = theater.id;
            document.getElementById('edit_name').value = theater.name;
            document.getElementById('edit_address').value = theater.address;
            document.getElementById('edit_phone').value = theater.phone;
            document.getElementById('edit_total_seats').value = theater.total_seats;
        });
    });
</script>

<?php include '../includes/footer.php'; ?>