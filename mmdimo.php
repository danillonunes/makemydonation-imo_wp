<?php
/**
 * @package Make My Donation
 */
/*
Plugin Name: Make My Donation - In Memory Of
Plugin URI: https://wordpress.org/plugins/makemydonation-imo
Description: Integrate your funeral home site with our Make My Donation - In Memory Of Platform and allow donations to over 1.4 million eligible US charities.
Version: 1.7.1
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

if ( is_admin() ) {
  add_action( 'admin_init', 'mmdimo_admin_init' );
  add_action( 'admin_menu', 'mmdimo_admin_menu' );
  add_action( 'add_meta_boxes', 'mmdimo_add_meta_box' );
  add_action( 'save_post', 'mmdimo_meta_box_save', 10, 3 );
  add_action( 'admin_notices', 'mmdimo_show_error_message' );
  add_action( 'admin_enqueue_scripts', 'mmdimo_admin_scripts' );
  add_action( 'wp_ajax_mmdimo_load_funeral_homes', 'mmdimo_load_funeral_homes' );
}

add_action( 'wp_enqueue_scripts', 'mmdimo_wp_scripts' );
add_shortcode( 'mmdimo_donation_link', 'shortcode_mmdimo_donation_link' );
add_shortcode( 'mmdimo_donation_url', 'shortcode_mmdimo_donation_url' );
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
      wp_enqueue_script( 'mmdimo-typeahead', plugin_dir_url( __FILE__ ) . 'lib/typeahead.bundle.min.js', array(), $data['Version'] );
      wp_enqueue_script( 'mmdimo-selectize.js', plugin_dir_url( __FILE__ ) . 'lib/selectize.js-0.12.3/dist/js/standalone/selectize.min.js', array(), $data['Version'] );
      wp_enqueue_style( 'mmdimo-selectize.css', plugin_dir_url( __FILE__ ) . 'lib/selectize.js-0.12.3/dist/css/selectize.default.css', array(), $data['Version'] );
      wp_enqueue_script( 'mmdimo-edit-form', plugin_dir_url( __FILE__ ) . 'js/mmdimo.edit-form.js', array(), $data['Version'] );
      wp_enqueue_style( 'mmdimo-edit-form', plugin_dir_url( __FILE__ ) . 'css/mmdimo.edit-form.css', array(), $data['Version'] );
      break;
    case 'settings_page_mmdimo':
      wp_enqueue_script( 'mmdimo-options-form', plugin_dir_url( __FILE__ ) . 'js/mmdimo.options-form.js', array(), $data['Version'] );
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

  $mmdimo_case_checked = TRUE;
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
  if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;

  if( !current_user_can( 'edit_post', $post_id ) ) return;

  if( ( !isset( $_POST['mmdimo_case_create'] ) || !$_POST['mmdimo_case_create'] ) && ( !isset( $_POST['mmdimo_case_update'] ) || !$_POST['mmdimo_case_update'] ) ) return;

  if ( $_POST['mmdimo_case_create'] && ( !isset( $_POST['mmdimo_case_update'] ) || !$_POST['mmdimo_case_update'] ) && ( !isset( $_POST['mmdimo_case_nonce'] ) || !wp_verify_nonce( $_POST['mmdimo_case_nonce'], 'mmdimo_post_case_create' ) ) ) return;

  if ( $_POST['mmdimo_case_update'] && ( !isset( $_POST['mmdimo_case_nonce'] ) || !wp_verify_nonce( $_POST['mmdimo_case_nonce'], 'mmdimo_post_case_update_' . $_POST['mmdimo_case_update'] ) ) ) return;

  require_once( MMDIMO_PLUGIN_DIR . '/api.php' );

  $current_case = array();
  if ( $_POST['mmdimo_case_update'] ) {
    $current_case = mmdimo_api_case_load( $_POST['mmdimo_case_update'] );
  }
  $new_case = array(
    'name' => $post->post_title,
    'family_email' => '',
    'family_emails' => explode(',', $_POST['mmdimo_family_emails']),
    'family_notify' => isset( $_POST['mmdimo_family_notify'] ) && $_POST['mmdimo_family_notify'] ? '1' : '0',
    'charity' => '',
    'charities' => $_POST['mmdimo_charity_select'] == 'select' ? explode(',', $_POST['mmdimo_charities']) : array(),
    'status' => 1,
  );
  $case = array_merge($current_case, $new_case);

  update_post_meta( $post_id, 'mmdimo_case', $case );

  if ( !isset( $case['id'] ) || !$case['id'] ) {
    $saved_case = mmdimo_api_case_create( $case );
  }
  else {
    $saved_case = mmdimo_api_case_update( $case );
  }

  if ( isset( $saved_case['id'] ) ) {
    update_post_meta( $post_id, 'mmdimo_case', $saved_case );

    if ( $_POST['mmdimo_case_update'] && ( !isset( $_POST['mmdimo_case_create'] ) || !$_POST['mmdimo_case_create'] )) {
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
