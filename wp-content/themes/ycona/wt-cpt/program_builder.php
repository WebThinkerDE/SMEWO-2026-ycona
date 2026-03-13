<?php
/* Custom Post Type - Program Builder */

function show_program_builder_custom_fields() {
	global $post;

	$meta               = get_post_meta( $post->ID, 'program_builder_fields', true );
	$meta               = is_array( $meta ) ? $meta : array();
	$program_title      = isset( $meta['program_title'] ) ? $meta['program_title'] : '';
	$short_description  = isset( $meta['short_description'] ) ? $meta['short_description'] : '';
	$topic_area         = isset( $meta['topic_area'] ) ? $meta['topic_area'] : '';
	$program_image      = isset( $meta['program_image'] ) ? $meta['program_image'] : '';
	$program_image_id   = isset( $meta['program_image_id'] ) ? $meta['program_image_id'] : '';
	$program_type       = isset( $meta['program_type'] ) ? $meta['program_type'] : 'Schulungen';
	$general_description = isset( $meta['general_description'] ) ? $meta['general_description'] : '';
	$glossary_display    = isset( $meta['glossary_display'] ) ? $meta['glossary_display'] : '';
	$chapters           = isset( $meta['chapters'] ) && is_array( $meta['chapters'] ) ? $meta['chapters'] : array();
	$initial_chapter    = ! empty( $chapters ) ? reset( $chapters ) : array();
	$initial_video_mp4  = isset( $initial_chapter['video_mp4'] ) ? $initial_chapter['video_mp4'] : '';
	$initial_video_poster = isset( $initial_chapter['video_poster'] ) ? $initial_chapter['video_poster'] : '';
	$initial_video_audio = isset( $initial_chapter['video_audio'] ) ? $initial_chapter['video_audio'] : '';
	$initial_video_title = isset( $initial_chapter['video_title'] ) ? $initial_chapter['video_title'] : '';
	$initial_chapter_title = isset( $initial_chapter['title'] ) ? $initial_chapter['title'] : '';
	$initial_chapter_short = isset( $initial_chapter['short_description'] ) ? $initial_chapter['short_description'] : '';
	$program_type_terms = get_terms(
		array(
			'taxonomy'   => 'program_builder_type',
			'hide_empty' => false,
		)
	);
	?>

	<input type="hidden" name="programBuilderMetaNonce" value="<?php echo esc_attr( wp_create_nonce( 'saveProgramBuilderFields' ) ); ?>">

	<div id="program-builder-editor" class="pb-editor" data-post-id="<?php echo esc_attr( $post->ID ); ?>">
		<div class="pb-editor-top">
			<div class="pb-editor-left">
				<h2 class="pb-page-title"><?php esc_html_e( 'Schulung anlegen', 'webthinkershop' ); ?></h2>

				<div class="pb-form-row">
					<label for="pb-program-title"><?php esc_html_e( 'Titel', 'webthinkershop' ); ?></label>
					<input id="pb-program-title" type="text" class="regular-text" name="program_builder_fields[program_title]" value="<?php echo esc_attr( $program_title ); ?>" placeholder="<?php esc_attr_e( 'Grundlagenschulung G1', 'webthinkershop' ); ?>">
				</div>

				<div class="pb-form-row">
					<label for="pb-short-description"><?php esc_html_e( 'Kurzbeschreibung', 'webthinkershop' ); ?></label>
					<textarea id="pb-short-description" rows="3" name="program_builder_fields[short_description]" placeholder="<?php esc_attr_e( 'Kurze Zusammenfassung...', 'webthinkershop' ); ?>"><?php echo esc_textarea( $short_description ); ?></textarea>
				</div>

				<div class="pb-form-row pb-form-row-inline">
					<div>
						<label for="pb-topic-area"><?php esc_html_e( 'Themenbereich auswählen', 'webthinkershop' ); ?></label>
						<input id="pb-topic-area" type="text" class="regular-text" name="program_builder_fields[topic_area]" value="<?php echo esc_attr( $topic_area ); ?>" placeholder="<?php esc_attr_e( 'Asphaltbau', 'webthinkershop' ); ?>">
					</div>
					<div>
						<label for="pb-program-type"><?php esc_html_e( 'Kategorie', 'webthinkershop' ); ?></label>
						<select id="pb-program-type" name="program_builder_fields[program_type]">
							<?php
							if ( ! is_wp_error( $program_type_terms ) && ! empty( $program_type_terms ) ) :
								foreach ( $program_type_terms as $term ) :
									?>
									<option value="<?php echo esc_attr( $term->name ); ?>" <?php selected( $program_type, $term->name ); ?>>
										<?php echo esc_html( $term->name ); ?>
									</option>
									<?php
								endforeach;
							else :
								?>
								<option value="Schulungen" <?php selected( $program_type, 'Schulungen' ); ?>><?php esc_html_e( 'Schulungen', 'webthinkershop' ); ?></option>
								<option value="Bewertung" <?php selected( $program_type, 'Bewertung' ); ?>><?php esc_html_e( 'Bewertung', 'webthinkershop' ); ?></option>
								<?php
							endif;
							?>
						</select>
					</div>
				</div>
			</div>

			<div class="pb-editor-right">
				<label><?php esc_html_e( 'Schulungsbild', 'webthinkershop' ); ?></label>
				<div class="pb-image-box" id="box-wrapper-program-image">
					<input type="hidden" class="meta-image-id" name="program_builder_fields[program_image_id]" value="<?php echo esc_attr( $program_image_id ); ?>">
					<input type="hidden" class="meta-image" name="program_builder_fields[program_image]" value="<?php echo esc_attr( $program_image ); ?>">
					<div class="image-preview">
						<img src="<?php echo esc_url( $program_image ); ?>" alt="">
					</div>
					<div class="pb-image-actions">
						<input type="button" data-id="program-image" class="button image-upload" value="<?php esc_attr_e( 'Datei auswählen', 'webthinkershop' ); ?>">
						<input type="button" data-id="program-image" class="button image-upload-remove" value="<?php esc_attr_e( 'Entfernen', 'webthinkershop' ); ?>">
					</div>
				</div>
			</div>
		</div>

		<div class="pb-builder-main">
			<div class="pb-preview-column">
				<h3><?php esc_html_e( 'Vorschau', 'webthinkershop' ); ?></h3>
				<div class="pb-preview-card">
					<div class="pb-preview-image pb-preview-video" id="pb-preview-video-wrap">
					<?php if ( ! empty( $initial_video_mp4 ) ) : ?>
						<video id="pb-preview-video" controls preload="metadata" <?php echo ! empty( $initial_video_poster ) ? 'poster="' . esc_url( $initial_video_poster ) . '"' : ''; ?>>
							<source src="<?php echo esc_url( $initial_video_mp4 ); ?>" type="video/mp4">
						</video>
						<?php elseif ( ! empty( $initial_video_audio ) ) : ?>
							<audio id="pb-preview-audio" controls preload="metadata">
								<source src="<?php echo esc_url( $initial_video_audio ); ?>">
							</audio>
						<?php else : ?>
							<div class="pb-preview-empty"><?php esc_html_e( 'Kein Kapitel-Video ausgewählt', 'webthinkershop' ); ?></div>
						<?php endif; ?>
					</div>
					<h4 class="pb-preview-title" id="pb-preview-title"><?php echo esc_html( $initial_video_title !== '' ? $initial_video_title : $initial_chapter_title ); ?></h4>
					<p class="pb-preview-description" id="pb-preview-description"><?php echo esc_html( $initial_chapter_short ); ?></p>
					<div class="pb-preview-event-toast" id="pb-preview-event-toast" hidden></div>
				</div>
			</div>

			<div class="pb-structure-column">
				<div class="pb-structure-header">
					<h3><?php esc_html_e( 'Programmstruktur', 'webthinkershop' ); ?></h3>
					<button type="button" class="button button-primary pb-add-chapter"><?php esc_html_e( 'Kapitel hinzufügen', 'webthinkershop' ); ?></button>
				</div>

				<div class="pb-chapters-list" id="pb-chapters-list">
					<?php
					foreach ( $chapters as $chapter_index => $chapter ) :
						$chapter_title       = isset( $chapter['title'] ) ? $chapter['title'] : '';
						$chapter_short       = isset( $chapter['short_description'] ) ? $chapter['short_description'] : '';
						$chapter_description = isset( $chapter['description'] ) ? $chapter['description'] : '';
						$chapter_date        = isset( $chapter['date'] ) ? $chapter['date'] : '';
						$chapter_video_title = isset( $chapter['video_title'] ) ? $chapter['video_title'] : '';
					$chapter_video_mp4   = isset( $chapter['video_mp4'] ) ? $chapter['video_mp4'] : '';
						$chapter_video_audio = isset( $chapter['video_audio'] ) ? $chapter['video_audio'] : '';
						$chapter_video_poster = isset( $chapter['video_poster'] ) ? $chapter['video_poster'] : '';
						$chapter_subtitle_1  = isset( $chapter['subtitle_1'] ) ? $chapter['subtitle_1'] : '';
						$chapter_subtitle_2  = isset( $chapter['subtitle_2'] ) ? $chapter['subtitle_2'] : '';
						$chapter_audio_1     = isset( $chapter['audio_1'] ) ? $chapter['audio_1'] : '';
						$chapter_audio_2     = isset( $chapter['audio_2'] ) ? $chapter['audio_2'] : '';
						$chapter_items       = isset( $chapter['items'] ) && is_array( $chapter['items'] ) ? $chapter['items'] : array();
						?>
						<div class="pb-chapter-row<?php echo 0 === (int) $chapter_index ? ' is-active' : ''; ?>" draggable="true" data-chapter-index="<?php echo esc_attr( $chapter_index ); ?>">
							<div class="pb-row-header">
								<span class="pb-drag-handle dashicons dashicons-move"></span>
								<span class="pb-position-label"><?php echo esc_html( sprintf( '%02d', $chapter_index + 1 ) ); ?></span>
								<strong class="pb-row-title"><?php echo esc_html( $chapter_title !== '' ? $chapter_title : __( 'Kapitel', 'webthinkershop' ) ); ?></strong>
								<div class="pb-row-actions">
									<select class="pb-new-item-kind">
										<option value="item"><?php esc_html_e( 'Item', 'webthinkershop' ); ?></option>
										<option value="quiz"><?php esc_html_e( 'Quiz', 'webthinkershop' ); ?></option>
										<option value="game"><?php esc_html_e( 'Game', 'webthinkershop' ); ?></option>
										<option value="info"><?php esc_html_e( 'Info', 'webthinkershop' ); ?></option>
										<option value="bewertung"><?php esc_html_e( 'Bewertung', 'webthinkershop' ); ?></option>
									</select>
									<button type="button" class="button button-small pb-add-item"><?php esc_html_e( '+ Hinzufügen', 'webthinkershop' ); ?></button>
									<button type="button" class="button button-small pb-edit-btn" data-type="chapter"><span class="dashicons dashicons-edit"></span></button>
									<button type="button" class="button button-small pb-delete-btn" data-type="chapter"><span class="dashicons dashicons-trash"></span></button>
								</div>
							</div>

							<input type="hidden" class="pb-field-chapter-title" name="program_builder_fields[chapters][<?php echo esc_attr( $chapter_index ); ?>][title]" value="<?php echo esc_attr( $chapter_title ); ?>">
							<input type="hidden" class="pb-field-chapter-short" name="program_builder_fields[chapters][<?php echo esc_attr( $chapter_index ); ?>][short_description]" value="<?php echo esc_attr( $chapter_short ); ?>">
							<input type="hidden" class="pb-field-chapter-description" name="program_builder_fields[chapters][<?php echo esc_attr( $chapter_index ); ?>][description]" value="<?php echo esc_attr( $chapter_description ); ?>">
							<input type="hidden" class="pb-field-chapter-date" name="program_builder_fields[chapters][<?php echo esc_attr( $chapter_index ); ?>][date]" value="<?php echo esc_attr( $chapter_date ); ?>">
							<input type="hidden" class="pb-field-chapter-video-title" name="program_builder_fields[chapters][<?php echo esc_attr( $chapter_index ); ?>][video_title]" value="<?php echo esc_attr( $chapter_video_title ); ?>">
						<input type="hidden" class="pb-field-chapter-video-mp4" name="program_builder_fields[chapters][<?php echo esc_attr( $chapter_index ); ?>][video_mp4]" value="<?php echo esc_attr( $chapter_video_mp4 ); ?>">
							<input type="hidden" class="pb-field-chapter-video-audio" name="program_builder_fields[chapters][<?php echo esc_attr( $chapter_index ); ?>][video_audio]" value="<?php echo esc_attr( $chapter_video_audio ); ?>">
							<input type="hidden" class="pb-field-chapter-video-poster" name="program_builder_fields[chapters][<?php echo esc_attr( $chapter_index ); ?>][video_poster]" value="<?php echo esc_attr( $chapter_video_poster ); ?>">
							<input type="hidden" class="pb-field-chapter-subtitle-1" name="program_builder_fields[chapters][<?php echo esc_attr( $chapter_index ); ?>][subtitle_1]" value="<?php echo esc_attr( $chapter_subtitle_1 ); ?>">
							<input type="hidden" class="pb-field-chapter-subtitle-2" name="program_builder_fields[chapters][<?php echo esc_attr( $chapter_index ); ?>][subtitle_2]" value="<?php echo esc_attr( $chapter_subtitle_2 ); ?>">
							<input type="hidden" class="pb-field-chapter-audio-1" name="program_builder_fields[chapters][<?php echo esc_attr( $chapter_index ); ?>][audio_1]" value="<?php echo esc_attr( $chapter_audio_1 ); ?>">
							<input type="hidden" class="pb-field-chapter-audio-2" name="program_builder_fields[chapters][<?php echo esc_attr( $chapter_index ); ?>][audio_2]" value="<?php echo esc_attr( $chapter_audio_2 ); ?>">

							<div class="pb-items-list">
								<?php foreach ( $chapter_items as $item_index => $item ) : ?>
									<?php
									$item_title       = isset( $item['title'] ) ? $item['title'] : '';
									$item_short       = isset( $item['short_description'] ) ? $item['short_description'] : '';
									$item_description = isset( $item['description'] ) ? $item['description'] : '';
									$item_date        = isset( $item['date'] ) ? $item['date'] : '';
									$item_kind        = isset( $item['kind'] ) ? $item['kind'] : 'item';
									$item_minute      = isset( $item['minute'] ) ? $item['minute'] : '';
									?>
									<div class="pb-item-row" draggable="true" data-item-index="<?php echo esc_attr( $item_index ); ?>">
										<span class="pb-drag-handle dashicons dashicons-menu"></span>
										<span class="pb-position-label"><?php echo esc_html( sprintf( '%02d|%02d', $chapter_index + 1, $item_index + 1 ) ); ?></span>
										<span class="pb-row-title"><?php echo esc_html( $item_title !== '' ? $item_title : __( 'Item', 'webthinkershop' ) ); ?></span>
										<span class="pb-item-kind"><?php echo esc_html( ucfirst( $item_kind ) ); ?></span>
										<span class="pb-item-minute"><?php echo esc_html( $item_minute ); ?></span>
										<div class="pb-row-actions">
											<button type="button" class="button button-small pb-edit-btn" data-type="item"><span class="dashicons dashicons-edit"></span></button>
											<button type="button" class="button button-small pb-delete-btn" data-type="item"><span class="dashicons dashicons-trash"></span></button>
										</div>

										<input type="hidden" class="pb-field-item-title" name="program_builder_fields[chapters][<?php echo esc_attr( $chapter_index ); ?>][items][<?php echo esc_attr( $item_index ); ?>][title]" value="<?php echo esc_attr( $item_title ); ?>">
										<input type="hidden" class="pb-field-item-short" name="program_builder_fields[chapters][<?php echo esc_attr( $chapter_index ); ?>][items][<?php echo esc_attr( $item_index ); ?>][short_description]" value="<?php echo esc_attr( $item_short ); ?>">
										<input type="hidden" class="pb-field-item-description" name="program_builder_fields[chapters][<?php echo esc_attr( $chapter_index ); ?>][items][<?php echo esc_attr( $item_index ); ?>][description]" value="<?php echo esc_attr( $item_description ); ?>">
										<input type="hidden" class="pb-field-item-date" name="program_builder_fields[chapters][<?php echo esc_attr( $chapter_index ); ?>][items][<?php echo esc_attr( $item_index ); ?>][date]" value="<?php echo esc_attr( $item_date ); ?>">
										<input type="hidden" class="pb-field-item-kind" name="program_builder_fields[chapters][<?php echo esc_attr( $chapter_index ); ?>][items][<?php echo esc_attr( $item_index ); ?>][kind]" value="<?php echo esc_attr( $item_kind ); ?>">
										<input type="hidden" class="pb-field-item-minute" name="program_builder_fields[chapters][<?php echo esc_attr( $chapter_index ); ?>][items][<?php echo esc_attr( $item_index ); ?>][minute]" value="<?php echo esc_attr( $item_minute ); ?>">
									</div>
								<?php endforeach; ?>
							</div>
						</div>
						<?php
					endforeach;
					?>
				</div>
			</div>
		</div>

		<div class="pb-general-section">
			<div class="pb-modal-timeline-wrap">
				<label><?php esc_html_e( 'Zeitstrahl', 'webthinkershop' ); ?></label>
				<div class="pb-modal-timeline" id="pb-general-timeline">
					<div class="pb-modal-timeline-track"></div>
					<div class="pb-modal-timeline-points" id="pb-general-timeline-points"></div>
				</div>
			</div>
			<div class="pb-modal-editor-wrap pb-general-description-wrap">
				<label for="pb_general_description"><?php esc_html_e( 'Beschreibung', 'webthinkershop' ); ?></label>
				<?php
				wp_editor(
					$general_description,
					'pb_general_description',
					array(
						'textarea_name' => 'program_builder_fields[general_description]',
						'textarea_rows' => 12,
						'media_buttons' => false,
						'teeny'         => false,
					)
				);
				?>
			</div>
			<?php if ( function_exists( 'wt_glossary_render_output' ) && post_type_exists( 'wt_glossary_term' ) ) : ?>
			<div class="pb-glossary-select-wrap" style="margin-top:1.5rem;">
				<label for="pb_glossary_display"><?php esc_html_e( 'Glossar anzeigen', 'webthinkershop' ); ?></label>
				<select id="pb_glossary_display" name="program_builder_fields[glossary_display]" class="widefat">
					<option value="" <?php selected( $glossary_display, '' ); ?>><?php esc_html_e( '— Keins —', 'webthinkershop' ); ?></option>
					<option value="all" <?php selected( $glossary_display, 'all' ); ?>><?php esc_html_e( 'Vollständiges Glossar', 'webthinkershop' ); ?></option>
					<?php
					$glossary_cats = get_terms( array(
						'taxonomy'   => 'wt_glossary_category',
						'hide_empty' => false,
					) );
					if ( ! is_wp_error( $glossary_cats ) && ! empty( $glossary_cats ) ) :
						foreach ( $glossary_cats as $term ) :
							?>
							<option value="<?php echo esc_attr( $term->slug ); ?>" <?php selected( $glossary_display, $term->slug ); ?>><?php echo esc_html( $term->name ); ?></option>
							<?php
						endforeach;
					endif;
					?>
				</select>
				<p class="description"><?php esc_html_e( 'Wählen Sie, welches Glossar unter der Schulung angezeigt werden soll.', 'webthinkershop' ); ?></p>
			</div>
			<?php endif; ?>
		</div>
	</div>

	<div id="pb-edit-modal" class="pb-modal" hidden>
		<div class="pb-modal-backdrop"></div>
		<div class="pb-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="pb-modal-title">
			<div class="pb-modal-header">
				<h3 id="pb-modal-title"><?php esc_html_e( 'Element bearbeiten', 'webthinkershop' ); ?></h3>
				<button type="button" class="button pb-modal-close">&times;</button>
			</div>
			<div class="pb-modal-body">
				<input type="hidden" id="pb-modal-type" value="">
				<input type="hidden" id="pb-modal-chapter-index" value="">
				<input type="hidden" id="pb-modal-item-index" value="">

				<p>
					<label for="pb-modal-field-title"><?php esc_html_e( 'Titel', 'webthinkershop' ); ?></label>
					<input type="text" id="pb-modal-field-title" class="widefat">
				</p>
				<p>
					<label for="pb-modal-field-short"><?php esc_html_e( 'Kurzbeschreibung', 'webthinkershop' ); ?></label>
					<textarea id="pb-modal-field-short" rows="3" class="widefat"></textarea>
				</p>
				<p>
					<label for="pb-modal-field-date"><?php esc_html_e( 'Datum', 'webthinkershop' ); ?></label>
					<input type="date" id="pb-modal-field-date" class="widefat">
				</p>
			<div id="pb-modal-chapter-video-wrap" class="pb-modal-media-wrap">

				<!-- Video Card -->
				<div class="pb-media-card">
					<div class="pb-media-card-header">
						<span class="dashicons dashicons-video-alt3"></span>
						<h4><?php esc_html_e( 'Video', 'webthinkershop' ); ?></h4>
					</div>
					<div class="pb-media-card-body">
						<div class="pb-media-field">
							<label for="pb-modal-video-title"><?php esc_html_e( 'Video Name', 'webthinkershop' ); ?></label>
							<input type="text" id="pb-modal-video-title" class="widefat" placeholder="<?php esc_attr_e( 'Kapitel 01 Video', 'webthinkershop' ); ?>">
						</div>
					<div class="pb-media-field">
						<label for="pb-modal-video-mp4"><?php esc_html_e( 'Video Datei', 'webthinkershop' ); ?></label>
						<div class="pb-media-input-row">
							<input type="url" id="pb-modal-video-mp4" class="widefat" placeholder="https://.../video.mp4">
							<button type="button" class="button pb-media-upload-btn" data-target="pb-modal-video-mp4" data-media-type="video" title="<?php esc_attr_e( 'Upload', 'webthinkershop' ); ?>"><span class="dashicons dashicons-upload"></span></button>
						</div>
					</div>
						<div class="pb-media-field">
							<label for="pb-modal-video-poster"><?php esc_html_e( 'Poster Bild', 'webthinkershop' ); ?></label>
							<div class="pb-media-input-row">
								<input type="url" id="pb-modal-video-poster" class="widefat" placeholder="https://.../poster.jpg">
								<button type="button" class="button pb-media-upload-btn" data-target="pb-modal-video-poster" data-media-type="image" title="<?php esc_attr_e( 'Upload', 'webthinkershop' ); ?>"><span class="dashicons dashicons-upload"></span></button>
							</div>
						</div>
					</div>
				</div>

				<!-- Audio Card -->
				<div class="pb-media-card">
					<div class="pb-media-card-header">
						<span class="dashicons dashicons-format-audio"></span>
						<h4><?php esc_html_e( 'Audio', 'webthinkershop' ); ?></h4>
					</div>
					<div class="pb-media-card-body">
						<div class="pb-media-field">
							<label for="pb-modal-video-audio"><?php esc_html_e( 'Fallback Audio', 'webthinkershop' ); ?></label>
							<div class="pb-media-input-row">
								<input type="url" id="pb-modal-video-audio" class="widefat" placeholder="https://.../audio.mp3">
								<button type="button" class="button pb-media-upload-btn" data-target="pb-modal-video-audio" data-media-type="audio" title="<?php esc_attr_e( 'Upload', 'webthinkershop' ); ?>"><span class="dashicons dashicons-upload"></span></button>
							</div>
						</div>
						<div class="pb-media-field-row">
							<div class="pb-media-field">
								<label for="pb-modal-audio-1"><?php esc_html_e( 'Audio Track 1', 'webthinkershop' ); ?></label>
								<div class="pb-media-input-row">
									<input type="url" id="pb-modal-audio-1" class="widefat" placeholder="https://.../audio-1.mp3">
									<button type="button" class="button pb-media-upload-btn" data-target="pb-modal-audio-1" data-media-type="audio" title="<?php esc_attr_e( 'Upload', 'webthinkershop' ); ?>"><span class="dashicons dashicons-upload"></span></button>
								</div>
							</div>
							<div class="pb-media-field">
								<label for="pb-modal-audio-2"><?php esc_html_e( 'Audio Track 2', 'webthinkershop' ); ?></label>
								<div class="pb-media-input-row">
									<input type="url" id="pb-modal-audio-2" class="widefat" placeholder="https://.../audio-2.mp3">
									<button type="button" class="button pb-media-upload-btn" data-target="pb-modal-audio-2" data-media-type="audio" title="<?php esc_attr_e( 'Upload', 'webthinkershop' ); ?>"><span class="dashicons dashicons-upload"></span></button>
								</div>
							</div>
						</div>
					</div>
				</div>

				<!-- Subtitles Card -->
				<div class="pb-media-card">
					<div class="pb-media-card-header">
						<span class="dashicons dashicons-editor-quote"></span>
						<h4><?php esc_html_e( 'Untertitel', 'webthinkershop' ); ?></h4>
					</div>
					<div class="pb-media-card-body">
						<div class="pb-media-field-row">
							<div class="pb-media-field">
								<label for="pb-modal-subtitle-1"><?php esc_html_e( 'Subtitle 1 (DE)', 'webthinkershop' ); ?></label>
								<div class="pb-media-input-row">
									<input type="url" id="pb-modal-subtitle-1" class="widefat" placeholder="https://.../subtitle.vtt">
									<button type="button" class="button pb-media-upload-btn" data-target="pb-modal-subtitle-1" title="<?php esc_attr_e( 'Upload', 'webthinkershop' ); ?>"><span class="dashicons dashicons-upload"></span></button>
								</div>
							</div>
							<div class="pb-media-field">
								<label for="pb-modal-subtitle-2"><?php esc_html_e( 'Subtitle 2 (EN)', 'webthinkershop' ); ?></label>
								<div class="pb-media-input-row">
									<input type="url" id="pb-modal-subtitle-2" class="widefat" placeholder="https://.../subtitle.vtt">
									<button type="button" class="button pb-media-upload-btn" data-target="pb-modal-subtitle-2" title="<?php esc_attr_e( 'Upload', 'webthinkershop' ); ?>"><span class="dashicons dashicons-upload"></span></button>
								</div>
							</div>
						</div>
					</div>
				</div>

			</div>
				<p id="pb-modal-kind-wrap">
					<label for="pb-modal-field-kind"><?php esc_html_e( 'Typ', 'webthinkershop' ); ?></label>
					<select id="pb-modal-field-kind" class="widefat">
						<option value="item"><?php esc_html_e( 'Item', 'webthinkershop' ); ?></option>
						<option value="bewertung"><?php esc_html_e( 'Bewertung', 'webthinkershop' ); ?></option>
						<option value="quiz"><?php esc_html_e( 'Quiz', 'webthinkershop' ); ?></option>
						<option value="game"><?php esc_html_e( 'Game', 'webthinkershop' ); ?></option>
						<option value="info"><?php esc_html_e( 'Info', 'webthinkershop' ); ?></option>
					</select>
				</p>
				<p id="pb-modal-minute-wrap">
					<label for="pb-modal-field-minute"><?php esc_html_e( 'Minute (mm:ss)', 'webthinkershop' ); ?></label>
					<input type="text" id="pb-modal-field-minute" class="widefat" placeholder="12:30">
				</p>
			</div>
			<div class="pb-modal-footer">
				<button type="button" class="button pb-modal-cancel"><?php esc_html_e( 'Abbrechen', 'webthinkershop' ); ?></button>
				<button type="button" class="button button-primary pb-modal-save"><?php esc_html_e( 'Speichern', 'webthinkershop' ); ?></button>
			</div>
		</div>
	</div>
	<?php
}
/* END - Custom Post Type - Program Builder */

