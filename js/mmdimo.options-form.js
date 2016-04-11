jQuery(function($) {

$('form').each(function() {
  var $form = $(this);
  var $username = $('#mmdimo-username', this);
  var $apikey = $('#mmdimo-api-key', this);
  var $fhid = $('#mmdimo-fhid', this);
  var $fhidTR = $fhid.closest('tr');
  var $fhidLoading = $fhidTR.find('.mmdimo-fhid-loading');
  var $fhidLabel = $fhidTR.find('.mmdimo-fhid-label');
  var $fhidDescription = $fhidTR.find('.mmdimo-fhid-description');
  var $selectFHID = $fhid.after('<select id="mmdimo-select-fhid"></select>').next('#mmdimo-select-fhid');
  var $selectFHIDLabel = $fhidTR.find('.mmdimo-select-fhid-label');
  var $selectFHIDDescription = $fhidTR.find('.mmdimo-select-fhid-description');

  $fhidTR.find('label').text('Funeral Home');

  $selectFHID.hide();

  var mmdimoAuthFieldChange = function() {
    var user = $username.val();
    var key = $apikey.val();

    if (user && key) {
      $username.removeClass('success error');
      $apikey.removeClass('success error');

      $fhid.hide();
      $fhidLabel.hide();
      $fhidDescription.hide();

      $fhidTR.show();
      $fhidLoading.show();

      $.ajax(ajaxurl, {
        dataType: 'json',
        data: {
          action: 'mmdimo_load_funeral_homes',
          username: user,
          key: key,
        },
        complete: function() {
          $fhidLoading.hide();
          if ($selectFHID.find('option').length <= 1) {
            $fhidTR.hide();
          }
        },
        success: function(data, status, xhr) {
          var funeralHomes = xhr.responseJSON;

          $selectFHID.html('');
          $.each(funeralHomes, function(k, funeralHome) {
            var option = '<option value="' + funeralHome.id + '" ' + ($fhid.val() == funeralHome.id ? 'selected="selected"' : '') + '>' + funeralHome.name + '</option>';
            $selectFHID.append(option);
          });
          $selectFHID.trigger('change');

          $username.addClass('success');
          $apikey.addClass('success');

          if (funeralHomes.length > 1) {
            $fhidTR.show();
            $selectFHIDLabel.show();
            $selectFHIDDescription.show();
            $selectFHID.show();
          }
        },
        statusCode: {
          404: function() {
            $username.addClass('error');
            $apikey.addClass('error');
          },
        }
      });
    }
  };

  $username.bind('change', mmdimoAuthFieldChange);
  $apikey.bind('change', mmdimoAuthFieldChange);

  $selectFHID.bind('change', function() {
    $fhid.val($(this).val());
  });

  mmdimoAuthFieldChange();
});

});