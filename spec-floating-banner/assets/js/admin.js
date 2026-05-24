(function ($) {
  'use strict';

  $(function () {
    var mediaFrame;
    var $form = $('#post');
    var $notice = $('#sfb_required_notice');
    var $pageSearch = $('#sfb_page_search');
    var $pageItems = $('.sfb-page-list__item');
    var $pageEmpty = $('.sfb-page-selector__empty');

    $('#sfb_upload_btn').on('click', function (event) {
      event.preventDefault();

      if (mediaFrame) {
        mediaFrame.open();
        return;
      }

      mediaFrame = wp.media({
        title: window.SFB_ADMIN_I18N && window.SFB_ADMIN_I18N.mediaTitle ? window.SFB_ADMIN_I18N.mediaTitle : 'Seleccionar imagen',
        multiple: false
      });

      mediaFrame.on('select', function () {
        var attachment = mediaFrame.state().get('selection').first().toJSON();

        $('#sfb_image_id').val(attachment.id);
        $('#sfb_preview').attr('src', attachment.url);
        $notice.hide();
      });

      mediaFrame.open();
    });

    $form.on('submit', function (event) {
      var imageId = $('#sfb_image_id').val();
      var linkField = $('#sfb_link').get(0);

      if (!imageId) {
        event.preventDefault();
        $notice.show();
        $('#sfb_upload_btn').focus();
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
