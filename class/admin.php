<?php
require_once "db_connect.php";

class Admin {
    public function adminLogin($email, $password) {
            global $conn;
            $stmt = $conn->prepare("SELECT * FROM admin WHERE email = ? AND password = ?");
            $stmt->bind_param("ss", $email, $password);
            $stmt->execute();
            $result = $stmt->get_result();
        
            $admin = $result->fetch_assoc();
        
            if ($admin) {
                unset($admin['password']);
                return $admin;
            } else {
                return false;
            }
        }
    }