<?php
require_once 'db.php';

class Booking extends Database
{
    public function createBooking(
        $user_id,
        $schedule_id,
        $seat_codes,
        $total_price,
        $seat_price,
        $payment_method = 'cash',
        $customer_name,
        $customer_email,
        $customer_phone
    ) {
        self::$connection->begin_transaction();
        $booking_code = $this->generateBookingCode($user_id);
        try {
            // Tạo booking mới
            $sql = "INSERT INTO bookings (user_id, schedule_id, customer_name, customer_email, customer_phone, total_price, payment_method, status, booking_code, created_at) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', ?, NOW())";
            $stmt = self::$connection->prepare($sql);
            $stmt->bind_param(
                "iisssdss",
                $user_id,
                $schedule_id,
                $customer_name,
                $customer_email,
                $customer_phone,
                $total_price,
                $payment_method,
                $booking_code
            );
            $stmt->execute();

            // Lấy ID của booking vừa tạo
            $booking_id = self::$connection->insert_id;

            // Duyệt qua tất cả ghế đã chọn
            foreach ($seat_codes as $seat_code) {
                $parts = explode('.', $seat_code);
                $seat_details = $parts[0];
                $seat_id = $parts[1];

                $seat_row = substr($seat_details, 0, 1);
                $seat_number = substr($seat_details, 1);

                // Kiểm tra ghế có còn sẵn không
                $sql = "SELECT id FROM seats WHERE seat_row = ? AND seat_number = ? AND id = ? AND status = 'available'";
                $stmt = self::$connection->prepare($sql);
                $stmt->bind_param("sii", $seat_row, $seat_number, $seat_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $seat = $result->fetch_assoc();

                if ($seat) {
                    // Thêm ghế vào bảng booking_seats
                    $sql = "INSERT INTO booking_seats (booking_id, seat_id, price) VALUES (?, ?, ?)";
                    $stmt = self::$connection->prepare($sql);
                    $stmt->bind_param("iis", $booking_id, $seat['id'], $seat_price);
                    $stmt->execute();

                    // Cập nhật trạng thái của ghế thành 'booked'
                    $sql = "UPDATE seats SET status = 'booked' WHERE id = ?";
                    $stmt = self::$connection->prepare($sql);
                    $stmt->bind_param("i", $seat['id']);
                    $stmt->execute();
                } else {
                    // Ném ngoại lệ nếu ghế không có sẵn hoặc đã đặt
                    throw new Exception("Ghế $seat_details đã được đặt.");
                }
            }

            // Commit transaction
            self::$connection->commit();
            return true;
        } catch (Exception $e) {
            // Rollback nếu có lỗi
            self::$connection->rollback();
            echo "Lỗi: " . $e->getMessage();
            return false;
        }
    }

    public function generateBookingCode($user_id)
    {
        $prefix = "BK";
        $date = date("YmdHis");
        $userPart = str_pad($user_id, 4, "0", STR_PAD_LEFT);

        $randomPart = strtoupper(bin2hex(random_bytes(4)));

        $booking_code = "{$prefix}-{$date}-U{$userPart}-{$randomPart}";

        if ($this->isBookingCodeExists($booking_code)) {
            return $this->generateBookingCode($booking_code, $user_id);
        }

        return $booking_code;
    }

    private function isBookingCodeExists($booking_code)
    {
        $sql = "SELECT COUNT(*) FROM bookings WHERE booking_code = ?";
        $stmt = self::$connection->prepare($sql);
        $stmt->bind_param("s", $booking_code);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();

        return $count > 0;
    }


    public function getUserBookings($user_id)
    {
        $sql = "SELECT b.*, m.title, s.show_date, s.show_time, t.name as theater_name, 
                    r.name as room_name, s.price, GROUP_CONCAT(CONCAT(seat_row, seat_number)) as seat_codes
                FROM bookings b 
                JOIN schedules s ON b.schedule_id = s.id 
                JOIN movies m ON s.movie_id = m.id 
                JOIN rooms r ON s.room_id = r.id
                JOIN theaters t ON r.theater_id = t.id 
                LEFT JOIN booking_seats bs ON b.id = bs.booking_id
                LEFT JOIN seats seat ON bs.seat_id = seat.id
                WHERE b.user_id = ? 
                GROUP BY b.id
                ORDER BY b.created_at DESC";

        $stmt = self::$connection->prepare($sql);
        $stmt->bind_param("i", $user_id);

        return $this->select($stmt);
    }

    public function getAllBookings()
    {
        $sql = "SELECT b.*, u.username, u.fullname, u.email, u.phone,
                m.title, s.show_date, s.show_time, t.name as theater_name, 
                r.name as room_name, s.price, 
                GROUP_CONCAT(CONCAT(seat_row, seat_number)) as seat_codes
            FROM bookings b 
            JOIN users u ON b.user_id = u.id 
            JOIN schedules s ON b.schedule_id = s.id 
            JOIN movies m ON s.movie_id = m.id 
            JOIN rooms r ON s.room_id = r.id
            JOIN theaters t ON r.theater_id = t.id 
            LEFT JOIN booking_seats bs ON b.id = bs.booking_id
            LEFT JOIN seats seat ON bs.seat_id = seat.id
            WHERE b.status != 'cancelled'
            GROUP BY b.id
            ORDER BY b.created_at DESC";

        $stmt = self::$connection->prepare($sql);
        return $this->select($stmt);
    }

    public function getBookedSeats($schedule_id)
    {
        $sql = "SELECT bs.seat_id 
                FROM booking_seats bs
                JOIN bookings b ON bs.booking_id = b.id 
                WHERE b.schedule_id = ? AND b.status != 'cancelled'";

        $stmt = self::$connection->prepare($sql);
        $stmt->bind_param("i", $schedule_id);

        return $this->select($stmt);
    }

    public function updateBookingStatus($booking_id, $status)
    {
        $sql = "UPDATE bookings SET status = ? WHERE id = ?";
        $stmt = self::$connection->prepare($sql);
        $stmt->bind_param("si", $status, $booking_id);

        return $stmt->execute();
    }

    public function getBookingById($id)
    {
        $sql = "SELECT b.*, u.username, u.fullname, u.email, u.phone,
                m.title, s.show_date, s.show_time, t.name as theater_name, 
                r.name as room_name, s.price, GROUP_CONCAT(bs.seat_id) as seats
                FROM bookings b 
                JOIN users u ON b.user_id = u.id 
                JOIN schedules s ON b.schedule_id = s.id 
                JOIN movies m ON s.movie_id = m.id 
                JOIN rooms r ON s.room_id = r.id
                JOIN theaters t ON r.theater_id = t.id 
                LEFT JOIN booking_seats bs ON b.id = bs.booking_id
                WHERE b.id = ?
                GROUP BY b.id";

        $stmt = self::$connection->prepare($sql);
        $stmt->bind_param("i", $id);

        return $this->select($stmt);
    }

    public function getLastBookingId()
    {
        $sql = "SELECT MAX(id) AS last_booking_id FROM bookings";
        $stmt = self::$connection->prepare($sql);

        if ($stmt->execute()) {
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                return $row['last_booking_id'];
            }
        }
        return null;
    }
}
