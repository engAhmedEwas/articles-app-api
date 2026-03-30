<?php
// app/controllers/Controller.php

class Controller {
    // دالة موحدة لإرسال الردود، بدلاً من الدالة الخارجية
    protected function jsonResponse($code, $message, $data = null, $errors = null) {

        $status = ($code >= 200 && $code < 300) ? 'success' : 'error';

        $response = [
            'status'  => $status,
            'data'    => $data,
            'message' => $message,
        ];

        if ($errors !== null) {
            $response['error'] = [
                'code'    => $code,
                'message' => $errors
            ];
        }

        http_response_code($code);
        echo json_encode($response);
        
        exit;
    }

    // دالة لاستقبال مدخلات الـ JSON وتدقيقها
    protected function getRequestInput() {
        $input = json_decode(file_get_contents("php://input"), true);
        if (!$input) {
            $this->jsonResponse(400, "Invalid JSON input");
        }
        return $input;
    }
}