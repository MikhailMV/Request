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
$inner_html = '';
$auth = new auth();

// Авторизация
if (isset($_POST['send'])) {
	if (!$auth->authorization()) {
		$error = $_SESSION['error'];
		unset ($_SESSION['error']);
	}
}

// выход
if (isset($_GET['exit'])) $auth->exit_user();

// Проверка авторизации
if ($auth->check()) 
    $inner_html .= '
        <div class="welcome">
            Добро пожаловать, '.$_SESSION['login_user'].'<br/>
            <a href="?exit" class="btn">Выйти</a>
        </div>
        ';
else {
	// Выводим в случае ошибок 
	if (isset($error)) $inner_html .= $error;

	// Отображение формы регистрации
    $inner_html .= '
        <div class="join">
            <a href="join.php" class="btn reg">Зарегистрироваться</a>
            <form action="" method="post"class="auth-form">
                Логин <input type="text" name="login" value="'.@$_POST['login'].'" class="login"/><br />
                Пароль <input type="password" name="passwd"/><br />
                <input type="submit" value="Войти" name="send" class="btn"/>
            </form>
        </div>
        ';
}
print $inner_html;

// Вывод таблицы заявок
print (get_request_table());

// Выделение последней добавленной заявки
if (isset($_GET['stress'])) shine_last_row(); 

?>

</main>
</body>
</html>
