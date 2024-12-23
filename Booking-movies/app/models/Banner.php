<?php
require_once 'db.php';

class Banner extends Database {

    public function getAllActiveBanners($limit = 10) {
        $sql = "SELECT b.*, m.title, m.description, m.rates, m.duration, m.poster 
                FROM banners b 
                LEFT JOIN movies m ON b.movie_id = m.id 
                WHERE b.status = 'active' 
                ORDER BY b.created_at DESC 
                LIMIT ?";
        $stmt = self::$connection->prepare($sql);
        $stmt->bind_param("i", $limit);
        
        return $this->select($stmt);
    }

    public function getBannerById($id) {
        $sql = "SELECT b.*, m.title, m.description, m.rates, m.duration, m.poster 
                FROM banners b
                LEFT JOIN movies m ON b.movie_id = m.id
                WHERE b.id = ?";
        $stmt = self::$connection->prepare($sql);
        $stmt->bind_param("i", $id);
        
        return $this->select($stmt);
    }

    public function getAllBanners() {
        $sql = "SELECT b.*, m.title 
                FROM banners b
                LEFT JOIN movies m ON b.movie_id = m.id
                ORDER BY b.created_at DESC";
        $stmt = self::$connection->prepare($sql);
        
        return $this->select($stmt);
    }

    public function addBanner($data) {
        $sql = "INSERT INTO banners (movie_id, image, status) 
                VALUES (?, ?, ?)";
        $stmt = self::$connection->prepare($sql);
        $stmt->bind_param("iss", 
            $data['movie_id'],
            $data['image'],
            $data['status'] ?? 'active'
        );

        return $stmt->execute();
    }

    public function updateBanner($id, $data) {
        $sql = "UPDATE banners 
                SET movie_id = ?, image = ?, status = ? 
                WHERE id = ?";
        $stmt = self::$connection->prepare($sql);
        $stmt->bind_param("issi",
            $data['movie_id'],
            $data['image'],
            $data['status'],
            $id
        );

        return $stmt->execute();
    }

    public function updateBannerStatus($id, $status) {
        $sql = "UPDATE banners SET status = ? WHERE id = ?";
        $stmt = self::$connection->prepare($sql);
        $stmt->bind_param("si", $status, $id);

        return $stmt->execute();
    }

    public function deleteBanner($id) {
        $sql = "DELETE FROM banners WHERE id = ?";
        $stmt = self::$connection->prepare($sql);
        $stmt->bind_param("i", $id);

        return $stmt->execute();
    }

    public function countBanners() {
        $sql = "SELECT COUNT(*) as total FROM banners";
        $stmt = self::$connection->prepare($sql);
        $result = $this->select($stmt);
        
        return $result[0]['total'];
    }

    public function getTotalBanners() {
        $sql = "SELECT COUNT(*) as total FROM banners";
        $result = self::$connection->query($sql);
        $row = $result->fetch_assoc();
        return $row['total'];
    }

    public function getBannersByPagination($offset, $limit) {
        $sql = "SELECT * FROM banners ORDER BY id DESC LIMIT ?, ?";
        $stmt = self::$connection->prepare($sql);
        $stmt->bind_param("ii", $offset, $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
} 