import React from 'react';
import Link from '@mui/material/Link'

const ExternalLink = ( {children, ...rest} ) => {
	return <Link {...rest} target="_blank" rel="noopener noreferrer">{children}</Link>;
}

export default ExternalLink;
