<?php
	$db = mysqli_connect($host, $user, $password) 
    or die("Ошибка подключения к серверу баз данных " . mysqli_error($db));
	
	$result = mysqli_select_db ($db, $db_mame);
	if(!$result){
		$sql = "CREATE DATABASE {$db_mame};";
		$isCreate = mysqli_query($db, $sql);
		if(!$isCreate){
			die("Ошибка создания БД: " . $db_mame);
		} else {
			mysqli_select_db ($db, $db_mame);
		}
	}
		
	if(mysqli_num_rows(mysqli_query($db, "SHOW TABLES LIKE 'users'")) == 0) {
		$sql = "CREATE TABLE users (
			  id INT NOT NULL AUTO_INCREMENT,
			  ip TEXT NOT NULL,
			  blockingDate BIGINT DEFAULT NULL,
			  PRIMARY KEY (id)
		  );";
		  mysqli_query($db, $sql)
		  or die("Ошибка создания таблицы users" . mysqli_error($db));
	}
	
	if(mysqli_num_rows(mysqli_query($db, "SHOW TABLES LIKE 'date'")) == 0) {
		$sql = "CREATE TABLE date (
			  id INT NOT NULL AUTO_INCREMENT,
			  user INT NOT NULL,
			  unixtime BIGINT NOT NULL,
			  PRIMARY KEY (id),
			  FOREIGN KEY (user) REFERENCES users(id)
		  );";
		mysqli_query($db, $sql)
		or die("Ошибка создания таблицы date" . mysqli_error($db));
	}