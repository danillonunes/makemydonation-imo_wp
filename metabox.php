<?php defined( 'ABSPATH' ) or die( 'No direct access allowed.' ); ?>

<?php if ( !$mmdimo_case || !$mmdimo_case['id'] ): ?>
  <?php wp_nonce_field( 'mmdimo_post_case_create', 'mmdimo_case_nonce' ); ?>
<?php else: ?>
  <?php wp_nonce_field( 'mmdimo_post_case_update_' . $mmdimo_case['id'], 'mmdimo_case_nonce' ); ?>
  <input type="hidden" name="mmdimo_case_update" value="<?php echo $mmdimo_case['id']; ?>">
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

  <div class="mmdimo-field-family-notify">
    <label class="selectit">
      <input value="1" type="checkbox" name="mmdimo_family_notify" id="mmdimo-family-notify"> <?php _e( 'Notify the family', 'mmdimo' ) ?>
    </label>
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
        <select id="mmdimo-charity-state" name="mmdimo_charity_state">
          <option value=""><?php _e('All states', 'mmdimo'); ?></option>
          <option value="AL" <?php if ('AL' == $mmdimo_default_state) { echo 'selected="selected"'; } ?>><?php _e('Alabama', 'mmdimo'); ?></option>
          <option value="AK" <?php if ('AK' == $mmdimo_default_state) { echo 'selected="selected"'; } ?>><?php _e('Alaska', 'mmdimo'); ?></option>
          <option value="AZ" <?php if ('AZ' == $mmdimo_default_state) { echo 'selected="selected"'; } ?>><?php _e('Arizona', 'mmdimo'); ?></option>
          <option value="AR" <?php if ('AR' == $mmdimo_default_state) { echo 'selected="selected"'; } ?>><?php _e('Arkansas', 'mmdimo'); ?></option>
          <option value="CA" <?php if ('CA' == $mmdimo_default_state) { echo 'selected="selected"'; } ?>><?php _e('California', 'mmdimo'); ?></option>
          <option value="CO" <?php if ('CO' == $mmdimo_default_state) { echo 'selected="selected"'; } ?>><?php _e('Colorado', 'mmdimo'); ?></option>
          <option value="CT" <?php if ('CT' == $mmdimo_default_state) { echo 'selected="selected"'; } ?>><?php _e('Connecticut', 'mmdimo'); ?></option>
          <option value="DE" <?php if ('DE' == $mmdimo_default_state) { echo 'selected="selected"'; } ?>><?php _e('Delaware', 'mmdimo'); ?></option>
          <option value="DC" <?php if ('DC' == $mmdimo_default_state) { echo 'selected="selected"'; } ?>><?php _e('District of Columbia', 'mmdimo'); ?></option>
          <option value="FL" <?php if ('FL' == $mmdimo_default_state) { echo 'selected="selected"'; } ?>><?php _e('Florida', 'mmdimo'); ?></option>
          <option value="GA" <?php if ('GA' == $mmdimo_default_state) { echo 'selected="selected"'; } ?>><?php _e('Georgia', 'mmdimo'); ?></option>
          <option value="HI" <?php if ('HI' == $mmdimo_default_state) { echo 'selected="selected"'; } ?>><?php _e('Hawaii', 'mmdimo'); ?></option>
          <option value="ID" <?php if ('ID' == $mmdimo_default_state) { echo 'selected="selected"'; } ?>><?php _e('Idaho', 'mmdimo'); ?></option>
          <option value="IL" <?php if ('IL' == $mmdimo_default_state) { echo 'selected="selected"'; } ?>><?php _e('Illinois', 'mmdimo'); ?></option>
          <option value="IN" <?php if ('IN' == $mmdimo_default_state) { echo 'selected="selected"'; } ?>><?php _e('Indiana', 'mmdimo'); ?></option>
          <option value="IA" <?php if ('IA' == $mmdimo_default_state) { echo 'selected="selected"'; } ?>><?php _e('Iowa', 'mmdimo'); ?></option>
          <option value="KS" <?php if ('KS' == $mmdimo_default_state) { echo 'selected="selected"'; } ?>><?php _e('Kansas', 'mmdimo'); ?></option>
          <option value="KY" <?php if ('KY' == $mmdimo_default_state) { echo 'selected="selected"'; } ?>><?php _e('Kentucky', 'mmdimo'); ?></option>
          <option value="LA" <?php if ('LA' == $mmdimo_default_state) { echo 'selected="selected"'; } ?>><?php _e('Louisiana', 'mmdimo'); ?></option>
          <option value="ME" <?php if ('ME' == $mmdimo_default_state) { echo 'selected="selected"'; } ?>><?php _e('Maine', 'mmdimo'); ?></option>
          <option value="MD" <?php if ('MD' == $mmdimo_default_state) { echo 'selected="selected"'; } ?>><?php _e('Maryland', 'mmdimo'); ?></option>
          <option value="MA" <?php if ('MA' == $mmdimo_default_state) { echo 'selected="selected"'; } ?>><?php _e('Massachusetts', 'mmdimo'); ?></option>
          <option value="MI" <?php if ('MI' == $mmdimo_default_state) { echo 'selected="selected"'; } ?>><?php _e('Michigan', 'mmdimo'); ?></option>
          <option value="MN" <?php if ('MN' == $mmdimo_default_state) { echo 'selected="selected"'; } ?>><?php _e('Minnesota', 'mmdimo'); ?></option>
          <option value="MS" <?php if ('MS' == $mmdimo_default_state) { echo 'selected="selected"'; } ?>><?php _e('Mississippi', 'mmdimo'); ?></option>
          <option value="MO" <?php if ('MO' == $mmdimo_default_state) { echo 'selected="selected"'; } ?>><?php _e('Missouri', 'mmdimo'); ?></option>
          <option value="MT" <?php if ('MT' == $mmdimo_default_state) { echo 'selected="selected"'; } ?>><?php _e('Montana', 'mmdimo'); ?></option>
          <option value="NE" <?php if ('NE' == $mmdimo_default_state) { echo 'selected="selected"'; } ?>><?php _e('Nebraska', 'mmdimo'); ?></option>
          <option value="NV" <?php if ('NV' == $mmdimo_default_state) { echo 'selected="selected"'; } ?>><?php _e('Nevada', 'mmdimo'); ?></option>
          <option value="NH" <?php if ('NH' == $mmdimo_default_state) { echo 'selected="selected"'; } ?>><?php _e('New Hampshire', 'mmdimo'); ?></option>
          <option value="NJ" <?php if ('NJ' == $mmdimo_default_state) { echo 'selected="selected"'; } ?>><?php _e('New Jersey', 'mmdimo'); ?></option>
          <option value="NM" <?php if ('NM' == $mmdimo_default_state) { echo 'selected="selected"'; } ?>><?php _e('New Mexico', 'mmdimo'); ?></option>
          <option value="NY" <?php if ('NY' == $mmdimo_default_state) { echo 'selected="selected"'; } ?>><?php _e('New York', 'mmdimo'); ?></option>
          <option value="NC" <?php if ('NC' == $mmdimo_default_state) { echo 'selected="selected"'; } ?>><?php _e('North Carolina', 'mmdimo'); ?></option>
          <option value="ND" <?php if ('ND' == $mmdimo_default_state) { echo 'selected="selected"'; } ?>><?php _e('North Dakota', 'mmdimo'); ?></option>
          <option value="OH" <?php if ('OH' == $mmdimo_default_state) { echo 'selected="selected"'; } ?>><?php _e('Ohio', 'mmdimo'); ?></option>
          <option value="OK" <?php if ('OK' == $mmdimo_default_state) { echo 'selected="selected"'; } ?>><?php _e('Oklahoma', 'mmdimo'); ?></option>
          <option value="OR" <?php if ('OR' == $mmdimo_default_state) { echo 'selected="selected"'; } ?>><?php _e('Oregon', 'mmdimo'); ?></option>
          <option value="PA" <?php if ('PA' == $mmdimo_default_state) { echo 'selected="selected"'; } ?>><?php _e('Pennsylvania', 'mmdimo'); ?></option>
          <option value="RI" <?php if ('RI' == $mmdimo_default_state) { echo 'selected="selected"'; } ?>><?php _e('Rhode Island', 'mmdimo'); ?></option>
          <option value="SC" <?php if ('SC' == $mmdimo_default_state) { echo 'selected="selected"'; } ?>><?php _e('South Carolina', 'mmdimo'); ?></option>
          <option value="SD" <?php if ('SD' == $mmdimo_default_state) { echo 'selected="selected"'; } ?>><?php _e('South Dakota', 'mmdimo'); ?></option>
          <option value="TN" <?php if ('TN' == $mmdimo_default_state) { echo 'selected="selected"'; } ?>><?php _e('Tennessee', 'mmdimo'); ?></option>
          <option value="TX" <?php if ('TX' == $mmdimo_default_state) { echo 'selected="selected"'; } ?>><?php _e('Texas', 'mmdimo'); ?></option>
          <option value="UT" <?php if ('UT' == $mmdimo_default_state) { echo 'selected="selected"'; } ?>><?php _e('Utah', 'mmdimo'); ?></option>
          <option value="VT" <?php if ('VT' == $mmdimo_default_state) { echo 'selected="selected"'; } ?>><?php _e('Vermont', 'mmdimo'); ?></option>
          <option value="VA" <?php if ('VA' == $mmdimo_default_state) { echo 'selected="selected"'; } ?>><?php _e('Virginia', 'mmdimo'); ?></option>
          <option value="WA" <?php if ('WA' == $mmdimo_default_state) { echo 'selected="selected"'; } ?>><?php _e('Washington', 'mmdimo'); ?></option>
          <option value="WV" <?php if ('WV' == $mmdimo_default_state) { echo 'selected="selected"'; } ?>><?php _e('West Virginia', 'mmdimo'); ?></option>
          <option value="WI" <?php if ('WI' == $mmdimo_default_state) { echo 'selected="selected"'; } ?>><?php _e('Wisconsin', 'mmdimo'); ?></option>
          <option value="WY" <?php if ('WY' == $mmdimo_default_state) { echo 'selected="selected"'; } ?>><?php _e('Wyoming', 'mmdimo'); ?></option>
        </select>
        <input type="text" id="mmdimo-charities" name="mmdimo_charities" size="20" value="<?php echo $mmdimo_charities; ?>">
        <input type="hidden" id="mmdimo-charity-metadata" name="mmdimo_charity_metadata" value='<?php echo $mmdimo_charity_metadata; ?>'>
        <br>
      </div>
    </p>
  </div>
</div>