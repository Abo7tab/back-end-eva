<?php

return [
    'required' => 'حقل :attribute مطلوب.',
    'string'   => 'يجب أن يكون حقل :attribute نصاً.',
    'boolean'  => 'حقل :attribute يجب أن يكون صحيحاً أو خاطئاً.',
    'unique'   => 'قيمة :attribute مُستخدمة من قبل.',
    'max'      => [
        'string' => 'يجب ألا يتجاوز طول :attribute :max حرفاً.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    */

    'attributes' => [
        'phone_number' => 'رقم الواتساب',
        'display_name' => 'الاسم التعريفي',
        'is_active'    => 'حالة النشاط',
    ],
];
