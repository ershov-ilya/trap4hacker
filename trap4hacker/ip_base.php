<?
function ResolveIP($ip = "0.0.0.0"){
	// Подключаемся к базе данных
	$db_host = "localhost";
	$db_user = "";
	$db_password = "";
	$db_database = "";
	
	$output='';
	
	$link = mysql_connect ($db_host, $db_user, $db_password);
	if ($link && mysql_select_db ($db_database)) {
		mysql_query ("set names utf8");
	} else {
		die ("db error");
	}
	
	// Преобразуем IP в число
	$int = sprintf("%u", ip2long($ip));
	
	$country_name = "";
	$country_id = 0;
	
	$city_name = "";
	$city_id = 0;
	
	// Ищем по российским и украинским городам
	$sql = "select * from (select * from net_ru where begin_ip<=$int order by begin_ip desc limit 1) as t where end_ip>=$int";
	$result = mysql_query($sql);
	if ($row = mysql_fetch_array($result)) {
		$city_id = $row['city_id'];
		$sql = "select * from net_city where id='$city_id'";
		$result = mysql_query($sql);
		if ($row = mysql_fetch_array($result)) {
			$city_name = $row['name_ru'];
			$country_id = $row['country_id'];
		} else {
			$city_id = 0;
		}
	}
	
	// Если не нашли - ищем страну и город по всему миру
	if (!$city_id) {
		// Ищем европейскую страну
		$sql = "select * from (select * from net_euro where begin_ip<=$int order by begin_ip desc limit 1) as t where end_ip>=$int";
		$result = mysql_query($sql);
		if (mysql_num_rows($result) == 0) {
			// Ищем страну в мире
			$sql = "select * from (select * from net_country_ip where begin_ip<=$int order by begin_ip desc limit 1) as t where end_ip>=$int";
			$result = mysql_query($sql);
		}
		if ($row = mysql_fetch_array($result)) {
			$country_id = $row['country_id'];
		}
	
		// Ищем город
		$city_name = "";
		$city_id = 0;
		// Ищем город в глобальной базе
		$sql = "select * from (select * from net_city_ip where begin_ip<=$int order by begin_ip desc limit 1) as t where end_ip>=$int";
		$result = mysql_query($sql);
		if ($row = mysql_fetch_array($result)) {
			$city_id = $row['city_id'];
			$sql = "select * from net_city where id='$city_id'";
			$result = mysql_query($sql);
			if ($row = mysql_fetch_array($result)) {
				$city_name = $row['name_ru'];
				$country_id = $row['country_id'];
			}
		}
	}
	
	// Выводим результат поиска
	
	if ($country_id == 0) {
		$output.= "Страна не определена";
	} else {
		// Название страны
		$sql = "select * from net_country where id='$country_id'";
		$result = mysql_query($sql);
		if ($row = mysql_fetch_array($result)) {
			$country_name = $row['name_ru'];
		}
		// Выводим
		$output.=  "$country_name ($country_id)";
	}
	
	$output.=  " - ";
	
	if ($city_id == 0) {
		$output.=  "Город не определен";
	} else {
		$output.=  "$city_name ($city_id)";
	}
	
	return $output;
}

// print ResolveIP();
?>