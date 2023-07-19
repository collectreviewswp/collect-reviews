import React from 'react';

import Box from "@mui/material/Box";

const Step = ( {children} ) => {
	return (
		<Box sx={{
			'& + &': {
				marginTop: 3,
			}
		}}>
			{children}
		</Box>
	);
}

export default Step;
