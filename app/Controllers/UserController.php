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

    public function profile($request, $response)
    {

        return $this->c->view->render($response, 'profile.twig', [
            'lastLogin' => $_SESSION['last_login']->format('Y-m-d')
        ]);
    }
}