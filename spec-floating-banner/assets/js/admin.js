(function ($) {
  'use strict';

  $(function () {
    var imageFrame;
    var videoFrame;
    var $form = $('#post');
    var $imageNotice = $('#sfb_image_required_notice');
    var $videoNotice = $('#sfb_video_required_notice');
    var $mediaTypeFields = $('input[name="sfb_media_type"]');
    var $mediaPanels = $('[data-sfb-media-panel]');
    var $mediaLabels = $('[data-sfb-label]');
    var $pageSearch = $('#sfb_page_search');
    var $pageItems = $('.sfb-page-list__item');
    var $pageEmpty = $('.sfb-page-selector__empty');
    var allowedVideoTypes = ['video/mp4', 'video/webm'];

    function getSelectedMediaType() {
      return String($mediaTypeFields.filter(':checked').val() || 'image');
    }

    function updateMediaFields() {
      var selectedMediaType = getSelectedMediaType();

      $mediaPanels.each(function () {
        var $panel = $(this);
        $panel.toggleClass('is-active', String($panel.data('sfb-media-panel')) === selectedMediaType);
      });

      $mediaLabels.each(function () {
        var $label = $(this);
        $label.toggleClass('is-active', String($label.data('sfb-label')) === selectedMediaType);
      });

      $('#sfb_cta_label').prop('required', selectedMediaType === 'video');
    }

    updateMediaFields();

    $mediaTypeFields.on('change', function () {
      updateMediaFields();
    });

    $('#sfb_upload_btn').on('click', function (event) {
      event.preventDefault();

      if (imageFrame) {
        imageFrame.open();
        return;
      }

      imageFrame = wp.media({
        title: window.SFB_ADMIN_I18N && window.SFB_ADMIN_I18N.mediaTitle ? window.SFB_ADMIN_I18N.mediaTitle : 'Seleccionar imagen',
        library: {
          type: 'image'
        },
        multiple: false
      });

      imageFrame.on('select', function () {
        var attachment = imageFrame.state().get('selection').first().toJSON();

        $('#sfb_image_id').val(attachment.id);
        $('#sfb_preview').attr('src', attachment.url);
        $imageNotice.hide();
      });

      imageFrame.open();
    });

    $('#sfb_video_upload_btn').on('click', function (event) {
      event.preventDefault();

      if (videoFrame) {
        videoFrame.open();
        return;
      }

      videoFrame = wp.media({
        title: window.SFB_ADMIN_I18N && window.SFB_ADMIN_I18N.videoTitle ? window.SFB_ADMIN_I18N.videoTitle : 'Seleccionar video',
        library: {
          type: 'video'
        },
        multiple: false
      });

      videoFrame.on('select', function () {
        var attachment = videoFrame.state().get('selection').first().toJSON();
        var mimeType = String(attachment.mime || '');

        if (allowedVideoTypes.indexOf(mimeType) === -1) {
          $('#sfb_video_id').val('');
          $('#sfb_video_preview').attr('src', '');
          $videoNotice.show();
          return;
        }

        $('#sfb_video_id').val(attachment.id);
        $('#sfb_video_preview').attr('src', attachment.url);
        $videoNotice.hide();
      });

      videoFrame.open();
    });

    $form.on('submit', function (event) {
      var selectedMediaType = getSelectedMediaType();
      var imageId = $('#sfb_image_id').val();
      var videoId = $('#sfb_video_id').val();
      var ctaLabelField = $('#sfb_cta_label').get(0);
      var linkField = $('#sfb_link').get(0);

      if (selectedMediaType === 'image' && !imageId) {
        event.preventDefault();
        $imageNotice.show();
        $('#sfb_upload_btn').focus();
        return false;
      }

      if (selectedMediaType === 'video' && !videoId) {
        event.preventDefault();
        $videoNotice.show();
        $('#sfb_video_upload_btn').focus();
        return false;
      }

      if (selectedMediaType === 'video' && ctaLabelField && !ctaLabelField.checkValidity()) {
        event.preventDefault();
        ctaLabelField.reportValidity();
        return false;
      }

      if (linkField && !linkField.checkValidity()) {
        event.preventDefault();
        linkField.reportValidity();
        return false;
      }

      return true;
    });

    $pageSearch.on('input', function () {
      var query = String($(this).val() || '').toLowerCase().trim();
      var visibleCount = 0;

      $pageItems.each(function () {
        var $item = $(this);
        var title = String($item.data('sfb-page-title') || '');
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
