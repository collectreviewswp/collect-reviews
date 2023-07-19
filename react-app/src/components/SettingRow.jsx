import React from 'react';
import Box from "@mui/material/Box";

import SettingLabel from "./SettingLabel";
import Row from "./Row";

import { useTheme } from '@mui/material/styles';
import {grey} from "@mui/material/colors";
import {__} from "@wordpress/i18n";

const SettingRow = ( {label, labelFor, field, description, optional = false} ) => {

	const theme = useTheme();

	return (
		<Row
			sx={{
				display: 'flex',
				[theme.breakpoints.down('sm')]: {
					flexDirection: 'column',
				},
			}}
		>
			<Box
				sx={{
					marginRight: 2,
					flex: '0 0 200px',
					paddingTop: '7px',
					[theme.breakpoints.down('sm')]: {
						flexBasis: 'auto',
						marginRight: 0,
						marginBottom: 1,
						paddingTop: 0
					},
				}}
			>
				{label && <SettingLabel htmlFor={labelFor} sx={{textTransform: 'capitalize'}}>{label}</SettingLabel>}
				{optional && <Box sx={{mt: '2px', fontSize: '12px', color: grey[ 600 ]}}>{__( 'Optional', 'collect-reviews' )}</Box>}
			</Box>
			<Box
				sx={{
					display: 'flex',
					flexDirection: 'column',
					flexGrow: 1,
				}}
			>
				<Box
					sx={{
						display: 'flex',
						alignItems: 'center',
						minHeight: '40px',
						'.MuiFormControl-root': {
							width: '100%',
						},
						'.MuiInputBase-root, .MuiAutocomplete-root': {
							maxWidth: '600px',
						}
					}}
				>
					{field && field}
				</Box>
				{description &&
					<Box sx={{
						fontSize: '14px',
						color: grey[ 800 ],
						mt: 1
					}}>
						{description}
					</Box>
				}
			</Box>
		</Row>
	);
}

export default SettingRow;
