<?php

// Aside menu
return [
    'items' => [
        // Dashboard
        [
            'title' => 'Basic Info',
            'root' => true,
            'icon' => 'media/svg/icons/Design/Layers.svg', // or can be 'flaticon-home' or any flaticon-*
            'page' => 'tickets.view',
            'new-tab' => false,
        ],
        [
            'title' => 'Chat',
            'root' => true,
            'icon' => 'media/svg/icons/Design/Layers.svg', // or can be 'flaticon-home' or any flaticon-*
            'page' => 'chat.index',
            'new-tab' => false,
        ],
        [
            'title' => 'Customer Pricing',
            'root' => true,
            'icon' => 'media/svg/icons/Design/Layers.svg', // or can be 'flaticon-home' or any flaticon-*
            'page' => 'tickets.customer.pricing',
            'new-tab' => false,
        ],
        [
            'title' => 'Interested Trainers',
            'root' => true,
            'icon' => 'media/svg/icons/Design/Layers.svg', // or can be 'flaticon-home' or any flaticon-*
            'page' => 'tickets.interested-trainers',
            'new-tab' => false,
        ],
        [
            'title' => 'Proposals',
            'root' => true,
            'icon' => 'media/svg/icons/Design/Layers.svg', // or can be 'flaticon-home' or any flaticon-*
            'page' => 'proposals',
            'new-tab' => false,
        ],
        [
            'title' => 'Meetings',
            'root' => true,
            'icon' => 'media/svg/icons/Design/Layers.svg', // or can be 'flaticon-home' or any flaticon-*
            'page' => 'meetings',
            'new-tab' => false,
        ],
        [
            'title' => 'Invoices',
            'root' => true,
            'icon' => 'media/svg/icons/Design/Layers.svg', // or can be 'flaticon-home' or any flaticon-*
            'page' => 'tickets.invoices',
            'new-tab' => false,
        ],
        [
            'title' => 'Activity Log',
            'root' => true,
            'icon' => 'media/svg/icons/Design/Layers.svg', // or can be 'flaticon-home' or any flaticon-*
            'page' => 'tickets.activitylog',
            'new-tab' => false,
        ],

        // [
        //     'title' => 'Themes',
        //     'desc' => '',
        //     'icon' => 'media/svg/icons/Design/Bucket.svg',
        //     'bullet' => 'dot',
        //     'root' => true,
        //     'submenu' => [
        //         [
        //             'title' => 'Light Aside',
        //             'page' => 'layout/themes/aside-light'
        //         ],
        //         [
        //             'title' => 'Dark Header',
        //             'page' => 'layout/themes/header-dark'
        //         ]
        //     ]
        // ],
    ]
];
