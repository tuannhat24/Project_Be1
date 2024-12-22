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

    public function addMovie($title, $description, $duration, $release_date, $status, $poster)
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

        if ($poster !== null) {
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

    public function searchMovies($keyword, $status = null)
    {
        $sql = "SELECT * FROM movies WHERE (title LIKE ? OR description LIKE ?)";
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

    public function getFilteredMovies($genre = '', $year = '', $country = '', $sort = 'newest') {
        $sql = "SELECT DISTINCT m.* FROM movies m";
        
        if (!empty($genre)) {
            $sql .= " INNER JOIN movie_genres mg ON m.id = mg.movie_id
                      INNER JOIN genres g ON mg.genre_id = g.id
                      WHERE g.slug = ?";
        } else {
            $sql .= " WHERE 1=1";
        }

        $types = "";
        $params = [];

        if (!empty($genre)) {
            $types .= "s";
            $params[] = $genre;
        }

        if (!empty($year)) {
            $sql .= " AND YEAR(m.release_date) = ?";
            $types .= "i";
            $params[] = $year;
        }

        if (!empty($country)) {
            $sql .= " AND m.country = ?";
            $types .= "s";
            $params[] = $country;
        }

        switch ($sort) {
            case 'oldest':
                $sql .= " ORDER BY m.release_date ASC";
                break;
            case 'rating':
                $sql .= " ORDER BY m.rates DESC";
                break;
            case 'name':
                $sql .= " ORDER BY m.title ASC";
                break;
            default:
                $sql .= " ORDER BY m.release_date DESC";
        }

        $stmt = self::$connection->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        return $this->select($stmt);
    }

    public function getMovieYears() {
        $sql = "SELECT DISTINCT YEAR(release_date) as year 
                FROM movies 
                ORDER BY year DESC";
        $stmt = self::$connection->prepare($sql);
        $years = $this->select($stmt);
        return array_column($years, 'year');
    }

    public function getMovieCountries() {
        $sql = "SELECT DISTINCT COALESCE(country, 'Việt Nam') as country 
                FROM movies 
                WHERE country IS NOT NULL OR country != ''
                ORDER BY country";
        $stmt = self::$connection->prepare($sql);
        $countries = $this->select($stmt);
        return array_column($countries, 'country');
    }

    public function getMovieGenres($movieId) {
        $sql = "SELECT g.* FROM genres g 
                INNER JOIN movie_genres mg ON g.id = mg.genre_id 
                WHERE mg.movie_id = ?";
        
        $stmt = self::$connection->prepare($sql);
        $stmt->bind_param("i", $movieId);
        $stmt->execute();
        
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
