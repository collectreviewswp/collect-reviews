import React from 'react';

import Typography from "@mui/material/Typography";

import Step from "../components/Step";
import StepTitle from "../components/StepTitle";
import StepDescription from "../components/StepDescription";
import Image from "../components/Image";

import {__, sprintf} from "@wordpress/i18n";

const Trustpilot = () => {
	return (
		<>
			<Step>
				<StepTitle>
					{__( 'Step 1', 'collect-reviews' )}
				</StepTitle>
				<StepDescription>
					<Typography dangerouslySetInnerHTML={{__html: sprintf( __( 'Go to your <a href="%s" target="_blank" rel="noopener noreferrer">Trustpilot</a> organization page.', 'collect-reviews' ), 'https://www.trustpilot.com/' )}}/>
				</StepDescription>
			</Step>

			<Step>
				<StepTitle>
					{__( 'Step 2', 'collect-reviews' )}
				</StepTitle>
				<StepDescription>
					<Typography>
						{__( 'Click the "Write a review" link.', 'collect-reviews' )}
					</Typography>

					<Image src="https://collectreviewswp.com/instructions/trustpilot-ask-review.jpg"/>
				</StepDescription>
			</Step>

			<Step>
				<StepTitle>
					{__( 'Step 3', 'collect-reviews' )}
				</StepTitle>
				<StepDescription>
					<Typography>
						{__( 'Copy the current URL from the browser address bar and paste it into the "Review URL" field.', 'collect-reviews' )}
					</Typography>

					<Image src="https://collectreviewswp.com/instructions/trustpilot-review-link.jpg"/>
				</StepDescription>
			</Step>
		</>
	);
}

export default Trustpilot;
