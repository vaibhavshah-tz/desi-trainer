<?php

return [
    'ACTIVE' => 1,
    'INACTIVE' => 0,
    'ACTIVE_LABEL' => 'Active',
    'INACTIVE_LABEL' => 'Inactive',
    'NOT_VERIFIED' => 2,
    'NOT_VERIFIED_LABEL' => 'Not Verified',
    'OTP_EXPIRED_MINUTES' => 10,
    'EMAIL_OTP_EXPIRED_MINUTES' => 10,
    'DEFAULT_MSG' => 'N/A',
    'READ_NOTIFICATION' => 1,
    'UNREAD_NOTIFICATION' => 0,
    'READ' => 1,
    'UNREAD' => 0,
    'CALL_SUPPORT' => 1,
    'SHOW_SPECIFIC_WORD' => 20,
    'FCM_SERVER_KEY' => 'AAAA63EqB2M:APA91bFXtfNDfXzb6_QqsgxkC5yiOpGz7D5fDFhb_G8cLe2LRT9cwpF5riQWIh_L0NYu7ICOPS76d_zXp5AFcSBM1Dx3eY6UUh9Eawp2Ep70VM6zVRHKvY7WoTF7-2nlPlbrb7xw0ace',
    'IMAGE_PATH' => [
        'AVATAR' => 'avatar',
        'CERTIFICATE' => 'certificate',
        'RESUME' => 'resume',
        'DEFAULT_AVATAR' => 'media/users/avatar.jpg',
        'COVER_IMAGE' => 'cover_image',
        'DEFAULT_COVER_IMAGE' => 'media/default/default_course.jpg',
        'INVOICE' => 'invoice',
        'CHAT_IMAGE' => 'chat-image',
    ],
    'ADMIN_ROLE' => [
        'SUPER_ADMIN' => 1,
        'SUB_ADMIN' => 2,
        'SUPER_ADMIN_LABEL' => 'Super Admin',
        'SUB_ADMIN_LABEL' => 'Sub Admin',
    ],
    'CUSTOMER_TYPE' => [
        'INDIVIDUAL' => 1,
        'EMPLOYER' => 2,
        'INDIVIDUAL_LABEL' => 'Individual',
        'EMPLOYER_LABEL' => 'Employer'
    ],
    'GENDER' => [
        'MALE' => 1,
        'FEMALE' => 2,
        'MALE_LABEL' => 'Male',
        'FEMALE_LABEL' => 'Female',
    ],
    'ADMIN_PERMISSION' => [
        'PERMISSION_KEY' => 'admin_permissions_',
        'CACHE_EXPIRED_TIME' => 3600
    ],
    'INTERVIEW_SUPPORT_TICKET' => [
        'START_TIME' => '10:00:00',
        'END_TIME' => '23:00:00',
        'START_TIME_LABEL' => '10 AM',
        'END_TIME_LABEL' => '11 PM',
        'CURRENT_DAY_END_TIME' => '22',
        'CURRENT_DAY_END_TIME_LABEL' => '10 PM',
        'CREATION_LIMIT' => '1', // hours
        'OFFICE_TIMEZONE' => 'Asia/kolkata'
    ],
    'JOB_TRAINING_TICKET' => [
        'CREATION_LIMIT' => '24' // hours
    ],
    'TICKET' => [
        'NEW' => 1,
        'PENDING' => 2,
        // 'UNASSIGNED' => 2,
        'IN_PROGRESS' => 3,
        // 'ASSIGNED' => 4,        
        'INACTIVE' => 4,
        // 'CLOSED' => 5,
        'COMPLETE' => 5,
        'CANCEL' => 6,
        'NEW_LABEL' => 'New',
        'PENDING_LABEL' => 'Pending',
        'UNASSIGNED_LABEL' => 'Unassigned',
        'IN_PROGRESS_LABEL' => 'In Progress',
        'ASSIGNED_LABEL' => 'Assigned',
        'CLOSED_LABEL' => 'Closed',
        'COMPLETE_LABEL' => 'Complete',
        'INACTIVE_LABEL' => 'Inactive',
        'CANCEL_LABEL' => 'Cancel',
        'REQUEST_TICKET_CLOSE' => 1,
        'IS_REQUEST_DEMO' => 1,
        'IS_GLOBAL_YES' => 1,
        'IS_GLOBAL_NO' => 0,
    ],
    'PROPOSAL' => [
        'ACCEPTED' => 1,
        'DENIED' => 2,
        'PENDING' => 0,
        'ACCEPTED_LABEL' => 'Accepted',
        'DENIED_LABEL' => 'Denied',
        'PENDING_LABEL' => 'Pending',
    ],
    'CRON_TIME' => [
        'TICKET_MINUTE' => 30,
        'TICKET_DAY' => 1,
        'CHAT_NOT_RPY_MINUTE' => 15,
        'GLOBAL_REQUEST_NEW_TICKET' => 10,
        'NEW_TICKET' => 5
    ],
    'NOTIFICATION_TYPE' => [
        'USER' => 1,
        'CUSTOMER' => 2,
        'TRAINER' => 3
    ],
    'NOTIFICATION_REDIRECTION_TYPE' => [
        'MEETING' => 1,
        'CUSTOMER' => 2,
        'TRAINER' => 3,
        'TICKET' => 4,
        'PROPOSAL' => 5,
        'CUSTOMER_PRICING' => 6,
        'TRAINER_QUOTE' => 7,
        'CHAT' => 8,
        'PROPOSAL_TRAINER' => 9,
        'CUSTOMER_PAYMENT' => 10,
        'INTERESTED_TICKET' => 11,
        'GLOBAL_REQUEST' => 12,
        'PAYMENT' => 13,
        'ASSIGNED_TICKET' => 14
    ],
    'PAYMENT' => [
        'PAID' => 1,
        'DUE' => 2,
        'PAID_LABEL' => 'Paid',
        'DUE_LABEL' => 'Due',
        'READ_NOTIFICATION' => 1,
        'UNREAD_NOTIFICATION' => 0
    ],
    'MEETING' => [
        'ACTIVE' => 1,
        'INACTIVE' => 0,
        'CANCEL' => 2,
        'CREATE_WITH' => [
            'CUSTOMER' => 1,
            'ASSIGNED_TRAINER' => 2,
            'CUSTOMER_AND_ASSIGNED_TRAINER' => 3,
            'INTERESTED_TRAINER' => 4,
            'CUSTOMER_AND_INTERESTED_TRAINER' => 5,
            'CUSTOMER_LABEL' => 'Customer',
            'ASSIGNED_TRAINER_LABEL' => 'Assigned trainer',
            'CUSTOMER_AND_ASSIGNED_TRAINER_LABEL' => 'Customer and assigned trainer',
            'INTERESTED_TRAINER_LABEL' => 'Interested trainer',
            'CUSTOMER_AND_INTERESTED_TRAINER_LABEL' => 'Customer and interested trainer',
        ]
    ],
    'SENDER_TYPE' => [
        'USER' => 1,
        'CUSTOMER' => 2,
        'TRAINER' => 3,
    ],
    'RAZORPAY_KEY_ID' => env('RAZORPAY_KEY_ID', 'rzp_test_XJDJqL2ucxopta'),
    'RAZORPAY_KEY_SECRET' => env('RAZORPAY_KEY_SECRET', 'Ct94AN0XafckpyzOrrkKIcow'),
    'CHANNEL_NAME' => [
        'ADMIN_TRAINER' => 'admin-trainer-',
        'ADMIN_CUSTOMER' => 'admin-customer-',
        'TRAINER_CUSTOMER' => 'trainer-customer-',
        'CUSTOMER_ONLINE' => 'customer-online-',
        'TRAINER_ONLINE' => 'trainer-online-',
        'ADMIN_ONLINE' => 'admin-online-',
    ],
    'CHAT_MEDIA_TYPE' => [
        'TEXT' => 1,
        'IMAGE' => 2,
        'PDF' => 3,
        'DOC' => 4
    ],
    'MAX_RECURSIVE_CALL' => 100,
    'TICKET_TYPE' => [
        'TRAINING' => 'TR-',
        'JOB_SUPPORT' => 'JS-',
        'INTERVIEW_SUPPORT' => 'IS-',
        'TRAINING_KEY' => 1,
        'JOB_SUPPORT_KEY' => 2,
        'INTERVIEW_SUPPORT_KEY' => 3,
        'JOB_SUPPORT_MIN_INR_PRICE' => 37000,
        'JOB_SUPPORT_MIN_USD_PRICE' => 500,
        'INTERVIEW_SUPPORT_MIN_INR_PRICE' => 7500,
        'INTERVIEW_SUPPORT_MIN_USD_PRICE' => 99
    ],
    'AUTO_REFRESH_INTERVAL' => 60, //seconds
    'MOBILE_COLOR_FLAG' => [
        'RED' => 1,
        'PURPLE' => 2,
        'SKY_BLUE' => 3,
        'GREY' => 4,
        'GREEN' => 5,
        'DARK_BLUE' => 6,
    ],
    'CURRENCY' => [
        'INR' => 'INR',
        'USD' => 'USD'
    ]
];
