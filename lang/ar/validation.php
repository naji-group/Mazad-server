<?php

return [

    /*
    |--------------------------------------------------------------------------
    | أسطر لغة التحقق
    |--------------------------------------------------------------------------
    |
    | تحتوي الأسطر التالية على رسائل الخطأ الافتراضية المستخدمة من قبل
    | فئة المدقق. بعض هذه القواعد تحتوي على عدة إصدارات مثل قواعد الحجم.
    | لا تتردد في تعديل أي من هذه الرسائل بما يتناسب مع متطلباتك.
    |
    */

    'accepted' => 'حقل :attribute يجب أن يتم قبوله.',
    'accepted_if' => 'حقل :attribute يجب أن يتم قبوله عندما يكون :other يساوي :value.',
    'active_url' => 'حقل :attribute لا يُمثّل رابطًا صحيحًا.',
    'after' => 'حقل :attribute يجب أن يكون تاريخًا بعد :date.',
    'after_or_equal' => 'حقل :attribute يجب أن يكون تاريخًا بعد أو يساوي :date.',
    'alpha' => 'حقل :attribute يجب أن يحتوي على حروف فقط.',
    'alpha_dash' => 'حقل :attribute قد يحتوي فقط على حروف، أرقام، شرطات وشرطات سفلية.',
    'alpha_num' => 'حقل :attribute قد يحتوي فقط على حروف وأرقام.',
    'array' => 'حقل :attribute يجب أن يكون مصفوفة.',
    'before' => 'حقل :attribute يجب أن يكون تاريخًا قبل :date.',
    'before_or_equal' => 'حقل :attribute يجب أن يكون تاريخًا قبل أو يساوي :date.',
    'between' => [
        'array' => 'حقل :attribute يجب أن يحتوي على عدد عناصر بين :min و :max.',
        'file' => 'حجم ملف :attribute يجب أن يكون بين :min و :max كيلوبايت.',
        'numeric' => 'قيمة :attribute يجب أن تكون بين :min و :max.',
        'string' => 'عدد أحرف النص :attribute يجب أن يكون بين :min و :max.',
    ],
    'boolean' => 'حقل :attribute يجب أن يكون صحيحًا أو خطأ.',
    'confirmed' => 'تأكيد حقل :attribute غير متطابق.',
    'current_password' => 'كلمة المرور غير صحيحة.',
    'date' => 'حقل :attribute ليس تاريخًا صحيحًا.',
    'date_equals' => 'حقل :attribute يجب أن يكون تاريخًا مطابقًا لـ :date.',
    'date_format' => 'حقل :attribute لا يطابق الشكل :format.',
    'declined' => 'حقل :attribute يجب رفضه.',
    'declined_if' => 'حقل :attribute يجب رفضه عندما يكون :other يساوي :value.',
    'different' => 'يجب أن يكون الحقلان :attribute و :other مختلفين.',
    'digits' => 'حقل :attribute يجب أن يتكون من :digits أرقام.',
    'digits_between' => 'حقل :attribute يجب أن يكون بين :min و :max رقمًا.',
    'dimensions' => 'حقل :attribute يحتوي على أبعاد صورة غير صالحة.',
    'distinct' => 'حقل :attribute يحتوي على قيمة مكررة.',
    'email' => 'حقل :attribute يجب أن يكون بريدًا إلكترونيًا صحيحًا.',
    'ends_with' => 'حقل :attribute يجب أن ينتهي بأحد القيم التالية: :values.',
    'enum' => 'القيمة المحددة في :attribute غير صالحة.',
    'exists' => 'القيمة المحددة في :attribute غير صالحة.',
    'file' => 'حقل :attribute يجب أن يكون ملفًا.',
    'filled' => 'حقل :attribute إجباري.',
    'gt' => [
        'array' => 'حقل :attribute يجب أن يحتوي على أكثر من :value عنصر.',
        'file' => 'حجم ملف :attribute يجب أن يكون أكبر من :value كيلوبايت.',
        'numeric' => 'قيمة :attribute يجب أن تكون أكبر من :value.',
        'string' => 'عدد أحرف النص :attribute يجب أن يكون أكبر من :value.',
    ],
    'gte' => [
        'array' => 'حقل :attribute يجب أن يحتوي على :value عناصر أو أكثر.',
        'file' => 'حجم ملف :attribute يجب أن يكون أكبر من أو يساوي :value كيلوبايت.',
        'numeric' => 'قيمة :attribute يجب أن تكون أكبر من أو تساوي :value.',
        'string' => 'عدد أحرف النص :attribute يجب أن يكون أكبر من أو يساوي :value.',
    ],
    'image' => 'حقل :attribute يجب أن يكون صورة.',
    'in' => 'القيمة المحددة في :attribute غير صالحة.',
    'in_array' => 'حقل :attribute غير موجود في :other.',
    'integer' => 'حقل :attribute يجب أن يكون عددًا صحيحًا.',
    'ip' => 'حقل :attribute يجب أن يكون عنوان IP صحيحًا.',
    'ipv4' => 'حقل :attribute يجب أن يكون عنوان IPv4 صحيحًا.',
    'ipv6' => 'حقل :attribute يجب أن يكون عنوان IPv6 صحيحًا.',
    'json' => 'حقل :attribute يجب أن يكون نصًا من نوع JSON.',
    'lt' => [
        'array' => 'حقل :attribute يجب أن يحتوي على أقل من :value عنصر.',
        'file' => 'حجم ملف :attribute يجب أن يكون أصغر من :value كيلوبايت.',
        'numeric' => 'قيمة :attribute يجب أن تكون أصغر من :value.',
        'string' => 'عدد أحرف النص :attribute يجب أن يكون أصغر من :value.',
    ],
    'lte' => [
        'array' => 'حقل :attribute يجب ألا يحتوي على أكثر من :value عنصر.',
        'file' => 'حجم ملف :attribute يجب أن يكون أصغر من أو يساوي :value كيلوبايت.',
        'numeric' => 'قيمة :attribute يجب أن تكون أصغر من أو تساوي :value.',
        'string' => 'عدد أحرف النص :attribute يجب أن يكون أصغر من أو يساوي :value.',
    ],
    'mac_address' => 'حقل :attribute يجب أن يكون عنوان MAC صحيحًا.',
    'max' => [
        'array' => 'حقل :attribute يجب ألا يحتوي على أكثر من :max عنصر.',
        'file' => 'حجم ملف :attribute يجب ألا يتجاوز :max كيلوبايت.',
        'numeric' => 'قيمة :attribute يجب ألا تكون أكبر من :max.',
        'string' => 'عدد أحرف النص :attribute يجب ألا يتجاوز :max.',
    ],
    'mimes' => 'حقل :attribute يجب أن يكون ملفًا من نوع: :values.',
    'mimetypes' => 'حقل :attribute يجب أن يكون ملفًا من نوع: :values.',
    'min' => [
        'array' => 'حقل :attribute يجب أن يحتوي على الأقل على :min عناصر.',
        'file' => 'حجم ملف :attribute يجب ألا يقل عن :min كيلوبايت.',
        'numeric' => 'قيمة :attribute يجب أن تكون على الأقل :min.',
        'string' => 'عدد أحرف النص :attribute يجب أن يكون على الأقل :min.',
    ],
    'multiple_of' => 'حقل :attribute يجب أن يكون من مضاعفات :value.',
    'not_in' => 'القيمة المحددة في :attribute غير صالحة.',
    'not_regex' => 'صيغة :attribute غير صالحة.',
    'numeric' => 'حقل :attribute يجب أن يكون رقمًا.',
    'password' => 'كلمة المرور غير صحيحة.',
    'present' => 'حقل :attribute يجب أن يكون موجودًا.',
    'prohibited' => 'حقل :attribute محظور.',
    'prohibited_if' => 'حقل :attribute محظور عندما يكون :other يساوي :value.',
    'prohibited_unless' => 'حقل :attribute محظور ما لم يكن :other ضمن :values.',
    'prohibits' => 'حقل :attribute يحظر وجود :other.',
    'regex' => 'صيغة :attribute غير صحيحة.',
    'required' => 'حقل :attribute مطلوب.',
    'required_array_keys' => 'حقل :attribute يجب أن يحتوي على مدخلات لـ: :values.',
    'required_if' => 'حقل :attribute مطلوب عندما يكون :other يساوي :value.',
    'required_unless' => 'حقل :attribute مطلوب ما لم يكن :other ضمن :values.',
    'required_with' => 'حقل :attribute مطلوب عندما تكون :values موجودة.',
    'required_with_all' => 'حقل :attribute مطلوب عندما تكون :values موجودة.',
    'required_without' => 'حقل :attribute مطلوب عندما لا تكون :values موجودة.',
    'required_without_all' => 'حقل :attribute مطلوب عندما لا توجد أي من :values.',
    'same' => 'يجب أن يتطابق حقل :attribute مع حقل :other.',
    'size' => [
        'array' => 'حقل :attribute يجب أن يحتوي على :size عنصرًا.',
        'file' => 'حجم ملف :attribute يجب أن يكون :size كيلوبايت.',
        'numeric' => 'قيمة :attribute يجب أن تكون :size.',
        'string' => 'عدد أحرف النص :attribute يجب أن يكون :size.',
    ],
    'starts_with' => 'حقل :attribute يجب أن يبدأ بأحد القيم التالية: :values.',
    'string' => 'حقل :attribute يجب أن يكون نصًا.',
    'timezone' => 'حقل :attribute يجب أن يكون نطاقًا زمنيًا صحيحًا.',
    'unique' => 'قيمة :attribute مُستخدمة من قبل.',
    'uploaded' => 'فشل في رفع :attribute.',
    'url' => 'صيغة :attribute غير صحيحة.',
    'uuid' => 'حقل :attribute يجب أن يكون UUID صحيحًا.',

    /*
    |--------------------------------------------------------------------------
    | تخصيص أسماء السمات
    |--------------------------------------------------------------------------
    |
    | يمكنك هنا تحديد أسماء أكثر وضوحًا للحقول بدلاً من عرضها كما هي في رسائل
    | الخطأ. مثلاً بدلاً من "email" يمكن إظهار "البريد الإلكتروني".
    |
    */

    'attributes' => [],

];
