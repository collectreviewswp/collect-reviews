import React from "react";

import Root from "../Root";
import Settings from "./index";
import WooCommerce from "./Integrations/WooCommerce";
import WPForms from "./Integrations/WPForms";
import EasyDigitalDownloads from "./Integrations/EasyDigitalDownloads";

export default [
	{
		path: "/",
		element: <Root/>,
		children: [
			{
				index: true,
				element: <Settings/>,
			},
			{
				path: "/integration/woocommerce",
				element: <WooCommerce/>,
			},
			{
				path: "/integration/easy-digital-downloads",
				element: <EasyDigitalDownloads/>,
			},
			{
				path: "/integration/wpforms",
				element: <WPForms/>,
			},
		],
	},
]
