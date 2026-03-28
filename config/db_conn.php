<?php
    
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    // Database Configration prams
    $db_host = "localhost";
    $db_name = "dashboardDB";
    $db_user = "ahmed";
    $db_password = "password";
    $db_charSet = "utf8mb4";

    // Create Database connection
    $conn = new mysqli($db_host, $db_user, $db_password, $db_name);
    $conn->set_charset($db_charSet);

    if ($conn->connect_error) {
        http_response_code(500);
        echo json_encode([
            "status" => "error",
            "message" => "Connection Failed: " . $conn->connect_error
        ]);
        die();
    }
