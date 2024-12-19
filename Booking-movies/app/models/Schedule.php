<?php
require_once 'db.php';

class Schedule extends Database
{
    public function getAllSchedules()
    {
        $sql = "SELECT s.*, m.title as movie_title, t.name as theater_name, r.name as room_name
                FROM schedules s
                JOIN movies m ON s.movie_id = m.id
                JOIN rooms r ON s.room_id = r.id
                JOIN theaters t ON r.theater_id = t.id
                ORDER BY s.show_date, s.show_time";
                
        $stmt = self::$connection->prepare($sql);
        return $this->select($stmt);
    }

    public function getSchedulesByMovie($movie_id)
    {
        $sql = "SELECT s.*, t.name as theater_name, r.name as room_name
                FROM schedules s 
                JOIN rooms r ON s.room_id = r.id
                JOIN theaters t ON r.theater_id = t.id
                WHERE s.movie_id = ? 
                AND CONCAT(s.show_date, ' ', s.show_time) > NOW()
                ORDER BY s.show_date, s.show_time";

        $stmt = self::$connection->prepare($sql);
        $stmt->bind_param("i", $movie_id);

        return $this->select($stmt);
    }

    public function getSchedulesByDate($date, $theater_id = null)
    {
        $sql = "SELECT s.*, m.title, m.poster, t.name as theater_name, r.name as room_name
                FROM schedules s 
                JOIN movies m ON s.movie_id = m.id 
                JOIN rooms r ON s.room_id = r.id
                JOIN theaters t ON r.theater_id = t.id 
                WHERE s.show_date = ?";

        if ($theater_id) {
            $sql .= " AND t.id = ?";
        }

        $sql .= " ORDER BY s.show_time";

        $stmt = self::$connection->prepare($sql);

        if ($theater_id) {
            $stmt->bind_param("si", $date, $theater_id);
        } else {
            $stmt->bind_param("s", $date);
        }

        return $this->select($stmt);
    }

    public function addSchedule($movie_id, $room_id, $show_date, $show_time, $price)
    {
        $sql = "INSERT INTO schedules (movie_id, room_id, show_date, show_time, price) 
                VALUES (?, ?, ?, ?, ?)";
                
        $stmt = self::$connection->prepare($sql);
        $stmt->bind_param("iissd", $movie_id, $room_id, $show_date, $show_time, $price);

        return $stmt->execute();
    }

    public function updateSchedule($id, $movie_id, $room_id, $show_date, $show_time, $price)
    {
        $sql = "UPDATE schedules 
                SET movie_id = ?, 
                    room_id = ?, 
                    show_date = ?, 
                    show_time = ?, 
                    price = ? 
                WHERE id = ?";
                
        $stmt = self::$connection->prepare($sql);
        $stmt->bind_param("iissdi", $movie_id, $room_id, $show_date, $show_time, $price, $id);

        return $stmt->execute();
    }

    public function deleteSchedule($id)
    {
        $sql = "DELETE FROM schedules WHERE id = ?";
        $stmt = self::$connection->prepare($sql);
        $stmt->bind_param("i", $id);

        return $stmt->execute();
    }

    public function getScheduleById($id)
    {
        $sql = "SELECT s.*, m.title, t.name as theater_name, r.name as room_name
                FROM schedules s 
                JOIN movies m ON s.movie_id = m.id 
                JOIN rooms r ON s.room_id = r.id
                JOIN theaters t ON r.theater_id = t.id 
                WHERE s.id = ?";
                
        $stmt = self::$connection->prepare($sql);
        $stmt->bind_param("i", $id);

        return $this->select($stmt);
    }

    public function getScheduleByMovieRoomDateTime($movie_id, $room_id, $show_date, $show_time)
    {
        $sql = "SELECT s.*, m.title as movie_title, t.name as theater_name, r.name as room_name
                FROM schedules s
                JOIN movies m ON s.movie_id = m.id
                JOIN rooms r ON s.room_id = r.id
                JOIN theaters t ON r.theater_id = t.id
                WHERE s.movie_id = ? 
                AND s.room_id = ? 
                AND s.show_date = ?
                AND s.show_time = ?";

        $stmt = self::$connection->prepare($sql);
        $stmt->bind_param("iiss", $movie_id, $room_id, $show_date, $show_time);

        return $this->select($stmt);
    }
}
