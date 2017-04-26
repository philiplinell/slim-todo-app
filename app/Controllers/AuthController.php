<?php

namespace App\Controllers;

use App\Models\User;
use PDO;
use PDOException;
use Respect\Validation\Validator as v;

class AuthController extends Controller
{

    public function getSignOut($request, $response)
    {
        $this->c->auth->logout();
        return $response->withRedirect($this->c->router->pathFor('home'));
    }
    
    public function getSignIn($request, $response)
    {
        return $this->c->view->render($response, 'auth/signin.twig');
    }
    
    public function postSignIn($request, $response)
    {
        $auth = $this->c->auth->attempt(
            $request->getParam('email'),
            $request->getParam('password')
        );

        if (!$auth) {
            // TODO: Add flash messages
            return $response->withRedirect($this->c->router->pathFor('auth.signin'));
        }
        return $response->withRedirect($this->c->router->pathFor('home'));
    }

    public function getSignUp($request, $response)
    {
        return $this->c->view->render($response, 'auth/signup.twig');
    }
    
    public function postSignUp($request, $response)
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
        $this->c->auth->attempt($params['email'], $params['password']);
        return $response->withRedirect($this->c->router->pathFor('todos.index'));
    }

    
}