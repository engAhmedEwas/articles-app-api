<?php
// app/controllers/UserController.php

    require_once 'Controller.php';
    require_once __DIR__ . '/../models/User.php';
    require_once __DIR__ . '/../requests/UserStoreRequest.php';

    class UserController extends Controller {
        private $userModel;
        private $db;

        public function __construct($db) {
            $this->db = $db;
            $this->userModel = new User($db);
        }

        // 1. عرض جميع المستخدمين (Index)
        public function index() {
            $users = $this->userModel->all();
            
            if ($users) {
                $this->jsonResponse(200, "Operation Completed!", $users);
            } else {
                $this->jsonResponse(404, "Not Found!", null, "No users found");
            }
        }

        // 2. عرض مستخدم واحد (Show)
        public function show($id) {
            $user = $this->userModel->findOrFail($id);
            
            if ($user) {
                $this->jsonResponse(200, "Operation Completed!", $user);
            } else {
                $this->jsonResponse(404, "Not Found!", null, "No User Found");
            }
        }

        // 3. إضافة مستخدم جديد (Store)
        public function store() {
            $input = $this->getRequestInput();

            // 1. استخدام الـ Request المخصص
            $request = new UserStoreRequest($this->db, $input);
            $validation = $request->validate();

            if ($validation->fails()) {
                return $this->jsonResponse(422, "Validation Error", $validation->errors());
            }

            if ($this->userModel->create($input)) {
                $this->jsonResponse(201, "User Has Been Added Successfully", $input);
            } else {
                $this->jsonResponse(500, "Server Error", null, "Failed to create user");
            }
        }

        // 4. تحديث بيانات مستخدم (Update)
        public function update($id) {
            // التأكد من وجود المستخدم أولاً
            if (!$this->userModel->findOrFail($id)) {
                return $this->jsonResponse(404, "Not Found!", null, "User not found");
            }

            $input = $this->getRequestInput();

            if ($this->userModel->update($id, $input)) {
                $this->jsonResponse(200, "Operation Completed!", ["id" => $id, "changes" => $input]);
            } else {
                $this->jsonResponse(500, "SQL Error!", null, "Failed to update user");
            }
        }

        // 5. حذف مستخدم (Destroy)
        public function destroy($id) {
            // التحقق من الوجود قبل الحذف
            if (!$this->userModel->findOrFail($id)) {
                return $this->jsonResponse(404, "Not Found!", null, "User not found or already deleted.");
            }

            if ($this->userModel->delete($id)) {
                $this->jsonResponse(200, "The User Has Been Deleted Successfully.");
            } else {
                $this->jsonResponse(500, "SQL Error!", null, "Failed to delete user");
            }
        }
    }