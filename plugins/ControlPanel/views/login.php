<?=$this->include('layout/header.php')?>

<div id="login-page">
    <?=$this->include('partials/messages.php')?>
    <form action="<?=url('controlpanel/login')?>" method="POST">
        <label for="username">Username</label>
        <br>
        <input type="text" name="username" autofocus required>

        <br><br>

        <label for="password">Password</label>
        <br>
        <input type="password" name="password" required>
        <br>
        <br>
        <input type="submit" value="Log in">
    </form>
</div>

<?=$this->include('layout/footer.php')?>