( function ( wp, blocks, element, data ) {

    var el                  = element.createElement,
        registerBlockType   = blocks.registerBlockType,
        withSelect          = data.withSelect,
        SelectControl       = wp.components.SelectControl,
        Fragment            = wp.element.Fragment,
        InspectorControls   = wp.blockEditor.InspectorControls,
        useBlockProps       = wp.blockEditor.useBlockProps;

    registerBlockType( 'wt/cards-block', {
        apiVersion: 3,
        title: 'Cards Block',
        icon: 'index-card',
        category: 'wt-shop-blocks',
        description: "Cards Block",
        example: {},


        attributes: {
            post_id: {
                type: 'string'
            },
            layout: {
                type: 'string'
            },
            columns: {
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
                order : 'asc',
                per_page: -1,
            }

            return {
                posts: select( 'core' ).getEntityRecords( 'postType', 'cards', query ),
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

            const columns_options = [
                { value: '2', label: '2 Columns' },
                { value: '3', label: '3 Columns' },
                { value: '4', label: '4 Columns' },
            ];

            if(!props.attributes.space_top)
            {
                props.setAttributes({space_top: "yes"})
            }

            if(!props.attributes.space_bottom)
            {
                props.setAttributes({space_bottom: "yes"})
            }

            if(!props.attributes.columns)
            {
                props.setAttributes({columns: "3"})
            }

            var options = [];

            if( props.posts )
            {
                options.push( { value: 0, label: 'Select...' } );

                props.posts.forEach((post) => {

                    options.push({value:post.id, label:post.title.raw });
                });
            }
            else
            {
                options.push( { value: 0, label: 'Loading...' } )
            }

            function on_change_post( newContent ) {
                props.setAttributes( { post_id: newContent } );
            }

            function update_layout (newValue) {
                props.setAttributes( { layout: newValue } )
            }

            function update_columns (newValue) {
                props.setAttributes( { columns: newValue } )
            }

            function update_space_bottom (newValue) {
                props.setAttributes( { space_bottom: newValue } )
            }

            function update_space_top (newValue) {
                props.setAttributes( { space_top: newValue } )
            }

            return (
                el(Fragment, null,
                    el(InspectorControls, {class: "wt-shop-SelectControl"},

                        el("div",
                            {
                                class: "webthiker-block-sidebar-element"
                            },
                            el("strong", null, "Layout"),
                            el(SelectControl,
                                {
                                    label: '',
                                    value: props.attributes.layout,
                                    options: [
                                        {
                                            value: 'container',
                                            label: 'Container'
                                        },
                                        {
                                            value: 'container-full',
                                            label: 'Full Width'
                                        }
                                    ],
                                    onChange: update_layout
                                }
                            ),
                            el("strong", null, "Columns"),
                            el(SelectControl,
                                {
                                    label: '',
                                    value: props.attributes.columns,
                                    options: columns_options,
                                    onChange: update_columns
                                }
                            ),
                            el("dt", null, "Space Top"),
                            el(SelectControl, {
                                value: props.attributes.space_top,
                                options: space_top,
                                onChange: update_space_top
                            }),

                            el("dt", null, "Space Bottom"),
                            el(SelectControl, {
                                value: props.attributes.space_bottom,
                                options: space_bottom,
                                onChange: update_space_bottom
                            }),

                        )
                    ),

                    el("div", useBlockProps( {
                            className: "webthiker-block worker-block count-" + props.attributes.number
                        } ),
                        el("h3", null, "Cards Block"),

                        el("dl", null,

                            el("div", null,

                                el("dt", null, "Cards choose"),
                                el("dd", null,
                                    el(SelectControl, {
                                        value: props.attributes.post_id,
                                        options: options,
                                        onChange: on_change_post,
                                    })
                                ),
         
                            ),
                        ),
                    )
                )
            );
        }),

        //set save function
        save: function( props ) {

            return null;
        }

    });
} )(
    window.wp,
    window.wp.blocks,
    window.wp.element,
    window.wp.data,
    window.wp.blockEditor
);
