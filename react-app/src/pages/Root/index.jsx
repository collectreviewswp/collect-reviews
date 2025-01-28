import React from "react";
import {Outlet, useLocation} from "react-router-dom";

import AppBar from '@mui/material/AppBar';
import Box from '@mui/material/Box';
import Toolbar from '@mui/material/Toolbar';
import Container from '@mui/material/Container';

import {ReactComponent as Logo} from "assets/images/logo.svg";
import Notice from "components/Notice";

import useNotices from "store/notices";
import ConfirmationModal from "../../components/ConfirmationModal";
import {useConfirmationModal} from "../../helpers/modals";

export default function Root({containerWidth = "lg"}) {

	const location = useLocation(),
		notices = useNotices(),
		confirmationModal = useConfirmationModal();

	// Clear notices on location change.
	React.useEffect(() => notices.clear(), [location]);

	return (
		<>
			<AppBar position="static" sx={{marginBottom: 4}}>
				<Container maxWidth={containerWidth}>
					<Toolbar disableGutters>
						<Logo style={{maxWidth: 170}}/>
					</Toolbar>
				</Container>
			</AppBar>

			<Container maxWidth={containerWidth}>
				{notices.list.map(notice =>
					<Notice
						key={notice.id}
						severity={notice.type}
						onClose={() => notices.remove(notice.id)}
						sx={{mb: 3}}
					>
						{notice.text}
					</Notice>
				)}

				<Box mt={4}>
					<Outlet/>
				</Box>
			</Container>

			<ConfirmationModal {...confirmationModal.modalProps}/>
		</>
	);
}

