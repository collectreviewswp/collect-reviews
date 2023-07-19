import React from 'react';
import {Form, Formik} from "formik";
import MenuItem from "@mui/material/MenuItem";
import Button from "@mui/lab/LoadingButton";

import PageHeader from "components/PageHeader";
import PageTitle from "components/PageTitle";
import MetaBox from "components/MetaBox";
import SettingRow from "components/SettingRow";
import Select from "components/fields/Select";
import TextField from "components/fields/TextField";
import PlatformInstructionModalLink from "components/integrations/PlatformInstructionModalLink";

import {__, _n, sprintf} from "@wordpress/i18n";
import {animateScroll as scroll} from "react-scroll";
import {useNavigate} from "react-router-dom";

import yup from "helpers/validation";
import api from "helpers/api";
import data from "helpers/data";
import {getPlatformName} from "helpers/platform";
import useNotices from "store/notices"

const NewReviewRequest = () => {
	const navigate = useNavigate(),
		notices = useNotices();

	const formSettings = {
		initialValues: {
			email: '',
			first_name: '',
			last_name: '',
			delay: 0,
			platform_type: 'google',
			platform_name: '',
			platform_review_url: '',
		},
		validationSchema: yup.object().shape( {
			email: yup.string()
				.email()
				.required(),
			first_name: yup.string(),
			last_name: yup.string(),
			platform_type: yup.string()
				.required(),
			platform_name: yup.string()
				.when( "platform_type", {
					is: v => v === 'custom',
					then: s => s.required()
				} ),
			platform_review_url: yup.string()
				.url()
				.required(),
		} ),
		onSubmit: async ( values, {setSubmitting} ) => {
			try {
				await api.post( 'create_review_request', values );

				// Go to the list of review requests.
				navigate( "/" );

				// TODO: Refactor (next release).
				// We need timeout to avoid notices to be cleared after the navigation.
				setTimeout( () => {
					notices.add( __( 'New review request created successfully.', 'collect-reviews' ), 'success' );
				}, 100 );
			} catch ( error ) {
				if ( error instanceof Error ) {
					notices.add( __( 'Something goes wrong. Check browser\'s JavaScript console to identify the issue and reach out to plugin\'s support.', 'collect-reviews' ), 'error' );
					console.error( error );
				} else {
					notices.add( error, 'error' );
				}

				setSubmitting( false );
			}

			scroll.scrollToTop();
		}
	};

	return (
		<>
			<PageHeader>
				<PageTitle>{__( 'New Review Request', 'collect-reviews' )}</PageTitle>
			</PageHeader>

			<Formik {...formSettings}>
				{( {values, isSubmitting, setFieldValue, setFieldTouched} ) => (
					<Form>
						<MetaBox>
							<SettingRow
								label={__( 'Recipient\'s Email Address', 'collect-reviews' )}
								labelFor="email"
								description={__( 'A review request email will be sent to the defined email address.', 'collect-reviews' )}
								field={
									<TextField
										name="email"
										id="email"
									/>
								}
							/>

							<SettingRow
								label={__( 'Recipient\'s First Name', 'collect-reviews' )}
								labelFor="first-name"
								optional
								description={__( 'Fill in this field if you want to use the customer\'s first name in the review request email.', 'collect-reviews' )}
								field={
									<TextField
										name="first_name"
										id="first-name"
									/>
								}
							/>

							<SettingRow
								label={__( 'Recipient\'s Last Name', 'collect-reviews' )}
								labelFor="last-name"
								optional
								description={__( 'Fill in this field if you want to use the customer\'s last name in the review request email.', 'collect-reviews' )}
								field={
									<TextField
										name="last_name"
										id="last-name"
									/>
								}
							/>

							<SettingRow
								label={__( 'Delay', 'collect-reviews' )}
								labelFor="delay"
								description={__( 'The delay before the review request email will be sent after the review request creation.', 'collect-reviews' )}
								field={
									<Select
										name="delay"
										id="delay"
									>
										<MenuItem value={0}>{__( 'send immediately', 'collect-reviews' )}</MenuItem>
										{Array.apply( null, Array( 23 ) ).map( ( v, i ) =>
											<MenuItem key={i} value={(i + 1) * 60 * 60}>{sprintf( _n( 'in %s hour', 'in %s hours', i + 1, 'collect-reviews' ), i + 1 )}</MenuItem>
										)}
										{Array.apply( null, Array( 31 ) ).map( ( v, i ) =>
											<MenuItem key={i + 24} value={(i + 1) * 60 * 60 * 24}>{sprintf( _n( 'in %s day', 'in %s days', i + 1, 'collect-reviews' ), i + 1 )}</MenuItem>
										)}
										{Array.apply( null, Array( 11 ) ).map( ( v, i ) =>
											<MenuItem key={i + 24 + 31} value={(i + 2) * 31 * 60 * 60 * 24}>{sprintf( __( 'in %s months', 'collect-reviews' ), i + 2 )}</MenuItem>
										)}
									</Select>
								}
							/>

							<SettingRow
								label={__( 'Platform', 'collect-reviews' )}
								labelFor="platform-type"
								description={
									<>
										{__( 'Service or platform where you want to collect reviews from your customers.', 'collect-reviews' )}
										<br/>
										{__( 'If you haven\'t found your desired platform, you can still select the "custom" option and enter the URL of any review platform.', 'collect-reviews' )}
									</>
								}
								field={
									<Select
										name="platform_type"
										id="platform-type"
										onChange={event => {
											setFieldValue( 'platform_name', event.target.value !== 'custom' ? getPlatformName( event.target.value ) : '' );
											setFieldTouched( 'platform_name', false );
										}}
									>
										<MenuItem value="custom">{__( 'custom', 'collect-reviews' )}</MenuItem>

										{(data.config?.platforms || []).map( ( {slug, name} ) =>
											<MenuItem key={slug} value={slug}>{name}</MenuItem>
										)}
									</Select>
								}
							/>

							{values.platform_type === 'custom' &&
								<SettingRow
									label={__( 'Platform Name', 'collect-reviews' )}
									labelFor="platform-name"
									description={__( 'It\'s just a label that will be displayed to the customer while submitting the review.', 'collect-reviews' )}
									field={
										<TextField
											name="platform_name"
											id="platform-name"
										/>
									}
								/>
							}

							<SettingRow
								label={__( 'Review URL', 'collect-reviews' )}
								labelFor="platform-review-url"
								description={
									<>
										{__( 'The URL of the page where you want to send your customers to leave a review.', 'collect-reviews' )}
										<br/>
										<PlatformInstructionModalLink platform={values.platform_type}/>
									</>
								}
								field={
									<TextField
										name="platform_review_url"
										id="platform-review-url"
									/>
								}
							/>
						</MetaBox>

						<Button loading={isSubmitting} type="submit" variant="contained" sx={{mt: 2}}>
							<span>{__( 'Create review request', 'collect-reviews' )}</span>
						</Button>
					</Form>
				)}
			</Formik>
		</>
	)
}

export default NewReviewRequest;
