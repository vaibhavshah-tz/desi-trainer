<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['namespace' => 'Api\v1', 'prefix' => 'v1'], function () {
        // Common Routes
        Route::get('country-list', 'CommonApiController@getCountry')->name('getcountry');
        Route::get('timezone-list', 'CommonApiController@getTimezone')->name('gettimezone');
        Route::get('ticket-type-list', 'CommonApiController@getTicketType')->name('ticket.type');
        Route::get('get-country-timezone-tickettype', 'CommonApiController@getCountryTimezoneTicketType')->name('country.timezone.type');
        Route::get('get-primary-skill', 'CommonApiController@getPrimarySkills')->name('getprimaryskill');

        Route::get('course-category', 'CommonApiController@getCourseCategory')->name('course.category');
        Route::get('primary-skill/{id}', 'CommonApiController@getPrimarySkill')->name('primary.skill');

        // Forget Password
        Route::post('forget-password', 'CommonApiController@forgetPassword')->name('forget.password');
        Route::post('reset-password', 'CommonApiController@resetPassword')->name('reset.password');
        Route::post('verify-email-token', 'CommonApiController@verifyEmailToken')->name('verify.email.token');

        // Customer Login
        Route::post('customer/login', 'CustomerController@login')->name('customer.login');
        Route::post('customer/registration', 'CustomerController@register')->name('customer.register');

        // Trainer Login
        Route::post('trainer/login', 'TrainerController@login')->name('trainer.login');
        Route::post('trainer/registration', 'TrainerController@registration')->name('trainer.registration');

        //SMS Otp
        Route::post('resend-otp', 'CommonApiController@resendOtp')->name('resend-otp');
        Route::post('verify-otp', 'CommonApiController@verifyOtp')->name('verify-otp');

        // Check unique email
        Route::post('check-email', 'CommonApiController@checkEmail')->name('check-email');

        // Check valid phone number
        Route::post('check-phone-number', 'CommonApiController@checkPhoneNumber')->name('check-phone-number');

        // Course list
        Route::get('course-list/{id}', 'CommonApiController@getCourse')->name('course.list');
        Route::get('all-course-list', 'CommonApiController@getAllCourses')->name('all-course.list');


        // Authentic customer routes
        Route::group(['prefix' => 'customer', 'middleware' => ['auth:customer']], function () {
                // Profile
                Route::post('change-password', 'CustomerController@changePassword')->name('customer.change.password');
                Route::post('logout', 'CustomerController@logout')->name('customer.logout');
                Route::match(['put', 'patch'], 'update-profile', 'CustomerController@updateProfile')->name('customer.update.profile');

                // Ticket
                Route::post('tickets/create', 'TicketController@create')->name('ticket.create');
                Route::get('ticket-list', 'TicketController@index')->name('ticket.list');
                Route::get('ticket-details/{id}', 'TicketController@getTicket')->name('ticket.details');

                // Meeting
                Route::get('meeting/ticket-meeting-list', 'MeetingController@ticketMeetingListing')->name('ticket.meeting.list');
                Route::get('meeting/all-meeting-list', 'MeetingController@allMeetingListing')->name('all.meeting.list');
                Route::post('meeting/mark-as-read', 'MeetingController@markAsRead')->name('meeting.mark.as.read');

                // Course
                Route::get('all-courses', 'CourseController@allCourses')->name('all-course.list');
                Route::get('trending-courses', 'CourseController@trendingCourses')->name('trending-course.list');
                Route::get('courses/{id}', 'CourseController@getCourse')->name('course.details');

                // Notification
                Route::get('notifications', 'NotificationController@index')->name('notification.index');
                Route::match(['put', 'patch'], 'notification/update-read/{id}', 'NotificationController@updateStatus')->name('notification.read');
                Route::get('notifications/unread-count', 'NotificationController@checkUnreadNotification')->name('notification.unread.count');

                //Payment
                Route::get('ticket/{id}/payments', 'PaymentController@payments')->name('ticket.payments');
                Route::get('ticket/create-order/{installment_id}', 'PaymentController@createOrder')->name('ticket.create-order');
                Route::post('ticket/{ticket_id}/payment/{installment_id}', 'PaymentController@checkPaymentStatus')->name('ticket.payment.check-status');
                Route::post('payment/mark-as-read', 'PaymentController@markAsRead')->name('ticket.payment.mark-read');

                // Chat
                Route::get('chat', 'ChatController@getMessages')->name('get-message');
                Route::post('chat', 'ChatController@saveMessage')->name('save-message');
                Route::post('chat/mark-as-read', 'ChatController@markAsRead')->name('chat.mark.read');
                Route::get('chat/unread-count', 'ChatController@getUnreadCount')->name('get-chat-count');
        });

        Route::group(['prefix' => 'trainer', 'middleware' => ['auth:trainer']], function () {
                Route::group(['middleware' => 'check.active.user'], function () {
                        // Ticket
                        Route::match(['put', 'patch'], 'mark-ticket-close/{id}', 'TicketController@markTicketClose')->name('ticket.markclose');

                        // Global Request
                        Route::post('mark-interested-ticket', 'GlobalRequestController@markInterestedTickets')->name('mark.interested.ticket');

                        // Meeting
                        Route::post('meeting/create', 'MeetingController@createMeeting')->name('meeting.create');

                        // Profile
                        Route::post('change-password', 'TrainerController@changePassword')->name('trainer.change.password');
                        Route::match(['put', 'patch'], 'update-profile', 'TrainerController@updateProfile')->name('trainer.update.profile');

                        // Proposal
                        Route::post('proposal-action/{id}', 'ProposalController@proposalAction')->name('ticket.proposal.action');

                        // Invoice
                        Route::post('ticket/quote/{quote_id}/create-invoice', 'PaymentController@createInvoice')->name('invoice.create');

                        // Chat
                        Route::post('chat/mark-as-read', 'ChatController@markAsRead')->name('chat.mark.read');

                        //Payment
                        Route::post('payment/mark-as-read', 'PaymentController@markAsRead')->name('ticket.payment.mark-read');
                });
                // Profile
                Route::post('logout', 'TrainerController@logout')->name('trainer.logout');

                // Global Request
                Route::get('global-request-list', 'GlobalRequestController@index')->name('global.request.list');
                Route::get('interested-ticket-list', 'GlobalRequestController@interestedTicketList')->name('interested.ticket.list');

                // Ticket
                Route::get('ticket-list', 'TicketController@index')->name('ticket.list');
                Route::get('ticket-details/{id}', 'TicketController@getTicket')->name('ticket.details');

                // Meeting
                Route::get('meeting/ticket-meeting-list', 'MeetingController@ticketMeetingListing')->name('ticket.meeting.list');
                Route::get('meeting/all-meeting-list', 'MeetingController@allMeetingListing')->name('all.meeting.list');
                Route::post('meeting/mark-as-read', 'MeetingController@markAsRead')->name('meeting.mark.as.read');

                // Proposal
                Route::get('proposals', 'ProposalController@index')->name('ticket.proposal.list');

                // Notification
                Route::get('notifications', 'NotificationController@index')->name('notification.index');
                Route::match(['put', 'patch'], 'notification/update-read/{id}', 'NotificationController@updateStatus')->name('notification.read');
                Route::get('notifications/unread-count', 'NotificationController@checkUnreadNotification')->name('notification.unread.count');

                //Payment
                Route::get('ticket/{id}/payments', 'PaymentController@payments')->name('ticket.payments');

                // Chat
                Route::get('chat', 'ChatController@getMessages')->name('get-message');
                Route::post('chat', 'ChatController@saveMessage')->name('save-message');
                Route::get('chat/unread-count', 'ChatController@getUnreadCount')->name('get-chat-count');

                // bottomNavigationCount
                Route::get('bottom-navigation-count', 'CommonApiController@bottomNavigationCount')->name('bottom-navigation-count');
                Route::match(['put', 'patch'], 'bottom-navigation-count', 'CommonApiController@bottomNavigationReadCount')->name('bottom-navigation-count-read');
        });
});
