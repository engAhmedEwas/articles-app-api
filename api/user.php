<?php

    require_once "../config/db_conn.php";
    require_once "../app/controllers/UserController.php";

    $controller = new UserController($conn);

    $method = $_SERVER['REQUEST_METHOD'];
    $id = $_GET['id'] ?? null;

    switch($method) {
        case 'GET':
            if ($id) {
                $controller->show($id);
            } else {
                $controller->index();
            }
            break;

        case 'POST':
            $controller->store();
            break;

        case 'PUT':
            if ($id) {
                $controller->update($id);
            }else{
                $this->jsonResponse(400, "ID is required for update");
            }
            break;

        case 'DELETE':
            if ($id) {
                $controller->destroy($id); 
            }else{
                $this->jsonResponse(400, "ID is required for deleted");
            }
            break;

        default:
            $this->jsonResponse(405, "Method not supported");
            break;
    }