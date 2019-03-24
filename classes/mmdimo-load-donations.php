<?php

class MMDIMO_Load_Donations extends WP_Background_Process {

  protected $prefix = 'mmdimo';

  protected $action = 'load_donations';

  /**
   * Load scheduled donations list for a given post id.
   */
  protected function task( $item ) {
    mmdimo_load_donations_by_post_process( $item['post_id'], $item['count'] );

    $this->clean_duplicates( $item );

    return false;
  }

  /**
  * Cleanup duplicated queue items from database.
  */
  protected function clean_duplicates( $item ) {
    global $wpdb;

    $table  = $wpdb->options;
    $key_column = 'option_name';
    $value_column = 'option_value';

    if ( is_multisite() ) {
      $table  = $wpdb->sitemeta;
      $key_column = 'meta_key';
    }

    $key = $wpdb->esc_like( $this->identifier . '_batch_' ) . '%';
    $value = serialize( array( $item ) );

    $query = $wpdb->prepare( "
      DELETE
      FROM {$table}
      WHERE {$key_column} LIKE %s AND {$value_column} = '%s'
    ",
    $key, $value );

    $wpdb->query( $query );
  }

  /**
   * Publicly expose check if queue is empty.
   */
  public function is_queue_empty() {
    return parent::is_queue_empty();
  }
}
