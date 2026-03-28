<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    header("Content-Type: application/json; charset=utf8mb4");

    require_once "../config/db_conn.php";
    $table_name = "db_articales";
    
    $method = $_SERVER['REQUEST_METHOD'];
    // GET All Articles 
    function index(){
        global $conn, $table_name, $method;
        $articles = [];
        
        $query = "SELECT * FROM " . $table_name;
        $stmt = $conn->query($query);

        if($stmt->num_rows > 0){
            while($row = $stmt->fetch_assoc()){
                $articles[] = $row;
            }
            http_response_code(200);
            echo json_encode($articles);
        }else{
            http_response_code(404);
            echo json_encode(["message" => "No article Found"]);
        }
        return 0;
    }


    // Show One Article By Id 
    function show($id){
        global $conn, $table_name, $method;
        
        $query_article = "SELECT * FROM " . $table_name . " WHERE id=$id";
        $stmt = $conn->query($query_article);
        if($stmt->num_rows == 1){
            $article = $stmt->fetch_assoc();
            http_response_code(200);
            echo json_encode($article);
        }else{
            http_response_code(404);
            echo json_encode(["message" => "No article Found"]);
        }
        return 0;
    }
    // Store a newly created article
    function store(){
        global $conn, $table_name;
        $input = json_decode(file_get_contents("php://input") , true);
        // 
        if(!$input) {
            http_response_code(400);
            echo json_encode(["message" => "Invalid JSON input"]);
        }

        $title = $input['title'];
        $description = $input['description'];
        $content = $input['content'];
        $author_id = $input['author_id'];
        $category_id = $input['category_id'];

        $query = "INSERT INTO " . $table_name . " (`title`, `description`, `content`, `author_id`, `category_id`) 
                VALUES ('$title', '$description', '$content', '$author_id', '$category_id')";
        if($conn->query($query) === true){
            echo json_encode(["message" => "An article Has Been Added"]);
        }
        else{
            echo json_encode(["message" => "An article Has Not Been Added" . $conn->error]);
        }
        return 0;
    }

    // Update the specified article
    function update($id){
        global $conn, $table_name;

        $input = json_decode(file_get_contents("php://input") , true);

        if(!$input) {
            http_response_code(400);
            echo json_encode(["message" => "Invalid JSON input"]);
        }

        $title = $input['title'];
        $description = $input['description'];
        $content = $input['content'];
        $author_id = $input['author_id'];
        $category_id = $input['category_id'];

        $query = "UPDATE " . $table_name . " SET `title` = '$title', `description` = '$description', `content` = '$content',
        `author_id` = '$author_id', `category_id` = '$category_id' WHERE `id` = '$id'";
        if($conn->query($query) === true){
            echo json_encode(["message" => "An Article Has Been Updated Successfully"]);
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
                echo json_encode(["message" => "The Article Has Been Deleted Successfully."]);
            }else {
                echo json_encode(["message" => "Article not found or already deleted."]);
            }
        }
        else
        {
            echo json_encode(["message" => "SQL Error: " . $conn->error]);
        }
    }

    // --- نظام التوزيع (Simple Router) ---
    if($method === 'GET'){
        if(isset($_GET['id'])){
            show($_GET['id']);
        } else {
            index();
        }
    } elseif($method === 'POST'){
        store();
    } elseif($method === 'PUT'){
        if(isset($_GET['id'])){
            update($_GET['id']); 
        } else {
            http_response_code(400);
            echo json_encode(["message" => "ID is required for Update"]);
        }
    } elseif($method === 'DELETE'){
        if(isset($_GET['id'])){
            destroy($_GET['id']); 
        } else {
            http_response_code(400);
            echo json_encode(["message" => "ID is required for Delete"]);
        }
    } else {
        http_response_code(405);
        echo json_encode(["message" => "Method Not Allowed"]);
    }
    