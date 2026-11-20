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

        if (options.removeButtonSelector) {
          $(options.removeButtonSelector).prop('hidden', false);
        }

        updateRequiredFieldsNotice();
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

  function getSelectedMediaType() {
    return String($('input[name="smp_media_type"]:checked').val() || 'image');
  }

  function hasFieldValue(selector) {
    return String($(selector).val() || '').trim() !== '';
  }

  // Devuelve el selector del primer campo obligatorio vacío según el tipo de contenido, o '' si no falta ninguno.
  function getFirstMissingRequiredField() {
    var selectedMediaType = getSelectedMediaType();

    if (selectedMediaType === 'video') {
      if (!hasFieldValue('#smp_video_id')) {
        return '#smp_upload_video_button';
      }

      if (!hasFieldValue('#smp_video_poster_id')) {
        return '#smp_upload_video_poster_button';
      }
    }

    if (selectedMediaType === 'video' || selectedMediaType === 'youtube') {
      if (!hasFieldValue('#smp_mobile_title')) {
        return '#smp_mobile_title';
      }

      if (!hasFieldValue('#smp_mobile_description')) {
        return '#smp_mobile_description';
      }
    }

    return '';
  }

  function updateRequiredFieldsNotice() {
    if (!getFirstMissingRequiredField()) {
      $('#smp_required_fields_notice').prop('hidden', true);
    }
  }

  // Solo se valida al publicar o actualizar (#publish); guardar borrador sigue permitido.
  function bindPublishValidation() {
    $('#publish').on('click', function (event) {
      var missingFieldSelector = getFirstMissingRequiredField();

      if (!missingFieldSelector) {
        return;
      }

      event.preventDefault();
      $('#smp_required_fields_notice').prop('hidden', false);
      $(missingFieldSelector).trigger('focus');
    });

    $('#smp_mobile_title, #smp_mobile_description').on('input', updateRequiredFieldsNotice);
    $('input[name="smp_media_type"]').on('change', updateRequiredFieldsNotice);
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

    bindMediaPicker({
      buttonSelector: '#smp_upload_video_poster_button',
      inputSelector: '#smp_video_poster_id',
      previewSelector: '#smp_video_poster_preview',
      removeButtonSelector: '#smp_remove_video_poster_button',
      title: getI18nText('posterMediaTitle', 'Seleccionar portada'),
      buttonText: getI18nText('posterMediaButton', 'Usar portada'),
      library: { type: 'image' },
      isValidAttachment: function (attachment) {
        return attachment.type === 'image';
      },
      createPreview: createImagePreview
    });

    $('.smp-remove-media').on('click', function (event) {
      var $removeButton = $(this);

      event.preventDefault();
      $($removeButton.attr('data-smp-input')).val('');
      $($removeButton.attr('data-smp-preview')).empty();
      $removeButton.prop('hidden', true);
    });

    bindPublishValidation();

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
