<?php
require_once 'db.php';

class TheaterModel extends Database
{
    // Lấy tất cả các rạp chiếu
    public function getAllTheaters()
    {
        $sql = "SELECT * FROM theaters";
        $stmt = self::$connection->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Lấy rạp chiếu theo ID
    public function getTheaterById($id)
    {
        $sql = "SELECT * FROM theaters WHERE id = ?";
        $stmt = self::$connection->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        } else {
            return null;
        }
    }

    // Thêm rạp chiếu mới
    public function addTheater($data)
    {
        $sql = "INSERT INTO theaters (name, address, phone, total_seats) VALUES (?, ?, ?, ?)";
        $stmt = self::$connection->prepare($sql);
        $stmt->bind_param("sssi", $data['name'], $data['address'], $data['phone'], $data['total_seats']);

        return $stmt->execute();
    }

    // Cập nhật thông tin rạp chiếu
    public function updateTheater($id, $data)
    {
        $sql = "UPDATE theaters SET name = ?, address = ?, phone = ?, total_seats = ? WHERE id = ?";
        $stmt = self::$connection->prepare($sql);
        $stmt->bind_param("sssii", $data['name'], $data['address'], $data['phone'], $data['total_seats'], $id);

        return $stmt->execute();
    }

    // Xóa rạp chiếu
    public function deleteTheater($id)
    {
        $sql = "DELETE FROM theaters WHERE id = ?";
        $stmt = self::$connection->prepare($sql);
        $stmt->bind_param("i", $id);

        return $stmt->execute();
    }

    // Đếm số lượng rạp chiếu
    public function countTheaters()
    {
        $sql = "SELECT COUNT(*) as total FROM theaters";
        $stmt = self::$connection->prepare($sql);
        $result = $this->select($stmt);

        return $result[0]['total'];
    }

    // Lấy tổng số rạp chiếu
    public function getTotalTheaters()
    {
        $sql = "SELECT COUNT(*) as total FROM theaters";
        $result = self::$connection->query($sql);
        $row = $result->fetch_assoc();

        return $row['total'];
    }

    // Lấy rạp chiếu theo phân trang
    public function getTheatersByPagination($offset, $limit)
    {
        $sql = "SELECT * FROM theaters ORDER BY name LIMIT ?, ?";
        $stmt = self::$connection->prepare($sql);
        $stmt->bind_param("ii", $offset, $limit);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function updateTheaterStatus($id, $status)
    {
        try {
            // Cập nhật trạng thái của phòng chiếu trong cơ sở dữ liệu
            $sql = "UPDATE theaters SET status = ? WHERE id = ?";
            $stmt = self::$connection->prepare($sql);

            // Ràng buộc giá trị cho câu lệnh SQL
            $stmt->bind_param("si", $status, $id);

            // Thực thi câu lệnh và kiểm tra kết quả
            return $stmt->execute();
        } catch (mysqli_sql_exception $e) {
            // Lỗi nếu có
            echo "Error: " . $e->getMessage();
            return false;
        }
    }
}
