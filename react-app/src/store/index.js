import React from 'react'

const StoreContext = React.createContext( null );
const useStore = () => React.useContext( StoreContext );

const initialState = {
	notices: [],
	confirmationModal: {
		open: false
	}
}

function StoreProvider( {children} ) {

	const [ data, setData ] = React.useState( initialState );

	return (
		<StoreContext.Provider value={{data, setData}}>
			{children}
		</StoreContext.Provider>
	);
}

export {StoreProvider, useStore}
