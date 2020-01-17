<?php
 
include( "config.php" );
include( "classes/SearchResultsProvider.php" );
include( "classes/SearchImageResultsProvider.php" );


	// Check if the term variable was set?
	if ( isset($_GET["term"]) )
		$term = $_GET["term"];
	else
		// User didn't enter any term to search for.
		exit( "There is no search term!" );
	
	// Set the type of search (default is sites).
	$type = isset( $_GET["type"] ) ? $_GET["type"] : "sites";
	
	// Check if we set the value of current page as well
	// If it is not set them set it as first page 1.
	$page = isset( $_GET["page"] ) ? $_GET["page"] : 1;
 
?>

<!-- Seach result page -->
<!DOCTYPE html>
<html>
<head>
	<title>MANA Search Engine</title>

	<!-- Use fancybox css -->
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.css" />

	<!-- use our cascading style sheets -->
	<link rel="stylesheet" type="text/css" href="assets/css/style.css">
	
	<!-- use jQuery CDN -->
	<script src="https://code.jquery.com/jquery-3.4.1.min.js" 
			integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" 
			crossorigin="anonymous"></script>
	
</head>

<body>

	<div class="Wrapper">
	
		<div class="SearchHeaderClass">
		
			<div class="SearchHeaderContentClass">
			
				<div class="LogoClass">
			
					<a href="index.php">
						<img src="assets/images/MANALogo.jpg">
					</a>
				
				</div>
				
				<div class="SearchClass">
				
					<form action="search.php" method="GET">
					
						<div class="SearchBarClass">
							<!-- Create a hidden element to hold the value of the current search type (sites or images) -->
							<input type="hidden" name="type" value="<?php echo $type; ?>">
							<input class="SearchInputBox" type="text" name="term" value="<?php echo $term ?>">
							<button class="SearchBtn">
								<img src="assets/images/search_icon.png">
							</button>
						</div>
					
					</form>
					
				</div>
			
			</div>
		
		
		<div class="SearchTabsClass">
		
			<ul class="TabsListClass">
				
				<!-- If the current search type is 'sites' then make it active so we can apply a special style on it -->
				<li class="<?php echo $type == 'sites' ? 'ActiveTypeClass' : '' ?>">
					<a href='<?php echo "search.php?term=$term&type=sites"; ?>'>
						Sites
					</a>
				</li>
				
				<!-- If the current search type is 'images' then make it active so we can apply a special style on it -->
				<li class="<?php echo $type == 'images' ? 'ActiveTypeClass' : '' ?>">
					<a href='<?php echo "search.php?term=$term&type=images"; ?>'>
						Images
					</a>
				</li>
				
			</ul>
		
		</div>
		
		
		</div>

		<div class="SearchResultMainClass">
		
			<?php
				
				if ( $type == "sites" )
				{
					$searchResultProvider = new SearchResultsProvider( $conn );
					// Page size: how many link to display in a single page.
					$pageLimit = 20;
				}
				else
				{
					$searchResultProvider = new SearchImageResultsProvider( $conn );
					$pageLimit = 30;
				}
				
				$resultCount = $searchResultProvider->getResultCount( $term );
				
				echo "<p class='ResultCountLabelClass'>$resultCount result found</p>";
				
				echo $searchResultProvider->getResultsHTML( $page, $pageLimit, $term );
			?>
		
		</div>
		
		
		<div class="SearchPaginationClass">
		
			<div class="SearchPageBtnsClass">
				
				<?php
				
					$totalPagesCount = ceil( $resultCount / $pageLimit );
					$pagesToShow = 10;
					$pagesLeft = min( $pagesToShow, $totalPagesCount );
					
					$currentPage = $page - floor( $pagesToShow / 2 );
					if ( $currentPage < 1 )
						$currentPage = 1;
					
					// To make sure we always display page index.
					if ( $currentPage + $pagesLeft > $totalPagesCount + 1 )
						$currentPage = $totalPagesCount + 1 - $pagesLeft;
					
					while ( $pagesLeft != 0 && $currentPage <= $totalPagesCount )
					{
						// Check if the currentPage value is equal to the current opened page. 
						if ( $currentPage == $page )
							echo "<span class='PageNumberClass'>$currentPage</span>";	
						else
						{
							echo "<a href='search.php?term=$term&type=$type&page=$currentPage'>
									<span class='PageNumberClass'>$currentPage</span>
								  </a>";
							
						}
						
						$currentPage++;
						$pagesLeft--;
					}
				
				?>
				
			</div>
		
		</div>
		

	</div>

	<script src="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.js"></script>
	<script src="https://unpkg.com/masonry-layout@4/dist/masonry.pkgd.min.js"></script>
	<script type="text/javascript" src="assets/js/script.js"></script>

</body>

</html>

























