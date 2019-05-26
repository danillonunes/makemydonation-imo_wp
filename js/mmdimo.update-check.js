jQuery(function($) {

$('#wpbody-content').each(function() {
  var $content = $(this);

  $.ajax(ajaxurl, {
    dataType: 'json',
    data: {
      action: 'mmdimo_check_update',
    },
    success: function(data, status, xhr) {
      if (data && data.new_version) {
        var update_message = '<div class="update-nag">There is a new version of Make My Donation - In Memory Of available. <a href="' + data.package + '">Download the new update</a>.</div>';
        $content.prepend(update_message);
      }
    }
  });
});

});
