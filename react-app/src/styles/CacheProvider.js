import {CacheProvider as EmotionCacheProvider} from "@emotion/react";

import createCache from "@emotion/cache";

import {shadowContainer} from "../shallowRoot";

const emotionRoot = document.createElement( 'style' );

shadowContainer.prepend( emotionRoot );

const cache = createCache( {
	key: 'css',
	prepend: true,
	container: emotionRoot,
} );

const CacheProvider = props => <EmotionCacheProvider {...props} value={cache}/>;

export default CacheProvider;
