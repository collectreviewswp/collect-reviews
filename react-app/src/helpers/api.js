import {__} from '@wordpress/i18n';
import axios from 'axios';

import {normalizeOptions} from "./data";

const {ajax_url} = window.collectReviews;

class Api {

	#action = 'collect_reviews_ajax';

	post( task, data ) {

		return this.request( {
			method: 'post',
			headers: {'content-type': 'application/x-www-form-urlencoded'},
			data: {
				action: this.#action,
				task: task,
				data: data
			},
		} );
	}

	get( task, data ) {

		return this.request( {
			method: 'get',
			params: {
				action: this.#action,
				task: task,
				data: data
			},
		} );
	}

	request( config ) {

		const defaultConfig = {
			url: ajax_url
		};

		return axios.request( {
			...defaultConfig,
			...config
		} ).then( response => {
			return response.data;
		} ).then( response => {
			if ( typeof response !== 'object' ) {
				throw this.getDefaultErrorMessage();
			} else if ( ! response.success ) {
				throw response.data;
			}

			return response.data;
		}, () => {
			throw this.getDefaultErrorMessage();
		} );
	}

	getDefaultErrorMessage() {

		return __( 'Something goes wrong. Check WordPress debug log to identify the issue.', 'collect-reviews' )
	};

	saveSettings( data ) {
		return this.post( 'save_settings', data ).then( updatedOptions => {
			normalizeOptions( updatedOptions );

			return updatedOptions;
		} );
	}
}

const api = new Api();

export default api;
