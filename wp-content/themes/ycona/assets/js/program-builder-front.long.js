(function () {
    "use strict";

    document.addEventListener("DOMContentLoaded", function () {
        var root = document.getElementById("pb-front");
        if (!root) return;

        var i18n = window.pbFrontI18n || {};
        var languageLabels = (i18n && i18n.languageLabels) ? i18n.languageLabels : {};
        var currentLang = i18n.currentLanguage || "";
        var currentLabel = i18n.currentLanguageLabel || "";
        function getI18n(key, fallback) {
            if (!i18n || typeof i18n[key] !== "string" || i18n[key] === "") return fallback;
            return i18n[key];
        }
        function getLanguageLabel(code, fallback) {
            if (!code || !languageLabels || typeof languageLabels[code] !== "string" || languageLabels[code] === "") {
                return fallback;
            }
            return languageLabels[code];
        }

        var chaptersRaw = [];
        try {
            chaptersRaw = JSON.parse(root.getAttribute("data-chapters") || "[]");
        } catch (e) {
            chaptersRaw = [];
        }
        if (!Array.isArray(chaptersRaw)) chaptersRaw = [];


        var playerWrap   = document.getElementById("pb-front-player-wrap");
        var npTitle      = document.getElementById("pb-front-np-title");
        var eventToast   = document.getElementById("pb-front-event-toast");
        var chaptersList = document.getElementById("pb-front-chapters-list");
        var activeIndex  = 0;
        var scheduledEvents = [];
        var toastTimer = null;

        function parseMinute(str) {
            if (!str) return null;
            var parts = str.split(":");
            if (parts.length === 2) return parseInt(parts[0], 10) * 60 + parseInt(parts[1], 10);
            if (parts.length === 1) return parseInt(parts[0], 10) * 60;
            return null;
        }
        function buildVideoPlayer(ch) {
            var mp4    = ch.video_mp4 || "";
            var poster = ch.video_poster || "";
            var audio  = ch.video_audio || "";
            var sub1   = ch.subtitle_1 || "";
            var sub2   = ch.subtitle_2 || "";
            var a1     = ch.audio_1 || "";
            var a2     = ch.audio_2 || "";
            var sub1Lang  = ch.subtitle_1_lang || currentLang || getI18n("subtitle1Lang", "en");
            var sub2Lang  = ch.subtitle_2_lang || getI18n("subtitle2Lang", "en");
            var a1Lang    = ch.audio_1_lang || sub1Lang;
            var a2Lang    = ch.audio_2_lang || sub2Lang;
            var sub1Label = ch.subtitle_1_label || getLanguageLabel(sub1Lang, currentLabel || getI18n("subtitle1Label", "English"));
            var sub2Label = ch.subtitle_2_label || getLanguageLabel(sub2Lang, getI18n("subtitle2Label", "English"));
            var a1Label   = ch.audio_1_label || getLanguageLabel(a1Lang, currentLabel || getI18n("audioTrack1Label", "Audio 1"));
            var a2Label   = ch.audio_2_label || getLanguageLabel(a2Lang, getI18n("audioTrack2Label", "Audio 2"));

            if (!mp4 && !audio) {
                return '<div class="pb-front-no-video">' + escHtml(getI18n("noVideo", "No video available")) + '</div>';
            }

            if (!mp4 && audio) {
                return '<audio id="pb-front-media" controls preload="metadata"><source src="' + escHtml(audio) + '"></audio>';
            }

            var audioTracks = [];
            if (a1) audioTracks.push({ url: a1, label: a1Label });
            if (a2) audioTracks.push({ url: a2, label: a2Label });
            var audioAttr = audioTracks.length ? " data-audio-tracks='" + JSON.stringify(audioTracks) + "'" : "";

            var hasSubs = !!(sub1 || sub2);
            var crossorigin = hasSubs ? ' crossorigin="anonymous"' : '';
            var posterAttr = poster ? ' poster="' + escHtml(poster) + '"' : '';

            var tracks = '';
            if (sub1) tracks += '<track kind="subtitles" src="' + escHtml(sub1) + '" srclang="' + escHtml(sub1Lang) + '" label="' + escHtml(sub1Label) + '">';
            if (sub2) tracks += '<track kind="subtitles" src="' + escHtml(sub2) + '" srclang="' + escHtml(sub2Lang) + '" label="' + escHtml(sub2Label) + '">';

            var sources = '<source src="' + escHtml(mp4) + '" type="video/mp4">';

            return '<div class="video-player video-player-skeleton" data-player' + audioAttr + '>' +
                '<div class="vp-skeleton" aria-hidden="true"><div class="vp-skeleton-shimmer"></div><div class="vp-skeleton-play"></div>' +
                '<div class="vp-skeleton-controls"><div class="vp-skeleton-btn"></div><div class="vp-skeleton-bar"></div><div class="vp-skeleton-time"></div><div class="vp-skeleton-btn"></div><div class="vp-skeleton-btn-sm"></div><div class="vp-skeleton-btn"></div></div></div>' +
                '<video preload="metadata" playsinline webkit-playsinline' + posterAttr + crossorigin + '>' + sources + tracks + '</video>' +
                '<div class="video-player-play-overlay" data-play-overlay aria-label="Play Video"><i class="bi bi-play-fill" aria-hidden="true"></i></div>' +
                '<div class="video-player-captions" data-captions aria-live="polite" aria-atomic="true"></div>' +
                '<div class="video-player-controls" data-controls>' +
                    '<button type="button" class="video-player-btn" data-play aria-label="Play"><i class="bi bi-play-fill"></i><i class="bi bi-pause-fill"></i></button>' +
                    '<div class="video-player-progress-wrap" data-progress-wrap><input type="range" class="video-player-progress" data-progress min="0" max="100" value="0" step="0.1" aria-label="Seek"></div>' +
                    '<span class="video-player-time" data-time aria-live="off">0:00</span>' +
                    '<button type="button" class="video-player-btn" data-mute aria-label="Mute"><i class="bi bi-volume-up-fill"></i><i class="bi bi-volume-mute-fill"></i></button>' +
                    '<input type="range" class="video-player-volume" data-volume min="0" max="100" value="100" step="1" aria-label="Volume">' +
                    '<button type="button" class="video-player-btn" data-cc aria-label="Captions"><span>CC</span></button>' +
                    '<button type="button" class="video-player-btn" data-fullscreen aria-label="Fullscreen"><i class="bi bi-fullscreen"></i><i class="bi bi-fullscreen-exit"></i></button>' +
                '</div>' +
                '<div class="video-player-loading" data-loading aria-hidden="true" role="status"></div>' +
                '<div class="video-player-error" data-error aria-live="assertive" hidden></div>' +
            '</div>';
        }

        function escHtml(s) {
            var d = document.createElement("div");
            d.appendChild(document.createTextNode(s));
            return d.innerHTML;
        }

        function showToast(text, kind) {
            if (!eventToast) return;
            if (toastTimer) clearTimeout(toastTimer);
            eventToast.textContent = text;
            eventToast.className = "pb-front-event-toast pb-toast-" + kind;
            eventToast.removeAttribute("hidden");
            toastTimer = setTimeout(function () {
                eventToast.setAttribute("hidden", "");
            }, 4000);
        }

        function setupScheduler() {
            scheduledEvents = [];
            var ch = chaptersRaw[activeIndex];
            if (!ch || !ch.items || !Array.isArray(ch.items)) return;

            ch.items.forEach(function (item) {
                var secs = parseMinute(item.minute || "");
                if (secs === null) return;
                var kind = item.kind || "item";
                var title = item.title || kind.charAt(0).toUpperCase() + kind.slice(1);
                scheduledEvents.push({ secs: secs, kind: kind, title: title, fired: false });
            });
            scheduledEvents.sort(function (a, b) { return a.secs - b.secs; });

            var media = playerWrap.querySelector("video") || playerWrap.querySelector("audio");
            if (!media) return;

            media.addEventListener("timeupdate", function () {
                var t = media.currentTime;
                scheduledEvents.forEach(function (ev) {
                    if (!ev.fired && t >= ev.secs && t < ev.secs + 2) {
                        ev.fired = true;
                        showToast(ev.kind.toUpperCase() + ": " + ev.title, ev.kind);
                    }
                });
            });

            media.addEventListener("seeked", function () {
                var t = media.currentTime;
                scheduledEvents.forEach(function (ev) {
                    if (t < ev.secs) ev.fired = false;
                });
            });
        }

        function activateChapter(index) {
            if (index < 0 || index >= chaptersRaw.length) return;
            activeIndex = index;

            var cards = chaptersList.querySelectorAll(".pb-front-chapter");
            cards.forEach(function (c, i) {
                c.classList.toggle("is-active", i === index);
            });

            var ch = chaptersRaw[index];
            var title = ch.video_title || ch.title || "";
            if (npTitle) npTitle.textContent = title;

            playerWrap.innerHTML = buildVideoPlayer(ch);

            if (typeof window.init_custom_video_players === "function") {
                window.init_custom_video_players();
            }

            setupScheduler();
        }

        // Chapter click
        if (chaptersList) {
            chaptersList.addEventListener("click", function (e) {
                var chapterEl = e.target.closest(".pb-front-chapter");
                if (!chapterEl) return;
                var idx = parseInt(chapterEl.getAttribute("data-chapter"), 10);
                if (!isNaN(idx) && idx !== activeIndex) {
                    activateChapter(idx);
                }
            });

            // Item click — seek to minute
            chaptersList.addEventListener("click", function (e) {
                var itemEl = e.target.closest(".pb-front-item");
                if (!itemEl) return;
                e.stopPropagation();

                var chIdx = parseInt(itemEl.getAttribute("data-chapter"), 10);
                if (!isNaN(chIdx) && chIdx !== activeIndex) {
                    activateChapter(chIdx);
                }

                var minute = itemEl.getAttribute("data-minute") || "";
                var secs = parseMinute(minute);
                if (secs !== null) {
                    setTimeout(function () {
                        var media = playerWrap.querySelector("video") || playerWrap.querySelector("audio");
                        if (media) {
                            media.currentTime = secs;
                            media.play().catch(function () {});
                        }
                    }, 150);
                }
            });
        }

        setupScheduler();
    });
})();
