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


<div>
    <table class="js-req-table"><tr class="title">
        <td>Пользователь</td><td>Заявка</td><td>Описание</td><td>Телефон</td><td>Изображение</td></tr>
    
<?php    
    // Выбор информации из базы
    global $dbh;
    $Rname = $_GET['name'];    
    $stmt =  $dbh->prepare('SELECT users.login_user, request.request_name, request.description, request.phone, request.image 
                            FROM users RIGHT JOIN request 
                            ON request.owner_id = users.id_user
                            WHERE request.request_name = ?');
    $stmt->bindParam(1, $Rname);
    $stmt->execute();
    $result = $stmt->FETCH(PDO::FETCH_ASSOC);
?>
        <tr>
            <td><? print $result['login_user']?></td>
            <td><? print $result['request_name']?></td>
            <td><? print $result['description']?></td>
            <td><? print $result['phone']?></td>            
            <td><a href="<? print $result['image']?>"><img src="<? print $result['image']?>" width="200px" height="200px"/></a></td>
        </tr>
    </table>
    <a href="index.php" class="btn">Назад</a>
</div>

</main>
</body>
</html>