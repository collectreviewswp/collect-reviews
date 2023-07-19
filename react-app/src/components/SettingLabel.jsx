import React from 'react';
import Typography from "@mui/material/Typography";

const SettingLabel = ( {children, sx = {}, ...rest} ) => {
	return (
		<Typography
			component="label"
			sx={{
				fontWeight: 500,
				fontSize: '15px',
				...sx
			}}
			{...rest}
		>
			{children}
		</Typography>
	);
}

export default SettingLabel;
