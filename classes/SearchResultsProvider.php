<?php

class SearchResultsProvider
{
	// A reference for our connection instance of PDO.
	private $conn;
	
	// Default constructor which takes a PDO conneciton instance as a param.
	public function __construct( $conn )
	{
		// Set the reference for the global connection instance.
		$this->conn = $conn;
	}
	
	// Returns the total count of result for a certain term.
	public function getResultCount( $term )
	{
		// Create a select query to get the count of the result for the given term.
		$query = $this->conn->prepare( "SELECT COUNT(*) as count 
										FROM sites 
										WHERE title LIKE :term 
										OR url LIKE :term 
										OR keywords LIKE :term 
										OR description LIKE :term" );
		
		// Add a percentage character to get all results that contain the term within.
		$term = "%" . $term . "%";
		// Now we can bind the our term variable with the placeholder.
		$query->bindParam( ":term", $term );
		// Execute the query.
		$query->execute();
		
		// Get the results.
		$row = $query->fetch( PDO::FETCH_ASSOC );
		
		// Return count coloumn which contains the total count of the results.
		return $row["count"];
	}
	
	// Returns a new html link element.
	public function getResultsHTML( $page, $pageSize, $term )
	{
		$fromLimit = ( $page - 1 ) * $pageSize;
		
		// Create a new query to get all the results with the given term.
		// * return them in descending order in term of clicks. we want to display the more popular first.
		$query = $this->conn->prepare( "SELECT * 
										FROM sites 
										WHERE title LIKE :term 
										OR url LIKE :term 
										OR keywords LIKE :term 
										OR description LIKE :term
										ORDER BY clicks DESC
										LIMIT :fromLimit, :pageSize" );
		
		// Add a percentage character to get all results that contain the term.
		$term = "%" . $term . "%";
		// Now we can bind the our term variable with the placeholder.
		$query->bindParam( ":term", $term );
		$query->bindParam( ":fromLimit", $fromLimit, PDO::PARAM_INT );
		$query->bindParam( ":pageSize", $pageSize, PDO::PARAM_INT );
		// Execute the query.
		$query->execute();
		
		// Create a new html div element.
		$resultsHTML = "<div class='SiteResultsClass'>";
		
		// Get all the data for each element.
		while ( $row = $query->fetch( PDO::FETCH_ASSOC ) )
		{
			$id = $row["id"];
			$url = $row["url"];
			$title = $row["title"];
			$description = $row["description"];
			
			$title = $this->trimString( $title, 80 );
			$description = $this->trimString( $description, 200 );
			
			$resultsHTML .= "<div class='SingleSiteResultClass'> 
			
								<h3 class='SingleSiteResultTitleClass'>
									<a class='SingleSiteResultTitleLinkClass' href='$url' data-linkID='$id'>
										$title
									</a>
								</h3>
								<span class='SingleSiteResultUrlClass'>$url</span>
								<span class='SingleSiteResultDescriptionClass'>$description</span>
								
							 </div>";
		}
		
		// end the div element.
		$resultsHTML .= "</div>";
		
		// Return the final generated html results.
		return $resultsHTML;
	}
	
	private function trimString( $str, $maxLength )
	{
		if ( strlen( $str ) > $maxLength )
			$str = substr( $str, 0, $maxLength ) . "...";
		return $str;
	}
}

?>



























