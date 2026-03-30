<?php
// app/core/Validator.php

    class Validator {
        private $errors = [];

        private $db;

        public function __construct($db = null) {
            $this->db = $db;
        }

        /**
         * الدالة الرئيسية التي تحاكي Laravel Validator::make()
         */
        public function make($data, $rules) {
            foreach ($rules as $field => $ruleString) {
                // تحويل النص 'required|email|min:3' إلى مصفوفة ['required', 'email', 'min:3']
                $individualRules = explode('|', $ruleString);
                
                foreach ($individualRules as $rule) {
                    $this->applyRule($field, $data[$field] ?? null, $rule);
                }
            }
            return $this;
        }

        private function applyRule($field, $value, $rule) {
            // التحقق من القواعد البسيطة
            if ($rule === 'required' && (is_null($value) || $value === '')) {
                $this->addError($field, "The $field field is required.");
            }

            if ($rule === 'email' && !empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                $this->addError($field, "The $field must be a valid email address.");
            }

            // التحقق من القواعد التي تحتوي على باراميتر مثل min:3
            if (strpos($rule, 'min:') === 0) {
                $minLimit = (int) explode(':', $rule)[1];
                if (strlen($value) < $minLimit) {
                    $this->addError($field, "The $field must be at least $minLimit characters.");
                }
            }

            if (strpos($rule, 'max:') === 0) {
                $maxLimit = (int) explode(':', $rule)[1];
                if (strlen($value) > $maxLimit) {
                    $this->addError($field, "The $field must not be greater than $maxLimit characters.");
                }
            }

            if ($rule === 'numeric' && !empty($value) && !is_numeric($value)) {
                $this->addError($field, "The $field must be a number.");
            }

            if (strpos($rule, 'unique:') === 0) {
                // 1. استخراج ما بعد النقطتين: "users,email"
                $params = explode(':', $rule)[1]; 
                
                // 2. فصل الجدول عن العمود: [0] => "users", [1] => "email"
                $parts = explode(',', $params);
                $table = $parts[0];
                $column = $parts[1];
                $exceptId = $parts[2] ?? null;

                // 3. الاستعلام من قاعدة البيانات
                $sql = "SELECT COUNT(*) FROM $table WHERE $column = :value";
                if ($exceptId) {
                    $sql .= " AND id != :exceptId";
                }
                $stmt = $this->db->prepare($sql);
                $stmt->execute(['value' => $value]);
                $count = $stmt->fetchColumn();

                // 4. إذا وجدنا نتيجة، فهذا يعني أن القيمة مكررة
                if ($count > 0) {
                    $this->addError($field, "The $field has already been taken.");
                }
            }
            
            // يمكنك إضافة قواعد أخرى هنا مثل 'numeric', 'max', 'url' بنفس المنطق
        }

        private function addError($field, $message) {
            $this->errors[$field][] = $message;
        }

        public function fails() {
            return !empty($this->errors);
        }

        public function errors() {
            return $this->errors;
        }
    }