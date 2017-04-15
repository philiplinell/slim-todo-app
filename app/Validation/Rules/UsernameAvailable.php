<?php

namespace App\Validation\Rules;

use Respect\Validation\Rules\AbstractRule;

class UsernameAvailable extends AbstractRule
{

    protected $c;

    public function __construct($c)
    {
        $this->c = $c;
    }
    
    public function validate($input)
    {
        $stmt = $this->c->db->prepare("SELECT * FROM USERS WHERE user_name = :user_name");
        $stmt->bindParam(':user_name', $input);
        $stmt->execute();
        $result = $stmt->fetch();
        if ($result) {
            return false;
        }
        return true;
    }
}