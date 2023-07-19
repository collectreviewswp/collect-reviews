import React from 'react';

import Typography from "@mui/material/Typography";
import Alert from "@mui/material/Alert";

import Step from "../components/Step";
import StepTitle from "../components/StepTitle";
import StepDescription from "../components/StepDescription";
import Image from "../components/Image";

import {__, sprintf} from "@wordpress/i18n";

const Google = () => {
	return (
		<>
			<Alert severity="warning" sx={{mb: 2}}>
				{__( 'Ensure you’ve signed in with your Google Business Profile account.', 'collect-reviews' )}
			</Alert>
			<Step>
				<StepTitle>
					{__( 'Step 1', 'collect-reviews' )}
				</StepTitle>
				<StepDescription>
					<Typography dangerouslySetInnerHTML={{__html: sprintf( __( 'Go to your <a href="%s" target="_blank" rel="noopener noreferrer">Google Business Profile account</a>.', 'collect-reviews' ), 'https://business.google.com/' )}}/>
				</StepDescription>
			</Step>

			<Step>
				<StepTitle>
					{__( 'Step 2', 'collect-reviews' )}
				</StepTitle>
				<StepDescription>
					<Typography>
						{__( 'Scroll down to see options from your Google Business Profile. Click "Ask for reviews".', 'collect-reviews' )}
					</Typography>

					<Image src="https://collectreviewswp.com/instructions/google-ask-review.jpg"/>
				</StepDescription>
			</Step>

			<Step>
				<StepTitle>
					{__( 'Step 3', 'collect-reviews' )}
				</StepTitle>
				<StepDescription>
					<Typography>
						{__( 'In the popup, copy the provided URL and paste it into the "Review URL" field.', 'collect-reviews' )}
					</Typography>

					<Image src="https://collectreviewswp.com/instructions/google-review-link.jpg"/>
				</StepDescription>
			</Step>
		</>
	);
}

export default Google;
