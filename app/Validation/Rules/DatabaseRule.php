<?php

namespace App\Validation\Rules;

use Respect\Validation\Rules\AbstractRule;

abstract class DatabaseRule extends AbstractRule
{
    protected $db;

    public function __construct($db)
    {
        if (!isset($db)) {
            throw new \Exception("Need to have access to the database. ");
        }
        $this->db = $db;
    }
}
