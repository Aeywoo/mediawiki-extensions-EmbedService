( function () {
	mw.hook( 'wikipage.content' ).add( () => {
		document.querySelectorAll( '[data-service="local-embed"] .embedservice-wrapper' ).forEach( ( div ) => {
			const consentDiv = div.querySelector( '.embedservice-consent' );
			const video = div.querySelector( 'video' );
			const fakeButton = div.querySelector( '.embedservice-loader__fakeButton' );
			const localEmbedStyle = div.querySelector( '.embedservice-localEmbedStyle' );

			if ( localEmbedStyle !== null && video !== null ) {
				video.addEventListener( 'play', () => {
					localEmbedStyle.classList.add( 'embedservice-localEmbedStyle--hidden' );
				} );

				video.addEventListener( 'ended', () => {
					localEmbedStyle.classList.remove( 'embedservice-localEmbedStyle--hidden' );
				} );
			}

			if ( consentDiv === null || video === null || fakeButton === null ) {
				return;
			}

			const clickListener = function () {
				video.controls = true;
				video.play();
				consentDiv.removeEventListener( 'click', clickListener );
				consentDiv.parentElement.removeChild( consentDiv );
			};

			fakeButton.innerHTML = mw.message( 'embedservice-play' ).escaped();

			video.controls = false;

			consentDiv.addEventListener( 'click', clickListener );
		} );
	} );
}() );
