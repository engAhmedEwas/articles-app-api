<?php

require_once 'Model.php';

// app/models/Article.php
class Article extends Model{
    protected $table_name = "db_articales"; // اسم الجدول هنا

    protected $allowed_columns = ['title', 'description', 'content', 'category_id', 'author_id'];
    /**
     * دالة مخصصة للتحقق من صلاحية الكاتب قبل الإضافة
     * لاحظ أنها وظيفة "منطقية" تخص المقالات فقط
     */
    public function canUserPost($author_id) {
        $sql = "SELECT id FROM users WHERE id = :id AND (role = 'author' OR role = 'admin') LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $author_id]);
        return $stmt->fetch() ? true : false;
    }
}