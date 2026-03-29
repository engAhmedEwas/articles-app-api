<?php
/**
 * دالة موحدة لإرسال استجابات الـ API بتنسيق JSON
 * 
 * @param int $code كود الحالة (200, 201, 404, 422, etc)
 * @param string $message الرسالة الوصفية
 * @param mixed $data البيانات التي سيتم إرسالها (اختياري)
 * @param mixed $errors تفاصيل الأخطاء في حال وجودها (اختياري)
 */
function apiResponse($code, $message, $data = null, $errors = null) {
    // تحديد حالة النجاح أو الفشل بناءً على الكود
    $status = ($code >= 200 && $code < 300) ? 'success' : 'error';

    // ضبط كود الاستجابة في الـ Header
    http_response_code($code);

    // بناء المصفوفة النهائية
    $response = [
        'status'  => $status,
        'data'    => $data,
        'message' => $message,
    ];

    // إضافة مفتاح error فقط إذا كانت هناك أخطاء فعلاً
    if ($errors !== null) {
        $response['error'] = [
            'code'    => $code,
            'message' => $errors
        ];
    }

    echo json_encode($response);
    exit; // إنهاء السكربت لضمان عدم إرسال أي بيانات أخرى بالخطأ
}