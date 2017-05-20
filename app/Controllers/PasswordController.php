<?php

namespace App\Controllers;

use PDO;
use PDOException;
use Respect\Validation\Validator as v;

class PasswordController extends Controller
{

    public function getChangePassword($request, $response)
    {
        return $this->c->view->render($response, 'auth/password/change.twig');
    }

    public function postChangePassword($request, $response)
    {
        $validation = $this->c->validator->validate($request, [
            'password_old' => v::noWhitespace()->notEmpty()->matchesPassword($this->c->auth->user()->user_pw),
            'password'     => v::noWhitespace()->notEmpty(),
        ]);

        if ($validation->failed()) {
            return  $response->withRedirect($this->c->router->pathFor('auth.password.change'));
        }

        if ($this->c->auth->changePassword($request->getParam('password'))) {
            $this->c->flash->addMessage('info', 'Password has been changed. ');
            // Success
        } else {
            // Failure
            $this->c->flash->addMessage('error', 'Could not change password. ');
        }
        return $response->withRedirect($this->c->router->pathFor('auth.password.change'));
    }
}
