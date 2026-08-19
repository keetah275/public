( function ( blocks, blockEditor, components, data, element, i18n ) {
	'use strict';

	var createElement = element.createElement;
	var useSelect = data.useSelect;
	var useBlockProps = blockEditor.useBlockProps;
	var __ = i18n.__;
	var isUnlockingArticle = false;

	function hasClass( block, className ) {
		var classes = block.attributes && block.attributes.className;
		return ( ' ' + ( classes || '' ) + ' ' ).indexOf( ' ' + className + ' ' ) !== -1;
	}

	function textFromContent( content ) {
		var holder = document.createElement( 'div' );
		holder.innerHTML = content || '';
		return ( holder.textContent || '' ).trim();
	}

	function collectHeadings( article ) {
		var main = ( article.innerBlocks || [] ).find( function ( block ) {
			return block.name === 'core/group' && hasClass( block, 'gc-main-content' );
		} );
		var source = main ? main.innerBlocks : article.innerBlocks;
		var headings = [];

		function visit( list ) {
			( list || [] ).forEach( function ( block ) {
				if ( block.name === 'core/heading' && ( block.attributes.level || 2 ) === 2 ) {
					var title = textFromContent( block.attributes.content );
					if ( title ) {
						headings.push( title );
					}
				}
				if ( main && block.innerBlocks ) {
					visit( block.innerBlocks );
				}
			} );
		}

		visit( source );
		return headings;
	}

	/*
	 * WordPress 7 treats inserted unsynced patterns as content-only sections
	 * while metadata.patternName remains on their root block. That prevents
	 * inserting headings even though the Group itself is not template locked.
	 * Remove only that marker from Gymcast article roots so their structure is
	 * editable as the pattern intends.
	 */
	function unlockGymcastArticles() {
		var editor;
		var articles = [];

		if ( isUnlockingArticle ) {
			return;
		}

		editor = data.select( 'core/block-editor' );
		if ( ! editor ) {
			return;
		}

		function findArticles( list ) {
			( list || [] ).forEach( function ( block ) {
				if (
					block.name === 'core/group' &&
					hasClass( block, 'gc-resource-article' ) &&
					block.attributes.metadata &&
					block.attributes.metadata.patternName
				) {
					articles.push( block );
				}
				findArticles( block.innerBlocks );
			} );
		}

		findArticles( editor.getBlocks() );
		if ( ! articles.length ) {
			return;
		}

		isUnlockingArticle = true;
		articles.forEach( function ( article ) {
			var metadata = Object.assign( {}, article.attributes.metadata );
			delete metadata.patternName;
			data.dispatch( 'core/block-editor' ).updateBlockAttributes(
				article.clientId,
				{ metadata: metadata }
			);
		} );
		isUnlockingArticle = false;
	}
	data.subscribe( unlockGymcastArticles );
	window.setTimeout( unlockGymcastArticles, 0 );

	blocks.registerBlockType( 'gymcast/table-of-contents', {
		apiVersion: 3,
		title: __( 'Automatic Contents', 'gymcast-marketing-tools' ),
		description: __( 'Automatically lists H2 headings from the main article section.', 'gymcast-marketing-tools' ),
		icon: 'list-view',
		category: 'widgets',
		supports: { html: false, reusable: false },
		edit: function ( props ) {
			var articleData = useSelect( function ( select ) {
				var editor = select( 'core/block-editor' );
				var parents = editor.getBlockParents( props.clientId );
				var article;
				var main;

				parents.reverse().some( function ( clientId ) {
					var block = editor.getBlock( clientId );
					if ( block && block.name === 'core/group' && hasClass( block, 'gc-resource-article' ) ) {
						article = block;
						return true;
					}
					return false;
				} );
				if ( article ) {
					main = ( article.innerBlocks || [] ).find( function ( block ) {
						return block.name === 'core/group' && hasClass( block, 'gc-main-content' );
					} );
				}

				return {
					headings: article ? collectHeadings( article ) : [],
					mainClientId: main ? main.clientId : null,
					mainBlockCount: main ? main.innerBlocks.length : 0
				};
			}, [ props.clientId ] );
			var blockProps = useBlockProps( { className: 'gc-toc' } );
			var headings = articleData.headings;

			function addSection() {
				var heading;
				var paragraph;
				var editorDispatch;

				if ( ! articleData.mainClientId ) {
					return;
				}

				heading = blocks.createBlock( 'core/heading', {
					level: 2,
					placeholder: __( 'Section heading', 'gymcast-marketing-tools' )
				} );
				paragraph = blocks.createBlock( 'core/paragraph', {
					placeholder: __( 'Add section content…', 'gymcast-marketing-tools' )
				} );
				editorDispatch = data.dispatch( 'core/block-editor' );
				editorDispatch.insertBlocks(
					[ heading, paragraph ],
					articleData.mainBlockCount,
					articleData.mainClientId
				);
				editorDispatch.selectBlock( heading.clientId );
			}

			return createElement(
				'div',
				blockProps,
				createElement( 'h2', { className: 'wp-block-heading' }, __( 'Contents', 'gymcast-marketing-tools' ) ),
				headings.length
					? createElement( 'ul', null, headings.map( function ( heading, index ) {
						return createElement( 'li', { key: index }, heading );
					} ) )
					: createElement( components.Placeholder, {
						label: __( 'Automatic Contents', 'gymcast-marketing-tools' ),
						instructions: __( 'Add H2 headings inside Main Article Content.', 'gymcast-marketing-tools' )
					} ),
				articleData.mainClientId && createElement(
					components.Button,
					{
						variant: 'secondary',
						onClick: addSection,
						className: 'gc-add-section-button'
					},
					__( 'Add H2 section', 'gymcast-marketing-tools' )
				)
			);
		},
		save: function () {
			return null;
		}
	} );
} )( window.wp.blocks, window.wp.blockEditor, window.wp.components, window.wp.data, window.wp.element, window.wp.i18n );
