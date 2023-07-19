import React from "react";

import {Link as RouterLink} from 'react-router-dom';
import Link from "@mui/material/Link";
import Button from '@mui/lab/LoadingButton';
import MenuItem from "@mui/material/MenuItem";
import KeyboardBackspaceIcon from '@mui/icons-material/KeyboardBackspace';
import {Formik, Form, FieldArray} from 'formik';

import PageTitle from "components/PageTitle";
import PageHeader from "components/PageHeader";
import TextField from "components/fields/TextField";
import Select from "components/fields/Select";
import Switch from "components/fields/Switch";
import SettingRow from "components/SettingRow";
import MetaBox from "components/MetaBox";
import PlatformInstructionModalLink from "components/integrations/PlatformInstructionModalLink";

import {__, _n, sprintf} from "@wordpress/i18n";
import {animateScroll as scroll} from 'react-scroll';

import useNotices from "store/notices";
import api from "helpers/api";
import data from "helpers/data";
import yup from 'helpers/validation';
import {getPlatformName} from "../../../../helpers/platform";

const WooCommerce = () => {

	const notices = useNotices();

	let initialValues = data.options?.integrations?.woocommerce || {
		enabled: true,
		triggers: []
	};

	if ( ! initialValues?.triggers || initialValues.triggers.length === 0 ) {
		initialValues.triggers = [
			{
				order_status: 'completed',
				review_request_delay: 14 * 24 * 60 * 60,
				platforms: []
			}
		]
	}

	initialValues.triggers = initialValues.triggers.map( trigger => ({
		...trigger,
		platforms: trigger?.platforms && trigger.platforms.length > 0 ?
			trigger.platforms :
			[
				{
					type: 'google',
					name: '',
					review_url: '',
				}
			]
	}) );

	const formSettings = {
		initialValues,
		validationSchema: yup.object().shape( {
			triggers: yup.array().of(
				yup.object().shape( {
					order_status: yup.string()
						.required(),
					platforms: yup.array().of(
						yup.object().shape( {
							type: yup.string()
								.required(),
							name: yup.string()
								.when( "type", {
									is: v => v === 'custom',
									then: s => s.required()
								} ),
							review_url: yup.string()
								.url()
								.required(),
						} )
					)
				} )
			),
		} ),
		onSubmit: async ( values, {setSubmitting, setValues} ) => {

			try {
				const updatedValues = await api.saveSettings( {
					integrations: {
						woocommerce: values
					}
				} );

				setValues( updatedValues.integrations.woocommerce || {} );

				// Update global options store.
				data.options = updatedValues;
				data.integrations = data.integrations.map( integration => {
					if ( integration.slug === 'woocommerce' ) {
						integration.is_configured = true;
						integration.is_enabled = values.enabled;
					}

					return integration;
				} );

				notices.add( __( 'Settings saved successfully.', 'collect-reviews' ), 'success' );
			} catch ( error ) {
				if ( error instanceof Error ) {
					notices.add( __( 'Something goes wrong. Check browser\'s JavaScript console to identify the issue and reach out to plugin\'s support.', 'collect-reviews' ), 'error' );
					console.error( error );
				} else {
					notices.add( error, 'error' );
				}
			}

			setSubmitting( false );
			scroll.scrollToTop();
		},
	};

	const orderStatuses = data.integrations?.find( i => i.slug === 'woocommerce' )?.order_statuses || {};

	return (
		<>
			<PageHeader>
				<PageTitle>{__( 'WooCommerce', 'collect-reviews' )}</PageTitle>

				<Link
					component={RouterLink}
					to="/"
					sx={{display: 'inline-flex', alignItems: 'center', mt: 1}}
					onClick={() => window.scrollTo( 0, 0 )}
				>
					<KeyboardBackspaceIcon fontSize="small" sx={{mr: 1}}/>
					{__( 'Back', 'collect-reviews' )}
				</Link>
			</PageHeader>

			<Formik {...formSettings}>
				{( {values, isSubmitting, setFieldValue, setFieldTouched} ) => (
					<Form>
						<MetaBox>
							<SettingRow
								label={__( 'Enabled', 'collect-reviews' )}
								labelFor="enabled"
								field={
									<Switch
										name="enabled"
										id="enabled"
									/>
								}
							/>

							<FieldArray name="triggers">
								{() => (values.triggers || []).map( ( v, triggerKey ) =>
									<React.Fragment key={triggerKey}>
										<SettingRow
											label={__( 'Trigger', 'collect-reviews' )}
											labelFor="order-status"
											description={__( 'The order status that will create the review request.', 'collect-reviews' )}
											field={
												<Select
													name={`triggers.${triggerKey}.order_status`}
													id="order-status"
												>
													{Object.entries( orderStatuses ).map( ( [ value, label ] ) =>
														<MenuItem key={value} value={value}>{label}</MenuItem>
													)}
												</Select>
											}
										/>

										<SettingRow
											label={__( 'Delay', 'collect-reviews' )}
											labelFor="review-request-delay"
											description={__( 'The delay before the review request email will be sent after the purchase. It can be useful if you prefer to wait until the order has been delivered or the product has been used for some time before sending the review request email.', 'collect-reviews' )}
											field={
												<Select
													name={`triggers.${triggerKey}.review_request_delay`}
													id="review-request-delay"
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

										<FieldArray name={`triggers.${triggerKey}.platforms`}>
											{() => values.triggers[ triggerKey ].platforms.map( ( v, platformKey ) =>
												<React.Fragment key={platformKey}>
													<SettingRow
														label={__( 'Platform', 'collect-reviews' )}
														labelFor="review-request-platform"
														description={
															<>
																{__( 'Service or platform where you want to collect reviews from your customers.', 'collect-reviews' )}
																<br/>
																{__( 'If you haven\'t found your desired platform, you can still select the "custom" option and enter the URL of any review platform.', 'collect-reviews' )}
															</>
														}
														field={
															<Select
																name={`triggers.${triggerKey}.platforms.${platformKey}.type`}
																id="review-request-platform"
																onChange={event => {
																	setFieldValue( `triggers.${triggerKey}.platforms.${platformKey}.name`, event.target.value !== 'custom' ? getPlatformName( event.target.value ) : '' );
																	setFieldTouched( `triggers.${triggerKey}.platforms.${platformKey}.name`, false );
																}}
															>
																<MenuItem value="custom">{__( 'custom', 'collect-reviews' )}</MenuItem>

																{(data.config?.platforms || []).map( ( {slug, name} ) =>
																	<MenuItem key={slug} value={slug}>{name}</MenuItem>
																)}
															</Select>
														}
													/>

													{values.triggers[ triggerKey ].platforms[ platformKey ].type === 'custom' &&
														<SettingRow
															label={__( 'Platform Name', 'collect-reviews' )}
															labelFor="review-request-platform-name"
															description={__( 'It\'s just a label that will be displayed to the customer while submitting the review.', 'collect-reviews' )}
															field={
																<TextField
																	name={`triggers.${triggerKey}.platforms.${platformKey}.name`}
																	id="review-request-platform-name"
																/>
															}
														/>
													}

													<SettingRow
														label={__( 'Review URL', 'collect-reviews' )}
														labelFor="review-request-platform-review-url"
														description={
															<>
																{__( 'The URL of the page where you want to send your customers to leave a review.', 'collect-reviews' )}
																<br/>
																<PlatformInstructionModalLink platform={values.triggers[ triggerKey ].platforms[ platformKey ].type}/>
															</>
														}
														field={
															<TextField
																name={`triggers.${triggerKey}.platforms.${platformKey}.review_url`}
																id="review-request-platform-review-url"
															/>
														}
													/>
												</React.Fragment>
											)}
										</FieldArray>
									</React.Fragment>
								)}
							</FieldArray>
						</MetaBox>

						<Button loading={isSubmitting} type="submit" variant="contained" sx={{mt: 2}}>
							<span>{__( 'Save settings', 'collect-reviews' )}</span>
						</Button>
					</Form>
				)}
			</Formik>
		</>
	);
}

export default WooCommerce;
