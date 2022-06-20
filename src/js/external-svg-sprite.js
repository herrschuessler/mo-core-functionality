/* global MoCoreExternalSvgSprite, Promise, fetch */
/* eslint no-useless-escape: "off" */
( async () => {
	const iconSetBaseUrl = MoCoreExternalSvgSprite.iconSetBaseUrl;
	const iconSetBaseUrlRegExp = iconSetBaseUrl.replace( /[.*+?^${}()|\/[\]\\]/g, '\\$&' );

	const iconSetUses = Array.from( document.querySelectorAll( "svg use" ) )
		.map( ( elem ) => {
			const url = elem.getAttribute( "xlink:href" ) || elem.getAttribute( "href" );
			return url && url.match( new RegExp( `^${iconSetBaseUrlRegExp}` ) )
				? { elem, url }
				: null;
		} )
		.filter( ( result ) => !!result )
		.map( ( data ) => {
			const result = data.url.match(
				new RegExp( `^${iconSetBaseUrlRegExp}((?:(?!\.svg).)+)\.svg[^#]*#(.+)` )
			);
			return { setId: result[ 1 ], iconId: result[ 2 ], ...data };
		} );

	const uniqueIconSetUrls = {};
	iconSetUses.forEach( ( { url, setId } ) => {
		if ( !uniqueIconSetUrls[ setId ] )
			uniqueIconSetUrls[ setId ] = url.split( "#" )[ 0 ];
	} );

	const svgHtml = await Promise.all(
		Object.values( uniqueIconSetUrls ).map( ( url ) =>
			fetch( url ).then( ( res ) => res.text() )
		)
	).then( ( parts ) => parts.join( "" ) );

	const wrapper = document.createElement( "div" );
	wrapper.style.display = "none";
	wrapper.innerHTML = svgHtml;
	document.body.insertBefore( wrapper, document.body.childNodes[ 0 ] );

	iconSetUses.forEach( ( { elem, iconId } ) => {
		elem.removeAttribute( "xlink:href" );
		elem.removeAttribute( "href" );
		elem.setAttribute( "href", `#${iconId}` );
	} );
} )();