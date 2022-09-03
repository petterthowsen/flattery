title: Register
---
<form action="<?=url('user/register')?>" method="POST">
<label for="username">Username</label><br>
<input name="username" id="username" type="text" minlength="3" maxlength="16">
<br><br>

<label for="email">Email</label><br>
<input type="email" name="email">
<br><br>

<label for="password">Password</label><br>
<input id="password" type="password" name="password">
<br><br>

<label for="password_confirm">Confirm Password</label><br>
<input type="password" id="password_confirm" name="password_confirm">
<br><br>

<input type="submit" value="Register">
</form>