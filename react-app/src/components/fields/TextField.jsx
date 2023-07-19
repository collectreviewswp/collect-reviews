import React from 'react';

import {Field} from "formik";
import {TextField as MuiTextField} from "formik-mui";
const TextField = ( props ) => {
	return (
		<Field
			component={MuiTextField}
			{...props}
		/>
	);
}

export default TextField;
