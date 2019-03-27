<?php

function the_mmdimo_donation_link( $before = '', $after = '', $echo = true, $content = NULL, $title = NULL, $target = NULL, $ein = NULL, $display_count = FALSE ) {
  $donation_link = get_the_mmdimo_donation_link(0, $content, $title, $target, $ein, $display_count);

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

function get_the_mmdimo_donation_link( $post = 0, $content = NULL, $title = NULL, $target = NULL, $ein = NULL, $display_count = FALSE ) {
  $url = get_the_mmdimo_donation_url( $post, $ein );
  $post = get_post( $post );
  $id = isset( $post->ID ) ? $post->ID : 0;

  if ( is_null($content) ) {
    $content = 'Donate';
  }

  if ( $display_count ) {
    $count = get_the_mmdimo_donations_count( $id );

    if ( $count ) {
      $content .=  ' (' . $count . ')';
    }
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
  $display_count = isset($attr['display_count'] ) && $attr['display_count'] ? TRUE : FALSE;

  if (!$content) {
    $content = NULL;
  }

  return get_the_mmdimo_donation_link(0, $content, $title, $target, $ein, $display_count);
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

    $case_url = str_replace('http://', 'https://', $mmdimo_case['url']);
    $query_strings[] = 'r=' . urlencode( get_permalink( $id ) );

    $url = $case_url . '?' . implode( '&', $query_strings );

    return apply_filters( 'the_mmdimo_donation_url', $url, $id );
  }

  return '';
}

function shortcode_mmdimo_donation_url() {
  return get_the_mmdimo_donation_url();
}

function the_mmdimo_donations_count( $before = '', $after = '', $echo = true, $content = NULL ) {
  $donations_count = get_the_mmdimo_donations_count(0, $content);

  if ( strlen($donations_count) == 0 ) {
    return;
  }

  $donations_count = $before . $donations_count . $after;

  if ( $echo ) {
    echo $donations_count;
  }
  else {
    return $donations_count;
  }
}

function get_the_mmdimo_donations_count( $post = 0, $content = NULL ) {
  $post = get_post( $post );
  $id = isset( $post->ID ) ? $post->ID : 0;

  $donations_count = mmdimo_load_donations_by_post($id, TRUE);

  if ( $post->ID && $donations_count['total'] ) {
    $count =  sprintf(_n( '%s donation', '%s donations', $donations_count['total'], 'mmdimo' ), $donations_count['total']);

    $content = '<span class="mmdimo-donation-count">' . $count . '</span>';

    return apply_filters( 'get_the_mmdimo_donations_count', $content, $post, $donations_count );
  }

  return '';
}

function shortcode_mmdimo_donations_count( $content = NULL ) {
  if (!$content) {
    $content = NULL;
  }

  return get_the_mmdimo_donations_count(0, $content);
}

function the_mmdimo_donations_list( $before = '', $after = '', $echo = true, $content = NULL, $display_attributes = array() ) {
  $donations = get_the_mmdimo_donations_list(0, $content, $display_attributes = array());

  if ( strlen($donations) == 0 ) {
    return;
  }

  $donations = $before . $donations . $after;

  if ( $echo ) {
    echo $donations;
  }
  else {
    return $donations;
  }
}

function get_the_mmdimo_donations_list( $post = 0, $content = NULL, $display_attributes = array() ) {
  $post = get_post( $post );
  $id = isset( $post->ID ) ? $post->ID : 0;

  $donations = mmdimo_load_donations_by_post($id);

  $default_display_attributes = array(
    'amount' => FALSE,
    'charity' => TRUE,
    'donor_name' => TRUE,
    'destination' => TRUE,
    'honoree' => FALSE,
    'deceased' => FALSE,
    'total_amount' => FALSE,
    'date' => FALSE
  );

  $display_attributes = array_merge($default_display_attributes, $display_attributes);

  if ( $post->ID && isset($donations['total']) && $donations['total'] ) {
    $list = '<ul class="mmdimo-donations-list">';

    foreach ( $donations['data'] as $donation ) {
      $item = '<li>';

      $item .= '<dl>';

      if ( $display_attributes['date'] ) {
        $item .= '<dt class="mmdimo-donations-list-date">' . __('Date:', 'mmdimo') . '</dt>';
        $item .= '<dd class="mmdimo-donations-list-date"><span class="mmdimo-donations-list-date">' . date(get_option( 'date_format' ), $donation->created) . '</span></dd>';
      }

      if ( $display_attributes['amount'] ) {
        $item .= '<dt class="mmdimo-donations-list-amount">' . __('Amount:', 'mmdimo') . '</dt>';
        $item .= '<dd class="mmdimo-donations-list-amount"><span class="mmdimo-donations-list-amount-sep">$</span> <span class="mmdimo-donations-list-amount">' . sprintf('%0.2f', $donation->amount / 100) . '</span></dd>';
      }

      if ( $display_attributes['donor_name'] ) {
        $item .= '<dt class="mmdimo-donations-list-donor-name">' . __('Donor:', 'mmdimo') . '</dt>';
        $item .= '<dd class="mmdimo-donations-list-donor-name"><span class="mmdimo-donations-list-donor-first-name">' . $donation->donor->first_name . '</span> <span class="mmdimo-donations-list-donor-last-name">' . $donation->donor->last_name . '</span></dd>';
      }

      if ( $display_attributes['charity'] && $donation->charities[0] ) {
        $item .= '<dt class="mmdimo-donations-list-charity">' . __('Charity:', 'mmdimo') . '</dt>';
        $item .= '<dd class="mmdimo-donations-list-charity">' . ucwords(strtolower($donation->charities[0]->charityName)) . '</dd>';
      }

      if ( $display_attributes['destination'] && $donation->program->destination ) {
        $item .= '<dt class="mmdimo-donations-list-program-destination">' . __('Destination:', 'mmdimo') . '</dt>';
        $item .= ' <dd class="mmdimo-donations-list-program-destination">' . $donation->program->destination . '</dd> ';
      }

      if ( $display_attributes['honoree'] && $donation->dedicate->dedicate_honoree ) {
        $item .= '<dt class="mmdimo-donations-list-dedicate-honoree">' . __('In the name of:', 'mmdimo') . '</dt>';
        $item .= ' <dd class="mmdimo-donations-list-dedicate-honoree">' . $donation->dedicate->dedicate_honoree . '</dd> ';
      }
      if ( $display_attributes['deceased'] && $donation->dedicate->dedicate_deceased ) {
        $item .= '<dt class="mmdimo-donations-list-dedicate-deceased">' . __('In memory of:', 'mmdimo') . '</dt>';
        $item .= ' <dd class="mmdimo-donations-list-dedicate-deceased">' . $donation->dedicate->dedicate_deceased . '</dd> ';
      }

      $item .= '</dl>';
      $item .= '</li>';

      $item = apply_filters( 'get_the_mmdimo_donations_list_item', $item, $post, $donation );

      $list .= $item;
    }
    $list .= '</ul>';

    $total_amount = '';
    if ($display_attributes['total_amount']) {
      $total_amount = '<div class="mmdimo-donations-list-total-amount"><strong>' . __('Total:', 'mmdimo') . '</strong> $' . sprintf('%0.2f', $donations['total_amount'] / 100) . '</div>';
      $total_amount = apply_filters( 'get_the_mmdimo_donations_list_total_amount', $total_amount, $post, $donations['total_amount'] );
    }

    $content = '<div class="mmdimo-donations-list"><h3>' . __('Donations', 'mmdimo') . '</h3>' . $total_amount . $content . $list . '</div>';

    return apply_filters( 'get_the_mmdimo_donations_list', $content, $post, $donations );
  }

  return '';
}

function shortcode_mmdimo_donations_list( $attr = array(), $content = NULL ) {
  $display_attributes = array(
    'amount',
    'charity',
    'donor_name',
    'destination',
    'honoree',
    'deceased',
    'total_amount',
    'date'
  );
  $display_attributes_values = array();

  foreach ($display_attributes as $display_attribute) {
    if (isset($attr['display_' . $display_attribute])) {
      $display_attributes_values[$display_attribute] = $attr['display_' . $display_attribute];
    }
  }

  if (!$content) {
    $content = NULL;
  }

  return get_the_mmdimo_donations_list(0, $content, $display_attributes_values);
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
