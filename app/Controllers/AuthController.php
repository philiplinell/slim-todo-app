<?php

namespace App\Controllers;

use App\Models\User;
use PDO;
use PDOException;
use Respect\Validation\Validator as v;
use App\Auth\Auth;

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
        $stmt = $this->c->db->prepare('SELECT last_login from users where user_id = :user_id');
        $stmt->execute([
            ':user_id' => $_SESSION['user']
        ]);
        $stmt->setFetchMode(PDO::FETCH_NUM);
        $lastLogin = new \DateTime($stmt->fetch()[0]);
        $_SESSION['last_login'] = $lastLogin;

        $today = new \DateTime('today');
        $interval = $lastLogin->diff($today);

        if ($interval->format('%a') != 0) {
            $stmt = $this->c->db->prepare('UPDATE users SET last_login = :last_login ' .
                                        'WHERE user_id = :user_id');
            $stmt->execute([
                ':last_login' => $today->format('y-m-d'),
                ':user_id' => $_SESSION['user']
            ]);
        }

        $this->c->flash->addMessage('info', 'Welcome back!');
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
        $stmt = $this->c->db->prepare("INSERT INTO users
                                        (user_pw, user_name, user_email, last_login)
                                        VALUES (:user_pw, :user_name, :user_email, :last_login) ");

        $d = new \DateTime();
        $userPassword = password_hash($params['password'], Auth::$currentHashAlgorithm, Auth::$currentHashOptions);

        $stmt->bindParam(':user_pw', $userPassword);
        $stmt->bindParam(':user_name', $params['username']);
        $stmt->bindParam(':user_email', $params['email']);
        $stmt->bindParam(':last_login', $d->format('y-m-d'));
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