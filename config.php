<?php

ob_start();

try
{
	$conn = new PDO( "mysql:dbname=mana_search_engine;host=localhost", "root", "" );
	$conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );
}
catch ( PDOExeption $e )
{
	echo "Connection failed: " . $e->getMessage();
}

?>