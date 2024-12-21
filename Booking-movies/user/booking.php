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

$user = $userModel->getUserById($_SESSION['user_id']);
$customer_name = $user[0]['fullname'];
$customer_email = $user[0]['email'];
$customer_phone = $user[0]['phone'];

$schedule_id = $_GET['schedule_id'];
$schedule_details = $scheduleModel->getScheduleById($schedule_id);

$room_id = $schedule_details[0]['room_id'];
$room = $roomModel->getRoomById($room_id);
$total_seats = $room['capacity'];

$seats = $seatModel->getAllSeatsByRoom($room_id);
$seat_codes = [];
$seat_statuses = [];
$seat_ids = [];
foreach ($seats as $seat) {
    $seat_codes[] = $seat['seat_row'] . $seat['seat_number'];
    $seat_statuses[] = $seat['status'];
    $seat_ids[] = $seat['id'];
}

if (empty($schedule_details)) {
    header("Location: ../");
    exit();
}

$schedule = $schedule_details[0];
$booked_seats = $bookingModel->getBookedSeats($schedule_id);
$booked_seats_array = array_column($booked_seats, 'seat_id');

// Xử lý đặt vé
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $seats = explode(',', $_POST['seats']);
    $payment_method = $_POST['payment_method'];
    $total_price = count($seats) * $schedule['price'];
    $seat_price = $schedule['price'];

    $success = true;

    if (!$bookingModel->createBooking(
        $_SESSION['user_id'],
        $schedule_id,
        $seats,
        $total_price,
        $seat_price,
        $payment_method,
        $customer_name,
        $customer_email,
        $customer_phone
    )) {
        $success = false;
    }

    if ($success) {
        if ($payment_method === 'momo') {
            header("Location: momo_payment.php?booking_id=" . $bookingModel->getLastBookingId());
            exit();
        } else {
            header("Location: profile.php");
            exit();
        }
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
                        for ($i = 0; $i < $total_seats; $i++) {
                            $j = $i + 1;
                            $seat_class = '';
                            switch ($seat_statuses[$i]) {
                                case 'booked':
                                    $seat_class = 'booked';
                                    break;
                                case 'available':
                                    $seat_class = 'available';
                                    break;
                                case 'unavailable':
                                    $seat_class = 'unavailable';
                                    break;
                                default:
                                    $seat_class = 'unknown';
                            }

                            echo "<div class='seat $seat_class' data-seat='$seat_codes[$i].$seat_ids[$i]'>$seat_codes[$i]</div>";
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
                    <p><strong>Phương thức thanh toán:</strong></p>
                    <form method="POST">
                        <div class="form-group">
                            <select name="payment_method" class="form-control" id="payment-method">
                                <option value="cash">Tiền mặt</option>
                                <option value="momo">Momo</option>
                            </select>
                        </div>
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
            const seatsList = Array.from(selectedSeats);
            const seatDetails = seatsList.map(seatCode => seatCode.split('.')[0]);
            const seatIds = seatsList.map(seatCode => seatCode.split('.')[1]);

            const totalPrice = selectedSeats.size * pricePerSeat;

            document.getElementById('seats-list').textContent = seatDetails;
            document.getElementById('total-price').textContent = totalPrice.toLocaleString();
            document.getElementById('seats-input').value = Array.from(selectedSeats);
            document.getElementById('book-button').disabled = selectedSeats.size === 0;
        }
    });
</script>

<?php include '../includes/footer.php'; ?>