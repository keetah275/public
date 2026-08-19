( function ( blocks, blockEditor, components, coreData, data, element, i18n ) {
	'use strict';

	var createElement = element.createElement;
	var useState = element.useState;
	var useSelect = data.useSelect;
	var __ = i18n.__;
	var isMigratingLegacyBlock = false;

	function hasClass( block, className ) {
		var classes = block.attributes && block.attributes.className;
		return ( ' ' + ( classes || '' ) + ' ' ).indexOf( ' ' + className + ' ' ) !== -1;
	}

	blocks.registerBlockType( 'gymcast/related-guides', {
		apiVersion: 3,
		title: __( 'Related Guides', 'gymcast-marketing-tools' ),
		description: __( 'Shows manual choices first, then guides with shared tags.', 'gymcast-marketing-tools' ),
		icon: 'admin-links',
		category: 'widgets',
		attributes: {
			manualPostIds: { type: 'array', default: [], items: { type: 'number' } }
		},
		supports: { html: false, reusable: false },
		edit: function ( props ) {
			var manualIds = props.attributes.manualPostIds || [];
			var state = useState( '' );
			var search = state[ 0 ];
			var setSearch = state[ 1 ];
			var blockProps = blockEditor.useBlockProps( { className: 'gc-related-guides' } );
			var records = useSelect( function ( select ) {
				var store = select( 'core' );
				var categories = store.getEntityRecords( 'taxonomy', 'category', { slug: 'guide', per_page: 1 } );
				var selected = manualIds.map( function ( id ) {
					return store.getEntityRecord( 'postType', 'post', id );
				} ).filter( Boolean );
				var query = { per_page: 20, status: 'publish', exclude: manualIds };
				var options = [];
				if ( search ) {
					query.search = search;
				}
				if ( categories && categories[ 0 ] ) {
					query.categories = categories[ 0 ].id;
					options = store.getEntityRecords( 'postType', 'post', query ) || [];
				}
				return {
					options: options,
					selected: selected
				};
			}, [ manualIds.join( ',' ), search ] );

			function addPost( value ) {
				var id = parseInt( value, 10 );
				if ( id && manualIds.indexOf( id ) === -1 ) {
					props.setAttributes( { manualPostIds: manualIds.concat( [ id ] ) } );
				}
				setSearch( '' );
			}

			function removePost( id ) {
				props.setAttributes( { manualPostIds: manualIds.filter( function ( item ) { return item !== id; } ) } );
			}

			return createElement(
				'div',
				blockProps,
				createElement( 'h2', { className: 'wp-block-heading' }, __( 'Related Guides', 'gymcast-marketing-tools' ) ),
				createElement( 'p', null, __( 'Shows up to three manually selected guides or guides that share tags with this post. The section is hidden when there are no relevant results.', 'gymcast-marketing-tools' ) ),
				createElement(
					blockEditor.InspectorControls,
					null,
					createElement(
						components.PanelBody,
						{ title: __( 'Manual related guides', 'gymcast-marketing-tools' ), initialOpen: true },
						createElement( components.ComboboxControl, {
							label: __( 'Add a Guide post', 'gymcast-marketing-tools' ),
							value: '',
							options: records.options.map( function ( post ) {
								return { value: String( post.id ), label: post.title.rendered || __( '(Untitled)', 'gymcast-marketing-tools' ) };
							} ),
							onChange: addPost,
							onFilterValueChange: setSearch
						} ),
						records.selected.map( function ( post ) {
							return createElement(
								'div',
								{ className: 'gc-related-guides-selection', key: post.id },
								createElement( 'span', null, post.title.rendered || __( '(Untitled)', 'gymcast-marketing-tools' ) ),
								createElement( components.Button, { isDestructive: true, variant: 'link', onClick: function () { removePost( post.id ); } }, __( 'Remove', 'gymcast-marketing-tools' ) )
							);
						} )
					)
				),
				manualIds.length
					? createElement( 'p', null, __( 'Manual selections: ', 'gymcast-marketing-tools' ) + manualIds.length )
					: createElement( 'p', null, __( 'No manual selections. Related guides will be fully automatic.', 'gymcast-marketing-tools' ) )
			);
		},
		save: function () {
			return null;
		}
	} );

	/* Replace the original static link Group when an older guide is opened. */
	function migrateLegacyRelatedGuides() {
		var editor;
		var legacy = [];

		if ( isMigratingLegacyBlock ) {
			return;
		}
		editor = data.select( 'core/block-editor' );
		if ( ! editor ) {
			return;
		}

		function findLegacyBlocks( list ) {
			( list || [] ).forEach( function ( block ) {
				if ( block.name === 'core/group' && hasClass( block, 'gc-related-guides' ) ) {
					legacy.push( block.clientId );
				}
				findLegacyBlocks( block.innerBlocks );
			} );
		}

		findLegacyBlocks( editor.getBlocks() );
		if ( ! legacy.length ) {
			return;
		}

		isMigratingLegacyBlock = true;
		legacy.forEach( function ( clientId ) {
			data.dispatch( 'core/block-editor' ).replaceBlocks(
				clientId,
				blocks.createBlock( 'gymcast/related-guides' )
			);
		} );
		isMigratingLegacyBlock = false;
	}
	data.subscribe( migrateLegacyRelatedGuides );
	window.setTimeout( migrateLegacyRelatedGuides, 0 );
} )( window.wp.blocks, window.wp.blockEditor, window.wp.components, window.wp.coreData, window.wp.data, window.wp.element, window.wp.i18n );
