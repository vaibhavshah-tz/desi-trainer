<?php

use App\Models\EmailTemplate;
use Illuminate\Database\Seeder;

class EmailTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        EmailTemplate::truncate();
        $emailTemplate = [
            [
                'name' => 'Admin reset password',
                'subject' => 'Reset Password Notification',
                'slug' => 'admin-reset-password',
                'body' => '<p>Hello {FULL_NAME}</p><p>You are receiving this email because we received a password reset request for your account.</p><p><a href="{URL}" class="btn btn-primary" target="_blank">Reset Password</a></p><p>This password reset link will expire in {MINUTE} minutes.</p><p>If you did not request a password reset, no further action is required.</p><p>Regards,</p><p>Desi Traner</p>',
                'keywords' => 'a:3:{i:0;a:2:{s:3:"key";s:9:"FULL_NAME";s:11:"description";s:21:"Full name of the user";}i:1;a:2:{s:3:"key";s:3:"URL";s:11:"description";s:25:"URL to reset the password";}i:2;a:2:{s:3:"key";s:6:"MINUTE";s:11:"description";s:25:"Minute of the expire link";}}'
            ],
            [
                'name' => 'New user',
                'subject' => 'New user registered',
                'slug' => 'new-user',
                'body' => '<p>Hello {FULL_NAME},</p><p><br></p><p>New user is created, Please click on link and login to system {APP_URL}</p><p>Username: {EMAIL}</p><p>Password: {PASSWORD}</p><p><br></p><p>Thanks &amp; Regards</p>',
                'keywords' => 'a:4:{i:0;a:2:{s:3:"key";s:9:"FULL_NAME";s:11:"description";s:21:"Full name of the user";}i:1;a:2:{s:3:"key";s:7:"APP_URL";s:11:"description";s:15:"Application url";}i:2;a:2:{s:3:"key";s:5:"EMAIL";s:11:"description";s:17:"Email of the user";}i:3;a:2:{s:3:"key";s:8:"PASSWORD";s:11:"description";s:20:"Password of the user";}}'
            ]
        ];

        EmailTemplate::insert($emailTemplate);
    }
}
