<?php

namespace App\Controllers;

use App\Models\User;
use PDO;
use PDOException;
use Respect\Validation\Validator as v;

class UserController extends Controller
{
    public function index($request, $response)
    {
        $user = new User;
        $users = [
            ['username' => 'alex'],
            ['username' => 'billy'],
            ['username' => 'dale'],
        ];
        return $this->c->view->render($response, 'users.twig', [
            'users' => $users,
        ]);
    }
}