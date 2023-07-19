import {createTheme} from '@mui/material/styles';
import {shadowRoot} from "./shallowRoot";
import {grey} from "@mui/material/colors";

// A custom theme for this app
const theme = createTheme( {
	palette: {
		primary: {
			main: '#1565c0',
			light: '#4383cc',
			dark: '#0e4686',
			contrastText: '#fff',
		},
		overlay: {
			light: 'rgba(255, 255, 255, 0.5)',
		},
		border: {
			main: grey[ 200 ],
		}
	},
	typography: {
		fontFamily: [
			'-apple-system',
			'BlinkMacSystemFont',
			'"Segoe UI"',
			'Roboto',
			'"Helvetica Neue"',
			'Arial',
			'sans-serif',
			'"Apple Color Emoji"',
			'"Segoe UI Emoji"',
			'"Segoe UI Symbol"',
		].join( ',' ),
		button: {
			textTransform: "capitalize"
		}
	},
	components: {
		MuiPopover: {
			defaultProps: {
				container: shadowRoot,
			}
		},
		MuiPopper: {
			defaultProps: {
				container: shadowRoot,
			}
		},
		MuiModal: {
			defaultProps: {
				container: shadowRoot,
			},
		},
		MuiInputBase: {
			defaultProps: {
				size: 'small',
			}
		},
		MuiButton: {
			defaultProps: {
				variant: 'contained',
			}
		},
		MuiLoadingButton: {
			defaultProps: {
				variant: 'contained',
			}
		},
		MuiFormHelperText: {
			styleOverrides: {
				root: {
					marginLeft: 0,
					marginTop: 8
				},
			},
		}
	},
} );

export default theme;
