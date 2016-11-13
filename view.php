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
$table = '';

// Вывод полной информации о заявке
$table .= '
    <div>
        <table class="js-req-table"><tr class="title">
            <td>Пользователь</td>
            <td>Заявка</td>
            <td>Описание</td>
            <td>Телефон</td></tr>
    ';
    
    // Выбор информации из базы
    global $dbh;
    $Rname = str_replace("'", "", ($dbh->quote($_GET['name'])));    
    $stmt =  $dbh->prepare('SELECT users.login_user, request.request_name, request.description, request.phone
                            FROM users RIGHT JOIN request 
                            ON request.owner_id = users.id_user
                            WHERE request.request_name = ?');
    $stmt->bindParam(1, $Rname);
    $stmt->execute();
    $result = $stmt->FETCH(PDO::FETCH_ASSOC);
    
    $table .= '
        <tr>
        <td>'.$result['login_user']  .'</td>
        <td>'.$result['request_name'].'</td>
        <td>'.$result['description'] .'</td>
        <td>'.$result['phone']       .'</td></tr></table>
    ';
print $table;
?>

<a href="index.php" class="btn">Назад</a>
</div>

</main>
</body>
</html>