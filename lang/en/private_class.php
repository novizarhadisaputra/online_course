<?php

return [
    'title' => 'Private Classes',
    'singular' => 'Private Class',
    'plural' => 'Private Classes',
    'navigation_group' => 'Master Data',
    
    'fields' => [
        'name' => 'Name',
        'slug' => 'Slug',
        'short_description' => 'Short Description',
        'description' => 'Description',
        'duration' => 'Duration',
        'duration_units' => 'Duration Units',
        'max_participants' => 'Max Participants',
        'start_date' => 'Start Date',
        'end_date' => 'End Date',
        'status' => 'Status',
        'is_paid' => 'Paid',
        'image' => 'Image',
        'created_at' => 'Created At',
        'updated_at' => 'Updated At',
    ],
    
    'status' => [
        'active' => 'Active',
        'inactive' => 'Inactive',
    ],
    
    'payment' => [
        'paid' => 'Paid',
        'free' => 'Free',
    ],
    
    'sections' => [
        'basic_info' => 'Basic Information',
        'schedule' => 'Schedule',
        'pricing' => 'Pricing',
        'metadata' => 'Metadata',
    ],
    
    'actions' => [
        'view' => 'View',
        'edit' => 'Edit',
        'delete' => 'Delete',
        'create' => 'Create New',
    ],
    
    'messages' => [
        'created' => 'Private class created successfully.',
        'updated' => 'Private class updated successfully.',
        'deleted' => 'Private class deleted successfully.',
    ],
    
    'validation' => [
        'name_required' => 'Name is required.',
        'name_unique' => 'Name has already been taken.',
        'max_participants_min' => 'Max participants must be at least 1.',
        'start_date_required' => 'Start date is required.',
        'end_date_after' => 'End date must be after start date.',
    ],
];