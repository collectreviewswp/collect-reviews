export function uniqid() {

	function seed( s, w ) {
		s = parseInt( s, 10 ).toString( 16 );
		return w < s.length ? s.slice( s.length - w ) : (w > s.length) ? new Array( 1 + (w - s.length) ).join( '0' ) + s : s;
	}

	return seed( parseInt( new Date().getTime() / 1000, 10 ), 8 ) + seed( Math.floor( Math.random() * 0x75bcd15 ) + 1, 5 );
}

export function strHashCode( str ) {
	let hash = 0,
		i, chr;

	if ( str.length === 0 ) return hash;

	for ( i = 0; i < str.length; i++ ) {
		chr = str.charCodeAt( i );
		hash = ((hash << 5) - hash) + chr;
		hash |= 0; // Convert to 32bit integer
	}

	return hash;
}
