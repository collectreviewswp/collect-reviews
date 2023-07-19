import reviewRequestsRoutes from './ReviewRequests/routes';
import settingsRoutes from './Settings/routes';

import data from "../helpers/data";

let routes;

// TODO: implement lazy loading (next release).
if ( data.page === 'collect-reviews-settings' ) {
	routes = settingsRoutes;
} else {
	routes = reviewRequestsRoutes;
}

export default routes;
