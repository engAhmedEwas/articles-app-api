<?php
// app/models/User.php

require_once 'Model.php';

class User extends Model {
    
    protected $table_name = "users";

    protected $allowed_columns = ['firstName', 'lastName', 'email', 'password', 'role'];

    public function isValidRole($role) {
        $allowed_roles = ['admin', 'author', 'n_user'];
        return in_array($role, $allowed_roles);
    }

    public function emailExists($email, $excludeId = null) {
        $sql = "SELECT id FROM {$this->table_name} WHERE email = :email";
        if ($excludeId) {
            $sql .= " AND id != :id";
        }
        $stmt = $this->db->prepare($sql);
        $params = ['email' => $email];
        if ($excludeId) $params['id'] = $excludeId;
        
        $stmt->execute($params);
        return $stmt->fetch() ? true : false;
    }
}