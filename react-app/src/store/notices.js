import {useStore} from "./index";
import {strHashCode, uniqid} from "helpers/common";

export default function useNotices() {

	const store = useStore();

	return {
		list: store.data.notices,
		add: ( text, type = 'info', floating = false, unique = true ) => {

			const hash = strHashCode( text + type );

			if ( unique && store.data.notices.findIndex( notice => notice.hash === hash ) !== -1 ) {
				return;
			}

			store.setData( {
				...store.data,
				notices: [
					...store.data.notices,
					{
						id: uniqid(),
						text,
						type,
						hash
					}
				]
			} );
		},
		remove: ( id ) => {
			const indexToRemove = store.data.notices.findIndex( notice => notice.id === id );

			if ( indexToRemove === -1 ) {
				return;
			}

			store.setData( {
				...store.data,
				notices: [
					...store.data.notices.slice( 0, indexToRemove ),
					...store.data.notices.slice( indexToRemove + 1 )
				]
			} );
		},
		clear: () => {
			store.setData( {
				...store.data,
				notices: []
			} );
		}
	}
}
