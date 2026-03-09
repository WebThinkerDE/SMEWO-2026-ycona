<?php
/**
 * Video block render callback.
 * Outputs title + custom video-player structure (see CUSTOM-VIDEO-PLAYER-PLAN.md).
 * Requires theme assets: custom-video-player.css, custom-video-player.js (enqueued globally).
 *
 * @package WordPress
 * @subpackage ycona
 */

function wt_video_block_rc( $attributes, $content ) {
	global $theme_path;

	wp_enqueue_style( 'custom-video-player', $theme_path . '/assets/video-player/custom-video-player.css', array(), _S_VERSION );
	wp_enqueue_script( 'custom-video-player', $theme_path . '/assets/video-player/custom-video-player.js', array(), _S_VERSION, true );
	wp_enqueue_style( 'wt_video_block', $theme_path . '/wt-blocks/video-block/video_block.css', array( 'css-main', 'custom-video-player' ), '1' );

	$title     = isset( $attributes['title'] ) ? $attributes['title'] : '';
	$video_url = isset( $attributes['video_url'] ) ? $attributes['video_url'] : '';
	if ( empty( $video_url ) && ! empty( $attributes['video']['url'] ) ) {
		$video_url = $attributes['video']['url'];
	}
	$poster_url = '';
	if ( ! empty( $attributes['poster'] ) && isset( $attributes['poster']['url'] ) ) {
		$poster_url = $attributes['poster']['url'];
	}

	if ( empty( $video_url ) ) {
		return '<section class="video-block video-block-no-src"><p class="video-block-empty">' . esc_html__( 'Please set a video URL in the block settings.', 'ycona' ) . '</p></section>';
	}

	$title_html = '';
	if ( $title !== '' ) {
		$title_html = '<h2 class="video-block-title">' . esc_html( $title ) . '</h2>';
	}

	$poster_attr = $poster_url !== '' ? ' poster="' . esc_url( $poster_url ) . '"' : '';
	$title_attr  = $title !== '' ? ' title="' . esc_attr( $title ) . '"' : '';

	// Get subtitles/captions from attributes if available
	$subtitles = isset( $attributes['subtitles'] ) ? $attributes['subtitles'] : array();
	$track_html = '';
	
	if ( ! empty( $subtitles ) && is_array( $subtitles ) ) {
		foreach ( $subtitles as $index => $track ) {
			if ( ! empty( $track['url'] ) ) {
				$track_label = ! empty( $track['label'] ) ? esc_attr( $track['label'] ) : 'Subtitles ' . ( $index + 1 );
				$track_srclang = ! empty( $track['srclang'] ) ? esc_attr( $track['srclang'] ) : 'en';
				$track_kind = ! empty( $track['kind'] ) ? esc_attr( $track['kind'] ) : 'subtitles';
				$track_html .= '<track kind="' . $track_kind . '" src="' . esc_url( $track['url'] ) . '" srclang="' . $track_srclang . '" label="' . $track_label . '">';
			}
		}
	}

	// Only add crossorigin if subtitles are present
	$crossorigin_attr = ! empty( $track_html ) ? ' crossorigin="anonymous"' : '';

	// Get audio tracks from attributes if available
	$audio_tracks = isset( $attributes['audio_tracks'] ) ? $attributes['audio_tracks'] : array();
	$audio_tracks_attr = '';

	if ( ! empty( $audio_tracks ) && is_array( $audio_tracks ) ) {
		$sanitised_tracks = array();
		foreach ( $audio_tracks as $a_track ) {
			if ( ! empty( $a_track['url'] ) ) {
				$sanitised_tracks[] = array(
					'url'   => esc_url( $a_track['url'] ),
					'label' => ! empty( $a_track['label'] ) ? esc_attr( $a_track['label'] ) : 'Audio ' . ( count( $sanitised_tracks ) + 1 ),
					'lang'  => ! empty( $a_track['lang'] )  ? esc_attr( $a_track['lang'] )  : '',
				);
			}
		}
		if ( ! empty( $sanitised_tracks ) ) {
			$audio_tracks_attr = " data-audio-tracks='" . wp_json_encode( $sanitised_tracks ) . "'";
		}
	}

	// Skin class — defaults to none (= Default skin).
	// Allowed values: '' | 'ocean' | 'cinema' | 'minimal'
	$skin    = isset( $attributes['skin'] ) ? sanitize_html_class( $attributes['skin'] ) : '';
	$skin_class = $skin !== '' ? ' video-player-skin-' . $skin : '';

	// Width & height — accept any CSS value (px, %, vw, auto, etc.)
	$width  = isset( $attributes['width'] )  ? trim( $attributes['width'] )  : '';
	$height = isset( $attributes['height'] ) ? trim( $attributes['height'] ) : '';

	$dimension_styles = '';
	if ( $width !== '' ) {
		$dimension_styles .= 'width:' . esc_attr( $width ) . ';';
	}
	if ( $height !== '' ) {
		$dimension_styles .= 'height:' . esc_attr( $height ) . ';';
	}
	$style_attr = $dimension_styles !== '' ? ' style="' . $dimension_styles . '"' : '';

	$markup = '<section class="video-block container">' . $title_html . '
	<div class="video-player video-player-skeleton' . $skin_class . '" data-player' . $style_attr . $audio_tracks_attr . '>

		<!-- Skeleton Loader (visible until video is ready) -->
		<div class="vp-skeleton" aria-hidden="true">
			<div class="vp-skeleton-shimmer"></div>
			<div class="vp-skeleton-play"></div>
			<div class="vp-skeleton-controls">
				<div class="vp-skeleton-btn"></div>
				<div class="vp-skeleton-bar"></div>
				<div class="vp-skeleton-time"></div>
				<div class="vp-skeleton-btn"></div>
				<div class="vp-skeleton-btn-sm"></div>
				<div class="vp-skeleton-btn"></div>
			</div>
		</div>

		<video src="' . esc_url( $video_url ) . '" preload="metadata" playsinline webkit-playsinline' . $poster_attr . $title_attr . $crossorigin_attr . '>' . $track_html . '</video>
		
		<!-- Centered Play Button Overlay -->
		<div class="video-player-play-overlay" data-play-overlay aria-label="' . esc_attr__( 'Play Video', 'ycona' ) . '">
			<i class="bi bi-play-fill" aria-hidden="true"></i>
		</div>
		
		<!-- Captions Display -->
		<div class="video-player-captions" data-captions aria-live="polite" aria-atomic="true"></div>
		
		<!-- Bottom Controls -->
		<div class="video-player-controls" data-controls>
			<button type="button" class="video-player-btn" data-play aria-label="' . esc_attr__( 'Play', 'ycona' ) . '">
				<i class="bi bi-play-fill" aria-hidden="true"></i>
				<i class="bi bi-pause-fill" aria-hidden="true"></i>
				<span class="sr-only">' . esc_html__( 'Play / Pause', 'ycona' ) . '</span>
			</button>
			<div class="video-player-progress-wrap" data-progress-wrap>
				<input type="range" class="video-player-progress" data-progress min="0" max="100" value="0" step="0.1" aria-label="' . esc_attr__( 'Seek', 'ycona' ) . '">
			</div>
			<span class="video-player-time" data-time aria-live="off">0:00</span>
			<button type="button" class="video-player-btn" data-mute aria-label="' . esc_attr__( 'Mute', 'ycona' ) . '">
				<i class="bi bi-volume-up-fill" aria-hidden="true"></i>
				<i class="bi bi-volume-mute-fill" aria-hidden="true"></i>
				<span class="sr-only">' . esc_html__( 'Mute / Unmute', 'ycona' ) . '</span>
			</button>
			<input type="range" class="video-player-volume" data-volume min="0" max="100" value="100" step="1" aria-label="' . esc_attr__( 'Volume', 'ycona' ) . '">
			<button type="button" class="video-player-btn" data-cc aria-label="' . esc_attr__( 'Captions', 'ycona' ) . '">
				<span>CC</span>
				<span class="sr-only">' . esc_html__( 'Toggle Captions', 'ycona' ) . '</span>
			</button>
			<button type="button" class="video-player-btn" data-quality aria-label="' . esc_attr__( 'Quality', 'ycona' ) . '" style="display:none">
				<i class="bi bi-gear-fill" aria-hidden="true"></i>
				<span class="vp-quality-label">AUTO</span>
			</button>
			<button type="button" class="video-player-btn" data-fullscreen aria-label="' . esc_attr__( 'Fullscreen', 'ycona' ) . '">
				<i class="bi bi-fullscreen" aria-hidden="true"></i>
				<i class="bi bi-fullscreen-exit" aria-hidden="true"></i>
				<span class="sr-only">' . esc_html__( 'Toggle Fullscreen', 'ycona' ) . '</span>
			</button>
		</div>
		
		<!-- Loading Indicator -->
		<div class="video-player-loading" data-loading aria-hidden="true" role="status">
			<span class="sr-only">' . esc_html__( 'Loading...', 'ycona' ) . '</span>
		</div>
		
		<!-- Error Display -->
		<div class="video-player-error" data-error aria-live="assertive" hidden></div>
	</div>
</section>';

	return $markup;
}
