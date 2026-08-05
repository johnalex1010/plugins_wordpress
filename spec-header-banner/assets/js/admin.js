(function ($) {
  'use strict';

  $(function () {
    var mediaFrame;
    var $form = $('#post');
    var $imageRequiredNotice = $('#shb_image_required_notice');
    var $textRequiredNotice = $('#shb_text_required_notice');
    var $contentTypeFields = $('input[name="shb_content_type"]');
    var $contentPanels = $('[data-shb-content-panel]');
    var $pageSearch = $('#shb_page_search');
    var $pageItems = $('.shb-page-selector__item');
    var $pageEmpty = $('.shb-page-selector__empty');

    function getSelectedContentType() {
      return String($contentTypeFields.filter(':checked').val() || 'image');
    }

    function syncContentPanels() {
      var selectedContentType = getSelectedContentType();

      $contentPanels.each(function () {
        var $panel = $(this);
        var isActive = String($panel.data('shb-content-panel')) === selectedContentType;

        $panel.toggleClass('is-hidden', !isActive);
        $panel.attr('aria-hidden', isActive ? 'false' : 'true');
      });

      $imageRequiredNotice.hide();
      $textRequiredNotice.hide();
    }

    $('#shb_upload_image_button').on('click', function (event) {
      event.preventDefault();

      if (typeof wp === 'undefined' || typeof wp.media === 'undefined') {
        return;
      }

      if (mediaFrame) {
        mediaFrame.open();
        return;
      }

      mediaFrame = wp.media({
        title: window.SHB_ADMIN_I18N && window.SHB_ADMIN_I18N.mediaTitle ? window.SHB_ADMIN_I18N.mediaTitle : 'Seleccionar Imagen',
        button: {
          text: window.SHB_ADMIN_I18N && window.SHB_ADMIN_I18N.mediaButton ? window.SHB_ADMIN_I18N.mediaButton : 'Usar Imagen'
        },
        multiple: false
      });

      mediaFrame.on('select', function () {
        var attachment = mediaFrame.state().get('selection').first().toJSON();
        var image = document.createElement('img');

        image.src = attachment.url;
        image.alt = attachment.alt || '';

        $('#shb_image_id').val(attachment.id);
        $('#shb_image_preview').empty().append(image);
        $imageRequiredNotice.hide();
      });

      mediaFrame.open();
    });

    $form.on('submit', function (event) {
      var selectedContentType = getSelectedContentType();

      if (selectedContentType === 'image' && !$('#shb_image_id').val()) {
        event.preventDefault();
        $imageRequiredNotice.show();
        $('#shb_upload_image_button').focus();
        return false;
      }

      if (selectedContentType === 'text' && !String($('#shb_text').val() || '').trim()) {
        event.preventDefault();
        $textRequiredNotice.show();
        $('#shb_text').focus();
        return false;
      }

      return true;
    });

    $contentTypeFields.on('change', syncContentPanels);

    $pageSearch.on('input', function () {
      var query = String($(this).val() || '').toLowerCase().trim();
      var visibleCount = 0;

      $pageItems.each(function () {
        var $item = $(this);
        var title = String($item.data('shb-page-title') || '');
        var isVisible = !query || title.indexOf(query) !== -1;

        $item.toggleClass('is-hidden', !isVisible);

        if (isVisible) {
          visibleCount += 1;
        }
      });

      $pageEmpty.toggleClass('is-visible', visibleCount === 0);
    });

    syncContentPanels();
  });
})(jQuery);
