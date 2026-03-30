<?php
    // routes/api.php

    // نعيد مصفوفة تحتوي على المسار، الطريقة، والـ Controller والدالة
    return [
        'articles/update' => [
            'method'     => 'PUT',
            'controller' => 'ArticleController',
            'action'     => 'updateArticle'
        ],
        // يمكنك إضافة مسارات أخرى هنا بسهولة
    ];