<?php

namespace App\Auth;

use PDO;
use PDOException;
use App\Models\User;

class Auth
{

    protected $db;
    public static $currentHashAlgorithm = PASSWORD_DEFAULT;
    public static $currentHashOptions = [
        'cost' => 14
    ];

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function user()
    {
        if (!isset($_SESSION['user'])) {
            return false;
        }
        $stmt = $this->db->prepare('SELECT * FROM USERS WHERE user_id = :user_id');
        $stmt->bindParam(':user_id', $_SESSION['user']);
        try {
            $stmt->execute();
            $stmt->setFetchMode(PDO::FETCH_CLASS, User::class);
            $user = $stmt->fetch();
            return $user;
        } catch (PDOException $e) {
            // TODO logg $e message
            return false;
        }
    }

    public function check()
    {
        return isset($_SESSION['user']);
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

                if ($this->passwordNeedsRehash($user)) {
                    $this->rehashPassword($user, $password);
                }
                return true;
            }

        } catch (PDOException $e) {
            var_dump($e->getMessage());
            // TODO: Log error
            return false;
        }
        return false;
    }

    private function passwordNeedsRehash($user):bool
    {
        $hashedUserPassword = $user->user_pw;

        return password_needs_rehash(
            $hashedUserPassword,
            self::$currentHashAlgorithm,
            self::$currentHashOptions
        );
    }

    private function rehashPassword($user, $password)
    {
        $stmt = $this->db->prepare('UPDATE users SET user_pw = :user_pw ' .
                                   'WHERE user_id = :user_id');
        $stmt->execute([
            ':user_id' => $user->user_id,
            ':user_pw' => password_hash(
                $password,
                self::$currentHashAlgorithm,
                self::$currentHashOptions
            )
        ]);
    }

    public function logout()
    {
        unset($_SESSION['user']);
    }

    public function changePassword($newPassword)
    {
        $newPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare('UPDATE users SET user_pw = :user_pw WHERE user_id = :user_id');
        $stmt->bindparam(':user_pw', $newPassword);
        $stmt->bindParam(':user_id', $this->user()->user_id);
        try {
            $stmt->execute();

            return true;
        } catch (PDOException $e) {
            var_dump($e->getMessage());
            die();
            // TODO: Logg error
            return false;
        }
    }
}
