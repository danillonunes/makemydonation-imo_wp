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

</table>

<?php submit_button(); ?>
</form>
</div>