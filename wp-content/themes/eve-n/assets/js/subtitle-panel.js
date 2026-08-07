/**
 * "Ondertitel" panel in the block editor sidebar.
 *
 * Native `PluginDocumentSettingPanel`, plain `wp.element.createElement` — no
 * JSX, no build step. Reads and writes the `even_subtitle` post meta field
 * registered with `show_in_rest` in inc/blog.php, so publishing sends it
 * through the same REST field WordPress already sanitises and authorises.
 */
( function ( wp ) {
	'use strict';

	if ( ! wp || ! wp.plugins || ! wp.editPost || ! wp.element || ! wp.components || ! wp.data || ! wp.i18n ) {
		return;
	}

	var registerPlugin = wp.plugins.registerPlugin;
	var PluginDocumentSettingPanel = wp.editPost.PluginDocumentSettingPanel;
	var createElement = wp.element.createElement;
	var TextControl = wp.components.TextControl;
	var useSelect = wp.data.useSelect;
	var useDispatch = wp.data.useDispatch;
	var __ = wp.i18n.__;

	var META_KEY = 'even_subtitle';

	function OndertitelPanel() {
		var meta = useSelect( function ( select ) {
			return select( 'core/editor' ).getEditedPostAttribute( 'meta' );
		}, [] );

		var editPost = useDispatch( 'core/editor' ).editPost;
		var subtitle = ( meta && meta[ META_KEY ] ) || '';

		return createElement(
			PluginDocumentSettingPanel,
			{
				name: 'even-ondertitel-panel',
				title: __( 'Ondertitel', 'eve-n' ),
			},
			createElement( TextControl, {
				label: __( 'Korte ondertitel, onder de titel op de blogkaart en in de header van het bericht.', 'eve-n' ),
				value: subtitle,
				maxLength: 160,
				onChange: function ( value ) {
					var nextMeta = {};
					var key;

					for ( key in meta ) {
						if ( Object.prototype.hasOwnProperty.call( meta, key ) ) {
							nextMeta[ key ] = meta[ key ];
						}
					}

					nextMeta[ META_KEY ] = value;
					editPost( { meta: nextMeta } );
				},
			} )
		);
	}

	registerPlugin( 'even-ondertitel', {
		render: OndertitelPanel,
	} );
} )( window.wp );
