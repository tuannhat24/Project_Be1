<?php
// session_start();
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

// Xử lý cập nhật trạng thái vé
if (isset($_POST['update_status'])) {
    $booking_id = $_POST['booking_id'];
    $status = $_POST['status'];

    if ($bookingModel->updateBookingStatus($booking_id, $status)) {
        $success = "Cập nhật trạng thái vé thành công!";
    } else {
        $error = "Có lỗi xảy ra khi cập nhật trạng thái vé!";
    }
}

// Thêm vào sau khi khởi tạo các biến cần thiết
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$itemsPerPage = 10;

$totalTickets = $bookingModel->getTotalBookings();
$pagination = new Pagination($totalTickets, $itemsPerPage, $page);
$tickets = $bookingModel->getBookingsByPagination($pagination->getOffset(), $pagination->getLimit());
?>

<div class="container mt-4">
    <h2 class="mb-4">Quản lý vé</h2>

    <?php if (isset($success)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert" id="successAlert">
            <?php echo $success; unset($success); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert" id="errorAlert">
            <?php echo $error; unset($error); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Mã vé</th>
                    <th>Người đặt</th>
                    <th>Phim</th>
                    <th>Rạp</th>
                    <th>Phòng</th>
                    <th>Suất chiếu</th>
                    <th>Ghế</th>
                    <th>Giá vé</th>
                    <th>Ngày đặt</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tickets as $ticket): ?>
                    <tr>
                        <td>#<?php echo $ticket['id']; ?></td>
                        <td><?php echo $ticket['fullname']; ?></td>
                        <td><?php echo $ticket['title']; ?></td>
                        <td><?php echo $ticket['theater_name']; ?></td>
                        <td><?php echo $ticket['room_name']; ?></td>
                        <td>
                            <?php
                            $show_datetime = date('d/m/Y', strtotime($ticket['show_date'])) . ' ' .
                                date('H:i', strtotime($ticket['show_time']));
                            echo $show_datetime;
                            ?>
                        </td>
                        <td><?php echo $ticket['seat_codes']; ?></td>
                        <td><?php echo number_format($ticket['price']); ?>đ</td>
                        <td><?php echo date('d/m/Y H:i', strtotime($ticket['created_at'])); ?></td>
                        <td>
                            <form method="POST" class="d-inline">
                                <input type="hidden" name="update_status">
                                <input type="hidden" name="booking_id" value="<?php echo $ticket['id']; ?>">
                                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                    <option value="pending" <?php echo $ticket['status'] == 'pending' ? 'selected' : ''; ?>>
                                        Đang chờ
                                    </option>
                                    <option value="confirmed" <?php echo $ticket['status'] == 'confirmed' ? 'selected' : ''; ?>>
                                        Đã xác nhận
                                    </option>
                                    <option value="cancelled" <?php echo $ticket['status'] == 'cancelled' ? 'selected' : ''; ?>>
                                        Đã hủy
                                    </option>
                                </select>
                            </form>
                        </td>
                        <td>
                            <button class="btn btn-info btn-sm view-ticket"
                                data-bs-toggle="modal"
                                data-bs-target="#ticketModal"
                                data-ticket='<?php echo json_encode($ticket); ?>'>
                                <i class="fas fa-eye"></i>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php echo $pagination->createLinks('manage_tickets.php'); ?>
    </div>
</div>

<!-- Modal Chi tiết vé -->
<div class="modal fade" id="ticketModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Chi tiết vé</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="ticket-details">
                <!-- Nội dung chi tiết vé sẽ được điền bởi JavaScript -->
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const viewButtons = document.querySelectorAll('.view-ticket');
        const ticketModal = new bootstrap.Modal(document.getElementById('ticketModal'));

        viewButtons.forEach(button => {
            button.addEventListener('click', function() {
                const ticket = JSON.parse(this.dataset.ticket);
                const details = document.getElementById('ticket-details');
                const phone = ticket.phone ? ticket.phone : 'Chưa có';
                const show_datetime = new Date(ticket.show_date + ' ' + ticket.show_time)
                    .toLocaleString('vi-VN');
                const created_at = new Date(ticket.created_at).toLocaleString('vi-VN');

                details.innerHTML = `
                    <p><strong>Mã vé:</strong> #${ticket.id}</p>
                    <p><strong>Người đặt:</strong> ${ticket.fullname}</p>
                    <p><strong>Số điện thoại:</strong> ${phone}</p>
                    <p><strong>Email:</strong> ${ticket.email}</p>
                    <p><strong>Phim:</strong> ${ticket.title}</p>
                    <p><strong>Rạp:</strong> ${ticket.theater_name}</p>
                    <p><strong>Phòng:</strong> ${ticket.room_name}</p>
                    <p><strong>Suất chiếu:</strong> ${show_datetime}</p>
                    <p><strong>Ghế:</strong> ${ticket.seat_codes}</p>
                    <p><strong>Giá vé:</strong> ${Number(ticket.price).toLocaleString()}đ</p>
                    <p><strong>Ngày đặt:</strong> ${created_at}</p>
                    <p><strong>Trạng thái:</strong> ${
                        ticket.status === 'pending' ? 'Đang chờ' :
                        ticket.status === 'confirmed' ? 'Đã xác nhận' : 'Đã hủy'
                    }</p>
                `;

                ticketModal.show();
            });
        });
    });
</script>

<?php include '../includes/footer.php'; ?>