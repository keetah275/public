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

	blocks.registerBlockType( 'gymcast/faq-section', {
		apiVersion: 3,
		title: __( 'Dynamic FAQ Section', 'gymcast-marketing-tools' ),
		description: __( 'Uses FAQ posts selected manually or matched by shared tags.', 'gymcast-marketing-tools' ),
		icon: 'editor-help',
		category: 'widgets',
		attributes: {
			manualPostIds: { type: 'array', default: [], items: { type: 'number' } },
			automaticMatching: { type: 'boolean', default: true }
		},
		supports: { html: false, reusable: false },
		edit: function ( props ) {
			var manualIds = props.attributes.manualPostIds || [];
			var state = useState( '' );
			var search = state[ 0 ];
			var setSearch = state[ 1 ];
			var blockProps = blockEditor.useBlockProps( { className: 'gc-faq' } );
			var records = useSelect( function ( select ) {
				var store = select( 'core' );
				var categories = store.getEntityRecords( 'taxonomy', 'category', { slug: 'faq', per_page: 1 } );
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
				return { options: options, selected: selected };
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

			function movePost( index, direction ) {
				var target = index + direction;
				var reordered;
				var moved;
				if ( target < 0 || target >= manualIds.length ) {
					return;
				}
				reordered = manualIds.slice();
				moved = reordered.splice( index, 1 )[ 0 ];
				reordered.splice( target, 0, moved );
				props.setAttributes( { manualPostIds: reordered } );
			}

			return createElement(
				'div',
				blockProps,
				createElement( 'h2', { className: 'wp-block-heading' }, __( 'Frequently Asked Questions', 'gymcast-marketing-tools' ) ),
				createElement( 'p', null, __( 'FAQ post titles become questions and their post content becomes the answer. The section is hidden when empty.', 'gymcast-marketing-tools' ) ),
				createElement(
					blockEditor.InspectorControls,
					null,
					createElement(
						components.PanelBody,
						{ title: __( 'FAQ selection', 'gymcast-marketing-tools' ), initialOpen: true },
						createElement( components.ToggleControl, {
							label: __( 'Add FAQs with shared tags automatically', 'gymcast-marketing-tools' ),
							checked: props.attributes.automaticMatching,
							onChange: function ( value ) { props.setAttributes( { automaticMatching: value } ); }
						} ),
						createElement( components.ComboboxControl, {
							label: __( 'Add an FAQ post', 'gymcast-marketing-tools' ),
							value: '',
							options: records.options.map( function ( post ) {
								return { value: String( post.id ), label: post.title.rendered || __( '(Untitled)', 'gymcast-marketing-tools' ) };
							} ),
							onChange: addPost,
							onFilterValueChange: setSearch
						} ),
						records.selected.map( function ( post, index ) {
							return createElement(
								'div',
								{ className: 'gc-faq-selection', key: post.id },
								createElement( 'span', null, post.title.rendered || __( '(Untitled)', 'gymcast-marketing-tools' ) ),
								createElement(
									'div',
									null,
									createElement( components.Button, { variant: 'tertiary', disabled: index === 0, onClick: function () { movePost( index, -1 ); } }, __( 'Up', 'gymcast-marketing-tools' ) ),
									createElement( components.Button, { variant: 'tertiary', disabled: index === manualIds.length - 1, onClick: function () { movePost( index, 1 ); } }, __( 'Down', 'gymcast-marketing-tools' ) ),
									createElement( components.Button, { isDestructive: true, variant: 'link', onClick: function () { removePost( post.id ); } }, __( 'Remove', 'gymcast-marketing-tools' ) )
								)
							);
						} )
					)
				),
				manualIds.length
					? createElement( 'p', null, __( 'Manually selected FAQs: ', 'gymcast-marketing-tools' ) + manualIds.length )
					: createElement( 'p', null, __( 'No FAQs selected manually.', 'gymcast-marketing-tools' ) )
			);
		},
		save: function () {
			return null;
		}
	} );

	/* Replace the original hard-coded FAQ Group when an older guide is opened. */
	function migrateLegacyFaqs() {
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
				if ( block.name === 'core/group' && hasClass( block, 'gc-faq' ) ) {
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
			data.dispatch( 'core/block-editor' ).replaceBlocks( clientId, blocks.createBlock( 'gymcast/faq-section' ) );
		} );
		isMigratingLegacyBlock = false;
	}
	data.subscribe( migrateLegacyFaqs );
	window.setTimeout( migrateLegacyFaqs, 0 );
} )( window.wp.blocks, window.wp.blockEditor, window.wp.components, window.wp.coreData, window.wp.data, window.wp.element, window.wp.i18n );
