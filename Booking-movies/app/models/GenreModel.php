<?php
require_once 'db.php';

class GenreModel extends Database
{
    public function getAllGenres()
    {
        $sql = "SELECT * FROM genres ORDER BY name";
        $stmt = self::$connection->prepare($sql);
        return $this->select($stmt);
    }

    public function addGenre($name, $slug)
    {
        $sql = "INSERT INTO genres (name, slug) VALUES (?, ?)";
        $stmt = self::$connection->prepare($sql);
        $stmt->bind_param("ss", $name, $slug);
        return $stmt->execute();
    }

    public function getGenresByMovieId($movieId)
    {
        $sql = "SELECT g.* FROM genres g 
                INNER JOIN movie_genres mg ON g.id = mg.genre_id 
                WHERE mg.movie_id = ?";
        $stmt = self::$connection->prepare($sql);
        $stmt->bind_param("i", $movieId);
        return $this->select($stmt);
    }

    public function updateMovieGenres($movieId, $genreIds)
    {
        // Xóa các thể loại cũ
        $sql = "DELETE FROM movie_genres WHERE movie_id = ?";
        $stmt = self::$connection->prepare($sql);
        $stmt->bind_param("i", $movieId);
        $stmt->execute();

        // Thêm các thể loại mới
        if (!empty($genreIds)) {
            $sql = "INSERT INTO movie_genres (movie_id, genre_id) VALUES (?, ?)";
            $stmt = self::$connection->prepare($sql);
            foreach ($genreIds as $genreId) {
                $stmt->bind_param("ii", $movieId, $genreId);
                $stmt->execute();
            }
        }
        return true;
    }
} 