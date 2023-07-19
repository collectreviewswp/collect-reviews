import data from "./data";

export function getPlatformName( slug ) {

	return data.config.platforms.find( p => p.slug === slug )?.name || slug;
}
