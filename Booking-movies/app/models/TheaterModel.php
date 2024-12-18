<?php
require_once 'db.php';

class TheaterModel extends Database 
{
    public function getAllTheaters() 
    {
        $sql = "SELECT * FROM theaters";
        $stmt = self::$connection->prepare($sql);
        
        return $this->select($stmt);
    }

    public function getTheaterById($id) 
    {
        $sql = "SELECT * FROM theaters WHERE id = ?";
        $stmt = self::$connection->prepare($sql);
        $stmt->bind_param("i", $id);
        
        return $this->select($stmt);
    }

    public function addTheater($name, $address, $total_seats) 
    {
        $sql = "INSERT INTO theaters (name, address, total_seats) VALUES (?, ?, ?)";
        $stmt = self::$connection->prepare($sql);
        $stmt->bind_param("ssi", $name, $address, $total_seats);
        
        return $stmt->execute();
    }

    public function updateTheater($id, $name, $address, $total_seats) 
    {
        $sql = "UPDATE theaters SET name = ?, address = ?, total_seats = ? WHERE id = ?";
        $stmt = self::$connection->prepare($sql);
        $stmt->bind_param("ssii", $name, $address, $total_seats, $id);
        
        return $stmt->execute();
    }

    public function deleteTheater($id) 
    {
        $sql = "DELETE FROM theaters WHERE id = ?";
        $stmt = self::$connection->prepare($sql);
        $stmt->bind_param("i", $id);
        
        return $stmt->execute();
    }
}