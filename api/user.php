<?php
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    error_reporting(E_ALL);
    header("Content-Type: application/json; charset = utf8mb4");

    require_once "../config/db_conn.php";
    require_once "../config/function.php";
    $table_name = "users";
    $method = $_SERVER['REQUEST_METHOD'];

    // show all users
    function index(){
        global $conn, $table_name;
        try{
            $users = [];
            $query = "SELECT * FROM " . $table_name;
            $stmt = $conn->query($query);
            if($stmt->num_rows > 0){
                while($row = $stmt->fetch_assoc()){
                    $users[] = $row;
                }
                apiResponse(200, "Opration Complited!", $users);
            }else{
                apiResponse(404, "Not Found!", null, "No users found");
            }
        }catch(Exception $e){
            apiResponse(500, "Server Error", null, $e->getMessage());
        }
    }

    // show one user
    function show($id){
        global $conn, $table_name;
        try{
            
            $query = "SELECT * FROM " . $table_name . " WHERE id=?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $result = $stmt->get_result();
            // $stmt = $conn->query($query);
            if($result->num_rows == 1){
                $user = $result->fetch_assoc();
                apiResponse(200, "Opration Complited!", $user);
            }
            else{
                apiResponse(404, "Not Found!", null, "No User Found");
            }

        }catch(Exception $e){
            apiResponse(500, "Server Error", null, $e->getMessage());
        }
    }

    // Store a newly created user
    function store(){
        global $conn, $table_name;
        try{
            $input = json_decode(file_get_contents("php://input") , true);
        
            if(!$input) {
                apiResponse(400, "Bad Request!", null, "Invalid JSON input");
                return 0;
            }

            $firstName = $input['firstName'];
            $lastName = $input['lastName'];
            $email = $input['email'];
            $password = $input['password'];
            $role = $input['role'];

            $allowed_roles = ['admin','author','n_user'];

            if (!in_array($role, $allowed_roles)) {
                apiResponse(400, "Bad Request!", null, "Invalid role selected");
                return 0;
            }

            $query = "INSERT INTO " . $table_name . " (`firstName`, `lastName`, `email`, `password`, `role`) 
                    VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('sssss', $firstName, $lastName, $email, $password, $role);
            
            if($stmt->execute()){
                apiResponse(201, "User Has Been Added");
            }
            else{
                apiResponse(400, "Bad Request!", null, $stmt->error);
            }
        }catch(Exception $e){
            apiResponse(500, "Server Error", null, $e->getMessage());
        }
        return 0;
    }

    // Update the specified user
    function update($id){
        global $conn, $table_name;
        try{
            $input = json_decode(file_get_contents("php://input") , true);

            if(!$input) {
                apiResponse(400, "Bad Request!", null, "Invalid JSON input");
                return 0;
            }

            $firstName = $input['firstName'];
            $lastName = $input['lastName'];
            $email = $input['email'];
            $password = $input['password'];
            $role = $input['role'];

            $allowed_roles = ['admin','author','n_user'];

            if (!in_array($role, $allowed_roles)) {
                apiResponse(400, "Bad Request!", null, "Invalid role selected");
                return 0;
            }


            $query = "UPDATE " . $table_name . " SET `firstName` = ?, `lastName` = ?, `email` = ?,
            `password` = ?, `role` = ? WHERE `id` = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('sssssi', $firstName, $lastName, $email, $password, $role, $id);

            if($stmt->execute()){
                apiResponse(200, "Operation Completed!", null, "User Has Been Updated Successfully");
            }
            else{
                apiResponse(500, "SQL Error!", null, $stmt->error);
            }
        }catch(Exception $e){
            apiResponse(500, "Server Error", null, $e->getMessage());
        }
        return 0;
    }

    // Remove the specified article
    function destroy($id){
        global $conn, $table_name;
        try{
            $query = "DELETE FROM " . $table_name . " WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('i', $id);

            if($stmt->execute()){
                if($stmt->affected_rows > 0) {
                    apiResponse(200, "Operation Completed!", null, "The User Has Been Deleted Successfully.");
                }else {
                    apiResponse(404, "Not Found!", null, "User not found or already deleted.");
                }
            }
            else
            {
                apiResponse(500, "SQL Error !", null, $stmt->error);
            }
        }catch(Exception $e){
            apiResponse(500, "Server Error", null, $e->getMessage());
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
            apiResponse(400, "Bad Request!", null, "ID is required for Update");
        }
    } elseif($method === 'DELETE'){
        if(isset($_GET['id'])&& !empty($_GET['id'])){
            destroy($_GET['id']); 
        } else {
            apiResponse(400, "Bad Request!", null, "ID is required for Delete");
        }
    }else {
        apiResponse(405, "Bad Request!", null, "Method Not Allowed");
    }

    