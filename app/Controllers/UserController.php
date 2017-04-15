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

    public function login($request, $response)
    {
        echo 'Login';
    }

    public function createUser($request, $response)
    {

        $validation = $this->c->validator->validate($request, [
            'email' => v::noWhitespace()->notEmpty()->emailAvailable($this->c->db),
            'username' => v::notEmpty()->alpha()->UsernameAvailable($this->c->db),
            'password' => v::noWhitespace()->notEmpty(),
        ]);

        if ($validation->failed()) {
            return $response->withRedirect($this->c->router->pathFor('user.register'));
        }
        $params = $request->getParams();
        $stmt = $this->c->db->prepare("INSERT INTO users (user_pw, user_name, user_email) VALUES (:user_pw, :user_name, :user_email) ");
        $stmt->bindParam(':user_pw', password_hash($params['password'], PASSWORD_DEFAULT));
        $stmt->bindParam(':user_name', $params['username']);
        $stmt->bindParam(':user_email', $params['email']);
        try {
            $stmt->execute();
        } catch (PDOException $e) {
            $this->c->flash->addMessage('error', 'Could not create user. ');
            return $response->withRedirect($this->c->router->pathFor('user.register'));
        }
        return $response->withRedirect($this->c->router->pathFor('todos.index'));

    }

    public function register($request, $response)
    {
        return $this->c->view->render($response, 'newuser.twig');
    }
}