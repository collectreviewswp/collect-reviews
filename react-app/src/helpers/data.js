const {collectReviews = {}} = window;

let normalizeOptions = function( o ) {
	for ( let i in o ) {
		if ( o[ i ] !== null && typeof (o[ i ]) === "object" ) {
			normalizeOptions( o[ i ] );
		} else if ( o[ i ] === "true" || o[ i ] === "false" ) {
			o[ i ] = o[ i ] === "true";
		}
	}
};

normalizeOptions( collectReviews );

export {normalizeOptions};

export default collectReviews;
