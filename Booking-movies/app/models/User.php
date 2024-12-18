<?php
require_once 'db.php';

class User extends Database 
{
    public function login($username, $password) 
    {
        $sql = "SELECT * FROM users WHERE username = ?";
        $stmt = self::$connection->prepare($sql);
        $stmt->bind_param("s", $username);
        
        $result = $this->select($stmt);
        
        if (count($result) > 0 && password_verify($password, $result[0]['password'])) {
            return $result[0];
        }
        return false;
    }

    public function register($username, $password, $email, $fullname, $phone) 
    {
        // Kiểm tra username và email đã tồn tại chưa
        $check_sql = "SELECT id FROM users WHERE username = ? OR email = ?";
        $check_stmt = self::$connection->prepare($check_sql);
        $check_stmt->bind_param("ss", $username, $email);
        $check_result = $this->select($check_stmt);
        
        if (count($check_result) > 0) {
            return false;
        }
        
        $sql = "INSERT INTO users (username, password, email, fullname, phone, role) 
                VALUES (?, ?, ?, ?, ?, 'user')";
                
        $stmt = self::$connection->prepare($sql);
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt->bind_param("sssss", $username, $hashed_password, $email, $fullname, $phone);
        
        return $stmt->execute();
    }

    public function getUserById($id) 
    {
        $sql = "SELECT * FROM users WHERE id = ?";
        $stmt = self::$connection->prepare($sql);
        $stmt->bind_param("i", $id);
        
        return $this->select($stmt);
    }

    public function updateProfile($id, $email, $fullname, $phone) 
    {
        $sql = "UPDATE users SET email = ?, fullname = ?, phone = ? WHERE id = ?";
        $stmt = self::$connection->prepare($sql);
        $stmt->bind_param("sssi", $email, $fullname, $phone, $id);
        
        return $stmt->execute();
    }

    public function changePassword($id, $old_password, $new_password) 
    {
        // Kiểm tra mật khẩu cũ
        $check_sql = "SELECT password FROM users WHERE id = ?";
        $check_stmt = self::$connection->prepare($check_sql);
        $check_stmt->bind_param("i", $id);
        $result = $this->select($check_stmt);
        
        if (!password_verify($old_password, $result[0]['password'])) {
            return false;
        }
        
        $sql = "UPDATE users SET password = ? WHERE id = ?";
        $stmt = self::$connection->prepare($sql);
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt->bind_param("si", $hashed_password, $id);
        
        return $stmt->execute();
    }

    public function getAllUsers() 
    {
        $sql = "SELECT id, username, email, fullname, phone, role, created_at 
                FROM users ORDER BY created_at DESC";
        $stmt = self::$connection->prepare($sql);
        
        return $this->select($stmt);
    }
} 