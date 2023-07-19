import * as React from 'react';

import clsx from 'clsx';
import {styled, useThemeProps} from '@mui/material/styles';
import {unstable_composeClasses as composeClasses} from '@mui/base';
import {getTabPanelUtilityClass} from '@mui/lab/TabPanel/tabPanelClasses';
import {getPanelId, getTabId, useTabContext} from '@mui/lab/TabContext';

const useUtilityClasses = ( ownerState ) => {
	const {classes} = ownerState;

	const slots = {
		root: [ 'root' ],
	};

	return composeClasses( slots, getTabPanelUtilityClass, classes );
};

const TabPanelRoot = styled( 'div', {
	name: 'MuiTabPanel',
	slot: 'Root',
	overridesResolver: ( props, styles ) => styles.root,
} )( ( {theme} ) => ({
	padding: theme.spacing( 3 ),
}) );

const TabPanel = React.forwardRef( function TabPanel( inProps, ref ) {
	const props = useThemeProps( {props: inProps, name: 'MuiTabPanel'} );

	const {children, className, value, mountOnLoad = false, ...other} = props;

	const ownerState = {
		...props,
	};

	const classes = useUtilityClasses( ownerState );

	const context = useTabContext();
	if ( context === null ) {
		throw new TypeError( 'No TabContext provided' );
	}
	const id = getPanelId( context, value );
	const tabId = getTabId( context, value );

	const [ visited, setVisited ] = React.useState( false );

	React.useEffect( () => {
		if ( value === context.value ) {
			setVisited( true );
		}
	}, [ context.value ] );

	return (
		<TabPanelRoot
			aria-labelledby={tabId}
			className={clsx( classes.root, className )}
			hidden={value !== context.value}
			id={id}
			ref={ref}
			role="tabpanel"
			ownerState={ownerState}
			{...other}
		>
			{(value === context.value || visited || mountOnLoad) && children}
		</TabPanelRoot>
	);
} );

export default TabPanel;
