<?php
/**
 * Single template for Program Builder CPT.
 *
 * @package ycona
 */

get_header();

while ( have_posts() ) :
	the_post();

	$meta               = get_post_meta( get_the_ID(), 'program_builder_fields', true );
	$meta               = is_array( $meta ) ? $meta : array();
	$program_title      = ! empty( $meta['program_title'] ) ? $meta['program_title'] : get_the_title();
	$short_description  = ! empty( $meta['short_description'] ) ? $meta['short_description'] : '';
	$topic_area         = ! empty( $meta['topic_area'] ) ? $meta['topic_area'] : '';
	$program_type       = ! empty( $meta['program_type'] ) ? $meta['program_type'] : '';
	$program_image      = ! empty( $meta['program_image'] ) ? $meta['program_image'] : '';
	$general_description = ! empty( $meta['general_description'] ) ? $meta['general_description'] : '';
	$glossary_display    = ! empty( $meta['glossary_display'] ) ? $meta['glossary_display'] : '';
	$chapters           = ! empty( $meta['chapters'] ) && is_array( $meta['chapters'] ) ? $meta['chapters'] : array();

	$first_chapter      = ! empty( $chapters ) ? reset( $chapters ) : array();
	$first_video_mp4    = ! empty( $first_chapter['video_mp4'] ) ? $first_chapter['video_mp4'] : '';
	$first_video_poster = ! empty( $first_chapter['video_poster'] ) ? $first_chapter['video_poster'] : '';
	$first_video_audio  = ! empty( $first_chapter['video_audio'] ) ? $first_chapter['video_audio'] : '';
	$first_video_title  = ! empty( $first_chapter['video_title'] ) ? $first_chapter['video_title'] : '';
	$first_subtitle_1   = ! empty( $first_chapter['subtitle_1'] ) ? $first_chapter['subtitle_1'] : '';
	$first_subtitle_2   = ! empty( $first_chapter['subtitle_2'] ) ? $first_chapter['subtitle_2'] : '';
	$first_audio_1      = ! empty( $first_chapter['audio_1'] ) ? $first_chapter['audio_1'] : '';
	$first_audio_2      = ! empty( $first_chapter['audio_2'] ) ? $first_chapter['audio_2'] : '';

	$current_lang = wt_shop_get_current_language_code();
	if ( $current_lang === '' ) {
		$current_lang = 'en';
	}
	$current_label = wt_shop_get_current_language_label( __( 'English', 'webthinkershop' ) );
	$english_label = wt_shop_get_language_label( 'en', __( 'English', 'webthinkershop' ) );

	$first_subtitle_1_lang  = ! empty( $first_chapter['subtitle_1_lang'] ) ? sanitize_text_field( $first_chapter['subtitle_1_lang'] ) : $current_lang;
	$first_subtitle_2_lang  = ! empty( $first_chapter['subtitle_2_lang'] ) ? sanitize_text_field( $first_chapter['subtitle_2_lang'] ) : 'en';
	$first_audio_1_lang     = ! empty( $first_chapter['audio_1_lang'] ) ? sanitize_text_field( $first_chapter['audio_1_lang'] ) : $first_subtitle_1_lang;
	$first_audio_2_lang     = ! empty( $first_chapter['audio_2_lang'] ) ? sanitize_text_field( $first_chapter['audio_2_lang'] ) : $first_subtitle_2_lang;
	$first_subtitle_1_label = ! empty( $first_chapter['subtitle_1_label'] ) ? sanitize_text_field( $first_chapter['subtitle_1_label'] ) : wt_shop_get_language_label( $first_subtitle_1_lang, $current_label );
	$first_subtitle_2_label = ! empty( $first_chapter['subtitle_2_label'] ) ? sanitize_text_field( $first_chapter['subtitle_2_label'] ) : wt_shop_get_language_label( $first_subtitle_2_lang, $english_label );
	$first_audio_1_label    = ! empty( $first_chapter['audio_1_label'] ) ? sanitize_text_field( $first_chapter['audio_1_label'] ) : wt_shop_get_language_label( $first_audio_1_lang, $current_label );
	$first_audio_2_label    = ! empty( $first_chapter['audio_2_label'] ) ? sanitize_text_field( $first_chapter['audio_2_label'] ) : wt_shop_get_language_label( $first_audio_2_lang, $english_label );

	$timeline_entries = array();
	foreach ( $chapters as $ci => $ch ) {
		$ch_title = ! empty( $ch['title'] ) ? $ch['title'] : sprintf( __( 'Kapitel %d', 'webthinkershop' ), $ci + 1 );
		$timeline_entries[] = array( 'label' => $ch_title, 'type' => 'chapter' );
		if ( ! empty( $ch['items'] ) && is_array( $ch['items'] ) ) {
			foreach ( $ch['items'] as $item ) {
				$kind  = ! empty( $item['kind'] ) ? $item['kind'] : 'item';
				$label = ! empty( $item['title'] ) ? $item['title'] : ucfirst( $kind );
				if ( ! empty( $item['minute'] ) ) {
					$label .= ' (' . esc_html( $item['minute'] ) . ')';
				}
				$timeline_entries[] = array( 'label' => $label, 'type' => 'item' );
			}
		}
	}

	$audio_tracks = array();
	if ( ! empty( $first_audio_1 ) ) {
		$audio_tracks[] = array( 'url' => esc_url( $first_audio_1 ), 'label' => $first_audio_1_label );
	}
	if ( ! empty( $first_audio_2 ) ) {
		$audio_tracks[] = array( 'url' => esc_url( $first_audio_2 ), 'label' => $first_audio_2_label );
	}
	$audio_tracks_attr = ! empty( $audio_tracks ) ? " data-audio-tracks='" . wp_json_encode( $audio_tracks ) . "'" : '';
	$has_subtitles     = ! empty( $first_subtitle_1 ) || ! empty( $first_subtitle_2 );
	$crossorigin_attr  = $has_subtitles ? ' crossorigin="anonymous"' : '';
	$poster_attr       = ! empty( $first_video_poster ) ? ' poster="' . esc_url( $first_video_poster ) . '"' : '';
?>

<div class="pb-front" id="pb-front" data-chapters="<?php echo esc_attr( wp_json_encode( $chapters ) ); ?>">
	<div class="container pb-front-container">

		<!-- Hero -->
		<div class="pb-front-hero">
			<?php if ( ! empty( $program_image ) ) : ?>
				<div class="pb-front-hero-image">
					<img src="<?php echo esc_url( $program_image ); ?>" alt="<?php echo esc_attr( $program_title ); ?>">
				</div>
			<?php endif; ?>
			<div class="pb-front-hero-body">
				<?php if ( ! empty( $program_type ) ) : ?>
					<span class="pb-front-badge"><?php echo esc_html( $program_type ); ?></span>
				<?php endif; ?>
				<h1 class="pb-front-title"><?php echo esc_html( $program_title ); ?></h1>
				<?php if ( ! empty( $short_description ) ) : ?>
					<p class="pb-front-short"><?php echo esc_html( $short_description ); ?></p>
				<?php endif; ?>
				<?php if ( ! empty( $topic_area ) ) : ?>
					<span class="pb-front-topic"><i class="bi bi-bookmark-fill"></i> <?php echo esc_html( $topic_area ); ?></span>
				<?php endif; ?>
			</div>
		</div>

		<!-- Main: video + chapters -->
		<div class="pb-front-main">

			<!-- Video column -->
			<div class="pb-front-video-col">
				<div class="pb-front-player-wrap" id="pb-front-player-wrap">
				<?php if ( ! empty( $first_video_mp4 ) ) : ?>
					<div class="video-player video-player-skeleton" data-player<?php echo $audio_tracks_attr; ?>>
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
						<video preload="metadata" playsinline webkit-playsinline<?php echo $poster_attr . $crossorigin_attr; ?>>
							<source src="<?php echo esc_url( $first_video_mp4 ); ?>" type="video/mp4">
								<?php if ( ! empty( $first_subtitle_1 ) ) : ?>
									<track kind="subtitles" src="<?php echo esc_url( $first_subtitle_1 ); ?>" srclang="<?php echo esc_attr( $first_subtitle_1_lang ); ?>" label="<?php echo esc_attr( $first_subtitle_1_label ); ?>">
								<?php endif; ?>
								<?php if ( ! empty( $first_subtitle_2 ) ) : ?>
									<track kind="subtitles" src="<?php echo esc_url( $first_subtitle_2 ); ?>" srclang="<?php echo esc_attr( $first_subtitle_2_lang ); ?>" label="<?php echo esc_attr( $first_subtitle_2_label ); ?>">
								<?php endif; ?>
							</video>
							<div class="video-player-play-overlay" data-play-overlay aria-label="<?php esc_attr_e( 'Play Video', 'webthinkershop' ); ?>">
								<i class="bi bi-play-fill" aria-hidden="true"></i>
							</div>
							<div class="video-player-captions" data-captions aria-live="polite" aria-atomic="true"></div>
							<div class="video-player-controls" data-controls>
								<button type="button" class="video-player-btn" data-play aria-label="<?php esc_attr_e( 'Play', 'webthinkershop' ); ?>">
									<i class="bi bi-play-fill" aria-hidden="true"></i>
									<i class="bi bi-pause-fill" aria-hidden="true"></i>
								</button>
								<div class="video-player-progress-wrap" data-progress-wrap>
									<input type="range" class="video-player-progress" data-progress min="0" max="100" value="0" step="0.1" aria-label="<?php esc_attr_e( 'Seek', 'webthinkershop' ); ?>">
								</div>
								<span class="video-player-time" data-time aria-live="off">0:00</span>
								<button type="button" class="video-player-btn" data-mute aria-label="<?php esc_attr_e( 'Mute', 'webthinkershop' ); ?>">
									<i class="bi bi-volume-up-fill" aria-hidden="true"></i>
									<i class="bi bi-volume-mute-fill" aria-hidden="true"></i>
								</button>
								<input type="range" class="video-player-volume" data-volume min="0" max="100" value="100" step="1" aria-label="<?php esc_attr_e( 'Volume', 'webthinkershop' ); ?>">
								<button type="button" class="video-player-btn" data-cc aria-label="<?php esc_attr_e( 'Captions', 'webthinkershop' ); ?>">
									<span>CC</span>
								</button>
								<button type="button" class="video-player-btn" data-fullscreen aria-label="<?php esc_attr_e( 'Fullscreen', 'webthinkershop' ); ?>">
									<i class="bi bi-fullscreen" aria-hidden="true"></i>
									<i class="bi bi-fullscreen-exit" aria-hidden="true"></i>
								</button>
							</div>
							<div class="video-player-loading" data-loading aria-hidden="true" role="status"></div>
							<div class="video-player-error" data-error aria-live="assertive" hidden></div>
						</div>
					<?php elseif ( ! empty( $first_video_audio ) ) : ?>
						<audio id="pb-front-audio" controls preload="metadata">
							<source src="<?php echo esc_url( $first_video_audio ); ?>">
						</audio>
					<?php elseif ( ! empty( $program_image ) ) : ?>
						<img src="<?php echo esc_url( $program_image ); ?>" alt="<?php echo esc_attr( $program_title ); ?>" class="pb-front-poster-fallback">
					<?php endif; ?>
				</div>

				<!-- Now playing label -->
				<div class="pb-front-now-playing" id="pb-front-now-playing">
					<span class="pb-front-np-label"><?php esc_html_e( 'Aktuell:', 'webthinkershop' ); ?></span>
					<strong class="pb-front-np-title" id="pb-front-np-title"><?php echo esc_html( ! empty( $first_video_title ) ? $first_video_title : ( ! empty( $first_chapter['title'] ) ? $first_chapter['title'] : '' ) ); ?></strong>
				</div>

				<!-- Event toast -->
				<div class="pb-front-event-toast" id="pb-front-event-toast" hidden></div>
			</div>

			<!-- Chapters sidebar -->
			<div class="pb-front-chapters-col">
				<h2 class="pb-front-chapters-heading"><?php esc_html_e( 'Programmstruktur', 'webthinkershop' ); ?></h2>
				<div class="pb-front-chapters-list" id="pb-front-chapters-list">
					<?php foreach ( $chapters as $ci => $ch ) :
						$ch_title = ! empty( $ch['title'] ) ? $ch['title'] : sprintf( __( 'Kapitel %d', 'webthinkershop' ), $ci + 1 );
						$ch_short = ! empty( $ch['short_description'] ) ? $ch['short_description'] : '';
						$ch_items = ! empty( $ch['items'] ) && is_array( $ch['items'] ) ? $ch['items'] : array();
					?>
						<div class="pb-front-chapter<?php echo $ci === 0 ? ' is-active' : ''; ?>" data-chapter="<?php echo esc_attr( $ci ); ?>">
							<div class="pb-front-chapter-header">
								<span class="pb-front-chapter-num"><?php echo esc_html( sprintf( '%02d', $ci + 1 ) ); ?></span>
								<div class="pb-front-chapter-info">
									<strong class="pb-front-chapter-title"><?php echo esc_html( $ch_title ); ?></strong>
									<?php if ( ! empty( $ch_short ) ) : ?>
										<span class="pb-front-chapter-short"><?php echo esc_html( $ch_short ); ?></span>
									<?php endif; ?>
								</div>
								<span class="pb-front-chapter-toggle"><i class="bi bi-chevron-down"></i></span>
							</div>
							<?php if ( ! empty( $ch_items ) ) : ?>
								<div class="pb-front-items">
									<?php foreach ( $ch_items as $ii => $item ) :
										$kind   = ! empty( $item['kind'] ) ? $item['kind'] : 'item';
										$ititle = ! empty( $item['title'] ) ? $item['title'] : ucfirst( $kind );
										$minute = ! empty( $item['minute'] ) ? $item['minute'] : '';
									?>
										<div class="pb-front-item" data-chapter="<?php echo esc_attr( $ci ); ?>" data-kind="<?php echo esc_attr( $kind ); ?>" data-minute="<?php echo esc_attr( $minute ); ?>">
											<span class="pb-front-item-icon pb-front-kind-<?php echo esc_attr( $kind ); ?>">
												<?php
												$icons = array(
													'quiz'      => 'bi-question-circle-fill',
													'game'      => 'bi-controller',
													'info'      => 'bi-info-circle-fill',
													'bewertung' => 'bi-star-fill',
													'item'      => 'bi-circle-fill',
												);
												$icon = isset( $icons[ $kind ] ) ? $icons[ $kind ] : 'bi-circle-fill';
												?>
												<i class="bi <?php echo esc_attr( $icon ); ?>"></i>
											</span>
											<span class="pb-front-item-title"><?php echo esc_html( $ititle ); ?></span>
											<?php if ( ! empty( $minute ) ) : ?>
												<span class="pb-front-item-minute"><?php echo esc_html( $minute ); ?></span>
											<?php endif; ?>
											<span class="pb-front-item-kind-badge"><?php echo esc_html( ucfirst( $kind ) ); ?></span>
										</div>
									<?php endforeach; ?>
								</div>
							<?php endif; ?>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>

		<!-- Timeline -->
		<?php if ( ! empty( $timeline_entries ) ) : ?>
		<div class="pb-front-timeline-section">
			<h2 class="pb-front-section-title"><?php esc_html_e( 'Zeitstrahl', 'webthinkershop' ); ?></h2>
			<div class="pb-front-timeline-scroll">
				<div class="pb-front-timeline" style="min-width:<?php echo max( count( $timeline_entries ) * 140, 400 ); ?>px">
					<div class="pb-front-timeline-track"></div>
					<div class="pb-front-timeline-points">
						<?php foreach ( $timeline_entries as $ti => $entry ) :
							$left = count( $timeline_entries ) === 1 ? 0 : ( $ti / ( count( $timeline_entries ) - 1 ) ) * 100;
							$cls  = 'pb-front-tl-point pb-front-tl-' . $entry['type'];
						?>
							<div class="<?php echo esc_attr( $cls ); ?>" style="left:<?php echo esc_attr( $left ); ?>%">
								<span class="pb-front-tl-connector"></span>
								<span class="pb-front-tl-dot"></span>
								<span class="pb-front-tl-label"><?php echo esc_html( $entry['label'] ); ?></span>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		</div>
		<?php endif; ?>

		<!-- Description -->
		<?php if ( ! empty( $general_description ) ) : ?>
		<div class="pb-front-description-section">
			<h2 class="pb-front-section-title"><?php esc_html_e( 'Beschreibung', 'webthinkershop' ); ?></h2>
			<div class="pb-front-description-body">
				<?php echo wp_kses_post( $general_description ); ?>
			</div>
		</div>
		<?php endif; ?>

		<!-- Glossary (when plugin active and option set) -->
		<?php
		if ( ! empty( $glossary_display ) && function_exists( 'wt_glossary_render_output' ) ) {
			$glossary_settings = function_exists( 'wt_glossary_get_settings' ) ? wt_glossary_get_settings() : array();
			$glossary_atts = array(
				'title'        => $glossary_settings['archive_title'] ?? __( 'Glossary', 'wt-glossary' ),
				'description'  => $glossary_settings['archive_description'] ?? '',
				'search_label' => $glossary_settings['archive_search_label'] ?? __( 'What word are you interested in?', 'wt-glossary' ),
				'show_search'  => 'yes',
				'show_nav'     => 'yes',
				'columns'      => $glossary_settings['archive_columns'] ?? '2',
				'category'     => ( $glossary_display === 'all' ) ? '' : $glossary_display,
			);
			echo '<div class="pb-front-glossary-section">';
			echo wt_glossary_render_output( $glossary_atts );
			echo '</div>';
		}
		?>

	</div>
</div>

<?php
endwhile;

get_footer();
