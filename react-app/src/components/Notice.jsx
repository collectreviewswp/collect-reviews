import React from "react";

import Alert from "@mui/material/Alert";
import Collapse from "@mui/material/Collapse";
import IconButton from "@mui/material/IconButton";
import CloseIcon from '@mui/icons-material/Close';

const Notice = ( {onClose = () => null, sx, ...rest} ) => {
	const [ open, setOpen ] = React.useState( true );

	return (
		<Collapse in={open} onExited={onClose} sx={sx}>
			<Alert
				action={
					<IconButton
						aria-label="close"
						color="inherit"
						size="small"
						onClick={() => setOpen( false )}
					>
						<CloseIcon fontSize="inherit"/>
					</IconButton>
				}
				{...rest}
			/>
		</Collapse>
	);
}

export default Notice;
