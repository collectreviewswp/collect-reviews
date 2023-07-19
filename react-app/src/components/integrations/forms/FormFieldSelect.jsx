import * as React from 'react';

import Autocomplete from 'components/fields/Autocomplete';

import {__, sprintf} from "@wordpress/i18n";

import api from "helpers/api";

export default function FormFieldSelect( {integration, formId, fieldType, ...rest} ) {
	const [ prevFormId, setPrevFormId ] = React.useState( false ),
		[ open, setOpen ] = React.useState( false ),
		[ loaded, setLoaded ] = React.useState( false ),
		[ options, setOptions ] = React.useState( [] ),
		loading = open && options.length === 0 && ! loaded,
		optionsCache = React.useRef( {} );

	React.useEffect( () => {
		if ( prevFormId !== false && formId !== prevFormId ) {

			if ( optionsCache.current[ formId ] !== undefined ) {
				setOptions( optionsCache.current[ formId ] );
			} else {
				setOptions( [] );
				setLoaded( false );
			}
		}

		setPrevFormId( formId );
	}, [ formId ] );

	React.useEffect( () => {
		let active = true;

		if ( ! loading ) {
			return undefined;
		}

		(async () => {
			try {
				const loadedOptions = await api.post( 'get_forms_integration_form_fields', {
					integration,
					form_id: formId,
					field_type: fieldType,
				} );

				if ( active ) {
					setOptions( loadedOptions );
					optionsCache.current[ formId ] = loadedOptions;
				}
			} catch ( error ) {
				// TODO: Add error handling in all places. Toast (next release).
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
			placeholder={sprintf( __( 'Select a form %s filed', 'collect-reviews' ), fieldType )}
			noOptionsText={sprintf( __( 'No %s fields found in the selected form', 'collect-reviews' ), fieldType )}
			{...rest}
		/>
	);
}
