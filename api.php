<?php
defined( 'ABSPATH' ) or die( 'No direct access allowed.' );

/**
 * Returns the API URL.
 *
 * @return
 *   String with the API URL value, or the default value if it's not set.
 */
function orghunter_charity_search_api_url() {
  $url = get_option( 'orghunter_api_url' );

  return $url ? $url : ORGHUNTER_CHARITY_SEARCH_API_URL_DEFAULT;
}

/**
 * Returns the API key.
 *
 * @return
 *   String with the API Key value, or an empty string if it's not set.
 */
function orghunter_charity_search_api_key() {
  $key = get_option( 'orghunter_api_key' );

  return $key ? $key : '';
}

/**
 * Charity Search categories load.
 */
function orghunter_charity_search_categories() {
  $categories = array();
  $categories_cache = get_option( 'orghunter_charity_search_categories_cache' );

  if ($categories_cache && $categories_cache->created + $categories_cache->expire > $_SERVER['REQUEST_TIME']) {
    $categories = $categories_cache->data;
  }
  else {
    $url = orghunter_charity_search_api_url();
    $key = orghunter_charity_search_api_key();
    $path = 'categories';

    if ($key) {
      $query = orghunter_charity_search_build_query( array(
        'user_key'   => $key,
      ) );

      $response = wp_remote_get( $url . '/' . $path . '?' . $query );

      switch ($response['response']['code']) {
        case '200':
          if ( isset( $response['body'] ) ) {
            $body = json_decode( $response['body'] );
            $results = $body->data;

            foreach ( $results as $category ) {
              if ( isset( $category->categoryId ) && $category->categoryDesc ) {
                $categories[$category->categoryId] = $category->categoryDesc;
              }
            }
          }

          $categories_cache = (object) array(
            'data' => $categories,
            'created' => $_SERVER['REQUEST_TIME'],
            'expire' => ORGHUNTER_CHARITY_SEARCH_CATEGORIES_CACHE_EXPIRE,
          );

          update_option( 'orghunter_charity_search_categories_cache', $categories_cache );
          break;
      }
    }
  }

  return $categories;
}

/**
 * Charity Search search results load.
 */
function orghunter_charity_search_results( $search ) {
  $results = array();
  $url = orghunter_charity_search_api_url();
  $key = orghunter_charity_search_api_key();
  $path = 'charitysearch';

  if ($key) {
    $query = orghunter_charity_search_build_query( array(
      'user_key'   => $key,
      'searchTerm' => $search['search_term'],
      'ein'        => $search['ein'],
      'state'      => $search['state'],
      'city'       => $search['city'],
      'zipCode'    => $search['zip_code'],
      'category'   => $search['category'],
      'eligible'   => $search['eligible'],
      'start'      => isset( $search['start'] ) ? $search['start'] : 0,
      'rows'       => $search['rows'],
    ) );

    $response = wp_remote_get( $url . '/' . $path . '?' . $query );
    $body = json_decode( $response['body'] );

    if ( isset( $response['body'] ) ) {
      $results = $body->data;
    }
  }

  return $results;
}

/**
 * Build query string.
 */
function orghunter_charity_search_build_query( $query_array = array() ) {
  $query = array();

  foreach ( $query_array as $key => $value ) {
    $query[] = $key . '=' . $value;
  }

  return implode( '&', $query );
}
