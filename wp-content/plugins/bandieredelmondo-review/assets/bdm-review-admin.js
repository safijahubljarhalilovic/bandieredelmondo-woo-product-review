(function($){
    function pickMedia(target){
      var frame = wp.media({
        title: 'Select image',
        button: { text: 'Use this image' },
        multiple: false
      });
  
      frame.on('select', function(){
        var attachment = frame.state().get('selection').first().toJSON();
  
        if (target === 'profile') {
          $('#bdm_profile_photo_id').val(attachment.id);
          $('#bdm_profile_photo_preview').attr('src', attachment.sizes && attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url).show();
        } else {
          $('#bdm_product_photo_id').val(attachment.id);
          $('#bdm_product_photo_preview').attr('src', attachment.sizes && attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url).show();
        }
      });
  
      frame.open();
    }
  
    function removeMedia(target){
      if (target === 'profile') {
        $('#bdm_profile_photo_id').val('');
        $('#bdm_profile_photo_preview').attr('src','').hide();
      } else {
        $('#bdm_product_photo_id').val('');
        $('#bdm_product_photo_preview').attr('src','').hide();
      }
    }
  
    $(document).on('click', '.bdm-media-pick', function(e){
      e.preventDefault();
      pickMedia($(this).data('target'));
    });
  
    $(document).on('click', '.bdm-media-remove', function(e){
      e.preventDefault();
      removeMedia($(this).data('target'));
    });

    function setStars($wrap, value){
        $wrap.find('.bdm-admin-star').each(function(){
            var star = parseInt($(this).data('star'), 10);
            $(this).toggleClass('is-on', star <= value);
        });
        $('#bdm_admin_rating_input').val(value);
        }

        $(document).on('click', '.bdm-admin-star', function(){
        var $wrap = $(this).closest('.bdm-admin-rating');
        var val = parseInt($(this).data('star'), 10);
        setStars($wrap, val);
    });
  })(jQuery);
  