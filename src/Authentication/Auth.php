<?php

namespace ThowsenMedia\Flattery\Authentication;

use ThowsenMedia\Flattery\Data\Data;
use ThowsenMedia\Flattery\HTTP\Session;

class Auth {

    private Session $session;

    private Data $data;
    
    private ?array $user = null;

    public function __construct(Session $session, Data $data)
    {
        $this->session = $session;
        $this->data = $data;

        # initialize
        if ($this->session->has('auth.token')) {
            $token = $this->session->get('auth.token');
            $username = $this->session->get('auth.username');

            if ($this->verifyToken($username, $token)) {
                $this->user = $this->data->get('users', $username);
            }else {
                $this->session->remove('auth.token');
                $this->session->remove('auth.username');
            }
        }
    }

    public function verifyToken(string $username, string $token)
    {
        $username = str_replace('.', '', $username);
        
        if ( ! $this->data->has("users", $username)) return false;
        
        $user = $this->data->get("users", $username);

        return $token === $user['auth_token'];
    }

    public function attempt(string $username, string $password)
    {
        str_replace('.', '', $username);

        $user = $this->data->get("users", $username);
        if (password_verify($password, $user['password'])) {
            return true;
        }

        return false;
    }

    public function login(string $username, string $password)
    {
        $username = str_replace('.', '', $username);

        if ($this->attempt($username, $password)) {
            # create a login token
            $token = md5(uniqid());
            
            # store the token in the database
            $this->data->set("users", "$username.auth_token", $token, true);
            
            # store it in the session
            $this->session->set('auth.token', $token);
            $this->session->set('auth.username', $username);

            $this->user = $this->data->get("users", $username);
            
            return true;
        }

        return false;
    }

    public function logout()
    {
        $this->user = null;
        $this->session->destroy();
    }

    public function isLoggedIn(): bool
    {
        return isset($this->user);
    }

    public function isAdmin(): bool
    {
        if ( ! isset($this->user)) return false;

        return isset($this->user['admin']) ? $this->user['admin'] == true : false;
    }

    public function getUser(): ?array
    {
        return $this->user;
    }

}