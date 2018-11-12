<?php
	include __DIR__ . '/db_config.php'; 
	include __DIR__ . '/connectDB.php'; 
	$blocktime = 600;
	
	if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} else {
		$ip = $_SERVER['REMOTE_ADDR'];
	}
	$curr_date = time();

	$sql = "SELECT users.* FROM users WHERE ip = '{$ip}';";
	$result = mysqli_query($db, $sql) or die("Ошибка запроса: " . mysqli_error($db)); 
	$myrow = mysqli_fetch_array($result);

	if($result->num_rows == 0){
		$sql = "INSERT INTO users (ip) VALUES('{$ip}')";
		mysqli_query($db, $sql) or die("Ошибка записи в БД: " . mysqli_error($db)); 
		$id = mysqli_insert_id($db);
		$sql = "INSERT INTO date (user, unixtime) VALUES('{$id}', '{$curr_date}')";
		mysqli_query($db, $sql) or die("Ошибка записи в БД: " . mysqli_error($db)); 
	} elseif($myrow["blockingDate"] != NULL && (intval($myrow["blockingDate"]) + $blocktime >= $curr_date)){
		header("HTTP/1.0 403 Forbidden");
		header("Expires: " . date('r', intval($myrow["blockingDate"]) + $blocktime));
		mysqli_close($db);
		die;
	} else {
		$id = intval($myrow["id"]);
		$sql = "INSERT INTO date (user, unixtime) VALUES('{$id}', '{$curr_date}')";
		mysqli_query($db, $sql) or die("Ошибка записи в БД: " . mysqli_error($db));
		
		$min = $curr_date - 60;
		$sql = "SELECT date.* FROM date WHERE user = '{$id}' AND unixtime >= {$min};";
		$result = mysqli_query($db, $sql) or die("Ошибка запроса: " . mysqli_error($db)); 
		if($result->num_rows > 5){
			$id = intval($myrow["id"]);
			$sql = "UPDATE users SET blockingDate = {$curr_date} WHERE id = {$id}";
			mysqli_query($db, $sql) or die("Ошибка запроса: " . mysqli_error($db)); 
			header("HTTP/1.0 403 Forbidden");
			header("Expires: " . date('r', $curr_date + $blocktime));
			mysqli_close($db);
			die;
		}
	}

	echo 'Hello world!';
	mysqli_close($db);