( function () {
	'use strict';

	/* ---------- Live search (WordPress REST API) ---------- */
	function decode( html ) {
		var el = document.createElement( 'textarea' );
		el.innerHTML = html;
		return el.value;
	}

	function initSearch( form ) {
		var input = form.querySelector( 'input[type="search"]' );
		var list = form.querySelector( '.sdl-search__list' );
		var endpoint = form.getAttribute( 'data-endpoint' );
		var timer = null;
		var controller = null;

		if ( ! input || ! list || ! endpoint ) {
			return;
		}

		function hide() {
			list.hidden = true;
			list.textContent = '';
		}

		function show( items ) {
			list.textContent = '';

			if ( ! items.length ) {
				var empty = document.createElement( 'li' );
				var msg = document.createElement( 'span' );
				msg.textContent = 'لا توجد نتائج';
				empty.appendChild( msg );
				list.appendChild( empty );
			}

			items.forEach( function ( item ) {
				var li = document.createElement( 'li' );
				var a = document.createElement( 'a' );
				li.setAttribute( 'role', 'option' );
				a.href = item.link;
				a.textContent = decode( item.title.rendered );
				li.appendChild( a );
				list.appendChild( li );
			} );

			list.hidden = false;
		}

		input.addEventListener( 'input', function () {
			var term = input.value.trim();
			window.clearTimeout( timer );

			if ( term.length < 2 ) {
				hide();
				return;
			}

			timer = window.setTimeout( function () {
				if ( controller ) {
					controller.abort();
				}
				controller = 'AbortController' in window ? new AbortController() : null;

				var url = endpoint + ( endpoint.indexOf( '?' ) > -1 ? '&' : '?' ) +
					'search=' + encodeURIComponent( term ) + '&per_page=5&_fields=link,title';

				fetch( url, controller ? { signal: controller.signal } : {} )
					.then( function ( r ) {
						return r.ok ? r.json() : [];
					} )
					.then( show )
					.catch( function () {} );
			}, 250 );
		} );

		input.addEventListener( 'keydown', function ( e ) {
			if ( 'Escape' === e.key ) {
				hide();
			}
		} );

		document.addEventListener( 'click', function ( e ) {
			if ( ! form.contains( e.target ) ) {
				hide();
			}
		} );
	}

	/* ---------- Video lightbox ---------- */
	var dialog = null;
	var frame = null;
	var title = null;
	var opener = null;

	function buildDialog() {
		dialog = document.createElement( 'dialog' );
		dialog.className = 'sdl-modal';
		dialog.setAttribute( 'aria-label', 'مشغل الفيديو' );

		var close = document.createElement( 'button' );
		close.type = 'button';
		close.className = 'sdl-modal__close';
		close.setAttribute( 'aria-label', 'إغلاق' );
		close.innerHTML = '&times;';
		close.addEventListener( 'click', function () {
			dialog.close();
		} );

		frame = document.createElement( 'div' );
		frame.className = 'sdl-modal__frame';

		title = document.createElement( 'p' );
		title.className = 'sdl-modal__title';

		dialog.appendChild( close );
		dialog.appendChild( frame );
		dialog.appendChild( title );

		// Click on the backdrop closes.
		dialog.addEventListener( 'click', function ( e ) {
			if ( e.target === dialog ) {
				dialog.close();
			}
		} );

		// Always remove the iframe so the video stops.
		dialog.addEventListener( 'close', function () {
			frame.textContent = '';
			if ( opener ) {
				opener.focus();
			}
		} );

		document.body.appendChild( dialog );
	}

	function openVideo( link ) {
		var id = link.getAttribute( 'data-youtube' );

		if ( ! id || ! /^[A-Za-z0-9_-]{11}$/.test( id ) ) {
			return false;
		}

		if ( ! dialog ) {
			buildDialog();
		}

		var iframe = document.createElement( 'iframe' );
		iframe.src = 'https://www.youtube-nocookie.com/embed/' + id + '?autoplay=1&rel=0&modestbranding=1';
		iframe.title = link.getAttribute( 'data-title' ) || 'YouTube video';
		iframe.allow = 'autoplay; encrypted-media; picture-in-picture; fullscreen';
		iframe.setAttribute( 'allowfullscreen', '' );
		iframe.setAttribute( 'referrerpolicy', 'strict-origin-when-cross-origin' );

		frame.textContent = '';
		frame.appendChild( iframe );
		title.textContent = iframe.title;
		opener = link;
		dialog.showModal();

		return true;
	}

	document.addEventListener( 'click', function ( e ) {
		var link = e.target.closest && e.target.closest( 'a[data-youtube]' );

		if ( ! link || e.defaultPrevented || e.button !== 0 || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey ) {
			return;
		}

		if ( typeof HTMLDialogElement === 'undefined' ) {
			return; // Old browser: follow the link to the video page.
		}

		if ( openVideo( link ) ) {
			e.preventDefault();
		}
	} );

	document.querySelectorAll( '.sdl-search' ).forEach( initSearch );
}() );
