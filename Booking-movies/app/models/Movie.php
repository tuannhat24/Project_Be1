<?php
require_once 'db.php';

class Movie extends Database
{
    public function getAllMovies($status = null)
    {
        $sql = "SELECT * FROM movies";
        if ($status) {
            $sql .= " WHERE status = ?";
        }
        $sql .= " ORDER BY release_date DESC";

        $stmt = self::$connection->prepare($sql);
        if ($status) {
            $stmt->bind_param("s", $status);
        }

        return $this->select($stmt);
    }

    public function getMovieById($id)
    {
        $sql = "SELECT * FROM movies WHERE id = ?";
        $stmt = self::$connection->prepare($sql);
        $stmt->bind_param("i", $id);

        return $this->select($stmt);
    }

    public function searchMovies($keyword, $status = null)
    {
        $sql = "SELECT * FROM movies WHERE title LIKE ? OR description LIKE ?";
        if ($status) {
            $sql .= " AND status = ?";
        }

        $stmt = self::$connection->prepare($sql);
        $search = "%$keyword%";

        if ($status) {
            $stmt->bind_param("sss", $search, $search, $status);
        } else {
            $stmt->bind_param("ss", $search, $search);
        }

        return $this->select($stmt);
    }

    public function addMovie($title, $description, $duration, $release_date, $status, $poster = null)
    {
        $sql = "INSERT INTO movies (title, description, duration, release_date, status, poster) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = self::$connection->prepare($sql);
        $stmt->bind_param("ssisss", $title, $description, $duration, $release_date, $status, $poster);

        return $stmt->execute();
    }

    public function updateMovie($id, $title, $description, $duration, $release_date, $status, $poster = null)
    {
        $sql = "UPDATE movies SET 
                title = ?, 
                description = ?, 
                duration = ?, 
                release_date = ?, 
                status = ?";

        if ($poster !== null) {
            $sql .= ", poster = ?";
        }

        $sql .= " WHERE id = ?";

        $stmt = self::$connection->prepare($sql);

        if ($poster != null) {
            $stmt->bind_param("ssisssi", $title, $description, $duration, $release_date, $status, $poster, $id);
        } else {
            $stmt->bind_param("ssissi", $title, $description, $duration, $release_date, $status, $id);
        }

        return $stmt->execute();
    }

    public function deleteMovie($id)
    {
        $sql = "DELETE FROM movies WHERE id = ?";
        $stmt = self::$connection->prepare($sql);
        $stmt->bind_param("i", $id);

        return $stmt->execute();
    }
}
