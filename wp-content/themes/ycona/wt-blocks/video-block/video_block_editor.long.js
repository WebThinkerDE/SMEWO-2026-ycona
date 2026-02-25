( function ( wp, blocks, element, data ) {

    var el                  = element.createElement,
        Fragment            = wp.element.Fragment,
        registerBlockType   = blocks.registerBlockType,
        MediaUpload         = wp.blockEditor.MediaUpload,
        TextControl         = wp.components.TextControl,
        InspectorControls   = wp.blockEditor.InspectorControls,
        useBlockProps       = wp.blockEditor.useBlockProps;

    registerBlockType( 'wt/video-block', {
        apiVersion: 3,
        title: 'Video-Block',
        icon: 'video-alt',
        category: 'wt-shop-blocks',
        description: 'Video with optional title. Uses custom video player (no native controls).',
        example: {},

        attributes: {
            title: {
                type: 'string',
                default: ''
            },
            video_url: {
                type: 'string',
                default: ''
            },
            video: {
                type: 'object',
                default: null
            },
            poster: {
                type: 'object',
                default: null
            },
            skin: {
                type: 'string',
                default: ''
            },
            width: {
                type: 'string',
                default: ''
            },
            height: {
                type: 'string',
                default: ''
            },
            subtitles: {
                type: 'array',
                default: []
            },
            audio_tracks: {
                type: 'array',
                default: []

            }

        },

        edit: function ( props )
        {
            var video_not_empty = props.attributes.video_url || ( props.attributes.video && props.attributes.video.url );
            var video_url_value = props.attributes.video_url || ( props.attributes.video && props.attributes.video.url ) || '';
            var video_not_present_class = video_not_empty ? '' : 'wt-video-not-present';

            function update_title( event )
            {
                props.setAttributes( { title: event.target.value } );
            }

            function update_video_url( event )
            {
                props.setAttributes( { video_url: event.target.value } );
            }

            function on_select_video( media ) {
                if ( media && media.url ) {
                    props.setAttributes( { video: { url: media.url, id: media.id }, video_url: media.url } );
                }
            }

            function remove_video() {
                props.setAttributes( { video: null, video_url: '' } );
            }

            function on_select_poster( media ) {
                if ( media && media.url ) {
                    props.setAttributes( { poster: { url: media.url, id: media.id } } );
                }
            }

            function remove_poster() {
                props.setAttributes( { poster: null } );
            }

            function update_skin( event ) {
                props.setAttributes( { skin: event.target.value } );
            }

            function update_width( event ) {
                props.setAttributes( { width: event.target.value } );
            }

            function update_height( event ) {
                props.setAttributes( { height: event.target.value } );
            }

            // --- Subtitle handlers ---
            var subtitles_list = props.attributes.subtitles || [];

            function add_subtitle() {
                var new_list = subtitles_list.concat( [{ url: '', label: '', srclang: 'en', kind: 'subtitles' }] );
                props.setAttributes( { subtitles: new_list } );
            }

            function update_subtitle( index, field, value ) {
                var new_list = subtitles_list.map( function ( item, i ) {
                    if ( i !== index ) return item;
                    var updated = {};
                    for ( var key in item ) { updated[ key ] = item[ key ]; }
                    updated[ field ] = value;
                    return updated;
                } );
                props.setAttributes( { subtitles: new_list } );
            }

            function on_select_subtitle( index, media ) {
                if ( media && media.url ) {
                    update_subtitle( index, 'url', media.url );
                }
            }

            function remove_subtitle( index ) {
                var new_list = subtitles_list.filter( function ( _, i ) { return i !== index; } );
                props.setAttributes( { subtitles: new_list } );
            }

            // --- Audio track handlers ---
            var audio_tracks_list = props.attributes.audio_tracks || [];

            function add_audio_track() {
                var new_list = audio_tracks_list.concat( [{ url: '', label: '', lang: '' }] );
                props.setAttributes( { audio_tracks: new_list } );
            }

            function update_audio_track( index, field, value ) {
                var new_list = audio_tracks_list.map( function ( item, i ) {
                    if ( i !== index ) return item;
                    var updated = {};
                    for ( var key in item ) { updated[ key ] = item[ key ]; }
                    updated[ field ] = value;
                    return updated;
                } );
                props.setAttributes( { audio_tracks: new_list } );
            }

            function on_select_audio_track( index, media ) {
                if ( media && media.url ) {
                    update_audio_track( index, 'url', media.url );
                }
            }

            function remove_audio_track( index ) {
                var new_list = audio_tracks_list.filter( function ( _, i ) { return i !== index; } );
                props.setAttributes( { audio_tracks: new_list } );
            }

            // Available skins for the dropdown
            var skin_options = [
                { value: '',        label: 'Default'  },
                { value: 'ocean',   label: 'Ocean'    },
                { value: 'cinema',  label: 'Cinema'   },
                { value: 'minimal', label: 'Minimal'  }
            ];

            var current_skin = props.attributes.skin || '';

            // Build the skin preview classes
            var skin_preview_class = 'video-block-editor-skin-preview';
            if ( current_skin ) {
                skin_preview_class += ' video-block-editor-skin-preview-' + current_skin;
            }

            return (
                el( Fragment, null,
                    el( InspectorControls, { key: 'video-block-inspector' },
                        el( 'div', { class: 'webthiker-block-sidebar-element' },

                        )
                    ),
                    el( 'div', useBlockProps( {
                            key: 'video-block-edit',
                            className: 'webthiker-block video-block-editor ' + video_not_present_class
                        } ),
                        el( 'h3', null, 'Video-Block' ),
                        el( 'dl', null,
                            el( 'dt', null, 'Titel (optional)' ),
                            el( 'dd', null,
                                el( 'input', {
                                    type: 'text',
                                    value: props.attributes.title,
                                    placeholder: 'Titel eingeben …',
                                    onChange: update_title
                                } )
                            ),
                            el( 'dt', null, 'Video-URL' ),
                            el( 'dd', null,
                                el( 'input', {
                                    type: 'text',
                                    value: video_url_value,
                                    placeholder: 'https://example.com/video.mp4',
                                    onChange: update_video_url
                                } )
                            ),
                            el( 'dt', null, 'Video aus Mediathek' ),
                            el( 'dd', null,
                                el( MediaUpload, {
                                    onSelect: on_select_video,
                                    allowedTypes: [ 'video' ],
                                    value: props.attributes.video && props.attributes.video.id ? props.attributes.video.id : undefined,
                                    render: function ( ref ) {
                                        var open = ref.open;
                                        return el( 'button', {
                                            type: 'button',
                                            className: 'btn',
                                            onClick: open
                                        }, 'Video wählen' );
                                    }
                                } ),
                                video_url_value ? el( 'button', {
                                    type: 'button',
                                    className: 'btn-remove-video',
                                    onClick: remove_video
                                }, 'Video entfernen' ) : null
                            ),
                            el( 'dt', null, 'Poster-Bild (optional)' ),
                            el( 'dd', null,
                                el( MediaUpload, {
                                    onSelect: on_select_poster,
                                    allowedTypes: [ 'image' ],
                                    value: props.attributes.poster && props.attributes.poster.id ? props.attributes.poster.id : undefined,
                                    render: function ( ref ) {
                                        var open = ref.open;
                                        return el( 'button', {
                                            type: 'button',
                                            className: 'btn',
                                            onClick: open
                                        }, 'Poster wählen' );
                                    }
                                } ),
                                props.attributes.poster && props.attributes.poster.url ? el( 'button', {
                                    type: 'button',
                                    className: 'btn-remove-video',
                                    onClick: remove_poster
                                }, 'Poster entfernen' ) : null
                            ),
                            el( 'dt', null, 'Player-Skin' ),
                            el( 'dd', { class: 'video-block-editor-skin-field' },
                                el( 'select', {
                                    value: current_skin,
                                    onChange: update_skin,
                                    className: 'video-block-editor-skin-select'
                                },
                                    skin_options.map( function ( opt ) {
                                        return el( 'option', { key: opt.value, value: opt.value }, opt.label );
                                    } )
                                ),
                                el( 'div', { class: skin_preview_class },
                                    el( 'span', { class: 'video-block-editor-skin-dot' } ),
                                    el( 'span', null, current_skin ? current_skin.charAt(0).toUpperCase() + current_skin.slice(1) : 'Default' )
                                )
                            ),
                            el( 'dt', null, 'Breite & Höhe (optional)' ),
                            el( 'dd', { class: 'video-block-editor-dimensions-field' },
                                el( 'label', { class: 'video-block-editor-dim-label' },
                                    'Breite',
                                    el( 'input', {
                                        type: 'text',
                                        value: props.attributes.width || '',
                                        placeholder: 'z.B. 800px, 100%, 50vw',
                                        onChange: update_width,
                                        className: 'video-block-editor-dim-input'
                                    } )
                                ),
                                el( 'label', { class: 'video-block-editor-dim-label' },
                                    'Höhe',
                                    el( 'input', {
                                        type: 'text',
                                        value: props.attributes.height || '',
                                        placeholder: 'z.B. 450px, auto',
                                        onChange: update_height,
                                        className: 'video-block-editor-dim-input'
                                    } )
                                )
                            ),
                            el( 'dt', null, 'Untertitel / Subtitles (optional)' ),
                            el( 'dd', { class: 'video-block-editor-subtitles-field' },
                                subtitles_list.map( function ( sub, idx ) {
                                    return ( function ( i ) {
                                        return el( 'div', { key: 'sub-' + i, class: 'video-block-editor-subtitle-row' },
                                            el( 'div', { class: 'video-block-editor-subtitle-url-row' },
                                                el( 'input', {
                                                    type: 'text',
                                                    value: sub.url || '',
                                                    placeholder: 'https://example.com/subs.vtt',
                                                    className: 'video-block-editor-subtitle-input',
                                                    onChange: function ( e ) { update_subtitle( i, 'url', e.target.value ); }
                                                } ),
                                                el( MediaUpload, {
                                                    onSelect: function ( media ) { on_select_subtitle( i, media ); },
                                                    allowedTypes: [ 'text/vtt', 'text/plain' ],
                                                    render: function ( ref ) {
                                                        return el( 'button', {
                                                            type: 'button',
                                                            className: 'btn video-block-editor-subtitle-upload-btn',
                                                            onClick: ref.open
                                                        }, 'Upload .vtt' );
                                                    }
                                                } )
                                            ),
                                            el( 'div', { class: 'video-block-editor-subtitle-meta-row' },
                                                el( 'input', {
                                                    type: 'text',
                                                    value: sub.label || '',
                                                    placeholder: 'Label (z.B. English)',
                                                    className: 'video-block-editor-subtitle-meta-input',
                                                    onChange: function ( e ) { update_subtitle( i, 'label', e.target.value ); }
                                                } ),
                                                el( 'input', {
                                                    type: 'text',
                                                    value: sub.srclang || '',
                                                    placeholder: 'Lang (z.B. en)',
                                                    className: 'video-block-editor-subtitle-lang-input',
                                                    onChange: function ( e ) { update_subtitle( i, 'srclang', e.target.value ); }
                                                } ),
                                                el( 'button', {
                                                    type: 'button',
                                                    className: 'btn-remove-video video-block-editor-subtitle-remove-btn',
                                                    onClick: function () { remove_subtitle( i ); }
                                                }, '\u2715' )
                                            )
                                        );
                                    } )( idx );
                                } ),
                                el( 'button', {
                                    type: 'button',
                                    className: 'btn video-block-editor-subtitle-add-btn',
                                    onClick: add_subtitle
                                }, '+ Untertitel hinzufügen' )
                            ),
                            el( 'dt', null, 'Audio-Tracks (optional)' ),
                            el( 'dd', { class: 'video-block-editor-audio-tracks-field' },
                                audio_tracks_list.map( function ( track, idx ) {
                                    return ( function ( i ) {
                                        return el( 'div', { key: 'audio-' + i, class: 'video-block-editor-audio-track-row' },
                                            el( 'div', { class: 'video-block-editor-audio-track-url-row' },
                                                el( 'input', {
                                                    type: 'text',
                                                    value: track.url || '',
                                                    placeholder: 'https://example.com/audio-de.mp3',
                                                    className: 'video-block-editor-audio-track-input',
                                                    onChange: function ( e ) { update_audio_track( i, 'url', e.target.value ); }
                                                } ),
                                                el( MediaUpload, {
                                                    onSelect: function ( media ) { on_select_audio_track( i, media ); },
                                                    allowedTypes: [ 'audio' ],
                                                    render: function ( ref ) {
                                                        return el( 'button', {
                                                            type: 'button',
                                                            className: 'btn video-block-editor-audio-track-upload-btn',
                                                            onClick: ref.open
                                                        }, 'Upload Audio' );
                                                    }
                                                } )
                                            ),
                                            el( 'div', { class: 'video-block-editor-audio-track-meta-row' },
                                                el( 'input', {
                                                    type: 'text',
                                                    value: track.label || '',
                                                    placeholder: 'Label (z.B. Deutsch)',
                                                    className: 'video-block-editor-audio-track-meta-input',
                                                    onChange: function ( e ) { update_audio_track( i, 'label', e.target.value ); }
                                                } ),
                                                el( 'input', {
                                                    type: 'text',
                                                    value: track.lang || '',
                                                    placeholder: 'Lang (z.B. de)',
                                                    className: 'video-block-editor-audio-track-lang-input',
                                                    onChange: function ( e ) { update_audio_track( i, 'lang', e.target.value ); }
                                                } ),
                                                el( 'button', {
                                                    type: 'button',
                                                    className: 'btn-remove-video video-block-editor-audio-track-remove-btn',
                                                    onClick: function () { remove_audio_track( i ); }
                                                }, '\u2715' )
                                            )
                                        );
                                    } )( idx );
                                } ),
                                el( 'button', {
                                    type: 'button',
                                    className: 'btn video-block-editor-audio-track-add-btn',
                                    onClick: add_audio_track
                                }, '+ Audio-Track hinzufügen' )
                            )
                        ),
                        el( 'div', { class: 'video-block-editor-preview' },
                            video_url_value
                                ? el( 'video', {
                                    src: video_url_value,
                                    controls: true,
                                    style: { maxWidth: '100%' }
                                } )
                                : el( 'span', null, 'Kein Video gewählt. URL eingeben oder Video aus Mediathek wählen.' )
                        )
                    )
                )
            );
        },

        save: function () {
            return null;
        }
    } );
} )(
    window.wp,
    window.wp.blocks,
    window.wp.element,
    window.wp.data
);
