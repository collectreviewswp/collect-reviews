import * as React from 'react';
import Button from '@mui/material/Button';
import DialogTitle from '@mui/material/DialogTitle';
import DialogActions from '@mui/material/DialogActions';
import Dialog from '@mui/material/Dialog';
import DialogContent from "@mui/material/DialogContent";
import {__, sprintf} from "@wordpress/i18n";

export default function ConfirmationModal( {open, action, description, onConfirm, onCancel, ...rest} ) {

	return (
		<Dialog
			sx={{'& .MuiDialog-paper': {width: '80%', maxHeight: 435}}}
			maxWidth="xs"
			open={open}
			{...rest}
		>
			<DialogTitle>
				{sprintf( __( 'Are you sura that you want to %s?', 'collect-reviews' ), action )}
			</DialogTitle>
			{description && <DialogContent sx={{fontSize: 14}}>{description}</DialogContent>}
			<DialogActions>
				<Button onClick={() => onCancel()} color="error">No</Button>
				<Button autoFocus onClick={() => onConfirm()} color="success">Yes</Button>
			</DialogActions>
		</Dialog>
	);
}

