<?php

require_once("./constants.php");

Class DB_MYSQL{
	public $dbh;

	function __construct(){
		$dbname='api';
		
		$hostname=Constants::MYSQL_ATTR_Hostname;
		$dsn = "mysql:host=$hostname;dbname=$dbname";
		$username = Constants::MYSQL_ATTR_USERNAME;
		$password = Constants::MYSQL_ATTR_PASSWORD;
		$options = array(
		    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
		); 
		$dbh = new PDO($dsn, $username, $password, $options);

		$this->dbh=$dbh;
	}
}
?>