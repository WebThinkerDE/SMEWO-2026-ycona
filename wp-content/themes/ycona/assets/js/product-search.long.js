/**
 * wt-shop AJAX Product Search
 *
 * Modern overlay search with debounce, keyboard navigation,
 * and full WooCommerce + WPML compatibility.
 *
 * @package wt-shop
 */
(function ($) {
    'use strict';

    /* ───── cache DOM ───── */
    var $trigger     = $('#wt-shop-search-trigger, #wt-shop-search-trigger-mobile');
    var $overlay     = $('#wt-shop-search-overlay');
    var $input       = $('#wt-shop-search-input');
    var $results     = $('#wt-shop-search-results');
    var $close       = $('#wt-shop-search-close');
    var $body        = $('body');


    /* ───── state ───── */
    var debounceTimer = null;
    var lastTerm      = '';
    var xhr           = null; // live XMLHttpRequest
    var activeIdx     = -1;   // keyboard highlight index

    /* ───── open / close ───── */
    function openSearch() {
        $overlay.addClass('is-active');
        $body.addClass('wt-shop-search-open');
        setTimeout(function () {
            $input.trigger('focus');
        }, 300);
    }

    function closeSearch() {
        $overlay.removeClass('is-active');
        $body.removeClass('wt-shop-search-open');
        $input.val('');
        $results.empty();
        lastTerm = '';
        activeIdx = -1;
    }

    /* trigger */
    $trigger.on('click', function (e) {
        e.preventDefault();
        openSearch();
    });

    /* close button */
    $close.on('click', function (e) {
        e.preventDefault();
        closeSearch();
    });

    /* click outside */
    $overlay.on('click', function (e) {
        if ($(e.target).is($overlay)) {
            closeSearch();
        }
    });

    /* ESC key */
    $(document).on('keydown', function (e) {
        if (e.key === 'Escape' && $overlay.hasClass('is-active')) {
            closeSearch();
        }
    });

    /* ───── search with debounce ───── */
    $input.on('input', function () {
        var term = $.trim($(this).val());

        clearTimeout(debounceTimer);
        if (xhr) {
            xhr.abort();
            xhr = null;
        }

        if (term.length < 2) {
            $results.empty();
            lastTerm = '';
            activeIdx = -1;
            return;
        }

        if (term === lastTerm) {
            return;
        }

        /* show loader */
        $results.html(
            '<div class="wt-shop-search-loading" role="status">' +
                '<span class="wt-shop-search-spinner"></span>' +
                '<span>' + wt_shop_search.i18n_searching + '</span>' +
            '</div>'
        );

        debounceTimer = setTimeout(function () {
            fetchResults(term);
        }, 320);
    });

    /* ───── AJAX fetch ───── */
    function fetchResults(term) {
        xhr = $.ajax({
            url:      wt_shop_search.ajaxurl,
            type:     'GET',
            dataType: 'json',
            data: {
                action: 'wt_shop_product_search',
                term:   term,
                nonce:  wt_shop_search.nonce
            },
            success: function (resp) {
                lastTerm  = term;
                activeIdx = -1;

                if (!resp.success || !resp.data.results.length) {
                    $results.html(
                        '<div class="wt-shop-search-empty">' +
                            '<i class="bi bi-emoji-frown" aria-hidden="true"></i>' +
                            '<span>' + wt_shop_search.i18n_no_results + '</span>' +
                        '</div>'
                    );
                    return;
                }

                renderResults(resp.data.results);
            },
            error: function (_, status) {
                if (status !== 'abort') {
                    $results.html(
                        '<div class="wt-shop-search-empty">' +
                            '<span>' + wt_shop_search.i18n_error + '</span>' +
                        '</div>'
                    );
                }
            },
            complete: function () {
                xhr = null;
            }
        });
    }

    /* ───── render ───── */
    function renderResults(items) {
        var html = '';
        $.each(items, function (i, item) {
            html += '<a href="' + item.url + '" class="wt-shop-search-item" data-idx="' + i + '">' +
                        '<div class="wt-shop-search-item-thumb">' +
                            '<img src="' + item.thumbnail + '" alt="' + item.title + '" loading="lazy" />' +
                        '</div>' +
                        '<div class="wt-shop-search-item-info">' +
                            '<span class="wt-shop-search-item-title">' + item.title + '</span>' +
                            (item.sku ? '<span class="wt-shop-search-item-sku">' + wt_shop_search.i18n_sku + ': ' + item.sku + '</span>' : '') +
                            '<span class="wt-shop-search-item-price">' + item.price + '</span>' +
                        '</div>' +
                        '<i class="bi bi-arrow-right wt-shop-search-item-arrow" aria-hidden="true"></i>' +
                    '</a>';
        });
        $results.html(html);
    }

    /* ───── keyboard navigation ───── */
    $input.on('keydown', function (e) {
        var $items = $results.find('.wt-shop-search-item');
        if (!$items.length) return;

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            activeIdx = Math.min(activeIdx + 1, $items.length - 1);
            highlightItem($items);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            activeIdx = Math.max(activeIdx - 1, 0);
            highlightItem($items);
        } else if (e.key === 'Enter' && activeIdx >= 0) {
            e.preventDefault();
            window.location.href = $items.eq(activeIdx).attr('href');
        }
    });

    function highlightItem($items) {
        $items.removeClass('is-highlighted');
        if (activeIdx >= 0) {
            $items.eq(activeIdx).addClass('is-highlighted');
            // scroll into view
            var el = $items.get(activeIdx);
            if (el && el.scrollIntoView) {
                el.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
            }
        }
    }

})(jQuery);
