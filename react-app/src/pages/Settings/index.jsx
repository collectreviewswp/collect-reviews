import React, {useEffect, useRef, useState, Suspense} from "react";

import {Formik, Form} from 'formik';
import {Link as RouterLink} from "react-router-dom";
import Card from "@mui/material/Card";
import CardMedia from "@mui/material/CardMedia";
import CardActions from "@mui/material/CardActions";
import CardContent from "@mui/material/CardContent";
import Grid from "@mui/material/Grid";
import Input from "@mui/material/Input";
import CircularProgress from "@mui/material/CircularProgress";
import TabContext from "@mui/lab/TabContext";
import Box from "@mui/material/Box";
import TabList from "@mui/lab/TabList";
import Tab from "@mui/material/Tab";
import Button from '@mui/lab/LoadingButton';
import MenuItem from "@mui/material/MenuItem";
import Typography from "@mui/material/Typography";
import Stack from "@mui/material/Stack";
import Alert from "@mui/material/Alert";

import PageHeader from "components/PageHeader";
import PageTitle from "components/PageTitle";
import TabPanel from "components/mui/TabPanel";
import Row from "components/Row";
import SettingRow from "components/SettingRow";
import MetaBox from "components/MetaBox";
import SettingLabel from "components/SettingLabel";
import TextField from "components/fields/TextField";
import Select from "components/fields/Select";

import {__, _n, sprintf} from "@wordpress/i18n";
import {animateScroll as scroll} from "react-scroll";
import {grey} from "@mui/material/colors";
import { lazy } from '@loadable/component'

import useNotices from "store/notices";
import data from "helpers/data";
import api from "helpers/api";
import yup from 'helpers/validation';

const Editor = lazy( () => import('components/TinyMceEditor') );

export default function Settings() {

	const notices = useNotices(),
		formRef = useRef( null );

	const initialValues = {
		review_request_email: {
			from_name: '',
			from_email: '',
			subject: '',
			logo: false,
			content: '',
			footer_text: '',
			...data.options?.review_request_email || {},
		},
		review_request: {
			frequency: -1,
			...data.options?.review_request || {},
		},
		negative_review: {
			email: '',
			threshold: 3,
			...data.options?.negative_review || {},
		}
	};

	const formSettings = {
		initialValues,
		validationSchema: yup.object().shape( {
			review_request_email: yup.object().shape( {
				from_name: yup.string().required(),
				from_email: yup.string().required().email(),
				subject: yup.string().required(),
			} ),
			negative_review: yup.object().shape( {
				email: yup.string().required().email(),
				threshold: yup.number().required().integer().min( 0 ).max( 5 ),
			} ),
		} ),
		onSubmit: async ( values, {setSubmitting, setValues} ) => {
			try {
				const updatedValues = await api.saveSettings( values );

				setValues( {
					review_request_email: updatedValues.review_request_email || {},
					review_request: updatedValues.review_request || {},
					negative_review: updatedValues.negative_review || {},
				} );

				// Update global options store.
				data.options = updatedValues;

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
		}
	};

	const [ emailActiveTab, setEmailActiveTab ] = useState( 'preview' ),
		emailIFrameRef = useRef( null ),
		emailLogoFileInput = useRef( null ),
		[ lastEmailData, setLastEmailData ] = useState(),
		[ emailPreviewLoading, setEmailPreviewLoading ] = useState( false );

	const handleEmailTabChange = ( event, newValue ) => {
		setEmailActiveTab( newValue );
	};

	// TODO: Prevent double loading. Maybe cancel prev request (next release).
	const updateEmailPreview = async ( emailData ) => {

		if ( ! emailIFrameRef.current ) {
			return;
		}

		const iframeDocument = emailIFrameRef.current.contentDocument || emailIFrameRef.current.contentWindow.document,
			iframeInner = iframeDocument.documentElement;

		// Set iframe height.
		const iframeBody = iframeInner.querySelector( 'table.body' );

		if ( iframeBody ) {
			emailIFrameRef.current.style.height = iframeBody.offsetHeight + 'px';
		}

		if ( JSON.stringify( lastEmailData ) === JSON.stringify( emailData ) ) {
			return;
		}

		setEmailPreviewLoading( true );

		try {
			iframeInner.innerHTML = await api.post( 'get_review_request_email_preview', emailData );

			// Set iframe height after load.
			const iframeBody = iframeInner.querySelector( 'table.body' );
			emailIFrameRef.current.style.height = iframeBody.offsetHeight + 'px';

			const images = iframeInner.querySelectorAll( 'img' );
			let imagesLoaded = 0;
			images.forEach( image => {
				image.onload = () => {
					imagesLoaded++;
					if ( imagesLoaded === images.length ) {
						emailIFrameRef.current.style.height = iframeBody.offsetHeight + 'px';
					}
				}
			} );

			// Disable links.
			iframeInner.querySelectorAll( 'a' ).forEach( link => link.style.pointerEvents = 'none' );

			// Save last email data.
			setLastEmailData( emailData );
		} catch ( e ) {
		}

		setEmailPreviewLoading( false );
	}

	useEffect( () => {
		if ( emailActiveTab === 'preview' ) {
			updateEmailPreview( formRef.current.values.review_request_email );
		}
	}, [ emailActiveTab ] );

	return (
		<>
			<PageHeader>
				<PageTitle>{__( 'Settings', 'collect-reviews' )}</PageTitle>
			</PageHeader>

			<Formik {...formSettings} innerRef={formRef}>
				{( {values, isSubmitting, setFieldValue} ) => (
					<Form>
						<MetaBox title={__( 'Integrations', 'collect-reviews' )}>
							<Row>
								<Typography>
									{__( 'Integrations let you collect reviews automatically. For example, send a review request email to the customer after the purchase via WooCommerce or form submission via WPForms. By setting up integrations, you can save time and effort while gathering valuable feedback from your customers.', 'collect-reviews' )}
								</Typography>
							</Row>

							<Row>
								{data.integrations.filter( i => i.is_available ).length > 0 ?
									<Grid container spacing={2}>
										{data.integrations.filter( i => i.is_available ).map( integration =>
											<Grid item xs={4}>
												<Card sx={{
													display: 'flex',
													flexDirection: 'column',
													height: '100%',
												}}>
													<Box sx={{
														display: 'flex',
														height: 90,
														backgroundColor: 'rgba( 21, 101, 192, 0.1 )',
													}}>
														<CardMedia
															component="img"
															image={`${data.plugin_url}/assets/images/integrations/${integration.slug}.svg`}
															alt={integration.title}
															sx={{
																margin: 'auto',
																maxWidth: 220,
																maxHeight: 70
															}}
														/>
													</Box>
													<CardContent sx={{borderBottom: '1px solid ' + grey[ 300 ], px: 2, py: 1}}>
														<Typography component="div" sx={{fontSize: 16, fontWeight: 500}}>
															{integration.title}
														</Typography>
													</CardContent>
													<CardActions>
														<Button
															component={RouterLink}
															to={`integration/${integration.slug.replaceAll( '_', '-' )}`}
															size="small"
															variant="outlined"
															onClick={() => window.scrollTo( 0, 0 )}
														>
															{integration.is_configured ? __( 'Edit', 'collect-reviews' ) : __( 'Configure', 'collect-reviews' )}
														</Button>

														{integration.is_configured &&
															<Box sx={{display: 'flex', alignItems: 'center', ml: 'auto'}}>
																{integration.is_enabled ?
																	<>
																		<Box sx={{
																			backgroundColor: 'success.main',
																			width: 10,
																			height: 10,
																			mr: 1,
																			borderRadius: '50%',
																			boxShadow: '0 0 0 2px rgb(46 125 50 / 30%)'
																		}}/>
																		<Typography sx={{fontSize: 13, fontWeight: 500, color: 'success.main'}}>
																			{__( 'Active', 'collect-reviews' )}
																		</Typography>
																	</>
																	:
																	<>
																		<Box sx={{
																			backgroundColor: 'error.main',
																			width: 10,
																			height: 10,
																			mr: 1,
																			borderRadius: '50%',
																			boxShadow: '0 0 0 2px rgb(211 47 47 / 30%)'
																		}}/>
																		<Typography sx={{fontSize: 13, fontWeight: 500, color: 'error.main'}}>
																			{__( 'Inactive', 'collect-reviews' )}
																		</Typography>
																	</>
																}
															</Box>
														}
													</CardActions>
												</Card>
											</Grid>
										)}
									</Grid>
									:
									<Alert severity="warning" icon={false}>
										<Typography sx={{mb: 2}}>
											{__( 'Unfortunately, you don\'t have installed any plugin that allows you to collect reviews automatically, but you can always create review requests manually.', 'collect-reviews' )}
										</Typography>

										<Typography>
											{__( 'Here is the list of plugins that allows you to collect reviews automatically:', 'collect-reviews' )}
										</Typography>

										<ul style={{margin: '8px 0 0', paddingLeft: 15, fontSize: 16}}>
											{data.integrations.map( integration => <li>{integration.title}</li> )}
										</ul>

										<Button sx={{mt: 3}} href={`${data.review_requests_page_url}#/new`} variant="outlined">
											{__( 'Create review request manually', 'collect-reviews' )}
										</Button>
									</Alert>
								}
							</Row>
						</MetaBox>

						<MetaBox title={__( 'Review Request Email Settings', 'collect-reviews' )}>
							<Row>
								<Typography>
									{__( 'Email that will be sent to the customer to request a review.', 'collect-reviews' )}
								</Typography>
							</Row>

							<SettingRow
								label={__( 'Frequency', 'collect-reviews' )}
								labelFor="frequency"
								description={__( 'How often the review request email can be sent to the same customer automatically.', 'collect-reviews' )}
								field={
									<Select
										name="review_request.frequency"
										id="frequency"
									>
										<MenuItem value={-1}>{__( 'Only once', 'collect-reviews' )}</MenuItem>
										<MenuItem value={0}>{__( 'Each time (e.g. after each purchase of form submission)', 'collect-reviews' )}</MenuItem>
										{Array.apply( null, Array( 12 ) ).map( ( v, i ) =>
											<MenuItem key={i} value={(i + 1) * 30}>{sprintf( _n( 'No more than 1 email in %s month', 'No more than 1 email in %s months', i + 1, 'collect-reviews' ), i + 1 )}</MenuItem>
										)}
									</Select>
								}
							/>

							<SettingRow
								label={__( 'Sender Name', 'collect-reviews' )}
								labelFor="email-from-name"
								description={__( 'The name that the review request emails are sent from.', 'collect-reviews' )}
								field={
									<TextField
										name="review_request_email.from_name"
										id="email-from-name"
									/>
								}
							/>

							<SettingRow
								label={__( 'Sender Email', 'collect-reviews' )}
								labelFor="email-from-email"
								description={__( 'The email that the review request emails are sent from.', 'collect-reviews' )}
								field={
									<TextField
										name="review_request_email.from_email"
										id="email-from-email"
									/>
								}
							/>

							<SettingRow
								label={__( 'Email Subject', 'collect-reviews' )}
								labelFor="email-subject"
								field={
									<TextField
										name="review_request_email.subject"
										id="email-subject"
									/>
								}
							/>

							<SettingRow
								label={__( 'Email Logo', 'collect-reviews' )}
								labelFor="email-logo"
								field={
									<>
										<Stack direction="row" spacing={1}>
											<Button variant="outlined" onClick={() => emailLogoFileInput.current.click()}>
												{values.review_request_email.logo || values.review_request_email.logo_raw ?
													__( 'Change image', 'collect-reviews' ) :
													__( 'Choose image', 'collect-reviews' )
												}
											</Button>

											{(values.review_request_email.logo || values.review_request_email.logo_raw) &&
												<Button
													color="error"
													onClick={() => {
														emailLogoFileInput.current.value = '';
														setFieldValue( 'review_request_email.logo', false );
														setFieldValue( 'review_request_email.logo_raw', false );

														updateEmailPreview( {
															...values.review_request_email,
															logo: false,
															logo_raw: false,
														} );
													}}
												>
													{__( 'Delete', 'collect-reviews' )}
												</Button>
											}
										</Stack>

										<Input
											id="email-logo"
											inputRef={emailLogoFileInput}
											style={{display: 'none'}}
											inputProps={{
												type: 'file',
												name: '',
												accept: '.jpg, .jpeg, .png, .svg',
												onChange: ( event ) => {
													const file = event.currentTarget.files[ 0 ],
														image = new Image();

													if ( ! file ) return;

													image.src = URL.createObjectURL( file );

													// Resize the image
													image.onload = function() {
														let canvas = document.createElement( 'canvas' ),
															width = image.width,
															height = image.height,
															aspectRatio = width / height,
															maxWidth = 300 * 2,
															maxHeight = 100 * 2;

														if ( width > maxWidth ) {
															width = maxWidth;
															height = maxWidth / aspectRatio;
														}

														if ( height > maxHeight ) {
															width = maxHeight * aspectRatio;
															height = maxHeight;
														}

														canvas.width = width;
														canvas.height = height;
														canvas.getContext( '2d' ).drawImage( image, 0, 0, width, height );

														const logo_raw = {
															url: canvas.toDataURL( 'image/png' ),
															width,
															height,
														};

														setFieldValue( 'review_request_email.logo', false );
														setFieldValue( 'review_request_email.logo_raw', logo_raw );

														updateEmailPreview( {
															...values.review_request_email,
															logo: false,
															logo_raw,
														} );
													};
												},
											}}
										/>
									</>
								}
							/>

							<Row>
								<TabContext value={emailActiveTab}>
									<Box sx={{borderBottom: 1, borderColor: 'divider'}}>
										<TabList onChange={handleEmailTabChange}>
											<Tab label={__( 'Preview', 'collect-reviews' )} value="preview"/>
											<Tab label={__( 'Edit', 'collect-reviews' )} value="edit"/>
										</TabList>
									</Box>
									<TabPanel value="preview" sx={{position: 'relative', padding: '24px 0 0'}}>
										{emailPreviewLoading &&
											<Box sx={{
												position: "absolute",
												top: 0,
												bottom: 0,
												left: 0,
												right: 0,
												display: "flex",
												backgroundColor: 'overlay.light'
											}}>
												<CircularProgress sx={{margin: "auto"}}/>
											</Box>
										}

										<iframe src="javascript:void(0);" ref={emailIFrameRef} style={{border: 'none', width: '100%', height: 500}}></iframe>
									</TabPanel>
									<TabPanel value="edit" sx={{padding: '24px 0 0',}}>
										<Row>
											<Suspense
												fallback={(
													<Box sx={{
														width: '100%',
														height: 500,
														display: "flex",
														backgroundColor: 'overlay.light'
													}}>
														<CircularProgress sx={{margin: "auto"}}/>
													</Box>
												)}
											>
												<Editor
													value={values.review_request_email.content}
													onEditorChange={newValue => setFieldValue( 'review_request_email.content', newValue )}
													init={{
														height: 500,
														menubar: false,
														plugins: [
															'lists', 'link', 'preview', 'code',
														],
														toolbar: 'undo redo | fontsize |' +
															'bold italic underline forecolor | alignleft aligncenter ' +
															'alignright | bullist numlist | ' +
															' link | code',
														content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size: 16px; }',
														font_size_formats: '8px 10px 12px 14px 16px 18px 24px 36px',
													}}
												/>
											</Suspense>
										</Row>

										<Row>
											<SettingLabel htmlFor="email-footer-text" sx={{display: 'block', mb: 1}}>
												{__( 'Email footer text', 'collect-reviews' )}
											</SettingLabel>
											<TextField
												name="review_request_email.footer_text"
												id="email-footer-text"
												multiline
												fullWidth
											/>
										</Row>

									</TabPanel>
								</TabContext>
							</Row>
						</MetaBox>

						<MetaBox title={__( 'Negative Reviews', 'collect-reviews' )}>
							<Row>
								<Typography>
									{__( 'To prevent negative reviews on review platforms, if a customer chooses a negative rating in the review request email, they will be redirected to a feedback form instead of the review platform (Google, Facebook, etc.).', 'collect-reviews' )}
								</Typography>
							</Row>

							<SettingRow
								label={__( 'Negative Review Email', 'collect-reviews' )}
								labelFor="negative-review-email"
								description={__( 'Email address to which negative reviews will be sent.', 'collect-reviews' )}
								field={
									<TextField
										name="negative_review.email"
										id="negative-review-email"
									/>
								}
							/>

							<SettingRow
								label={__( 'Negative Review Threshold', 'collect-reviews' )}
								labelFor="negative-review-threshold"
								description={__( 'The number of stars that are considered as a negative rating. For example, if the value is set to 3 stars, then 1, 2, or 3 stars will be considered as a negative rating, while 4 and 5-star ratings will be classified as positive.', 'collect-reviews' )}
								field={
									<TextField
										name="negative_review.threshold"
										id="negative-review-threshold"
									/>
								}
							/>
						</MetaBox>

						<Button loading={isSubmitting} type="submit" sx={{mt: 2}}>
							<span>{__( 'Save settings', 'collect-reviews' )}</span>
						</Button>
					</Form>
				)}
			</Formik>
		</>
	);
}
