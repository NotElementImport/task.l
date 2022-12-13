<?php

return [
    'BasicPermission' => [
        'type' => 2,
        'description' => 'Use a site',
    ],
    'User' => [
        'type' => 1,
        'description' => 'Basic user a site',
        'children' => [
            'BasicPermission',
        ],
    ],
];
