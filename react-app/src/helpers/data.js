const {collect_reviews_admin = {}} = window;

let normalizeOptions = function( o ) {
	for ( let i in o ) {
		if ( o[ i ] !== null && typeof (o[ i ]) === "object" ) {
			normalizeOptions( o[ i ] );
		} else if ( o[ i ] === "true" || o[ i ] === "false" ) {
			o[ i ] = o[ i ] === "true";
		}
	}
};

normalizeOptions( collect_reviews_admin );

export {normalizeOptions};

export default collect_reviews_admin;
