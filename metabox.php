<?php defined( 'ABSPATH' ) or die( 'No direct access allowed.' ); ?>

<?php if ( !$mmdimo_case || !isset($mmdimo_case['id']) ): ?>
  <?php wp_nonce_field( 'mmdimo_post_case_create', 'mmdimo_case_nonce' ); ?>
<?php else: ?>
  <?php wp_nonce_field( 'mmdimo_post_case_update_' . $mmdimo_case_update_id, 'mmdimo_case_nonce' ); ?>
  <input type="hidden" name="mmdimo_case_update" value="<?php echo $mmdimo_case_update_id; ?>">
<?php endif; ?>
<label class="selectit">
  <input value="1" type="checkbox" name="mmdimo_case_create" id="mmdimo-case-create" <?php if ( $mmdimo_case_checked ) { echo 'checked="checked"'; } ?>> <?php _e( 'Allow Memorial Donations', 'mmdimo' ) ?>
</label>
<div class="case-data">

  <div class="mmdimo-field-family-emails">
    <p>
      <label for="mmdimo-family-emails"><?php _e( 'Family emails' ); ?></label>
      <input type="text" id="mmdimo-family-emails" name="mmdimo_family_emails" size="30" value="<?php echo $mmdimo_family_emails; ?>">
    </p>
  </div>

  <div class="mmdimo-field-charity">
    <p>
      <label for="mmdimo-charity"><?php _e( 'Charity' ); ?></label>
      <div id="mmdimo-charity-select">
        <label class="mmdimo-charity-select-all">
          <input type="radio" name="mmdimo_charity_select" id="mmdimo-charity-select-all" value="all" <?php if (!$mmdimo_charities): ?>checked="checked"<?php endif; ?>>
          <?php _e( 'Allow donations to any charity' ); ?>
        </label>
        <label class="mmdimo-charity-select-select">
          <input type="radio" name="mmdimo_charity_select" id="mmdimo-charity-select-select" value="select" <?php if ($mmdimo_charities): ?>checked="checked"<?php endif; ?>>
          <?php _e( 'Allow donations to selected charities only' ); ?>
        </label>
      </div>

      <div id="mmdimo-charity-selection">
        <div id="orghunter-csc" data-default-state="<?php echo $mmdimo_default_state; ?>"></div>
        <input type="text" id="mmdimo-charities" name="mmdimo_charities" size="20" value="<?php echo $mmdimo_charities; ?>">
        <input type="hidden" id="mmdimo-charity-metadata" name="mmdimo_charity_metadata" value='<?php echo base64_encode($mmdimo_charity_metadata); ?>'>
        <br>
      </div>
    </p>
  </div>
</div>
