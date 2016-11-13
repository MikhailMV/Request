<?php

class auth {
	/**
    * Проверка ошибок при добавлении нового пользователя
    *  
    */
	function check_new_user($login, $passwd, $passwd2) {
		
        // Проверка существования пользователя в базе
        global $dbh;        
        $stmt =  $dbh->prepare('SELECT * FROM users WHERE login_user = :login');        
        $stmt->bindParam(':login', $login);
        $stmt->execute();
        $result = $stmt->FETCH(PDO::FETCH_ASSOC);
        
        // Проверка наличия ошибок
		if (empty($login) or empty($passwd) or empty($passwd2))
            $error[]='Все поля обязательны для заполнения';
		if ($passwd != $passwd2) 
            $error[]='Введенные пароли не совпадают';
		if (strlen($login)<3 or strlen($login)>30) 
            $error[]='Длина логина должна быть от 3 до 30 символов';
		if (strlen($passwd)<3 or strlen($passwd)>30) 
            $error[]='Длина пароля должна быть от 3 до 30 символов';   
        if ($result) 
            $error[]='Пользователь с таким именем уже существует';
        
		// Возвращаем массив ошибок или положительный ответ
		if (isset($error)) return $error;
		else return 'good';
	}
    
	/**
    * Регистрация нового пользователя
    *  
    */
	function reg($login, $passwd, $passwd2, $chbox) {
		if (($this->check_new_user($login, $passwd, $passwd2))=='good') {            
            
            // Проверка переданных данных
            global $dbh;            
            $login = str_replace("'", "", ($dbh->quote($_POST["login"])));
            $passwd = str_replace("'", "", ($dbh->quote($_POST["passwd"])));            
            $passwd = md5($passwd.'lol'); // хеш пароля с добавкой
            $is_admin;
            if($chbox!=NULL) 
                $is_admin = 1;
            else $is_admin = 0;
                    
            // Запись в базу
            $data = array($login, $passwd, $is_admin); 
            $stmt = $dbh->prepare('INSERT INTO users (login_user, passwd_user, is_admin) 
                                   VALUES (?, ?, ?)');            
            if($stmt->execute($data))
                return true;            
			else {
				print 'Возникла ошибка при регистрации нового пользователя.';
				return false;
			}
		} 
        else {
			print error_print($this->check_new_user($login, $passwd, $passwd2));
			return false;
		}
	}


	/**
    * Проверка авторизации
    *  
    */
	function check() {
		if (isset($_SESSION['is_admin']) and isset($_SESSION['login_user'])) return true;	
	}

	/**
    * Авторизация
    *  
    */
	function authorization() {
        
        global $dbh;
        $login = str_replace("'", "", ($dbh->quote($_POST["login"])));
        $passwd = str_replace("'", "", ($dbh->quote($_POST["passwd"])));
        $passwd = md5($passwd.'lol');
        
        // проверка существования логина и пароля
        $stmt = $dbh->prepare('SELECT is_admin FROM users 
                               WHERE login_user = :login AND passwd_user = :pass');        
        $stmt->bindParam(':login', $login);
        $stmt->bindParam(':pass', $passwd);
        $stmt->execute();
        $result = $stmt->FETCH(PDO::FETCH_ASSOC);
        
        // проверка существования логина
        $stmt = $dbh->prepare('SELECT * FROM users WHERE login_user = :login');        
        $stmt->bindParam(':login', $login);
        $stmt->execute();
        $exist = $stmt->FETCH(PDO::FETCH_ASSOC);
        
		if ($result) {
			// пользователь найден в бд, логин совпадает с паролем			
            $_SESSION['is_admin']=$result["is_admin"];
			$_SESSION['login_user']=$login;                       
			return true;
		} 
        else {
			// пользователь не найден в бд, или пароль не соответствует введенному
			if ($exist) 
                $error[]='Введен неверный пароль';
			else 
                $error[]='Такой пользователь не существует';
			$_SESSION['error'] = error_print($error);
			return false;
		}
	}

	/**
    * Выход
    *  
    */
	function exit_user() {
		// разрушаем сессию и отправляем на главную
		session_destroy();
        print "<script type='text/javascript'>
             window.location = 'index.php'
             </script>";	
	}  
}

/**
* Вывод таблицы заявок
*  
*/
function get_request_table() {
    
    $table = '';
    
    // Таблица заявок для администратора
    if($_SESSION['is_admin']==1) {
        $table .= '
            <table class="js-req-table">
                <tr class="title">
                    <td>Пользователь</td>
                    <td>Заявки:</td></tr>
        ';
        
        // Выводим из базы таблицу заявок
        global $dbh;        
        $stmt = $dbh->prepare('SELECT users.login_user, request.request_name
                               FROM users RIGHT JOIN request ON request.owner_id = users.id_user');        
        $stmt->execute();    
        $result = $stmt->FETCHALL(PDO::FETCH_ASSOC);
        
        // Заполнение таблицы
        foreach($result as $arry) { 
            $table .= '
                <tr><td>'.$arry['login_user']  .'</td>
                <td><a href="view.php?name='.$arry['request_name'].'">'.$arry['request_name'].'</a></td></tr>
            ';
        }
        $table.='</table>';
        
        // Добавим кнопку скачать заявки
        $table .= '<a href="add_request.php" class="btn">Добавить заявку</a>';
        $table .= '<a href="?xml" class="btn xml">Скачать заявки</a>';
        
        // Скачать заявки в xml
        if (isset($_GET['xml'])) {                 
            $file = 'Request-list.xml';
            file_put_contents($file, create_XML($result));
        } 
    }

    // Таблица заявок для пользователя
    if($_SESSION['is_admin']==0 and $_SESSION['login_user']) {
        $table .= '
            <table class="js-req-table"><tr class="title"><td>Заявки:</td></tr>
        ';
        
        // Информация из базы
        global $dbh;
        $stmt =  $dbh->prepare('SELECT request_name
                                FROM request WHERE owner_id = 
                                (SELECT id_user FROM users WHERE login_user = :login)');
        $stmt->bindParam(':login', $_SESSION['login_user']);
        $stmt->execute();
        $result = $stmt->FETCHALL(PDO::FETCH_ASSOC);
        
        // Заполнение таблицы
        foreach($result as $arry) {    	          
            $table .= '
                <tr><td><a href="view.php?name='.$arry['request_name'].'">'.$arry['request_name'].'</a></td></tr>
            ';           
        }
        
        $table.='</table>';
        $table.='<a href="add_request.php" class="btn">Добавить заявку</a>';        
    }
    
    return $table;
}

/**
* Вывод ошибок
*  
*/
function error_print($error) {
    $err = '
        <div class = "error-list">
            <h2>Произошли следующие ошибки:</h2>'."\n".'<ul>
        ';
    foreach($error as $key=>$value) {
        $err.='<li>'.$value.'</li>';
    }
    return $err.'</ul></div>';
}

/**
* Функция преобразования в XML
*  
*/ 
function create_XML($res_table) {
    $xmlData = "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\n";
    $xmlData .= "<Список_заявок>\n";
        
    foreach($res_table as $res_key => $arry) {
        
        $xmlData .= "\t<"."Заявка_" . ($res_key + 1) . ">\n";
                
        foreach ($arry as $key => $value) {            
            $xmlData .= "\t\t<" . $key . ">\n";
                $xmlData .= "\t\t\t" . $value . "\n";                
            $xmlData .= "\t\t</" . $key . ">\n";                                    
        }        
        $xmlData .= "\t</"."Заявка_" . ($res_key + 1) . ">\n";
    }        
    $xmlData .= "</Список_заявок>\n";

    return $xmlData;
}

/**
* Проверка данных при добавлении заявки
*  
*/
function check_add_data($req_name, $description, $phone) {
    if (empty($req_name) or empty($description) or empty($phone))
            $error[]='Все поля обязательны для заполнения';
    if (strlen($description)<10) 
            $error[]='Описание должно быть не менее 10 символов';    
    if (!preg_match("|^[\d]+$|", $phone)) 
            $error[]='Номер телефона должен состоять из цифр без пробелов';
            
    // Возвращаем массив ошибок, если он есть
    if (isset($error)) { 
        $_SESSION['error'] = $error;
        return false;        
    }
	else return 'good';
}

/**
* Добавление заявки в базу
*  
*/
function add_new_request($req_name, $description, $phone) {
    
    // Проверка переданных данных
    global $dbh;
    $login = str_replace("'", "", $dbh->quote($_SESSION['login_user']));
    $req_name = str_replace("'", "", $dbh->quote($req_name));
    $description = str_replace("'", "", $dbh->quote($description));
    $phone = str_replace("'", "", $dbh->quote($phone));
        
    // Получим owner_id из базы
    $stmt =  $dbh->prepare('SELECT id_user FROM users WHERE login_user = :login');        
    $stmt->bindParam(':login', $login);
    $stmt->execute();
    $id = $stmt->FETCH(PDO::FETCH_ASSOC);
        
    // Добавление проверенных данных в базу
    $data = array($id['id_user'], $req_name, $description, $phone);        
    $stmt1 = $dbh->prepare('INSERT INTO request (owner_id, request_name, description, phone) 
                            VALUES (?, ?, ?, ?) ');            
    if($stmt1->execute($data))
        return true;            
    else {
        print 'Возникла ошибка при добавлении заявки.';
        return false;
    }
}

/**
* Выделение цветом последней строки таблицы
*  
*/
function shine_last_row() {
    print "
        <script type='text/javascript'>
            var reqTable = document.querySelector('.js-req-table');
            for(var i = 0; i < reqTable.rows[0].cells.length; i++ )
            reqTable.rows[reqTable.rows.length-1].cells[i].style.background = '#2af7ed';
        </script>
    ";
}
?>
