import React from "react";

import Root from "../Root";
import ReviewRequests from "./index";
import NewReviewRequest from "./New";

export default [
	{
		path: "/",
		element: <Root containerWidth={false}/>,
		children: [
			{
				index: true,
				element: <ReviewRequests/>,
			},
		],
	},
	{
		path: "/new",
		element: <Root/>,
		children: [
			{
				index: true,
				element: <NewReviewRequest/>,
			},
		],
	},
]
