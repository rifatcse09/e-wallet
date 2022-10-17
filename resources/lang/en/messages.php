<?php

return [
    'image' => [
        'required' => 'File is required',
        'image' => 'File is is not a valid image',
        'mimes' => 'Invalid image format',
        'max' => 'Image size must be less thant 20MB',
    ],
    'wallet_insufficient' => 'In sufficiant wallet amount ',
    'success_message' => 'Request successful',
    'error_message' => 'Request failed',
    'currency_convert_api_error' => 'Currency Conversion API error',
    'currency_convert_server_error' => 'Currency conversion server not connected',
    'transaction_failed' => 'Transaction Failed !',
    'users' => [
        'deleted_successfully' => 'User deleted successfully',
        'could_not_be_deleted' => 'User could not be deleted',
    ],
    'registration' => [
        'success_message' => 'Registration successful',
        'error_message' => 'Registration failed',
    ],
    'name' => [
        'required' => 'Please enter a username',
        'max' => 'Name must be less than 255 characters',
    ],
    'email' => [
        'required' => 'Please enter your e-mail address',
        'string' => 'E-mail address must be text',
        'email' => 'E-mail address must be valid',
        'max' => 'E-mail address must be less than 255 characters',
        'already_registered' => 'Please log in with your registered email address and password.',
    ],
    'password' => [
        'required' => 'Please enter a password',
        'min' => 'Password must be minimum 8 characters',
        'confirmed' => 'Password and confirm password must be same',
    ],
    'image' => [
        'url' => 'Image must be a valid url'
    ],
    'sender_id' => [
        'required' => 'The sender id field is required.',
        'integer' => 'The sender id must be numeric.',
    ],
    'receiver_id' => [
        'required' => 'The receiver id field is required.',
        'integer' => 'The receiver id must be numeric',
    ],
    'amount' => [
        'required' => 'The amount field is required.',
    ],

];
