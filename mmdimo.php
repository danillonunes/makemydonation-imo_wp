<?php
/**
 * @package Make My Donation
 */
/*
Plugin Name: Make My Donation - In Memory Of
Plugin URI: https://wordpress.org/plugins/makemydonation-imo
Description: Integrate your funeral home site with our Make My Donation - In Memory Of Platform and allow donations to over 1.5 million eligible US charities.
Version: 1.13.1
Author: Make My Donation
Author URI: http://makemydonation.org
License: GPLv2 or later
Text Domain: mmdimo
*/

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

// Define directory where the plugin is installed
define( 'MMDIMO_PLUGIN_DIR', realpath(dirname(__FILE__)) );

// Default API URL
define( 'MMDIMO_API_URL_DEFAULT', 'https://api.makemydonation.org/v1' );

require_once( MMDIMO_PLUGIN_DIR . '/template.php' );

require_once( MMDIMO_PLUGIN_DIR . '/lib/wp-background-processing/classes/wp-async-request.php' );
require_once( MMDIMO_PLUGIN_DIR . '/lib/wp-background-processing/classes/wp-background-process.php' );

if ( is_admin() ) {
  add_action( 'admin_init', 'mmdimo_admin_init' );
  add_action( 'admin_menu', 'mmdimo_admin_menu' );
  add_action( 'add_meta_boxes', 'mmdimo_add_meta_box' );
  add_action( 'save_post', 'mmdimo_meta_box_save', 10, 3 );
  add_action( 'admin_notices', 'mmdimo_show_error_message' );
  add_action( 'admin_enqueue_scripts', 'mmdimo_admin_scripts' );
  add_action( 'wp_ajax_mmdimo_load_funeral_homes', 'mmdimo_load_funeral_homes' );
  add_action( 'wp_ajax_mmdimo_orghunter_csc_ajax', 'mmdimo_orghunter_csc_ajax' );
  add_action( 'wp_ajax_mmdimo_check_update', 'mmdimo_check_update_ajax' );
}

add_action( 'init', 'mmdimo_init' );
add_action( 'wp_enqueue_scripts', 'mmdimo_wp_scripts' );
add_shortcode( 'mmdimo_donation_link', 'shortcode_mmdimo_donation_link' );
add_shortcode( 'mmdimo_donation_url', 'shortcode_mmdimo_donation_url' );
add_shortcode( 'mmdimo_donations_count', 'shortcode_mmdimo_donations_count' );
add_shortcode( 'mmdimo_donations_list', 'shortcode_mmdimo_donations_list' );
add_shortcode( 'mmdimo_donation_charity:name', 'shortcode_mmdimo_donation_charity_name' );
add_shortcode( 'mmdimo_donation_charity:ein', 'shortcode_mmdimo_donation_charity_ein' );

function mmdimo_wp_scripts() {
  wp_enqueue_style( 'mmdimo-donation-link', plugin_dir_url( __FILE__ ) . 'css/mmdimo.donation-link.css' );
}

function mmdimo_admin_init() {
  register_setting( 'mmdimo', 'mmdimo_api_url' );
  register_setting( 'mmdimo', 'mmdimo_username' );
  register_setting( 'mmdimo', 'mmdimo_api_key' );
  register_setting( 'mmdimo', 'mmdimo_fhid' );
  register_setting( 'mmdimo', 'mmdimo_post_type' );
  register_setting( 'mmdimo', 'mmdimo_default_state' );
  register_setting( 'mmdimo', 'mmdimo_case_check_default' );
  register_setting( 'mmdimo', 'mmdimo_update' );
  register_setting( 'mmdimo', 'mmdimo_install_uuid' );
}

function mmdimo_init() {
  if ( isset( $_GET['action'] ) && $_GET['action'] == 'mmdimo_load_donations' ) {
    return;
  }

  require_once( MMDIMO_PLUGIN_DIR . '/classes/mmdimo-load-donations.php' );
  $load_donations = new MMDIMO_Load_Donations();

  if ( isset( $_GET['doing_wp_cron'] ) ) {
    if ( $load_donations->is_queue_empty() ) {
      return;
    }
  }

  $load_donations->dispatch();
}

function mmdimo_admin_menu() {
  add_options_page( 'Make My Donation – In Memory Of', 'Make My Donation', 'manage_options', 'mmdimo', 'mmdimo_options' );
}

function mmdimo_options() {
  include( MMDIMO_PLUGIN_DIR . '/options.php' );
}

function mmdimo_admin_scripts( $hook ) {
  $data = get_plugin_data( __FILE__ );

  switch ( $hook ) {
    case 'post.php':
    case 'post-new.php':
      wp_enqueue_script( 'mmdimo-selectize.js', plugin_dir_url( __FILE__ ) . 'lib/selectize.js-0.12.3/dist/js/standalone/selectize.min.js', array(), $data['Version'] );
      wp_enqueue_style( 'mmdimo-selectize.css', plugin_dir_url( __FILE__ ) . 'lib/selectize.js-0.12.3/dist/css/selectize.default.css', array(), $data['Version'] );
      wp_enqueue_script( 'mmdimo-edit-form', plugin_dir_url( __FILE__ ) . 'js/mmdimo.edit-form.js', array(), $data['Version'] );
      wp_enqueue_style( 'mmdimo-edit-form', plugin_dir_url( __FILE__ ) . 'css/mmdimo.edit-form.css', array(), $data['Version'] );

      wp_enqueue_script( 'mmdimo-ohcsc-typeahead.js', plugin_dir_url( __FILE__ ) . 'lib/orghunter-csc/lib/corejs-typeahead/typeahead.bundle.min.js', array(), $data['Version'] );
      wp_enqueue_script( 'mmdimo-ohcsc-chosen.js', plugin_dir_url( __FILE__ ) . 'lib/orghunter-csc/lib/chosen/chosen.jquery.min.js', array(), $data['Version'] );
      wp_enqueue_script( 'mmdimo-ohcsc.js', plugin_dir_url( __FILE__ ) . 'lib/orghunter-csc/js/orghunter.csc.min.js', array(), $data['Version'] );
      wp_enqueue_style( 'mmdimo-ohcsc-chosen.css', plugin_dir_url( __FILE__ ) . 'lib/orghunter-csc/lib/chosen/chosen.min.css', array(), $data['Version'] );
      wp_enqueue_style( 'mmdimo-ohcsc.css', plugin_dir_url( __FILE__ ) . 'lib/orghunter-csc/css/orghunter.csc.min.css', array(), $data['Version'] );
      break;
    case 'settings_page_mmdimo':
      wp_enqueue_script( 'mmdimo-options-form', plugin_dir_url( __FILE__ ) . 'js/mmdimo.options-form.js', array(), $data['Version'] );
      wp_enqueue_script( 'mmdimo-update-check', plugin_dir_url( __FILE__ ) . 'js/mmdimo.update-check.js', array(), $data['Version'] );
      wp_enqueue_style( 'mmdimo-options-form', plugin_dir_url( __FILE__ ) . 'css/mmdimo.options-form.css', array(), $data['Version'] );
      break;
  }
}

function mmdimo_add_meta_box() {
  $mmdimo_post_type = get_option( 'mmdimo_post_type' );
  if (!$mmdimo_post_type) {
    $mmdimo_post_type = NULL;
  }
  add_meta_box( 'mmdimo_meta_box', __('Make My Donation – In Memory Of', 'mmdimo'), 'mmdimo_meta_box_callback', $mmdimo_post_type, 'normal' );
}

function mmdimo_meta_box_callback( $post ) {
  $mmdimo_case = get_post_meta( $post->ID, 'mmdimo_case', TRUE );
  $mmdimo_family_emails = isset( $mmdimo_case['family_email'] ) ? $mmdimo_case['family_email'] : isset( $mmdimo_case['family_emails'] ) ? $mmdimo_case['family_emails'] : '';
  if (is_array($mmdimo_family_emails)) {
    $mmdimo_family_emails = implode( ',', $mmdimo_family_emails );
  }

  $mmdimo_case_checked = get_option('mmdimo_case_check_default') !== '';
  if ( isset( $mmdimo_case ) && $mmdimo_case ) {
    if ( isset( $mmdimo_case['status'] ) && $mmdimo_case['status'] == 0 ) {
      $mmdimo_case_checked = FALSE;
    }
  }
  else if ( $post->post_status != 'auto-draft' ) {
    $mmdimo_case_checked = FALSE;
  }

  $mmdimo_charity_metadata = get_post_meta( $post->ID, 'mmdimo_charity_metadata', TRUE );
  $mmdimo_charities = isset( $mmdimo_case['charity'] ) ? $mmdimo_case['charity'] : '';
  $mmdimo_charities = isset( $mmdimo_case['charities'] ) && is_array( $mmdimo_case['charities'] ) ? implode( ',', $mmdimo_case['charities'] ) : $mmdimo_charities;

  $mmdimo_default_state = get_option( 'mmdimo_default_state' );
  if ( isset( $mmdimo_charity_metadata ) ) {
    $mmdimo_charity_metadata_array = json_decode( $mmdimo_charity_metadata, TRUE );
    if ( !empty( $mmdimo_charity_metadata_array ) ) {
      $mmdimo_charities = implode( ',', array_keys( $mmdimo_charity_metadata_array ) );
    }
    if ( isset( $mmdimo_charity_metadata_array['charity'] ) ) {
      $mmdimo_charity_metadata_array = array(
        'charity-ein-' . $mmdimo_charity_metadata_array['charity']['ein'] => $mmdimo_charity_metadata_array
      );
    }
    else {
      $mmdimo_charity_metadata_array_sorted = array();
      $mmdimo_charity_metadata_array = $mmdimo_charity_metadata_array ? $mmdimo_charity_metadata_array : array();
      foreach ($mmdimo_charity_metadata_array as $ein => $charity) {
        $mmdimo_charity_metadata_array_sorted['charity-ein-' . $ein] = $charity;
      }
      $mmdimo_charity_metadata_array = $mmdimo_charity_metadata_array_sorted;
    }
    $mmdimo_charity_metadata = json_encode( $mmdimo_charity_metadata_array );
  }

  include( MMDIMO_PLUGIN_DIR . '/metabox.php' );
}

function mmdimo_meta_box_save( $post_id, $post, $update ) {
  $request_data = $_POST;
  $validate = mmdimo_meta_box_save_validate($post_id, $post, $update, $request_data);

  if (!is_wp_error($validate)) {
    require_once( MMDIMO_PLUGIN_DIR . '/api.php' );

    $current_case = array();
    if ( $postmeta_case = get_post_meta( $post_id, 'mmdimo_case', TRUE )) {
      $current_case = $postmeta_case;
    }
    if ( isset( $_POST['mmdimo_case_update'] ) && $_POST['mmdimo_case_update'] ) {
      $remote_case = mmdimo_api_case_load( $_POST['mmdimo_case_update'] );
      $current_case = array_merge($current_case, $remote_case);
    }
    $new_case = array(
      'internal_id' => mmdimo_case_generate_internal_id($post_id),
      'name' => $post->post_title,
      'family_emails' => explode(',', $_POST['mmdimo_family_emails']),
      'charities' => $_POST['mmdimo_charity_select'] == 'select' ? explode(',', $_POST['mmdimo_charities']) : array(),
      'status' => 1,
      'return_url' => get_permalink($post_id)
    );
    $case = array_merge($current_case, $new_case);

    update_post_meta( $post_id, 'mmdimo_case', $case );
    if ( isset( $_POST['mmdimo_charity_metadata'] ) ) {
      update_post_meta( $post_id, 'mmdimo_charity_metadata', preg_replace( '/charity-ein-/', '', $_POST['mmdimo_charity_metadata'] ) );
    }

    $saved_case = mmdimo_api_case_create_or_update( $case );

    if ( isset( $saved_case['id'] ) ) {
      update_post_meta( $post_id, 'mmdimo_case', $saved_case );

      if ( ( isset( $_POST['mmdimo_case_update'] ) && $_POST['mmdimo_case_update'] ) && ( !isset( $_POST['mmdimo_case_create'] ) || !$_POST['mmdimo_case_create'] )) {
        $saved_case['status'] = 0;

        update_post_meta( $post_id, 'mmdimo_case', $saved_case );
      }

      if ( isset( $_POST['mmdimo_charity_metadata'] ) ) {
        update_post_meta( $post_id, 'mmdimo_charity_metadata', preg_replace( '/charity-ein-/', '', $_POST['mmdimo_charity_metadata'] ) );
      }
    }
    else {
      mmdimo_error_message(__( 'Error while connecting to the Make My Donation API server. Edit and save the post to try again.', 'mmdimo' ));
    }
  }
}

/**
 * Validate the case data sent from the metabox in a save_post action.
 *
 * See save_post action reference for $post_id, $post and $update parameters.
 *
 * @param $post_id
 * @param $post
 * @param $update
 * @param $request
 *   The PHP formatted array with the request data. Usually the global $_POST variable.
 *
 * @return
 *   TRUE if valid or WP_Error object if invalid.
 */
function mmdimo_meta_box_save_validate($post_id, $post, $update, $request) {
  $error = new WP_Error();

  if (empty($request)) {
    $error->add('mmdimo_meta_box_save_empty_request', __('Empty request data.', 'mmdimo'));
  }

  $doing_autosave = defined('DOING_AUTOSAVE') && DOING_AUTOSAVE;
  if ($doing_autosave) {
    $error->add('mmdimo_meta_box_save_autosave', __('Not creating a case while doing autosave.', 'mmdimo'));
  }

  $can_edit = current_user_can('edit_post', $post_id);
  if (!$can_edit) {
    $error->add('mmdimo_meta_box_save_not_authorized', __('User cannot edit this post.', 'mmdimo'));
  }

  $post_type = get_post_type($post_id);
  $default_invalid_types = array('revision');
  $mmdimo_post_type = get_option('mmdimo_post_type');
  $default_valid_type = !$mmdimo_post_type && !in_array($post_type, $default_invalid_types);
  $custom_valid_type = $mmdimo_post_type && $mmdimo_post_type == $post_type;
  $post_type = $default_valid_type || $custom_valid_type;
  if (!$post_type) {
    $error->add('mmdimo_meta_box_save_invalid_post_type', __('Invalid post type.', 'mmdimo'));
  }

  $action_create = isset($request['mmdimo_case_create']) && $request['mmdimo_case_create'];
  $action_update = isset($request['mmdimo_case_update']) && $request['mmdimo_case_update'];
  $nonce_create = isset($request['mmdimo_case_nonce']) && wp_verify_nonce($request['mmdimo_case_nonce'], 'mmdimo_post_case_create');
  $nonce_update = isset($request['mmdimo_case_nonce']) && wp_verify_nonce($request['mmdimo_case_nonce'], 'mmdimo_post_case_update');
  $doing_create = $action_create && !$action_update && $nonce_create;
  $doing_update = $action_update && !$action_create && $nonce_update;
  $create_or_update = $doing_create || $doing_update;
  if (!$create_or_update) {
    $error->add('mmdimo_meta_box_save_invalid_create_or_update', __('Invalid create or update nonce.', 'mmdimo'));
  }

  if ($error->get_error_codes()) {
    return $error;
  }

  return TRUE;
}

function mmdimo_error_message( $message = NULL ) {
  if ( $message ) {
    setcookie('mmdimo-error-message', $message, time() + 5);
  }
  else if ( isset( $_COOKIE['mmdimo-error-message'] ) ) {
    return $_COOKIE['mmdimo-error-message'];
  }
}

function mmdimo_show_error_message() {
  $message = mmdimo_error_message();
  if ($message) {
    echo '<div class="notice notice-error is-dismissible"><p>' . $message . '</p></div>';
  }
}

function mmdimo_load_funeral_homes() {
  $auth = array(
    'user' => $_REQUEST['username'],
    'key' => $_REQUEST['key'],
  );

  require_once( MMDIMO_PLUGIN_DIR . '/api.php' );
  $response = mmdimo_api_request( 'get', "funeral_home", NULL, array(), $auth );

  if ( isset( $response['response'] ) ) {
    switch ($response['response']['code']) {
      case 200:
        die($response['body']);
      default:
        status_header(404);
        die('');
    }
  }
}

function mmdimo_load_donations_by_post( $post_id = 0, $count = FALSE ) {
  $now = time();

  $meta_id = $count ? 'mmdimo_donations_count' : 'mmdimo_donations';

  $donations = get_post_meta( $post_id, $meta_id, TRUE );

  if ( !$donations ) {
    $donations = array(
      'content' => '',
      'expires' => 0
    );
  }

  if ( $now > $donations['expires'] ) {
    require_once( MMDIMO_PLUGIN_DIR . '/classes/mmdimo-load-donations.php' );
    $load_donations = new MMDIMO_Load_Donations();

    $load_donations->push_to_queue( array( 'post_id' => $post_id, 'count' => $count ) );
    $load_donations->save();
  }


  return $donations['content'];
}

function mmdimo_load_donations_by_post_process( $post_id = 0, $count = FALSE ) {
  $now = time();
  $expires = 60 * 60;

  $meta_id = $count ? 'mmdimo_donations_count' : 'mmdimo_donations';

  $donations = array(
    'content' => '',
    'expires' => 0
  );

  $mmdimo_case = get_post_meta( $post_id, 'mmdimo_case', TRUE );
  $case_id = $mmdimo_case && isset( $mmdimo_case['id'] ) ? $mmdimo_case['id'] : NULL;
  require_once( MMDIMO_PLUGIN_DIR . '/api.php' );

  if ( $case_id && $content = mmdimo_api_case_donations_load($case_id, $count) ) {
    $donations['content'] = $content;
    $donations['expires'] = $now + $expires;
    update_post_meta( $post_id, $meta_id, $donations );
  }

  return $donations;
}

function mmdimo_check_update_ajax() {
  $check_update = mmdimo_check_update();
  if ($check_update) {
    die(json_encode($check_update));
  }
}

function mmdimo_check_update() {
  $plugin_info = mmdimo_get_plugin_info();
  $current_plugin = current( $plugin_info );
  $updated_plugin = get_option( 'mmdimo_update' );

  if ($updated_plugin && version_compare( $updated_plugin['new_version'], $current_plugin['Version'] ) && $updated_plugin['check_time'] + (60 * 60 * 24) < time()) {
    return $updated_plugin;
  }
  elseif ($updated_plugin && version_compare( $updated_plugin['new_version'], $current_plugin['Version'] ) <= 0) {
    delete_option( 'mmdimo_update' );
  }

  $remote_plugin = mmdimo_check_update_remote( $plugin_info );

  if ( $remote_plugin ) {
    $remote_plugin['check_time'] = time();
    update_option( 'mmdimo_update', $remote_plugin );

    $updated_plugin = $remote_plugin;
  }

  return $updated_plugin;
}

function mmdimo_get_plugin_info() {
  $plugins = get_plugins();

  foreach ($plugins as $plugin_path => $plugin) {
    if (strpos($plugin_path, 'mmdimo.php') !== FALSE) {
      return array($plugin_path => $plugin);
    }
  }

  return NULL;
}

function mmdimo_check_update_remote( $plugin ) {
  $to_send = array(
    'plugins' => $plugin,
    'active' => array_keys($plugin)
  );

  // include an unmodified $wp_version
  include( ABSPATH . WPINC . '/version.php' );

  $options = array(
    'timeout'    => 5,
    'body'       => array(
      'plugins'      => wp_json_encode( $to_send ),
      'translations' => wp_json_encode( array() ),
      'locale'       => wp_json_encode( array() ),
      'all'          => wp_json_encode( false ),
    ),
    'user-agent' => 'WordPress/' . $wp_version . '; ' . home_url( '/' ),
  );

  $url = 'http://api.wordpress.org/plugins/update-check/1.1/';
  if ( $ssl = wp_http_supports( array( 'ssl' ) ) ) {
    $url = set_url_scheme( $url, 'https' );
  }

  $raw_response = wp_remote_post( $url, $options );

  if ( !is_wp_error( $raw_response ) ) {
    $response = json_decode( wp_remote_retrieve_body( $raw_response ), true );
    if ( isset( $response['plugins'] ) ) {
      return current( $response['plugins'] );
    }
  }

  return FALSE;
}

function mmdimo_orghunter_csc_ajax() {
  require_once( MMDIMO_PLUGIN_DIR . '/api.php' );
  $path = substr($_SERVER['PHP_SELF'], strlen($_SERVER['SCRIPT_NAME']) + 1);
  $spath = explode('/', $path);
  $response = array();

  switch ($spath[0]) {
    case 'cities':
      if (strlen($spath[1]) == 2) {
        $response = mmdimo_api_request( 'get', 'orghunter/data/' . $path );
      }
      break;
    case 'charitysearch':
    case 'charitysearchalias':
      $accepted_parameters = array('eligible', 'state', 'city', 'searchTerm');
      foreach ($accepted_parameters as $accepted_parameter) {
        if (isset($_GET[$accepted_parameter])) {
          $parameters[$accepted_parameter] = $_GET[$accepted_parameter];
        }
      }
      $query = http_build_query($parameters);
      $response = mmdimo_api_request( 'get', 'orghunter/data/' . $path . '?' . $query );
      break;
  }

  if (isset($response['response'])) {
    switch ($response['response']['code']) {
      case 200:
        header('Content-Type: application/json');
        die($response['body']);
      default:
        status_header(404);
        die('');
    }
  }

  $response = mmdimo_api_cities_list( $state );
}

/**
 * Get an unique id for this plugin installation.
 *
 * @return
 *   String with the UUID value.
 */
function mmdimo_get_install_uuid() {
  $uuid = get_option('mmdimo_install_uuid');

  if (!$uuid) {
    $uuid = sprintf(
        '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand( 0, 0xffff ),
        mt_rand( 0, 0xffff ),
        mt_rand( 0, 0xffff ),
        mt_rand( 0, 0x0fff ) | 0x4000,
        mt_rand( 0, 0x3fff ) | 0x8000,
        mt_rand( 0, 0xffff ),
        mt_rand( 0, 0xffff ),
        mt_rand( 0, 0xffff )
    );
    update_option('mmdimo_install_uuid', $uuid);
  }

  return $uuid;
}

/**
 * Generate an unique internal id for a given post id.
 *
 * @return
 *   String with generated internal id.
 */
function mmdimo_case_generate_internal_id($post_id) {
  return implode(':', array(
    'wp',
    'mmdimo',
    mmdimo_get_install_uuid(),
    parse_url(get_option('siteurl'), PHP_URL_HOST),
    $post_id
  ));
}
