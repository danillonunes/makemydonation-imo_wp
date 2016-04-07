<?php

function the_mmdimo_donation_link( $before = '', $after = '', $echo = true, $content = NULL, $title = NULL, $target = NULL ) {
  $donation_link = get_the_mmdimo_donation_link(0, $content, $title, $target);

  if ( strlen($donation_link) == 0 ) {
    return;
  }

  $donation_url = $before . $donation_link . $after;

  if ( $echo ) {
    echo $donation_link;
  }
  else {
    return $donation_link;
  }
}

function get_the_mmdimo_donation_link( $post = 0, $content = NULL, $title = NULL, $target = NULL ) {
  $url = get_the_mmdimo_donation_url( $post );
  $post = get_post( $post );

  if ( is_null($content) ) {
    $content = 'Donate';
  }
  if ( is_null($title) ) {
    $title = 'Make My Donation in Memory of ' . $post->post_title;
  }
  if ( is_null($target) ) {
    $target = '';
  }
  else {
    $target = 'target="' . $target . '"';
  }

  if ( $post->ID ) {
    $link = '<a class="mmdimo-donation-link" href="' . esc_url( $url ) . '" title="' . $title . '" ' . $target . '>' . $content . '</a>';

    return apply_filters( 'get_the_mmdimo_donation_link', $link, $post, $url );
  }

  return '';
}

function shortcode_mmdimo_donation_link( $attr = array(), $content = NULL ) {
  $title = isset($attr['title'] ) ? $attr['title'] : NULL;
  $target = isset($attr['target'] ) ? $attr['target'] : NULL;

  if (!$content) {
    $content = NULL;
  }

  return get_the_mmdimo_donation_link(0, $content, $title, $target);
}

function the_mmdimo_donation_url( $before = '', $after = '', $echo = true ) {
  $donation_url = get_the_mmdimo_donation_url();

  if ( strlen($donation_url) == 0 ) {
    return;
  }

  $donation_url = $before . $donation_url . $after;

  if ( $echo ) {
    echo $donation_url;
  }
  else {
    return $donation_url;
  }
}

function get_the_mmdimo_donation_url( $post = 0 ) {
  $post = get_post( $post );
  $id = isset( $post->ID ) ? $post->ID : 0;
  $mmdimo_case = get_post_meta( $id, 'mmdimo_case', TRUE );
  if ( isset( $mmdimo_case['id'] ) && !isset( $mmdimo_case['url'] ) ) {
    $mmdimo_case = mmdimo_api_case_load( $mmdimo_case['id'] );
  }

  if ( isset( $mmdimo_case['url'] ) && $mmdimo_case['url'] ) {
    $url = $mmdimo_case['url'] . '?r=' . urlencode( get_permalink( $id ) );

    return apply_filters( 'the_mmdimo_donation_url', $url, $id );
  }

  return '';
}

function shortcode_mmdimo_donation_url() {
  return get_the_mmdimo_donation_url();
}
