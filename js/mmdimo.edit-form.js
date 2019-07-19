jQuery(function($) {

var $metabox = $('#mmdimo_meta_box');

$metabox.each(function() {
  var $create = $('#mmdimo-case-create', $metabox);
  var $data = $('.case-data', $metabox);

  if ($create.length) {
    $data.hide();

    $create.bind('change', function() {
      if ($(this).is(':checked')) {
        $data.slideDown();
      }
      else {
        $data.slideUp();
      }
    }).trigger('change');
  }
});

$metabox.each(function() {
  var $family = $('.mmdimo-field-family-emails input', $metabox);
  var $notify = $('.mmdimo-field-family-notify', $metabox);

  $family
    .selectize({
      'create': true,
      'createOnBlur': true,
      'highlight': false,
      'persist': false,
      'openOnFocus': false,
      'maxOptions': 0,
      'maxItems': null,
      'createFilter': /^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/
    })
    .bind('change', function() {
      $notify.hide();
      if ($family.val() != '') {
        $notify.show();
      }
    })
    .trigger('change');

    $('<style type="text/css">.selectize-control.multi .selectize-input [data-value], .selectize-control.multi .selectize-input .active[data-value] { background-color: ' + _wpColorScheme.icons.base + '; } </style>').appendTo($family);
});

$('#mmdimo_meta_box').each(function() {
  var metabox = this;
  var $create = $('#mmdimo-case-create', metabox);
  var $data = $('.case-data', metabox);
  var $charities = $('#mmdimo-charities', metabox);
  var $orghunterCSC = $('#orghunter-csc', metabox);
  var $select = $('#mmdimo-charity-select input', metabox);
  var $all = $('#mmdimo-charity-select-all', metabox);
  var $single = $('#mmdimo-charity-select-select', metabox);
  var $charitySelect = $('#mmdimo-charity-selection', metabox);
  var $charityMeta = $('#mmdimo-charity-metadata', metabox);
  var $charityActive = $orghunterCSC.before('<ul id="mmdimo-charity-active" class="mmdimo-empty"></ul>').prev('#mmdimo-charity-active');
  var metadata = {};

  if ($charities.length) {
    $orghunterCSC.orghunterCSC({
      apiURL: ajaxurl,
      apiData: {
        action: 'mmdimo_orghunter_csc_ajax',
      },
      searchAlias: true,
      stateChosen: false,
      size: 'small',
      resultsHeight: '220px',
      resultsLimit: 20,
      defaultState: $orghunterCSC.attr('data-default-state'),
      onCharitySelect: function(ein, data) {
        var charity = {
          ein: ein,
          ein_hash: data.ein_hash,
          parent_ein: data.parent_ein,
          name: data.charityName,
          url: data.url,
          city: data.city,
          state: data.state
        };
        $charities.trigger('mmdimo-add', [charity]);
        $orghunterCSC.trigger('ohcsc-reset', [{
          keepState: true,
          keepCity: true,
        }]);
      }
    });

    $charities
      .hide()
      .on('mmdimo-add', function(e, charity) {
        var charity_id = charity.ein_hash || charity.ein;
        metadata['charity-ein-' + charity_id] = {
          charity: charity
        };
        $charities.trigger('mmdimo-reval');

        $charityActive
          .removeClass('mmdimo-empty')
          .append('<li id="mmdimo-charity-active-' + charity_id + '"><strong>' + charity.name + '</strong><br><span>EIN: ' + charity.ein + ' — ' + charity.city + ', ' + charity.state + '</span> <a class="mmdimo-charity-clear" title="Clear selected charity">Remove</a></li>');
      })
      .on('mmdimo-remove', function(e, charity_id) {
        var ein = charity_id.substring(0,9);
        delete metadata['charity-ein-' + ein];
        delete metadata['charity-ein-' + charity_id];
        $charities.trigger('mmdimo-reval');

        $charityActive.find('#mmdimo-charity-active-' + ein)
          .remove();
        $charityActive.find('#mmdimo-charity-active-' + charity_id)
          .remove();

        if (!$charityActive.children('li').length) {
          $charityActive.addClass('mmdimo-empty');
        }
      })
      .on('mmdimo-reval', function() {
        $charities.val(Object.keys(metadata).join(',').replace(/charity-ein-/g, ''));
        $charityMeta.val(JSON.stringify(metadata));
      });

    if ($charityMeta.val()) {
      var defaultMetadata = $.parseJSON(atob($charityMeta.val()));
      if (typeof defaultMetadata.charity != 'undefined') {
        metadata['charity-ein-' + defaultMetadata.charity.ein_hash] = defaultMetadata;
      }
      else {
        $.each(defaultMetadata, function(ein_hash, charity) {
          metadata[ein_hash] = charity;
        });
      }

      $.each(metadata, function(ein_hash, option) {
        $charities.trigger('mmdimo-add', option.charity);
      });
    }

    window.setTimeout(function() {
      $orghunterCSC.trigger('ohcsc-refresh');
    }, 1000);

    $select
      .bind('change', function() {
        if ($single.is(':checked')) {
          $charitySelect.show();
        }
        else {
         $charitySelect.hide();
        }
        $orghunterCSC.trigger('ohcsc-refresh');
      })
      .trigger('change');

    $charityActive
      .on('click', '.mmdimo-charity-clear', function() {
        var charity_id = $(this).parent('li').attr('id').match(/mmdimo-charity-active-(.*)/)[1];

        $charities.trigger('mmdimo-remove', charity_id);
      });
  }
});

});
