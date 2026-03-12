// native dom ready function
var dom_rdy = function(fn) {

    //sanity check
    if(typeof fn !== "function") {
        return;
    }

    //if document is already loaded, run method
    if(document.readyState === "complete") {
        return fn();
    }

    //otherwise, wait until document is loaded
    document.addEventListener("DOMContentLoaded",fn,false);
};

dom_rdy(function() {

    /* Functions for CPT Slider */
    // click for slider accordions
    jQuery(document).on("click","div.postbox .cpt-element .click-area", function (e) {

        // vars
        let s_box = jQuery(e.currentTarget.parentElement);

        // check if element has active class
        let check = s_box.hasClass("active");

        // toggle active class
        if(!check)
        {
            s_box.addClass("active");
        }
        else
        {
            s_box.removeClass("active");
        }
    })
    ;

    // event handler for select change "option" (link / box-text)
    jQuery(document).on("change","#slider_fields_meta_box select.slider-option",function (e) {

        // get/set vars
        let option        = jQuery( this ).val();
        let parent        = jQuery( e.currentTarget ).closest(".content-area");

        // toggle active class
        if(option === "link")
        {
            parent.removeClass("boxtext");
            parent.addClass("link");
        }
        else if(option === "boxtext")
        {
            parent.removeClass("link");
            parent.addClass("boxtext");
        }
    });


    jQuery(document).on("change","input.boxtextstyle2",function (e) {

        // get/set vars
        let option        = jQuery( this ).prop('checked');
        let parent        = jQuery( e.currentTarget ).closest(".slider-option-boxtext");

        // toggle active class
        if(option)
        {
            parent.addClass("boxtextstyle2");
        }
        else
        {
            parent.removeClass("boxtextstyle2");
        }
    });

// image selection popup - image button is clicked
    jQuery(document).on("click",
        '#squares_fields_meta_box .image-upload, ' +
        '#squares_fields_meta_box .image-upload-2, ' +
        '#teasers_fields_meta_box .image-upload, ' +
        '#icons_fields_meta_box .image-upload, ' +
        '#slider_fields_meta_box .image-upload, ' +
        '#testimonials_fields_meta_box .image-upload, ' +
        '#cards_fields_meta_box .image-upload, ' +
        '#testimonials_fields_meta_box .image-upload-2, ' +
        '#flags_fields_meta_box .image-upload '
        , function(e) {
            // prevents default action
            e.preventDefault();

            // get/set data
            let meta_image_frame, meta_image_preview, meta_image_id, meta_image;
            let target = jQuery(e.currentTarget);

            // get data attributes
            let id          = target.data("id");
            let imgsource   = target.hasClass('image-upload-3') ? 'meta-image-3' : (target.hasClass('image-upload-2') ? 'meta-image-2' : target.data("imgsource"));

            if (imgsource === 'meta-image-3') {
                meta_image_preview  = jQuery("#box-wrapper-"+id+" .image-preview-3 img");
                meta_image_id       = jQuery("#box-wrapper-"+id+" .meta-image-id-3");
                meta_image          = jQuery("#box-wrapper-"+id+" .meta-image-3");
            } else if (imgsource === 'meta-image-2') {
                meta_image_preview  = jQuery("#box-wrapper-"+id+" .image-preview-2 img");
                meta_image_id       = jQuery("#box-wrapper-"+id+" .meta-image-id-2");
                meta_image          = jQuery("#box-wrapper-"+id+" .meta-image-2");
            } else {
                meta_image_preview  = jQuery("#box-wrapper-"+id+" .image-preview img");
                meta_image_id       = jQuery("#box-wrapper-"+id+" .meta-image-id");
                meta_image          = jQuery("#box-wrapper-"+id+" .meta-image");
            }

            // sets up the media library frame
            meta_image_frame = wp.media({
                title: 'Select Media',
                multiple : false,
                library : {
                    type : 'image',
                }
            });

            // image is selected
            meta_image_frame.on('select', function() {

                // Grabs the attachment selection and creates a JSON representation of the model
                let media_attachment = meta_image_frame
                    .state()
                    .get('selection')
                    .first()
                    .toJSON();

                // Sends the attachment URL to custom image input field
                meta_image_id.val(media_attachment.id);
                meta_image.val(media_attachment.url);
                meta_image_preview.attr('src', media_attachment.url);
            });

            // open the media library frame
            meta_image_frame.open();
        });
// END - image selection popup

// image selection remove - image-remove button is clicked
    jQuery(document).on("click",
        '#squares_fields_meta_box .image-upload-remove, ' +
        '#squares_fields_meta_box .image-upload-remove-2, ' +
        '#teasers_fields_meta_box .image-upload-remove, ' +
        '#icons_fields_meta_box .image-upload-remove, ' +
        '#slider_fields_meta_box .image-upload-remove, ' +
        '#testimonials_fields_meta_box .image-upload-remove, ' +
        '#cards_fields_meta_box .image-upload-remove, ' +
        '#testimonials_fields_meta_box .image-upload-remove-2, ' +
        '#flags_fields_meta_box .image-upload-remove '
        , function(e) {
            console.log('button removed clicked');
            // prevents default action
            e.preventDefault();
            

            // get/set data
            let meta_image_preview, meta_image_id, meta_image;
            let target = jQuery(e.currentTarget);


            // get data attributes
            let id          = target.data("id");
            let imgsource   = target.hasClass('image-upload-remove-3') ? 'meta-image-3' : (target.hasClass('image-upload-remove-2') ? 'meta-image-2' : target.data("imgsource"));

            if (imgsource === 'meta-image-3') {
                meta_image_preview  = jQuery("#box-wrapper-"+id+" .image-preview-3 img");
                meta_image_id       = jQuery("#box-wrapper-"+id+" .meta-image-id-3");
                meta_image          = jQuery("#box-wrapper-"+id+" .meta-image-3");
            } else if (imgsource === 'meta-image-2') {
                meta_image_preview  = jQuery("#box-wrapper-"+id+" .image-preview-2 img");
                meta_image_id       = jQuery("#box-wrapper-"+id+" .meta-image-id-2");
                meta_image          = jQuery("#box-wrapper-"+id+" .meta-image-2");
            } else {
                meta_image_preview  = jQuery("#box-wrapper-"+id+" .image-preview img");
                meta_image_id       = jQuery("#box-wrapper-"+id+" .meta-image-id");
                meta_image          = jQuery("#box-wrapper-"+id+" .meta-image");
            }

            meta_image_preview.attr("src", "");
            meta_image_id.val("");
            meta_image.val("");
        });
// END - image selection remove





    // sort Functions
    // after click on down - generic selector for all CPTs
    jQuery(document).on('click', '.wt-wrapper-cpt .sort-down', function(e) {
        let c_card = jQuery(this).closest('.cpt-element');
        let t_card = c_card.next('.cpt-element');

        c_card.insertAfter(t_card);

        set_buttons();
        reset_sort();
    });

    // after click on up - generic selector for all CPTs
    jQuery(document).on('click', '.wt-wrapper-cpt .sort-up', function(e) {
        let c_card = jQuery(this).closest('.cpt-element');
        let t_card = c_card.prev('.cpt-element');

        c_card.insertBefore(t_card);

        set_buttons();
        reset_sort();
    });


    /* Functions for CPT */
    // remove element - works for all CPTs inside .wt-wrapper-cpt
    jQuery(document).on('click', '.wt-wrapper-cpt .remove', function() {
        jQuery(this).closest('.cpt-element').remove();
        set_buttons();
        reset_sort();
    });

    // remove element - for sliders/squares (legacy)
    jQuery(document).on('click', '.remove', function() {
        var type = jQuery(this).data("type");

        if(type === "cpt-element")
        {
            jQuery(this).closest('.cpt-element').remove();
            set_buttons();
            reset_sort();

        }
        else if (!jQuery(this).closest('.wt-wrapper-cpt').length)
        {
            jQuery(this).closest('.slider').remove();
            jQuery(this).closest('.squares').remove();
        }
    });
    /* END - Functions for CPT */

    /* END - DOM-Ready */
});

/* Functions for CPT */
function get_existing_elements(name_of_element){

    let count = jQuery(name_of_element).length;

    return count+1;
}

// Generic setButtons - works for all CPTs
function set_buttons(){
    // Show all sort buttons first
    jQuery('.wt-wrapper-cpt .sort-buttons button').show();

    // For each CPT wrapper, hide the first sort-up and last sort-down
    jQuery('.wt-wrapper-cpt').each(function() {
        jQuery(this).find('.cpt-element:first-child .sort-up').hide();
        jQuery(this).find('.cpt-element:last-child .sort-down').hide();
    });
}

// Generic resetSort - works for all CPTs
function reset_sort(){
    jQuery('.wt-wrapper-cpt').each(function() {
        var i = 0;
        jQuery(this).find('.cpt-element').each(function(){
            jQuery(this).attr("data-sort", i);
            i++;
        });
    });

}
// Theme options: persist tab in URL (param) and restore on load/refresh
(function theme_options_tab_url() {
    function get_tab_from_url() {
        var hash = (window.location.hash || '').replace(/^#/, '');
        if (hash) return hash;
        var url_params = new URLSearchParams(window.location.search);
        return url_params.get('tab') || '';
    }

    function set_tab_in_url(tab_id) {
        if (!tab_id) return;
        var current_url = new URL(window.location.href);
        current_url.searchParams.set('tab', tab_id);
        if (window.history && window.history.replaceState) {

            window.history.replaceState(null, '', current_url.pathname + (current_url.search ? current_url.search : '') + (current_url.hash || ''));
        }
    }
    function switch_to_tab(tab_id) {
        if (!tab_id) return;
        var $nav = jQuery('.wt-nav-item[data-target="#' + tab_id + '"]');
        var $panel = jQuery('.wt-tab-panel#' + tab_id);
        if ($nav.length && $panel.length) {
            jQuery('.wt-nav-item').removeClass('active').attr('aria-selected', 'false');
            jQuery('.wt-tab-panel').removeClass('active').attr('aria-hidden', 'true');
            $nav.addClass('active').attr('aria-selected', 'true');
            $panel.addClass('active').attr('aria-hidden', 'false');
        }
    }
    function init_tab_url() {
        if (!jQuery('.wt-tab-panel').length) return;
        var tab_id = get_tab_from_url();
        if (tab_id) switch_to_tab(tab_id);
        jQuery(document).on('click', '.wt-nav-item[data-target]', function() {
            var target = jQuery(this).attr('data-target') || '';
            var tab_id = target.replace(/^#/, '');
            if (tab_id) set_tab_in_url(tab_id);
        });
    }
    if (document.readyState === 'loading') {
        jQuery(document).ready(init_tab_url);
    } else {
        init_tab_url();
    }
})();

/* Program Builder admin editor */
(function program_builder_admin() {
    function ready(fn) {
        if (document.readyState === "loading") {
            document.addEventListener("DOMContentLoaded", fn);
        } else {
            fn();
        }
    }

    ready(function() {
        var $editor = jQuery("#program-builder-editor");
        if (!$editor.length) {
            return;
        }

        var $chaptersList = $editor.find("#pb-chapters-list");
        var $modal = jQuery("#pb-edit-modal");
        var $modalKindWrap = jQuery("#pb-modal-kind-wrap");
        var $modalMinuteWrap = jQuery("#pb-modal-minute-wrap");
        var $modalChapterVideoWrap = jQuery("#pb-modal-chapter-video-wrap");
        var previewEventTimer = null;

        function escape_html(value) {
            return String(value || "")
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }

        function chapter_html() {
            return '' +
                '<div class="pb-chapter-row" draggable="true" data-chapter-index="0">' +
                '  <div class="pb-row-header">' +
                '    <span class="pb-drag-handle dashicons dashicons-move"></span>' +
                '    <span class="pb-position-label">01</span>' +
                '    <strong class="pb-row-title">Kapitel</strong>' +
                '    <div class="pb-row-actions">' +
                '      <select class="pb-new-item-kind">' +
                '        <option value="item">Item</option>' +
                '        <option value="quiz">Quiz</option>' +
                '        <option value="game">Game</option>' +
                '        <option value="info">Info</option>' +
                '        <option value="bewertung">Bewertung</option>' +
                '      </select>' +
                '      <button type="button" class="button button-small pb-add-item">+ Hinzufugen</button>' +
                '      <button type="button" class="button button-small pb-edit-btn" data-type="chapter"><span class="dashicons dashicons-edit"></span></button>' +
                '      <button type="button" class="button button-small pb-delete-btn" data-type="chapter"><span class="dashicons dashicons-trash"></span></button>' +
                '    </div>' +
                '  </div>' +
                '  <input type="hidden" class="pb-field-chapter-title" name="program_builder_fields[chapters][0][title]" value="">' +
                '  <input type="hidden" class="pb-field-chapter-short" name="program_builder_fields[chapters][0][short_description]" value="">' +
                '  <input type="hidden" class="pb-field-chapter-description" name="program_builder_fields[chapters][0][description]" value="">' +
                '  <input type="hidden" class="pb-field-chapter-date" name="program_builder_fields[chapters][0][date]" value="">' +
                '  <input type="hidden" class="pb-field-chapter-video-title" name="program_builder_fields[chapters][0][video_title]" value="">' +
                '  <input type="hidden" class="pb-field-chapter-video-mp4" name="program_builder_fields[chapters][0][video_mp4]" value="">' +
                '  <input type="hidden" class="pb-field-chapter-video-audio" name="program_builder_fields[chapters][0][video_audio]" value="">' +
                '  <input type="hidden" class="pb-field-chapter-video-poster" name="program_builder_fields[chapters][0][video_poster]" value="">' +
                '  <input type="hidden" class="pb-field-chapter-subtitle-1" name="program_builder_fields[chapters][0][subtitle_1]" value="">' +
                '  <input type="hidden" class="pb-field-chapter-subtitle-2" name="program_builder_fields[chapters][0][subtitle_2]" value="">' +
                '  <input type="hidden" class="pb-field-chapter-audio-1" name="program_builder_fields[chapters][0][audio_1]" value="">' +
                '  <input type="hidden" class="pb-field-chapter-audio-2" name="program_builder_fields[chapters][0][audio_2]" value="">' +
                '  <div class="pb-items-list"></div>' +
                '</div>';
        }

        function normalize_item_kind(kind) {
            var allowed = ["item", "quiz", "game", "info", "bewertung"];
            return allowed.indexOf(kind) !== -1 ? kind : "item";
        }

        function kind_display(kind) {
            if (!kind) {
                return "Item";
            }
            return kind.charAt(0).toUpperCase() + kind.slice(1);
        }

        function pad2(num) {
            var n = parseInt(num, 10);
            if (isNaN(n) || n < 0) {
                n = 0;
            }
            return n < 10 ? "0" + n : String(n);
        }

        function parse_minute_to_seconds(value) {
            var input = String(value || "").trim();
            if (!input) {
                return null;
            }
            if (/^\d+$/.test(input)) {
                return parseInt(input, 10);
            }
            var match = input.match(/^(\d{1,2}):(\d{1,2})$/);
            if (!match) {
                return null;
            }
            var mins = parseInt(match[1], 10);
            var secs = parseInt(match[2], 10);
            if (isNaN(mins) || isNaN(secs) || secs > 59) {
                return null;
            }
            return mins * 60 + secs;
        }

        function seconds_to_minute(seconds) {
            var s = parseInt(seconds, 10);
            if (isNaN(s) || s < 0) {
                s = 0;
            }
            var mins = Math.floor(s / 60);
            var secs = s % 60;
            return pad2(mins) + ":" + pad2(secs);
        }

        function show_preview_event(message) {
            var $toast = $editor.find("#pb-preview-event-toast");
            if (!$toast.length) {
                return;
            }
            if (previewEventTimer) {
                window.clearTimeout(previewEventTimer);
                previewEventTimer = null;
            }
            $toast.text(message).removeAttr("hidden");
            previewEventTimer = window.setTimeout(function() {
                $toast.attr("hidden", "hidden").text("");
            }, 3500);
        }

        function item_html(kind) {
            var itemKind = normalize_item_kind(kind || "item");
            var defaultTitle = kind_display(itemKind);
            return '' +
                '<div class="pb-item-row" draggable="true" data-item-index="0">' +
                '  <span class="pb-drag-handle dashicons dashicons-menu"></span>' +
                '  <span class="pb-position-label">01|01</span>' +
                '  <span class="pb-row-title">' + defaultTitle + '</span>' +
                '  <span class="pb-item-kind">' + defaultTitle + '</span>' +
                '  <span class="pb-item-minute"></span>' +
                '  <div class="pb-row-actions">' +
                '    <button type="button" class="button button-small pb-edit-btn" data-type="item"><span class="dashicons dashicons-edit"></span></button>' +
                '    <button type="button" class="button button-small pb-delete-btn" data-type="item"><span class="dashicons dashicons-trash"></span></button>' +
                '  </div>' +
                '  <input type="hidden" class="pb-field-item-title" name="program_builder_fields[chapters][0][items][0][title]" value="' + defaultTitle + '">' +
                '  <input type="hidden" class="pb-field-item-short" name="program_builder_fields[chapters][0][items][0][short_description]" value="">' +
                '  <input type="hidden" class="pb-field-item-description" name="program_builder_fields[chapters][0][items][0][description]" value="">' +
                '  <input type="hidden" class="pb-field-item-date" name="program_builder_fields[chapters][0][items][0][date]" value="">' +
                '  <input type="hidden" class="pb-field-item-kind" name="program_builder_fields[chapters][0][items][0][kind]" value="' + itemKind + '">' +
                '  <input type="hidden" class="pb-field-item-minute" name="program_builder_fields[chapters][0][items][0][minute]" value="">' +
                '</div>';
        }

        function reindex_program_builder() {
            $chaptersList.find(".pb-chapter-row").each(function(chapterIndex) {
                var $chapter = jQuery(this);
                $chapter.attr("data-chapter-index", chapterIndex);
                $chapter.find("> .pb-row-header .pb-position-label").text(String(chapterIndex + 1).padStart(2, "0"));

                $chapter.find(".pb-field-chapter-title").attr("name", "program_builder_fields[chapters][" + chapterIndex + "][title]");
                $chapter.find(".pb-field-chapter-short").attr("name", "program_builder_fields[chapters][" + chapterIndex + "][short_description]");
                $chapter.find(".pb-field-chapter-description").attr("name", "program_builder_fields[chapters][" + chapterIndex + "][description]");
                $chapter.find(".pb-field-chapter-date").attr("name", "program_builder_fields[chapters][" + chapterIndex + "][date]");
                $chapter.find(".pb-field-chapter-video-title").attr("name", "program_builder_fields[chapters][" + chapterIndex + "][video_title]");
                $chapter.find(".pb-field-chapter-video-mp4").attr("name", "program_builder_fields[chapters][" + chapterIndex + "][video_mp4]");
                $chapter.find(".pb-field-chapter-video-audio").attr("name", "program_builder_fields[chapters][" + chapterIndex + "][video_audio]");
                $chapter.find(".pb-field-chapter-video-poster").attr("name", "program_builder_fields[chapters][" + chapterIndex + "][video_poster]");
                $chapter.find(".pb-field-chapter-subtitle-1").attr("name", "program_builder_fields[chapters][" + chapterIndex + "][subtitle_1]");
                $chapter.find(".pb-field-chapter-subtitle-2").attr("name", "program_builder_fields[chapters][" + chapterIndex + "][subtitle_2]");
                $chapter.find(".pb-field-chapter-audio-1").attr("name", "program_builder_fields[chapters][" + chapterIndex + "][audio_1]");
                $chapter.find(".pb-field-chapter-audio-2").attr("name", "program_builder_fields[chapters][" + chapterIndex + "][audio_2]");

                $chapter.find(".pb-items-list .pb-item-row").each(function(itemIndex) {
                    var $item = jQuery(this);
                    $item.attr("data-item-index", itemIndex);
                    $item.find("> .pb-position-label").text(
                        String(chapterIndex + 1).padStart(2, "0") + "|" + String(itemIndex + 1).padStart(2, "0")
                    );

                    $item.find(".pb-field-item-title").attr("name", "program_builder_fields[chapters][" + chapterIndex + "][items][" + itemIndex + "][title]");
                    $item.find(".pb-field-item-short").attr("name", "program_builder_fields[chapters][" + chapterIndex + "][items][" + itemIndex + "][short_description]");
                    $item.find(".pb-field-item-description").attr("name", "program_builder_fields[chapters][" + chapterIndex + "][items][" + itemIndex + "][description]");
                    $item.find(".pb-field-item-date").attr("name", "program_builder_fields[chapters][" + chapterIndex + "][items][" + itemIndex + "][date]");
                    $item.find(".pb-field-item-kind").attr("name", "program_builder_fields[chapters][" + chapterIndex + "][items][" + itemIndex + "][kind]");
                    $item.find(".pb-field-item-minute").attr("name", "program_builder_fields[chapters][" + chapterIndex + "][items][" + itemIndex + "][minute]");
                });
            });
        }

        function chapter_preview_markup($chapter) {
            if (!$chapter || !$chapter.length) {
                return '<div class="pb-preview-empty">Kein Kapitel-Video ausgewählt</div>';
            }
            var mp4 = $chapter.find(".pb-field-chapter-video-mp4").val() || "";
            var poster = $chapter.find(".pb-field-chapter-video-poster").val() || "";
            var fallbackAudio = $chapter.find(".pb-field-chapter-video-audio").val() || "";

            if (mp4) {
                var posterAttr = poster ? ' poster="' + escape_html(poster) + '"' : "";
                return '<video id="pb-preview-video" controls preload="metadata"' + posterAttr + '><source src="' + escape_html(mp4) + '" type="video/mp4"></video>';
            }
            if (fallbackAudio) {
                return '<audio id="pb-preview-audio" controls preload="metadata"><source src="' + escape_html(fallbackAudio) + '"></audio>';
            }
            return '<div class="pb-preview-empty">Kein Kapitel-Video ausgewählt</div>';
        }

        function update_preview_for_chapter($chapter) {
            var $preview = $editor.find("#pb-preview-video-wrap");
            if (!$chapter || !$chapter.length) {
                $preview.html('<div class="pb-preview-empty">Kein Kapitel-Video ausgewählt</div>');
                $editor.find("#pb-preview-title").text("");
                $editor.find("#pb-preview-description").text("");
                $editor.find("#pb-preview-event-toast").attr("hidden", "hidden").text("");
                return;
            }
            $preview.html(chapter_preview_markup($chapter));
            var videoTitle = $chapter.find(".pb-field-chapter-video-title").val() || "";
            var chapterTitle = $chapter.find(".pb-field-chapter-title").val() || "Kapitel";
            var chapterShort = $chapter.find(".pb-field-chapter-short").val() || "";
            $editor.find("#pb-preview-title").text(videoTitle || chapterTitle);
            $editor.find("#pb-preview-description").text(chapterShort);
            $editor.find("#pb-preview-event-toast").attr("hidden", "hidden").text("");
            setup_preview_scheduler($chapter);
        }

        function get_active_chapter() {
            var $active = $chaptersList.find(".pb-chapter-row.is-active").first();
            if ($active.length) {
                return $active;
            }
            var $first = $chaptersList.find(".pb-chapter-row").first();
            if ($first.length) {
                $first.addClass("is-active");
            }
            return $first;
        }

        function update_preview() {
            update_preview_for_chapter(get_active_chapter());
        }

        function render_program_timeline() {
            var $points = jQuery("#pb-general-timeline-points");
            if (!$points.length) {
                return;
            }
            var entries = [];
            $chaptersList.find(".pb-chapter-row").each(function(chapterIndex) {
                var $chapter = jQuery(this);
                var chapterTitle = $chapter.find("> .pb-row-header .pb-row-title").text().trim() || ("Kapitel " + (chapterIndex + 1));
                entries.push({ label: chapterTitle, type: "chapter" });
                $chapter.find(".pb-item-row").each(function() {
                    var itemTitle = jQuery(this).find("> .pb-row-title").text().trim();
                    var itemKind = jQuery(this).find(".pb-field-item-kind").val() || "item";
                    var itemMinute = jQuery(this).find(".pb-field-item-minute").val() || "";
                    var itemLabel = itemTitle || (itemKind.charAt(0).toUpperCase() + itemKind.slice(1));
                    if (itemMinute) {
                        itemLabel += " (" + itemMinute + ")";
                    }
                    entries.push({ label: itemLabel, type: "item" });
                });
            });

            if (!entries.length) {
                entries.push({ label: "Start", type: "chapter" });
            }

            var minWidth = entries.length * 140;
            var $timeline = jQuery("#pb-general-timeline");
            $timeline.css("min-width", minWidth + "px");

            var markup = "";
            for (var i = 0; i < entries.length; i++) {
                var left = entries.length === 1 ? 0 : (i / (entries.length - 1)) * 100;
                var cls = "pb-timeline-point pb-tp-" + entries[i].type;
                markup += '' +
                    '<div class="' + cls + '" style="left:' + left + '%">' +
                    '  <span class="pb-timeline-connector"></span>' +
                    '  <span class="pb-timeline-dot"></span>' +
                    '  <span class="pb-timeline-label">' + escape_html(entries[i].label) + '</span>' +
                    '</div>';
            }
            $points.html(markup);
        }

        function sort_items_by_minute($chapter) {
            if (!$chapter || !$chapter.length) {
                return;
            }
            var $list = $chapter.find(".pb-items-list").first();
            var rows = $list.find(".pb-item-row").get();
            rows.sort(function(a, b) {
                var aRaw = jQuery(a).find(".pb-field-item-minute").val() || "";
                var bRaw = jQuery(b).find(".pb-field-item-minute").val() || "";
                var aSec = parse_minute_to_seconds(aRaw);
                var bSec = parse_minute_to_seconds(bRaw);
                if (aSec === null && bSec === null) {
                    return 0;
                }
                if (aSec === null) {
                    return 1;
                }
                if (bSec === null) {
                    return -1;
                }
                return aSec - bSec;
            });
            for (var i = 0; i < rows.length; i++) {
                $list.append(rows[i]);
            }
        }

        function sort_all_items_by_minute() {
            $chaptersList.find(".pb-chapter-row").each(function() {
                sort_items_by_minute(jQuery(this));
            });
        }

        function setup_preview_scheduler($chapter) {
            var $previewWrap = $editor.find("#pb-preview-video-wrap");
            var mediaEl = $previewWrap.find("video, audio").get(0);
            if (!mediaEl) {
                return;
            }
            var events = [];
            $chapter.find(".pb-item-row").each(function() {
                var $item = jQuery(this);
                var minuteRaw = $item.find(".pb-field-item-minute").val() || "";
                var seconds = parse_minute_to_seconds(minuteRaw);
                if (seconds === null) {
                    return;
                }
                events.push({
                    seconds: seconds,
                    minute: seconds_to_minute(seconds),
                    kind: ($item.find(".pb-field-item-kind").val() || "item"),
                    title: ($item.find(".pb-field-item-title").val() || "Item"),
                    fired: false
                });
            });
            events.sort(function(a, b) {
                return a.seconds - b.seconds;
            });

            if (mediaEl._pbSchedulerBound) {
                mediaEl.removeEventListener("timeupdate", mediaEl._pbSchedulerBound);
            }
            if (mediaEl._pbSchedulerSeekedBound) {
                mediaEl.removeEventListener("seeked", mediaEl._pbSchedulerSeekedBound);
            }

            var onTimeUpdate = function() {
                var now = Math.floor(mediaEl.currentTime || 0);
                for (var i = 0; i < events.length; i++) {
                    var evt = events[i];
                    if (!evt.fired && now >= evt.seconds) {
                        evt.fired = true;
                        var kindText = kind_display(evt.kind);
                        show_preview_event(kindText + " @ " + evt.minute + " - " + evt.title);
                    }
                }
            };
            var onSeeked = function() {
                var now = Math.floor(mediaEl.currentTime || 0);
                for (var i = 0; i < events.length; i++) {
                    events[i].fired = events[i].seconds < now;
                }
            };

            mediaEl.addEventListener("timeupdate", onTimeUpdate);
            mediaEl.addEventListener("seeked", onSeeked);
            mediaEl._pbSchedulerBound = onTimeUpdate;
            mediaEl._pbSchedulerSeekedBound = onSeeked;
        }

        function open_modal($targetRow, type) {
            var title = "";
            var shortValue = "";
            var date = "";
            var kind = "item";
            var minute = "";
            var videoTitle = "";
            var videoMp4 = "";
            var videoAudio = "";
            var videoPoster = "";
            var subtitle1 = "";
            var subtitle2 = "";
            var audio1 = "";
            var audio2 = "";

            if (type === "chapter") {
                title = $targetRow.find(".pb-field-chapter-title").val() || "";
                shortValue = $targetRow.find(".pb-field-chapter-short").val() || "";
                date = $targetRow.find(".pb-field-chapter-date").val() || "";
                videoTitle = $targetRow.find(".pb-field-chapter-video-title").val() || "";
                videoMp4 = $targetRow.find(".pb-field-chapter-video-mp4").val() || "";
                videoAudio = $targetRow.find(".pb-field-chapter-video-audio").val() || "";
                videoPoster = $targetRow.find(".pb-field-chapter-video-poster").val() || "";
                subtitle1 = $targetRow.find(".pb-field-chapter-subtitle-1").val() || "";
                subtitle2 = $targetRow.find(".pb-field-chapter-subtitle-2").val() || "";
                audio1 = $targetRow.find(".pb-field-chapter-audio-1").val() || "";
                audio2 = $targetRow.find(".pb-field-chapter-audio-2").val() || "";
                $modalKindWrap.hide();
                $modalMinuteWrap.hide();
                $modalChapterVideoWrap.show();
            } else {
                title = $targetRow.find(".pb-field-item-title").val() || "";
                shortValue = $targetRow.find(".pb-field-item-short").val() || "";
                date = $targetRow.find(".pb-field-item-date").val() || "";
                kind = $targetRow.find(".pb-field-item-kind").val() || "item";
                minute = $targetRow.find(".pb-field-item-minute").val() || "";
                $modalKindWrap.show();
                $modalMinuteWrap.show();
                $modalChapterVideoWrap.hide();
            }

            jQuery("#pb-modal-type").val(type);
            jQuery("#pb-modal-chapter-index").val($targetRow.closest(".pb-chapter-row").data("chapter-index"));
            jQuery("#pb-modal-item-index").val($targetRow.data("item-index") || "");
            jQuery("#pb-modal-field-title").val(title);
            jQuery("#pb-modal-field-short").val(shortValue);
            jQuery("#pb-modal-field-date").val(date);
            jQuery("#pb-modal-field-kind").val(kind);
            jQuery("#pb-modal-field-minute").val(minute);
            jQuery("#pb-modal-video-title").val(videoTitle);
            jQuery("#pb-modal-video-mp4").val(videoMp4);
            jQuery("#pb-modal-video-audio").val(videoAudio);
            jQuery("#pb-modal-video-poster").val(videoPoster);
            jQuery("#pb-modal-subtitle-1").val(subtitle1);
            jQuery("#pb-modal-subtitle-2").val(subtitle2);
            jQuery("#pb-modal-audio-1").val(audio1);
            jQuery("#pb-modal-audio-2").val(audio2);

            $modal.data("target-row", $targetRow);
            $modal.removeAttr("hidden");
        }

        function close_modal() {
            $modal.attr("hidden", "hidden");
            $modal.removeData("target-row");
        }

        function save_modal() {
            var type = jQuery("#pb-modal-type").val();
            var $targetRow = $modal.data("target-row");
            if (!$targetRow || !$targetRow.length) {
                close_modal();
                return;
            }

            var title = jQuery("#pb-modal-field-title").val() || "";
            var shortValue = jQuery("#pb-modal-field-short").val() || "";
            var date = jQuery("#pb-modal-field-date").val() || "";
            var kind = jQuery("#pb-modal-field-kind").val() || "item";
            var minute = jQuery("#pb-modal-field-minute").val() || "";
            var videoTitle = jQuery("#pb-modal-video-title").val() || "";
            var videoMp4 = jQuery("#pb-modal-video-mp4").val() || "";
            var videoAudio = jQuery("#pb-modal-video-audio").val() || "";
            var videoPoster = jQuery("#pb-modal-video-poster").val() || "";
            var subtitle1 = jQuery("#pb-modal-subtitle-1").val() || "";
            var subtitle2 = jQuery("#pb-modal-subtitle-2").val() || "";
            var audio1 = jQuery("#pb-modal-audio-1").val() || "";
            var audio2 = jQuery("#pb-modal-audio-2").val() || "";

            if (type === "chapter") {
                $targetRow.find(".pb-field-chapter-title").val(title);
                $targetRow.find(".pb-field-chapter-short").val(shortValue);
                $targetRow.find(".pb-field-chapter-date").val(date);
                $targetRow.find(".pb-field-chapter-video-title").val(videoTitle);
                $targetRow.find(".pb-field-chapter-video-mp4").val(videoMp4);
                $targetRow.find(".pb-field-chapter-video-audio").val(videoAudio);
                $targetRow.find(".pb-field-chapter-video-poster").val(videoPoster);
                $targetRow.find(".pb-field-chapter-subtitle-1").val(subtitle1);
                $targetRow.find(".pb-field-chapter-subtitle-2").val(subtitle2);
                $targetRow.find(".pb-field-chapter-audio-1").val(audio1);
                $targetRow.find(".pb-field-chapter-audio-2").val(audio2);
                $targetRow.find("> .pb-row-header .pb-row-title").text(title || "Kapitel");
            } else {
                var normalizedSeconds = parse_minute_to_seconds(minute);
                minute = normalizedSeconds === null ? "" : seconds_to_minute(normalizedSeconds);
                $targetRow.find(".pb-field-item-title").val(title);
                $targetRow.find(".pb-field-item-short").val(shortValue);
                $targetRow.find(".pb-field-item-date").val(date);
                $targetRow.find(".pb-field-item-kind").val(kind);
                $targetRow.find(".pb-field-item-minute").val(minute);
                $targetRow.find("> .pb-row-title").text(title || "Item");
                $targetRow.find(".pb-item-kind").text(kind_display(kind));
                $targetRow.find(".pb-item-minute").text(minute);
                sort_items_by_minute($targetRow.closest(".pb-chapter-row"));
            }

            render_program_timeline();
            update_preview();
            close_modal();
        }

        // Program image upload/remove
        jQuery(document).on("click", "#program_builder_fields_meta_box .image-upload", function(e) {
            e.preventDefault();
            var frame = wp.media({
                title: "Select Media",
                multiple: false,
                library: { type: "image" }
            });

            frame.on("select", function() {
                var media = frame.state().get("selection").first().toJSON();
                var $box = jQuery("#box-wrapper-program-image");
                $box.find(".meta-image-id").val(media.id || "");
                $box.find(".meta-image").val(media.url || "");
                $box.find(".image-preview img").attr("src", media.url || "");
                update_preview();
            });
            frame.open();
        });

        jQuery(document).on("click", "#program_builder_fields_meta_box .image-upload-remove", function(e) {
            e.preventDefault();
            var $box = jQuery("#box-wrapper-program-image");
            $box.find(".meta-image-id").val("");
            $box.find(".meta-image").val("");
            $box.find(".image-preview img").attr("src", "");
            update_preview();
        });

        // Modal media upload buttons (video, audio, subtitles, poster)
        jQuery(document).on("click", ".pb-media-upload-btn", function(e) {
            e.preventDefault();
            var $btn = jQuery(this);
            var targetId = $btn.data("target");
            var mediaType = $btn.data("media-type") || "";
            var $input = jQuery("#" + targetId);

            var frameOpts = {
                title: "Datei auswählen",
                multiple: false
            };
            if (mediaType) {
                frameOpts.library = { type: mediaType };
            }

            var frame = wp.media(frameOpts);

            frame.on("select", function() {
                var attachment = frame.state().get("selection").first().toJSON();
                $input.val(attachment.url || "").trigger("change");
            });

            frame.open();
        });

        // Add chapter/item
        jQuery(document).on("click", ".pb-add-chapter", function() {
            $chaptersList.find(".pb-chapter-row").removeClass("is-active");
            $chaptersList.append(chapter_html());
            $chaptersList.find(".pb-chapter-row").last().addClass("is-active");
            reindex_program_builder();
            render_program_timeline();
            update_preview();
        });

        jQuery(document).on("click", ".pb-add-item", function() {
            var $chapter = jQuery(this).closest(".pb-chapter-row");
            var selectedKind = normalize_item_kind($chapter.find(".pb-new-item-kind").val() || "item");
            $chaptersList.find(".pb-chapter-row").removeClass("is-active");
            $chapter.addClass("is-active");
            $chapter.find(".pb-items-list").append(item_html(selectedKind));
            reindex_program_builder();
            render_program_timeline();
            update_preview();
        });

        // Delete chapter/item
        jQuery(document).on("click", ".pb-delete-btn", function() {
            var type = jQuery(this).data("type");
            if (type === "chapter") {
                jQuery(this).closest(".pb-chapter-row").remove();
            } else {
                jQuery(this).closest(".pb-item-row").remove();
            }
            reindex_program_builder();
            render_program_timeline();
            update_preview();
        });

        jQuery(document).on("click", ".pb-chapter-row > .pb-row-header", function(e) {
            if (jQuery(e.target).closest(".pb-row-actions").length) {
                return;
            }
            var $chapter = jQuery(this).closest(".pb-chapter-row");
            $chaptersList.find(".pb-chapter-row").removeClass("is-active");
            $chapter.addClass("is-active");
            update_preview_for_chapter($chapter);
        });

        // Open modal with existing values
        jQuery(document).on("click", ".pb-edit-btn", function() {
            var type = jQuery(this).data("type");
            var $row = type === "chapter" ? jQuery(this).closest(".pb-chapter-row") : jQuery(this).closest(".pb-item-row");
            if (type === "chapter") {
                $chaptersList.find(".pb-chapter-row").removeClass("is-active");
                $row.addClass("is-active");
                update_preview_for_chapter($row);
            }
            open_modal($row, type);
        });

        jQuery(document).on("click", ".pb-modal-close, .pb-modal-cancel, .pb-modal-backdrop", function() {
            close_modal();
        });

        jQuery(document).on("click", ".pb-modal-save", function() {
            save_modal();
        });

        // Keep preview synced
        jQuery(document).on("input change", "#pb-program-title, #pb-short-description", function() {
            update_preview();
        });

        // Drag & drop
        var chapterDragged = null;
        var itemDragged = null;
        var itemSourceList = null;
        var chapterPlaceholder = document.createElement("div");
        chapterPlaceholder.className = "pb-drop-placeholder";
        var itemPlaceholder = document.createElement("div");
        itemPlaceholder.className = "pb-drop-placeholder";

        function reset_drag_state() {
            if (chapterPlaceholder.parentNode) {
                chapterPlaceholder.parentNode.removeChild(chapterPlaceholder);
            }
            if (itemPlaceholder.parentNode) {
                itemPlaceholder.parentNode.removeChild(itemPlaceholder);
            }
            jQuery(".pb-dragging").removeClass("pb-dragging");
            chapterDragged = null;
            itemDragged = null;
            itemSourceList = null;
        }

        // Chapter drag
        $chaptersList.get(0).addEventListener("dragstart", function(e) {
            var row = e.target.closest(".pb-chapter-row");
            if (!row || e.target.closest(".pb-item-row")) {
                return;
            }
            reset_drag_state();
            chapterDragged = row;
            row.classList.add("pb-dragging");
            e.dataTransfer.setData("text/plain", "");
            e.dataTransfer.effectAllowed = "move";
        });

        $chaptersList.get(0).addEventListener("dragover", function(e) {
            if (chapterDragged) {
                e.preventDefault();
                e.dataTransfer.dropEffect = "move";
                var target = e.target.closest(".pb-chapter-row");
                if (target && target !== chapterDragged) {
                    var rect = target.getBoundingClientRect();
                    var before = e.clientY < rect.top + rect.height / 2;
                    if (before) {
                        target.parentNode.insertBefore(chapterPlaceholder, target);
                    } else {
                        target.parentNode.insertBefore(chapterPlaceholder, target.nextSibling);
                    }
                }
            }
        });

        $chaptersList.get(0).addEventListener("drop", function(e) {
            if (!chapterDragged) {
                return;
            }
            e.preventDefault();
            if (chapterPlaceholder.parentNode) {
                chapterPlaceholder.parentNode.insertBefore(chapterDragged, chapterPlaceholder);
            }
            reset_drag_state();
            reindex_program_builder();
            render_program_timeline();
        });

        $chaptersList.get(0).addEventListener("dragend", function() {
            reset_drag_state();
        });

        // Item drag
        document.addEventListener("dragstart", function(e) {
            var row = e.target.closest(".pb-item-row");
            if (!row) {
                return;
            }
            e.stopPropagation();
            reset_drag_state();
            itemDragged = row;
            itemSourceList = row.closest(".pb-items-list");
            row.classList.add("pb-dragging");
            e.dataTransfer.setData("text/plain", "");
            e.dataTransfer.effectAllowed = "move";
        }, true);

        document.addEventListener("dragover", function(e) {
            if (!itemDragged) {
                return;
            }
            var target = e.target.closest(".pb-item-row");
            var targetList = e.target.closest(".pb-items-list");
            if (targetList === itemSourceList) {
                e.preventDefault();
                e.dataTransfer.dropEffect = "move";
            }
            if (target && target !== itemDragged && target.closest(".pb-items-list") === itemSourceList) {
                var rect = target.getBoundingClientRect();
                var before = e.clientY < rect.top + rect.height / 2;
                if (before) {
                    target.parentNode.insertBefore(itemPlaceholder, target);
                } else {
                    target.parentNode.insertBefore(itemPlaceholder, target.nextSibling);
                }
            }
        });

        document.addEventListener("drop", function(e) {
            if (!itemDragged) {
                return;
            }
            var targetList = e.target.closest(".pb-items-list");
            if (targetList !== itemSourceList) {
                reset_drag_state();
                return;
            }
            e.preventDefault();
            if (itemPlaceholder.parentNode) {
                itemPlaceholder.parentNode.insertBefore(itemDragged, itemPlaceholder);
            }
            reset_drag_state();
            reindex_program_builder();
            render_program_timeline();
        });

        document.addEventListener("dragend", function(e) {
            if (e.target.closest && e.target.closest(".pb-item-row")) {
                reset_drag_state();
            }
        });

        reindex_program_builder();
        sort_all_items_by_minute();
        reindex_program_builder();
        update_preview();
        render_program_timeline();
    });
})();