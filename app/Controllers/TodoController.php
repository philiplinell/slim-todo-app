<?php

namespace App\Controllers;

use App\Models\Todo;
use PDO;

class TodoController extends Controller
{
    public function index($request, $response)
    {
        $stmt = $this->c->db->prepare('SELECT * FROM todos WHERE user_id = :user_id');
        $stmt->bindParam(':user_id', $_SESSION['user']);
        try {
            $stmt->execute();
            $stmt->setFetchMode(PDO::FETCH_CLASS, Todo::class);
            $todos = $stmt->fetchAll();
            if ($todos) {
                return $this->c->view->render($response, 'todos.twig', [
                    'todos' => $todos,
                ]);
            }
        } catch (PDOException $e) {
            // Post to logger
            var_dump($e->getMessage());
        }

        return $this->c->view->render($response, 'todos.twig');
    }

    public function done($request, $response, $args)
    {
        $this->markTodoAsDone($args['id']);
        return $response->withRedirect($this->c->router->pathFor('todos.index'));
    }

    public function undone($request, $response, $args)
    {
        $this->markTodoAsDone($args['id'], false);
        return $response->withRedirect($this->c->router->pathFor('todos.index'));
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
}
