<?php

namespace App\Validation\Rules;

use Respect\Validation\Rules\AbstractRule;

class EmailAvailable extends AbstractRule
{

    protected $c;

    public function __construct($c)
    {
        $this->c = $c;
    }
    
    public function validate($input)
    {
        $stmt = $this->c->db->prepare("SELECT * FROM USERS WHERE user_email = :user_email");
        $stmt->bindParam(':user_email', $input);
        $stmt->execute();
        $result = $stmt->fetch();
        if ($result) {
            return false;
        }
        return true;
    }
}