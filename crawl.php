<?php

// Include out config.php file which is going to connect us with the database.
include( "config.php" );
// Include out DomDocumentParser class.
include( "classes/DomDocumentParser.php" );


// Define global arrays.
// A list contains all the links that have been crawled.
$alreadyCrawledLinks = array();
// All links that we still have to crawl.
$toBeCrawled = array();
// A list of all already founded images. 
$foundedImages = array();



function checkLinkExists( $url )
{
	// We need to reference the global connection variable in our config.php file.
	global $conn;
	
	// Create out query to insert the new link.
	// First, we need to start out prepare stage.
	$query = $conn->prepare( "SELECT * FROM sites WHERE url = :url" ); // NOTE: first we need to add placeholders for our values (more secure against mysql injection).
	// Now, we can bind out value (variable) to the placeholder.
	$query->bindParam( ":url", $url );
	// Execute the query.
	$query->execute();
	
	return $query->rowCount() != 0;
}

function addLinkToDatabase( $url, $title, $description, $keywords )
{
	// We need to reference the global connection variable in our config.php file.
	global $conn;
	
	// Create out query to insert the new link.
	// First, we need to start out prepare stage.
	$query = $conn->prepare( "INSERT INTO sites(url, title, description, keywords)
							  VALUES(:url, :title, :description, :keywords)" );		// NOTE: first we need to add placeholders for our values (more secure against mysql injection).
	
	// Now, we can bind out values (variables) to the placeholders.
	$query->bindParam( ":url", $url );
	$query->bindParam( ":title", $title );
	$query->bindParam( ":description", $description );
	$query->bindParam( ":keywords", $keywords );
	
	// Execute the query.
	return $query->execute();
}



function addImageToDatabase( $url, $src, $title, $alt )
{
	// We need to reference the global connection variable in our config.php file.
	global $conn;
	
	// Create out query to insert the new image.
	// First, we need to start out prepare stage.
	$query = $conn->prepare( "INSERT INTO images(siteUrl, imgUrl, title, alt)
							  VALUES(:siteUrl, :imgUrl, :title, :alt)" );		// NOTE: first we need to add placeholders for our values (more secure against mysql injection).
	
	// Now, we can bind out values (variables) to the placeholders.
	$query->bindParam( ":siteUrl", $url );
	$query->bindParam( ":imgUrl", $src );
	$query->bindParam( ":title", $title );
	$query->bindParam( ":alt", $alt );
	
	// Execute the query.
	return $query->execute();
}



// Converts a relative link to an absolute url.
function createLink( $src, $url )
{
	// Define scheme (http) and host (ex. mana3d.com) variables  
	$scheme = parse_url( $url )["scheme"];
	$host = parse_url( $url )["host"];
	
	// Check if the current link starts with '//' so, we need to add the scheme only.
	if ( substr( $src, 0, 2 ) == "//" )
		$src = $scheme . "." . $src;
	// Check if the current link starts with '/' so, we need to add the scheme and double forward slash.
	else if ( substr( $src, 0, 1 ) == "/" )
		$src = $scheme . "://" . $host . $src;
	else if ( substr( $src, 0, 2 ) == "./" )
		// use dirname to get the current directory path.
		// substr( $src, 1 ) start from 1 because we don't want the dot in '/.'
		$src = $scheme . "://" . $host . dirname( parse_url( $url )["path"] ) . substr( $src, 1 );
	else if ( substr( $src, 0, 3 ) == "../" )
		$src = $scheme . "://" . $host . "/" . $src;
	else if ( substr( $src, 0, 5 ) != "https" && substr( $src, 0, 4 ) != "http" )
		$src = $scheme . "://" . $host . "/" . $src;
	
	return $src;
}


function getLinkDetails( $url )
{
	// Make a global reference for foundedImages array so we can use it here in this funciton.
	global $foundedImages;
	
	
	// Create a new instance of DomDocumentParser.
	$parser = new DomDocumentParser( $url );
	
	// Get an array of all elements with tag 'title'.
	$titleArray = $parser->getTitles();
	
	// Check if we don't have any item?
	if ( sizeof( $titleArray ) == 0 || $titleArray->item(0) == NULL )
		return;
	
	// Get the first title just in case there is more than one title!
	$title = $titleArray->item(0)->nodeValue;
	$title = str_replace( "\n", "", $title );
	
	// Check if there is a title for this link?
	if ( $title == "" )
		return;
	
	// Now we will start getting the description and keywords.
	$description = "";
	$keywords = "";
	
	// Get all meta data.
	$metaArray = $parser->getMeta();
	
	foreach ( $metaArray as $meta )
	{
		if ( $meta->getAttribute( "name" ) == "description" )
			$description = $meta->getAttribute( "content" );
		
		if ( $meta->getAttribute( "name" ) == "keywords" )
			$keywords = $meta->getAttribute( "content" );
	}
	
	// Remove any newline character.
	$description = str_replace( "\n", "", $description );
	$keywords = str_replace( "\n", "", $keywords );
	
	// Check if we already have this link in out database?
	if ( !checkLinkExists( $url ) )
		// Add the new link to our database.
		addLinkToDatabase( $url, $title, $description, $keywords );
	
	
	// Get all images.
	$imageArray = $parser->getImages();
	foreach ( $imageArray as $img )
	{
		$src = $img->getAttribute( "src" );
		$alt = $img->getAttribute( "alt" );
		$title = $img->getAttribute( "title" );
		
		// Check if this image has neither a title nor an alt.
		if ( !$alt && !$title )
			continue;
		
		// Just make sure we have an absoulte link.
		$src = createLink( $src, $url );
		
		// Check if we don't have this image?
		if ( !in_array( $src, $foundedImages ) )
		{
			// Add this image to foundedImages array.
			$foundedImages[] = $src;
			
			// Add image to our database.
			addImageToDatabase( $url, $src, $title, $alt );
		}
	}
}


// looks for all links in the given url.
function followLinks( $url )
{
	// Refere to the global version of alreadyCrawledLinks & toBeCrawled
	// we're doing that because crawl.php is not a class. 
	global $alreadyCrawledLinks;
	global $toBeCrawled;
	
	// Create a new instance of DomDocumentParser.
	$parser = new DomDocumentParser( $url );
	
	// Get all links.
	$linkList = $parser->getLinks();
	
	// Iterat through the linkList
	foreach ( $linkList as $link )
	{
		// We're only intersted in link (href)
		$href = $link->getAttribute( "href" );
		
		// Ignore none link.
		if ( strpos( $href, "#" ) !== false || 
			 ( strlen( $href ) == 1 and substr( $href, 0 ) == "/" ) ||
			 substr( $href, 0, 11 ) == "javascript:" )
			continue;
		
		// Create the actual link.
		$href = createLink( $href, $url );
		
		// Check if this link has not been crawled yet?
		if ( !in_array( $href, $alreadyCrawledLinks ) )
		{
			// Then add it to the list. 
			$alreadyCrawledLinks[] = $href;		// in PHP empty brakets [] means increament the current index.
			$toBeCrawled[] = $href;
			
			// Get details for the current link.
			getLinkDetails( $href );
		}
		else
			return;
	}
	
	// Get rid of the current item on the top of the array.
	array_shift( $toBeCrawled );	// array_shift similar to pop
	
	foreach ( $toBeCrawled as $site )
		followLinks( $site );
}

// Startup URL.
$startUrl = "https://www.bbc.com";
// Get all links for our URL.
followLinks( $startUrl );


?>