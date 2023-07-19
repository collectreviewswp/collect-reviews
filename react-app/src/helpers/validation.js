import * as yup from 'yup';
import {__} from '@wordpress/i18n'

yup.setLocale( {
	mixed: {
		required: __( 'This field is required.', 'collect-reviews' ),
		notType: __( 'This field must be a ${type}.', 'collect-reviews' ),
	},
	string: {
		url: __( 'This field must be a valid URL.', 'collect-reviews' ),
		email: __( 'This field must be a valid email address.', 'collect-reviews' ),
	},
	number: {
		min: __( 'This field must be greater than or equal to ${min}.', 'collect-reviews' ),
		max: __( 'This field must be less than or equal to ${max}.', 'collect-reviews' ),
		integer: __( 'This field must be an integer.', 'collect-reviews' ),
	},
} );

export default yup;
