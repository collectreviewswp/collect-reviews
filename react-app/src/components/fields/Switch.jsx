import React from 'react';

import {Field, useField} from "formik";
import {Switch as MuiSwitch} from "formik-mui";
import FormControlLabel from "@mui/material/FormControlLabel";

import {__} from "@wordpress/i18n";

const Switch = ( props ) => {
	const [ field ] = useField( props.name );

	return (
		<FormControlLabel
			label={field.value ?
				__( 'On', 'collect-reviews' ) :
				__( 'Off', 'collect-reviews' )
			}
			control={<Field component={MuiSwitch} type="checkbox" {...props}/>}
		/>
	);
}

export default Switch;
