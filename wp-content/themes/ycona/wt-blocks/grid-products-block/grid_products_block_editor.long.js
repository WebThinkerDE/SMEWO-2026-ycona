(function (blocks, element, blockEditor, components, data) {
    var el = element.createElement,
        Fragment = element.Fragment,
        useState = element.useState,
        registerBlockType = blocks.registerBlockType,
        withSelect = data.withSelect,
        InspectorControls = blockEditor.InspectorControls,
        useBlockProps = blockEditor.useBlockProps,
        SelectControl = components.SelectControl;

    registerBlockType('wt/grid-products-block', {
        apiVersion: 3,
        title: 'Grid products',
        icon: 'grid-view',
        category: 'wt-shop-blocks',
        description: 'Product grid – same design as you may like. Show random products or choose specific ones.',
        example: {},
        attributes: {
            layout: { type: 'string' },
            show_title: { type: 'string' },
            title: { type: 'string' },
            use_manual: { type: 'string' },
            product_ids: { type: 'string' },
            limit: { type: 'string' },
            space_top: { type: 'string' },
            space_bottom: { type: 'string' },


        },

        edit: withSelect(function (select) {
            return {
                products: select('core').getEntityRecords('postType', 'product', {
                    per_page: -1,
                    orderby: 'title',
                    order: 'asc',
                    status: 'publish',
                }),
            };
        })(function (props) {
            var attrs = props.attributes;
            var products = props.products || [];
            if (!attrs.layout) props.setAttributes({ layout: 'container' });
            if (!attrs.show_title) props.setAttributes({ show_title: 'yes' });
            if (!attrs.use_manual) props.setAttributes({ use_manual: 'no' });
            if (!attrs.space_top) props.setAttributes({ space_top: 'yes' });
            if (!attrs.space_bottom) props.setAttributes({ space_bottom: 'yes' });

            function setLayout(val) { props.setAttributes({ layout: val }); }
            function setShowTitle(val) { props.setAttributes({ show_title: val }); }
            function setTitle(e) { props.setAttributes({ title: e.target.value }); }
            function setUseManual(val) { props.setAttributes({ use_manual: val }); }
            function setLimit(e) { props.setAttributes({ limit: e.target.value }); }
            function setSpaceTop(val) { props.setAttributes({ space_top: val }); }
            function setSpaceBottom(val) { props.setAttributes({ space_bottom: val }); }

            function addProduct(id) {
                if (!id) return;
                var ids = (attrs.product_ids || '').split(',').map(function (s) { return s.trim(); }).filter(Boolean);
                if (ids.indexOf(String(id)) !== -1) return;
                ids.push(String(id));
                props.setAttributes({ product_ids: ids.join(',') });
            }

            function removeProduct(id) {
                var ids = (attrs.product_ids || '').split(',').map(function (s) { return s.trim(); }).filter(Boolean);
                ids = ids.filter(function (i) { return i !== String(id); });
                props.setAttributes({ product_ids: ids.join(',') });
            }

            var productSearchState = useState('');
            var productSearch = productSearchState[0];
            var setProductSearch = productSearchState[1];
            var searchFocusedState = useState(false);
            var searchFocused = searchFocusedState[0];
            var setSearchFocused = searchFocusedState[1];

            var selectedIds = (attrs.product_ids || '').split(',').map(function (s) { return s.trim(); }).filter(Boolean);
            var allProductOptions = [];
            products.forEach(function (p) {
                var name = (p.title && p.title.raw) ? p.title.raw : (p.name || 'Product #' + p.id);
                allProductOptions.push({ value: String(p.id), label: name });
            });
            var searchLower = (productSearch || '').toLowerCase();
            var productOptionsFiltered = searchLower
                ? allProductOptions.filter(function (opt) { return opt.label.toLowerCase().indexOf(searchLower) !== -1; })
                : allProductOptions;
            var dropdownOptions = productOptionsFiltered.filter(function (opt) { return selectedIds.indexOf(opt.value) === -1; });
            function getProductName(id) {
                var p = products.filter(function (x) { return String(x.id) === String(id); })[0];
                return p ? ((p.title && p.title.raw) ? p.title.raw : (p.name || '#' + id)) : '#' + id;
            }
            function getProductLabel(id) {
                var name = getProductName(id);
                return name + ' (' + id + ')';
            }

            var isManual = attrs.use_manual === 'yes';

            return el(Fragment, null,
                el(InspectorControls, { key: 'inspector', class: 'wt-shop-SelectControl' },
                    el('div', { className: 'webthiker-block-sidebar-element' },
                        el('strong', null, 'Layout'),
                        el(SelectControl, {
                            value: attrs.layout || 'container',
                            options: [
                                { value: 'container', label: 'Container' },
                                { value: 'container-full', label: 'Full Width' },
                            ],
                            onChange: setLayout,
                            __next40pxDefaultSize: true,
                            __nextHasNoMarginBottom: true,
                        }),
                        el('strong', null, 'Title'),
                        el(SelectControl, {
                            value: attrs.show_title || 'yes',
                            options: [
                                { value: 'yes', label: 'Show title' },
                                { value: 'no', label: 'Hide title' },
                            ],
                            onChange: setShowTitle,
                            __next40pxDefaultSize: true,
                            __nextHasNoMarginBottom: true,
                        }),
                        el('p', { className: 'description' }, 'Title text'),
                        el('input', {
                            type: 'text',
                            value: attrs.title || '',
                            placeholder: 'Products',
                            onChange: setTitle,
                            className: 'components-text-control__input',
                            style: { width: '100%', marginBottom: '12px' }
                        }),
                        el('p', { className: 'description' }, 'Products'),
                        el(SelectControl, {
                            value: attrs.use_manual || 'no',
                            options: [
                                { value: 'no', label: 'Random products' },
                                { value: 'yes', label: 'Choose products' },
                            ],
                            onChange: setUseManual,
                            __next40pxDefaultSize: true,
                            __nextHasNoMarginBottom: true,
                        }),
                        isManual && el('div', { className: 'wt-you-may-like-products-box', style: { marginTop: '8px' } },
                            el('div', { className: 'wt-you-may-like-products-box-inner' },
                                el('div', { className: 'wt-you-may-like-search-select', style: { position: 'relative', marginBottom: '8px' } },
                                    el('input', {
                                        type: 'text',
                                        value: productSearch,
                                        placeholder: 'Search and select product…',
                                        onChange: function (e) { setProductSearch(e.target.value); },
                                        onFocus: function () { setSearchFocused(true); },
                                        onBlur: function () { setTimeout(function () { setSearchFocused(false); }, 150); },
                                        className: 'wt-you-may-like-search components-text-control__input',
                                        style: { width: '100%', padding: '6px 8px', border: '1px solid #ddd', borderRadius: '4px', fontSize: '13px' }
                                    }),
                                    searchFocused && (productSearch !== '' || dropdownOptions.length <= 25) && el('div', {
                                        className: 'wt-you-may-like-search-dropdown',
                                        style: { position: 'absolute', left: 0, right: 0, top: '100%', marginTop: '2px', maxHeight: '200px', overflowY: 'auto', background: '#fff', border: '1px solid #ddd', borderRadius: '4px', boxShadow: '0 2px 6px rgba(0,0,0,0.1)', zIndex: 10 }
                                    }, dropdownOptions.length ? dropdownOptions.map(function (opt) {
                                        return el('button', {
                                            key: opt.value,
                                            type: 'button',
                                            className: 'wt-you-may-like-search-option',
                                            style: { display: 'block', width: '100%', padding: '8px 10px', border: 0, background: 0, textAlign: 'left', cursor: 'pointer', fontSize: '13px' },
                                            onClick: function () { addProduct(opt.value); setProductSearch(''); }
                                        }, opt.label);
                                    }) : el('div', { style: { padding: '8px 10px', fontSize: '13px', color: '#757575' } }, products.length ? 'No matching products' : 'Loading…'))
                                ),
                                selectedIds.length > 0 && el('div', { className: 'wt-you-may-like-chips' },
                                    selectedIds.map(function (id) {
                                        return el('div', {
                                            key: id,
                                            className: 'wt-you-may-like-chip'
                                        }, el('button', {
                                            type: 'button',
                                            'aria-label': 'Remove',
                                            className: 'wt-you-may-like-chip-remove',
                                            onClick: function () { removeProduct(id); }
                                        }, el('span', { className: 'dashicons dashicons-no-alt', style: { fontSize: '14px', width: '14px', height: '14px' } })),
                                            el('span', { className: 'wt-you-may-like-chip-label' }, getProductLabel(id)));
                                    })
                                )
                            )
                        ),
                        el('p', { className: 'description', style: { marginTop: '12px' } }, 'Max number of products'),
                        el('input', {
                            type: 'number',
                            min: 1,
                            value: attrs.limit !== undefined && attrs.limit !== '' ? attrs.limit : '',
                            placeholder: 'e.g. 8',
                            onChange: setLimit,
                            className: 'components-text-control__input',
                            style: { width: '100%' }
                        }),
                        el('strong', null, 'Abstand oben'),
                        el(SelectControl, {
                            value: attrs.space_top || 'yes',
                            options: [ { value: 'yes', label: 'Ja' }, { value: 'no', label: 'Nein' } ],
                            onChange: setSpaceTop,
                            __next40pxDefaultSize: true,
                            __nextHasNoMarginBottom: true,
                        }),
                        el('strong', null, 'Abstand unten'),
                        el(SelectControl, {
                            value: attrs.space_bottom || 'yes',
                            options: [ { value: 'yes', label: 'Ja' }, { value: 'no', label: 'Nein' } ],
                            onChange: setSpaceBottom,
                            __next40pxDefaultSize: true,
                            __nextHasNoMarginBottom: true,
                        })
                    )
                ),
                el('div', useBlockProps( {
                    id: 'grid-products-block',
                    className: 'webthiker-block worker-block wt-grid-products-block-editor'
                } ),
                    el('h3', null, 'Grid products'),
                    el('dl', null,
                        el('dt', null, el('span', null, 'Section title'), el('small', null, '(optional)')),
                        el('dd', null,
                            el('input', {
                                type: 'text',
                                value: attrs.title || '',
                                placeholder: 'Products',
                                onChange: setTitle,
                                className: 'components-text-control__input',
                                style: { width: '100%' }
                            })
                        ),
                        el('dt', null, el('span', null, 'Layout')),
                        el('dd', null, (attrs.layout || 'container') === 'container-full' ? 'Full width' : 'Container'),
                        el('dt', null, el('span', null, 'Products')),
                        el('dd', null,
                            isManual && selectedIds.length
                                ? selectedIds.map(getProductName).join(', ')
                                : 'Random products (max ' + (attrs.limit !== undefined && attrs.limit !== '' ? attrs.limit : '—') + ')'
                        ),
                        el('dt', null, el('span', null, 'Abstand oben')),
                        el('dd', null, (attrs.space_top || 'yes') === 'yes' ? 'Ja' : 'Nein'),
                        el('dt', null, el('span', null, 'Abstand unten')),
                        el('dd', null, (attrs.space_bottom || 'yes') === 'yes' ? 'Ja' : 'Nein')
                    )
                )
            );
        }),

        save: function () {
            return null;
        },
    });
})(window.wp.blocks, window.wp.element, window.wp.blockEditor, window.wp.components, window.wp.data);
