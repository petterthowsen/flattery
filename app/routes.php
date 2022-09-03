<?php

router()->post('user/register', function() {
    $input = input('username', 'email', 'password', 'password_confirm');
    
    $validator = validator([
        'username' => 'required|alpha-numeric|min:3',
        'email' => 'required',
        'password' => 'required|min:12',
        'password_confirm' => 'required|min:12'
    ]);
    
    $errors = [];

    if ( ! $validator->passes($input)) {
        $errors = $validator->getErrors(false);
    }else {
        dump($input);
        if ($input['password'] !== $input['password_confirm']) {
            $errors[] = 'Passwords do not match.';
        }

        if (data()->has('users', $input['username'])) {
            $errors[] = 'That username is unavailable.';
        }
    }

    if(count($errors) > 0) {
        return redirect()->back()->with('errors', $errors);
    }else {
        $user = [
            'email' => $input['email'],
            'password' => password_hash($input['password']),
        ];

        return redirect('/')->with('message', 'Your account has been created.');
    }
    
});