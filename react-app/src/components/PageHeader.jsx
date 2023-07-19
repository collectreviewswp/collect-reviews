import React from 'react';

import Box from "@mui/material/Box";

const PageHeader = ({children, sx = {}}) => {
	return (
		<Box sx={{
			marginBottom: 3,
			...sx
		}}>
			{children}
		</Box>
	);
}

export default PageHeader;
