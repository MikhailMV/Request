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

if (isset($_POST['add'])) {
    
    // Выдод ошибок, если они есть
    if (!check_add_data($_POST['name'], $_POST['description'], ($_POST['phone']))) {
        $inner_html.=error_print($_SESSION['error']);
        unset ($_SESSION['error']);        
    }
    
    // Добавляем заявку в случае отсутствия ошибок
    else {         
        add_new_request($_POST['name'], $_POST['description'], $_POST['phone']);
        
        // переходим на главную после добавления заявки
        print "<script type='text/javascript'>
               window.location = 'index.php?stress'
               </script>";         
    }    
}
  
$inner_html .= '
    <div class="add-page">
        <form action="" method="post" class="add-form">
    		Название <input type="text" name="name" class="name"/><br />
    		Телефон <input type="text" name="phone" class="phone"/><br />
            Описание <input type="text" name="description" class="descr"/><br />
            
    		<input type="submit" value="Добавить" name="add" class="btn"/>        
            <a href="index.php" class="btn back">Назад</a>
        </form>
    </div>
'; 

print $inner_html;   
?>

</main>
</body>
</html>