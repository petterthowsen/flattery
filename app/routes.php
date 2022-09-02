<?php

router()->post('user/register', function() {
    $input = input('email', 'password', 'password_confirm');
    
    $validator = validator([
        'email' => 'required',
        'password' => 'required|min:12',
        'password_confirm' => 'required'
    ]);

    

    return 'you submitted';
});