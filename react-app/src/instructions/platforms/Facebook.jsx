import React from 'react';

import Typography from "@mui/material/Typography";

import Step from "../components/Step";
import StepTitle from "../components/StepTitle";
import StepDescription from "../components/StepDescription";
import Image from "../components/Image";

import {__, sprintf} from "@wordpress/i18n";

import askReviewImg from 'assets/images/instructions/facebook-ask-review.jpg';
import reviewLinkImg from 'assets/images/instructions/facebook-review-link.jpg';

const Facebook = () => {
	return (
		<>
			<Step>
				<StepTitle>
					{__( 'Step 1', 'collect-reviews' )}
				</StepTitle>
				<StepDescription>
					<Typography dangerouslySetInnerHTML={{__html: sprintf( __( 'Go to your <a href="%s" target="_blank" rel="noopener noreferrer">Facebook</a> company page.', 'collect-reviews' ), 'https://www.facebook.com/' )}}/>
				</StepDescription>
			</Step>

			<Step>
				<StepTitle>
					{__( 'Step 2', 'collect-reviews' )}
				</StepTitle>
				<StepDescription>
					<Typography>
						{__( 'Click the "Reviews" menu item.', 'collect-reviews' )}
					</Typography>

					<Image src={askReviewImg}/>
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

					<Image src={reviewLinkImg}/>
				</StepDescription>
			</Step>
		</>
	);
}

export default Facebook;
