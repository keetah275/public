( function ( components, data, editPost, element, i18n, plugins ) {
	'use strict';

	var createElement = element.createElement;
	var useSelect = data.useSelect;
	var __ = i18n.__;
	var PluginDocumentSettingPanel = editPost.PluginDocumentSettingPanel;

	function SeoMetaPanel() {
		var editorData = useSelect( function ( select ) {
			var editor = select( 'core/editor' );
			return {
				content: editor.getEditedPostContent(),
				meta: editor.getEditedPostAttribute( 'meta' ) || {}
			};
		}, [] );

		if ( editorData.content.indexOf( 'gc-resource-article' ) === -1 ) {
			return null;
		}

		function updateMeta( key, value ) {
			var nextMeta = Object.assign( {}, editorData.meta );
			nextMeta[ key ] = value;
			data.dispatch( 'core/editor' ).editPost( { meta: nextMeta } );
		}

		var title = editorData.meta._gymcast_meta_title || '';
		var description = editorData.meta._gymcast_meta_description || '';

		return createElement(
			PluginDocumentSettingPanel,
			{
				name: 'gymcast-seo-meta',
				title: __( 'Gymcast SEO', 'gymcast-marketing-tools' ),
				className: 'gymcast-seo-meta-panel'
			},
			createElement( components.TextControl, {
				label: __( 'Meta title', 'gymcast-marketing-tools' ),
				value: title,
				help: title.length + ' / 60 ' + __( 'characters recommended', 'gymcast-marketing-tools' ),
				onChange: function ( value ) { updateMeta( '_gymcast_meta_title', value ); }
			} ),
			createElement( components.TextareaControl, {
				label: __( 'Meta description', 'gymcast-marketing-tools' ),
				value: description,
				help: description.length + ' / 160 ' + __( 'characters recommended', 'gymcast-marketing-tools' ),
				onChange: function ( value ) { updateMeta( '_gymcast_meta_description', value ); }
			} )
		);
	}

	plugins.registerPlugin( 'gymcast-seo-meta', { render: SeoMetaPanel } );
} )( window.wp.components, window.wp.data, window.wp.editPost, window.wp.element, window.wp.i18n, window.wp.plugins );
