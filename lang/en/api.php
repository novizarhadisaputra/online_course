<?php

return [
    'messages' => [
        'success' => 'Success',
        'error' => 'An error occurred',
        'not_found' => 'Data not found',
        'unauthorized' => 'Unauthorized access',
        'forbidden' => 'Access forbidden',
        'validation_failed' => 'Validation failed',
        'server_error' => 'Internal server error',
        'created' => 'Data created successfully',
        'updated' => 'Data updated successfully',
        'deleted' => 'Data deleted successfully',
    ],
    
    'private_class' => [
        'list_success' => 'Private classes retrieved successfully',
        'detail_success' => 'Private class details retrieved successfully',
        'not_found' => 'Private class not found',
        'inactive' => 'Private class is inactive',
        'add_to_cart_success' => 'Private class added to cart successfully',
        'cart_updated' => 'Cart updated successfully',
        'invalid_quantity' => 'Invalid quantity',
        'invalid_price' => 'Invalid price',
        'max_participants_exceeded' => 'Number of participants exceeds maximum limit',
    ],
    
    'validation' => [
        'required' => 'The :attribute field is required',
        'string' => 'The :attribute field must be a string',
        'integer' => 'The :attribute field must be an integer',
        'min' => 'The :attribute field must be at least :min characters',
        'max' => 'The :attribute field must not exceed :max characters',
        'uuid' => 'The :attribute field must be a valid UUID',
        'exists' => 'The selected :attribute is invalid',
        'unique' => 'The :attribute has already been taken',
        'email' => 'The :attribute field must be a valid email address',
        'date' => 'The :attribute field must be a valid date',
        'after' => 'The :attribute field must be after :date',
        'before' => 'The :attribute field must be before :date',
        'numeric' => 'The :attribute field must be a number',
        'boolean' => 'The :attribute field must be true or false',
        'array' => 'The :attribute field must be an array',
        'in' => 'The selected :attribute is invalid',
    ],
    
    'pagination' => [
        'showing' => 'Showing :from to :to of :total results',
        'previous' => 'Previous',
        'next' => 'Next',
        'first' => 'First',
        'last' => 'Last',
    ],
    
    'filters' => [
        'all' => 'All',
        'active' => 'Active',
        'inactive' => 'Inactive',
        'paid' => 'Paid',
        'free' => 'Free',
        'search_placeholder' => 'Search...',
        'sort_by' => 'Sort by',
        'sort_order' => 'Sort order',
        'asc' => 'Ascending',
        'desc' => 'Descending',
    ],
    
    'auth' => [
        'login_required' => 'Please login first',
        'invalid_credentials' => 'Invalid email or password',
        'account_disabled' => 'Your account has been disabled',
        'token_expired' => 'Token has expired',
        'token_invalid' => 'Invalid token',
    ],
];