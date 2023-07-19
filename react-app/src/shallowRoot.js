// Create a shadow DOM root.
const container = document.getElementById( 'collect-reviews-root' ),
	shadowContainer = container.attachShadow( {mode: 'open'} ),
	shadowRoot = document.createElement( 'div' );

shadowContainer.appendChild( shadowRoot );

export {shadowContainer, shadowRoot};
