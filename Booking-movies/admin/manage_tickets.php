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

// Lấy danh sách vé
$tickets = $bookingModel->getAllBookings();
?>

<div class="container mt-4">
    <h2>Quản lý vé</h2>

    <?php if (isset($success)): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Mã vé</th>
                    <th>Người đặt</th>
                    <th>Phim</th>
                    <th>Rạp</th>
                    <th>Suất chiếu</th>
                    <th>Ghế</th>
                    <th>Ngày đặt</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tickets as $ticket): ?>
                    <tr>
                        <td>#<?php echo $ticket['id']; ?></td>
                        <td>
                            <?php echo $ticket['fullname']; ?><br>
                            <small><?php echo $ticket['phone']; ?></small>
                        </td>
                        <td><?php echo $ticket['title']; ?></td>
                        <td><?php echo $ticket['theater_name']; ?></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($ticket['show_time'])); ?></td>
                        <td><?php echo $ticket['seat_number']; ?></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($ticket['booking_date'])); ?></td>
                        <td>
                            <form method="POST" class="status-form">
                                <input type="hidden" name="update_status">
                                <input type="hidden" name="booking_id" value="<?php echo $ticket['id']; ?>">
                                <select name="status" class="form-select form-select-sm"
                                    onchange="this.form.submit()">
                                    <option value="pending"
                                        <?php echo $ticket['status'] == 'pending' ? 'selected' : ''; ?>>
                                        Đang chờ
                                    </option>
                                    <option value="confirmed"
                                        <?php echo $ticket['status'] == 'confirmed' ? 'selected' : ''; ?>>
                                        Đã xác nhận
                                    </option>
                                    <option value="cancelled"
                                        <?php echo $ticket['status'] == 'cancelled' ? 'selected' : ''; ?>>
                                        Đã hủy
                                    </option>
                                </select>
                            </form>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-info view-ticket"
                                data-ticket='<?php echo json_encode($ticket); ?>'>
                                Chi tiết
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal chi tiết vé -->
<div class="modal fade" id="ticketModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Chi tiết vé</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="ticket-details"></div>
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

                details.innerHTML = `
                <p><strong>Mã vé:</strong> #${ticket.id}</p>
                <p><strong>Người đặt:</strong> ${ticket.fullname}</p>
                <p><strong>Số điện thoại:</strong> ${phone}</p>
                <p><strong>Email:</strong> ${ticket.email}</p>
                <p><strong>Phim:</strong> ${ticket.title}</p>
                <p><strong>Rạp:</strong> ${ticket.theater_name}</p>
                <p><strong>Suất chiếu:</strong> ${new Date(ticket.show_time).toLocaleString()}</p>
                <p><strong>Ghế:</strong> ${ticket.seat_number}</p>
                <p><strong>Ngày đặt:</strong> ${new Date(ticket.booking_date).toLocaleString()}</p>
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