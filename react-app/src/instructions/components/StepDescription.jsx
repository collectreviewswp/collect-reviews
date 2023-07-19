import React from 'react';

import Box from "@mui/material/Box";

const StepDescription = ( {children, ...rest} ) => {
	return (
		<Box {...rest}>{children}</Box>
	);
}

export default StepDescription;
