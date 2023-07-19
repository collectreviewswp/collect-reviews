import React, {useState} from 'react';

import Dialog from "@mui/material/Dialog";
import DialogTitle from "@mui/material/DialogTitle";
import DialogContent from "@mui/material/DialogContent";
import DialogActions from "@mui/material/DialogActions";
import Button from "@mui/lab/LoadingButton";
import Link from "@mui/material/Link";

import PlatformInstruction from "instructions/platforms";

import {__, sprintf} from "@wordpress/i18n";

import {getPlatformName} from "helpers/platform";

const PlatformInstructionModalLink = ( {platform} ) => {

	const [ isOpen, setIsOpen ] = useState( false ),
		platformName = getPlatformName( platform )

	function openModal( e ) {
		e.preventDefault();
		setIsOpen( true );
	}

	// TODO: add modal transition (next release).
	return (
		<>
			<Link href="#" onClick={openModal}>
				{platform !== 'custom' ?
					sprintf( __( 'Read instruction on how to get %s Review URL', 'collect-reviews' ), platformName ) :
					__( 'Read instruction on how to get Review URL', 'collect-reviews' )
				}
			</Link>

			<Dialog
				open={isOpen}
				fullWidth
				maxWidth="sm"
				sx={{
					zIndex: 999999
				}}
			>
				<DialogTitle sx={{borderBottom: '1px solid', borderColor: 'border.main'}}>
					{platform !== 'custom' ?
						sprintf( __( 'How to get %s Review URL', 'collect-reviews' ), platformName ) :
						__( 'How to get Review URL', 'collect-reviews' )
					}
				</DialogTitle>
				<DialogContent sx={{pt: '20px !important'}}>
					<PlatformInstruction platform={platform}/>
				</DialogContent>
				<DialogActions sx={{borderTop: '1px solid', borderColor: 'border.main'}}>
					<Button
						onClick={() => setIsOpen( false )}
						variant="text"
						sx={{textTransform: 'uppercase'}}
					>
						{__( 'Close', 'collect-reviews' )}
					</Button>
				</DialogActions>
			</Dialog>
		</>
	);
}

export default PlatformInstructionModalLink;
