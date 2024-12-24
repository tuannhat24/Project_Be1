<?php
require_once 'db.php';

class CommentModel extends Database
{
    public function addComment($movie_id, $user_id, $comment)
    {
        $sql = "INSERT INTO comments (movie_id, user_id, comment) VALUES (?, ?, ?)";
        $stmt = self::$connection->prepare($sql);
        $stmt->bind_param("iis", $movie_id, $user_id, $comment);
        
        if (!$stmt->execute()) {
            error_log("Error adding comment: " . $stmt->error);
            return false;
        }
        return true;
    }

    public function getComments($movie_id)
    {
        $sql = "SELECT * FROM comments WHERE movie_id = ? ORDER BY created_at DESC";
        $stmt = self::$connection->prepare($sql);
        $stmt->bind_param("i", $movie_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function delete($id)
    {
        $sql = parent::$connection->prepare('DELETE FROM `comments` WHERE `id` = ?');
        if ($sql) {
            $sql->bind_param('i', $id);
            return $sql->execute();
        } else {
            echo "Error preparing statement: " . parent::$connection->error; 
            return false;
        }
    }
}
?>