/**
 * Custom HTML5 Video Player
 * ================================
 * Modern, cross-browser compatible video player with custom controls
 *
 * Features:
 * - Play/Pause control
 * - Seek bar with buffered state visualization
 * - Volume control + mute
 * - Fullscreen toggle (cross-browser)
 * - Closed Captions (WebVTT) with custom UI
 * - Time display (current / duration)
 * - Keyboard shortcuts (Space, F, M, C, Arrows, Esc)
 * - Touch and mobile support
 * - Loading states
 * - Error handling
 * - Accessibility (ARIA attributes, keyboard navigation)
 * - Respects prefers-reduced-motion
 * - Adaptive streaming: HLS (via hls.js) and DASH (via dash.js)
 * - CSS-based skins (Default, Ocean, Cinema, Minimal)
 *
 * Browser Support:
 * - Chrome, Firefox, Safari, Edge (desktop)
 * - iOS Safari, Android Chrome (mobile)
 *
 * Adaptive Streaming:
 * - HLS  (.m3u8) — Supported everywhere. Native on Safari, hls.js elsewhere.
 * - DASH (.mpd)  — Supported everywhere except iOS Safari (no MSE).
 *
 * @license MIT
 */

(function () {
  'use strict';

  /* ===========================================
     Adaptive Streaming – HLS & DASH loaders
     ===========================================
     We lazy-load the libraries from CDN only when
     the video source actually needs them.
     =========================================== */

  var HLS_CDN  = 'https://cdn.jsdelivr.net/npm/hls.js@latest/dist/hls.min.js';
  var DASH_CDN = 'https://cdn.dashjs.org/latest/dash.all.min.js';

  /**
   * Detect iOS / iPadOS reliably.
   * iPadOS 13+ reports a desktop (Macintosh) user-agent but still has
   * multi-touch, so we combine the classic check with a touch heuristic.
   * Cached after first call.
   * @returns {boolean}
   */
  var _is_ios_cached = null;
  function is_ios_device() {
    if (_is_ios_cached !== null) return _is_ios_cached;
    var ua = navigator.userAgent || '';
    _is_ios_cached = (
      /iPad|iPhone|iPod/.test(ua) ||
      (navigator.maxTouchPoints > 1 && /Macintosh/.test(ua))
    ) && !window.MSStream;
    return _is_ios_cached;
  }

  /** Keep track of script loading so we only inject once. */
  var _hls_loading  = false;
  var _dash_loading = false;
  var _hls_ready    = false;
  var _dash_ready   = false;
  var _hls_callbacks = [];
  var _dash_callbacks = [];

  /**
   * Load an external script if not already present.
   * @param {string} src           Script URL
   * @param {Function} callback    Called when loaded
   * @param {string} flag_loading  'hls' or 'dash'
   */
  function load_script(src, callback, type) {
    if (type === 'hls') {
      if (_hls_ready)  { callback(); return; }
      _hls_callbacks.push(callback);
      if (_hls_loading) return;
      _hls_loading = true;
    } else {
      if (_dash_ready) { callback(); return; }
      _dash_callbacks.push(callback);
      if (_dash_loading) return;
      _dash_loading = true;
    }

    var script = document.createElement('script');
    script.src = src;
    script.async = true;

    script.onload = function () {
      var cbs;
      if (type === 'hls') {
        _hls_ready = true;
        cbs = _hls_callbacks;
        _hls_callbacks = [];
      } else {
        _dash_ready = true;
        cbs = _dash_callbacks;
        _dash_callbacks = [];
      }
      for (var i = 0; i < cbs.length; i++) cbs[i]();
    };

    script.onerror = function () {
      console.warn('[CustomVideoPlayer] Failed to load ' + src);
    };

    document.head.appendChild(script);
  }

  /**
   * Detect stream type from a URL.
   * @param {string} url
   * @returns {'hls'|'dash'|'native'}
   */
  function detect_stream_type(url) {
    if (!url) return 'native';
    var lower = url.split('?')[0].toLowerCase();
    if (lower.indexOf('.m3u8') !== -1) return 'hls';
    if (lower.indexOf('.mpd')  !== -1) return 'dash';
    return 'native';
  }

  /**
   * Format seconds into MM:SS or HH:MM:SS format
   * @param {number} seconds - Time in seconds
   * @returns {string} Formatted time string
   */
  function format_time(seconds) {
    if (!isFinite(seconds) || seconds < 0) return '0:00';
    
    var hours = Math.floor(seconds / 3600);
    var minutes = Math.floor((seconds % 3600) / 60);
    var secs = Math.floor(seconds % 60);
    
    if (hours > 0) {
      return hours + ':' + pad_zero(minutes) + ':' + pad_zero(secs);
    }
    return minutes + ':' + pad_zero(secs);
  }

  /**
   * Pad single digit numbers with leading zero
   * @param {number} num - Number to pad
   * @returns {string} Padded string
   */
  function pad_zero(num) {
    return num < 10 ? '0' + num : String(num);
  }

  /**
   * CustomVideoPlayer — one instance per wrapper element.
   * @param {HTMLElement} wrapper_element - Element with [data-player]
   */
  function CustomVideoPlayer(wrapper_element) {
    if (!wrapper_element || !wrapper_element.querySelector) return;

    var video_element = wrapper_element.querySelector('video');
    if (!video_element) return;

    this.wrapper = wrapper_element;
    this.video = video_element;

    // Get DOM references
    var controls_container = wrapper_element.querySelector('[data-controls]');
    var play_button = wrapper_element.querySelector('[data-play]');
    var play_overlay = wrapper_element.querySelector('[data-play-overlay]');
    var progress_wrap = wrapper_element.querySelector('[data-progress-wrap]');
    var progress_input = wrapper_element.querySelector('[data-progress]');
    var mute_button = wrapper_element.querySelector('[data-mute]');
    var volume_input = wrapper_element.querySelector('[data-volume]');
    var fullscreen_button = wrapper_element.querySelector('[data-fullscreen]');
    var cc_button = wrapper_element.querySelector('[data-cc]');
    var quality_button = wrapper_element.querySelector('[data-quality]');
    var time_display = wrapper_element.querySelector('[data-time]');
    var loading_element = wrapper_element.querySelector('[data-loading]');
    var error_element = wrapper_element.querySelector('[data-error]');
    var captions_display = wrapper_element.querySelector('[data-captions]');
    var captions_menu = wrapper_element.querySelector('[data-captions-menu]');
    var quality_menu = wrapper_element.querySelector('[data-quality-menu]');

    this.controls = controls_container;
    this.play_btn = play_button;
    this.play_overlay = play_overlay;
    this.progress_wrap = progress_wrap;
    this.progress_input = progress_input;
    this.mute_btn = mute_button;
    this.volume_input = volume_input;
    this.fullscreen_btn = fullscreen_button;
    this.cc_btn = cc_button;
    this.quality_btn = quality_button;
    this.time_display = time_display;
    this.loading_element = loading_element;
    this.error_element = error_element;
    this.captions_display = captions_display;
    this.captions_menu = captions_menu;
    this.quality_menu = quality_menu;

    // State
    this._progress_dragging = false;
    this._bound_handlers = {};
    this._text_tracks = [];
    this._active_track = null;
    this._controls_timeout = null;
    this._mouse_move_timeout = null;
    this._quality_levels = [];     // populated by HLS/DASH init
    this._active_quality = -1;     // -1 = auto

    // Skeleton state
    this._skeleton_removed = false;
    this._skeleton_fallback_timeout = null;

    // Audio tracks state
    this._audio_tracks = [];           // {url, label, lang, audio_element}
    this._active_audio_track = -1;     // -1 = default (video built-in audio)
    this.audio_btn = null;
    this.audio_menu = null;

    // Stall recovery (iOS)
    this._stall_timer = null;
    this._last_known_time = -1;
    this._stall_recovery_attempts = 0;
    this._ios_playback_prepared = false;

    // Initialize buffered progress elements if progress_wrap exists
    if (this.progress_wrap) {
      this._init_progress_visuals();
    }

    // Adaptive streaming references (set by _init_stream)
    this._hls_instance  = null;
    this._dash_instance = null;

    this._bind_handlers();
    this._attach_listeners();
    this._init_stream();     // HLS / DASH / native
    this._init_text_tracks();
    this._init_audio_tracks();
    this._sync_ui();

    // iOS: volume is hardware-only (read-only in JS). Hide the slider.
    if (is_ios_device() && this.volume_input) {
      this.volume_input.style.display = 'none';
    }
    // iOS: metadata-only preload often stalls around 0-1s.
    // Switch to preload=auto so Safari fetches media segments eagerly.
    if (is_ios_device()) {
      this.video.preload = 'auto';
      this.video.setAttribute('preload', 'auto');
    }

    // On touch devices (e.g. iOS), show controls immediately when paused so play button is visible
    if ('ontouchstart' in window || navigator.maxTouchPoints > 0) {
      if (this.video.paused) {
        this.wrapper.classList.add('video-player-controls-visible');
      }
    }

    // If the video is already loaded (e.g. browser cache), remove skeleton instantly
    if (this.video.readyState >= 3) { // HAVE_FUTURE_DATA or better
      this._remove_skeleton(true);
    } else if ('ontouchstart' in window || navigator.maxTouchPoints > 0) {
      // iOS/touch: canplay often delayed or never fires. Remove skeleton after a short delay
      // so the user sees the video/poster and play button instead of being stuck.
      var player_ref = this;
      this._skeleton_fallback_timeout = setTimeout(function () {
        player_ref._remove_skeleton();
      }, 2500);
    }
  }

  /**
   * Initialize progress bar visual elements (buffered, played bars)
   */
  CustomVideoPlayer.prototype._init_progress_visuals = function () {
    if (!this.progress_wrap) return;
    
    // Check if visuals already exist
    if (this.progress_wrap.querySelector('.video-player-progress-track')) return;
    
    var track_container = document.createElement('div');
    track_container.className = 'video-player-progress-track';
    
    var buffered_bar = document.createElement('div');
    buffered_bar.className = 'video-player-progress-buffered';
    buffered_bar.setAttribute('data-buffered', '');
    
    var played_bar = document.createElement('div');
    played_bar.className = 'video-player-progress-played';
    played_bar.setAttribute('data-played', '');
    
    track_container.appendChild(buffered_bar);
    track_container.appendChild(played_bar);
    
    // Insert before the range input
    if (this.progress_input) {
      this.progress_wrap.insertBefore(track_container, this.progress_input);
    } else {
      this.progress_wrap.appendChild(track_container);
    }
    
    this.buffered_bar = buffered_bar;
    this.played_bar = played_bar;
  };

  /* ===========================================
     Adaptive Streaming – init
     =========================================== */

  /**
   * Detect source type and set up HLS / DASH if needed.
   * For plain .mp4/.webm the browser handles playback natively.
   *
   * HLS (.m3u8):
   *   - Safari supports HLS natively — no library needed.
   *   - All other browsers use hls.js (loaded lazily from CDN).
   *
   * DASH (.mpd):
   *   - Supported everywhere with MSE (Chrome, Firefox, Edge).
   *   - NOT supported on iOS Safari (no MSE).
   *   - Uses dash.js (loaded lazily from CDN).
   */
  CustomVideoPlayer.prototype._init_stream = function () {
    var video   = this.video;
    var src_url = video.getAttribute('src') || video.getAttribute('data-src') || '';

    // Also check <source> elements
    if (!src_url) {
      var source_el = video.querySelector('source');
      if (source_el) src_url = source_el.getAttribute('src') || '';
    }

    var stream_type = detect_stream_type(src_url);
    var player_instance = this;

    if (stream_type === 'hls') {
      this._init_hls(src_url);
    } else if (stream_type === 'dash') {
      this._init_dash(src_url);
    }
    // 'native' — browser handles it, nothing to do.
  };

  /**
   * Initialize HLS playback.
   * Safari plays .m3u8 natively; other browsers need hls.js.
   * @param {string} src - HLS manifest URL (.m3u8)
   */
  CustomVideoPlayer.prototype._init_hls = function (src) {
    var video = this.video;
    var player_instance = this;

    // Safari / iOS — native HLS support via <video>
    if (video.canPlayType('application/vnd.apple.mpegurl') ||
        video.canPlayType('application/x-mpegURL')) {
      video.src = src;
      return;
    }

    // Other browsers — load hls.js
    load_script(HLS_CDN, function () {
      if (typeof Hls === 'undefined' || !Hls.isSupported()) {
        // Final fallback: try native anyway
        video.src = src;
        return;
      }

      var hls = new Hls({
        startLevel: -1,          // auto-select quality
        capLevelToPlayerSize: true
      });

      hls.loadSource(src);
      hls.attachMedia(video);
      hls.on(Hls.Events.MANIFEST_PARSED, function (event, data) {
        // Stream ready — user can press play
        player_instance._set_loading(false);

        // Build quality levels from HLS
        var levels = [];
        var hls_levels = hls.levels || [];
        for (var i = 0; i < hls_levels.length; i++) {
          levels.push({
            index: i,
            height: hls_levels[i].height || 0,
            width: hls_levels[i].width || 0,
            bitrate: hls_levels[i].bitrate || 0

          });
        }
        // Sort by height descending
        levels.sort(function (a, b) { return b.height - a.height; });
        player_instance._quality_levels = levels;
        player_instance._build_quality_menu();
      });

      hls.on(Hls.Events.ERROR, function (event, data) {
        if (data.fatal)
        {
          switch (data.type)
          {
            case Hls.ErrorTypes.NETWORK_ERROR:
              player_instance._show_error('Network error — retrying…');
              hls.startLoad();
              break;
            case Hls.ErrorTypes.MEDIA_ERROR:
              player_instance._show_error('Media error — recovering…');
              hls.recoverMediaError();
              break;

            default:
              player_instance._show_error('Streaming error');
              hls.destroy();
              break;
          }
        }
      });

      player_instance._hls_instance = hls;
    }, 'hls');
  };

  /**
   * Initialize DASH playback via dash.js.
   * @param {string} src - DASH manifest URL (.mpd)
   */
  CustomVideoPlayer.prototype._init_dash = function (src) {
    var video = this.video;
    var player_instance = this;

    // iOS / iPadOS Safari has no MSE — DASH won't work
    if (is_ios_device()) {
      player_instance._show_error('DASH streaming is not supported on this device.');
      return;
    }

    load_script(DASH_CDN, function () {
      if (typeof dashjs === 'undefined') {
        player_instance._show_error('Failed to load DASH library');
        return;
      }

      var dash_player = dashjs.MediaPlayer().create();
      dash_player.initialize(video, src, false); // false = don't autoplay

      dash_player.on('error', function (e) {
        player_instance._show_error('DASH streaming error');
      });

      player_instance._dash_instance = dash_player;

      // DASH quality levels — wait for stream to be initialized
      // dash.js v5: getBitrateInfoListFor() replaced by getRepresentationsByType()
      dash_player.on('streamInitialized', function () {
        var representations = [];
        try {
          representations = dash_player.getRepresentationsByType('video') || [];
        } catch (e) {
          // Fallback for older dash.js versions (< v5)
          if (typeof dash_player.getBitrateInfoListFor === 'function') {
            representations = dash_player.getBitrateInfoListFor('video') || [];
          }
        }
        var levels = [];
        for (var i = 0; i < representations.length; i++) {
          var rep = representations[i];
          levels.push({
            index: i,
            height: rep.height || 0,
            width: rep.width || 0,
            bitrate: rep.bitrateInKbit || rep.bitrate || 0
          });
        }
        // Sort by height descending
        levels.sort(function (a, b) { return b.height - a.height; });
        player_instance._quality_levels = levels;
        player_instance._build_quality_menu();
      });
    }, 'dash');
  };

  /* ===========================================
     Quality / Resolution Selector
     =========================================== */

  /**
   * Build the quality selection menu from available levels.
   * Called after HLS manifest is parsed or DASH stream initialised.
   */
  CustomVideoPlayer.prototype._build_quality_menu = function () {
    if (this._quality_levels.length === 0) {
      // No levels — hide quality button
      if (this.quality_btn) this.quality_btn.style.display = 'none';
      return;
    }

    // Show quality button
    if (this.quality_btn) this.quality_btn.style.display = '';

    // Create menu if it doesn't exist
    if (!this.quality_menu) {
      this.quality_menu = document.createElement('div');
      this.quality_menu.className = 'video-player-quality-menu';
      this.quality_menu.setAttribute('data-quality-menu', '');
      this.wrapper.appendChild(this.quality_menu);
    }

    // Clear existing items
    this.quality_menu.innerHTML = '';

    // Title
    var title = document.createElement('div');
    title.className = 'video-player-quality-menu-title';
    title.textContent = 'Quality';
    this.quality_menu.appendChild(title);

    var player_instance = this;

    // "Auto" option — always first
    var auto_btn = document.createElement('button');
    auto_btn.className = 'video-player-quality-menu-item active';
    auto_btn.textContent = 'Auto';
    auto_btn.setAttribute('type', 'button');
    auto_btn.setAttribute('data-level', '-1');
    auto_btn.addEventListener('click', function () {
      player_instance._select_quality(-1);
    });
    this.quality_menu.appendChild(auto_btn);

    // Individual levels
    for (var i = 0; i < this._quality_levels.length; i++) {
      var level = this._quality_levels[i];
      var label = level.height ? level.height + 'p' : Math.round(level.bitrate / 1000) + ' kbps';

      var btn = document.createElement('button');
      btn.className = 'video-player-quality-menu-item';
      btn.textContent = label;
      btn.setAttribute('type', 'button');
      btn.setAttribute('data-level', String(level.index));

      (function (idx) {
        btn.addEventListener('click', function () {
          player_instance._select_quality(idx);
        });
      })(level.index);

      this.quality_menu.appendChild(btn);
    }
  };

  /**
   * Select a quality level.
   * @param {number} level_index  -1 = auto, otherwise the stream-level index
   */
  CustomVideoPlayer.prototype._select_quality = function (level_index) {
    this._active_quality = level_index;

    // Apply to HLS
    if (this._hls_instance) {
      if (level_index === -1) {
        this._hls_instance.currentLevel = -1;          // auto
        this._hls_instance.nextLevel = -1;
      } else {
        this._hls_instance.currentLevel = level_index;
      }
    }

    // Apply to DASH (v5 API: setRepresentationForTypeByIndex replaces setQualityFor)
    if (this._dash_instance) {
      if (level_index === -1) {
        // Re-enable ABR auto switching
        this._dash_instance.updateSettings({
          streaming: { abr: { autoSwitchBitrate: { video: true } } }
        });
      } else {
        // Disable ABR auto switching, then set the quality manually
        this._dash_instance.updateSettings({
          streaming: { abr: { autoSwitchBitrate: { video: false } } }
        });
        // dash.js v5 uses setRepresentationForTypeByIndex
        if (typeof this._dash_instance.setRepresentationForTypeByIndex === 'function') {
          this._dash_instance.setRepresentationForTypeByIndex('video', level_index);
        } else if (typeof this._dash_instance.setQualityFor === 'function') {
          this._dash_instance.setQualityFor('video', level_index);
        }
      }
    }

    // Update menu UI
    if (this.quality_menu) {
      var items = this.quality_menu.querySelectorAll('.video-player-quality-menu-item');
      for (var i = 0; i < items.length; i++) {
        var item_level = parseInt(items[i].getAttribute('data-level'), 10);
        items[i].classList.toggle('active', item_level === level_index);
      }
    }

    // Update button label
    if (this.quality_btn) {
      var label_el = this.quality_btn.querySelector('.vp-quality-label');
      if (label_el) {
        if (level_index === -1) {
          label_el.textContent = 'AUTO';
        } else {
          // Find the matching level
          for (var j = 0; j < this._quality_levels.length; j++) {
            if (this._quality_levels[j].index === level_index) {
              label_el.textContent = this._quality_levels[j].height
                ? this._quality_levels[j].height + 'p'
                : 'Q' + level_index;
              break;
            }
          }
        }
      }
    }

    this._close_quality_menu();
  };

  /**
   * Toggle quality menu visibility
   */
  CustomVideoPlayer.prototype._toggle_quality_menu = function () {
    if (!this.quality_menu) return;
    var is_open = this.quality_menu.classList.contains('active');
    // Close other menus first
    this._close_captions_menu();
    this._close_audio_menu();
    if (is_open) {
      this._close_quality_menu();
    } else {
      this.quality_menu.classList.add('active');
    }
  };

  /**
   * Close quality menu
   */
  CustomVideoPlayer.prototype._close_quality_menu = function () {
    if (this.quality_menu) {
      this.quality_menu.classList.remove('active');
    }
  };

  /**
   * Initialize text tracks (captions/subtitles)
   */
  CustomVideoPlayer.prototype._init_text_tracks = function () {
    var video_element = this.video;
    var player_instance = this;
    
    // Force all tracks to hidden mode
    var tracks = video_element.textTracks;
    if (tracks) {
      for (var i = 0; i < tracks.length; i++) {
        tracks[i].mode = 'hidden';
      }
    }
    
    // Get track elements
    var track_elements = video_element.querySelectorAll('track');
    this._text_tracks = [];
    
    for (var j = 0; j < track_elements.length; j++) {
      var track_el = track_elements[j];
      if (track_el.kind === 'subtitles' || track_el.kind === 'captions') {
        this._text_tracks.push({
          element: track_el,
          track: track_el.track,
          label: track_el.label || track_el.srclang || 'Track ' + (j + 1),
          srclang: track_el.srclang || ''
        });
      }
    }
    
    // Build captions menu if we have tracks and a CC button
    if (this._text_tracks.length > 0 && this.cc_btn) {
      this._build_captions_menu();
      
      // Listen for cue changes on all tracks
      this._text_tracks.forEach(function(track_info) {
        if (track_info.track) {
          track_info.track.addEventListener('cuechange', function() {
            player_instance._on_cuechange();
          });
        }
      });
    } else if (this.cc_btn) {
      // Hide CC button if no tracks available
      this.cc_btn.style.display = 'none';
    }
  };

  /**
   * Build the captions selection menu
   */
  CustomVideoPlayer.prototype._build_captions_menu = function () {
    if (!this.cc_btn || this._text_tracks.length === 0) return;
    
    // Create menu if it doesn't exist
    if (!this.captions_menu) {
      this.captions_menu = document.createElement('div');
      this.captions_menu.className = 'video-player-captions-menu';
      this.captions_menu.setAttribute('data-captions-menu', '');
      this.wrapper.appendChild(this.captions_menu);
    }
    
    // Clear existing menu items
    this.captions_menu.innerHTML = '';
    
    // Add title
    var title = document.createElement('div');
    title.className = 'video-player-captions-menu-title';
    title.textContent = 'Subtitles';
    this.captions_menu.appendChild(title);
    
    // Add "Off" option
    var off_button = document.createElement('button');
    off_button.className = 'video-player-captions-menu-item active';
    off_button.textContent = 'Off';
    off_button.setAttribute('type', 'button');
    off_button.setAttribute('data-track-index', '-1');
    this.captions_menu.appendChild(off_button);
    
    var player_instance = this;
    off_button.addEventListener('click', function() {
      player_instance._select_track(-1);

    });
    
    // Add track options
    this._text_tracks.forEach(function(track_info, index) {
      var button = document.createElement('button');
      button.className = 'video-player-captions-menu-item';
      button.textContent = track_info.label;
      button.setAttribute('type', 'button');
      button.setAttribute('data-track-index', String(index));
      
      button.addEventListener('click', function() {
        player_instance._select_track(index);
      });
      
      player_instance.captions_menu.appendChild(button);
    });
  };

  /**
   * Select a caption track
   * @param {number} track_index - Index of track (-1 for off)
   */
  CustomVideoPlayer.prototype._select_track = function (track_index) {
    var player_instance = this;
    
    // Disable all tracks
    this._text_tracks.forEach(function(track_info) {
      if (track_info.track) {
        track_info.track.mode = 'hidden';
      }
    });
    
    // Clear active track
    this._active_track = null;
    
    // Update menu items
    if (this.captions_menu) {
      var menu_items = this.captions_menu.querySelectorAll('.video-player-captions-menu-item');
      menu_items.forEach(function(item) {
        item.classList.remove('active');
      });
    }
    
    // Enable selected track
    if (track_index >= 0 && track_index < this._text_tracks.length) {
      var selected_track_info = this._text_tracks[track_index];
      if (selected_track_info.track) {
        selected_track_info.track.mode = 'showing';
        this._active_track = selected_track_info;
      }
      
      // Update menu
      if (this.captions_menu) {
        var selected_menu_item = this.captions_menu.querySelector('[data-track-index="' + track_index + '"]');
        if (selected_menu_item) {
          selected_menu_item.classList.add('active');
        }
      }
      
      this.wrapper.classList.add('video-player-captions-active');
    } else {
      // "Off" selected
      if (this.captions_menu) {
        var off_item = this.captions_menu.querySelector('[data-track-index="-1"]');
        if (off_item) {
          off_item.classList.add('active');
        }
      }
      this.wrapper.classList.remove('video-player-captions-active');
    }
    
    // Hide captions display if no track active
    if (!this._active_track && this.captions_display) {
      this.captions_display.textContent = '';
      this.captions_display.classList.remove('active');
    }
    
    // Close menu
    this._close_captions_menu();
    
    // Update cue display
    this._on_cuechange();
  };

  /**
   * Toggle captions menu visibility
   */
  CustomVideoPlayer.prototype._toggle_captions_menu = function () {
    if (!this.captions_menu) return;
    
    var is_active = this.captions_menu.classList.contains('active');
    this._close_quality_menu();
    this._close_audio_menu();
    
    if (is_active) {
      this._close_captions_menu();
    } else {
      this.captions_menu.classList.add('active');
    }
  };

  /**
   * Close captions menu
   */
  CustomVideoPlayer.prototype._close_captions_menu = function () {
    if (this.captions_menu) {
      this.captions_menu.classList.remove('active');
    }
  };

  /**
   * Handle cue changes (display current caption)
   */
  CustomVideoPlayer.prototype._on_cuechange = function () {
    if (!this.captions_display || !this._active_track || !this._active_track.track) {
      if (this.captions_display) {
        this.captions_display.textContent = '';
        this.captions_display.classList.remove('active');
      }
      return;
    }
    
    var active_cues = this._active_track.track.activeCues;
    
    if (active_cues && active_cues.length > 0) {
      // Display the first active cue
      var cue_text = active_cues[0].text;
      this.captions_display.textContent = cue_text;
      this.captions_display.classList.add('active');
    } else {
      this.captions_display.textContent = '';
      this.captions_display.classList.remove('active');
    }
  };

  /* ===========================================
     Audio Track Switching
     =========================================== */

  /**
   * Initialize external audio tracks from the data-audio-tracks attribute.
   * Creates hidden <audio> elements and syncs them to the main video.
   */
  CustomVideoPlayer.prototype._init_audio_tracks = function () {
    var raw = this.wrapper.getAttribute('data-audio-tracks');
    if (!raw) return;

    var parsed;
    try { parsed = JSON.parse(raw); } catch (e) { return; }
    if (!Array.isArray(parsed) || parsed.length === 0) return;

    var player_instance = this;

    for (var i = 0; i < parsed.length; i++) {
      var entry = parsed[i];
      if (!entry.url) continue;

      var audio_el = document.createElement('audio');
      audio_el.preload = 'metadata';
      audio_el.src = entry.url;
      audio_el.style.display = 'none';
      this.wrapper.appendChild(audio_el);

      this._audio_tracks.push({
        url:   entry.url,
        label: entry.label || 'Audio ' + (i + 1),
        lang:  entry.lang  || '',
        audio_element: audio_el
      });
    }

    if (this._audio_tracks.length === 0) return;

    // Insert an audio-track button into the controls bar (before fullscreen btn)
    if (this.controls) {
      this.audio_btn = document.createElement('button');
      this.audio_btn.type = 'button';
      this.audio_btn.className = 'video-player-btn';
      this.audio_btn.setAttribute('data-audio-track-btn', '');
      this.audio_btn.setAttribute('aria-label', 'Audio Track');
      this.audio_btn.innerHTML = '<i class="bi bi-music-note-beamed" aria-hidden="true"></i>';

      // Insert before the fullscreen button if it exists, otherwise append
      if (this.fullscreen_btn && this.fullscreen_btn.parentNode === this.controls) {
        this.controls.insertBefore(this.audio_btn, this.fullscreen_btn);
      } else {
        this.controls.appendChild(this.audio_btn);
      }

      this.audio_btn.addEventListener('click', function () {
        player_instance._toggle_audio_menu();
      });
    }

    // Build the audio track menu
    this._build_audio_menu();

    // Sync audio elements with the main video (play/pause/seek/volume/rate)
    this.video.addEventListener('play', function () {
      player_instance._sync_audio_play();
    });
    this.video.addEventListener('pause', function () {
      player_instance._sync_audio_pause();
    });
    this.video.addEventListener('seeked', function () {
      player_instance._sync_audio_time();
    });
    this.video.addEventListener('ratechange', function () {
      player_instance._sync_audio_rate();
    });
    this.video.addEventListener('volumechange', function () {
      player_instance._sync_audio_volume();
    });
  };

  /**
   * Build the audio track selection menu.
   */
  CustomVideoPlayer.prototype._build_audio_menu = function () {
    if (this._audio_tracks.length === 0) return;

    if (!this.audio_menu) {
      this.audio_menu = document.createElement('div');
      this.audio_menu.className = 'video-player-audio-menu';
      this.audio_menu.setAttribute('data-audio-menu', '');
      this.wrapper.appendChild(this.audio_menu);
    }

    this.audio_menu.innerHTML = '';

    var title = document.createElement('div');
    title.className = 'video-player-audio-menu-title';
    title.textContent = 'Audio';
    this.audio_menu.appendChild(title);

    var player_instance = this;

    // "Default" option (video's built-in audio)
    var default_btn = document.createElement('button');
    default_btn.className = 'video-player-audio-menu-item active';
    default_btn.textContent = 'Default';
    default_btn.setAttribute('type', 'button');
    default_btn.setAttribute('data-audio-index', '-1');
    default_btn.addEventListener('click', function () {
      player_instance._select_audio_track(-1);
    });
    this.audio_menu.appendChild(default_btn);

    // Individual tracks
    for (var i = 0; i < this._audio_tracks.length; i++) {
      var track = this._audio_tracks[i];
      var label = track.label;
      if (track.lang) label += ' (' + track.lang + ')';

      var btn = document.createElement('button');
      btn.className = 'video-player-audio-menu-item';
      btn.textContent = label;
      btn.setAttribute('type', 'button');
      btn.setAttribute('data-audio-index', String(i));

      (function (idx) {
        btn.addEventListener('click', function () {
          player_instance._select_audio_track(idx);
        });
      })(i);

      this.audio_menu.appendChild(btn);
    }
  };
  /**
   * Select an audio track.
   * @param {number} index  -1 = default (video built-in), 0+ = external track
   */
  CustomVideoPlayer.prototype._select_audio_track = function (index) {
    // Pause & reset all external audio elements
    for (var i = 0; i < this._audio_tracks.length; i++) {
      var audio_el = this._audio_tracks[i].audio_element;
      if (audio_el) {
        audio_el.pause();
        audio_el.currentTime = 0;

      }
    }

    this._active_audio_track = index;

    var player_instance = this;

    if (index === -1) {
      // Restore video's own audio
      this.video.muted = false;
    } else {
      // Mute video's built-in audio, play external track instead
      this.video.muted = true;

      var selected = this._audio_tracks[index];
      if (selected && selected.audio_element) {
        selected.audio_element.currentTime = this.video.currentTime;
        selected.audio_element.volume = this.video.volume;
        selected.audio_element.playbackRate = this.video.playbackRate;
        if (!this.video.paused) {
          selected.audio_element.play().catch(function (err) {
            // Playback blocked (common on iOS) — revert to default audio
            player_instance.video.muted = false;
            player_instance._active_audio_track = -1;
            player_instance._update_audio_menu_ui(-1);
            player_instance._show_error('Audio track not available on this device');
          });
        }
      }
    }

    this._update_audio_menu_ui(index);
    this._close_audio_menu();
  };

  /**
   * Update audio menu UI to reflect the active track.
   * @param {number} active_index  Currently active audio track index
   */
  CustomVideoPlayer.prototype._update_audio_menu_ui = function (active_index) {
    if (this.audio_menu) {
      var items = this.audio_menu.querySelectorAll('.video-player-audio-menu-item');
      for (var j = 0; j < items.length; j++) {
        var item_index = parseInt(items[j].getAttribute('data-audio-index'), 10);
        items[j].classList.toggle('active', item_index === active_index);
      }
    }
  };

  /** Sync helpers — keep active external audio in sync with video */
  CustomVideoPlayer.prototype._sync_audio_play = function () {
    if (this._active_audio_track < 0) return;
    var player_instance = this;
    var track = this._audio_tracks[this._active_audio_track];
    if (track && track.audio_element) {
      track.audio_element.currentTime = this.video.currentTime;
      track.audio_element.play().catch(function () {
        // Playback blocked — revert to default audio
        player_instance.video.muted = false;
        player_instance._active_audio_track = -1;
        player_instance._update_audio_menu_ui(-1);
      });
    }
  };

  CustomVideoPlayer.prototype._sync_audio_pause = function () {
    if (this._active_audio_track < 0) return;
    var track = this._audio_tracks[this._active_audio_track];
    if (track && track.audio_element) {
      track.audio_element.pause();
    }
  };

  CustomVideoPlayer.prototype._sync_audio_time = function () {
    if (this._active_audio_track < 0) return;
    var track = this._audio_tracks[this._active_audio_track];
    if (track && track.audio_element) {
      track.audio_element.currentTime = this.video.currentTime;
    }
  };

  CustomVideoPlayer.prototype._sync_audio_rate = function () {
    if (this._active_audio_track < 0) return;
    var track = this._audio_tracks[this._active_audio_track];
    if (track && track.audio_element) {
      track.audio_element.playbackRate = this.video.playbackRate;
    }
  };

  CustomVideoPlayer.prototype._sync_audio_volume = function () {
    // Only sync volume to external audio if we're on an external track
    if (this._active_audio_track < 0) return;
    var track = this._audio_tracks[this._active_audio_track];
    if (track && track.audio_element) {
      track.audio_element.volume = this.video.volume;
    }
  };

  CustomVideoPlayer.prototype._toggle_audio_menu = function () {
    if (!this.audio_menu) return;
    var is_open = this.audio_menu.classList.contains('active');
    this._close_captions_menu();
    this._close_quality_menu();
    if (is_open) {
      this._close_audio_menu();
    } else {
      this.audio_menu.classList.add('active');
    }
  };

  CustomVideoPlayer.prototype._close_audio_menu = function () {
    if (this.audio_menu) this.audio_menu.classList.remove('active');
  };

  /**
   * Bind event handlers
   */
  CustomVideoPlayer.prototype._bind_handlers = function () {
    var player_instance = this;
    var handlers = this._bound_handlers;

    handlers.play = function () { player_instance.toggle_play(); };
    handlers.progress_input = function () { player_instance._on_progress_input(); };
    handlers.progress_change = function () { player_instance._on_progress_change(); };
    handlers.progress_mouse_down = function () { player_instance._progress_dragging = true; };
    handlers.progress_mouse_up = function () { player_instance._progress_dragging = false; };
    handlers.mute = function () { player_instance.toggle_mute(); };
    handlers.volume_input = function () { player_instance._on_volume_input(); };
    handlers.fullscreen = function () { player_instance.toggle_fullscreen(); };
    handlers.cc = function () { player_instance._toggle_captions_menu(); };
    handlers.quality = function () { player_instance._toggle_quality_menu(); };
    handlers.timeupdate = function () { 
      player_instance._sync_progress();
      player_instance._update_time_display();
    };
    handlers.durationchange = function () { 
      player_instance._sync_progress(); 
      player_instance._update_time_display();
    };
    handlers.progress = function () { player_instance._sync_buffered(); };
    handlers.loadstart = function () {
      // Only show the loading spinner if the user has initiated playback.
      // On iOS the video stays paused until a user gesture; showing a
      // spinner over a paused video that will never auto-buffer is wrong.
      // The skeleton loader handles the visual during the initial load.
      if (!player_instance.video.paused) {
        player_instance._set_loading(true);
      }
    };
    handlers.loadedmetadata = function () {
      // Metadata arrived — clear any loading state.
      // On iOS/touch canplay may never fire until user plays, so this
      // is the safety-net that hides the spinner after the initial load.
      player_instance._set_loading(false);

      if ('ontouchstart' in window || navigator.maxTouchPoints > 0) {
        player_instance._remove_skeleton();
      }
    };
    handlers.waiting = function () {
      player_instance._set_loading(true);
      player_instance._start_stall_watchdog();
    };
    handlers.stalled = function () {
      // Only show spinner for stall if video is actively playing/buffering.
      // Stalls during initial preload on iOS shouldn't show a spinner.
      if (!player_instance.video.paused) {
        player_instance._set_loading(true);
        player_instance._start_stall_watchdog();
      }
    };
    handlers.canplay = function () {
      player_instance._set_loading(false);
      player_instance._remove_skeleton();
    };
    handlers.canplaythrough = function () { player_instance._set_loading(false); };
    handlers.playing = function () { 
      player_instance._set_loading(false); 
      player_instance._set_playing(true);
      player_instance._start_stall_watchdog();
    };
    handlers.pause = function () {
      player_instance._set_playing(false);
      player_instance._stop_stall_watchdog();
    };
    handlers.ended = function () { 
      player_instance._set_playing(false); 
      player_instance._sync_progress();
      player_instance._stop_stall_watchdog();
    };
    handlers.error = function () { player_instance._show_error(); };
    handlers.keydown = function (event) { player_instance._on_keydown(event); };
    handlers.fullscreen_change = function () { player_instance._sync_fullscreen_class(); };
    handlers.click_outside = function (event) { player_instance._on_click_outside(event); };
  };

  /**
   * Attach event listeners
   */
  CustomVideoPlayer.prototype._attach_listeners = function () {
    var player_instance = this;
    var video_element = this.video;
    var wrapper_element = this.wrapper;
    var handlers = this._bound_handlers;

    // Control buttons
    if (this.play_btn) this.play_btn.addEventListener('click', handlers.play);
    if (this.play_overlay) this.play_overlay.addEventListener('click', handlers.play);
    if (this.progress_input) {
      this.progress_input.addEventListener('input', handlers.progress_input);
      this.progress_input.addEventListener('change', handlers.progress_change);
      this.progress_input.addEventListener('mousedown', handlers.progress_mouse_down);
      this.progress_input.addEventListener('mouseup', handlers.progress_mouse_up);
      this.progress_input.addEventListener('touchstart', handlers.progress_mouse_down);
      this.progress_input.addEventListener('touchend', handlers.progress_mouse_up);
    }
    if (this.mute_btn) this.mute_btn.addEventListener('click', handlers.mute);
    if (this.volume_input) this.volume_input.addEventListener('input', handlers.volume_input);
    if (this.fullscreen_btn) this.fullscreen_btn.addEventListener('click', handlers.fullscreen);
    if (this.cc_btn) this.cc_btn.addEventListener('click', handlers.cc);
    if (this.quality_btn) this.quality_btn.addEventListener('click', handlers.quality);

    // Video events
    video_element.addEventListener('timeupdate', handlers.timeupdate);
    video_element.addEventListener('durationchange', handlers.durationchange);
    video_element.addEventListener('progress', handlers.progress);
    video_element.addEventListener('loadstart', handlers.loadstart);
    video_element.addEventListener('loadedmetadata', handlers.loadedmetadata);
    video_element.addEventListener('waiting', handlers.waiting);
    video_element.addEventListener('stalled', handlers.stalled);
    video_element.addEventListener('canplay', handlers.canplay);
    video_element.addEventListener('canplaythrough', handlers.canplaythrough);
    video_element.addEventListener('playing', handlers.playing);
    video_element.addEventListener('pause', handlers.pause);
    video_element.addEventListener('ended', handlers.ended);
    video_element.addEventListener('error', handlers.error);

    // Keyboard and click
    wrapper_element.addEventListener('keydown', handlers.keydown);
    wrapper_element.setAttribute('tabindex', '0');
    wrapper_element.addEventListener('click', function (click_event) {
      if (click_event.target === wrapper_element || click_event.target === video_element) {
        player_instance.toggle_play();
      }
    });

    // Mouse movement for auto-hide controls
    wrapper_element.addEventListener('mousemove', function() {
      player_instance._show_controls_temporarily();
    });

    wrapper_element.addEventListener('mouseenter', function() {
      player_instance._show_controls_temporarily();
    });

    wrapper_element.addEventListener('mouseleave', function() {
      if (!player_instance.video.paused) {
        player_instance._hide_controls();
      }
    });

    // Touch events for iOS/mobile: show controls on tap so play button and control bar are visible
    if ('ontouchstart' in window || navigator.maxTouchPoints > 0) {
      wrapper_element.addEventListener('touchstart', function () {
        player_instance._show_controls_temporarily();
      }, { passive: true });
    }

    // Fullscreen changes
    document.addEventListener('fullscreenchange', handlers.fullscreen_change);
    document.addEventListener('webkitfullscreenchange', handlers.fullscreen_change);
    document.addEventListener('mozfullscreenchange', handlers.fullscreen_change);
    document.addEventListener('MSFullscreenChange', handlers.fullscreen_change);

    // iOS native video fullscreen events
    video_element.addEventListener('webkitbeginfullscreen', function () {
      player_instance.wrapper.classList.add('video-player-fullscreen');
    });
    video_element.addEventListener('webkitendfullscreen', function () {
      player_instance.wrapper.classList.remove('video-player-fullscreen');
    });

    // Click outside to close menus
    document.addEventListener('click', handlers.click_outside);
  };

  /**
   * Handle clicks outside the player to close menus
   */
  CustomVideoPlayer.prototype._on_click_outside = function (event) {
    if (!this.wrapper.contains(event.target)) {
      this._close_captions_menu();
      this._close_quality_menu();
      this._close_audio_menu();
    }
  };

  /* ===========================================
     Stall Recovery (primarily for iOS)
     ===========================================
     iOS Safari with preload="metadata" often stalls after the user
     presses play: the video reaches ~0-1 s and then stops advancing.
     The watchdog checks every few seconds whether currentTime has
     changed.  If not, it nudges the video with a small seek, and
     as a last resort reloads the source.
     =========================================== */

  /**
   * Start the stall watchdog.  Called when the video enters a
   * potentially-stalled state (waiting / playing).
   */
  CustomVideoPlayer.prototype._start_stall_watchdog = function () {
    this._stop_stall_watchdog();

    var player_instance = this;
    this._last_known_time = this.video.currentTime;

    this._stall_timer = setInterval(function () {
      var vid = player_instance.video;
      if (vid.paused || vid.ended) {
        player_instance._stop_stall_watchdog();
        return;
      }

      var current = vid.currentTime;
      // If time hasn't changed by at least 0.1 s → video is stuck
      if (Math.abs(current - player_instance._last_known_time) < 0.1) {
        player_instance._stall_recovery_attempts++;

        if (player_instance._stall_recovery_attempts <= 3) {
          // Nudge: seek slightly forward to force a rebuffer
          var nudge = current + 0.1;
          if (isFinite(vid.duration) && nudge < vid.duration) {
            vid.currentTime = nudge;
          } else {
            vid.currentTime = current;   // seek-to-self also triggers rebuffer
          }
        } else {
          // Heavier recovery: reload the video source
          player_instance._stop_stall_watchdog();
          var src = vid.src || (vid.querySelector('source') || {}).src;
          if (src) {
            var resume_time = current;
            vid.load();
            vid.addEventListener('loadedmetadata', function onMeta() {
              vid.removeEventListener('loadedmetadata', onMeta);
              vid.currentTime = resume_time;
              vid.play().catch(function () {});
            });
          }
        }
      } else {
        // Making progress — reset counter
        player_instance._stall_recovery_attempts = 0;
      }
      player_instance._last_known_time = current;
    }, 4000);
  };

  /**
   * Stop the stall watchdog timer.
   */
  CustomVideoPlayer.prototype._stop_stall_watchdog = function () {
    if (this._stall_timer) {
      clearInterval(this._stall_timer);
      this._stall_timer = null;
    }
    this._stall_recovery_attempts = 0;
  };

  /**
   * Prepare iOS playback before first user-initiated play.
   * Helps avoid "stuck at 0:01" on local MP4 streams.
   */
  CustomVideoPlayer.prototype._prepare_ios_playback = function () {
    if (!is_ios_device() || this._ios_playback_prepared) return;
    this._ios_playback_prepared = true;

    this.video.preload = 'auto';
    this.video.setAttribute('preload', 'auto');

    // Force iOS to start fetching playable media data.
    // Safe in a user-gesture path (toggle_play click/tap).
    if (this.video.readyState < 3) {
      try { this.video.load(); } catch (e) { /* ignore */ }
    }
  };

  /**
   * Toggle play/pause.
   * video.play() returns a Promise on modern browsers (including iOS Safari)
   * which rejects if playback is not allowed (e.g. no user gesture).
   */
  CustomVideoPlayer.prototype.toggle_play = function () {
    var player_instance = this;
    try {
      if (this.video.paused) {
        this._prepare_ios_playback();
        var play_promise = this.video.play();
        if (play_promise && typeof play_promise.catch === 'function') {
          play_promise.catch(function (err) {
            if (err.name !== 'AbortError') {
              player_instance._show_error(err.message || 'Playback failed');
            }
          });
        }
      } else {
        this.video.pause();
      }
    } catch (error) {
      this._show_error(error.message);
    }
  };

  /**
   * Set playing state
   * @param {boolean} is_playing - Whether video is playing
   */
  CustomVideoPlayer.prototype._set_playing = function (is_playing) {
    this.wrapper.classList.toggle('video-player-playing', is_playing);
    this.wrapper.classList.toggle('video-player-paused', !is_playing);
    if (this.play_btn) {
      this.play_btn.setAttribute('aria-label', is_playing ? 'Pause' : 'Play');
    }
  };

  /**
   * Sync progress bar with current time
   */
  CustomVideoPlayer.prototype._sync_progress = function () {
    if (!this.progress_input || this._progress_dragging) return;
    
    var video_element = this.video;
    var duration_seconds = video_element.duration;
    
    if (!isFinite(duration_seconds) || duration_seconds <= 0) return;
    
    var progress_percent = (video_element.currentTime / duration_seconds) * 100;
    var clamped_percent = Math.min(100, Math.max(0, progress_percent));
    
    this.progress_input.value = String(clamped_percent);
    
    // Update visual played bar
    if (this.played_bar) {
      this.played_bar.style.width = clamped_percent + '%';
    }
  };

  /**
   * Sync buffered ranges visualization
   */
  CustomVideoPlayer.prototype._sync_buffered = function () {
    if (!this.buffered_bar || !this.video.buffered || !this.video.duration) return;
    
    var duration = this.video.duration;
    var buffered = this.video.buffered;
    
    if (buffered.length > 0) {
      // Get the last buffered range (usually the most relevant)
      var buffered_end = buffered.end(buffered.length - 1);
      var buffered_percent = (buffered_end / duration) * 100;
      this.buffered_bar.style.width = Math.min(100, buffered_percent) + '%';
    }
  };

  /**
   * Handle progress input (seeking)
   */
  CustomVideoPlayer.prototype._on_progress_input = function () {
    if (!this.progress_input) return;
    
    var progress_percent = Number(this.progress_input.value);
    var duration_seconds = this.video.duration;
    
    if (isFinite(duration_seconds) && duration_seconds > 0) {
      this.video.currentTime = (progress_percent / 100) * duration_seconds;
      
      // Update visual played bar immediately for responsiveness
      if (this.played_bar) {
        this.played_bar.style.width = progress_percent + '%';
      }
    }
  };

  /**
   * Handle progress change (after seeking)
   */
  CustomVideoPlayer.prototype._on_progress_change = function () {
    this._on_progress_input();
  };

  /**
   * Toggle mute
   */
  CustomVideoPlayer.prototype.toggle_mute = function () {
    this.video.muted = !this.video.muted;
    this.wrapper.classList.toggle('video-player-muted', this.video.muted);
    
    if (this.mute_btn) {
      this.mute_btn.setAttribute('aria-label', this.video.muted ? 'Unmute' : 'Mute');
    }
    if (this.volume_input) {
      this.volume_input.value = this.video.muted ? '0' : String(Math.round(this.video.volume * 100));
    }
  };

  /**
   * Handle volume input
   */
  CustomVideoPlayer.prototype._on_volume_input = function () {
    if (!this.volume_input) return;
    
    var volume_percent = Number(this.volume_input.value);
    this.video.volume = Math.min(1, Math.max(0, volume_percent / 100));
    this.video.muted = volume_percent === 0;
    this.wrapper.classList.toggle('video-player-muted', this.video.muted);
  };

  /**
   * Toggle fullscreen.
   * iOS Safari doesn't support the Fullscreen API on arbitrary elements —
   * only <video>.webkitEnterFullscreen() works, so we fall back to that.
   */
  CustomVideoPlayer.prototype.toggle_fullscreen = function () {
    var wrapper_element = this.wrapper;
    var video_element = this.video;

    try {
      if (this._is_fullscreen()) {
        if (document.exitFullscreen) document.exitFullscreen();
        else if (document.webkitExitFullscreen) document.webkitExitFullscreen();
        else if (document.mozCancelFullScreen) document.mozCancelFullScreen();
        else if (document.msExitFullscreen) document.msExitFullscreen();
      } else if (wrapper_element.requestFullscreen) {
        wrapper_element.requestFullscreen();
      } else if (wrapper_element.webkitRequestFullscreen) {
        wrapper_element.webkitRequestFullscreen();
      } else if (wrapper_element.mozRequestFullScreen) {
        wrapper_element.mozRequestFullScreen();
      } else if (wrapper_element.msRequestFullscreen) {
        wrapper_element.msRequestFullscreen();
      } else if (video_element.webkitEnterFullscreen) {
        // iOS Safari: Fullscreen API not available on wrappers,
        // fall back to native video fullscreen.
        video_element.webkitEnterFullscreen();
      }
    } catch (error) {
      // Last resort: try native video fullscreen (iOS)
      if (video_element.webkitEnterFullscreen) {
        try { video_element.webkitEnterFullscreen(); } catch (e) { /* ignore */ }
      }
    }
  };

  /**
   * Check if in fullscreen mode
   * @returns {boolean} True if in fullscreen
   */
  CustomVideoPlayer.prototype._is_fullscreen = function () {
    return !!(
      document.fullscreenElement ||
      document.webkitFullscreenElement ||
      document.mozFullScreenElement ||
      document.msFullscreenElement
    );
  };

  /**
   * Sync fullscreen class state
   */
  CustomVideoPlayer.prototype._sync_fullscreen_class = function () {
    var is_fullscreen_active = this._is_fullscreen();
    var is_this_player_fullscreen = is_fullscreen_active && (
      document.fullscreenElement === this.wrapper ||
      document.webkitFullscreenElement === this.wrapper ||
      document.mozFullScreenElement === this.wrapper ||
      document.msFullscreenElement === this.wrapper
    );
    
    this.wrapper.classList.toggle('video-player-fullscreen', !!is_this_player_fullscreen);
    
    if (this.fullscreen_btn) {
      this.fullscreen_btn.setAttribute('aria-label', is_this_player_fullscreen ? 'Exit fullscreen' : 'Fullscreen');
    }
  };

  /**
   * Set loading / buffering state.
   * Uses "is-loading" on the wrapper to avoid name collision with
   * the child element ".video-player-loading".
   * @param {boolean} is_loading - Whether video is loading / buffering
   */
  CustomVideoPlayer.prototype._set_loading = function (is_loading) {
    this.wrapper.classList.toggle('is-loading', is_loading);
  };

  /**
   * Remove the skeleton loader.
   * Called once when the video fires "canplay" for the first time.
   * @param {boolean} [immediate] - Skip fade animation (e.g. video was cached)
   */
  CustomVideoPlayer.prototype._remove_skeleton = function (immediate) {
    if (this._skeleton_fallback_timeout) {
      clearTimeout(this._skeleton_fallback_timeout);
      this._skeleton_fallback_timeout = null;
    }
    if (this._skeleton_removed) return;
    this._skeleton_removed = true;

    this.wrapper.classList.remove('video-player-skeleton');

    var skeleton = this.wrapper.querySelector('.vp-skeleton');
    if (!skeleton) return;

    if (immediate) {
      // Already loaded — remove instantly, no flash
      if (skeleton.parentNode) skeleton.parentNode.removeChild(skeleton);
      return;
    }

    // Fade out then remove
    skeleton.style.transition = 'opacity 0.35s ease';
    skeleton.style.opacity = '0';

    setTimeout(function () {
      if (skeleton.parentNode) skeleton.parentNode.removeChild(skeleton);
    }, 350);
  };

  /**
   * Show error message
   * @param {string} message - Error message
   */
  CustomVideoPlayer.prototype._show_error = function (message) {
    var error_message = message || (this.video.error && this.video.error.message) || 'Video error';
    
    if (this.error_element) {
      this.error_element.textContent = error_message;
      this.error_element.hidden = false;
      
      // Auto-hide error after 5 seconds
      var player_instance = this;
      setTimeout(function() {
        if (player_instance.error_element) {
          player_instance.error_element.hidden = true;
        }
      }, 5000);
    }
  };

  /**
   * Update time display
   */
  CustomVideoPlayer.prototype._update_time_display = function () {
    if (!this.time_display) return;
    
    var current_time = this.video.currentTime || 0;
    var duration = this.video.duration;
    
    if (isFinite(duration) && duration > 0) {
      this.time_display.textContent = format_time(current_time) + ' / ' + format_time(duration);
    } else {
      this.time_display.textContent = format_time(current_time);
    }
  };

  /**
   * Show controls temporarily (auto-hide after 3 seconds)
   */
  CustomVideoPlayer.prototype._show_controls_temporarily = function () {
    var player_instance = this;
    
    // Show controls
    this.wrapper.classList.add('video-player-controls-visible');
    
    // Clear existing timeout
    if (this._mouse_move_timeout) {
      clearTimeout(this._mouse_move_timeout);
    }
    
    // Hide controls after 3 seconds of inactivity (only if playing)
    if (!this.video.paused) {
      this._mouse_move_timeout = setTimeout(function() {
        player_instance._hide_controls();
      }, 3000);
    }
  };

  /**
   * Hide controls
   */
  CustomVideoPlayer.prototype._hide_controls = function () {
    this.wrapper.classList.remove('video-player-controls-visible');
    if (this._mouse_move_timeout) {
      clearTimeout(this._mouse_move_timeout);
      this._mouse_move_timeout = null;
    }
  };

  /**
   * Handle keyboard shortcuts
   * @param {KeyboardEvent} keyboard_event - Keyboard event
   */
  CustomVideoPlayer.prototype._on_keydown = function (keyboard_event) {
    var target = keyboard_event.target;
    
    // Don't handle if focus is on an input/button (unless it's the wrapper itself)
    if (target.tagName === 'INPUT' && target !== this.progress_input && target !== this.volume_input) return;
    if (target.tagName === 'BUTTON' && !this.wrapper.contains(target)) return;
    if (!this.wrapper.contains(target)) return;
    
    var handled = false;
    
    switch (keyboard_event.key) {
      case ' ':
      case 'Spacebar': // For older browsers
        keyboard_event.preventDefault();
        this.toggle_play();
        handled = true;
        break;
      case 'k':
      case 'K':
        keyboard_event.preventDefault();
        this.toggle_play();
        handled = true;
        break;
      case 'f':
      case 'F':
        keyboard_event.preventDefault();
        this.toggle_fullscreen();
        handled = true;
        break;
      case 'm':
      case 'M':
        keyboard_event.preventDefault();
        this.toggle_mute();
        handled = true;
        break;
      case 'c':
      case 'C':
        keyboard_event.preventDefault();
        if (this.cc_btn) {
          this._toggle_captions_menu();
        }
        handled = true;
        break;
      case 'ArrowLeft':
        keyboard_event.preventDefault();
        this.video.currentTime = Math.max(0, this.video.currentTime - 10);
        handled = true;
        break;
      case 'ArrowRight':
        keyboard_event.preventDefault();
        this.video.currentTime = Math.min(this.video.duration || 0, this.video.currentTime + 10);
        handled = true;
        break;
      case 'ArrowUp':
        keyboard_event.preventDefault();
        this.video.volume = Math.min(1, this.video.volume + 0.1);
        if (this.volume_input) {
          this.volume_input.value = String(Math.round(this.video.volume * 100));
        }
        handled = true;
        break;
      case 'ArrowDown':
        keyboard_event.preventDefault();
        this.video.volume = Math.max(0, this.video.volume - 0.1);
        if (this.volume_input) {
          this.volume_input.value = String(Math.round(this.video.volume * 100));
        }
        handled = true;
        break;
      case 'Escape':
      case 'Esc': // For older browsers
        if (this._is_fullscreen()) {
          keyboard_event.preventDefault();
          this.toggle_fullscreen();
          handled = true;
        }
        // Also close captions menu
        this._close_captions_menu();
        break;
      case '0':
      case '1':
      case '2':
      case '3':
      case '4':
      case '5':
      case '6':
      case '7':
      case '8':
      case '9':
        // Seek to percentage (0 = start, 9 = 90%)
        if (isFinite(this.video.duration) && this.video.duration > 0) {
          keyboard_event.preventDefault();
          var percent = parseInt(keyboard_event.key) / 10;
          this.video.currentTime = this.video.duration * percent;
          handled = true;
        }
        break;
    }
    
    if (handled) {
      // Show controls briefly
      this._show_controls_temporarily();
    }
  };

  /**
   * Sync UI with current video state
   */
  CustomVideoPlayer.prototype._sync_ui = function () {
    this._set_playing(!this.video.paused);
    this.wrapper.classList.toggle('video-player-muted', this.video.muted);
    
    if (this.volume_input) {
      this.volume_input.value = String(this.video.muted ? 0 : Math.round(this.video.volume * 100));
    }
    
    this._sync_progress();
    this._sync_buffered();
    this._sync_fullscreen_class();
    this._update_time_display();
  };

  /**
   * Destroy player and remove event listeners
   */
  CustomVideoPlayer.prototype.destroy = function () {
    var handlers = this._bound_handlers;
    var video_element = this.video;
    var wrapper_element = this.wrapper;
    
    // Destroy adaptive streaming instances
    if (this._hls_instance) {
      this._hls_instance.destroy();
      this._hls_instance = null;
    }
    if (this._dash_instance) {
      this._dash_instance.reset();
      this._dash_instance = null;
    }

    // Destroy external audio track elements
    if (this._audio_tracks) {
      for (var i = 0; i < this._audio_tracks.length; i++) {
        var audio_el = this._audio_tracks[i].audio_element;
        if (audio_el) {
          audio_el.pause();
          audio_el.src = '';
          if (audio_el.parentNode) audio_el.parentNode.removeChild(audio_el);
        }
      }
      this._audio_tracks = [];
    }
    
    // Clear timeouts
    if (this._controls_timeout) clearTimeout(this._controls_timeout);
    if (this._mouse_move_timeout) clearTimeout(this._mouse_move_timeout);
    if (this._skeleton_fallback_timeout) clearTimeout(this._skeleton_fallback_timeout);
    this._stop_stall_watchdog();
    
    // Remove button listeners
    if (this.play_btn) this.play_btn.removeEventListener('click', handlers.play);
    if (this.play_overlay) this.play_overlay.removeEventListener('click', handlers.play);
    if (this.progress_input) {
      this.progress_input.removeEventListener('input', handlers.progress_input);
      this.progress_input.removeEventListener('change', handlers.progress_change);
      this.progress_input.removeEventListener('mousedown', handlers.progress_mouse_down);
      this.progress_input.removeEventListener('mouseup', handlers.progress_mouse_up);
      this.progress_input.removeEventListener('touchstart', handlers.progress_mouse_down);
      this.progress_input.removeEventListener('touchend', handlers.progress_mouse_up);
    }
    if (this.mute_btn) this.mute_btn.removeEventListener('click', handlers.mute);
    if (this.volume_input) this.volume_input.removeEventListener('input', handlers.volume_input);
    if (this.fullscreen_btn) this.fullscreen_btn.removeEventListener('click', handlers.fullscreen);
    if (this.cc_btn) this.cc_btn.removeEventListener('click', handlers.cc);
    if (this.quality_btn) this.quality_btn.removeEventListener('click', handlers.quality);
    
    // Remove video listeners
    video_element.removeEventListener('timeupdate', handlers.timeupdate);
    video_element.removeEventListener('durationchange', handlers.durationchange);
    video_element.removeEventListener('progress', handlers.progress);
    video_element.removeEventListener('loadstart', handlers.loadstart);
    video_element.removeEventListener('loadedmetadata', handlers.loadedmetadata);
    video_element.removeEventListener('waiting', handlers.waiting);
    video_element.removeEventListener('stalled', handlers.stalled);
    video_element.removeEventListener('canplay', handlers.canplay);
    video_element.removeEventListener('canplaythrough', handlers.canplaythrough);
    video_element.removeEventListener('playing', handlers.playing);
    video_element.removeEventListener('pause', handlers.pause);
    video_element.removeEventListener('ended', handlers.ended);
    video_element.removeEventListener('error', handlers.error);
    
    // Remove wrapper listeners
    wrapper_element.removeEventListener('keydown', handlers.keydown);
    
    // Remove document listeners
    document.removeEventListener('fullscreenchange', handlers.fullscreen_change);
    document.removeEventListener('webkitfullscreenchange', handlers.fullscreen_change);
    document.removeEventListener('mozfullscreenchange', handlers.fullscreen_change);
    document.removeEventListener('MSFullscreenChange', handlers.fullscreen_change);
    document.removeEventListener('click', handlers.click_outside);
    
    // Clear reference
    delete wrapper_element._custom_video_player;
  };

  /**
   * Initialize all players on the page.
   */
  function init_all_players() {
    var player_wrappers = document.querySelectorAll('[data-player]');
    
    for (var index = 0; index < player_wrappers.length; index++) {
      var wrapper_element = player_wrappers[index];
      
      if (wrapper_element && !wrapper_element._custom_video_player) {
        var player_instance = new CustomVideoPlayer(wrapper_element);
        
        if (player_instance.video) {
          wrapper_element._custom_video_player = player_instance;
        }
      }
    }
  }

  // Initialize on DOM ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init_all_players);
  } else {
    init_all_players();
  }

  // Expose API
  window.CustomVideoPlayer = CustomVideoPlayer;
  window.init_custom_video_players = init_all_players;
})();
