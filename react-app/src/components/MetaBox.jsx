import React from 'react';

import Accordion from "@mui/material/Accordion";
import AccordionDetails from "@mui/material/AccordionDetails";
import AccordionSummary from "@mui/material/AccordionSummary";
import Typography from "@mui/material/Typography";

const MetaBox = ( {title, children} ) => {
	return (
		<Accordion
			expanded={true}
		>
			{title &&
				<AccordionSummary sx={{
					borderBottom: '1px solid #eee',
					paddingX: 3,
					cursor: 'default'
				}}>
					<Typography sx={{fontWeight: 500}}>{title}</Typography>
				</AccordionSummary>
			}
			<AccordionDetails sx={{padding: 3}}>{children}</AccordionDetails>
		</Accordion>
	);
}

export default MetaBox;
