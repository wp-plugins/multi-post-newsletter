var multi_post_newsletter;
( function( $ ) {
	multi_post_newsletter = {
		init : function () {
			$( '#menu-posts' ).removeClass( 'wp-has-current-submenu wp-menu-open' );
			$( '.toplevel_page_mpnl_generate' ).addClass( 'wp-has-current-submenu wp-menu-open' );
			$( '#toplevel_page_mpnl_generate' ).addClass( 'wp-has-current-submenu wp-menu-open' );
			
			$( '.sortable-holder' ).sortable( {
				placeholder: 'sortable-placeholder',
				connectWith: '.post-sortable',
				items: '.stuffbox',
				handle: '.hndle',
				cursor: 'move',
				distance: 2,
				tolerance: 'pointer',
				forcePlaceholderSize: true,
				helper: 'clone',
				opacity: 0.65,
				start: function( e, ui ) {
					$( 'body' ).css( {
						WebkitUserSelect: 'none',
						KhtmlUserSelect: 'none'
					} );
				},
				stop: function( e, ui ) {
					multi_post_newsletter.save_post_order();
					$( 'body' ).css( {
						WebkitUserSelect: '',
						KhtmlUserSelect: ''
					} );
				},
			} );
		},
	
		save_post_order : function () {
			var post_vars, page_columns = $( '.columns-prefs input:checked' ).val() || 0;
			post_vars = {
				action: 'save_post_order',
			};
			$( '.sortable-holder' ).each( function() {
				post_vars['order[' + this.id.split( '-' )[0] + ']'] = $( this ).sortable( 'toArray' ).join( ',' );
			} );
			$.post( ajaxurl, post_vars, function( response ) {
				$( '#ajax-response' ).html( response );
			} );
		},
		
		switch_preview : function ( type ) {
			if ( 'text' == type ) {
				$( '#link_html_preview' ).removeClass( 'nav-tab-active' );
				$( '#link_text_preview' ).addClass( 'nav-tab-active' );
				$( '#text-preview' ).css( 'display', 'block' );
				$( '#html-preview' ).css( 'display', 'none' );
			}
			else {
				$( '#link_html_preview' ).addClass( 'nav-tab-active' );
				$( '#link_text_preview' ).removeClass( 'nav-tab-active' );
				$( '#text-preview' ).css( 'display', 'none' );
				$( '#html-preview' ).css( 'display', 'block' );
			}
		}
	};
	$( document ).ready( function( $ ) { multi_post_newsletter.init(); } );
} )( jQuery );