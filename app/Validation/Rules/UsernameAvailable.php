<?php

namespace App\Validation\Rules;

class UsernameAvailable extends DatabaseRule
{
    public function validate($input)
    {
        $stmt = $this->db->prepare("SELECT * FROM USERS WHERE user_name = :user_name");
        $stmt->bindParam(':user_name', $input);
        $stmt->execute();
        $result = $stmt->fetch();
        if ($result) {
            return false;
        }
        return true;
    }
}