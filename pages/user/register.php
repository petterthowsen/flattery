title: register
---
<form action="<?=url('user/register')?>" method="POST">
<label for="email">Email</label><br>
<input type="email" name="email">
<br><br>

<label for="password">Password</label><br>
<input type="password" name="password">
<br><br>

<label for="password_confirm">Confirm Password</label><br>
<input type="password" name="password_confirm">
<br><br>

<input type="submit" value="Register">
</form>