<?php

include( "../config.php" );

if ( isset( $_POST["imgUrl"] ) )
{
	$query = $conn->prepare( "UPDATE images SET clicks = clicks + 1 WHERE imgUrl=:imgUrl" );
	$query->bindParam( ":imgUrl", $_POST["imgUrl"] );
	$query->execute();
}
else
{
	echo "image url is null";
}

?>