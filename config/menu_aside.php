<?php
// Aside menu
return [
    'items' => [
        // Dashboard
        [
            'title' => 'Dashboard',
            'root' => true,
            'icon' => 'media/svg/icons/Design/Layers.svg', // or can be 'flaticon-home' or any flaticon-*
            'page' => 'dashboard',
            'new-tab' => false,
        ],
        // Sub Admin
        [
            'title' => 'Sub Admin',
            'root' => true,
            'icon' => 'media/svg/icons/Communication/Shield-user.svg', // or can be 'flaticon-home' or any flaticon-*
            'page' => 'subadmin.index',
            'new-tab' => false,
            'permissions' => 'sub-admin-list'
        ],
        [
            'title' => 'Customer',
            'root' => true,
            'icon' => 'media/svg/icons/General/User.svg', // or can be 'flaticon-home' or any flaticon-*
            'page' => 'customer.index',
            'new-tab' => false,
        ],
        [
            'title' => 'Trainer',
            'root' => true,
            'icon' => 'media/svg/icons/Layout/Layout-4-blocks.svg', // or can be 'flaticon-home' or any flaticon-*
            'page' => 'trainer.index',
            'new-tab' => false,
        ],
        [
            'title' => 'Course Category',
            'root' => true,
            'icon' => 'media/svg/icons/Text/Bullet-list.svg', // or can be 'flaticon-home' or any flaticon-*
            'page' => 'course-category.index',
            'new-tab' => false,
        ],
        [
            'title' => 'Primary Skill',
            'root' => true,
            'icon' => 'media/svg/icons/Home/Bulb1.svg', // or can be 'flaticon-home' or any flaticon-*
            'page' => 'primary.skill.index',
            'new-tab' => false,
        ],
        [
            'title' => 'Course',
            'root' => true,
            'icon' => 'media/svg/icons/Home/Book-open.svg', // or can be 'flaticon-home' or any flaticon-*
            'page' => 'courses.index',
            'new-tab' => false,
        ],
        [
            'title' => 'Ticket Management',
            'desc' => '',
            'icon' => 'media/svg/icons/Design/Layers.svg',
            'bullet' => 'dot',
            'root' => true,
            'submenu' => [
                [
                    'title' => 'All Tickets',
                    'page' => 'tickets'
                ],
                [
                    'title' => 'Assigned Tickets',
                    'page' => 'tickets.assigned'
                ]
            ]
        ],
        [
            'title' => 'Notifications',
            'root' => true,
            'icon' => 'media/svg/icons/Home/Bulb1.svg', // or can be 'flaticon-home' or any flaticon-*
            'page' => 'notifications.index',
            'new-tab' => false,
        ],
    ]
];
