import * as React from 'react';

import Autocomplete from 'components/fields/Autocomplete';

import {__} from "@wordpress/i18n";

import api from "helpers/api";

export default function FormSelect( {integration, ...rest} ) {
	const [ open, setOpen ] = React.useState( false ),
		[ loaded, setLoaded ] = React.useState( false ),
		[ options, setOptions ] = React.useState( [] );

	const loading = open && options.length === 0 && ! loaded;

	React.useEffect( () => {
		let active = true;

		if ( ! loading ) {
			return undefined;
		}

		(async () => {
			try {
				const loadedOptions = await api.post( 'get_forms_integration_forms', {
					integration,
				} );

				if ( active ) {
					setOptions( loadedOptions );
				}
			} catch ( error ) {
				console.log( error );
			} finally {
				if ( active ) {
					setLoaded( true );
				}
			}
		})();

		return () => {
			active = false;
		};
	}, [ loading ] );

	return (
		<Autocomplete
			open={open}
			onOpen={() => {
				setOpen( true );
			}}
			onClose={() => {
				setOpen( false );
			}}
			isOptionEqualToValue={( option, value ) => option.id === value.id}
			getOptionLabel={( option ) => `${option.title} (#${option.id})`}
			options={options}
			loading={loading}
			placeholder={__( 'Select a form', 'collect-reviews' )}
			noOptionsText={__( 'No forms found', 'collect-reviews' )}
			{...rest}
		/>
	);
}
