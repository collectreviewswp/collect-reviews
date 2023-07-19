import React, {useState} from 'react';

import {Field} from "formik";
import {DesktopDateTimePicker as MuiDateTimePicker} from "formik-mui-x-date-pickers";

const DateTimePicker = ( props ) => {
	const [ open, setOpen ] = useState( false );

	return (
		<Field
			component={MuiDateTimePicker}
			open={open}
			onOpen={() => setOpen( true )}
			onClose={() => setOpen( false )}
			slotProps={{
				textField: {
					readOnly: true,
				},
				field: {
					onClick: ( e ) => setOpen( true )
				}
			}}
			{...props}
		/>
	);
}

export default DateTimePicker;
