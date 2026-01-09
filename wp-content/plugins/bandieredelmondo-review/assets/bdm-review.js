(function($){
  function openModal($overlay){ $overlay.css('display','flex'); }
  function closeModal($overlay){ $overlay.hide(); }

  function setStars($wrap, val){
    $wrap.find('.bdm-star').each(function(){
      var s = parseInt($(this).data('star'), 10);
      $(this).toggleClass('is-on', s <= val);
    });
    $wrap.closest('form').find('input[name="rating"]').val(val);
  }

  $(document).on('click', '.bdm-review-cta-btn', function(e){
    e.preventDefault();
    var target = $(this).data('target');
    openModal($('#' + target));
  });

  $(document).on('click', '.bdm-review-modal-close', function(e){
    e.preventDefault();
    closeModal($(this).closest('.bdm-review-modal-overlay'));
  });

  $(document).on('click', '.bdm-review-modal-overlay', function(e){
    if ($(e.target).is('.bdm-review-modal-overlay')) {
      closeModal($(this));
    }
  });

  $(document).on('click', '.bdm-star', function(){
    var val = parseInt($(this).data('star'), 10);
    var $wrap = $(this).closest('.bdm-stars');
    setStars($wrap, val);
  });

  $(document).on('submit', '.bdm-review-form', function(e){
    e.preventDefault();

    var $form = $(this);
    var $btn = $form.find('.bdm-review-submit');
    var $msg = $form.find('.bdm-review-msg');

    $msg.text('');
    $btn.prop('disabled', true).text(BDMReview.i18n.submitting);

    $.post(BDMReview.ajaxUrl, $form.serialize(), function(resp){
      if (resp && resp.success) {
        $msg.text(resp.data.message);
        $form[0].reset();
        setStars($form.find('.bdm-stars'), 0);
      } else {
        $msg.text((resp && resp.data && resp.data.message) ? resp.data.message : BDMReview.i18n.error);
      }
    }).fail(function(){
      $msg.text(BDMReview.i18n.error);
    }).always(function(){
      $btn.prop('disabled', false).text($btn.data('label'));
    });
  });

})(jQuery);
