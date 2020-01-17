var timer;

$(document).ready( function() {
	// Create an event for click on a link result (element of a class -> SingleSiteResultTitleLinkClass) from (SearchResultsProvider.php)
	$( ".SingleSiteResultTitleLinkClass" ).on( "click", function() {

		// Get the url of this link.
		var url = $(this).attr( "href" ); // (this) refers to current SingleSiteResultTitleLinkClass object
		
		// Get the ID of this link.
		var id = $(this).attr( "data-linkID" );
		
		// Check if this link doesn't have an ID?
		if ( !id )
			alert( "data-linkID attribute not found" );
		
		// Call onClick event.
		onClickLink( id, url );
		
		// To prevent this link to go to another page.
		return false;
	} );
	
	// Call Masonry functionality.
 	var grid = $( ".ImageResultsClass" );
	// When the layout finish calcuating.
	grid.on( "layoutComplete", function() {
		// In the css file we set the visibility img result to hidden so now after finish calcaulating the layout we can set it back to visible.
		$( ".SingleImageResultClass img" ).css( "visibility", "visible" );
	} );
	grid.masonry( {
		itemSelector: ".SingleImageResultClass",
		columnWidth: 200,
		gutter: 20,
		isInitLayout: false
	});
	
	// Apply this on every single element that has data-fancybox attribute. 
	$( "[data-fancybox]" ).fancybox( {
		
		caption : function( instance, item ) 
		{
	        var caption = $(this).data('caption') || '';
	        var siteUrl = $(this).data('siteurl') || '';


	        if ( item.type === 'image' ) {
	            caption = (caption.length ? caption + '<br />' : '')
	             + '<a href="' + item.src + '">View image</a><br>'
	             + '<a href="' + siteUrl + '">Visit page</a>';
	        }

	        return caption;
	    },
		afterShow : function( instance, item ) 
		{
			onClickImage( item.src );
	    }
		
	} );
});


function onClickLink( linkID, url )
{ 
	$.post( "ajax/updateLinkClickCount.php", { linkID: linkID } )
	.done( function( result ) {
		// result should be empty otherwise there is something went worng!
		if ( result != "" )
		{
			alert( result );
			return;
		}
		
		// Now we can redirect the user to the target link.
		window.location.href = url;
	});
}

function onClickImage( imgUrl )
{ 
	$.post( "ajax/updateImageClickCount.php", { imgUrl: imgUrl } )
	.done( function( result ) {
		if ( result != "" )
		{
			alert( result );
			return;
		}
	});
}


function loadImage( src, className )
{
	var img = $( "<img>" );
	
	img.on( "load", function() {
		// Add the <img> to <a > element in clasName => (image$count)
		$( "." + className + " a" ).append( img );
		
		clearTimeout( timer );
		
		timer = setTimeout( function() {
			// Re-Call Masonry functionality.
			$( ".ImageResultsClass" ).masonry();
		}, 500 );
		
		// Re-Call Masonry functionality.
		$( ".ImageResultsClass" ).masonry();
	} );
	
	img.on( "error", function() {
		
		// Remove the element form the html.
		$( "." + clasName ).remove();
		
		// Mark this img link as broken by calling ajax function.
		$.post( "ajax/setImageBrokenLink.php", { src: src } );
	} );
	
	// Set the source image <img src='??'>.
	img.attr( "src", src );
}
