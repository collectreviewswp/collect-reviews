import {useStore} from "store";

const useConfirmationModal = () => {

	const store = useStore();

	const open = ( action, onConfirm, description = '' ) => {

		store.setData( {
			...store.data,
			confirmationModal: {
				open: true,
				action,
				onConfirm,
				description
			}
		} );
	}

	const close = () => store.setData( {
		...store.data,
		confirmationModal: {
			...store.data.confirmationModal,
			open: false
		}
	} );

	const onClosed = () => store.setData( {
		...store.data,
		confirmationModal: {
			open: false
		}
	} );

	const modalProps = {
		open: store.data.confirmationModal.open,
		action: store.data.confirmationModal?.action || '',
		description: store.data.confirmationModal?.description || false,
		onConfirm: () => {
			if ( store.data.confirmationModal?.onConfirm ) {
				close();
				store.data.confirmationModal.onConfirm();
			}
		},
		onCancel: close,
		TransitionProps: {onExited: () => onClosed}
	}

	return {open, close, modalProps};
}

export {useConfirmationModal};
