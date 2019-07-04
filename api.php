<?php
defined( 'ABSPATH' ) or die( 'No direct access allowed.' );

/**
 * Returns the API URL.
 *
 * @return
 *   String with the API URL value, or the default value if it's not set.
 */
function mmdimo_api_url() {
  $url = get_option( 'mmdimo_api_url' );

  return $url ? $url : MMDIMO_API_URL_DEFAULT;
}

/**
 * Returns the API authentication data.
 *
 * @return
 *   Array with the username and api key.
 */
function mmdimo_api_auth() {
  return array(
    'user' => get_option( 'mmdimo_username' ),
    'key' => get_option( 'mmdimo_api_key' ),
  );
}

/**
 * Helper function to do a generic IMO API request.
 */
function mmdimo_api_request( $method, $path, $data = NULL, $args = array(), $auth = NULL ) {
  $url = mmdimo_api_url();
  $auth = !is_null( $auth ) ? $auth : mmdimo_api_auth();

  $default_args = array(
    'method' => strtoupper( $method ),
    'timeout' => 30,
    'headers' => array(
      'Authorization' => 'Basic ' . base64_encode( $auth['user'] . ':' . $auth['key'] ),
      'Content-Type' => 'application/json',
    ),
  );

  if ( $data ) {
    $default_args['body'] = json_encode( $data );
  }

  $args = array_merge( $default_args, $args );

  $raw_response = wp_remote_request( $url . '/' . $path, $args );

  if ( !is_wp_error( $raw_response ) ) {
    return $raw_response;
  }
}

/**
 * Load the list of funeral homes using the API.
 *
 * @return
 *   Array with the funeral homes data as in the API description.
 */
function mmdimo_api_funeral_home_index() {
  $response = mmdimo_api_request( 'get', "funeral_home" );

  if ( isset( $response['response'] ) ) {
    switch ($response['response']['code']) {
      case 200:
        return (array) json_decode( $response['body'] );
    }
  }

  return FALSE;
}

/**
 * Load a case using the API.
 *
 * @param $case_id
 *   The case id.
 * @return
 *   Array with the case data as in the API description.
 */
function mmdimo_api_case_load( $case_id ) {
  $response = mmdimo_api_request( 'get', "case/$case_id" );

  if ( isset( $response['response'] ) ) {
    switch ($response['response']['code']) {
      case 200:
        return (array) json_decode( $response['body'] );
    }
  }

  return FALSE;
}

/**
 * Load donations from a single case using the API.
 *
 * @param $case_id
 *   The case id.
 * @return
 *   Array with the donation data as in the API description.
 */
function mmdimo_api_case_donations_load( $case_id, $count = FALSE ) {
  $count = (int) $count;
  $response = mmdimo_api_request( 'get', "donation?funeral_home_case=$case_id&count=$count" );

  if ( is_array( $response ) && isset( $response['response'] ) ) {
    switch ($response['response']['code']) {
      case 200:
        return (array) json_decode( $response['body'] );
    }
  }

  return FALSE;
}

/**
 * Create a case using the API.
 *
 * @param $case
 *   Array with the case data as in the API description.
 * @return
 *   Array with the case data as in the API description.
 */
function mmdimo_api_case_create( $case ) {
  if ( isset( $case['name'] ) && $case['name'] ) {
    $default_values = array(
      'funeral_home' => get_option('mmdimo_fhid'),
      'internal_id' => '',
      'family_emails' => array(),
      'charities' => array(),
    );
    $case = array_merge( $default_values, $case );

    $response = mmdimo_api_request( 'post', 'case', $case );

    if ( isset( $response['response'] ) ) {
      switch ($response['response']['code']) {
        case 200:
          return (array) json_decode( $response['body'] );
      }
    }

    return FALSE;
  }
}

/**
 * Update a case using the API.
 *
 * @param $case
 *   Array with the case data as in the API description.
 * @return
 *   Array with the case data as in the API description.
 */
function mmdimo_api_case_update( $case ) {
  if ( isset( $case['id'] ) && $case['id'] && isset( $case['name'] ) && $case['name'] ) {
    $default_values = array(
      'funeral_home' => get_option('mmdimo_fhid'),
      'internal_id' => '',
      'family_emails' => array(),
      'charities' => array(),
    );
    $case = array_merge( $default_values, $case );

    $response = mmdimo_api_request( 'put', 'case/' . $case['id'], $case );

    if ( isset( $response['response'] ) ) {
      switch ($response['response']['code']) {
        case 200:
          return (array) json_decode( $response['body'] );
      }
    }

    return FALSE;
  }
}

/**
 * Create or update a case using the API.
 *
 * @param $case
 *   Array with the case data as in the API description.
 * @return
 *   Array with the case data as in the API description.
 */
function mmdimo_api_case_create_or_update( $case ) {
  if ( isset( $case['internal_id'] ) && $case['internal_id'] && isset( $case['name'] ) && $case['name'] ) {
    $default_values = array(
      'action' => 'create_or_update',
      'funeral_home' => get_option('mmdimo_fhid'),
      'family_emails' => array(),
      'charities' => array(),
    );
    $case = array_merge( $default_values, $case );

    $response = mmdimo_api_request( 'post', 'case', $case );

    if ( isset( $response['response'] ) ) {
      switch ($response['response']['code']) {
        case 200:
          return (array) json_decode( $response['body'] );
      }
    }

    return FALSE;
  }
}
