<?php
require_once 'db.php';

class Schedule extends Database 
{
    public function getAllSchedules() {
        $sql = "SELECT s.*, m.title as movie_title, t.name as theater_name 
                FROM schedules s
                JOIN movies m ON s.movie_id = m.id
                JOIN theaters t ON s.theater_id = t.id";
        $result = self::$connection->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getSchedulesByMovie($movie_id) 
    {
        $sql = "SELECT s.*, t.name as theater_name 
                FROM schedules s 
                JOIN theaters t ON s.theater_id = t.id 
                WHERE s.movie_id = ? AND s.show_time > NOW()
                ORDER BY s.show_time ASC";
                
        $stmt = self::$connection->prepare($sql);
        $stmt->bind_param("i", $movie_id);
        
        return $this->select($stmt);
    }

    public function getSchedulesByDate($date, $theater_id = null) 
    {
        $sql = "SELECT s.*, m.title, m.poster, t.name as theater_name 
                FROM schedules s 
                JOIN movies m ON s.movie_id = m.id 
                JOIN theaters t ON s.theater_id = t.id 
                WHERE DATE(s.show_time) = ?";
                
        if ($theater_id) {
            $sql .= " AND s.theater_id = ?";
        }
        
        $sql .= " ORDER BY s.show_time ASC";
        
        $stmt = self::$connection->prepare($sql);
        
        if ($theater_id) {
            $stmt->bind_param("si", $date, $theater_id);
        } else {
            $stmt->bind_param("s", $date);
        }
        
        return $this->select($stmt);
    }

    public function addSchedule($movie_id, $theater_id, $show_time, $price) 
    {
        $sql = "INSERT INTO schedules (movie_id, theater_id, show_time, price) 
                VALUES (?, ?, ?, ?)";
        $stmt = self::$connection->prepare($sql);
        $stmt->bind_param("iisd", $movie_id, $theater_id, $show_time, $price);
        
        return $stmt->execute();
    }

    public function updateSchedule($id, $movie_id, $theater_id, $show_time, $price) 
    {
        $sql = "UPDATE schedules 
                SET movie_id = ?, theater_id = ?, show_time = ?, price = ? 
                WHERE id = ?";
        $stmt = self::$connection->prepare($sql);
        $stmt->bind_param("iisdi", $movie_id, $theater_id, $show_time, $price, $id);
        
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
        $sql = "SELECT s.*, m.title, t.name as theater_name 
                FROM schedules s 
                JOIN movies m ON s.movie_id = m.id 
                JOIN theaters t ON s.theater_id = t.id 
                WHERE s.id = ?";
        $stmt = self::$connection->prepare($sql);
        $stmt->bind_param("i", $id);
        
        return $this->select($stmt);
    }
} 