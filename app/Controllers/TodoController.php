<?php

namespace App\Controllers;

use App\Models\User;

class TodoController extends Controller
{
    public function index($request, $response)
    {
        return $this->c->view->render($response, 'todos.twig');
    }
}
