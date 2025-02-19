<?php
require_once '../../config/database.php';

class UserModel extends Database {

    public function login($data) {
        $connection = parent::openConnection();
        
        try {
            $connection->beginTransaction();
            
            $stmt = $connection->prepare("SELECT * FROM users WHERE user_email = ?");
            $stmt->bindParam(1, $data['email']);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($data['password'], $user['user_password'])) {
                $connection->commit();
                return $user;
            } else {
                $connection->rollBack();
                return false;
            }
            
        } catch (PDOException $e) {
            $connection->rollBack();
            echo "Error: " . $e->getMessage();
            return false;
        } finally {
            parent::closeConnection();
        }
    }



    public function register($data) {
        $connection = parent::openConnection();
        
        try {
            // Hash the password before storing it
            $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT);
            
            // Prepare the query to insert the new user
            $stmt = $connection->prepare("INSERT INTO users (user_email, user_password) VALUES (?, ?)");
            $stmt->bindParam(1, $data['email']);
            $stmt->bindParam(2, $hashedPassword);
            
            // Execute the query
            $stmt->execute();
            
            return 200; // User registered successfully
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        } finally {
            // Close the connection
            parent::closeConnection();
        }
    }

    

}
