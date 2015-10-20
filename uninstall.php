<?php
defined( 'WP_UNINSTALL_PLUGIN' ) or die( 'No direct access allowed.' );

$results_page_id = get_option( 'orghunter_charity_search_results_page_id' );
wp_delete_post( $results_page_id );

$options = array(
  'orghunter_api_url',
  'orghunter_api_key',
  'orghunter_affiliate_id',
  'orghunter_results_count',
  'widget_orghunter_charity_search_widget',
  'orghunter_charity_search_results_page_id',
  'orghunter_charity_search_categories_cache',
);

foreach ( $options as $option ) {
  delete_option( $option );
  delete_site_option( $option );
}
