# Articles API (Pure PHP)

تطبيق Backend بسيط لإدارة المقالات باستخدام لغة PHP وقاعدة بيانات MySQL، يطبق مفاهيم الـ RESTful API.

## المميزات (Features)
* **CRUD Operations**: دعم كامل لعمليات (Create, Read, Update, Delete).
* **JSON Response**: التعامل مع البيانات واستلامها بصيغة JSON.
* **Clean Logic**: فصل العمليات بناءً على نوع الـ HTTP Request (GET, POST, PUT, DELETE).

## التقنيات المستخدمة (Technologies)
* **PHP Native**
* **MySQL Database**
* **APIdog** (للإختبار)

## المسارات (Endpoints)
| Method | Endpoint | Description |
| :--- | :--- | :--- |
| GET | `/api/articles.php` | جلب جميع المقالات |
| GET | `/api/articles.php?id={id}` | جلب مقال محدد |
| POST | `/api/articles.php` | إضافة مقال جديد |
| PUT | `/api/articles.php?id={id}` | تحديث مقال بالكامل |
| DELETE | `/api/articles.php?id={id}` | حذف مقال |

## كيفية التشغيل
1. قم باستيراد قاعدة البيانات من ملف الـ SQL المرفق.
2. قم بتعديل بيانات الاتصال في `config/db_conn.php`.