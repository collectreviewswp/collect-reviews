import React from 'react';

import Box from "@mui/material/Box";

const Image = ( props ) => {
	return <Box
		component="img"
		sx={{
			display: 'block',
			width: '100%',
			height: 'auto',
			border: '1px solid',
			borderColor: 'border.main',
			mt:1
		}}
		width="552"
		{...props}
	/>;
}

export default Image;
