<?php
    // app/requests/BaseRequest.php
    require_once __DIR__ . '/../Core/Validator.php';
    abstract class BaseRequest {
        protected $data;
        protected $db;
        protected $validator;

        public function __construct($db, $data) {
            $this->db = $db;
            $this->data = $data;
            $this->validator = new Validator($this->db);
        }

        abstract public function rules();

        public function validate() {
            return $this->validator->make($this->data, $this->rules());
        }
    }