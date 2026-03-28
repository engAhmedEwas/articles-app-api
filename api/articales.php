<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    header("Content-Type: application/json; charset=utf8mb4");

    require_once "../config/db_conn.php";
    $table_name = "db_articles";
    $articles = [];

    $method = $_SERVER['REQUEST_METHOD'];
    if($method === 'GET'){
        if(isset($_GET['id'])){
            $articleId = $_GET['id'];
            $query_article = "SELECT * FROM " . $table_name . " WHERE id=$articleId";
            $stmt = $conn->query($query_article);
            if($stmt->num_rows == 1){
                $article = $stmt->fetch_assoc();
                echo json_encode($article);
                exit;
            }else{
                echo json_encode([
                "message" => "No article Found"
                ], 404);
                exit;
            }
        }
        
        $query = "SELECT * FROM " . $table_name;
        $stmt = $conn->query($query);

        if($stmt->num_rows > 0){
            while($row = $stmt->fetch_assoc()){
                $articles[] = $row;
            }
            echo json_encode($articles);
        }else{
            echo json_encode(["message" => "No article Found"]);
        }
        exit;
    }

    // 
    if($method === "POST"){
        $input = json_decode(file_get_contents("php://input") , true);

        if(!$input) {
            http_response_code(400);
            echo json_encode(["message" => "Invalid JSON input"]);
            exit;
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
        exit;
    }

    // 
    


    if($method === "PUT"){
        if(isset($_GET['id'])){
            $articleId = $_GET['id'];
            $input = json_decode(file_get_contents("php://input") , true);

            if(!$input) {
                http_response_code(400);
                echo json_encode(["message" => "Invalid JSON input"]);
                exit;
            }

            $title = $input['title'];
            $description = $input['description'];
            $content = $input['content'];
            $author_id = $input['author_id'];
            $category_id = $input['category_id'];

            $query = "UPDATE " . $table_name . " SET `title` = '$title', `description` = '$description', `content` = '$content',
            `author_id` = '$author_id', `category_id` = '$category_id' WHERE `id` = '$articleId'";

            if($conn->query($query) === true){
                echo json_encode(["message" => "An Article Has Been Updated Successfully"]);
            }
            else{
                echo json_encode(["message" => "SQL Error: " . $conn->error]);
            }
        }else{
            echo json_encode(["message" => "Missing Article ID in URL"]);
        }
        exit;
    }
        
        

    // 
    if($method === "DELETE"){
        if(isset($_GET['id'])){
            $articleId = $_GET['id'];
            $query = "DELETE FROM " . $table_name . " WHERE id = $articleId";
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
        else
        {
            echo json_encode(["message" => "ID Not Provided"]);
        }
        exit;
    }