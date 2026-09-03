const { makeIframe, fetchThumb } = require( './modules/iframe.js' );

( function () {
	mw.hook( 'wikipage.content' ).add( () => {
		document.querySelectorAll( '.embedservice-evl' ).forEach( ( evl ) => {
			evl.addEventListener( 'click', ( e ) => {
				e.preventDefault();

				const player = ( evl.dataset && evl.dataset.player ) || 'default';
				const iframeConfig = JSON.parse( ( evl.dataset && evl.dataset.mwIframeconfig ) || '{}' );

				const playerContainer = document.querySelector( `.embedservice.evlplayer-${ player }` );
				if ( playerContainer === null ) {
					mw.log.warn( `No player with id '${ player }' found.` );
					return;
				}

				const iframe = playerContainer.querySelector( 'iframe' );
				// Iframe exists, no consent required or already given
				if ( iframe !== null ) {
					playerContainer.dataset.service = ( evl.dataset && evl.dataset.service ) || 'youtube';

					for ( const [ key, value ] of Object.entries( iframeConfig ) ) {
						iframe.setAttribute( key, value );
					}

					return;
				}

				// No iframe exists, only when explicit consent is required
				if ( !evl.dataset.mwIframeconfig ) {
					mw.log.warn( `No iframe config found for player with id '${ player }'.` );
					return;
				}

				const div = playerContainer;
				const wrapper = div.querySelector( '.embedservice-wrapper' );
				const consentDiv = wrapper.querySelector( '.embedservice-consent' );

				const origService = div.dataset ? div.dataset.service : undefined;

				div.dataset.mwIframeconfig = evl.dataset.mwIframeconfig;
				div.dataset.service = evl.dataset.service;

				// eslint-disable-next-line mediawiki/msg-doc
				const serviceMessage = mw.message( 'embedservice-service-' + ( ( evl.dataset && evl.dataset.service ) || 'youtube' ) ).escaped();
				const privacyMessage = mw.message( 'embedservice-consent-privacy-notice-text', serviceMessage ).escaped();

				div.querySelector( '.embedservice-loader__service' ).innerText = serviceMessage;
				div.querySelector( '.embedservice-privacyNotice__content' ).innerText = privacyMessage;

				if ( evl.dataset.privacyUrl ) {
					const link = document.createElement( 'a' );
					link.href = evl.dataset.privacyUrl;
					link.rel = 'nofollow,noopener';
					link.target = '_blank';
					link.classList.add( 'embedservice-privacyNotice__link' );
					link.innerText = mw.message( 'embedservice-consent-privacy-policy' ).escaped();

					div.querySelector( '.embedservice-privacyNotice__content' ).appendChild( link );
				}

				if ( origService === 'videolink' ) {
					makeIframe( div );
				} else {
					fetchThumb( iframeConfig.src, consentDiv, wrapper.parentElement );
				}
			} );
		} );
	} );
}() );
