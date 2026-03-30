<?php
    // app/requests/UserStoreRequest.php
    require_once 'BaseRequest.php';
    class UserStoreRequest extends BaseRequest {
        public function rules() {
            return [
                'firstName'   => 'required|min:2|max:50',
                'email'       => 'required|email|unique:users,email',
                'password'    => 'required|min:6',
                'role'        => 'required'
            ];
        }
    }