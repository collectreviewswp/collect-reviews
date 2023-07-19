import React from 'react';

import Typography from "@mui/material/Typography";
import Alert from "@mui/material/Alert";

import Step from "../components/Step";
import StepTitle from "../components/StepTitle";
import StepDescription from "../components/StepDescription";

import {__} from "@wordpress/i18n";

const Custom = () => {
	return (
		<>
			<Alert severity="warning" sx={{mb: 2}}>
				{__( 'This instruction suits most platforms, but some can have different behavior.', 'collect-reviews' )}
			</Alert>

			<Step>
				<StepTitle>
					{__( 'Step 1', 'collect-reviews' )}
				</StepTitle>
				<StepDescription>
					<Typography>
						{__( 'Go to a review submission page of your company on the desired platform.', 'collect-reviews' )}
					</Typography>
				</StepDescription>
			</Step>

			<Step>
				<StepTitle>
					{__( 'Step 2', 'collect-reviews' )}
				</StepTitle>
				<StepDescription>
					<Typography>
						{__( 'Copy the current URL from the browser address bar and paste it into the "Review URL" field.', 'collect-reviews' )}
					</Typography>
				</StepDescription>
			</Step>
		</>
	);
}

export default Custom;
