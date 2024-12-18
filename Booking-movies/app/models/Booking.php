<?php
require_once 'db.php';

class Booking extends Database 
{
    public function createBooking($user_id, $schedule_id, $seat_number) 
    {
        $sql = "INSERT INTO bookings (user_id, schedule_id, seat_number, status) 
                VALUES (?, ?, ?, 'pending')";
                
        $stmt = self::$connection->prepare($sql);
        $stmt->bind_param("iis", $user_id, $schedule_id, $seat_number);
        
        return $stmt->execute();
    }

    public function getUserBookings($user_id) 
    {
        $sql = "SELECT b.*, m.title, s.show_time, t.name as theater_name 
                FROM bookings b 
                JOIN schedules s ON b.schedule_id = s.id 
                JOIN movies m ON s.movie_id = m.id 
                JOIN theaters t ON s.theater_id = t.id 
                WHERE b.user_id = ? 
                ORDER BY b.booking_date DESC";
                
        $stmt = self::$connection->prepare($sql);
        $stmt->bind_param("i", $user_id);
        
        return $this->select($stmt);
    }

    public function getBookedSeats($schedule_id) 
    {
        $sql = "SELECT seat_number 
                FROM bookings 
                WHERE schedule_id = ? AND status != 'cancelled'";
                
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

    public function getAllBookings() 
    {
        $sql = "SELECT b.*, u.username, u.fullname, u.phone, 
                m.title, s.show_time, t.name as theater_name 
                FROM bookings b 
                JOIN users u ON b.user_id = u.id 
                JOIN schedules s ON b.schedule_id = s.id 
                JOIN movies m ON s.movie_id = m.id 
                JOIN theaters t ON s.theater_id = t.id 
                ORDER BY b.booking_date DESC";
                
        $stmt = self::$connection->prepare($sql);
        
        return $this->select($stmt);
    }
} 