<?php

function the_mmdimo_donation_link( $before = '', $after = '', $echo = true, $content = NULL, $title = NULL, $target = NULL, $ein = NULL ) {
  $donation_link = get_the_mmdimo_donation_link(0, $content, $title, $target, $ein);

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

function get_the_mmdimo_donation_link( $post = 0, $content = NULL, $title = NULL, $target = NULL, $ein = NULL ) {
  $url = get_the_mmdimo_donation_url( $post, $ein );
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

  if ( $post->ID && $url ) {
    $link = '<a class="mmdimo-donation-link" href="' . esc_url( $url ) . '" title="' . $title . '" ' . $target . '>' . $content . '</a>';

    $link .= '<span class="mmdimo-donation-url"> [' . esc_url( $url ) . '] </span>';

    return apply_filters( 'get_the_mmdimo_donation_link', $link, $post, $url );
  }

  return '';
}

function shortcode_mmdimo_donation_link( $attr = array(), $content = NULL ) {
  $title = isset($attr['title'] ) ? $attr['title'] : NULL;
  $target = isset($attr['target'] ) ? $attr['target'] : NULL;
  $ein = isset($attr['ein'] ) ? $attr['ein'] : NULL;

  if (!$content) {
    $content = NULL;
  }

  return get_the_mmdimo_donation_link(0, $content, $title, $target, $ein);
}

function the_mmdimo_donation_url( $before = '', $after = '', $echo = true, $ein = NULL ) {
  $donation_url = get_the_mmdimo_donation_url( 0, $ein );

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

function get_the_mmdimo_donation_url( $post = 0, $ein = NULL ) {
  $post = get_post( $post );
  $id = isset( $post->ID ) ? $post->ID : 0;
  $mmdimo_case = get_post_meta( $id, 'mmdimo_case', TRUE );
  if ( isset( $mmdimo_case['id'] ) && !isset( $mmdimo_case['url'] ) ) {
    $mmdimo_case = mmdimo_api_case_load( $mmdimo_case['id'] );
  }

  if ( isset( $mmdimo_case['url'] ) && $mmdimo_case['url'] ) {
    $query_strings = array();

    if ( is_numeric( $ein ) ) {
      $mmdimo_charity = json_decode( get_post_meta( $id, 'mmdimo_charity_metadata', TRUE ), TRUE );
      $charities = array();

      if ( isset( $mmdimo_charity['charity'] ) && isset( $mmdimo_charity['charity']['ein'] ) ) {
        $charities[] = $mmdimo_charity['charity']['ein'];
      }
      elseif ( !empty( $mmdimo_charity ) ) {
        foreach ( $mmdimo_charity as $charity ) {
          if ( isset( $charity['charity'] ) && isset( $charity['charity']['ein'] ) ) {
            $charities[] = $charity['charity']['ein'];
          }
        }
      }

      if ( in_array( $ein, $charities ) ) {
        $query_strings[] = 'ein=' . $ein;
      }
    }

    $query_strings[] = 'r=' . urlencode( get_permalink( $id ) );

    $url = $mmdimo_case['url'] . '?' . implode( '&', $query_strings );

    return apply_filters( 'the_mmdimo_donation_url', $url, $id );
  }

  return '';
}

function shortcode_mmdimo_donation_url() {
  return get_the_mmdimo_donation_url();
}

function the_mmdimo_donation_charity_name( $before = '', $after = '', $echo = TRUE ) {
  $charity_name = get_the_mmdimo_donation_charity_name();

  if ( strlen($charity_name) == 0 ) {
    return;
  }

  $charity_name = $before . $charity_name . $after;

  if ( $echo ) {
    echo $charity_name;
  }
  else {
    return $charity_name;
  }
}

function get_the_mmdimo_donation_charity_name( $post = 0 ) {
  $post = get_post( $post );
  $id = isset( $post->ID ) ? $post->ID : 0;

  $mmdimo_charity = json_decode( get_post_meta( $id, 'mmdimo_charity_metadata', TRUE ), TRUE );
  $charity_name = '';

  if ( isset( $mmdimo_charity['charity'] ) && isset( $mmdimo_charity['charity']['name'] ) ) {
    $charity_name = $mmdimo_charity['charity']['name'];
  }
  elseif ( !empty( $mmdimo_charity ) ) {
    $charities = array();
    foreach ( $mmdimo_charity as $charity ) {
      if ( isset( $charity['charity'] ) && isset( $charity['charity']['name'] ) ) {
        $charities[] = $charity['charity']['name'];
      }
    }
    $charity_name = implode( ', ', $charities );
  }

  return apply_filters( 'the_mmdimo_donation_charity_name', $charity_name, $id );
}

function shortcode_mmdimo_donation_charity_name() {
  return get_the_mmdimo_donation_charity_name();
}

function the_mmdimo_donation_charity_ein( $before = '', $after = '', $echo = TRUE ) {
  $charity_ein = get_the_mmdimo_donation_charity_ein();

  if ( strlen($charity_ein) == 0 ) {
    return;
  }

  $charity_ein = $before . $charity_ein . $after;

  if ( $echo ) {
    echo $charity_ein;
  }
  else {
    return $charity_ein;
  }
}

function get_the_mmdimo_donation_charity_ein( $post = 0 ) {
  $post = get_post( $post );
  $id = isset( $post->ID ) ? $post->ID : 0;

  $mmdimo_charity = json_decode( get_post_meta( $id, 'mmdimo_charity_metadata', TRUE ), TRUE );
  $charity_ein = '';

  if ( isset( $mmdimo_charity['charity'] ) && isset( $mmdimo_charity['charity']['ein'] ) ) {
    $charity_ein = $mmdimo_charity['charity']['ein'];
  }
  elseif ( !empty( $mmdimo_charity ) ) {
    $charities = array();
    foreach ( $mmdimo_charity as $charity ) {
      if ( isset( $charity['charity'] ) && isset( $charity['charity']['ein'] ) ) {
        $charities[] = $charity['charity']['ein'];
      }
    }
    $charity_ein = implode( ', ', $charities );
  }

  return apply_filters( 'the_mmdimo_donation_charity_ein', $charity_ein, $id );
}

function shortcode_mmdimo_donation_charity_ein() {
  return get_the_mmdimo_donation_charity_ein();
}
