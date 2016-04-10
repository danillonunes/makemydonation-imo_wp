<?php defined( 'ABSPATH' ) or die( 'No direct access allowed.' ); ?>

<?php if ( !$mmdimo_case || !$mmdimo_case['id'] ): ?>
  <?php wp_nonce_field( 'mmdimo_post_case_create', 'mmdimo_case_nonce' ); ?>
  <label class="selectit">
    <input value="1" type="checkbox" name="mmdimo_case_create" id="mmdimo-case-create" <?php if ( $mmdimo_case || $mmdimo_default_checked ) { echo 'checked="checked"'; } ?>> <?php _e( 'Allow Memorial Donations', 'mmdimo' ) ?>
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
      <input type="text" id="mmdimo-family-email" name="mmdimo_family_email" size="30" value="<?php echo $mmdimo_family_email; ?>">
    </p>
  </div>

  <div class="mmdimo-field-family-notify">
    <label class="selectit">
      <input value="1" type="checkbox" name="mmdimo_family_notify" id="mmdimo-family-notify"> <?php _e( 'Notify the family', 'mmdimo' ) ?>
    </label>
  </div>

  <div class="mmdimo-field-charity">
    <p>
      <label for="mmdimo-charity">Charity</label>
      <br>
      <select id="mmdimo-charity-state" name="mmdimo_charity_state">
        <option value=""><?php _e('All states', 'mmdimo'); ?></option>
        <option value="AL"><?php _e('Alabama', 'mmdimo'); ?></option>
        <option value="AK"><?php _e('Alaska', 'mmdimo'); ?></option>
        <option value="AZ"><?php _e('Arizona', 'mmdimo'); ?></option>
        <option value="AR"><?php _e('Arkansas', 'mmdimo'); ?></option>
        <option value="CA"><?php _e('California', 'mmdimo'); ?></option>
        <option value="CO"><?php _e('Colorado', 'mmdimo'); ?></option>
        <option value="CT"><?php _e('Connecticut', 'mmdimo'); ?></option>
        <option value="DE"><?php _e('Delaware', 'mmdimo'); ?></option>
        <option value="DC"><?php _e('District of Columbia', 'mmdimo'); ?></option>
        <option value="FL"><?php _e('Florida', 'mmdimo'); ?></option>
        <option value="GA"><?php _e('Georgia', 'mmdimo'); ?></option>
        <option value="HI"><?php _e('Hawaii', 'mmdimo'); ?></option>
        <option value="ID"><?php _e('Idaho', 'mmdimo'); ?></option>
        <option value="IL"><?php _e('Illinois', 'mmdimo'); ?></option>
        <option value="IN"><?php _e('Indiana', 'mmdimo'); ?></option>
        <option value="IA"><?php _e('Iowa', 'mmdimo'); ?></option>
        <option value="KS"><?php _e('Kansas', 'mmdimo'); ?></option>
        <option value="KY"><?php _e('Kentucky', 'mmdimo'); ?></option>
        <option value="LA"><?php _e('Louisiana', 'mmdimo'); ?></option>
        <option value="ME"><?php _e('Maine', 'mmdimo'); ?></option>
        <option value="MD"><?php _e('Maryland', 'mmdimo'); ?></option>
        <option value="MA"><?php _e('Massachusetts', 'mmdimo'); ?></option>
        <option value="MI"><?php _e('Michigan', 'mmdimo'); ?></option>
        <option value="MN"><?php _e('Minnesota', 'mmdimo'); ?></option>
        <option value="MS"><?php _e('Mississippi', 'mmdimo'); ?></option>
        <option value="MO"><?php _e('Missouri', 'mmdimo'); ?></option>
        <option value="MT"><?php _e('Montana', 'mmdimo'); ?></option>
        <option value="NE"><?php _e('Nebraska', 'mmdimo'); ?></option>
        <option value="NV"><?php _e('Nevada', 'mmdimo'); ?></option>
        <option value="NH"><?php _e('New Hampshire', 'mmdimo'); ?></option>
        <option value="NJ"><?php _e('New Jersey', 'mmdimo'); ?></option>
        <option value="NM"><?php _e('New Mexico', 'mmdimo'); ?></option>
        <option value="NY"><?php _e('New York', 'mmdimo'); ?></option>
        <option value="NC"><?php _e('North Carolina', 'mmdimo'); ?></option>
        <option value="ND"><?php _e('North Dakota', 'mmdimo'); ?></option>
        <option value="OH"><?php _e('Ohio', 'mmdimo'); ?></option>
        <option value="OK"><?php _e('Oklahoma', 'mmdimo'); ?></option>
        <option value="OR"><?php _e('Oregon', 'mmdimo'); ?></option>
        <option value="PA"><?php _e('Pennsylvania', 'mmdimo'); ?></option>
        <option value="RI"><?php _e('Rhode Island', 'mmdimo'); ?></option>
        <option value="SC"><?php _e('South Carolina', 'mmdimo'); ?></option>
        <option value="SD"><?php _e('South Dakota', 'mmdimo'); ?></option>
        <option value="TN"><?php _e('Tennessee', 'mmdimo'); ?></option>
        <option value="TX"><?php _e('Texas', 'mmdimo'); ?></option>
        <option value="UT"><?php _e('Utah', 'mmdimo'); ?></option>
        <option value="VT"><?php _e('Vermont', 'mmdimo'); ?></option>
        <option value="VA"><?php _e('Virginia', 'mmdimo'); ?></option>
        <option value="WA"><?php _e('Washington', 'mmdimo'); ?></option>
        <option value="WV"><?php _e('West Virginia', 'mmdimo'); ?></option>
        <option value="WI"><?php _e('Wisconsin', 'mmdimo'); ?></option>
        <option value="WY"><?php _e('Wyoming', 'mmdimo'); ?></option>
      </select>
      <input type="text" id="mmdimo-charity" name="mmdimo_charity" size="20" value="<?php echo $mmdimo_charity; ?>">
      <input type="hidden" id="mmdimo-charity-metadata" name="mmdimo_charity_metadata" value='<?php echo $mmdimo_charity_metadata; ?>'>

      <br>
    </p>
  </div>
</div>