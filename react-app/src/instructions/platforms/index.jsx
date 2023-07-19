import React from 'react';

import Google from "./Google";
import Tripadvisor from "./Tripadvisor";
import Trustpilot from "./Trustpilot";
import Yelp from "./Yelp";
import Facebook from "./Facebook";
import Custom from "./Custom";

const PlatformInstruction = ( {platform} ) => {

	switch ( platform ) {
		case 'google':
			return <Google/>;
		case 'tripadvisor':
			return <Tripadvisor/>;
		case 'trustpilot':
			return <Trustpilot/>;
		case 'yelp':
			return <Yelp/>;
		case 'facebook':
			return <Facebook/>;
		case 'custom':
			return <Custom/>
		default:
			return null;
	}
}

export default PlatformInstruction;
