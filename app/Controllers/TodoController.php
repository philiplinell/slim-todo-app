<?php

namespace App\Controllers;

use App\Models\Todo;
use PDO;

class TodoController extends Controller
{

    private $todoPlaceholders = [
        'Buy secret vulcano island',
        'Hollow out vulcano',
        'Buy furry white cat',
        'Sing in the shower',
        'Watch Guardians of the Galaxy 3',
        'Build spaceship',
        'Buy supercomputer'
    ];

    public function index($request, $response)
    {
        $stmt = $this->c->db->prepare('SELECT * FROM todos WHERE user_id = :user_id');
        $stmt->bindParam(':user_id', $_SESSION['user']);
        try {
            $stmt->execute();
            $stmt->setFetchMode(PDO::FETCH_CLASS, Todo::class);
            $todos = $stmt->fetchAll();
            if ($todos) {
                $doneTodos = count(array_filter($todos, function($todo) {
                    return $todo->todo_done;
                }));
            }

            return $this->c->view->render($response, 'todos.twig', [
                'todos'        => $todos,
                'done_todos'   => $doneTodos ?? '0',
                'todo_message' => $this->getMessage()
            ]);
        } catch (PDOException $e) {
            // Post to logger
            var_dump($e->getMessage());
        }

        return $this->c->view->render($response, 'todos.twig');
    }

    public function done($request, $response, $args)
    {
        if (empty($args)) {
            // POST request
            $this->markTodoAsDone($request->getParam('todo_id'));
            return $response->withStatus(200);
        } else {
            $this->markTodoAsDone($args['id']);
            return $response->withRedirect($this->c->router->pathFor('todos.index'));
        }
    }

    public function undone($request, $response, $args)
    {
        if (empty($args)) {
            // POST request
            $this->markTodoAsDone($request->getParam('todo_id'), false);
            return $response->withStatus(200);
        } else {
            $this->markTodoAsDone($args['id'], false);
            return $response->withRedirect($this->c->router->pathFor('todos.index'));
        }
    }

    private function markTodoAsDone(int $id, bool $status = true) {
        $stmt = $this->c->db->prepare('UPDATE todos SET todo_done = :status WHERE todo_id = :id AND user_id = :user_id');
        $statusAsInt = $status ? 1 : 0;
        $stmt->execute([
            ':status' => $statusAsInt,
            ':id'     => $id,
            ':user_id' => $_SESSION['user']
        ]);
    }

    public function create($request, $response)
    {
        // Add flash message
        if (empty($request->getParam('description'))) {
            return $response->withRedirect($this->c->router->pathFor('todos.index'));
        }

        $stmt = $this->c->db->prepare('INSERT INTO todos (todo_done,' .
                                      ' todo_description, todo_done_at, user_id)' .
                                      ' VALUES (0, :description, :done_at, :user_id)');
        $d = new \DateTime();
        $stmt->execute([
            ':description' => $request->getParam('description'),
            ':done_at'     => $d->format('d-m-y'),
            ':user_id'     => $_SESSION['user']
        ]);
        return $response->withRedirect($this->c->router->pathFor('todos.index'));
    }

    private function getMessage(): string
    {
        return $this->todoPlaceholders[rand(0, sizeof($this->todoPlaceholders)-1)];
    }

}
