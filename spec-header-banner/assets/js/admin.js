(function ($) {
  'use strict';

  $(function () {
    var mediaFrame;
    var $form = $('#post');
    var $notice = $('#shb_required_notice');
    var $pageSearch = $('#shb_page_search');
    var $pageItems = $('.shb-page-selector__item');
    var $pageEmpty = $('.shb-page-selector__empty');

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
        title: 'Seleccionar Imagen',
        button: {
          text: 'Usar Imagen'
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
        $notice.hide();
      });

      mediaFrame.open();
    });

    $form.on('submit', function (event) {
      if (!$('#shb_image_id').val()) {
        event.preventDefault();
        $notice.show();
        $('#shb_upload_image_button').focus();
        return false;
      }

      return true;
    });

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
  });
})(jQuery);
