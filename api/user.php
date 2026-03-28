<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    header("Content-Type: application/json; charset = utf8mp4");

    require_once "../config/db_conn.php";
    $table_name = "users";
    $method = $_SERVER['REQUEST_METHOD'];

    // show all users
    function index(){
        global $conn, $table_name;

        $users = [];
        $query = "SELECT * FROM " . $table_name;
        $stmt = $conn->query($query);
        if($stmt->num_rows > 0){
            while($row = $stmt->fetch_assoc()){
                $users[] = $row;
            }
            http_response_code(200);
            echo json_encode($users);
        }else{
            http_response_code(404);
            echo json_encode(["message" => "No User Found"]);
        }
    }

    // show one user
    function show($id){
        global $conn, $table_name;

        $query = "SELECT * FROM " . $table_name . " WHERE id=$id";
        $stmt = $conn->query($query);
        if($stmt->num_rows == 1){
            $user = $stmt->fetch_assoc();
            http_response_code(200);
            echo json_encode($user);
        }
        else{
            http_response_code(404);
            echo json_encode(["message" => "No User Found"]);
        }
    }

    // Store a newly created user
    function store(){
        global $conn, $table_name;

        $input = json_decode(file_get_contents("php://input") , true);
    
        if(!$input) {
            http_response_code(400);
            echo json_encode(["message" => "Invalid JSON input"]);
            return 0;
        }

        $firstName = $input['firstName'];
        $lastName = $input['lastName'];
        $email = $input['email'];
        $password = $input['password'];
        $role = $input['role'];

        $allowed_roles = ['admin','author','n_user'];

        if (!in_array($role, $allowed_roles)) {
            http_response_code(400);
            echo json_encode(["message" => "Invalid role selected"]);
            return 0;
        }

        $query = "INSERT INTO " . $table_name . " (`firstName`, `lastName`, `email`, `password`, `role`) 
                VALUES ('$firstName', '$lastName', '$email', '$password', '$role')";
        if($conn->query($query) === true){
            echo json_encode(["message" => "An User Has Been Added"]);
        }
        else{
            echo json_encode(["message" => "An User Has Not Been Added" . $conn->error]);
        }
        return 0;
    }

    // Update the specified user
    function update($id){
        global $conn, $table_name;

        $input = json_decode(file_get_contents("php://input") , true);

        if(!$input) {
            http_response_code(400);
            echo json_encode(["message" => "Invalid JSON input"]);
            return 0;
        }

        $firstName = $input['firstName'];
        $lastName = $input['lastName'];
        $email = $input['email'];
        $password = $input['password'];
        $role = $input['role'];

        $allowed_roles = ['admin','author','n_user'];

        if (!in_array($role, $allowed_roles)) {
            http_response_code(400);
            echo json_encode(["message" => "Invalid role selected"]);
            return 0;
        }


        $query = "UPDATE " . $table_name . " SET `firstName` = '$firstName', `lastName` = '$lastName', `email` = '$email',
        `password` = '$password', `role` = '$role' WHERE `id` = '$id'";

        if($conn->query($query) === true){
            echo json_encode(["message" => "An User Has Been Updated Successfully"]);
        }
        else{
            echo json_encode(["message" => "SQL Error: " . $conn->error]);
        }
        return 0;
    }

    // Remove the specified article
    function destroy($id){
        global $conn, $table_name;

        $query = "DELETE FROM " . $table_name . " WHERE id = $id";
        if($conn->query($query) === true){
            if($conn->affected_rows > 0) {
                echo json_encode(["message" => "The User Has Been Deleted Successfully."]);
            }else {
                echo json_encode(["message" => "User not found or already deleted."]);
            }
        }
        else
        {
            echo json_encode(["message" => "SQL Error: " . $conn->error]);
        }
    }

    // Routing
    if($method === 'GET'){
        if(isset($_GET['id']) && !empty($_GET['id'])){
            show($_GET['id']);
        } else {
            index();
        }
    } elseif($method === 'POST'){
        store();
    } elseif($method === 'PUT'){
        if(isset($_GET['id']) && !empty($_GET['id'])){
            update($_GET['id']); 
        } else {
            http_response_code(400);
            echo json_encode(["message" => "ID is required for Update"]);
        }
    } elseif($method === 'DELETE'){
        if(isset($_GET['id'])&& !empty($_GET['id'])){
            destroy($_GET['id']); 
        } else {
            http_response_code(400);
            echo json_encode(["message" => "ID is required for Delete"]);
        }
    }else {
        http_response_code(405);
        echo json_encode(["message" => "Method Not Allowed"]);
    }

    