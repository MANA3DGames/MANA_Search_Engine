<?php

class SearchImageResultsProvider
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
										FROM images 
										WHERE (title LIKE :term 
										OR alt LIKE :term) 
										AND broken=0" );
		
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
										FROM images 
										WHERE (title LIKE :term 
										OR alt LIKE :term) 
										AND broken=0
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
		$resultsHTML = "<div class='ImageResultsClass'>";
		
		// Count for each image.
		$count = 0;
		
		// Get all the data for each element.
		while ( $row = $query->fetch( PDO::FETCH_ASSOC ) )
		{
			$count++;
			
			$id = $row["id"];
			$imgUrl = $row["imgUrl"];
			$siteUrl = $row["siteUrl"];
			$title = $row["title"];
			$alt = $row["alt"];
			
			if ( $title )
				$displayText = $title;
			else if ( $alt )
				$displayText = $alt;
			else
				$displayText = $imgUrl;
				
			
			$resultsHTML .= "<div class='SingleImageResultClass image$count'> 
								<a href='$imgUrl' data-fancybox data-caption='$displayText' data-siteurl='$siteUrl'>
									
									<script>
										
										$(document).ready( function() {
											loadImage( \"$imgUrl\", \"image$count\" );
										} );
										
									</script>
									
									<span class='SingleImageResultdetailsClass'>$displayText</span>
								</a>
							 </div>";
		}
		
		// end the div element.
		$resultsHTML .= "</div>";
		
		// Return the final generated html results.
		return $resultsHTML;
	}
}

?>



























