<?php

use ThowsenMedia\Flattery\Extending\Plugin;

class ControlPanel extends Plugin {

    public function register()
    {
        router()->get('/controlpanel', [$this, 'getControlPanel']);
        router()->get('/controlpanel/login', [$this, 'getLogin']);
        router()->post('/controlpanel/login', [$this, 'postLogin']);
        router()->get('/controlpanel/logout', [$this, 'getLogout']);

        router()->get('/controlpanel/dashboard', [$this, 'getDashboard']);
    }

    public function install()
    {
        
    }

    public function enable()
    {
        
    }

    public function disable()
    {
        
    }

    public function uninstall()
    {

    }

    public function run()
    {
        
    }

    public function getControlPanel()
    {
        if (auth()->isAdmin()) {
            return redirect('controlpanel/dashboard');
        }else if (auth()->isLoggedIn()) {
            return redirect('/');
        }else {
            return redirect('controlpanel/login');
        }
    }

    public function getLogin()
    {
        return $this->view('login.php');
    }

    public function getLogout()
    {
        auth()->logout();
        return redirect('/');
    }

    public function postLogin()
    {
        if (isset($_POST['username']) and isset($_POST['password'])) {
            if (auth()->login($_POST['username'], $_POST['password'])) {
                return redirect('/controlpanel/dashboard')
                    ->with('message', 'Welcome back, ' .$_POST['username']);
            }
        }

        return redirect('/controlpanel/login')
            ->with('message', 'authentication failed.');
    }

    public function getDashboard()
    {
        return $this->view('control-panel.php');
    }

    public function getPlugins()
    {
        return $this->view('plugins.php');
    }

}