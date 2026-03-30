# Articles & Users API (Pure PHP) - V3 (The Framework Edition) - With AI

تطبيق Backend متكامل يعتمد على معمارية **MVC**، تم تطويره بـ **PHP Native** ليحاكي تجربة العمل على الأطر البرمجية الاحترافية مثل **Laravel**. يركز هذا الإصدار على "هندسة البرمجيات" وفصل المسؤوليات بشكل كامل.

## 🚀 التطورات الجوهرية في الإصدار (V3)

### 1. معمارية الـ MVC المتقدمة
* **Base Model & Inheritance**: تم بناء كلاس `Model` أساسي يحتوي على دوال SQL ديناميكية (`all`, `find`, `findOrFail`, `store`, `update`, `delete`) لتقليل تكرار الكود (DRY Principle).
* **Base Controller**: نظام موحد لإرسال ردود الـ JSON مع التعامل التلقائي مع الـ HTTP Status Codes.

### 2. محرك التحقق الاحترافي (Validation Engine)
تم بناء نظام **Validator** متكامل يحاكي Laravel تماماً، حيث يدعم القواعد التالية:
* `required`   : التأكد من وجود البيانات.
* `email`      : التحقق من صيغة البريد الإلكتروني.
* `min` & `max`: التحكم في طول النصوص المدخلة.
* `unique`     : التحقق الديناميكي من عدم تكرار البيانات في قاعدة البيانات (مثل البريد الإلكتروني).

### 3. نظام طلبات البيانات (Request Layer)
* فصل منطق التحقق عن الـ Controllers عبر إنشاء كلاسات **Request** مخصصة (مثل `UserStoreRequest`).
* العودة بـ `422 Unprocessable Entity` في حالة فشل التحقق مع مصفوفة أخطاء تفصيلية.

### 4. تحسينات الأمان والأداء
* **PDO Named Parameters**: الانتقال الكامل لـ PDO لضمان أقصى حماية من الـ SQL Injection.
* **Aggregate Functions**: استخدام `COUNT(*)` للتحقق من وجود البيانات بكفاءة عالية وسرعة فائقة.

## 🛠 التقنيات المستخدمة (Technologies)
* **PHP Native (Version 8.5.0)**
* **OOP (Object-Oriented Programming)**
* **MySQL Database (PDO)**
* **Apache Server (Ubuntu)**
* **APIdog** (تم الاختبار بنجاح لكافة المسارات والـ Validation).

## 📡 المسارات المدعومة (Endpoints)

### 1. المستخدمين (Users)
|  Method  |         Endpoint        |            Validation Rules            |
| :------- | :---------------------- | :------------------------------------- |
| `GET`    | `/api/user.php`         | جلب كافة المستخدمين                    |
| `POST`   | `/api/user.php`         | `required`, `email`, `unique`, `min:6` |
| `PUT`    | `/api/user.php?id={id}` | تحديث البيانات مع استثناء ID الحالي    |
| `DELETE` | `/api/user.php?id={id}` | حذف آمن مع التحقق من الوجود            |

### 2. المقالات (Articles)
* تدعم كامل عمليات الـ CRUD باستخدام الـ Dynamic Model الجديد.

## 📝 ملاحظات التطوير القادم (Roadmap)
* [ . ] **Security Phase**: تفعيل `password_hash()` و `password_verify()`.
* [ . ] **Authentication**: بناء نظام JWT (JSON Web Tokens) لتأمين المسارات.
* [ . ] **Authorization**: تحديد الصلاحيات بناءً على الـ Roles (Admin vs User).

---

**ملاحظة للمطورين:** هذا المشروع ليس مجرد تطبيق CRUD، بل هو تمرين هندسي لتطبيق الـ Design Patterns في بيئة PHP خام.

**ملاحظه اخيره:** الهدف كان فهم طريقة عمل ال Freamwork العملاقه وما يحدث behhind the seen لذلك قررت انشاء هذا النموزج ، وايضا كان اختيار فكرة المقاله لانها لا تحتوي على logic كثير او معقد قد يشتتني في البدايه ، اتنمنى ان ينال اعجابكم.