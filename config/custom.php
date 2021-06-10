<?php

return [
    'subdomain' => [
        'api' => 'api.ferone-laravel.ru',
        'ferone' => 'back.ferone-laravel.ru',
        'site' => 'suare-laravel.su',
    ],

    'statuses' => [
        'owner' => [
            'name' => 'Владелец',
            'permissions' => [
                'users_owner_add',
                'users_owner_view',
                'users_owner_modify',
                'users_owner_delete',
            ],
        ],
        'head' => [
            'name' => 'Руководитель',
            'permissions' => [
                'users_head_add',
                'users_head_view',
                'users_head_modify',
                'users_head_delete',
            ],
        ],
        'specialist' => [
            'name' => 'Специалист',
            'permissions' => [
                'users_specialist_add',
                'users_specialist_view',
                'users_specialist_modify',
                'users_specialist_delete',
            ],
        ],
        'employee' => [
            'name' => 'Сотрудник',
            'permissions' => [
                'users_employee_add',
                'users_employee_view',
                'users_employee_modify',
                'users_employee_delete',
            ],
        ],
    ],
];
