<?php

// Home
Breadcrumbs::for('home', function ($trail) {
    $trail->push('Home', route('dashboard'));
});

// Sub admin listing
Breadcrumbs::for('sub-admin', function ($trail) {
    $trail->parent('home');
    $trail->push('Sub Admin', route('subadmin.index'));
});
Breadcrumbs::for('sub-admin-create', function ($trail) {
    $trail->parent('sub-admin');
    $trail->push('Create', route('subadmin.index'));
});
Breadcrumbs::for('sub-admin-edit', function ($trail, $data) {
    $trail->parent('sub-admin');
    $trail->push("Edit");
});
Breadcrumbs::for('sub-admin-edit-title', function ($trail, $data) {
    $trail->parent('sub-admin-edit', $data);
    $trail->push($data->full_name);
});
Breadcrumbs::for('sub-admin-view', function ($trail, $data) {
    $trail->parent('sub-admin');
    $trail->push("View");
});
Breadcrumbs::for('sub-admin-view-title', function ($trail, $data) {
    $trail->parent('sub-admin-view', $data);
    $trail->push($data->full_name);
});

Breadcrumbs::for('admin-profile', function ($trail) {
    $trail->parent('home');
    $trail->push('Profile');
});

Breadcrumbs::for('admin-personal-info', function ($trail) {
    $trail->parent('admin-profile');
    $trail->push('Personal Information', route('admin.edit-profile'));
});

Breadcrumbs::for('admin-change-password', function ($trail) {
    $trail->parent('admin-profile');
    $trail->push('Change Password', route('admin.change-password'));
});

Breadcrumbs::for('email-templates', function ($trail) {
    $trail->parent('home');
    $trail->push('Email Template', route('emailtemplate.index'));
});

Breadcrumbs::for('add-email-templates', function ($trail) {
    $trail->parent('email-templates');
    $trail->push('Create', route('emailtemplate.create'));
});

Breadcrumbs::for('edit-email-templates', function ($trail) {
    $trail->parent('email-templates');
    $trail->push('Edit');
});

Breadcrumbs::for('edit-email-templates-title', function ($trail, $data) {
    $trail->parent('edit-email-templates');
    $trail->push($data->name, route('emailtemplate.edit', $data->id));
});

Breadcrumbs::for('view-email-templates', function ($trail) {
    $trail->parent('email-templates');
    $trail->push('View');
});

Breadcrumbs::for('view-email-templates-title', function ($trail, $data) {
    $trail->parent('view-email-templates');
    $trail->push($data->name, route('emailtemplate.view', $data->id));
});

// Course category listing
Breadcrumbs::for('course-category', function ($trail) {
    $trail->parent('home');
    $trail->push('Course Category', route('course-category.index'));
});

// Primary skill listing
Breadcrumbs::for('primary-skill', function ($trail) {
    $trail->parent('home');
    $trail->push('Primary Skill', route('primary.skill.index'));
});

// Trainer listing
Breadcrumbs::for('trainer', function ($trail) {
    $trail->parent('home');
    $trail->push('Trainer', route('trainer.index'));
});
Breadcrumbs::for('trainer-create', function ($trail) {
    $trail->parent('trainer');
    $trail->push('Create', route('trainer.index'));
});
Breadcrumbs::for('trainer-edit', function ($trail, $data) {
    $trail->parent('trainer');
    $trail->push("Edit");
});
Breadcrumbs::for('trainer-edit-title', function ($trail, $data) {
    $trail->parent('trainer-edit', $data);
    $trail->push($data->full_name);
});
Breadcrumbs::for('trainer-view', function ($trail, $data) {
    $trail->parent('trainer');
    $trail->push("View");
});
Breadcrumbs::for('trainer-view-title', function ($trail, $data) {
    $trail->parent('trainer-view', $data);
    $trail->push($data->full_name);
});

Breadcrumbs::for('customers', function ($trail) {
    $trail->parent('home');
    $trail->push('Customers', route('customer.index'));
});

Breadcrumbs::for('add-customers', function ($trail) {
    $trail->parent('customers');
    $trail->push('Create', route('customer.create'));
});

Breadcrumbs::for('edit-customers', function ($trail) {
    $trail->parent('customers');
    $trail->push('Edit');
});

Breadcrumbs::for('edit-customers-title', function ($trail, $data) {
    $trail->parent('edit-customers');
    $trail->push($data->full_name, route('customer.edit', $data->id));
});

Breadcrumbs::for('view-customers', function ($trail) {
    $trail->parent('customers');
    $trail->push('View');
});

Breadcrumbs::for('view-customers-title', function ($trail, $data) {
    $trail->parent('view-customers');
    $trail->push($data->full_name, route('customer.view', $data->id));
});

// Course listing
Breadcrumbs::for('courses', function ($trail) {
    $trail->parent('home');
    $trail->push('Course', route('courses.index'));
});
Breadcrumbs::for('courses-create', function ($trail) {
    $trail->parent('courses');
    $trail->push('Create', route('courses.index'));
});
Breadcrumbs::for('courses-edit', function ($trail, $data) {
    $trail->parent('courses');
    $trail->push("Edit");
});
Breadcrumbs::for('courses-edit-title', function ($trail, $data) {
    $trail->parent('courses-edit', $data);
    $trail->push($data->name);
});
Breadcrumbs::for('courses-view', function ($trail, $data) {
    $trail->parent('courses');
    $trail->push("View");
});
Breadcrumbs::for('courses-view-title', function ($trail, $data) {
    $trail->parent('courses-view', $data);
    $trail->push($data->name);
});

// Ticket listing
Breadcrumbs::for('tickets', function ($trail) {
    $trail->parent('home');
    $trail->push('Tickets', route('tickets'));
});
Breadcrumbs::for('assigned-tickets', function ($trail) {
    $trail->parent('home');
    $trail->push('Assigned Tickets', route('tickets.assigned'));
});
Breadcrumbs::for('tickets-view', function ($trail, $data) {
    $trail->parent('tickets');
    $trail->push("View");
});
Breadcrumbs::for('tickets-view-title', function ($trail, $data) {
    $trail->parent('tickets-view', $data);
    $trail->push($data->ticket_id);
});
Breadcrumbs::for('tickets-edit', function ($trail, $data) {
    $trail->parent('tickets');
    $trail->push("Edit");
});
Breadcrumbs::for('tickets-edit-title', function ($trail, $data) {
    $trail->parent('tickets-edit', $data);
    $trail->push($data->ticket_id);
});

// Meeting
Breadcrumbs::for('meetings', function ($trail, $ticketId) {
    $trail->parent('tickets');
    $trail->push('Meetings', route('meetings', $ticketId));
});
Breadcrumbs::for('meeting-create', function ($trail, $ticketId) {
    $trail->parent('meetings', $ticketId);
    $trail->push('Create', route('tickets.meetings.create', $ticketId));
});

// Proposal
Breadcrumbs::for('proposals', function ($trail, $ticketId) {
    $trail->parent('tickets');
    $trail->push('Proposals', route('proposals', $ticketId));
});
Breadcrumbs::for('proposal-create', function ($trail, $ticketId) {
    $trail->parent('proposals', $ticketId);
    $trail->push('Create', route('tickets.proposals.create', $ticketId));
});

Breadcrumbs::for('proposal-view', function ($trail, $ticketId) {
    $trail->parent('proposals', $ticketId);
    $trail->push('View');
});
Breadcrumbs::for('proposal-view-title', function ($trail, $data) {
    $trail->parent('proposal-view', $data->ticket_id);
    $trail->push($data->name, route('tickets.proposals.view', ['id' => $data->ticket_id, 'proposal_id' => $data->id]));
});
Breadcrumbs::for('proposal-edit', function ($trail, $ticketId) {
    $trail->parent('proposals', $ticketId);
    $trail->push('Edit');
});
Breadcrumbs::for('proposal-edit-title', function ($trail, $data) {
    $trail->parent('proposal-edit', $data->ticket_id);
    $trail->push($data->name, route('tickets.proposals.edit', ['id' => $data->ticket_id, 'proposal_id' => $data->id]));
});

// Notification listing
Breadcrumbs::for('notification', function ($trail) {
    $trail->parent('home');
    $trail->push('Notifications', route('notifications.index'));
});

// Ticket log listing
Breadcrumbs::for('ticket-log', function ($trail) {
    $trail->parent('tickets');
    $trail->push('Activity Logs');
});

// Customer Pricing
Breadcrumbs::for('customer-pricing', function ($trail, $ticketId) {
    $trail->parent('tickets');
    $trail->push('Customer Pricing', route('tickets.customer.pricing', $ticketId));
});

// Invoice
Breadcrumbs::for('invoices', function ($trail, $ticketId) {
    $trail->parent('tickets');
    $trail->push('Invoices', route('tickets.invoices', $ticketId));
});
Breadcrumbs::for('view-invoices', function ($trail, $ticketId) {
    $trail->parent('invoices', $ticketId);
    $trail->push('View');
});
Breadcrumbs::for('view-invoices-title', function ($trail, $data) {
    $ticketId = $data->customerQuote->ticket_id;
    $trail->parent('view-invoices', $ticketId);
    $trail->push($data->name, route('tickets.invoices.view', ['id' => $ticketId, 'invoice_id' => $data->id]));
});

// Interested trainers
Breadcrumbs::for('interested-trainers', function ($trail, $ticketId) {
    $trail->parent('tickets');
    $trail->push('Interested Trainers', route('tickets.interested-trainers', $ticketId));
});

// Chats
Breadcrumbs::for('chats', function ($trail, $ticketId) {
    $trail->parent('tickets');
    $trail->push('Chats');
});
