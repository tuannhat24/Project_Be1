<?php
require_once 'db.php';

class Seat extends Database
{
    public function getAllSeatsByRoom($room_id)
    {
        $sql = "SELECT * FROM seats WHERE room_id = ? ORDER BY seat_row, seat_number";
        $stmt = self::$connection->prepare($sql);
        $stmt->bind_param("i", $room_id);
        $stmt->execute();
        $result = $stmt->get_result();

        // Dùng fetch_all để lấy tất cả các ghế (mảng chứa tất cả kết quả)
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
