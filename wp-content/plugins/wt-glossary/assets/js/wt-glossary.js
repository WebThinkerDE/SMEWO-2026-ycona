/**
 * WebThinker Glossary — Frontend JavaScript
 * Vanilla JS (no jQuery dependency).
 * Handles: A–Z filtering, live search with debounce, tooltips.
 *
 * @package WT_Glossary
 */

(function () {
    'use strict';

    /* ── Utilities ─────────────────────────────────────────────── */

    function wt_glossary_debounce(fn, delay) {
        var timer_id = null;
        return function () {
            var context = this;
            var args = arguments;
            clearTimeout(timer_id);
            timer_id = setTimeout(function () {
                fn.apply(context, args);
            }, delay);
        };
    }

    /* ── DOM Ready ─────────────────────────────────────────────── */

    document.addEventListener('DOMContentLoaded', function () {

        var wrappers = document.querySelectorAll('.wt-glossary-wrapper');

        if (!wrappers.length) {
            return;
        }

        wrappers.forEach(function (wrapper) {
            wt_glossary_init_instance(wrapper);
        });

        /* ── Tooltips for auto-linked glossary terms ───────────── */
        wt_glossary_init_tooltips();
    });

    function wt_glossary_init_instance(wrapper) {

        var search_input = wrapper.querySelector('.wt-glossary-search');
        var search_clear = wrapper.querySelector('.wt-glossary-search-clear');
        var nav_container = wrapper.querySelector('.webthinker-glossary-nav');
        var list_el = wrapper.querySelector('#wt-glossary-list');
        var ajax_results = wrapper.querySelector('#wt-glossary-ajax-results');
        var loading_el = wrapper.querySelector('#wt-glossary-loading');
        var recent_container = wrapper.querySelector('.wt-glossary-recent');
        var recent_list = recent_container ? recent_container.querySelector('.wt-glossary-recent-list') : null;
        var recent_clear = recent_container ? recent_container.querySelector('.wt-glossary-recent-clear') : null;

        var columns = wrapper.getAttribute('data-columns') || '2';
        var category = wrapper.getAttribute('data-category') || '';
        var active_letter = 'all';
        var is_loading = false;

        var recent_limit = 5;
        if (recent_container) {
            var limit_attr = parseInt(recent_container.getAttribute('data-max-items') || '5', 10);
            if (!isNaN(limit_attr) && limit_attr > 0) {
                recent_limit = limit_attr;
            }
        }

        var recent_storage_key = 'wtGlossaryRecentTerms';
        if (category) {
            recent_storage_key += ':' + category;
        }

        /* ── Recent searches ──────────────────────────────────── */

        function wt_glossary_get_recent_terms() {
            try {
                if (!window.localStorage) {
                    return [];
                }
                var raw = window.localStorage.getItem(recent_storage_key);
                var parsed = raw ? JSON.parse(raw) : [];
                return Array.isArray(parsed) ? parsed : [];
            } catch (e) {
                return [];
            }
        }

        function wt_glossary_set_recent_terms(items) {
            try {
                if (!window.localStorage) {
                    return;
                }
                window.localStorage.setItem(recent_storage_key, JSON.stringify(items));
            } catch (e) {
                // Ignore storage errors (private mode, etc.)
            }
        }

        function wt_glossary_render_recent_terms(items) {
            if (!recent_container || !recent_list) {
                return;
            }

            if (!items.length) {
                recent_container.style.display = 'none';
                recent_list.innerHTML = '';
                return;
            }

            recent_list.innerHTML = '';

            items.forEach(function (item) {
                if (!item || !item.title) {
                    return;
                }

                var button = document.createElement('button');
                button.type = 'button';
                button.className = 'wt-glossary-recent-item';
                button.setAttribute('data-term-title', item.title);
                if (item.permalink) {
                    button.setAttribute('data-term-url', item.permalink);
                }

                var title_el = document.createElement('span');
                title_el.className = 'wt-glossary-recent-item-title';
                title_el.textContent = item.title;
                button.appendChild(title_el);

                if (item.short_desc) {
                    var desc_el = document.createElement('span');
                    desc_el.className = 'wt-glossary-recent-item-desc';
                    desc_el.textContent = item.short_desc;
                    button.appendChild(desc_el);
                }

                recent_list.appendChild(button);
            });

            recent_container.style.display = '';
        }

        function wt_glossary_update_recent_terms(terms) {
            if (!terms || !terms.length) {
                return;
            }

            var current = wt_glossary_get_recent_terms();
            var next_terms = terms.slice(0, 1);

            next_terms.forEach(function (term) {
                if (!term || !term.title) {
                    return;
                }

                var term_id = term.id ? String(term.id) : '';
                var existing_index = -1;

                for (var i = 0; i < current.length; i++) {
                    var current_id = current[i].id ? String(current[i].id) : '';
                    if ((term_id && current_id === term_id) || current[i].title === term.title) {
                        existing_index = i;
                        break;
                    }
                }

                if (existing_index !== -1) {
                    current.splice(existing_index, 1);
                }

                current.unshift({
                    id: term.id || '',
                    title: term.title,
                    short_desc: term.short_desc || '',
                    permalink: term.permalink || ''
                });
            });

            current = current.slice(0, recent_limit);
            wt_glossary_set_recent_terms(current);
            wt_glossary_render_recent_terms(current);
        }

        if (recent_clear) {
            recent_clear.addEventListener('click', function () {
                wt_glossary_set_recent_terms([]);
                wt_glossary_render_recent_terms([]);
            });
        }

        if (recent_list && search_input) {
            recent_list.addEventListener('click', function (event) {
                var target = event.target.closest('.wt-glossary-recent-item');
                if (!target) {
                    return;
                }

                var term_title = target.getAttribute('data-term-title') || '';
                if (!term_title) {
                    return;
                }

                search_input.value = term_title;
                wt_glossary_toggle_clear_btn(true);
                wt_glossary_switch_to_all();
                wt_glossary_do_search(term_title);
            });
        }

        wt_glossary_render_recent_terms(wt_glossary_get_recent_terms());

        /* ── A–Z Navigation ────────────────────────────────────── */

        if (nav_container) {
            var nav_buttons = nav_container.querySelectorAll('.wt-glossary-nav-btn');

            nav_buttons.forEach(function (btn) {
                btn.addEventListener('click', function () {

                    if (btn.disabled) {
                        return;
                    }

                    var letter = btn.getAttribute('data-letter');

                    // Update active state
                    nav_buttons.forEach(function (b) {
                        b.classList.remove('wt-glossary-nav-btn-active');
                    });
                    btn.classList.add('wt-glossary-nav-btn-active');

                    active_letter = letter;

                    // Clear search when changing letter
                    if (search_input) {
                        search_input.value = '';
                        wt_glossary_toggle_clear_btn(false);
                    }

                    if (letter === 'all') {
                        // Show static content
                        wt_glossary_show_static_list();
                    } else {
                        // Filter by letter via client-side (no AJAX needed for letter-only)
                        wt_glossary_filter_by_letter(letter);
                    }
                });
            });
        }

        /* ── Client-side letter filtering ──────────────────────── */

        function wt_glossary_filter_by_letter(letter) {

            if (!list_el) {
                return;
            }

            wt_glossary_show_static_list();

            var sections = list_el.querySelectorAll('.wt-glossary-letter-section');
            var found = false;

            sections.forEach(function (section) {
                var section_letter = section.getAttribute('data-letter');
                if (section_letter === letter) {
                    section.style.display = '';
                    found = true;
                } else {
                    section.style.display = 'none';
                }
            });

            if (!found) {
                wt_glossary_show_no_results();
            }
        }

        /* ── Search ────────────────────────────────────────────── */

        if (search_input) {
            var debounced_search = wt_glossary_debounce(function () {
                var query = search_input.value.trim();

                wt_glossary_toggle_clear_btn(query.length > 0);

                if (query.length === 0) {
                    // Reset to current letter filter or show all
                    if (active_letter === 'all') {
                        wt_glossary_show_static_list();
                    } else {
                        wt_glossary_filter_by_letter(active_letter);
                    }
                    return;
                }

                if (query.length < 2) {
                    return;
                }

                wt_glossary_switch_to_all();
                wt_glossary_do_search(query);

            }, 300);

            search_input.addEventListener('input', debounced_search);

            search_input.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') {
                    search_input.value = '';
                    wt_glossary_toggle_clear_btn(false);
                    wt_glossary_show_static_list();
                }
            });
        }

        /* ── Clear button ──────────────────────────────────────── */

        if (search_clear) {
            search_clear.addEventListener('click', function () {
                if (search_input) {
                    search_input.value = '';
                    search_input.focus();
                }
                wt_glossary_toggle_clear_btn(false);
                wt_glossary_show_static_list();
            });
        }

        function wt_glossary_toggle_clear_btn(show) {
            if (search_clear) {
                search_clear.style.display = show ? 'block' : 'none';
            }
        }

        /**
         * Switch A–Z nav to "All" and update active state (e.g. when searching).
         */
        function wt_glossary_switch_to_all() {
            active_letter = 'all';
            if (!nav_container) {
                return;
            }
            var nav_buttons = nav_container.querySelectorAll('.wt-glossary-nav-btn');
            nav_buttons.forEach(function (b) {
                b.classList.remove('wt-glossary-nav-btn-active');
                if (b.getAttribute('data-letter') === 'all') {
                    b.classList.add('wt-glossary-nav-btn-active');
                }
            });
        }

        /* ── AJAX search ───────────────────────────────────────── */

        function wt_glossary_do_search(query) {

            if (is_loading) {
                return;
            }

            is_loading = true;
            wt_glossary_show_loading(true);

            var params = typeof wt_glossary_params !== 'undefined' ? wt_glossary_params : {};
            var form_data = new FormData();
            form_data.append('action', 'wt_glossary_search');
            form_data.append('nonce', params.nonce || '');
            form_data.append('search_query', query);
            form_data.append('letter', ''); // Search ignores letter
            form_data.append('category', category);
            form_data.append('columns', columns);

            fetch(params.ajax_url || '/wp-admin/admin-ajax.php', {
                method: 'POST',
                body: form_data,
                credentials: 'same-origin',
            })
                .then(function (response) {
                    return response.json();
                })
                .then(function (data) {
                    is_loading = false;
                    wt_glossary_show_loading(false);

                    if (data.success && ajax_results) {
                        if (list_el) {
                            list_el.style.display = 'none';
                        }
                        ajax_results.innerHTML = data.data.html;
                        ajax_results.style.display = '';

                        if (data.data && data.data.terms) {
                            wt_glossary_update_recent_terms(data.data.terms);
                        }
                    } else {
                        wt_glossary_show_no_results();
                    }
                })
                .catch(function () {
                    is_loading = false;
                    wt_glossary_show_loading(false);
                    wt_glossary_show_no_results();
                });
        }

        /* ── Show / hide helpers ───────────────────────────────── */

        function wt_glossary_show_static_list() {
            if (list_el) {
                list_el.style.display = '';
                var sections = list_el.querySelectorAll('.wt-glossary-letter-section');
                sections.forEach(function (s) {
                    s.style.display = '';
                });
            }
            if (ajax_results) {
                ajax_results.style.display = 'none';
                ajax_results.innerHTML = '';
            }
        }

        function wt_glossary_show_no_results() {
            var params = typeof wt_glossary_params !== 'undefined' ? wt_glossary_params : {};
            var msg = (params.i18n && params.i18n.no_results) || 'No terms found.';

            if (ajax_results) {
                if (list_el) {
                    list_el.style.display = 'none';
                }
                ajax_results.innerHTML = '<p class="wt-glossary-no-results">' + msg + '</p>';
                ajax_results.style.display = '';
            }
        }

        function wt_glossary_show_loading(show) {
            if (loading_el) {
                loading_el.style.display = show ? 'flex' : 'none';
            }
        }
    }

    /* ── Tooltip initialisation ────────────────────────────────── */

    function wt_glossary_init_tooltips() {

        var tooltip_links = document.querySelectorAll('.wt-glossary-tooltip-link');

        if (!tooltip_links.length) {
            return;
        }

        var active_tooltip = null;

        tooltip_links.forEach(function (link) {

            var desc = link.getAttribute('data-glossary-desc');
            if (!desc) {
                return;
            }

            link.addEventListener('mouseenter', function () {
                wt_glossary_remove_active_tooltip();

                var tooltip = document.createElement('div');
                tooltip.className = 'wt-glossary-tooltip';
                tooltip.textContent = desc;
                link.appendChild(tooltip);

                // Ensure tooltip fits in viewport
                requestAnimationFrame(function () {
                    var rect = tooltip.getBoundingClientRect();

                    if (rect.top < 0) {
                        tooltip.style.bottom = 'auto';
                        tooltip.style.top = 'calc(100% + 8px)';
                        tooltip.querySelector('::after') || null; // Arrow handled by CSS
                    }

                    if (rect.left < 8) {
                        tooltip.style.left = '0';
                        tooltip.style.transform = 'translateY(4px)';
                    } else if (rect.right > window.innerWidth - 8) {
                        tooltip.style.left = 'auto';
                        tooltip.style.right = '0';
                        tooltip.style.transform = 'translateY(4px)';
                    }

                    tooltip.classList.add('wt-glossary-tooltip-visible');
                });

                active_tooltip = tooltip;
            });

            link.addEventListener('mouseleave', function () {
                wt_glossary_remove_active_tooltip();
            });
        });

        function wt_glossary_remove_active_tooltip() {
            if (active_tooltip && active_tooltip.parentNode) {
                active_tooltip.parentNode.removeChild(active_tooltip);
                active_tooltip = null;
            }
        }
    }

})();
