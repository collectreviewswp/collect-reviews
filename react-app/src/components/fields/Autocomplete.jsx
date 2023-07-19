import React from 'react';

import TextField from '@mui/material/TextField';
import CircularProgress from '@mui/material/CircularProgress';
import {Field, useField} from "formik";
import {Autocomplete as MuiAutocomplete} from 'formik-mui';

import {__} from "@wordpress/i18n";

const Autocomplete = ( {loading, placeholder, ...props} ) => {
	const [ , meta ] = useField( props.name );

	return (
		<Field
			component={MuiAutocomplete}
			sx={{width: '100%'}}
			loading={loading}
			loadingText={__( 'Loading...', 'collect-reviews' )}
			noOptionsText={__( 'No options', 'collect-reviews' )}
			renderInput={( params ) => (
				<TextField
					{...params}
					InputProps={{
						...params.InputProps,
						endAdornment: (
							<React.Fragment>
								{loading ? <CircularProgress color="inherit" size={20}/> : null}
								{params.InputProps.endAdornment}
							</React.Fragment>
						),
					}}
					placeholder={placeholder}
					error={meta.touched && !! meta.error}
					helperText={meta.touched && meta.error}
				/>
			)}
			{...props}
		/>
	);
}

export default Autocomplete;
