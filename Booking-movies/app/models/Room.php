<?php
require_once 'db.php';

class Room extends Database 
{
    public function getAllRooms() 
    {
        $sql = "SELECT r.*, t.name as theater_name 
                FROM rooms r
                JOIN theaters t ON r.theater_id = t.id
                ORDER BY t.name, r.name";
                
        $stmt = self::$connection->prepare($sql);
        return $this->select($stmt);
    }

    public function getRoomById($id) 
    {
        $sql = "SELECT r.*, t.name as theater_name 
                FROM rooms r
                JOIN theaters t ON r.theater_id = t.id
                WHERE r.id = ?";
                
        $stmt = self::$connection->prepare($sql);
        $stmt->bind_param("i", $id);
        return $this->select($stmt);
    }

    public function getRoomsByTheater($theater_id) 
    {
        $sql = "SELECT * FROM rooms WHERE theater_id = ? ORDER BY name";
        $stmt = self::$connection->prepare($sql);
        $stmt->bind_param("i", $theater_id);
        return $this->select($stmt);
    }
} 