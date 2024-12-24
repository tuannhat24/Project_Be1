<?php
require_once 'db.php';

class Room extends Database
{
    // Lấy tất cả các phòng chiếu
    public function getAllRooms($limit = 10)
    {
        $sql = "SELECT r.*, t.name as theater_name 
                FROM rooms r
                JOIN theaters t ON r.theater_id = t.id
                ORDER BY t.name, r.name
                LIMIT ?";
        $stmt = self::$connection->prepare($sql);
        $stmt->bind_param("i", $limit);
        return $this->select($stmt);
    }

    // Lấy phòng chiếu theo ID
    public function getRoomById($room_id)
    {
        $sql = "SELECT r.*, t.name as theater_name
                FROM rooms r
                JOIN theaters t ON r.theater_id = t.id
                WHERE r.id = ?";
        $stmt = self::$connection->prepare($sql);
        $stmt->bind_param('i', $room_id);
        $stmt->execute();

        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        } else {
            return null;
        }
    }

    // Lấy phòng chiếu theo rạp
    public function getRoomsByTheater($theater_id)
    {
        $sql = "SELECT r.*, t.name as theater_name
                FROM rooms r
                JOIN theaters t ON r.theater_id = t.id
                WHERE r.theater_id = ? ORDER BY r.name";
        $stmt = self::$connection->prepare($sql);
        $stmt->bind_param("i", $theater_id);
        return $this->select($stmt);
    }

    // Thêm phòng chiếu mới
    public function addRoom($data)
    {
        $sql = "INSERT INTO rooms (theater_id, name, capacity, room_type, status) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = self::$connection->prepare($sql);
        $stmt->bind_param(
            "isisi",
            $data['theater_id'],
            $data['name'],
            $data['capacity'],
            $data['room_type'],
            $data['status']
        );

        return $stmt->execute();
    }

    // Cập nhật phòng chiếu
    public function updateRoom($id, $data)
    {
        $sql = "UPDATE rooms 
                SET theater_id = ?, name = ?, capacity = ?, room_type = ?, status = ? 
                WHERE id = ?";
        $stmt = self::$connection->prepare($sql);
        $stmt->bind_param(
            "isisis",
            $data['theater_id'],
            $data['name'],
            $data['capacity'],
            $data['room_type'],
            $data['status'],
            $id
        );

        return $stmt->execute();
    }

    // Cập nhật trạng thái phòng chiếu
    public function updateRoomStatus($id, $status)
    {
        $sql = "UPDATE rooms SET status = ? WHERE id = ?";
        $stmt = self::$connection->prepare($sql);
        $stmt->bind_param("si", $status, $id);

        return $stmt->execute();
    }

    // Xóa phòng chiếu
    public function deleteRoom($id)
    {
        $sql = "DELETE FROM rooms WHERE id = ?";
        $stmt = self::$connection->prepare($sql);
        $stmt->bind_param("i", $id);

        return $stmt->execute();
    }

    // Đếm số lượng phòng chiếu
    public function countRooms()
    {
        $sql = "SELECT COUNT(*) as total FROM rooms";
        $stmt = self::$connection->prepare($sql);
        $result = $this->select($stmt);

        return $result[0]['total'];
    }

    // Lấy tổng số phòng chiếu
    public function getTotalRooms()
    {
        $sql = "SELECT COUNT(*) as total FROM rooms";
        $result = self::$connection->query($sql);
        $row = $result->fetch_assoc();
        return $row['total'];
    }

    // Lấy phòng chiếu theo phân trang
    public function getRoomsByPagination($offset, $limit)
    {
        $sql = "SELECT r.*, t.name as theater_name
                FROM rooms r
                JOIN theaters t ON r.theater_id = t.id
                ORDER BY r.name 
                LIMIT ?, ?";
        $stmt = self::$connection->prepare($sql);
        $stmt->bind_param("ii", $offset, $limit);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
