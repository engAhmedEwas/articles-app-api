<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    header("Content-Type: application/json; charset: utf8mp4");

    require_once "../config/db_conn.php";
    $table_name = "users";
    $users = [];

    $method = $_SERVER['REQUEST_METHOD'];
    if($method === 'GET'){
        if(isset($_GET['id'])){
            $userId = $_GET['id'];
            $query_user = "SELECT * FROM " . $table_name . " WHERE id=$userId";
            $stmt = $conn->query($query_user);
            if($stmt->num_rows == 1){
                $user = $stmt->fetch_assoc();
                echo json_encode($user);
                exit;
            }else{
                echo json_encode([
                "message" => "No User Found"
                ], 404);
                exit;
            }
        }
        exit;
    }