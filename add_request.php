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
// Проверка авторизации пользователя
if (!isset($_SESSION['login_user'])) {
    print "<script type='text/javascript'>
        window.location = 'index.php'
        </script>";
}

if (isset($_POST['add'])) {
    
    // Выдод ошибок, если они есть
    if (!check_add_data($_POST['name'], $_POST['description'], ($_POST['phone']))) {
        print error_print($_SESSION['error']);
        unset ($_SESSION['error']);        
    }
    
    // Добавляем заявку в случае отсутствия ошибок
    else {         
        add_new_request($_POST['name'], $_POST['description'], $_POST['phone']);       
                 
    }    
}
?>  
    <div class="add-page">
        <form action="" method="post" class="add-form" ENCTYPE="multipart/form-data">
    		Название <input type="text" name="name" class="name"/><br />
    		Телефон <input type="text" name="phone" class="phone"/><br />
            Описание <input type="text" name="description" class="descr"/><br />   
            Размер изображения не превышает 512 Кб, пиксели по ширине не более 500, по высоте не более 1500.<br/> 
            Выберите файл для загрузки: 
            <input type="file" name="userfile">
            <input type="submit" name="upload" value="Загрузить"> 
            <input type="submit" value="Добавить" name="add" class="btn add_req"/>        
            <a href="index.php" class="btn back">Назад</a>            
        </form>
    </div>
<?
if (isset ($_POST['upload'])) {
    $uploaddir = 'images/';  
    // Имя изображения  
    $apend=date('YmdHis').rand(100,1000).'.jpg';      
    $uploadfile = "$uploaddir$apend"; 
    
    // Проверка загружаемого изображения
    if(($_FILES['userfile']['type'] == 'image/gif' || 
        $_FILES['userfile']['type'] == 'image/jpeg' || 
        $_FILES['userfile']['type'] == 'image/png') && 
        ($_FILES['userfile']['size'] != 0 and $_FILES['userfile']['size']<=512000)) { 
     
        if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {         
            $size = getimagesize($uploadfile);
            // Проверка ширины и высоты        
            if ($size[0] < 501 && $size[1]<1501) {                  
                print "Файл загружен.";
                $_SESSION['file'] = $uploadfile;
            } 
            else {
                print "Загружаемое изображение превышает допустимые нормы (ширина не более - 500; высота не более 1500)"; 
                unlink($uploadfile);              
            } 
       } 
       else print "Файл не загружен, вернитеcь и попробуйте еще раз";      
    } 
    else print "Размер файла до 512Кб";     
}
?>    
</main>
</body>
</html>