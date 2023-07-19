import React from 'react';

import Typography from "@mui/material/Typography";

const PageTitle = ({children}) => {
	return (
		<Typography sx={{fontSize: 20, fontWeight: 500}}>
			{children}
		</Typography>
	);
}
export default PageTitle;
