<?php

class DomDocumentParser
{
	// An instance of DomDocument.
	private $dDoc;
	
	// Default constructor for our DomDocumentParser class.
	public function __construct($url)
	{
		// Create a request options.
		$rOptions = array( 'http'=>array( 'method'=>"GET", 'header'=>"User-Agent: MANAAgent/1.0\n" ) );
		// Create stream context.
		$context = stream_context_create( $rOptions );
		// Initialize DomDocument instance.
		$this->dDoc = new DomDocument();
		// Now, we can load all HTML in the target url.
		@$this->dDoc->loadHTML( file_get_contents( $url, false, $context ) );
	}
	
	
	// Return all the links that have been found by loadHTML.
	public function getLinks()
	{
		// We are only intrested in element with a tag (links)
		return $this->dDoc->getElementsByTagName( "a" );
	}
	
	// Return all the titles that have been found by loadHTML.
	public function getTitles()
	{
		// We are only intrested in element with a tag (title)
		return $this->dDoc->getElementsByTagName( "title" );
	}
	
	// Return all the MetaData that have been found by loadHTML.
	public function getMeta()
	{
		// We are only intrested in element with a tag (meta)
		return $this->dDoc->getElementsByTagName( "meta" );
	}
	
	// Return all the images that have been found by loadHTML.
	public function getImages()
	{
		// We are only intrested in element with a tag (img)
		return $this->dDoc->getElementsByTagName( "img" );
	}
}

?>