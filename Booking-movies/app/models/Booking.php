<?php
require_once 'db.php';

class Booking extends Database
{
    public function createBooking($user_id, $schedule_id, $seat_number)
    {
        // Bắt đầu transaction
        self::$connection->begin_transaction();

        try {
            // Tạo booking mới
            $sql = "INSERT INTO bookings (user_id, schedule_id, status) 
                    VALUES (?, ?, 'pending')";
            $stmt = self::$connection->prepare($sql);
            $stmt->bind_param("ii", $user_id, $schedule_id);
            $stmt->execute();
            
            // Lấy ID của booking vừa tạo
            $booking_id = self::$connection->insert_id;
            
            // Thêm thông tin ghế
            $sql = "INSERT INTO booking_seats (booking_id, seat_id) VALUES (?, ?)";
            $stmt = self::$connection->prepare($sql);
            $stmt->bind_param("ii", $booking_id, $seat_number);
            $stmt->execute();

            // Commit transaction
            self::$connection->commit();
            return true;
        } catch (Exception $e) {
            // Rollback nếu có lỗi
            self::$connection->rollback();
            return false;
        }
    }

    public function getUserBookings($user_id)
    {
        $sql = "SELECT b.*, m.title, s.show_date, s.show_time, t.name as theater_name, 
                r.name as room_name, s.price, GROUP_CONCAT(bs.seat_id) as seats
                FROM bookings b 
                JOIN schedules s ON b.schedule_id = s.id 
                JOIN movies m ON s.movie_id = m.id 
                JOIN rooms r ON s.room_id = r.id
                JOIN theaters t ON r.theater_id = t.id 
                LEFT JOIN booking_seats bs ON b.id = bs.booking_id
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
                r.name as room_name, s.price, GROUP_CONCAT(bs.seat_id) as seats
                FROM bookings b 
                JOIN users u ON b.user_id = u.id 
                JOIN schedules s ON b.schedule_id = s.id 
                JOIN movies m ON s.movie_id = m.id 
                JOIN rooms r ON s.room_id = r.id
                JOIN theaters t ON r.theater_id = t.id 
                LEFT JOIN booking_seats bs ON b.id = bs.booking_id
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
}
