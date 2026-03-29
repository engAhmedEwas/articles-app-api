<?php
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    error_reporting(E_ALL);
    header("Content-Type: application/json; charset=utf8mb4");

    require_once "../config/db_conn.php";
    require_once "../config/function.php";
    $table_name = "db_articales";
    
    $method = $_SERVER['REQUEST_METHOD'];
    // GET All Articles 
    function index(){
        global $conn, $table_name, $method;
        $articles = [];
        try{
            $query = "SELECT * FROM " . $table_name;
            $stmt = $conn->query($query);

            if($stmt->num_rows > 0){
                while($row = $stmt->fetch_assoc()){
                    $articles[] = $row;
                }
                apiResponse(200, "Opration Complited!", $articles);

            }else{
                apiResponse(404, "Not Found!", null, "Ther Are NO Articles!");
    
            }
        }catch(Exception $e){
            apiResponse(500, "Server Error", null, $e->getMessage());
            
        }

        return 0;
    }


    // Show One Article By Id 
    function show($id){
        global $conn, $table_name, $method;
        try{
            $query_article = "SELECT * FROM " . $table_name . " WHERE id= ?";
            $stmt = $conn->prepare($query_article);
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $result = $stmt->get_result();
            if($result->num_rows == 1){
                $article = $result->fetch_assoc();
                apiResponse(200, "Opriation Compilated!", $article);
            }else{
                apiResponse(404, "Not Found!", null, "The Requested Article Not Found!");
            }

        }catch(Exception $e){
            apiResponse(500, "Server Error", null, $e->getMessage());
        }
        return 0;
    }
    // Store a newly created article
    function store(){
        global $conn, $table_name;

        $input = json_decode(file_get_contents("php://input") , true);
        try {    
            if(!$input) {
                apiResponse(400, "Bad Request", null, "In Valid JSON input");
                return 0;
            }

            $errors = [];
            if(!empty($errors)){
                apiResponse(422, "Unporcessable Content!", null, $errors);
                return 0;
            }

            $title = $input['title'];
            $description = $input['description'];
            $content = $input['content'];
            $author_id = $input['author_id'];
            $category_id = $input['category_id'];

            // استعلام التحقق باستخدام Prepared Statement
            $query_author = "SELECT `id`, `role` FROM `users` WHERE `id` = ? 
                            AND (`role` = 'author' OR `role` = 'admin')";
            
            $stmt_user = $conn->prepare($query_author);
            $stmt_user->bind_param('i', $author_id);
            $stmt_user->execute();
            
            // جلب النتيجة للتحقق من وجود المستخدم
            $author_result = $stmt_user->get_result();

            if($author_result->num_rows === 0){
                apiResponse(403, "Forbidden", null, "User not found or unauthorized to post");
                return 0;
            }

            $query = "INSERT INTO " . $table_name . " (`title`, `description`, `content`, `author_id`, `category_id`) 
            VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('sssii', $title, $description, $content, $author_id, $category_id);

            if ($stmt->execute()) {
                
                $new_id = $conn->insert_id;

                $created_data = [
                    'id' => $new_id,
                    'title' => $title,
                    'description' => $description,
                    'content' => $content,
                    'author_id' => $author_id,
                    'category_id' => $category_id,
                    'created_at' => date("Y-m-d H:i:s") 
                ];

                // 4. إرسال الاستجابة الناجحة مع البيانات
                apiResponse(201, "Created Successfully", $created_data);

            } else {
                apiResponse(500, "Database Error", null, $stmt->error);
            }
        }catch(Exception $e){
            apiResponse(500, "Server Error", null, $e->getMessage());
        }
        return 0;
    }

    // Update the specified article
    function update($id){
        global $conn, $table_name;

        $input = json_decode(file_get_contents("php://input") , true);
        try{
            if(!$input) {
                apiResponse(400, "Bad Request", null, "Invalid JSON input");
            }

            $title = $input['title'];
            $description = $input['description'];
            $content = $input['content'];
            $author_id = $input['author_id'];
            $category_id = $input['category_id'];

            $query = "UPDATE " . $table_name . " SET `title` = ?, `description` = ?, `content` = ?,
            `author_id` = ?, `category_id` = ? WHERE `id` = ?";

            $stmt = $conn->prepare($query);
            $stmt->bind_param('sssiii', $title, $description, $content, $author_id, $category_id, $id);

            if($stmt->execute()){
                
                $created_data = [
                    'id' => $id,
                    'title' => $title,
                    'description' => $description,
                    'content' => $content,
                    'author_id' => $author_id,
                    'category_id' => $category_id,
                    'created_at' => date("Y-m-d H:i:s") 
                ];

                apiResponse(200, "Updated Successfully", $created_data);
            } else {
                apiResponse(500, "Database Error", null, $stmt->error);
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
                    apiResponse(200, "Deleted Successfully");
                }else {
                    apiResponse(404, "not found", null, "Article not found or already deleted.");
                }
            }else{
                apiResponse(500, "Server Error", null, $stmt->error);
            }
        }catch(Exception $e){
            apiResponse(500, "Server Error", null, $e->getMessage());
        }

    }


    // --- نظام التوزيع (Simple Router) ---
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
        if(isset($_GET['id']) && !empty($_GET['id'])){
            destroy($_GET['id']); 
        } else {
            apiResponse(400, "Bad Request!", null, "ID is required for Delete");
        }
    } else {
        apiResponse(405, "Bad Request!", null, "Method Not Allowed");
    }
    