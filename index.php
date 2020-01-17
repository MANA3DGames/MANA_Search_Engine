<!-- The startup page for our MANA Search Engine -->
<!DOCTYPE html>
<html>
	<head>
		<title>MANA Search Engine</title>
		
		<meta name="description" content="Search Engine">
		<meta name="keywords" content="MANA, mana search, search engine, search, images, sites, websites">
		<meta name="author" content="Mahmoud Abu Obaid">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		
		<!-- use our cascading style sheets -->
		<link rel="stylesheet" type="text/css" href="assets/css/style.css">
		
	</head>

	<body>

		<!-- We need to class names here for the css, because we're going to use Wrapper in other pages -->
		<div class="Wrapper IndexClass">
		
			<div class="MainClass">
			
				<!-- MANA 3D Game Logo -->
				<div class="LogoClass">
					<img src="assets/images/MANALogo.jpg" title="MANA 3D Games Logo" alt="MANA Search Engine Logo">
				</div>
			
				<div class="SearchClass">
				
					<form action="search.php" method="GET">
					
						<input class="SearchInputBox" type="text" name="term">
						<input class="SearchBtn" type="submit" value="Search">
						
					</form>
				</div>
			
			</div>

		</div>

	</body>

</html>