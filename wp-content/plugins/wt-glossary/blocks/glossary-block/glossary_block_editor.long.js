( function ( wp, blocks, element, data ) {

    var el                  = element.createElement,
        Fragment            = wp.element.Fragment,
        registerBlockType   = blocks.registerBlockType,
        SelectControl       = wp.components.SelectControl,
        ToggleControl       = wp.components.ToggleControl,
        TextControl         = wp.components.TextControl,
        TextareaControl     = wp.components.TextareaControl,
        InspectorControls   = wp.blockEditor.InspectorControls,
        useBlockProps       = wp.blockEditor.useBlockProps,
        PanelBody           = wp.components.PanelBody;

    var i18n = ( typeof wt_glossary_editor_i18n !== 'undefined' ) ? wt_glossary_editor_i18n : {};
    var LETTERS = 'A B C D E F G H I J K L M N O P Q R S T U V W X Y Z'.split( ' ' );

    registerBlockType( 'wt/glossary-block', {
        apiVersion: 3,
        title: 'Glossar',
        icon: 'book-alt',
        category: 'wt-shop-blocks',
        description: 'Displays the glossary with A–Z navigation, search, and term cards.',
        example: {},
        keywords: [ 'glossary', 'glossar', 'terms', 'lexikon' ],

        attributes: {
            title: {
                type: 'string',
                default: 'Glossary'
            },
            description: {
                type: 'string',
                default: ''
            },
            search_label: {
                type: 'string',
                default: 'What word are you interested in?'
            },
            show_search: {
                type: 'string',
                default: 'yes'
            },
            show_nav: {
                type: 'string',
                default: 'yes'
            },
            columns: {
                type: 'string',
                default: '2'
            },
            category: {
                type: 'string',
                default: ''
            }
        },

        edit: function ( props ) {

            var attributes = props.attributes;

            return (
                el( Fragment, null,

                    el( InspectorControls, null,
                        el( PanelBody, { title: i18n.content || 'Content', initialOpen: true },

                            el( TextControl, {
                                label: i18n.title || 'Title',
                                value: attributes.title,
                                onChange: function ( v ) { props.setAttributes( { title: v } ); }
                            }),

                            el( TextareaControl, {
                                label: i18n.description || 'Description',
                                value: attributes.description,
                                onChange: function ( v ) { props.setAttributes( { description: v } ); }
                            }),

                            el( TextControl, {
                                label: i18n.search_label || 'Search Label',
                                value: attributes.search_label,
                                onChange: function ( v ) { props.setAttributes( { search_label: v } ); }
                            })
                        ),

                        el( PanelBody, { title: i18n.display_settings || 'Display Settings', initialOpen: false },

                            el( ToggleControl, {
                                label: i18n.show_search || 'Show Search',
                                checked: attributes.show_search === 'yes',
                                onChange: function ( v ) { props.setAttributes( { show_search: v ? 'yes' : 'no' } ); }
                            }),

                            el( ToggleControl, {
                                label: i18n.show_nav || 'Show A–Z Navigation',
                                checked: attributes.show_nav === 'yes',
                                onChange: function ( v ) { props.setAttributes( { show_nav: v ? 'yes' : 'no' } ); }
                            }),

                            el( SelectControl, {
                                label: i18n.columns || 'Columns',
                                value: attributes.columns,
                                options: [
                                    { value: '1', label: i18n.col_1 || '1 Column' },
                                    { value: '2', label: i18n.col_2 || '2 Columns' },
                                    { value: '3', label: i18n.col_3 || '3 Columns' },
                                    { value: '4', label: i18n.col_4 || '4 Columns' }
                                ],
                                onChange: function ( v ) { props.setAttributes( { columns: v } ); }
                            }),

                            el( TextControl, {
                                label: i18n.category_slug || 'Category Slug (optional)',
                                value: attributes.category || '',
                                placeholder: i18n.category_slug_hint || 'e.g. logistics',
                                onChange: function ( v ) { props.setAttributes( { category: v } ); }
                            })
                        )
                    ),

                    el( 'div', useBlockProps( { className: 'webthiker-block wt-glossary-editor-block' } ),

                        el( 'div', { className: 'wt-glossary-editor-preview' },

                            attributes.title
                                ? el( 'h2', { className: 'wt-glossary-editor-heading' }, attributes.title )
                                : null,

                            attributes.description
                                ? el( 'p', { className: 'wt-glossary-editor-description' }, attributes.description )
                                : null,

                            attributes.show_search === 'yes'
                                ? el( 'div', { className: 'wt-glossary-editor-search-area' },
                                    attributes.search_label
                                        ? el( 'div', { className: 'wt-glossary-editor-search-label' }, attributes.search_label )
                                        : null,
                                    el( 'div', { className: 'wt-glossary-editor-search-bar' },
                                        el( 'span', { className: 'dashicons dashicons-search wt-glossary-editor-search-icon' } ),
                                        el( 'span', { className: 'wt-glossary-editor-search-placeholder' }, i18n.search || 'Search' )
                                    ),
                                    el( 'div', { className: 'wt-glossary-editor-recent-row' },
                                        el( 'span', { className: 'wt-glossary-editor-recent-label' }, i18n.top_searched || 'Top searched:' ),
                                        el( 'span', { className: 'wt-glossary-editor-recent-tag' }, 'API' ),
                                        el( 'span', { className: 'wt-glossary-editor-recent-tag' }, 'SEO' ),
                                        el( 'span', { className: 'wt-glossary-editor-recent-tag' }, 'WordPress' )
                                    )
                                )
                                : null,

                            attributes.show_nav === 'yes'
                                ? el( 'div', { className: 'wt-glossary-editor-nav' },
                                    LETTERS.map( function ( letter ) {
                                        return el( 'span', {
                                            key: letter,
                                            className: 'wt-glossary-editor-nav-letter'
                                        }, letter );
                                    })
                                )
                                : null,

                            el( 'div', { className: 'wt-glossary-editor-cards-preview' },
                                el( 'div', { className: 'wt-glossary-editor-letter-heading' }, 'A' ),
                                el( 'div', { className: 'wt-glossary-editor-cards-row', 'data-cols': attributes.columns },
                                    el( 'div', { className: 'wt-glossary-editor-card' },
                                        el( 'div', { className: 'wt-glossary-editor-card-title' }, 'Aa Word' ),
                                        el( 'div', { className: 'wt-glossary-editor-card-desc' }, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce ac ex vulputate…' ),
                                        el( 'div', { className: 'wt-glossary-editor-card-link' }, ( i18n.know_more || 'Know More' ) + ' →' )
                                    ),
                                    el( 'div', { className: 'wt-glossary-editor-card' },
                                        el( 'div', { className: 'wt-glossary-editor-card-title' }, 'Aa Word' ),
                                        el( 'div', { className: 'wt-glossary-editor-card-desc' }, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce ac ex vulputate…' ),
                                        el( 'div', { className: 'wt-glossary-editor-card-link' }, ( i18n.know_more || 'Know More' ) + ' →' )
                                    )
                                )
                            )
                        )
                    )
                )
            );
        },

        save: function () {
            return null;
        }

    });
} )(
    window.wp,
    window.wp.blocks,
    window.wp.element,
    window.wp.data
);
