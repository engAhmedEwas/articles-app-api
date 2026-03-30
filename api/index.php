<?php
    // api/index.php

    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    error_reporting(E_ALL);
    header("Content-Type: application/json; charset=utf8mb4");

    // ثم تكمل بقية استدعاءات الـ Routes والـ Database

    require_once '../config/db_conn.php';
    // تحميل ملف المسارات
    $routes = require_once '../routes/api.php';

    // 1. الحصول على المسار المطلوب (مثلاً: articles/update)
    $path = $_GET['path'] ?? ''; 
    $method = $_SERVER['REQUEST_METHOD'];

    // 2. البحث في الخريطة (الـ Routing Logic)
    if (array_key_exists($path, $routes)) {
        $route = $routes[$path];

        // التأكد من أن الـ Method (GET, PUT, etc) صحيحة
        if ($route['method'] === $method) {
            
            $controllerName = $route['controller'];
            $action = $route['action'];

            // استدعاء ملف الـ Controller
            require_once "../app/controllers/{$controllerName}.php";

            // إنشاء كائن من الـ Controller وتنفيذ الدالة
            $controller = new $controllerName($conn);
            
            $id = $_GET['id'] ?? null;
            $controller->$action($id); 

        } else {
            echo json_encode(["message" => "Method Not Allowed"]);
        }
    } else {
        echo json_encode(["message" => "Route Not Found"]);
    }