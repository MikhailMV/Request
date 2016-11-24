<?php
include_once 'conf.php';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
	<title>Система заявок на ремонт</title>
    <link rel="stylesheet" type="text/css" href="CSS/style.css">
</head>
<body>
<main>

<?php
$reg = new auth();
if (isset($_POST['send'])): ?>
	<? if ($reg->reg($_POST['login'], $_POST['passwd'], $_POST['passwd2'], $_POST['chbox'])): ?> 
		<div class="join">
            <h2>Регистрация успешна.</h2>
            Вы можете войти<br/> 
            <a href="index.php" class="btn after-auth">Авторизоваться</a>
        </div>
	<? else: ?>
        <div class="join">
            <a href="index.php" class="btn auth">Авторизоваться</a><br/>
            <form action="" method="post" class="auth-form">
                Логин <input type="text" name="login" value="<?isset($_POST['login']) ? $_POST['login'] :''?>" class="login"/><br />
                Пароль <input type="password" name="passwd"/><br />
                Пароль <input type="password" name="passwd2"/><br />
                Администратор <input type="checkbox" name="chbox"/><br/>
                <input type="submit" value="Регистрация" name="send" class="btn"/>
            </form>
        </div>
    <? endif ?>
<? else: ?>
    <div class="join">
        <a href="index.php" class="btn auth">Авторизоваться</a><br/>
        <form action="" method="post" class="auth-form">
            Логин <input type="text" name="login" value="<?isset($_POST['login']) ? $_POST['login'] :''?>" class="login"/><br />
            Пароль <input type="password" name="passwd"/><br />
            Пароль <input type="password" name="passwd2"/><br />
            Администратор <input type="checkbox" name="chbox"/><br/>
            <input type="submit" value="Регистрация" name="send" class="btn"/>
        </form>
    </div>
<? endif ?>

</main>
</body>
</html>
