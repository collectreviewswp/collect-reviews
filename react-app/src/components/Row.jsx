import React from 'react';
import Box from "@mui/material/Box";

const Row = ( {children, sx = {}, ...rest} ) => {
	return (
		<Box
			sx={{
				'& + .row': {
					borderTop: '1px solid #eee',
					marginTop: 3,
					paddingTop: 3,
				},
				...sx
			}}
			className="row"
			{...rest}
		>
			{children}
		</Box>
	);
}

export default Row;
