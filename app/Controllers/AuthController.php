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
        $this->c->flash->addMessage('info', 'You have been logged out. ');
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
            $this->c->flash->addMessage('error', 'Could not sign you in with those details. ');
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
            return $response->withRedirect($this->c->router->pathFor('auth.signup'));
        }
        $params = $request->getParams();
        $stmt = $this->c->db->prepare("INSERT INTO users (user_pw, user_name, user_email) VALUES (:user_pw, :user_name, :user_email) ");

        $userPassword = password_hash($params['password'], PASSWORD_DEFAULT);
        $stmt->bindParam(':user_pw', $userPassword);
        $stmt->bindParam(':user_name', $params['username']);
        $stmt->bindParam(':user_email', $params['email']);
        try {
            $stmt->execute();
        } catch (PDOException $e) {
            $this->c->flash->addMessage('error', 'Could not create user. ');
            return $response->withRedirect($this->c->router->pathFor('auth.signup'));
        }

        // Sign in user
        $this->c->auth->attempt($params['email'], $params['password']);

        $this->c->flash->addMessage('info', 'You have been signed up!');
        
        return $response->withRedirect($this->c->router->pathFor('todos.index'));
    }

    
}