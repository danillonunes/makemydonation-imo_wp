<?php defined( 'ABSPATH' ) or die( 'No direct access allowed.' ); ?>

<?php if ( !$mmdimo_case || !$mmdimo_case['id'] ): ?>
  <?php wp_nonce_field( 'mmdimo_post_case_create', 'mmdimo_case_nonce' ); ?>
  <label class="selectit">
    <input value="1" type="checkbox" name="mmdimo_case_create" id="mmdimo-case-create" <?php if ( $mmdimo_case || $mmdimo_default_checked ) { echo 'checked="checked"'; } ?>> <?php _e( 'Allow Memorial Donations' ) ?>
  </label>
<?php else: ?>
  <?php wp_nonce_field( 'mmdimo_post_case_update_' . $mmdimo_case['id'], 'mmdimo_case_nonce' ); ?>
  <input type="hidden" name="mmdimo_case_update" value="<?php echo $mmdimo_case['id']; ?>">
<?php endif; ?>
<div class="case-data">
  <div class="mmdimo-field-family-email">
    <p>
      <label for="mmdimo-family-email">Family email</label>
      <br>
      <input type="text" id="mmdimo-family-email" name="mmdimo_family_email" size="20" value="<?php echo $mmdimo_family_email; ?>">
    </p>
  </div>

  <div class="mmdimo-field-family-notify">
    <label class="selectit">
      <input value="1" type="checkbox" name="mmdimo_family_notify" id="mmdimo-family-notify"> <?php _e( 'Notify the family' ) ?>
    </label>
  </div>

  <div class="mmdimo-field-charity">
    <p>
      <label for="mmdimo-charity">Charity</label>
      <br>
      <select id="mmdimo-charity-state" name="mmdimo_charity_state">
        <option value=""><?php _e('All states'); ?></option>
        <option value="AL"><?php _e('Alabama'); ?></option>
        <option value="AK"><?php _e('Alaska'); ?></option>
        <option value="AZ"><?php _e('Arizona'); ?></option>
        <option value="AR"><?php _e('Arkansas'); ?></option>
        <option value="CA"><?php _e('California'); ?></option>
        <option value="CO"><?php _e('Colorado'); ?></option>
        <option value="CT"><?php _e('Connecticut'); ?></option>
        <option value="DE"><?php _e('Delaware'); ?></option>
        <option value="DC"><?php _e('District of Columbia'); ?></option>
        <option value="FL"><?php _e('Florida'); ?></option>
        <option value="GA"><?php _e('Georgia'); ?></option>
        <option value="HI"><?php _e('Hawaii'); ?></option>
        <option value="ID"><?php _e('Idaho'); ?></option>
        <option value="IL"><?php _e('Illinois'); ?></option>
        <option value="IN"><?php _e('Indiana'); ?></option>
        <option value="IA"><?php _e('Iowa'); ?></option>
        <option value="KS"><?php _e('Kansas'); ?></option>
        <option value="KY"><?php _e('Kentucky'); ?></option>
        <option value="LA"><?php _e('Louisiana'); ?></option>
        <option value="ME"><?php _e('Maine'); ?></option>
        <option value="MD"><?php _e('Maryland'); ?></option>
        <option value="MA"><?php _e('Massachusetts'); ?></option>
        <option value="MI"><?php _e('Michigan'); ?></option>
        <option value="MN"><?php _e('Minnesota'); ?></option>
        <option value="MS"><?php _e('Mississippi'); ?></option>
        <option value="MO"><?php _e('Missouri'); ?></option>
        <option value="MT"><?php _e('Montana'); ?></option>
        <option value="NE"><?php _e('Nebraska'); ?></option>
        <option value="NV"><?php _e('Nevada'); ?></option>
        <option value="NH"><?php _e('New Hampshire'); ?></option>
        <option value="NJ"><?php _e('New Jersey'); ?></option>
        <option value="NM"><?php _e('New Mexico'); ?></option>
        <option value="NY"><?php _e('New York'); ?></option>
        <option value="NC"><?php _e('North Carolina'); ?></option>
        <option value="ND"><?php _e('North Dakota'); ?></option>
        <option value="OH"><?php _e('Ohio'); ?></option>
        <option value="OK"><?php _e('Oklahoma'); ?></option>
        <option value="OR"><?php _e('Oregon'); ?></option>
        <option value="PA"><?php _e('Pennsylvania'); ?></option>
        <option value="RI"><?php _e('Rhode Island'); ?></option>
        <option value="SC"><?php _e('South Carolina'); ?></option>
        <option value="SD"><?php _e('South Dakota'); ?></option>
        <option value="TN"><?php _e('Tennessee'); ?></option>
        <option value="TX"><?php _e('Texas'); ?></option>
        <option value="UT"><?php _e('Utah'); ?></option>
        <option value="VT"><?php _e('Vermont'); ?></option>
        <option value="VA"><?php _e('Virginia'); ?></option>
        <option value="WA"><?php _e('Washington'); ?></option>
        <option value="WV"><?php _e('West Virginia'); ?></option>
        <option value="WI"><?php _e('Wisconsin'); ?></option>
        <option value="WY"><?php _e('Wyoming'); ?></option>
      </select>
      <input type="text" id="mmdimo-charity" name="mmdimo_charity" size="20" value="<?php echo $mmdimo_charity; ?>">
      <input type="hidden" id="mmdimo-charity-metadata" name="mmdimo_charity_metadata" value='<?php echo $mmdimo_charity_metadata; ?>'>

      <br>
    </p>
  </div>
</div>