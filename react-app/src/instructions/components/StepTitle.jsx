import React from 'react';

import Typography from "@mui/material/Typography";

const StepTitle = ( {children} ) => {
	return (
		<Typography variant="h4" sx={{fontSize: 17, fontWeight:500, mb:1}}>{children}</Typography>
	);
}

export default StepTitle;
