<?php

namespace App\Auth;

use PDO;
use PDOException;
use App\Models\User;


class Auth {

protected $db;

public function __construct($db)
{
    $this->db = $db;
}

public function attempt($email, $password)
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE user_email = :user_email');
        $stmt->bindParam(':user_email', $email);
        try {
            $stmt->execute();
            $stmt->setFetchMode(PDO::FETCH_CLASS, User::class);
            $user = $stmt->fetch();

            if (!$user) {
                return false;
            }
            
            if (password_verify($password, $user->user_pw)) {
                $_SESSION['user'] = $user->user_id;
                return true;
            }
            
        } catch (PDOException $e) {
            var_dump($e->getMessage());
            // TODO: Log error
            return false;
        }
        return false;
    }
}