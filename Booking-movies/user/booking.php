<?php
include '../includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['schedule_id'])) {
    header("Location: ../");
    exit();
}

$schedule_id = $_GET['schedule_id'];
$schedule_details = $scheduleModel->getScheduleById($schedule_id);

if (empty($schedule_details)) {
    header("Location: ../");
    exit();
}

$schedule = $schedule_details[0];
$booked_seats = $bookingModel->getBookedSeats($schedule_id);
$booked_seats_array = array_column($booked_seats, 'seat_number');

// Xử lý đặt vé
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $seats = explode(',', $_POST['seats']);
    $success = true;

    foreach ($seats as $seat) {
        if (!$bookingModel->createBooking($_SESSION['user_id'], $schedule_id, $seat)) {
            $success = false;
            break;
        }
    }

    if ($success) {
        header("Location: profile.php");
        exit();
    } else {
        $error = "Có lỗi xảy ra khi đặt vé. Vui lòng thử lại!";
    }
}
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Chọn ghế</h5>
                </div>
                <div class="card-body">
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>

                    <div class="text-center mb-4">
                        <div class="screen">
                            <p>Màn hình</p>
                        </div>
                    </div>

                    <div class="seats-container">
                        <div class="seat-area"></div>
                        <?php
                        $total_seats = 120; // Có thể thay đổi theo số ghế của rạp
                        for ($i = 1; $i <= $total_seats; $i++) {
                            $seat_class = in_array($i, $booked_seats_array) ? 'booked' : 'available';
                            echo "<div class='seat $seat_class' data-seat='$i'>$i</div>";
                        }
                        ?>
                    </div>

                    <div class="mt-3">
                        <div class="seat-legend">
                            <span class="seat available"></span>Ghế trống
                            <span class="seat booked"></span>Đã đặt
                            <span class="seat selected"></span>Đang chọn
                            <span class="seat center_area"></span>Vùng trung tâm
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Thông tin đặt vé</h5>
                </div>
                <div class="card-body">
                    <p><strong>Phim:</strong> <?php echo $schedule['title']; ?></p>
                    <p><strong>Rạp:</strong> <?php echo $schedule['theater_name']; ?></p>
                    <p><strong>Suất chiếu:</strong> <?php echo date('d/m/Y H:i', strtotime($schedule['show_time'])); ?></p>
                    <p><strong>Giá vé:</strong> <?php echo number_format($schedule['price']); ?>đ/ghế</p>

                    <hr>

                    <p><strong>Ghế đã chọn:</strong> <span id="seats-list"></span></p>
                    <p><strong>Tổng tiền:</strong> <span id="total-price">0</span>đ</p>

                    <form method="POST">
                        <input type="hidden" name="seats" id="seats-input">
                        <button type="submit" id="book-button" class="btn btn-primary w-100" disabled>
                            Đặt vé
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const seats = document.querySelectorAll('.seat.available');
        const selectedSeats = new Set();
        const pricePerSeat = <?php echo $schedule['price']; ?>;

        seats.forEach(seat => {
            seat.addEventListener('click', function() {
                const seatNumber = this.dataset.seat;

                if (this.classList.contains('selected')) {
                    this.classList.remove('selected');
                    selectedSeats.delete(seatNumber);
                } else {
                    this.classList.add('selected');
                    selectedSeats.add(seatNumber);
                }

                updateBookingForm();
            });
        });

        function updateBookingForm() {
            const seatsList = Array.from(selectedSeats).join(', ');
            const totalPrice = selectedSeats.size * pricePerSeat;

            document.getElementById('seats-list').textContent = seatsList;
            document.getElementById('total-price').textContent = totalPrice.toLocaleString();
            document.getElementById('seats-input').value = Array.from(selectedSeats);
            document.getElementById('book-button').disabled = selectedSeats.size === 0;
        }
    });
</script>

<?php include '../includes/footer.php'; ?>