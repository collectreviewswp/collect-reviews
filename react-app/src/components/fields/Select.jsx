import React from 'react';

import {Field} from "formik";
import {Select as MuiSelect} from "formik-mui";

const Select = ( props ) => {
	return (
		<Field
			component={MuiSelect}
			{...props}
		/>
	);
}

export default Select;
