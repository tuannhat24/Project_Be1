<?php
require_once 'db.php';

class Schedule extends Database
{
    public function getAllSchedules()
    {
        $sql = "SELECT s.*, m.title as movie_title, r.name as room_name, t.name as theater_name 
                FROM schedules s
                JOIN movies m ON s.movie_id = m.id
                JOIN rooms r ON s.room_id = r.id
                JOIN theaters t ON r.theater_id = t.id
                ORDER BY s.show_date DESC, s.show_time DESC";
                
        $stmt = self::$connection->prepare($sql);
        return $this->select($stmt);
    }

    public function getScheduleById($id)
    {
        $sql = "SELECT s.*, m.title as movie_title, r.name as room_name, t.name as theater_name 
                FROM schedules s
                JOIN movies m ON s.movie_id = m.id
                JOIN rooms r ON s.room_id = r.id
                JOIN theaters t ON r.theater_id = t.id
                WHERE s.id = ?";
                
        $stmt = self::$connection->prepare($sql);
        $stmt->bind_param("i", $id);
        return $this->select($stmt);
    }

    public function addSchedule($movie_id, $room_id, $show_date, $show_time, $price)
    {
        $sql = "INSERT INTO schedules (movie_id, room_id, show_date, show_time, price) 
                VALUES (?, ?, ?, ?, ?)";
                
        $stmt = self::$connection->prepare($sql);
        $stmt->bind_param("iissi", $movie_id, $room_id, $show_date, $show_time, $price);
        return $stmt->execute();
    }

    public function updateSchedule($id, $movie_id, $room_id, $show_date, $show_time, $price)
    {
        $sql = "UPDATE schedules 
                SET movie_id = ?, room_id = ?, show_date = ?, show_time = ?, price = ? 
                WHERE id = ?";
                
        $stmt = self::$connection->prepare($sql);
        $stmt->bind_param("iissii", $movie_id, $room_id, $show_date, $show_time, $price, $id);
        return $stmt->execute();
    }

    public function deleteSchedule($id)
    {
        // Kiểm tra xem có booking nào cho lịch chiếu này không
        $check_sql = "SELECT COUNT(*) as count FROM bookings WHERE schedule_id = ?";
        $check_stmt = self::$connection->prepare($check_sql);
        $check_stmt->bind_param("i", $id);
        $result = $this->select($check_stmt);
        
        if ($result[0]['count'] > 0) {
            return false; // Không thể xóa vì đã có người đặt vé
        }
        
        $sql = "DELETE FROM schedules WHERE id = ?";
        $stmt = self::$connection->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function getScheduleByMovieRoomDateTime($movie_id, $room_id, $show_date, $show_time)
    {
        $sql = "SELECT * FROM schedules 
                WHERE movie_id = ? AND room_id = ? AND show_date = ? AND show_time = ?";
                
        $stmt = self::$connection->prepare($sql);
        $stmt->bind_param("iiss", $movie_id, $room_id, $show_date, $show_time);
        return $this->select($stmt);
    }

    public function getSchedulesByMovie($movie_id)
    {
        $sql = "SELECT s.*, m.title as movie_title, r.name as room_name, t.name as theater_name 
                FROM schedules s
                JOIN movies m ON s.movie_id = m.id
                JOIN rooms r ON s.room_id = r.id
                JOIN theaters t ON r.theater_id = t.id
                WHERE s.movie_id = ? AND s.show_date >= CURDATE()
                ORDER BY s.show_date ASC, s.show_time ASC";
                
        $stmt = self::$connection->prepare($sql);
        $stmt->bind_param("i", $movie_id);
        return $this->select($stmt);
    }

    public function getUpcomingSchedules()
    {
        $sql = "SELECT s.*, m.title as movie_title, r.name as room_name, t.name as theater_name 
                FROM schedules s
                JOIN movies m ON s.movie_id = m.id
                JOIN rooms r ON s.room_id = r.id
                JOIN theaters t ON r.theater_id = t.id
                WHERE s.show_date >= CURDATE()
                ORDER BY s.show_date ASC, s.show_time ASC
                LIMIT 10";
                
        $stmt = self::$connection->prepare($sql);
        return $this->select($stmt);
    }
}
