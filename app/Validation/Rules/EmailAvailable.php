<?php

namespace App\Validation\Rules;

class EmailAvailable extends DatabaseRule
{

    public function validate($input)
    {
        $stmt = $this->db->prepare("SELECT * FROM USERS WHERE user_email = :user_email");
        $stmt->bindParam(':user_email', $input);
        $stmt->execute();
        $result = $stmt->fetch();
        if ($result) {
            return false;
        }
        return true;
    }
}
