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
        
        if (count($result) > 0) {
            $user = $result[0];
            
            // Kiểm tra tài khoản có bị khóa không
            if (isset($user['status']) && $user['status'] == 'blocked') {
                return ['error' => 'Tài khoản của bạn đã bị khóa'];
            }
            
            // Kiểm tra mật khẩu
            if (password_verify($password, $user['password'])) {
                return $user;
            }
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
        $sql = "SELECT id, username, email, fullname, phone, role, status, created_at 
                FROM users ORDER BY created_at DESC";
        $stmt = self::$connection->prepare($sql);
        
        return $this->select($stmt);
    }

    public function updateUserStatus($user_id, $status) 
    {
        try {
            // Debug
            error_log("Updating user status: ID=$user_id, Status=$status");
            
            // Kiểm tra status hợp lệ
            if (!in_array($status, ['active', 'blocked'])) {
                error_log("Invalid status: $status");
                return false;
            }
            
            // Không cho phép khóa tài khoản admin
            $user = $this->getUserById($user_id);
            if (!$user || $user[0]['role'] == 'admin') {
                error_log("Cannot block admin account");
                return false;
            }
            
            $sql = "UPDATE users SET status = ? WHERE id = ?";
            $stmt = self::$connection->prepare($sql);
            $stmt->bind_param("si", $status, $user_id);
            
            $result = $stmt->execute();
            error_log("Update result: " . ($result ? "success" : "failed"));
            return $result;
            
        } catch (Exception $e) {
            error_log("Error updating user status: " . $e->getMessage());
            return false;
        }
    }

    public function updateUserByAdmin($user_id, $email, $fullname, $phone, $role) 
    {
        // Kiểm tra email đã tồn tại chưa (trừ email hiện tại của user)
        $check_sql = "SELECT id FROM users WHERE email = ? AND id != ?";
        $check_stmt = self::$connection->prepare($check_sql);
        $check_stmt->bind_param("si", $email, $user_id);
        $check_result = $this->select($check_stmt);
        
        if (count($check_result) > 0) {
            return false;
        }
        
        // Kiểm tra role hợp lệ
        if (!in_array($role, ['admin', 'user'])) {
            return false;
        }
        
        // Không cho phép thay đổi role của chính mình
        if ($user_id == $_SESSION['user_id']) {
            $role = $_SESSION['role'];
        }
        
        $sql = "UPDATE users SET email = ?, fullname = ?, phone = ?, role = ? WHERE id = ?";
        $stmt = self::$connection->prepare($sql);
        $stmt->bind_param("ssssi", $email, $fullname, $phone, $role, $user_id);
        
        return $stmt->execute();
    }

    public function updateUserRole($user_id, $role) 
    {
        try {
            // Kiểm tra role hợp lệ
            if (!in_array($role, ['admin', 'user'])) {
                return false;
            }
            
            // Lấy thông tin user hiện tại
            $user = $this->getUserById($user_id);
            if (!$user) {
                return false;
            }
            
            // Cập nhật role nhưng giữ nguyên các thông tin khác
            $sql = "UPDATE users 
                    SET role = ?,
                        email = ?,
                        fullname = ?,
                        phone = ?
                    WHERE id = ?";
                    
            $stmt = self::$connection->prepare($sql);
            $email = $user[0]['email'];
            $fullname = $user[0]['fullname'];
            $phone = $user[0]['phone'];
            
            $stmt->bind_param("ssssi", $role, $email, $fullname, $phone, $user_id);
            
            return $stmt->execute();
            
        } catch (Exception $e) {
            error_log("Error updating user role: " . $e->getMessage());
            return false;
        }
    }
} 