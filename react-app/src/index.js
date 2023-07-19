import './publicPath';
import React from 'react';
import ReactDOM from 'react-dom/client';
import {createHashRouter, RouterProvider} from "react-router-dom";
import {ThemeProvider} from '@mui/material/styles';
import {LocalizationProvider} from '@mui/x-date-pickers/LocalizationProvider';
import { AdapterDayjs } from '@mui/x-date-pickers/AdapterDayjs';
import dayjs from 'dayjs';
import dayjsUtc from 'dayjs/plugin/utc';
// TODO: implement locales (next release).
import 'dayjs/locale/en';
import ScopedCssBaseline from '@mui/material/ScopedCssBaseline';

import reportWebVitals from './reportWebVitals';

import {StoreProvider} from "./store";
import CacheProvider from "./styles/CacheProvider";

import routes from "./pages";
import theme from './theme';
import {shadowRoot} from "./shallowRoot";

import './styles/global.scss';

dayjs.extend(dayjsUtc);

const root = ReactDOM.createRoot( shadowRoot ),
	router = createHashRouter( routes );

root.render(
	<React.StrictMode>
		<CacheProvider>
			<ThemeProvider theme={theme}>
				<LocalizationProvider dateAdapter={AdapterDayjs} adapterLocale="en">
					<ScopedCssBaseline sx={{backgroundColor: 'transparent'}}>
						<StoreProvider>
							<RouterProvider router={router}/>
						</StoreProvider>
					</ScopedCssBaseline>
				</LocalizationProvider>
			</ThemeProvider>
		</CacheProvider>
	</React.StrictMode>
);

// If you want to start measuring performance in your app, pass a function
// to log results (for example: reportWebVitals(console.log))
// or send to an analytics endpoint. Learn more: https://bit.ly/CRA-vitals
reportWebVitals();
