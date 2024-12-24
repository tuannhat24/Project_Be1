<?php
require_once 'db.php';

class RatingModel extends Database
{
    // Thêm hoặc cập nhật đánh giá
    public function addOrUpdateRating($movie_id, $user_id, $rating)
    {
        // Kiểm tra xem người dùng đã đánh giá phim này chưa
        $sql = "SELECT * FROM ratings WHERE movie_id = ? AND user_id = ?";
        $stmt = self::$connection->prepare($sql);
        $stmt->bind_param("ii", $movie_id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Nếu đã có đánh giá, cập nhật
            $sql = "UPDATE ratings SET rating = ? WHERE movie_id = ? AND user_id = ?";
            $stmt = self::$connection->prepare($sql);
            $stmt->bind_param("iii", $rating, $movie_id, $user_id);
        } else {
            // Nếu chưa có đánh giá, thêm mới
            $sql = "INSERT INTO ratings (movie_id, user_id, rating) VALUES (?, ?, ?)";
            $stmt = self::$connection->prepare($sql);
            $stmt->bind_param("iii", $movie_id, $user_id, $rating);
        }

        if (!$stmt->execute()) {
            error_log("Error adding/updating rating: " . $stmt->error); 
            return false;
        }
        return true;
    }

    // Lấy đánh giá trung bình
    public function getAverageRating($movie_id)
    {
        $sql = "SELECT AVG(rating) as average_rating FROM ratings WHERE movie_id = ?";
        $stmt = self::$connection->prepare($sql);
        $stmt->bind_param("i", $movie_id);
        if (!$stmt->execute()) {
            error_log("Error getting average rating: " . $stmt->error); // Ghi lỗi vào log
            return null; // Trả về null nếu có lỗi
        }
        $result = $stmt->get_result();
        return $result->fetch_assoc(); // Trả về đánh giá trung bình
    }
}
