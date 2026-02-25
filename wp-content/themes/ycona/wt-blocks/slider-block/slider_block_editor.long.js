( function ( wp, blocks, element, data ) {

	var el                  = element.createElement,
		registerBlockType   = blocks.registerBlockType,
		withSelect          = data.withSelect,
		SelectControl       = wp.components.SelectControl,
		Fragment            = wp.element.Fragment,
		InspectorControls   = wp.blockEditor.InspectorControls,
		useBlockProps       = wp.blockEditor.useBlockProps;

	registerBlockType( 'wt/slider-block', {
		apiVersion: 3,
		title: 'Slider Block',
		icon: 'slides',
		category: 'wt-shop-blocks',
		description: 'Slider Block – Swiper with prev/next (hidden if only one slide).',
		example: {},
		attributes: {
			post_id: {
				type: 'string'
			},
			layout: {
				type: 'string'
			},
			space_top: {
				type: 'string',
			},
			space_bottom: {
				type: 'string',
			},
		},

		edit: withSelect( function ( select ) {

			var query = {
				orderby : 'title',
				order   : 'asc',
				per_page: -1,
			};

			return {
				posts: select( 'core' ).getEntityRecords( 'postType', 'slider', query ),
			};
		} )( function ( props ) {

			const space_bottom = [
				{ value: 'yes', label: 'Ja' },
				{ value: 'no', label: 'Nein' },
			];
			const space_top = [
				{ value: 'yes', label: 'Ja' },
				{ value: 'no', label: 'Nein' },
			];
			if ( ! props.attributes.space_top ) {
				props.setAttributes( { space_top: 'yes' } );

			}
			if ( ! props.attributes.space_bottom ) {
				props.setAttributes( { space_bottom: 'yes' } );
			}

			var options = [];

			if ( props.posts ) {
				options.push( { value: 0, label: 'Select...' } );
				props.posts.forEach( function ( post ) {
					options.push( { value: String( post.id ), label: post.title.raw } );
				} );
			} else {
				options.push( { value: 0, label: 'Loading...' } );
			}

			function on_change_post( new_value ) {
				props.setAttributes( { post_id: new_value } );
			}

			function update_layout( new_value ) {
				props.setAttributes( { layout: new_value } );
			}

			function update_space_bottom( new_value ) {
				props.setAttributes( { space_bottom: new_value } );
			}

			function update_space_top( new_value ) {
				props.setAttributes( { space_top: new_value } );
			}

			var blockProps = useBlockProps( { className: 'webthiker-block worker-block slider-block-editor' } );

			return (
				el( Fragment, null,
					el( InspectorControls, { class: 'wt-shop-SelectControl' },
						el( 'div', { class: 'webthiker-block-sidebar-element' },
							el( 'strong', null, 'Layout' ),
							el( SelectControl, {
								label: '',
								value: props.attributes.layout,
								options: [
									{ value: 'container', label: 'Container' },
									{ value: 'container-full', label: 'Full Width' },
								],
								onChange: update_layout,
							} ),
							el( 'dt', null, 'Space Top' ),
							el( SelectControl, {
								value: props.attributes.space_top,
								options: space_top,
								onChange: update_space_top,
							} ),
							el( 'dt', null, 'Space Bottom' ),
							el( SelectControl, {
								value: props.attributes.space_bottom,
								options: space_bottom,
								onChange: update_space_bottom,
							} ),
						)
					),
					el( 'div', blockProps,
						el( 'h3', null, 'Slider Block' ),
						el( 'dl', null,
							el( 'div', null,
								el( 'dt', null, 'Slider choose' ),
								el( 'dd', null,
									el( SelectControl, {
										value: props.attributes.post_id,
										options: options,
										onChange: on_change_post,
									} )
								),
							),
						),
					)
				)
			);
		} ),

		save: function () {
			return null;
		},
	} );
} )(
	window.wp,
	window.wp.blocks,
	window.wp.element,
	window.wp.data,
	window.wp.blockEditor
);
