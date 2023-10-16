import data from "./data";

export function getPlatformName( slug ) {

	return data.platforms.find( p => p.slug === slug )?.name || slug;
}
