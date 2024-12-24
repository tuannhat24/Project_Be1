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

// Xử lý thêm/sửa rooms
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'] ?? null;
    $theater_id = $_POST['theater_id'];
    $name = $_POST['name'];
    $capacity = $_POST['capacity'];
    $room_type = $_POST['room_type'];
    $status = $_POST['status'];
    $data = [
        'theater_id' => $theater_id,
        'name' => $name,
        'capacity' => $capacity,
        'room_type' => $room_type,
        'status' => $status
    ];

    if ($id) {
        // Cập nhật phòng chiếu
        if ($roomModel->updateRoom($id, $data)) {
            $_SESSION['success'] = "Cập nhật phòng chiếu thành công!";
        } else {
            $_SESSION['error'] = "Có lỗi xảy ra khi cập nhật phòng chiếu!";
        }
    } else {
        // Thêm phòng chiếu mới
        if ($roomModel->addRoom($data)) {
            $_SESSION['success'] = "Thêm phòng chiếu thành công!";
        } else {
            $_SESSION['error'] = "Có lỗi xảy ra khi thêm phòng chiếu!";
        }
    }
    header('Location: manage_rooms.php');
    exit();
}

// Xử lý xóa phòng
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    if ($roomModel->deleteRoom($id)) {
        $_SESSION['success'] = "Xóa phòng chiếu thành công!";
    } else {
        $_SESSION['error'] = "Có lỗi xảy ra!";
    }
    header('Location: manage_rooms.php');
    exit();
}

// Xử lý cập nhật trạng thái
if (isset($_GET['toggle_status'])) {
    $id = $_GET['toggle_status'];
    $status = $_GET['status'] == 'active' ? 'maintenance' : 'active';
    if ($roomModel->updateRoomStatus($id, $status)) {
        $_SESSION['success'] = "Cập nhật trạng thái thành công!";
    }
    header('Location: manage_rooms.php');
    exit();
}

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$itemsPerPage = 10;

$totalRooms = $roomModel->getTotalRooms();
$pagination = new Pagination($totalRooms, $itemsPerPage, $page);
$rooms = $roomModel->getRoomsByPagination($pagination->getOffset(), $pagination->getLimit());

$theaters = $theaterModel->getAllTheaters();
?>

<div class="container mt-4">
    <h2>Quản lý các phòng chiếu phim</h2>

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

    <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addRoomModal">
        Thêm phòng chiếu mới
    </button>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Id</th>
                    <th>Rạp</th>
                    <th>Tên phòng</th>
                    <th>Sức chứa</th>
                    <th>Loại phòng</th>
                    <th>Trạng thái</th>
                    <th>Ngày tạo</th>
                    <th>Ngày cập nhật</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rooms as $room): ?>
                    <tr>
                        <td><?php echo $room['id']; ?></td>
                        <td>
                            <?php
                            foreach ($theaters as $theater) {
                                if ($theater['id'] == $room['theater_id']) {
                                    echo $theater['name'];
                                    break;
                                }
                            }
                            ?>
                        </td>
                        <td><?php echo $room['name']; ?></td>
                        <td><?php echo $room['capacity']; ?></td>
                        <td><?php echo $room['room_type']; ?></td>
                        <td>
                            <a href="?toggle_status=<?php echo $room['id']; ?>&status=<?php echo $room['status']; ?>"
                                class="btn btn-sm <?php echo $room['status'] == 'active' ? 'btn-success' : 'btn-secondary'; ?>">
                                <?php echo $room['status'] == 'active' ? 'Hiện' : 'Ẩn'; ?>
                            </a>
                        </td>
                        <td><?php echo date('d/m/Y H:i', strtotime($room['created_at'])); ?></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($room['updated_at'])); ?></td>
                        <td>
                            <button class="btn btn-sm btn-info edit-room"
                                data-room='<?php echo json_encode($room); ?>'
                                data-bs-toggle="modal" data-bs-target="#editRoomModal">
                                Sửa
                            </button>
                            <a href="?delete=<?php echo $room['id']; ?>"
                                class="btn btn-sm btn-danger"
                                onclick="return confirm('Bạn có chắc muốn xóa phòng này?')">
                                Xóa
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php echo $pagination->createLinks('manage_rooms.php'); ?>
    </div>
</div>


<!-- Modal Thêm phòng chiếu -->
<div class="modal fade" id="addRoomModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thêm Phòng Chiếu Mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="manage_rooms.php" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="theater_id">Chọn Rạp Chiếu</label>
                        <select name="theater_id" class="form-control" required>
                            <option value="">Chọn Rạp Chiếu</option>
                            <?php foreach ($theaters as $theater): ?>
                                <option value="<?php echo $theater['id']; ?>">
                                    <?php echo $theater['name']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="name">Tên phòng chiếu</label>
                        <input type="text" name="name" class="form-control" required />
                    </div>
                    <div class="form-group">
                        <label for="capacity">Sức chứa phòng</label>
                        <input type="number" id="capacity" name="capacity" class="form-control" required />
                    </div>
                    <div class="form-group">
                        <label for="room_type">Loại phòng</label>
                        <select id="room_type" name="room_type" class="form-select" required>
                            <option value="2D" <?= $room['room_type'] === '2D' ? 'selected' : '' ?>>2D</option>
                            <option value="3D" <?= $room['room_type'] === '3D' ? 'selected' : '' ?>>3D</option>
                            <option value="4D" <?= $room['room_type'] === '4D' ? 'selected' : '' ?>>4D</option>
                        </select>
                    </div>
                    <!-- <div class="form-group">
                        <label for="status">Trạng thái</label>
                        <select id="status" name="status" class="form-control" required>
                            <option value="active">Hiện</option>
                            <option value="maintenance">Ẩn</option>
                        </select>
                    </div> -->
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Thêm phòng</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy bỏ</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Sửa phòng chiếu -->
<div class="modal fade" id="editRoomModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Sửa Phòng Chiếu</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="manage_rooms.php" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="theater_id">Chọn Rạp Chiếu</label>
                        <select name="theater_id" id="theater_id" class="form-select">
                            <option value="" disabled>-- Chọn Rạp Chiếu --</option>
                            <?php foreach ($theaters as $theater): ?>
                                <option value="<?php echo $theater['id']; ?>" <?php echo ($theater['id'] == $theaters) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($theater['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="name">Tên phòng chiếu</label>
                        <input type="text" name="name" id="edit_name" class="form-control" required />
                    </div>
                    <div class="form-group">
                        <label for="capacity">Sức chứa phòng</label>
                        <input type="number" name="capacity" id="edit_capacity" class="form-control" required />
                    </div>
                    <div class="form-group">
                        <label for="room_type">Loại phòng</label>
                        <select id="edit_room_type" name="room_type" class="form-select" required>
                            <option value="2D" <?= $room['room_type'] === '2D' ? 'selected' : '' ?>>2D</option>
                            <option value="3D" <?= $room['room_type'] === '3D' ? 'selected' : '' ?>>3D</option>
                            <option value="4D" <?= $room['room_type'] === '4D' ? 'selected' : '' ?>>4D</option>
                        </select>
                    </div>
                    <!-- <div class="form-group">
                        <label for="status">Trạng thái</label>
                        <select name="status" id="edit_status" class="form-control" required>
                            <option value="active">Hiện</option>
                            <option value="maintenance">Ẩn</option>
                        </select>
                    </div> -->
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="id" id="edit_id" />
                    <button type="submit" class="btn btn-primary">Cập nhật phòng</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy bỏ</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const editRoomBtns = document.querySelectorAll('.edit-room');
        editRoomBtns.forEach(button => {
            button.addEventListener('click', (event) => {
                const roomData = JSON.parse(event.target.dataset.room);
                document.getElementById('edit_id').value = roomData.id;
                document.getElementById('edit_name').value = roomData.name;
                document.getElementById('edit_capacity').value = roomData.capacity;
                document.getElementById('edit_room_type').value = roomData.room_type;
                document.getElementById('edit_status').value = roomData.status;
                // Update theater dropdown
                // This assumes you have already loaded the theaters dynamically on the server side.
                // Dynamically populate the theater select element based on roomData.theater_id.
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