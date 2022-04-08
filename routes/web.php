<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Authentication routes
Route::group(['middleware' => 'guest'], function () {
    Route::get('/login', 'Auth\LoginController@showLoginForm')->name('login');
    Route::post('/login', 'Auth\LoginController@login');

    Route::get('/password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
    Route::post('/password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');

    Route::get('/password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
    Route::post('/password/reset', 'Auth\ResetPasswordController@reset')->name('password.update');

    // Call webhooks
    Route::get('/webhook/answer', 'CallController@answer')->name('webhook.answer');
    Route::post('/webhook/event', 'CallController@event')->name('webhook.event');
});

Route::group(['middleware' => 'auth'], function () {
    //admin logout
    Route::post('/logout', 'Auth\LoginController@logout')->name('logout');
    // Route::get('/', 'PagesController@index')->name('dashboard');
    Route::get('/', 'DashboardController@index')->name('dashboard');
    Route::get('/dashboard/get-ticket-list', 'DashboardController@getTicketList')->name('dashboard.getTicketlist');
    Route::get('/dashboard/get-course-list', 'DashboardController@getCourseList')->name('dashboard.getCourseList');
    Route::get('/dashboard/get-invoice-list', 'DashboardController@getInvoiceList')->name('dashboard.getInvoiceList');

    // Sub Admin
    Route::get('/sub-admins', 'SubAdminController@index')->name('subadmin.index')->middleware('permission.check:sub-admin-list');
    Route::any('/sub-admins/get-list', 'SubAdminController@getList')->name('subadmin.getlist');
    Route::get('/sub-admins/create', 'SubAdminController@create')->name('subadmin.create')->middleware('permission.check:create-sub-admin');
    Route::post('/sub-admins/create', 'SubAdminController@store')->name('subadmin.store');
    Route::get('/sub-admins/edit/{id}', 'SubAdminController@edit')->name('subadmin.edit')->middleware('permission.check:edit-sub-admin');
    Route::match(['put', 'patch'], '/sub-admins/edit/{id}', 'SubAdminController@update')->name('subadmin.update');
    Route::any('/sub-admins/delete/{id}', 'SubAdminController@delete')->name('subadmin.delete')->middleware('permission.check:delete-sub-admin');
    Route::get('/sub-admins/view/{id}', 'SubAdminController@view')->name('subadmin.view')->middleware('permission.check:view-sub-admin');
    Route::post('/sub-admins/update-status', 'SubAdminController@updateStatus')->name('subadmin.update.status');

    // Course Category
    Route::get('/course-category', 'CourseCategoryController@index')->name('course-category.index');
    Route::any('/course-category/get-list', 'CourseCategoryController@getList')->name('course.category.getlist');
    Route::post('/course-category/create', 'CourseCategoryController@store')->name('course.category.store');
    Route::get('/course-category/edit/{id}', 'CourseCategoryController@edit')->name('course.category.edit');
    Route::any('/course-category/delete/{id}', 'CourseCategoryController@delete')->name('course.category.delete');

    // Primary skills
    Route::get('/primary-skill', 'PrimarySkillController@index')->name('primary.skill.index');
    Route::any('/primary-skill/get-list', 'PrimarySkillController@getList')->name('primary.skill.getlist');
    Route::post('/primary-skill/create', 'PrimarySkillController@store')->name('primary.skill.store');
    Route::get('/primary-skill/edit/{id}', 'PrimarySkillController@edit')->name('primary.skill.edit');
    Route::any('/primary-skill/delete/{id}', 'PrimarySkillController@delete')->name('primary.skill.delete');
    Route::get('/primary-skill/getCategoryskill', 'PrimarySkillController@getCategorySkill')->name('categorywise.skills');


    // Admin edit profile
    Route::get('/edit-profile', 'AdministratorController@edit')->name('admin.edit-profile');
    Route::post('/edit-profile', 'AdministratorController@update')->name('admin.update-profile');

    //Admin change password
    Route::get('/change-password', 'AdministratorController@changePassword')->name('admin.change-password');
    Route::post('/change-password', 'AdministratorController@updatePassword')->name('admin.update-password');

    // Check unique user email
    Route::post('/check-email', 'AdministratorController@checkEmail')->name('user.check-email');

    // Email template
    Route::get('/email-templates', 'EmailTemplateController@index')->name('emailtemplate.index');
    Route::any('/email-templates/get-list', 'EmailTemplateController@getList')->name('emailtemplate.getlist');
    Route::get('/email-templates/create', 'EmailTemplateController@create')->name('emailtemplate.create');
    Route::post('/email-templates/create', 'EmailTemplateController@store')->name('emailtemplate.store');
    Route::get('/email-templates/edit/{id}', 'EmailTemplateController@edit')->name('emailtemplate.edit');
    Route::match(['put', 'patch'], '/email-templates/update/{id}', 'EmailTemplateController@update')->name('emailtemplate.update');
    Route::get('/email-templates/view/{id}', 'EmailTemplateController@view')->name('emailtemplate.view');
    Route::post('/email-templates/check-name', 'EmailTemplateController@checkName')->name('emailtemplate.check-name');

    // Customer
    Route::get('/customers', 'CustomerController@index')->name('customer.index');
    Route::any('/customers/get-list', 'CustomerController@getList')->name('customer.getlist');
    Route::get('/customers/create', 'CustomerController@create')->name('customer.create');
    Route::post('/customers/create', 'CustomerController@store')->name('customer.store');
    Route::get('/customers/edit/{id}', 'CustomerController@edit')->name('customer.edit');
    Route::match(['put', 'patch'], '/customers/update/{id}', 'CustomerController@update')->name('customer.update');
    Route::get('/customers/view/{id}', 'CustomerController@view')->name('customer.view');
    Route::post('/customers/check-name', 'CustomerController@checkName')->name('customer.check-name');
    Route::any('/customers/delete/{id}', 'CustomerController@delete')->name('customer.delete');
    Route::post('/customers/update-status', 'CustomerController@updateStatus')->name('customer.update.status');
    Route::post('/customers/check-email', 'CustomerController@checkEmail')->name('customer.check-email');
    Route::post('/customers/check-username', 'CustomerController@checkUsername')->name('customer.check-username');

    // Trainer
    Route::get('/trainers', 'TrainerController@index')->name('trainer.index');
    Route::any('/trainers/get-list', 'TrainerController@getList')->name('trainer.getlist');
    Route::get('/trainers/create', 'TrainerController@create')->name('trainer.create');
    Route::post('/trainers/create', 'TrainerController@store')->name('trainer.store');
    Route::get('/trainers/edit/{id}', 'TrainerController@edit')->name('trainer.edit');
    Route::match(['put', 'patch'], '/trainers/edit/{id}', 'TrainerController@update')->name('trainer.update');
    Route::any('/trainers/delete/{id}', 'TrainerController@delete')->name('trainer.delete');
    Route::get('/trainers/view/{id}', 'TrainerController@view')->name('trainer.view');
    Route::post('/trainers/update-status', 'TrainerController@updateStatus')->name('trainer.update.status');

    // Check unique user email
    Route::post('/trainers/check-email', 'TrainerController@checkEmail')->name('trainer.check-email');
    Route::post('/trainers/check-username', 'TrainerController@checkUsername')->name('trainer.check-username');

    // Courses
    Route::get('/courses', 'CoursesController@index')->name('courses.index');
    Route::any('/courses/get-list', 'CoursesController@getList')->name('courses.getlist');
    Route::get('/courses/create', 'CoursesController@create')->name('courses.create');
    Route::post('/courses/create', 'CoursesController@store')->name('courses.store');
    Route::get('/courses/edit/{id}', 'CoursesController@edit')->name('courses.edit');
    Route::match(['put', 'patch'], '/courses/edit/{id}', 'CoursesController@update')->name('courses.update');

    Route::match(['post', 'put', 'patch'], '/courses/details', 'CoursesController@saveDetails')->name('courses.save.details');
    Route::match(['post', 'put', 'patch'], '/courses/key-features', 'CoursesController@saveKeyFeatures')->name('courses.key-features');

    // Course Section
    Route::match(['post', 'put', 'patch'], '/courses/course-curriculum', 'CoursesController@saveCourseCurriculum')->name('courses.curriculum');
    Route::get('/courses/get-section-list', 'CoursesController@getSectionListing')->name('courses.section.listing');
    Route::get('/courses/get-parent-section-list', 'CoursesController@getParentSectionListing')->name('courses.parent.section.listing');
    Route::get('/courses/delete-section', 'CoursesController@deleteSection')->name('courses.delete.section');

    // Course Topics
    Route::get('/courses/get-topic-list', 'CoursesController@getTopicListing')->name('courses.topic.listing');
    Route::get('/courses/delete-topic', 'CoursesController@deleteTopic')->name('courses.delete.topic');

    // Course FAQs
    Route::match(['post', 'put', 'patch'], '/courses/save-faq', 'CoursesController@saveFaq')->name('courses.save.faq');
    Route::get('/courses/get-faq-list', 'CoursesController@getFaqListing')->name('courses.faq.listing');
    Route::get('/courses/get-faq', 'CoursesController@getFaq')->name('courses.getfaq');
    Route::get('/courses/delete-faq', 'CoursesController@deleteFaq')->name('courses.delete.faq');


    Route::any('/courses/delete/{id}', 'CoursesController@delete')->name('courses.delete');
    Route::get('/courses/view/{id}', 'CoursesController@view')->name('courses.view');
    Route::post('/courses/update-status', 'CoursesController@updateStatus')->name('courses.update.status');

    // Get category wise course
    Route::get('/courses/getCategoryCourse', 'CoursesController@getCategoryCourse')->name('categorywise.course');

    // Check unique user email
    Route::post('/courses/check-email', 'CoursesController@checkEmail')->name('courses.check-email');
    Route::post('/courses/check-username', 'CoursesController@checkUsername')->name('courses.check-username');

    Route::post('/check-call-support', 'SubAdminController@checkCallSupport')->name('sub-admin.check-call-support');

    // Ticket
    Route::get('/tickets', 'TicketController@index')->name('tickets');
    Route::get('/assigned-tickets', 'TicketController@assignedTicket')->name('tickets.assigned');
    Route::any('/tickets/get-list', 'TicketController@getList')->name('tickets.getlist');
    Route::get('/tickets/get-assigned-admin/{id}', 'TicketController@getAssignedAdmin')->name('tickets.get-assigned-admin');
    Route::post('/tickets/assign-admin', 'TicketController@assignAdmin')->name('tickets.assign-admin');
    Route::post('/tickets/update-is-global', 'TicketController@updateIsGlobal')->name('tickets.update.isglobal');

    Route::group(['middleware' => 'check.ticket.access'], function () {
        // Ticket
        Route::any('/tickets/delete/{id}', 'TicketController@delete')->name('tickets.delete');
        Route::any('/tickets/edit/{id}', 'TicketController@edit')->name('tickets.edit');
        Route::any('/tickets/view/{id}', 'TicketController@view')->name('tickets.view');
        Route::match(['put', 'patch'], '/tickets/update/{id}', 'TicketController@update')->name('tickets.update');

        // Meeting
        Route::get('/tickets/{id}/meetings', 'MeetingController@index')->name('meetings');
        Route::any('/tickets/{id}/meetings/get-list', 'MeetingController@getList')->name('tickets.meetings.getlist');
        Route::any('/tickets/{id}/meetings/cancel/{meeting_id}', 'MeetingController@cancel')->name('tickets.meetings.cancel')->middleware('check.ticket.status');
        Route::get('/tickets/{id}/meetings/create', 'MeetingController@create')->name('tickets.meetings.create');
        Route::post('/tickets/{id}/meetings/create', 'MeetingController@store')->name('tickets.meetings.store')->middleware('check.ticket.status');

        // Proposal
        Route::get('/tickets/{id}/proposals', 'ProposalController@index')->name('proposals');
        Route::any('/tickets/{id}/proposals/get-list', 'ProposalController@getList')->name('tickets.proposals.getlist');
        Route::any('/tickets/{id}/proposals/get-trainer-list', 'ProposalController@getTrainerList')->name('tickets.proposals.get-trainer-list');
        Route::get('/tickets/{id}/proposals/create', 'ProposalController@create')->name('tickets.proposals.create');
        Route::post('/tickets/{id}/proposals/create', 'ProposalController@store')->name('tickets.proposals.store')->middleware('check.ticket.status');
        Route::get('/tickets/{id}/proposals/edit/{proposal_id}', 'ProposalController@edit')->name('tickets.proposals.edit');
        Route::post('/tickets/{id}/proposals/edit/{proposal_id}', 'ProposalController@update')->name('tickets.proposals.update')->middleware('check.ticket.status');
        Route::get('/tickets/{id}/proposals/view/{proposal_id}', 'ProposalController@view')->name('tickets.proposals.view');
        Route::any('/tickets/{id}/proposals/{proposal_id}/assign-trainer/{trainer_id}', 'ProposalController@assignTrainer')->name('tickets.proposals.assign-trainer')->middleware('check.ticket.status');
        Route::any('/tickets/{id}/proposals/delete/{proposal_id}', 'ProposalController@delete')->name('tickets.proposals.delete')->middleware('check.ticket.status');
        Route::post('/tickets/{id}/proposals/check-quote', 'ProposalController@checkProposalQuote')->name('tickets.proposals.check-quote');

        // Activity log
        Route::get('/tickets/{id}/activity-log', 'TicketLogController@index')->name('tickets.activitylog');

        // Customer pricing
        Route::get('/tickets/{id}/customer-pricing', 'CustomerPricingController@index')->name('tickets.customer.pricing');
        Route::post('/tickets/{id}/customer-pricing', 'CustomerPricingController@store')->name('tickets.customer.pricing.store')->middleware('check.ticket.status');
        Route::get('/tickets/{id}/customer-pricing/{customer_quote_id}/installment/create', 'CustomerPricingController@createInstallment')->name('tickets.customer-pricing.installments.create');
        Route::post('/tickets/{id}/customer-pricing/{customer_quote_id}/installment/create', 'CustomerPricingController@storeInstallment')->name('tickets.customer-pricing.installments.store')->middleware('check.ticket.status');
        Route::get('/tickets/{id}/customer-pricing/{customer_quote_id}/installment/edit/{installment_id}', 'CustomerPricingController@editInstallment')->name('tickets.customer-pricing.installments.edit');
        Route::post('/tickets/{id}/customer-pricing/{customer_quote_id}/installment/edit/{installment_id}', 'CustomerPricingController@updateInstallment')->name('tickets.customer-pricing.installments.update')->middleware('check.ticket.status');
        Route::get('/tickets/{id}/customer-pricing/{customer_quote_id}/installment/view/{installment_id}', 'CustomerPricingController@viewInstallment')->name('tickets.customer-pricing.installments.view');
        Route::any('/tickets/{id}/customer-pricing/{customer_quote_id}/installment/delete/{installment_id}', 'CustomerPricingController@deleteInstallment')->name('tickets.customer-pricing.installments.delete')->middleware('check.ticket.status');
        Route::post('/tickets/{id}/customer-pricing/{customer_quote_id}/installment/check-amount', 'CustomerPricingController@checkInstallmentAmount')->name('tickets.customer-pricing.installments.check-amount');
        Route::post('/tickets/{id}/customer-pricing/check-amount', 'CustomerPricingController@checkQuoteAmount')->name('tickets.customer-pricing.check-amount');

        // Invoices
        Route::get('/tickets/{id}/invoices', 'InvoiceController@index')->name('tickets.invoices');
        Route::get('/tickets/{id}/invoices/get-list', 'InvoiceController@getList')->name('tickets.invoices.getlist');
        Route::get('/tickets/{id}/invoices/view/{invoice_id}', 'InvoiceController@view')->name('tickets.invoices.view');
        Route::get('/tickets/{id}/trainer-invoices/edit/{invoice_id}', 'InvoiceController@editTrainerInvoice')->name('tickets.trainer-invoices.edit');
        Route::post('/tickets/{id}/trainer-invoices/edit/{invoice_id}', 'InvoiceController@updateTrainerInvoice')->name('tickets.trainer-invoices.update');

        // Interested trainers
        Route::get('/tickets/{id}/interested-trainers', 'InterestedTrainerController@index')->name('tickets.interested-trainers');
        Route::any('/tickets/{id}/interested-trainers/get-list', 'InterestedTrainerController@getList')->name('tickets.interested-trainers.getlist');

        // Call
        // Route::get('/user/call/{id}', 'CallController@callInfo')->name('user.call');
    });

    // Check meeting trainer
    Route::post('/meeting/check-trainer', 'MeetingController@checkTrainer')->name('meetings.check-trainer');

    // Notifications
    Route::get('notifications', 'NotificationController@index')->name('notifications.index');
    Route::post('/notifications/read', 'NotificationController@updateRead')->name('notifications.read');

    // Chat
    Route::get('/tickets/{id}/chat/{trainer_id?}', 'ChatController@index')->name('chat.index');
    Route::post('/save-chat-message', 'ChatController@saveMessage')->name('chat.save.message');
    Route::get('/tickets/{id}/chat-html/{trainer_id?}', 'ChatController@chatHtml')->name('chat.chatHtml');
    Route::post('/chat/chat-read', 'ChatController@chatRead')->name('chat.read');


    // Call
    Route::get('/user/call/{id}', 'CallController@callInfo')->name('user.call');
    Route::get('/get-token', 'CallController@getToken')->name('call.get-token');

    Route::post('/check-price-rate', 'CustomerPricingController@checkPriceRate')->name('check-price-rate');
});

// Demo routes
Route::get('/datatables', 'PagesController@datatables');
Route::get('/ktdatatables', 'PagesController@ktDatatables');
Route::get('/select2', 'PagesController@select2');
Route::get('/icons/custom-icons', 'PagesController@customIcons');
Route::get('/icons/flaticon', 'PagesController@flaticon');
Route::get('/icons/fontawesome', 'PagesController@fontawesome');
Route::get('/icons/lineawesome', 'PagesController@lineawesome');
Route::get('/icons/socicons', 'PagesController@socicons');
Route::get('/icons/svg', 'PagesController@svg');

// Quick search dummy route to display html elements in search dropdown (header search)
Route::get('/quick-search', 'PagesController@quickSearch')->name('quick-search');
Route::get('/cache-clear', function () {
    \Artisan::call('cache:clear');
    \Artisan::call('view:clear');
    \Artisan::call('route:clear');
    \Artisan::call('optimize:clear');
    \Artisan::call('config:clear');
    \Artisan::call('config:cache');

    return redirect('/');
});
