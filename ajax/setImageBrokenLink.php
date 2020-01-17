<?php

include( "../config.php" );

if ( isset( $_POST["src"] ) )
{
	$query = $conn->prepare( "UPDATE images SET broken = 1 WHERE imgUrl=:src" );
	$query->bindParam( ":src", $_POST["src"] );
	$query->execute();
}
else
{
	echo "link src is null";
}

?>