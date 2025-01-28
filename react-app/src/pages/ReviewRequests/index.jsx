import React, {useState, useEffect, createContext, useContext} from "react";
import {Link as RouterLink} from 'react-router-dom';
import {DataGrid} from '@mui/x-data-grid';
import Box from "@mui/material/Box";
import Link from "@mui/material/Link";
import Tooltip from "@mui/material/Tooltip";
import ScheduleIcon from '@mui/icons-material/Schedule';
import HighlightOffIcon from '@mui/icons-material/HighlightOff';
import Number1Icon from '@mui/icons-material/LooksOne';
import Number2Icon from '@mui/icons-material/LooksTwo';
import Number3Icon from '@mui/icons-material/Looks3';
import Number4Icon from '@mui/icons-material/Looks4';
import Number5Icon from '@mui/icons-material/Looks5';
import TaskAltIcon from '@mui/icons-material/TaskAlt';
import DeleteIcon from '@mui/icons-material/Delete';
import Typography from "@mui/material/Typography";
import Button from "@mui/lab/LoadingButton";
import Alert from "@mui/material/Alert";
import {grey} from '@mui/material/colors';

import PageHeader from "components/PageHeader";
import PageTitle from "components/PageTitle";

import {__} from "@wordpress/i18n";
import {animateScroll as scroll} from "react-scroll";

import api from "helpers/api";
import useNotices from "store/notices";
import {useConfirmationModal} from "helpers/modals";


const columns = [
	{
		field: 'status',
		headerName: '',
		width: 44,
		minWidth: 44,
		align: 'center',
		renderCell: ( params ) => {
			let tooltip = __( 'The review request email is scheduled to be sent.', 'collect-reviews' );

			if ( params.row.status === 1 ) {
				tooltip = __( 'The review request email was sent.', 'collect-reviews' );
			}

			if ( params.row.status === 2 ) {
				tooltip = __( 'The review request email failed to send.', 'collect-reviews' );
			}

			return (
				<Tooltip
					title={tooltip}
					slotProps={{
						popper: {
							sx: {
								zIndex: 9991, // Use higher z-index than WP dashboard menu.
							},
						},
					}}
				>
					<Box
						sx={{
							'svg': {
								display: 'block',
							}
						}}
					>
						{params.value === 0 && <ScheduleIcon sx={{color: grey[ 800 ]}}/>}
						{params.value === 1 && <TaskAltIcon sx={{color: 'success.main'}}/>}
						{params.value === 2 && <HighlightOffIcon sx={{color: 'error.main'}}/>}
					</Box>
				</Tooltip>
			)
		},
		sortable: false
	},
	{field: 'email', headerName: __( 'Email', 'collect-reviews' ), minWidth: 207, sortable: false, flex: 2},
	{field: 'platform_name', headerName: __( 'Platform', 'collect-reviews' ), minWidth: 120, sortable: false, flex: 1},
	{field: 'created_date', headerName: __( 'Created date', 'collect-reviews' ), minWidth: 220, sortable: false, flex: 1},
	{field: 'send_date', headerName: __( 'Send date', 'collect-reviews' ), minWidth: 220, sortable: false, flex: 1},
	{field: 'rate_date', headerName: __( 'Rate date', 'collect-reviews' ), minWidth: 220, sortable: false, flex: 1},
	{
		field: 'rating',
		headerName: __( 'Rating', 'collect-reviews' ),
		width: 70,
		align: 'center',
		renderCell: ( params ) => {
			let color = grey[ 800 ],
				tooltip = __( 'The customer chose the rating in the email but hasn\'t submitted the review.', 'collect-reviews' );

			if ( params.row.reply_status === 2 ) {
				color = 'error.main';
				tooltip = __( 'The negative review form was submitted.', 'collect-reviews' );
			} else if ( params.row.reply_status === 3 ) {
				color = 'success.main';
				tooltip = __( 'The positive review link was clicked and the customer was redirected to the review platform.', 'collect-reviews' );
			}

			return params.value > 0 &&
				<Tooltip title={tooltip}>
					<Box
						sx={{
							'svg': {
								display: 'block',
							}
						}}
					>
						{params.value === 1 && <Number1Icon sx={{color: color}}/>}
						{params.value === 2 && <Number2Icon sx={{color: color}}/>}
						{params.value === 3 && <Number3Icon sx={{color: color}}/>}
						{params.value === 4 && <Number4Icon sx={{color: color}}/>}
						{params.value === 5 && <Number5Icon sx={{color: color}}/>}
					</Box>
				</Tooltip>;
		},
		sortable: false
	},
	{
		field: 'refs',
		headerName: __( 'Ref', 'collect-reviews' ),
		width: 140,
		renderCell: params => (
			<Box>
				{params.value.map( ref =>
					<Tooltip title={ref.tooltip}>
						{ref.url ?
							<Link href={ref.url} target="_blank">{ref.text}</Link> :
							<Typography fontSize='inherit'>{ref.text}</Typography>
						}
					</Tooltip>
				)}
			</Box>
		),
		sortable: false
	},
	{
		field: 'actions',
		headerName: '',
		width: 44,
		minWidth: 44,
		align: 'center',
		renderCell: params => <DeleteAction reviewRequest={params.row}/>,
		sortable: false
	},
];

const TableContext = createContext( null );

/**
 * TODO: (next release)
 * 		- get default row count per page from backend.
 * 		- save current page to query params and restore it on page load.
 * 		- get initial rows from backend on page load.
 * 		- implement filtering.
 * 		- implement cache.
 */

export default function ReviewRequests() {

	const [ paginationModel, setPaginationModel ] = useState( {
		page: 0,
		pageSize: 20,
	} );

	const [ isLoading, setIsLoading ] = useState( false ),
		[ rows, setRows ] = useState( {list: [], totalCount: 0} ),
		notices = useNotices();

	const loadData = async () => {

		try {
			setIsLoading( true );

			const result = await api.get( 'get_review_requests', {
				page: paginationModel.page,
				per_page: paginationModel.pageSize
			} );

			setRows( {
				list: result.list,
				totalCount: result.total_count
			} );
		} catch ( error ) {
			if ( error instanceof Error ) {
				notices.add( __( 'Something goes wrong. Check browser\'s JavaScript console to identify the issue and reach out to plugin\'s support.', 'collect-reviews' ), 'error' );
				console.error( error );
			} else {
				notices.add( error, 'error' );
			}
		}

		setIsLoading( false );
		scroll.scrollToTop();
	}

	useEffect( () => {
		loadData();
	}, [ paginationModel ] );

	return (
		<>
			<PageHeader sx={{display: 'flex', alignItems: 'center'}}>
				<PageTitle>{__( 'Review Requests', 'collect-reviews' )}</PageTitle>

				<Button
					component={RouterLink}
					to="/new"
					variant="outlined"
					size="small"
					sx={{ml: 'auto'}}
					onClick={() => window.scrollTo( 0, 0 )}
				>
					{__( 'Create review request', 'collect-reviews' )}
				</Button>
			</PageHeader>
			<TableContext.Provider value={{setIsLoading, reload: loadData}}>
				<DataGrid
					columns={columns}
					rows={rows.list}
					rowCount={rows.totalCount}
					getRowHeight={() => 'auto'}
					autoHeight
					loading={isLoading}
					setLoading={setIsLoading}
					pageSizeOptions={[ 20 ]}
					paginationMode="server"
					paginationModel={paginationModel}
					onPaginationModelChange={setPaginationModel}
					keepNonExistentRowsSelected
					localeText={{
						noRowsLabel: __( 'No review requests found.', 'collect-reviews' ),
						noResultsOverlayLabel: __( 'No review requests found.', 'collect-reviews' ),
					}}
					checkboxSelection={false}
					disableRowSelectionOnClick
					disableColumnMenu
					sx={{
						'backgroundColor': '#fff',
						'borderRadius': 0,
						'.MuiDataGrid-columnHeaders:hover .MuiDataGrid-columnSeparator': {visibility: 'hidden'},
						'&.MuiDataGrid-root--densityStandard .MuiDataGrid-cell': {p: '15px 5px'},
						'.MuiDataGrid-row:hover': {backgroundColor: 'transparent'},
						'.MuiDataGrid-cell[data-field="refs"]': {py: '6.5px'},
						'.MuiDataGrid-columnHeader:focus, .MuiDataGrid-cell:focus': {outline: 'none'},
						'.MuiDataGrid-row:nth-of-type(even)': {
							backgroundColor: grey[50],
						}
					}}
				/>
			</TableContext.Provider>
		</>
	);
}

const DeleteAction = ( {reviewRequest} ) => {

	const notices = useNotices(),
		table = useContext( TableContext ),
		confirmationModal = useConfirmationModal();

	const deleteItem = async () => {
		try {
			table.setIsLoading( true );

			await api.post( 'delete_review_request', {
				id: reviewRequest.id
			} );

			await table.reload();

			notices.add( __( 'Review request deleted successfully.', 'collect-reviews' ), 'success' );
		} catch ( error ) {
			if ( error instanceof Error ) {
				notices.add( __( 'Something goes wrong. Check browser\'s JavaScript console to identify the issue and reach out to plugin\'s support.', 'collect-reviews' ), 'error' );
				console.error( error );
				scroll.scrollToTop();
			} else {
				notices.add( error, 'error' );
			}
		} finally {
			table.setIsLoading( false );
		}
	}

	let description = '';

	if ( reviewRequest.status === 1 && reviewRequest.rating === 0 ) {
		description =
			<Alert severity="warning">{__( 'Pay attention that if you delete already sent and not processed review request, the recipient will not be able to leave a review.', 'collect-reviews' )}</Alert>;
	}

	return (
		<DeleteIcon
			sx={{
				cursor: 'pointer',
				color: grey[ 400 ],
				transition: '0.2s',
				'&:hover': {
					color: 'error.main',
				}
			}}
			onClick={() => confirmationModal.open( __( 'delete review request', 'collect-reviews' ), deleteItem, description )}
		/>
	);
}
