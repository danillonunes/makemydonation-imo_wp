<?php defined( 'ABSPATH' ) or die( 'No direct access allowed.' ); ?>
<div class="wrap">
<h2><?php _e( 'Make My Donation – In Memory Of', 'mmdimo' ); ?></h2>

<form method="post" action="options.php">
<?php settings_fields( 'mmdimo' ); ?>
<?php do_settings_sections( 'mmdimo' ); ?>
<table class="form-table">

<tr>
<th scope="row"><label for="mmdimo_username"><?php _e( 'Username', 'mmdimo' ); ?></label></th>
<td>
  <input type="text" class="regular-text" name="mmdimo_username" id="mmdimo-username" value="<?php echo get_option('mmdimo_username'); ?>" />
  <p class="description"><?php _e( 'Your Make My Donation account username.', 'mmdimo' ); ?> <?php _e( 'If you don’t have one yet,', 'mmdimo' );?> <a href="https://funerals.makemydonation.org/user/register/affiliate" target="_blank"><?php _e( 'create a free account with us', 'mmdimo' );?></a>.</p>
</td>
</tr>

<tr>
<th scope="row"><label for="mmdimo_api_key"><?php _e( 'API Key', 'mmdimo' ); ?></label></th>
<td>
  <input type="text" class="regular-text" name="mmdimo_api_key" id="mmdimo-api-key" value="<?php echo get_option('mmdimo_api_key'); ?>" />
  <p class="description"><?php _e( 'The API Key provided by your Make My Donation account.', 'mmdimo' ); ?> <a href="https://funerals.makemydonation.org/user/api" target="_blank"><?php _e( 'Click here to get your api key', 'mmdimo' );?></a>.</p>
</td>
</tr>

<tr class="mmdimo-fhid">
<th scope="row">
  <label for="mmdimo_fhid" class="mmdimo-fhid-label"><?php _e( 'Funeral Home ID', 'mmdimo' ); ?></label>
  <label for="mmdimo_select_fhid" class="mmdimo-select-fhid-label"><?php _e( 'Funeral Home', 'mmdimo' ); ?></label>
</th>
<td>
  <p class="mmdimo-fhid-loading spinner"><?php _e( 'Validating your credentials...', 'mmdimo' ); ?></p>
  <input type="text" class="regular-text" name="mmdimo_fhid" id="mmdimo-fhid" value="<?php echo get_option('mmdimo_fhid'); ?>" />
  <p class="description mmdimo-fhid-description"><?php _e( 'The Funeral Home ID or Funeral Home Subdomain.', 'mmdimo' ); ?></p>
  <p class="description mmdimo-select-fhid-description"><?php _e( 'Select the Funeral Home you want to use.', 'mmdimo' ); ?></p>
</td>
</tr>

<tr>
<th scope="row"><label for="mmdimo_post_type"><?php _e( 'Post Type', 'mmdimo' ); ?></label></th>
<td>
  <?php $mmdimo_post_type = get_option('mmdimo_post_type'); ?>
  <select class="postform" name="mmdimo_post_type" id="mmdimo-post-type">
    <option value="">-- <?php _e( 'All', 'mmdimo' ); ?> --</option>
    <?php foreach (get_post_types() as $type): ?>
      <?php switch($type) {
        case 'attachment':
        case 'nav_menu_item':
        case 'revision':
          continue 2;
        } ?>
      <?php $post_type = get_post_type_object($type); ?>
      <option value="<?php echo $type; ?>" <?php if ($type == $mmdimo_post_type) { echo 'selected="selected"'; } ?>><?php echo $post_type->labels->singular_name; ?></option>
    <?php endforeach; ?>
  </select>
  <p class="description"><?php _e( 'If you are using Custom Post Types for your funeral home obituaries, chose the post type here. If you leave the default option, you will be allowed to manually create a funeral home case for all post types.', 'mmdimo' ); ?></p>
</td>
</tr>

<tr>
<th scope="row"><label for="mmdimo_case_check_default"><?php _e( 'Default Settings', 'mmdimo' ); ?></label></th>
<td>
  <?php $mmdimo_case_check_default = get_option('mmdimo_case_check_default') !== ''; ?>
  <p>
    <label>
      <input type="checkbox" name="mmdimo_case_check_default" id="mmdimo-case-check-default" value="1" <?php if ($mmdimo_case_check_default) { echo 'checked="checked"'; } ?>>
      <?php _e( 'Allow Memorial Donations', 'mmdimo' ); ?>
    </label>
  </p>
  <p class="description"><?php _e( 'Enable memorial donations by default when creating a new post.', 'mmdimo' ); ?></p>
</td>
</tr>

<tr>
<th scope="row"><label for="mmdimo_default_state"><?php _e( 'Default State', 'mmdimo' ); ?></label></th>
<td>
  <?php $mmdimo_default_state = get_option('mmdimo_default_state'); ?>
  <select id="mmdimo-default-state" name="mmdimo_default_state">
    <option value="">-- <?php _e('No default', 'mmdimo'); ?> --</option>
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
  <p class="description"><?php _e( 'The default state to search for charities when creating a new post.', 'mmdimo' ); ?></p>
</td>
</tr>

</table>

<?php submit_button(); ?>
</form>
</div>
