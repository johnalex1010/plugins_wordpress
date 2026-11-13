(function ($) {
  'use strict';

  var adminI18n = window.SMP_ADMIN_I18N || {};

  function getI18nText(key, fallback) {
    return adminI18n[key] ? adminI18n[key] : fallback;
  }

  function isMediaLibraryAvailable() {
    return typeof wp !== 'undefined' && typeof wp.media !== 'undefined';
  }

  function bindMediaPicker(options) {
    var mediaFrame;

    $(options.buttonSelector).on('click', function (event) {
      event.preventDefault();

      if (!isMediaLibraryAvailable()) {
        return;
      }

      if (mediaFrame) {
        mediaFrame.open();
        return;
      }

      mediaFrame = wp.media({
        title: options.title,
        button: {
          text: options.buttonText
        },
        library: options.library,
        multiple: false
      });

      mediaFrame.on('select', function () {
        var attachment = mediaFrame.state().get('selection').first().toJSON();

        if (!options.isValidAttachment(attachment)) {
          return;
        }

        $(options.inputSelector).val(attachment.id);
        $(options.previewSelector).empty().append(options.createPreview(attachment));
      });

      mediaFrame.open();
    });
  }

  function createImagePreview(attachment) {
    var image = document.createElement('img');

    image.src = attachment.url;
    image.alt = attachment.alt || '';

    return image;
  }

  function createVideoPreview(attachment) {
    var video = document.createElement('video');

    video.src = attachment.url;
    video.controls = true;
    video.preload = 'metadata';

    return video;
  }

  function toggleMediaSections(selectedMediaType) {
    $('[data-smp-media-section]').each(function () {
      var allowedMediaTypes = String($(this).attr('data-smp-media-section') || '').split(' ');

      $(this).prop('hidden', allowedMediaTypes.indexOf(selectedMediaType) === -1);
    });
  }

  $(function () {
    var videoMimeTypes = $.isArray(adminI18n.videoMimeTypes) ? adminI18n.videoMimeTypes : ['video/mp4', 'video/webm'];
    var $pageSearch = $('#smp_page_search');
    var $pageItems = $('.smp-page-selector__item');
    var $pageEmpty = $('.smp-page-selector__empty');

    bindMediaPicker({
      buttonSelector: '#smp_upload_image_button',
      inputSelector: '#smp_image_id',
      previewSelector: '#smp_image_preview',
      title: getI18nText('mediaTitle', 'Seleccionar Imagen'),
      buttonText: getI18nText('mediaButton', 'Usar Imagen'),
      library: { type: 'image' },
      isValidAttachment: function () {
        return true;
      },
      createPreview: createImagePreview
    });

    bindMediaPicker({
      buttonSelector: '#smp_upload_video_button',
      inputSelector: '#smp_video_id',
      previewSelector: '#smp_video_preview',
      title: getI18nText('videoMediaTitle', 'Seleccionar Video'),
      buttonText: getI18nText('videoMediaButton', 'Usar Video'),
      library: { type: videoMimeTypes },
      isValidAttachment: function (attachment) {
        return videoMimeTypes.indexOf(attachment.mime) !== -1;
      },
      createPreview: createVideoPreview
    });

    $('input[name="smp_media_type"]').on('change', function () {
      toggleMediaSections(String($(this).val() || 'image'));
    });

    $pageSearch.on('input', function () {
      var query = String($(this).val() || '').toLowerCase().trim();
      var visibleCount = 0;

      $pageItems.each(function () {
        var $item = $(this);
        var title = String($item.data('smp-page-title') || '');
        var isVisible = !query || title.indexOf(query) !== -1;

        $item.toggleClass('is-hidden', !isVisible);

        if (isVisible) {
          visibleCount += 1;
        }
      });

      $pageEmpty.toggleClass('is-visible', visibleCount === 0);
    });
  });
})(jQuery);
